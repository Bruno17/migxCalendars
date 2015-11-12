<?php

class Custom_migxcalendars_minical
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
        $this->helpers = & $this->xettcal->helpers; 
    }

    function getdatas()
    {
        global $modx;

        $resource_id = $modx->resource->get('id');

        $now = time();

        $month = abs($this->bloxconfig['month']);
        $year = $this->bloxconfig['year'];

        $timestart = xetadodb_mktime(0, 0, 0, $month, 01, $year);

        $timeend = $this->xettcal->get_ts_monthend($timestart);

        $timestampstart = strftime('%Y-%m-%d %H:%M:%S', $timestart); //umwandlung zu mysql-dateformat
        $timestampend = strftime('%Y-%m-%d %H:%M:%S', $timeend);

        $category = $this->bloxconfig['custom']['category'];
        $sync_list = isset($_REQUEST['sync_list']) ? $_REQUEST['sync_list'] : 0;
        unset($_GET['sync_list']);
        if ($sync_list) {
            $_GET['tsmonth'] = $timestart;
            unset($_GET['tsday']);
            unset($_GET['tsweek']);
        }

        $listmode = 'upcoming';

        if (isset($_GET['tsday'])) {
            $listmode = 'dayevents';
            unset($_GET['tsmonth']);
            unset($_GET['tsweek']);
        }
        if (isset($_GET['tsmonth'])) {
            $listmode = 'monthevents';
            unset($_GET['tsday']);
            unset($_GET['tsweek']);
        }
        if (isset($_GET['tsweek'])) {
            $listmode = 'weekevents';
            unset($_GET['tsday']);
            unset($_GET['tsmonth']);
        }

        $listmode = isset($_REQUEST['listmode']) ? $_REQUEST['listmode'] : $listmode;
        unset($_GET['listmode']);


        //Monatsevents laden
        $limit = 0;

        $events = $this->getEvents($timestampstart, $timestampend, $limit, $category);
        //$events = addCustomFields($mxCalApp, $events);

        $timestampfirstday = xetadodb_mktime(0, 0, 0, $month, 01, $year);

        $limit = 0;

        switch ($listmode) {
            case 'dayevents':
                $timestamp = (int)$_GET['tsday'];
                $d = xetadodb_date("d", $timestamp);
                $m = xetadodb_date("m", $timestamp);
                $y = xetadodb_date("Y", $timestamp);
                $tseventlist = xetadodb_mktime(0, 0, 0, $m, $d, $y);
                $timestampend = $this->xettcal->get_ts_dayend($tseventlist);
                break;
            case 'weekevents':
                $timestamp = (int)$_GET['tsweek'];
                $tseventlist = $this->xettcal->get_ts_weekstart($timestamp);
                $timestampend = $this->xettcal->get_ts_weekend($tseventlist);
                break;
            case 'monthevents':
                $timestamp = (int)$_GET['tsmonth'];
                $d = xetadodb_date("d", $timestamp);
                $m = xetadodb_date("m", $timestamp);
                $y = xetadodb_date("Y", $timestamp);
                $tseventlist = xetadodb_mktime(0, 0, 0, $m, '01', $y);
                $timestampend = $this->xettcal->get_ts_monthend($tseventlist);
                break;
            case 'upcoming':
            default:
                $timestamp = $now;
                $d = xetadodb_date("d", $timestamp);
                $m = xetadodb_date("m", $timestamp);
                $y = xetadodb_date("Y", $timestamp);
                $tseventlist = xetadodb_mktime(0, 0, 0, $m, $d, $y);
                $timestampend = xetadodb_mktime(0, 0, 0, $m, $d, $y + 1);
                $limit = array('limit' => $this->bloxconfig['limit'], 'offset' => $this->bloxconfig['offset']);
                break;
        }

        $timestampstart = strftime('%Y-%m-%d %H:%M:%S', $tseventlist); //umwandlung zu mysql-dateformat
        $timestampend = strftime('%Y-%m-%d %H:%M:%S', $timestampend);

        $listevents = $this->getEvents($timestampstart, $timestampend, $limit, $category);
        $numRows = $modx->getPlaceholder('total');

        //Kategorien laden
        
        $cat_rows = array();
        $c = $modx->newQuery('migxCalendarCategories');
        $c->where(array('published' => 1));
        if ($collection = $modx->getCollection('migxCalendarCategories', $c)) {
            foreach ($collection as $object) {
                $cat_row = $object->toArray();
                $cat_rows[] = $cat_row;
            }
        }

        $cat_row = array();
        $cat_row['id'] = '0';
        $cat_row['name'] = 'Alle';
        $cat_rows[] = $cat_row;

        $categories = array();
        foreach ($cat_rows as $category) {
            $link['category'] = $modx->getOption('id', $category, 0);
            $category['cat_link'] = $this->helpers->smartModxUrl($resource_id, null, $link);
            $current = (int) $modx->getOption('category',$_GET,0);
            $category['active_class'] = $current == $link['category'] ? 'active' : '';
            $categories[] = $category;
        }        

        $cal = $this->xettcal->getMonthCal($year, $month);
        $bloxdatas = $this->xettcal->makeMonthArray($this->bloxconfig, $cal, array(), $events);
        //$bloxdatas['innerrows']['month'][0]['innerrows']['monthevents'] = $events;
        $bloxdatas['innerrows']['eventlist'] = $listevents;
        $bloxdatas['innerrows']['category'] = $categories;

        //echo '<pre>' . print_r($this->bloxconfig, true) . '</pre>';

        //echo '<pre>' . print_r($bloxdatas, true) . '</pre>';
        unset($_GET['tsmonth']);
        unset($_GET['tsweek']);
        unset($_GET['tsday']);
        $link = array();
        $link['tsday'] = time();
        $bloxdatas['link_today'] = $this->helpers->smartModxUrl($resource_id, null, $link);
        $link = array();
        $link['tsweek'] = time();
        $bloxdatas['link_thisweek'] = $this->helpers->smartModxUrl($resource_id, null, $link);
        $link = array();
        $link['tsmonth'] = time();
        $bloxdatas['link_thismonth'] = $this->helpers->smartModxUrl($resource_id, null, $link);
        $link = array();
        $link['listmode'] = 'upcoming';
        $bloxdatas['link_upcoming'] = $this->helpers->smartModxUrl($resource_id, null, $link);


        require_once ($this->bloxconfig['absolutepath'] . 'inc/Pagination.php');
        $p = new Pagination(array(
            'per_page' => $this->bloxconfig['limit'],
            'num_links' => $this->bloxconfig['numLinks'],
            'cur_page' => $this->bloxconfig['page'],
            'total_rows' => $numRows,
            'page_query_string' => $this->bloxconfig['pageVarKey'],
            'use_page_numbers' => true));

        $bloxdatas['pagination'] = $p->create_links();
        //$bloxdatas['innerrows']['row'] = $rows;

        if ($this->bloxconfig['debug']) {
            //echo '<pre>' . print_r($bloxdatas, true) . '</pre>';
            //echo '---------------------------------------';
            //echo '<pre>' . print_r($rows, true) . '</pre>';
        }

        return $bloxdatas;
    }

    function getEvents($start, $end, $limit, $categories)
    {
        global $modx;

        $categories = is_array($categories) ? $categories : array($categories);
        
        $scriptProperties['packageName'] = 'migxcalendars';
        $scriptProperties['classname'] = 'migxCalendarDates';
        $scriptProperties['toJsonPlaceholder'] = 'migxcal_events';
        $scriptProperties['selectfields'] = $modx->getOption('datefields', $scriptProperties, 'id,startdate,enddate,title,allday,published,description');
        $joins = '[
        {"alias":"Event","selectfields":"id,title,allday,repeating,description"},
        {"alias":"Category","classname":"migxCalendarCategories","on":"Category.id=Event.categoryid"},
        {"alias":"Location","classname":"migxCalendarLocation","on":"Location.id=Event.location_id"},
        {"alias":"Organizer","classname":"migxCalendarPeople","on":"Organizer.id=Event.organizer_id"}
        ]';
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

        $wheres[] = array('migxCalendarDates.startdate:<=' => $end, 'migxCalendarDates.enddate:>=' => $start);
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
        echo $sn;
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
