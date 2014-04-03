<?php

//if (!$modx->hasPermission('quip.thread_list')) return $modx->error->failure($modx->lexicon('access_denied'));

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
        if (is_dir($modelpath)){
            $modx->addPackage($packageName, $modelpath, $prefix);
        } 
        
    }
}

$classname = 'migxCalendarEvents';
$object = $modx->newObject($classname);

$params = $modx->fromJson($modx->getOption('reqTempParams',$scriptProperties,''));

$object->fromArray($params);

$object->set('preventsave',1);
$object->set('id',$object->get('object_id'));

if ($object->save() == false) {
    $updateerror = true;
    $errormsg = $modx->lexicon('quip.thread_err_save');
    return;
}

$rows = $object->get('createdDates'); 
