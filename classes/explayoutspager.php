<?php

/**
 * Exponential 4 pagination helper compatible with the Exp Site API Pagerfanta adapters.
 *
 * Wraps a Site API query and exposes page-by-page results.
 */
class expLayoutsPager
{
    protected $query;
    protected $perPage;
    protected $currentPage;
    protected $type;

    public function __construct( $query, $perPage = 10, $currentPage = 1, $type = 'location' )
    {
        $this->query = is_array( $query ) ? $query : array();
        $this->perPage = max( 1, (int)$perPage );
        $this->currentPage = max( 1, (int)$currentPage );
        $this->type = in_array( $type, array( 'content', 'location' ) ) ? $type : 'location';
    }

    public static function create( $query, $perPage = 10, $currentPage = 1, $type = 'location' )
    {
        return new self( $query, $perPage, $currentPage, $type );
    }

    public function getResults()
    {
        $query = $this->query;
        $query['limit'] = $this->perPage;
        $query['offset'] = $this->getOffset();

        if ( $this->type === 'content' )
            return expLayoutsSiteAPI::filterContent( $query );

        return expLayoutsSiteAPI::filterLocations( $query );
    }

    public function getItems()
    {
        $result = $this->getResults();
        return isset( $result['items'] ) ? $result['items'] : array();
    }

    public function getTotal()
    {
        $result = $this->getResults();
        return isset( $result['total'] ) ? (int)$result['total'] : 0;
    }

    public function getTotalPages()
    {
        $total = $this->getTotal();
        return $total > 0 ? (int)ceil( $total / $this->perPage ) : 0;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function getOffset()
    {
        return ( $this->currentPage - 1 ) * $this->perPage;
    }

    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage()
    {
        return $this->currentPage < $this->getTotalPages();
    }

    public function getPreviousPage()
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : 1;
    }

    public function getNextPage()
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : $this->getTotalPages();
    }

    public function getPageUrl( $page, $baseUrl = '' )
    {
        $separator = strpos( $baseUrl, '?' ) === false ? '?' : '&';
        return $baseUrl . $separator . 'page=' . (int)$page;
    }

    public function getPages( $proximity = 2 )
    {
        $pages = array();
        $total = $this->getTotalPages();
        $start = max( 1, $this->currentPage - $proximity );
        $end = min( $total, $this->currentPage + $proximity );

        for ( $i = $start; $i <= $end; $i++ )
            $pages[] = $i;

        return $pages;
    }
}
