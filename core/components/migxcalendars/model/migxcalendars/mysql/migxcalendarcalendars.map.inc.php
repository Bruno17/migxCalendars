<?php
$xpdo_meta_map['migxCalendarCalendars']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_calendars',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'webusergroup' => NULL,
    'published' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'webusergroup' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
    ),
    'published' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
  ),
  'aggregates' => 
  array (
    'CalendarId' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'id',
      'foreign' => 'calendar_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
