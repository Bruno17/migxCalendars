<?php
$xpdo_meta_map['migxCalendarFeed']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_feeds',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'feed' => '',
    'type' => '',
    'defaultcategoryid' => 0,
    'timerint' => 0,
    'timermeasurement' => '',
    'lastrunon' => NULL,
    'nextrunon' => NULL,
    'published' => 1,
  ),
  'fieldMeta' => 
  array (
    'feed' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'defaultcategoryid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'timerint' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'timermeasurement' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'lastrunon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'nextrunon' => 
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
  ),
  'aggregates' => 
  array (
    'eventfeed' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'id',
      'foreign' => 'feeds_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
