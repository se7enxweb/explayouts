# explayouts FAQ

## How does this differ from netgen/layouts-ibexa?

`netgen/layouts-ibexa` is a Symfony bundle that plugs the upstream layouts engine into Ibexa CMS. `explayouts` re-implements the same concepts (layouts, zones, blocks, collections, mapping rules) natively for the eZ Publish legacy kernel: persistence via `eZPersistentObject`, configuration via INI files (`explayouts.ini`) instead of Symfony DI/YAML, rendering via legacy `.tpl` templates, and admin screens as legacy module views. There is no Symfony container, no Twig and no Doctrine.

## Which database tables does it own?

All `explayouts_*` tables: `explayouts_layout`, `explayouts_zone`, `explayouts_block`, `explayouts_block_parameter`, `explayouts_collection`, `explayouts_collection_item`, `explayouts_collection_query`, `explayouts_rule`, `explayouts_rule_target`, `explayouts_rule_condition`. Schema files are in `sql/<driver>/schema.sql`.

## How is the layout for a page chosen?

`expLayoutsResolver::resolve()` walks all enabled rules (`expLayoutsRule::fetchEnabled()`) ordered by priority and returns the layout of the first rule whose conditions are all met and one of whose targets matches the request path or node. If no rule matches, the `ResolverSettings/DefaultLayout` identifier from `explayouts.ini` is used as fallback.

Target types (`expLayoutsResolver::TARGET_TYPES`): `path`, `path_prefix`, `path_info_prefix`, `path_regex` (the pattern without delimiters), `node`, `subtree`, `route`, and `module` / `module_view` for the admin layouts. Condition types (`expLayoutsResolver::CONDITION_TYPES`): `siteaccess`, `class` (also read as `content_type` and `ibexa_content_type`), `query_parameter`, `route_parameter`, `time`.

A rule without a target matches no page; the catch-all is the target `path_info_prefix` `/`. A condition of an unknown type is never met. `bin/php/check_rules.php` lists the rules this affects (read only).

A `route` target names a module view, Exponential's routes: `content/search`, `user/*`, `content/view` for every content page, `*` for all. The route of a page is the module view its address stands for (a URL alias is `content/view`, the front page its `IndexPage`). The media design lays out only content pages with the regular rules; on its other pages it asks `fetch( 'explayouts', 'resolve_route_layout', hash() )`, which answers only for a rule with a route target.

Answers that depend on a `query_parameter` condition are not cached: `/page` and `/page?variant=b` each get their own answer (the page cache does not keep an address with a query parameter it does not know, `[HttpCacheSettings] QueryStringParameters`). An answer that depends on a `time` condition is cached until the condition's next start or end, and the page caches do not keep the page past that moment.

## How do I add my own block type?

Write a class implementing `expLayoutsBlockHandlerInterface` (methods `getParameters()` and `getValues( $block )`), then declare it in an `explayouts.ini` override with an `AvailableBlocks[]` entry (in your extension's settings, wherever it is listed in `ActiveExtensions[]`: explayouts' list has no reset line) and a `BlockDefinition_<identifier>` section pointing `Handler=` at your class. See USAGE.md for a full example.

## What are draft and published statuses?

Layouts, zones and blocks carry a `status` column: `1` = draft, `2` = published. The renderer defaults to published (`status = 2`); the admin UIs work on drafts and publish via the service layer (`explayouts_core`) or the JSON API (`explayouts_ui_api`).

## Do blocks support dynamic content lists?

Yes. Blocks with `HasCollection=1` (e.g. the `list` block) attach a collection. Manual collections hold node ids; dynamic collections run a query handler (children, parent, subtree, siblings, latest, random) created through `expLayoutsQueryHandlerFactory`.
