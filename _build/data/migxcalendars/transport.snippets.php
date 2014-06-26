<?php
/**
 * snippets transport file for migxCalendars extra
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'name' => 'migxcalGetEvents',
    'description' => '',
    'properties' => '',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/migxcalgetevents.snippet.php'));

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => '2',
    'property_preprocess' => '',
    'name' => 'migxcalCalendar',
    'description' => '',
    'properties' => '',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/migxcalcalendar.snippet.php'));

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => '3',
    'property_preprocess' => '',
    'name' => 'migxcalDetailView',
    'description' => '',
    'properties' => '',
), '', true, true);
$snippets[3]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/migxcaldetailview.snippet.php'));

return $snippets;
