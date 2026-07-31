<?php


class expLayoutsExpCollection
{
    public function getQueryTypes()
    {
        return array(
            'children' => 'expLayoutsExpRelationListQuery',
            'reverse_relation' => 'expLayoutsExpReverseRelationListQuery',
            'tags' => 'expLayoutsExpTagsQuery',
        );
    }

    public function getValues( $queryType, array $params = array() )
    {
        switch ( $queryType )
        {
            case 'children':
                return expLayoutsExpRelationListQuery::getValues( $params['content_id'], $params['field'] );
            case 'reverse_relation':
                return expLayoutsExpReverseRelationListQuery::getValues( $params['content_id'], $params['field'] );
            case 'tags':
                return expLayoutsExpTagsQuery::getValues( $params['tag'], $params );
        }
        return array();
    }

    public function getCount( $queryType, array $params = array() )
    {
        return count( $this->getValues( $queryType, $params ) );
    }


    public function getAvailableQueryTypes()
    {
        return array_keys( $this->getQueryTypes() );
    }

    public function getQueryTypeNames()
    {
        return $this->getQueryTypes();
    }
}
