<?php
// @description Report whether the explayouts install and layout classes and the database class are loaded
echo 'Class expLayoutsInstall: ' . ( class_exists( 'expLayoutsInstall' ) ? 'yes' : 'no' ) . "\n";
echo 'Class expLayoutsLayout: ' . ( class_exists( 'expLayoutsLayout' ) ? 'yes' : 'no' ) . "\n";
echo 'Class eZDB: ' . ( class_exists( 'eZDB' ) ? 'yes' : 'no' ) . "\n";
