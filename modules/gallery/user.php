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
* Main/User
*
* Lists user's albums, images, forum images
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

    class user
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;
        var $img_list;
        var $albums;

    	/**
    	 * user::start()
    	 * 
		 * Begins execution of this module, $param is used as an entry point into the
		 * module.
		 * 
    	 * @param string $param
    	 * @return nothing
    	 **/
    	function start( $param="" )
    	{
            /*
            * Check input
            */
            $this->ipsclass->input['st'] = $this->glib->validate_int( $this->ipsclass->input['st'] );
            $this->ipsclass->input['user'] = $this->glib->validate_int( $this->ipsclass->input['user'] );
            
            if( ! $this->ipsclass->input['user'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }

			// Get the skin	            
			
			/* Fatal error bug fix */
           if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_user'] ) ) {
           		$this->ipsclass->load_template( 'skin_gallery_user' );
           }
           $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_user' ];
		
			// Get our image listing class
            require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );
            $this->img_list = new ImageListing();
            $this->img_list->ipsclass =& $this->ipsclass;
            $this->img_list->glib =& $this->glib;
            $this->img_list->init();
            
            // Get the container class
            require( $this->ipsclass->gallery_root . 'lib/album_control.php' );
            $this->albums = new album_control();            
            $this->albums->ipsclass =& $this->ipsclass;
            $this->albums->glib =& $this->glib;
           // $this->albums->html = &$this->html;
			
			// Entry point
            switch( $param )
            {
                case 'ipbimages':
                    $this->ipb_images();
                break;

                case 'view_album':
                    $this->view_album();
                break;

                case 'view_cat':
                    $this->view_cat();
                break;

                default:
                    $this->index();
                break;
            }
			
			// Do Output Stuff           
            $this->_load_menu();

    		$this->output .= "</td></tr></table>";
            
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];	
		
		    $this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery'>".$this->ipsclass->lang['gallery']."</a>";
    		$this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->ipsclass->input['user']}'>".$this->ipsclass->lang['user_info']."</a>";
    	
        	$this->ipsclass->print->add_output( $this->output );
            $this->ipsclass->print->do_output( array( 'TITLE' => $this->title, 'JS' => 1, NAV => $this->nav ) );
        }

		
        /**
         * user::index()
         * 
		 * Builds an list of all images this user has posted in the 
		 * gallery, user view permissions are also checked.
		 * 
         * @return nothing
         **/
        function index()
        {
            $mid = $this->ipsclass->input['user'];

            // Get Albums
			$this->albums->get_user_albums_thumbs( $mid );
			
            // Get Category Permissions
            $category_cache = $this->glib->get_allowed_cats();

            if( is_array( $category_cache ) )
            {
                $cats = implode( ",", $category_cache );
                      
                // Do page spanning stuff
                $all = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'count(id) AS total', 'from' => 'gallery_images', 'where' => " member_id={$mid} AND category_id IN ( {$cats} ) AND approved=1" ) );

                $this->ipsclass->input['st'] = ( intval( $this->ipsclass->input['st'] ) ) ? intval( $this->ipsclass->input['st'] ) : 0;
                $show = 9;
	    	    $all_imgs['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $all['total'],
                                                                    'PER_PAGE'    => $show,
                                                                    'CUR_ST_VAL'  => $this->ipsclass->input['st'],
                                                                    'BASE_URL'    => $this->base_url."?automodule=gallery&cmd=user&user={$mid}"
                                                            )     );
            
                // Get the data
                $this->img_list->get_listing_data( array(
                                                'st'        => $this->ipsclass->input['st'],
                                                'member'    => $mid,
                                                'show'      => $show,
                                                'approve'   => 1,
                                                'sort_key'  => 'i.id',
                                                'order_key' => 'DESC',
                                                'allow_cats' => $category_cache,

                                        )     );
            }
    
            // Start displaying
			if( $this->albums->total )
			{
	            $this->output .= $this->html->user_view_top();
				$this->output .= $this->albums->get_formated_albums_thumbs( $this->albums->data );
			}
			
	        $this->output .= $this->html->user_view_top_cat($all_imgs);

		    $this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => 3 ) );

		

            $this->output .= $this->html->img_view_end( 3, $all_imgs['SHOW_PAGES'] );

            $this->output = preg_replace( "/<#NOW_SHOWING#>/", ( $this->img_list->total_images ) ? $this->img_list->total_images : 0, $this->output );
            $this->output = preg_replace( "/<#TOTAL#>/",       ( $all['total'] ) ? $all['total'] : 0 , $this->output );
         }
         
        /**
         * user::ipb_images()
         * 
		 * Builds a list of all images the user has posted in the
		 * main forum.
		 * 
         * @return nothing
         **/
        function ipb_images()
        {
        	/**
        	* Templates
        	**/
            if( !is_object( $this->ipsclass->compiled_templates['skin_topic'] ) ) 
            {
		    	$this->ipsclass->load_template( 'skin_topic' );
            }
            $this->t_html = $this->ipsclass->compiled_templates[ 'skin_topic' ];
            
            /**
            * Input Validate
            **/
            $mid = $this->glib->validate_int( $this->ipsclass->input['user'] );

			// Get Albums
			$this->albums->get_user_albums( $mid );

            // Forum Filter
            $filter = $this->html->forum_filter( $this->ipsclass->build_forum_jump( 0, 1 ) );

            if( $this->ipsclass->input['f'] )
            {
                $forum_filter = " AND forum_id={$this->ipsclass->input['f']} ";
            }
            
            /**
            * Need to count images first
            * Not the best fix, but temp only until I can rewrite for 2.1
            **/
            $this->ipsclass->DB->cache_add_query( "count_ipb_images", array( 'mid' => $mid, 'forum_filter' => $forum_filter ), 'gallery_sql_queries' );
            $this->ipsclass->DB->simple_exec();
            $all = $this->ipsclass->DB->fetch_row();
            
            // Get the images  
            $this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;                          
            $this->ipsclass->DB->cache_add_query( "get_ipb_images", array( 'mid' => $mid, 'forum_filter' => $forum_filter, 'st' => $this->ipsclass->input['st'] ), 'gallery_sql_queries' );
            $this->ipsclass->DB->simple_exec();
            
            // Do some display related stuff
            $this->total_images = $this->ipsclass->DB->get_num_rows();
            $imgs['where']       = $this->ipsclass->lang['where_all_ipb'];
            $imgs['description'] = $filter;
            
            
            /**
            * Pull images, toss out the ones we aren't allowed to view
            * ( fixes bug #394
            **/
            if( $this->total_images )
            {
            	while( $i = $this->ipsclass->DB->fetch_row( $this->res ) )
            	{
            		$perms = unserialize( $i['permission_array'] );
            		if( ! $this->ipsclass->check_perms( $perms['read_perms'] ) )
            		{
            			$this->total_images--;
            			continue;
            		}
            		
            		/**
            		* We are allowed to view forum, and thus, images
            		**/
            		$this->images[] = $i;
            	}
            }
            if( is_array( $this->images ) )
            {		
            	/**
           		* Now do pagination
          		**/
            	$imgs['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $all['total'],
            	'PER_PAGE'    => 9,
            	'CUR_ST_VAL'  => $this->ipsclass->input['st'],
            	'BASE_URL'    => $this->base_url."?automodule=gallery&cmd=user&user={$mid}&op=ipbimages&f={$this->ipsclass->input['f']}"
            	)     );

				/**
				* Begin output
				**/
            	$this->output .= $this->html->img_view_top( $imgs );           
                $this->output .= $this->img_list->img_html->view_begin_row();
            
                // Loop through all our images
                foreach( $this->images as $i )
                {
                    if( $col_count >= 3 )
                    {
                        $this->output .= $this->img_list->img_html->view_end_row();
                        $this->output .= $this->img_list->img_html->view_begin_row();
                        $col_count = 0;
                    }
                    $col_count++;
                
					if ( $i['attach_thumb_location'] && $i['attach_thumb_width'] )
					{
						$i['image'] = $this->t_html->Show_attachments_img_thumb( $i['attach_thumb_location'],
																		$i['attach_thumb_width'],
																		$i['attach_thumb_height'],
																		$i['attach_id'],
																		$this->ipsclass->size_format( $i['attach_filesize'] ),
																		$i['attach_hits'],
																		$i['attach_file'],
																		'post'
																	  );
																	  
					}
					else
					{
						$i['image'] = $this->t_html->Show_attachments_img( $i['attach_location'] );
					}

  
                    $i['date']    = $this->ipsclass->get_date( $i['post_date'], 'LONG' );
                    $i['name']    = $this->glib->make_name_link( $i['author_id'], $i['author_name'] );
                    $i['v_post'] = "<a href='{$this->ipsclass->base_url}showtopic={$i['topic_id']}&amp;view=findpost&amp;p={$i['attach_pid']}'>View Post</a>";
         
                    $this->output .= $this->img_list->img_html->view_row_ipb( $i );

                }
            
                if( $col_count == 0 )
                {
                    $this->output .= $this->img_list->img_html->view_end_row();
                }
                
                $this->output .= $this->html->img_view_end( 3, $imgs['SHOW_PAGES'] );
            }
            else
            {
            	$this->output .= $this->html->img_view_top( $imgs );
                $this->output .= $this->img_list->img_html->basic_row( 'none_found' );
                $this->output .= $this->html->img_view_end();
            }
            
            $this->output = preg_replace( "/<#NOW_SHOWING#>/", $this->total_images, $this->output );
            $this->output = preg_replace( "/<#TOTAL#>/",       $all['total']      , $this->output );   
        }

        /**
         * user::view_album()
         * 
		 * Builds a list of all images posted in the specified album. Album
		 * permissions are checked before diplaying
		 * 
         * @return nothing
         **/
        function view_album()
        {
            $mid = intval( $this->ipsclass->input['user'] );

            if( $mid == $this->ipsclass->member['id'] )
            {
                $own = true;
            }
			// Get Albums
			$this->albums->get_user_albums( $mid );

            $this->ipsclass->input['album'] = intval( $this->ipsclass->input['album'] );
            if( ! $this->ipsclass->input['album'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }
			
			// Get the album data
            $album = $this->albums->get_album( $this->ipsclass->input['album'] );
			
			// Build page spanning links
            $this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;
            $show = 9;

			$imgs['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $album['images'],
                                                                'PER_PAGE'    => $show,
                                                                'CUR_ST_VAL'  => $this->ipsclass->input['st'],
                                                                'BASE_URL'    => $this->ipsclass->base_url."automodule=gallery&cmd=user&user={$mid}&op=view_album&album={$this->ipsclass->input['album']}"
                                                        )     );           

            // Get the data
            $this->img_list->get_listing_data( array(
                                            'st'         => $this->ipsclass->input['st'],
                                            'show'       => $show,
                                            'approve'    => 1,
                                            'sort_key'   => 'i.id',
                                            'order_key'  => 'DESC',
                                            'album'      => $this->ipsclass->input['album'],
                                            'pinned'     => 1,
                                    )     );

            // Start displaying
            $imgs['description'] = "{$album['description']}";
            $imgs['where'] = $this->ipsclass->lang['where_album'] . " {$album['name']}";
              
         //   $this->output .= '<script language="javascript" src="jscripts/ipb_galleryjs.js"></script>';
            if( $own )
            {
                $imgs['upload'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;album={$album['id']}'><{GALLERY_UPLOAD}></a>";
                $imgs['edit'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=albums&amp;op=edit&amp;album={$album['id']}'><{P_EDIT}></a>";
                if( $this->ipsclass->member['g_multi_file_limit'] > 1 )  {
                	$imgs['multi_upload_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;album={$album['id']}&amp;multi=1'><{GALLERY_MULTI_UPLOAD}></a>";
                }
            }

			$imgs['slideshow'] = ( $this->ipsclass->member['g_slideshows'] ) ? "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=slideshow&amp;album={$album['id']}'><{A_SSBUTTON}></a>" : "";

            $this->output .= $this->html->img_view_top( $imgs );
            $this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => 3 ) );
            $this->output .= $this->html->img_view_end( 3, $imgs['SHOW_PAGES'] );

            $this->output = preg_replace( "/<#NOW_SHOWING#>/", $this->img_list->total_images, $this->output );
            $this->output = preg_replace( "/<#TOTAL#>/",       $album['images']      , $this->output );

        }

       	/**
       	 * user::_load_menu()
		 * 
		 * Generates the sidebar of the user page
       	 * 
       	 * @return  nothing
       	 **/
       	function _load_menu()
    	{
    		/**
    		* Load up information about the user
    		**/
            $mid = $this->ipsclass->input['user'];
            
            /**
            * Get mgroup
            **/
            $member = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'mgroup', 'from' => 'members', 'where' => "id={$mid}" ) );
            
            // ---------------------------------------------
            // Get Image Totals
            // ---------------------------------------------
            $info = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'COUNT( id ) AS total_images, SUM( file_size ) AS total_diskspace', 'from' => 'gallery_images', 'where' => "member_id={$mid} AND approved=1" ) );

            // ---------------------------------------------
            // Get Comment Totals
            // ---------------------------------------------
            $data = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'COUNT( pid ) AS total_comments', 'from' => 'gallery_comments', 'where' => "author_id={$mid} AND approved=1" ) );
            $info = array_merge( $info, $data );

			// ---------------------------------------------
			// Get Album List
			// ---------------------------------------------
            $albums_list = $this->albums->get_album_text_links( $this->ipsclass->input['user'] );



            // ----------------------------------------
            // Do a diskspace used display
            // ----------------------------------------               
            if( $this->ipsclass->cache[ 'group_cache'][ $member['mgroup'] ]['g_max_diskspace'] )
            {
                $percent = round( $info['total_diskspace'] / $this->glib->kb_to_byte( $this->ipsclass->cache[ 'group_cache' ][ $member['mgroup'] ]['g_max_diskspace'] ), 2 ) * 100 . '%';
                $allowed = $percent . ' ' . $this->ipsclass->lang['dp_percent'];
            }
            
    		//--------------------------------------------
        	// Print the top button menu
        	//--------------------------------------------    
            $info['total_diskspace'] = $this->glib->byte_to_kb( $info['total_diskspace'] );
            $info['where']           = $this->ipsclass->lang['where_all'];
			$info['pub_albums']      = $albums_list['public_list'];
			$info['name']            = $albums_list['name'];

    		//--------------------------------------------
        	// Get the menu HTML
        	//-------------------------------------------- 
        	$menu_html = $this->html->user_bar( $info );
			$menu_html = str_replace( "<!-- PRIVATE_ALBUMS -->", $albums_list['private_list'], $menu_html );
            $menu_html = str_replace( "<!-- CONTROLS -->"      , $albums_list['controls']    , $menu_html ); 
            $menu_html = str_replace( "<!-- TOTAL_ALLOWED -->" , $allowed                    , $menu_html );

        	$this->ipsclass->print->add_output( $menu_html );
        }
            


    }
?>
