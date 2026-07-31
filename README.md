Exponential Layouts
===================

General description
-------------------

Exponential Layouts (`explayouts`) is a visual page builder for Exponential 6 / Exponential Legacy. It is the main integration extension of the Exponential Layouts suite: it owns the `explayouts_*` database tables, renders layouts, zones and blocks on the frontend, resolves which layout applies to a request via mapping rules, and adds a "Layouts" tab to the admin content view.

It is an Exponential Legacy port inspired by the `netgen/layouts-ibexa` package. Instead of Symfony services and Doctrine, it uses `eZPersistentObject` value classes, INI-driven block/layout type definitions (`explayouts.ini`) and legacy module views.

This extension provides the following capabilities:

- Layout persistence - Store layouts, zones, blocks, block parameters, collections, collection items and mapping rules in dedicated `explayouts_*` database tables, with draft and published statuses.
- Layout resolving - Decide which layout applies to a request path or content node through prioritized mapping rules with targets and conditions, with a configurable fallback layout.
- Frontend rendering - Prepare a complete layout/zone/block tree for template rendering and ship the layout, zone and block view templates that render it.
- Block handlers - Around 25 built-in, INI-wired block handlers (text, title, HTML, image, list, grid, gallery, carousel, map, video and more), all implementing a single simple interface.
- Dynamic collections - Attach manual or query-driven content collections to blocks (children, parent, subtree, siblings, latest, random, manual and pluggable custom query types).
- Admin integration - A legacy admin module with dashboard, layout/rule/block editors, preview, setup and template editor screens, plus a "Layouts" tab on the admin content node view.
- Template fetch functions - Resolve and load layouts directly from templates via the `explayouts` module fetch functions.
- Import / export - Transfer layouts and mapping rules between installations.
- CLI tooling - Command line helpers for schema installation, base data seeding, listing, publishing and diagnostics.

Features
--------

The following features are provided by the Exponential Layouts extension:

- Persistent value objects - `expLayoutsLayout`, `expLayoutsZone`, `expLayoutsBlock` and `expLayoutsBlockParameter` model the layout tree; `expLayoutsCollection` and `expLayoutsCollectionItem` model block collections (manual and dynamic); `expLayoutsRule`, `expLayoutsRuleTarget` and `expLayoutsRuleCondition` model the layout resolver rules. All are `eZPersistentObject` classes mapped to the `explayouts_*` schema.
- Layout resolver - `expLayoutsResolver::resolve()` resolves the layout for the current request URI, an explicit path or a content node, evaluating enabled rules by priority and falling back to `explayouts.ini` `[ResolverSettings] DefaultLayout` when no rule matches.
- Renderer - `expLayoutsRenderer::prepareLayout()` turns a layout into a ready-to-render array (id, identifier, name, layout_type, zones each with their blocks, block_count) consumed by the shipped layout templates.
- INI-defined layout types - `expLayoutsLayoutType` reads `LayoutType_*` definitions and their zones from `explayouts.ini`. Shipped layout types: `1_column`, `2_column`, `3_column`, `4_column`, `hero`, `sidebar_left`, `sidebar_right`, `featured`, `mosaic`.
- INI-defined block definitions - `expLayoutsBlockHandlerFactory` instantiates block handlers from `explayouts.ini` `BlockDefinition_*` sections. Around 25 built-in handlers ship with the extension (`expLayoutsTextBlockHandler`, `expLayoutsTitleBlockHandler`, `expLayoutsHtmlBlockHandler`, `expLayoutsImageBlockHandler`, `expLayoutsListBlockHandler`, `expLayoutsSingleBlockHandler`, `expLayoutsButtonBlockHandler`, `expLayoutsCardBlockHandler`, `expLayoutsGridBlockHandler`, `expLayoutsGalleryBlockHandler`, `expLayoutsCarouselBlockHandler`, `expLayoutsMapBlockHandler`, `expLayoutsVideoBlockHandler` and more), all implementing `expLayoutsBlockHandlerInterface`.
- Dynamic collection query handlers - `expLayoutsQueryHandlerFactory` instantiates the query handlers behind dynamic collections: `children`, `parent`, `subtree`, `siblings`, `latest`, `random`, `manual`, plus pluggable query types such as `exp_content_relation_list`, `exp_content_reverse_relation_list` and `exp_content_tags` provided by sibling extensions.
- Template fetch functions - `layout`, `resolve_layout`, `resolve_layout_for_node` and `rules_for_node` on the `explayouts` module make layout resolution available to any template, including `pagelayout.tpl`.
- Legacy admin module - `modules/explayouts` provides dashboard, layout list/edit, block edit, rule list/edit, preview, setup and template editor views.
- Admin content view integration - `settings/admininterface.ini.append.php` adds the "Layouts" tab to the admin node view window controls, showing the layouts and rules that apply to the node.
- Import / export - `expLayoutsImporter` and `expLayoutsExporter` transfer layouts and rules; used by the transfer endpoints of `explayouts_ui_api` and the Import screen of `explayouts_ui`.
- Multi-database schema - `sql/` ships the schema for MySQL/MariaDB, SQLite and PostgreSQL (plus an experimental MongoDB schema description).
- Installation and demo data - `expLayoutsInstall` and `expLayoutsFixtures` handle schema installation and demo data seeding.
- CLI helpers - `bin/php/` contains command line scripts such as `install_base_data.php`, `layout_list.php`, `layout_publish.php`, `layout_info.php` and `diagnose.php`.
- Frontend templates - `design/standard/templates/explayouts/` ships `layout.tpl`, `zone.tpl` and the per-type layout templates in `layouts/` (e.g. `2_column.tpl`), all overridable through the design cascade.
- Customization layers - Every shipped INI setting can be overridden through the standard INI cascade, every template through the design override cascade, and new blocks/queries are added purely in your own extension via the handler interfaces (see [doc/USAGE.md](doc/USAGE.md)).

Version
-------

- The current version of Exponential Layouts is 1.0.0
- Last Major update: July 30, 2026

Copyright
---------

- Exponential Layouts is copyright 1998 - 2026 7x
- See: [LICENSE.md](LICENSE.md) for more information on the terms of the copyright and license

License
-------

Exponential Layouts is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE.md](LICENSE.md) file.

Exponential Layouts is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License or at your
option a later version.

Exponential Layouts is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
Exponential Layouts under certain conditions. The GNU GPL license
is distributed with the software, see the file LICENSE.md.

It is also available at http://www.gnu.org/licenses/gpl.txt

You should have received a copy of the GNU General Public License
along with Exponential Layouts in LICENSE.md. If not, see http://www.gnu.org/licenses/.

Using Exponential Layouts under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact
info@se7enx.com

Requirements
------------

The following requirements exists for using the Exponential Layouts extension:

Exponential version
- Make sure you use Exponential 6 / eZ Publish Legacy (required) or higher.

PHP version
- Make sure you have PHP 8.1 or higher.

Database server
- Make sure you have MySQL/MariaDB, SQLite or PostgreSQL available; the extension ships a schema for each in its `sql/` directory.

Sibling extensions (optional, for the full suite)
- `explayouts` works standalone for rendering and resolving. For the full suite also activate:
  - `explayouts_core` — service layer used by the admin UIs
  - `explayouts_standard` — additional standard block handler set
  - `explayouts_ui` — legacy admin module and assets
  - `explayouts_ui_api` — modern SPA admin app and JSON API
  - `explayouts_api` — content adapters for pickers/integration

Installation
------------

In short: place the extension in `extension/explayouts`, activate it via `site.ini` `[ExtensionSettings] ActiveExtensions[]` (or per siteaccess via `ActiveAccessExtensions[]`), import the database schema for your database from the `sql/` directory (this creates the `explayouts_*` tables: layout, zone, block, block_parameter, collection, collection_item, collection_query, rule, rule_target, rule_condition), optionally seed base data with `php extension/explayouts/bin/php/install_base_data.php`, then regenerate autoloads and clear all caches.

See [INSTALL.md](INSTALL.md) for the full step-by-step installation instructions, including the settings shipped with the extension (`explayouts.ini`, `module.ini`, `design.ini`, `menu.ini`, `admininterface.ini`).

Usage
-----

The extension is used from three sides: PHP code, templates and the admin interface.

Key classes:

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

Resolving a layout in PHP:

```php
<?php
$layout = expLayoutsResolver::resolve();                  // current request URI
$layout = expLayoutsResolver::resolve( 'about-us/team' ); // explicit path

if ( $layout instanceof expLayoutsLayout )
{
    $prepared = expLayoutsRenderer::prepareLayout( $layout, 2 ); // 2 = published
}
```

Resolving a layout in templates via the registered fetch functions:

```smarty
{def $layout = fetch( 'explayouts', 'resolve_layout_for_node',
                      hash( 'node_id', $module_result.content_info.node_id ) )}
{def $layout2 = fetch( 'explayouts', 'resolve_layout', hash() )}
{def $layout3 = fetch( 'explayouts', 'layout', hash( 'identifier', 'homepage' ) )}
{def $rules = fetch( 'explayouts', 'rules_for_node',
                     hash( 'node_id', $module_result.content_info.node_id ) )}
```

What else is included:

- `modules/explayouts` — legacy admin module: dashboard, layout list/edit, block edit, rule list/edit, preview, setup, template editor (`/explayouts/dashboard`, `/explayouts/layout_list`, `/explayouts/rule_list`, ...).
- Template fetch functions: `layout`, `resolve_layout`, `resolve_layout_for_node`, `rules_for_node`.
- `settings/admininterface.ini.append.php` — adds the "Layouts" tab to the admin node view window controls.
- `sql/` — schema for MySQL, SQLite and PostgreSQL (plus an experimental MongoDB schema description).
- `bin/php/` — CLI helpers (`install_base_data.php`, `layout_list.php`, `layout_publish.php`, `diagnose.php`, ...).
- Frontend templates under `design/standard/templates/explayouts/` including layout type templates (`1_column`, `2_column`, `3_column`, `4_column`, `featured`).

See [doc/USAGE.md](doc/USAGE.md) for exhaustive usage scenarios: working with the value objects, block and query handlers, layout types, import/export, admin screens, CLI scripts, and the full customization guide covering the settings layer (INI cascade), the template layer (design override cascade) and the PHP layer (safe extension points such as custom block and query handlers).

Documentation
-------------

| Document | Description |
|----------|-------------|
| [INSTALL.md](INSTALL.md) | Step-by-step installation: activation, database schema, autoloads, shipped settings |
| [doc/USAGE.md](doc/USAGE.md) | PHP and template usage, fetch functions, handlers, CLI and the customization layers |
| [doc/FAQ.md](doc/FAQ.md) | Answers to the most common questions and problems |
| [doc/TODO.md](doc/TODO.md) | Known gaps and planned improvements |
| [doc/SUPPORT.md](doc/SUPPORT.md) | How and where to get help |
| [LICENSE.md](LICENSE.md) | The complete GNU General Public License agreement |

Troubleshooting
---------------

Read the FAQ
- Some problems are more common than others. The most common ones are listed in [doc/FAQ.md](doc/FAQ.md).

Use our support systems
- If you have questions not handled by this document or the FAQ, you can reach us via [7x : se7enx.com](https://se7enx.com).
- If you find a bug or defect, please report it to the [Exponential Layouts: Issue Tracker](https://github.com/se7enxweb/explayouts/issues).
