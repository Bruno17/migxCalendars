<?php

/**
 * MIGXdb
 *
 * Copyright 2012 by Bruno Perner <b.perner@gmx.de>
 *
 * This file is part of MIGXdb, for editing custom-tables in MODx Revolution CMP.
 *
 * MIGXdb is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MIGXdb is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MIGXdb; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA 
 *
 * @package migx
 */
/**
 * Update and Create-processor for migxdb
 *
 * @package migx
 * @subpackage processors
 */
//if (!$modx->hasPermission('quip.thread_view')) return $modx->error->failure($modx->lexicon('access_denied'));

//return $modx->error->failure('huhu');

if (empty($scriptProperties['object_id'])) {
    $updateerror = true;
    $errormsg = $modx->lexicon('quip.thread_err_ns');
    return;
}

$config = $modx->migx->customconfigs;
$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
$errormsg = '';

if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}
$packageName = $config['packageName'];

$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
$is_container = $modx->getOption('is_container', $config, false);
if (is_dir($modelpath)) {
    $modx->addPackage($packageName, $modelpath, $prefix);
}
$classname = $config['classname'];

$auto_create_tables = isset($config['auto_create_tables']) ? $config['auto_create_tables'] : true;
$modx->setOption(xPDO::OPT_AUTO_CREATE_TABLES, $auto_create_tables);

if ($modx->lexicon) {
    $modx->lexicon->load($packageName . ':default');
}

$co_id = $modx->getOption('co_id', $scriptProperties, '');

if (isset($scriptProperties['data'])) {
    $scriptProperties = array_merge($scriptProperties, $modx->fromJson($scriptProperties['data']));
}

$resource_id = $modx->getOption('resource_id', $scriptProperties, false);
$resource_id = !empty($co_id) ? $co_id : $resource_id;

$checkConnected = $modx->migx->checkForConnectedResource($resource_id, $config);

$joinalias = isset($config['join_alias']) ? $config['join_alias'] : '';
$has_jointable = false;

if (!empty($joinalias)) {
    $has_jointable = isset($config['has_jointable']) && $config['has_jointable'] == 'no' ? false : true;
    if ($fkMeta = $modx->getFKDefinition($classname, $joinalias)) {
        $joinclass = $fkMeta['class'];
        if ($checkConnected && $fkMeta['owner'] == 'foreign') {
            $scriptProperties[$fkMeta['local']] = $resource_id;
        }
        $joinvalues = array();
    } else {
        $joinalias = '';
    }
}


$task = $modx->getOption('task', $scriptProperties, 'update');

switch ($task) {
    case 'publish':
        $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('publishedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('published', '1');
        $unpub = $object->get('unpub_date');
        if ($unpub < strftime('%Y-%m-%d %H:%M:%S')) {
            $object->set('unpub_date', null);
        }
        break;
    case 'unpublish':
        $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('unpublishedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('published', '0');
        $object->set('unpublishedby', $modx->user->get('id')); //feld fehlt noch
        $pub = $object->get('pub_date');
        if ($pub < strftime('%Y-%m-%d %H:%M:%S')) {
            $object->set('pub_date', null);
        }
        break;
    case 'delete':
        $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('deletedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('deleted', '1');
        $object->set('deletedby', $modx->user->get('id'));
        break;
    case 'recall':
        $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('deleted', '0');
        break;
    default:

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
            $fieldid++;
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
            $field = explode('.', $field);

            if (count($field) > 1) {
                //extended field (json-array)
                $postvalues[$field[0]][$field[1]] = $value;
            } else {
                $postvalues[$field[0]] = $value;
            }


            if (!empty($joinalias)) {
                // check for jointable- fields
                //$len = strlen($joinalias)+1;
                if (substr($field[0], 0, 7) == 'Joined_') {
                    $joinvalues[substr($field[0], 7)] = $value;
                    unset($postvalues[$field[0]]);
                }
            }
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

        if ($scriptProperties['object_id'] == 'new') {
            $object = $modx->newObject($classname);
            $tempvalues['createdon'] = strftime('%Y-%m-%d %H:%M:%S');
            $postvalues['createdby'] = $modx->user->get('id');
            //handle published
            $postvalues['published'] = isset($postvalues['published']) ? $postvalues['published'] : '1';
            $oldstart = 0;
        } else {
            $object = $modx->getObject($classname, $scriptProperties['object_id']);
            if (empty($object))
                return $modx->error->failure($modx->lexicon('quip.thread_err_nf'));
            $postvalues['editedon'] = strftime('%Y-%m-%d %H:%M:%S');
            $postvalues['editedby'] = $modx->user->get('id');
            $tempvalues['createdon'] = $object->get('createdon');
            $tempvalues['publishedon'] = $object->get('publishedon');
            $oldstart = $object->get('startdate');
        }


        if (isset($postvalues['published']) && $postvalues['published'] == '1') {
            $pub = $object->get('published');
            if (empty($pub)) {
                $tempvalues['publishedon'] = strftime('%Y-%m-%d %H:%M:%S');
                $postvalues['publishedby'] = $modx->user->get('id');
            }
            /*
            $unpub = $object->get('unpub_date');
            if ($unpub < strftime('%Y-%m-%d %H:%M:%S'))
            {
            $postvalues['unpub_date'] = null;
            }
            */
        }
        /*
        if ($postvalues['published'] == '0')
        {
        $pub = $object->get('pub_date');
        if ($pub < strftime('%Y-%m-%d %H:%M:%S'))
        {
        $postvalues['pub_date'] = null;
        }
        }
        */
        //overwrites
        if (empty($postvalues['ow_createdon'])) {
            $postvalues['createdon'] = $tempvalues['createdon'];
        }
        if (empty($postvalues['ow_publishedon'])) {
            $postvalues['publishedon'] = $tempvalues['publishedon'];
        }

        if (!$is_container && !empty($postvalues['resource_id'])) {
            $postvalues['customerid'] = $postvalues['resource_id'];
        }

        if ($checkConnected) {

        } else {
            unset($postvalues['resource_id']);
        }

        //handle enddate
        $enddate = $modx->getOption('enddate', $postvalues, 0);
        $startdate = $modx->getOption('startdate', $postvalues, 0);
        if ($enddate <= $startdate) {
            $postvalues['enddate'] = $startdate;
        }

        $object->fromArray($postvalues);
}


if ($object->save() == false) {
    $updateerror = true;
    $errormsg = $modx->lexicon('quip.thread_err_save');
    return;
}

//handle repeatings
$repeating = $modx->getOption('repeating', $postvalues, 0);
$repeatenddate = $modx->getOption('repeatenddate', $postvalues, 0);
$repeattype = $modx->getOption('repeattype', $postvalues, 1);
$parent = $object->get('id');

if ($repeatenddate > $startdate && !empty($repeating)) {
    $modx->removeCollection($classname,array('parent' => $parent , 'startdate:>' => $repeatenddate));
    switch ($repeattype) {
        case 0:
            //daily
            break;
        case 1:
            //weekly
            $addtime = 7 * 24 * 60 * 60;
            $eventstart = $startdate;
            $eventend = $enddate;
            $oldtime = strftime('%H:%M:%S', strtotime($oldstart));
            
            while ($eventstart <= $repeatenddate) {
                $eventstart = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventstart) + $addtime);
                $eventend = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventend) + $addtime);
                $olddate = strftime('%Y-%m-%d ', strtotime($eventstart)) . $oldtime;
                
                if ($child = $modx->getObject($classname,array('parent'=>$parent,'startdate'=>$olddate))){
                    //child-event exists allready, modify it
                    
                }else{
                    $child = $modx->newObject($classname);
                }
                
                $child->fromArray($postvalues);
                $child->set('parent',$parent);
                $child->set('repeating',0);
                $child->set('startdate',$eventstart);   
                $child->set('enddate',$eventend);
                $child->save(); 
            }
            break;
        case 2:
            //monthly
            break;
        case 3:
            //yearly
            break;
    }
} else {
    $modx->removeCollection($classname,array('parent = ' . $parent));
}


if ($has_jointable && !empty($joinalias)) {

    //handle join-table
    //todo make it more flexible, not only for resource-connections with joinalias 'Resource'
    if ($joinFkMeta = $modx->getFKDefinition($joinclass, 'Resource')) {
        $localkey = $joinFkMeta['local'];

        if ($joinobject = $modx->getObject($joinclass, array('resource_id' => $scriptProperties['resource_id'], $localkey => $object->get('id')))) {
            $joinobject->fromArray($joinvalues);
        } else {
            $joinobject = $modx->newObject($joinclass);
            $joinobject->fromArray($joinvalues);
            $joinobject->set('active', '1');
            $joinobject->set('resource_id', $scriptProperties['resource_id']);
            $joinobject->set($localkey, $object->get('id'));
        }
        $joinobject->save();
    }
}

//clear cache for all contexts
$collection = $modx->getCollection('modContext');
foreach ($collection as $context) {
    $contexts[] = $context->get('key');
}
$modx->cacheManager->refresh(array(
    'db' => array(),
    'auto_publish' => array('contexts' => $contexts),
    'context_settings' => array('contexts' => $contexts),
    'resource' => array('contexts' => $contexts),
    ));

?>
