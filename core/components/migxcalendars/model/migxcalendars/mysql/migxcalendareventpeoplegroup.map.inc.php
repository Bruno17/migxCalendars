<?php
$xpdo_meta_map['migxCalendarEventPeopleGroup']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_event_peoplegroups',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'event_id' => NULL,
    'date_id' => NULL,
    'owner_id' => NULL,
    'total_people' => NULL,
    'name' => NULL,
    'description' => NULL,
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
    'owner_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
    ),
    'total_people' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'description' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
    ),
  ),
  'composites' => 
  array (
    'People' => 
    array (
      'class' => 'migxCalendarEventPeople',
      'local' => 'id',
      'foreign' => 'group_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Owner' => 
    array (
      'class' => 'modUser',
      'local' => 'owner_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
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
