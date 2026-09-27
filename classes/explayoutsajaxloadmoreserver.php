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

        self::renderAsPublicSite();

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

    /**
     * Render as the public site when called from the admin.
     *
     * The admin's layout preview renders its blocks in the public siteaccess
     * (explayouts_ui/preview.php). Where the admin has a host of its own
     * (edit.alpha) the Load more links it prints point at that host, so the
     * next page was rendered under the admin siteaccess, with the admin design,
     * and came back empty. The same switch as the preview's is made here, only
     * for an admin siteaccess (an admin design): the public siteaccesses call
     * this for their own pages and keep their own design.
     */
    protected static function renderAsPublicSite()
    {
        $ini = eZINI::instance();
        if ( strpos( (string)$ini->variable( 'DesignSettings', 'SiteDesign' ), 'admin' ) !== 0 )
            return;
        $siteAccess = $ini->variable( 'SiteSettings', 'DefaultAccess' );
        $access = $GLOBALS['eZCurrentAccess'];
        if ( !$siteAccess || ( isset( $access['name'] ) && $access['name'] === $siteAccess ) )
            return;
        $access['name'] = $siteAccess;
        if ( $access['type'] === eZSiteAccess::TYPE_URI )
            $access['uri_part'] = array( $siteAccess );
        eZSiteAccess::load( $access );
        $ini = eZINI::instance();
        $res = eZTemplateDesignResource::instance();
        $res->setDesignSetting( $ini->variable( 'DesignSettings', 'SiteDesign' ), 'site' );
        $res->setOverrideAccess( $siteAccess );
    }
}
