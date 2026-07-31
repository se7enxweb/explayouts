<?php
$http = eZHTTPTool::instance();
$module = $Params['Module'];

if ( !eZUser::currentUser()->hasAccessTo( 'explayouts', 'read' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$message = '';
$error = '';

if ( eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) && $http->hasPostVariable( 'DeleteLayout' ) )
{
    $deleteId = (int)$http->postVariable( 'DeleteLayoutID' );
    $layout = expLayoutsLayout::fetch( $deleteId );
    if ( $layout )
    {
        $zones = expLayoutsZone::fetchByLayout( $deleteId, $layout->attribute( 'status' ) );
        foreach ( $zones as $zone )
        {
            $blocks = expLayoutsBlock::fetchByZone( $zone->attribute( 'id' ), $layout->attribute( 'status' ) );
            foreach ( $blocks as $block )
            {
                foreach ( expLayoutsBlockParameter::fetchByBlock( $block->attribute( 'id' ) ) as $param )
                    $param->remove();
                $block->remove();
            }
            $zone->remove();
        }
        $layout->remove();
        $message = 'Layout deleted.';
    }
    else
    {
        $error = 'Layout not found.';
    }
}

if ( eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) && $http->hasPostVariable( 'CopyLayout' ) )
{
    $copyId = (int)$http->postVariable( 'CopyLayoutID' );
    $layout = expLayoutsLayout::fetch( $copyId );
    if ( $layout )
    {
        $newLayout = expLayoutsLayout::create( $layout->attribute( 'identifier' ) . '_copy' );
        $newLayout->setAttribute( 'name', $layout->attribute( 'name' ) . ' (Copy)' );
        $newLayout->setAttribute( 'layout_type', $layout->attribute( 'layout_type' ) );
        $newLayout->setAttribute( 'status', 1 );
        $newLayout->setAttribute( 'created', time() );
        $newLayout->setAttribute( 'modified', time() );
        $newLayout->store();

        $zoneMap = array();
        foreach ( expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), $layout->attribute( 'status' ) ) as $zone )
        {
            $newZone = expLayoutsZone::create( $newLayout->attribute( 'id' ), $zone->attribute( 'identifier' ), 1 );
            $newZone->setAttribute( 'position', $zone->attribute( 'position' ) );
            $newZone->setAttribute( 'linked_layout_id', $zone->attribute( 'linked_layout_id' ) );
            $newZone->store();
            $zoneMap[(int)$zone->attribute( 'id' )] = (int)$newZone->attribute( 'id' );
        }

        foreach ( expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), $layout->attribute( 'status' ) ) as $zone )
        {
            $zoneId = (int)$zone->attribute( 'id' );
            $newZoneId = $zoneMap[$zoneId];
            foreach ( expLayoutsBlock::fetchByZone( $zoneId, $layout->attribute( 'status' ) ) as $block )
            {
                $newBlock = expLayoutsBlock::create( $newZoneId, $newLayout->attribute( 'id' ), $block->attribute( 'definition_identifier' ), $block->attribute( 'name' ) );
                $newBlock->setAttribute( 'view_type', $block->attribute( 'view_type' ) );
                $newBlock->setAttribute( 'position', $block->attribute( 'position' ) );
                $newBlock->setAttribute( 'status', 1 );
                $newBlock->store();

                foreach ( expLayoutsBlockParameter::fetchByBlock( $block->attribute( 'id' ) ) as $param )
                {
                    expLayoutsBlockParameter::set( $newBlock->attribute( 'id' ), $param->attribute( 'name' ), $param->attribute( 'value' ) );
                }

                $collection = expLayoutsCollection::fetchByBlock( $block->attribute( 'id' ) );
                if ( $collection )
                {
                    $newCollection = expLayoutsCollection::create( $newBlock->attribute( 'id' ), $collection->attribute( 'collection_type' ) );
                    $newCollection->setAttribute( 'offset_value', $collection->attribute( 'offset_value' ) );
                    $newCollection->setAttribute( 'limit_value', $collection->attribute( 'limit_value' ) );
                    $newCollection->store();

                    foreach ( expLayoutsCollectionItem::fetchByCollection( $collection->attribute( 'id' ) ) as $item )
                    {
                        $newItem = expLayoutsCollectionItem::create( $newCollection->attribute( 'id' ), $item->attribute( 'value_id' ), $item->attribute( 'value_type' ), $item->attribute( 'item_type' ) );
                        $newItem->setAttribute( 'position', $item->attribute( 'position' ) );
                        $newItem->store();
                    }
                }
            }
        }

        $message = 'Layout copied.';
    }
    else
    {
        $error = 'Layout not found.';
    }
}

if ( eZUser::currentUser()->hasAccessTo( 'explayouts', 'read' ) && $http->hasPostVariable( 'ExportLayout' ) )
{
    $exportId = (int)$http->postVariable( 'ExportLayoutID' );
    $json = expLayoutsExporter::exportLayout( $exportId );
    if ( $json !== false )
    {
        $filename = 'layout_' . $exportId . '_' . time() . '.json';
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        echo $json;
        eZExecution::cleanExit();
    }
    else
    {
        $error = 'Layout not found for export.';
    }
}

if ( eZUser::currentUser()->hasAccessTo( 'explayouts', 'edit' ) && $http->hasPostVariable( 'ImportLayout' ) )
{
    $json = $http->hasPostVariable( 'ImportJson' ) ? trim( $http->postVariable( 'ImportJson' ) ) : '';
    if ( $json !== '' )
    {
        $result = expLayoutsImporter::importJson( $json );
        if ( isset( $result['error'] ) )
            $error = $result['error'];
        else
            return $module->redirectTo( 'explayouts/layout_edit/' . (int)$result['layout_id'] );
    }
    else
    {
        $error = 'No JSON provided.';
    }
}

$layouts = expLayoutsLayout::fetchList( false );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'layouts', $layouts );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:explayouts/layout_list.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'explayouts/layout', 'Layouts' ) ) );
return $Result;
