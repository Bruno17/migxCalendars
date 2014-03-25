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
        if (is_dir($modelpath)){
            $modx->addPackage($packageName, $modelpath, $prefix);
        } 
        
    }
}

$classname = $config['classname'];
$joins = isset($config['joins']) && !empty($config['joins']) ? $modx->fromJson($config['joins']) : false;
/*
if (isset($scriptProperties['data'])) {
    $scriptProperties = array_merge($scriptProperties, $modx->fromJson($scriptProperties['data']));
}
*/
$object_id = $modx->getOption('object_id', $scriptProperties, 0);
$c = $modx->newQuery($classname,$object_id);
$c->select($modx->getSelectColumns($classname, $classname));
if ($joins) {
    $modx->migx->prepareJoins($classname, $joins, $c);
}

if ($object = $modx->getObject($classname,$c)){
    
    $result['success'] = true;
    $result['message'] = '';
    $result['object'] = $object->toArray();

} else {
    $result['success'] = false;
    $result['message'] = 'object not found';
}

return $result;