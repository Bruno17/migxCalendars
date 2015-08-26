<?php

class Custom_migxcalendars_upcoming
{

    /**
     * Constructor
     *
     * @access	public
     * @param	array	initialization parameters
     */
    public function __construct(&$blox)
    {
        $this->blox = &$blox;
        $this->bloxconfig = &$blox->bloxconfig;

        $includeclass = 'xettcal';
        $includefile = $this->bloxconfig['absolutepath'] . 'inc/' . $includeclass . '.class.inc.php';
        include_once ($includefile);
        $this->xettcal = new xettcal($this->blox);
        $this->helpers = &$this->xettcal->helpers;
    }

    function getdatas()
    {
        global $modx;

        $resource_id = $modx->resource->get('id');

        $month = abs($this->bloxconfig['month']);
        $year = $this->bloxconfig['year'];
        $monthcount = 3;
        
        $monthnames = explode(',',$this->bloxconfig['monthnames']);

        for ($i = 0; $i < $monthcount; $i++) {
            $workingmonth = $month + $i;
            
            $timestart = xetadodb_mktime(0, 0, 0, $workingmonth, 01, $year);

            $timeend = $this->xettcal->get_ts_monthend($timestart);

            $timestampstart = strftime('%Y-%m-%d %H:%M:%S', $timestart); //umwandlung zu mysql-dateformat
            $timestampend = strftime('%Y-%m-%d %H:%M:%S', $timeend);

            $category = $this->bloxconfig['custom']['category'];

            //Monatsevents laden
            $limit = 0;

            $monthdatas = array();
            $monthdatas['timestamp'] = $timestart;
            $monthdatas['month'] = $workingmonth;
            $monthdatas['monthname'] = isset($monthnames[$workingmonth-1]) ? $monthnames[$workingmonth-1] : '' ;

            $monthdatas['innerrows']['date'] = $this->getEvents($timestampstart, $timestampend, $limit, $category, 1);
            //$events = addCustomFields($mxCalApp, $events);

            //$cal = $this->xettcal->getMonthCal($year, $workingmonth);
            //$datas = $this->xettcal->makeMonthArray($this->bloxconfig, $cal, array(), $events);
            
            $bloxdatas['innerrows']['month'][] = $monthdatas;
            
        }


        //$bloxdatas['innerrows']['month'][0]['innerrows']['monthevents'] = $events;

        //echo '<pre>' . print_r($this->bloxconfig, true) . '</pre>';

        //echo '<pre>' . print_r($bloxdatas, true) . '</pre>';

        if ($this->bloxconfig['debug']) {
            echo '<pre>' . print_r($bloxdatas, true) . '</pre>';
            echo '---------------------------------------';
            echo '<pre>' . print_r($rows, true) . '</pre>';
        }

        $bloxdatas['innerrows']['category'] = $this->getCategories($category);

        $output = $bloxdatas; 
        //$output['bloxoutput'] = $modx->toJson($bloxdatas);
        return $output;
    }
    
    function getCategories($active_categories=''){
        global $modx;
        
        $active_categories = is_array($active_categories) ? $active_categories : array($active_categories);
        
        $scriptProperties['packageName'] = 'migxcalendars';
        $scriptProperties['classname'] = 'migxCalendarCategories';
        //$scriptProperties['tpl'] = 'migxcal_categoryTpl';
        $scriptProperties['toJsonPlaceholder'] = 'migxcal_categories';

        $modx->runSnippet('migxLoopCollection',$scriptProperties);
        
        $result = $modx->getPlaceholder('migxcal_categories');
        $input_arrays = json_decode($result, true);
        $output_arrays = array();
        foreach ($input_arrays as $array) { 
            $id = $modx->getOption('id',$array,0);
            $array['active'] = in_array($id,$active_categories) ? 'active' : 'inactive';
            $output_arrays[] = $array;
        }       
  
        
       
        return $output_arrays;
        
    }

    function getEvents($start, $end=0 , $limit=0, $categories='', $startsonly=0)
    {
        global $modx;

        $categories = is_array($categories) ? $categories : array($categories);

        $scriptProperties['packageName'] = 'migxcalendars';
        $scriptProperties['classname'] = 'migxCalendarDates';
        $scriptProperties['toJsonPlaceholder'] = 'migxcal_events';
        $scriptProperties['selectfields'] = $modx->getOption('datefields', $scriptProperties, 'id,startdate,enddate,title,allday,published,description');
        $joins = '[{"alias":"Event","selectfields":"id,title,allday,repeating,description"},{"alias":"Category","classname":"migxCalendarCategories","on":"Category.id=Event.categoryid"}]';
        $scriptProperties['joins'] = $modx->getOption('joins', $scriptProperties, $joins);
        if (!empty($limit)) {
            $scriptProperties['limit'] = $modx->getOption('limit', $limit, 0);
            $scriptProperties['offset'] = $modx->getOption('offset', $limit, 0);
        }
        $scriptProperties['sortConfig'] = '[{"sortby":"startdate"}]';

        // Parse the start/end parameters.
        // These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
        // Since no timezone will be present, they will parsed as UTC.

        if ($modx->lexicon) {
            $modx->lexicon->load($scriptProperties['packageName'] . ':default');
        }

        //$start = $modx->getOption('start', $_GET, '');
        //$end = $modx->getOption('end', $_GET, '');
        //$categories = $modx->getOption('categories', $_GET, '');

        //$range_start = parseDateTime($start);
        //$range_end = parseDateTime($end);
        $wheres = array();

        if (empty($startsonly)){
            $wheres[] = array('migxCalendarDates.startdate:<=' => $end, 'migxCalendarDates.enddate:>=' => $start);
        }else{
            $wheres[] = array('migxCalendarDates.startdate:>=' => $start,'migxCalendarDates.startdate:<=' => $end);    
        } 
        
        $wheres[] = array('Event.deleted' => 0, 'Event.published' => 1);

        if (is_array($categories)) {
            $cat_array = array();
            foreach ($categories as $category) {
                if (!empty($category) && is_numeric($category)) {
                    $cat_array[] = $category;
                }
            }
            if (count($cat_array) > 0) {
                $wheres[] = array('Category.id:IN' => $cat_array);
            }
        }

        $hide_published = $modx->getOption('c_hide_published', $categories, 0);
        $show_unpublished = $modx->getOption('c_show_unpublished', $categories, 0);
        if (!empty($show_unpublished) && empty($hide_published)) {

        } else {
            if (empty($show_unpublished)) {
                $wheres[] = array('migxCalendarDates.published' => 1);
            }
            if (!empty($hide_published)) {
                $wheres[] = array('migxCalendarDates.published:!=' => 1);
            }
        }

        $scriptProperties['where'] = $modx->toJson($wheres);


        // Parse the timezone parameter if it is present.
        $timezone = null;
        if (isset($_GET['timezone'])) {
            $timezone = new DateTimeZone($_GET['timezone']);
        }

        // Read and parse our events JSON file into an array of event data arrays.
        //$json = file_get_contents(dirname(__FILE__) . '/../json/events.json');

        //$scriptProperties['debug'] = '1';
        $sn = $modx->runSnippet('migxLoopCollection', $scriptProperties);
        //echo $sn;
        $result = $modx->getPlaceholder('migxcal_events');
        $input_arrays = json_decode($result, true);
        // Accumulate an output array of event data arrays.
        $output_arrays = array();
        foreach ($input_arrays as $array) {

            $array['start'] = $array['startdate'];
            $array['end'] = $array['enddate'];

            $array['Time'] = strtotime($array['start']);
            $array['Timeend'] = strtotime($array['end']);

            //$array['detail_id'] = $detail_id;

            if (isset($array['Event_allday']) && isset($array['allday']) && $array['allday'] == '2') {
                //inherit
                $array['allDay'] = $array['Event_allday'];
            } else {
                $array['allDay'] = $array['allday'];
            }

            $array['title'] = !empty($array['title']) ? $array['title'] : $array['Event_title'];
            if (!empty($array['Category_backgroundColor'])) {
                $array['backgroundColor'] = $array['Category_backgroundColor'];
            }
            if (!empty($array['Category_borderColor'])) {
                $array['borderColor'] = $array['Category_borderColor'];

            }
            if (!empty($array['Category_textColor'])) {
                $array['textColor'] = $array['Category_textColor'];
            }
            
            if (empty($array['title'])){
                $array['title'] = $array['Event_title'];
            }

            // Convert the input array into a useful Event object
            //$event = new Event($array, $timezone);

            // If the event is in-bounds, add it to the output
            /*
            if ($event->isWithinDayRange($range_start, $range_end)) {
            $output_arrays[] = $event->toArray();
            }
            */
            $output_arrays[] = $array;

        }
        return $output_arrays;
    }

}
