<?php
/**
 * The code of extension/explayouts/modules/explayouts/preview.php, moved into a class (#207 stage 1). The file extension/explayouts/modules/explayouts/preview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Explayouts\Explayouts
{

class Preview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        if ( !\eZUser::currentUser()->hasAccessTo( 'explayouts', 'read' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        $layoutId = isset( $Params['LayoutID'] ) ? (int)$Params['LayoutID'] : 0;
        $status = isset( $Params['Status'] ) ? (int)$Params['Status'] : 2;
        $layout = \expLayoutsLayout::fetch( $layoutId, $status );

        if ( !$layout )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );

        $prepared = \expLayoutsRenderer::prepareLayout( $layout );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'layout', $prepared );

        $Result = array();
        $Result['pagelayout'] = false;
        $Result['content'] = $tpl->fetch( 'design:explayouts/layout.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'explayouts/preview', 'Preview layout' ) ) );
        return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
