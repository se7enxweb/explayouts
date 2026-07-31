<?php


class expLayoutsExpSearch
{
    public function search( $text, array $options = array() )
    {
        return expLayoutsFullTextCriterion::getQuery( $text, $options );
    }


    public function findByClass( $text, $classIdentifier, $limit = 25 )
    {
        return expLayoutsQueryType::fullText( $text, array( 'class_filter', array( $classIdentifier ), 'limit' => $limit ) );
    }

    public function findByPath( $pathString, $limit = 25 )
    {
        return expLayoutsQueryType::subtree( $pathString, array( 'limit' => $limit ) );
    }
}
