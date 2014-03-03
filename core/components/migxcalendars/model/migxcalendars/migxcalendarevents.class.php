<?php

class migxCalendarEvents extends xPDOSimpleObject
{

    public function save($cacheFlag = null)
    {

        $preventsave = $this->get('preventsave');
        
        if ($oldobject = $this->xpdo->getObject($this->_class, $this->get('id'))) {
            $oldstart = $oldobject->get('startdate');
        }

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
        if (!empty($categories)){
            $this->set('categories','||' . trim($categories , '|') . '||');
        }

        if (!$preventsave){
            $result = parent::save($cacheFlag);
        }else{
            $result = true;
        }

        //handle repeatings
        $repeating = $this->get('repeating');
        $repeatenddate = $this->get('repeatenddate');
        $repeattype = $this->get('repeattype');
        $parent = $this->get('id');

        $classname = 'migxCalendarDates';
        $values = $this->toArray();

        if ($repeatenddate > $startdate && !empty($repeating)) {
            //remove dates out of range
            if (!$preventsave){
                $this->xpdo->removeCollection($classname, array('event_id' => $parent, array('startdate:<' => $startdate, 'OR:startdate:>' => $repeatenddate)));
            }
            
            switch ($repeattype) {
                case 0:
                    //daily
                    break;
                case 1:
                    //weekly
                    $addtime = 7 * 24 * 60 * 60;
                    $eventstart = $startdate;
                    $eventend = $enddate;
                    $event_wd = strftime('%w',strtotime($startdate));
                    $old_wd = strftime('%w',strtotime($oldstart));
                    if ($event_ws != $old_wd){
                        //moved to other day, we remove all repeatings completly
                        if (!$preventsave){
                            $this->xpdo->removeCollection($classname, array('event_id' => $parent,'type' => 'repeating','repeating_index:>' => 0));
                        }
                    }
                    $oldtime = strftime('%H:%M:%S', strtotime($oldstart));
                    $olddate = strftime('%Y-%m-%d ', strtotime($eventstart)) . $oldtime;
                    $repeating_index = 0;
                    $type = 'repeating';
                    while ($eventstart <= $repeatenddate) {
                        $this->createDate($classname,$eventstart, $eventend, $type, $olddate, $repeating_index);
                        $eventstart = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventstart) + $addtime);
                        $eventend = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventend) + $addtime);
                        $olddate = strftime('%Y-%m-%d ', strtotime($eventstart)) . $oldtime;
                        $repeated = 1;
                        $repeating_index ++;
                    }
                    break;
                case 2:
                    //monthly
                    break;
                case 3:
                    //yearly
                    break;
            }
        } else {
            //no repeating, remove all repeatings
            if (!$preventsave){
                $this->xpdo->removeCollection($classname, array('event_id' => $parent,'type' => 'repeating','repeating_index:>' => 0));
            }
            //create or modify single date
            $this->createDate($classname,$startdate, $enddate);
        }
        
        return $result;

    }

    public function createDate($classname, $eventstart, $eventend, $type='single', $olddate = '', $repeating_index = 0)
    {
        $parent = $this->get('id');
        $preventsave = $this->get('preventsave');
        
        if (!empty($repeating_index)){
            $child = $this->xpdo->getObject($classname, array('event_id' => $parent, 'startdate' => $olddate));
        }else{
            $child = $this->xpdo->getObject($classname, array('event_id' => $parent, 'repeating_index' => '0'));    
        }
        
        if ($child) {
            //child-event exists allready, modify it
            $values['published'] = $child->get('published');

        } else {
            $child = $this->xpdo->newObject($classname);
        }

        if ($child) {
            //$child->fromArray($values);
            $child->set('event_id', $parent);
            $child->set('type', $type);
            $child->set('startdate', $eventstart);
            $child->set('enddate', $eventend);
            $child->set('repeating_index', $repeating_index);
            $child->set('preventsave',$preventsave);
            $child->event = &$this;
            $child->save();
        }
        
        $dates = $this->get('createdDates');
        if (!is_array($dates)){
           $dates = array();          
        } 
        $dates[] = $child->toArray(); 
        $this->set('createdDates',$dates);
    }
    
    public function handleAllday()
    {
        
        $allday = $this->get('allday');
        
        
        if (!empty($allday)) {
            $startdate = strftime('%Y-%m-%d ', strtotime($this->get('startdate')));
            $this->set('startdate',$startdate . '00:00:00');
            $enddate = strftime('%Y-%m-%d ', strtotime($this->get('enddate')));
            $this->set('enddate',$enddate . '23:59:59');            
        }

    }    

}
