# Installing explayouts

## Requirements

- Exponential Legacy / Exponential 6 (eZ Publish legacy kernel), PHP 8.1+
- MySQL/MariaDB, SQLite or PostgreSQL

## 1. Put the extension in place

The extension lives in:

```
extension/explayouts
```

## 2. Activate

Add it to the active extensions, either globally in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=explayouts
```

or per siteaccess in `settings/siteaccess/<access>/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveAccessExtensions[]=explayouts
```

## 3. Install the database schema

Import the schema for your database from the extension's `sql/` directory, e.g. for MySQL `sql/mysql/schema.sql` (SQLite: `sql/sqlite/schema.sql`, PostgreSQL: `sql/pgsql/schema.sql`). This creates the `explayouts_*` tables (layout, zone, block, block_parameter, collection, collection_item, collection_query, rule, rule_target, rule_condition).

Optionally seed base data:

```bash
php extension/explayouts/bin/php/install_base_data.php
```

## 4. Regenerate autoloads and clear caches

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --purge --allow-root-user
```

## 5. Settings shipped with the extension

- `settings/explayouts.ini.append.php` — block definitions (`BlockSettings`, `BlockDefinition_*`), layout types (`LayoutType_*`, `Zone_*`) and resolver settings (`ResolverSettings/DefaultLayout`). Override in `settings/override/explayouts.ini.append.php` to customize.
- `settings/module.ini.append.php` — registers the `explayouts` module.
- `settings/design.ini.append.php` — registers the design extension (admin + frontend templates).
- `settings/menu.ini.append.php` — adds the "Exponential Layouts" admin menu tab.
- `settings/admininterface.ini.append.php` — adds the "Layouts" tab to the admin node view.

## Sibling extensions

`explayouts` works standalone for rendering and resolving. For the full suite also activate:

- `explayouts_core` — service layer used by the admin UIs
- `explayouts_standard` — additional standard block handler set
- `explayouts_ui` — legacy admin module and assets
- `explayouts_ui_api` — modern SPA admin app and JSON API
- `explayouts_api` — content adapters for pickers/integration
