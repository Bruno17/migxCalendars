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

if (isset($scriptProperties['object_id']) && is_numeric($scriptProperties['object_id']) && $object = $modx->getObject($classname, $scriptProperties['object_id'])) {
    if (isset($scriptProperties['data'])) {
        $scriptProperties = array_merge($scriptProperties, $modx->fromJson($scriptProperties['data']));
    }
    $startdate = str_replace('T', ' ', $modx->getOption('startdate', $scriptProperties, ''));
    $enddate = str_replace('T', ' ', $modx->getOption('enddate', $scriptProperties, ''));
    $allday = $modx->getOption('allday', $scriptProperties, '0');
    $allday = !empty($allday) ? '1' : '0';

    $type = $object->get('type');
    if ($type == 'repeating') {
        $classname = 'migxCalendarEvents';
        $scriptProperties['object_id'] = $object->get('event_id');
        //set object_id for fields
        $_REQUEST['object_id'] = $scriptProperties['object_id'];
        $old_start = $object->get('startdate');
        $movedelta = strtotime($startdate) - strtotime($old_start);
        $datedelta = strtotime($enddate) - strtotime($startdate);

        if ($object = $modx->getObject($classname, $scriptProperties['object_id'])) {
            $object->set('allday', $allday);
            $object->set('startdate', strftime('%Y-%m-%d %H:%M:%S', strtotime($object->get('startdate')) + $movedelta));
            $object->set('enddate', strftime('%Y-%m-%d %H:%M:%S', strtotime($object->get('startdate')) + $datedelta));

            $object->save();
        }
    } else {
        $allday = !empty($allday) ? '1' : '2';
        $object->set('allday', $allday);
        $object->set('startdate', $startdate);
        $object->set('enddate', $enddate);
        $object->save();
    }

}

echo 'huhu';
