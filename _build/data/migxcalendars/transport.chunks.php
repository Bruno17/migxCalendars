<?php
/**
 * chunks transport file for migxCalendars extra
 *
 * Copyright 2014 by Bruno Perner b.perner@gmx.de
 * Created on 06-26-2014
 *
 * @package migxcalendars
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'name' => 'migxcal_categoryTpl',
    'description' => '',
    'properties' => '',
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcal_categorytpl.chunk.html'));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => '2',
    'property_preprocess' => '',
    'name' => 'migxcal_controller',
    'description' => '',
    'properties' => array(),
), '', true, true);
$chunks[2]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcal_controller.chunk.html'));

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => '3',
    'property_preprocess' => '',
    'name' => 'migxcalEditableOptions',
    'description' => '',
    'properties' => array(),
), '', true, true);
$chunks[3]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcaleditableoptions.chunk.html'));

$chunks[4] = $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => '4',
    'property_preprocess' => '',
    'name' => 'migxcal_eventbuttons',
    'description' => '',
    'properties' => array(),
), '', true, true);
$chunks[4]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcal_eventbuttons.chunk.html'));

$chunks[5] = $modx->newObject('modChunk');
$chunks[5]->fromArray(array(
    'id' => '5',
    'property_preprocess' => '',
    'name' => 'migxcalExtraOptions',
    'description' => '',
    'properties' => array(),
), '', true, true);
$chunks[5]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcalextraoptions.chunk.html'));

$chunks[6] = $modx->newObject('modChunk');
$chunks[6]->fromArray(array(
    'id' => '6',
    'property_preprocess' => '',
    'name' => 'migxcal_modalBodyTpl',
    'description' => '',
    'properties' => '',
), '', true, true);
$chunks[6]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcal_modalbodytpl.chunk.html'));

$chunks[7] = $modx->newObject('modChunk');
$chunks[7]->fromArray(array(
    'id' => '7',
    'property_preprocess' => '',
    'name' => 'migxcal_detailTpl',
    'description' => '',
    'properties' => '',
), '', true, true);
$chunks[7]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/migxcal_detailtpl.chunk.html'));

return $chunks;
