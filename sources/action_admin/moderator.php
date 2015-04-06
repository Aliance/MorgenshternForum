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
|   > Admin Category functions
|   > Module written by Matt Mecham
|   > Date started: 1st march 2002
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

class ad_moderator {

	var $base_url;
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "content";
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "mod";
	
	function auto_run()
	{
		$this->ipsclass->forums->forums_init();
		
		require ROOT_PATH.'sources/lib/admin_forum_functions.php';
		
		$this->forumfunc = new admin_forum_functions();
		$this->forumfunc->ipsclass =& $this->ipsclass;
		
		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'add':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->moderator_add_preform();
				break;
			
			case 'add_final':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->mod_form('add');
				break;
			case 'doadd':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->add_mod();
				break;
				
			case 'edit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->mod_form('edit');
				break;
				
			case 'doedit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->do_edit();
				break;
				
			case 'remove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->do_delete();
				break;
				
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->show_list();
				break;
		}
		
	}
	
	//-----------------------------------------
	//
	// DELETE MODERATOR
	//
	//-----------------------------------------
	
	function do_delete()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("Вы должны ввести ID модератора");
		}
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "mid=".intval($this->ipsclass->input['mid']) ) );
		$this->ipsclass->DB->simple_exec();
		
		$mod = $this->ipsclass->DB->fetch_row();
		
		if ( $mod['is_group'] )
		{
			$name = 'Группа: '.$mod['group_name'];
		}
		else
		{
			$name = $mod['member_name'];
		}
		
		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'moderators', 'where' => "mid=".intval($this->ipsclass->input['mid']) ) );
		
		$this->rebuild_moderator_cache();
		
		$this->ipsclass->admin->save_log("Удален модератор '{$name}'");
		
		$this->ipsclass->main_msg = "Модератор удален";
		$this->ipsclass->admin->redirect_noscreen( $this->ipsclass->base_url.'&section=content&act=forum' );
	}	
	
	
	//-----------------------------------------
	//
	// EDIT MODERATOR
	//
	//-----------------------------------------
	
	function do_edit()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("Вы должны ввести ID модератора");
		}
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "mid=".intval($this->ipsclass->input['mid']) ) );
		$this->ipsclass->DB->simple_exec();
		
		$mod = $this->ipsclass->DB->fetch_row();
		
		//-----------------------------------------
		// Build Mr Hash
		//-----------------------------------------
		
		$this->ipsclass->DB->do_update( 'moderators', array( 
															  'forum_id'     => intval($this->ipsclass->input['forum_id']),
															  'edit_post'    => intval($this->ipsclass->input['edit_post']),
															  'edit_topic'   => intval($this->ipsclass->input['edit_topic']),
															  'delete_post'  => intval($this->ipsclass->input['delete_post']),
															  'delete_topic' => intval($this->ipsclass->input['delete_topic']),
															  'view_ip'      => intval($this->ipsclass->input['view_ip']),
															  'open_topic'   => intval($this->ipsclass->input['open_topic']),
															  'close_topic'  => intval($this->ipsclass->input['close_topic']),
															  'mass_move'    => intval($this->ipsclass->input['mass_move']),
															  'mass_prune'   => intval($this->ipsclass->input['mass_prune']),
															  'move_topic'   => intval($this->ipsclass->input['move_topic']),
															  'pin_topic'    => intval($this->ipsclass->input['pin_topic']),
															  'unpin_topic'  => intval($this->ipsclass->input['unpin_topic']),
															  'post_q'       => intval($this->ipsclass->input['post_q']),
															  'topic_q'      => intval($this->ipsclass->input['topic_q']),
															  'allow_warn'   => intval($this->ipsclass->input['allow_warn']),
															  'split_merge'  => intval($this->ipsclass->input['split_merge']),
															  'edit_user'    => intval($this->ipsclass->input['edit_user']),
															  'can_mm'	     => intval($this->ipsclass->input['can_mm']),
															  'mod_can_set_open_time'  => intval($this->ipsclass->input['mod_can_set_open_time']),
															  'mod_can_set_close_time' => intval($this->ipsclass->input['mod_can_set_close_time']),
														  ) , 'mid='.intval($this->ipsclass->input['mid']) );
		
		$this->rebuild_moderator_cache();
		
		$this->ipsclass->admin->save_log("Изменен модератор '{$mod['member_name']}'");
		
		$this->ipsclass->main_msg = "Модератор изменен";
		$this->ipsclass->admin->redirect_noscreen( $this->ipsclass->base_url.'&section=content&act=forum' );
	}	
	
	//-----------------------------------------
	//
	// ADD MODERATOR
	//
	//-----------------------------------------
	
	function add_mod()
	{
		if ($this->ipsclass->input['fid'] == "")
		{
			$this->ipsclass->admin->error("Вы должны ввести ID форума для добавления модератора.");
		}
		
		//-----------------------------------------
		// Build Mr Hash
		//-----------------------------------------
		
		$mr_hash = array( 
							'edit_post'    => intval($this->ipsclass->input['edit_post']),
							'edit_topic'   => intval($this->ipsclass->input['edit_topic']),
							'delete_post'  => intval($this->ipsclass->input['delete_post']),
							'delete_topic' => intval($this->ipsclass->input['delete_topic']),
							'view_ip'      => intval($this->ipsclass->input['view_ip']),
							'open_topic'   => intval($this->ipsclass->input['open_topic']),
							'close_topic'  => intval($this->ipsclass->input['close_topic']),
							'mass_move'    => intval($this->ipsclass->input['mass_move']),
							'mass_prune'   => intval($this->ipsclass->input['mass_prune']),
							'move_topic'   => intval($this->ipsclass->input['move_topic']),
							'pin_topic'    => intval($this->ipsclass->input['pin_topic']),
							'unpin_topic'  => intval($this->ipsclass->input['unpin_topic']),
							'post_q'       => intval($this->ipsclass->input['post_q']),
							'topic_q'      => intval($this->ipsclass->input['topic_q']),
							'allow_warn'   => intval($this->ipsclass->input['allow_warn']),
							'split_merge'  => intval($this->ipsclass->input['split_merge']),
							'edit_user'    => intval($this->ipsclass->input['edit_user']),
							'can_mm'	   => intval($this->ipsclass->input['can_mm']),
							'mod_can_set_open_time'  => intval($this->ipsclass->input['mod_can_set_open_time']),
							'mod_can_set_close_time' => intval($this->ipsclass->input['mod_can_set_close_time']),
						);
						
		$forum_ids = array();
		
		$this->ipsclass->DB->simple_construct( array( 'select' => 'id', 'from' => 'forums', 'where' => "id IN(".$this->ipsclass->input['fid'].")" ) );
		$this->ipsclass->DB->simple_exec();
		
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$forum_ids[ $i['id'] ] = $i['id'];
		}
		
		//-----------------------------------------
						
		if ($this->ipsclass->input['mod_type'] == 'group')
		{
		
			if ($this->ipsclass->input['gid'] == "")
			{
				$this->ipsclass->admin->error("Невозможно найти группу с таким ID");
			}
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'where' => "g_id=".intval($this->ipsclass->input['gid']) ) );
			$this->ipsclass->DB->simple_exec();
		
			if ( ! $group = $this->ipsclass->DB->fetch_row() )
			{
				$this->ipsclass->admin->error("Невозможно найти группу с таким ID");
			}
			
			//-----------------------------------------
			// Already using this group on this forum?
			//-----------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "forum_id IN(".$this->ipsclass->input['fid'].") and group_id=".intval($this->ipsclass->input['gid']) ) );
			$this->ipsclass->DB->simple_exec();
			
			while( $f = $this->ipsclass->DB->fetch_row() )
			{
				unset($forum_ids[ $f['forum_id'] ]);
			}
			
			$mr_hash['member_name'] = '-1';
			$mr_hash['member_id']   = '-1';
			$mr_hash['group_id']    = $group['g_id'];
			$mr_hash['group_name']  = $group['g_title'];
			$mr_hash['is_group']    = 1;
			
			$ad_log = "Добавлена группа модераторов '{$group['g_title']}'";
			
		}
		else
		{
		
			if ($this->ipsclass->input['mem'] == "")
			{
				$this->ipsclass->admin->error("Вы не выбрали пользователя которому хотите дать модераторские права");
			}
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "id=".intval($this->ipsclass->input['mem']) ) );
			$this->ipsclass->DB->simple_exec();
		
			if ( ! $mem = $this->ipsclass->DB->fetch_row() )
			{
				$this->ipsclass->admin->error("Вы не выбрали пользователя которому хотите дать модераторские права.");
			}
			
			//-----------------------------------------
			// Already using this member on this forum?
			//-----------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "forum_id IN(".$this->ipsclass->input['fid'].") and member_id=".intval($this->ipsclass->input['mem']) ) );
			$this->ipsclass->DB->simple_exec();
			
			while( $f = $this->ipsclass->DB->fetch_row() )
			{
				unset($forum_ids[ $f['forum_id'] ]);
			}
			
			$mr_hash['member_name'] = $mem['name'];
			$mr_hash['member_id']   = $mem['id'];
			$mr_hash['is_group']    = 0;
			
			$ad_log = "Добавлен модератор '{$mem['name']}'";
		
		}
		
		//-----------------------------------------
		// Check for legal forums
		//-----------------------------------------
		
		if ( count($forum_ids) == 0)
		{
			$this->ipsclass->admin->error("Вы не выбрали ни одного форума для которых хотите добавить модераторов.");
		}
		
		//-----------------------------------------
		// Loopy loopy
		//-----------------------------------------
		
		foreach ($forum_ids as $cartman)
		{
			$mr_hash['forum_id'] = $cartman;
			
			$this->ipsclass->DB->force_data_type = array( 'member_name' => 'string' );
			
			$this->ipsclass->DB->do_insert( 'moderators', $mr_hash );
		}
		
		$this->ipsclass->admin->save_log($ad_log);
		
		$this->rebuild_moderator_cache();
		
		$this->ipsclass->main_msg = "Модераторы добавлены";
		$this->ipsclass->admin->redirect_noscreen( $this->ipsclass->base_url.'&section=content&act=forum' );
	}	
	
	//-----------------------------------------
	//
	// Rebuild moderator cache
	//
	//-----------------------------------------
	
	function rebuild_moderator_cache()
	{
		$this->ipsclass->cache['moderators'] = array();
		
		//-----------------------------------------
		// Get dem moderators
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select'   => 'moderator.*',
												 'from'     => array( 'moderators' => 'moderator' ),
												 'add_join' => array( 0 => array( 'select' => 'm.members_display_name',
																				  'from'   => array( 'members' => 'm' ),
																				  'where'  => "m.id=moderator.member_id",
																				  'type'   => 'left' ) ) ) );
		
		$this->ipsclass->DB->exec_query();
		
		while ( $i = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['moderators'][ $i['mid'] ] = $i;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'moderators', 'array' => 1, 'deletefirst' => 1 ) );
	}
	
	//-----------------------------------------
	//
	// ADD FINAL, display the add / edit form
	//
	//-----------------------------------------
	
	function mod_form( $type='add' )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$group = array();
		
		if ($type == 'add')
		{
			if ( $this->ipsclass->input['fid'] == "" )
			{
				$this->ipsclass->admin->error("Вы должны ввести ID форума для добавления модератора");
			}	
				
			$mod   = array();
			$names = array();
			
			//-----------------------------------------
			// Get forums
			//-----------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'name', 'from' => 'forums', 'where' => "id IN(".$this->ipsclass->input['fid'].")" ) );
			$this->ipsclass->DB->simple_exec();
		
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$names[] = $r['name'];
			}
			
			$thenames = implode( ", ", $names );
			
			//-----------------------------------------
			// Start proper
			//-----------------------------------------
			
			$button = "Добавить этого модератора";
			
			$form_code = 'doadd';
			
			if ( $this->ipsclass->input['group'] )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => 'g_id, g_title', 'from' => 'groups', 'where' => "g_id=".intval($this->ipsclass->input['group']) ) );
				$this->ipsclass->DB->simple_exec();
				
				if (! $group = $this->ipsclass->DB->fetch_row() )
				{
					$this->ipsclass->admin->error("Невозможно найти выбранную группу пользователей");
				}
				
				$this->ipsclass->admin->page_detail = "Добавление группы:<b> {$group['g_title']}</b> в качестве модератора для: $thenames";
				$this->ipsclass->admin->page_title = "Добавление группы модераторов";
			}
			else
			{
				if ( ! $this->ipsclass->input['member_id'] )
				{
					$this->ipsclass->admin->error("Вы должны указать ID пользователя");
				}
				else
				{
					$this->ipsclass->DB->simple_construct( array( 'select' => 'name, id', 'from' => 'members', 'where' => "id=".intval($this->ipsclass->input['member_id']) ) );
					$this->ipsclass->DB->simple_exec();
		
					if ( ! $mem = $this->ipsclass->DB->fetch_row() )
					{
						$this->ipsclass->admin->error("Пользователь с таким ID не существует");
					}
					
					$member_id   = $mem['id'];
					$member_name = $mem['name'];
				}
				
				$this->ipsclass->admin->page_detail = "Добавление пользователя $member_name в качестве модератора для: $thenames";
				$this->ipsclass->admin->page_title = "Добавление модератора";
			
			}
			
		}
		else
		{
			if ($this->ipsclass->input['mid'] == "")
			{
				$this->ipsclass->admin->error("Вы должны указать ID существующего модератора.");
			}
			
			$button    = "Изменить этого модератора";
			
			$form_code = "doedit";
			
			$this->ipsclass->admin->page_title  = "Измение модераторских прав";
			$this->ipsclass->admin->page_detail = "Пожалуйста, перед тем, как подтвердить форму, проверьте все поля очень внимательно";
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'moderators', 'where' => "mid=".intval($this->ipsclass->input['mid']) ) );
			$this->ipsclass->DB->simple_exec();
		
			if ( ! $mod = $this->ipsclass->DB->fetch_row() )
			{
				$this->ipsclass->admin->error("Невозможно найти запись с этим модератором");
			}
			
			$member_id   = $mod['member_id'];
			$member_name = $mod['member_name'];
		}
		
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'     , $form_code ),
																			 2 => array( 'act'      , 'mod'      ),
																			 3 => array( 'mid'      , $mod['mid']),
																			 4 => array( 'fid'      , $this->ipsclass->input['fid'] ),
																			 5 => array( 'mem'      , $member_id ),
																			 6 => array( 'mod_type' , $this->ipsclass->input['group'] ? 'group' : 'name' ),
																			 7 => array( 'gid'      , $group['g_id'] ),
																			 8 => array( 'gname'    , $group['g_name'] ),
																			 9 => array( 'section'  , $this->ipsclass->section_code ),
																	)      );
		
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Основные настройки" );
		
		//-----------------------------------------
		
		if ($type == 'edit')
		{
			$forums = array();
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, name', 'from' => 'forums', 'order' => "position" ) );
			$this->ipsclass->DB->simple_exec();
		
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$forums[] = array( $r['id'], $r['name'] );
			}
			
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Какой форум модерирует?</b>" ,
												  $this->ipsclass->adskin->form_dropdown( "forum_id", $forums, $mod['forum_id'] )
									     )      );
		}
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может изменять чужие темы/голосования?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("edit_post", $mod['edit_post'] )
									     )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может изменять название чужих тем?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("edit_topic", $mod['edit_topic'] )
									     )      );							     
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может удалять чужие сообщения?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("delete_post", $mod['delete_post'] )
									     )      );							     
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может удалять чужие темы/голосования?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("delete_topic", $mod['delete_topic'] )
									     )      );							     
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может видеть IP-адреса пользователей?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("view_ip", $mod['view_ip'] )
									     )      );		
				
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может открывать темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("open_topic", $mod['open_topic'] )
									     )      );		
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может закрывать темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("close_topic", $mod['close_topic'] )
									     )      );	
									     	
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может перемещать темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("move_topic", $mod['move_topic'] )
									     )      );							     
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может закреплять темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("pin_topic", $mod['pin_topic'] )
									     )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может опускать темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("unpin_topic", $mod['unpin_topic'] )
									     )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может соединять/разъединять темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("split_merge", $mod['split_merge'] )
									     )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может устанавливать время открытия темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("mod_can_set_open_time", $mod['mod_can_set_open_time'] )
									     )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может устанавливать время закрытия темы?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("mod_can_set_close_time", $mod['mod_can_set_close_time'] )
									     )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Настройки модераторской панели управления" );
		
		//-----------------------------------------
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может перемещать сразу несколько тем?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("mass_move", $mod['mass_move'] )
									     )      );	
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может удалять сразу несколько тем?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("mass_prune", $mod['mass_prune'] )
									     )      );
									     						     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может переключать 'видимость' тем?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("topic_q", $mod['topic_q'] )
									     )      );							     
									     	
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может переключать 'видимость' сообщений?</b>" ,
												  $this->ipsclass->adskin->form_yes_no("post_q", $mod['post_q'] )
									     )      );							     
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Дополнительные настройки" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может предупреждать других пользователей?</b><div class='desctext'>При включении этой опции модератор сможет придупреждать любого пользователя на всех форумах</div>" ,
												  $this->ipsclass->adskin->form_yes_no("allow_warn", $mod['allow_warn'] )
									     )      );							     
									     	
		//$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can edit user avatars and signatures?</b>" ,
		//										  $this->ipsclass->adskin->form_yes_no("edit_user", $mod['edit_user'] )
		//							     )      );
									   
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Может использовать мульти-модерацию?</b><br />".$this->ipsclass->adskin->js_help_link('mod_mmod', 'Важная информация' ) ,
												  $this->ipsclass->adskin->form_yes_no("can_mm", $mod['can_mm'] )
									     )      );						     
									     
		//-----------------------------------------
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form($button);
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();							     						     
	}
	
	
	
	/*-------------------------------------------------------------------------*/
	// REFINE MEMBER SEARCH
	/*-------------------------------------------------------------------------*/
	
	function moderator_add_preform()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$type = $this->ipsclass->input['name'] ? 'name' : 'group';
		$this->ipsclass->input['fid'] = preg_replace( "#^,#", "", $this->ipsclass->input['modforumids'] );
		
		//-----------------------------------------
		// Are we adding a group as a mod?
		//-----------------------------------------
		
		if ( $type == 'group' )
		{
			$this->mod_form();
			exit();
		}
		
		//-----------------------------------------
		// Got forums?
		//-----------------------------------------
		
		if ( ! $this->ipsclass->input['fid'] )
		{
			$this->ipsclass->main_msg = "Вы должны выбрать форум (несколько форумов) для добавления модератора!";
			$this->ipsclass->admin->redirect_noscreen( $this->ipsclass->base_url.'&section=content&act=forum' );
		}
		
		//-----------------------------------------
		// Else continue as normal.
		//-----------------------------------------
		
		if ( $this->ipsclass->input['name'] == "" )
		{
			$this->ipsclass->main_msg = "Вы должны ввести имя";
			$this->ipsclass->admin->redirect_noscreen( $this->ipsclass->base_url.'&section=content&act=forum' );
		}
		
		$this->ipsclass->DB->simple_construct( array( 'select' => 'id, members_display_name as name', 'from' => 'members', 'where' => "name LIKE '".$this->ipsclass->input['name']."%' OR members_display_name LIKE '".$this->ipsclass->input['name']."%'" ) );
		$this->ipsclass->DB->simple_exec();
		
		if (! $this->ipsclass->DB->get_num_rows() )
		{
			$this->ipsclass->main_msg = "Нет пользователей подходящих вашему запросу.";
			$this->ipsclass->admin->redirect_noscreen( $this->ipsclass->base_url.'&section=content&act=forum' );
		}
		
		//-----------------------------------------
		// Show possible matches
		//-----------------------------------------
		
		$form_array = array();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$form_array[] = array( $r['id'] , $r['name'] );
		}
		
		$this->ipsclass->admin->page_title = "Добавление модератора";
		
		$this->ipsclass->admin->page_detail = "Пожалуйста, найдите пользователя (или группу пользователей) для модерирования выбранных форумов.";
		
		//-----------------------------------------
		// Show form
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'add_final' ),
																			 2 => array( 'act'   , 'mod'    ),
																			 3 => array( 'fid'   , $this->ipsclass->input['fid']),
																			 4 => array( 'section', $this->ipsclass->section_code ),
																	)      );
		
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Поиск пользователя" );
		
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Введите начало имени или все имя пользователя:</b>" ,
												  							 $this->ipsclass->adskin->form_dropdown( "member_id", $form_array )
									     							)      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Найти пользователя");
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();
		
	}
	
	
	
	
}


?>
