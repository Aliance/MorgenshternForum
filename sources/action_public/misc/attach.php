<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.1.7
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Wednesday 27th of September 2006 08:13:32 AM
|   Release: 2871a4c8b602386260eeb8bf9da57e29
|   Licence Info: http://www.invisionboard.com/?license
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2005-10-24 22:17:00 +0100 (Mon, 24 Oct 2005) $
|   > $Revision: 64 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > Attachment Handler module
|   > Module written by Matt Mecham
|   > Date started: 10th March 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class attach {

	/*-------------------------------------------------------------------------*/
	//
	// AUTO RUN
	//
	/*-------------------------------------------------------------------------*/
	
    function auto_run()
    {
		$this->ipsclass->input['id']  = intval($this->ipsclass->input['id']);
        $this->ipsclass->input['tid'] = intval($this->ipsclass->input['tid']);
        
        //-----------------------------------------
		// Got attachment types?
		//-----------------------------------------
		
		$this->ipsclass->cache['attachtypes'] = array();
			
		$this->ipsclass->DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$this->ipsclass->DB->simple_exec();
	
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}
		
		//-----------------------------------------
		// What to do..
		//-----------------------------------------
		
        switch( $this->ipsclass->input['code'] )
        {
        	case 'showtopic':
        		$this->show_topic_attachments();
        		break;
        	default:
        		$this->show_post_attachment();
        		break;
        }
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// SHOW TOPIC ATTACHMENTS ( MULTIPLE )
	//
	/*-------------------------------------------------------------------------*/
	
	function show_topic_attachments()
	{
		if ( ! $this->ipsclass->input['tid'] )
        {
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
        
        //-----------------------------------------
        // get topic..
        //-----------------------------------------
        
        $topic = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'topics', 'where' => 'tid='.$this->ipsclass->input['tid'] ) );
        
        if ( ! $topic['topic_hasattach'] )
        {
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
        
        //-----------------------------------------
        // Check forum..
        //-----------------------------------------
        
        if ( ! $this->ipsclass->forums->forum_by_id[ $topic['forum_id'] ] )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}
		
		//-----------------------------------------
		// Get forum skin and lang
		//-----------------------------------------
		
		$this->ipsclass->load_language('lang_forum');
		$this->ipsclass->load_language('lang_topic');
		
        $this->ipsclass->load_template('skin_forum');
		
		//-----------------------------------------
		// aight.....
		//-----------------------------------------
		
		$this->output .= $this->ipsclass->compiled_templates['skin_forum']->forums_attachments_top($topic['title']);
		
		$this->ipsclass->DB->cache_add_query( 'forum_get_attachments', array( 'tid' => $this->ipsclass->input['tid'] ) );
    	
    	$this->ipsclass->DB->cache_exec_query();
    	
		while ( $row = $this->ipsclass->DB->fetch_row() )
		{
			if ( $this->ipsclass->check_perms($this->ipsclass->forums->forum_by_id[ $row['forum_id'] ]['read_perms']) != TRUE )
			{
				continue;
			}
			
			$row['image']       = $this->ipsclass->cache['attachtypes'][ $row['attach_ext'] ]['atype_img'];
			
			$row['short_name']  = $this->ipsclass->txt_truncate( $row['attach_file'], 30 );
															  
			$row['attach_date'] = $this->ipsclass->get_date( $row['attach_date'], 'SHORT' );
			
			$row['real_size']   = $this->ipsclass->size_format( $row['attach_filesize'] );
			
			$this->output .= $this->ipsclass->compiled_templates['skin_forum']->forums_attachments_row( $row );
		}
		
		$this->output .= $this->ipsclass->compiled_templates['skin_forum']->forums_attachments_bottom();
		
		$this->ipsclass->print->pop_up_window($this->ipsclass->lang['attach_title'], $this->output);
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// SHOW POST ATTACHMENT ( SINGLE )
	//
	/*-------------------------------------------------------------------------*/
	
	function show_post_attachment()
	{
		if ( ! $this->ipsclass->input['id'] )
        {
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
        
        //-----------------------------------------
        // get attachment
        //-----------------------------------------
        
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_id=".$this->ipsclass->input['id'] ) );
        $this->ipsclass->DB->simple_exec();
        
        if ( ! $attach = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
		}
        
        //-----------------------------------------
        // Handle post attachments.
        //-----------------------------------------
        
        if ( $this->ipsclass->input['type'] == 'post' )
        {
        	//-----------------------------------------
        	// TheWalrus inspired fix for previewing
        	// the post and clicking the attachment...
        	//-----------------------------------------
        		
        	if ( $attach['attach_pid'] == 0 AND $attach['attach_member_id'] == $this->ipsclass->member['id'] )
        	{
        		# We're OK (Further checking, maybe post key?
        	}
        	else
        	{
        		//-----------------------------------------
        		// Get post thingy majiggy to check perms
        		//-----------------------------------------
        	
        		$this->ipsclass->DB->cache_add_query( 'attach_get_perms', array( 'apid' => $attach['attach_pid'] ) );
        		$this->ipsclass->DB->cache_exec_query();
        	
				if ( ! $post = $this->ipsclass->DB->fetch_row() )
				{
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
				}
				
				if ( ! $this->ipsclass->forums->forum_by_id[ $post['forum_id'] ] )
				{
					//-----------------------------------------
					// TheWalrus inspired fix for previewing
					// the post and clicking the attachment...
					//-----------------------------------------
					
					if ( $attach['attach_pid'] == 0 AND $attach['attach_member_id'] == $this->ipsclass->member['id'] )
					{
						# We're ok.
					}
					else
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
					}
				}
				
				if ( $this->ipsclass->check_perms($this->ipsclass->forums->forum_by_id[ $post['forum_id'] ]['read_perms']) == FALSE )
				{
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
				}
			}
        }
        else if ( $this->ipsclass->input['type'] == 'msg' and $attach['attach_msg'] )
        {
        	$this->ipsclass->DB->simple_construct( array( 'select' => 'mt_id, mt_owner_id', 'from' => 'message_topics', 'where' => 'mt_owner_id='.$this->ipsclass->member['id'].' AND mt_msg_id='.$attach['attach_msg'] ) );
        	$this->ipsclass->DB->simple_exec();
        	
        	if ( ! $post = $this->ipsclass->DB->fetch_row() )
        	{
        		$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
			}
			
        }
        else
        {
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
        
        //-----------------------------------------
        // Show attachment
        //-----------------------------------------
        
        $file = $this->ipsclass->vars['upload_dir']."/".$attach['attach_location'];
        	
		if ( file_exists( $file ) and ( $this->ipsclass->cache['attachtypes'][ $attach['attach_ext'] ]['atype_mimetype'] != "" ) )
		{
			//-----------------------------------------
			// Update the "hits"..
			//-----------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'update' => 'attachments', 'set' =>"attach_hits=attach_hits+1", 'where' => "attach_id=".$this->ipsclass->input['id'] ) );
			$this->ipsclass->DB->simple_exec();
			
			//-----------------------------------------
			// If this is a TXT / HTML file, force an
			// odd extension to prevent IE from opening
			// it inline.
			//-----------------------------------------
			
			$file_extension = preg_replace( "#^.*\.(.+?)$#s", "\\1", $attach['attach_file'] );
			$safe_array     = array( 'txt', 'html', 'htm' );
			
			if ( in_array( strtolower($file_extension), $safe_array ) )
			{
				//$attach['attach_file'] .= '-rename';
			}
			
			//-----------------------------------------
			// Set up the headers..
			//-----------------------------------------
			
			header( "Content-Type: ".$this->ipsclass->cache['attachtypes'][ $attach['attach_ext'] ]['atype_mimetype'] );
			header( "Content-Disposition: inline; filename=\"".$attach['attach_file']."\"" );
			header( "Content-Length: ".(string)(filesize( $file ) ) );
			
			//-----------------------------------------
			// Open and display the file..
			//-----------------------------------------
			
			$fh = fopen( $file, 'rb' );  // 2871a4c8b602386260eeb8bf9da57e29, Set binary for Win even if it's an ascii file, it won't hurt.
			fpassthru( $fh );
			@fclose( $fh );
			exit();
		}
		else
		{
			//-----------------------------------------
			// File does not exist..
			//-----------------------------------------
			
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
		}
        
    }
        
       
}

?>