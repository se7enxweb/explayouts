<?php
$module = $Params['Module'];

if ( !eZUser::currentUser()->hasAccessTo( 'explayouts', 'read' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$layoutId = isset( $Params['LayoutID'] ) ? (int)$Params['LayoutID'] : 0;
$status = isset( $Params['Status'] ) ? (int)$Params['Status'] : 2;
$layout = expLayoutsLayout::fetch( $layoutId, $status );

if ( !$layout )
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

$prepared = expLayoutsRenderer::prepareLayout( $layout );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'layout', $prepared );

$Result = array();
$Result['pagelayout'] = false;
$Result['content'] = $tpl->fetch( 'design:explayouts/layout.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'explayouts/preview', 'Preview layout' ) ) );
return $Result;
