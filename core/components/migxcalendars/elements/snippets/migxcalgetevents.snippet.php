<?php
/**
 * @author Bruno Perner
 * @copyright 2014
 */

//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timezone" GET parameter will force all ISO8601 date stings to a given timezone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

// Require our Event class and datetime utilities
require $modx->getOption('core_path') . 'components/migxcalendars/model/fullcalendar/php/utils.php';

// Short-circuit if the client did not give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end'])) {
    //die("Please provide a date range.");
}

$eventbuttonsTpl = $modx->getOption('eventbuttonsTpl', $scriptProperties, 'migxcal_eventbuttons');
$modalBodyTpl = $modx->getOption('modalBodyTpl', $scriptProperties, 'migxcal_modalBodyTpl');
$detail_id = $modx->getOption('detail_id', $scriptProperties, '');

$scriptProperties['packageName'] = 'migxcalendars';
$scriptProperties['classname'] = 'migxCalendarDates';
$scriptProperties['toJsonPlaceholder'] = 'migxcal_events';
$scriptProperties['selectfields'] = $modx->getOption('datefields', $scriptProperties, 'id,startdate,enddate,title,allday,published,description');
$joins = '[{"alias":"Event","selectfields":"id,title,allday,repeating,description"},{"alias":"Category","classname":"migxCalendarCategories","on":"Category.id=Event.categoryid"}]';
$scriptProperties['joins'] = $modx->getOption('joins',$scriptProperties,$joins);

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.

if ($modx->lexicon) {
    $modx->lexicon->load($scriptProperties['packageName'] . ':default');
}

$start = $modx->getOption('start', $_GET, '');
$end = $modx->getOption('end', $_GET, '');
$categories = $modx->getOption('categories', $_GET, '');

$range_start = parseDateTime($start);
$range_end = parseDateTime($end);
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
$modx->runSnippet('migxLoopCollection', $scriptProperties);
$result = $modx->getPlaceholder('migxcal_events');
$input_arrays = json_decode($result, true);
// Accumulate an output array of event data arrays.
$output_arrays = array();
foreach ($input_arrays as $array) {

    $array['start'] = $array['startdate'];
    $array['end'] = $array['enddate'];
    
    $array['detail_id'] = $detail_id;

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

    $array['popupmenu'] = $modx->getChunk($eventbuttonsTpl, $array);
    $wd = date('D', strtotime($array['startdate']));
    
    if (!empty($modalBodyTpl)){
        $array['modalBody'] = $modx->getChunk($modalBodyTpl, $array);     
    }

    $array['popup_placement'] = $wd == 'Sun' ? 'left' : 'right';

    // Convert the input array into a useful Event object
    $event = new Event($array, $timezone);

    // If the event is in-bounds, add it to the output
    /*
    if ($event->isWithinDayRange($range_start, $range_end)) {
    $output_arrays[] = $event->toArray();
    }
    */
    $output_arrays[] = $event->toArray();

}

// Send JSON to the client.
return json_encode($output_arrays);