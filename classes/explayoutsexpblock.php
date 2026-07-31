<?php


class expLayoutsExpBlock
{
    public function getConfig( $blockIdentifier )
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        $group = 'BlockDefinition_' . $blockIdentifier;
        if ( !$ini->hasGroup( $group ) )
            return array();

        return $ini->group( $group );
    }


    public function hasBlock( $blockIdentifier )
    {
        return count( $this->getConfig( $blockIdentifier ) ) > 0;
    }

    public function getBlockIdentifiers()
    {
        $ini = eZINI::instance( 'explayouts.ini' );
        return $ini->hasGroup( 'BlockDefinition' ) ? array_keys( $ini->group( 'BlockDefinition' ) ) : array();
    }
}
