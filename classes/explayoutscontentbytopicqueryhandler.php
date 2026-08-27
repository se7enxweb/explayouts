<?php

class expLayoutsContentByTopicQueryHandler implements expLayoutsQueryHandlerInterface
{
    public function getName()
    {
        return 'Topics';
    }

    public function getParameters()
    {
        $classes = array();
        $classList = eZContentClass::fetchAllClasses( true, true );
        foreach ( $classList as $class )
        {
            $classes[(string)$class->attribute( 'identifier' )] = (string)$class->attribute( 'name' );
        }

        return array(
            'use_topic_from_current_content' => array(
                'name' => 'Use topic from current content',
                'type' => 'checkbox',
                'default' => '1',
            ),
            'topic_content_id' => array(
                'name' => 'Topic content',
                'type' => 'text',
                'default' => '',
            ),
            'parent_location_id' => array(
                'name' => 'Parent location',
                'type' => 'browse',
                'default' => '2',
            ),
            'use_current_location' => array(
                'name' => 'Use current location as parent',
                'type' => 'checkbox',
                'default' => '0',
            ),
            'sort_type' => array(
                'name' => 'Sort type',
                'type' => 'select',
                'options' => array(
                    'date_published' => 'Published',
                    'date_modified' => 'Modified',
                    'content_name' => 'Alphabetical',
                ),
                'default' => 'date_published',
            ),
            'sort_direction' => array(
                'name' => 'Sort direction',
                'type' => 'select',
                'options' => array(
                    'descending' => 'Descending',
                    'ascending' => 'Ascending',
                ),
                'default' => 'descending',
            ),
            'query_type' => array(
                'name' => 'Fetch type',
                'type' => 'select',
                'options' => array(
                    'list' => 'List',
                    'tree' => 'Tree',
                ),
                'default' => 'list',
            ),
            'only_main_locations' => array(
                'name' => 'Fetch only main locations',
                'type' => 'checkbox',
                'default' => '1',
            ),
            'exclude_current_location' => array(
                'name' => 'Exclude current location from results',
                'type' => 'checkbox',
                'default' => '1',
            ),
            'filter_by_content_type' => array(
                'name' => 'Filter by content type',
                'type' => 'checkbox',
                'default' => '0',
            ),
            'content_types' => array(
                'name' => 'Content types',
                'type' => 'multiselect',
                'options' => $classes,
                'default' => array(),
            ),
            'content_types_filter' => array(
                'name' => 'Filter type',
                'type' => 'select',
                'options' => array(
                    'include' => 'Include content types',
                    'exclude' => 'Exclude content types',
                ),
                'default' => 'include',
            ),
        );
    }

    public function fetch( $parameters )
    {
        $offset = isset( $parameters['offset'] ) ? (int)$parameters['offset'] : 0;
        $limit = isset( $parameters['limit'] ) ? (int)$parameters['limit'] : 0;

        if ( !isset( $parameters['parent_location_id'] ) || (int)$parameters['parent_location_id'] === 0 )
        {
            if ( isset( $parameters['parent_node_id'] ) && (int)$parameters['parent_node_id'] > 0 )
                $parameters['parent_location_id'] = (int)$parameters['parent_node_id'];
            elseif ( isset( $parameters['node_id'] ) && (int)$parameters['node_id'] > 0 )
                $parameters['parent_location_id'] = (int)$parameters['node_id'];
        }

        return expLayoutsDynamicCollection::contentByTopic( $parameters, $offset, $limit );
    }
}
