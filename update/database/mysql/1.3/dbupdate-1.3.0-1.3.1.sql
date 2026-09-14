--
-- explayouts 1.3.0 to 1.3.1, MySQL.
--
-- The lookup index on exp_info_collection carries the name of the column it
-- covers on installations created before the schema files named it properly.
-- Every schema file this extension ships - mysql, postgresql and sqlite - has
-- called it exp_info_collection_object for some time, so a fresh installation
-- gets that name and an older one keeps the column name.
--
-- The difference is harmless to work with and impossible to ignore: Setup >
-- System Upgrade > Database consistency check compares the live database
-- against the schema every active extension declares, and reports it on every
-- run, on every installation that predates the change.
--
-- Nothing is added or removed here. The same column is indexed, under the name
-- the schema has always meant. Run once: a second run fails on the first
-- statement, because by then there is no index of the old name to drop.
--

ALTER TABLE exp_info_collection DROP INDEX contentobject_id;
ALTER TABLE exp_info_collection ADD INDEX exp_info_collection_object ( contentobject_id );
