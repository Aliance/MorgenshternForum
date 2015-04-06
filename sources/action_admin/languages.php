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
|   > $Date: 2006-10-30 17:01:56 -0600 (Mon, 30 Oct 2006) $
|   > $Revision: 685 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > Language functions
|   > Module written by Matt Mecham
|   > Date started: 22nd April 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Неверный вход</h1> У Вас нет доступа к директиве этого файла. Если вы проводили обновления, убедитесь, что не забыли обновить 'admin.php'.";
	exit();
}

class ad_languages {

	var $base_url;
	var $ipsclass;

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
	var $perm_child = "lang";

	function auto_run()
	{
		if ( TRIAL_VERSION )
		{
			print "Эта функция отключена в бесплатной версии.";
			exit();
		}

		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Управление языками' );

		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{

			case 'add':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->add_language();
				break;

			case 'edit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->do_form('edit');
				break;

			case 'edit2':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->show_file();
				break;

			case 'doadd':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->save_wrapper('add');
				break;

			case 'doedit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->save_langfile();
				break;

			case 'remove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->remove();
				break;

			case 'editinfo':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->edit_info();
				break;

			case 'export':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':export' );
				$this->export();
				break;

			case 'import':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':import' );
				$this->import();
				break;

			case 'doimport':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':import' );
				$this->doimport();
				break;

			case 'makedefault':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->make_default();
				break;

			case 'swap':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->member_swap();
				break;
			//-----------------------------------------
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->list_current();
				break;
		}
	}

	//-----------------------------------------
	// Swap members choice
	//-----------------------------------------

	function member_swap()
	{
		$new_dir = "";
		$old_dir = "";
		$this->ipsclass->input['new'] = intval($this->ipsclass->input['new']);
		$this->ipsclass->input['old'] = intval($this->ipsclass->input['old']);

		if ( $this->ipsclass->input['old'] and $this->ipsclass->input['new'] )
		{
			if ( $this->ipsclass->input['old'] != 'none' )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid IN ( {$this->ipsclass->input['old']}, {$this->ipsclass->input['new']})" ) );
				$this->ipsclass->DB->simple_exec();
			}
			else
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid={$this->ipsclass->input['new']}" ) );
				$this->ipsclass->DB->simple_exec();
			}

			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				if (  $r['lid'] == $this->ipsclass->input['old'] )
				{
					$old_dir = $r['ldir'];
				}

				if (  $r['lid'] == $this->ipsclass->input['new'] )
				{
					$new_dir = $r['ldir'];
				}
			}

			if ( $new_dir and $old_dir )
			{
				$this->ipsclass->DB->do_update( 'members', array( 'language' => $new_dir ), "language='{$old_dir}'" );
			}
			else if ( $this->ipsclass->input['old'] == 'none' )
			{
				$this->ipsclass->DB->do_update( 'members', array( 'language' => $new_dir ), "language='' or language IS NULL" );
			}
		}

		$this->ipsclass->main_msg = "У всех пользователей переустановлен язык";
		$this->list_current();
	}

	//-----------------------------------------
	// Rebuild CACHE
	//-----------------------------------------

	function rebuild_cache()
	{
		$this->ipsclass->cache['languages'] = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['languages'][] = $r;
		}

		$this->ipsclass->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------

	function make_default()
	{
		$new_dir = stripslashes(urldecode(trim($_GET['id'])));

		if ($new_dir == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		// Update conf file

		$this->ipsclass->admin->rebuild_config( array( 'default_language' => $new_dir ) );

		// Bring it all back to yoooo!

		$this->ipsclass->boink_it($this->ipsclass->base_url."&{$this->ipsclass->form_code}");

	}


	/*-------------------------------------------------------------------------*/
	// IMPORT - DO IT
	/*-------------------------------------------------------------------------*/

	function doimport()
	{
		$messages = array();

		//-----------------------------------------
		// Check
		//-----------------------------------------

		if ( ! $this->ipsclass->input['lang_name'] )
		{
			$this->ipsclass->admin->error("Вы должны ввести названия для импортируемого языка!");
		}

		if ( $_FILES['FILE_UPLOAD']['name'] == "" or ! $_FILES['FILE_UPLOAD']['name'] or ($_FILES['FILE_UPLOAD']['name'] == "none") )
		{
			//-----------------------------------------
			// check and load from server
			//-----------------------------------------

			if ( ! $this->ipsclass->input['lang_location'] )
			{
				$this->ipsclass->main_msg = "Импортируемый файл не найден.";
				$this->import();
			}

			if ( ! file_exists( ROOT_PATH . $this->ipsclass->input['lang_location'] ) )
			{
				$this->ipsclass->main_msg = "Невозможно найти файл: " . ROOT_PATH . $this->ipsclass->input['lang_location'];
				$this->import();
			}

			if ( preg_match( "#\.gz$#", $this->ipsclass->input['lang_location'] ) )
			{
				if ( $FH = @gzopen( ROOT_PATH.$this->ipsclass->input['lang_location'], 'rb' ) )
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
				if ( $FH = @fopen( ROOT_PATH.$this->ipsclass->input['lang_location'], 'rb' ) )
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

			if( !$content )
			{
				$this->ipsclass->main_msg = "There was an error processing the file.";
				$this->import();
			}
		}

		//-----------------------------------------
		// Check dirs, etc
		//-----------------------------------------

		$safename = substr( str_replace( " ", "", strtolower( preg_replace( "[^a-zA-Z0-9]", "", $this->ipsclass->input['lang_name'] ) ) ), 0, 10 );

		if ( @file_exists( CACHE_PATH.'cache/lang_cache/'.$safename ) )
		{
			$safename = $safename . substr( time(), 5, 10 );
		}

		if ( ! $content )
		{
			$this->ipsclass->main_msg = "XML-архив пуст, пожалуйста, проверьте архив и попробуйте еще раз";
			$this->import();
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->lite_parser = 1;
		$xml->xml_parse_document( $content );

		//-----------------------------------------
		// pArse
		//-----------------------------------------

		$lang_array = array();

		foreach( $xml->xml_array['languageexport']['languagegroup']['langbit'] as $entry )
		{
			if( $entry['file']['VALUE'] == 'lang_javascript.js' )
			{
				$lang_array[ $entry['file']['VALUE'] ] = $entry['value']['VALUE'];
			}
			else
			{
			$key   = $entry['key']['VALUE'];
			$value = $entry['value']['VALUE'];
			$file  = $entry['file']['VALUE'];

			$lang_array[ $file ][ $key ] = $value;
		}
		}

		//-----------------------------------------
		// Sort...
		//-----------------------------------------

		ksort($lang_array);

		if ( ! count( $lang_array ) )
		{
			$this->ipsclass->main_msg = "XML-архив пуст, пожалуйста, проверьте архив и попробуйте еще раз";
			$this->import();
		}

		//-----------------------------------------
		// Attempt dir creation
		//-----------------------------------------

		if ( ! @mkdir( CACHE_PATH.'cache/lang_cache/'.$safename, 0777 ) )
		{
			$this->ipsclass->main_msg = "Невозможно создать директорию '$safename' в директории './lang' - проверьте права доступа (CHMOD) у директории 'lang' и попробуйте еще раз.";
			$this->import();
		}
		else
		{
			@chmod( CACHE_PATH.'cache/lang_cache/'.$safename, 0777 );
		}

		//print "<pre>"; print_r( $new_file_array ); exit();

		//-----------------------------------------
		// Loop, sort - compile and save
		//-----------------------------------------

		foreach( $lang_array as $file => $data )
		{
			$new_file_array = array();

			$real_name      = $file;

			if( $real_name == 'lang_javascript.js' )
			{
				$file_contents = base64_decode( $data );

				if ( $FH = @fopen( CACHE_PATH.'cache/lang_cache/'.$safename.'/'.$real_name, 'w' ) )
				{
					@fwrite( $FH, $file_contents );
					@fclose( $FH );

					$messages[] = "Файл '{$file}' импортирован!"; 
				}
				else
				{
					$messages[] = "Невозможно создать файл '{$file}', файл пропущен...";
				}

				continue;
			}

			if( is_array($lang_array[ $file ]) AND count($lang_array[ $file ]) )
			{
			foreach( $lang_array[ $file ] as $k => $v )
			{
				$new_file_array[ $k ] = $v;
			}

			ksort($new_file_array);
			}

			if ( count( $new_file_array ) )
			{
				$file_contents = "<?php\n\n".'$lang = array('."\n";

				foreach( $new_file_array as $k => $v)
				{
					$file_contents .= "\n'".$k."'  => \"".preg_replace( '/"/', '\\"', stripslashes($v) )."\",";
				}

				$file_contents .= "\n\n);\n\n?".">";

				if ( $FH = @fopen( CACHE_PATH.'cache/lang_cache/'.$safename.'/'.$real_name, 'w' ) )
				{
					@fwrite( $FH, $file_contents );
					@fclose( $FH );

					$messages[] = "Файл '{$file}' импортирован!";
				}
				else
				{
					$messages[] = "Невозможно создать файл '{$file}', файл пропущен...";
				}

			}
			else
			{
				$messages[] = "Файл '{$file}' пуст, файл пропущен...";
			}

			unset($new_file_array);
			unset($file_contents);
		}

		//-----------------------------------------
		// Write to DB
		//-----------------------------------------

		$this->ipsclass->DB->do_insert( 'languages', array(
											'ldir'    => $safename,
											'lname'   => $this->ipsclass->input['lang_name'],
											'lauthor' => $xml->xml_array['languageexport']['ATTRIBUTES']['author'],
											'lemail'  => $xml->xml_array['languageexport']['ATTRIBUTES']['email'],
					  )                   );

		$this->rebuild_cache();

		$this->ipsclass->main_msg = "Импортирование успешно завершено<br />".implode( "\n<br />", $messages );
		$this->import();
	}

	/*-------------------------------------------------------------------------*/
	// Import XML Archive (FORM)
	/*-------------------------------------------------------------------------*/

	function import()
	{
		$this->ipsclass->admin->page_detail = "Эта секция позволяет вам импортировать XML-файлы содержащие языковые настройки.";
		$this->ipsclass->admin->page_title  = "Импорт языка";
		$this->ipsclass->admin->nav[] 		= array( '', 'Import Language Pack' );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'          , 'doimport'    ),
																			 2 => array( 'act'           , 'lang'        ),
																			 3 => array( 'MAX_FILE_SIZE' , '10000000000' ),
																			 4 => array( 'section'       , $this->ipsclass->section_code ),
																 ) , "uploadform", " enctype='multipart/form-data'"      );

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "50%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "50%" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Импортирование XML-файла" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Загрузка XML-архива с языковыми настройками</b><div style='color:gray'>Выберите файл для загрузки с вашего компьютера. Выбранный файл должен начинаться с 'ipb_language' и заканчиваться либо '.xml', либо '.xml.gz'.</div>" ,
										  				         $this->ipsclass->adskin->form_upload(  )
								                        )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b><u>ИЛИ</u> введите название XML-архива</b><div style='color:gray'>Этот файл должен быть загружен в основную директорию форума.</div>" ,
										  				         $this->ipsclass->adskin->form_input( 'lang_location', 'ipb_language.xml.gz'  )
								                        )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Введите название для новых языковых настроек</b><div style='color:gray'>Например: Русский, RU, English, US...</div>" ,
										  				         $this->ipsclass->adskin->form_input( 'lang_name', ''  )
								                        )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Импортирование XML-файла");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();


	}


	/*-------------------------------------------------------------------------*/
	// EXPORT: Export languages into XML download
	/*-------------------------------------------------------------------------*/

	function export()
	{
		//-----------------------------------------
		// check
		//-----------------------------------------

		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		//-----------------------------------------
		// Get data from DB
		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID");
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Set Doctype if not using ISO-8859-1
		//-----------------------------------------
		$xml->doc_type = $this->ipsclass->vars['gb_char_set'];
		$xml->class_xml();

		//-----------------------------------------
		// Set up..
		//-----------------------------------------

		$lang_dir = ROOT_PATH."cache/lang_cache/".$row['ldir'];

		if ( ! is_dir($lang_dir) )
		{
			$this->ipsclass->admin->error("Невозможно найти директорию '$lang_dir', проверьте существует ли эта директория?");
		}

		$lang_files = array( 'lang_boards.php', 'lang_buddy.php', 'lang_calendar.php', 'lang_emails.php', 'lang_email_content.php', 'lang_error.php',
								'lang_forum.php', 'lang_global.php', 'lang_help.php', 'lang_legends.php', 'lang_login.php', 'lang_mlist.php',
							 	'lang_mod.php', 'lang_msg.php', 'lang_online.php', 'lang_portal.php', 'lang_post.php', 'lang_printpage.php',
							 	'lang_profile.php', 'lang_register.php', 'lang_search.php', 'lang_stats.php', 'lang_subscriptions.php',
							 	'lang_topic.php', 'lang_ucp.php', 'lang_chatpara.php' , 'lang_editors.php', 'lang_chatsigma.php', 'lang_javascript.js',
							 	'acp_lang_acpperms.php', 'acp_lang_member.php', 'acp_lang_portal.php', 'lang_gallery.php', 'lang_gallery_location.php',
							 	'lang_blog.php', 'lang_blog_emails.php', 'lang_blog_location.php', 'lang_blog_portal.php', 'lang_blog_ucp.php', 'lang_downloads.php'
						   );

		//-----------------------------------------
		// Start XML
		//-----------------------------------------

		$xml->xml_set_root( 'languageexport', array( 'exported' => time(), 'author' => $row['lauthor'], 'email' => $row['lemail'] ) );

		$xml->xml_add_group( 'languagegroup' );

		//-----------------------------------------
		// Get all the lang bits
		//-----------------------------------------

		foreach( $lang_files as $file )
		{
			if ( @is_file( $lang_dir.'/'.$file ) )
			{
				$lang = array();

				if( $file == 'lang_javascript.js' )
				{
					$content   = array();

					$js_contents = file_get_contents( $lang_dir.'/'.$file );

					$content[]	= $xml->xml_build_simple_tag( 'key'  , 'blah' );
					$content[]	= $xml->xml_build_simple_tag( 'value'  , base64_encode( $js_contents ) );
					$content[]	= $xml->xml_build_simple_tag( 'file' , $file );
					$entry[] 	= $xml->xml_build_entry( 'langbit', $content );
				}
				else
				{
					require( $lang_dir.'/'.$file );

				foreach( $lang as $k => $v )
				{
					$content   = array();

					$content[] = $xml->xml_build_simple_tag( 'key'  , $k    );
					$content[] = $xml->xml_build_simple_tag( 'value', $v    );
					$content[] = $xml->xml_build_simple_tag( 'file' , $file );

					$entry[] = $xml->xml_build_entry( 'langbit', $content );
				}
			}
		}
		}

		$xml->xml_add_entry_to_group( 'languagegroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$this->ipsclass->admin->show_download( $xml->xml_document, 'ipb_language.xml' );
	}



	function show_file()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID.");
		}

		//-----------------------------------------

		$lang_dir   = CACHE_PATH."cache/lang_cache/".$row['ldir'];

		$form_array = array();

		$lang_file = $lang_dir."/".$this->ipsclass->input['lang_file'];


		if ( ! is_writeable($lang_dir) )
		{
			$this->ipsclass->admin->error("Невозможно записать в директорию '$lang_dir', проверьте права доступа (CHMOD) и, если необходимо установить права доступа 0777. IPB не может сделать это за вас.");
		}

		if (! file_exists($lang_file) )
		{
			$this->ipsclass->admin->error("Cannot locate {$this->ipsclass->input['lang_file']} in '$lang_dir', please go back and check the input");
		}
		else
		{
			require $lang_file;
		}

		if ($this->ipsclass->input['lang_file'] == 'email_content.php')
		{
			$is_email = 1;
		}

		if ( ! is_writeable($lang_file) )
		{
			$this->ipsclass->admin->error("Невозможно записать в файл '$lang_file', проверьте права доступа (CHMOD) и, если необходимо установить права доступа 0777. IPB не может сделать это за вас.");
		}


		$this->ipsclass->admin->page_detail = "Вы можете изменить любую фразу в формах ниже.";
		$this->ipsclass->admin->page_title  = "Изменение языкового модуля: ".$row['lname'];
		$this->ipsclass->admin->nav[] 		= array( '', "Изменение языка «{$row['lname']}»" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'      , 'doedit'    ),
																			 2 => array( 'act'       , 'lang'      ),
																			 3 => array( 'id'        , $this->ipsclass->input['id']   ),
																			 4 => array( 'lang_file' , $this->ipsclass->input['lang_file']   ),
																			 5 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "Название языкового блока" , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "Содержимое языкового блока"    , "80%" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Языковой файл: ".$this->ipsclass->input['lang_file'] );

		foreach($lang as $k => $v)
		{
			//-----------------------------------------
			// Swop < and > into ascii entities
			// to prevent textarea breaking html
			//-----------------------------------------

			$v = stripslashes($v);

			$v = str_replace("&", "&#38;", $v );
			$v = str_replace("<", "&#60;", $v );
			$v = str_replace(">", "&#62;", $v );
			$v = str_replace("'", "&#39;", $v );

			$rows = 5;

			$cols = 70;

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
																	  "&lt;ibf.lang.<b>".$k."</b>&gt;",
																	  $this->ipsclass->adskin->form_textarea('XX_'.$k, $v, $cols, $rows),
														   )      );
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Изменить этот файл");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();


	}

	//-----------------------------------------
	// Edit language pack information
	//-----------------------------------------

	function edit_info()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID");
		}

		$final['lname'] = stripslashes($_POST['lname']);

		if (isset($_POST['lname']))
		{
			$final['lauthor'] = stripslashes($_POST['lauthor']);
			$final['lemail']  = stripslashes($_POST['lemail']);
		}

		$this->ipsclass->DB->do_update( 'languages', $final, "lid='".$this->ipsclass->input['id']."'" );

		$this->rebuild_cache();

		$this->ipsclass->admin->done_screen("Языковой модуль обновлен", "Управление языками", "{$this->ipsclass->form_code}" );

	}

	//-----------------------------------------
	// Add language pack
	//-----------------------------------------


	function add_language()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		//-----------------------------------------

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID");
		}

		//-----------------------------------------

		//-----------------------------------------

		if ( ! is_writeable(CACHE_PATH.'cache/lang_cache') )
		{
			$this->ipsclass->admin->error("Невозможно записать в директорию /cache/lang_cache/, проверьте права доступа (CHMOD) и, если необходимо установить права доступа 0777. IPB не может сделать это за вас");
		}

		//-----------------------------------------

		if ( ! is_dir(CACHE_PATH.'cache/lang_cache/'.$row['ldir']) )
		{
			$this->ipsclass->admin->error("Невозможно найти оригинальный язык для копирования, вернитесь назад и попробуйте еще раз");
		}

		//-----------------------------------------

		$row['lname'] = $row['lname'].".2";

		// Insert a new row into the DB...

		$final = array();

		foreach($row as $k => $v)
		{
			if ($k == 'lid')
			{
				continue;
			}
			else
			{
				$final[ $k ] = $v;
			}
		}

		$this->ipsclass->DB->do_insert( 'languages', $final );

		$new_id = $this->ipsclass->DB->get_insert_id();

		//-----------------------------------------

		if ( ! $this->ipsclass->admin->copy_dir( CACHE_PATH.'cache/lang_cache/'.$row['ldir'] , CACHE_PATH.'cache/lang_cache/'.$new_id ) )
		{
			$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'languages', 'where' => "lid='$new_id'" ) );

			$this->ipsclass->admin->error( $this->ipsclass->admin->errors );
		}
		else
		{
			$this->ipsclass->DB->do_update( 'languages', array( 'ldir' => $new_id ), "lid='$new_id'" );
		}

		//-----------------------------------------
		// Pass to edit / add form...
		//-----------------------------------------

		$this->rebuild_cache();

		$this->do_form('add', $new_id);

	}

	//-----------------------------------------
	// REMOVE WRAPPERS
	//-----------------------------------------

	function remove()
	{
		//-----------------------------------------

		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		if ($this->ipsclass->input['id'] == 1)
		{
			$this->ipsclass->admin->error("Вы не можете удалить этот язык.");
		}

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID");
		}

		// Is it default??????????????? ok enuff

		if ($this->ipsclass->vars['default_language'] == "")
		{
			$this->ipsclass->vars['default_language'] = 'ru';
		}

		if ($row['ldir'] == $this->ipsclass->vars['default_language'])
		{
			$this->ipsclass->admin->error("Вы не можете удалить этот язык, пока он установлен, как язык по умолчанию. Выберите другой язык и установите его по умолчанию, после этого попробуйте еще раз");
		}

		$this->ipsclass->DB->do_update( 'members', array( 'language' => $this->ipsclass->vars['default_language'] ), "language='{$row['ldir']}'" );

		if ( $this->ipsclass->admin->rm_dir( CACHE_PATH.'cache/lang_cache/'.$row['ldir'] ) )
		{
			$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );

			$this->rebuild_cache();

			$this->ipsclass->boink_it($this->ipsclass->base_url."&{$this->ipsclass->form_code}");
			exit();
		}
		else
		{
			$this->ipsclass->admin->error("Невозможно удалить языковые модули, проверьте права доступа (CHMOD) к файлам языка");
		}
	}



	//-----------------------------------------
	// ADD / EDIT IMAGE SETS
	//-----------------------------------------

	function save_langfile()
	{
		//-----------------------------------------

		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		if ($this->ipsclass->input['lang_file'] == "")
		{
			$this->ipsclass->admin->error("Вы должны указать имя языкового модуля, вернитесь назад и попробуйте еще раз");
		}

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID");
		}

		$lang_file = CACHE_PATH."cache/lang_cache/".$row['ldir']."/".$this->ipsclass->input['lang_file'];

		if (! file_exists( $lang_file ) )
		{
			$this->ipsclass->admin->error("Невозможно найти файл '$lang_file', проверьте есть ли он.");
		}

		if (! is_writeable( $lang_file ) )
		{
			$this->ipsclass->admin->error("Невозможно записать в файл '$lang_file', проверьте права доступа (CHMOD) и, если необходимо установить права доступа 0777. IPB не может сделать это за вас");
		}

		$barney = array();

		foreach ($this->ipsclass->input as $k => $v)
		{
			if ( preg_match( "/^XX_(\S+)$/", $k, $match ) )
			{
				if ( isset($this->ipsclass->input[ $match[0] ]) )
				{
					$v = str_replace("&#39;", "'", stripslashes($_POST[ $match[0] ]) );
					$v = str_replace("&#60;", "<",  $v );
					$v = str_replace("&#62;", ">", $v );
					$v = str_replace("&#38;", "&", $v );
					$v = str_replace("\r", "", $v );

					$barney[ $match[1] ] = $v;
				}
			}
		}

		if ( count($barney) < 1 )
		{
			$this->ipsclass->admin->error("Что-то сделано не так, вернитесь назад и попробуйте еще раз");
		}

		$start = "<?php\n\n".'$lang = array('."\n";

		foreach($barney as $key => $text)
		{
			$text   = preg_replace("/\n{1,}$/", "", $text);
			$start .= "\n'".$key."'  => \"".str_replace( '"', '\"', $text)."\",";
		}

		$start .= "\n\n);\n\n?".">";

		if ($fh = fopen( $lang_file, 'w') )
		{
			fwrite($fh, $start );
			fclose($fh);
		}
		else
		{
			$this->ipsclass->admin->error("Нельзя сделать запись в $lang_file");
		}

		if ( $this->ipsclass->input['id'] )
		{
			$this->ipsclass->admin->done_screen("Язык обновлен", "Управление языками", "{$this->ipsclass->form_code}&code=edit&id={$this->ipsclass->input['id']}", 'redirect' );
		}
		else
		{
			$this->ipsclass->admin->done_screen("Язык обновлен", "Управление языками", "{$this->ipsclass->form_code}", 'redirect' );
		}
	}

	//-----------------------------------------
	// EDIT SPLASH
	//-----------------------------------------

	function do_form( $method='add', $id="" )
	{
		$author = "";

		//-----------------------------------------

		if ($id != "")
		{
			$this->ipsclass->input['id'] = $id;
		}

		//-----------------------------------------

		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно определить ID языка");
		}

		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'languages', 'where' => "lid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти язык по введенному ID");
		}

		//-----------------------------------------

		$lang_dir = CACHE_PATH."cache/lang_cache/".$row['ldir'];

		$form_array = array();

		if ($method != 'add')
		{
			if ( ! is_writeable($lang_dir) )
			{
				$this->ipsclass->admin->error("Невозможно записать в директорию '$lang_dir', проверьте права доступа (CHMOD) и, если необходимо установить права доступа 0777. IPB не может сделать это за вас.");
			}
		}

		//-----------------------------------------

		if ( is_dir($lang_dir) )
		{
			$handle = opendir($lang_dir);

			while (($filename = readdir($handle)) !== false)
			{
				if (($filename != ".") && ($filename != ".."))
				{
					if (preg_match("/^index/", $filename))
					{
						continue;
					}

					if (preg_match("/\.php$/", $filename))
					{
						$form_array[] = array( $filename, preg_replace( "/\.php$/", "", $filename ) );
					}
				}
			}

			closedir($handle);
		}

		if ($row['lauthor'] and $row['lemail'])
		{
			$author = "<br /><br />Язык «{$row['lname']}» был создан «<a href='mailto:{$row['lemail']}' target='_blank'>{$row['lauthor']}</a>».";
		}
		else if ($row['lauthor'])
		{
			$author = "<br /><br />Язык «{$row['lname']}» был создан «{$row['lauthor']}».";
		}

		//-----------------------------------------

		$this->ipsclass->admin->page_detail = "Эта секция позволяет вам изменять языковые модули выбранного языка.$author $url";
		$this->ipsclass->admin->page_title  = "Изменение языковых модулей";
		$this->ipsclass->admin->nav[] 		= array( '', "Изменение языка «{$row['lname']}»" );

		//-----------------------------------------

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'editinfo'    ),
																			 2 => array( 'act'   , 'lang'       ),
																			 3 => array( 'id'    , $this->ipsclass->input['id']     ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "40%" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Изменение информации о языке" );


		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<b>Название языка</b>",
													$this->ipsclass->adskin->form_input('lname', $row['lname']),
									     )      );

		if ($method == 'add')
		{

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
														"<b>Автор языка:</b>",
														$this->ipsclass->adskin->form_input('lauthor', $row['lauthor']),
											 )      );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
														"<b>E-mail автора:</b>",
														$this->ipsclass->adskin->form_input('lemail', $row['lemail']),
											 )      );

		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Изменить установки языка");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'edit2'    ),
																			 2 => array( 'act'   , 'lang'     ),
																			 3 => array( 'id'    , $this->ipsclass->input['id']   ),
																			 4 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "40%" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Редактирование языковых модулей языка '".$row['lname']."'" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<b>Выберете языковой файл для редактирования</b>",
													$this->ipsclass->adskin->form_dropdown('lang_file', $form_array),
									     )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Редактировать этот файл");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}

	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		if ($this->ipsclass->vars['default_language'] == "")
		{
			$this->ipsclass->vars['default_language'] = 'ru';
		}

		$form_array = array();

		$this->ipsclass->admin->page_detail = "В этой секции вы можете изменять, удалять существующие языки и добавлять новые";
		$this->ipsclass->admin->page_title  = "Управление языками";

		//-----------------------------------------

		$this->ipsclass->DB->cache_add_query( 'languages_list_current', array() );
		$this->ipsclass->DB->cache_exec_query();

		$used_ids = array();
		$show_array = array();

		$this->ipsclass->html .= ""; // removed js check delete

		if ( $this->ipsclass->DB->get_num_rows() )
		{

			$this->ipsclass->adskin->td_header[] = array( "Название"        , "40%" );
			$this->ipsclass->adskin->td_header[] = array( "Пользователей используют"      , "30%" );
			$this->ipsclass->adskin->td_header[] = array( "Экспорт"       , "10%" );
			$this->ipsclass->adskin->td_header[] = array( "Изменение"         , "10%" );
			$this->ipsclass->adskin->td_header[] = array( "Удаление"       , "10%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Используемые языки" );

			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$show_array[ $r['lid'] ] = isset($show_array[ $r['lid'] ]) ? $show_array[ $r['lid'] ] : '';

				if ($this->ipsclass->vars['default_language'] == $r['ldir'])
				{
					$root = "<span style='color:red;font-weight:bold'> (По умолчанию)</span>";
				}
				else
				{
					$root = " ( <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=makedefault&id=".urlencode($r['ldir'])."'>Сделать языком по умолчанию</a> )";
				}

				$show_array[ $r['lid'] ] .= stripslashes($r['lname'])."<br />";

				if ( in_array( $r['lid'], $used_ids ) )
				{
					continue;
				}

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>".stripslashes($r['lname'])."</b> $root",
														  "<center>{$r['mcount']}</center>",
														  "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=export&id={$r['lid']}'>Экспортировать</a></center>",
														  "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=edit&id={$r['lid']}'>Изменить</a></center>",
														  "<center><a href='javascript:checkdelete(\"{$this->ipsclass->form_code_js}&code=remove&id={$r['lid']}\")'>Удалить</a></center>",
												 )      );

				$used_ids[] = $r['lid'];

				$form_array[] = array( $r['lid'], $r['lname'] );

			}

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		if ( count($used_ids) < 1 )
		{
			$used_ids[] = '0';
		}

		$this->ipsclass->DB->simple_construct( array( 'select' => 'lid, ldir, lname', 'from' => 'languages', 'where' => "lid NOT IN(".implode(",",$used_ids).")" ) );
		$this->ipsclass->DB->simple_exec();

		if ( $this->ipsclass->DB->get_num_rows() )
		{

			$this->ipsclass->adskin->td_header[] = array( "Название"  , "40%" );
			$this->ipsclass->adskin->td_header[] = array( "Экспорт" , "10%" );
			$this->ipsclass->adskin->td_header[] = array( "Изменение"   , "30%" );
			$this->ipsclass->adskin->td_header[] = array( "Удаление" , "20%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Текущие неиспользуемые языки" );



			while ( $r = $this->ipsclass->DB->fetch_row() )
			{

				if ($this->ipsclass->vars['default_language'] == $r['ldir'])
				{
					$root = "<span style='color:red;font-weight:bold'> (используется по умолчанию)</span>";
				}
				else
				{
					$root = " ( <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=makedefault&id=".urlencode($r['ldir'])."'>Сделать языком по умолчанию</a> )";
				}

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>".stripslashes($r['lname'])."</b> $root",
														  "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=export&id={$r['lid']}'>Экспорт</a></center>",
														  "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=edit&id={$r['lid']}'>Изменить</a></center>",
														  "<center><a href='javascript:checkdelete(\"{$this->ipsclass->form_code_js}&code=remove&id={$r['lid']}\")'>Удалить</a></center>",
												 )      );

				$form_array[] = array( $r['lid'], $r['lname'] );

			}

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		//-----------------------------------------
		// Create new set?
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'add'     ),
												  				 2 => array( 'act'   , 'lang'    ),
												  				 4 => array( 'section', $this->ipsclass->section_code ),
									     				)      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Создание языка" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Создать новый язык, используя существующий...</b>" ,
										  		 			      $this->ipsclass->adskin->form_dropdown( "id", $form_array)
								 						)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Создать новый язык");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// Create new set?
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'swap'     ),
												  				 2 => array( 'act'   , 'lang'    ),
												  				 4 => array( 'section', $this->ipsclass->section_code ),
									     				)      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Переустановка языка у пользователей форума" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Пользователи использующие язык...</b>" ,
										  		 			      $this->ipsclass->adskin->form_dropdown( "old", array_merge( array( -1 => array( 'none', 'по умолчанию' ) ), $form_array ) )
								 						)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Изменить язык на...</b>" ,
										  		 			      $this->ipsclass->adskin->form_dropdown( "new", $form_array)
								 						)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Переустановить");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}


}


?>