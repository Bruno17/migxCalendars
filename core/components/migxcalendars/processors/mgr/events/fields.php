<?php

$config = $modx->migx->customconfigs;
$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
$object_id = 'new';

if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}
$packageName = $config['packageName'];
$sender = 'default/fields';

$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
if (is_dir($modelpath)) {
    $modx->addPackage($packageName, $modelpath, $prefix);
}

//get Event-id of Date
//$classname = $config['classname'];
/*
if (isset($scriptProperties['object_id']) && is_numeric($scriptProperties['object_id']) && $object = $modx->getObject($classname, $scriptProperties['object_id'])) {
$scriptProperties['object_id'] = $object->get('event_id');
//set object_id for fields
$_REQUEST['object_id'] = $scriptProperties['object_id'];
}
*/

if (isset($scriptProperties['tempParams'])) {
    $extraParams = $modx->fromJson($scriptProperties['tempParams']);
    if (isset($extraParams['event_id'])) {
        $scriptProperties['object_id'] = $extraParams['event_id'];
        //set object_id for fields
        $_REQUEST['object_id'] = $scriptProperties['object_id'];
    }

}

$classname = 'migxCalendarEvents';


$joinalias = isset($config['join_alias']) ? $config['join_alias'] : '';

//$joins = isset($config['joins']) && !empty($config['joins']) ? $modx->fromJson($config['joins']) : false;
$joins = false;

if (!empty($joinalias)) {
    if ($fkMeta = $modx->getFKDefinition($classname, $joinalias)) {
        $joinclass = $fkMeta['class'];
    } else {
        $joinalias = '';
    }
}

if ($this->modx->lexicon) {
    $this->modx->lexicon->load($packageName . ':default');
}

if (empty($scriptProperties['object_id']) || $scriptProperties['object_id'] == 'new') {
    if ($object = $modx->newObject($classname)) {
        $object->set('object_id', 'new');
    }

} else {
    $c = $modx->newQuery($classname, $scriptProperties['object_id']);
    $pk = $modx->getPK($classname);
    $c->select('
        `' . $classname . '`.*,
    	`' . $classname . '`.`' . $pk . '` AS `object_id`
    ');
    if (!empty($joinalias)) {
        $c->leftjoin($joinclass, $joinalias);
        $c->select($modx->getSelectColumns($joinclass, $joinalias, 'Joined_'));
    }
    if ($joins) {
        $modx->migx->prepareJoins($classname, $joins, $c);
    }
    //$c->prepare();echo $c->toSql();
    if ($object = $modx->getObject($classname, $c)) {
        $object_id = $object->get('id');
    }
}

$_SESSION['migxWorkingObjectid'] = $object_id;

//handle json fields
if ($object) {
    $record = $object->toArray();
} else {
    $record = array();
}


foreach ($record as $field => $fieldvalue) {
    if (!empty($fieldvalue) && is_array($fieldvalue)) {
        foreach ($fieldvalue as $key => $value) {
            $record[$field . '.' . $key] = $value;
        }
    }
}
