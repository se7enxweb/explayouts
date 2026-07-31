# Using explayouts

## Resolving a layout in PHP

```php
<?php
// Resolve the layout for the current request URI (falls back to
// ResolverSettings/DefaultLayout from explayouts.ini):
$layout = expLayoutsResolver::resolve();

// Or resolve for an explicit path:
$layout = expLayoutsResolver::resolve( 'about-us/team' );

if ( $layout instanceof expLayoutsLayout )
{
    // Prepare the full zone/block tree for rendering (status 2 = published)
    $prepared = expLayoutsRenderer::prepareLayout( $layout, 2 );
    // $prepared is an array: id, identifier, name, layout_type,
    // zones (each with blocks), block_count
}
```

## Resolving a layout in templates

The extension registers fetch functions on the `explayouts` module:

```smarty
{* By node *}
{def $layout = fetch( 'explayouts', 'resolve_layout_for_node',
                      hash( 'node_id', $module_result.content_info.node_id ) )}

{* By path (current request when path is omitted) *}
{def $layout2 = fetch( 'explayouts', 'resolve_layout', hash() )}

{* By identifier *}
{def $layout3 = fetch( 'explayouts', 'layout', hash( 'identifier', 'homepage' ) )}

{* Rules that map to a node *}
{def $rules = fetch( 'explayouts', 'rules_for_node',
                     hash( 'node_id', $module_result.content_info.node_id ) )}
```

Layout rendering templates live under `design/standard/templates/explayouts/` (`layout.tpl`, `zone.tpl`, and per-type templates in `layouts/`, e.g. `2_column.tpl`). `design/standard/templates/pagelayout.tpl` shows a complete integration: resolve the layout for the current node/path, then include `explayouts/layout.tpl` with the prepared layout.

## Working with value objects

```php
<?php
$layout = expLayoutsLayout::fetch( $layoutId );
$layout = expLayoutsLayout::fetchByIdentifier( 'homepage', 2 );

$zones  = expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), 2 );
$blocks = expLayoutsBlock::fetchByZone( $zones[0]->attribute( 'id' ), 2 );
// Pass null as status to fetch drafts and published rows together:
$allZones = expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), null );
```

## Block handlers

Block definitions come from `explayouts.ini`:

```php
<?php
$handler = expLayoutsBlockHandlerFactory::get( 'text' );   // expLayoutsTextBlockHandler
$params  = $handler->getParameters();          // parameter definitions
$values  = $handler->getValues( $blockArray ); // values for the view template

$available = expLayoutsBlockHandlerFactory::getAvailableBlocks();
$info      = expLayoutsBlockHandlerFactory::getBlockInfo( 'list' );
// $info: identifier, name, view_types, has_collection
```

## Dynamic collection queries

Blocks with `HasCollection=1` attach a collection. Dynamic collections run a query handler:

```php
<?php
$queryHandler = expLayoutsQueryHandlerFactory::get( 'children' ); // expLayoutsChildrenQueryHandler
$queries = expLayoutsQueryHandlerFactory::getAvailableQueries();
// children, parent, subtree, siblings, latest, random, manual,
// exp_content_relation_list, exp_content_reverse_relation_list, exp_content_tags
```

## Layout types

```php
<?php
$types = expLayoutsLayoutType::getAvailableTypes();
$info  = expLayoutsLayoutType::getTypeInfo( '2_column' );
$zones = expLayoutsLayoutType::getZones( '2_column' );   // e.g. array( 'left', 'right' )
```

Shipped layout types: `1_column`, `2_column`, `3_column`, `4_column`, `hero`, `sidebar_left`, `sidebar_right`, `featured`, `mosaic`.

## Import / export

```php
<?php
$exporter = new expLayoutsExporter();
$importer = new expLayoutsImporter();
```

Used by the transfer endpoints of `explayouts_ui_api` and the Import screen in `explayouts_ui`.

## Admin screens

- Admin module views: `/explayouts/dashboard`, `/explayouts/layout_list`, `/explayouts/layout_edit`, `/explayouts/block_edit`, `/explayouts/rule_list`, `/explayouts/rule_edit`, `/explayouts/preview`, `/explayouts/setup`, `/explayouts/template_editor`.
- The "Layouts" tab on the admin content node view shows layouts and rules for the node (template `design/admin/templates/tabs/explayouts.tpl`).

## CLI

Helper scripts under `bin/php/`, e.g.:

```bash
php extension/explayouts/bin/php/layout_list.php
php extension/explayouts/bin/php/layout_publish.php
php extension/explayouts/bin/php/diagnose.php
```

## Customization

### Settings layer (INI cascade)

Every setting shipped in `extension/explayouts/settings/*.ini.append.php` can be overridden from outside the extension. The effective value is resolved through the standard INI cascade, from lowest to highest priority:

1. `extension/explayouts/settings/` — the defaults shipped here
2. `settings/siteaccess/<siteaccess>/` — per-siteaccess overrides
3. `extension/<your_extension>/settings/siteaccess/<siteaccess>/` — siteaccess overrides shipped in an active extension
4. `settings/override/` — global overrides, always win

Examples (place in `settings/override/explayouts.ini.append.php` or a siteaccess variant):

```ini
[ResolverSettings]
# Fallback layout when no rule matches
DefaultLayout=homepage

[BlockSettings]
# Add your own block
AvailableBlocks[]=my_block

[BlockDefinition_my_block]
Name=My block
Handler=myBlockHandler
ViewTypes[]=default

[LayoutType_landing]
Name=Landing page
Zones[]=hero
Zones[]=main
```

To swap the handler of a shipped block, redefine only its section:

```ini
[BlockDefinition_text]
Handler=myTextBlockHandler
```

The admin "Layouts" node tab can be disabled by overriding `admininterface.ini` `[WindowControlsSettings] AdditionalTabs[]` without the `layouts` entry, and the admin menu entries by overriding the `menu.ini` sections (`Topmenu_explayouts_dashboard`, `Leftmenu_explayouts_dashboard`).

### Template layer (design override cascade)

Frontend templates resolve through the design cascade: current siteaccess design, then additional site designs, then `standard`. To restyle a layout without touching this extension, ship the same-relative-path template in your own design extension, e.g.:

```
extension/mytheme/design/mydesign/templates/explayouts/layout.tpl
extension/mytheme/design/mydesign/templates/explayouts/zone.tpl
extension/mytheme/design/mydesign/templates/explayouts/layouts/2_column.tpl
```

with `mydesign` selected as (additional) site design for the siteaccess. The same works for the admin design (`design/admin/templates/tabs/explayouts.tpl`) from an admin-design extension. Block view templates referenced by `ViewTypes[]` follow the same rule.

### PHP layer (safe extension points)

- **Block handlers** — implement `expLayoutsBlockHandlerInterface` (`getParameters()`, `getValues( $block )`) in your own extension and wire it via `BlockDefinition_<id>` `Handler=`. This is the primary extension point; no core code changes needed.
- **Query handlers** — implement `expLayoutsQueryHandlerInterface` and register it via `[QuerySettings] AvailableQueries[]` plus a `QueryType_<id>` section with `Handler=`. Sibling extensions `explayouts_relation_list_query` and `explayouts_tags_query` are working examples (`exp_content_relation_list`, `exp_content_tags`).
- **Fetch functions** — build page logic on the `explayouts` module fetch functions (`layout`, `resolve_layout`, `resolve_layout_for_node`, `rules_for_node`) rather than querying the `explayouts_*` tables directly.
- **Services** — for create/update/delete flows, use the `explayouts_core` service classes instead of storing `eZPersistentObject` rows yourself.

Avoid subclassing the persistent value objects (`expLayoutsLayout` etc.); their definition maps to the `explayouts_*` schema and is treated as internal.
