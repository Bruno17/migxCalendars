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
$classname = $config['classname'];

$joinalias = isset($config['join_alias']) ? $config['join_alias'] : '';

$joins = isset($config['joins']) && !empty($config['joins']) ? $modx->fromJson($config['joins']) : false;

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
    if ($object = $modx->getObject($classname, $c)) {
        $object_id = $object->get('id');
    }
}

$_SESSION['migxWorkingObjectid'] = $object_id;


if ($object) {
    $record = $object->toArray();
    $event_object = $object->getOne('Event');
} else {
    $record = array();
}


foreach ($record as $field => $fieldvalue) {
    if (substr($field, 0, 6) == 'Event_') {
        $record[$field . '_old'] = $fieldvalue;
    } else {
        $record['old_' . $field] = $fieldvalue;
    }

    if (!empty($fieldvalue) && is_array($fieldvalue)) {
        foreach ($fieldvalue as $key => $value) {
            $record['old_' . $field . '.' . $key] = $value;
        }
    }
}

if (isset($scriptProperties['data'])) {
    $data = $modx->fromJson($scriptProperties['data']);
    $allday = $modx->getOption('allday',$data,'new');
    $old_allday = $object->get('allday');
    if ($allday != 'new') {
        if ($event_object) {
            if ($old_allday == '2') {
                //allday inherited from Container
                
                if ($allday == $event_object->get('allday')){
                    $data['allday'] = '2';
                }
            }
        }
    }
    else{
        //new
        $data['allday'] = '2';
        $data['Event_allday'] = '0';    
    }

    $record = array_merge($record, $data);
    $record['repeating'] = isset($record['type']) && $record['type'] == 'repeating' ? '1' : '0';

    //$record['allday'] = empty($record['allday']) ? '2' : $record['allday'];
    $startdate = $modx->getOption('startdate', $record, '');
    $addtime = '+1hour';
    $record['enddate'] = empty($record['enddate']) ? strftime('%Y-%m-%d %H:%M:%S', strtotime($startdate . $addtime)) : $record['enddate'];
}
