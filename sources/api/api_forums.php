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
|   > $Date: 2005-10-10 14:08:54 +0100 (Mon, 10 Oct 2005) $
|   > $Revision: 23 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > API: Languages
|   > Module written by Matt Mecham
|   > Date started: Wednesday 30th November 2005 (11:40)
|
+--------------------------------------------------------------------------
*/

/**
* API: Форумов
*
* ПРИМЕР ИСПОЛЬЗОВАНИЯ
* <code>
* $api =  new api_forums();
* # Опционально - если $ipsclass не передается в объект, он будет инициализирован самостоятельно
* $api->ipsclass =& $this->ipsclass;
* $api->api_init();
* $jump_list = $api->return_forum_jump_option_list( array(1,3,5) );
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
* API: Форумов
*
* Класс предоставляет все методы API для получения списка форумов.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Matt Mecham
* @version		2.1
* @since		2.1.0
*/
class api_forums extends api_core
{
	/**
	* IPS Class Object
	*
	* @var object
	*/
	//var $ipsclass;
	
	
	/*-------------------------------------------------------------------------*/
	// Получение списка форумов в виде списка <option></option>
	/*-------------------------------------------------------------------------*/
	/**
	* Возвращает список всех форумов БЕЗ тега SELECT
	* Внимание: метод возвращает ВСЕ форумы без учета прав доступа.
	*
	* @param	array 	Массив ID выбранных форумов
	* @return   string	HTML <option> список форумов;
	*/
	function return_forum_jump_option_list( $selected=array() )
	{
		//-----------------------------------------
		// Get forums...
		//-----------------------------------------
	
		$this->ipsclass->forums->forums_init();
		
		$content = $this->ipsclass->forums->forums_forum_jump( 0, 0, 1 );
		
		//-----------------------------------------
		// Splice in selected IDs
		//-----------------------------------------
		
		if ( is_array( $selected ) and count( $selected ) )
		{
			foreach( $selected as $id )
			{
				$content = preg_replace( "#value=([\"'])($id)[\"']#si", "value=\\1\\2\\1 selected='selected'", $content );
			}
		}
		
		//-----------------------------------------
		// Return...
		//-----------------------------------------
		
		return $content;
	}
	
	
	
	
	
	
	
	
	
}



?>