<?php

class expLayoutsExpForm
{
    public function getFormFields( $content )
    {
        $contentObject = expLayoutsSiteAPI::getContentObject( $content );
        if ( !$contentObject instanceof eZContentObject )
            return array();

        $dataMap = $contentObject->attribute( 'data_map' );
        $fields = array();
        foreach ( $dataMap as $identifier => $attribute )
        {
            if ( is_object( $attribute ) )
                $fields[$identifier] = $attribute->attribute( 'data_type_string' );
        }
        return $fields;
    }

    public function submit( $contentId, array $data = array() )
    {
        $contentObject = expLayoutsSiteAPI::loadContent( (int)$contentId );
        if ( !$contentObject instanceof eZContentObject )
            return false;

        return expLayoutsSiteBundleContentForms::setFieldValue( $contentId, 'title', isset( $data['title'] ) ? $data['title'] : '' );
    }
}