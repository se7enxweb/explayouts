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

### Complete `[ExtensionSettings]` example (copy from here)

This is the complete, working `[ExtensionSettings]` block from a production
Exponential 6 install running the full Exponential Layouts suite, exactly as
it appears in `settings/override/site.ini.append.php`. The grouping and
order are deliberate and should be kept:

1. **Exponential / eZ base extensions first** — editor, JS core, datatypes
   and services the platform itself expects. Their INI and design layers
   load first so everything later can override them.
2. **Community / bc extensions second** — site tooling that builds on the
   base but is independent of Layouts.
3. **The Exponential Layouts suite LAST, grouped together** — `explayouts`
   (engine) before its UIs (`explayouts_ui`, `explayouts_ui_api`,
   `explayouts_content_browser_ui`), because with
   `ExtensionOrdering=enabled` later entries win INI/design overrides, and
   the UI extensions intentionally layer on top of the engine's defaults.
   Themes and site-specific extensions come after the suite for the same
   reason.

```ini
[ExtensionSettings]
# --- Exponential / eZ base ------------------------------------------------
ActiveExtensions[]=xrowmetadata
ActiveExtensions[]=ezjscore
ActiveExtensions[]=ezoe
ActiveExtensions[]=ezformtoken
ActiveExtensions[]=ezwt
ActiveExtensions[]=ezstarrating
ActiveExtensions[]=ezgmaplocation
ActiveExtensions[]=ezautosave
ActiveExtensions[]=ezodf
ActiveExtensions[]=ezie
ActiveExtensions[]=ezprestapi

# --- Community / bc site tooling -------------------------------------------
ActiveExtensions[]=ezpaypal
ActiveExtensions[]=owsimpleoperator
ActiveExtensions[]=swark
ActiveExtensions[]=bcgooglesitemaps
ActiveExtensions[]=bcwebsitestatistics
ActiveExtensions[]=bccie
ActiveExtensions[]=xrowextract
ActiveExtensions[]=enhancedezbinaryfile
ActiveExtensions[]=enhancedselection2
ActiveExtensions[]=bcwebshop
ActiveExtensions[]=ezwebin
ActiveExtensions[]=ezmultiupload
ActiveExtensions[]=ezprestapiprovider
ActiveExtensions[]=ezupdate
ActiveExtensions[]=git_manager
ActiveExtensions[]=expdse

# --- Exponential Layouts suite (keep grouped, engine before UIs) -----------
ActiveExtensions[]=explayouts
ActiveExtensions[]=explayouts_ui
ActiveExtensions[]=explayouts_ui_api
ActiveExtensions[]=explayouts_content_browser_ui

# --- Themes and site-specific extensions last ------------------------------
ActiveExtensions[]=sevenx_themes_simple
ActiveExtensions[]=expauthentication_2fa
```

Notes on this example:

- `explayouts_ui` pulls in the classic admin module; `explayouts_ui_api`
  serves the SPA admin app and its JSON API; `explayouts_content_browser_ui`
  provides the content picker those UIs open. Activating the UIs implicitly
  requires `explayouts` — never list a UI without the engine above it.
- `explayouts_core`, `explayouts_standard`, `explayouts_api` and the query
  extensions may additionally be listed (in the same suite group, after
  `explayouts`) when your project uses their services directly — see
  "Sibling extensions" below.
- Frontend-only theme extensions activated per siteaccess (for example a
  media theme via `ActiveAccessExtensions[]` in
  `settings/siteaccess/<access>/site.ini.append.php`) do NOT appear here;
  keep global stack concerns and per-siteaccess theme concerns separate.

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
