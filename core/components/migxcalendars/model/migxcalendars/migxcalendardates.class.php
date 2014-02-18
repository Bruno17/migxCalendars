<?php

class migxCalendarDates extends xPDOSimpleObject
{

    public function save($cacheFlag = null)
    {

        $published = $this->get('published');
        
        $active = true;
        if (empty($published)) {
            $active = false;
         }

        if ($active) {
            if (!$this->handleDouble()) {
                return true;
            }
        }


        //handle enddate - enddate can't be lower than startdate
        $enddate = $this->get('enddate');
        $startdate = $this->get('startdate');
        if ($enddate <= $startdate) {
            $this->set('enddate', $startdate);
        }

        return parent::save($cacheFlag);

    }

    public function handleDouble()
    {
        $result = true;
        if ($eventO = $this->getOne('Event')) {
            if ($categoryO = $eventO->getOne('Category')) {
                $ondoubleevents = $categoryO->get('ondoubleevents');
                if (empty($ondoubleevents)) {
                    return true;
                }
                if ($this->activeExists($this->get('startdate'), $this->get('enddate'), $categoryO->get('id'))) {
                    switch ($ondoubleevents) {
                        case 'unpublish':
                            $this->set('published', 0);
                            break;
                        case 'ignore':
                            $result = false;
                            break;
                    }
                }

            }
        }

        return $result;
    }

    public function activeExists($start, $end, $categoryid)
    {

        $joinclass = 'migxCalendarEvents';
        $jalias = 'Event';
        $c = $this->xpdo->newQuery($this->_class);
        $c->leftjoin($joinclass, $jalias);
        $c->where(array(
            'id:!=' => $this->get('id'),
            'startdate:<' => $end,
            'enddate:>' => $start,
            'Event.categoryid' => $categoryid,
            'Event.deleted' => 0,
            'published' => 1));

        return $this->xpdo->getCollection($this->_class, $c);

    }

}
