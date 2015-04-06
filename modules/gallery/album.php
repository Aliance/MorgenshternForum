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
* Main/Album Control
*
* Creates new member albums, upload to albums
* etc.
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

    class album
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;
        var $albums;
		
    	/**
    	 * album::start()
    	 * 
		 * Begins execution of this module, $param is used as an entry point into the
		 * module.
		 * 
    	 * @param string $param
    	 * @return none
    	 **/	
    	function start( $param="" )
    	{
    		/*
    		* Are they logged in? No guests */
    		if( !$this->ipsclass->member['id'] )  {
    			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    		}
    		
    		/**
    		 * Check some input
    		 */
    		$this->ipsclass->input['st'] = $this->glib->validate_int( $this->ipsclass->input['st'] );
    		
            if( $this->ipsclass->input['album'] )
            {
                $this->ipsclass->input['album'] = intval( $this->ipsclass->input['album'] );
                if( ! $this->ipsclass->input['album'] )
                {
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                }
            }

            if( ! $this->ipsclass->member['g_create_albums'] )
            {
                $param = "not_authorized";
            }
            
            // Get the album controller
    		require( $this->ipsclass->gallery_root . 'lib/album_control.php' );
    		$this->	albums = new album_control();  
                $this->albums->ipsclass =& $this->ipsclass;
                $this->albums->glib =& $this->glib;

                /* Load language file */
	    	$this->ipsclass->load_language( 'lang_ucp' );

	    	/*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_albums'] ) ) {
				$this->ipsclass->load_template( 'skin_gallery_albums' );
            }
	    	$this->html     = $this->ipsclass->compiled_templates[ 'skin_gallery_albums' ];
	    	
	    	/*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_ucp'] ) ) {
              	$this->ipsclass->load_template( 'skin_ucp' );
            }
        	$this->ucp_html = $this->ipsclass->compiled_templates[ 'skin_ucp' ];

            switch( $param )
            {
                case 'upload':
                    $this->upload_form();
                break;

                case 'del':
					if( $this->ipsclass->input['confirm_delete'] )
					{
						$this->_do_del();
					}
					else
					{
						$this->confirm_delete();
					}
                break;

                case 'edit':
                    $this->edit_album_form();
                break;
                
                case 'edit_album':
                    $this->_do_album_edit();
                break;

                case 'not_authorized':
                    $this->output .= $this->html->error( 'dead_section' );
                break;
                
                case 'create_album':
                    $this->_do_album_create();
                break;

                case 'addnew':
                    $this->add_new_form();
                break;

                case 'do_upload':
                    $this->_do_upload();
                break; 

                case 'buddyperms':
                	if( $this->ipsclass->input['do'] == "changeperms" )  {
                		$this->_buddy_perms();
                	}
                	else {
                		$this->buddy_perms();
                	}
               	break;

                default:
                    $this->index();
                break;
            }

            /*
            * Load up UserCP  */
            require_once( ROOT_PATH."sources/lib/func_usercp.php" );
			$this->ucp   = new func_usercp( &$this );
			$this->ucp->ipsclass = &$this->ipsclass;

	    	$this->output = $this->ucp->ucp_generate_menu() . $this->output;

			/*
			* Build forum jump, output */
    		$fj = $this->ipsclass->build_forum_jump();
			$fj = preg_replace( "!#Forum Jump#!", $this->ipsclass->lang['forum_jump'], $fj);

			$this->output .= $this->ucp_html->CP_end();
    		$this->output .= $this->ucp_html->forum_jump($fj, $links);
		
		    $this->nav[] = "<a href='".$this->ipsclass->base_url."act=UserCP&amp;amp;CODE=00'>".$this->ipsclass->lang['t_title']."</a>";
    		$this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery&amp;cmd=albums'>".$this->ipsclass->lang['m_gallery_albums']."</a>";
    	
        	$this->ipsclass->print->add_output($this->output);
            $this->ipsclass->print->do_output( array( 'TITLE' => $this->ipsclass->lang['m_gallery'], 'JS' => 1, NAV => $this->nav ) );

        }

        /**
         * album::index()
         * 
		 * Lists current albums along with options for each
		 * 
         * @return none
         **/
        function index()
        {
            // -----------------------------------------------------
            // Category Stuff, needed for getting the category name for
			// album's within a category
            // -----------------------------------------------------
            require( $this->ipsclass->gallery_root .  'categories.php' );
            $this->category = new Categories;
            $this->category->ipsclass =& $this->ipsclass;
            $this->category->glib =& $this->glib;
            $this->category->read_data( true, 'Select a category' );
			
			// Get the albums
    		$this->albums->get_user_albums( $this->ipsclass->member['id'] );
            
            // Did we find any albums?
            if( ! $this->albums->total )
            {
                $this->output .= $this->html->no_albums();
                return;
            }
			
			// Top of the album list
            $this->output .= $this->html->index_top();
			
			// List the albums
			foreach( $this->albums->data as $i )
            {
				$i['in_category'] = ( $i['category_id'] ) ? "<a href='{$this->ipsclass->base_url}&act=module&module=gallery&cmd=sc&cat={$i['category_id']}'>{$this->category->data[$i['category_id']]['name']}</a>" : '--';
				/*if( !$i['public_album'] )  {
					$i[ 'status' ] = "<a href='{$this->ipsclass->base_url}&automodule=gallery&cmd=albums&op=buddyperms&albumid={$i['id']}' title='{$this->ipsclass->lang['buddy_perms']}'>{$i['status']}</a>";
				}*/
                $this->output .= $this->html->index_row( $i );
            }
			
			// Bottom of the album list
            $this->output .= $this->html->index_end();
        }
        
        /**
        * album::_buddy_perms()
        * Commit perm changes 
        * @since 2.0
        **/
        function _buddy_perms()  {
        	$_perm_key = array( 'perm_view', 'perm_edit', 'perm_upload', 'perm_delete' );
        	/*
        	* Grab the persons friends  */
        	$this->glib->get_user_buddies( $this->ipsclass->member['id'] );
        	foreach( $this->glib->user->friends as $key=>$buddy )  {
        		$_perms = unserialize( $buddy['gallery_album_perms'] );
        		$_perms[ $this->ipsclass->input['album'] ] = array();
        		foreach( $_perm_key as $_perm ) {
        			$perms[ $_perm ] = ( $this->ipsclass->input[ $_perm . '_' . $buddy['contact_id'] ] == "on" ) ? 1 : 0;
        		}
        		$_perms[ $this->ipsclass->input['album'] ] = $perms;
        		
        		/*
        		* Run the update */
        		$_perms = serialize( $_perms );
        		$this->ipsclass->DB->do_update( "contacts", array( "gallery_album_perms" => $_perms ), "contact_id = {$buddy['contact_id']}" );
        		unset( $_perms );
        		unset( $perms );
        	}
        	
        	/*
        	* Weee */
        	$this->output .= $this->html->perms_finished();
        }
        
         /**
         * album::buddy_perms()
         * 
		 * Set up individual gallery album permissions on your buddies
		 * 
         * @since 2.0
         **/
         function buddy_perms()  {
         	if( intval( $this->ipsclass->input['albumid'] ) )  {
         		$this->albums->get_user_albums( $this->ipsclass->member['id'] );
         		$album = $this->albums->get_album( $this->ipsclass->input['albumid'], true );
         	}
         	/*  Lang replace */
         	$this->ipsclass->lang['perms_album'] = str_replace( "<#ALBUM#>", $album['name'], $this->ipsclass->lang['perms_album'] );
         	$this->output .= $this->html->buddy_perms_start( $album['id'] );
         	
         	/*
         	* Start looping through buddies and their permissions */
         	if( !$this->glib->get_user_buddies( $this->ipsclass->member['id'] ) )  {
         		/*
         		* No friends :( */
         		$this->output .= $this->html->buddy_no_friends();
         	}
         	else  {
         	
         		/* Buddies */
         		foreach( $this->glib->user->friends as $key=>$buddy )  {
         			if( empty( $buddy['gallery_album_perms'] ) ) {
         				/* No perms on any albums, EVARRR */
         				$buddy['perm_view'] ='';
         				$buddy['perm_edit'] ='';
         				$buddy['perm_upload'] ='';
         				$buddy['perm_delete'] ='';
         				$perms['perm_view'] ='aff_cross.gif';
         				$perms['perm_edit'] ='aff_cross.gif';
         				$perms['perm_upload'] ='aff_cross.gif';
         				$perms['perm_delete'] ='aff_cross.gif';
         			}
         			else {
         				$_perms = unserialize( $buddy['gallery_album_perms'] );
         				/*
         				* Now in format:
         				*  [ Album_Id ] => 	perm_view => int
         									perm_edit => int
         									perm_upload => int
         									perm_delete => int
         				*/
         				if( empty( $_perms[ $album['id'] ] ) )  {
         					/* No perms on any albums, EVARRR */
         					$buddy['perm_view'] ='';
         					$buddy['perm_edit'] ='';
         					$buddy['perm_upload'] ='';
         					$buddy['perm_delete'] ='';
         				}
         				else {
         					foreach( $_perms[ $album[ 'id' ] ] as $key=>$value )  {
         						$_key = ( $key . '_' . $buddy['contact_id'] );
         						$buddy[ $key ] = ( $value ) ? 'checked="CHECKED" ' : '';
         						$perms[ $key ] = ( $value ) ? 'aff_tick.gif' : 'aff_cross.gif';
         					}
         				}
         			}
         			/* Permissions */
         			$this->output .= $this->html->buddy_perms_buddy( $buddy );
         			$this->output .= $this->html->buddy_perms_perms_st();
         			$this->output .= $this->html->buddy_perms_perms_row( $perms );
         			$this->output .= $this->html->buddy_perms_perms_end();
         		}	
         	/*
         	* fin */
         	$this->output .= $this->html->buddy_perms_end();
         	
         	}
         }

        /**
         * album::_do_album_create()
         * 
		 * Inserts a new album into the database
		 * 
         * @return none
         **/
        function _do_album_create()
        {
        	if( !$this->ipsclass->vars[ 'gallery_album_create' ] )  {
        		/* All albums must be created via ACP */
        		$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        	}
            if( empty( $this->ipsclass->input['name'] ) )
            {
                $errors[] = $this->ipsclass->lang['album_no_name'];
            }
                
            if( empty( $this->ipsclass->input['description'] ) )
            {
                $errors[] = $this->ipsclass->lang['album_no_desc'];
            }
            
            /**
            * Not allowed to create in member's gallery?
            **/
            if( !$this->ipsclass->vars['gallery_user_category'] && $this->ipsclass->input['cat'] == 0 )
            {
            	$errors[] = $this->ipsclass->lang['album_no_mem_cat'];
            }

            if( $errors )
            {
                $this->add_new_form( $errors );
                return;
            }
            
            /**
            * Check the person's "has_gallery" var, if 0, this must be their first album, update
            **/
            if( !$this->ipsclass->member['has_gallery'] )
            {
            	$this->ipsclass->DB->do_update( 'members', array( 'has_gallery' => 1 ), "id={$this->ipsclass->member['id']}" );
            }

            $insert = array( 'member_id'   => $this->ipsclass->member['id'],
                             'public_album'=> $this->ipsclass->input['public'], 
                             'name'        => $this->ipsclass->input['name'],
                             'description' => $this->ipsclass->input['description'],
                             'category_id' => ( $this->ipsclass->input['cat'] ) ? intval( $this->ipsclass->input['cat'] ) : 0,
                           );

            $this->ipsclass->DB->do_insert( 'gallery_albums', $insert );

            $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['album_created'], 'automodule=gallery&cmd=albums' );

        }

        /**
         * album::add_new_form()
         * 
		 * Displays the form for adding a new album
		 * 
         * @param array $errors
         * @return none
         **/
        function add_new_form( $errors=array() )
        {
        	
        	/*
        	* Let user assign to category ( check perms ) */
        	require( $this->ipsclass->gallery_root .  'categories.php' );
            $category = new Categories;
            $category->ipsclass =& $this->ipsclass;
            $category->glib =& $this->glib;
            $category->restrict = true;
            
            /**
            * If the member's Gallery isn't enabled, set different default
            **/
            $root = ( $this->ipsclass->vars['gallery_user_category'] ) ? $this->ipsclass->lang['mem_gallery'] : $this->ipsclass->lang['cat_select'];
            $category->read_data( true, $root );
            
            /**
            * If mem gallery isn't enabled, and there's no
            * cat to create album in, kill album form
            **/
            if( !$this->ipsclass->vars['gallery_user_category'] && !$category->allowed_cats )
            {
            	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_allowed_cats' ) );
            }
            
            // ------------------------------------
            // Does this group have an album limit?
            // ------------------------------------
            if( $this->ipsclass->member['g_album_limit'] )
            {
                $this->ipsclass->DB->simple_construct( array( 'select' => 'count(id) AS total', 'from' => 'gallery_albums', 'where' => "member_id={$this->ipsclass->member['id']}" ) );
                $this->ipsclass->DB->simple_exec();

                $total = $this->ipsclass->DB->fetch_row();

                if( $total['total'] >= $this->ipsclass->member['g_album_limit'] )
                {
                    $this->output .= $this->html->error( 'album_nm' );
                    return;
                }
            }
            
            if( count( $errors ) )
            {
                $error_text = "<ul>";
                foreach( $errors as $error )
                {
                    $error_text .= "<li> {$error}";
                }
                $error_text .= "</ul>";

                $error_out = $this->html->form_errors( $error_text );

            }
            
           /**
           * Dern dropdown
           **/
           $default_cat = ( isset( $this->ipsclass->input['cat'] ) ) ? $this->ipsclass->input['cat'] : 0;
           $drop_down = $category->build_dropdown('cat', "", $default_cat );
     
            
            $this->output .= $this->html->album_form( array( 'type'        => $this->ipsclass->lang[ 'create_album' ],
            												 'type_op'		=> 'create_album',
                                                             'name'        => $this->ipsclass->input['name'],
                                                             'description' => $this->ipsclass->input['description'], 
                                                             'cat_dropdown' => $drop_down,
                                                             'public'      => ( $this->ipsclass->input['public'] ) ? 'checked' : '',
                                                             'errors'      => $error_out,
                                                             'q_cat'		=> false
                                                     )      );
            
        }

        /**
         * album::_do_album_edit()
         * 
		 * Proccess the request to edit an album
		 * 
         * @return none
         **/
        function _do_album_edit()
        {
            if( empty( $this->ipsclass->input['name'] ) )
            {
                $errors[] = $this->ipsclass->lang['album_no_name'];
            }
                
            if( empty( $this->ipsclass->input['description'] ) )
            {
                $errors[] = $this->ipsclass->lang['album_no_desc'];
            }

            /**
            * Any errors?
            **/
            if( $errors )
            {
                $this->edit_album_form( $errors );
                return;
            }

            $update = array( 'member_id'   => $this->ipsclass->member['id'],
                             'public_album'=> $this->ipsclass->input['public'], 
                             'name'        => $this->ipsclass->input['name'],
                             'description' => $this->ipsclass->input['description'] );

            $this->ipsclass->DB->do_shutdown_update( 'gallery_albums', $update, "id={$this->ipsclass->input['album']}" );

            $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['album_edited'], 'act=module&amp;module=gallery&amp;cmd=albums' );
        }


        /**
         * album::edit_album_form()
         * 
		 * Displays the edit album form
		 * 
         * @param array $errors
         * @return none
         **/
        function edit_album_form( $errors=array() )
        {
            // ------------------------------------
            // Does this group have an album limit?
            // ------------------------------------
            $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_albums', 'where' => "id={$this->ipsclass->input['album']}" ) );
            $this->ipsclass->DB->simple_exec();
            $album = $this->ipsclass->DB->fetch_row();

            if( $album['member_id'] != $this->ipsclass->member['id'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }
            
            if( count( $errors ) )
            {
                $error_text = "<ul>";
                foreach( $errors as $error )
                {
                    $error_text .= "<li> {$error}";
                }
                $error_text .= "</ul>";

                $error_out = $this->html->form_errors( $error_text );

            }

            $this->output .= $this->html->album_form( array( 'type'        => $this->ipsclass->lang[ 'edit_album' ],
            												 'type_op'		=> 'edit_album',
                                                             'name'        => $album['name'],
                                                             'description' => $album['description'], 
                                                             'cat_dropdown' => $this->ipsclass->lang['album_no_move'],
                                                             'public'      => ( $album['public_album'] ) ? 'checked' : '',
                                                             'errors'      => $error_out,
                                                             'hiddens'     => "<input type='hidden' name='album' value='{$this->ipsclass->input['album']}' />",
                                                     )      );
            
        }

        /**
         * album::confirm_delete()
         * 
		 * 
         * @return none
         **/
        function confirm_delete()
        {
		   $this->output .= $this->html->confirm_delete( $this->ipsclass->input['album'] );          
        }

        /**
         * album::_do_del()
         * 
		 * Process the request to remove an album.  This removes the DB entry,
		 * all images on the disk, and removes the image entries from the DB.
		 * 
         * @return none
         **/
        function _do_del()
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'member_id', 'from' => 'gallery_albums', 'where' => "id={$this->ipsclass->input['album']}" ) );
            $this->ipsclass->DB->simple_exec();
            $album = $this->ipsclass->DB->fetch_row();

            if( $album['member_id'] != $this->ipsclass->member['id'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }
            
            // Delete the album
            $this->albums->delete_album( $this->ipsclass->input['album'] );
            
            /**
            * Count member's albums, if 0, reset their has_gallery key
            **/
            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT( * ) AS total', 'from' => 'gallery_albums', 'where' => "member_id = {$album['member_id']}" ) );
            $this->ipsclass->DB->simple_exec();
            $total = $this->ipsclass->DB->fetch_row();
            if( $total['total'] <= 0 )
            {
            	$this->ipsclass->DB->do_update( 'members', array( 'has_gallery' => '0' ), "id={$album['member_id']}" );
            }
            
            // Redirect
            $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['album_deleted'], "act=module&amp;module=gallery&amp;cmd=albums" );            
        }    
    }
?>
