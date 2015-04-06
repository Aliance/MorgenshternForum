<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   Invision Power Board INSTALLER
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Tuesday 27th of March 2007 07:00:16 AM
|   Release: 936d62a249c0dc8fd81438cdbc911b98
|   Licence Info: http://www.invisionboard.com/?license
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+--------------------------------------------------------------------------
|
|   > Script written by Matthew Mecham
|   > Date started: 19th March 2004
|   > Updated for 2.1: 23 June 2005
|
+--------------------------------------------------------------------------
 */

@set_time_limit( 0 );

//--------------------------------
// Setup
//--------------------------------

require( "./core/conf.php" );

//--------------------------------
// Define Constants
//--------------------------------

$INFO = array();

//--------------------------------
// Load our classes
//--------------------------------

require ROOT_PATH. "sources/ipsclass.php";
require ROOT_PATH. "conf_global.php";

require KERNEL_PATH		. 'class_converge.php';
require INS_ROOT_PATH   . 'core/template.php';
require KERNEL_PATH 	. 'class_xml.php';
require INS_ROOT_PATH   . 'core/class_tar.php';

# Initiate super-class
$ipsclass       = new ipsclass();
$ipsclass->vars = $INFO;

//--------------------------------
// Load the DB driver and such
//--------------------------------



$ipsclass->init_db_connection();
$ipsclass->DB->return_die = 1;
$ipsclass->DB->obj['use_error_log'] = 0;

//--------------------------------
//  Set up our vars
//--------------------------------

$ipsclass->parse_incoming();

$ipsclass->initiate_ipsclass();	

//--------------------------------
//  Set converge
//--------------------------------

$ipsclass->converge = new class_converge( $ipsclass->DB );

//--------------------------------
// Setup Main Installer Class
//--------------------------------

require( INS_ROOT_PATH . 'core/class_installer.php' );
require( INS_ROOT_PATH . 'custom/app.php' );

$install = new application_installer();
$install->ipsclass =& $ipsclass;
$install->read_config();

if ( !isset($ipsclass->vars['mysql_codepage']) AND $cp = $install->get_db_codepage() )
{
    $ipsclass->DB->obj['mysql_codepage'] = $cp;
}

$install->template = new install_template( $ipsclass );
$install->template->product_name         = $install->product_name;
$install->template->product_version      = $install->product_version;
$install->template->product_long_version = $install->product_long_version;

//--------------------------------
//  Saved Data
//--------------------------------
$install->saved_data = unserialize( stripslashes( urldecode( $ipsclass->input['saved_data'] ) ) );

// -------------------------------
// Run Install Step
// -------------------------------

$install->pre_process();
$install->process();
$install->post_process();

// -------------------------------
// Output
// -------------------------------
$install->template->saved_data = urlencode( serialize( $install->saved_data ) );
$install->template->output( $install->product_name, $install->product_version );

?>