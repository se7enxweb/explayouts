CREATE SEQUENCE IF NOT EXISTS explayouts_block_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_block (
  definition_identifier character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('explayouts_block_id_seq'::text) NOT NULL,
  item_view_type text NOT NULL,
  layout_id integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  parent_id integer DEFAULT 0 NOT NULL,
  placeholder character varying(255) DEFAULT ''::character varying NOT NULL,
  "position" integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 1 NOT NULL,
  view_type character varying(255) DEFAULT ''::character varying NOT NULL,
  zone_id integer DEFAULT 0 NOT NULL
);
CREATE INDEX idx_block_layout_status ON explayouts_block USING btree ( layout_id, status );

CREATE INDEX idx_block_position ON explayouts_block USING btree ( "position" );

CREATE INDEX idx_block_zone_status ON explayouts_block USING btree ( zone_id, status );

ALTER TABLE ONLY explayouts_block ADD CONSTRAINT explayouts_block_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_block_parameter_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_block_parameter (
  block_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('explayouts_block_parameter_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  value text
);
CREATE INDEX idx_block_parameter_block ON explayouts_block_parameter USING btree ( block_id );

CREATE UNIQUE INDEX idx_block_parameter_block_name ON explayouts_block_parameter USING btree ( block_id, name );

ALTER TABLE ONLY explayouts_block_parameter ADD CONSTRAINT explayouts_block_parameter_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_collection_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_collection (
  block_id integer DEFAULT 0 NOT NULL,
  collection_type character varying(255) DEFAULT 'manual'::character varying NOT NULL,
  id integer DEFAULT nextval('explayouts_collection_id_seq'::text) NOT NULL,
  limit_value integer DEFAULT 0 NOT NULL,
  offset_value integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 1 NOT NULL
);
CREATE UNIQUE INDEX idx_collection_block ON explayouts_collection USING btree ( block_id );

ALTER TABLE ONLY explayouts_collection ADD CONSTRAINT explayouts_collection_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_collection_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_collection_item (
  collection_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('explayouts_collection_item_id_seq'::text) NOT NULL,
  item_type character varying(255) DEFAULT 'manual'::character varying NOT NULL,
  "position" integer DEFAULT 0 NOT NULL,
  value_id integer DEFAULT 0 NOT NULL,
  value_type character varying(255) DEFAULT 'ez_content'::character varying NOT NULL
);
CREATE INDEX idx_collection_item_collection_position ON explayouts_collection_item USING btree ( collection_id, "position" );

ALTER TABLE ONLY explayouts_collection_item ADD CONSTRAINT explayouts_collection_item_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_collection_query_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_collection_query (
  collection_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('explayouts_collection_query_id_seq'::text) NOT NULL,
  parameters text,
  query_type character varying(255) DEFAULT ''::character varying NOT NULL
);
CREATE UNIQUE INDEX idx_collection_query_collection ON explayouts_collection_query USING btree ( collection_id );

ALTER TABLE ONLY explayouts_collection_query ADD CONSTRAINT explayouts_collection_query_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_layout_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_layout (
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('explayouts_layout_id_seq'::text) NOT NULL,
  identifier character varying(255) DEFAULT ''::character varying NOT NULL,
  layout_type character varying(255) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  status integer DEFAULT 1 NOT NULL
);
CREATE INDEX idx_layout_status ON explayouts_layout USING btree ( status );

CREATE UNIQUE INDEX idx_layout_identifier_status ON explayouts_layout USING btree ( identifier, status );

ALTER TABLE ONLY explayouts_layout ADD CONSTRAINT explayouts_layout_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_rule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_rule (
  enabled integer DEFAULT 1 NOT NULL,
  id integer DEFAULT nextval('explayouts_rule_id_seq'::text) NOT NULL,
  layout_id integer DEFAULT 0 NOT NULL,
  priority integer DEFAULT 0 NOT NULL
);
CREATE INDEX idx_rule_enabled_priority ON explayouts_rule USING btree ( enabled, priority );

ALTER TABLE ONLY explayouts_rule ADD CONSTRAINT explayouts_rule_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_rule_condition_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_rule_condition (
  condition_type character varying(255) DEFAULT ''::character varying NOT NULL,
  condition_value character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('explayouts_rule_condition_id_seq'::text) NOT NULL,
  rule_id integer DEFAULT 0 NOT NULL
);
CREATE INDEX idx_rule_condition_rule ON explayouts_rule_condition USING btree ( rule_id );

ALTER TABLE ONLY explayouts_rule_condition ADD CONSTRAINT explayouts_rule_condition_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_rule_target_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_rule_target (
  id integer DEFAULT nextval('explayouts_rule_target_id_seq'::text) NOT NULL,
  rule_id integer DEFAULT 0 NOT NULL,
  target_type character varying(255) DEFAULT ''::character varying NOT NULL,
  target_value character varying(255) DEFAULT ''::character varying NOT NULL
);
CREATE INDEX idx_rule_target_rule ON explayouts_rule_target USING btree ( rule_id );

ALTER TABLE ONLY explayouts_rule_target ADD CONSTRAINT explayouts_rule_target_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS explayouts_zone_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS explayouts_zone (
  id integer DEFAULT nextval('explayouts_zone_id_seq'::text) NOT NULL,
  identifier character varying(255) DEFAULT ''::character varying NOT NULL,
  layout_id integer DEFAULT 0 NOT NULL,
  linked_layout_id integer DEFAULT NULL,
  "position" integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 1 NOT NULL
);
CREATE INDEX idx_zone_layout_status ON explayouts_zone USING btree ( layout_id, status );

CREATE INDEX idx_zone_position ON explayouts_zone USING btree ( "position" );

ALTER TABLE ONLY explayouts_zone ADD CONSTRAINT explayouts_zone_pkey PRIMARY KEY ( id );

