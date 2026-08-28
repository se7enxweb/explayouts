<?php /* #?ini charset="utf-8"?

[BlockSettings]
AvailableBlocks[]
AvailableBlocks[]=text
AvailableBlocks[]=title
AvailableBlocks[]=markdown
AvailableBlocks[]=html
AvailableBlocks[]=image
AvailableBlocks[]=video
AvailableBlocks[]=rich_text
AvailableBlocks[]=map
AvailableBlocks[]=button
AvailableBlocks[]=single
AvailableBlocks[]=spacer
AvailableBlocks[]=divider
AvailableBlocks[]=quote
AvailableBlocks[]=card
AvailableBlocks[]=alert
AvailableBlocks[]=badge
AvailableBlocks[]=progress
AvailableBlocks[]=accordion
AvailableBlocks[]=tabs
AvailableBlocks[]=list
AvailableBlocks[]=grid
AvailableBlocks[]=gallery
AvailableBlocks[]=slider
AvailableBlocks[]=thumb_gallery
AvailableBlocks[]=grid_gallery
AvailableBlocks[]=sushi_bar
AvailableBlocks[]=list_zigzag
AvailableBlocks[]=list_accordion
AvailableBlocks[]=carousel
AvailableBlocks[]=twig_block
AvailableBlocks[]=full_view
AvailableBlocks[]=hero
AvailableBlocks[]=about
AvailableBlocks[]=features
AvailableBlocks[]=logos
AvailableBlocks[]=lead
AvailableBlocks[]=column
AvailableBlocks[]=two_columns
AvailableBlocks[]=three_columns
AvailableBlocks[]=four_columns

[BlockDefinition_two_columns]
Name=Two columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=two_columns_66_33
ViewTypes[]=two_columns_33_66
IsContainer=1
Placeholders[]=left
Placeholders[]=right

[BlockDefinition_column]
Name=Column
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=column
IsContainer=1
Placeholders[]=main

[BlockDefinition_twig_block]
Name=Twig block
Handler=expLayoutsTwigBlockHandler
ViewTypes[]=twig_block

[BlockDefinition_text]
Name=Text
Handler=expLayoutsTextBlockHandler
ViewTypes[]=default

[BlockDefinition_title]
Name=Title
Handler=expLayoutsTitleBlockHandler
ViewTypes[]=default

[BlockDefinition_html]
Name=HTML snippet
Handler=expLayoutsHtmlBlockHandler
ViewTypes[]=default

[BlockDefinition_rich_text]
Name=Rich text
Handler=expLayoutsRichTextBlockHandler
ViewTypes[]=default

[BlockDefinition_image]
Name=Image
Handler=expLayoutsImageBlockHandler
ViewTypes[]=default

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

[BlockDefinition_single]
Name=Exponential content field
Handler=expLayoutsSingleBlockHandler
ViewTypes[]=default

[BlockDefinition_button]
Name=Button / Link
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

[BlockDefinition_slider]
Name=Slider
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_thumb_gallery]
Name=Thumb gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_grid_gallery]
Name=Grid gallery
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_sushi_bar]
Name=Sushi bar
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_list_zigzag]
Name=Zig-Zag (List)
Handler=expLayoutsListBlockHandler
ViewTypes[]=list_zigzag
HasCollection=1

[BlockDefinition_list_accordion]
Name=Accordion (List)
Handler=expLayoutsListBlockHandler
ViewTypes[]=list_accordion
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
Name=External video
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

[BlockDefinition_markdown]
Name=Markdown
Handler=expLayoutsMarkdownBlockHandler
ViewTypes[]=default

[BlockDefinition_full_view]
Name=Full view
Handler=expLayoutsFullViewBlockHandler
ViewTypes[]=default

[BlockDefinition_hero]
Name=Hero
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

[BlockDefinition_about]
Name=About
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

[BlockDefinition_features]
Name=Features
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

[BlockDefinition_lead]
Name=Lead
Handler=expLayoutsComponentBlockHandler
ViewTypes[]=default

[BlockDefinition_logos]
Name=Logos
Handler=expLayoutsGalleryBlockHandler
ViewTypes[]=default
HasCollection=1

[BlockDefinition_three_columns]
Name=Three columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=three_columns
IsContainer=1
Placeholders[]=col_1
Placeholders[]=col_2
Placeholders[]=col_3

[BlockDefinition_four_columns]
Name=Four columns
Handler=expLayoutsContainerBlockHandler
ViewTypes[]=four_columns
IsContainer=1
Placeholders[]=col_1
Placeholders[]=col_2
Placeholders[]=col_3
Placeholders[]=col_4

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

[TemplateEditorSettings]
AllowedTemplateRoots[]
AllowedTemplateRoots[]=design
AllowedTemplateRoots[]=extension

[ResolverSettings]
DefaultLayout=
CacheTTL=3600

[NexusNodeMap]
190=744
195=749
218=218
619=619
721=721
722=722
749=749
752=752
911=911
939=939
940=940
941=941
946=946
947=947
953=953
959=959
1060=1060
1061=1061

*/ ?>
