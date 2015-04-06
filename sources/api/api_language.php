<?php

/*
+--------------------------------------------------------------------------
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
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-11-28 09:33:12 -0600 (Tue, 28 Nov 2006) $
|   > $Revision: 742 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > API: Languages
|   > Module written by Matt Mecham
|   > Date started: Wednesday 19th July 2005 (14:33)
|
+--------------------------------------------------------------------------
*/

/**
* API: Языков
*
* ПРИМЕР ИСПОЛЬЗОВАНИЯ
* <code>
* $api =  new api_language();
* # Опционально - если $ipsclass не передается в объект, он будет инициализирован самостоятельно
* $api->ipsclass =& $this->ipsclass;
* $api->api_init();
* $api->lang_add_strings( array('lang_key' => "Language value" ), 'lang_subscriptions' );
* </code>
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author		Matt Mecham
* @copyright	Invision Power Services, Inc.
* @version		2.1
*/

if ( ! defined( 'IPS_API_PATH' ) )
{
	/**
	* Define classes path
	*/
	define( 'IPS_API_PATH', dirname(__FILE__) ? dirname(__FILE__) : '.' );
}

if ( ! class_exists( 'api_core' ) )
{
	require_once( IPS_API_PATH.'/api_core.php' );
}

/**
* API: Языков
*
* Класс предоставляет все методы API для добавления языковых конструкций.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Matt Mecham
* @version		2.1
* @since		2.1.0
*/
class api_language extends api_core
{
	/**
	* IPS Class Object
	*
	* @var object
	*/
	//var $ipsclass;
	
	
	/*-------------------------------------------------------------------------*/
	// Добавление языковых конструкций в IPB
	/*-------------------------------------------------------------------------*/
	/**
	* Добавляет языковые конструкции к системе языков IPB
	*
	* @param	array	Массив добавляемых конструкций (структура массива Ключ конструкции => значение)
	* @param	string	Языковой файл, куда добавляется конструкция, нап-р: lang_global
	* @param	string	К какому языковому набору (языку) добавить конструкцию ('all' во все)
	* @return void;
	*/
	function lang_add_strings( $to_add=array(), $add_lang_file='', $add_where='all')
	{
		//-------------------------------
		// Check?
		//-------------------------------
		
		if ( ! count( $to_add ) )
		{
			$this->api_error[] = "input_missing_fields";
			return;
		}
		
		if ( ! $add_lang_file )
		{
			$this->api_error[] = "input_missing_fields";
			return;
		}
		
		//-------------------------------
		// Trim off .php
		//-------------------------------
		
		$add_lang_file = str_replace( '.php', '', $add_lang_file );
		
		//-------------------------------
		// Get lang stuff from DB
		//-------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages' ) );
		$o = $this->ipsclass->DB->simple_exec();
		
		//-------------------------------
		// Go loopy
		//-------------------------------
		
		while( $row = $this->ipsclass->DB->fetch_row( $o ) )
		{
			$lang = array();
			
			if ( $add_where == $row['ldir'] OR $add_where == 'all' )
			{
				$lang_file = CACHE_PATH."cache/lang_cache/".$row['ldir']."/".$add_lang_file.'.php';
				
				if ( file_exists( $lang_file ) )
				{
					require ( $lang_file );
				}
				else
				{
					$this->errors[] = "file_not_found";
					return;
				}
				
				foreach( $to_add as $k => $v )
				{
					$lang[ $k ] = $v;
				}
				
				//-------------------------------
				// Write output
				//-------------------------------
				
				$start = "<?php\n\n".'$lang = array('."\n";
		
				foreach( $lang as $key => $text)
				{
					$text   = preg_replace("/\n{1,}$/", "", $text);
					$text 	= stripslashes($text);
					$text	= preg_replace( '/"/', '\\"', $text );
					$start .= "\n'".$key."'  => \"".$text."\",";
				}
				
				$start .= "\n\n);\n\n?".">";
				
				if ( $fh = @fopen( $lang_file, 'w') )
				{
					fwrite($fh, $start );
					fclose($fh);
				}
				else
				{
					$this->api_error[] = "file_not_writeable";
					continue;
				}
			}
		}
	}
	
	
	
	
}



?>