CREATE TABLE explayouts_block (
  definition_identifier varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  item_view_type text NOT NULL,
  layout_id int NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  parent_id int NOT NULL DEFAULT '0',
  placeholder varchar(255) NOT NULL DEFAULT '',
  position int NOT NULL DEFAULT '0',
  status int NOT NULL DEFAULT '1',
  view_type varchar(255) NOT NULL DEFAULT '',
  zone_id int NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_block_layout_status ( layout_id, status ),
  KEY idx_block_position ( position ),
  KEY idx_block_zone_status ( zone_id, status )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_block_parameter (
  block_id int NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  value text,
  PRIMARY KEY ( id ),
  KEY idx_block_parameter_block ( block_id ),
  UNIQUE KEY idx_block_parameter_block_name ( block_id, name )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_collection (
  block_id int NOT NULL DEFAULT '0',
  collection_type varchar(255) NOT NULL DEFAULT 'manual',
  id int(11) NOT NULL AUTO_INCREMENT,
  limit_value int NOT NULL DEFAULT '0',
  offset_value int NOT NULL DEFAULT '0',
  status int NOT NULL DEFAULT '1',
  PRIMARY KEY ( id ),
  UNIQUE KEY idx_collection_block ( block_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_collection_item (
  collection_id int NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  item_type varchar(255) NOT NULL DEFAULT 'manual',
  position int NOT NULL DEFAULT '0',
  value_id int NOT NULL DEFAULT '0',
  value_type varchar(255) NOT NULL DEFAULT 'ez_content',
  PRIMARY KEY ( id ),
  KEY idx_collection_item_collection_position ( collection_id, position )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_collection_query (
  collection_id int NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  parameters text,
  query_type varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  UNIQUE KEY idx_collection_query_collection ( collection_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_layout (
  created int NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_type varchar(255) NOT NULL DEFAULT '',
  modified int NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  status int NOT NULL DEFAULT '1',
  PRIMARY KEY ( id ),
  KEY idx_layout_status ( status ),
  UNIQUE KEY idx_layout_identifier_status ( identifier, status )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_rule (
  enabled int NOT NULL DEFAULT '1',
  id int(11) NOT NULL AUTO_INCREMENT,
  layout_id int NOT NULL DEFAULT '0',
  priority int NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_rule_enabled_priority ( enabled, priority )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_rule_condition (
  condition_type varchar(255) NOT NULL DEFAULT '',
  condition_value varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  rule_id int NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY idx_rule_condition_rule ( rule_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_rule_target (
  id int(11) NOT NULL AUTO_INCREMENT,
  rule_id int NOT NULL DEFAULT '0',
  target_type varchar(255) NOT NULL DEFAULT '',
  target_value varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  KEY idx_rule_target_rule ( rule_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE explayouts_zone (
  id int(11) NOT NULL AUTO_INCREMENT,
  identifier varchar(255) NOT NULL DEFAULT '',
  layout_id int NOT NULL DEFAULT '0',
  linked_layout_id int DEFAULT NULL,
  position int NOT NULL DEFAULT '0',
  status int NOT NULL DEFAULT '1',
  PRIMARY KEY ( id ),
  KEY idx_zone_layout_status ( layout_id, status ),
  KEY idx_zone_position ( position )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;
