<?php


class expLayoutsSiteBundlePagerfanta
{
    public static function createPager( $query, $perPage = 10, $currentPage = 1 )
    {
        return expLayoutsPager::create( $query, $perPage, $currentPage );
    }


    public static function paginate( array $items, $page = 1, $limit = 25 )
    {
        $offset = ( (int)$page - 1 ) * (int)$limit;
        return array_slice( $items, $offset, $limit );
    }
}
