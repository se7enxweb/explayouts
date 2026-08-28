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
CREATE UNIQUE INDEX idx_layout_identifier_status ON explayouts_layout USING btree ( identifier, status );

CREATE INDEX idx_layout_status ON explayouts_layout USING btree ( status );

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



CREATE SEQUENCE IF NOT EXISTS eztags_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS eztags (
  depth integer DEFAULT 1 NOT NULL,
  id integer DEFAULT nextval('eztags_id_seq'::text) NOT NULL,
  keyword character varying(255) DEFAULT ''::character varying NOT NULL,
  language_mask integer DEFAULT 0 NOT NULL,
  main_language_id integer DEFAULT 0 NOT NULL,
  main_tag_id integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  parent_id integer DEFAULT 0 NOT NULL,
  path_string character varying(255) DEFAULT ''::character varying NOT NULL,
  remote_id character varying(100) DEFAULT ''::character varying NOT NULL
);
CREATE INDEX idx_eztags_keyword ON eztags USING btree ( keyword );

CREATE INDEX idx_eztags_keyword_id ON eztags USING btree ( keyword, id );

CREATE UNIQUE INDEX idx_eztags_remote_id ON eztags USING btree ( remote_id );

ALTER TABLE ONLY eztags ADD CONSTRAINT eztags_pkey PRIMARY KEY ( id );



CREATE SEQUENCE IF NOT EXISTS eztags_attribute_link_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS eztags_attribute_link (
  id integer DEFAULT nextval('eztags_attribute_link_id_seq'::text) NOT NULL,
  keyword_id integer DEFAULT 0 NOT NULL,
  object_id integer DEFAULT 0 NOT NULL,
  objectattribute_id integer DEFAULT 0 NOT NULL,
  objectattribute_version integer DEFAULT 0 NOT NULL,
  priority integer DEFAULT 0 NOT NULL
);
CREATE INDEX idx_eztags_attr_link_keyword_id ON eztags_attribute_link USING btree ( keyword_id );

CREATE INDEX idx_eztags_attr_link_kid_oaid_oav ON eztags_attribute_link USING btree ( keyword_id, objectattribute_id, objectattribute_version );

CREATE INDEX idx_eztags_attr_link_kid_oid ON eztags_attribute_link USING btree ( keyword_id, object_id );

CREATE INDEX idx_eztags_attr_link_oaid_oav ON eztags_attribute_link USING btree ( objectattribute_id, objectattribute_version );

ALTER TABLE ONLY eztags_attribute_link ADD CONSTRAINT eztags_attribute_link_pkey PRIMARY KEY ( id );




CREATE TABLE IF NOT EXISTS eztags_keyword (
  keyword character varying(255) DEFAULT ''::character varying NOT NULL,
  keyword_id integer DEFAULT 0 NOT NULL,
  language_id integer DEFAULT 0 NOT NULL,
  locale character varying(255) DEFAULT ''::character varying NOT NULL,
  status integer DEFAULT 0 NOT NULL
);

ALTER TABLE ONLY eztags_keyword ADD CONSTRAINT eztags_keyword_pkey PRIMARY KEY ( keyword_id, locale );



CREATE SEQUENCE IF NOT EXISTS ezurl_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS ezurl (
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezurl_id_seq'::text) NOT NULL,
  is_valid integer DEFAULT 1 NOT NULL,
  last_checked integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  original_url_md5 character varying(32) DEFAULT ''::character varying NOT NULL,
  url text
);
CREATE INDEX ezurl_url ON ezurl USING btree ( url );

ALTER TABLE ONLY ezurl ADD CONSTRAINT ezurl_pkey PRIMARY KEY ( id );




CREATE TABLE IF NOT EXISTS ezurl_object_link (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  contentobject_attribute_version integer DEFAULT 0 NOT NULL,
  url_id integer DEFAULT 0 NOT NULL
);
CREATE INDEX ezurl_ol_coa_id ON ezurl_object_link USING btree ( contentobject_attribute_id );

CREATE INDEX ezurl_ol_coa_version ON ezurl_object_link USING btree ( contentobject_attribute_version );

CREATE INDEX ezurl_ol_url_id ON ezurl_object_link USING btree ( url_id );


