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
|   > $Date: 2006-08-01 17:02:55 +0100 (Tue, 01 Aug 2006) $
|   > $Revision: 425 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Personal Profile Portal Class: Topics
|   > Module written by Matt Mecham
|   > Date started: 2nd August 2006
|
+--------------------------------------------------------------------------
*/

/**
* Main content
*
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class profile_topics
{
	/**
	* Global IPSCLASS
	* @var	object
	*/
	var $ipsclass;
	
	/*-------------------------------------------------------------------------*/
	// Return data
	/*-------------------------------------------------------------------------*/
	
	/**
	* Returns a block of HTML back to the ajax handler
	* which then replaces the inline content with the HTML
	* returned.
	*
	*/
	function return_html_block( $member=array() ) 
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$content   = '';
		$last_x    = 5;
		$forum_ids = array();
		
		//-----------------------------------------
		// Skin set loaded?
		//-----------------------------------------
		
		if ( ! is_object( $this->ipsclass->compiled_templates['skin_profile'] ) )
		{
			$this->ipsclass->load_template( 'skin_profile' );
		}
		
		//-----------------------------------------
		// Got a member?
		//-----------------------------------------
		
		if ( ! is_array( $member ) OR ! count( $member ) )
		{
			return '';
		}
		
		//-----------------------------------------
		// Allowed forums...
		//-----------------------------------------
		
		$forum_ids = $this->ipsclass->forums->forums_get_all_allowed_forum_ids( 'read_perms' );
		
		//-----------------------------------------
		// Remove trash can...
		//-----------------------------------------
		
		if ( $this->ipsclass->vars['forum_trash_can_id'] )
		{
			unset( $forum_ids[ $this->ipsclass->vars['forum_trash_can_id'] ] );
		}
		
		//-----------------------------------------
		// Check.. Again...
		//-----------------------------------------
		
		if ( ! is_array( $forum_ids ) OR ! count( $forum_ids ) )
		{
			return $this->ipsclass->lang['err_no_posts_to_show'];
		}
		
		//-----------------------------------------
		// Load parser
		//-----------------------------------------
		
		require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $parser                      = new parse_bbcode();
        $parser->ipsclass            = $this->ipsclass;
        $parser->allow_update_caches = 0;
        
        $parser->bypass_badwords = intval( $this->ipsclass->member['g_bypass_badwords'] );
		
		//-----------------------------------------
		// Get last X posts
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select'   => 't.*',
		  										 'from'     => array( 'topics' => 't' ),
		  										 'where'    => 't.starter_id='.$member['id'].' AND t.forum_id IN(' .  implode( ',', $forum_ids ). ') AND t.approved=1',
												 'order'    => 't.start_date DESC',
												 'limit'    => array( 0, $last_x ),
												 'add_join' => array( 0 => array(
																				  'select' => 'p.*',
																				  'from'   => array( 'posts' => 'p' ),
																				  'where'  => 't.topic_firstpost=p.pid',
																				  'type'   => 'inner' ) ) ) ) ;
			
		$o = $this->ipsclass->DB->exec_query();
		
		while( $row = $this->ipsclass->DB->fetch_row( $o ) )
		{
			if ( ! $this->ipsclass->member['view_img'] )
			{
				//-----------------------------------------
				// unconvert smilies first, or it looks a bit crap.
				//-----------------------------------------
				
				$row['post'] = preg_replace( "#<!--emo&(.+?)-->.+?<!--endemo-->#", "\\1" , $row['post'] );
				
				$row['post'] = preg_replace( "/<img src=[\"'](.+?)[\"'].+?".">/", "(IMG:<a href='\\1' target='_blank'>\\1</a>) ", $row['post'] );
			}

			$row['_post_date']  = $this->ipsclass->get_date( $row['post_date'], 'SHORT' );
			$row['_date_array'] = $this->ipsclass->date_getgmdate( $row['post_date'] + $this->ipsclass->get_time_offset() );
			
			$parser->parse_html  = ( $this->ipsclass->cache['forum_cache'][ $row['forum_id'] ]['use_html'] and $this->ipsclass->cache['group_cache'][ $member['mgroup'] ]['g_dohtml'] and $row['post_htmlstate'] ) ? 1 : 0;
			$parser->parse_nl2br = $row['post_htmlstate'] == 2 ? 1 : 0;

			$row['post'] = $parser->pre_display_parse( $row['post'] );
			
			$row['post'] .= "\n<!--IBF.ATTACHMENT_". $row['pid']. "-->";
			
			//-----------------------------------------
			// ATTACHMENTS
			//-----------------------------------------

			if ( $row['topic_hasattach'] )
			{
				if ( ! is_object( $this->class_attach ) )
				{
					//-----------------------------------------
					// Grab render attach class
					//-----------------------------------------

					require_once( ROOT_PATH . 'sources/classes/attach/class_attach.php' );
					$this->class_attach           =  new class_attach();
					$this->class_attach->ipsclass =& $this->ipsclass;
					
					$this->ipsclass->load_template( 'skin_topic' );
				}
				
				//-----------------------------------------
				// Not got permission to view downloads?
				//-----------------------------------------
				
				if ( $this->ipsclass->check_perms($this->ipsclass->forums->forum_by_id[ $row['forum_id'] ]['download_perms']) === FALSE )
				{
					$this->ipsclass->vars['show_img_upload'] = 0;
				}						
				
				$this->class_attach->type  = 'post';
				$this->class_attach->init();

				$row['post'] = $this->class_attach->render_attachments( $row['post'], array( $row['pid'] => $row['pid'] ) );
			}
			
			$content .= $this->ipsclass->compiled_templates['skin_profile']->personal_portal_single_column( $row, $this->ipsclass->lang['profile_read_topic'], $this->ipsclass->base_url.'act=findpost&amp;pid='.$row['pid'], $row['title'] );
		}
		
		if ( stristr( $content, "[attachmentid=" ) )
		{
			$content = preg_replace( "#\[attachmentid=(\d+)\]#is", "", $content );
		}		
		
		//-----------------------------------------
		// Macros...
		//-----------------------------------------
		
		if ( ! is_array( $this->ipsclass->skin['_macros'] ) OR ! count( $this->ipsclass->skin['_macros'] ) )
    	{
    		$this->ipsclass->skin['_macros'] = unserialize( stripslashes($this->ipsclass->skin['_macro']) );
    	}
		
		if ( is_array( $this->ipsclass->skin['_macros'] ) )
      	{
			foreach( $this->ipsclass->skin['_macros'] as $row )
			{
				if ( $row['macro_value'] != "" )
				{
					$content = str_replace( "<{".$row['macro_value']."}>", $row['macro_replace'], $content );
				}
			}
		}
		
		$content = str_replace( "<#IMG_DIR#>", $this->ipsclass->skin['_imagedir'], $content );
		$content = str_replace( "<#EMO_DIR#>", $this->ipsclass->skin['_emodir']  , $content );
		
		//-----------------------------------------
		// Return content..
		//-----------------------------------------
		
		return $content ? $content : $this->ipsclass->compiled_templates['skin_profile']->personal_portal_no_content( 'err_no_posts_to_show' );
	}
	
}


?>