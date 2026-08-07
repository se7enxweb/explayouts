CREATE TABLE explayouts_block (
  definition_identifier varchar(255) NOT NULL DEFAULT '',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  item_view_type text NOT NULL,
  layout_id INTEGER NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  parent_id INTEGER NOT NULL DEFAULT '0',
  placeholder varchar(255) NOT NULL DEFAULT '',
  position INTEGER NOT NULL DEFAULT '0',
  status INTEGER NOT NULL DEFAULT '1',
  view_type varchar(255) NOT NULL DEFAULT '',
  zone_id INTEGER NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_block_layout_status ON explayouts_block  ( layout_id, status );



  CREATE  INDEX idx_block_position ON explayouts_block  ( position );



  CREATE  INDEX idx_block_zone_status ON explayouts_block  ( zone_id, status );





CREATE TABLE explayouts_block_parameter (
  block_id INTEGER NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  value text
);
  CREATE  INDEX idx_block_parameter_block ON explayouts_block_parameter  ( block_id );



  CREATE  UNIQUE INDEX idx_block_parameter_block_name ON explayouts_block_parameter  ( block_id, name );





CREATE TABLE explayouts_collection (
  block_id INTEGER NOT NULL DEFAULT '0',
  collection_type varchar(255) NOT NULL DEFAULT 'manual',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  limit_value INTEGER NOT NULL DEFAULT '0',
  offset_value INTEGER NOT NULL DEFAULT '0',
  status INTEGER NOT NULL DEFAULT '1'
);
  CREATE  UNIQUE INDEX idx_collection_block ON explayouts_collection  ( block_id );





CREATE TABLE explayouts_collection_item (
  collection_id INTEGER NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  item_type varchar(255) NOT NULL DEFAULT 'manual',
  position INTEGER NOT NULL DEFAULT '0',
  value_id INTEGER NOT NULL DEFAULT '0',
  value_type varchar(255) NOT NULL DEFAULT 'ez_content'
);
  CREATE  INDEX idx_collection_item_collection_position ON explayouts_collection_item  ( collection_id, position );





CREATE TABLE explayouts_collection_query (
  collection_id INTEGER NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parameters text,
  query_type varchar(255) NOT NULL DEFAULT ''
);
  CREATE  UNIQUE INDEX idx_collection_query_collection ON explayouts_collection_query  ( collection_id );





CREATE TABLE explayouts_layout (
  created INTEGER NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_type varchar(255) NOT NULL DEFAULT '',
  modified INTEGER NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  status INTEGER NOT NULL DEFAULT '1'
);
  CREATE  INDEX idx_layout_status ON explayouts_layout  ( status );



  CREATE  UNIQUE INDEX idx_layout_identifier_status ON explayouts_layout  ( identifier, status );





CREATE TABLE explayouts_rule (
  enabled INTEGER NOT NULL DEFAULT '1',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  layout_id INTEGER NOT NULL DEFAULT '0',
  priority INTEGER NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_rule_enabled_priority ON explayouts_rule  ( enabled, priority );





CREATE TABLE explayouts_rule_condition (
  condition_type varchar(255) NOT NULL DEFAULT '',
  condition_value varchar(255) NOT NULL DEFAULT '',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  rule_id INTEGER NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_rule_condition_rule ON explayouts_rule_condition  ( rule_id );





CREATE TABLE explayouts_rule_target (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  rule_id INTEGER NOT NULL DEFAULT '0',
  target_type varchar(255) NOT NULL DEFAULT '',
  target_value varchar(255) NOT NULL DEFAULT ''
);
  CREATE  INDEX idx_rule_target_rule ON explayouts_rule_target  ( rule_id );





CREATE TABLE explayouts_zone (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_id INTEGER NOT NULL DEFAULT '0',
  linked_layout_id INTEGER DEFAULT NULL,
  position INTEGER NOT NULL DEFAULT '0',
  status INTEGER NOT NULL DEFAULT '1'
);
  CREATE  INDEX idx_zone_layout_status ON explayouts_zone  ( layout_id, status );



  CREATE  INDEX idx_zone_position ON explayouts_zone  ( position );



