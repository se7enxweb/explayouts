<?php


class expLayoutsExponential
{
    public static function getContentProvider()
    {
        return new expLayoutsExpContentProvider();
    }

    public static function getCollectionQueryTypeHandler()
    {
        return new expLayoutsExpCollection();
    }

    public static function getBlockDefinition()
    {
        return new expLayoutsExpBlock();
    }

    public static function getItemValueConverter()
    {
        return new expLayoutsExpItem();
    }

    public static function getParameters()
    {
        return new expLayoutsExpParameters();
    }

    public static function getForm()
    {
        return new expLayoutsExpForm();
    }

    public static function getLayout()
    {
        return new expLayoutsExpLayout();
    }

    public static function getLocale()
    {
        return new expLayoutsExpLocale();
    }

    public static function getSearch()
    {
        return new expLayoutsExpSearch();
    }

    public static function getSecurity()
    {
        return new expLayoutsExpSecurity();
    }

    public static function getUtils()
    {
        return new expLayoutsExpUtils();
    }

    public static function getValidator()
    {
        return new expLayoutsExpValidator();
    }

    public static function getAdminUI()
    {
        return new expLayoutsExpAdminUI();
    }

    public static function getHttpCache()
    {
        return new expLayoutsExpHttpCache();
    }

    public static function getContext()
    {
        return new expLayoutsExpContext();
    }
}
