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
|   > API: Settings
|   > Module written by Brandon Farber
|   > Date started: Tuesday June 6th 2006 (11:12)
|
+--------------------------------------------------------------------------
*/

/**
* API: Настроек форума
*
* ПРИМЕР ИСПОЛЬЗОВАНИЯ
* <code>
* $api =  new api_settings();
* # Опционально - если $ipsclass не передается в объект, он будет инициализирован самостоятельно
* $api->ipsclass =& $this->ipsclass;
* $api->api_init();
* $api->update_settings( $path_to_xml_file );
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
* API: Настроек форума
*
* Класс предоставляет все методы API для добавления новых настроек.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Brandon Farber
* @version		2.2
* @since		2.2.0
*/
class api_settings extends api_core
{
	/**
	* IPS Class Object
	*
	* @var object
	*/
	//var $ipsclass;
	
	var $xml;
	
	var $setting_groups			= array();
	var $setting_groups_by_key	= array();
	
	
	/*-------------------------------------------------------------------------*/
	// Добавление настроек IPB (существующие настройки обновляются)
	/*-------------------------------------------------------------------------*/
	/**
	* Добавляет или обносляет настройки IPB
	*
	* @param	string	Путь к xml файлу с настройками
	* @param	array	Массив значений по умолчанию настроек 
    *                   (ключ_настройки => значение_по_умолчанию)
	* @param	array	Массив текущих значений настроек 
    *                   (ключ_настройки => текущее_значение)
	* @return 	void;
	*/
	function update_settings( $xml_file_path, $defaults=array(), $vals=array() )
	{
		//-------------------------------
		// Check?
		//-------------------------------
		
		if ( !$xml_file_path )
		{
			$this->api_error[] = "input_missing_fields";
			return;
		}
		
		require_once( KERNEL_PATH.'class_xml.php' );

		$this->xml = new class_xml();
        $xml->lite_parser = 1;
		
		//-------------------------------
		// Get file contents
		//-------------------------------	
		
		if( !file_exists( $xml_file_path ) )
		{
			$this->api_error[] = "invalid_file";
			return;
		}
		
		$settings_content = implode( "", file($xml_file_path) );
		
		if( !$settings_content )
		{
			$this->api_error[] = "invalid_file";
			return;
		}			
		
		//-------------------------------
		// Unpack the datafile (SETTINGS)
		//-------------------------------

		$this->xml->xml_parse_document( $settings_content );		
		
		//-------------------------------
		// Get current settings.
		//-------------------------------

		$cur_settings = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => 'conf_id, conf_key',
									  					'from'   => 'conf_settings',
									 					'order'  => 'conf_id' ) );

		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$cur_settings[ $r['conf_key'] ] = $r['conf_id'];
		}
		
		//-----------------------------------------
		// Get current titles
		//-----------------------------------------

		$this->setting_get_groups();		
				
		//-----------------------------------------
		// pArse
		//-----------------------------------------

		$fields = array( 'conf_title'   , 'conf_description', 'conf_group'    , 'conf_type'    , 'conf_key'        , 'conf_default',
						 'conf_extra'   , 'conf_evalphp'    , 'conf_protected', 'conf_position', 'conf_start_group', 'conf_end_group',
						 'conf_help_key', 'conf_add_cache'  , 'conf_title_keyword' );

		$setting_fields = array( 'conf_title_keyword', 'conf_title_title', 'conf_title_desc', 'conf_title_noshow', 'conf_title_module' );

		//-----------------------------------------
		// Fix up...
		//-----------------------------------------

		if ( ! is_array( $this->xml->xml_array['settingexport']['settinggroup']['setting'][0]  ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $this->xml->xml_array['settingexport']['settinggroup']['setting'];

			unset($this->xml->xml_array['settingexport']['settinggroup']['setting']);

			$this->xml->xml_array['settingexport']['settinggroup']['setting'][0] = $tmp;
		}

		//-----------------------------------------
		// Loop through and sort out settings...
		//-----------------------------------------

		foreach( $this->xml->xml_array['settingexport']['settinggroup']['setting'] as $id => $entry )
		{
			$newrow = array();

			//-----------------------------------------
			// Is setting?
			//-----------------------------------------

			if ( ! $entry['conf_is_title']['VALUE'] )
			{
				foreach( $fields as $f )
				{
					$newrow[$f] = $entry[ $f ]['VALUE'];
				}

				$new_settings[] = $newrow;
			}

			//-----------------------------------------
			// Is title?
			//-----------------------------------------

			else
			{
				foreach( $setting_fields as $f )
				{
					$newrow[$f] = $entry[ $f ]['VALUE'];
				}

				$new_titles[] = $newrow;
			}
		}

		//-----------------------------------------
		// Sort out titles...
		//-----------------------------------------

		if ( is_array( $new_titles ) and count( $new_titles ) )
		{
			foreach( $new_titles as $idx => $data )
			{
				if ( $data['conf_title_title'] AND $data['conf_title_keyword'] )
				{
					//-----------------------------------------
					// Get ID based on key
					//-----------------------------------------

					$conf_id = $this->setting_groups_by_key[ $data['conf_title_keyword'] ]['conf_title_id'];

					$save = array( 'conf_title_title'   => $data['conf_title_title'],
								   'conf_title_desc'    => $data['conf_title_desc'],
								   'conf_title_keyword' => $data['conf_title_keyword'],
								   'conf_title_noshow'  => $data['conf_title_noshow'],
								   'conf_title_module'  => $data['conf_title_module']  );

					//-----------------------------------------
					// Not got a row, insert first!
					//-----------------------------------------

					if ( ! $conf_id )
					{
						$this->ipsclass->DB->do_insert( 'conf_settings_titles', $save );
						$conf_id = $this->ipsclass->DB->get_insert_id();

					}
					else
					{
						//-----------------------------------------
						// Update...
						//-----------------------------------------

						$this->ipsclass->DB->do_update( 'conf_settings_titles', $save, 'conf_title_id='.$conf_id );
					}

					//-----------------------------------------
					// Update settings cache
					//-----------------------------------------

					$save['conf_title_id']                                      = $conf_id;
					$this->setting_groups_by_key[ $save['conf_title_keyword'] ] = $save;
					$this->setting_groups[ $save['conf_title_id'] ]             = $save;

					//-----------------------------------------
					// Remove need update...
					//-----------------------------------------

					$need_update[] = $conf_id;
				}
			}
		}

		//-----------------------------------------
		// Sort out settings
		//-----------------------------------------

		if ( is_array( $new_settings ) and count( $new_settings ) )
		{
			foreach( $new_settings as $idx => $data )
			{
				if( is_array( $vals ) AND count( $vals ) )
				{
					foreach( $vals as $k => $v )
					{
						if( $data['conf_key'] == $k )
						{
							$data['conf_value'] = $v;
						}
					}
				}
				
				if ( is_array( $defaults ) AND count( $defaults ) )
				{
					foreach( $defaults as $k => $v )
					{
						if( $data['conf_key'] == $k )
						{
							$data['conf_default'] = $v;
						}
					}
				}				
				
				//-----------------------------------------
				// Make PHP slashes safe
				//-----------------------------------------

				$data['conf_evalphp'] = str_replace( '\\', '\\\\', $data['conf_evalphp'] );

				//-----------------------------------------
				// Now assign to the correct ID based on
				// our title keyword...
				//-----------------------------------------

				$data['conf_group'] = $this->setting_groups_by_key[ $data['conf_title_keyword'] ]['conf_title_id'];

				//-----------------------------------------
				// Remove from array
				//-----------------------------------------

				unset( $data['conf_title_keyword'] );

				if ( $cur_settings[ $data['conf_key'] ] )
				{
					//-----------------------------------------
					// Update
					//-----------------------------------------

					$this->ipsclass->DB->do_update( 'conf_settings', $data, 'conf_id='.$cur_settings[ $data['conf_key'] ] );
					$updated++;
				}
				else
				{
					//-----------------------------------------
					// INSERT
					//-----------------------------------------

					$this->ipsclass->DB->do_insert( 'conf_settings', $data );
					$inserted++;
				}
			}
		}

		//-----------------------------------------
		// Update group counts...
		//-----------------------------------------

		if ( count( $need_update ) )
		{
			foreach( $need_update as $id )
			{
				$conf = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'count(*) as count', 'from' => 'conf_settings', 'where' => 'conf_group='.$id ) );

				$count = intval($conf['count']);

				$this->ipsclass->DB->do_update( 'conf_settings_titles', array( 'conf_title_count' => $count ), 'conf_title_id='.$id );
			}
		}
		
		//----------------------------
		// Update the settings cache
		//----------------------------
		
		$this->ipsclass->cache['settings'] = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_add_cache=1' ) );
		$info = $this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row($info) )
		{
			$value = $r['conf_value'] != "" ?  $r['conf_value'] : $r['conf_default'];

			if ( $value == '{blank}' )
			{
				$value = '';
			}

			$this->ipsclass->cache['settings'][ $r['conf_key'] ] = $this->ipsclass->txt_safeslashes($value);
		}

		$this->ipsclass->update_cache( array( 'name' => 'settings', 'array' => 1, 'deletefirst' => 1 ) );		
	}
	
	
	/*-------------------------------------------------------------------------*/
	// _Internal
	/*-------------------------------------------------------------------------*/

	function setting_get_groups()
	{
		$this->setting_groups = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings_titles', 'order' => 'conf_title_title' ) );
		$this->ipsclass->DB->simple_exec();

		while( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->setting_groups[ $r['conf_title_id'] ]             = $r;
			$this->setting_groups_by_key[ $r['conf_title_keyword'] ] = $r;
		}
	}	
		
}

?>