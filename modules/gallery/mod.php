<?php
/*
+--------------------------------------------------------------------------
|   Invision Gallery Module v<#VERSION#>
|   ========================================
|   by Adam Kinder
|   (c) 2001 - 2005 Invision Power Services
|   ========================================
|   
|   Nullfied by SneakerXZ
|   
+---------------------------------------------------------------------------
*/


/**
* Main/Moderation
*
* Moderation stuffs.  Edit, pin, unpin, launch ze missiles
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		2.0
*/

class mod
{
        var $ipsclass;
        var $glib;
	var $output;
	var $info;
	var $html;

	/**
	* mod::start()
	*
	* Begins execution of this module, $param is used as an entry point into the
	* module.
	*
	* @param string $param
	* @return none
	**/
	function start( $param="" )
	{
		/**
		* What's our entry point?
		**/
		switch( $param )
		{
			case 'delimg':
				$this->ipsclass->input['img'] = $this->glib->validate_int( $this->ipsclass->input['img'] );
				$this->delete_image( $this->ipsclass->input['img'] );
			break;

			case 'delreply':
				$this->ipsclass->input['comment'] = $this->glib->validate_int( $this->ipsclass->input['comment'] );
				$this->delete_reply( $this->ipsclass->input['comment'] );
			break;

			case 'moveimg':
				$this->ipsclass->input['img']      = $this->glib->validate_int( $this->ipsclass->input['img'] );
				$this->ipsclass->input['category'] = $this->glib->validate_int( $this->ipsclass->input['category'] );

				// Move the image or show the form
				if( $this->ipsclass->input['op'] == 'domove')
				{
					$this->process_move( $this->ipsclass->input['img'], $this->ipsclass->input['category'] );
				}
				else
				{
					$this->move_form( $this->ipsclass->input['img'] );
				}
			break;
			
			case 'pin':
				$this->pin_image( $this->glib->validate_int( $this->ipsclass->input['img'] ), 1 );
			break;
			
			case 'unpin':
				$this->unpin_image( $this->glib->validate_int( $this->ipsclass->input['img'] ), 1 );
			break;			

			case 'multi':
				$this->multi_mod();
			break;
			
			case 'reportimage':
				$this->ipsclass->input['img'] = $this->glib->validate_int( $this->ipsclass->input['img'] );
				$this->report_form( $this->ipsclass->input['img'] );
			break;
			
			case 'doreportimg':
				$this->ipsclass->input['img'] = $this->glib->validate_int( $this->ipsclass->input['img'] );
				$this->do_report_img( $this->ipsclass->input['img'] );
			break;
			
			case 'reportcomment':
				$this->ipsclass->input['comment'] = $this->glib->validate_int( $this->ipsclass->input['comment'] );
				$this->comment_report_form( $this->ipsclass->input['comment'] );
			break;
			
			case 'doreportcomment':
				$this->ipsclass->input['comment'] = $this->glib->validate_int( $this->ipsclass->input['comment'] );
				$this->do_report_comment( $this->ipsclass->input['comment'] );
			break;			
		}

		$this->ipsclass->print->add_output( $this->output );

		$this->ipsclass->print->do_output( array(
		'TITLE'    => $this->title,
		'NAV'      => $this->nav,
		)       );
	}

	/**
	* register_class()
	*
	* Register the parent clss
	*
	* @param object $class
	* @return none
	**/
	function register_class( &$class )
	{
		$this->class =& $class;
	}

	/**
	* do_report_img()
	*
	* Sends the image report
	*
	* @param integer $img
	* @return none
	**/	
	function do_report_img( $img )
	{		
		/* Make sure we have an image id */
		if( ! $img )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}		
		
		/* Make sure the form was completed */
		if( empty( $this->ipsclass->input['message'] ) )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );	
		}

		/* Get the image and category info */
		$image = $this->glib->get_image_info( $img );
		$cat   = ( !$image['category_id'] ) ? $this->glib->get_album_info( $image['album_id'] ) : $this->glib->get_category_info( $image['category_id'] );

		/* Make sure we can view this category */
		if( ! $this->ipsclass->check_perms( $cat['perms_images'] ) && $image['category_id'] )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );		
		}
		
		/* Get a list of moderator masks */
		
		/*
		* TODO: Offload this when reworking permissions for 2.1 */
		if( $image['category_id'] )
		{
			/*
			* In a category, grab mods */
			$mods = explode( ',', $cat['perms_moderate'] );
			
			/* Format SQL Query */
			foreach( $mods as $mod )
			{
				$q[] = "$mod IN ( g_perm_id )";
			}
			$q = implode( " OR ", $q );
		}
		else
		{
			/*
			* In an album, grab any groups that have album mod perms */
			$q = "g_mod_albums = 1 OR g_id = {$this->ipsclass->vars['admin_group']}";
		}
		
		/*
		* Grab groups */
		$this->ipsclass->DB->simple_construct( array( 'select' => 'g_id', 'from' => 'groups', 'where' => $q ) );
		$this->ipsclass->DB->simple_exec();
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$mod_groups[] = $i['g_id'];
		}
		
		/* Query for members */
		$this->ipsclass->DB->simple_construct( array( 'select' => 'id, email, name', 'from' => 'members', 'where' => "mgroup IN ( ".implode( ",",$mod_groups )." )" ) );
		$this->ipsclass->DB->simple_exec();
		
		$mods = array();
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$mods[] = $i;
		}
		/* Get needed modules */
            require KERNEL_PATH . "class_email.php";		
		    $this->email = new class_email();
		    $this->email->html_email = 1;
		    $this->email->ipsclass = &$this->ipsclass;
		
		require_once( ROOT_PATH.'sources/lib/func_msg.php' );
		$this->lib = new func_msg();
                $this->lib->ipsclass =& $this->ipsclass;
		$this->lib->init();
		
		/* Loop through the mods */
		$report = trim( stripslashes( $this->ipsclass->input['message'] ) );

		foreach( $mods as $data )
		{
			$message = $this->ipsclass->lang['report_img_email'];

			$message = str_replace( '<#MODNAME#>'   , $data['name']            , $message );
			$message = str_replace( '<#SENDERNAME#>', $this->ipsclass->member['name'], $message );		
			$message = str_replace( '<#CAPTION#>'   , $image['caption']        , $message );
			$message = str_replace( '<#LINK#>'      , "<a href='{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}"."?automodule=gallery&amp;cmd=si&amp;img=$img'>{$this->ipsclass->lang['link_to_image']}</a>", $message );			
			$message = str_replace( '<#REPORT#>'    , $report                  , $message );
			
			$subject = $this->ipsclass->lang['report_img_page'].' '.$this->ipsclass->vars['board_name'];
						
			if( $this->ipsclass->var['gallery_send_report'] == 'email' )
			{
				$this->email->to      = $data['email'];
				$this->email->from		= $this->ipsclass->member['email'];
				$this->email->subject	= $subject;
				$this->email->message	= $message;
				$this->email->send_mail();				
			}
			else
			{
				$this->lib->to_by_id    = $data['id'];
 				$this->lib->from_member = $this->ipsclass->member;
 				$this->lib->msg_title   = $this->ipsclass->lang['report_img_page'].' '.$image['caption'];
 				$this->lib->msg_post    = $message;
				$this->lib->force_pm    = 1;
				
				$this->lib->send_pm();
				
				if ( $this->lib->error )
				{
					print $this->error;
					exit();
				}				
			}
		}
		
		$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['report_redirect_image'], "automodule=gallery&amp;cmd=si&amp;img=$img" );		
	}

	/**
	* report_form()
	*
	* Displays the form for reporting
	*
	* @param integer $img
	* @return none
	**/	
	function report_form( $img )
	{		
		/* Load Skin Bits */
		
		/* Fatal error bug fix */
        if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_img'] ) ) {
			$this->ipsclass->load_template( 'skin_gallery_img' );
        }
		$this->html = $this->ipsclass->compiled_templates['skin_gallery_img'];
		
		/* Get the image */
		$data = $this->glib->get_image_info( $img );
						
		$this->output .= $this->html->report_form( $img, $data['caption'] );
		
        $this->nav[] = "<a href='".$this->ipsclass->base_url."automodule=gallery'>{$this->ipsclass->lang['gallery']}</a>";
        $this->nav[] = $this->ipsclass->lang['report_img_page'];
        
        $this->title = $this->ipsclass->lang['report_img_page'];
				
	}
	
	/**
	* do_report_comment()
	*
	* Sends the comment report
	*
	* @param integer $img
	* @return none
	**/	
	function do_report_comment( $comment )
	{	
		/* Make sure we have an image id */
		if( ! $comment )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}		
		
		/* Make sure the form was completed */
		if( empty( $this->ipsclass->input['message'] ) )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );	
		}

		/* Get the image and category info */
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_comments', 'where' => "pid={$comment}" ) );
		$this->ipsclass->DB->simple_exec();
		$comment = $this->ipsclass->DB->fetch_row();
		
				/* Get the image and category info */
		$image = $this->glib->get_image_info( $comment['img_id'] );
		$cat   = ( !$image['category_id'] ) ? $this->glib->get_album_info( $image['album_id'] ) : $this->glib->get_category_info( $image['category_id'] );

		/* Make sure we can view this category */
		if( ! $this->ipsclass->check_perms( $cat['perms_images'] ) && $image['category_id'] )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );		
		}
		
		/* Get a list of moderator masks */
		
		/*
		* TODO: Offload this when reworking permissions for 2.1 */
		if( $image['category_id'] )
		{
			/*
			* In a category, grab mods */
			$mods = explode( ',', $cat['perms_moderate'] );
			
			/* Format SQL Query */
			foreach( $mods as $mod )
			{
				$q[] = "$mod IN ( g_perm_id )";
			}
			$q = implode( " OR ", $q );
		}
		else
		{
			/*
			* In an album, grab any groups that have album mod perms */
			$q = "g_mod_albums = 1 OR g_id = {$this->ipsclass->vars['admin_group']}";
		}

		/* Query for groups */
		$this->ipsclass->DB->simple_construct( array( 'select' => 'g_id', 'from' => 'groups', 'where' => $q ) );
		$this->ipsclass->DB->simple_exec();
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$mod_groups[] = $i['g_id'];	
		}
		
		/* Query for members */
		$this->ipsclass->DB->simple_construct( array( 'select' => 'id, email, name', 'from' => 'members', 'where' => "mgroup IN ( ".implode( ",",$mod_groups )." )" ) );
		$this->ipsclass->DB->simple_exec();
		
		$mods = array();
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$mods[] = $i;
		}
		/* Get needed modules */
         require KERNEL_PATH . "class_email.php";		
		    $this->email = new class_email();
		    $this->email->html_email = 1;
		    $this->email->ipsclass = &$this->ipsclass;
		
		require_once( ROOT_PATH.'sources/lib/func_msg.php' );
		$this->lib = new func_msg();
                $this->lib->ipsclass =& $this->ipsclass; 
		$this->lib->init();
		
		/* Loop through the mods */
		$report = trim( stripslashes( $this->ipsclass->input['message'] ) );
		$st = $this->ipsclass->input['st'];
		foreach( $mods as $data )
		{
			$message = $this->ipsclass->lang['report_comment_email'];

			$message = str_replace( '<#MODNAME#>'   , $data['name']            , $message );
			$message = str_replace( '<#SENDERNAME#>', $this->ipsclass->member['name'], $message );		
			$message = str_replace( '<#LINK#>'      , "<a href='{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}"."?automodule=gallery&cmd=si&img={$comment['img_id']}&st={$st}#{$comment['pid']}'>{$this->ipsclass->lang['link_to_comment']}</a>", $message );			
			$message = str_replace( '<#REPORT#>'    , $report                  , $message );
			
			$subject = $this->ipsclass->lang['report_img_page'].' '.$this->ipsclass->vars['board_name'];
						
			if( $this->ipsclass->var['gallery_send_report'] == 'email' )
			{
				$this->email->to      = $data['email'];
				$this->email->from		= $this->ipsclass->member['email'];
				$this->email->subject	= $subject;
				$this->email->message	= $message;
				$this->email->send_mail();			
			}
			else
			{
				$this->lib->to_by_id    = $data['id'];
 				$this->lib->from_member = $this->ipsclass->member;
 				$this->lib->msg_title   = $this->ipsclass->lang['report_comment_page'];
 				$this->lib->msg_post    = $message;
				$this->lib->force_pm    = 1;
				
				$this->lib->send_pm();
				
				if ( $this->lib->error )
				{
					print $this->error;
					exit();
				}				
			}
		}
		
		$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['report_redirect_comment'], "automodule=gallery&amp;cmd=si&amp;img={$comment['img_id']}" );		
	}	
	
	/**
	* comment_report_form()
	*
	* Displays the form for reporting
	*
	* @param integer $img
	* @return none
	**/	
	function comment_report_form( $comment )
	{	
		/* Load Skin Bits */
		
		/* Fatal error bug fix */
        if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_comments'] ) ) {
			$this->ipsclass->load_template( 'skin_gallery_comments' );
        }
		$this->html = $this->ipsclass->compiled_templates['skin_gallery_comments'];
		
		/* Get the image */
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_comments', 'where' => "pid={$comment}" ) );
		$this->ipsclass->DB->simple_exec();
		$data = $this->ipsclass->DB->fetch_row();
		$data['st'] = ( !empty( $this->ipsclass->input['st'] ) ) ? $this->ipsclass->input['st'] : 0;
	
						
		$this->output .= $this->html->report_comment_form( $data );
		
        $this->nav[] = "<a href='".$this->ipsclass->base_url."automodule=gallery'>{$this->ipsclass->lang['gallery']}</a>";
        $this->nav[] = $this->ipsclass->lang['report_comment_page'];
        
        $this->title = $this->ipsclass->lang['report_comment_page'];
				
	}	
	
	/**
	* multi_mod()
	*
	* Moderate multiple images
	*
	* @param integer $img
	* @return none
	**/
	function multi_mod()
	{		
		/**
		* Security Check
		**/
		$can_del = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_del = 1;
		}
		else
		{
			if( ( $this->img['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_del_own'] ) || $this->ipsclass->member['g_mod_albums'] )
			{
				$can_del = 1;
			}
		}
		
		/**
		* What kind of multi moderation are we doing?
		**/
		if( $this->ipsclass->input['type'] != 'comment' )
		{
			if( empty( $this->ipsclass->input['selectedimgids'] ) )  {
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'gallery_noimgids') );
			}
			$img_ids = explode( ",", $this->ipsclass->input['selectedimgids'] );

			foreach( $img_ids as $img )
			{
				$img = $this->glib->validate_int( $img );
				switch( $this->ipsclass->input['galleryact'] )
				{
					case 'approve':
						$this->approve_image( $img );
					break;
				
					case 'unapprove':
						$this->decline_image( $img );
					break;
				
					case 'delete':
						$this->delete_image( $img, 0 );
					break;
					
					case 'pin':
						$this->pin_image( $img, 0 );
					break;
				
					case 'unpin':
						$this->unpin_image( $img, 0 );
					break;	
					
					case 'move':
						$this->process_move( $img, $this->ipsclass->input['move_cat'], true );
				 	break;		
				}
			}
			
			/**
			* Redirect
			**/
			if ( $this->ipsclass->input['cat'] )
			{
				$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['inline_mod'], "automodule=gallery&amp;cmd=sc&amp;cat={$this->ipsclass->input['cat']}" );
			}
			else 
			{
				$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['inline_mod'], "automodule=gallery&amp;cmd=si&amp;img={$this->ipsclass->input['img']}" );
			}
		}
		else
		{
			if( empty( $this->ipsclass->input['selectedgcids'] ) )  
			{
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'gallery_nogcids') );
			}
			
			$cids = explode( ",", $this->ipsclass->input['selectedgcids'] );
			$type = ( !empty( $this->ipsclass->input['cat'] ) ) ? 'cat' : 'album';
			foreach( $cids as $cid )
			{
				$cid = $this->glib->validate_int( $cid );
				switch( $this->ipsclass->input['galleryact'] )
				{
					case 'delcomments':
						$this->delete_reply( $cid, 0 );
					break;
					
					case 'approvecomments':
						$this->approve_comment( $cid, $this->glib->validate_int( $this->ipsclass->input['img'] ), $type );
					break;
					
					case 'unapprovecomments':
						$this->unapprove_comment( $cid, $this->glib->validate_int( $this->ipsclass->input['img'] ), $type );
					break;
				}	
			}
			
			/**
			* Redirect
			**/
			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['inline_mod'], "automodule=gallery&amp;cmd=si&amp;img={$this->ipsclass->input['img']}#comments" );		
		}
	}
	
	/**
	* unapprove_comment()
	*
	* Unapproves a comment
	*
	* @since 2.0.3
	**/		
	function unapprove_comment( $cid, $img, $type )
	{	
		/**
		* Get the category/album and image information
		**/
		$this->img = $this->glib->get_image_info( $img );
		if( $type == 'cat' ) {
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
			$this->ipsclass->DB->simple_update( 'gallery_categories', 'comments=comments-1', "id={$this->category['id']}" , 1 ); 
			$this->ipsclass->DB->simple_exec();
		}
		if( $type == 'album' )  {
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
			$this->ipsclass->DB->simple_update( 'gallery_albums', 'comments=comments-1', "id={$this->album['id']}", 1 );
			$this->ipsclass->DB->simple_exec();
		}

		// Update stats
		$this->ipsclass->DB->simple_update( 'gallery_images'    , 'comments=comments-1', "id={$this->img['id']}", 1 );       
		$this->ipsclass->DB->simple_exec();

		// Set the comment approved
		$this->ipsclass->DB->do_update( 'gallery_comments', array( 'approved' => '0' ), "pid={$cid}", 1 );		
	}

	/**
	* approve_comment()
	*
	* Makes a queued comment visible to the public
	*
	* @param integer $cid	
	* @param integer $img
	* @return none
	**/	
	function approve_comment( $cid, $img, $type )
	{		
		/**
		* Get the category/album and image information
		**/
		$this->img = $this->glib->get_image_info( $img );
		if( $type == 'cat' ) {
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
			$this->ipsclass->DB->simple_update( 'gallery_categories', 'comments=comments+1', "id={$this->category['id']}" , 1 ); 
			$this->ipsclass->DB->simple_exec();
		}
		if( $type == 'album' )  {
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
			$this->ipsclass->DB->simple_update( 'gallery_albums', 'comments=comments+1', "id={$this->album['id']}", 1 ); 
			$this->ipsclass->DB->simple_exec();
		}

		// Update stats
		$this->ipsclass->DB->simple_update( 'gallery_images'    , 'comments=comments+1', "id={$this->img['id']}", 1 );  
		$this->ipsclass->DB->simple_exec();

		// Set the comment approved
		$this->ipsclass->DB->do_update( 'gallery_comments', array( 'approved' => '1' ), "pid={$cid}", 1 );		
	} 

	
	/**
	* pin_image()
	*
	* Pin the image
	*
	* @param integer $img
	* @return none
	**/
	function pin_image( $img, $redir=1 )
	{	
		$img = $this->glib->validate_int( $img );
		
        $this->ipsclass->DB->do_update( 'gallery_images', array( 'pinned' => '1' ), "id={$img}" );
        
        if( $redir )
        {
			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['img_pinned'], "automodule=gallery&cmd=si&img={$img}" );        	
        }
	}
	
	/**
	* unpin_image()
	*
	* Pin the image
	*
	* @param integer $img
	* @return none
	**/
	function unpin_image( $img, $redir=1 )
	{		
		$img = $this->glib->validate_int( $img );
		
        $this->ipsclass->DB->do_update( 'gallery_images', array( 'pinned' => '0' ), "id={$img}" );
        
        if( $redir )
        {
			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['img_unpinned'], "automodule=gallery&cmd=si&img={$img}" );        	
        }        
	}		

	/**
	* decline_image()
	*
	* Removes a moderate image
	*
	* @param integer $img
	* @return none
	**/
	function decline_image( $img )
	{
		/**
		* Get the category/album and image information
		**/
		$img = $this->glib->validate_int( $img );
		$this->img = $this->glib->get_image_info( $img );

		if( $this->img['category_id'] )
		{
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
		}
		else
		{
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
		}
		
		/**
		* Security Check
		**/
		$can_del = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_del = 1;
		}
		else
		{
			if( ( $this->img['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_del_own'] ) || $this->ipsclass->member['g_mod_albums'] )
			{
				$can_del = 1;
			}
		}		

		/**
		* Get the image info
		**/		
		$this->ipsclass->DB->simple_construct( array( "select" => 'masked_file_name, directory, approved', 'from' => 'gallery_images', 'where' => "id={$img}" ) );
		$this->ipsclass->DB->simple_exec();
		$del = $this->ipsclass->DB->fetch_row();
		
		/**
		 * If the image is already approved, then we are going to assume that the user wants to set this image to be
		 * invisible, and not deleted.
		 **/
		if( $del['approved'] )
		{
			$this->ipsclass->DB->do_update( 'gallery_images', array( 'approved' => 0 ), "id={$img}" );
			$this->ipsclass->DB->simple_construct( array( 
											'update' => 'gallery_categories',
											'set'    => "images=images-1, mod_images=mod_images+1",
											'where'  => "id={$this->img['category_id']}",
								)      );
			$this->ipsclass->DB->simple_exec();		
			
			/*
			* Is this image the last_pic for the category? If so, reset to next approved pic */
			if( $this->category['last_pic'] == $img )  
			{
				$this->ipsclass->DB->simple_construct( array(
										"select"	=>	"id",
										"from"		=>	"gallery_images",
										"where"		=>	"category_id={$this->img['category_id']} AND approved=1",
										"order"		=>	"date DESC" ) );
				$this->ipsclass->DB->simple_exec();
				if( !$this->ipsclass->DB->get_num_rows() )  
				{
					/*
					* Last image in category */
					$this->ipsclass->DB->do_update( 'gallery_categories', 
											array( 'last_pic' => '',
													'last_name' => '',
													'last_member_id' => '' ), "id={$this->img['category_id']}" );
				}
				else
				{
					$_tmp = $this->ipsclass->DB->fetch_row();
					$next_img = $this->glib->get_image_info( $_tmp['id'] );
					$this->ipsclass->DB->do_update( 'gallery_categories',
													array( 	'last_pic' => $next_img['id'],
															'last_name' => '',
															'last_member_id' => $next_img['member_id'] ),
													"id={$this->img['category_id']}" );
				}
			}
		}
		else
		{
			/**
			* Delete the physical image
			**/		
			$dir = ( $del['directory'] ) ? "{$del['directory']}/" : "";
			@unlink( $this->ipsclass->vars['upload_dir'].'/'.$dir.$del['masked_file_name'] );
			if( file_exists( $this->ipsclass->vars['upload_dir'].'/'.$dir.'tn_'.$del['masked_file_name'] ) )
			{
				@unlink( $this->ipsclass->vars['upload_dir'].'/'.$dir.'tn_'.$del['masked_file_name'] );
			}
			
			/**
			* Delete the medium sized image as well
			**/
			if( file_exists( $this->ipsclass->vars['upload_dir'].'/'.$dir.'med_'.$del['masked_file_name'] ) )
			{
				@unlink( $this->ipsclass->vars['upload_dir'].'/'.$dir.'med_'.$del['masked_file_name'] );
			}

			/**
			* Delete the image from the database
			**/		
			$this->ipsclass->DB->simple_construct( array( "delete" => 'gallery_images', 'where' => "id={$img}" ) );
			$this->ipsclass->DB->simple_exec();

			/**
			* Update the moderated images counter
			**/			
			$this->ipsclass->DB->simple_construct( array( 
											'update' => 'gallery_categories',
											'set'    => "mod_images=mod_images-1",
											'where'  => "id={$this->category['id']}",
			)      );
			$this->ipsclass->DB->simple_exec();		
		}
	}

	/**
	* approve_image()
	*
	* Sets a moderated image to be viweable
	*
	* @param integer $img
	* @return none
	**/
	function approve_image( $img )
	{
		/**
		* Get the category/album and image information
		**/
		$img = $this->glib->validate_int( $img );
		$this->img = $this->glib->get_image_info( $img );

		if( $this->img['category_id'] )
		{
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
		}
		else
		{
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
		}
		
		/**
		* Security Check
		**/
		$can_del = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_del = 1;
		}
		else
		{
			if( ( $this->img['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_del_own'] ) || $this->ipsclass->member['g_mod_albums'] )
			{
				$can_del = 1;
			}
		}		

		/**
		* Need to update the parents, if they exist
		**/
		if( ! class_exists( "Categories" ) )
		{
			require( $this->ipsclass->gallery_root .  'categories.php' );				
		}
		$this->parents = new Categories;
                $this->parents->ipsclass =& $this->ipsclass;
                $this->parents->glib =& $this->glib;
		$this->parents->read_data( false, '' );

		$cid = $this->category['id'];
		while( $this->parents->data[$cid]['parent'] )
		{
			$cid = $this->parents->data[$cid]['parent'];
			$update_parents[] = $cid;
		}

		/**
		* Accept the image
		**/
		$this->ipsclass->DB->simple_construct( array( 'update' => 'gallery_images',
		'set'    => "approved=1, lastcomment=".time(),
		'where'  => "id={$img}",
		)      );
		$this->ipsclass->DB->simple_exec();

		$this->ipsclass->DB->simple_construct( array( 'update' => 'gallery_categories',
				'set'    => "images=images+1, last_name = '{$this->img['members_display_name']}', last_member_id = {$this->img['member_id']}, last_pic={$img}, mod_images=mod_images-1",
				'where'  => "id={$this->category['id']}",
		)      );
		
		$this->ipsclass->DB->simple_exec();

		/**
		* Parents
		**/
		if( is_array( $update_parents ) )
		{
			$update_parents = implode( ",", $update_parents );
			$this->ipsclass->DB->simple_construct( array( 'update' => 'gallery_categories',
				'set'    => "last_pic={$img}, last_name='{$this->img['members_display_name']}', last_member_id={$this->img['member_id']}",
				'where'  => "id IN ( {$update_parents} )",
			)      );
			$this->ipsclass->DB->simple_exec();
		}
	}

	/**
	* delete_image()
	*
	* Removes the specified image, permissions checked
	*
	* @param integer $img
	* @return none
	**/
	function delete_image( $img, $redir=1 )
	{
		/**
		* Get the category/album and image information
		**/
		$this->img = $this->glib->get_image_info( $img );

		if( $this->img['category_id'] )
		{
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
		}
		else
		{
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
		}

		/**
		* Security Check
		**/
		$can_del = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_del = 1;
		}
		else
		{
			if( ( $this->img['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_del_own'] ) || $this->ipsclass->member['g_mod_albums'] )
			{
				$can_del = 1;
			}
		}

		if( ! $can_del )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		/**
		* Delete the physical files
		**/
		@unlink( $this->glib->get_full_path( $this->img ) );
		if( $this->img['thumbnail'] )
		{
			@unlink( $this->glib->get_full_tn_path( $this->img ) );
		}
		
		/**
		* Medium sized image?
		**/
		if( $this->img['medium_file_name'] )
		{
			@unlink( $this->glib->get_full_med_path( $this->img ) );
		}

		/**
		* Delete the rating entries
		**/
		$this->ipsclass->DB->simple_delete( 'gallery_ratings', "img_id={$img}" );
		$this->ipsclass->DB->simple_exec();

		if( $this->category )
		{
			if( $img == $this->category['last_pic'] || ! $this->category['last_pic'] )
			{
				// Get the image info
				$this->ipsclass->DB->simple_construct( array( 'select' => 'id, member_id',
				'from'   => 'gallery_images',
				'where'  => "id < {$img} AND category_id={$this->category['id']}",
				'order'  => "id DESC" ) );
				$this->ipsclass->DB->simple_exec();
				$new = $this->ipsclass->DB->fetch_row();

				$new['id']        = ( $new['id'] )        ? $new['id']        : 0;
				$new['member_id'] = ( $new['member_id'] ) ? $new['member_id'] : 0;

				// Need the member name too
				$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$new['member_id']}" ) );
				$this->ipsclass->DB->simple_exec();
				$mem = $this->ipsclass->DB->fetch_row();

				$update = ", last_pic={$new['id']}, last_name='{$mem['name']}', last_member_id={$new['member_id']}";

			}
			if( !$this->img['approved'] )  {
				$update .= ', mod_images=mod_images-1';
			}
			$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images-1, comments=comments-{$this->img['comments']} {$update}", "id={$this->category['id']}", 1 );
			$this->ipsclass->DB->simple_exec();
		}
		else
		{
			if( $img == $this->album['last_pic'] || !  $this->album['last_pic'] )
			{
				// Get the image info
				$this->ipsclass->DB->simple_construct( array( 'select' => 'id, member_id',
				'from'   => 'gallery_images',
				'where'  => "id < {$img} AND album_id={$this->album['id']}",
				'order'  => "id DESC" ) );
				$this->ipsclass->DB->simple_exec();
				$new = $this->ipsclass->DB->fetch_row();

				$new['id'] = ( $new['id'] ) ? $new['id'] : 0;
				$new['member_id'] = ( $new['member_id'] ) ? $new['member_id'] : 0;

				// Need the member name too
				$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$new['member_id']}" ) );
				$this->ipsclass->DB->simple_exec();
				$mem = $this->ipsclass->DB->fetch_row();

				$update = ", last_pic={$new['id']}, last_name='{$mem['name']}'";
			}

			$this->ipsclass->DB->simple_update( 'gallery_albums', "images=images-1, comments=comments-{$this->img['comments']} {$update}", "id={$this->album['id']}", 1 );
			$this->ipsclass->DB->simple_exec();

		}
		$this->ipsclass->DB->simple_delete( 'gallery_images', "id={$img}" );
		$this->ipsclass->DB->simple_exec();

		$this->ipsclass->DB->simple_delete( 'gallery_comments', "img_id={$img}" );
		$this->ipsclass->DB->simple_exec();

		$url = ( $this->img['category_id'] ) ? "cmd=sc&amp;cat={$this->category['id']}" : "cmd=user&amp;user={$this->album['member_id']}&amp;op=view_album&amp;album={$this->album['id']}";
		
		if( $redir )
		{
			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['image_delete'], "act=module&amp;module=gallery&amp;{$url}" );
		}
	}

	/**
	* post::delete_reply()
	*
	* Removes a comment from an image, permissions are checked
	*
	* @param integer $pid
	* @return nothing
	**/
	function delete_reply( $pid, $redir=1 )
	{
		/**
		* Get the category and image information
		**/
		$img = $this->glib->validate_int( $this->ipsclass->input['img'] );
		$this->img = $this->glib->get_image_info( $img );

		if( $this->img['category_id'] )
		{
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
		}
		else
		{
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
		}

		/**
		* Security Checks
		**/
		$can_del = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) && $this->category )
		{
			$can_del = 1;
		}
		else if( $this->ipsclass->member['g_mod_albums'] && $this->album )
		{
			$can_del = 1;
		}
		else
		{
			if( $this->ipsclass->member['id'] == $this->img['member_id'] && $this->ipsclass->member['g_del_own'] )
			{
				$can_del = 1;
			}
		}

		if( ! $can_del )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		/**
		* Update Stats
		**/
		if( $this->category )
		{
			$this->ipsclass->DB->simple_update( 'gallery_categories', "comments=comments-1", "id={$this->category['id']}", 1 );
			$this->ipsclass->DB->simple_exec();
		}
		else
		{
			$this->ipsclass->DB->simple_update( 'gallery_albums', "comments=comments-1", "id={$this->album['id']}", 1 );
			$this->ipsclass->DB->simple_exec();
		}

		$this->ipsclass->DB->simple_update( 'gallery_images', "comments=comments-1", "id={$this->img['id']}", 1 );
		$this->ipsclass->DB->simple_exec();

		/**
		* Delete Comment
		**/
		$this->ipsclass->DB->simple_delete( 'gallery_comments', "pid={$pid}" );
		$this->ipsclass->DB->simple_exec();
		
		if( $redir )
		{
			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['comment_deleted'], "act=module&amp;module=gallery&amp;cmd=si&amp;img={$this->img['id']}" );
		}
	}

	/**
	* process_move()
	*
	* Moves an image from one category to another, permissions are checked
	*
	* @param integer $img
	* @param integer $dest_cat
	*
	* @return none
	**/
	function process_move( $img, $dest_cat, $multi=false )
	{
		/**
		* Get the category/album and image information
		**/
		$this->img = $this->glib->get_image_info( $img );

		if( $this->img['category_id'] )
		{
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
		}
		else
		{
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
		}

		/**
		* Security Checks
		**/
		$can_edit = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_edit = 1;
		}
		else
		{
			if( $this->ipsclass->member['id'] == $this->img['member_id'] && $this->ipsclass->member['g_move_own'] )
			{
				$can_edit = 1;
			}
		}

		if( ! $can_edit )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		/**
		* Get a category list
		**/
		$this->ipsclass->DB->simple_construct( array( 'select' => '*',
		'from'   => 'gallery_categories',
		'where'  => "id = {$dest_cat}",
		'order'  => 'c_order' ) );
		$this->ipsclass->DB->simple_exec();

		/**
		* Is the category valid?
		**/
		if( ! $this->ipsclass->DB->get_num_rows() )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		$cat = $this->ipsclass->DB->fetch_row();

		if( $cat['album_mode'] )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'mv_invalid_ac' ) );
		}

		if( $dest_cat == $this->img['category_id'] )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'mv_same_cat' ) );
		}

		/**
		* Update the image with the new category id
		**/
		$this->ipsclass->DB->simple_update( 'gallery_images', "category_id={$cat['id']}", "id={$this->img['id']}", 1 );
		$this->ipsclass->DB->simple_exec();

		/**
		* Check unapproved status
		**/
		$skip_new_cat_last_image = 0;
		if( !$this->img['approved'] )
		{
			/**
			* Ok.. need to update some status stuff
			* First, mod_images-1
			**/
			$mod_image_old_cat = ", mod_images=mod_images-1";
			$mod_image_new_cat = ", mod_images=mod_images+1";
			$skip_new_cat_last_image = 1;
		}
		
		/**
		* Check flag 
		**/
		if( !$skip_new_cat_last_image )
		{
			/**
			* Update the last image for the receiving category?
			**/
			$this->ipsclass->DB->simple_construct( array( 'select' => 'date',
			'from'   => 'gallery_images',
			'where'  => "id={$cat['last_pic']}" ) );
			$this->ipsclass->DB->simple_exec();
	
			$last = $this->ipsclass->DB->fetch_row();
	
			if( $this->img['date'] > $last['date'] )
			{
				if( $this->img['id'] )
				{
					$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$this->img['member_id']}" ) );
					$this->ipsclass->DB->simple_exec();
					$mem = $this->ipsclass->DB->fetch_row();
	
					$new_last_pic = ", last_pic={$this->img['id']}, last_member_id={$this->img['member_id']}, last_name='{$mem['name']}'";
				}
			}
		}

		/**
		* Update the last image for the sending category?
		**/
		if( $this->img['id'] == $this->category['last_pic'] )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, member_id',
			'from'   => 'gallery_images',
			'where'  => "category_id={$this->category['id']}",
			'order'  => 'id DESC',
			'limit'  => array( 0, 1 )
			) );
			$this->ipsclass->DB->simple_exec();

			$new = $this->ipsclass->DB->fetch_row();

			if( $new['id'] )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$new['member_id']}" ) );
				$this->ipsclass->DB->simple_exec();
				$mem = $this->ipsclass->DB->fetch_row();

				$old_last_pic = ", last_pic={$new['id']}, last_member_id={$new['member_id']}, last_name='{$mem['name']}'";
			}
			else
			{
				/*
				* Category is now empty */
				$old_last_pic = ", last_pic='0', last_member_id='0', last_name=''";
			}
		}

		/**
		* Update the categories
		**/

		$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+1, comments=comments+{$this->img['comments']} {$mod_image_new_cat} {$new_last_pic}", "id={$cat['id']}", 1 );
		$this->ipsclass->DB->simple_exec();

		$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images-1, comments=comments-{$this->img['comments']} {$mod_image_old_cat} {$old_last_pic}", "id={$this->category['id']}", 1 );
		$this->ipsclass->DB->simple_exec();

		if( !$multi )  {
			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['img_moved'], "automodule=gallery&cmd=si&img={$this->img['id']}" );
		}
	}


	/**
	* move_form()
	*
	* Shows the form for moving an image from one category to another, permissions checked
	*
	* @return none
	**/
	function move_form( $img )
	{
		/* Fatal error bug fix */
        if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_post'] ) ) {
			$this->ipsclass->load_template( 'skin_gallery_post' );
        }
		$this->html = $this->ipsclass->compiled_templates['skin_gallery_post'];

		/**
		* Get the category and image information
		**/
		$this->img = $this->glib->get_image_info( $img );

		if( $this->img['category_id'] )
		{
			$this->category = $this->glib->get_category_info( $this->img['category_id'] );
		}
		else
		{
			$this->album = $this->glib->get_album_info( $this->img['album_id'] );
		}

		/**
		* Security Checks
		**/
		$can_edit = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_edit = 1;
		}
		else
		{
			if( $this->ipsclass->member['id'] == $this->img['member_id'] && $this->ipsclass->member['g_move_own'] )
			{
				$can_edit = 1;
			}
		}

		if( ! $can_edit )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		/**
		* Get Categories
		**/
		require( $this->ipsclass->gallery_root . 'categories.php' );
		$this->catlist = new Categories;
                $this->catlist->ipsclass =& $this->ipsclass;
                $this->catlist->glib =& $this->glib;
		$this->catlist->read_data( true );
		$this->catlist->current = $this->img['category_id'];
		$sub = $this->catlist->build_dropdown( 'category' );

		/**
		* The form
		**/
		$this->output .= $this->html->move_form_top( 'submit_move' );
		$this->output .= $this->html->post_form_row( $this->ipsclass->lang['image_move'], $this->img['caption'] );
		$this->output .= $this->html->post_form_row( $this->ipsclass->lang['move_to'], $sub, $this->ipsclass->lang['move_msg'] );
		$this->output .= $this->html->post_form_end( 'submit_move' );

		/**
		* Page Navigation and title
		**/
		$this->title = $this->ipsclass->vars['board_name'] . $this->ipsclass->lang['sep'] . " " . $this->ipsclass->lang['gallery'];
		$this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";

		if( $this->category )
		{
			if( $this->category['parent'] )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => 'id, name',
				'from'   => 'gallery_categories',
				'where'  => "id={$this->category['parent']}" ) );
				$this->ipsclass->DB->simple_exec();

				$parent = $this->ipsclass->DB->fetch_row();

				$this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=sc&amp;cat={$parent['id']}'>{$parent['name']}</a>";
			}
			$this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=sc&amp;cat={$this->category['id']}'>{$this->category['name']}</a>";
		}
		else
		{
			$this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->album['member_id']}&amp;op=view_album&amp;album={$this->album['id']}'>{$this->album['name']}</a>";
		}

		$this->nav[] = "{$this->ipsclass->lang['moving']}<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=si&amp;img={$this->img['id']}'>{$this->img['caption']}</a>";

	}
}
?>
