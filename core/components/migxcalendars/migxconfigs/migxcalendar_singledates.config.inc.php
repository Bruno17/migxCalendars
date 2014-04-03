<?php

$action = $this->modx->getOption('action',$_REQUEST,'');
$reqTempParams = $this->modx->fromJson($this->modx->getOption('reqTempParams',$_REQUEST,'')) ;
$data = $this->modx->fromJson($this->modx->getOption('data',$_REQUEST,'')) ;

if ($action == 'mgr/migxdb/getList'){
    if (!empty($reqTempParams['event_id'])){
        $_POST['object_id'] = $reqTempParams['event_id'];    
    }    
    
}

if ($action == 'mgr/migxdb/update'){
    if (isset($data['Joined_id'])){
        $_POST['co_id'] = $data['Joined_id'];    
    }    
    
}