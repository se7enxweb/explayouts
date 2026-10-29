# Changelog

## 1.4.46 (2026-10-29)

- Updated: The admin stylesheets use the shared admin tokens for weights, durations, radii, table headers and muted text, with the old values as fallbacks where a design has no tokens.

## 1.4.45 (2026-10-28)

- Added: Translations into 59 more languages: aka-GH, arc-IQ, asm-IN, aze-AZ, bel-BY, bho-IN, bod-CN, ceb-PH, che-RU,
  ckb-IQ, cym-GB, eng-AU, eus-ES, fil-PH, ful-SN, gla-GB, glg-ES, haw-US, huy-IL, hye-AM, ind-ID, jav-ID, kat-GE,
  kaz-KZ, khm-KH, kik-KE, kin-RW, kir-KG, lao-LA, lin-CD, ltz-LU, lug-UG, mai-IN, mkd-MK, mlg-MG, mon-MN, mri-NZ,
  msa-MY, mya-MM, nep-NP, orm-ET, pcm-NG, pnb-PK, run-BI, sin-LK, sna-ZW, snd-PK, sun-ID, syr-IQ, tat-RU, tgk-TJ,
  tha-TH, tir-ET, tuk-TM, uig-CN, uzb-UZ, vie-VN, wol-SN and yid-US.
- Updated: The ara-SA, bos-BA, dsb-DE, ell-GR, est-EE, fas-IR, fin-FI, fre-FR, gle-IE, hin-IN, hun-HU, isl-IS, jpn-JP,
  kal-GL, kor-KR, kur-TR, lav-LV, mlt-MT, pus-AF, ron-RO, slk-SK, slv-SI, sqi-AL, swa-KE and ukr-UA translations are
  complete.

## 1.4.44 (2026-10-26)

- Added: Chinese translations in Simplified (chi-CN) and Traditional (chi-TW) script; chi-HK falls back to chi-TW,
  from ekked's pull request #20.
- Added: Translations into seventeen languages from South Asia and Africa: Bengali, Urdu, Marathi, Telugu, Tamil,
  Gujarati, Kannada, Malayalam, Punjabi, Odia, Hausa, Amharic, Yoruba, Igbo, isiZulu, Somali and isiXhosa, from
  ekked's pull request #19.
- Added: Russian, Catalan, Serbian (Cyrillic and Latin), Bulgarian, Lithuanian, Portuguese (Portugal) and Afrikaans
  translations, from ekked's pull request #18.
- Added: Translations into eighteen more administration languages: Hindi, Korean, Bosnian, Lower Sorbian, Estonian,
  Irish, Icelandic, Greenlandic, Latvian, Hungarian, Maltese, Romanian, Albanian, Slovak, Slovenian, Japanese, Greek
  and Finnish, from ekked's pull request #17.
- Added: Swahili (Kenya) translation, from ekked's pull request #15.
- Fixed: Translations that contradicted their language guide: wrong meanings, glossary breaches, forms of address,
  typos and English left in the text, from ekked's pull request #16.

## 1.4.42 (2026-10-21)

### Fixed

- **Route targets match on the site.** A route target names the module view a page addresses: `content/search`,
  `user/*`, `content/view` (every content page), `*`. A URL alias counts as the content view it stands for, the front
  page as its `IndexPage`; a leading siteaccess segment is dropped. The path decides the answer, so it is cached like a
  path target. `fetch( 'explayouts', 'resolve_route_layout', hash() )` gives a pagelayout that lays out only content
  pages the layout a route rule chose for its other pages (the media design uses it).
- **Time conditions switch at their start and end.** An answer that depends on a `time` condition of a rule naming the
  page is cached until the next `from` or `to` of that condition (not for `CacheTTL`), and when that moment comes
  before the page cache's `[HttpCacheSettings] MaxAge`, the page is kept out of the page caches
  (`ezpRequestRuleKernel::keepOutOfSharedCaches()`, `Cache-Control: private, no-store`); on a cache hit too. A range
  that is over is cached as before. `query_parameter` answers stay uncached, so one address can be A/B tested
  (`/page` and `/page?variant=b`).
- **An extension's `AvailableBlocks[]` is kept wherever the extension is listed.** The lists other extensions add to
  (`AvailableBlocks[]` of `[BlockSettings]` and `[AdminBlockSettings]`, `AvailableQueries[]`, `SiteAccessMatch[]`,
  `AllowedTemplateRoots[]`) start without a reset line; the reset emptied what every extension read before explayouts
  had added (the media design's `product_highlight` block was never offered). A block listed twice is offered once.
  To replace a list, put the reset line in `settings/override/` or a siteaccess.
- **The `ext:explayouts:*` helpers run on their own.** `./console ext:explayouts:block_list`, `layout_info`,
  `collection_list`, `layout_copy`, `layout_list`, `rule_list`, `share_create` and the other helpers of `bin/php/`
  ended with `Class "..." not found`: they were written for `bin/php/ezexec.php`. Each now starts Exponential through
  `bin/php/inc/standalone.php` when it is not run through ezexec (`-s <siteaccess>` works); under ezexec nothing
  changes.

## 1.4.41 (2026-10-20)

### Changed (check before upgrading)

- **A rule without a target matches no page.** It used to match every page. The catch-all is the target
  `path_info_prefix` `/`.
- **A condition of an unknown type is never met.** It used to be ignored, so the rule applied everywhere.
  `ibexa_content_type` is read as the class condition.
- Answers that depend on a `query_parameter` or `time` condition are no longer cached; the resolver cache is pruned
  to `[ResolverSettings] CacheMaxEntries` (5000).
- **Imports create a draft.** Rules of an export come along disabled and with known target and condition types only;
  layout type, block definitions and view types must be ones of the settings; a zone links to a shared layout by its
  identifier only. Both export forms import (this extension's and the layout editor's).
- The classic views save nothing when a form is refused (unknown types, values of the wrong kind) and clear the
  resolver cache after every write; the rule list shows disabled rules too.
- The Layouts tab of the node view is drawn and filled only for users with `explayouts/read`.
- Block values that reach markup are checked: links (http, https, mailto, tel or relative), embeds, colors, CSS
  classes, the element of a title, template names of the template block and of included block templates.
- The layout preview and Load more from the administration render in the administration's public siteaccess
  (`expLayoutsPreviewSiteAccess`, `[PreviewSettings] SiteAccess`), never in one with another database.

### Added (parity with netgen/layouts-standard)

- **Markdown block renders Markdown** (the reference: `markdown_to_html`). With `league/commonmark` installed it is
  CommonMark with HTML in the text escaped, unsafe links dropped and the nesting limited; links with another scheme
  than http, https, mailto and tel become text, images from other hosts become links (inside a link: their text).
  Without the library a scanner of its own (no backtracking patterns, linear in the text) renders headings,
  paragraphs, lists, quotes, code, emphasis, links and images of the site the way CommonMark does
  (`expLayoutsMarkdown`). Suggested in `composer.json`.
- **Two columns** has the view types `two_columns_50_50` (first, as in the reference), `_33_66` and `_66_33`;
  **three columns** `three_columns_33_33_33`, `_50_25_25`, `_25_50_25` and `_25_25_50` (a block stored as
  `three_columns` renders 33:33:33); the view types are named (`ViewTypeNames[]`).
- **Layout types `layout_3`, `layout_5` and `layout_6`** of the reference, and templates for `layout_1` to `layout_6`
  with the reference's grid (`layout_4` showed its zones one under the other).
- **External video**: service (YouTube, Vimeo, Dailymotion) and video id as in the reference, and a caption; the
  address field stays for existing blocks. YouTube plays from youtube-nocookie.com, Vimeo with `dnt=1`.
- **Map**: latitude, longitude, zoom (0-20), map type and marker as in the reference. With
  `[MapSettings] GoogleMapsApiKey` the map is Google's Maps Embed API, without it OpenStreetMap; the embed address
  stays for existing blocks.
- Video and map load after a click in the standard templates (`javascript/explayouts_embed.js`): no request to the
  other host before. Their addresses are addresses of another host only: `//host/...` is read as https, a page of
  this site is refused (it would sit in a frame with scripts and its own origin, outside any sandbox).
- **Gallery blocks**: the reference's design parameters next/previous, pagination, infinite loop, transition,
  autoplay with its time, thumbnails to move, details and details on hover, each for the view types the reference
  offers it for - with two deliberate exceptions: the autoplay time is offered for the slider too (the reference's
  slider plays automatically but leaves the time out), and `number_of_thumbnails` and `enable_lightbox`, which this
  port had before, stay offered for all five gallery view types (the reference limits the lightbox to the grid
  gallery and the thumbnails to grid gallery and sushi bar).
- Selects for the title's HTML tag (h1-h6, p, div), the button's style (default, highlighted, link) and target.
- Heading level for the component blocks (hero, about, features, lead); features take a list of features
  (`Title|Text` per line, `expLayoutsFeaturesBlockHandler`).
- Number fields carry min and max where the reference has them.
- **Content and Design tab as in the reference**: `[BlockDefinition_<id>] ContentParameters[]` names the parameters
  of the group "content" (the block's text, links, images, video, map); the layout editor shows them in the Content
  tab with the block label and the collection, all others in the Design tab (`expLayoutsBlockHandlerFactory::
  parameterGroup()`). A definition without the list keeps every parameter in the Design tab.
- View type names for the list block.
- Gallery blocks show their items, offer the gallery view types and open a lightbox (`javascript/explayouts_lightbox.js`).
- `bin/php/install_admin_layouts.php` installs the explayouts tables, the base layout and the admin layouts of admin4l
  into the database of one siteaccess (MySQL/MariaDB; `DRY=1` for a dry run).
- Translations: French, Ukrainian, Arabic, Persian, Pashto and Kurdish (Kurmanji); more messages for Polish, Turkish,
  Italian, Brazilian Portuguese, Swedish, Dutch, Czech, Norwegian, Danish and Croatian.

### Fixed

- The export of the layout list failed: the class is `expLayoutsExporter` again (the old name stays as an alias).
- The scripts under `bin/php` read their arguments when run through `bin/php/ezexec.php`.
- The content component blocks `exp_component_*` have a standard template.
- The layout type `admin_full` declares no `main_top` and `main_bottom` zones, and the admin layout seed carries no
  zones or breadcrumb block that it no longer declares.

### Upgrade

- **Markdown blocks stored before** were printed with `nl2br`: every line break was a break. To keep their look,
  `explayouts.ini [MarkdownSettings] HardBreaks` is `enabled` by default. CommonMark (and the reference) joins the
  lines of a paragraph; set `HardBreaks=disabled` for that, and end a line with two spaces or a backslash where a
  break is meant.
- Video and map blocks whose address is a path on this site show nothing any more; enter the address of the player
  or map.

- New classes (`expLayoutsSafeValue`, `expLayoutsPreviewSiteAccess`, `expLayoutsMarkdown`,
  `expLayoutsFeaturesBlockHandler`) and template operators: run
  `php bin/php/ezpgenerateautoloads.php -e` and clear the INI and template caches.
- Run `php bin/php/ezexec.php -s <siteaccess> extension/explayouts/bin/php/check_rules.php` to list enabled rules
  without a target and targets or conditions of an unknown type.
