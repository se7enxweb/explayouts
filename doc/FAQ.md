# explayouts FAQ

## How does this differ from netgen/layouts-ibexa?

`netgen/layouts-ibexa` is a Symfony bundle that plugs Netgen Layouts into Ibexa CMS. `explayouts` re-implements the same concepts (layouts, zones, blocks, collections, mapping rules) natively for the eZ Publish legacy kernel: persistence via `eZPersistentObject`, configuration via INI files (`explayouts.ini`) instead of Symfony DI/YAML, rendering via legacy `.tpl` templates, and admin screens as legacy module views. There is no Symfony container, no Twig and no Doctrine.

## Which database tables does it own?

All `explayouts_*` tables: `explayouts_layout`, `explayouts_zone`, `explayouts_block`, `explayouts_block_parameter`, `explayouts_collection`, `explayouts_collection_item`, `explayouts_collection_query`, `explayouts_rule`, `explayouts_rule_target`, `explayouts_rule_condition`. Schema files are in `sql/<driver>/schema.sql`.

## How is the layout for a page chosen?

`expLayoutsResolver::resolve()` walks all enabled rules (`expLayoutsRule::fetchEnabled()`) ordered by priority and returns the layout of the first rule whose targets match the request path or node. If no rule matches, the `ResolverSettings/DefaultLayout` identifier from `explayouts.ini` is used as fallback.

Supported target types: `path`, `path_prefix`, `path_info_prefix`, `path_regex`, `node`/`content_node`, `subtree`. Supported condition types: `siteaccess` and `content_type` (unknown condition types match by default).

## How do I add my own block type?

Write a class implementing `expLayoutsBlockHandlerInterface` (methods `getParameters()` and `getValues( $block )`), then declare it in an `explayouts.ini` override with an `AvailableBlocks[]` entry and a `BlockDefinition_<identifier>` section pointing `Handler=` at your class. See USAGE.md for a full example.

## What are draft and published statuses?

Layouts, zones and blocks carry a `status` column: `1` = draft, `2` = published. The renderer defaults to published (`status = 2`); the admin UIs work on drafts and publish via the service layer (`explayouts_core`) or the JSON API (`explayouts_ui_api`).

## Do blocks support dynamic content lists?

Yes. Blocks with `HasCollection=1` (e.g. the `list` block) attach a collection. Manual collections hold node ids; dynamic collections run a query handler (children, parent, subtree, siblings, latest, random) created through `expLayoutsQueryHandlerFactory`.
