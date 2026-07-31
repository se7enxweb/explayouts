<?php

/**
 * eZ Publish 4 port of Netgen SiteBundle API/Search/Criterion/FullText.
 */
class expLayoutsFullTextCriterion
{
    protected $text;

    public function __construct( $text = '' )
    {
        $this->text = (string)$text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getQuery( array $options = array() )
    {
        $limit = isset( $options['limit'] ) ? (int)$options['limit'] : 25;
        $offset = isset( $options['offset'] ) ? (int)$options['offset'] : 0;

        return expLayoutsQueryType::fullText( $this->text, array( 'limit' => $limit, 'offset' => $offset ) );
    }

    public function getSpellcheckQuery()
    {
        // eZ Publish 4 legacy search has no spellcheck support.
        return null;
    }
}
