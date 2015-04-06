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
|   > $Date: 2006-12-13 16:16:03 -0500 (Wed, 13 Dec 2006) $
|   > $Revision: 791 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > mySQL Admin Stuff
|   > Module written by Matt Mecham
|   > Date started: 21st October 2002
|
|	> Module Version Number: 1.0.0
|   > Music listen to when coding this: Martin Grech - Open Heart Zoo
|   > Talk about useless information!
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

@set_time_limit(1200);


class ad_sql_module {

	var $base_url;
	var $mysql_version   = "";
	var $true_version    = "";
	var $str_gzip_header = "\x1f\x8b\x08\x00\x00\x00\x00\x00";

	var $db_has_issues	 = false;

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "admin";

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "sql";

	/*-------------------------------------------------------------------------*/
	// Auto run module
	/*-------------------------------------------------------------------------*/

	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Инструменты' );

		//-----------------------------------------
		// Make sure we're a root admin, or else!
		//-----------------------------------------

		if ($this->ipsclass->member['mgroup'] != $this->ipsclass->vars['admin_group'])
		{
			$this->ipsclass->admin->error("Извините, но эти функции только для администраторской группы");
		}

		//-----------------------------------------
		// Get the mySQL version.
		//-----------------------------------------

		$this->ipsclass->DB->sql_get_version();

		$this->true_version  = $this->ipsclass->DB->true_version;
   		$this->mysql_version = $this->ipsclass->DB->mysql_version;

		switch($this->ipsclass->input['code'])
		{
			case 'dotool':
				$this->run_tool();
				break;

			case 'runtime':
				$this->view_sql("SHOW STATUS");
				break;

			case 'system':
				$this->view_sql("SHOW VARIABLES");
				break;

			case 'processes':
				$this->view_sql("SHOW PROCESSLIST");
				break;

			case 'runsql':
				$_POST['query'] = isset($_POST['query']) ? $_POST['query'] : '';
				$q = $_POST['query'] == "" ? urldecode($_GET['query']) : $_POST['query'];
				$this->view_sql(trim(stripslashes($q)));
				break;

			case 'backup':
				$this->show_backup_form();
				break;

			case 'safebackup':
				$this->sbup_splash();
				break;

			case 'dosafebackup':
				$this->do_safe_backup();
				break;

			case 'export_tbl':
				$this->do_safe_backup(trim(urldecode(stripslashes($_GET['tbl']))));
				break;

			//-----------------------------------------
			default:
				$this->list_index();
				break;
		}
	}

	//-----------------------------------------
	// Back up baby, back up
	//-----------------------------------------

	function do_safe_backup($tbl_name="")
	{
		if ($tbl_name == "")
		{
			// Auto all tables
			$skip        = intval($this->ipsclass->input['skip']);
			$create_tbl  = intval($this->ipsclass->input['create_tbl']);
			$enable_gzip = intval($this->ipsclass->input['enable_gzip']);
			$filename    = 'ibf_dbbackup';
		}
		else
		{
			// Man. click export
			$skip        = 0;
			$create_tbl  = 0;
			$enable_gzip = 1;
			$filename    = $tbl_name;
		}

		$output = "";

		@header("Pragma: no-cache");

		$do_gzip = 0;

		if( $enable_gzip )
		{
			$phpver = phpversion();

			if($phpver >= "4.0")
			{
				if(extension_loaded("zlib"))
				{
					$do_gzip = 1;
				}
			}
		}

		if( $do_gzip != 0 )
		{
			@ob_start();
			@ob_implicit_flush(0);
			header("Content-Type: text/x-delimtext; name=\"$filename.sql.gz\"");
			header("Content-disposition: attachment; filename=$filename.sql.gz");
		}
		else
		{
			header("Content-Type: text/x-delimtext; name=\"$filename.sql\"");
			header("Content-disposition: attachment; filename=$filename.sql");
		}

		//-----------------------------------------
		// Get tables to work on
		//-----------------------------------------

		if ($tbl_name == "")
		{
			$tmp_tbl = $this->ipsclass->DB->get_table_names();

			foreach($tmp_tbl as $tbl)
			{
				// Ensure that we're only peeking at IBF tables

				if ( preg_match( "/^".$this->ipsclass->vars['sql_tbl_prefix']."/", $tbl ) )
				{
					// We've started our headers, so print as we go to stop
					// poss memory problems

					$this->get_table_sql($tbl, $create_tbl, $skip);
				}
			}
		}
		else
		{
			$this->get_table_sql($tbl_name, $create_tbl, $skip);
		}

		//-----------------------------------------
		// GZIP?
		//-----------------------------------------

		if($do_gzip)
		{
			$size     = ob_get_length();
			$crc      = crc32(ob_get_contents());
			$contents = gzcompress(ob_get_contents());
			ob_end_clean();
			echo $this->str_gzip_header
				.substr($contents, 0, strlen($contents) - 4)
				.$this->gzip_four_chars($crc)
				.$this->gzip_four_chars($size);
		}

		exit();
	}

	//-----------------------------------------
	// Internal handler to return content from table
	//-----------------------------------------

	function get_table_sql($tbl, $create_tbl, $skip=0)
	{
		if ($create_tbl)
		{
			// Generate table structure

			if ( $this->ipsclass->input['addticks'] )
			{
				$this->ipsclass->DB->query("SHOW CREATE TABLE `".$this->ipsclass->vars['sql_database'].".".$tbl."`");
			}
			else
			{
				$this->ipsclass->DB->query("SHOW CREATE TABLE ".$this->ipsclass->vars['sql_database'].".".$tbl);
			}

			$ctable = $this->ipsclass->DB->fetch_row();

			echo $this->sql_strip_ticks($ctable['Создать таблицу']).";\n";
		}

		// Are we skipping? Woohoo, where's me rope?!

		if ($skip == 1)
		{
			if ($tbl == $this->ipsclass->vars['sql_tbl_prefix'].'admin_sessions'
				OR $tbl == $this->ipsclass->vars['sql_tbl_prefix'].'sessions'
				OR $tbl == $this->ipsclass->vars['sql_tbl_prefix'].'reg_anti_spam'
				OR $tbl == $this->ipsclass->vars['sql_tbl_prefix'].'search_results'
			   )
			{
				return $ret;
			}
		}

		// Get the data

		$this->ipsclass->DB->query("SELECT * FROM $tbl");

		// Check to make sure rows are in this
		// table, if not return.

		$row_count = $this->ipsclass->DB->get_num_rows();

		if ($row_count < 1)
		{
			return TRUE;
		}

		//-----------------------------------------
		// Get col names
		//-----------------------------------------

		$f_list = "";

		$fields = $this->ipsclass->DB->get_result_fields();

		$cnt = count($fields);

		for( $i = 0; $i < $cnt; $i++ )
		{
			$f_list .= $fields[$i]->name . ", ";
		}

		$f_list = preg_replace( "/, $/", "", $f_list );

		while ( $row = $this->ipsclass->DB->fetch_row() )
		{
			//-----------------------------------------
			// Get col data
			//-----------------------------------------

			$d_list = "";

			for( $i = 0; $i < $cnt; $i++ )
			{
				if ( ! isset($row[ $fields[$i]->name ]) )
				{
					$d_list .= "NULL,";
				}
				elseif ( $row[ $fields[$i]->name ] != '' )
				{
					$d_list .= "'".$this->sql_add_slashes($row[ $fields[$i]->name ]). "',";
				}
				else
				{
					$d_list .= "'',";
				}
			}

			$d_list = preg_replace( "/,$/", "", $d_list );

			echo "INSERT INTO $tbl ($f_list) VALUES($d_list);\n";
		}

		return TRUE;

	}

	//-----------------------------------------
	// sql_strip_ticks from field names
	//-----------------------------------------

	function sql_strip_ticks($data)
	{
		return str_replace( "`", "", $data );
	}

	//-----------------------------------------
	// Add slashes to single quotes to stop sql breaks
	//-----------------------------------------

	function sql_add_slashes($data)
	{
		$data = str_replace('\\', '\\\\', $data);
        $data = str_replace('\'', '\\\'', $data);
        $data = str_replace("\r", '\r'  , $data);
        $data = str_replace("\n", '\n'  , $data);

        return $data;
	}

	//-----------------------------------------
	// Almost there!
	//-----------------------------------------

	function sbup_splash()
	{
		$this->ipsclass->admin->page_detail = "В этой секции вы можете произвести резервную копию вашей базы данных.";
		$this->ipsclass->admin->nav[] = array( '', 'Резервное копирование MySQL' );
		$this->ipsclass->admin->page_title  = "mySQL ".$this->true_version." — резервное копирование";

		// Check for mySQL version..
		// Might change at some point..

		if ( $this->mysql_version < 3232 )
		{
			$this->ipsclass->admin->error("Извините, но версия mySQL ниже, чем 3.23.21 и Вы не сможете осуществить резервное копирование");
		}

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Простое копирование" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<b>Резервное копирование базы mySQL</b><br /><br />После того, как Вы нажмёте на ссылку ниже, необходимо будет подождать, пока Ваш браузер не загрузит следующую страницу. Время загрузки может затянуться на несколько минут, в зависимости от размера копируемой базы данных.
													<br /><br />
													<b><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=dosafebackup&create_tbl={$this->ipsclass->input['create_tbl']}&addticks={$this->ipsclass->input['addticks']}&skip={$this->ipsclass->input['skip']}&enable_gzip={$this->ipsclass->input['enable_gzip']}'>Нажмите сюда для начала резервного копирования</a></b>"
									     )      );


		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		$this->ipsclass->admin->output();


	}


	function show_backup_form()
	{
		$this->ipsclass->admin->nav[] = array( '', 'Резервное копирование данных' );

		$this->ipsclass->admin->page_detail = "В этой секции вы сможете сделать резервное копирование вашей базы данных.
							  <br /><br /><b>Простое копирование</b>
							  <br />Этот инструмент соберет все данные в один файл и предложит вам его скачать. Полезно, когда
                              у хостинг-провайдера включен безопасный режим PHP и база имеет не очень большой размер.
							  <!--<br /><br />
							  <b>Расширенное копирование</b>
							  <br />Эта функция позволит Вас разделить базу данных на несколько секций и сохранить на диске.
							  <br />Примечание: этим можно пользоваться только если в PHP не включен безопасный режим. -->";

		$this->ipsclass->admin->page_title  = "mySQL ".$this->true_version." — резервное копирование данных";

		// Check for mySQL version..
		// Might change at some point..

		if ( $this->mysql_version < 3232 )
		{
			$this->ipsclass->admin->error("Извините, но версия mySQL ниже, чем 3.23.21 и вы не сможете осуществить резервное копирование");
		}

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "40%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'sql' ),
																			 2 => array( 'code' , 'safebackup'),
																			 4 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Просто копирование" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<b>Добавить команду «CREATE TABLE»?</b><br />Добавить обратные кавычки в названиях таблиц?<br />(включите это, если у вас выскакивает ошибка mySQL) <input type='checkbox' name='addticks' value=1>",
													$this->ipsclass->adskin->form_yes_no( 'create_tbl', 1),
									     )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<b>Пропустить не существенные данные?</b><br />Такие данные, как: ibf_sessions, ibf_admin_sessions, ibf_search_results, ibf_reg_anti_spam — не будут добавлены.",
													$this->ipsclass->adskin->form_yes_no( 'skip', 1),
									     )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<b>Сжать в GZIP?</b><br />Если GZIP включён, то размер файла-копии будет сжат на более меньший размер.",
													$this->ipsclass->adskin->form_yes_no( 'enable_gzip', 0),
									     )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Начать");
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		$this->ipsclass->admin->output();
	}


	//-----------------------------------------
	// Run mySQL queries
	//-----------------------------------------


	function view_sql($sql)
	{
		$limit = 50;
		$start = intval($this->ipsclass->input['st']) >=0 ? intval($this->ipsclass->input['st']) : 0;
		$pages = "";

		$this->ipsclass->admin->page_detail = "В этой секции вы можете администратировать вашу базу данных.";
		$this->ipsclass->admin->page_title  = "mySQL ".$this->true_version." — администратирование";

		$map = array( 'processes' => "процессы",
					  'runtime'   => "информация SQL Runtime",
					  'system'    => "переменные",
					);

		if ( isset($map[ $this->ipsclass->input['code'] ]) AND $map[ $this->ipsclass->input['code'] ] != "" )
		{
			$tbl_title = $map[ $this->ipsclass->input['code'] ];
			$man_query = 0;
		}
		else
		{
			$tbl_title = "Самостоятельный запрос";
			$man_query = 1;
		}

		//-----------------------------------------

		if ($man_query == 1)
		{
			$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'sql' ),
											      2 => array( 'code' , 'runsql'),
											      4 => array( 'section', $this->ipsclass->section_code ),
										 )      );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Выполнение запроса" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<center>".$this->ipsclass->adskin->form_textarea("query", $sql )."</center>" ) );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Выполнить");
			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		//-----------------------------------------

		$this->ipsclass->DB->return_die = 1;

		$the_queries = array();

		if( strstr( $sql, ";" ) )
		{
			$the_queries = preg_split( "/;[\r\n|\n]+/", $sql, -1, PREG_SPLIT_NO_EMPTY );
		}
		else
		{
			$the_queries[] = $sql;
		}

		if( !count($the_queries) )
		{
			$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Ошибка SQL" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array("Запросы не найдены") );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

			$this->ipsclass->admin->output(); // End output and script
		}

		foreach( $the_queries as $sql )
		{
			$links 	= "";
			$sql 	= trim($sql);
			// Check for drop, create and flush

			$test_sql = str_replace( "\'", "", $sql );
			$apos_count = substr_count( $test_sql, "'" );

			if( $apos_count%2 != 0 )
			{
				$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

				$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Ошибка" );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array("Этот запрос недействителен: {$sql}") );

				$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

				unset( $apos_count, $test_sql );
				continue;
			}

			unset( $apos_count, $test_sql );

			if ( preg_match( "/^DROP|FLUSH/i",$sql ) )
			{
				$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

				$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Ошибка" );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array("Извините, эти запросы запрещены в целях безопасности.") );

				$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

				continue;
			}
			else if ( preg_match( "/^(DELETE|UPDATE)/i", preg_replace( "#\s{1,}#s", "", $sql ) ) and preg_match( "/admin_login_logs/i", preg_replace( "#\s{1,}#s", "", $sql ) ) )
			{
				$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

				$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Ошибка" );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array("Вам запрещено удалять или обновлять эту таблицу.") );

				$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

				continue;
			}

			$this->ipsclass->DB->error = "";
				
			$this->ipsclass->DB->allow_sub_select = 1;
			
			$this->ipsclass->DB->query($sql,1);

			// Check for errors..

			if ( $this->ipsclass->DB->error != "")
			{
				$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

				$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Ошибка SQL" );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array($this->ipsclass->DB->error) );

				$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

				continue;

			}

			if ( preg_match( "/^INSERT|UPDATE|DELETE|ALTER|TRUNCATE|CREATE|REPLACE INTO/i", $sql ) )
			{
				// We can't show any info, and if we're here, there isn't
				// an error, so we're good to go.

				$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

				$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Результат" );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array("Запрос: ".htmlspecialchars($sql)."<br />Успешно выполнен.") );

				$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

				continue;

			}
			else if ( preg_match( "/^SELECT/i", $sql ) )
			{
				// Sort out the pages and stuff
				// auto limit if need be

				if ( ! preg_match( "/LIMIT[ 0-9,]+$/i", $sql ) )
				{
					$rows_returned = $this->ipsclass->DB->get_num_rows();

					if ($rows_returned > $limit)
					{
						$links = $this->ipsclass->adskin->build_pagelinks( array( 'TOTAL_POSS'  => $rows_returned,
															   'PER_PAGE'    => $limit,
															   'CUR_ST_VAL'  => $start,
															   'L_SINGLE'    => "Одна страница",
															   'L_MULTI'     => "Страниц: ",
															   'BASE_URL'    => $this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=runsql&query=".urlencode($sql),
															 )
													  );

						if( substr( $sql, -1, 1 ) == ";" )
						{
							$sql = substr( $sql, 0, -1 );
						}

						$sql .= " LIMIT $start, $limit";

						// Re-run with limit

						$this->ipsclass->DB->query($sql, 1); /// bypass table swapping
					}
				}

			}

			$fields = $this->ipsclass->DB->get_result_fields();

			$cnt = count($fields);

			// Print the headers - we don't what or how many so...

			for( $i = 0; $i < $cnt; $i++ )
			{
				$this->ipsclass->adskin->td_header[] = array( $fields[$i]->name , "*" );
			}

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Результат: ".$tbl_title );

			if ($links != "")
			{
				$pages = $this->ipsclass->adskin->add_td_basic( $links, 'left', 'tablerow2' );

				$this->ipsclass->html .= $pages;
			}

			while( $r = $this->ipsclass->DB->fetch_row() )
			{
				// Grab the rows - we don't what or how many so...

				$rows = array();

				for( $i = 0; $i < $cnt; $i++ )
				{
					if ($man_query == 1)
					{
						// Limit output
						if ( strlen($r[ $fields[$i]->name ]) > 200 )
						{
							$r[ $fields[$i]->name ] = substr($r[ $fields[$i]->name ], 0, 200) .'...';
						}
					}

					$rows[] = wordwrap( htmlspecialchars(nl2br($r[ $fields[$i]->name ])) , 50, "<br />", 1 );
				}

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( $rows );

			}

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

			$this->ipsclass->DB->free_result();
		}

		//-----------------------------------------

		$this->ipsclass->admin->output();


	}

	//-----------------------------------------
	// I'm A TOOL!
	//-----------------------------------------

	function run_tool()
	{
		$this->ipsclass->admin->page_detail = "Эта секция позволит вам администрировать вашу базу данных.$extra";
		$this->ipsclass->admin->page_title  = "mySQL ".$this->true_version." — инструменты";

		//-----------------------------------------
		// have we got some there tables me laddo?
		//-----------------------------------------

		$tables = array();

 		foreach ($this->ipsclass->input as $key => $value)
 		{
 			if ( preg_match( "/^tbl_(\S+)$/", $key, $match ) )
 			{
 				if ($this->ipsclass->input[$match[0]])
 				{
 					$tables[] = $match[1];
 				}
 			}
 		}

 		if ( count($tables) < 1 )
 		{
 			$this->ipsclass->admin->error("Вы должны выбрать какую-нибудь таблицу, чтобы запустить этот запрос иначе ничего не выйдет");
 		}

 		//-----------------------------------------
		// What tool is one running?
		// optimize analyze check repair
		//-----------------------------------------

		if (strtoupper($this->ipsclass->input['tool']) == 'DROP' || strtoupper($this->ipsclass->input['tool']) == 'CREATE' || strtoupper($this->ipsclass->input['tool']) == 'FLUSH')
		{
			$this->ipsclass->admin->error("Вы не можете делать этого, извините");
		}

		foreach($tables as $table)
		{
			$this->ipsclass->DB->query(strtoupper($this->ipsclass->input['tool'])." TABLE $table");

			$fields = $this->ipsclass->DB->get_result_fields();

			$data = $this->ipsclass->DB->fetch_row();

			$cnt = count($fields);

			// Print the headers - we don't what or how many so...

			for( $i = 0; $i < $cnt; $i++ )
			{
				$this->ipsclass->adskin->td_header[] = array( $fields[$i]->name , "*" );
			}

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Результат: ".$this->ipsclass->input['tool']." ".$table );

			// Grab the rows - we don't what or how many so...

			$rows = array();

			for( $i = 0; $i < $cnt; $i++ )
			{
				$rows[] = $data[ $fields[$i]->name ];
			}

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( $rows );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		//-----------------------------------------

		$this->ipsclass->admin->output();


	}


	function db_index_diag()
	{
		//-----------------------------------------
		// Fixing something?
		//-----------------------------------------

		$queries_to_run = array();

		foreach( $this->ipsclass->input as $k => $v )
		{
			if( preg_match( "/^query(\d+)$/", $k, $matches ) )
			{
				$queries_to_run[] = $v;
			}
		}

		if( isset($this->ipsclass->input['query']) AND $this->ipsclass->input['query'] )
		{
			$queries_to_run[] = $this->ipsclass->input['query'];
		}

		if( count($queries_to_run) > 0 )
		{
			foreach( $queries_to_run as $the_query )
			{
				$sql = trim( urldecode( base64_decode($the_query) ) );

				if ( preg_match( "/^DROP|FLUSH/i", trim($sql) ) )
				{
					$this->ipsclass->main_msg = "Эти запросы запрещены в целях безопасности.";
				}
				else
				{
					$this->ipsclass->DB->return_die = 1;

					$this->ipsclass->DB->query($sql,1);

					if( $this->ipsclass->DB->error != "" )
					{
						$this->ipsclass->main_msg .= "<span style='color:red;'>Ошибка SQL</span><br />{$this->ipsclass->DB->error}<br />";
					}
					else
					{
						$this->ipsclass->main_msg .= "Запрос: ".htmlspecialchars($sql)."<br />Успешно выполнен.<br />";
					}

					$this->ipsclass->DB->error  = "";
					$this->ipsclass->DB->failed = 0;
				}
			}
		}

		require ( ROOT_PATH.'/install/sql/mysql_tables.php' );

		$indexes = array();

		if( is_array($TABLE) && count($TABLE) )
		{
			foreach( $TABLE as $definition )
			{
				$table_name  	= "";
				$fields_str  	= "";
				$primary_key 	= "";
				$tablename   	= array();
				$fields		 	= array();
				$final_keys  	= array();
				$col_definition = "";
				$colmatch	 	= array();
				$final_primary 	= array();

		        preg_match( "#CREATE TABLE\s+?(.+?)\s+?\(#ie", $definition, $tablename );
		        $table_name = $tablename[1];

		        if ( preg_match( "#\s+?PRIMARY\s+?KEY\s+?\((.*?)\)(?:(?:[,\s+?$])?\((.+?)\))?#is", $definition, $fields ) )
		        {
			        if( count( $fields ) )
			        {
				        $primary_key = trim($fields[1]);

				        $col_definition = $this->sql_strip_ticks( $definition );

				        preg_match( "#^\s+?{$primary_key}\s+?(.+?)(?:[,$])#im", $col_definition, $colmatch );

				        $col_definition = trim($colmatch[1]);

				        $final_primary = array( $primary_key, $col_definition );
            		}
		        }

		        if ( preg_match_all( "#(?<!PRIMARY)\s+?KEY\s+?(?:(\w+?)\s+?)?\((.*?)\)(?:(?:[,\s+?$])?\((.+?)\))?#is", $definition, $fields, PREG_PATTERN_ORDER ) )
		        {
			        if( count( $fields[2] ) )
			        {
				        $i = 0;

				        foreach( $fields[2] as $index_cols )
				        {
		            		$index_cols = trim( $this->sql_strip_ticks( $index_cols ) );

		            		$index_name = $fields[1][$i] ? $fields[1][$i] : $index_cols;

		            		$final_keys[] = array( $index_name, implode( ",", array_map( 'trim', explode( ",", $index_cols ) ) ) );

		            		$i++;
	            		}
            		}
		        }



			    if( $table_name AND ( $primary_key OR count($final_keys) ) )
			    {
				    $indexes[] = array( 'table' 	=> $table_name,
				    					'primary'	=> $final_primary,
				    					'index'		=> $final_keys
				    				  );
			    }
		    }
	    }

	    //echo "<pre>";print_r($indexes);echo "</pre>";exit;

	    if( !count($indexes) )
	    {
		   $this->ipsclass->admin->error( "Невозможно обработать файл «mysql_tables.php»." );
		}

		$output = array();

		$table_html_count = 0;
		$this->db_has_issues		= false;

		foreach( $indexes as $data )
		{
			$table_name = str_replace( "ibf_", "", $data['table']);

			$row = $this->ipsclass->DB->sql_get_table_schematic( $table_name );

			$tbl = $this->sql_strip_ticks( $row['Create Table'] );

			if( is_array($data['primary']) AND count($data['primary']) )
			{
				$index_name 		= $data['primary'][0];
				$column_definition	= $data['primary'][1];

				if ( preg_match( "#\s+?PRIMARY\s+?KEY\s+?\({$index_name}\)?[,\s+?$]?#is", $tbl, $match ) )
				{
					$ok = 1;
				}
				else
				{
					$query_needed = "ALTER TABLE ".SQL_PREFIX."$table_name CHANGE {$index_name} {$index_name} {$column_definition}, ADD PRIMARY KEY ({$index_name})";

					if( !$this->ipsclass->DB->field_exists( $index_name, $table_name ) )
					{
						$query_needed = str_replace( "CHANGE {$index_name}", "ADD", $query_needed );
					}

					$popup_div = "<div style='border: 2px outset rgb(85, 85, 85); padding: 4px; background: rgb(238, 238, 238) none repeat scroll 0%; position: absolute; width: auto; display: none; text-align: center; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;' id='{$index_name}' align='center'>{$query_needed}</div>";

					$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:red'>".SQL_PREFIX."{$table_name}</span>",
																			"<span style='color:red'>{$index_name}</span>",
																			"<center><script type='text/javascript'>all_queries[{$table_html_count}] = '".urlencode(base64_encode($query_needed))."';</script><a href='{$this->ipsclass->base_url}&amp;section=help&amp;act=diag&amp;code=dbindex&amp;query=".urlencode(base64_encode($query_needed))."'><b>Исправить автоматически</b></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href'#' onclick=\"toggleview('{$index_name}');return false;\" style='cursor: pointer;'><b>Исправить в ручную</b></a><br />{$popup_div}</center>"
																   ) 	  );
					$table_html_count++;
					$this->db_has_issues		= true;
				}

				if ( $ok )
				{
					$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:green'>".SQL_PREFIX."{$table_name}</span>",
																			"<span style='color:green'>{$index_name}</span>",
																			"&nbsp;"
																   ) 	  );
				}
			}

			if ( is_array( $data['index'] ) and count( $data['index'] ) )
			{
				foreach( $data['index'] as $indexes )
				{
					$index_name = $indexes[0];
					$index_cols = $indexes[1] ? $indexes[1] : $index_name;
					$ok         = 0;

					//print $index_name; print "<br />".$index_cols;print "<br />".$tbl;

					if ( preg_match( "#(?<!PRIMARY)\s+?KEY\s+?{$index_name}\s+?(\((.+?)\))?#is", $tbl, $match ) )
					{
						$ok = 1;

						//-----------------------------------------
						// Multi index column?
						//-----------------------------------------

						if ( $index_cols != $index_name )
						{
							foreach( explode( ',', $indexes[1] ) as $mc )
							{
								if ( ! strstr( $match[1], $mc ) )
								{
									$popup_div = "<div style='border: 2px outset rgb(85, 85, 85); padding: 4px; background: rgb(238, 238, 238) none repeat scroll 0%; position: absolute; width: auto; display: none; text-align: center; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;' id='{$index_name}' align='center'>ALTER TABLE ".SQL_PREFIX."{$table_name} DROP INDEX {$index_name}, ADD INDEX {$index_name} ({$index_cols});</div>";

									$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:red'>".SQL_PREFIX."{$table_name}</span>",
																							"<span style='color:red'>{$index_name}<br />Missing field '{$mc}'</span>",
																							"<center><script type='text/javascript'>all_queries[{$table_html_count}] = '".urlencode(base64_encode("ALTER TABLE ".SQL_PREFIX."{$table_name} DROP INDEX {$index_name}, ADD INDEX {$index_name} ({$index_cols})"))."';</script><a href='{$this->ipsclass->base_url}&amp;section=help&amp;act=diag&amp;code=dbindex&amp;query=".urlencode(base64_encode("ALTER TABLE ".SQL_PREFIX."{$table_name} DROP INDEX {$index_name}, ADD INDEX {$index_name} ({$index_cols})"))."'><b>Исправить автоматически</b></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href'#' onclick=\"toggleview('{$index_name}');return false;\" style='cursor: pointer;'><b>Исправить в ручную</b></a><br />{$popup_div}</center>"
																				   ) 	  );

									$ok       = 0;
									$table_html_count++;
									$this->db_has_issues		= true;
								}
							}
						}
					}
					else
					{
						$index_columns = $indexes[1] ? $indexes[1] : $index_name;

						$popup_div = "<div style='border: 2px outset rgb(85, 85, 85); padding: 4px; background: rgb(238, 238, 238) none repeat scroll 0%; position: absolute; width: auto; display: none; text-align: center; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;' id='{$index_name}' align='center'>ALTER TABLE ".SQL_PREFIX."$table_name ADD INDEX {$index_name}({$index_columns});</div>";

						$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:red'>".SQL_PREFIX."{$table_name}</span>",
																				"<span style='color:red'>{$index_name}</span>",
																				"<center><script type='text/javascript'>all_queries[{$table_html_count}] = '".urlencode(base64_encode("ALTER TABLE ".SQL_PREFIX."$table_name ADD INDEX {$index_name}({$index_columns})"))."';</script><a href='{$this->ipsclass->base_url}&amp;section=help&amp;act=diag&amp;code=dbindex&amp;query=".urlencode(base64_encode("ALTER TABLE ".SQL_PREFIX."$table_name ADD INDEX {$index_name}({$index_columns})"))."'><b>Исправить автоматически</b></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href'#' onclick=\"toggleview('{$index_name}');return false;\" style='cursor: pointer;'><b>Исправить в ручную</b></a><br />{$popup_div}</center>"
																	   ) 	  );
						$table_html_count++;
						$this->db_has_issues		= true;
					}

					if ( $ok )
					{
						$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:green'>".SQL_PREFIX."{$table_name}</span>",
																				"<span style='color:green'>{$index_name}</span>",
																				"&nbsp;"
																	   ) 	  );
					}
				}
			}
		}

		return $output;
	}


	function db_table_diag( $print = 1 )
	{
		$good_img = "<img src='{$this->ipsclass->skin_acp_url}/images/aff_tick.png' border='0' alt='YN' class='ipd' />";
		$bad_img  = "<img src='{$this->ipsclass->skin_acp_url}/images/aff_cross.png' border='0' alt='YN' class='ipd' />";

		$separator = $print ? "|" : "<br />";

		//-----------------------------------------
		// Tool based on code by Stewart - thx :D
		//-----------------------------------------

		//-----------------------------------------
		// Fixing something?
		//-----------------------------------------

		$queries_to_run = array();

		foreach( $this->ipsclass->input as $k => $v )
		{
			if( preg_match( "/^query(\d+)$/", $k, $matches ) )
			{
				$queries_to_run[] = $v;
			}
		}

		if( isset($this->ipsclass->input['query']) AND $this->ipsclass->input['query'] )
		{
			$queries_to_run[] = $this->ipsclass->input['query'];
		}

		if( count($queries_to_run) > 0 )
		{
			foreach( $queries_to_run as $the_query )
			{
				$sql = trim( urldecode( base64_decode($the_query) ) );

				if ( preg_match( "/^DROP|FLUSH/i", trim($sql) ) )
				{
					$this->ipsclass->main_msg = "Sorry, those queries are not allowed for your safety";
				}
				else
				{
					$this->ipsclass->DB->return_die = 1;

					$this->ipsclass->DB->query($sql,1);

					if( $this->ipsclass->DB->error != "" )
					{
						$this->ipsclass->main_msg .= "<span style='color:red;'>SQL Error</span><br />{$this->ipsclass->DB->error}<br />";
					}
					else
					{
						$this->ipsclass->main_msg .= "Query: ".htmlspecialchars($sql)."<br />Executed Successfully<br />";
					}

					$this->ipsclass->DB->error  = "";
					$this->ipsclass->DB->failed = 0;
				}
			}
		}

		require ( ROOT_PATH.'/install/sql/mysql_tables.php' );

		$this->db_has_issues 	= false;
		$queries_needed 		= array();
		$tables_needed 			= array();

		if( is_array($TABLE) && count($TABLE) )
		{
			$table_html_count = 0;

			foreach( $TABLE as $the_table )
			{
				$expected_columns = array();

				if( preg_match("#CREATE TABLE\s+?(.+?)\s+?\(#ie", $the_table, $bits))
				{
					$tbl_name = $bits[1];
					$tbl_name = str_replace( "ibf_", "", $tbl_name );

					$table_defs[$tbl_name] = str_replace( $bits[1], SQL_PREFIX . $tbl_name, $the_table );

					// Get the columns and lose the first line (it's the table name)
					$columns_array = explode( "\n", $the_table );
					array_shift($columns_array);

					// Get rid of the end junk
					if ( (strpos(end($columns_array), ");") == 0) ||
						 (strpos(end($columns_array), ")") == 0)  ||
						 (strpos(end($columns_array), ";") == 0) )
					{
						array_pop($columns_array);
					}

					reset($columns_array);

					foreach( $columns_array as $col )
					{
						$temp = preg_split( "/[\s]+/" , $col );
						$col_name = trim( next( $temp ) );

						if( $col_name != "PRIMARY" &&
							$col_name != "KEY" &&
							$col_name != "UNIQUE" &&
							$col_name != "" &&
							$col_name != "(" &&
							$col_name != ";" &&
							$col_name != ");" )
						{
							$expected_columns[] = $col_name;
							$this->columns_to_defs[$tbl_name][$col_name] = trim( str_replace( ',', ';', $col ) );
						}
					}
				}
				else if ( preg_match("#ALTER TABLE ([a-z_]*) ADD ([a-z_]*) #is", $the_table, $bits) )
				{
					if( $bits[1] != "" &&
						$bits[2] != "" &&
						$bits[2] != 'INDEX' &&
						strpos($bits[2], 'TYPE') === false )
					{
						$tbl_name = trim($bits[1]);
						$tbl_name = str_replace( "ibf_", "", $tbl_name );
						$col_name = trim($bits[2]);

						$expected_columns[] = $col_name;
						$this->columns_to_defs[$tbl_name][$col_name] = str_replace( $bits[1], SQL_PREFIX . $tbl_name, $the_table ) . ";";
					}
				}
				else
				{
					continue;
				}

				// Get the current schema....
				$this->ipsclass->DB->return_die = 1;

				if( !$this->ipsclass->DB->table_exists( $tbl_name ) )
				{
					$popup_div = "<div style='border: 2px outset rgb(85, 85, 85); padding: 4px; background: rgb(238, 238, 238) none repeat scroll 0%; position: absolute; width: auto; display: none; text-align: center; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;' id='{$tbl_name}' align='center'>{$table_defs[$tbl_name]}</div>";

					$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:red'>" . SQL_PREFIX . $tbl_name . "</span>",
																			"<center>{$bad_img}</center>",
																			"<center><script type='text/javascript'>all_queries[{$table_html_count}] = '".urlencode(base64_encode($table_defs[$tbl_name]))."';</script><a href='{$this->ipsclass->base_url}&amp;section=help&amp;act=diag&amp;code=dbchecker&amp;query=".urlencode(base64_encode($table_defs[$tbl_name]))."'><b>Исправить автоматически</b></a>&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;<a href='#' onclick=\"toggleview('{$tbl_name}');return false;\" style='cursor: pointer;'><b>Исправить в ручную</b></a><br />{$popup_div}</center>"
																   ) 	  );
					$this->ipsclass->DB->failed = 0;
					$this->db_has_issues		= true;

					$table_html_count++;
				}
				else
				{
					// Here we go...
					$missing 		= array();

					foreach( $expected_columns as $trymeout )
					{
						if( ! $this->ipsclass->DB->field_exists( $trymeout, $tbl_name ) )
						{
							$this->db_has_issues 	= true;
							$missing[] 				= $trymeout;
							$query_needed 			= "ALTER TABLE " . SQL_PREFIX . $tbl_name . " ADD " . $this->columns_to_defs[$tbl_name][$trymeout];

							if( preg_match( "/auto_increment;/", $query_needed ) )
							{
								$query_needed = substr( $query_needed, 0, -1 ).", ADD PRIMARY KEY( ". $trymeout . ");";
							}

							$popup_div = "<div style='border: 2px outset rgb(85, 85, 85); padding: 4px; background: rgb(238, 238, 238) none repeat scroll 0%; position: absolute; width: auto; display: none; text-align: center; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;' id='{$tbl_name}{$trymeout}' align='center'>{$query_needed}</div>";

							$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:red'>" . SQL_PREFIX . $tbl_name . " (missing column {$trymeout})</span>",
																					"<center>{$bad_img}</center>",
																					"<center><script type='text/javascript'>all_queries[{$table_html_count}] = '".urlencode(base64_encode($query_needed))."';</script><a href='{$this->ipsclass->base_url}&amp;section=help&amp;act=diag&amp;code=dbchecker&amp;query=".urlencode(base64_encode($query_needed))."'><b>Исправить автоматически</b></a>&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;<a href='#' onclick=\"toggleview('{$tbl_name}{$trymeout}');return false;\" style='cursor: pointer;'><b>Исправить в ручную</b></a><br />{$popup_div}</center>"
																		   ) 	  );
							$table_html_count++;
						}
					}

					if( !count( $missing ) )
					{
						$output[] = $this->ipsclass->adskin->add_td_row( array( "<span style='color:green'>" . SQL_PREFIX . $tbl_name . "</span>",
																				"<center>{$good_img}</center>",
																				"&nbsp;"
																	   ) 	  );
					}
				}
			}
		}

		return $output;
	}


	//-----------------------------------------
	// SHOW ALL TABLES AND STUFF!
	// 5 hours ago this seemed like a damned good idea.
	//-----------------------------------------

	function list_index()
	{
		$form_array = array();
		$extra 		= "";

		if ( $this->mysql_version < 3232 )
		{
			$extra = "<br /><b>Примечание: версия вашей mySQL ограниченная и возможно некоторые функции были удалены</b>";
		}

		$this->ipsclass->admin->page_detail = "Эта секция позволит вам администрировать вашу базу данных.$extra";
		$this->ipsclass->admin->page_title  = "SQL ".$this->true_version." — инструменты";

		//-----------------------------------------
		// Show advanced stuff for mySQL > 3.23.03
		//-----------------------------------------

		$idx_size = 0;
		$tbl_size = 0;


		$this->ipsclass->html .= "
				     <script language='Javascript'>
                     <!--
                     function CheckAll(cb) {
                         var fmobj = document.theForm;
                         for (var i=0;i<fmobj.elements.length;i++) {
                             var e = fmobj.elements[i];
                             if ((e.name != 'allbox') && (e.type=='checkbox') && (!e.disabled)) {
                                 e.checked = fmobj.allbox.checked;
                             }
                         }
                     }
                     function CheckCheckAll(cb) {
                         var fmobj = document.theForm;
                         var TotalBoxes = 0;
                         var TotalOn = 0;
                         for (var i=0;i<fmobj.elements.length;i++) {
                             var e = fmobj.elements[i];
                             if ((e.name != 'allbox') && (e.type=='checkbox')) {
                                 TotalBoxes++;
                                 if (e.checked) {
                                     TotalOn++;
                                 }
                             }
                         }
                         if (TotalBoxes==TotalOn) {fmobj.allbox.checked=true;}
                         else {fmobj.allbox.checked=false;}
                     }
                     //-->
                     </script>
                     ";

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'sql' ),
																			 2 => array( 'code' , 'dotool'),
																			 4 => array( 'section', $this->ipsclass->section_code ),
																	) , "theForm"     );

		if ( $this->mysql_version >= 3230 )
		{

			$this->ipsclass->adskin->td_header[] = array( "Таблица"      , "20%" );
			$this->ipsclass->adskin->td_header[] = array( "Записей"       , "10%" );
			$this->ipsclass->adskin->td_header[] = array( "Экспорт"     , "10%" );
			$this->ipsclass->adskin->td_header[] = array( '<input name="allbox" type="checkbox" value="Выбрать все" onClick="CheckAll();">'     , "10%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Таблицы Invision Power Board" );

			$this->ipsclass->DB->query("SHOW TABLE STATUS FROM `".$this->ipsclass->vars['sql_database']."`");

			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				// Check to ensure it's a table for this install...

				if ( ! preg_match( "/^".$this->ipsclass->vars['sql_tbl_prefix']."/", $r['Name'] ) )
				{
					continue;
				}

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b><span style='font-size:12px'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=runsql&query=".urlencode("SELECT * FROM {$r['Name']}")."'>{$r['Name']}</a></span></b>",
														  "<center>{$r['Rows']}</center>",
														  "<center><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=export_tbl&tbl={$r['Name']}'>Экспорт</a></center></b>",
														  "<center><input name=\"tbl_{$r['Name']}\" value=1 type='checkbox' onClick=\"CheckCheckAll();\"></center>",
												 )      );
			}
		}
		else
		{
			// display a basic information table

			$this->ipsclass->adskin->td_header[] = array( "Таблица"      , "60%" );
			$this->ipsclass->adskin->td_header[] = array( "Ячейки"       , "30%" );
			$this->ipsclass->adskin->td_header[] = array( '<input name="allbox" type="checkbox" value="Выбрать все" onClick="CheckAll();">'     , "10%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Таблицы Invision Power Board" );

			$tables = $this->ipsclass->DB->get_table_names();

			foreach($tables as $tbl)
			{
				// Ensure that we're only peeking at IBF tables

				if ( ! preg_match( "/^".$this->ipsclass->vars['sql_tbl_prefix']."/", $tbl ) )
				{
					continue;
				}

				$this->ipsclass->DB->query("SELECT COUNT(*) AS Rows FROM $tbl");

				$cnt = $this->ipsclass->DB->fetch_row();

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b><span style='font-size:12px'>$tbl</span></b>",
														  "<center>{$cnt['Rows']}</center>",
														  "<center><input name='tbl_$tbl' type='checkbox' onClick=\"CheckCheckAll(this);\"></center>",
												 )      );

			}

		}

		//-----------------------------------------
		// Add in the bottom stuff
		//-----------------------------------------

		if ( $this->mysql_version < 3232 )
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "<select id='button' name='tool'>
													<option value='optimize'>Оптимизация</option>
												  </select>
												 <input type='submit' value='Выполнить' class='realbutton'></form>", "center", "tablerow2" );
		}
		else
		{

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "<select id='button' name='tool'>
													<option value='optimize'>Оптимизация</option>
													<option value='repair'>Восстановление</option>
													<option value='check'>Проверка</option>
													<option value='analyze'>Анализ</option>
												  </select>
												 <input type='submit' value='Выполнить' class='realbutton'></form>", "center", "tablerow2" );
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'act'  , 'sql' ),
											      2 => array( 'code' , 'runsql'),
											      4 => array( 'section', $this->ipsclass->section_code ),
										 )      );

		$this->ipsclass->adskin->td_header[] = array( "{none}"      , "30%" );
		$this->ipsclass->adskin->td_header[] = array( "{none}"      , "70%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Выполнение запроса к базе данных" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>SQL запрос</b><div class='desctext'>Только для опытных пользователей.</div>",
												  $this->ipsclass->adskin->form_textarea("query", "" ),
												 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Выполнить");
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------

		$this->ipsclass->admin->output();

	}

    function gzip_four_chars($val)
	{
		for ($i = 0; $i < 4; $i ++)
		{
			$return .= chr($val % 256);
			$val     = floor($val / 256);
		}

		return $return;
	}
}


?>