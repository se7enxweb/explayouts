CREATE TABLE IF NOT EXISTS explayouts_layout (
    id SERIAL PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT '',
    layout_type VARCHAR(255) NOT NULL DEFAULT '',
    status INT NOT NULL DEFAULT 1,
    created INT NOT NULL DEFAULT 0,
    modified INT NOT NULL DEFAULT 0,
    UNIQUE (identifier, status)
);
CREATE INDEX idx_layout_status ON explayouts_layout(status);

CREATE TABLE IF NOT EXISTS explayouts_zone (
    id SERIAL PRIMARY KEY,
    layout_id INT NOT NULL,
    identifier VARCHAR(255) NOT NULL,
    linked_layout_id INT DEFAULT NULL,
    status INT NOT NULL DEFAULT 1,
    position INT NOT NULL DEFAULT 0
);
CREATE INDEX idx_zone_layout_status ON explayouts_zone(layout_id, status);
CREATE INDEX idx_zone_position ON explayouts_zone(position);

CREATE TABLE IF NOT EXISTS explayouts_block (
    id SERIAL PRIMARY KEY,
    zone_id INT NOT NULL,
    layout_id INT NOT NULL,
    position INT NOT NULL DEFAULT 0,
    definition_identifier VARCHAR(255) NOT NULL,
    view_type VARCHAR(255) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status INT NOT NULL DEFAULT 1
);
CREATE INDEX idx_block_zone_status ON explayouts_block(zone_id, status);
CREATE INDEX idx_block_layout_status ON explayouts_block(layout_id, status);
CREATE INDEX idx_block_position ON explayouts_block(position);

CREATE TABLE IF NOT EXISTS explayouts_block_parameter (
    id SERIAL PRIMARY KEY,
    block_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    value TEXT,
    UNIQUE (block_id, name)
);
CREATE INDEX idx_block_parameter_block ON explayouts_block_parameter(block_id);

CREATE TABLE IF NOT EXISTS explayouts_collection (
    id SERIAL PRIMARY KEY,
    block_id INT NOT NULL,
    collection_type VARCHAR(255) NOT NULL DEFAULT 'manual',
    offset_value INT NOT NULL DEFAULT 0,
    limit_value INT NOT NULL DEFAULT 0,
    status INT NOT NULL DEFAULT 1,
    UNIQUE (block_id)
);
CREATE INDEX idx_collection_block ON explayouts_collection(block_id);

CREATE TABLE IF NOT EXISTS explayouts_collection_item (
    id SERIAL PRIMARY KEY,
    collection_id INT NOT NULL,
    position INT NOT NULL DEFAULT 0,
    value_type VARCHAR(255) NOT NULL DEFAULT 'ez_content',
    value_id INT NOT NULL,
    item_type VARCHAR(255) NOT NULL DEFAULT 'manual'
);
CREATE INDEX idx_collection_item_collection_position ON explayouts_collection_item(collection_id, position);

CREATE TABLE IF NOT EXISTS explayouts_collection_query (
    id SERIAL PRIMARY KEY,
    collection_id INT NOT NULL,
    query_type VARCHAR(255) NOT NULL DEFAULT '',
    parameters TEXT,
    UNIQUE (collection_id)
);

CREATE TABLE IF NOT EXISTS explayouts_rule (
    id SERIAL PRIMARY KEY,
    layout_id INT NOT NULL,
    priority INT NOT NULL DEFAULT 0,
    enabled INT NOT NULL DEFAULT 1
);
CREATE INDEX idx_rule_enabled_priority ON explayouts_rule(enabled, priority);

CREATE TABLE IF NOT EXISTS explayouts_rule_target (
    id SERIAL PRIMARY KEY,
    rule_id INT NOT NULL,
    target_type VARCHAR(255) NOT NULL,
    target_value VARCHAR(255) NOT NULL
);
CREATE INDEX idx_rule_target_rule ON explayouts_rule_target(rule_id);

CREATE TABLE IF NOT EXISTS explayouts_rule_condition (
    id SERIAL PRIMARY KEY,
    rule_id INT NOT NULL,
    condition_type VARCHAR(255) NOT NULL,
    condition_value VARCHAR(255) NOT NULL
);
CREATE INDEX idx_rule_condition_rule ON explayouts_rule_condition(rule_id);
