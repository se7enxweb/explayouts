<?php

class expLayoutsExpSiteApiContentProvider
{
    public function provideContent( $locationId = null )
    {
        if ( $locationId !== null )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$locationId );
            if ( $node instanceof eZContentObjectTreeNode )
                return $node->attribute( 'object' );
            return false;
        }

        return expLayoutsExpSiteApi::currentObject();
    }

    public function provideLocation( $locationId = null )
    {
        if ( $locationId !== null )
            return eZContentObjectTreeNode::fetch( (int)$locationId );

        return expLayoutsExpSiteApi::currentNode();
    }
}