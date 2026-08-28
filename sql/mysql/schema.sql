CREATE TABLE explayouts_block (
  definition_identifier varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  item_view_type text NOT NULL,
  layout_id int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  parent_id int(11) NOT NULL DEFAULT '0',
  placeholder varchar(255) NOT NULL DEFAULT '',
  position int(11) NOT NULL DEFAULT '0',
  status int(11) NOT NULL DEFAULT '1',
  view_type varchar(255) NOT NULL DEFAULT '',
  zone_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_block_layout_status ( layout_id, status ),
  KEY idx_block_position ( position ),
  KEY idx_block_zone_status ( zone_id, status )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_block_parameter (
  block_id int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  value text,
  PRIMARY KEY ( id ),
  KEY idx_block_parameter_block ( block_id ),
  UNIQUE KEY idx_block_parameter_block_name ( block_id, name )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_collection (
  block_id int(11) NOT NULL DEFAULT '0',
  collection_type varchar(255) NOT NULL DEFAULT 'manual',
  id int(11) NOT NULL AUTO_INCREMENT,
  limit_value int(11) NOT NULL DEFAULT '0',
  offset_value int(11) NOT NULL DEFAULT '0',
  status int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY ( id ),
  UNIQUE KEY idx_collection_block ( block_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_collection_item (
  collection_id int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  item_type varchar(255) NOT NULL DEFAULT 'manual',
  position int(11) NOT NULL DEFAULT '0',
  value_id int(11) NOT NULL DEFAULT '0',
  value_type varchar(255) NOT NULL DEFAULT 'ez_content',
  PRIMARY KEY ( id ),
  KEY idx_collection_item_collection_position ( collection_id, position )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_collection_query (
  collection_id int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  parameters text,
  query_type varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  UNIQUE KEY idx_collection_query_collection ( collection_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_layout (
  created int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_type varchar(255) NOT NULL DEFAULT '',
  modified int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  status int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY ( id ),
  UNIQUE KEY idx_layout_identifier_status ( identifier, status ),
  KEY idx_layout_status ( status )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_rule (
  enabled int(11) NOT NULL DEFAULT '1',
  id int(11) NOT NULL AUTO_INCREMENT,
  layout_id int(11) NOT NULL DEFAULT '0',
  priority int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_rule_enabled_priority ( enabled, priority )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_rule_condition (
  condition_type varchar(255) NOT NULL DEFAULT '',
  condition_value varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  rule_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_rule_condition_rule ( rule_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_rule_target (
  id int(11) NOT NULL AUTO_INCREMENT,
  rule_id int(11) NOT NULL DEFAULT '0',
  target_type varchar(255) NOT NULL DEFAULT '',
  target_value varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  KEY idx_rule_target_rule ( rule_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_zone (
  id int(11) NOT NULL AUTO_INCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_id int(11) NOT NULL DEFAULT '0',
  linked_layout_id int(11) DEFAULT NULL,
  position int(11) NOT NULL DEFAULT '0',
  status int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY ( id ),
  KEY idx_zone_layout_status ( layout_id, status ),
  KEY idx_zone_position ( position )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE eztags (
  depth int(11) NOT NULL DEFAULT '1',
  id int(11) NOT NULL AUTO_INCREMENT,
  keyword varchar(255) NOT NULL DEFAULT '',
  language_mask int(11) NOT NULL DEFAULT '0',
  main_language_id int(11) NOT NULL DEFAULT '0',
  main_tag_id int(11) NOT NULL DEFAULT '0',
  modified int(11) NOT NULL DEFAULT '0',
  parent_id int(11) NOT NULL DEFAULT '0',
  path_string varchar(255) NOT NULL DEFAULT '',
  remote_id varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  KEY idx_eztags_keyword ( keyword ),
  KEY idx_eztags_keyword_id ( keyword, id ),
  UNIQUE KEY idx_eztags_remote_id ( remote_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE eztags_attribute_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  keyword_id int(11) NOT NULL DEFAULT '0',
  object_id int(11) NOT NULL DEFAULT '0',
  objectattribute_id int(11) NOT NULL DEFAULT '0',
  objectattribute_version int(11) NOT NULL DEFAULT '0',
  priority int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_eztags_attr_link_keyword_id ( keyword_id ),
  KEY idx_eztags_attr_link_kid_oaid_oav ( keyword_id, objectattribute_id, objectattribute_version ),
  KEY idx_eztags_attr_link_kid_oid ( keyword_id, object_id ),
  KEY idx_eztags_attr_link_oaid_oav ( objectattribute_id, objectattribute_version )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE eztags_keyword (
  keyword varchar(255) NOT NULL DEFAULT '',
  keyword_id int(11) NOT NULL DEFAULT '0',
  language_id int(11) NOT NULL DEFAULT '0',
  locale varchar(255) NOT NULL DEFAULT '',
  status int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( keyword_id, locale )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE ezurl (
  created int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  is_valid int(11) NOT NULL DEFAULT '1',
  last_checked int(11) NOT NULL DEFAULT '0',
  modified int(11) NOT NULL DEFAULT '0',
  original_url_md5 varchar(32) NOT NULL DEFAULT '',
  url longtext,
  PRIMARY KEY ( id ),
  KEY ezurl_url ( url( 255 ) )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE ezurl_object_link (
  contentobject_attribute_id int(11) NOT NULL DEFAULT '0',
  contentobject_attribute_version int(11) NOT NULL DEFAULT '0',
  url_id int(11) NOT NULL DEFAULT '0',
  KEY ezurl_ol_coa_id ( contentobject_attribute_id ),
  KEY ezurl_ol_coa_version ( contentobject_attribute_version ),
  KEY ezurl_ol_url_id ( url_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;
