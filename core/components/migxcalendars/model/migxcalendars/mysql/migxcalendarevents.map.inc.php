<?php
$xpdo_meta_map['migxCalendarEvents']= array (
  'package' => 'migxcalendars',
  'version' => NULL,
  'table' => 'migxcalendars_events',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'title' => '',
    'description' => '',
    'content' => '',
    'categoryid' => NULL,
    'categories' => '',
    'link' => '',
    'linkrel' => '',
    'linktarget' => '',
    'location_id' => NULL,
    'allday' => 0,
    'startdate' => NULL,
    'enddate' => NULL,
    'repeating' => 0,
    'repeattype' => NULL,
    'repeaton' => NULL,
    'repeatfrequency' => NULL,
    'repeatenddate' => NULL,
    'source' => 'local',
    'feeds_id' => 0,
    'feeds_uid' => '',
    'lastedit' => NULL,
    'context' => '',
    'calendar_id' => 0,
    'form_chunk' => '',
    'createdon' => NULL,
    'createdby' => 0,
    'editedon' => NULL,
    'editedby' => 0,
    'published' => 1,
    'deleted' => 0,
    'deletedon' => NULL,
    'deletedby' => 0,
    'parent' => 0,
    'images' => '',
    'videos' => '',
    'extended' => '',
  ),
  'fieldMeta' => 
  array (
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
    'content' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'categoryid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'index' => 'index',
    ),
    'categories' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'fulltext',
    ),
    'link' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'linkrel' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'linktarget' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'location_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'index' => 'index',
    ),
    'allday' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
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
    'repeating' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'repeattype' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => true,
    ),
    'repeaton' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '15',
      'phptype' => 'string',
      'null' => true,
    ),
    'repeatfrequency' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'int',
      'null' => true,
    ),
    'repeatenddate' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'source' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => 'local',
    ),
    'feeds_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'feeds_uid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'lastedit' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => true,
    ),
    'context' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'calendar_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'form_chunk' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'createdby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'editedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'editedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'deleted' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'deletedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'deletedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'images' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'videos' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'extended' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'composites' => 
  array (
    'images' => 
    array (
      'class' => 'migxCalendarEventImages',
      'local' => 'id',
      'foreign' => 'event_id',
      'cardinality' => 'many',
    ),
    'videos' => 
    array (
      'class' => 'migxCalendarEventVideos',
      'local' => 'id',
      'foreign' => 'event_id',
      'cardinality' => 'many',
    ),
    'Children' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
    ),
    'Dates' => 
    array (
      'class' => 'migxCalendarDates',
      'local' => 'id',
      'foreign' => 'event_id',
      'cardinality' => 'many',
    ),
    'People' => 
    array (
      'class' => 'migxCalendarEventPeople',
      'local' => 'id',
      'foreign' => 'event_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Images' => 
    array (
      'class' => 'migxCalendarEventImages',
      'local' => 'id',
      'foreign' => 'date_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Videos' => 
    array (
      'class' => 'migxCalendarEventVideos',
      'local' => 'id',
      'foreign' => 'date_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'CalendarId' => 
    array (
      'class' => 'migxCalendarCalendars',
      'local' => 'calendar_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'eventfeed' => 
    array (
      'class' => 'migxCalendarFeed',
      'local' => 'feeds_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'WebUserGroup' => 
    array (
      'class' => 'migxCalendarEventWUG',
      'local' => 'id',
      'foreign' => 'webusergroup',
      'cardinality' => 'many',
      'owner' => 'foreign',
    ),
    'CreatedBy' => 
    array (
      'class' => 'modUser',
      'local' => 'createdby',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'EditedBy' => 
    array (
      'class' => 'modUser',
      'local' => 'editedby',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Parent' => 
    array (
      'class' => 'migxCalendarEvents',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Category' => 
    array (
      'class' => 'migxCalendarCategories',
      'local' => 'categoryid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
