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
|		          http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-10-06 04:09:22 -0500 (Fri, 06 Oct 2006) $
|   > $Revision: 611 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Components Functions
|   > Module written by Matt Mecham
|   > Date started: 12th April 2005 (13:09)
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class ad_components
{
	# Globals
	var $ipsclass;
	var $html;
	
	var $perm_main  = 'admin';
	var $perm_child = 'components';
	
	/*-------------------------------------------------------------------------*/
	// Main handler
	/*-------------------------------------------------------------------------*/
	
	function auto_run() 
	{
		$this->html = $this->ipsclass->acp_load_template('cp_skin_tools');
		
		switch($this->ipsclass->input['code'])
		{
			case 'master_xml_export':
				$this->master_xml_export();
				break;
				
			case 'manage':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->components_list();
				break;
			
			case 'component_add':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->components_form('add');
				break;
			case 'component_add_do':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->components_save('add');
				break;
			
			case 'component_edit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->components_form('edit');
				break;
			case 'component_edit_do':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->components_save('edit');
				break;
				
			case 'component_delete':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->components_delete();
				break;				
			
			case 'component_export':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':export' );
				$this->components_export('single');
				break;
			case 'component_import':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':import' );
				$this->components_import();
				break;
				
			case 'component_move':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->components_move();
				break;
			case 'component_toggle_enabled':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->components_toggle_enabled();
				break;
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->components_list();
				break;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Export Master XML
	/*-------------------------------------------------------------------------*/
	
	function master_xml_export()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$entry = array();
		
		//-----------------------------------------
		// Get XML class
		//-----------------------------------------
		
		require_once( KERNEL_PATH.'class_xml.php' );
		
		$xml = new class_xml();
		
		$xml->doc_type = $this->ipsclass->vars['gb_char_set'];

		$xml->xml_set_root( 'export', array( 'exported' => time() ) );
		
		//-----------------------------------------
		// Set group
		//-----------------------------------------
		
		$xml->xml_add_group( 'group' );
		
		//-----------------------------------------
		// Get templates...
		//-----------------------------------------
	
		$this->ipsclass->DB->simple_construct( array( 'select' => '*',
													  'from'   => 'components',
													  'where'  => "com_section != 'bugtracker'" ) );
		
		$this->ipsclass->DB->simple_exec();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$content = array();
			
			//-----------------------------------------
			// Sort the fields...
			//-----------------------------------------
			
			foreach( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}
			
			$entry[] = $xml->xml_build_entry( 'row', $content );
		}
		
		$xml->xml_add_entry_to_group( 'group', $entry );
		
		$xml->xml_format_document();
		
		$doc = $xml->xml_document;
		
		//-----------------------------------------
		// Print to browser
		//-----------------------------------------
		
		$this->ipsclass->admin->show_download( $doc, 'components.xml', '', 0 );
	}
	
	/*-------------------------------------------------------------------------*/
	// Components: Toggle enabled
	/*-------------------------------------------------------------------------*/
	
	function components_toggle_enabled()
	{
		//--------------------------------------------
		// INIT
		//--------------------------------------------
		
		$com_id = intval($this->ipsclass->input['com_id']);
		
		//--------------------------------------------
		// Checks...
		//--------------------------------------------
		
		if ( ! $com_id )
		{
			$this->ipsclass->main_msg = "Вы не указали ID компонента.";
			$this->components_list();
			return;
		}
		
		//--------------------------------------------
		// Get from database
		//--------------------------------------------
		
		$component = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'components', 'where' => 'com_id='.$com_id ) );
		
		$com_enabled = $component['com_enabled'] ? 0 : 1;
		
		$this->ipsclass->DB->do_update( 'components', array( 'com_enabled' => $com_enabled ), 'com_id='.$com_id );
		
		$this->components_rebuildcache();
		
		$this->ipsclass->main_msg = "Переключатель компонентов";
		$this->components_list();
	}
	
	/*-------------------------------------------------------------------------*/
	// Components: Position
	/*-------------------------------------------------------------------------*/
	
	function components_move()
	{
		//--------------------------------------------
		// INIT
		//--------------------------------------------
		
		$com_id 	= intval($this->ipsclass->input['com_id']);
		$move   	= trim($this->ipsclass->input['move']);
		$components = array();
		$final_coms = array();
		$used_pos   = array();
		$max_pos    = 0;
		
		//--------------------------------------------
		// Checks...
		//--------------------------------------------
		
		if ( ! $com_id OR ! $move )
		{
			$this->ipsclass->main_msg = "Вы не указали ID...";
			$this->components_list();
			return;
		}
		
		//--------------------------------------------
		// Get components from database
		//--------------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select' => '*',
												 'from'   => 'components',
												 'order'  => 'com_position ASC' ) );
		$this->ipsclass->DB->exec_query();
		
		while( $c = $this->ipsclass->DB->fetch_row() )
		{
			$max_pos += 1;
			
			if ( in_array( $c['com_position'], $used_pos ) )
			{
				
				$c['com_position'] = $max_pos;
			}
			else
			{
				$used_pos[] = $c['com_position'];
			}
			
			$components[ $c['com_id'] ] = $c['com_position'];
		}
		
		asort($components);
		
		$i 		  = 0;
		$did_move = 0;
		
		foreach( $components as $k => $v )
		{
			$i++;
			
			if( $k == $com_id )
			{
				if( $move == 'up' )
				{
					// Move up (lower #)
					$last = array_pop( $final_coms );
					$final_coms[ $k ] = $i - 1;
					
					foreach( $components as $k2 => $v2 )
					{
						if ( $v2 == $last )
						{
							$final_coms[ $k2 ] = $i;
							break;
						}
					}
				}
				else
				{
					// Move down (higher #)
					
					$final_coms[ $k ] = $i + 1;
					$did_move = 1;
				}
			}
			else
			{
				if ( $did_move == 1 )
				{
					$final_coms[ $k ] = $i - 1;
					$did_move = 0;
				}
				else
				{
					$final_coms[ $k ] = $i;
				}
			}
		}
		
		/*echo "<pre>";
		print_r($final_coms);
		exit;*/
		
		foreach( $final_coms as $k => $v )
		{
			$this->ipsclass->DB->do_update( 'components', array( 'com_position' => $v ), 'com_id='.$k );
		}
		
		$this->components_rebuildcache();
		
		$this->ipsclass->main_msg = "Формирование пунктов компонента";
		$this->components_list();
	}
	
	/*-------------------------------------------------------------------------*/
	// Components Rebuild Cache
	/*-------------------------------------------------------------------------*/
	
	function components_rebuildcache()
	{
		$this->ipsclass->cache['components'] = array();
			
		$this->ipsclass->DB->simple_construct( array( 'select' => 'com_id,com_enabled,com_section,com_filename,com_url_uri,com_url_title,com_position',
													  'from'   => 'components',
													  'where'  => 'com_enabled=1',
													  'order'  => 'com_position ASC' ) );
		$this->ipsclass->DB->simple_exec();
	
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['components'][] = $r;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'components', 'array' => 1, 'deletefirst' => 1 ) );
	}
	
	/*-------------------------------------------------------------------------*/
	// Components Import
	/*-------------------------------------------------------------------------*/
	
	function components_import()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$updated        = 0;
		$inserted       = 0;
		$cur_components = array();
		
		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			//-----------------------------------------
			// check and load from server
			//-----------------------------------------
			
			if ( ! $this->ipsclass->input['file_location'] )
			{
				$this->ipsclass->main_msg = "Вы не указали файл для импортирования.";
				$this->components_list();
				return;
			}
			
			if ( ! file_exists( ROOT_PATH . $this->ipsclass->input['file_location'] ) )
			{
				$this->ipsclass->main_msg = "Невозможно открыть указанный файл: " . ROOT_PATH . $this->ipsclass->input['file_location'];
				$this->components_list();
				return;
			}
			
			if ( preg_match( "#\.gz$#", $this->ipsclass->input['file_location'] ) )
			{
				if ( $FH = @gzopen( ROOT_PATH.$this->ipsclass->input['file_location'], 'rb' ) )
				{
					while ( ! @gzeof( $FH ) )
					{
						$content .= @gzread( $FH, 1024 );
					}
					
					@gzclose( $FH );
				}
			}
			else
			{
				if ( $FH = @fopen( ROOT_PATH.$this->ipsclass->input['file_location'], 'rb' ) )
				{
					$content = @fread( $FH, filesize(ROOT_PATH.$this->ipsclass->input['lang_location']) );
					@fclose( $FH );
				}
			}
		}
		else
		{
			//-----------------------------------------
			// Get uploaded schtuff
			//-----------------------------------------
			
			$tmp_name = $_FILES['FILE_UPLOAD']['name'];
			$tmp_name = preg_replace( "#\.gz$#", "", $tmp_name );
			
			$content  = $this->ipsclass->admin->import_xml( $tmp_name );
			
			if ( ! $content )
			{
				$this->ipsclass->main_msg = "Невозможно прочитать загруженный файл.";
				$this->components_list();
				return;
			}			
		}
		
		//-----------------------------------------
		// Get current components.
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => 'com_id, com_section',
													  'from'   => 'components',
													  'order'  => 'com_id' ) );
		
		$this->ipsclass->DB->simple_exec();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$cur_components[ $r['com_section'] ] = $r['com_id'];
		}
		
		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------
		
		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();
		
		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------
		
		$xml->xml_parse_document( $content );
		
		//-----------------------------------------
		// pArse
		//-----------------------------------------
		
		$fields = array( 'com_title'   , 'com_description', 'com_author' , 'com_url', 'com_version', 'com_menu_data',
						 'com_enabled' , 'com_safemode'   , 'com_section', 'com_filename', 'com_url_title', 'com_url_uri' );
		
		if ( ! is_array( $xml->xml_array['componentexport']['componentgroup']['component'][0]  ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------
			
			$tmp = $xml->xml_array['componentexport']['componentgroup']['component'];
			
			unset($xml->xml_array['componentexport']['componentgroup']['component']);
			
			$xml->xml_array['componentexport']['componentgroup']['component'][0] = $tmp;
		}
		
		foreach( $xml->xml_array['componentexport']['componentgroup']['component'] as $entry )
		{
			$newrow = array();
				
			foreach( $fields as $f )
			{
				$newrow[$f] = $entry[ $f ]['VALUE'];
			}
			
			$this->ipsclass->DB->force_data_type = array( 'com_version' => 'string' );
			
			if ( $cur_components[ $entry['com_section']['VALUE'] ] )
			{
				//-----------------------------------------
				// Update
				//-----------------------------------------
				
				$this->ipsclass->DB->do_update( 'components', $newrow, 'com_id='.$cur_components[ $entry['com_section']['VALUE'] ] );
				$updated++;
			}
			else
			{
				//-----------------------------------------
				// INSERT
				//-----------------------------------------
				
				$newrow['com_date_added'] = time();
				
				$this->ipsclass->DB->do_insert( 'components', $newrow );
				$inserted++;
			}
		}
		
		//-----------------------------------------
		// Done...
		//-----------------------------------------
		
		$this->components_rebuildcache();
		
		$this->ipsclass->main_msg = "$updated компонентов обновлено, $inserted компонентов добавлено.";
		
		$this->components_list();
	}
	
	/*-------------------------------------------------------------------------*/
	// Components Export
	/*-------------------------------------------------------------------------*/
	
	function components_export($type='single')
	{
		//--------------------------------------------
		// INIT
		//--------------------------------------------
		
		$com_id = intval($this->ipsclass->input['com_id']);
		$rows   = array();
		
		//--------------------------------------------
		// Checks...
		//--------------------------------------------
		
		if ( $type == 'single' )
		{
			if ( ! $com_id )
			{
				$this->ipsclass->main_msg = "Вы не указали ID компонента.";
				$this->components_list();
				return;
			}
			
			//--------------------------------------------
			// Get DB row(s)
			//--------------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'components', 'where' => 'com_id='.$com_id ) );
			$this->ipsclass->DB->simple_exec();
			
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$rows[] = $r;
			}
		}
		else
		{
			//--------------------------------------------
			// Get DB row(s)
			//--------------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'components' ) );
			$this->ipsclass->DB->simple_exec();
			
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$rows[] = $r;
			}
		}
		
		//-------------------------------
		// Get XML class
		//-------------------------------
		
		require_once( KERNEL_PATH.'class_xml.php' );
		
		$xml = new class_xml();
		
		$xml->xml_set_root( 'componentexport', array( 'exported' => time() ) );
		
		//-------------------------------
		// Add component
		//-------------------------------
		
		$xml->xml_add_group( 'componentgroup' );
		
		$entry = array();
		
		foreach( $rows as $r )
		{
			$content = array();
			
			foreach( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}
			
			$entry[] = $xml->xml_build_entry( 'component', $content );
		}
		
		$xml->xml_add_entry_to_group( 'componentgroup', $entry );
		
		$xml->xml_format_document();
		
		$doc = $xml->xml_document;

		//-------------------------------
		// Print to browser
		//-------------------------------
		
		$this->ipsclass->admin->show_download( $doc, 'ipd_components.xml', '', 0 );
	}
	
	/*-------------------------------------------------------------------------*/
	// Components Save
	/*-------------------------------------------------------------------------*/
	
	function components_save($type='add')
	{
		//--------------------------------------------
		// INIT
		//--------------------------------------------
		
		$com_id          = intval($this->ipsclass->input['com_id']);
		$com_title       = trim( $this->ipsclass->txt_stripslashes($this->ipsclass->input['com_title']) );
		$com_version     = trim( $this->ipsclass->txt_stripslashes($this->ipsclass->input['com_version']) );
		$com_description = trim( $this->ipsclass->txt_stripslashes( $this->ipsclass->txt_htmlspecialchars($_POST['com_description'])) );
		$com_author      = trim( $this->ipsclass->txt_stripslashes($this->ipsclass->input['com_author']) );
		$com_url         = trim( $this->ipsclass->txt_stripslashes($this->ipsclass->input['com_url']) );
		//$com_menu_data   = trim( $this->ipsclass->txt_stripslashes( $this->ipsclass->txt_windowstounix($_POST['com_menu_data']) ) );
		$com_enabled     = intval($this->ipsclass->input['com_enabled']);
		$com_section     = trim( $this->ipsclass->txt_stripslashes($this->ipsclass->input['com_section']) );
		$com_filename    = trim( $this->ipsclass->txt_stripslashes($this->ipsclass->input['com_section']) );
		$com_safemode    = intval($this->ipsclass->input['com_safemode']);
		$com_url_title   = trim( $this->ipsclass->txt_stripslashes( $this->ipsclass->txt_windowstounix($_POST['com_url_title']) ) );
		$com_url_uri     = trim( $this->ipsclass->txt_stripslashes( $this->ipsclass->txt_windowstounix($_POST['com_url_uri']) ) );
		
		//--------------------------------------------
		// Checks...
		//--------------------------------------------
		
		if ( $type == 'edit' )
		{
			if ( ! $com_id )
			{
				$this->ipsclass->main_msg = "Вы не указали ID компонента.";
				$this->components_list();
				return;
			}
		}
		
		if ( ! $com_title OR ! $com_section OR ! $com_filename )
		{
			$this->ipsclass->main_msg = "Вы должны заполнить все поля.";
			$this->components_form( $type );
			return;
		}
		
		//--------------------------------------------
		// Build menu
		//--------------------------------------------
		
		$menu_data = array();
		
		if ( is_array( $_POST['menu_text'] ) and count( $_POST['menu_text'] ) )
		{
			foreach( $_POST['menu_text'] as $mainid => $text )
			{
				if ( ! $mainid OR ! $text OR ! $_POST['menu_url'][ $mainid ] )
				{
					continue;
				}
				
				$menu_data[ $mainid ]['menu_text']      = $this->ipsclass->parse_clean_value( $text );
				$menu_data[ $mainid ]['menu_url']       = $this->ipsclass->txt_stripslashes( $_POST['menu_url'][ $mainid ] );
				$menu_data[ $mainid ]['menu_redirect']  = intval( $_POST['menu_redirect'][ $mainid ] );
				$menu_data[ $mainid ]['menu_permbit']   = $this->ipsclass->txt_stripslashes( $_POST['menu_permbit'][ $mainid ] );
				$menu_data[ $mainid ]['menu_permlang']  = $this->ipsclass->parse_clean_value( $_POST['menu_permlang'][ $mainid ] );
			}
		}
	
		//--------------------------------------------
		// Save...
		//--------------------------------------------
		
		$this->ipsclass->DB->force_data_type = array( 'com_version' => 'string' );
		
		$array = array( 'com_title'       => $com_title,
						'com_author'      => $com_author,
						'com_version'     => $com_version,
						'com_url'         => $com_url,
						'com_menu_data'   => serialize( $menu_data ),
						'com_enabled'     => $com_enabled,
						'com_safemode'    => $com_safemode,
						'com_section'     => $com_section,
						'com_filename'    => $com_filename,
						'com_description' => $com_description,
						'com_url_uri'     => $com_url_uri,
						'com_url_title'   => $com_url_title,
					 );
					 
		if ( $type == 'add' )
		{
			//--------------------------------------------
			// Same filename or section?
			//--------------------------------------------
			
			$test = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'com_id', 'from' => 'components', 'where' => "com_section='{$com_section}' OR com_filename='{$com_filename}'" ) );
			
			if ( $test['com_id'] )
			{
				$this->ipsclass->main_msg = "Убедитесь в том, что в этом компоненте код и имя файла уникальны.";
				$this->components_form( $type );
				return;
			}
		
			$array['com_date_added'] = time();
			
			$this->ipsclass->DB->do_insert( 'components', $array );
			$this->ipsclass->main_msg = 'Новый компонент добавлен.';
		}
		else
		{
			
			$this->ipsclass->DB->do_update( 'components', $array, 'com_id='.$com_id );
			$this->ipsclass->main_msg = 'Компонент изменен.';
		}
		
		$this->components_rebuildcache();
		
		$this->components_list();
	}
	
	/*-------------------------------------------------------------------------*/
	// Components: Form
	/*-------------------------------------------------------------------------*/
	
	function components_form( $type='add' )
	{
		//-----------------------------------------
		// Init Vars
		//-----------------------------------------
		
		$com_id    = intval($this->ipsclass->input['com_id']);
		$menu_data = array();
		$menu_title    = '';
		$menu_url      = '';
		$menu_redirect = '';
		$menu_permbit  = '';
		$menu_permlang = '';
		
		//-----------------------------------------
		// Check (please?)
		//-----------------------------------------
		
		if ( $type == 'add' )
		{
			$formcode = 'component_add_do';
			$title    = "Добавление нового компонента";
			$button   = "Добавить";
		}
		else
		{
			$component = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'components', 'where' => 'com_id='.$com_id ) );
			
			if ( ! $component['com_id'] )
			{
				$this->ipsclass->main_msg = "Вы не указали ID компонента";
				$this->components_list();
				return;
			}
			
			$formcode = 'component_edit_do';
			$title    = "Редактирование компонента «{$component['com_title']}»";
			$button   = "Изменить";
		}
				
		//--------------------------------------------
		// Build menu: Did we just hit preview?
		//--------------------------------------------
		
		if ( is_array( $_POST['menu_text'] ) and count( $_POST['menu_text'] ) )
		{
			foreach( $_POST['menu_text'] as $mainid => $text )
			{
				$menu_text .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $text )."',\n";
			}
			
			foreach( $_POST['menu_url'] as $mainid => $text )
			{
				$menu_url .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $text )."',\n";
			}
			
			foreach( $_POST['menu_redirect'] as $mainid => $text )
			{
				$menu_redirect .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $text )."',\n";
			}
			
			foreach( $_POST['menu_permbit'] as $mainid => $text )
			{
				$menu_permbit .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $text )."',\n";
			}
			
			foreach( $_POST['menu_permlang'] as $mainid => $text )
			{
				$menu_permlang .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $text )."',\n";
			}
		}
		else
		{
			//--------------------------------------------
			// Build menu: Guess not...
			//--------------------------------------------
			
			if ( $component['com_menu_data'] )
			{
				$menu_data_raw = unserialize( $component['com_menu_data'] );
				
				if ( is_array( $menu_data_raw ) )
				{
					foreach( $menu_data_raw as $mainid => $data )
					{
						$menu_text     .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $data['menu_text'] )."',\n";
						$menu_url      .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $data['menu_url'] )."',\n";
						$menu_redirect .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $data['menu_redirect'] )."',\n";
						$menu_permbit  .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $data['menu_permbit'] )."',\n";
						$menu_permlang .=  "\t{$mainid} : '".str_replace( "'", '&#39;', $data['menu_permlang'] )."',\n";
					}
				}
			}
		}
		
		//-----------------------------------------
		// Trim off trailing commas (Safari hates it)
		//-----------------------------------------
		
		$menu_text     = preg_replace( "#,(\n)?$#", "\\1", $menu_text );
		$menu_url      = preg_replace( "#,(\n)?$#", "\\1", $menu_url );
		$menu_redirect = preg_replace( "#,(\n)?$#", "\\1", $menu_redirect );
		$menu_permbit  = preg_replace( "#,(\n)?$#", "\\1", $menu_permbit );
		$menu_permlang = preg_replace( "#,(\n)?$#", "\\1", $menu_permlang );
		
		//-------------------------------
		// Form elements
		//-------------------------------
		
		$form = array();
		
		$form['com_title']       = $this->ipsclass->adskin->form_input(        'com_title'      , $this->ipsclass->input['com_title']       ? stripslashes($this->ipsclass->input['com_title'])       : $component['com_title'] );
		$form['com_description'] = $this->ipsclass->adskin->form_input(        'com_description', $this->ipsclass->input['com_description'] ? stripslashes($this->ipsclass->input['com_description']) : $component['com_description'] );
		$form['com_author']      = $this->ipsclass->adskin->form_input(        'com_author'     , $this->ipsclass->input['com_author']      ? stripslashes($this->ipsclass->input['com_author'])      : $component['com_author'] );
		$form['com_url']         = $this->ipsclass->adskin->form_input(        'com_url'        , $this->ipsclass->input['com_url']         ? stripslashes($this->ipsclass->input['com_url'])         : $component['com_url'] );
		$form['com_version']     = $this->ipsclass->adskin->form_simple_input( 'com_version'    , $this->ipsclass->input['com_version']     ? stripslashes($this->ipsclass->input['com_version'])     : $component['com_version'], 10 );
		$form['com_enabled']     = $this->ipsclass->adskin->form_yes_no(       'com_enabled'    , $this->ipsclass->input['com_enabled']     ? stripslashes($this->ipsclass->input['com_enabled'])     : $component['com_enabled'] );
		$form['com_section']     = $this->ipsclass->adskin->form_simple_input( 'com_section'    , $this->ipsclass->input['com_section']     ? stripslashes($this->ipsclass->input['com_section'])     : $component['com_section'], 20 );
		$form['com_filename']    = $this->ipsclass->adskin->form_simple_input( 'com_filename'   , $this->ipsclass->input['com_filename']    ? stripslashes($this->ipsclass->input['com_filename'])    : $component['com_filename'], 20 );
		$form['com_url_title']   = $this->ipsclass->adskin->form_input(        'com_url_title'  , $this->ipsclass->input['com_url_title'] ? stripslashes($this->ipsclass->input['com_url_title']) : $component['com_url_title'] );
		$form['com_url_uri']     = $this->ipsclass->adskin->form_input(        'com_url_uri'    , $this->ipsclass->input['com_url_uri']   ? stripslashes($this->ipsclass->input['com_url_uri'])   : $component['com_url_uri']   );
		
		if ( IN_DEV )
		{
			$form['com_safemode'] = $this->ipsclass->adskin->form_yes_no( 'com_safemode', $this->ipsclass->input['com_safemode'] ? $this->ipsclass->input['com_safemode']: $component['com_safemode'] );
		}
		
		$this->ipsclass->html .= $this->html->components_form( $form, $title, $formcode, $button, $component, $menu_text, $menu_url, $menu_redirect, $menu_permbit, $menu_permlang );
		
		$this->ipsclass->html_help_title = "Управление компонентами";
		$this->ipsclass->html_help_msg   = "Эта секция позволит вам добавить новый или изменить уже существующий компонент.";
		
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Управление компонентами' );
		$this->ipsclass->admin->nav[] = array( '', 'Добавление/изменение компонента' );
		$this->ipsclass->admin->output();
	}
	
	/*-------------------------------------------------------------------------*/
	// List current filetypes
	/*-------------------------------------------------------------------------*/
	
	function components_list()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Управление компонентами' );
		$this->ipsclass->admin->page_title  = "Управление компонентами";
		$this->ipsclass->admin->page_detail = "Эта секция позволит вам управлять компонентами форума.";
		
		//-------------------------------
		// INIT
		//-------------------------------
		
		$content     = "";
		$seen_count  = 0;
		$total_items = 0;
		$rows        = array();
		
		//-------------------------------
		// Get components
		//-------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'components', 'order' => 'com_position ASC' ) );
		$this->ipsclass->DB->simple_exec();
		
		while( $r = $this->ipsclass->DB->fetch_row() )
		{
			$total_items++;
			$rows[] = $r;
		}
		
		foreach( $rows as $r )
		{
			//-------------------------------
			// Version...
			//-------------------------------
			
			$r['_fullname'] = $r['com_title'];
			
			if ( $r['com_version'] )
			{
				$r['_fullname'] .= ' v'.$r['com_version'];
			}
			
			//-------------------------------
			// Author...
			//-------------------------------
			
			$r['_fullauthor'] = $r['com_author'];
			
			if ( $r['com_url'] )
			{
				$r['_fullauthor'] = "<a href='{$r['com_url']}' title='{$r['com_url']}' target='_blank'>{$r['_fullauthor']}</a>";
			}
			
			//-------------------------------
			// (Alex) Cross
			//-------------------------------
			
			$r['_enabled_img'] = $r['com_enabled'] ? 'aff_tick.png' : 'aff_cross.png';
			
			//-------------------------------
			// Work out position images
			//-------------------------------
			
			$r['_pos_up']   = $this->html->components_position_blank($r['com_id']);
			$r['_pos_down'] = $this->html->components_position_blank($r['com_id']);
			
			//-------------------------------
			// Work out position images
			//-------------------------------
			
			if ( ($seen_count + 1) == $total_items )
			{
				# Show up only
				$r['_pos_up']   = $this->html->components_position_up($r['com_id']);
			}
			else if ( $seen_count > 0 AND $seen_count < $total_items )
			{
				# Show both...
				$r['_pos_up']   = $this->html->components_position_up($r['com_id']);
				$r['_pos_down'] = $this->html->components_position_down($r['com_id']);
			}
			else
			{
				# Show down only
				$r['_pos_down'] = $this->html->components_position_down($r['com_id']);
			}
			
			$seen_count++;
				
			$content .= $this->html->component_row($r);
		}
		
		$this->ipsclass->html .= $this->html->component_overview( $content );
		
		$this->ipsclass->admin->output();
	}
	
	
	function components_delete( )
	{
		//-----------------------------------------
		// Init Vars
		//-----------------------------------------
		
		$com_id    = intval($this->ipsclass->input['com_id']);
		
		if( !$com_id )
		{
			$this->ipsclass->main_msg = "Вы не указали ID компонента.";
			$this->components_list();
			return;
		}		
			
		$component = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'components', 'where' => 'com_id='.$com_id ) );
		
		if ( ! $component['com_id'] )
		{
			$this->ipsclass->main_msg = "Указанный компонент не найден в базе данных.";
			$this->components_list();
			return;
		}
		
		if( $component['com_safemode'] && !IN_DEV )
		{
			$this->ipsclass->main_msg = "Этот компонент нельзя изменить или удалить.";
			$this->components_list();
			return;
		}
		
		$this->ipsclass->DB->build_and_exec_query( array( 'delete' => 'components', 'where' => 'com_id='.$com_id ) );

		$this->components_rebuildcache();
		$this->ipsclass->main_msg = "Компонент был успешно удален.";
		$this->components_list();
	}		
	

}


?>