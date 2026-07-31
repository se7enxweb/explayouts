<?php

/**
 * eZ Publish 4 port of Netgen SiteBundle RedirectHelper.
 *
 * Checks a location's content for internal_redirect (ezobjectrelation)
 * and external_redirect (ezurl/ezstring) fields and returns a redirect URL.
 */
class expLayoutsRedirectHelper
{
    public function checkRedirect( $location )
    {
        if ( !$location instanceof eZContentObjectTreeNode )
            return false;

        $content = expLayoutsSiteAPI::loadContent( (int)$location->attribute( 'contentobject_id' ) );
        if ( !$content instanceof eZContentObject )
            return false;

        $dataMap = $content->attribute( 'data_map' );

        if ( isset( $dataMap['internal_redirect'] ) && is_object( $dataMap['internal_redirect'] ) )
        {
            $internalRedirect = $dataMap['internal_redirect']->content();
            if ( is_numeric( $internalRedirect ) && (int)$internalRedirect > 0 )
            {
                $targetContent = expLayoutsSiteAPI::loadContent( (int)$internalRedirect );
                if ( $targetContent instanceof eZContentObject )
                {
                    $targetNode = $targetContent->mainNode();
                    if ( $targetNode instanceof eZContentObjectTreeNode && (int)$targetNode->attribute( 'node_id' ) !== (int)$location->attribute( 'node_id' ) )
                    {
                        return array(
                            'url' => $targetNode->attribute( 'url_alias' ),
                            'status' => 301,
                        );
                    }
                }
            }
        }

        if ( isset( $dataMap['external_redirect'] ) && is_object( $dataMap['external_redirect'] ) )
        {
            $externalRedirect = trim( $dataMap['external_redirect']->content() );
            if ( $externalRedirect !== '' )
            {
                if ( strpos( $externalRedirect, 'http' ) === 0 )
                {
                    return array(
                        'url' => $externalRedirect,
                        'status' => 301,
                    );
                }

                $rootNode = expLayoutsSiteAPI::loadLocation( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );
                $rootUrl = $rootNode instanceof eZContentObjectTreeNode ? $rootNode->attribute( 'url_alias' ) : '/';
                $url = rtrim( $rootUrl, '/' ) . '/' . ltrim( $externalRedirect, '/' );

                return array(
                    'url' => $url,
                    'status' => 301,
                );
            }
        }

        return false;
    }
}
