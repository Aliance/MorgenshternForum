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
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Skin Tools
|   > Module written by Matt Mecham
|   > Date started: 22nd January 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class ad_skintools {

	var $base_url;
	var $db_html_files = "";
	var $ff_html_files = "";
	var $skin_id       = "";
	var $ff_fixes      = array();
	var $log           = array();

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "lookandfeel";

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "skintools";


	function auto_run()
	{
		$this->ipsclass->admin->page_detail = "Пожалуйста, перед использованием инструментов внимательно ознакомьтесь с их описанием.<br />Эти инструменты рекомендуется использовать, например, после обновления форума с более ранних версий.";
		$this->ipsclass->admin->page_title  = "Инструменты стилей";
		$this->ipsclass->admin->nav[] 		= array( $this->ipsclass->form_code, 'Инструменты стилей' );

		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'rebuildcaches':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':recache' );
				$this->rebuildcaches();
				break;

			case 'rewritemastercache':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':recache' );
				$this->rewrite_master_cache();
				break;

			case 'rebuildmastermacros':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':recache' );
				$this->rewrite_master_macros();
				break;

			case 'rebuildmaster':
				$this->rebuildmaster();
				break;

			case 'rebuildmasterhtml':
				$this->rebuildmaster_html();
				break;

			case 'rebuildmastercomponents':
				$this->rebuildmaster_components();
				break;

			case 'changemember':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':changemember' );
				$this->change_member();
				break;

			case 'changeforum':
				$this->change_forum();
				break;

			//-----------------------------------------
			// Search stuff
			//-----------------------------------------

			case 'searchsplash':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':search' );
				$this->searchreplace_start();
				break;

			case 'simplesearch':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':search' );
				$this->simple_search();
				break;

			case 'searchandreplace':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':search' );
				$this->search_and_replace();
				break;

			//-----------------------------------------
			// Search stuff
			//-----------------------------------------

			case 'easylogo':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->easy_logo_start();
				break;
			case 'easylogo_complete':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->easy_logo_complete();
				break;
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->show_intro();
				break;
		}
	}

	//-----------------------------------------
	// REBUILD MASTER MACROS
	//-----------------------------------------

	function rewrite_master_macros()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$file     = ROOT_PATH . 'resources/macro.xml';
		$macros   = array();
		$updated  = 0;
		$inserted = 0;

		//-----------------------------------------
		// CHECK
		//-----------------------------------------

		if ( ! file_exists( $file ) )
		{
			$this->ipsclass->main_msg = "Файл «{$file}» не найден — запуск инструмента невозможен.";
			$this->show_intro();
		}

		//-----------------------------------------
		// Get current macros
		//-----------------------------------------

		$this->ipsclass->DB->build_query( array( 'select' => '*',
												 'from'   => 'skin_macro',
												 'where'  => 'macro_set=1' ) );

		$this->ipsclass->DB->exec_query();

		while( $row = $this->ipsclass->DB->fetch_row() )
		{
			$macros[ $row['macro_value'] ] = $row['macro_replace'];
		}

		//-----------------------------------------
		// Get XML
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Get XML file
		//-----------------------------------------

		$skin_content = implode( "", file($file) );

		//-----------------------------------------
		// Unpack the datafile (TEMPLATES)
		//-----------------------------------------

		$xml->lite_parser = 1;
		$xml->xml_parse_document( $skin_content );

		//-----------------------------------------
		// Check macros
		//-----------------------------------------

		if ( ! is_array( $xml->xml_array['macroexport']['macrogroup']['macro'] ) )
		{
			$this->ipsclass->main_msg = "Ошибка в файле «macros.xml»...";
			$this->show_intro();
		}

		foreach( $xml->xml_array['macroexport']['macrogroup']['macro'] as $entry )
		{
			$_key = $entry[ 'macro_value' ]['VALUE'];
			$_val = $entry[ 'macro_replace' ]['VALUE'];

			if ( $macros[ $_key ] )
			{
				$updated++;

				$this->ipsclass->DB->do_update( 'skin_macro', array( 'macro_value'   => $_key,
																	 'macro_replace' => $_val ), "macro_set=1 AND macro_value='".$this->ipsclass->DB->add_slashes( $_key )."'" );
			}
			else
			{
				$inserted++;

				$this->ipsclass->DB->do_insert( 'skin_macro', array( 'macro_set'     => 1,
																	 'macro_value'   => $_key,
																	 'macro_replace' => $_val  ) );
			}
		}

		$this->ipsclass->cache_func->_recache_macros( 1, -1 );

		$this->ipsclass->main_msg = "$updated макросов обновлено и $inserted добавлено.";
		$this->show_intro();
	}

	/*-------------------------------------------------------------------------*/
	// Rebuild Master System Skin Set
	/*-------------------------------------------------------------------------*/

	/**
	* Rebuild Master System Templates from cacheid_1 directory
	*
	* @return	void
	*/
	function rewrite_master_cache()
	{
		$this->ipsclass->cache_func->_recache_templates( 1, -1, 0, 1, 1 );

		$this->ipsclass->main_msg .= implode("<br />", $this->ipsclass->cache_func->messages);

		$this->show_intro();
	}

	//-----------------------------------------
	// EASY LOGO CHANGER (COMPLETE)
	//-----------------------------------------

	function easy_logo_complete()
	{
		//-----------------------------------------
		// Init
		//-----------------------------------------

		$master = array();

		//-----------------------------------------
		// Check id
		//-----------------------------------------

		if ( ! $this->ipsclass->input['set_skin_set_id'] )
		{
			$this->ipsclass->main_msg = "Не выбран ID шаблона, вернитесь назад и повторите попытку";
			$this->easy_logo_start();
		}

		//-----------------------------------------
		// Grab the default template bit
		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "group_name='skin_global' AND func_name='global_board_header'" ) );
		$this->ipsclass->DB->simple_exec();

		while( $r = $this->ipsclass->DB->fetch_row() )
		{
			$master[ $r['set_id'] ] = $r;
		}

		if ( !isset($master[ $this->ipsclass->input['set_skin_set_id'] ]) OR !is_array($master[ $this->ipsclass->input['set_skin_set_id'] ]) )
		{
			$final_html = $master[1]['section_content'];
		}
		else
		{
			$final_html = $master[ $this->ipsclass->input['set_skin_set_id'] ]['section_content'];
		}

		if ( ! strstr( $final_html, '<!--ipb.logo.end-->' ) )
		{
			$this->ipsclass->main_msg = "Невозможно найти логотип для этого шаблона, убедитесь в том, что ваш шаблон обновлен.";
			$this->easy_logo_start();
		}

		//-----------------------------------------
		// Upload or new logo?
		//-----------------------------------------

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			if ( ! $_POST['logo_url'] )
			{
				$this->ipsclass->main_msg = "Вы должны либо загрузить новый логотип, либо ввести ссылку на существующий";
				$this->easy_logo_start();
			}

			$newlogo = $_POST['logo_url'];
		}
		else
		{
			if ( ! is_writable( CACHE_PATH.'style_images' ) )
			{
				$this->ipsclass->main_msg = "Вы должны быть уверены, что у 'style_images' стоят правильные права (CHMOD) на запись в нее. Должны стоять 0777.";
				$this->easy_logo_start();
			}

			//-----------------------------------------
			// Upload
			//-----------------------------------------

			$FILE_NAME = $_FILES['FILE_UPLOAD']['name'];
			$FILE_SIZE = $_FILES['FILE_UPLOAD']['size'];
			$FILE_TYPE = $_FILES['FILE_UPLOAD']['type'];

			//-----------------------------------------
			// Silly spaces
			//-----------------------------------------

			$FILE_NAME = preg_replace( "/\s+/", "_", $FILE_NAME );

			//-----------------------------------------
			// Naughty Opera adds the filename on the end of the
			// mime type - we don't want this.
			//-----------------------------------------

			$FILE_TYPE = preg_replace( "/^(.+?);.*$/", "\\1", $FILE_TYPE );

			//-----------------------------------------
			// Correct file type?
			//-----------------------------------------

			if ( ! preg_match( "#\.(?:gif|jpg|jpeg|png)$#is", $FILE_NAME ) )
			{
				$this->ipsclass->main_msg = "Неверный формат файла. Файл должен иметь формат GIF, JPEG или PNG.";
				$this->easy_logo_start();
			}

			if ( move_uploaded_file( $_FILES[ 'FILE_UPLOAD' ]['tmp_name'], CACHE_PATH."style_images/{$this->ipsclass->input['set_skin_set_id']}_".$FILE_NAME) )
			{
				@chmod( CACHE_PATH."style_images/{$this->ipsclass->input['set_skin_set_id']}_".$FILE_NAME, 0777 );
			}
			else
			{
				$this->ipsclass->main_msg = "Неудачная загрузка. Проверьте права доступа (CHMOD) у директории 'style_images' и убедитесь, что загружаемый файл имеет размер меньше, чем 2 Mb.";
				$this->easy_logo_start();
			}

			$newlogo = "style_images/{$this->ipsclass->input['set_skin_set_id']}_".urlencode($FILE_NAME);
		}

		//-----------------------------------------
		// Convert back stuff
		//-----------------------------------------

		foreach( array( 'headerhtml', 'javascripthtml', 'leftlinkshtml', 'rightlinkshtml' ) as $mail )
		{
			//$_POST[ $mail ] = $this->ipsclass->admin->form_to_text( $_POST[ $mail ] );
			//$_POST[ $mail ] = str_replace( "\r\n", "\n", $_POST[ $mail ] );
		}

		//-----------------------------------------
		// Okay! Form the template
		//-----------------------------------------

		//$final_html = $_POST['headerhtml'];
		//$final_html = str_replace( "<{BOARD_LOGO}>", "<!--ipb.logo.start--><img src='$newlogo' alt='IPB' style='vertical-align:top' border='0' /><!--ipb.logo.end-->"      , $final_html );
		//$final_html = str_replace( "<{JAVASCRIPT}>", "<!--ipb.javascript.start-->\n{$_POST['javascripthtml']}\n<!--ipb.javascript.end-->"       , $final_html );
		//$final_html = str_replace( "<{LEFT_HAND_SIDE_LINKS}>", "<!--ipb.leftlinks.start-->{$_POST['leftlinkshtml']}<!--ipb.leftlinks.end-->"    , $final_html );
		//$final_html = str_replace( "<{RIGHT_HAND_SIDE_LINKS}>", "<!--ipb.rightlinks.start-->{$_POST['rightlinkshtml']}<!--ipb.rightlinks.end-->", $final_html );

		$final_html = preg_replace( "#<!--ipb.logo.start-->.+?<!--ipb.logo.end-->#si", "<!--ipb.logo.start--><img src='$newlogo' alt='IPB' style='vertical-align:top' border='0' /><!--ipb.logo.end-->"      , $final_html );

		//-----------------------------------------
		// Update the DeeBee
		//-----------------------------------------

		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => "set_id=".intval($this->ipsclass->input['set_skin_set_id'])." AND group_name='skin_global' AND func_name='global_board_header'" ) );

		$this->ipsclass->DB->do_insert( 'skin_templates', array( 'section_content' => $final_html,
																 'set_id'          => $this->ipsclass->input['set_skin_set_id'],
																 'group_name'      => 'skin_global',
																 'func_name'       => 'global_board_header',
																 'func_data'       => '$component_links=""'
									 )                         );

		$this->ipsclass->cache_func->_rebuild_all_caches(array($this->ipsclass->input['set_skin_set_id']));

		$this->ipsclass->main_msg = 'Логотип изменен. Кеш стиля обновлен (ID: '.$this->ipsclass->input['set_skin_set_id'].')';

		$this->ipsclass->main_msg .= "<br />".implode("<br />", $this->ipsclass->cache_func->messages);

		$this->easy_logo_start();
	}

	//-----------------------------------------
	// EASY LOGO CHANGER (START)
	//-----------------------------------------

	function easy_logo_start()
	{
		//-----------------------------------------
		// Init
		//-----------------------------------------

		$master    = array();
		$skin_list = "";
		$html      = array();

		//-----------------------------------------
		// Grab the default template bit
		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "group_name='skin_global' AND func_name='global_board_header'" ) );
		$this->ipsclass->DB->simple_exec();

		while( $r = $this->ipsclass->DB->fetch_row() )
		{
			$master[ $r['set_id'] ] = $r;
		}

		if ( ! $master[1]['section_content'] )
		{
			$this->ipsclass->main_msg = "Невозможно найти базовый шаблон 'global_board_header'";
			$this->show_intro();
		}

		if ( ! strstr( $master[1]['section_content'], '<!--ipb.logo.end-->' ) )
		{
			$this->ipsclass->main_msg = "Невозможно найти тег логотипа, убедитесь в том, что ваш шаблон обновлен.";
			$this->show_intro();
		}

		//-----------------------------------------
		// Get Skin Names
		//-----------------------------------------

		$skin_list = $this->_get_skinlist( 1 );

		//-----------------------------------------
		// get URL
		//-----------------------------------------

		preg_match( "#<!--ipb.logo.start--><img src=[\"'](.+?)[\"'].+?<!--ipb.logo.end-->#si", $master[1]['section_content'], $match );

		$current_img_url = $match[1];

		//-----------------------------------------
		// get current HTML
		//-----------------------------------------

		$current_html = $master[1]['section_content'];

		$current_html = preg_replace( "#<!--ipb.javascript.start-->.+?<!--ipb.javascript.end-->#is"               , "<{JAVASCRIPT}>"                   , $current_html );
		$current_html = preg_replace( "#<!--ipb.logo.start--><img src=[\"'](.+?)[\"'].+?<!--ipb.logo.end-->#si"   , "<{BOARD_LOGO}>"                   , $current_html );
		$current_html = preg_replace( "#<!--ipb.leftlinks.start-->.+?<!--ipb.leftlinks.end-->#si"                 , "<{LEFT_HAND_SIDE_LINKS}>"         , $current_html );
		$current_html = preg_replace( "#<!--ipb.rightlinks.start-->.+?<!--ipb.rightlinks.end-->#si"               , "<{RIGHT_HAND_SIDE_LINKS}>"        , $current_html );

		//-----------------------------------------
		// Regex out me bits
		//-----------------------------------------

		preg_match( "#<!--ipb.javascript.start-->(.+?)<!--ipb.javascript.end-->#si", $master[1]['section_content'], $match );
		$html['javascript'] = $this->ipsclass->admin->text_to_form($match[1]);

		preg_match( "#<!--ipb.leftlinks.start-->(.+?)<!--ipb.leftlinks.end-->#si"  , $master[1]['section_content'], $match );
		$html['leftlinks']  = $this->ipsclass->admin->text_to_form($match[1]);

		preg_match( "#<!--ipb.rightlinks.start-->(.+?)<!--ipb.rightlinks.end-->#si"  , $master[1]['section_content'], $match );
		$html['rightlinks']  = $this->ipsclass->admin->text_to_form($match[1]);

		$current_html        = $this->ipsclass->admin->text_to_form($current_html);

		//-----------------------------------------
		// Can we upload into style_images?
		//-----------------------------------------

		$warning = ! is_writable( CACHE_PATH.'style_images' ) ? "<div class='redbox' style='padding:4px'><strong><font color='red'>ВНИМАНИЕ</font>: неправильные CHMOD директории /style_images/. Если вы желаете загрузить новый логотип, пожалуйста, установите 0755 или 0777 CHMOD для этой директории!</strong></div>" : '';

		//-----------------------------------------
		// Start the form
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'          ),
															     2 => array( 'code' , 'easylogo_complete'  ),
															     3 => array( 'MAX_FILE_SIZE', '10000000000' ),
															     4 => array( 'section', $this->ipsclass->section_code ),
													 ) , "uploadform", " enctype='multipart/form-data'"     );


		$this->ipsclass->html .= "<div class='tableborder'>
							<div class='tableheaderalt'>Установка логотипа</div>
							<div class='tablepad' style='background-color:#EAEDF0'>
							$warning
							<fieldset class='tdfset'>
							 <legend><strong>Конфигурация</strong></legend>
							 <table width='100%' cellpadding='5' cellspacing='0' border='0'>
							 <tr>
							   <td width='40%' class='tablerow1'>К какому шаблону добавить логотип?<div class='graytext'>Если вы уже изменяли шапку стиля из 'Управления шаблонами', то это действие удалит все ваши изменения.</div></td>
							   <td width='60%' class='tablerow1'>$skin_list</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tablerow1'>Ссылка к логотипу<div class='graytext'>Вы можете использовать относительные ссылки или полный URL, начинающийся с <b>http://</div></td>
							   <td width='60%' class='tablerow1'>".$this->ipsclass->adskin->form_simple_input('logo_url', ( isset($_POST['logo_url']) AND $_POST['logo_url'] ) ? $_POST['logo_url'] : $current_img_url, '60' )."</td>
							 </tr>
							 <tr>
							   <td width='40%' class='tablerow1'><b><u>ИЛИ</u></b> загрузить новый логотип<div class='graytext'>Выберите файл для загрузки с вашего компьютера. Файл должен иметь разрешение <b>.gif</b>, <b>.jpg</b>, <b>.jpeg</b> или <b>.png</b></div></td>
							   <td width='60%' class='tablerow1'>".$this->ipsclass->adskin->form_upload()."</td>
							 </tr>
							</table>
							</fieldset>
							</div>
							</div>";

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form_standalone("Установить");

		//-----------------------------------------

		$this->ipsclass->admin->output();
	}

	//-----------------------------------------
	// REBUILD MASTER COMPONENTS
	//-----------------------------------------

	function rebuildmaster_components()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$file    = ROOT_PATH . 'resources/skinsets.xml';

		if ( ! file_exists( $file ) )
		{
			$this->ipsclass->main_msg = "$file не найден. Пожалуйста, вернитесь и проверьте еще все еще раз";
			$this->show_intro();
		}

		//-----------------------------------------
		// Get XML
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Get XML file (CSS/WRAPPERS)
		//-----------------------------------------

		$skin_content = implode( "", file($file) );

		//-----------------------------------------
		// Unpack the datafile (TEMPLATES)
		//-----------------------------------------

		$xml->lite_parser = 1;
		$xml->xml_parse_document( $skin_content );


		//-----------------------------------------
		// (TEMPLATES)
		//-----------------------------------------

		if ( ! $xml->xml_array['export']['group']['row'][0]['set_css']['VALUE'] OR ! $xml->xml_array['export']['group']['row'][0]['set_wrapper']['VALUE'] )
		{
			$this->ipsclass->main_msg = "Error with resources/ipb_templates.xml - could not process XML properly";
			$this->show_intro();
		}
		else
		{
			$this->ipsclass->DB->do_update( 'skin_sets', array( 'set_css'           => $xml->xml_array['export']['group']['row'][0]['set_css']['VALUE'],
																'set_cache_css'     => $xml->xml_array['export']['group']['row'][0]['set_css']['VALUE'],
																'set_wrapper'       => $xml->xml_array['export']['group']['row'][0]['set_wrapper']['VALUE'],
																'set_cache_wrapper' => $xml->xml_array['export']['group']['row'][0]['set_wrapper']['VALUE'],
															  ), 'set_skin_set_id=1' );
		}

		$this->ipsclass->main_msg = "Компоненты главного стиля успешно обновлены.";
		$this->show_intro();
	}

	//-----------------------------------------
	// REBUILD MASTER HTML
	//-----------------------------------------

	function rebuildmaster_html()
	{
		$master  = array();
		$inserts = 0;
		$updates = 0;

		//-----------------------------------------
		// Template here?
		//-----------------------------------------

		if ( ! file_exists( ROOT_PATH.'resources/ipb_templates.xml' ) )
		{
			$this->ipsclass->main_msg = "resources/ipb_templates.xml cannot be found in the forums root directory. Please check, upload or try again";
			$this->show_intro();
		}

		//-----------------------------------------
		// First, get all the default bits
		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => 'suid,group_name,func_name', 'from' => 'skin_templates', 'where' => 'set_id=1' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$master[ strtolower( $r['group_name'] ) ][ strtolower( $r['func_name'] ) ] = $r['suid'];
		}

		//-----------------------------------------
		// Get XML
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Get XML file (TEMPLATES)
		//-----------------------------------------

		$xmlfile = ROOT_PATH.'resources/ipb_templates.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-----------------------------------------
		// Unpack the datafile (TEMPLATES)
		//-----------------------------------------

		$xml->lite_parser = 1;
		$xml->xml_parse_document( $setting_content );

		//-----------------------------------------
		// (TEMPLATES)
		//-----------------------------------------

		if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			$this->ipsclass->main_msg = "Error with resources/ipb_templates.xml - could not process XML properly";
			$this->show_intro();
		}

		foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $entry )
		{
			$newrow = array();

			$newrow['group_name']      = $entry[ 'group_name' ]['VALUE'];
			$newrow['section_content'] = $entry[ 'section_content' ]['VALUE'];
			$newrow['func_name']       = $entry[ 'func_name' ]['VALUE'];
			$newrow['func_data']       = $entry[ 'func_data' ]['VALUE'];
			$newrow['set_id']          = 1;
			$newrow['updated']         = time();

			if ( $master[ strtolower( $newrow['group_name'] ) ][ strtolower( $newrow['func_name'] ) ] )
			{
				//-----------------------------------------
				// Update
				//-----------------------------------------

				$updates++;

				$this->ipsclass->DB->do_update( 'skin_templates', $newrow, 'suid='.$master[ strtolower( $newrow['group_name'] ) ][ strtolower( $newrow['func_name'] ) ] );
			}
			else
			{
				//-----------------------------------------
				// Insert
				//-----------------------------------------

				$inserts++;

				$this->ipsclass->DB->do_insert( 'skin_templates', $newrow );
			}
		}

		$this->ipsclass->main_msg = "Главный стиль обновлен!<br />Произведено $updates обновлений и $inserts добавлений.";

		$this->show_intro();
	}

	//-----------------------------------------
	// COMPLEX SEARCH
	//-----------------------------------------

	function search_and_replace()
	{
		//-----------------------------------------
		// Get $skin_names stuff
		//-----------------------------------------

		require_once( ROOT_PATH.'sources/lib/skin_info.php' );

		$SEARCH_set  = intval( $this->ipsclass->input['set_skin_set_id'] );
		$SEARCH_all  = intval( $this->ipsclass->input['searchall'] );

		//-----------------------------------------
		// Get set stuff
		//-----------------------------------------

		$this_set = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$SEARCH_set ) );

		//-----------------------------------------
		// Clean up before / after
		//-----------------------------------------

		$before = $this->ipsclass->txt_stripslashes($_POST['searchfor']);
		$after  = $this->ipsclass->txt_stripslashes($_POST['replacewith']);
		$before = str_replace( '"', '\"', $before );
		$after  = str_replace( '"', '\"', $after  );

		if ( ! $before )
		{
			$this->ipsclass->main_msg = "Вы не ввели значение для поиска.";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Clean up regex
		//-----------------------------------------

		if ( $this->ipsclass->input['regexmode'] )
		{
			$before = str_replace( '#', '\#', $before );

			//-----------------------------------------
			// Test to ensure they are legal
			// - catch warnings, etc
			//-----------------------------------------

			ob_start();
			eval( "preg_replace( \"#{$before}#i\", \"{$after}\", '' );");
			$return = ob_get_contents();
			ob_end_clean();

			if ( $return )
			{
				$this->ipsclass->main_msg = "Произошла ошибка во время замены - проверьте введенные значения для поиска и замены.";
				$this->searchreplace_start();
			}
		}

		//-----------------------------------------
		// we're here, so it's good
		//-----------------------------------------

		$templates = array();
		$the_templates = array();
		$matches   = 0;

		if ( $SEARCH_all )
		{
			$the_templates = $this->ipsclass->cache_func->_get_templates( $this_set['set_skin_set_id'], $this_set['set_skin_set_parent'], 'all' );
		}
		else
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'set_id='.$SEARCH_set ) );
			$this->ipsclass->DB->simple_exec();

			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$the_templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
			}
		}

		if( count($the_templates) && is_array($the_templates) )
		{
			foreach( $the_templates as $group_name => $group_data )
			{
				foreach( $group_data as $func_name => $template_data )
				{
					if ( $this->ipsclass->input['regexmode'] )
					{
						if ( preg_match( "#{$before}#i", $template_data['section_content'] ) )
						{
							$templates[ $group_name ][ $func_name ] = $template_data;
							$matches++;
						}
					}
					else if ( strstr( $template_data['section_content'], $before ) )
					{
						$templates[ $group_name ][ $func_name ] = $template_data;
						$matches++;
					}
				}
			}
		}


		/*$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'set_id='.$SEARCH_set ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( $this->ipsclass->input['regexmode'] )
			{
				if ( preg_match( "#{$before}#i", $r['section_content'] ) )
				{
					$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
					$matches++;
				}
			}
			else if ( strstr( $r['section_content'], $before ) )
			{
				$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
				$matches++;
			}
		}*/

		//-----------------------------------------
		// No matches...
		//-----------------------------------------

		if ( ! count($templates) )
		{
			$this->ipsclass->html .= "<div class='tableborder'>
								 <div class='tableheaderalt'>Результаты поиска и замены</div>
								 <div class='tablepad'>
								  <b>Вы искали: ".stripslashes(htmlspecialchars($before))."</b>
								  <br />
								  <br />
								  Не найдено ни одной записи, соответствующей искомой строке. Попробуйте еще раз и расширьте диапазон поиска.
								 </div>
								</div>";

			$this->ipsclass->admin->output();
		}

		//-----------------------------------------
		// Swapping or showing?
		//-----------------------------------------

		if ( $this->ipsclass->input['testonly'] )
		{
			$this->ipsclass->html .= "<div class='tableborder'>
								 <div class='tableheaderalt'>Результаты поиска и замены</div>
								 <div class='tablepad' style='padding:5px'><b style='font-size:12px'>{$matches} записей содержащих '".htmlentities($before)."' заменены на '".htmlentities($after)."'</b><br /><br />";

			//-----------------------------------------
			// Go fru dem all and print..
			//-----------------------------------------

			foreach( $templates as $group => $d )
			{
				foreach( $templates[ $group ] as $tmp_data )
				{
					if ( isset($skin_names[ $group ]) )
					{
						$group_name = $skin_names[ $group ][0];
					}
					else
					{
						$group_name = $group;
					}

					$html = $tmp_data['section_content'];

					//-----------------------------------------
					// Decode...
					//-----------------------------------------

					$hl    = $before;
					$after = str_replace( '\\\\', '\\\\\\', $after );

					if ( ! $after )
					{
						$hl   = preg_replace( "#\((.+?)\)#s", "(?:\\1)", $hl );
						$html = preg_replace( "#({$hl})#si" , '{#-^--opentag--^-#}'."\\1".'{#-^--closetag--^-#}', $html );
					}
					else
					{
						//-----------------------------------------
						// Wrap tags (so we don't use
						// < >, etc )
						//-----------------------------------------

						$html = preg_replace( "#{$hl}#si", '{#-^--opentag--^-#}'.$after.'{#-^--closetag--^-#}', $html );
					}

					//-----------------------------------------
					// Clean up..
					//-----------------------------------------

					$html = str_replace( "{#-^--opentag--^-#}\\", '{#-^--opentag--^-#}', $html );

					//-----------------------------------------
					// convert to printable html
					//-----------------------------------------

					$html = str_replace( "<" , "&lt;"  , $html);
					$html = str_replace( ">" , "&gt;"  , $html);
					$html = str_replace( "\"", "&quot;", $html);

					$html = preg_replace( "!&lt;\!--(.+?)(//)?--&gt;!s"              , "&#60;&#33;<span style='color:red'>--\\1--\\2</span>&#62;", $html );
					$html = preg_replace( "#&lt;([^&<>]+)&gt;#s"                     , "<span style='color:blue'>&lt;\\1&gt;</span>"             , $html );   //Matches <tag>
					$html = preg_replace( "#&lt;([^&<>]+)=#s"                        , "<span style='color:blue'>&lt;\\1</span>="                , $html );   //Matches <tag
					$html = preg_replace( "#&lt;/([^&]+)&gt;#s"                      , "<span style='color:blue'>&lt;/\\1&gt;</span>"            , $html );   //Matches </tag>
					$html = preg_replace( "!=(&quot;|')([^<>])(&quot;|')(\s|&gt;)!s" , "=\\1<span style='color:purple'>\\2</span>\\3\\4"         , $html );   //Matches ='this'

					//-----------------------------------------
					// convert back wrap tags
					//-----------------------------------------

					$html = str_replace( '{#-^--opentag--^-#}' , "<span style='color:red;font-weight:bold;background-color:yellow'>", $html );
					$html = str_replace( '{#-^--closetag--^-#}', "</span>", $html );

					$this->ipsclass->html .= "<div class='tableborder'>
										 <div class='tableheaderalt'>{$group_name} &middot; {$tmp_data['func_name']}</div>
										 <div class='tablerow2' style='height:100px;overflow:auto'><pre>{$html}</pre></div>
										</div>
										<br />";
				}
			}

			$this->ipsclass->html .= "</div></div>";

			$this->ipsclass->admin->nav[] = array( "", "Результаты поиска в шаблоне ".$this_set['set_name'] );

			$this->ipsclass->admin->output();
		}
		else
		{
			//-----------------------------------------
			// Jus' do iiit
			//-----------------------------------------

			$after  = str_replace( '\\\\', '\\\\\\', $after );
			$report = array();

			foreach( $templates as $group => $d )
			{
				foreach( $templates[ $group ] as $tmp_data )
				{
					if ( $this->ipsclass->input['regexmode'] )
					{
						$tmp_data['section_content'] = preg_replace( "#{$before}#si", $after, $tmp_data['section_content'] );

					}
					else
					{
						$tmp_data['section_content'] = str_replace( $before, $after, $tmp_data['section_content'] );
					}

					$do_insert = 0;
					$insert_array = array();

					// Protect master templates...
					if( $tmp_data['set_id'] == 1 )
					{
						$tmp_data['set_id'] = $SEARCH_set;

						$quick_check = $this->ipsclass->DB->simple_exec_query( array( 'select' => "COUNT(*) as thecnt", 'from' => 'skin_templates',
														'where' => "group_name='{$tmp_data['group_name']}' AND func_name='{$tmp_data['func_name']}' AND set_id='{$tmp_data['set_id']}'" ) );

						if( $quick_check['thecnt'] == 0 )
						{
							$do_insert = 1;
						}
					}

					if( !$do_insert )
					{
						//-----------------------------------------
						// Update DB
						//-----------------------------------------

						$this->ipsclass->DB->do_update( 'skin_templates', array( 'section_content' => $tmp_data['section_content'] ), 'suid='.$tmp_data['suid'] );
					}
					else
					{
						$insert_array = array( 'set_id' 			=> $tmp_data['set_id'],
												'group_name' 		=> $tmp_data['group_name'],
												'func_name' 		=> $tmp_data['func_name'],
												'section_content' 	=> $tmp_data['section_content'],
												'func_data' 		=> $tmp_data['func_data'],
												'updated' 			=> time(),
												'can_remove' 		=> 1
											 );

						$this->ipsclass->DB->do_insert( 'skin_templates', $insert_array );
					}

					$report[] = $tmp_data['func_name'].' updated...';
				}
			}

			//-----------------------------------------
			// Recache skin template..
			//-----------------------------------------

			$this->ipsclass->cache_func->_recache_templates( $SEARCH_set, $this_set['set_skin_set_parent'] );
			$report[] = "Templates recached for set {$this_set['set_name']}";

			$this->ipsclass->main_msg = implode( "<br />", $report );
			$this->searchreplace_start();
		}
	}

	//-----------------------------------------
	// SIMPLE SEARCH
	//-----------------------------------------

	function simple_search()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$rawword   = $_GET['searchkeywords'] ? urldecode( $_GET['searchkeywords'] ) : $_POST['searchkeywords'];
 		$templates = array();
		$final     = array();
		$matches   = array();

		//-----------------------------------------
		// CLEAN UP
		//-----------------------------------------

		$SEARCH_word = trim( $this->ipsclass->txt_safeslashes( $rawword ) );
		$SEARCH_safe = urlencode( $SEARCH_word );
		$SEARCH_all  = intval( $this->ipsclass->input['searchall'] );
		$SEARCH_set  = intval( $this->ipsclass->input['set_skin_set_id'] );

		//-----------------------------------------
		// check (please?)
		//-----------------------------------------

		if ( ! $SEARCH_word )
		{
			$this->ipsclass->main_msg = "Вы должны ввести искомое слово";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Get set stuff
		//-----------------------------------------

		$this_set = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$SEARCH_set ) );

		if ( ! $this_set['set_skin_set_id'] )
		{
			$this->ipsclass->main_msg = "В базе данных ничего не было найдено";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Get templates from DB
		//-----------------------------------------

		if ( $SEARCH_all )
		{
			$templates = $this->ipsclass->cache_func->_get_templates( $this_set['set_skin_set_id'], $this_set['set_skin_set_parent'], 'all' );
		}
		else
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => 'set_id='.$SEARCH_set ) );
			$this->ipsclass->DB->simple_exec();

			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$templates[ $r['group_name'] ][ strtolower($r['func_name']) ] = $r;
			}
		}

		if ( ! count( $templates ) )
		{
			$this->ipsclass->main_msg = "Шаблоны не найдены!";
			$this->searchreplace_start();
		}

		//-----------------------------------------
		// Go fru dem all and search
		//-----------------------------------------

		foreach( $templates as $group => $d )
		{
			foreach( $templates[ $group ] as $tmp_data )
			{
				if ( strstr( strtolower( $tmp_data['section_content'] ), strtolower( $SEARCH_word ) ) )
				{
					$final[ $group ][] = $tmp_data;
				}
			}
		}

		//-----------------------------------------
		// Print..
		//-----------------------------------------

		if ( ! count($final) )
		{
			$this->ipsclass->html .= "<div class='tableborder'>
								 <div class='tableheaderalt'>Результаты поиска</div>
								 <div class='tablepad'>
								  <b>Вы искали: ".htmlentities($SEARCH_word)."</b>
								  <br />
								  <br />
								  Не найдено ни одной записи, соответствующей искомой строке. Попробуйте еще раз и расширьте диапазон поиска.
								 </div>
								</div>";

			$this->ipsclass->admin->output();
		}

		//-----------------------------------------
		// SET ids right
		//-----------------------------------------

		$this->ipsclass->input['id']   = $SEARCH_set;
		$this->ipsclass->input['p']    = $this_set['set_skin_set_parent'];
		$this->ipsclass->input['code'] = 'template-sections-list';
		$this->ipsclass->input['act']  = 'templ';
		$this->ipsclass->form_code     = 'section=lookandfeel&amp;act=templ';
		$this->ipsclass->form_code_js  = str_replace( '&amp;', '&', $this->ipsclass->form_code );

		//-----------------------------------------
		// Pass array
		//-----------------------------------------

		require_once( ROOT_PATH."sources/action_admin/skin_template_bits.php" );
		$temp              =  new ad_skin_template_bits();
		$temp->ipsclass    =& $this->ipsclass;
		$temp->search_bits =  $final;
		$temp->auto_run();
	}

	//-----------------------------------------
	// SEARCH & REPLACE SPLASH
	//-----------------------------------------

	function searchreplace_start()
	{
		$skin_list = $this->_get_skinlist( 1 );

		$this->ipsclass->admin->page_detail = "Этот инструмеент позволяет вам быстро найти и заменить HTML-коды.";
		$this->ipsclass->admin->page_title  = "Поиск и замена в стилях";

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'     ),
																			 2 => array( 'code' , 'simplesearch'  ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Простой поиск" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Искать...</b><br /><span style='color:gray'>Введите одно слово или целый блок HTML для поиска</span>",
															       $this->ipsclass->adskin->form_simple_input( 'searchkeywords', '', 30 )
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Поиск в...</b>",
															     $skin_list
															     ."<br /><input type='checkbox' name='searchall' value='1'> Так же искать в родительских стилях, включая главный стиль."
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Искать");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// Search and replace
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'     ),
															     			 2 => array( 'code' , 'searchandreplace'  ),
															     	 		 4 => array( 'section', $this->ipsclass->section_code ),
													    )      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Поиск и замена" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Поиск по...</b><br /><span style='color:gray'>Введите одно слово или целый блок HTML для поиска.<br />Если включены регулярные выражения, то вы можете их использовать в своем запросе.</span>",
															      $this->ipsclass->adskin->form_textarea( 'searchfor', $_POST['searchfor'] )
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Заменить на...</b><br /><span style='color:gray'>Введите одно слово или целый блок HTML для замены.<br />Если включены регулярные выражения, то вы можете их использовать в своем запросе.</span>",
															     $this->ipsclass->adskin->form_textarea( 'replacewith', $_POST['replacewith'] )
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Поиск в стиле...</b><br /><span style='color:gray'>Примечание: поиск и замена будут произведены только в шаблонах выбранного стиля. Действие не будет произведено для шаблонов вложенных стилей.</span>",
															     $skin_list
															     ."<br /><input type='checkbox' name='searchall' value='1'> Так же искать в родительских стилях, включая главный стиль."
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Только тестирование запроса?</b><br /><span style='color:gray'>При выборе 'Да', вы сможете увидеть предложенные замены.</span>",
															      $this->ipsclass->adskin->form_yes_no( 'testonly', 1 )
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Включить регулярные выражения?</b><br /><span style='color:gray'>Если выбрано 'Да', вы сможете использовать 'regex' в запросах поиска и замены.
																Пример: Замена тегов <b>&lt;br&gt;
																 <br />Искать: <b>&lt;(br)&#92;s?/?&gt;</b>
																 <br />Заменить на: <b>&lt;&#92;&#92;1 clear='all' /&gt;</b></span>",
															      $this->ipsclass->adskin->form_yes_no( 'regexmode', 0 )
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Искать и заменить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
	}

	//-----------------------------------------
	// Swap members...
	//-----------------------------------------

	function change_member()
	{
		if( is_array($this->ipsclass->input['set_skin_set_id']) AND count($this->ipsclass->input['set_skin_set_id']) )
		{
			$this->ipsclass->input['set_skin_set_id'] = $this->ipsclass->clean_int_array($this->ipsclass->input['set_skin_set_id']);

			$query_bit = " IN (".implode(",",$this->ipsclass->input['set_skin_set_id']).")";
		}
		else
		{
			$this->ipsclass->main_msg = "Вы не выбрали ни одного стиля для удаления выбора его у пользователей";
			$this->show_intro();
			return;
		}

		$new_id = intval($this->ipsclass->input['set_skin_set_id2']);

		if ($new_id == 'n')
		{
			$this->ipsclass->DB->do_update( 'members', array( 'skin' => '' ), 'skin'.$query_bit );
		}
		else
		{
			$this->ipsclass->DB->do_update( 'members', array( 'skin' => $new_id ), 'skin'.$query_bit );
		}

		$this->ipsclass->main_msg = "Шаблоны пользователей обновлены";

		$this->show_intro();
	}

	//-----------------------------------------
	// Swap forums...
	//-----------------------------------------

	function change_forum()
	{
		if( is_array($this->ipsclass->input['set_skin_set_id']) AND count($this->ipsclass->input['set_skin_set_id']) )
		{
			$this->ipsclass->input['set_skin_set_id'] = $this->ipsclass->clean_int_array($this->ipsclass->input['set_skin_set_id']);

			$query_bit = " IN (".implode(",",$this->ipsclass->input['set_skin_set_id']).")";
		}
		else
		{
			$this->ipsclass->main_msg = "You did not choose any skin(s) to remove from the member's choice";
			$this->show_intro();
			return;
		}

		$new_id = intval($this->ipsclass->input['set_skin_set_id2']);

		if ($new_id == 'n')
		{
			$this->ipsclass->DB->do_update( 'forums', array( 'skin_id' => '' ), 'skin_id'.$query_bit );
		}
		else
		{
			$this->ipsclass->DB->do_update( 'forums', array( 'skin_id' => $new_id ), 'skin_id'.$query_bit );
		}

		$this->ipsclass->update_forum_cache();

		$this->ipsclass->main_msg = "Forums updated";

		$this->show_intro();
	}

	//-----------------------------------------
	// REBUILD MASTER
	//-----------------------------------------

	function rebuildmaster()
	{
		$pid = intval($this->ipsclass->input['phplocation']);
		$cid = intval($this->ipsclass->input['csslocation']);

		if ( $this->ipsclass->input['phpyes'] )
		{
			if ( ! file_exists( CACHE_PATH.'cache/skin_cache/cacheid_'.$pid ) )
			{
				$this->ipsclass->main_msg = "IPB не может обновить главный шаблон, так как директория 'cacheid_$pid' не существует";
			}

			$this->ipsclass->cache_func->_rebuild_templates_from_php($pid);

			$this->ipsclass->main_msg = 'Попытка обновить PHP кеш-файлы главного стиля...';

			$this->ipsclass->main_msg .= "<br />".implode("<br />", $this->ipsclass->cache_func->messages);
		}

		if ( $this->ipsclass->input['cssyes'] )
		{
			if ( ! file_exists( CACHE_PATH.'style_images/css_'.$cid.'.css' ) )
			{
				$this->ipsclass->main_msg = "IPB не может обновить базовые CSS стили, так как директория 'css_$cid' не существует";
			}

			$css = @file_get_contents( CACHE_PATH.'style_images/css_'.$cid.'.css' );

			if ( ! $css )
			{
				$this->ipsclass->main_msg = "IPB не может обновить базовые CSS стили, так как директория 'css_$cid' пустая";
			}

			$css = trim( preg_replace( "#^.*\*~START CSS~\*/#s", "", $css ) );

			//-----------------------------------------
			// Attempt to rearrange style_images dir stuff
			//-----------------------------------------

			$this->ipsclass->main_msg = "Попытка восстановить CSS настройки из кеша CSS файлов...";

			$css = preg_replace( "#url\(([\"'])?(.+?)/(.+?)([\"'])?\)#is", "url(\\1style_images/1/\\3\\4)", $css );

			$this->ipsclass->DB->do_update( 'skin_sets', array( 'set_css' => $css, 'set_cache_css' => $css, 'set_css_updated' => time() ), 'set_skin_set_id=1' );

			$this->ipsclass->cache_func->_write_css_to_cache(1);

			$this->ipsclass->main_msg .= "<br />".implode("<br />", $this->ipsclass->cache_func->messages);
		}

		$this->show_intro();
	}

	//-----------------------------------------
	// REBUILD CACHES
	//-----------------------------------------

	function rebuildcaches()
	{
		$this->ipsclass->cache_func->_rebuild_all_caches(array($this->ipsclass->input['set_skin_set_id']));

		$this->ipsclass->main_msg = 'Кеш стиля обновлен (ID: '.$this->ipsclass->input['set_skin_set_id'].')';

		$this->ipsclass->main_msg .= "<br />".implode("<br />", $this->ipsclass->cache_func->messages);

		$this->show_intro();
	}

	//-----------------------------------------
	// SHOW MAIN SCREEN
	//-----------------------------------------

	function show_intro()
	{
		$skin_list = $this->_get_skinlist();

		//-----------------------------------------
		// REBUILD MASTER TEMPLATES
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																			 2 => array( 'code' , 'rebuildmasterhtml'  ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
													    			)      );

		//-----------------------------------------
		// Attempt to get filemtime
		//-----------------------------------------

		$filemtime  = 0;
		$file       = ROOT_PATH . 'resources/ipb_templates.xml';
		$error      = "";
		$notice     = "";
		$extra_html = "";

		if ( @file_exists( $file ) )
		{
			if ( $filemtime = @filemtime( $file ) )
			{
				$notice = "resources/ipb_templates.xml — последнее обновление: " . $this->ipsclass->get_date( $filemtime, 'JOINED' );
			}

			if ( $filemtime2 = @filemtime( ROOT_PATH . 'sources/ipsclass.php' ) )
			{
				if ( ( $filemtime2 - (86400 * 7) ) > $filemtime )
				{
					$error = "Пожалуйста, проверьте файл «resources/ipb_templates.xml» — «ipsclass.php» более чем на неделю новее.";
				}
			}
		}
		else
		{
			$error = "Невозможно найти файл «{$file}» — убедитесь, что он был загружен.";
		}

		//-----------------------------------------
		// Got notices?
		//-----------------------------------------

		if ( $notice )
		{
			$extra_html .= "<div class='input-ok-content'>$notice</div>";
		}

		if ( $error )
		{
			$extra_html .= "<div class='input-warn-content'>$error</div>";
		}

		//-----------------------------------------
		// Continue
		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "{none}"  , "100%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Обновление HTML шаблонов главного стиля" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Воспользовавшись этим инструментом, вы обновить все HTML шаблоны главного стиля из файла «ipb_templates.xml».
																			  <br /><span style='color:gray'>После запуска инструмента, возможно, вам потребуется обновить кеш-файлы стилей, чтобы увидеть изменения.</span>
																			  $extra_html",
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// REBUILD MASTER CSS and BOARDWRAPPER
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																			 2 => array( 'code' , 'rebuildmastercomponents'  ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
													    			)      );

		//-----------------------------------------
		// Attempt to get filemtime
		//-----------------------------------------

		$filemtime  = 0;
		$file       = ROOT_PATH . 'resources/skinsets.xml';
		$error      = "";
		$notice     = "";
		$extra_html = "";

		if ( @file_exists( $file ) )
		{
			if ( $filemtime = @filemtime( $file ) )
			{
				$notice = "resources/skinsets.xml — последнее обновление: " . $this->ipsclass->get_date( $filemtime, 'JOINED' );
			}

			if ( $filemtime2 = @filemtime( ROOT_PATH . 'sources/ipsclass.php' ) )
			{
				if ( ( $filemtime2 - (86400 * 7) ) > $filemtime )
				{
					$error = "Пожалуйста, проверьте файл «resources/skinsets.xml» — «ipsclass.php» более чем на неделю новее.";
				}
			}
		}
		else
		{
			$error = "Невозможно найти файл «{$file}» — убедитесь, что он был загружен.";
		}

		//-----------------------------------------
		// Got notices?
		//-----------------------------------------

		if ( $notice )
		{
			$extra_html .= "<div class='input-ok-content'>$notice</div>";
		}

		if ( $error )
		{
			$extra_html .= "<div class='input-warn-content'>$error</div>";
		}

		//-----------------------------------------
		// Continue
		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "{none}"  , "100%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Обновление компонентов главного стиля" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Воспользовавшись этим инструментом, вы обновите общий шаблон форума, CSS, а так же настройки главного стиля из файла «skinsets.xml».
																			  <br /><span style='color:gray'>После запуска инструмента, возможно, вам потребуется обновить кеш-файлы стилей, чтобы увидеть изменения.</span>
																			  $extra_html",
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// REBUILD MASTER MACROS
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																			 2 => array( 'code' , 'rebuildmastermacros'  ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
													    			)      );

		//-----------------------------------------
		// Attempt to get filemtime
		//-----------------------------------------

		$filemtime  = 0;
		$file       = ROOT_PATH . 'resources/macro.xml';
		$error      = "";
		$notice     = "";
		$extra_html = "";

		if ( @file_exists( $file ) )
		{
			if ( $filemtime = @filemtime( $file ) )
			{
				$notice = "resources/macro.xml — последнее обновление: " . $this->ipsclass->get_date( $filemtime, 'JOINED' );
			}

			if ( $filemtime2 = @filemtime( ROOT_PATH . 'sources/ipsclass.php' ) )
			{
				if ( ( $filemtime2 - (86400 * 7) ) > $filemtime )
				{
					$error = "Пожалуйста, проверьте файл «resources/macro.xml» — «ipsclass.php» более чем на неделю новее.";
				}
			}
		}
		else
		{
			$error = "Невозможно найти файл «{$file}» — убедитесь, что он был загружен.";
		}

		//-----------------------------------------
		// Got notices?
		//-----------------------------------------

		if ( $notice )
		{
			$extra_html .= "<div class='input-ok-content'>$notice</div>";
		}

		if ( $error )
		{
			$extra_html .= "<div class='input-warn-content'>$error</div>";
		}

		//-----------------------------------------
		// Continue
		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "{none}"  , "100%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Обновление макросов главного стиля" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Воспользовавшись этим инструментом, вы обновите все макросы главного стиля из файла «macro.xml».
																			  <br /><span style='color:gray'>После запуска инструмента, возможно, вам потребуется обновить кеш-файлы стилей, чтобы увидеть изменения.</span>
																			  $extra_html",
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// REBUILD CACHES
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
															     			 2 => array( 'code' , 'rebuildcaches'  ),
															     			 4 => array( 'section', $this->ipsclass->section_code ),
													    )      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Обновление кеш-файлов" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Выберите стиль, у которого необходимо обновить кеш-файлы.<br /><span style='color:gray'>Эта опция позволяет обновить все кеш-файлы всех HTML шаблонов, CSS стилей и макросов выбранного стиля, а также всех вложенных шаблонов.</span><br />[ <a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=sets&code=rebuildalltemplates'>Обновить кеш-файлы у всех стилей</a> ]",
															     $skin_list
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// CHANGE MEMBERS
		//-----------------------------------------

		$dd_two = str_replace( "select name='set_skin_set_id'", "select name='set_skin_set_id2'", $skin_list );
		$dd_two = str_replace( "<!--DD.OPTIONS-->", "<option value='n'>Нет — использовать по умолчанию</option>", $dd_two );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																			 2 => array( 'code' , 'changemember'  ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
													   			    )      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Переустановка стиля у пользователей" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "У пользователей, использующих стиль...<br /><span style='color:gray'>Вы можете выбрать несколько стилей, зажав «Ctrl» на клавиатуре.</span>",
																			 str_replace( "select name='set_skin_set_id'", "select name='set_skin_set_id[]' multiple='multiple' size='6'", $skin_list )
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Переустановить на...",
															     $dd_two
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																			 2 => array( 'code' , 'changeforum'  ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
													   			    )      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Переустановка стиля у форумов" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "У форумов, использующих стиль...<br /><span style='color:gray'>Вы можете выбрать несколько стилей, зажав «Ctrl» на клавиатуре.</span>",
																			 str_replace( "select name='set_skin_set_id'", "select name='set_skin_set_id[]' multiple='multiple' size='6'", $skin_list )
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Переустановить на...",
															     $dd_two
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// REBUILD MASTER
		//-----------------------------------------

		if ( IN_DEV )
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																				 2 => array( 'code' , 'rebuildmaster'  ),
																				 4 => array( 'section', $this->ipsclass->section_code ),
																		)      );

			$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
			$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Обновление главного стиля" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "При помощи этого инструмента вы сможете обновить главный стиль из CSS и PHP-файлов.<br /><span style='color:gray'>Пожалуйста, используйте этот инструмент крайне аккуратно.</span>",
																				 "<input type='checkbox' name='phpyes' value='1' /> Директория кеш-файлов PHP: skin_cache/cacheid_ ".$this->ipsclass->adskin->form_simple_input( 'phplocation', '1', 3 )."<br />".
																				 "<input type='checkbox' name='cssyes' value='1' /> Кеш-файл CSS: style_images/css_ ".$this->ipsclass->adskin->form_simple_input( 'csslocation', '1',3 )
																		)      );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

			//-----------------------------------------
			// Rewrite cache files to directory
			//-----------------------------------------

			$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'skintools'      ),
																     			 2 => array( 'code' , 'rewritemastercache'  ),
																     			 4 => array( 'section', $this->ipsclass->section_code ),
														    )      );

			$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
			$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Перезапись «cacheid_1» из базы данных" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Обновление кеш-файлов директории «cacheid_1» главного стиля...<br /><span style='color:gray'>Этот инструмент перезапишет все кеш-файлы главного стиля из базы данных.</span>",
														    			)      );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Запустить");

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}

	//-----------------------------------------
	// Get dropdown of skin
	//-----------------------------------------

	function _get_skinlist( $check_default=0 )
	{
		$skin_sets = array();
		$skin_list = "<select name='set_skin_set_id' class='dropdown'><!--DD.OPTIONS-->";

		//-----------------------------------------
		// Get formatted list of skin sets
		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'order' => 'set_skin_set_parent, set_skin_set_id' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $s = $this->ipsclass->DB->fetch_row() )
		{
			$skin_sets[ $s['set_skin_set_id'] ] = $s;
			$skin_sets[ $s['set_skin_set_parent'] ]['_children'][] = $s['set_skin_set_id'];
		}

		//-----------------------------------------
		// Roots
		//-----------------------------------------

		foreach( $skin_sets as $id => $data )
		{
			if ( isset($data['set_skin_set_parent']) AND $data['set_skin_set_parent'] < 1 and $id > 1 )
			{
				if( $check_default )
				{
					$default = $data['set_default'] ? " selected='selected'" : '';
				}

				$skin_list .= "\n<option value='$id'{$default}>{$data['set_name']}</option><!--CHILDREN:{$id}-->";
			}
		}

		//-----------------------------------------
		// Kids...
		//-----------------------------------------

		foreach( $skin_sets as $id => $data )
		{
			if ( isset($data['_children']) AND is_array( $data['_children'] ) and count( $data['_children'] ) > 0 )
			{
				$html = "";

				foreach( $data['_children'] as $cid )
				{
					if( $check_default )
					{
						$default = $skin_sets[ $cid ]['set_default'] ? " selected='selected'" : '';
					}

					$html .= "\n<option value='$cid'{$default}>---- {$skin_sets[ $cid ]['set_name']}</option>";
				}

				$skin_list = str_replace( "<!--CHILDREN:{$id}-->", $html, $skin_list );
			}
		}

		$skin_list .= "</select>";

		return $skin_list;
	}

	//-----------------------------------------
	// Sort by group name
	//-----------------------------------------

	function perly_alpha_sort($a, $b)
	{
		return strcmp($a['easy_name'], $b['easy_name']);
	}

}


?>