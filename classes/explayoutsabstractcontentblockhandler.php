<?php

abstract class expLayoutsAbstractContentBlockHandler implements expLayoutsBlockHandlerInterface
{
    /**
     * The block's items, and the HTTP cache told what the page now shows:
     * each item's node and object, and for a query (anything but a manual
     * collection) that its result can change with any content change ("dq")
     * and with the children of its parent.
     */
    protected function fetchItems( $parameters, $block = false )
    {
        $source = $this->itemSource( $parameters, $block );
        $result = $this->fetchItemsFromSource( $parameters, $source );
        if ( class_exists( 'ezpHttpCacheListener' ) && is_array( $result ) )
        {
            $tags = array();
            foreach ( (array)( $result['items'] ?? array() ) as $item )
            {
                if ( $item instanceof eZContentObjectTreeNode )
                {
                    $tags[] = 'l' . (int)$item->attribute( 'node_id' );
                    $tags[] = 'c' . (int)$item->attribute( 'contentobject_id' );
                }
            }
            if ( $source['query_type'] !== 'manual' )
            {
                $tags[] = 'dq';
                $parent = (int)( $parameters['parent_node_id'] ?? $parameters['node_id'] ?? 0 );
                if ( $parent > 0 )
                    $tags[] = 'pl' . $parent;
            }
            ezpHttpCacheListener::addTags( $tags );
        }
        return $result;
    }

    /**
     * Where the block's items come from, worked out once per render: the
     * block's collection (fetched once) and the query type - 'dynamic' for a
     * dynamic collection, otherwise query_type, otherwise 'manual' for a
     * manual collection with items when the block names no parent node (the
     * editor gives every block with a collection a manual collection and adds
     * its items there, but never sets query_type: the block ran the
     * 'children' query of an empty parent and showed nothing), otherwise
     * 'children'. A parent node comes first, as in the gallery blocks.
     *
     * @return array( 'query_type' => string, 'collection' => expLayoutsCollection|null[, 'items' => the manual
     *               collection's items when they were fetched to decide] )
     */
    protected function itemSource( $parameters, $block = false )
    {
        $collection = null;
        if ( is_array( $block ) && isset( $block['id'] ) )
        {
            $collection = expLayoutsCollection::fetchByBlock( (int)$block['id'] );
            if ( !$collection )
                $collection = null;
        }

        if ( $collection && $collection->attribute( 'collection_type' ) === 'dynamic' )
            return array( 'query_type' => 'dynamic', 'collection' => $collection );

        $queryType = isset( $parameters['query_type'] ) ? trim( (string)$parameters['query_type'] ) : '';
        if ( $queryType === '' )
        {
            $parent = (int)( $parameters['parent_node_id'] ?? 0 );
            $node = (int)( $parameters['node_id'] ?? 0 );
            $queryType = 'children';
            if ( $parent <= 0 && $node <= 0 && $collection && $collection->attribute( 'collection_type' ) === 'manual' )
            {
                $items = expLayoutsCollectionItem::fetchByCollection( (int)$collection->attribute( 'id' ) );
                if ( count( $items ) > 0 )
                    return array( 'query_type' => 'manual', 'collection' => $collection, 'items' => $items );
            }
        }

        return array( 'query_type' => $queryType, 'collection' => $collection );
    }

    protected function fetchItemsFromQuery( $parameters, $block = false )
    {
        return $this->fetchItemsFromSource( $parameters, $this->itemSource( $parameters, $block ) );
    }

    protected function fetchItemsFromSource( $parameters, $source )
    {
        $collection = $source['collection'];
        $queryType = $source['query_type'];

        // Dynamic collections imported from the Nexus reference carry their own query row;
        // execute it ahead of the block-level query_type parameter.
        if ( $queryType === 'dynamic' )
        {
            $dynResult = expLayoutsDynamicCollection::fetch( $collection );
            if ( $dynResult !== false )
                return $dynResult;
            $queryType = isset( $parameters['query_type'] ) && trim( (string)$parameters['query_type'] ) !== '' ? trim( (string)$parameters['query_type'] ) : 'children';
        }

        $handler = expLayoutsQueryHandlerFactory::get( $queryType );

        if ( $handler )
        {
            $params = $parameters;

            if ( $queryType === 'manual' && $collection )
            {
                $params['collection_id'] = (int)$collection->attribute( 'id' );
                // already fetched by itemSource(): handed on, not fetched again
                if ( isset( $source['items'] ) )
                    $params['collection_items'] = $source['items'];
            }

            if ( !isset( $params['node_id'] ) || (int)$params['node_id'] === 0 )
                $params['node_id'] = isset( $params['parent_node_id'] ) ? (int)$params['parent_node_id'] : 0;

            return $handler->fetch( $params );
        }

        $parentNodeId = isset( $parameters['parent_node_id'] ) ? (int)$parameters['parent_node_id'] : 0;
        if ( $parentNodeId <= 0 )
            return array( 'total' => 0, 'items' => array() );

        $options = array(
            'limit' => isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 10,
            'offset' => isset( $parameters['offset'] ) ? (int)$parameters['offset'] : 0,
            'class_filter' => isset( $parameters['class_filter'] ) ? trim( $parameters['class_filter'] ) : '',
            'sort' => isset( $parameters['sort'] ) ? $parameters['sort'] : 'published',
        );

        return expLayoutsSiteAPI::filterChildren( $parentNodeId, $options );
    }

    /**
     * Design-time parameters rendered in the Design tab of the SPA sidebar.
     */
    public function getDesignParameters()
    {
        return array(
            'number_of_columns' => array(
                'name' => 'Number of columns',
                'type' => 'select',
                'options' => array(
                    '2' => '2 columns',
                    '3' => '3 columns',
                    '4' => '4 columns',
                    '6' => '6 columns',
                ),
                'default' => '3',
                'view_type' => 'grid,grid_featured',
            ),
            'background_color:enabled' => array(
                'name' => 'Set background color',
                'type' => 'compound_checkbox',
                'default' => '',
                'children' => array(
                    'background_color:color' => array(
                        'name' => 'Background color',
                        'type' => 'select',
                        'options' => array(
                            'primary' => 'Primary (Yellow)',
                            'secondary' => 'Secondary (Light gray)',
                            'white' => 'White',
                            'black' => 'Black',
                        ),
                        'default' => 'secondary',
                    ),
                ),
            ),
            'background_image:enabled' => array(
                'name' => 'Set background image',
                'type' => 'compound_checkbox',
                'default' => '',
                'children' => array(
                    'background_image:image' => array(
                        'name' => 'Background image',
                        'type' => 'browse',
                        'default' => '',
                    ),
                ),
            ),
            'vertical_whitespace:enabled' => array(
                'name' => 'Use vertical whitespace',
                'type' => 'compound_checkbox',
                'default' => '1',
                'children' => array(
                    'vertical_whitespace:top' => array(
                        'name' => 'On top',
                        'type' => 'select',
                        'options' => array(
                            'none' => 'None',
                            'small' => 'Small',
                            'medium' => 'Medium',
                            'large' => 'Large',
                        ),
                        'default' => 'none',
                    ),
                    'vertical_whitespace:bottom' => array(
                        'name' => 'On bottom',
                        'type' => 'select',
                        'options' => array(
                            'none' => 'None',
                            'small' => 'Small',
                            'medium' => 'Medium',
                            'large' => 'Large',
                        ),
                        'default' => 'medium',
                    ),
                ),
            ),
            'paged_collections:enabled' => array(
                'name' => 'Enable pagination',
                'type' => 'compound_checkbox',
                'default' => '',
                'children' => array(
                    'paged_collections:type' => array(
                        'name' => 'Pagination type',
                        'type' => 'select',
                        'options' => array(
                            'pager' => 'Pager',
                            'load_more' => 'Load more',
                        ),
                        'default' => 'load_more',
                    ),
                    'paged_collections:max_pages' => array(
                        'name' => 'Maximum number of pages',
                        'type' => 'integer',
                        'default' => '',
                    ),
                    'paged_collections:ajax_first' => array(
                        'name' => 'Load first page via AJAX',
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                ),
            ),
            'css_class' => array(
                'name' => 'CSS class',
                'type' => 'string',
                'default' => '',
            ),
            'css_id' => array(
                'name' => 'CSS ID',
                'type' => 'string',
                'default' => '',
            ),
            'set_container' => array(
                'name' => 'Wrap in container',
                'type' => 'compound_checkbox',
                'default' => '1',
                'children' => array(
                    'set_container:size' => array(
                        'name' => 'Container size',
                        'type' => 'select',
                        'options' => array(
                            '' => 'Regular',
                            'narrow' => 'Narrow',
                            'wide' => 'Wide',
                        ),
                        'default' => '',
                    ),
                ),
            ),
        );
    }

    /**
     * Available item view types per view type (used by master/slave selects).
     */
    public function getItemViewTypes()
    {
        return array(
            'standard' => 'Standard',
            'standard_with_intro' => 'Standard (with intro)',
            'overlay' => 'Overlay',
            'card' => 'Card',
            'card_with_intro' => 'Card (with intro)',
            'line' => 'Line',
            'mini' => 'Mini',
            'listitem' => 'List item',
            'listitem_with_intro' => 'List item (with intro)',
        );
    }
}
