<?php
/**
 * menus transport file for migxCalendars extra
 *
 * Copyright 2014 by Bruno Perner b.perner@gmx.de
 * Created on 03-09-2014
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
/* @var xPDOObject[] $menus */

$action = $modx->newObject('modAction');
$action->fromArray( array (
  'id' => 1,
  'namespace' => 'migx',
  'controller' => 'index',
  'haslayout' => '1',
  'lang_topics' => 'example:default',
  'assets' => '',
), '', true, true);

$menus[1] = $modx->newObject('modMenu');
$menus[1]->fromArray( array (
  'text' => 'migxCalendars',
  'parent' => '',
  'description' => '',
  'icon' => '',
  'menuindex' => '',
  'params' => '&configs=migxcalendar_events,migxcalendars||migxcalendar_categories',
  'handler' => '',
  'permissions' => '',
), '', true, true);
$menus[1]->addOne($action);

return $menus;
