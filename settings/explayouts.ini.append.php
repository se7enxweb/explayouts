<?php /* #?ini charset="utf-8"?

# The lists other extensions add to -- AvailableBlocks[] ([BlockSettings] and [AdminBlockSettings]), AvailableQueries[],
# SiteAccessMatch[] and AllowedTemplateRoots[] -- start here without a reset line (Name[] alone). The settings of the
# extensions are read in the reverse order of ActiveExtensions[], so a reset here emptied what every extension read
# before this file had added: an extension's AvailableBlocks[]=myblock survived only when the extension came before
# explayouts in ActiveExtensions[]. Without it, an extension adds a block wherever it is listed, and a block both list
# is offered once. To replace a list rather than add to it, write the reset line where it is read after every
# extension: settings/override/explayouts.ini.append.php or settings/siteaccess/<siteaccess>/explayouts.ini.append.php
# (./console exp:ini clear explayouts.ini/BlockSettings/AvailableBlocks[] <scope>).

# ContentParameters[] of a block definition: the parameters the layout editor shows in its Content tab (with the block
# label and the collection), as the reference's parameters of the group "content" - the block's own text, links,
# images, video and map. The others (view type, CSS class and ID, container, whitespace, the design options of the
# galleries and lists) are in the Design tab, as is every parameter of a definition that lists none. A compound
# parameter takes its children along.

# ItemViewTypes[<item view type>]=<label> of a block definition: item view types editors can choose for the items of
# the block, after the ones its handler offers in code (getItemViewTypes()). They are an axis of their own: every view
# type of the definition offers all of them; whether a design draws a combination is up to the design. A design that
# draws more item views (content/views/<item view type>.tpl) adds them in its siteaccess settings, with no code.
[BlockSettings]
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
# The view types of the reference, its first one first (a new block gets it). A block stored with another view type
# (default) renders 50:50.
ViewTypes[]=two_columns_50_50
ViewTypes[]=two_columns_33_66
ViewTypes[]=two_columns_66_33
ViewTypeNames[two_columns_50_50]=50:50
ViewTypeNames[two_columns_33_66]=33:66
ViewTypeNames[two_columns_66_33]=66:33
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
ContentParameters[]=block_name
ViewTypes[]=tpl_block

Category=placeholders
[BlockDefinition_text]
Name=Text
Handler=expLayoutsTextBlockHandler
ContentParameters[]=content
ViewTypes[]=default

Category=basic
[BlockDefinition_title]
Name=Title
Handler=expLayoutsTitleBlockHandler
ContentParameters[]=title
ContentParameters[]=use_link
ViewTypes[]=default

Category=basic
[BlockDefinition_html]
Name=HTML snippet
Handler=expLayoutsHtmlBlockHandler
ContentParameters[]=content
ViewTypes[]=default

Category=basic
[BlockDefinition_rich_text]
Name=Rich text
Handler=expLayoutsRichTextBlockHandler
ContentParameters[]=content
ViewTypes[]=default
# rich_text is the view type of Netgen Layouts' rich text block; layouts imported from Netgen keep it
ViewTypes[]=rich_text
ViewTypeNames[rich_text]=Rich text

Category=basic
[BlockDefinition_image]
Name=Image
Handler=expLayoutsImageBlockHandler
ContentParameters[]=image_url
ContentParameters[]=alt
ContentParameters[]=link
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
ViewTypeNames[list]=List
ViewTypeNames[grid]=Grid
ViewTypeNames[list_numbered]=Numbered list
ViewTypeNames[list_zigzag]=Zig-zag
ViewTypeNames[list_accordion]=Accordion
ViewTypeNames[grid_featured]=Grid (first item large)
# the item views of the zig-zag and the accordion list; the handler's list in code does not have them
ItemViewTypes[zigzag]=Zig-zag
ItemViewTypes[accordion]=Accordion
HasCollection=1

Category=listing
[BlockDefinition_single]
Name=Single content
Handler=expLayoutsSingleBlockHandler
ContentParameters[]=node_id
ViewTypes[]=default

Category=placeholders
[BlockDefinition_button]
Name=Button / Link
Handler=expLayoutsButtonBlockHandler
ContentParameters[]=text
ContentParameters[]=use_link
ContentParameters[]=label
ContentParameters[]=url
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
ContentParameters[]=title
ContentParameters[]=image_url
ContentParameters[]=content
ContentParameters[]=link_url
ContentParameters[]=link_label
ViewTypes[]=default

Category=basic
[BlockDefinition_accordion]
Name=Accordion
Handler=expLayoutsAccordionBlockHandler
ContentParameters[]=items
ViewTypes[]=default

Category=basic
[BlockDefinition_tabs]
Name=Tabs
Handler=expLayoutsTabsBlockHandler
ContentParameters[]=items
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
# The reference has one gallery block whose view types are the gallery
# variants; here each variant is a block of its own in the toolbox, and
# every one offers all view types in the Design tab, its own first (a new
# block gets the first one). A block stored with view type 'default'
# renders as its definition (expLayoutsGalleryBlockHandler::galleryViewType()).
# ViewTypeNames[] are the labels of the View type select.
ViewTypes[]=gallery
ViewTypes[]=slider
ViewTypes[]=thumb_gallery
ViewTypes[]=grid_gallery
ViewTypes[]=sushi_bar
ViewTypeNames[gallery]=Gallery
ViewTypeNames[slider]=Slider
ViewTypeNames[thumb_gallery]=Thumb gallery
ViewTypeNames[grid_gallery]=Grid gallery
ViewTypeNames[sushi_bar]=Sushi bar
HasCollection=1

Category=gallery
[BlockDefinition_slider]
Name=Slider
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=slider
ViewTypes[]=gallery
ViewTypes[]=thumb_gallery
ViewTypes[]=grid_gallery
ViewTypes[]=sushi_bar
ViewTypeNames[slider]=Slider
ViewTypeNames[gallery]=Gallery
ViewTypeNames[thumb_gallery]=Thumb gallery
ViewTypeNames[grid_gallery]=Grid gallery
ViewTypeNames[sushi_bar]=Sushi bar
HasCollection=1

Category=gallery
[BlockDefinition_thumb_gallery]
Name=Thumb gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=thumb_gallery
ViewTypes[]=gallery
ViewTypes[]=slider
ViewTypes[]=grid_gallery
ViewTypes[]=sushi_bar
ViewTypeNames[thumb_gallery]=Thumb gallery
ViewTypeNames[gallery]=Gallery
ViewTypeNames[slider]=Slider
ViewTypeNames[grid_gallery]=Grid gallery
ViewTypeNames[sushi_bar]=Sushi bar
HasCollection=1

Category=gallery
[BlockDefinition_grid_gallery]
Name=Grid gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=grid_gallery
ViewTypes[]=gallery
ViewTypes[]=slider
ViewTypes[]=thumb_gallery
ViewTypes[]=sushi_bar
ViewTypeNames[grid_gallery]=Grid gallery
ViewTypeNames[gallery]=Gallery
ViewTypeNames[slider]=Slider
ViewTypeNames[thumb_gallery]=Thumb gallery
ViewTypeNames[sushi_bar]=Sushi bar
HasCollection=1

Category=gallery
[BlockDefinition_sushi_bar]
Name=Sushi bar
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=sushi_bar
ViewTypes[]=gallery
ViewTypes[]=slider
ViewTypes[]=thumb_gallery
ViewTypes[]=grid_gallery
ViewTypeNames[sushi_bar]=Sushi bar
ViewTypeNames[gallery]=Gallery
ViewTypeNames[slider]=Slider
ViewTypeNames[thumb_gallery]=Thumb gallery
ViewTypeNames[grid_gallery]=Grid gallery
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
ContentParameters[]=quote
ContentParameters[]=author
ContentParameters[]=source
ViewTypes[]=default

Category=components
[BlockDefinition_alert]
Name=Alert
Handler=expLayoutsAlertBlockHandler
ContentParameters[]=message
ViewTypes[]=default

Category=basic
[BlockDefinition_video]
Name=External video
Handler=expLayoutsVideoBlockHandler
ContentParameters[]=service
ContentParameters[]=video_id
ContentParameters[]=caption
ContentParameters[]=video_url
ViewTypes[]=default

Category=basic
[BlockDefinition_badge]
Name=Badge
Handler=expLayoutsBadgeBlockHandler
ContentParameters[]=label
ContentParameters[]=link
ViewTypes[]=default

Category=basic
[BlockDefinition_progress]
Name=Progress bar
Handler=expLayoutsProgressBlockHandler
ContentParameters[]=value
ContentParameters[]=label
ViewTypes[]=default

Category=basic
[BlockDefinition_map]
Name=Map
Handler=expLayoutsMapBlockHandler
ContentParameters[]=latitude
ContentParameters[]=longitude
ContentParameters[]=zoom
ContentParameters[]=map_type
ContentParameters[]=show_marker
ContentParameters[]=embed_url
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
ContentParameters[]=content
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
ContentParameters[]=title
ContentParameters[]=content
ContentParameters[]=image_url
ContentParameters[]=image_alt
ContentParameters[]=link_text
ContentParameters[]=link_url
ViewTypes[]=default

Category=components
[BlockDefinition_about]
Name=About
Handler=expLayoutsComponentBlockHandler
ContentParameters[]=title
ContentParameters[]=content
ContentParameters[]=image_url
ContentParameters[]=image_alt
ContentParameters[]=link_text
ContentParameters[]=link_url
ViewTypes[]=default

Category=components
[BlockDefinition_features]
Name=Features
Handler=expLayoutsFeaturesBlockHandler
ContentParameters[]=title
ContentParameters[]=content
ContentParameters[]=items
ContentParameters[]=image_url
ContentParameters[]=image_alt
ContentParameters[]=link_text
ContentParameters[]=link_url
ViewTypes[]=default

Category=components
[BlockDefinition_lead]
Name=Lead
Handler=expLayoutsComponentBlockHandler
ContentParameters[]=title
ContentParameters[]=content
ContentParameters[]=image_url
ContentParameters[]=image_alt
ContentParameters[]=link_text
ContentParameters[]=link_url
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
# The view types of the reference. A block stored with the former view type three_columns renders 33:33:33.
ViewTypes[]=three_columns_33_33_33
ViewTypes[]=three_columns_50_25_25
ViewTypes[]=three_columns_25_50_25
ViewTypes[]=three_columns_25_25_50
ViewTypeNames[three_columns_33_33_33]=33:33:33
ViewTypeNames[three_columns_50_25_25]=50:25:25
ViewTypeNames[three_columns_25_50_25]=25:50:25
ViewTypeNames[three_columns_25_25_50]=25:25:50
IsContainer=1
Placeholders[]=col_1
Placeholders[]=col_2
Placeholders[]=col_3

Category=containers
[BlockDefinition_four_columns]
Name=Four columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=four_columns
ViewTypeNames[four_columns]=25:25:25:25
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
ContentParameters[]=content
ViewTypes[]=hero_style_1
ViewTypes[]=hero_style_2
ViewTypes[]=hero_style_3

Category=components
[BlockDefinition_exp_component_features]
Name=Features component
Handler=expLayoutsContentComponentBlockHandler
ContentParameters[]=content
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
ContentParameters[]=content
ViewTypes[]=about_style_1
ViewTypes[]=about_style_2
ViewTypes[]=about_style_3
ViewTypes[]=about_style_4
ViewTypes[]=about_style_5

Category=components
[BlockDefinition_exp_component_logos]
Name=Logos component
Handler=expLayoutsContentComponentBlockHandler
ContentParameters[]=content
ViewTypes[]=logos_style_1
ViewTypes[]=logos_style_2

Category=components
[BlockDefinition_exp_component_quote]
Name=Quote component
Handler=expLayoutsContentComponentBlockHandler
ContentParameters[]=content
ViewTypes[]=quote_style_1

Category=components
[BlockDefinition_exp_component_lead]
Name=Lead component
Handler=expLayoutsContentComponentBlockHandler
ContentParameters[]=content
ViewTypes[]=lead_style_1
ViewTypes[]=lead_style_2

Category=components
[QuerySettings]
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
AvailableBlocks[]=admin_logo
AvailableBlocks[]=admin_search
AvailableBlocks[]=admin_theme_switch
AvailableBlocks[]=admin_menu_button
AvailableBlocks[]=admin_sidebar_controls
AvailableBlocks[]=admin_sidebar_toggles
AvailableBlocks[]=admin_tab_menu
AvailableBlocks[]=admin_left_menu
AvailableBlocks[]=admin_content_tree
AvailableBlocks[]=admin_clear_cache
AvailableBlocks[]=admin_bookmarks
AvailableBlocks[]=admin_ui_switch
AvailableBlocks[]=admin_current_user
AvailableBlocks[]=admin_preferences
AvailableBlocks[]=admin_quick_settings
AvailableBlocks[]=admin_toolbar_right
AvailableBlocks[]=admin_toolbar_developer
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

[BlockDefinition_admin_menu_button]
Name=Admin: menu button (phone width)
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_header
Group=admin

[BlockDefinition_admin_sidebar_controls]
Name=Admin: sidebar controls
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_header
Group=admin

# Kept for layouts stored before the split: the menu button and the sidebar controls in one block. The page draws
# it with the rest of the header zone, after the tools (the old Tab order); new layouts use the two blocks above.
[BlockDefinition_admin_sidebar_toggles]
Name=Admin: menu button and sidebar controls (combined)
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

[BlockDefinition_admin_ui_switch]
Name=Admin: interface choices (language)
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

[BlockDefinition_admin_toolbar_right]
Name=Admin: right toolbar (toolbar.ini admin_right)
Handler=expLayoutsAdminPartBlockHandler
ViewTypes[]=default
Category=admin_sidebar
Group=admin

[BlockDefinition_admin_toolbar_developer]
Name=Admin: developer toolbar (toolbar.ini admin_developer)
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
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/1_column.tpl
Zones[]=main

[LayoutType_2_column]
Name=2 columns
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/2_column.tpl
Zones[]=left
Zones[]=right

[LayoutType_3_column]
Name=3 columns
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/3_column.tpl
Zones[]=left
Zones[]=main
Zones[]=right

[LayoutType_4_column]
Name=4 columns
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/4_column.tpl
Zones[]=col1
Zones[]=col2
Zones[]=col3
Zones[]=col4

[LayoutType_hero]
Name=Hero + 3 columns
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/hero.tpl
Zones[]=top
Zones[]=left
Zones[]=main
Zones[]=right

[LayoutType_sidebar_left]
Name=Sidebar left
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/sidebar_left.tpl
Zones[]=sidebar
Zones[]=main

[LayoutType_sidebar_right]
Name=Sidebar right
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/sidebar_right.tpl
Zones[]=main
Zones[]=sidebar

[LayoutType_featured]
Name=Featured
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/featured.tpl
Zones[]=hero
Zones[]=feature1
Zones[]=feature2
Zones[]=feature3
Zones[]=bottom

[LayoutType_mosaic]
Name=Mosaic
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/mosaic.tpl
Zones[]=a
Zones[]=b
Zones[]=c
Zones[]=d
Zones[]=e

# The six layout types of the reference (netgen/layouts-standard, layout_types.yaml), with its zones and, in their
# templates, its grid: layout_3 left 4/right 8 columns, layout_4 8/4, layout_5 6/6, layout_6 top and left/center in
# 8 columns (8/4) beside right in 4.
[LayoutType_layout_1]
Name=Single zone
Template=explayouts/layouts/layout_1.tpl
Zones[]=main

[LayoutType_layout_2]
Name=Header / Main / Footer
Template=explayouts/layouts/layout_2.tpl
Zones[]=header
Zones[]=post_header
Zones[]=main
Zones[]=pre_footer
Zones[]=footer

[LayoutType_layout_3]
Name=Header / Left 33 / Right 66 / Footer
Template=explayouts/layouts/layout_3.tpl
Zones[]=header
Zones[]=post_header
Zones[]=left
Zones[]=right
Zones[]=pre_footer
Zones[]=footer

[LayoutType_layout_4]
Name=Header / Left 66 / Right 33 / Footer
Template=explayouts/layouts/layout_4.tpl
Zones[]=header
Zones[]=post_header
Zones[]=left
Zones[]=right
Zones[]=pre_footer
Zones[]=footer

[LayoutType_layout_5]
Name=Header / Left 50 / Right 50 / Footer
Template=explayouts/layouts/layout_5.tpl
Zones[]=header
Zones[]=post_header
Zones[]=left
Zones[]=right
Zones[]=pre_footer
Zones[]=footer

[LayoutType_layout_6]
Name=Header / Top, Left, Center / Right / Footer
Template=explayouts/layouts/layout_6.tpl
Zones[]=header
Zones[]=post_header
Zones[]=top
Zones[]=left
Zones[]=center
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
# no main_top and main_bottom: admin4l draws those zones only inside #maincolumn, which this type does not have
Zones[]=header
Zones[]=topmenu
Zones[]=main
Zones[]=footer

# The Exp Layouts panel of the Exp Debug bar (settings/debugbar.ini.append.php). Its links into the administration
# go to AdminURL when set (e.g. https://admin.example.com, for an admin on its own host), else to the site's
# directory followed by the first plain name of [AdminLayoutSettings] SiteAccessMatch[].
[DebugBarSettings]
AdminURL=

# The admin layouts: the pages of the administration interface assembled from
# layouts, zones and blocks (design admin4l). Layout types with Group=admin are
# admin layouts. They are resolved by module and view for the siteaccesses
# listed here, and never reach the public site; site layouts never reach the
# admin.
[AdminLayoutSettings]
# enabled|disabled. disabled returns every admin page to plain admin4.
Enabled=enabled
# Siteaccess names (fnmatch patterns) the admin layouts apply to.
SiteAccessMatch[]=admin
SiteAccessMatch[]=admin_*
SiteAccessMatch[]=*_admin
SiteAccessMatch[]=admintest_*
SiteAccessMatch[]=editor
# Layout used when no admin rule matches the module and view (identifier of a
# published admin layout). Empty: nothing resolves and the page is plain admin4.
DefaultLayout=admin_3col
# Seconds a resolved answer is remembered. Publishing an admin layout or
# changing a rule clears it at once.
CacheTTL=3600

[TemplateEditorSettings]
# Folders, relative to the installation, whose .tpl files the template editor
# (explayouts/template_editor, explayouts_ui/template_editor) lists, reads and
# writes. Shipped: the templates of the layout types and blocks only. Every
# template in a root can be changed by whoever may open the editor, so do not
# add design/ or extension/ as a whole: they hold the administration's page
# layout and every admin override. A site design that overrides layout
# templates is added with its own folder, for example
#   AllowedTemplateRoots[]=extension/mysite/design/mysite/templates/explayouts
# An empty list allows nothing.
AllowedTemplateRoots[]=extension/explayouts/design/standard/templates/explayouts/layouts
AllowedTemplateRoots[]=extension/explayouts/design/standard/templates/explayouts/block
AllowedTemplateRoots[]=extension/explayouts/design/standard/templates/explayouts/tpl_block
# enabled: the editor may create a .tpl that does not exist yet inside a root
# (its New button is shown). disabled: only existing templates can be saved.
AllowNewTemplates=disabled
# Before a template is overwritten, its previous version is copied to
# <var directory>/explayouts/template_backup/<path>, one generation (the next
# save replaces it); no .tpl.bak is written next to the template. The var
# directory must not be served by the web server: the shipped .htaccess_root
# of Exponential serves only listed storage and cache paths below var/.
# Symbolic links inside a root are never read or written.

[ResolverSettings]
DefaultLayout=1d96945167435ac185b86cc0f9ef7084
CacheTTL=3600
# Entries the resolver cache (var/<vardir>/cache/explayouts/resolver) keeps at most: every path asked for gets one,
# also the made-up paths of robots. Expired entries are removed first, then the oldest.
CacheMaxEntries=5000

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

[MarkdownSettings]
# enabled (the default, so markdown blocks stored before keep their look): every line break is a <br />.
# disabled: as in CommonMark (and the reference), a single line break inside a paragraph is a soft break; a hard
# break takes two spaces or a backslash at the end of the line. enabled: every line break is a <br />, as the markdown
# block printed its text (escaped, with nl2br) before it rendered Markdown - for blocks stored then.
HardBreaks=enabled

[MapSettings]
# Key of the Maps Embed API of Google for the map block. Set: a map with a point (latitude, longitude) is Google's,
# as in the reference, with its map types roadmap and satellite. Empty: the map is OpenStreetMap's (one map type).
# The key is part of the address of the embedded map, so restrict it to the site's addresses in Google's console.
# Either way the map loads after a click in the standard templates and in cjw_bootstrap5.
GoogleMapsApiKey=

[PreviewSettings]
# The public siteaccess an administration renders layouts in (the layout preview, Load more of a list block called
# from the administration). Set it in the settings of the administration siteaccess. Empty: the administration's
# site.ini [SiteSettings] FrontSiteAccess, else the first of its RelatedSiteAccessList[] that is not an
# administration, else DefaultAccess. The siteaccess must use the same database as the administration; if it does
# not, the preview says so instead of rendering. Not set here: a value in an extension's global settings would win
# over the one of the administration siteaccess.
#SiteAccess=

*/ ?>
