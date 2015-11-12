<?php
$object = & $modx->getOption('object',$scriptProperties,null);
$record_fields = $object->get('record_fields');

$classname = 'migxCalendarPeopleRole';
$role_ids = array();
if ($object){
    $people_id = $object->get('id');
    if ($collection = $modx->getCollection($classname,array('people_id'=>$people_id))){
        foreach ($collection as $role){
            $role_ids[] = $role->get('role_id'); 
        }
    }
}
$record_fields['roles'] = implode('||',$role_ids);
$object->set('record_fields',$record_fields);