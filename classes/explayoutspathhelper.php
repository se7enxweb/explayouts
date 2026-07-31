<?php

/**
 * eZ Publish 4 port of Netgen SiteBundle PathHelper.
 *
 * Builds a breadcrumb-style path array for a given location.
 */
class expLayoutsPathHelper
{
    public function getPath( $locationId, array $options = array() )
    {
        $options = $this->resolveOptions( $options );

        $locationId = (int)$locationId;
        $node = expLayoutsSiteAPI::loadLocation( $locationId );
        if ( !$node instanceof eZContentObjectTreeNode )
            return array();

        $rootLocationId = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        $excludedContentTypes = isset( $options['excluded_content_types'] ) ? (array)$options['excluded_content_types'] : array();

        $path = $node->pathArray();
        array_shift( $path ); // remove root (1)

        $pathArray = array();
        $rootLocationFound = false;

        foreach ( $path as $pathItemId )
        {
            $pathItemId = (int)$pathItemId;

            if ( $pathItemId === $rootLocationId )
                $rootLocationFound = true;

            if ( !$rootLocationFound )
                continue;

            $location = expLayoutsSiteAPI::loadLocation( $pathItemId );
            if ( !$location instanceof eZContentObjectTreeNode )
                continue;

            if ( !$options['show_current_location'] && $pathItemId === $locationId )
                continue;

            $content = $location->object();
            $contentTypeIdentifier = $content ? $content->attribute( 'class_identifier' ) : '';

            if ( !$options['use_all_content_types'] && in_array( $contentTypeIdentifier, $excludedContentTypes, true ) )
                continue;

            $disableItemUrl = $options['use_all_content_types'] && in_array( $contentTypeIdentifier, $excludedContentTypes, true );

            $itemName = $location->attribute( 'name' );
            $dataMap = $content ? $content->attribute( 'data_map' ) : array();
            if ( isset( $dataMap['breadcrumb_title'] ) && is_object( $dataMap['breadcrumb_title'] ) )
            {
                $breadcrumbTitle = trim( $dataMap['breadcrumb_title']->content() );
                if ( $breadcrumbTitle !== '' )
                    $itemName = $breadcrumbTitle;
            }

            $url = false;
            if ( !$disableItemUrl && $pathItemId !== $locationId )
            {
                $url = $location->attribute( 'url_alias' );
                if ( $options['absolute_url'] )
                    $url = eZSys::indexURL() . $url;
            }

            $pathArray[] = array(
                'text' => $itemName,
                'url' => $url,
                'location' => $location,
            );
        }

        return $pathArray;
    }

    private function resolveOptions( array $options )
    {
        $defaults = array(
            'use_all_content_types' => false,
            'show_current_location' => false,
            'absolute_url' => false,
            'excluded_content_types' => array(),
        );

        foreach ( $defaults as $key => $value )
        {
            if ( !isset( $options[$key] ) )
                $options[$key] = $value;
        }

        return $options;
    }
}
