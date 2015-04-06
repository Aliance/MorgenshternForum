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
|   > Func chgat
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



class func_chat
{

	var $class  = "";
	var $server = "";
	var $html   = "";
	
	function func_chat()
	{
		$this->server = str_replace( 'http://', '', $this->ipsclass->vars['chat04_whodat_server_addr'] );
	}
	
	//-----------------------------------------
	// register_class($class)
	//
	// Register a $this-> with this class 
	//
	//-----------------------------------------
	
	function register_class()
	{
		// NO LONGER NEEDED
	}

	//-----------------------------------------
	// Print online list
	//-----------------------------------------
	
	function get_online_list()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$member_ids         = array();
		$to_load            = array();
		
		// Let's use the new config vars if they are available, else revert to the legacy variable names
		$_hide_whoschatting = ( isset($this->ipsclass->vars['chat_hide_whoschatting']) ) ? $this->ipsclass->vars['chat_hide_whoschatting'] : $this->ipsclass->vars['chat04_hide_whoschatting'];
		$_who_on            = ( isset($this->ipsclass->vars['chat_who_on']) ) ? $this->ipsclass->vars['chat_who_on'] : $this->ipsclass->vars['chat04_who_on'];
		
		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( ! $_who_on )
		{
			return;
		}
		
		//-----------------------------------------
		// Sort and show :D
		//-----------------------------------------
		
		if ( is_array( $this->ipsclass->cache['chatting'] ) AND count( $this->ipsclass->cache['chatting'] ) )
		{
			foreach( $this->ipsclass->cache['chatting'] as $id => $data )
			{
				if ( $data['updated'] < ( time() - 120 ) )
				{
					continue;
				}
				
				$to_load[ $id ] = $id;
			}
		}
		
		//-----------------------------------------
		// Is this a root admin in disguise?
		// Is that kinda like a diamond in the rough?
		//-----------------------------------------
					
		$our_mgroups = array();
		
		if( $this->ipsclass->member['mgroup_others'] )
		{
			$our_mgroups = explode( ",", $this->ipsclass->clean_perm_string( $this->ipsclass->member['mgroup_others'] ) );
		}
		
		$our_mgroups[] = $this->ipsclass->member['mgroup'];		
		
		//-----------------------------------------
		// Got owt?
		//-----------------------------------------
		
		if ( count($to_load) )
		{
			$this->ipsclass->DB->build_query( array( 'select' => 'm.id, m.members_display_name, m.mgroup',
												     'from'   => array( 'members' => 'm' ),
												     'where'  => "m.id IN(".implode(",",$to_load).")",
	 												 'add_join' => array( 0 => array( 'select' => 's.login_type',
																					  'from'   => array( 'sessions' => 's' ),
																					  'where'  => 's.member_id=m.id',
																					  'type'   => 'left' ) ),
													 'order'  => 'm.members_display_name' ) );
			$this->ipsclass->DB->exec_query();
			
			while ( $m = $this->ipsclass->DB->fetch_row() )
			{
				$m['members_display_name'] = $this->ipsclass->make_name_formatted( $m['members_display_name'], $m['mgroup'] );
								
				if( $m['login_type'] )
				{
					if ( (in_array( $this->ipsclass->vars['admin_group'], $our_mgroups )) and ($this->ipsclass->vars['disable_admin_anon'] != 1) )
					{
						$member_ids[] = "<a href=\"{$this->ipsclass->base_url}showuser={$m['id']}\">{$m['members_display_name']}</a>";
					}
				}
				else
				{
					$member_ids[] = "<a href=\"{$this->ipsclass->base_url}showuser={$m['id']}\">{$m['members_display_name']}</a>";
				}
			}
		}		
		
		//-----------------------------------------
		// Got owt?
		//-----------------------------------------
		
		if ( count( $member_ids ) )
		{
			$final = implode( ",\n", $member_ids );
			
			$this->html = $this->ipsclass->compiled_templates['skin_boards']->whoschatting_show( intval(count($member_ids)), $final );
		}
		else
		{
			if ( ! $_hide_whoschatting )
			{
				$this->html = $this->ipsclass->compiled_templates['skin_boards']->whoschatting_empty();
			}
		}
		
		return $this->html;
	}





}



?>