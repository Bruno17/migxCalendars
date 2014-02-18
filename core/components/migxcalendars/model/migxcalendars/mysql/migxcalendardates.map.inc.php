<?php
$xpdo_meta_map['migxCalendarDates']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_dates',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'startdate' => NULL,
    'enddate' => NULL,
    'published' => 1,
    'event_id' => 0,
    'repeating' => 0,
  ),
  'fieldMeta' => 
  array (
    'startdate' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'enddate' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
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
    'event_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'repeating' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Event' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'event_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
