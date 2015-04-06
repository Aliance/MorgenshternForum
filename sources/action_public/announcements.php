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
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-10-17 16:30:46 -0500 (Tue, 17 Oct 2006) $
|   > $Revision: 652 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > Announcements module
|   > Module written by Matt Mecham
|   > Date started: 29th March 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class announcements
{
	# Classes
	var $ipsclass;
	
    /*-------------------------------------------------------------------------*/
    // CONSTRUCTOR
    /*-------------------------------------------------------------------------*/
    
    function announcements()
    {
    
    }
    
    /*-------------------------------------------------------------------------*/
    // AUTO RUN
    /*-------------------------------------------------------------------------*/
    
    function auto_run()
    {
        $this->ipsclass->input['id'] = intval($this->ipsclass->input['id']);
        $this->ipsclass->input['f']  = intval($this->ipsclass->input['f']);
        
        if ( ! $this->ipsclass->input['id'] and ! $this->ipsclass->input['f'] )
        {
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
        
        //-----------------------------------------
		// Get the announcement
		//-----------------------------------------
		
		$this->ipsclass->DB->cache_add_query( 'ucp_get_all_announcements_byid', array( 'id' => $this->ipsclass->input['id'] ) );
		$this->ipsclass->DB->cache_exec_query();
		
		$announce = $this->ipsclass->DB->fetch_row();
		
		if ( ! $announce['announce_id'] or ! $announce['announce_forum'] )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
       
        //-----------------------------------------
		// Permission to see it?
		//-----------------------------------------
		
		$pass = 0;
		
		if ( $announce['announce_forum'] == '*' )
		{
			$pass = 1;
		}
		else
		{
			$tmp = explode( ",", $announce['announce_forum'] );
			
			if ( ! is_array( $tmp ) and ! ( count( $tmp ) ) )
			{
				$pass = 0;
			}
			else
			{
				foreach( $tmp as $id )
				{
					if ( $this->ipsclass->forums->forum_by_id[ $id ]['id'] )
					{
						$pass = 1;
						break;
					}
				}
			}
		}
		
		if ( $pass != 1 )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
         
        //-----------------------------------------
        // Mkay, get the require libraries
        //-----------------------------------------
        
        require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $parser                      =  new parse_bbcode();
        $parser->ipsclass            =& $this->ipsclass;
        $parser->allow_update_caches = 1;
        $parser->bypass_badwords     = intval($this->ipsclass->member['g_bypass_badwords']);
        
        require_once( ROOT_PATH.'sources/action_public/topics.php' );
        $topic = new topics();
        $topic->ipsclass =& $this->ipsclass;
        
        $topic->topic_init();
        
        $topic->topic['forum_id'] = 0;
        $topic->topic['tid']	  = 0;
        
    	//-----------------------------------------
    	// Parsey parsey!
    	//-----------------------------------------
        
        $member = $topic->parse_member( $announce );
        
		if ( $member['id'] )
		{
			$member['_members_display_name'] = "<a href='{$this->base_url}showuser={$member['id']}'>{$member['members_display_name_short']}</a>";
		}        
        
        //-----------------------------------------
		// Parse HTML tag on the fly
		//-----------------------------------------
		
		$parser->parse_smilies = 1;
		$parser->parse_html    = $announce['announce_html_enabled'];
		$parser->parse_bbcode  = 1;
		$parser->parse_nl2br   = 1;
		
		$announce['announce_post'] = $parser->pre_display_parse( $parser->pre_db_parse( $announce['announce_post'] ) );
		
		if ( $announce['announce_start'] and $announce['announce_end'] )
		{
			$announce['running_date'] = sprintf( $this->ipsclass->lang['announce_both'], gmdate( 'd.m.Y', $announce['announce_start'] ), gmdate( 'd.m.Y', $announce['announce_end'] ) );
		}
		else if ( $announce['announce_start'] and ! $announce['announce_end'] )
		{
			$announce['running_date'] = sprintf( $this->ipsclass->lang['announce_start'], gmdate( 'd.m.Y', $announce['announce_start'] ) );
		}
		else if ( ! $announce['announce_start'] and $announce['announce_end'] )
		{
			$announce['running_date'] = sprintf( $this->ipsclass->lang['announce_end'], gmdate( 'd.m.Y', $announce['announce_end'] ) );
		}
		else
		{
			$announce['running_date'] = '';
		}
		
		$this->output = $this->ipsclass->compiled_templates['skin_topic']->announcement_show($announce, $member);
		
		//-----------------------------------------
		// Show
		//-----------------------------------------
		
		if ( $this->ipsclass->input['f'] )
		{
			$this->nav = $this->ipsclass->forums->forums_breadcrumb_nav( $this->ipsclass->input['f'] );
		}
		
		//-----------------------------------------
		// Update hits
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'update' => 'announcements', 'set' => 'announce_views=announce_views+1', 'where' => "announce_id=".$this->ipsclass->input['id'] ) );
		$this->ipsclass->DB->simple_shutdown_exec();
		
		$this->ipsclass->print->add_output( $this->output );
        $this->ipsclass->print->do_output( array( 'TITLE'    => $this->ipsclass->vars['board_name']." -> ".$this->ipsclass->forums->forum_by_id[ $this->ipsclass->input['f'] ]['name'],
        					 	  'JS'       => 0,
        					 	  'NAV'      => $this->nav,
        				 )      );
        
    }
    
    /*-------------------------------------------------------------------------*/
    // REBUILD
    /*-------------------------------------------------------------------------*/
    
    function announce_retire_expired()
    {
	    
    	//-----------------------------------------
    	// Update all out of date 'uns
    	//-----------------------------------------
    	
    	$this->ipsclass->DB->do_update( 'announcements', array( 'announce_active' => 0 ), 'announce_end != 0 AND announce_end < '.time() );
    	
    	$this->announce_recache();
    }
    
    /*-------------------------------------------------------------------------*/
    // REBUILD
    /*-------------------------------------------------------------------------*/
    
    function announce_recache()
    {
    	$this->ipsclass->cache['announcements'] = array();
    	
    	$this->ipsclass->DB->cache_add_query( 'ucp_get_all_announcements', array() );
		$this->ipsclass->DB->cache_exec_query();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$start_ok = 0;
			$end_ok   = 0;
			
			if ( ! $r['announce_active'] )
			{
				continue;
			}
			
			if ( ! $r['announce_start'] )
			{
				$start_ok = 1;
			}
			else if ( $r['announce_start'] < time() )
			{
				$start_ok = 1;
			}
			
			if ( ! $r['announce_end'] )
			{
				$end_ok = 1;
			}
			else if ( $r['announce_end'] > time() )
			{
				$end_ok = 1;
			}
			
			if ( $start_ok and $end_ok )
			{
				$this->ipsclass->cache['announcements'][ $r['announce_id'] ] = array( 'announce_id'    => $r['announce_id'],
																					  'announce_title' => $r['announce_title'],
																					  'announce_start' => $r['announce_start'],
																					  'announce_end'   => $r['announce_end'],
																					  'announce_forum' => $r['announce_forum'],
																					  'announce_views' => $r['announce_views'],
																					  'member_id'      => $r['id'],
																					  'member_name'    => $r['members_display_name']
																					);
			}
		}
		
		$this->ipsclass->DB->obj['use_shutdown'] = 0;
		$this->ipsclass->update_cache( array( 'name' => 'announcements', 'array' => 1, 'deletefirst' => 1 ) );
    }
        
       
}

?>