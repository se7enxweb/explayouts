# explayouts

Exponential Layouts is a visual page builder for Exponential Legacy / Exponential 6. It is the main integration extension of the Exponential Layouts suite: it owns the `explayouts_*` database tables, renders layouts, zones and blocks on the frontend, resolves which layout applies to a request via mapping rules, and adds a "Layouts" tab to the admin content view.

Exponential Legacy port inspired by the `netgen/layouts-ibexa` package. Instead of Symfony services and Doctrine, it uses `eZPersistentObject` value classes, INI-driven block/layout type definitions (`explayouts.ini`) and legacy module views.

## Key classes

| Class | Purpose |
|-------|---------|
| `expLayoutsLayout`, `expLayoutsZone`, `expLayoutsBlock`, `expLayoutsBlockParameter` | Persistent value objects for layouts, zones, blocks and block parameters |
| `expLayoutsCollection`, `expLayoutsCollectionItem` | Block collections and their items (manual and dynamic) |
| `expLayoutsRule`, `expLayoutsRuleTarget`, `expLayoutsRuleCondition` | Layout resolver rules, targets and conditions |
| `expLayoutsResolver` | Resolves the layout for a request path or node (`resolve()`) |
| `expLayoutsRenderer` | Prepares a layout/zone/block tree for template rendering (`prepareLayout()`) |
| `expLayoutsBlockHandlerFactory` | Instantiates block handlers from `explayouts.ini` `BlockDefinition_*` sections |
| `expLayoutsQueryHandlerFactory` | Instantiates dynamic collection query handlers (children, parent, subtree, siblings, latest, random, manual) |
| `expLayoutsLayoutType` | Reads `LayoutType_*` definitions and their zones from `explayouts.ini` |
| `expLayoutsImporter`, `expLayoutsExporter` | Transfer (import/export) of layouts and rules |
| `expLayoutsInstall`, `expLayoutsFixtures` | Schema installation and demo data |

Around 25 built-in block handlers ship with the extension (`expLayoutsTextBlockHandler`, `expLayoutsTitleBlockHandler`, `expLayoutsHtmlBlockHandler`, `expLayoutsImageBlockHandler`, `expLayoutsListBlockHandler`, `expLayoutsSingleBlockHandler`, `expLayoutsButtonBlockHandler`, `expLayoutsCardBlockHandler`, `expLayoutsGridBlockHandler`, `expLayoutsGalleryBlockHandler`, `expLayoutsCarouselBlockHandler`, `expLayoutsMapBlockHandler`, `expLayoutsVideoBlockHandler` and more), all implementing `expLayoutsBlockHandlerInterface`.

## What else is included

- `modules/explayouts` — legacy admin module: dashboard, layout list/edit, block edit, rule list/edit, preview, setup, template editor.
- Template fetch functions: `layout`, `resolve_layout`, `resolve_layout_for_node`, `rules_for_node`.
- `settings/admininterface.ini.append.php` — adds the "Layouts" tab to the admin node view window controls.
- `sql/` — schema for MySQL, SQLite and PostgreSQL (plus an experimental MongoDB schema description).
- `bin/php/` — CLI helpers (`install_base_data.php`, `layout_list.php`, `layout_publish.php`, `diagnose.php`, ...).
- Frontend templates under `design/standard/templates/explayouts/` including layout type templates (`1_column`, `2_column`, `3_column`, `4_column`, `featured`).

## Documentation

- [INSTALL.md](INSTALL.md) — activation and schema installation
- [doc/USAGE.md](doc/USAGE.md) — PHP and template usage, customization
- [doc/FAQ.md](doc/FAQ.md) — common questions
- [doc/TODO.md](doc/TODO.md) — known gaps
- [doc/SUPPORT.md](doc/SUPPORT.md) — how to get help
