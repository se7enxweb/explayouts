CREATE TABLE explayouts_block (
  definition_identifier varchar(255) NOT NULL DEFAULT '',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  item_view_type text NOT NULL,
  layout_id INTEGER(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  parent_id INTEGER(11) NOT NULL DEFAULT '0',
  placeholder varchar(255) NOT NULL DEFAULT '',
  position INTEGER(11) NOT NULL DEFAULT '0',
  status INTEGER(11) NOT NULL DEFAULT '1',
  view_type varchar(255) NOT NULL DEFAULT '',
  zone_id INTEGER(11) NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_block_layout_status ON explayouts_block  ( layout_id, status );



  CREATE  INDEX idx_block_position ON explayouts_block  ( position );



  CREATE  INDEX idx_block_zone_status ON explayouts_block  ( zone_id, status );





CREATE TABLE explayouts_block_parameter (
  block_id INTEGER(11) NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  value text
);
  CREATE  INDEX idx_block_parameter_block ON explayouts_block_parameter  ( block_id );



  CREATE  UNIQUE INDEX idx_block_parameter_block_name ON explayouts_block_parameter  ( block_id, name );





CREATE TABLE explayouts_collection (
  block_id INTEGER(11) NOT NULL DEFAULT '0',
  collection_type varchar(255) NOT NULL DEFAULT 'manual',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  limit_value INTEGER(11) NOT NULL DEFAULT '0',
  offset_value INTEGER(11) NOT NULL DEFAULT '0',
  status INTEGER(11) NOT NULL DEFAULT '1'
);
  CREATE  UNIQUE INDEX idx_collection_block ON explayouts_collection  ( block_id );





CREATE TABLE explayouts_collection_item (
  collection_id INTEGER(11) NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  item_type varchar(255) NOT NULL DEFAULT 'manual',
  position INTEGER(11) NOT NULL DEFAULT '0',
  value_id INTEGER(11) NOT NULL DEFAULT '0',
  value_type varchar(255) NOT NULL DEFAULT 'ez_content'
);
  CREATE  INDEX idx_collection_item_collection_position ON explayouts_collection_item  ( collection_id, position );





CREATE TABLE explayouts_collection_query (
  collection_id INTEGER(11) NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parameters text,
  query_type varchar(255) NOT NULL DEFAULT ''
);
  CREATE  UNIQUE INDEX idx_collection_query_collection ON explayouts_collection_query  ( collection_id );





CREATE TABLE explayouts_layout (
  created INTEGER(11) NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_type varchar(255) NOT NULL DEFAULT '',
  modified INTEGER(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  status INTEGER(11) NOT NULL DEFAULT '1'
);
  CREATE  UNIQUE INDEX idx_layout_identifier_status ON explayouts_layout  ( identifier, status );



  CREATE  INDEX idx_layout_status ON explayouts_layout  ( status );





CREATE TABLE explayouts_rule (
  enabled INTEGER(11) NOT NULL DEFAULT '1',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  layout_id INTEGER(11) NOT NULL DEFAULT '0',
  priority INTEGER(11) NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_rule_enabled_priority ON explayouts_rule  ( enabled, priority );





CREATE TABLE explayouts_rule_condition (
  condition_type varchar(255) NOT NULL DEFAULT '',
  condition_value varchar(255) NOT NULL DEFAULT '',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  rule_id INTEGER(11) NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_rule_condition_rule ON explayouts_rule_condition  ( rule_id );





CREATE TABLE explayouts_rule_target (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  rule_id INTEGER(11) NOT NULL DEFAULT '0',
  target_type varchar(255) NOT NULL DEFAULT '',
  target_value varchar(255) NOT NULL DEFAULT ''
);
  CREATE  INDEX idx_rule_target_rule ON explayouts_rule_target  ( rule_id );





CREATE TABLE explayouts_zone (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_id INTEGER(11) NOT NULL DEFAULT '0',
  linked_layout_id INTEGER(11) DEFAULT NULL,
  position INTEGER(11) NOT NULL DEFAULT '0',
  status INTEGER(11) NOT NULL DEFAULT '1'
);
  CREATE  INDEX idx_zone_layout_status ON explayouts_zone  ( layout_id, status );



  CREATE  INDEX idx_zone_position ON explayouts_zone  ( position );





CREATE TABLE eztags (
  depth INTEGER(11) NOT NULL DEFAULT '1',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  keyword varchar(255) NOT NULL DEFAULT '',
  language_mask INTEGER(11) NOT NULL DEFAULT '0',
  main_language_id INTEGER(11) NOT NULL DEFAULT '0',
  main_tag_id INTEGER(11) NOT NULL DEFAULT '0',
  modified INTEGER(11) NOT NULL DEFAULT '0',
  parent_id INTEGER(11) NOT NULL DEFAULT '0',
  path_string varchar(255) NOT NULL DEFAULT '',
  remote_id varchar(100) NOT NULL DEFAULT ''
);
  CREATE  INDEX idx_eztags_keyword ON eztags  ( keyword );



  CREATE  INDEX idx_eztags_keyword_id ON eztags  ( keyword, id );



  CREATE  UNIQUE INDEX idx_eztags_remote_id ON eztags  ( remote_id );





CREATE TABLE eztags_attribute_link (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  keyword_id INTEGER(11) NOT NULL DEFAULT '0',
  object_id INTEGER(11) NOT NULL DEFAULT '0',
  objectattribute_id INTEGER(11) NOT NULL DEFAULT '0',
  objectattribute_version INTEGER(11) NOT NULL DEFAULT '0',
  priority INTEGER(11) NOT NULL DEFAULT '0'
);
  CREATE  INDEX idx_eztags_attr_link_keyword_id ON eztags_attribute_link  ( keyword_id );



  CREATE  INDEX idx_eztags_attr_link_kid_oaid_oav ON eztags_attribute_link  ( keyword_id, objectattribute_id, objectattribute_version );



  CREATE  INDEX idx_eztags_attr_link_kid_oid ON eztags_attribute_link  ( keyword_id, object_id );



  CREATE  INDEX idx_eztags_attr_link_oaid_oav ON eztags_attribute_link  ( objectattribute_id, objectattribute_version );





CREATE TABLE eztags_keyword (
  keyword varchar(255) NOT NULL DEFAULT '',
  keyword_id INTEGER(11) NOT NULL DEFAULT '0',
  language_id INTEGER(11) NOT NULL DEFAULT '0',
  locale varchar(255) NOT NULL DEFAULT '',
  status INTEGER(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( keyword_id, locale )
);


CREATE TABLE ezurl (
  created INTEGER(11) NOT NULL DEFAULT '0',
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  is_valid INTEGER(11) NOT NULL DEFAULT '1',
  last_checked INTEGER(11) NOT NULL DEFAULT '0',
  modified INTEGER(11) NOT NULL DEFAULT '0',
  original_url_md5 varchar(32) NOT NULL DEFAULT '',
  url longtext
);
  CREATE  INDEX ezurl_url ON ezurl  ( url );





CREATE TABLE ezurl_object_link (
  contentobject_attribute_id INTEGER(11) NOT NULL DEFAULT '0',
  contentobject_attribute_version INTEGER(11) NOT NULL DEFAULT '0',
  url_id INTEGER(11) NOT NULL DEFAULT '0'
);
  CREATE  INDEX ezurl_ol_coa_id ON ezurl_object_link  ( contentobject_attribute_id );



  CREATE  INDEX ezurl_ol_coa_version ON ezurl_object_link  ( contentobject_attribute_version );



  CREATE  INDEX ezurl_ol_url_id ON ezurl_object_link  ( url_id );



