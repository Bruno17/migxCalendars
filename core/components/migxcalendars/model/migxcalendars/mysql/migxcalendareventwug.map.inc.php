<?php
$xpdo_meta_map['migxCalendarEventWUG']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_wug',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'eventid' => NULL,
    'webusergroup' => NULL,
  ),
  'fieldMeta' => 
  array (
    'eventid' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
    ),
    'webusergroup' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'composites' => 
  array (
    'WebUserGroup' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'webusergroup',
      'foreign' => 'modUserGroup',
      'cardinality' => 'many',
    ),
    'EventId' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'eventid',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
