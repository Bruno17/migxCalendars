<?php
$xpdo_meta_map['migxCalendarEventImages']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_events_images',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'event_id' => NULL,
    'date_id' => NULL,
    'filepath' => '',
    'title' => '',
    'description' => '',
    'published' => 1,
  ),
  'fieldMeta' => 
  array (
    'event_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
    ),
    'date_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
    ),
    'filepath' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
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
    'Event' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'event_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Date' => 
    array (
      'class' => 'migxCalendarDates',
      'local' => 'date_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
