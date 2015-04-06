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
|   > Warn Log functions
|   > Module written by Matt Mecham
|   > Date started: 4th June 2003
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

class ad_warnlogs {

	var $base_url;

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
	var $perm_child = "warnlog";

	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Журнал предупреждений' );

		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{

			case 'view':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->view();
				break;

			case 'viewcontact':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->view_contact();
				break;

			case 'viewnote':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->view_note();
				break;

			case 'remove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->remove();
				break;


			//-----------------------------------------
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// View NOTE in da pop up innit
	//-----------------------------------------

	function view_note()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно получить ID email, попробуйте снова");
		}

		//-----------------------------------------
        // Load and config the post parser
        //-----------------------------------------

        require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $this->parser                      = new parse_bbcode();
        $this->parser->ipsclass            = $this->ipsclass;
        $this->parser->allow_update_caches = 1;

        $this->parser->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);

		$id = intval($this->ipsclass->input['id']);

		$this->ipsclass->DB->cache_add_query( 'warnlogs_view_note', array( 'id' => $id ) );
		$this->ipsclass->DB->cache_exec_query();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно получить ID email, попробуйте снова ($id)");
		}

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$content = preg_match( "#<content>(.+?)</content>#is", $row['wlog_notes'], $cont );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Warn Notes" );

		$row['date']  = $this->ipsclass->admin->get_date( $row['wlog_date'], 'LONG' );

		$this->parser->parse_html    = 0;
		$this->parser->parse_nl2br   = 1;
		$this->parser->parse_smilies = 1;
		$this->parser->parse_bbcode  = 1;

		$text = $this->parser->pre_display_parse( $this->parser->pre_db_parse($cont[1]) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<strong>От:</strong> {$row['p_name']}
													<br /><strong>Кому:</strong> {$row['a_name']}
													<br /><strong>Отправлено:</strong> {$row['date']}
													<hr>
													<br />$text
												    "
										 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->print_popup();


	}


	//-----------------------------------------
	// View contact in da pop up innit
	//-----------------------------------------

	function view_contact()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Невозможно получить ID email, попробуйте снова");
		}

		$id = intval($this->ipsclass->input['id']);

		$this->ipsclass->DB->cache_add_query( 'warnlogs_view_note', array( 'id' => $id ) );
		$this->ipsclass->DB->cache_exec_query();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно получить ID email, попробуйте снова ($id)");
		}

		$type = $row['wlog_contact'] == 'pm' ? "Личное сообщение" : "EMAIL";

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$subject = preg_match( "#<subject>(.+?)</subject>#is", $row['wlog_contact_content'], $subj );
		$content = preg_match( "#<content>(.+?)</content>#is", $row['wlog_contact_content'], $cont );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( $type.": ".$subj[1] );



		$row['date'] = $this->ipsclass->admin->get_date( $row['wlog_date'], 'LONG' );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<strong>От:</strong> {$row['p_name']}
													<br /><strong>Кому:</strong> {$row['a_name']}
													<br /><strong>Отправлено:</strong> {$row['date']}
													<br /><strong>Тема:</strong> $subj[1]
													<hr>
													<br />$cont[1]
												    "
										 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->print_popup();


	}




	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function view()
	{
		$start = intval($this->ipsclass->input['st']) >=0 ? intval($this->ipsclass->input['st']) : 0;

		$this->ipsclass->html .= ""; // removed js popwin

		$this->ipsclass->admin->page_detail = "Просмотр всех записей о предупреждений пользователя";
		$this->ipsclass->admin->page_title  = "Журнал предупреждений";

		if ($this->ipsclass->input['search_string'] == "" and $this->ipsclass->input['mid'])
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(wlog_id) as count', 'from' => 'warn_logs', 'where' => "wlog_mid=".intval($this->ipsclass->input['mid']) ) );
			$this->ipsclass->DB->simple_exec();

			$row = $this->ipsclass->DB->fetch_row();

			$row_count = $row['count'];

			$query = "&{$this->ipsclass->form_code}&mid={$this->ipsclass->input['mid']}&code=view";

			$this->ipsclass->DB->cache_add_query( 'warnlogs_view', array( 'mid' => intval($this->ipsclass->input['mid']), 'start' => $start ) );
			$this->ipsclass->DB->cache_exec_query();
		}
		else
		{
			$this->ipsclass->input['search_string'] = urldecode($this->ipsclass->input['search_string']);

			if ( ($this->ipsclass->input['search_type'] == 'notes') )
			{
				$dbq = "l.wlog_notes LIKE '%".$this->ipsclass->input['search_string']."%'";
			}
			else
			{
				$dbq = "l.wlog_contact_content LIKE '%".$this->ipsclass->input['search_string']."%'";
			}

			$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(l.wlog_id) as count', 'from' => 'warn_logs l', 'where' => $dbq ) );
			$this->ipsclass->DB->simple_exec();

			$row = $this->ipsclass->DB->fetch_row();

			$row_count = $row['count'];

			$query = "&{$this->ipsclass->form_code}&code=view&search_type={$this->ipsclass->input['search_type']}&search_string=".urlencode($this->ipsclass->input['search_string']);

			$this->ipsclass->DB->cache_add_query( 'warnlogs_view_two', array( 'dbq' => $dbq, 'start' => $start ) );
			$this->ipsclass->DB->cache_exec_query();
		}

		$links = $this->ipsclass->adskin->build_pagelinks( array( 'TOTAL_POSS'  => $row_count,
														  'PER_PAGE'    => 30,
														  'CUR_ST_VAL'  => $start,
														  'L_SINGLE'    => "Страница",
														  'L_MULTI'     => "Страницы: ",
														  'BASE_URL'    => $this->ipsclass->base_url.$query,
														)
												 );

		$this->ipsclass->admin->page_detail = "В этой секции вы можете просматривать и управлять журналом предупреждений";
		$this->ipsclass->admin->page_title  = "Журнал предупреждений";

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "Тип"        , "5%" );
		$this->ipsclass->adskin->td_header[] = array( "Пользователь" , "15%" );
		$this->ipsclass->adskin->td_header[] = array( "Уведомлен?"   , "5%" );
		$this->ipsclass->adskin->td_header[] = array( "Проверка сообщений"       , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Бан"        , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Запрет сообщений"     , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Время"        , "15%" );
		$this->ipsclass->adskin->td_header[] = array( "Модератор"   , "15%" );
		$this->ipsclass->adskin->td_header[] = array( "Посмотреть заметки"   , "10%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Журнал предупреждений" );
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic($links, 'right', 'tablesubheader');

		$days = array( 'd' => "Дней", 'h' => "часов" );

		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $row = $this->ipsclass->DB->fetch_row() )
			{

				$row['wlog_date'] = $this->ipsclass->admin->get_date( $row['wlog_date'], 'LONG' );

				$type = ( $row['wlog_type'] == 'pos' )      ? '<span style="color:green;font-weight:bold">-</span>' : '<span style="color:red;font-weight:bold">+</span>';
				$cont = ( $row['wlog_contact'] !=  'none' ) ? "<center><a href='javascript:pop_win(\"&{$this->ipsclass->form_code}&code=viewcontact&id={$row['wlog_id']}\",\"Log\", 400,400)'>Просмотр</a></center>" : '&nbsp;';

				$mod     = preg_match( "#<mod>(.+?)</mod>#is"        , $row['wlog_notes'], $mm );
				$post    = preg_match( "#<post>(.+?)</post>#is"      , $row['wlog_notes'], $pm );
				$susp    = preg_match( "#<susp>(.+?)</susp>#is"      , $row['wlog_notes'], $sm );
				$content = preg_match( "#<content>(.+?)</content>#is", $row['wlog_notes'], $cm );

				$content = $cm[1];

				$mod  = trim($mm[1]);
				$post = trim($pm[1]);
				$susp = trim($sm[1]);

				list($v, $u, $i) = explode(',', $mod);

				if ( $i == 1 )
				{
					$mod = 'Не определено';
				}
				else if ( $v == "" )
				{
					$mod = 'Нет';
				}
				else
				{
					$mod = $v.' '.$days[$u];
				}

				//-----------------------------------------

				list($v, $u, $i) = explode(',', $post);

				if ( $i == 1 )
				{
					$post = 'Не определено';
				}
				else if ( $v == "" )
				{
					$post = 'Нет';
				}
				else
				{
					$post = $v.' '.$days[$u];
				}

				list($v, $u) = explode(',', $susp);

				if ( $v == "" )
				{
					$susp = 'нет';
				}
				else
				{
					$susp = $v.' '.$days[$u];
				}

				//-----------------------------------------

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
														  "<center>$type</center>",
														  "<b>{$row['a_name']}</b>",
														  $cont,
														  $mod,
														  $susp,
														  $post,
														  "{$row['wlog_date']}",
														  "<b>{$row['p_name']}</b>",
														  "<center><a href='javascript:pop_win(\"&{$this->ipsclass->form_code}&code=viewnote&id={$row['wlog_id']}\",\"Log\",400,400)'>Просмотр</a></center>"
												 )      );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<center>Нет записей</center>");
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic($links, 'right', 'tablesubheader');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}

	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function remove()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("Вы не выбрали пользователя, которому необходимо удалить предупреждение.");
		}

		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'warn_logs', 'where' => "wlog_mid=".intval($this->ipsclass->input['mid']) ) );

		$this->ipsclass->admin->save_log("Предупреждение удалено");

		$this->ipsclass->boink_it($this->ipsclass->base_url."&{$this->ipsclass->form_code}");
	}


	//-----------------------------------------
	// SHOW LOGS
	//-----------------------------------------

	function list_current()
	{
		$form_array = array();

		$this->ipsclass->admin->page_detail = "В этой секции вы можете просматривать и управлять журналом предупреждений.<br />Примечание: удаление записей из журнала не понижает уровень предупреждений пользователя.";
		$this->ipsclass->admin->page_title  = "Журнал предупреждений";

		$this->ipsclass->html .= ""; // removed js popwin

		//-----------------------------------------
		// VIEW LAST 5
		//-----------------------------------------

		$this->ipsclass->DB->cache_add_query( 'warnlogs_list_current', array() );
		$this->ipsclass->DB->cache_exec_query();

		$this->ipsclass->adskin->td_header[] = array( "Тип"            , "5%" );
		$this->ipsclass->adskin->td_header[] = array( "Пользователь"   , "25%" );
		$this->ipsclass->adskin->td_header[] = array( "Уведомлен?"      , "5%" );
		$this->ipsclass->adskin->td_header[] = array( "Время"            , "25%" );
		$this->ipsclass->adskin->td_header[] = array( "Модератор"       , "25%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Последние 10 записей" );

		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $row = $this->ipsclass->DB->fetch_row() )
			{
				$row['wlog_date'] = $this->ipsclass->admin->get_date( $row['wlog_date'], 'LONG' );

				$type = ( $row['wlog_type'] == 'pos' ) ? '<span style="color:green;font-weight:bold">-</span>' : '<span style="color:red;font-weight:bold">+</span>';
				$cont = ( $row['wlog_contact'] !=  'none' ) ? "<center><a title='Показать сообщение' href='javascript:pop_win(\"&{$this->ipsclass->form_code}&code=viewcontact&id={$row['wlog_id']}\",\"Log\",400,400)'><img src='{$this->ipsclass->skin_acp_url}/images/acp_check.gif' border='0' alt='X'></a></center>" : '&nbsp;';

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
																					 "<center>$type</center>",
																					 "<b>{$row['a_name']}</b>",
																					 $cont,
																					 "{$row['wlog_date']}",
																					 "<b>{$row['p_name']}</b>",
																			)      );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<center>Нет записей</center>");
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "Пользователь"            , "30%" );
		$this->ipsclass->adskin->td_header[] = array( "Время предупреждения"           , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "Просмотр всех предупреждений"     , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "Удаление всех предупреждений"   , "30%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Журнал предупреждений" );

		$this->ipsclass->DB->cache_add_query( 'warnlogs_list_current_two', array() );
		$this->ipsclass->DB->cache_exec_query();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>{$r['members_display_name']}</b>",
																				 "<center>{$r['act_count']}</center>",
																				 "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=view&mid={$r['wlog_mid']}'>Просмотр</a></center>",
																				 "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=remove&mid={$r['wlog_mid']}'>Удалить</a></center>",
																		)      );
		}



		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
												  2 => array( 'act'   , 'warnlog'       ),
												  4 => array( 'section', $this->ipsclass->section_code ),
									     )      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Поиск действий в журнале" );

		$form_array = array(
							  0 => array( 'notes'  , 'Заметки' ),
							  1 => array( 'contact', 'Отправка Email/PM'  ),
						   );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Искать </b>" ,
										  		  $this->ipsclass->adskin->form_input( "search_string")
								 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Искать в </b>" ,
										  		  $this->ipsclass->adskin->form_dropdown( "search_type", $form_array)
								 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Искать");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}



}


?>