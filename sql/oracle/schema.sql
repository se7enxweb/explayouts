-- Oracle schema for the Exponential Layouts (explayouts) extension.
-- Compatible with Oracle 11g / 12c / 19c and later.
-- Uses NUMBER for integer storage, CLOB for large text/JSON values,
-- and a sequence + trigger pair per table for id auto-increment behaviour.

-- Layouts
CREATE TABLE explayouts_layout (
    id NUMBER(11,0) NOT NULL,
    identifier VARCHAR2(255) NOT NULL,
    name VARCHAR2(255) NOT NULL DEFAULT '',
    layout_type VARCHAR2(255) NOT NULL DEFAULT '',
    status NUMBER(11,0) NOT NULL DEFAULT 1,
    created NUMBER(11,0) NOT NULL DEFAULT 0,
    modified NUMBER(11,0) NOT NULL DEFAULT 0,
    CONSTRAINT explayouts_layout_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_layout_uk_identifier_status UNIQUE (identifier, status)
);

CREATE INDEX explayouts_layout_idx_status ON explayouts_layout(status);

CREATE SEQUENCE explayouts_layout_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_layout_id_trg
BEFORE INSERT ON explayouts_layout
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_layout_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Zones
CREATE TABLE explayouts_zone (
    id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) NOT NULL,
    identifier VARCHAR2(255) NOT NULL,
    linked_layout_id NUMBER(11,0) DEFAULT NULL,
    status NUMBER(11,0) NOT NULL DEFAULT 1,
    position NUMBER(11,0) NOT NULL DEFAULT 0,
    CONSTRAINT explayouts_zone_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_zone_idx_layout_status ON explayouts_zone(layout_id, status);
CREATE INDEX explayouts_zone_idx_position ON explayouts_zone(position);

CREATE SEQUENCE explayouts_zone_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_zone_id_trg
BEFORE INSERT ON explayouts_zone
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_zone_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Blocks
CREATE TABLE explayouts_block (
    id NUMBER(11,0) NOT NULL,
    zone_id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) NOT NULL,
    position NUMBER(11,0) NOT NULL DEFAULT 0,
    definition_identifier VARCHAR2(255) NOT NULL,
    view_type VARCHAR2(255) NOT NULL DEFAULT '',
    name VARCHAR2(255) NOT NULL DEFAULT '',
    status NUMBER(11,0) NOT NULL DEFAULT 1,
    parent_id NUMBER(11,0) NOT NULL DEFAULT 0,
    placeholder VARCHAR2(255) NOT NULL DEFAULT '',
    item_view_type CLOB,
    CONSTRAINT explayouts_block_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_block_idx_zone_status ON explayouts_block(zone_id, status);
CREATE INDEX explayouts_block_idx_layout_status ON explayouts_block(layout_id, status);
CREATE INDEX explayouts_block_idx_position ON explayouts_block(position);

CREATE SEQUENCE explayouts_block_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_block_id_trg
BEFORE INSERT ON explayouts_block
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_block_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Block parameters
CREATE TABLE explayouts_block_parameter (
    id NUMBER(11,0) NOT NULL,
    block_id NUMBER(11,0) NOT NULL,
    name VARCHAR2(255) NOT NULL,
    value CLOB,
    CONSTRAINT explayouts_block_parameter_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_block_parameter_uk_block_name UNIQUE (block_id, name)
);

CREATE INDEX explayouts_block_parameter_idx_block ON explayouts_block_parameter(block_id);

CREATE SEQUENCE explayouts_block_parameter_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_block_parameter_id_trg
BEFORE INSERT ON explayouts_block_parameter
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_block_parameter_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Collections
CREATE TABLE explayouts_collection (
    id NUMBER(11,0) NOT NULL,
    block_id NUMBER(11,0) NOT NULL,
    collection_type VARCHAR2(255) NOT NULL DEFAULT 'manual',
    offset_value NUMBER(11,0) NOT NULL DEFAULT 0,
    limit_value NUMBER(11,0) NOT NULL DEFAULT 0,
    status NUMBER(11,0) NOT NULL DEFAULT 1,
    CONSTRAINT explayouts_collection_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_collection_uk_block UNIQUE (block_id)
);

CREATE INDEX explayouts_collection_idx_block ON explayouts_collection(block_id);

CREATE SEQUENCE explayouts_collection_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_collection_id_trg
BEFORE INSERT ON explayouts_collection
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_collection_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Collection items
CREATE TABLE explayouts_collection_item (
    id NUMBER(11,0) NOT NULL,
    collection_id NUMBER(11,0) NOT NULL,
    position NUMBER(11,0) NOT NULL DEFAULT 0,
    value_type VARCHAR2(255) NOT NULL DEFAULT 'ez_content',
    value_id NUMBER(11,0) NOT NULL,
    item_type VARCHAR2(255) NOT NULL DEFAULT 'manual',
    CONSTRAINT explayouts_collection_item_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_collection_item_idx_collection_position ON explayouts_collection_item(collection_id, position);

CREATE SEQUENCE explayouts_collection_item_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_collection_item_id_trg
BEFORE INSERT ON explayouts_collection_item
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_collection_item_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Collection queries
CREATE TABLE explayouts_collection_query (
    id NUMBER(11,0) NOT NULL,
    collection_id NUMBER(11,0) NOT NULL,
    query_type VARCHAR2(255) NOT NULL DEFAULT '',
    parameters CLOB,
    CONSTRAINT explayouts_collection_query_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_collection_query_uk_collection UNIQUE (collection_id)
);

CREATE INDEX explayouts_collection_query_idx_collection ON explayouts_collection_query(collection_id);

CREATE SEQUENCE explayouts_collection_query_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_collection_query_id_trg
BEFORE INSERT ON explayouts_collection_query
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_collection_query_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Rules
CREATE TABLE explayouts_rule (
    id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) NOT NULL,
    priority NUMBER(11,0) NOT NULL DEFAULT 0,
    enabled NUMBER(11,0) NOT NULL DEFAULT 1,
    CONSTRAINT explayouts_rule_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_rule_idx_enabled_priority ON explayouts_rule(enabled, priority);

CREATE SEQUENCE explayouts_rule_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_rule_id_trg
BEFORE INSERT ON explayouts_rule
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_rule_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Rule targets
CREATE TABLE explayouts_rule_target (
    id NUMBER(11,0) NOT NULL,
    rule_id NUMBER(11,0) NOT NULL,
    target_type VARCHAR2(255) NOT NULL,
    target_value VARCHAR2(255) NOT NULL,
    CONSTRAINT explayouts_rule_target_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_rule_target_idx_rule ON explayouts_rule_target(rule_id);

CREATE SEQUENCE explayouts_rule_target_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_rule_target_id_trg
BEFORE INSERT ON explayouts_rule_target
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_rule_target_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Rule conditions
CREATE TABLE explayouts_rule_condition (
    id NUMBER(11,0) NOT NULL,
    rule_id NUMBER(11,0) NOT NULL,
    condition_type VARCHAR2(255) NOT NULL,
    condition_value VARCHAR2(255) NOT NULL,
    CONSTRAINT explayouts_rule_condition_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_rule_condition_idx_rule ON explayouts_rule_condition(rule_id);

CREATE SEQUENCE explayouts_rule_condition_id_seq START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_rule_condition_id_trg
BEFORE INSERT ON explayouts_rule_condition
FOR EACH ROW
WHEN (NEW.id IS NULL)
BEGIN
    SELECT explayouts_rule_condition_id_seq.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/
