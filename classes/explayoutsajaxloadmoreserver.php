<?php
/**
 * ezjscServer functions for loading paged collection blocks.
 *
 * URL: /ezjscore/call/expajaxloadmore::loadMore::<block_id>::<page>[?ContentType=html]
 */
class expLayoutsAjaxLoadMoreServer
{
    public static function loadMore( $args, &$environment, $isPackageStage = false )
    {
        $blockId = isset( $args[0] ) ? (int)$args[0] : 0;
        $page = isset( $args[1] ) ? (int)$args[1] : 1;
        if ( $blockId <= 0 || $page <= 0 )
            return '';

        $block = expLayoutsBlock::fetch( $blockId, true );
        if ( !$block )
            return '';

        $collection = expLayoutsCollection::fetchByBlock( $blockId, true );
        if ( !$collection || $collection->attribute( 'collection_type' ) !== 'dynamic' )
            return '';

        $limit = (int)$collection->attribute( 'limit_value' );
        if ( $limit <= 0 )
            $limit = 10;
        $baseOffset = (int)$collection->attribute( 'offset_value' );
        $offset = $baseOffset + ( $page - 1 ) * $limit;

        // Pinned/manual items occupy slots in the rendered grid but should not
        // consume dynamic results. Compensate offset by the number of manual
        // items placed on pages before the requested one.
        $manualItems = expLayoutsCollectionItem::fetchByCollection( $collection->attribute( 'id' ), true );
        $manualSkip = 0;
        $pageStart = ( $page - 1 ) * $limit;
        foreach ( $manualItems as $item )
        {
            $itemPosition = (int)$item->attribute( 'position' );
            if ( $itemPosition < $pageStart )
                $manualSkip++;
        }
        $offset -= $manualSkip;
        if ( $offset < $baseOffset )
            $offset = $baseOffset;

        // Temporarily override offset/limit for the requested page.
        $collection->setAttribute( 'offset_value', $offset );
        $collection->setAttribute( 'limit_value', $limit );

        $result = expLayoutsDynamicCollection::fetch( $collection );
        if ( $result === false || empty( $result['items'] ) )
            return '';

        $preparedBlock = expLayoutsRenderer::prepareBlock( $block );
        $preparedBlock['values'] = $result;

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'block', $preparedBlock );

        $viewType = $preparedBlock['view_type'];
        $itemView = $preparedBlock['item_view_type'];
        $viewLabel = $itemView;
        $withIntro = 0;
        if ( $itemView === 'standard_with_intro' )
        {
            $itemView = 'standard';
            $withIntro = 1;
        }
        elseif ( $itemView === 'listitem_with_intro' )
        {
            $itemView = 'listitem';
            $withIntro = 1;
        }
        elseif ( $itemView === 'line_with_intro' )
        {
            $itemView = 'line';
            $withIntro = 1;
        }

        if ( $viewType === 'grid' )
        {
            $cols = 2;
            if ( isset( $preparedBlock['parameters']['number_of_columns'] ) && $preparedBlock['parameters']['number_of_columns'] !== '' )
                $cols = (int)$preparedBlock['parameters']['number_of_columns'];
            if ( !in_array( $cols, array( 2, 3, 4, 6 ) ) )
                $cols = 2;
            $tpl->setVariable( 'item_view_type', $itemView );
            $tpl->setVariable( 'view_type_label', $viewLabel );
            $tpl->setVariable( 'with_intro', $withIntro );
            $tpl->setVariable( 'row_class', 'row' );
            return $tpl->fetch( 'design:explayouts/block/list/grid/' . $cols . '_columns.tpl' );
        }

        if ( $viewType === 'list' )
        {
            $tpl->setVariable( 'li_view', $itemView );
            $tpl->setVariable( 'li_view_label', $viewLabel );
            $tpl->setVariable( 'li_with_intro', $withIntro );
            $tpl->setVariable( 'li_paged', false );
            return $tpl->fetch( 'design:explayouts/block/list/list_items.tpl' );
        }

        return '';
    }
}
