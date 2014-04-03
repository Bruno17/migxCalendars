<?php

$config = $modx->migx->customconfigs;

$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}

if (!empty($config['packageName'])) {
    $packageNames = explode(',', $config['packageName']);
    //all packages must have the same prefix for now!
    foreach ($packageNames as $packageName) {
        $packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
        $modelpath = $packagepath . 'model/';
        if (is_dir($modelpath)) {
            $modx->addPackage($packageName, $modelpath, $prefix);
        }

    }
}

if ($modx->lexicon) {
    $modx->lexicon->load($packageName . ':default');
}

$classname = 'migxCalendarDates';

$rows = array();

if (isset($scriptProperties['reqTempParams'])) {
    $reqTempParams = $modx->fromJson($scriptProperties['reqTempParams']);
    unset($scriptProperties['reqTempParams']);
    $scriptProperties = array_merge($scriptProperties,$reqTempParams);
    $scriptProperties['Event_preventsave'] = 1;
    $scriptProperties['Event_publish_all_repeatings'] = 1;
    $scriptProperties['Event_resolve_repeatings'] = true;
    
    include (dirname(dirname(__file__)) . '/events/update_events.php');
}

if (is_object($event_object)){
    $rows = $event_object->get('createdDates');    
}




