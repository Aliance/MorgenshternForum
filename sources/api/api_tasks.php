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
|   > $Date: 2006-05-25 10:15:22 -0400 (Thu, 25 May 2006) $
|   > $Revision: 278 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > API: Tasks
|   > Module written by Brandon Farber
|   > Date started: Oct 25th 2006 (15:16 EST)
|
+--------------------------------------------------------------------------
*/

/**
* API: Менеджера задач
*
* ПРИМЕР ИСПОЛЬЗОВАНИЯ
* <code>
* $api =  new api_tasks();
* # Опционально - если $ipsclass не передается в объект, он будет инициализирован самостоятельно
* $api->ipsclass =& $this->ipsclass;
* $api->api_init();
* $api->add_task( $path_to_xml_file );
* </code>
*
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
* API: Менеджера задач
*
* Класс предоставляет все методы API для добавления заданий в планировщик форума.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Brandon Farber
* @version		2.2
* @since		2.2.0
*/
class api_tasks extends api_core
{
	/**
	* Объект супер-класса IPS 
	*
	* @var object
	*/
	//var $ipsclass;
	
	var $xml;
	var $error		= '';
	
	var $updated	= 0;
	var $inserted	= 0;
	
	
	/*-------------------------------------------------------------------------*/
	// Добавление задач/Создание нового списка задач
	/*-------------------------------------------------------------------------*/
	/**
	* Добавляет задачу в планировщик IPB, или создает новый список задач из XML
	*
	* @param	string	Путь к xml-файлу со списком задач 
	* @return 	void;
	*/
	function add_task( $xml_file_path='' )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$file     = $xml_file_path ? $xml_file_path : ROOT_PATH . 'resources/tasks.xml';
		$inserted = 0;
		$updated  = 0;
		$tasks    = array();
		
		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( ! file_exists( $file ) )
		{
			$this->error = 'no_file';
			return;
		}	
		
		//-----------------------------------------
		// Get current task info
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select' => '*',
												 'from'   => 'task_manager' ) );
												
		$this->ipsclass->DB->exec_query();
		
		while( $row = $this->ipsclass->DB->fetch_row() )
		{
			$tasks[ $row['task_key'] ] = $row;
		}
		
		//-----------------------------------------
		// Get XML
		//-----------------------------------------
		
		require_once( KERNEL_PATH.'class_xml.php' );
		$xml = new class_xml();		
		$xml->lite_parser = 1;
        		
		//-----------------------------------------
		// Get XML file (TASKS)
		//-----------------------------------------
		
		$task_xml = implode( "", file($file) );
		
		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------
		
		$xml->xml_parse_document( $task_xml );
		
		if ( ! is_array( $xml->xml_array['export']['group']['row'][0] ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------
			
			$tmp = $xml->xml_array['export']['group']['row'];
			
			unset($xml->xml_array['export']['group']['row']);
			
			$xml->xml_array['export']['group']['row'][0] = $tmp;
		}		
		
		//-----------------------------------------
		// TASKS
		//-----------------------------------------
		
		foreach( $xml->xml_array['export']['group']['row'] as $id => $entry )
		{
			$newrow = array();
			
			$_key = $entry['task_key']['VALUE'];
			
			foreach( $entry as $f => $data )
			{
				if ( $f == 'VALUE' or $f == 'task_id' )
				{
					continue;
				}
				
				if ( $f == 'task_cronkey' )
				{
					$entry[ $f ]['VALUE'] = $tasks[ $_key ]['task_cronkey'] ? $tasks[ $_key ]['task_cronkey'] : md5( uniqid( microtime() ) );
				}
				
				if ( $f == 'task_next_run' )
				{
					$entry[ $f ]['VALUE'] = $tasks[ $_key ]['task_next_run'] ? $tasks[ $_key ]['task_next_run'] : time();
				}
				
				$newrow[$f] = $entry[ $f ]['VALUE'];
			}
			
			if ( $tasks[ $_key ]['task_key'] )
			{
				$this->updated++;
				$this->ipsclass->DB->do_update( 'task_manager', $newrow, "task_key='" . $tasks[ $_key ]['task_key'] . "'" );
			}
			else
			{
				$this->inserted++;
				$this->ipsclass->DB->do_insert( 'task_manager', $newrow );
			}
		}	
		
		return TRUE;
	}
}


?>