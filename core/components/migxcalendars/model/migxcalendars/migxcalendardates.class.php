<?php

class migxCalendarDates extends xPDOSimpleObject
{

    /**
     * @access public
     * @var event A reference to the event object.
     */
    public $event = null;

    public function save($cacheFlag = null)
    {

        $published = $this->get('published');
        $this->event = $this->getOne('Event');

        //handle enddate - enddate can't be lower than startdate
        $enddate = $this->get('enddate');
        $startdate = $this->get('startdate');
        if ($enddate <= $startdate) {
            $this->set('enddate', $startdate);
        }

        //handle allday
        $this->handleAllday();

        $active = true;
        if (empty($published)) {
            $active = false;
        }

        if ($active) {
            if (!$this->handleDouble()) {
                return true;
            }
            if (!$this->handleDoubleInAssignedCats()) {
                return true;
            }            
        }

        return parent::save($cacheFlag);

    }

    public function handleDouble()
    {
        $result = true;
        if ($eventO = &$this->event) {
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

    public function handleDoubleInAssignedCats()
    {
        $result = true;
        if ($eventO = &$this->event) {
            if ($categoryO = $eventO->getOne('Category')) {
                $ondoubleevents = $categoryO->get('ondates_in_assigned_cats');
                if (empty($ondoubleevents)) {
                    return true;
                }

                $categories = explode('||', $eventO->get('categories'));
                foreach ($categories as $category) {
                    if (!empty($category)) {
                        if ($dates = $this->activeExists($this->get('startdate'), $this->get('enddate'), $category)) {
                            switch ($ondoubleevents) {
                                case 'unpublishdates':
                                    foreach ($dates as $dateO) {
                                        $dateO->set('published', 0);
                                        $dateO->save();
                                    }
                                    break;
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


            }
        }

        return $result;
    }

    public function handleAllday()
    {
        
        $allday = $this->get('allday');
        
        if (empty($allday) && $this->event) {
            $allday = $this->event->get('allday');
        }

        if ($allday == 1) {
            $startdate = strftime('%Y-%m-%d ', strtotime($this->get('startdate')));
            $this->set('startdate', $startdate . '00:00:00');
            $enddate = strftime('%Y-%m-%d ', strtotime($this->get('enddate')));
            $this->set('enddate', $enddate . '23:59:59');
        }

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
            'Event.deleted' => 0,
            'published' => 1,
            array('Event.categoryid' => $categoryid, 'OR:Event.categories:LIKE' => '%||' . $categoryid . '||%')));

        return $this->xpdo->getCollection($this->_class, $c);

    }

}
