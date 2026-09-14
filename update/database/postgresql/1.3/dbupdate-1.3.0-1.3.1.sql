--
-- explayouts 1.3.0 to 1.3.1, PostgreSQL.
--
-- See the MySQL file of the same name for why. PostgreSQL can rename an index
-- in place, and will do nothing if there is none of the old name, so this is
-- safe to run more than once.
--

ALTER INDEX IF EXISTS contentobject_id RENAME TO exp_info_collection_object;
