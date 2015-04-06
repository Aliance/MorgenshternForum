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
+----------------------------------------------------------------------------
|   Action controller for install page
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Action controller for install page
 */

class action_install
{
	var $install;
	var $extra_install;

	var $helpfiles	= 0;

	function action_install( & $install )
	{
		$this->install =& $install;
	}

	// SQL > FINAL > SETTINGS > ACPPERMS / HELP FILE > DB CHECKER
	// After all sets have run: SKIN REVERT > TEMPLATES > OTHER [ Email Templates? ] > Build Caches > DB Check

	function run()
	{
		$this->install->get_version_latest();

		$this->install->saved_data['helpfile'] 	= intval($this->install->ipsclass->input['helpfile']) 	? intval($this->install->ipsclass->input['helpfile']) 	: $this->install->saved_data['helpfile'];
		$this->install->saved_data['man'] 		= intval($this->install->ipsclass->input['man']) 		? intval($this->install->ipsclass->input['man']) 		: $this->install->saved_data['man'];

		if ( isset($this->install->saved_data['mysql_codepage']) AND !isset($this->install->ipsclass->vars['mysql_codepage']) ) {
			$this->install->ipsclass->DB->obj['mysql_codepage'] = $this->install->saved_data['mysql_codepage'];
		}

		/* Switch */
		switch( $this->install->ipsclass->input['sub'] )
		{
			case 'sql':
				$this->install_sql();
			break;

			case 'settings':
				$this->install_settings();
			break;

			case 'acpperms':
				$this->install_acpperms();
			break;

			case 'finish':
				$this->install_finish();
			break;

			case 'checkdb':
				$this->install_checkdb();
			break;

			case 'skinrevert':
				$this->install_skinrevert();
			break;

			case 'templates':
				$this->install_templates();
			break;

			case 'caches':
				$this->install_caches();
			break;

			default:
				/* Output */

				$count = $this->install->ipsclass->DB->build_and_exec_query( array( 'select' => 'count(*) as num', 'from' => 'posts' ) );

				if( $count['num'] > 100000 )
				{
					$do_manual = 1;
				}
				else
				{
					$do_manual = 0;
				}

				$this->install->template->append( $this->install->template->install_page( $do_manual ) );
				$this->install->template->next_action = '?p=install&sub=sql';
				$this->install->template->hide_next   = 1;
			break;
		}

		//----------------------------------------------
		// Log errors for tech support
		//----------------------------------------------

		if( count($this->install->error) > 0 )
		{
			$file_name = ROOT_PATH . 'cache/sql_upgrade_log_'.date('m_d_y').'.cgi';

			$_error_string  = "\n===================================================";
			$_error_string .= "\n Date: ". date( 'r' );
			$_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
			$_error_string .= "\n Member ID: " . $this->install->ipsclass->member['id'];
			$_error_string .= "\n Version Folder: " .$this->install->current_upgrade;
			$_error_string .= "\n Current Sub Step: " .$this->install->ipsclass->input['sub'];
			$_error_string .= "\n Current workact: " .$this->install->saved_data['workact'];
			$_error_string .= "\n\n\n ".$this->install->ipsclass->my_br2nl(implode( "\n", $this->install->error ));

			$fh = fopen( $file_name, "a" );
			fwrite( $fh, $_error_string, strlen( $_error_string ) );
			fclose( $fh );
		}
	}

	/*-------------------------------------------------------------------------*/
	// Installs the SQL
	/*-------------------------------------------------------------------------*/
	/**
	* Installs SQL schematic
	*
	* @return void
	*/
	function install_sql()
	{
		if ( $this->install->saved_data['mysql_codepage'] AND !isset($this->install->ipsclass->vars['mysql_codepage']) )
		{
			$output = $this->install->write_codepage_configuration();
		}

		if ( $this->install->current_upgrade AND $this->install->current_upgrade < 20000 )
		{
			// Jump right to the finish routine for 1.x

			$this->install_finish();
			return;
		}

		$output					= "";
		$message 				= array();
		$this->install->error   = array();

		$SQL = array();
		$cnt = 0;

		if ( file_exists( INS_ROOT_PATH.'installfiles/upg_'.$this->install->current_upgrade.'/'.strtolower($this->install->ipsclass->vars['sql_driver']).'_updates.php' ) )
		{
			require_once ( INS_ROOT_PATH.'installfiles/upg_'.$this->install->current_upgrade.'/'.strtolower($this->install->ipsclass->vars['sql_driver']).'_updates.php' );
		}

		if ( file_exists( INS_ROOT_PATH.'sql/'.$this->install->ipsclass->vars['sql_driver'].'_install.php' ) )
		{
			require_once( INS_ROOT_PATH.'sql/'.$this->install->ipsclass->vars['sql_driver'].'_install.php' );

			$extra_install           =  new install_extra();
			$extra_install->ipsclass =& $this->install->ipsclass;
		}

		// Create/Alter tables
		if ( count( $SQL ) > 0 )
		{
			$this->sqlcount = 0;

			$this->install->ipsclass->DB->return_die = 1;

			foreach( $SQL as $q )
			{
				$this->install->ipsclass->DB->allow_sub_select 	= 1;
				$this->install->ipsclass->DB->error				= '';

				$q = str_replace( "<%time%>", time(), $q );

				if ( preg_match("/CREATE TABLE (\S+) \(/", $q) )
				{
					//-----------------------------------
					// Pass to handler
					//-----------------------------------

					if ( $extra_install AND method_exists( $extra_install, 'process_query_create' ) )
					{
						$q = $extra_install->process_query_create( $q );
					}
				}

				if( $this->install->saved_data['man'] )
				{
					$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $q ) )."\n\n";
				}
				else
				{
					$this->install->ipsclass->DB->query( $q );

					if ( $this->install->ipsclass->DB->error )
					{
						$this->install->error[] = $q."<br /><br />".$this->install->ipsclass->DB->error;
					}
					else
					{
						$this->sqlcount++;
					}
				}
			}

			$message[] = "Выполнено $this->sqlcount запросов...";
		}
		else
		{
			// If there are no SQL queries to run, jump to settings

			$this->install_finish();
			return;
		}


		if ( count( $this->install->error ) > 0 )
		{
			$this->install->message = count($message) ? implode( "<br />", $message ) : "Продолжаем обновление";

			$this->install->template->warning( array_merge( array( $this->install->message ),
															array( 'Проблемы при обновлении до '.$this->install->versions[ $this->install->current_upgrade ]. ' (' . $this->install->current_upgrade . ')' ),
															array( "<span style='color:red'>Ошибок: ".count($this->install->error).'</span>' ),
															$this->install->error ) );
			$this->install->template->in_error   = 1;

			$this->install->template->next_action = '?p=install&sub=finish';

			return;
		}

		if( $this->install->saved_data['man'] AND $output )
		{
			$output = "<h3><b>Для продолжения обновления необходимо выполнить следующие MySQL запросы:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output)).'</div>';

			$this->install->template->next_action = '?p=install&sub=finish';
			$this->install->template->append( $output );
		}
		else
		{
			$output[] = count($message) ? implode( "<br />", $message ) : "Нет запросов, настроек для импортирования...<br /><br />Продолжаем обновление";
			$this->install->template->next_action = '?p=install&sub=finish';
			$this->install->template->append( $this->install->template->install_page_refresh( $output ) );
			$this->install->template->hide_next = 1;
		}
	}

	/*-------------------------------------------------------------------------*/
	// Installs the Settings
	/*-------------------------------------------------------------------------*/
	/**
	* Installs Partial Settings
	*
	* @return void
	*/
	function install_settings()
	{
		if ( $this->install->current_upgrade AND $this->install->current_upgrade < 20000 )
		{
			// Jump right to the finish routine for 1.x
			// Reorganized - should never hit here

			$this->install_finish();
			return;
		}

		$message 				= array();
		$this->install->error   = array();

		//-------------------------------
		// Load module...
		//-------------------------------

		require_once( ROOT_PATH . 'sources/action_admin/settings.php' );
		$settings           =  new ad_settings();
		$settings->ipsclass =& $this->install->ipsclass;

		//-------------------------------
		// Set location
		//-------------------------------

		$this->install->ipsclass->input['file_location'] = 'resources/settings.xml';

		//-------------------------------
		// Run it
		//-------------------------------

		$settings->settings_do_import( 1, 1 );

		$message[] = $this->install->ipsclass->main_msg;

		// 2.2.0 preserve login type

		if( $this->install->saved_data['ipbli_usertype'] )
		{
			$this->install->ipsclass->DB->do_update( "conf_settings", array( 'conf_value' => $this->install->saved_data['ipbli_usertype'] ), "conf_key='ipbli_usertype'" );

			$this->install->ipsclass->DB->do_update( "login_methods", array( 'login_user_id' => $this->install->saved_data['ipbli_usertype'] ), "login_folder_name='internal'" );
		}


		if ( count( $this->install->error ) > 0 )
		{
			$this->install->message = count($message) ? implode( "<br />", $message ) : "Продолжаем обновление";

			$this->install->template->warning( array_merge( array( $this->install->message ),
															array( 'Проблемы при обновлении до '.$this->install->versions[ $this->install->current_upgrade ]. ' (' . $this->install->current_upgrade . ')' ),
															array( "<span style='color:red'>Ошибок: ".count($this->install->error).'</span>' ),
															$this->install->error ) );
			$this->install->template->in_error   = 1;

			$this->install->template->next_action = '?p=install&sub=acpperms';

			return;
		}

		$output[] = count($message) ? implode( "<br />", $message ) : "Нет настроек для импортирования...<br /><br />Продолжаем обновление";
		$this->install->template->next_action = '?p=install&sub=acpperms';
		$this->install->template->append( $this->install->template->install_page_refresh( $output ) );
		$this->install->template->hide_next = 1;
	}

	/*-------------------------------------------------------------------------*/
	// Installs the ACP Permissions
	/*-------------------------------------------------------------------------*/
	/**
	* Installs ACP Permissions
	*
	* @return void
	*/
	function install_acpperms()
	{
		if( $this->install->current_upgrade AND $this->install->current_upgrade < 20000 )
		{
			// Jump right to the finish routine for 1.x
			// Reorganized - should never hit here

			$this->install_finish();
			return;
		}

		$message 				= array();
		$this->install->error   = array();

		//-----------------------------------------
		// Do Tasks - Just stick it here shall we
		//-----------------------------------------

		require_once( ROOT_PATH . 'sources/api/api_tasks.php' );
		$api =  new api_tasks();
		$api->ipsclass =& $this->install->ipsclass;
		$api->api_init();

		$api->add_task();

		$message[] = $api->error ? "<span style='color:red;'>Не удалось обновить список задач планировщика.  Воспользуйтесь интсрументом обновления задач в АЦ.</span>" : "Обновление списка задач планировщика...";

		//-------------------------------
		// Load module...
		//-------------------------------

		require_once( ROOT_PATH . 'sources/action_admin/acppermissions.php' );
		$settings           =  new ad_acppermissions();
		$settings->ipsclass =& $this->install->ipsclass;

		//-------------------------------
		// Set location
		//-------------------------------

		$this->install->ipsclass->input['file_location'] = 'resources/acpperms.xml';

		//-------------------------------
		// Run it
		//-------------------------------

		$settings->acpperms_xml_import( 1 );

		$message[] = $this->install->ipsclass->main_msg;

		//-----------------------------------------
		// Install FAQ
		//-----------------------------------------
		$message[] 	= "Обновление разделов помощи...";
		$xml 		= new class_xml();
		$xml->lite_parser = 1;

		$updatehelp = ( isset( $this->install->saved_data['helpfile'] ) && $this->install->saved_data['helpfile'] ) ? 1 : 0;

		$content = implode( "", file( ROOT_PATH . 'resources/faq.xml' ) );

		if ( $content )
		{
			$xml->xml_parse_document( $content );

			foreach( $xml->xml_array['export']['group']['row'] as $id => $entry )
			{
				$newrow = array();
				foreach( $entry as $f => $data )
				{
					if ( $f == 'VALUE' or $f == 'id' )
					{
						continue;
					}

					$newrow[$f] = $entry[ $f ]['VALUE'];
				}

				if ( $newrow['title'] )
				{
					$cur_faq = $this->install->ipsclass->DB->build_and_exec_query( array( 'select'	=> 'id', 'from' => 'faq', 'where' => "title = '".$this->install->ipsclass->DB->add_slashes( $newrow['title'] )."'" ) );

					if ( $cur_faq['id'] )
					{
						if ( $updatehelp )
						{
							$this->install->ipsclass->DB->do_update( 'faq', $newrow, "id = ".$cur_faq['id'] );
						}
					}
					else
					{
						$this->install->ipsclass->DB->do_insert( 'faq', $newrow );
					}
				}
			}
		}

		if ( count( $this->install->error ) > 0 )
		{
			$this->install->message = count($message) ? implode( "<br />", $message ) : "Продолжаем обновление";

			$this->install->template->warning( array_merge( array( $this->install->message ),
															array( 'Проблемы при обновлении до '.$this->install->versions[ $this->install->current_upgrade ]. ' (' . $this->install->current_upgrade . ')' ),
															array( "<span style='color:red'>Ошибок: ".count($this->install->error).'</span>' ),
															$this->install->error ) );
			$this->install->template->in_error   = 1;

			$this->install->template->next_action = '?p=install&sub=finish';

			return;
		}

		$this->install->template->next_action = '?p=install&sub=checkdb';
		$this->install->template->append( $this->install->template->install_page_refresh( $message ) );
		$this->install->template->hide_next = 1;
	}

	/*-------------------------------------------------------------------------*/
	// Finishes the version
	/*-------------------------------------------------------------------------*/
	/**
	* Runs version upgrade script and finishes the version
	*
	* @return void
	*/
	function install_finish()
	{
		$continue = 0;

		$upg_file = INS_ROOT_PATH.'installfiles/upg_'.$this->install->current_upgrade.'/version_upgrade.php';

		if ( file_exists( $upg_file ) )
		{
			require_once( $upg_file );

			if ( file_exists( INS_ROOT_PATH.'sql/'.$this->install->ipsclass->vars['sql_driver'].'_install.php' ) )
			{
				require_once( INS_ROOT_PATH.'sql/'.$this->install->ipsclass->vars['sql_driver'].'_install.php' );

				$this->install->extra_install           =  new install_extra();
				$this->install->extra_install->ipsclass =& $this->install->ipsclass;
			}

			$upgrade = new version_upgrade( $this->install );
			$result  = $upgrade->auto_run();

			if ( count( $this->install->error ) > 0 )
			{
				$this->install->template->warning( array_merge( array( $this->install->message ),
																array( 'Проблемы при обновлении до '.$this->install->versions[ $this->install->current_upgrade ]. ' (' . $this->install->current_upgrade . ')' ),
																array( "<span style='color:red'>Ошибок: ".count($this->install->error).'</span>' ),
																$this->install->error ) );
				$this->install->template->in_error   = 1;

				if ( ! $result )
				{
					$this->install->template->next_action = '?p=install&sub=finish';
				}
				elseif ( $this->install->current_upgrade >= $this->install->last_poss_id || $this->install->current_upgrade == 0 )
				{
					$this->install->template->next_action = '?p=install&sub=settings';
				}
				else
				{
					$this->install->template->next_action = '?p=install&sub=sql';
				}

				$in_error = 1;
			}

			//-----------------------------------------
			// 'version_upgrade.php' is now done
			//-----------------------------------------

			if ( $result )
			{
				// The individual upgrade files all shoot you to 2.0...

				if ( $this->install->current_upgrade < 20000 )
				{
					$this->install->current_upgrade = '10004';
					unset($this->install->saved_data['vid']);
				}

				//------------------------------------------
				// Update DB
				//------------------------------------------

				$this->install->ipsclass->DB->do_insert( 'upgrade_history', array(	'upgrade_version_id'    	=> $this->install->current_upgrade,
																				  		'upgrade_version_human' => $this->install->versions[ $this->install->current_upgrade ],
																				  		'upgrade_date'  		=> time(),
																				  		'upgrade_mid'   		=> $this->install->saved_data['mid'],
						     						   )                         	  );

				if ( $in_error == 1 )
				{
					return;
				}

				if ( $this->install->message )
				{
					$output[] = $this->install->message;
				}

				$output[] = "Успешно завершено обновление до версии <nobr>{$this->install->versions[ $this->install->current_upgrade ]}</nobr>";
			}
			else
			{
				if ( $in_error == 1 )
				{
					return;
				}

				if ( $this->install->message )
				{
					$output[] = $this->install->message;
				}
				else
				{
					$output[] = "Продолжаем обновление...";
				}

				$continue = 1;
			}
		}
		else
		{
			//------------------------------------------
			// Update DB
			//------------------------------------------

			if ( $this->install->current_upgrade )
			{
				$this->install->ipsclass->DB->do_insert( 'upgrade_history', array(	'upgrade_version_id'    	=> $this->install->current_upgrade,
																				  	'upgrade_version_human' => $this->install->versions[ $this->install->current_upgrade ],
																				  	'upgrade_date'  		=> time(),
																				  	'upgrade_mid'   		=> $this->install->saved_data['mid'],
						     						   )                         );

				$output[] = "Успешно завершено обновление до версии <nobr>{$this->install->versions[ $this->install->current_upgrade ]}</nobr>";
			}
		}

		//-----------------------------------------
		// Next...
		//-----------------------------------------

		if ( $continue )
		{
			//-----------------------------------------
			// More to do?
			//-----------------------------------------

			$this->install->template->next_action = '?p=install&sub=finish';
		}
		elseif ( $this->install->current_upgrade >= $this->install->last_poss_id || $this->install->current_upgrade == 0 )
		{
			//-----------------------------------------
			// Last update?
			//-----------------------------------------

			$this->install->template->next_action = '?p=install&sub=settings';
		}
		else
		{
			//-----------------------------------------
			// Do SQL
			//-----------------------------------------

			$this->install->template->next_action = '?p=install&sub=sql';
		}

		if ( $this->install->do_man )
		{
			$this->install->template->append( implode( "<br />", $output ) );
		}
		else
		{
			$this->install->template->append( $this->install->template->install_page_refresh( $output ) );
			$this->install->template->hide_next   = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// Runs the DB Checker after install
	/*-------------------------------------------------------------------------*/
	/**
	* Checks DB for errors
	*
	* @return void
	*/
	function install_checkdb()
	{
		$message 				= array();
		$this->install->error   = array();

		if (  @file_exists( ROOT_PATH."/install/sql/{$this->install->ipsclass->vars['sql_driver']}_tables.php" ) )
		{
			//-------------------------------
			// Load ACP Skin...
			//-------------------------------

			require_once( ROOT_PATH   . "sources/lib/admin_skin.php" );
			$this->install->ipsclass->skin_acp = 'IPB2_Standard';
			$this->install->ipsclass->skin_acp_url = $this->install->ipsclass->vars['board_url']."/skin_acp/".$this->install->ipsclass->skin_acp;

			$this->install->ipsclass->adskin           = new admin_skin();
			$this->install->ipsclass->adskin->ipsclass =& $this->install->ipsclass;
			$this->install->ipsclass->adskin->init_admin_skin();

			//-------------------------------
			// Load module...
			//-------------------------------

			require_once( ROOT_PATH."sources/action_admin/sql_{$this->install->ipsclass->vars['sql_driver']}.php" );

			$this->sql_driver = new ad_sql_module();
			$this->sql_driver->ipsclass =& $this->install->ipsclass;

			$output = array();
			$output = $this->sql_driver->db_table_diag( 0 );

			$our_output = "		<style type='text/css' media='all'>
									.tableheader,
									.tableheaderalt
									{
										font-size:12px;
										vertical-align:middle;
										font-weight:bold;
										color:#FFF;
										padding:8px 0px 8px 5px;
										background-image: url({$this->install->ipsclass->vars['board_url']}/skin_acp/IPB2_Standard/images/folder_css_images/table_title_gradient.gif);
										background-repeat: repeat-x;
										background-color:#3363A1;
									}
									@import url('install.css');
								</style>
								<script type='text/javascript' src='dbchecker.js'></script>
						";

			if( $this->sql_driver->db_has_issues )
			{
				$this->install->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );
				$this->install->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );

				$our_output .= $this->install->ipsclass->adskin->start_table( "ВНИМАНИЕ: Обнаружены ошибки" );

				$our_output .= $this->install->ipsclass->adskin->add_td_row( array( array( "<script type='text/javascript'>var save_data = '".serialize($this->install->saved_data)."';</script><span class='rss-feed-invalid'>Найдены ошибки в таблицах базы данных форума.  Вы можете ознакомиться со списком найденных ошибок ниже, либо <a href='#' onclick='fix_all_dberrors();'>исправить</a> сразу все ошибки.", 2 ) ) );

				$our_output .= $this->install->ipsclass->adskin->end_table();
			}
			else
			{
				$this->install->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );
				$this->install->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );

				$our_output .= $this->install->ipsclass->adskin->start_table( "Нет ошибок" );

				$our_output .= $this->install->ipsclass->adskin->add_td_row( array( array( "<span class='rss-feed-valid'>Ошибок в ваше базе данных не найдено.", 2 ) ) );

				$our_output .= $this->install->ipsclass->adskin->end_table();
			}

			$this->install->ipsclass->adskin->td_header[] = array( "Таблица"    	, "30%" );
			$this->install->ipsclass->adskin->td_header[] = array( "Состояние"  	, "20%" );
			$this->install->ipsclass->adskin->td_header[] = array( "Исправить"       	, "50%" );

			$our_output .= $this->install->ipsclass->adskin->start_table( "Результаты проверки таблиц базы данных" );

			foreach( $output as $html_row )
			{
				$html_row = preg_replace( "#'&amp;section=help&amp;act=diag&amp;code=dbchecker&amp;query=(.*?)'#i", "'index.php?p=install&sub=checkdb&saved_data=".serialize($this->install->saved_data)."&query=$1'", $html_row );

				$our_output .= $html_row;
			}

			$our_output .= $this->install->ipsclass->adskin->end_table();
		}
		else
		{
			// Can't run db checker as table def isn't there
			$this->install_skinrevert();
			return;
		}

		$this->install->template->next_action = '?p=install&sub=skinrevert';
		$this->install->template->append( $our_output );
	}


	/*-------------------------------------------------------------------------*/
	// Install: Revert Skin Changes
	/*-------------------------------------------------------------------------*/
	/**
	* Install templates
	*
	* @return void
	*/
	function install_skinrevert()
	{
		$message 				= array();
		$this->install->error   = array();

		// First time around, we won't have this

		$this->install->saved_data['do'] = isset($this->install->ipsclass->input['do']) ? $this->install->ipsclass->input['do'] : $this->install->saved_data['do'];

		if( $this->install->saved_data['do'] == 'none' )
		{
			$this->install_templates();
			return;
		}

		if ( ! $this->install->saved_data['do'] )
		{
			$id = intval($this->install->saved_data['skinid']) ? intval($this->install->saved_data['skinid']) : 1;
			$this->install->saved_data['new_skin'] = intval($this->install->saved_data['new_skin']);

			$default = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => '*',
																			   'from'   => 'skin_sets',
																			   'where'  => "set_skin_set_id > {$id}",
																			   'order'  => 'set_skin_set_id ASC',
																			   'limit'  => array(0,1) ) );

			if ( $default['set_skin_set_id'] AND ( $default['set_skin_set_id'] == $this->install->saved_data['new_skin'] ) )
			{
				$default = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => '*',
																				   'from'   => 'skin_sets',
																				   'where'  => "set_skin_set_id > {$this->install->saved_data['new_skin']}",
																				   'order'  => 'set_skin_set_id ASC',
																				   'limit'  => array(0,1) ) );
			}

			if ( ! $default['set_skin_set_id'] )
			{
				$this->install->template->next_action = '?p=install&sub=templates';
				$this->install->template->append( $this->install->template->install_page_refresh( array( 'Обработаны все стили' ) ) );
				$this->install->template->hide_next   = 1;
				return;
			}
			else
			{
				$this->install->saved_data['skinid'] = $default['set_skin_set_id'];

				$this->install->template->next_action = '?p=install&sub=skinrevert';
				$this->install->template->append( $this->install->template->install_template_skinrevert( $default['set_name'] ) );
				return;
			}
		}
		else
		{
			$man     = intval( $this->install->saved_data['skinid'] );
			$cnt     = 0;

			$default = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".$man ) );

			$this->install->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "set_id=".$man ) );
			$outer = $this->install->ipsclass->DB->simple_exec();

			if( $this->install->ipsclass->DB->get_num_rows($outer) )
			{
				while( $r = $this->install->ipsclass->DB->fetch_row($outer) )
				{
					if( $r['set_id'] == 1 )
					{
						continue;
					}
					else
					{
						$this->install->ipsclass->DB->simple_exec_query( array( 'delete' => 'skin_templates', 'where' => 'suid='.$r['suid'] ) );
						$cnt++;
					}
				}
			}

			$this->install->ipsclass->DB->do_update( 'skin_sets', array( 'set_css' => '', 'set_wrapper' => '' ), "set_skin_set_id=".$man );

			$next = $this->install->ipsclass->DB->simple_exec_query( array( 'select'  => '*',
																			'from'    => 'skin_sets',
																			'where'   => "set_skin_set_id > ".$man,
																			'order'   => 'set_skin_set_id ASC',
																			'limit'   => array(0,1) ) );

			if( $next['set_skin_set_id'] )
			{
				if( $this->install->saved_data['do'] == 'all' )
				{
					$this->install->saved_data['skinid'] = $next['set_skin_set_id'];
					$this->install->saved_data['do']	 = 'all';
					$this->install->template->next_action = '?p=install&sub=skinrevert';
					$this->install->template->append( $this->install->template->install_page_refresh( array( "Изменения в $cnt шаблонах из стиля '{$default['set_name']}' отменены, переходим к следующему стилю..." ) ) );
					$this->install->template->hide_next   = 1;
					return;
				}
				else
				{
					$this->install->saved_data['skinid'] = $next['set_skin_set_id'];
					$this->install->template->next_action = '?p=install&sub=skinrevert';
					unset($this->install->saved_data['do']);
					$this->install->template->append( $this->install->template->install_page_refresh( array( "Изменения в $cnt шаблонах из стиля '{$default['set_name']}' отменены, переходим к следующему стилю..." ) ) );
					$this->install->template->hide_next   = 1;
					return;
				}
			}
			else
			{
				$this->install->template->next_action = '?p=install&sub=templates';
				$this->install->template->append( $this->install->template->install_page_refresh( array( "Изменения в $cnt шаблонах из стиля '{$default['set_name']}' отменены, переходим к следующему шаблону..." ) ) );
				$this->install->template->hide_next   = 1;
				return;
			}
		}
	}


	/*-------------------------------------------------------------------------*/
	// Install: Templates
	/*-------------------------------------------------------------------------*/
	/**
	* Install templates
	*
	* @return void
	*/
	function install_templates()
	{
		//-----------------------------------
		// Get XML
		//-----------------------------------

		$xml = new class_xml();
		$xml->lite_parser = 1;

		//-----------------------------------
		// Get XML file (TEMPLATES)
		//-----------------------------------

		$xmlfile = ROOT_PATH.'resources/ipb_templates.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-------------------------------
		// Unpack the datafile (TEMPLATES)
		//-------------------------------

		$xml->xml_parse_document( $setting_content );

		//-------------------------------
		// (TEMPLATES)
		//-------------------------------

		if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			$this->install->template->in_error   = 1;
			$this->install->error[] = "Проблемы с ресуром resources/ipb_templates.xml - ресурс содержит не правильный XML код";

			$this->install->template->warning( $this->install->error );

			$this->install->template->next_action = '?p=install&sub=rebuild';
			return;
		}
		else
		{
			$output[] = "Главный шаблон обновлен, переходим к созданию кешей шаблонов...";

			foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $id => $entry )
			{
				$row = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => 'suid',
																	  'from'   => 'skin_templates',
																	  'where'  => "group_name='{$entry[ 'group_name' ]['VALUE']}' AND func_name='{$entry[ 'func_name' ]['VALUE']}' and set_id=1"
															 )      );

				$this->install->ipsclass->DB->free_result();

				$this->install->ipsclass->DB->allow_sub_select 	= 1;

				if ( $row['suid'] )
				{
					$this->install->ipsclass->DB->do_update( 'skin_templates', array( 'func_data'       => $entry[ 'func_data' ]['VALUE'],
																			 'section_content' => $entry[ 'section_content' ]['VALUE'],
																			 'updated'         => time()
																		   )
																	, 'suid='.$row['suid'] );
				}
				else
				{
					$this->install->ipsclass->DB->do_insert( 'skin_templates', array( 'func_data'       => $entry[ 'func_data' ]['VALUE'],
																			 'func_name'       => $entry[ 'func_name' ]['VALUE'],
																			 'section_content' => $entry[ 'section_content' ]['VALUE'],
																			 'group_name'      => $entry[ 'group_name' ]['VALUE'],
																			 'updated'         => time(),
																			 'set_id'          => 1
												 )                         );
				}
			}
		}

		//-----------------------------------------
		// Next...
		//-----------------------------------------

		unset($xml);

		$this->install->template->append( $this->install->template->install_page_refresh( $output ) );
		$this->install->template->next_action = '?p=install&sub=caches';
		$this->install->template->hide_next   = 1;
	}

	/*-------------------------------------------------------------------------*/
	// Install: Caches
	/*-------------------------------------------------------------------------*/
	/**
	* Install Caches
	*
	* @return void
	*/
	function install_caches()
	{
		//-----------------------------------------
		// Do Caches
		//-----------------------------------------

		require_once( ROOT_PATH . 'sources/api/api_skins.php' );
		$api =  new api_skins();
		$api->ipsclass =& $this->install->ipsclass;
		$api->api_init();

		$this->install->ipsclass->DB->allow_sub_select 	= 1;

		if ( isset( $this->install->ipsclass->input['sid'] ) )
		{
			if ( $this->install->ipsclass->input['sid'] == 0 )
			{
				$output = $this->install->cache_and_cleanup();
				$this->install->template->next_action = '?p=done';
			}
			else
			{
				$messages = $api->skin_rebuild_caches( intval( $this->install->ipsclass->input['sid'] ) );
				$output = $messages['messages'];
				$this->install->template->next_action = '?p=install&sub=caches&sid='.$messages['completed'];
			}
		}
		else
		{
				$messages = $api->skin_rebuild_caches( 0 );
				$output = $messages['messages'];
				$this->install->template->next_action = '?p=install&sub=caches&sid='.$messages['completed'];
		}

		//-----------------------------------------
		// Next...
		//-----------------------------------------

		$this->install->template->hide_next   = 1;
		$this->install->template->append( $this->install->template->install_page_refresh( $output ) );
	}

}

?>