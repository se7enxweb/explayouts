CREATE TABLE IF NOT EXISTS explayouts_layout (
    id SERIAL PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT '',
    layout_type VARCHAR(255) NOT NULL DEFAULT '',
    status INTEGER NOT NULL DEFAULT 1,
    created INTEGER NOT NULL DEFAULT 0,
    modified INTEGER NOT NULL DEFAULT 0,
    UNIQUE (identifier, status)
);

CREATE INDEX IF NOT EXISTS idx_layout_status ON explayouts_layout (status);

CREATE TABLE IF NOT EXISTS explayouts_zone (
    id SERIAL PRIMARY KEY,
    layout_id INTEGER NOT NULL,
    identifier VARCHAR(255) NOT NULL,
    linked_layout_id INTEGER DEFAULT NULL,
    status INTEGER NOT NULL DEFAULT 1,
    position INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_zone_layout_status ON explayouts_zone (layout_id, status);
CREATE INDEX IF NOT EXISTS idx_zone_position ON explayouts_zone (position);

CREATE TABLE IF NOT EXISTS explayouts_block (
    id SERIAL PRIMARY KEY,
    zone_id INTEGER NOT NULL,
    layout_id INTEGER NOT NULL,
    position INTEGER NOT NULL DEFAULT 0,
    definition_identifier VARCHAR(255) NOT NULL,
    view_type VARCHAR(255) NOT NULL DEFAULT '',
    name VARCHAR(255) NOT NULL DEFAULT '',
    status INTEGER NOT NULL DEFAULT 1
);

CREATE INDEX IF NOT EXISTS idx_block_zone_status ON explayouts_block (zone_id, status);
CREATE INDEX IF NOT EXISTS idx_block_layout_status ON explayouts_block (layout_id, status);
CREATE INDEX IF NOT EXISTS idx_block_position ON explayouts_block (position);

CREATE TABLE IF NOT EXISTS explayouts_block_parameter (
    id SERIAL PRIMARY KEY,
    block_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    value TEXT,
    UNIQUE (block_id, name)
);

CREATE INDEX IF NOT EXISTS idx_parameter_block ON explayouts_block_parameter (block_id);

CREATE TABLE IF NOT EXISTS explayouts_collection (
    id SERIAL PRIMARY KEY,
    block_id INTEGER NOT NULL,
    collection_type VARCHAR(255) NOT NULL DEFAULT 'manual',
    offset_value INTEGER NOT NULL DEFAULT 0,
    limit_value INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 1,
    UNIQUE (block_id)
);

CREATE INDEX IF NOT EXISTS idx_collection_block ON explayouts_collection (block_id);

CREATE TABLE IF NOT EXISTS explayouts_collection_item (
    id SERIAL PRIMARY KEY,
    collection_id INTEGER NOT NULL,
    position INTEGER NOT NULL DEFAULT 0,
    value_type VARCHAR(255) NOT NULL DEFAULT 'ez_content',
    value_id INTEGER NOT NULL,
    item_type VARCHAR(255) NOT NULL DEFAULT 'manual'
);

CREATE INDEX IF NOT EXISTS idx_collection_item_position ON explayouts_collection_item (collection_id, position);

CREATE TABLE IF NOT EXISTS explayouts_collection_query (
    id SERIAL PRIMARY KEY,
    collection_id INTEGER NOT NULL,
    query_type VARCHAR(255) NOT NULL DEFAULT '',
    parameters TEXT,
    UNIQUE (collection_id)
);

CREATE TABLE IF NOT EXISTS explayouts_rule (
    id SERIAL PRIMARY KEY,
    layout_id INTEGER NOT NULL,
    priority INTEGER NOT NULL DEFAULT 0,
    enabled INTEGER NOT NULL DEFAULT 1
);

CREATE INDEX IF NOT EXISTS idx_rule_enabled_priority ON explayouts_rule (enabled, priority);

CREATE TABLE IF NOT EXISTS explayouts_rule_target (
    id SERIAL PRIMARY KEY,
    rule_id INTEGER NOT NULL,
    target_type VARCHAR(255) NOT NULL,
    target_value VARCHAR(255) NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_rule_target_rule ON explayouts_rule_target (rule_id);

CREATE TABLE IF NOT EXISTS explayouts_rule_condition (
    id SERIAL PRIMARY KEY,
    rule_id INTEGER NOT NULL,
    condition_type VARCHAR(255) NOT NULL,
    condition_value VARCHAR(255) NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_rule_condition_rule ON explayouts_rule_condition (rule_id);
