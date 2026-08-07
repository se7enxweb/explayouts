<?php /* #?ini charset="utf-8"?

[BlockSettings]
AvailableBlocks[]
AvailableBlocks[]=text
AvailableBlocks[]=title
AvailableBlocks[]=html
AvailableBlocks[]=image
AvailableBlocks[]=list
AvailableBlocks[]=single
AvailableBlocks[]=button
AvailableBlocks[]=spacer
AvailableBlocks[]=divider
AvailableBlocks[]=card
AvailableBlocks[]=accordion
AvailableBlocks[]=tabs
AvailableBlocks[]=grid
AvailableBlocks[]=gallery
AvailableBlocks[]=quote
AvailableBlocks[]=alert
AvailableBlocks[]=video
AvailableBlocks[]=badge
AvailableBlocks[]=progress
AvailableBlocks[]=map
AvailableBlocks[]=carousel

[BlockDefinition_text]
Name=Text / HTML
Handler=expLayoutsTextBlockHandler
ViewTypes[]=default

[BlockDefinition_title]
Name=Title
Handler=expLayoutsTitleBlockHandler
ViewTypes[]=default

[BlockDefinition_html]
Name=Raw HTML
Handler=expLayoutsHtmlBlockHandler
ViewTypes[]=default

[BlockDefinition_image]
Name=Image
Handler=expLayoutsImageBlockHandler
ViewTypes[]=default

[BlockDefinition_list]
Name=Content list
Handler=expLayoutsListBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_single]
Name=Single content
Handler=expLayoutsSingleBlockHandler
ViewTypes[]=default

[BlockDefinition_button]
Name=Button
Handler=expLayoutsButtonBlockHandler
ViewTypes[]=default

[BlockDefinition_spacer]
Name=Spacer
Handler=expLayoutsSpacerBlockHandler
ViewTypes[]=default

[BlockDefinition_divider]
Name=Divider
Handler=expLayoutsDividerBlockHandler
ViewTypes[]=default

[BlockDefinition_card]
Name=Card
Handler=expLayoutsCardBlockHandler
ViewTypes[]=default

[BlockDefinition_accordion]
Name=Accordion
Handler=expLayoutsAccordionBlockHandler
ViewTypes[]=default

[BlockDefinition_tabs]
Name=Tabs
Handler=expLayoutsTabsBlockHandler
ViewTypes[]=default

[BlockDefinition_grid]
Name=Grid
Handler=expLayoutsGridBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_gallery]
Name=Gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_quote]
Name=Quote
Handler=expLayoutsQuoteBlockHandler
ViewTypes[]=default

[BlockDefinition_alert]
Name=Alert
Handler=expLayoutsAlertBlockHandler
ViewTypes[]=default

[BlockDefinition_video]
Name=Video
Handler=expLayoutsVideoBlockHandler
ViewTypes[]=default

[BlockDefinition_badge]
Name=Badge
Handler=expLayoutsBadgeBlockHandler
ViewTypes[]=default

[BlockDefinition_progress]
Name=Progress bar
Handler=expLayoutsProgressBlockHandler
ViewTypes[]=default

[BlockDefinition_map]
Name=Map
Handler=expLayoutsMapBlockHandler
ViewTypes[]=default

[BlockDefinition_carousel]
Name=Carousel
Handler=expLayoutsCarouselBlockHandler
ViewTypes[]=default
HasCollection=1

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

[TemplateEditorSettings]
AllowedTemplateRoots[]
AllowedTemplateRoots[]=design
AllowedTemplateRoots[]=extension

[ResolverSettings]
DefaultLayout=
CacheTTL=3600
