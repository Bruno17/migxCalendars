<?php
$dateVarKey = $modx->getOption('dateVarKey', $scriptProperties, 'date_id');
$date_id = (int) $modx->getOption($dateVarKey, $_REQUEST, 0);

$output = '';
if (!empty($date_id)) {

    $wheres = array();
    $wheres[] = array('id'=>$date_id);
    $wheres[] = array('Event.deleted' => 0, 'Event.published' => 1);

    $scriptProperties['packageName'] = 'migxcalendars';
    $scriptProperties['classname'] = 'migxCalendarDates';
    $scriptProperties['joins'] = '[{"alias":"Event"},{"alias":"Category","classname":"migxCalendarCategories","on":"Category.id=Event.categoryid"}]';
    $scriptProperties['where'] = $modx->toJson($wheres);

    $output = $modx->runSnippet('migxLoopCollection', $scriptProperties);
}

$properties['load_fullcalendar'] = '0';
$modx->runSnippet('migxcalCalendar', $properties);

return $output;