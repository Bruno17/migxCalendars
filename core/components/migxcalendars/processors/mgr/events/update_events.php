<?php

$event_classname = 'migxCalendarEvents';

if (isset($scriptProperties['object_id']) && is_numeric($scriptProperties['object_id']) && $object = $modx->getObject($classname, $scriptProperties['object_id'])) {
    //get the date-object, if any
    
} else {
    //otherwise create a new one
    $object = $modx->newObject($classname);
}


if ($object) {

    

    if (isset($scriptProperties['data'])) {
        $scriptProperties = array_merge($scriptProperties, $modx->fromJson($scriptProperties['data']));
    }
    
    $remove = $modx->getOption('delete_date', $scriptProperties, false);
    
    if ($remove){
        $object->remove();
        $message = $modx->lexicon('migxcal.date_removed');
        return;
        //return $modx->error->success('huhu');    
    }
    
    //$startdate = str_replace('T', ' ', $modx->getOption('startdate', $scriptProperties, ''));
    //$enddate = str_replace('T', ' ', $modx->getOption('enddate', $scriptProperties, ''));
    //$allday = $modx->getOption('allday', $scriptProperties, '0');
    //$allday = !empty($allday) ? '1' : '0';
    $event_id = $modx->getOption('Event_id', $scriptProperties, 0);
    $date_array = $object->toArray();

    //get the event-object (date-container) or create a new one
    if ($event_object = $modx->getObject($event_classname, $object->get('event_id'))) {
        $event_object->set('event_array', $event_object->toArray());
    } elseif (!empty($event_id) && $event_object = $modx->getObject($event_classname, $event_id)) {
        $event_object->set('event_array', $event_object->toArray());
    } else {
        $event_object = $modx->newObject($event_classname);
    }

    if ($event_object) {
        //get and save event-object
        //$object->set('allday', $allday);
        
        $modx->migx->loadConfigs();
        $tabs = $modx->migx->getTabs();
        $form_fields = $modx->migx->extractFieldsFromTabs($tabs);

        $fieldid = 0;
        $postvalues = array();
        $arraydelimiters = $modx->getOption('arraydelimiters', $config, array());
        $arrayenclosings = $modx->getOption('arrayenclosings', $config, array());
        $default_arraydelimiter = $modx->getOption('arraydelimiter', $config, '||');
        $default_arrayenclosing = $modx->getOption('arrayenclosing', $config, '');
        $validation_errors = array();        

        foreach ($scriptProperties as $field => $value) {
            /* handles checkboxes & multiple selects elements */
            if (is_array($value)) {
                $featureInsert = array();
                while (list($featureValue, $featureItem) = each($value)) {
                    $featureInsert[count($featureInsert)] = $featureItem;
                }
                $arraydelimiter = $modx->getOption($field, $arraydelimiters, $default_arraydelimiter);
                $arrayenclosing = $modx->getOption($field, $arrayenclosings, $default_arrayenclosing);
                $value = $arrayenclosing . implode($arraydelimiter, $featureInsert) . $arrayenclosing;
            }
            
            $form_field = $modx->getOption($field, $form_fields, '');

            $validation = $modx->getOption('validation', $form_field, '');
            if (!empty($validation)) {
                $validations = explode(',', str_replace('||', ',', $validation));
                foreach ($validations as $validation_type) {
                    switch ($validation_type) {
                        case 'required':
                            if (empty($value)) {
                                $error = $form_field;
                                $error['validation_type'] = $validation_type;
                                //$error['field'] = ;
                                $validation_errors[] = $error;

                            }
                            break;
                    }
                }
            }            
            
            //store Event-fields
            if (substr($field, 0, 6) == 'Event_') {
                $event_object->set(substr($field, 6), $value);
            }
            $scriptProperties[$field] = $value;
        }
        
        if (count($validation_errors) > 0) {
            $updateerror = true;
            foreach ($validation_errors as $error) {
                $field_caption = $modx->getOption('caption', $error, '');
                $validation_type = $modx->getOption('validation_type', $error, '');
                //$errormsg .=   $modx->lexicon('quip.thread_err_save');
                $errormsg .= $field_caption . ': ' . $validation_type . '<br/>';
            }
            return;
        }        

        $startdate = $modx->getOption('startdate', $scriptProperties, '');
        $addtime = '+1hour';
        $scriptProperties['enddate'] = empty($scriptProperties['enddate']) ? strftime('%Y-%m-%d %H:%M:%S', strtotime($startdate . $addtime)) : $scriptProperties['enddate'];

        
        $event_object->set('date_array', $date_array);
        $event_object->set('scriptProperties', $scriptProperties);
        
        if ($event_object->save()) {
            $event_id = $event_object->get('id');
        }
    }

}