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
|                  http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-10-05 11:04:08 -0500 (Thu, 05 Oct 2006) $
|   > $Revision: 609 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Func chat
|   > Script written by Matt Mecham
|   > Date started: 1st march 2002
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
    print "<h1>Неверный вход</h1> У Вас нет доступа к директиве этого файла. Если Вы проводили обновления, убедитесь, что не забыли обновить 'admin.php'.";
	exit();
}


class chat_functions
{

	var $class  = "";
	var $server = "";
	var $html   = "";
	
	function chat_functions()
	{
		$this->server = str_replace( 'http://', '', $this->ipsclass->vars['chat_server_addr'] );
	}
	
	//-----------------------------------------
	// register_class($class)
	//
	// Register a $this-> with this class 
	//
	//-----------------------------------------
	
	function register_class(&$class)
	{
		$this->class = $class;
	}

	//-----------------------------------------
	// Print online list
	//
	//-----------------------------------------
	
	function get_online_list()
	{
		if ( ! $this->ipsclass->vars['chat_who_on'] )
		{
			return;
		}
		
		//-----------------------------------------
		// Get details from the DB
		//-----------------------------------------
		
		$row = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='chatstat'" ) );
		
		list( $hits, $time ) = explode( '&', $row['cs_extra'] );
		
		//-----------------------------------------
		// Do we need to update?
		//-----------------------------------------
		
		$final = "";
		$time_is_running_out = time();
		$member_ids = array();
		$new        = array();
		
		if ( $time < time() - ( $this->ipsclass->vars['chat_who_save'] * 60 ) )
		{
			$server_url = 'http://'.$this->server.'/ipc_who.pl?id='.$this->ipsclass->vars['chat_account_no'].'&pw='.$this->ipsclass->vars['chat_pass_md5'];
			
			if ( $data = @file( $server_url ) )
			{
				if ( count($data) > 0 )
				{
					$hits_left = array_shift($data);
				}
				
				foreach( $data as $t )
				{
					$t = strtolower(trim($t));
					$t = str_replace( '_', ' ', $t );
					$t = str_replace( '"', '&quot;', $t );
					
					$new[] = $t;
				}
				
				$name_string = implode( '","', $new );
				
				if ( count($new) > 0 )
				{ 
					$this->ipsclass->DB->build_query( array( 'select' => 'id, members_display_name, mgroup',
															 'from'   => 'members',
															 'where'  => "members_l_username IN (\"".$name_string."\")",
															 'order'  => 'members_display_name' ) );
					$this->ipsclass->DB->exec_query
					
					while ( $m = $this->ipsclass->DB->fetch_row() )
					{
						$m['members_display_name'] = $this->ipsclass->make_name_formatted( $m['members_display_name'], $m['mgroup'] );
						
						$member_ids[] = "<a href=\"{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}?showuser={$m['id']}\">{$m['members_display_name']}</a>";
					}
					
					$final = implode( ",\n", $member_ids );
					
					$final .= '|&|'.intval(count($member_ids));
				}
				
				$this->ipsclass->DB->do_update( 'cache_store', array( 'cs_value' => addslashes($final), 'cs_extra' => "{$hits_left}&{$time_is_running_out}" ), 'where' => "cs_key='chatstat'" );
				
				$row['cs_value'] = $final;
			}
		}
		
		//-----------------------------------------
		// Any members to show?
		//-----------------------------------------
		
		$this->ipsclass->vars['chat_height'] += $this->ipsclass->vars['chat_poppad'] ? $this->ipsclass->vars['chat_poppad'] : 50;
		$this->ipsclass->vars['chat_width']  += $this->ipsclass->vars['chat_poppad'] ? $this->ipsclass->vars['chat_poppad'] : 50;
		
		$chat_link = ( $this->ipsclass->vars['chat_display'] == 'self' )
				   ? $this->class->html->whoschatting_inline_link()
				   : $this->class->html->whoschatting_popup_link();
		
		list ($names, $count) = explode( '|&|', $row['cs_value'] );
		
		if ( $count > 0 )
		{
			$txt = sprintf( $this->ipsclass->lang['whoschatting_delay'], $this->ipsclass->vars['chat_who_save'] );
			$this->html = $this->class->html->whoschatting_show( intval($count), stripslashes($names), $chat_link, $txt );
		}
		else
		{
			if ( ! $this->ipsclass->vars['chat_hide_whoschatting'] )
			{
				$this->html = $this->class->html->whoschatting_empty($chat_link);
			}
		}
		
		return $this->html;
				
	}
}
?>