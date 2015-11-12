<?php
$object = & $modx->getOption('object',$scriptProperties,null);
$properties = $modx->getOption('scriptProperties',$scriptProperties,array());
$postvalues = $modx->getOption('postvalues',$scriptProperties,array());
 
$co_id = $modx->getOption('co_id',$properties,0);
$roles = $modx->getOption('roles',$properties,0);
$roles = is_array($roles) ? $roles : array($roles);
 
$configs = $modx->getOption('configs', $properties, '');
 
if ($object){
    $people_id = $object->get('id');
    $classname = 'migxCalendarPeopleRole';
    if ($collection = $modx->getCollection($classname,array('people_id'=>$people_id))){
        foreach ($collection as $role){
            $role_ids[$role->get('role_id')] = $role->get('role_id'); 
        }
    }    
    
    foreach ($roles as $key=>$role_id){
        if(!empty($role_id)){
            if ($role = $modx->getObject($classname,array('role_id'=>$role_id,'people_id'=>$people_id))){
                
            }else{
                $role = $modx->newObject($classname);
                $role->set('role_id',$role_id);
                $role->set('people_id',$people_id);
                $role->save();
            }
            unset($role_ids[$role_id]);
        }
    }

    foreach ($role_ids as $role_id){
        if(!empty($role_id)){
            if ($role = $modx->getObject($classname,array('role_id'=>$role_id,'people_id'=>$people_id))){
                $role->remove();    
            }
        }        
    }
}
 
return '';