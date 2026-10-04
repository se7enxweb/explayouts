<?php /* #?ini charset="utf-8"?

[BlockSettings]
AvailableBlocks[]
AvailableBlocks[]=title
AvailableBlocks[]=text
AvailableBlocks[]=button
AvailableBlocks[]=rich_text
AvailableBlocks[]=video
AvailableBlocks[]=map
AvailableBlocks[]=markdown
AvailableBlocks[]=html
AvailableBlocks[]=image
AvailableBlocks[]=spacer
AvailableBlocks[]=divider
AvailableBlocks[]=card
AvailableBlocks[]=alert
AvailableBlocks[]=badge
AvailableBlocks[]=progress
AvailableBlocks[]=accordion
AvailableBlocks[]=tabs
AvailableBlocks[]=hero
AvailableBlocks[]=about
AvailableBlocks[]=features
AvailableBlocks[]=logos
AvailableBlocks[]=quote
AvailableBlocks[]=lead
AvailableBlocks[]=list
AvailableBlocks[]=grid
AvailableBlocks[]=list_zigzag
AvailableBlocks[]=list_accordion
AvailableBlocks[]=gallery
AvailableBlocks[]=slider
AvailableBlocks[]=thumb_gallery
AvailableBlocks[]=grid_gallery
AvailableBlocks[]=sushi_bar
AvailableBlocks[]=carousel
AvailableBlocks[]=column
AvailableBlocks[]=two_columns
AvailableBlocks[]=three_columns
AvailableBlocks[]=four_columns
AvailableBlocks[]=tpl_block
AvailableBlocks[]=full_view
AvailableBlocks[]=single

[BlockDefinition_two_columns]
Name=Two columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=two_columns_66_33
ViewTypes[]=two_columns_33_66
IsContainer=1
Placeholders[]=left
Placeholders[]=right

Category=containers
[BlockDefinition_column]
Name=Column
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=column
IsContainer=1
Placeholders[]=main

Category=containers
[BlockDefinition_tpl_block]
Name=Template block
Handler=expLayoutsTplBlockHandler
ViewTypes[]=tpl_block

Category=placeholders
[BlockDefinition_text]
Name=Text
Handler=expLayoutsTextBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_title]
Name=Title
Handler=expLayoutsTitleBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_html]
Name=HTML snippet
Handler=expLayoutsHtmlBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_rich_text]
Name=Rich text
Handler=expLayoutsRichTextBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_image]
Name=Image
Handler=expLayoutsImageBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_list]
Name=List
Handler=expLayoutsListBlockHandler
ViewTypes[]=list
ViewTypes[]=grid
ViewTypes[]=list_numbered
ViewTypes[]=list_zigzag
ViewTypes[]=list_accordion
ViewTypes[]=grid_featured
HasCollection=1

Category=listing
[BlockDefinition_single]
Name=Single content
Handler=expLayoutsSingleBlockHandler
ViewTypes[]=default

Category=placeholders
[BlockDefinition_button]
Name=Button / Link
Handler=expLayoutsButtonBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_spacer]
Name=Spacer
Handler=expLayoutsSpacerBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_divider]
Name=Divider
Handler=expLayoutsDividerBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_card]
Name=Card
Handler=expLayoutsCardBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_accordion]
Name=Accordion
Handler=expLayoutsAccordionBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_tabs]
Name=Tabs
Handler=expLayoutsTabsBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_grid]
Name=Grid
Handler=expLayoutsGridBlockHandler
ViewTypes[]=default
HasCollection=1

Category=listing
[BlockDefinition_gallery]
Name=Gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

Category=gallery
[BlockDefinition_slider]
Name=Slider
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

Category=gallery
[BlockDefinition_thumb_gallery]
Name=Thumb gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

Category=gallery
[BlockDefinition_grid_gallery]
Name=Grid gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

Category=gallery
[BlockDefinition_sushi_bar]
Name=Sushi bar
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

Category=gallery
[BlockDefinition_list_zigzag]
Name=Zig-Zag (List)
Handler=expLayoutsListBlockHandler
ViewTypes[]=list_zigzag
HasCollection=1

Category=listing
[BlockDefinition_list_accordion]
Name=Accordion (List)
Handler=expLayoutsListBlockHandler
ViewTypes[]=list_accordion
HasCollection=1

Category=listing
[BlockDefinition_quote]
Name=Quote
Handler=expLayoutsQuoteBlockHandler
ViewTypes[]=default

Category=components
[BlockDefinition_alert]
Name=Alert
Handler=expLayoutsAlertBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_video]
Name=External video
Handler=expLayoutsVideoBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_badge]
Name=Badge
Handler=expLayoutsBadgeBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_progress]
Name=Progress bar
Handler=expLayoutsProgressBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_map]
Name=Map
Handler=expLayoutsMapBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_carousel]
Name=Carousel
Handler=expLayoutsCarouselBlockHandler
ViewTypes[]=default
HasCollection=1

Category=gallery
[BlockDefinition_markdown]
Name=Markdown
Handler=expLayoutsMarkdownBlockHandler
ViewTypes[]=default

Category=basic
[BlockDefinition_full_view]
Name=Full view
Handler=expLayoutsFullViewBlockHandler
# The reference declares exactly one view type for this block - full_view -
# and matches its template on block\view_type: full_view. 'default' emitted
# ngl-vt-default where the reference emits ngl-vt-full_view.
ViewTypes[]=full_view

Category=placeholders
[BlockDefinition_hero]
Name=Hero
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

Category=components
[BlockDefinition_about]
Name=About
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

Category=components
[BlockDefinition_features]
Name=Features
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

Category=components
[BlockDefinition_lead]
Name=Lead
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

Category=components
[BlockDefinition_logos]
Name=Logos
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

Category=components
[BlockDefinition_three_columns]
Name=Three columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=three_columns
IsContainer=1
Placeholders[]=col_1
Placeholders[]=col_2
Placeholders[]=col_3

Category=containers
[BlockDefinition_four_columns]
Name=Four columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=four_columns
IsContainer=1
Placeholders[]=col_1
Placeholders[]=col_2
Placeholders[]=col_3
Placeholders[]=col_4

Category=containers

# Content-backed component blocks. The reference generates these definitions at
# runtime from a config provider, one per component content type, with the
# component's style view types. eZ4 reads definitions from INI, so they are
# declared here. Without them getBlockInfo()/get() returned false, which left
# the block with no view types, no parameters and no preview, and made the
# editor sidebar request fail outright.
#
# The identifiers are exp_component_<type>; they were ibexa_component_<type>
# and are renamed in stored blocks by bin/php/updatecomponentblockidentifiers.php.
# Until then a block stored under the old name still finds its definition here
# (expLayoutsBlockHandlerFactory::currentIdentifier()). The Name values below
# are what the editor displays.
[BlockDefinition_exp_component_hero]
Name=Hero component
Handler=expLayoutsContentComponentBlockHandler
ViewTypes[]=hero_style_1
ViewTypes[]=hero_style_2
ViewTypes[]=hero_style_3

Category=components
[BlockDefinition_exp_component_features]
Name=Features component
Handler=expLayoutsContentComponentBlockHandler
ViewTypes[]=features_style_1
ViewTypes[]=features_style_2
ViewTypes[]=features_style_3
ViewTypes[]=features_style_4
ViewTypes[]=features_style_5
ViewTypes[]=features_style_6
ViewTypes[]=features_style_7

Category=components
[BlockDefinition_exp_component_about]
Name=About component
Handler=expLayoutsContentComponentBlockHandler
ViewTypes[]=about_style_1
ViewTypes[]=about_style_2
ViewTypes[]=about_style_3
ViewTypes[]=about_style_4
ViewTypes[]=about_style_5

Category=components
[BlockDefinition_exp_component_logos]
Name=Logos component
Handler=expLayoutsContentComponentBlockHandler
ViewTypes[]=logos_style_1
ViewTypes[]=logos_style_2

Category=components
[BlockDefinition_exp_component_quote]
Name=Quote component
Handler=expLayoutsContentComponentBlockHandler
ViewTypes[]=quote_style_1

Category=components
[BlockDefinition_exp_component_lead]
Name=Lead component
Handler=expLayoutsContentComponentBlockHandler
ViewTypes[]=lead_style_1
ViewTypes[]=lead_style_2

Category=components
[QuerySettings]
AvailableQueries[]
AvailableQueries[]=children
AvailableQueries[]=parent
AvailableQueries[]=subtree
AvailableQueries[]=siblings
AvailableQueries[]=latest
AvailableQueries[]=random
AvailableQueries[]=manual
AvailableQueries[]=exp_content_relation_list
AvailableQueries[]=exp_content_reverse_relation_list
AvailableQueries[]=exp_content_tags
AvailableQueries[]=exponential_content_search
AvailableQueries[]=content_by_topic

[QueryType_children]
Name=Children of a node
Handler=expLayoutsChildrenQueryHandler

[QueryType_parent]
Name=Parent of a node
Handler=expLayoutsParentQueryHandler

[QueryType_subtree]
Name=Subtree of a node
Handler=expLayoutsSubtreeQueryHandler

[QueryType_siblings]
Name=Siblings of a node
Handler=expLayoutsSiblingsQueryHandler

[QueryType_latest]
Name=Latest content
Handler=expLayoutsLatestQueryHandler

[QueryType_random]
Name=Random content
Handler=expLayoutsRandomQueryHandler

[QueryType_manual]
Name=Manual collection
Handler=expLayoutsManualQueryHandler

[QueryType_exp_content_relation_list]
Name=Exp relation list
Handler=expLayoutsRelationListQueryHandler

[QueryType_exp_content_reverse_relation_list]
Name=Exp reverse relation list
Handler=expLayoutsReverseRelationListQueryHandler

[QueryType_exp_content_tags]
Name=Exp tags
Handler=expLayoutsTagsQueryHandler

[QueryType_exponential_content_search]
Name=Exponential
Handler=expLayoutsExponentialContentSearchQueryHandler

[QueryType_content_by_topic]
Name=Topics
Handler=expLayoutsContentByTopicQueryHandler

# The admin blocks (design admin4l): each renders one part of the admin4 page with
# admin4's own template, so the markup is the one of admin4. Group=admin keeps them
# out of the site layouts' block list; AdminBlockSettings lists them for the admin
# layouts. Their view templates are design:explayouts/block/<identifier>.tpl.
[AdminBlockSettings]
AvailableBlocks[]
AvailableBlocks[]=admin_logo
AvailableBlocks[]=admin_search
AvailableBlocks[]=admin_theme_switch
AvailableBlocks[]=admin_sidebar_toggles
AvailableBlocks[]=admin_tab_menu
AvailableBlocks[]=admin_left_menu
AvailableBlocks[]=admin_content_tree
AvailableBlocks[]=admin_clear_cache
AvailableBlocks[]=admin_bookmarks
AvailableBlocks[]=admin_current_user
AvailableBlocks[]=admin_preferences
AvailableBlocks[]=admin_quick_settings
AvailableBlocks[]=admin_breadcrumb
AvailableBlocks[]=admin_module_result
AvailableBlocks[]=admin_footer
AvailableBlocks[]=admin_popup_menu
AvailableBlocks[]=admin_overlay
AvailableBlocks[]=admin_debug_area

[BlockDefinition_admin_logo]
Name=Admin: logo and site preview
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_header
Group=admin

[BlockDefinition_admin_search]
Name=Admin: search
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_header
Group=admin

[BlockDefinition_admin_theme_switch]
Name=Admin: light and dark mode switch
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_header
Group=admin

[BlockDefinition_admin_sidebar_toggles]
Name=Admin: sidebar toggles
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_header
Group=admin

[BlockDefinition_admin_tab_menu]
Name=Admin: tab menu
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_navigation
Group=admin

[BlockDefinition_admin_left_menu]
Name=Admin: left menu of the navigation part
Handler=expLayoutsAdminLeftMenuBlockHandler
ViewTypes[]=default
Category=admin_navigation
Group=admin

[BlockDefinition_admin_content_tree]
Name=Admin: content structure
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_navigation
Group=admin

[BlockDefinition_admin_clear_cache]
Name=Admin: clear cache
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_sidebar
Group=admin

[BlockDefinition_admin_bookmarks]
Name=Admin: bookmarks
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_sidebar
Group=admin

[BlockDefinition_admin_current_user]
Name=Admin: current user
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_sidebar
Group=admin

[BlockDefinition_admin_preferences]
Name=Admin: user preferences
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_sidebar
Group=admin

[BlockDefinition_admin_quick_settings]
Name=Admin: quick settings
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_sidebar
Group=admin

[BlockDefinition_admin_breadcrumb]
Name=Admin: breadcrumb path
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_page
Group=admin

[BlockDefinition_admin_module_result]
Name=Admin: module result
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_page
Group=admin

[BlockDefinition_admin_footer]
Name=Admin: footer and copyright
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_page
Group=admin

[BlockDefinition_admin_popup_menu]
Name=Admin: context menu
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_page
Group=admin

[BlockDefinition_admin_overlay]
Name=Admin: overlay and loader
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_page
Group=admin

[BlockDefinition_admin_debug_area]
Name=Admin: debug report area
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_page
Group=admin

[LayoutType_1_column]
Name=1 column
Zones[]=main

[LayoutType_2_column]
Name=2 columns
Zones[]=left
Zones[]=right

[LayoutType_3_column]
Name=3 columns
Zones[]=left
Zones[]=main
Zones[]=right

[LayoutType_4_column]
Name=4 columns
Zones[]=col1
Zones[]=col2
Zones[]=col3
Zones[]=col4

[LayoutType_hero]
Name=Hero + 3 columns
Zones[]=top
Zones[]=left
Zones[]=main
Zones[]=right

[LayoutType_sidebar_left]
Name=Sidebar left
Zones[]=sidebar
Zones[]=main

[LayoutType_sidebar_right]
Name=Sidebar right
Zones[]=main
Zones[]=sidebar

[LayoutType_featured]
Name=Featured
Zones[]=hero
Zones[]=feature1
Zones[]=feature2
Zones[]=feature3
Zones[]=bottom

[LayoutType_mosaic]
Name=Mosaic
Zones[]=a
Zones[]=b
Zones[]=c
Zones[]=d
Zones[]=e

[LayoutType_layout_1]
Name=Single zone
Zones[]=main

[LayoutType_layout_2]
Name=Header / Main / Footer
Zones[]=header
Zones[]=post_header
Zones[]=main
Zones[]=pre_footer
Zones[]=footer

[LayoutType_layout_4]
Name=Header / Left / Right / Footer
Zones[]=header
Zones[]=post_header
Zones[]=left
Zones[]=right
Zones[]=pre_footer
Zones[]=footer

[LayoutType_admin_3col]
Name=Admin: left, main, right
Group=admin
Zones[]=header
Zones[]=topmenu
Zones[]=left
Zones[]=right
Zones[]=main_top
Zones[]=main
Zones[]=main_bottom
Zones[]=footer

[LayoutType_admin_2col]
Name=Admin: left, main
Group=admin
Zones[]=header
Zones[]=topmenu
Zones[]=left
Zones[]=main_top
Zones[]=main
Zones[]=main_bottom
Zones[]=footer

[LayoutType_admin_full]
Name=Admin: main only
Group=admin
Zones[]=header
Zones[]=topmenu
Zones[]=main_top
Zones[]=main
Zones[]=main_bottom
Zones[]=footer

# The admin layouts: the pages of the administration interface assembled from
# layouts, zones and blocks (design admin4l). Layout types with Group=admin are
# admin layouts. They are resolved by module and view for the siteaccesses
# listed here, and never reach the public site; site layouts never reach the
# admin.
[AdminLayoutSettings]
# enabled|disabled. disabled returns every admin page to plain admin4.
Enabled=enabled
# Siteaccess names (fnmatch patterns) the admin layouts apply to.
SiteAccessMatch[]
SiteAccessMatch[]=admin
SiteAccessMatch[]=admin_*
SiteAccessMatch[]=admintest_*
SiteAccessMatch[]=editor
# Layout used when no admin rule matches the module and view (identifier of a
# published admin layout). Empty: nothing resolves and the page is plain admin4.
DefaultLayout=admin_3col
# Seconds a resolved answer is remembered. Publishing an admin layout or
# changing a rule clears it at once.
CacheTTL=3600

[TemplateEditorSettings]
AllowedTemplateRoots[]
AllowedTemplateRoots[]=design
AllowedTemplateRoots[]=extension

[ResolverSettings]
DefaultLayout=1d96945167435ac185b86cc0f9ef7084
CacheTTL=3600

# Maps a location id from the reference site onto content in this
# installation. Values are REMOTE IDS, not node ids.
#
# Node ids are handed out at install time and are not stable between
# installations, so a map written in node ids silently starts pointing at
# whatever content happens to hold that id next time. That is what happened to
# 190: it read 190=131, and node 131 is a test component, so the "All Recipes"
# button on /healthy-eating linked to /media/components/test-sck2.
#
# The identity entries below were worse than wrong - they were inert. Written
# as 721=721 and so on they relied on the nexus id also existing as a node id
# here, which it does not, so every one of them resolved to nothing. Their
# numbers were in fact the numeric half of a media-n- remote id all along.
[NexusNodeMap]
190=media-n-744
195=media-n-851
218=media-n-812
721=media-n-721
722=media-n-722
749=media-n-749
752=media-n-752
911=media-n-911
939=media-n-939
# 619 has neither a node nor a media-n-619 remote id in the shipped content;
# left unmapped rather than guessed at.
619=619
940=940
941=941
946=946
947=947
953=953
959=959
1060=1060
1061=1061

*/ ?>
