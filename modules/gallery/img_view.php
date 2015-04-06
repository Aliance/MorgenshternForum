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
* Main/Image View
*
* Module used for displaying an image, along with it's
* associated information such as comments
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

    class img_view
    {
        var $ipsclass;
        var $glib;

        var $output;
        var $info;
        var $html;
        var $shutdown_query = array();
        var $data;
        var $parser;

        var $is_moderator = false;

    	/**
    	 * img_view::start()
    	 *
		 * Begins execution of this module, $param is used as an entry point into the
		 * module.
		 *
    	 * @param string $param
    	 * @return none
    	 **/
    	function start( $param="" )
    	{
            // -------------------------------------------------------
            // Get the html and language files
            // -------------------------------------------------------
            
            /* Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_img'] ) ) {
            	$this->ipsclass->load_template('skin_gallery_img');
            }
            $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_img' ];
            $this->ipsclass->load_language( 'lang_topic' );

            // -------------------------------------------------------
            // Check Auth
            // -------------------------------------------------------
	        if( $this->ipsclass->member['id'] )
    	    {
    	        $perms = explode( ':', $this->ipsclass->member['gallery_perms'] );
    	        if( ! $perms[0] )
				{
		            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
				}
    	    }
    	    
    	    /**
			* Grab the parser
			*/
    	    require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
    	    $this->parser                      =  new parse_bbcode();
    	    $this->parser->ipsclass            =& $this->ipsclass;
    	    $this->parser->allow_update_caches = 1;

    	    $this->parser->parse_html    = 0;
    	    $this->parser->parse_nl2br   = 1;
    	    $this->parser->parse_smilies = 1;
    	    $this->parser->parse_bbcode  = 1;

    	    $this->parser->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);

            // -------------------------------------------------------
            // What's our entry point?
            // -------------------------------------------------------
            switch( $param )
            {
                case 'show':
                    $this->display_image( $this->ipsclass->input['img'] );
                break;
            }

            $this->ipsclass->print->add_output( $this->output );

            $this->ipsclass->print->do_output( array(
                                      'TITLE'    => $this->title,
            					 	  'NAV'      => $this->nav,
                             )       );
        }

    	/**
    	 * img_view::display_image()
    	 *
		 * Decides weither we are displaying an image in an album or category,
		 * then calls on the appropriate method
		 *
    	 * @param integer $img
    	 * @return none
    	 **/
    	function display_image( $img )
    	{
            // -------------------------------------------------------
            // Check the input
            // -------------------------------------------------------
            $img = intval( $img );
            
            if( ! $img )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'img_not_found' ) );
            }
			
            // -------------------------------------------------------
            // Get the image info
            // -------------------------------------------------------
            $this->ipsclass->DB->cache_add_query( 'get_image', array( 'img' => $img, 'member_id' => $this->ipsclass->member['id'] ), 'gallery_sql_queries' );
			$this->ipsclass->DB->simple_exec();
            $this->data = $this->ipsclass->DB->fetch_row();

            // -------------------------------------------------------
            // Did we find the image?
            // -------------------------------------------------------
            if( ! $this->ipsclass->DB->get_num_rows() )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'img_not_found' ) );
            }
			
            // -------------------------------------------------------
            // What kind of image are we viewing?
            // -------------------------------------------------------
            if( $this->data['album_id'] )
            {
                $this->show_album_image();
            }
            else
            {
                $this->show_cat_image();
            }

            // -------------------------------------------------------
            // Stat Update!
            // -------------------------------------------------------
            $views = $this->data['views'] + 1;
            $this->ipsclass->DB->do_shutdown_update( 'gallery_images', array( 'views' => $views ), "id={$this->data['id']}" );
        }

        /**
         * img_view::show_album_image()
         *
		 * Displays an image from an album, along with the associated information
		 *
         * @return none
         **/
        function show_album_image()
        {
            // -----------------------------------------------------
            // Load Album Data
            // -----------------------------------------------------
            $this->album = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'gallery_albums', 'where' => "id={$this->data['album_id']}" ) );

            // -------------------------------------------------------
            // Check Permissions
            // -------------------------------------------------------
            if( ! $this->album['public_album'] )
            {
                if( ( $this->album['member_id'] != $this->ipsclass->member['id'] ) && ! $this->ipsclass->member['g_mod_albums'] )
                {
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                }
            }
            
            // -------------------------------------------------------
            // Category Permissions
            // -------------------------------------------------------
			if( $this->album['category_id'] )
			{
				/* Get the category info & cache it */
				$this->ipsclass->DB->simple_construct( array( 'select' => 'id, password, name, perms_view', 'from' => 'gallery_categories', 'where' => "id={$this->album['category_id']}" ) );
				$this->ipsclass->DB->simple_exec();
				$cat = $this->ipsclass->DB->fetch_row();


				/* Check Permissions */
				if( ! $this->ipsclass->check_perms( $cat['perms_view'] ) )
				{
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
				}								
			}
	            

            // -------------------------------------------------------
            // Are we a moderator?
            // -------------------------------------------------------
            if( ($this->ipsclass->member['id'] == $this->album['member_id'] ) || $this->ipsclass->member['g_mod_albums'] )
            {
                $this->is_moderator = true;
            }

            // -------------------------------------------------------
            // Start formatting information
            // -------------------------------------------------------
			$ts_save = $this->data['date'];
            $this->data['author'] = $this->glib->make_name_link( $this->data['mid'] , $this->data['mname'] );
            $this->data['date']   = $this->ipsclass->get_date( $this->data['date'], 'LONG' );
            $this->data['description'] = $this->parser->pre_display_parse( $this->data['description'] );

            // -------------------------------------------------------
            // Rating Stuff
            // -------------------------------------------------------
			require( $this->ipsclass->gallery_root . 'rate.php' );
			$rate = new rate;
                        $rate->ipsclass =& $this->ipsclass;
                        $rate->glib =& $this->glib;
			$this->data['rate'] = $rate->rating_display( $this->html, $this->data );
	
            // -------------------------------------------------------
            // Do we get to see any special buttons?
            // -------------------------------------------------------
            $this->get_special_buttons( 'album' );

            // -------------------------------------------------------
            // Start formatting information
            // -------------------------------------------------------
            if( $this->data['media'] )
            {
                $ext  = '.' . strtolower( array_pop( explode( ".", $this->data['masked_file_name'] ) ) );
				$this->ipsclass->DB->simple_construct( array( "select" => '*', 'from' => 'gallery_media_types', 'where' => "extension LIKE '%{$ext}%'" ) );
                $this->ipsclass->DB->simple_exec();
                $media = $this->ipsclass->DB->fetch_row();

                $display = html_entity_decode( $media['display_code'] );

                $dir = ( $this->data['directory'] ) ? "{$this->data['directory']}/" : "";
                $themedia = $this->ipsclass->vars['gallery_images_url'].'/'.$dir.$this->data['masked_file_name'];

                $display = str_replace( "{FILE}", $themedia, $display );
                $this->data['image']  = $display;
            }
            else
            {
                $this->data['image']  = $this->glib->make_image_tag( $this->data, 0, 1 );
            }
            
            /**
            * 204: Format copyright correctly
            **/
            $this->data['copyright'] = html_entity_decode( $this->data['copyright'] );

            // -------------------------------------------------------
            // Show the image
            // -------------------------------------------------------
            $this->output .= $this->html->show_image( $this->data );

            // -------------------------------------------------------
            // Photostrip
            // -------------------------------------------------------
            if( $this->ipsclass->vars['gallery_display_photostrip'] == "on" )  {
				$this->show_photostrip( $ts_save );
            }

            // -------------------------------------------------------
            // Comment time!
            // -------------------------------------------------------
            require( $this->ipsclass->gallery_root . '/lib/comment_view.php' );
            $comments = new comment_view();
            $comments->ipsclass =& $this->ipsclass;
            $comments->glib =& $this->glib;
            $comments->data =& $this->data;
            $comments->is_moderator = &$this->is_moderator;
            $comments->init();
            $this->output .= $comments->get();

            // -------------------------------------------------------
            // Quick Reply
            // -------------------------------------------------------
            $this->output .= ( $comments->no_comments ) ? $this->html->page_end() : $this->html->page_end( $this->data );
            $allow_comments = ( $this->category->data ) ? $this->category->data[$this->data['category_id']]['allow_comments'] : 1;
            
            if( $this->ipsclass->member['g_comment'] && $allow_comments && !$comments->no_comments ) {
				$this->ipsclass->input['img'] = $this->data['id'];
	   			$this->output = str_replace( "<!--IBF.QUICK_REPLY_CLOSED-->", $this->html->quick_reply_box_closed(), $this->output );
	    		$this->output = str_replace( "<!--IBF.QUICK_REPLY_OPEN-->"  , $this->html->quick_reply_box_open($this->topic['forum_id'], $this->topic['tid'], $show, $this->md5_check), $this->output );
            }
            
            if( $this->is_moderator && !$comments->no_comments )
            {
            	/*
            	* Print comment mod form */
            	$this->output = str_replace( "<!--GALLERY_MOD_COMMENTS-->", $comments->mod_form(), $this->output );
            }
            
            // -------------------------------------------------------
            // Page Stuff
            // -------------------------------------------------------
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['viewing_img'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->data['member_id']}&amp;op=view_album&amp;album={$this->data['album_id']}'>{$this->album['name']}</a>";
            $this->nav[] = $this->ipsclass->lang['viewing_img'];
        }

        /**
         * img_view::show_cat_image()
         *
		 * Returns an image from a category, with all associated data
		 *
         * @return none
         **/
        function show_cat_image()
        {
            // -----------------------------------------------------
            // Load Category Data
            // -----------------------------------------------------
            require( $this->ipsclass->gallery_root . 'categories.php' );
            $this->category = new Categories;
            $this->category->ipsclass =& $this->ipsclass;
            $this->category->glib =& $this->glib;
            $this->category->read_data( true, 'Select a category' );
            $this->cat = $this->category->data[$this->data['category_id']];

            // -------------------------------------------------------
            // Check Permissions
            // -------------------------------------------------------
            $this->glib->check_cat_auth( $this->cat['id'], $this->cat['password'], $this->cat['perms_view'] );

            // -------------------------------------------------------
            // Are we a moderator?
            // -------------------------------------------------------
            if( $this->ipsclass->check_perms( $this->cat['perms_moderate'] ) )
            {
                $this->is_moderator = true;
            }

            // -------------------------------------------------------
            // Has this image been approved?
            // -------------------------------------------------------
            if( ! $this->is_moderator && ! $this->data['approved'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }

            // -------------------------------------------------------
            // Start formatting information
            // -------------------------------------------------------
            if( $this->data['media'] )
            {
            	$_ext = explode( ".", $this->data['masked_file_name'] );
                $ext  = '.' . strtolower( array_pop( $_ext ) );
				$this->ipsclass->DB->simple_construct( array( "select" => '*', 'from' => 'gallery_media_types', 'where' => "extension LIKE '%{$ext}%'" ) );
                $this->ipsclass->DB->simple_exec();
                $media = $this->ipsclass->DB->fetch_row();

                $display = stripslashes( html_entity_decode( $media['display_code'] ) );

                $dir = ( $this->data['directory'] ) ? "{$this->data['directory']}/" : "";
                $themedia = $this->ipsclass->vars['gallery_images_url'].'/'.$dir.$this->data['masked_file_name'];

                $display = str_replace( "{FILE}", $themedia, $display );
                $this->data['image']  = $display;
            }
            else
            {
                $this->data['image']  = $this->glib->make_image_tag( $this->data, 0, 1 );
            }
            
            $this->data['author'] = $this->glib->make_name_link( $this->data['mid'] , $this->data['mname'] );

			$save_ts = $this->data['date'];

            $this->data['date']   = $this->ipsclass->get_date( $this->data['date'], 'LONG' );

            // -------------------------------------------------------
            // Rating Stuff
            // -------------------------------------------------------
			require( $this->ipsclass->gallery_root . 'rate.php' );
			$rate = new rate;
                        $rate->ipsclass =& $this->ipsclass;
                        $rate->glib =& $this->glib;
			$this->data['rate'] = $rate->rating_display( $this->html, $this->data );

            // -------------------------------------------------------
            // Do we get to see any special buttons?
            // -------------------------------------------------------
            $this->get_special_buttons( 'cat' );

            /**
            * 204: Format copyright correctly
            **/
            $this->data['copyright'] = html_entity_decode( $this->data['copyright'] );
            $this->data['description'] = $this->parser->pre_display_parse( $this->data['description'] );
            
            // -------------------------------------------------------
            // Show the image
            // -------------------------------------------------------
            $this->output .= $this->html->show_image( $this->data );

            // -------------------------------------------------------
            // Photostrip
            // -------------------------------------------------------
            if( $this->ipsclass->vars['gallery_display_photostrip'] == "on" )  {
				$this->show_photostrip( $save_ts );
            }

            // -------------------------------------------------------
            // Comment time!
            // -------------------------------------------------------
            require( $this->ipsclass->gallery_root . '/lib/comment_view.php' );
            $comments = new comment_view();
            $comments->ipsclass =& $this->ipsclass;
            $comments->glib =& $this->glib;
            $comments->data =& $this->data;
            $comments->is_moderator = &$this->is_moderator;
            $comments->init();
            $this->output .= $comments->get();

            // -------------------------------------------------------
            // Quck Reply
            // -------------------------------------------------------
            $this->output .= ( $comments->no_comments ) ? $this->html->page_end() : $this->html->page_end( $this->data );
            
            if( $this->ipsclass->check_perms( $this->cat['perms_comments'] ) && $this->ipsclass->member['g_comment'] && $this->cat['allow_comments'] && !$comments->no_comments )
            {
		    	$show = "none";

			    $sqr = $this->ipsclass->my_getcookie("open_qr");

    			if ( $sqr == 1 )
	    		{
		    		$show = "show";
    			}

				$this->ipsclass->input['img'] = $this->data['id'];
	    		$this->output = str_replace( "<!--IBF.QUICK_REPLY_CLOSED-->", $this->html->quick_reply_box_closed(), $this->output );
		    	$this->output = str_replace( "<!--IBF.QUICK_REPLY_OPEN-->"  , $this->html->quick_reply_box_open($this->topic['forum_id'], $this->topic['tid'], $show, $this->md5_check), $this->output );
    		}
    		
    		if( $this->is_moderator && !$comments->no_comments )
            {
            	/*
            	* Print comment mod form */
            	$this->output = str_replace( "<!--GALLERY_MOD_COMMENTS-->", $comments->mod_form(), $this->output );
            }

            // -------------------------------------------------------
            // Page Stuff
            // -------------------------------------------------------
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['viewing_img'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav = array_merge( $this->nav, $this->category->_build_category_breadcrumbs( $this->cat['id'] ) );
            $this->nav[] = $this->ipsclass->lang['viewing_img'];
        }

        /**
         * img_view::show_photostrip()
         *
		 * Displays the photstrip for this category/album
		 *
         * @param integer $timestamp
         * @return none
         **/
		function show_photostrip( $timestamp )
		{
			// -------------------------------------------------------
			// Category or album?
			// -------------------------------------------------------
			if( $this->data['category_id'] )
			{
				$where = "category_id={$this->data['category_id']}";
				$name  = $this->category->data[$this->data['category_id']]['name'];
			}
			else
			{
				$where = "album_id={$this->data['album_id']}";
				$name  = $this->album['name'];
			}

            // -------------------------------------------------------
			// Previous two images
            // -------------------------------------------------------

			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, caption, masked_file_name, directory, media, thumbnail',
				                          'from'   => 'gallery_images',
				                          'where'  => "{$where} AND id > {$this->ipsclass->input['img']}",
				                          'order'  => "id ASC",
                        				  'limit'  => array( 0,2 )
				                         ) );
			$res = $this->ipsclass->DB->simple_exec();

			// How many images did we find
			$total = $this->ipsclass->DB->get_num_rows( $res );
			if( $total == 2 )
			{
				$ctrl = array( ' <{GALLERY_LEFT_IMAGE}> ', ' <{GALLERY_LEFT_IMAGE}><{GALLERY_LEFT_IMAGE}> ' );
			}
			else
			{
				$ctrl = array( ' <{GALLERY_LEFT_IMAGE}> ' );
			}

			// Loop through the images we found
			$total = 0;
			$prev = array();
			while( $info = $this->ipsclass->DB->fetch_row( $res ) )
			{
				$prev[] = $this->html->photostrip_noncurr( array( 'img'     => $this->glib->make_image_link( $info, $info['thumbnail'] ),
					                                              'control' => "<a href='{$this->ipsclass->base_url}act=module&module=gallery&cmd=si&img={$info['id']}'>{$ctrl[$total]}</a>" ) );
				$total++;
			}

			$prev = implode( "", array_reverse( $prev ) );

			// Add spacers if needed
			for( $i = $total; $i < 2; $i++ )
			{
				$spacer .= $this->html->photostrip_noncurr( array( 'img'     => '&nbsp;',
					                                               'control' => '&nbsp;' ) );
			}
			$images = $images . $spacer . $prev;


            // -------------------------------------------------------
			// Current image
            // -------------------------------------------------------
			$images .= $this->html->photostrip_curr( $this->glib->make_image_tag( $this->data, $this->data['thumbnail'], 0 ) );

            // -------------------------------------------------------
			// Next two images
            // -------------------------------------------------------
			$this->ipsclass->DB->simple_construct( array( 'select' => 'id, caption, masked_file_name, directory, media, thumbnail',
				                          'from'   => 'gallery_images',
				                          'where'  => "{$where} AND id < {$this->ipsclass->input['img']}",
				                          'order'  => "id DESC",
				                          'limit'  => array( 0,2 ) ) );
			$res = $this->ipsclass->DB->simple_exec();

			// Loop through current images
			$ctrl  = '';
			$total = 0;
			while( $info = $this->ipsclass->DB->fetch_row( $res ) )
			{
				$ctrl .= '<{GALLERY_RIGHT_IMAGE}>';
				$images .= $this->html->photostrip_noncurr( array( 'img'     => $this->glib->make_image_link( $info, $info['thumbnail'] ),
					                                               'control' => "<a href='{$this->ipsclass->base_url}act=module&module=gallery&cmd=si&img={$info['id']}'>{$ctrl}</a>" ) );
				$total++;
			}

			// Add spacers if needed
			for( $i = $total; $i < 2; $i++ )
			{
				$images .= $this->html->photostrip_noncurr( array( 'img'     => '&nbsp;',
					                                               'control' => '&nbsp;' ) );
			}

			$this->output .= $this->html->photostrip( array( 'images' => $images, 'name' => $name ) );
		}

        /**
         * img_view::get_special_buttons()
         *
		 * Checks to see if the user can view any special buttons, these include
		 * edit, delete, move, pin, ecard, favorite, add reply
		 *
         * @param string $mode
         * @return none
         **/
        function get_special_buttons( $mode='cat' )
        {
			// -------------------------------------------------------
	        // Edit Image Button
		    // -------------------------------------------------------
            if( $this->is_moderator || ( $this->data['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_edit_own'] ) )
            {
                $this->data['edit_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=editimg&amp;img={$this->data['id']}{$media}'><{P_EDIT}></a>";
            }

			// -------------------------------------------------------
	        // Move Image Button
		    // -------------------------------------------------------
            if( ( $this->is_moderator || ( $this->data['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_move_own'] ) ) && $this->category )
            {
                $this->data['move_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=moveimg&amp;img={$this->data['id']}'><{GALLERY_MOVE}></a>";
            }

 			// -------------------------------------------------------
	        // Delete Image Button
		    // -------------------------------------------------------
            if( $this->is_moderator || ( $this->data['member_id'] == $this->ipsclass->member['id'] && $this->ipsclass->member['g_del_own'] ) )
            {
                $this->data['delete_button'] = "<a href='javascript:delete_img(\"?act=module&amp;module=gallery&amp;cmd=delimg&amp;img={$this->data['id']}\")'><{P_DELETE}></a>";
            }

			// -------------------------------------------------------
	        // Pin Image Button
		    // -------------------------------------------------------
            if( $this->is_moderator )
            {
                $this->data['pin_button'] = ( ! $this->data['pinned'] ) ? "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=mod&amp;op=pin&amp;img={$this->data['id']}'><{GALLERY_PIN}></a>" : "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=mod&amp;op=unpin&amp;img={$this->data['id']}'><{GALLERY_UNPIN}></a>";
            }

			// -------------------------------------------------------
	        // E-Card Button
		    // -------------------------------------------------------
            if( $this->ipsclass->vars['gallery_use_ecards'] && $this->ipsclass->member['g_ecard'] )
            {
               $this->data['ecard_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=ecard&amp;img={$this->data['id']}'><{GALLERY_ECARD}></a>";
            }

			// -------------------------------------------------------
	        // Favorite Button
		    // -------------------------------------------------------
            if( $this->ipsclass->member['g_favorites'] && $this->ipsclass->member['id'] != 0 )
            {
                $this->data['favorite_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=favs&amp;op=add&amp;img={$this->data['id']}'><{GALLERY_FAV}></a>";
            }

			// -------------------------------------------------------
	        // Comment Button
		    // -------------------------------------------------------
            if( $mode == 'cat' )
            {
                if( $this->ipsclass->check_perms( $this->cat['perms_comments'] ) && $this->ipsclass->member['g_comment'] && $this->cat['allow_comments'] )
                {
                    $this->data['comment_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=postcomment&amp;img={$this->data['id']}'><{A_COMMENT}></a>";
                }
                else {
                	$this->data['comment_button'] = '';
                }
            }
            else
            {
                if( $this->ipsclass->member['g_comment'] )
                {
                    $this->data['comment_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=postcomment&amp;img={$this->data['id']}'><{A_COMMENT}></a>";
                }

            }
            
            // -------------------------------------------------------
            // Report Image Button
            // -------------------------------------------------------
            if( ! $this->ipsclass->vars['gallery_disable_report_images'] )
            {
            	$this->data['report_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=mod&amp;op=reportimage&amp;img={$this->data['id']}'><{P_REPORT}></a>";
            }

			// -------------------------------------------------------
	        // Get URL Link
		    // -------------------------------------------------------
            if( $this->ipsclass->member['g_img_local'] )
            {
                $dir = ( $this->data['directory'] ) ? "{$this->data['directory']}/" : "";
                $url = $this->ipsclass->vars['gallery_images_url'] . '/' . $dir . $this->data['masked_file_name'];
                $this->data['get_url'] = "<a href='#' onclick='get_url(\"{$url}\"); return false;'>{$this->ipsclass->lang['click']}</a>";
            }
        }
    }
?>
