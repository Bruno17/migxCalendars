<?php

class migxCalendarEvents extends xPDOSimpleObject
{

    public function save($cacheFlag = null)
    {

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

        $result = parent::save($cacheFlag);

        //handle repeatings
        $repeating = $this->get('repeating');
        $repeatenddate = $this->get('repeatenddate');
        $repeattype = $this->get('repeattype');
        $parent = $this->get('id');

        $classname = 'migxCalendarDates';
        $values = $this->toArray();

        if ($repeatenddate > $startdate && !empty($repeating)) {
            //remove dates out of range
            $this->xpdo->removeCollection($classname, array('event_id' => $parent, array('startdate:<' => $startdate, 'OR:startdate:>' => $repeatenddate)));

            switch ($repeattype) {
                case 0:
                    //daily
                    break;
                case 1:
                    //weekly
                    $addtime = 7 * 24 * 60 * 60;
                    $eventstart = $startdate;
                    $eventend = $enddate;
                    $oldtime = strftime('%H:%M:%S', strtotime($oldstart));
                    $olddate = strftime('%Y-%m-%d ', strtotime($eventstart)) . $oldtime;
                    $repeated = 0;
                    while ($eventstart <= $repeatenddate) {
                        $this->createDate($classname,$eventstart, $eventend, $olddate, $repeated, $repeating);
                        $eventstart = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventstart) + $addtime);
                        $eventend = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventend) + $addtime);
                        $olddate = strftime('%Y-%m-%d ', strtotime($eventstart)) . $oldtime;
                        $repeated = 1;
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
            $this->xpdo->removeCollection($classname, array('event_id' => $parent,'repeating' => '1'));
            //create or modify single date
            $this->createDate($classname,$startdate, $enddate);
        }

        return $result;

    }

    public function createDate($classname, $eventstart, $eventend, $olddate = '', $repeating=0, $repeating=0 )
    {
        $parent = $this->get('id');
        
        if (!empty($repeating)){
            $child = $this->xpdo->getObject($classname, array('event_id' => $parent, 'startdate' => $olddate));
        }else{
            $child = $this->xpdo->getObject($classname, array('event_id' => $parent, 'repeating' => '0'));    
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
            $child->set('startdate', $eventstart);
            $child->set('enddate', $eventend);
            $child->set('repeating', $repeating);
            $child->save();
        }
    }

}
