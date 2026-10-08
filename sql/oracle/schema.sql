-- Oracle schema for the Exponential Layouts (explayouts) extension.
-- Compatible with Oracle 11g / 12c / 19c and later.
-- Uses NUMBER for integer storage, CLOB for large text/JSON values,
-- and a sequence + trigger pair per table for id auto-increment behaviour,
-- named like the ezoracle schema handler names them (se_<table>, <table>_id_tr),
-- so eZOracleDB::lastSerialID() finds the sequence.
-- Read by eZDBInterface::insertFile(), which splits at ";" + newline: each
-- trigger is one line ending in END;; so that END; is kept. Text columns are
-- nullable because Oracle stores an empty string as NULL.

-- Layouts
CREATE TABLE explayouts_layout (
    id NUMBER(11,0) NOT NULL,
    identifier VARCHAR2(255),
    name VARCHAR2(255),
    layout_type VARCHAR2(255),
    shared NUMBER(11,0) DEFAULT 0 NOT NULL,
    status NUMBER(11,0) DEFAULT 1 NOT NULL,
    created NUMBER(11,0) DEFAULT 0 NOT NULL,
    modified NUMBER(11,0) DEFAULT 0 NOT NULL,
    CONSTRAINT explayouts_layout_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_layout_uk_identifier_status UNIQUE (identifier, status)
);

CREATE INDEX explayouts_layout_idx_status ON explayouts_layout(status);

CREATE SEQUENCE se_explayouts_layout START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_layout_id_tr BEFORE INSERT ON explayouts_layout FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_layout.nextval INTO :new.id FROM dual; END;;

-- Zones
CREATE TABLE explayouts_zone (
    id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) NOT NULL,
    identifier VARCHAR2(255),
    linked_layout_id NUMBER(11,0),
    linked_zone_identifier VARCHAR2(255),
    status NUMBER(11,0) DEFAULT 1 NOT NULL,
    position NUMBER(11,0) DEFAULT 0 NOT NULL,
    CONSTRAINT explayouts_zone_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_zone_idx_layout_status ON explayouts_zone(layout_id, status);
CREATE INDEX explayouts_zone_idx_position ON explayouts_zone(position);

CREATE SEQUENCE se_explayouts_zone START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_zone_id_tr BEFORE INSERT ON explayouts_zone FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_zone.nextval INTO :new.id FROM dual; END;;

-- Blocks
CREATE TABLE explayouts_block (
    id NUMBER(11,0) NOT NULL,
    zone_id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) NOT NULL,
    position NUMBER(11,0) DEFAULT 0 NOT NULL,
    definition_identifier VARCHAR2(255),
    view_type VARCHAR2(255),
    name VARCHAR2(255),
    status NUMBER(11,0) DEFAULT 1 NOT NULL,
    parent_id NUMBER(11,0) DEFAULT 0 NOT NULL,
    placeholder VARCHAR2(255),
    item_view_type CLOB,
    CONSTRAINT explayouts_block_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_block_idx_zone_status ON explayouts_block(zone_id, status);
CREATE INDEX explayouts_block_idx_layout_status ON explayouts_block(layout_id, status);
CREATE INDEX explayouts_block_idx_position ON explayouts_block(position);

CREATE SEQUENCE se_explayouts_block START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_block_id_tr BEFORE INSERT ON explayouts_block FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_block.nextval INTO :new.id FROM dual; END;;

-- Block parameters
CREATE TABLE explayouts_block_parameter (
    id NUMBER(11,0) NOT NULL,
    block_id NUMBER(11,0) NOT NULL,
    name VARCHAR2(255),
    value CLOB,
    CONSTRAINT explayouts_block_parameter_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_block_parameter_uk_block_name UNIQUE (block_id, name)
);

CREATE INDEX explayouts_block_parameter_idx_block ON explayouts_block_parameter(block_id);

CREATE SEQUENCE se_explayouts_block_parameter START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_block_para_0b755_tr BEFORE INSERT ON explayouts_block_parameter FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_block_parameter.nextval INTO :new.id FROM dual; END;;

-- Collections
CREATE TABLE explayouts_collection (
    id NUMBER(11,0) NOT NULL,
    block_id NUMBER(11,0) NOT NULL,
    collection_type VARCHAR2(255) DEFAULT 'manual',
    offset_value NUMBER(11,0) DEFAULT 0 NOT NULL,
    limit_value NUMBER(11,0) DEFAULT 0 NOT NULL,
    status NUMBER(11,0) DEFAULT 1 NOT NULL,
    CONSTRAINT explayouts_collection_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_collection_uk_block UNIQUE (block_id)
);

-- explayouts_collection_idx_block: the UNIQUE constraint above indexes block_id already

CREATE SEQUENCE se_explayouts_collection START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_collection_id_tr BEFORE INSERT ON explayouts_collection FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_collection.nextval INTO :new.id FROM dual; END;;

-- Collection items
CREATE TABLE explayouts_collection_item (
    id NUMBER(11,0) NOT NULL,
    collection_id NUMBER(11,0) NOT NULL,
    position NUMBER(11,0) DEFAULT 0 NOT NULL,
    value_type VARCHAR2(255) DEFAULT 'ez_content',
    value_id NUMBER(11,0) NOT NULL,
    item_type VARCHAR2(255) DEFAULT 'manual',
    CONSTRAINT explayouts_collection_item_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_collection_item_idx_collection_position ON explayouts_collection_item(collection_id, position);

CREATE SEQUENCE se_explayouts_collection_item START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_collection_23a3f_tr BEFORE INSERT ON explayouts_collection_item FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_collection_item.nextval INTO :new.id FROM dual; END;;

-- Collection queries
CREATE TABLE explayouts_collection_query (
    id NUMBER(11,0) NOT NULL,
    collection_id NUMBER(11,0) NOT NULL,
    query_type VARCHAR2(255),
    parameters CLOB,
    CONSTRAINT explayouts_collection_query_pk PRIMARY KEY (id),
    CONSTRAINT explayouts_collection_query_uk_collection UNIQUE (collection_id)
);

-- explayouts_collection_query_idx_collection: the UNIQUE constraint above indexes collection_id already

CREATE SEQUENCE se_explayouts_collection_query START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_collection_07110_tr BEFORE INSERT ON explayouts_collection_query FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_collection_query.nextval INTO :new.id FROM dual; END;;

-- Rules
CREATE TABLE explayouts_rule (
    id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) NOT NULL,
    priority NUMBER(11,0) DEFAULT 0 NOT NULL,
    enabled NUMBER(11,0) DEFAULT 1 NOT NULL,
    CONSTRAINT explayouts_rule_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_rule_idx_enabled_priority ON explayouts_rule(enabled, priority);

CREATE SEQUENCE se_explayouts_rule START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_rule_id_tr BEFORE INSERT ON explayouts_rule FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_rule.nextval INTO :new.id FROM dual; END;;

-- Rule targets
CREATE TABLE explayouts_rule_target (
    id NUMBER(11,0) NOT NULL,
    rule_id NUMBER(11,0) NOT NULL,
    target_type VARCHAR2(255),
    target_value VARCHAR2(255),
    CONSTRAINT explayouts_rule_target_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_rule_target_idx_rule ON explayouts_rule_target(rule_id);

CREATE SEQUENCE se_explayouts_rule_target START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_rule_target_id_tr BEFORE INSERT ON explayouts_rule_target FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_rule_target.nextval INTO :new.id FROM dual; END;;

-- Rule conditions
CREATE TABLE explayouts_rule_condition (
    id NUMBER(11,0) NOT NULL,
    rule_id NUMBER(11,0) NOT NULL,
    condition_type VARCHAR2(255),
    condition_value VARCHAR2(255),
    CONSTRAINT explayouts_rule_condition_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_rule_condition_idx_rule ON explayouts_rule_condition(rule_id);

CREATE SEQUENCE se_explayouts_rule_condition START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_rule_condi_95fba_tr BEFORE INSERT ON explayouts_rule_condition FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_rule_condition.nextval INTO :new.id FROM dual; END;;

-- Collected information from the site bundle contact forms
-- (expLayoutsSiteBundleInfoCollection::submit)
CREATE TABLE exp_info_collection (
    id NUMBER(11,0) NOT NULL,
    contentobject_id NUMBER(11,0) DEFAULT 0 NOT NULL,
    data CLOB,
    created NUMBER(11,0) DEFAULT 0 NOT NULL,
    CONSTRAINT exp_info_collection_pk PRIMARY KEY (id)
);

CREATE INDEX exp_info_collection_idx_object ON exp_info_collection(contentobject_id);

CREATE SEQUENCE se_exp_info_collection START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER exp_info_collection_id_tr BEFORE INSERT ON exp_info_collection FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_exp_info_collection.nextval INTO :new.id FROM dual; END;;

CREATE TABLE explayouts_share (
    id NUMBER(11,0) NOT NULL,
    layout_id NUMBER(11,0) DEFAULT 0 NOT NULL,
    token VARCHAR2(64),
    created NUMBER(11,0) DEFAULT 0 NOT NULL,
    CONSTRAINT explayouts_share_pk PRIMARY KEY (id)
);

CREATE INDEX explayouts_share_idx_layout ON explayouts_share(layout_id);
CREATE UNIQUE INDEX explayouts_share_idx_token ON explayouts_share(token);

CREATE SEQUENCE se_explayouts_share START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER explayouts_share_id_tr BEFORE INSERT ON explayouts_share FOR EACH ROW WHEN (new.id IS NULL) BEGIN SELECT se_explayouts_share.nextval INTO :new.id FROM dual; END;;
