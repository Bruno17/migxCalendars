<?php
/**
 * templates transport file for migxCalendars extra
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
/* @var xPDOObject[] $templates */


$templates = array();

$templates[1] = $modx->newObject('modTemplate');
$templates[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'templatename' => 'migxcal_baseTemplate',
    'description' => '',
    'icon' => '',
    'template_type' => '0',
    'properties' => '',
), '', true, true);
$templates[1]->setContent(file_get_contents($sources['source_core'] . '/elements/templates/migxcal_basetemplate.template.html'));

$templates[2] = $modx->newObject('modTemplate');
$templates[2]->fromArray(array(
    'id' => '2',
    'property_preprocess' => '',
    'templatename' => 'migxcal_editableTemplate',
    'description' => '',
    'icon' => '',
    'template_type' => '0',
    'properties' => '',
), '', true, true);
$templates[2]->setContent(file_get_contents($sources['source_core'] . '/elements/templates/migxcal_editabletemplate.template.html'));

$templates[3] = $modx->newObject('modTemplate');
$templates[3]->fromArray(array(
    'id' => '3',
    'property_preprocess' => '',
    'templatename' => 'migxcal_dateDetailTemplate',
    'description' => '',
    'icon' => '',
    'template_type' => '0',
    'properties' => '',
), '', true, true);
$templates[3]->setContent(file_get_contents($sources['source_core'] . '/elements/templates/migxcal_datedetailtemplate.template.html'));

return $templates;
