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

        if ($this->isNew()) {
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
        $repeaton = explode('||', $this->get('repeaton'));
        $repeatfrequency = $this->get('repeatfrequency');
        $repeatfrequency = empty($repeatfrequency) ? 1 : $repeatfrequency;
        $repeatoccurences_inmonth = $this->get('repeatoccurences_inmonth');
        $all_occurences_inmonth = true;

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
        $old_repeaton = $this->xpdo->getOption('repeaton', $event_array, '');
        $old_repeatfrequency = $this->xpdo->getOption('repeatfrequency', $event_array, '');
        $old_repeatoccurences_inmonth = $this->xpdo->getOption('repeatoccurences_inmonth', $event_array, '');

        $resolve_repeatings = $this->get('resolve_repeatings');
        $resolve_repeatings = !empty($resolve_repeatings) ? true : false;

        if ($this->get('repeatoccurences_inmonth') != $old_repeatoccurences_inmonth) {
            $resolve_repeatings = true;
        }
        if ($this->get('repeaton') != $old_repeaton) {
            $resolve_repeatings = true;
        }
        if ($repeatfrequency != $old_repeatfrequency) {
            $resolve_repeatings = true;
        }        
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
        if (empty($repeating)) {
            $resolve_repeatings = false;
        }

        $classname = 'migxCalendarDates';
        //$values = $this->toArray();

        if ($resolve_repeatings && $repeatenddate > $startdate) {

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
                    $repeattype = 'month';
                    break;
                case 3:
                    //yearly
                    break;
                case 1:
                default:
                    //weekly
                    $repeattype = 'weeks';
            }

            $addtime = '+' . $repeatfrequency . ' ' . $repeattype;
            $check_newmonth = false;
            if (!empty($repeatoccurences_inmonth)) {
                $all_occurences_inmonth = false;
                $repeatoccurences_inmonth = explode('||', $repeatoccurences_inmonth);
                if ($repeattype == 'weeks') {
                    //don't use repeatfrequency other than 1 together with repeatoccurences_inmonth
                    $repeatfrequency = 1;
                }
                $addtime = '+' . $repeatfrequency . ' ' . $repeattype;
                if ($repeattype == 'month') {
                    //with repeatoccurences_inmonth we need to loop thrue all weeks and evtl. check for new month
                    $addtime = '+1 weeks';
                    if ($repeatfrequency > 1) {
                        $check_newmonth = true;
                    }
                }
            }

            if (count($repeaton) > 0 && !empty($repeaton[0])) {
                //specified weekdays
                $event_wd = $repeaton[0];
                $moveddays = '';
            } else {
                //get dow from startdate
                $event_wd = date('D', strtotime($date_startdate));
                $repeaton = array();
                $repeaton[] = $event_wd;
                $diff_days_moved = $this->dateDiffDays($date_startdate, $old_startdate);
                $moveddays = !empty($diff_days_moved) ? $diff_days_moved . ' day' : '';
            }

            //$eventend_wd = date('D', strtotime($date_enddate));
            $old_wd = date('D', strtotime($old_startdate));


            $currentweek_firststart = strftime('%Y-%m-%d ' . $date_starttime, strtotime($startdate . ' ' . $event_wd));

            $diff_days = $this->dateDiffDays($date_startdate, $date_enddate);
            $adddays = !empty($diff_days) ? $diff_days . ' day' : '';
            $current_enddate = strftime('%Y-%m-%d ' . $date_endtime, strtotime($currentweek_firststart . ' ' . $adddays));

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
            $olddate = strftime('%Y-%m-%d ' . $oldtime, strtotime($currentweek_firststart . ' ' . $moveddays));
            $repeating_index = 0;
            $type = 'repeating';
            $current_month = '';
            $firstloop = true;
            while ($currentweek_firststart <= $repeatenddate) {
                //loop weeks
                $weekevents = array();
                foreach ($repeaton as $event_wd) {
                    //loop weekdays
                    $current_startdate = strftime('%Y-%m-%d ' . $date_starttime, strtotime($currentweek_firststart . ' ' . $event_wd));
                    $dayNumberInMonth = $this->dayNumberInMonth($current_startdate);
                    if ($all_occurences_inmonth || in_array($dayNumberInMonth, $repeatoccurences_inmonth)) {
                        $weekevents[] = $current_startdate;
                    }
                }

                sort($weekevents);

                $next_month_frequence = false;
                foreach ($weekevents as $current_startdate) {
                    //loop weekdays
                    $current_enddate = strftime('%Y-%m-%d ' . $date_endtime, strtotime($current_startdate . ' ' . $adddays));
                    $olddate = strftime('%Y-%m-%d ' . $oldtime, strtotime($current_startdate . ' ' . $moveddays));

                    $month = strftime('%m', strtotime($current_startdate));
                    //echo 'check:' . $current_startdate . "\n";
                    if ($month != $current_month) {
                        if ($check_newmonth && !$firstloop) {
                            //if new month and repeatfrequency > 1 and repeattype == month, switch to next month-frequence
                            //echo 'new month in weekday-loop' . "\n";
                            $next_month_frequence = true;
                            break;
                        }
                        $current_month = $month;
                    }
                    //echo 'create:' . $current_startdate . "\n";
                    if ($date_o = $this->createDate($classname, $current_startdate, $current_enddate, $type, $olddate, $repeating_index, $date_id, $scriptProperties)) {

                    }
                    $repeating_index++;
                    $firstloop = false;
                }

                if ($month != $current_month) {
                    if (!$firstloop && $check_newmonth) {
                        //if new month and repeatfrequency > 1 and repeattype == month, switch to next month-frequence
                        //echo 'new month in week-loop';
                        $next_month_frequence = true;
                    }
                    $current_month = $month;
                }
                if ($next_month_frequence) {
                    $prevmonth = strftime('%Y-%m-01 %H:%M:%S', strtotime($current_startdate . ' -1 month'));
                    $nextmonth = strftime('%Y-%m-%d %H:%M:%S', strtotime($prevmonth . ' +' . $repeatfrequency . ' month'));
                    //echo 'prev month:' . $prevmonth . "\n";
                    //echo 'new month:' . $nextmonth . "\n";
                    $currentweek_firststart = strftime('%Y-%m-%d %H:%M:%S', strtotime($nextmonth));
                    $firstloop = true;
                } else {
                    //echo 'new week (no frequence):' . $current_startdate . "\n";
                    $currentweek_firststart = strftime('%Y-%m-%d %H:%M:%S', strtotime($currentweek_firststart . $addtime));
                    //echo 'new week (no frequence):' . $currentweek_firststart . "\n";
                }

                //$current_enddate = strftime('%Y-%m-%d ' . $date_endtime, strtotime($currentweek_firststart . ' ' . $adddays));
                //$olddate = strftime('%Y-%m-%d ' . $oldtime, strtotime($currentweek_firststart . ' ' . $moveddays));
            }


            if (!$preventsave) {
                //remove all remaining dates from other editings
                $this->xpdo->removeCollection($classname, array(
                    'event_id' => $parent,
                    'editedon:!=' => $this->get('editedon'),
                    'type' => 'repeating'
                    ));
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
            if (!empty($date_startdate)) {
                $this->createDate($classname, $date_startdate, $date_enddate, 'single', '', 0, $date_id, $scriptProperties);
            }


        }
        
        return $result;

    }

    function dayNumberInMonth($date = '') {
        if ($date == '') {
            $t = date('d-m-Y');
        } else {
            $t = date('d-m-Y', strtotime($date));
        }

        $dayName = strtolower(date("D", strtotime($t)));
        $dayNum = strtolower(date("d", strtotime($t)));
        $return = floor(($dayNum - 1) / 7) + 1;
        return $return;
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
            if (!empty($publish_all)) {
                $child->set('published', '1');
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
