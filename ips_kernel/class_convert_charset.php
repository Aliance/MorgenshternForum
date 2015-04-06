<?php

/*
+---------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2006 Invision Power Services, Inc.
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
|   INVISION POWER BOARD ÍÅ ßÂËßÅÒÑß ÁÅÑÏËÀÒÍÛÌ ÏÐÎÃÐÀÌÌÍÛÌ ÎÁÅÑÏÅ×ÅÍÈÅÌ!
|   Ïðàâà íà ÏÎ ïðèíàäëåæàò Invision Power Services
|   Ïðàâà íà ïåðåâîä IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|
|   > CONVERT CHAR SET FUNCTIONS (Simple, light parser)
|   > Script written by Matt Mecham
|   > Date started: Friday 2nd December 2005 (10:18)
|
+---------------------------------------------------------------------------
*/

/**
* Wrapper class for converting character sets. This is to keep a consistent
* interface even if we decide to change the actual engine.
*
* EXAMPLE: Converting text from one char set to another
* <code>
* $convert = new class_convert_charset();
* $converted_text = $convert->convert_charset( $string, $string_char_set, $destination_char_set );
* print $converted_text;
* </code>
*
* @package		IPS_KERNEL
* @author		Matt Mecham
* @copyright	Invision Power Services, Inc.
* @version		3.0
*/

if ( ! defined( 'IPS_CLASSES_PATH' ) )
{
	/**
	* Define classes path
	*/
	define( 'IPS_CLASSES_PATH', dirname(__FILE__) );
}

/**
* RSS class for creation and extraction of RSS feeds.
*
* Does what it says on the tin.
*
* @package	IPS_KERNEL
* @author   Matt Mecham
* @version	3.0
*/
class class_convert_charset
{
	/**
	* IPSCLASS object
	*
	* @var object
	*/
	var $ipsclass;
	

	/*-------------------------------------------------------------------------*/
	// Convert charset
	/*-------------------------------------------------------------------------*/
	/**
	* Converts a text string from its current charset to a destination charset
	* As above
	*
	* @param	string	Text string
	* @param	string	Text string char set (original)
	* @param	string	Desired character set (destination)
	* @return	string
	*/
	function convert_charset( $string, $string_char_set, $destination_char_set='UTF-8' )
	{
		$string_char_set = strtolower($string_char_set);
		$t               = $string;
		
		//-----------------------------------------
		// Did we pass a destination?
		//-----------------------------------------
		
		$destination_char_set = strtolower($destination_char_set);
		
		//-----------------------------------------
		// Not the same?
		//-----------------------------------------
		
		if ( $destination_char_set == $string_char_set )
		{
			return;
		}
		
		if( !$string_char_set )
		{
			return;
		}
		
		if( !$t OR $t == '' )
		{
			return $string;
		}		
		
		//-----------------------------------------
		// Do the convert - internally..
		//-----------------------------------------
		
		/*if ( function_exists( 'mb_convert_encoding' ) )
		{
			$text = mb_convert_encoding( $string, $destination_char_set, $string_char_set );
		}
		else if ( function_exists( 'recode_string' ) )
		{
			$text = recode_string( $string_char_set.'..'.$destination_char_set, $string );
		}
		else if ( function_exists( 'iconv' ) )
		{
			$text = iconv( $string_char_set, $destination_char_set.'//TRANSLIT', $string);
		}
		else
		{*/
			require_once( IPS_CLASSES_PATH . '/i18n/convertcharset/ConvertCharset.class.php' );
			$convert = new ConvertCharset();
			$text    = $convert->Convert($string, $string_char_set, $destination_char_set, false );
		//}
		
		return $text ? $text : $t;
	}
	
}

?>