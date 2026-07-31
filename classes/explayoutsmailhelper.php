<?php

/**
 * eZ Publish 4 port of Netgen SiteBundle Helper/MailHelper.
 */
class expLayoutsMailHelper
{
    public static function sendMail( $receivers, $subject, $body, $sender = null )
    {
        $sender = self::createSender( $sender );

        $mail = new eZMail();
        $mail->setSender( $sender['email'], $sender['name'] );
        $mail->setSubject( $subject );
        $mail->setBody( $body );

        foreach ( self::createReceivers( $receivers ) as $receiver )
        {
            $mail->addReceiver( $receiver['email'], $receiver['name'] );
        }

        return eZMailTransport::send( $mail );
    }

    private static function createSender( $sender )
    {
        if ( is_array( $sender ) && count( $sender ) === 1 )
        {
            $keys = array_keys( $sender );
            $email = $keys[0];
            $name = $sender[$email];
            return array( 'email' => $email, 'name' => $name );
        }

        if ( is_string( $sender ) && $sender !== '' )
            return array( 'email' => $sender, 'name' => '' );

        $ini = eZINI::instance( 'site.ini' );
        $email = $ini->hasVariable( 'MailSettings', 'AdminEmail' ) ? $ini->variable( 'MailSettings', 'AdminEmail' ) : '';
        $name = $ini->hasVariable( 'MailSettings', 'AdminName' ) ? $ini->variable( 'MailSettings', 'AdminName' ) : '';

        return array( 'email' => $email, 'name' => $name );
    }

    private static function createReceivers( $receivers )
    {
        $list = array();

        if ( is_string( $receivers ) )
            $receivers = array( $receivers );

        foreach ( $receivers as $key => $value )
        {
            if ( is_string( $key ) && $key !== '' )
            {
                $list[] = array( 'email' => $key, 'name' => $value );
            }
            elseif ( is_string( $value ) && $value !== '' )
            {
                $list[] = array( 'email' => $value, 'name' => '' );
            }
        }

        return $list;
    }
}
