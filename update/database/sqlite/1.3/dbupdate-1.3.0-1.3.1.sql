--
-- explayouts 1.3.0 to 1.3.1, SQLite.
--
-- See the MySQL file of the same name for why. SQLite keeps indexes as objects
-- of their own and takes IF EXISTS on both halves, so this is safe to run more
-- than once.
--

DROP INDEX IF EXISTS contentobject_id;
CREATE INDEX IF NOT EXISTS exp_info_collection_object ON exp_info_collection ( contentobject_id );
