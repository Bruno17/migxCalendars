<?php

class migxCalendarEvents extends xPDOSimpleObject {

    public function save($cacheFlag = null) {

        $preventsave = $this->get('preventsave');

        //handle enddate
        $enddate = $this->get('enddate');
        $startdate = $this->get('startdate');
        if ($enddate <= $startdate) {
            $time = strftime('%H:%M:%S', strtotime($enddate));
            $enddate = strftime('%Y-%m-%d ', strtotime($startdate)) . $time;
            if ($enddate <= $startdate) {
                $enddate = $startdate;
            }
            $this->set('enddate', $enddate);
        }

        //handle allday
        $this->handleAllday();

        //handle categories - add double-pipes at front and end for better filtering
        $categories = $this->get('categories');
        if (!empty($categories)) {
            $this->set('categories', '||' . trim($categories, '|') . '||');
        }

        $this->set('editedon', strftime('%Y-%m-%d %H:%M:%S'));
        if (is_object($this->xpdo->user)) {
            $this->set('editedby', $this->xpdo->user->get('id'));
        }
        
        if ($this->isNew()){
            $this->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            if (is_object($this->xpdo->user)) {
                $this->set('createdby', $this->xpdo->user->get('id'));
            }                
        }

        if (!$preventsave) {
            $result = parent::save($cacheFlag);
        } else {
            $result = true;
        }

        //handle repeatings
        $repeating = $this->get('repeating');
        $repeatenddate = $this->get('repeatenddate');
        $repeattype = $this->get('repeattype');
        $repeattype = empty($repeattype) && !empty($repeating) ? '1' : $repeattype; //set repeattye to 1 for now
        $parent = $this->get('id');

        $scriptProperties = $this->get('scriptProperties');
        $date_array = $this->get('date_array');
        $event_array = $this->get('event_array');

        $date_startdate = str_replace('T', ' ', $this->xpdo->getOption('startdate', $scriptProperties, ''));
        $date_enddate = str_replace('T', ' ', $this->xpdo->getOption('enddate', $scriptProperties, ''));

        //handle enddate
        if ($date_enddate <= $date_startdate) {
            $time = strftime('%H:%M:%S', strtotime($date_enddate));
            $date_enddate = strftime('%Y-%m-%d ', strtotime($date_startdate)) . $time;
            if ($date_enddate <= $date_startdate) {
                $date_enddate = $date_startdate;
            }
        }

        $date_starttime = strftime('%H:%M:%S', strtotime($date_startdate));
        $date_endtime = strftime('%H:%M:%S', strtotime($date_enddate));

        $date_id = $this->xpdo->getOption('id', $date_array, 0);

        $old_startdate = $this->xpdo->getOption('startdate', $date_array, '');
        $old_enddate = $this->xpdo->getOption('enddate', $date_array, '');
        $old_repeatstart = $this->xpdo->getOption('startdate', $event_array, '');
        $old_repeatend = $this->xpdo->getOption('repeatenddate', $event_array, '');
        $old_repeating = $this->xpdo->getOption('repeating', $event_array, '');

        $resolve_repeatings = $this->get('resolve_repeatings');
        $resolve_repeatings =  !empty($resolve_repeatings) ? true : false ;


        if ($repeatenddate != $old_repeatend) {
            $resolve_repeatings = true;
        }
        if ($startdate != $old_repeatstart) {
             $resolve_repeatings = true;
        }
        if ($repeating != $old_repeating) {
            $resolve_repeatings = true;
            //repeating has changed, we remove all other dates completly
            if (!$preventsave) {
                $this->xpdo->removeCollection($classname, array('event_id' => $parent, 'id:!=' => $date_id));
            }
        }
        if ($date_startdate != $old_startdate) {
            $resolve_repeatings = true;
        }
        if ($date_enddate != $old_enddate) {
            $resolve_repeatings = true;
        }
        if (empty($repeating)){
            $resolve_repeatings = false;
        }
        
        $classname = 'migxCalendarDates';
        //$values = $this->toArray();

        if ($resolve_repeatings && $repeatenddate > $startdate ) {
            
            //remove dates out of range
            /*
            if (!$preventsave) {
                $this->xpdo->removeCollection($classname, array(
                    'type' => 'repeating',
                    'event_id' => $parent,
                    array('startdate:<' => $startdate, 'OR:startdate:>' => $repeatenddate)));
            }
            */
            switch ($repeattype) {
                case 4:
                    //daily
                    break;
                case 2:
                    //monthly
                    break;
                case 3:
                    //yearly
                    break;
                case 1:
                default:
                    //weekly
                    $event_wd = date('D', strtotime($date_startdate));
                    $eventend_wd = date('D', strtotime($date_enddate));
                    $old_wd = date('D', strtotime($old_startdate));
                    $addtime = '+1 weeks';

                    $current_startdate = strftime('%Y-%m-%d ' . $date_starttime, strtotime($startdate . ' ' . $event_wd));

                    $diff_days = $this->dateDiffDays($date_startdate, $date_enddate);
                    $adddays = !empty($diff_days) ? $diff_days . ' day' : '';
                    $current_enddate = strftime('%Y-%m-%d ' . $date_endtime, strtotime($current_startdate . ' ' . $adddays));

                    $diff_days_moved = $this->dateDiffDays($date_startdate, $old_startdate);
                    $moveddays = !empty($diff_days_moved) ? $diff_days_moved . ' day' : '';

                    $clean_if_otherday = false;
                    if ($clean_if_otherday && $event_wd != $old_wd) {
                        //moved to other day, we remove all repeatings completly
                        if (!$preventsave) {
                            $this->xpdo->removeCollection($classname, array(
                                'event_id' => $parent,
                                'type' => 'repeating',
                                //'id:!=' => $date_id
                                ));
                        }

                    }
                    $oldtime = strftime('%H:%M:%S', strtotime($old_startdate));
                    $olddate = strftime('%Y-%m-%d ' . $oldtime, strtotime($current_startdate . ' ' . $moveddays));
                    $repeating_index = 0;
                    $type = 'repeating';
                    while ($current_startdate <= $repeatenddate) {
                        if ($date_o = $this->createDate($classname, $current_startdate, $current_enddate, $type, $olddate, $repeating_index, $date_id, $scriptProperties)) {

                        }
                        $current_startdate = strftime('%Y-%m-%d %H:%M:%S', strtotime($current_startdate . $addtime));
                        $current_enddate = strftime('%Y-%m-%d %H:%M:%S', strtotime($current_enddate . $addtime));
                        $olddate = strftime('%Y-%m-%d ' . $oldtime, strtotime($current_startdate . ' ' . $moveddays));
                        $repeating_index++;
                    }

                    if (!$preventsave) {
                        //remove all remaining dates from other editings
                        $this->xpdo->removeCollection($classname, array(
                            'event_id' => $parent,
                            'editedon:!=' => $this->get('editedon'),
                            ));
                    }

                    break;
            }
        } else {
            if (empty($repeating)) {
                //no repeating, remove all repeatings, but not the current one
                if (!$preventsave) {
                    $this->xpdo->removeCollection($classname, array(
                        'event_id' => $parent,
                        'type' => 'repeating',
                        'id:!=' => $date_id));
                }
            }

            //create or modify current date
            if (!empty($date_startdate)){
                $this->createDate($classname, $date_startdate, $date_enddate, 'single', '', 0, $date_id, $scriptProperties);
            }
            

        }

        return $result;

    }

    public function createDate($classname, $eventstart, $eventend, $type = 'single', $olddate = '', $repeating_index = 0, $date_id = 0, $values) {
        $parent = $this->get('id');
        $preventsave = $this->get('preventsave');

        if ($child = $this->xpdo->getObject($classname, array('event_id' => $parent, 'startdate' => $olddate))) {

        } else {
            //$child = $this->xpdo->getObject($classname, array('event_id' => $parent, 'repeating_index' => '0'));
            if ($type != 'repeating') {
                $child = $this->xpdo->getObject($classname, $date_id);
            }
        }

        if ($child) {
            //child-event exists allready, modify it
            $values['published'] = $child->get('published');
        } else {
            $child = $this->xpdo->newObject($classname);
        }

        if ($child) {
            if ($values) {
                if ($type == 'repeating') {
                    if ($child->get('id') == $date_id) {
                        $child->fromArray($values);
                    }
                } else {
                    $child->fromArray($values);
                }
            }
            $publish_all = $this->get('publish_all_repeatings');
            if (!empty($publish_all)){
                $child->set('published','1');    
            }            
            $child->set('event_id', $parent);
            $child->set('type', $type);
            $child->set('startdate', $eventstart);
            $child->set('enddate', $eventend);
            $child->set('repeating_index', $repeating_index);
            $child->set('preventsave', $preventsave);
            $child->event = &$this;
            $child->set('editedon', $this->get('editedon'));
            $child->set('editedby', $this->get('editedby'));
            $child->save();
        }

        $dates = $this->get('createdDates');
        if (!is_array($dates)) {
            $dates = array();
        }
        $dates[] = $child->toArray();
        $this->set('createdDates', $dates);
        return $child;
    }

    public function handleAllday() {

        $allday = $this->get('allday');


        if (!empty($allday)) {
            $startdate = strftime('%Y-%m-%d ', strtotime($this->get('startdate')));
            $this->set('startdate', $startdate . '00:00:00');
            $enddate = strftime('%Y-%m-%d ', strtotime($this->get('enddate')));
            $this->set('enddate', $enddate . '23:59:59');
        }

    }

    public function dateDiffDays($startdate, $enddate) {
        $startdate = strtotime(strftime('%Y-%m-%d 00:00:00', strtotime($startdate)));
        $enddate = strtotime(strftime('%Y-%m-%d 00:00:00', strtotime($enddate)));
        $diff = $enddate - $startdate;
        return $diff / (24 * 3600);
    }

}
