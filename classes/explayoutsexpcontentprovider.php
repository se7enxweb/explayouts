<?php


class expLayoutsExpContentProvider
{
    public function provideContent( $contentId = null )
    {
        if ( $contentId !== null )
            return expLayoutsSiteAPI::loadContent( (int)$contentId );

        return expLayoutsSiteAPI::loadContent( 1 );
    }

    public function provideLocation( $locationId = null )
    {
        if ( $locationId !== null )
            return expLayoutsSiteAPI::loadLocation( (int)$locationId );

        return expLayoutsSiteAPI::loadLocation( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );
    }


    public function provideChildren( $locationId = null )
    {
        $location = $locationId !== null ? expLayoutsSiteAPI::loadLocation( (int)$locationId ) : $this->provideLocation();
        if ( !$location instanceof eZContentObjectTreeNode )
            return array();
        return expLayoutsSiteAPI::filterChildren( (int)$location->attribute( 'node_id' ), array( 'limit' => 25 ) );
    }
}
