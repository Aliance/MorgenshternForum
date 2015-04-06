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
* Main/Post
*
* Used to post images and comments, edit images/comments
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

class post
{
    var $ipsclass;
    var $glib;
	var $output;
	var $info;
	var $html;
	var $parser;
	var $han_editor;

	/**
	* post::start()
	*
	* Begins execution of this module, $param is used as an entry point into the
	* module.
	*
	* @param string $param
	* @return nothing
	**/
	function start( $param="" )
	{
		// -----------------------------------------------------------
		// Get the skin and language
		// -----------------------------------------------------------
        
		/* Fatal error bug fix */
        if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_post'] ) )
        {
			$this->ipsclass->load_template( 'skin_gallery_post' );
        }
		$this->html     = $this->ipsclass->compiled_templates[ 'skin_gallery_post' ];
		$this->ipsclass->load_language( 'lang_post' );

		/**
		* Load up some options before loading parser & editor
		**/
		$this->ipsclass->input['enablesig']   = $this->ipsclass->input['enablesig']   == 'yes' ? 1 : 0;
		$this->ipsclass->input['enableemo']   = $this->ipsclass->input['enableemo']   == 'yes' ? 1 : 0;
		
		/**
		* Remove any board tags from Post 
		**/
		$this->ipsclass->input['Post'] = $this->ipsclass->remove_tags( $this->ipsclass->input['Post'] );
		
		// -----------------------------------------------------------
		// Load the parser
		// -----------------------------------------------------------
		require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $this->parser                      =  new parse_bbcode();
        $this->parser->ipsclass            =& $this->ipsclass;
        $this->parser->allow_update_caches = 1;
        
        /**
        * Parser settings 
        **/
        $this->parser->parse_html    = intval($this->ipsclass->member['g_dohtml']);
        $this->parser->parse_nl2br   = 1;
        $this->parser->parse_smilies = $this->ipsclass->input['enableemo'];
        $this->parser->parse_bbcode  = 1;
        $this->parser->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);
        
        /**
        * Load up the rte/std editor handler
        **/
        require_once( ROOT_PATH."sources/handlers/han_editor.php" );
        $this->han_editor           = new han_editor();
        $this->han_editor->ipsclass =& $this->ipsclass;
        $this->han_editor->init();

		// -----------------------------------------------------------
		// Grab the image and category information
		// -----------------------------------------------------------
		if( $this->ipsclass->input['img'] )
		{
			// Check input and get the image
			$this->imgid = $this->glib->validate_int( $this->ipsclass->input['img'] );
			$this->img   = $this->glib->get_image_info( $this->imgid );

			// Category or album?
			if( $this->img['category_id'] )
			{
				$this->category = $this->glib->get_category_info( $this->img['category_id'] );
			}
			else
			{
				$this->album = $this->glib->get_album_info( $this->img['album_id'] );
			}
		}
		else if ( $this->ipsclass->input['cat'] )
		{
			// Check input and get the category
			$this->catid    = $this->glib->validate_int( $this->ipsclass->input['cat'] );
			$this->category = $this->glib->get_category_info( $this->catid );
		}
		else if( $this->ipsclass->input['album'] )
		{
			// Check input and get the album
			$this->albumid = $this->glib->validate_int( $this->ipsclass->input['album'] );
			$this->album   = $this->glib->get_album_info( $this->albumid );
		}

		// -----------------------------------------------------------
		// What's our entry point into this module?
		// -----------------------------------------------------------
		switch( $param )
		{
			case 'reply':
				// ------------------------------------------
				// Security Checks First
				// ------------------------------------------
				if( is_array( $this->category ) )
				{
					if( ! $this->ipsclass->check_perms( $this->category['perms_comments'] ) || !$this->category['allow_comments'] || !$this->ipsclass->member['g_comment'] )
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
					}
				}
				else if ( is_array( $this->album ) )
				{
					if( ! $this->ipsclass->member['g_comment'] )
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
					}
				}

				if( $this->ipsclass->input['op'] == 'doaddcomment' )
				{
					$this->process_reply( $this->ipsclass->input['img'] );
				}
				else
				{
					$this->reply_form( $this->ipsclass->input['img'] );
				}
			break;

			case 'delreply':
				$this->delete_reply( $this->ipsclass->input['comment'] );
			break;

			case 'editreply':
				$this->ipsclass->input['comment'] = intval( $this->ipsclass->input['comment'] );
				( $this->ipsclass->input['op'] == 'doeditcomment' ) ? $this->process_edit_reply( $this->ipsclass->input['comment'] ) : $this->edit_reply_form( $this->ipsclass->input['comment'] );
			break;

			case 'proc':
			$this->process_single_img();
			break;

			case 'editimg':
				( $this->ipsclass->input['op'] == 'doedit' ) ? $this->process_edit() : $this->edit_form();
			break;

			case 'form':
			default:
				$this->show_img_form();
			break;
		}


		$this->ipsclass->print->add_output( $this->output );

		$this->ipsclass->print->do_output( array(
		'TITLE'    => $this->title,
		'NAV'      => $this->nav
		)       );

	}
	// **********************************************************************
	//
	// Comment Posting Methods
	//
	// **********************************************************************


	/**
	* post::process_edit_reply()
	*
	* Process the edit reply form, permissions are checked.
	*
	* @param integer $pid
	* @return nothing
	**/
	function process_edit_reply( $pid )
	{
		// Get th e comment info
		$this->ipsclass->DB->simple_construct( array( 'select' => 'author_id, img_id',
		'from'   => 'gallery_comments',
		'where'  => "pid={$pid}" ) );
		$this->ipsclass->DB->simple_exec();

		$info = $this->ipsclass->DB->fetch_row();

		// -----------------------------------------------------------
		// Security Checks, may as well get it over with ;)
		// -----------------------------------------------------------
		$can_edit = 0;
		if( $this->_perm_chk( $this->ipsclass->member['g_edit_own'], $info['author_id'] ) )
		{
			$can_edit = 1;
		}

		if( ! $can_edit )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		// ---------------------------------------
		// Make sure they actually made a comment
		// ---------------------------------------
		if( empty( $this->ipsclass->input['Post'] ) )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_comment' ) );
		}

		// ---------------------------------------
		// and that it's not too big...
		// ---------------------------------------
		$this->ipsclass->vars['max_post_length'] = $this->ipsclass->vars['max_post_length'] ? $this->ipsclass->vars['max_post_length'] : 2140000;
		if( strlen( $this->ipsclass->input['Post'] ) > ($this->ipsclass->vars['max_post_length'] * 1024 ) )
		{
			$this->ipsclass->Error( array( LEVEL => 1, MSG => 'post_too_long') );
		}

		$append_edit = 1;

		if( $this->ipsclass->member['g_append_edit'] )
		{
			if( $this->ipsclass->input['add_edit'] != 1 )
			{
				$append_edit = 0;
			}
		}

		$this->ipsclass->input['Post'] = $this->han_editor->process_raw_post( 'Post' );
		$comment = $this->parser->pre_db_parse( $this->ipsclass->input['Post'] );

		$html = ( $this->category['allow_html'] || $this->album ) AND $this->ipsclass->member['g_dohtml'] ? 1 : 0 ;

		$update =  array( 'comment'     => $comment,
		'use_sig'     => $this->ipsclass->input['enablesig'],
		'use_emo'     => $this->ipsclass->input['enableemo'],
		'edit_time'   => time(),
		'edit_name'   => $this->ipsclass->member['name'],
		'append_edit' => $append_edit,
		);

		$this->ipsclass->DB->do_update( 'gallery_comments', $update, "pid=".intval($this->ipsclass->input['comment']), 1 );

		$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['comment_edited'], "&automodule=gallery&cmd=si&img={$info['img_id']}" );

	}



	/**
	* post::edit_reply_form()
	*
	* Shows the form for editing a comment
	*
	* @param integer $pid
	* @return nothing
	**/
	function edit_reply_form( $pid )
	{
		// Get th e comment info
		$this->ipsclass->DB->simple_construct( array( 'select' => '*',
		'from'   => 'gallery_comments',
		'where'  => "pid={$pid}" ) );
		$this->ipsclass->DB->simple_exec();

		$info = $this->ipsclass->DB->fetch_row();

		// -----------------------------------------------------------
		// Security Checks, may as well get it over with ;)
		// -----------------------------------------------------------
		$can_edit = 0;
		if( $this->_perm_chk( $this->ipsclass->member['g_edit_own'], $info['author_id'] ) )
		{
			$can_edit = 1;
		}

		if( ! $can_edit )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		if ( $this->han_editor->method == 'rte' )
		{
			$info['comment'] = $this->parser->convert_ipb_html_to_html( $info['comment'] );
		}
		else
		{
			$this->parser->parse_html    = $this->ipsclass->member['g_dohtml'];
			$this->parser->parse_nl2br   = $this->ipsclass->member['g_dohtml'];
			$this->parser->parse_smilies = $info['use_emo'];
			$this->parser->parse_bbcode  = 1;
			$info['comment'] = $this->parser->pre_edit_parse( $info['comment'] );
		}
			
        $editor_html = $this->han_editor->show_editor( $info['comment'], 'Post' );

		$this->output .= $this->html->post_javascript();
		$this->output .= $this->html->edit_reply_form_top( 'edit_comment' );
		$this->output .= $this->html->post_description_field( $editor_html );
		$this->output .= $this->html->get_post_options( 'checked', 'checked' );
		$this->output .= $this->html->post_form_end( 'edit_comment' );

		// Append Edit Box?
		if( $this->ipsclass->member['g_append_edit'] )
		{
			$this->output = preg_replace( "/<!-- APPEND_EDIT -->/", '<br />'.$this->html->get_append_chk(), $this->output );
		}

		$this->html_add_smilie_box();

		$this->title = $this->ipsclass->vars['board_name'] . $this->ipsclass->lang['sep'] . " " . $this->ipsclass->lang['gallery'];

		$this->nav[] = "<a href='{$this->ipsclass->base_url}?act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";

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

		$this->nav[] = "{$this->ipsclass->lang['editing']}<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=si&amp;img={$this->img['id']}'>{$this->img['caption']}</a>";
	}

	/**
	* post::process_reply()
	*
	* Process the add reply form, adds the comment to the image, permissions are checked
	*
	* @param integer $img
	* @return none
	**/
	function process_reply( $img )
	{
		if( $this->ipsclass->input['preview'] )
		{
			$this->reply_form( $this->ipsclass->input['img'] );
			return;
		}

		// ---------------------------------------
		// Make sure they actually made a comment
		// ---------------------------------------
		if( empty( $this->ipsclass->input['Post'] ) )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_comment' ) );
		}

		// ---------------------------------------
		// and that it's not too big...
		// ---------------------------------------
		$this->ipsclass->vars['max_post_length'] = $this->ipsclass->vars['max_post_length'] ? $this->ipsclass->vars['max_post_length'] : 2140000;
		if( strlen( $this->ipsclass->input['Post'] ) > ($this->ipsclass->vars['max_post_length'] * 1024 ) )
		{
			$this->ipsclass->Error( array( LEVEL => 1, MSG => 'post_too_long') );
		}

		// ---------------------------------------
		// Prepare the comment for db insertion
		// ---------------------------------------
		$comment = $this->han_editor->process_raw_post( 'Post' );
		$comment = $this->parser->pre_db_parse( $comment );

		$html = $this->category['allow_html'] AND $this->ipsclass->member['g_dohtml'] ? 1 : 0 ;

		$insert = array(
		'author_id'   => $this->ipsclass->member['id'],
		'author_name' => $this->ipsclass->member['name'],
		'use_sig'     => $this->ipsclass->input['enablesig'],
		'use_emo'     => $this->ipsclass->input['enableemo'],
		'ip_address'  => $this->ipsclass->input['IP_ADDRESS'],
		'post_date'   => time(),
		'comment'     => $comment,
		'approved'    => ( $this->category['approve_comments'] ) ? 0 : 1,
		'img_id'      => $img,
		);

		// ---------------------------------------
		// Update some stats
		// ---------------------------------------
		$msg = 'comment_moderated';
		if( $this->category )
		{
			if( ! $this->category['approve_comments'] )
			{
				$msg = 'comment_added';
				$this->ipsclass->DB->simple_update( 'gallery_images', 'comments=comments+1, lastcomment='.time(), "id={$img}", 1 ); $this->ipsclass->DB->simple_exec();
				$this->ipsclass->DB->simple_update( 'gallery_categories', 'comments=comments+1', "id={$this->img['category_id']}", 1 ); $this->ipsclass->DB->simple_exec();

				if( $this->category['inc_post_count'] )
				{
					$this->ipsclass->DB->simple_update( 'members', 'posts=posts+1', "id={$this->ipsclass->member['id']}", 1 ); $this->ipsclass->DB->simple_exec();
				}
			}
		}
		else
		{
			// Update category stats
			if( $this->album['category_id'] )
			{
				$this->ipsclass->DB->simple_update( 'gallery_categories', "comments=comments+1", 'id='.$this->album['category_id'], 1 );
				$this->ipsclass->DB->simple_exec();
			}

			$msg = 'comment_added';
			$this->ipsclass->DB->simple_update( 'gallery_images', 'comments=comments+1, lastcomment='.time(), "id={$img}", 1 ); $this->ipsclass->DB->simple_exec();
			$this->ipsclass->DB->simple_update( 'gallery_albums', 'comments=comments+1', "id={$this->img['album_id']}", 1 ); $this->ipsclass->DB->simple_exec();
		}

		$this->ipsclass->DB->do_insert( 'gallery_comments', $insert );

		$this->ipsclass->print->redirect_screen( $this->ipsclass->lang[$msg], "&automodule=gallery&cmd=si&img={$img}" );

	}


	/**
	* post::reply_form()
	*
	* Shows the add comment form
	*
	* @param integer $img
	* @return none
	**/
	function reply_form( $img )
 	{
        $raw_post = $this->check_multi_quote(); 
        
         $editor_html = $this->han_editor->show_editor( $raw_post, 'Post' );
		
		$this->output .= $this->html->reply_form_top( 'submit_comment' );
		$this->output .= $this->html->post_description_field( $editor_html );
		$this->output .= $this->html->get_post_options( 'checked', 'checked' );
		$this->output .= $this->html->post_form_end( 'submit_comment' );

		$this->html_add_smilie_box();

		$this->title = $this->ipsclass->vars['board_name'] . $this->ipsclass->lang['sep'] . " " . $this->ipsclass->lang['gallery'];

		$this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";

		if( $this->category )
		{
			if( $this->category['parent'] )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => 'id, name',
				'from' => 'gallery_categories',
				'where' => "id={$this->category['parent']}" ) );
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

		$this->nav[] = "{$this->ipsclass->lang['commenting']}<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=si&amp;img={$this->img['id']}'>{$this->img['caption']}</a>";

	}

	/************************************************************************
	*
	* Image Posting Methods
	*
	************************************************************************/


	/**
	* edit_form()
	*
	* Shows the for editing an image
	*
	* @return none
	**/
	function edit_form()
	{
		// -----------------------------------------------------------
		// Security Checks, may as well get it over with ;)
		// -----------------------------------------------------------
		$can_edit = 0;
		if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
		{
			$can_edit = 1;
		}
		else
		{
			if( $this->ipsclass->member['id'] == $this->img['member_id'] && $this->ipsclass->member['g_edit_own'] )
			{
				$can_edit = 1;
			}
			else if( $this->ipsclass->member['g_mod_albums'] )
			{
				$can_edit = 1;
			}
		}

		if( ! $can_edit )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		// -----------------------------------------------------------
		// Start building the form
		// -----------------------------------------------------------
		$lang = ( $this->ipsclass->input['media'] ) ? 'edit_media' : 'edit_post';

		$this->output .= $this->html->post_javascript();
		$this->output .= $this->html->edit_form_top( $lang, $this->category['id'] );
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_form_fields', 'order' => 'position' ) );
		$q = $this->ipsclass->DB->simple_exec();
		while( $i = $this->ipsclass->DB->fetch_row( $q ) )
		{
			// First off we need to see if this is a special field
			if( $i['id'] == 1 )
			{
				$this->output .= $this->html->post_form_row( $this->ipsclass->lang['caption']    , $this->html->post_control_textbox( 'caption', $this->img['caption'] ) );
			}
			else if( $i['id'] == 2 )
			{
				if ( $this->han_editor->method == 'rte' )
				{
					$this->img['description'] = $this->parser->convert_ipb_html_to_html( $this->img['description'] );
				}
				else
				{
					$this->parser->parse_html    = $this->ipsclass->member['g_dohtml'];
					$this->parser->parse_nl2br   = 1;
					$this->parser->parse_smilies = 1;
					$this->parser->parse_bbcode  = 1;
					$this->img['description'] = $this->parser->pre_edit_parse( $this->img['description'] );
				}
				
                $editor_html = $this->han_editor->show_editor( $this->img['description'], 'Post' );
				
                $this->output .= $this->html->post_description_field( $editor_html );
                
                /**
				* Are users allowed to set copyrights?
				**/
				if( $this->ipsclass->vars['gallery_allow_usercopyright'] == 'yes' )
				{
					/**
					* Add copyright field
					**/
					$this->_add_text_field( array( 'id' => 'copyright', 'name' => $this->ipsclass->lang['user_copyright_name'], 'description' => $this->ipsclass->lang['user_copyright_desc'] ), $this->img['copyright'] );
				}
			}
			else if( $i['id'] == 3 )
			{
				if( $this->ipsclass->input['media'] )
				{
					$this->output .= $this->html->post_form_row( $this->ipsclass->lang['media'], $this->html->post_control_file( 'media', $this->ipsclass->input['media'] ) );
				}
				else
				{
					$this->output .= $this->html->post_form_row( $this->ipsclass->lang['image'], $this->html->post_control_file( 'image', $this->ipsclass->input['image'] ) );
				}                }
				// Must be a custom field
				else
				{
					$field = "field_{$i['id']}";
					switch( $i['type'] )
					{
						case 'text':
						$this->_add_text_field( $i, $this->img[$field] );
						break;

						case 'area':
						$this->_add_textarea_field( $i, $this->img[$field] );
						break;
						case 'date':
						$this->_add_date_field( $i, $this->img[$field] );
						break;

						case 'drop':
						$this->_add_dropdown_field( $i, $this->img[$field] );
						break;

						case 'sepr':
						$this->_add_field_sep( $i, $this->img[$field] );
						break;
					}
				}
			}

			$this->output .= $this->html->post_form_end( $lang );

			$this->html_add_smilie_box();

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

			$this->nav[] = "{$this->ipsclass->lang['editing_img']}<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=si&amp;img={$this->img['id']}'>{$this->img['caption']}</a>";

		}

		/**
		* multi_upload_form()
		*
		* Form to display multiple upload fields
		*
		* @return none
		**/
		function multi_upload_form()
		{
			// -------------------------------------------------------------
			// Multiple Upload Fields
			// -------------------------------------------------------------

			// Top of the multiform
			$this->output .= $this->html->post_form_top( 'submit_multi' );

			$this->_add_field_sep( array( 'name' => $this->ipsclass->lang['multi_browse'] ) );

			for( $i=0; $i < $this->ipsclass->member['g_multi_file_limit']; $i++ )
			{
				$this->output .= $this->html->post_form_row( $this->ipsclass->lang['image'], $this->html->post_control_file( 'image'.$i, $this->ipsclass->input['image'] ) );
			}

			// Bottom of the Multi form
			$this->output .= $this->html->post_form_end( 'submit_multi' );

			// -------------------------------------------------------------
			// Zip Upload Form
			// -------------------------------------------------------------
			if( $this->ipsclass->member['g_zip_upload'] )
			{
				// Top of the zip form
				$this->output .= $this->html->post_form_top( 'zip' );

				$this->_add_field_sep( array( 'name' => $this->ipsclass->lang['zip_browse'] ) );
				$this->output .= $this->html->post_form_row( $this->ipsclass->lang['zip'], $this->html->post_control_file( 'zipfile' ) );

				// Bottom of the zip form
				$this->output .= $this->html->post_form_end( 'zip' );
			}

			$this->title = $this->ipsclass->vars['board_name'] . $this->ipsclass->lang['sep'] . " " . $this->ipsclass->lang['gallery'];

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
				$this->nav[] = $this->ipsclass->lang["nav_submit_post"].'<a href="'.$this->ipsclass->base_url.'act=module&amp;module=gallery&amp;cmd=sc&amp;cat='.$this->category['id'].'">'.$this->category['name'].'</a>';
			}
			else
			{
				$this->nav[] = $this->ipsclass->lang["nav_submit_post"]."<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->album['member_id']}&amp;op=view_album&amp;album={$this->album['id']}'>{$this->album['name']}</a>";
			}
		}

		/**
		* show_img_form()
		*
		* Shows the add new image form
		*
		* @return none
		**/
		function show_img_form()
		{
			// Check Auths
			$perms = explode( ':', $this->ipsclass->member['gallery_perms'] );

			if( ! $perms[1] )
			{
				if( $this->ipsclass->member['id'] != 0 )
				{
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_img_post' ) );
				}
			}

			if( ! $this->ipsclass->check_perms( $this->category['perms_images'] ) && ! $this->album )
			{
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
			}

			if( $this->ipsclass->input['multi'] )
			{
				if( $this->ipsclass->member['g_multi_file_limit'] || $this->ipsclass->member['g_zip_upload'] )
				{
					$this->multi_upload_form();
					return;
				}
			}

			$lang = 'submit_post';
	 
			/* Generate editor HTML */
			$editor_html = $this->han_editor->show_editor( $raw_post, 'Post' );

			$this->output .= $this->html->post_form_top( $lang );

			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_form_fields', 'order' => 'position' ) );
			$this->ipsclass->DB->simple_exec();


			while( $i = $this->ipsclass->DB->fetch_row() )
			{
				// First off we need to see if this is a special field
				if( $i['id'] == 1 )
				{
					$this->output .= $this->html->post_form_row( $this->ipsclass->lang['caption']    , $this->html->post_control_textbox( 'caption', $this->ipsclass->input['caption'] ) );
				}
				else if( $i['id'] == 2 )
				{
					$this->output .= $this->html->post_description_field( $editor_html );
					
					/**
					* Are users allowed to set copyrights?
					**/
					if( $this->ipsclass->vars['gallery_allow_usercopyright'] == 'yes' )
					{
						/**
						* Add copyright field
						**/
						$this->_add_text_field( array( 'id' => 'copyright', 'name' => $this->ipsclass->lang['user_copyright_name'], 'description' => $this->ipsclass->lang['user_copyright_desc'] ) );
					}
				}
				else if( $i['id'] == 3 )
				{
					$this->output .= $this->html->post_form_row( $this->ipsclass->lang['image'], $this->html->post_control_file( 'image', $this->ipsclass->input['image'] ) );
				}
				// Must be a custom field
				else
				{
					switch( $i['type'] )
					{
						case 'text':
						$this->_add_text_field( $i );
						break;

						case 'area':
						$this->_add_textarea_field( $i );
						break;

						case 'date':
						$this->_add_date_field( $i );
						break;

						case 'drop':
						$this->_add_dropdown_field( $i );
						break;

						case 'sepr':
						$this->_add_field_sep( $i );
						break;
					}
				}
			}

			$this->output .= $this->html->post_form_end( $lang );

			$this->html_add_smilie_box();

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
				$this->nav[] = $this->ipsclass->lang["nav_{$lang}"].'<a href="'.$this->ipsclass->base_url.'act=module&amp;module=gallery&amp;cmd=sc&amp;cat='.$this->category['id'].'">'.$this->category['name'].'</a>';
			}
			else
			{
				$this->nav[] = $this->ipsclass->lang["nav_{$lang}"]."<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->album['member_id']}&amp;op=view_album&amp;album={$this->album['id']}'>{$this->album['name']}</a>";
			}
		}


		/**
		* process_single_img()
		*
		* Adds an image to the gallery, checks permissions
		*
		* @return none
		**/
		function process_single_img()
		{
			require( $this->ipsclass->gallery_root . 'lib/image.php' );

			// -------------------------------------------------------------
			// Are we uploading multiple files?
			// -------------------------------------------------------------
			if( $_FILES['image0'] )
			{
				$this->process_multi_img();
				return;
			}

			// -------------------------------------------------------------
			// Are we uploading a zip file?
			// -------------------------------------------------------------
			if( $_FILES['zipfile'] )
			{
				$this->process_zip_file();
				return;
			}

			// -------------------------------------------------------------
			// Check Auths
			// -------------------------------------------------------------
			$perms = explode( ':', $this->ipsclass->member['gallery_perms'] );

			if( ! $perms[1] )
			{
				if( $this->ipsclass->member['id'] != 0 )
				{
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_img_post' ) );
				}
			}

			// If this is a category, check to see that we have permission
			if( ! $this->ipsclass->check_perms( $this->category['perms_images'] ) && $this->category )
			{
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
			}

			// -------------------------------------------------------------
			// If this is an album, make sure we are under the limit
			// -------------------------------------------------------------
			if( $this->album )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS total', 'from' => 'gallery_images', 'where' => "album_id={$this->ipsclass->input['album']}" ) );
				$this->ipsclass->DB->simple_exec();
				$count = $this->ipsclass->DB->fetch_row();

				if( $this->ipsclass->member['g_img_album_limit'] )
				{
					if( $count['total'] >= $this->ipsclass->member['g_img_album_limit'] )
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'a_img_limit' ) );
					}
				}
			}

			// -------------------------------------------------------------
			// Analayze Upload
			// -------------------------------------------------------------

			// Get the image details
			$file_name = $_FILES['image']['name'];

			// Did we find an image?
			if( empty( $file_name ) || $file_name == "none" )
			{
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_img_upload' ) );
			}

			// -------------------------------------------------------------
			// Empty Primary Fields Check
			// -------------------------------------------------------------
			if( empty( $this->ipsclass->input['caption'] ) )
			{
				$this->ipsclass->input['caption'] = $file_name;
			}

			// -------------------------------------------------------------
			// Empty Custom Fields Check
			// -------------------------------------------------------------
			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, type, required',
			'from'   => 'gallery_form_fields',
			'where'  => "id > 3" ) );
			$this->ipsclass->DB->simple_exec();


			if( $this->ipsclass->DB->get_num_rows() )
			{
				while( $i = $this->ipsclass->DB->fetch_row() )
				{
					$fields[$i['id']] = $i;
				}

				foreach( $fields as $id => $field )
				{
					$t_field = "field_$id";
					if( $field['required'] && empty( $this->ipsclass->input[$t_field] ) && $field['type'] != 'sepr' && $field['type'] != 'date' )
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'custom_empty' ) );
					}
				}
			}

			// Get our settings
			$settings = $this->_get_settings();
			$this->parser->parse_smilies = 1;
			$new_file_info = $this->glib->process_uploaded_file( 'image', $settings['thumb'], $settings['watermark'], $settings['container'], $settings['allow_media'] );
			$description = $this->han_editor->process_raw_post( 'Post' );
            $description = $this->parser->pre_db_parse( $description );
			
			$html = $this->category['allow_html'] AND $this->ipsclass->member['g_dohtml'] ? 1 : 0 ;

			// -------------------------------------------------------------
			// Insert information into the database, we're nearly home free now :D
			// -------------------------------------------------------------
			$insert = array( 'member_id'        => $this->ipsclass->member['id'],
			'category_id'      => ( $this->ipsclass->input['cat'] )   ? $this->ipsclass->input['cat']   : 0,
			'album_id'         => ( $this->ipsclass->input['album'] ) ? $this->ipsclass->input['album'] : 0,
			'caption'          => $this->ipsclass->input['caption'],
			'description'      => $description,
			'approved'         => $settings['approve'],
			'thumbnail'        => $settings['thumbnail'],
			'views'            => 0,
			'comments'         => 0,
			'date'             => time(),
			'ratings_total'    => 0,
			'ratings_count'    => 0, );

			$insert = array_merge( $insert, $new_file_info );

			// -------------------------------------------------------------
			// We need to insert the custom form data as well
			// -------------------------------------------------------------
			if( is_array( $fields ) )
			{
				foreach( $fields as $id => $field )
				{
					$t_field = "field_$id";

					if( $field['type'] == 'date' )
					{
						$insert[$t_field] = $this->_format_date_field( $this->ipsclass->input[$t_field.'_month'], $this->ipsclass->input[$t_field.'_day'], $this->ipsclass->input[$t_field.'_year'], $this->ipsclass->input[$t_field.'_hour'], $this->ipsclass->input[$t_field.'_minute'] );
					}
					else if( $field['type'] != 'sepr' )
					{
						$insert[$t_field] = $this->ipsclass->input[$t_field];
					}
				}
			}
			
			/**
			* Check copyright information
			**/
			if( $this->ipsclass->vars['gallery_allow_usercopyright'] != 'disabled' )
			{
				$insert['copyright'] = ( $this->ipsclass->vars['gallery_allow_usercopyright'] == 'yes' ) ? $this->ipsclass->input['field_copyright'] : $this->ipsclass->vars['gallery_copyright_default'];
			}
			
			// ------------------------------------------------------------
			// Need to update the parents, if they exist and this is a category
			// ------------------------------------------------------------
			if( $this->category )
			{
				require( $this->ipsclass->gallery_root .  'categories.php' );
				$this->parents = new Categories;
                                $this->parents->ipsclass =& $this->ipsclass;
                                $this->parents->glib =& $this->glib;
                                $this->parents->data =& $this->data;
				$this->parents->read_data( false, '' );

				$cid = $this->ipsclass->input['cat'];
				while( $this->parents->data[$cid]['parent'] )
				{
					$cid = $this->parents->data[$cid]['parent'];
					$update_parents[] = $cid;
				}

				if( is_array( $update_parents ) )
				{
					$update_parents = implode( ",", $update_parents );
				}
			}

			$this->ipsclass->DB->do_insert( 'gallery_images', $insert );
			$pid = $this->ipsclass->DB->get_insert_id();

			// -------------------------------------------------------------
			// Need to update the container information now
			// -------------------------------------------------------------
			if( $this->category )
			{
				$msg = 'image_moderated';
				if( ! $this->category['approve_images'] )
				{
					$msg = 'image_uploaded';
					$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+1, last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}', last_member_id={$this->ipsclass->member['id']}", "id={$this->category['id']}", 1 );
					$this->ipsclass->DB->simple_exec();

					if( $update_parents )
					{
						$this->ipsclass->DB->simple_update( 'gallery_categories', "last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}', last_member_id={$this->ipsclass->member['id']}", "id IN ( {$update_parents} )", 1 );
						$this->ipsclass->DB->simple_exec();
					}
				}
				else
				{
					$this->ipsclass->DB->simple_update( 'gallery_categories', "mod_images=mod_images+1", "id={$this->category['id']}", 1 );
					$this->ipsclass->DB->simple_exec();					
				}

				$this->ipsclass->print->redirect_screen( $this->ipsclass->lang[$msg], '&automodule=gallery&cmd=sc&cat='.$this->category['id'] );
			}
			else
			{
				// Update album stats
				$this->ipsclass->DB->simple_update( 'gallery_albums', "images=images+1, last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}'", 'id='.$this->ipsclass->input['album'], 1 );
				$this->ipsclass->DB->simple_exec();

				// Update category stats
				if( $this->album['category_id'] )
				{
					$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+1, last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}', last_member_id={$this->ipsclass->member['id']}", 'id='.$this->album['category_id'], 1 );
					$this->ipsclass->DB->simple_exec();
				}

				$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['image_uploaded'], '&automodule=gallery&cmd=albums' );
			}

		}

		// For future use
		function process_zip_file()
		{
		

		}

		/**
		* process_multi_img()
		*
		* Adds multiple images
		*
		* @return none
		**/
		function process_multi_img()
		{
			// ------------------------------------
			// Get the categories that will need updating
			// ------------------------------------
			if( $this->ipsclass->input['cat'] )
			{
				require( $this->ipsclass->gallery_root .  'categories.php' );
				$this->parents = new Categories;
                                $this->parents->ipsclass =& $this->ipsclass;
                                $this->parents->glib =& $this->glib;
                                $this->parents->data =& $this->data;
				$this->parents->read_data( false, '' );

				$cid = $this->ipsclass->input['cat'];
				while( $this->parents->data[$cid]['parent'] )
				{
					$cid = $this->parents->data[$cid]['parent'];
					$update_parents[] = $cid;
				}

				if( is_array( $update_parents ) )
				{
					$update_parents = implode( ",", $update_parents );
				}
			}

			// Begin looping
			for( $i = 0; $i < $this->ipsclass->member['g_multi_file_limit']; $i++ )
			{
				// Current Image
				$curr = "image{$i}";

				// Get the image details
				$file_name = $_FILES[$curr]['name'];

				// Do we have an image?
				if( empty( $file_name ) || $file_name == "none" )
				{
					continue;
				}

				// Do thumbnail?
				if( $this->ipsclass->input['cat'] )
				{
					$thumb = ( $this->category['thumbnail'] && $this->ipsclass->vars['gallery_create_thumbs'] ) ? 1 : 0;
					$watermark = $this->category['watermark_images'];
					$container = $this->category['id'];
				}
				else
				{
					$thumb     = ( $this->ipsclass->vars['gallery_create_thumbs'] )  ? 1 : 0;
					$watermark = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? 1 : 0;
					$container = $this->album['id'];
				}

				// Process the file
				$new_file_info = $this->glib->process_uploaded_file( $curr, $thumb, $watermark, $container );

				// -------------------------------------------------------------
				// Insert information into the database, we're nearly home free now :D
				// -------------------------------------------------------------
				$insert = array( 'member_id'        => $this->ipsclass->member['id'],
				'category_id'      => ( $this->ipsclass->input['cat'] ) ? $this->ipsclass->input['cat'] : 0,
				'album_id'         => ( $this->ipsclass->input['album'] ) ? $this->ipsclass->input['album'] : 0,
				'caption'          => $file_name,
				'description'      => '',
				'approved'         => ( $this->category['approve_images'] ) ? 0 : 1,
				'thumbnail'        => $thumbnail,
				'views'            => 0,
				'comments'         => 0,
				'date'             => time(),
				'ratings_total'    => 0,
				'ratings_count'    => 0, );

				$insert = array_merge( $insert, $new_file_info );

				$this->ipsclass->DB->do_insert( 'gallery_images', $insert );
				$pid = $this->ipsclass->DB->get_insert_id();

				// -------------------------------------------------------------
				// Need to update the category information now
				// -------------------------------------------------------------
				if( $this->ipsclass->input['cat'] )
				{
					$msg = 'image_moderated';
					if( ! $this->category['approve_images'] )
					{
						$msg = 'image_uploaded';
						$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+1, last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}', last_member_id = {$this->ipsclass->member['id']}", "id={$this->category['id']}", 1 );
						$this->ipsclass->DB->simple_exec();

						if( $update_parents )
						{
							$this->ipsclass->DB->simple_update( 'gallery_categories', "last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}', last_member_id = {$this->ipsclass->member['id']}", "id IN ( {$update_parents} )", 1 );
							$this->ipsclass->DB->simple_exec();
						}
					}
					else
					{
						$this->ipsclass->DB->simple_update( 'gallery_categories', "mod_images=mod_images+1", "id={$this->category['id']}", 1 );
						$this->ipsclass->DB->simple_exec();					
					}					
				}
				else
				{
					// Update album stats
					$this->ipsclass->DB->simple_update( 'gallery_albums', "images=images+1, last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}'", 'id='.$this->ipsclass->input['album'], 1 );
					$this->ipsclass->DB->simple_exec();

					// Update category stats
					if( $this->album['category_id'] )
					{
						$this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+1, last_pic={$pid}, last_name='{$this->ipsclass->member['members_display_name']}', last_member_id = {$this->ipsclass->member['id']}", 'id='.$this->album['category_id'], 1 );
						$this->ipsclass->DB->simple_exec();
					}
				}
			}

			if( $this->ipsclass->input['cat'] )
			{
				$this->ipsclass->print->redirect_screen( $this->ipsclass->lang[$msg], '&automodule=gallery&cmd=sc&cat='.$this->category['id'] );
			}
			else
			{
				$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['image_uploaded'], '&automodule=gallery&cmd=albums' );
			}

		}

		/**
		* process_edit()
		*
		* Edit's an image, checks permissions
		*
		* @return none
		**/
		function process_edit()
		{
			require( $this->ipsclass->gallery_root . 'lib/image.php' );
             
			// -------------------------------------------------------------
			// Analayze Upload
			// -------------------------------------------------------------

			// Get the image details
			$type = ( isset( $_FILES['media'] ) ) ? 'media' : 'image';
			$file_name = $_FILES[$type]['name'];

			// Did we find an image?
			$upload = true;
			if( empty( $file_name ) || $file_name == "none" )
			{
				$upload = false;
			}

			// -------------------------------------------------------------
			// Empty Primary Fields Check
			// -------------------------------------------------------------
			if( empty( $this->ipsclass->input['caption'] ) )
			{
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_caption' ) );
			}

			// -------------------------------------------------------------
			// Empty Custom Fields Check
			// -------------------------------------------------------------
			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, type, required',
			'from'   => 'gallery_form_fields',
			'where'  => "id > 3" ) );
			$this->ipsclass->DB->simple_exec();


			if( $this->ipsclass->DB->get_num_rows() )
			{
				while( $i = $this->ipsclass->DB->fetch_row() )
				{
					$fields[$i['id']] = $i;
				}

				foreach( $fields as $id => $field )
				{
					$t_field = "field_$id";
					if( $field['required'] && empty( $this->ipsclass->input[$t_field] ) && $field['type'] != 'sepr' && $field['type'] != 'date' )
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'custom_empty' ) );
					}
				}
			}

			if( $upload )
			{
				// Delete the old stuff
				$dir = ( $this->img['directory'] ) ? "{$this->img['directory']}/" : "";
				@unlink( $this->ipsclass->vars['gallery_images_path'].'/'.$dir.$this->img['masked_file_name'] );
				if( $this->img['thumbnail'] )
				{
					@unlink( $this->ipsclass->vars['gallery_images_path'].'/'.$dir.'tn_'.$this->img['masked_file_name'] );
				}

				// Get our settings
				$settings = $this->_get_settings();

				$new_file_info = $this->glib->process_uploaded_file( 'image', $settings['thumb'], $settings['watermark'], $settings['container'], $settings['allow_media'] );
			}


			// -------------------------------------------------------------
			// Update the info
			// -------------------------------------------------------------
			$this->parser->parse_smilies = 1;
			$description = $this->han_editor->process_raw_post( 'Post' );
			$description = $this->parser->pre_db_parse( $description );

			$html = $this->category['allow_html'] AND $this->ipsclass->member['g_dohtml'] ? 1 : 0 ;
			
			$update = array( 'caption'          => $this->ipsclass->input['caption'],
			'description'      => $description 
			);
			
			/**
			* Check copyright information
			**/
			if( $this->ipsclass->vars['gallery_allow_usercopyright'] != 'disabled' )
			{
				$update['copyright'] = ( $this->ipsclass->vars['gallery_allow_usercopyright'] == 'yes' ) ? $this->ipsclass->input['field_copyright'] : $this->ipsclass->vars['gallery_copyright_default'];
			}

			// -------------------------------------------------------------
			// We need to insert the custom form data as well
			// -------------------------------------------------------------
			if( is_array( $fields ) )
			{
				foreach( $fields as $id => $field )
				{
					$t_field = "field_$id";

					if( $field['type'] == 'date' )
					{
						$update[$t_field] = $this->_format_date_field( $this->ipsclass->input[$t_field.'_month'], $this->ipsclass->input[$t_field.'_day'], $this->ipsclass->input[$t_field.'_year'], $this->ipsclass->input[$t_field.'_hour'], $this->ipsclass->input[$t_field.'_minute'] );
					}
					else if( $field['type'] != 'sepr' )
					{
						$update[$t_field] = $this->ipsclass->input[$t_field];
					}
				}
			}
			// -------------------------------------------------------------
			// Merge the file data, if it exists
			// -------------------------------------------------------------
			if( is_array( $new_file_info ) )
			{
				$update = array_merge( $update, $new_file_info );
			}

			$this->ipsclass->DB->do_update( 'gallery_images', $update, "id={$this->ipsclass->input['img']}" );

			$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['image_edited'], "&automodule=gallery&cmd=si&img={$this->img['id']}" );
		}

		/**
		* _get_settings()
		*
		* Returns settings used for uploading images
		*
		* @return array
		**/
		function _get_settings()
		{
			// -----------------------------------
			// Category Settings
			// -----------------------------------
			if( $this->category )
			{
				$thumb          = ( $this->category['thumbnail'] && $this->ipsclass->vars['gallery_create_thumbs'] ) ? 1 : 0;
				$watermark      = $this->category['watermark_images'];
				if ( !$this->ipsclass->member['g_movies'] || !$this->category['allow_movies'] )  
            	{
            		$allow_media = 0;
            	}
            	else
            	{
            		$allow_media = 1;
            	}
				$container      = $this->category['id'];
				$html           = $this->category['allow_html'] AND $this->ipsclass->member['g_dohtml'] ? 1 : 0 ;
				$approve_images = ( $this->category['approve_images'] ) ? 0 : 1;
				$code           = $this->category['allow_ibfcode'];
			}
			// -----------------------------------
			// Album Settings
			// -----------------------------------
			else
			{
				$thumb          = ( $this->ipsclass->vars['gallery_create_thumbs'] )  ? 1 : 0;
				$watermark      = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? 1 : 0;
				$allow_media 	= $this->ipsclass->member['g_movies'];
				$container      = $this->album['id'];
				$html           = $this->ipsclass->member['g_dohtml'] ? 1 : 0 ;
				$approve_images = 1;
				$code           = 1;
			}

			return array( 'thumb'     => $thumb,
			'watermark' => $watermark,
			'html'      => $html,
			'allow_media' => $allow_media,
			'approve'   => $approve_images,
			'container' => $container,
			);
		}

		/**********************************************************************
		*
		* Form Generation Methods
		*
		**********************************************************************/


		/**
		* _add_text_field()
		*
		* Adds a text field to the current form.  $field contains the id, name, and
		* description for the custom form element being added.  $def is the default
		* value for the field
		*
		* @param array $field
		* @param string $def
		* @return none
		**/
		function _add_text_field( $field, $def='' )
		{
			$field_name = "field_{$field['id']}";
			$def = ( $def ) ? $def: $this->ipsclass->input[$field_name];

			$this->output .= $this->html->post_form_row( $field['name'], $this->html->post_control_textbox( $field_name, $def ), $field['description'] );
		}

		/**
		* _add_field_sep()
		*
		* Adds a seperator to the form, $field contans the id name, and
		* description for the custom form element being added.  $def is the default
		* value for the field
		*
		* @param array $field
		* @return none
		**/
		function _add_field_sep( $field )
		{
			$this->output .= $this->html->post_form_sep( $field['name'], $field['description'] );
		}


		/**
		* _add_textarea_field()
		*
		* Adds a text area to the current form.  $field contains the id, name, and
		* description for the custom form element being added.  $def is the default
		* value for the field
		*
		* @param array $field
		* @param string $def
		* @return none
		**/
		function _add_textarea_field( $field, $def='' )
		{
			$field_name = "field_{$field['id']}";
			$def = ( $def ) ? $def: $this->ipsclass->input[$field_name];
			$this->output .= $this->html->post_form_row( $field['name'], $this->html->post_control_textarea( $field_name, $def ), $field['description'] );
		}


		/**
		* _add_date_field()
		*
		* Adds a date field to the current form.  $field contains the id, name, and
		* description for the custom form element being added.  $def is the default
		* value for the field
		*
		* @param array $field
		* @param string $def
		* @return none
		**/
		function _add_date_field( $field, $def='' )
		{
			$field_name = "field_{$field['id']}";

			if( $def )
			{
				$curr = $this->_unconvert_date_field( $def, 1 );
			}
			else
			{
				$curr = localtime( time(), true );

				$curr = array( 'day'    => $curr['tm_mday'],
				'month'  => $curr['tm_mon'] + 1,
				'year'   => 1900 + $curr['tm_year'],
				'minute' => $curr['tm_min'],
				'hour'   => $curr['tm_hour'],
				);
			}


			// Days
			for( $k = 1; $k <= 31; $k++ )
			{
				$days[$k] = $k;
			}

			// All the months
			$months = array( '01' => 'January', '02' => 'February', '03' => 'March'    , '04' => 'April'  , '05' => 'May'     , '06' => 'June',
			'07' => 'July'   , '08' => 'August'  , '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' );

			// A few years
			for( $k = 1900; $k <= 2004; $k++ )
			{
				$years[$k] = $k;
			}
			// Hours
			for( $k = 0; $k <= 24; $k++ )
			{
				$hours[$k] = $k;
			}

			// Minutes
			for( $k = 1; $k <= 60; $k++ )
			{
				$min[$k] = $k;
			}

			$out  = $this->_make_drop( $field_name.'_month', $months, $curr['month'] );
			$out .= $this->_make_drop( $field_name.'_day'   ,  $days , $curr['day'] );
			$out .= ', ';
			$out .= $this->_make_drop( $field_name.'_year' , $years , $curr['year'] );
			$out .= '&nbsp;&nbsp;';
			$out .= $this->_make_drop( $field_name.'_hour', $hours, $curr['hour'] );
			$out .= ' : ';
			$out .= $this->_make_drop( $field_name.'_minute' , $min , $curr['minute'] );

			$this->output .= $this->html->post_form_row( $field['name'], $out, $field['description'] );

		}


		/**
		* _format_date_field()
		*
		* Formats a date field for database insertion
		*
		* @param integer $month
		* @param integer $day
		* @param integer $year
		* @param integer $hour
		* @param integer $minute
		* @return string
		**/
		function _format_date_field( $month, $day, $year, $hour, $minute )
		{
			// --------------------------------------------
			// Name of the field in the database
			// --------------------------------------------
			$field_name = "field_{$field['id']}";

			// --------------------------------------------
			// An array of 31 days
			// --------------------------------------------
			for( $k = 1; $k <= 31; $k++ )
			{
				$days[$k] = $k;
			}

			// --------------------------------------------
			// All of the months
			// --------------------------------------------
			$months = array( '01' => 'January', '02' => 'February', '03' => 'March'    , '04' => 'April'  , '05' => 'May'     , '06' => 'June',
			'07' => 'July'   , '08' => 'August'  , '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' );

			// --------------------------------------------
			// Lots of years
			// --------------------------------------------
			for( $k = 1900; $k <= 2007; $k++ )
			{
				$years[$k] = $k;
			}

			// --------------------------------------------
			// 24 Hours
			// --------------------------------------------
			for( $k = 0; $k <= 24; $k++ )
			{
				$hours[$k] = $k;
			}

			// --------------------------------------------
			// 60 Minutes
			// --------------------------------------------
			for( $k = 1; $k <= 60; $k++ )
			{
				$min[$k] = $k;
			}

			$format = "<#MONTH#> <#DAY#>, <#YEAR#>&nbsp;&nbsp;<#HOUR#>:<#MINUTE#>";

			$nice = preg_replace( "/<#MONTH#>/" , $months[$month], $format );
			$nice = preg_replace( "/<#DAY#>/"   , $days[$day]    , $nice   );
			$nice = preg_replace( "/<#YEAR#>/"  , $years[$year]  , $nice   );
			$nice = preg_replace( "/<#HOUR#>/"  , $hours[$hour]  , $nice   );
			$nice = preg_replace( "/<#MINUTE#>/", $min[$minute]  , $nice   );

			return $nice;

		}

		/**
		* _unconvert_date_field()
		*
		* Nothing pretty about this function, but it does the job...assuming that the format
		* in _format_date_field isn't messed with
		*
		* @param string $date
		* @return array
		**/
		function _unconvert_date_field( $date )
		{
			// --------------------------------------------
			// Name of the field in the database
			// --------------------------------------------
			$field_name = "field_{$field['id']}";

			// --------------------------------------------
			// An array of 31 days
			// --------------------------------------------
			for( $k = 1; $k <= 31; $k++ )
			{
				$days[$k] = $k;
			}

			// --------------------------------------------
			// All of the months
			// --------------------------------------------
			$months = array( '01' => 'January', '02' => 'February', '03' => 'March'    , '04' => 'April'  , '05' => 'May'     , '06' => 'June',
			'07' => 'July'   , '08' => 'August'  , '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' );

			// --------------------------------------------
			// Lots of years
			// --------------------------------------------
			for( $k = 1900; $k <= 2007; $k++ )
			{
				$years[$k] = $k;
			}

			// --------------------------------------------
			// 24 Hours
			// --------------------------------------------
			for( $k = 0; $k <= 24; $k++ )
			{
				$hours[$k] = $k;
			}

			// --------------------------------------------
			// 60 Minutes
			// --------------------------------------------
			for( $k = 1; $k <= 60; $k++ )
			{
				$min[$k] = $k;
			}

			$temp = explode( " ", $date );

			// Get the month
			foreach( $months as $k => $v )
			{
				if( $v == $temp[0] )
				{
					$arr['month'] = $k;
					break;
				}
			}

			// Get the day
			$arr['day'] = preg_replace( "/(,)$/", "", $temp[1] );

			$temp = explode( "&nbsp;&nbsp;", $temp[2] );

			// Get the year
			$arr['year'] = $temp[0];

			$temp = explode( ":", $temp[1] );

			// Get the hour and minute
			$arr['hour'] = $temp[0];
			$arr['minute'] = $temp[1];

			return $arr;

		}

		/**
		* _add_dropdown_field()
		*
		* Adds a drop down field to the current form.  $field contains the id, name, and
		* description for the custom form element being added.  $def is the default
		* value for the field
		*
		* @param array $field
		* @param string $def
		* @return none
		**/
		function _add_dropdown_field( $field, $def='' )
		{
			$field_name = "field_{$field['id']}";
			$def = ( $def ) ? $def: $this->ipsclass->input[$field_name];

			$content = explode( '|', $field['content'] );

			foreach( $content as $tfield )
			{
				$temp = explode( "=", $tfield );
				$options[$temp[0]] = $temp[1];
			}


			$this->output .= $this->html->post_form_row( $field['name'], $this->_make_drop( $field_name, $options, $def ), $field['description'] );
		}


		/**
		* _make_drop()
		*
		* Generates a drop down box
		*
		* @param string $name
		* @param string $content
		* @param string $curr
		* @return string
		**/
		function _make_drop( $name, $content, $curr='' )
		{
			foreach( $content as $k => $v )
			{
				$sel = ( $k == $curr ) ? 'selected' : '';
				$options .= "<option value='{$k}' {$sel}>{$v}</option>";
			}

			$drop = $this->html->post_control_drop( $name, $options );

			return $drop;
		}

		/**
		* html_add_smilie_box()
		*
		* Adds a smiely box to the current form
		* Function taken from Post.php in source files
		*
		* @author Matt Mecham
		* @return none
		**/
		function html_add_smilie_box($in_html="")
		{
			//-----------------------------------------
			// Get post class and skin stuff
			//-----------------------------------------
			
			if ( ! is_object( $this->class_post ) )
			{
				require_once( ROOT_PATH.'sources/classes/post/class_post.php' );
				$this->class_post           =  new class_post;
				$this->class_post->ipsclass =& $this->ipsclass;
			}
			
			if ( ! is_object( $this->ipsclass->compiled_templates['skin_post'] ) )
			{
				$this->ipsclass->load_language('lang_post');
        		$this->ipsclass->load_template('skin_post');
			}
			
			//-----------------------------------------
			// Do it!
			//-----------------------------------------
			
			$this->output = $this->class_post->html_add_smilie_box( $this->output );
		}
		
		

		function smilie_alpha_sort($a, $b)
		{
			return strcmp( $a['typed'], $b['typed'] );
		}

		function _perm_chk( $perm, $mid )
		{
			if( $this->ipsclass->member['g_mod_albums'] && $this->album )
			{
				return true;
			}

			if( $this->ipsclass->check_perms( $this->category['perms_moderate'] ) )
			{
				return true;
			}

			if( $perm && $mid == $this->ipsclass->member['id'] )
			{
				return true;
			}

			echo "{$perm} {$mid} {$this->ipsclass->member['id']}";

			return false;
		}
		
		function check_multi_quote()
		{
			$add_tags = 0;
			
			if ( ! $this->ipsclass->input['gal_pids'] )
			{
				$this->ipsclass->input['gal_pids'] = $this->ipsclass->my_getcookie('gal_pids');
				if ($this->ipsclass->input['gal_pids'] == ",")
				{
					$this->ipsclass->input['gal_pids'] = "";
				}
			}

			$this->ipsclass->input['gal_pids'] = preg_replace( "/[^,\d]/", "", trim($this->ipsclass->input['gal_pids']) );

			if ( $this->ipsclass->input['gal_pids'] )
			{
				$this->ipsclass->my_setcookie('gal_pids', ',', 0);
				
				$this->quoted_pids = preg_split( '/,/', $this->ipsclass->input['gal_pids'], -1, PREG_SPLIT_NO_EMPTY );
				//-----------------------------------------
				// Get the posts from the DB and ensure we have
				// suitable read permissions to quote them
				//-----------------------------------------
				
				if ( count($this->quoted_pids) )
				{
					foreach( $this->quoted_pids as $pid )
					{
						$clean[] = $this->glib->validate_int( $pid );	
					}
					$this->quoted_pids = $clean;
					$this->ipsclass->DB->cache_add_query( 'comments_get_quoted', array( 'quoted_pids' => $this->quoted_pids ), 'gallery_sql_queries' );
					$q = $this->ipsclass->DB->cache_exec_query();
	
					while ( $tp = $this->ipsclass->DB->fetch_row( $q ) )
					{
						$this->allowed_cats = ( $this->allowed_cats ) ? $this->allowed_cats : $this->glib->get_allowed_cats();
					
							if ( $this->han_editor->method == 'rte' )
							{
								$tmp_post = $this->parser->convert_ipb_html_to_html(  $tp['comment'] );
							}
							else
							{
								$tmp_post = trim( $this->parser->pre_edit_parse( $tp['comment'] ) );
							}

							if ($this->ipsclass->vars['strip_quotes'])
							{
								$tmp_post = preg_replace( "#\[QUOTE(=.+?,.+?)?\].+?\[/QUOTE\]#is", "", $tmp_post );

								$tmp_post = preg_replace( "#(?:\n|\r){3,}#s", "\n", trim($tmp_post) );
							}

							if ( $tmp_post )
							{
								$raw_post .= "[quote name='".$this->parser->make_quote_safe($tp['author_name'])."' date='".$this->parser->make_quote_safe($this->ipsclass->get_date( $tp['post_date'], 'LONG', 1 ))."']\n$tmp_post\n[/quote]\n\n\n";
							}
					}
					
					$raw_post = trim($raw_post)."\n";
				}
			}
			
			if ( isset( $this->ipsclass->input['Post'] ) )
			{
				//-----------------------------------------
				// Raw post from preview?
				//-----------------------------------------
			
				$raw_post .= isset($_POST['Post']) ? $this->ipsclass->txt_htmlspecialchars($_POST['Post']) : "";
		
				if (isset($raw_post))
				{
					$raw_post = $this->ipsclass->txt_raw2form($raw_post);
				}
			}
			return $raw_post;
		}		
}
?>
