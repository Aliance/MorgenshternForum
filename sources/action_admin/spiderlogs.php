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
|   > Spider (MAN) Logs
|   > Module written by Matt Mecham
|   > Date started: 28th May 2003
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

class ad_spiderlogs {

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
	var $perm_child = "spiderlog";


	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Журнал действий поисковых роботов' );
		
		//-----------------------------------------
		// Get bot names
		//-----------------------------------------
		
		foreach( explode( "\n", $this->ipsclass->vars['search_engine_bots'] ) as $bot )
		{
			list($ua, $n) = explode( "=", $bot );
			
			$this->bot_map[ strtolower($ua) ] = $n;
		}
		
		switch($this->ipsclass->input['code'])
		{
			case 'view':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->view();
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
	// View Logs
	//-----------------------------------------
	
	function view()
	{
		$start = intval($this->ipsclass->input['st']) >=0 ? intval($this->ipsclass->input['st']) : 0;
		
        $this->ipsclass->admin->page_detail = "Ниже указан список всех действий, которые были произведены поисковыми роботами на вашем форуме.<br />В этом журнале фиксируются действия только известных роботов, которые указаны в настройке «Список „user-agent“ поисковых роботов».";
        $this->ipsclass->admin->page_title  = "Журнал действий поисковых роботов";

		$botty = urldecode($this->ipsclass->input['bid']);
		$botty = str_replace( "&#33;", "!", $botty );
	
		if ( !isset($this->ipsclass->input['search_string']) OR $this->ipsclass->input['search_string'] == "" )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(sid) as count', 'from' => 'spider_logs', 'where' => "bot='$botty'" ) );
			$this->ipsclass->DB->simple_exec();
		
			$row = $this->ipsclass->DB->fetch_row();
			
			$row_count = $row['count'];
			
			$query = "&{$this->ipsclass->form_code}&bid={$this->ipsclass->input['bid']}&code=view";
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*',
										  'from'   => 'spider_logs',
										  'where'  => "bot='$botty'",
										  'order'  => 'entry_date DESC',
										  'limit'  => array( $start, 20 ) ) );
			$this->ipsclass->DB->simple_exec();
		}
		else
		{
			$this->ipsclass->input['search_string'] = urldecode($this->ipsclass->input['search_string']);
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(sid) as count', 'from' => 'spider_logs', 'where' => "query_string LIKE '%{$this->ipsclass->input['search_string']}%'" ) );
			$this->ipsclass->DB->simple_exec();
			
			$row = $this->ipsclass->DB->fetch_row();
			
			$row_count = $row['count'];
			
			$query = "&{$this->ipsclass->form_code}&code=view&search_string=".urlencode($this->ipsclass->input['search_string']);
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*',
										                'from'   => 'spider_logs',
										                'where'  => "query_string LIKE '%{$this->ipsclass->input['search_string']}%'",
										                'order'  => 'entry_date DESC',
										                'limit'  => array( $start, 20 ) ) );
			$this->ipsclass->DB->simple_exec();
		}
		
		$links = $this->ipsclass->adskin->build_pagelinks( array(   'TOTAL_POSS'  => $row_count,
											                        'PER_PAGE'    => 20,
											                        'CUR_ST_VAL'  => $start,
                                                                    'L_SINGLE'    => "Одна страница",
                                                                    'L_MULTI'     => "Страниц: ",
											                        'BASE_URL'    => $this->ipsclass->base_url.$query,
											 )
									  );
									  
        $this->ipsclass->admin->page_detail = "Ниже указан список всех действий, которые были произведены поисковыми роботами на вашем форуме.<br />В этом журнале фиксируются действия только известных роботов, которые указаны в настройке «Список „user-agent“ поисковых роботов».";
        $this->ipsclass->admin->page_title  = "Журнал действий поисковых роботов";
		
        //-----------------------------------------
		// Show form!
		//-----------------------------------------
		
        $this->ipsclass->adskin->td_header[] = array( "Поисковый робот"            , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Строка запроса"        , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Дата"      , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "IP-адрес"          , "10%" );
		
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Список действий — результаты поиска" );
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic($links, 'right', 'tablesubheader');
		
		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $row = $this->ipsclass->DB->fetch_row() )
			{
				$extra = "";
				
				if ( preg_match( '#lo-fi#i', $row['query_string'] ) )
				{
					$extra = '(Lo-Fi)';
					$row['query_string'] = 'showtopic='.preg_replace( "#Lo-Fi\: t(.+?)\.html#", "\\1", $row['query_string'] );
				}
				
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>".$this->bot_map[ strtolower($row['bot']) ]."</b>",
																		 "<a href='{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}?{$row['query_string']}' target='_blank'>$extra {$row['query_string']}</a>",
																		 $this->ipsclass->admin->get_date( $row['entry_date'], 'LONG' ),
																		 "{$row['ip_address']}",
																)      );
			
			}
		}
		else
		{
                        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<center>Поиск не дал результатов</center>");
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
		if ($this->ipsclass->input['bid'] == "")
		{
                        $this->ipsclass->admin->error("Вы не выбрали бота, которого хотите удалить!");
		}
		
		$botty = urldecode($this->ipsclass->input['bid']);
		$botty = str_replace( "&#33;", "!", $botty );
		
		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'spider_logs', 'where' => "bot='$botty'" ) );
		
                $this->ipsclass->admin->save_log("Удаленные записи из журнала поисковых роботов");
		
		$this->ipsclass->boink_it($this->ipsclass->base_url."&{$this->ipsclass->form_code}");
		exit();
	}
	
	
	//-----------------------------------------
	// SHOW ALL BOTS
	//-----------------------------------------
	
	function list_current()
	{
		$form_array = array();
	
        $this->ipsclass->admin->page_detail = "Ниже указан список всех действий, которые были произведены поисковыми роботами на вашем форуме.<br />В этом журнале фиксируются действия только известных роботов, которые указаны в настройке «Список „user-agent“ поисковых роботов».";
        $this->ipsclass->admin->page_title  = "Журнал действий поисковых роботов";

		//-----------------------------------------
		
        $this->ipsclass->adskin->td_header[] = array( "Поисковый робот"            , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Хитов"                , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Запущен"            , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Все действия"     , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Удалить все действия"   , "20%" );
		
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Список действий" );
		
									  
		$this->ipsclass->DB->cache_add_query( 'spiderlogs_list_current', array() );
		$this->ipsclass->DB->cache_exec_query();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$url_butt = urlencode($r['bot']);
			
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $this->bot_map[ strtolower($r['bot']) ],
																	 "<center>{$r['cnt']}</center>",
																	  $this->ipsclass->admin->get_date( $r['entry_date'], 'SHORT' ),
                                                                                                                                         "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=view&bid={$url_butt}'>Просмотр</a></center>",
                                                                                                                                         "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=remove&bid={$url_butt}'>Удалить</a></center>",
															)      );
		}
			
		
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		
		//-----------------------------------------
		//-------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
																 2 => array( 'act'   , 'spiderlog'       ),
																 4 => array( 'section', $this->ipsclass->section_code ),
														)      );
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
                $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Поиск действий" );
			
		//-----------------------------------------
		
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Искать...</b>" ,
                                                                                                    $this->ipsclass->adskin->form_input( "search_string").'... в строке запроса'
								 )      );
		
                $this->ipsclass->html .= $this->ipsclass->adskin->end_form("Найти");
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		//-----------------------------------------
		//-------------------------------
		
		$this->ipsclass->admin->output();
	
	}
	
	
	
}


?>