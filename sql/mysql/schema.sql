CREATE TABLE IF NOT EXISTS explayouts_layout (
    id INT(11) NOT NULL AUTO_INCREMENT,
    identifier VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT '',
    layout_type VARCHAR(255) NOT NULL DEFAULT '',
    status INT(11) NOT NULL DEFAULT 1,
    created INT(11) NOT NULL DEFAULT 0,
    modified INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY unique_identifier_status (identifier, status),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_zone (
    id INT(11) NOT NULL AUTO_INCREMENT,
    layout_id INT(11) NOT NULL,
    identifier VARCHAR(255) NOT NULL,
    linked_layout_id INT(11) DEFAULT NULL,
    status INT(11) NOT NULL DEFAULT 1,
    position INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_layout_status (layout_id, status),
    KEY idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_block (
    id INT(11) NOT NULL AUTO_INCREMENT,
    zone_id INT(11) NOT NULL,
    layout_id INT(11) NOT NULL,
    position INT(11) NOT NULL DEFAULT 0,
    definition_identifier VARCHAR(255) NOT NULL,
    view_type VARCHAR(255) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_zone_status (zone_id, status),
    KEY idx_layout_status (layout_id, status),
    KEY idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_block_parameter (
    id INT(11) NOT NULL AUTO_INCREMENT,
    block_id INT(11) NOT NULL,
    name VARCHAR(255) NOT NULL,
    value LONGTEXT,
    PRIMARY KEY (id),
    KEY idx_block (block_id),
    UNIQUE KEY unique_block_name (block_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_collection (
    id INT(11) NOT NULL AUTO_INCREMENT,
    block_id INT(11) NOT NULL,
    collection_type VARCHAR(255) NOT NULL DEFAULT 'manual',
    offset_value INT(11) NOT NULL DEFAULT 0,
    limit_value INT(11) NOT NULL DEFAULT 0,
    status INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_block (block_id),
    UNIQUE KEY unique_block (block_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_collection_item (
    id INT(11) NOT NULL AUTO_INCREMENT,
    collection_id INT(11) NOT NULL,
    position INT(11) NOT NULL DEFAULT 0,
    value_type VARCHAR(255) NOT NULL DEFAULT 'ez_content',
    value_id INT(11) NOT NULL,
    item_type VARCHAR(255) NOT NULL DEFAULT 'manual',
    PRIMARY KEY (id),
    KEY idx_collection_position (collection_id, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_collection_query (
    id INT(11) NOT NULL AUTO_INCREMENT,
    collection_id INT(11) NOT NULL,
    query_type VARCHAR(255) NOT NULL DEFAULT '',
    parameters LONGTEXT,
    PRIMARY KEY (id),
    UNIQUE KEY unique_collection (collection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_rule (
    id INT(11) NOT NULL AUTO_INCREMENT,
    layout_id INT(11) NOT NULL,
    priority INT(11) NOT NULL DEFAULT 0,
    enabled INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_enabled_priority (enabled, priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_rule_target (
    id INT(11) NOT NULL AUTO_INCREMENT,
    rule_id INT(11) NOT NULL,
    target_type VARCHAR(255) NOT NULL,
    target_value VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_rule (rule_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS explayouts_rule_condition (
    id INT(11) NOT NULL AUTO_INCREMENT,
    rule_id INT(11) NOT NULL,
    condition_type VARCHAR(255) NOT NULL,
    condition_value VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_rule (rule_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
