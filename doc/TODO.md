# explayouts TODO

- `expLayoutsResolver::resolve()` writes matched/unmatched rule information with `error_log()` on every request; switch to `eZDebug` and make it opt-in.
- `sql/pgsql/` and `sql/postgresql/` contain duplicate PostgreSQL schemas; keep one directory.
- The MongoDB backend (`sql/mongodb/schema.json`, `expLayoutsMongoInstaller`) is experimental and not exercised by the kernel database layer.
- `settings/admininterface.ini.append.php` also registers an "Authors" node view tab that is unrelated to layouts; it should move to its own extension.
- `design/standard/templates/pagelayout.tpl` still uses the legacy `$sevenx_layout` template variable name from before the sevenx to exp rename.
- Stray backup files (`extension.xml~`, `settings/admininterface.ini.append.php~`) should be removed from the tree.
- No automated tests; the `bin/php/diagnose.php` / `diagnose_db.php` scripts are the only verification tooling.
