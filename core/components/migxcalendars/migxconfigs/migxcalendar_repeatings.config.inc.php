<?php

$action = $this->modx->getOption('action',$_REQUEST,'');
$reqTempParams = $this->modx->fromJson($this->modx->getOption('reqTempParams',$_REQUEST,'')) ;

if ($action == 'mgr/migxdb/getList'){
    if (!empty($reqTempParams['event_id'])){
        $_POST['object_id'] = $reqTempParams['event_id'];    
    }    
    
}

