<?php


class expLayoutsExpParameters
{
    public function getParameter( $name, $default = null )
    {
        return expLayoutsSiteBundleDependencyInjection::getParameter( $name, $default );
    }


    public function hasParameter( $name )
    {
        return expLayoutsSiteBundleDependencyInjection::getParameter( $name ) !== null;
    }

    public function setParameter( $name, $value )
    {
        expLayoutsSiteBundleDependencyInjection::setParameter( $name, $value );
    }
}
