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
* Library/Album Control
*
* Class for handling album stuff
*
* @package		Gallery
* @subpackage 	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.3
*/

	class album_control
	{
		var $data  = array();
		var $total = 0;
                var $ipsclass;
                var $glib;
                var $html;

    	/**
    	 * album_control::album_control()
    	 * 
		 * Constructor
		 * 
		 * 
    	 * @return none
    	 **/			
		function album_control( )
		{
		}

    	/**
    	 * album_control::get_user_albums()
    	 * 
		 * Gets all the albums for the specified member id
		 * 
		 * @param integer $mid
    	 * @return none
    	 **/		
		function get_user_albums( $mid )
		{		
			$this->data = array();
			
			// Get the albums
            $this->ipsclass->DB->simple_construct( array( 'select' => '*', 
                                                'from'   => 'gallery_albums', 
                                                'where'  => "member_id={$mid}" ) );
            $this->ipsclass->DB->simple_exec();
            
            if( $this->ipsclass->DB->get_num_rows() )
            {
            	while( $row = $this->ipsclass->DB->fetch_row() )
            	{
            		// Add in extra stuff
                	$row['status']   = $this->get_status( $row );
                	$row['controls'] = $this->get_text_controls( $row );
                        	
            		$this->data[$row['id']] = $row;
            		$this->total++;
            	}
            }
		}

    	/**
    	 * album_control::get_user_albums_thumbs()
    	 * 
		 * Returns a list of albums with thumbnails for the specified member id
		 * 
		 * @param integer $mid
    	 * @return none
    	 **/		
		function get_user_albums_thumbs( $mid )
		{			
			$this->data = array();
			
			// Get the query
			$this->ipsclass->DB->cache_add_query( 'get_album_list', 
			 			   array( 'pre'              => "a",
							 	  'SORT_KEY'         => 'name',
    	                          'ORDER_KEY'        => 'asc',
    	                          'st'               => intval( $this->ipsclass->input['st'] ),
    	                          'gallery_user_row' => 100,
    	                          'mid'              => " AND a.member_id={$mid}",
			 				   ), 'gallery_sql_queries'      );
			 				   
            $this->ipsclass->DB->simple_exec();
            
            $this->total = $this->ipsclass->DB->get_num_rows();
            
            if( $this->total )
            {
				while( $i = $this->ipsclass->DB->fetch_row() )
				{
					if( ! $this->_cat_auth( $i ) )
					{
						continue;	
					}
					
					$i['image']  = $this->glib->make_image_link( array( 'masked_file_name' => $i['masked_file_name'], 
														   'directory'        => $i['directory'], 
														   'media'            => $i['media'], 
														   'date'             => $i['date'], 
														   'id'               => $i['last_pic'],
														   ), $i['thumbnail'] );
                    $i['date']   = $this->ipsclass->get_date( $i['date'], 'LONG' );
                    
                    $this->data[] = $i;
				}
            }
		}

    	/**
    	 * album_control::get_formated_albums_thumbs()
    	 * 
		 * Uses the data from get_user_album_thumbs to display the albums
		 * 
    	 * @return string
    	 **/		
		function get_formated_albums_thumbs()
        {
             /* Load template */
             if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_imagelisting'] ) )  {
             	$this->ipsclass->load_template('skin_gallery_imagelisting');
             }
			 $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_imagelisting' ];
			
			if( is_array( $this->data ) )
			{
				$output .= $this->html->container_row_top();
				$output .= $this->html->view_begin_row();
				
				foreach( $this->data as $i )
				{
					if( ! $i['public_album'] ) continue;

					// Are we ready to show the next row of albums?
					if( $col_count >=2 )
					{
						$output .= $this->html->view_end_row();
						$output .= $this->html->view_begin_row();
						$col_count = 0;
					}
					
					// Increment the column count
					$col_count++;
					
					// Get the html for showing the album
					$output .= $this->html->view_album_row( $i );										
				}
				
				// Do we need to end the row?
				if( $col_count != 0 )
				{
					$output .= $this->html->view_end_row();
				}
				
				$output .= $this->html->container_row_bottom();
			}
			else
			{
				$output .= $this->html->basic_row( 'none_found' );
			}
			
			return $output;
        }

    	/**
    	 * album_control::get_album_text_links()
    	 * 
		 * Generates the album portion of the user bar
		 * 
    	 * @return string
    	 **/		
		function get_album_text_links( $mid )
		{
			if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_user'] ) ) {
				$this->ipsclass->load_template('skin_gallery_user');
			}
			$this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_user' ];
			
			// Are we a moderator?
			$can_mod = $this->_can_moderate();

			// Loop through albums
			foreach( $this->data as $i )
			{
				if( ! $this->_cat_auth( $i ) )
				{
					continue;	
				}
				
				if( $i['public_album'] )
				{
                    $public  .= $this->html->user_bar_row( "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$mid}&amp;op=view_album&amp;album={$i['id']}'>{$i['name']}</a> ( {$i['images']} )" );

				}
				else if( $can_mod ||  $this->_is_owner( $mid, $i['member_id'] ) )
				{
					$private .= $this->html->user_bar_row( "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$mid}&amp;op=view_album&amp;album={$i['id']}'>{$i['name']}</a> ( {$i['images']} )" );				
				}
				$member_id   = $i['member_id'];
			}
			
			// Format the public albums
            $public_list = $this->html->user_bar_public_albums( $public, $mid );
            
            // Foramt the private albums, if necessary
			if( $can_mod || $this->_is_owner( $mid, $i['member_id'] ) )
            {
				if( $private )
				{
	                $private_list  = $this->html->user_bar_header( $this->ipsclass->lang['user_priv_albums'] );
		            $private_list .= $private;
			        $private_list .= $this->html->user_bar_footer();
				}

				if( $this->_is_owner( $mid, $this->ipsclass->member['id'] ) )
				{
	                $controls  = $this->html->user_bar_header( $this->ipsclass->lang['controls'] );
		            $controls .= $this->html->user_bar_row( "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=albums'>{$this->ipsclass->lang['edit_albums']}</a>" );
			        $controls .= $this->html->user_bar_footer();
				}
            }
            
            // Format the name
			$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$mid}" ) );
			$this->ipsclass->DB->simple_exec();
			$temp = $this->ipsclass->DB->fetch_row();
			$member_name = $temp['name'];
			
            $name = "<a href='{$this->ipsclass->base_url}showuser={$mid}'>{$member_name}</a>";
            return array( 'public_list'  => $public_list,
                          'private_list' => $private_list,
                          'controls'     => $controls,
                          'name'         => $name, );
		}

		function get_album( $id, $bypass=false )
		{
			// Get the album
			$album = $this->data[$id];

			/* Bypass check ( buddy perms ) */
			if( !$bypass )  {
				// Permissions Check
            	if( ! $album['public_album'] )
            	{
					if( ! ( $this->_is_owner( $album['member_id'], $this->ipsclass->member['id'] ) || $this->_can_moderate() ) )
					{
						$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
					}
            	}                       
			
				if( ! $this->_cat_auth( $album ) ) {
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );				
				}
			}
			return $album;
		}
		
		function _is_owner( $mid1, $mid2 )
		{
			return ( $mid1 == $mid2 );
		}
		
		function _can_moderate()
		{
			return $this->ipsclass->member['g_mod_albums'];
		}
		
		function _cat_auth( $album )
		{
			if( $album['category_id'] )
			{
				/* Get the category info & cache it */
				if( ! $this->cat_cache[$album['category_id']] )
				{
					$this->ipsclass->DB->simple_construct( array( 'select' => 'id, password, name, perms_view', 'from' => 'gallery_categories', 'where' => "id={$album['category_id']}" ) );
					$this->ipsclass->DB->simple_exec();
					$this->cat_cache[$album['category_id']] = $this->ipsclass->DB->fetch_row();
				}

				/* Check Permissions */
				if( ! $this->ipsclass->check_perms( $this->cat_cache[$album['category_id']]['perms_view'] ) )
				{
					return false;
				}								
			}
			
			return true;
		}	
	

    	/**
    	 * album_control::get_status()
    	 * 
		 * Returns the status of the album, either public or private
		 * 
		 * @param array $i
    	 * @return none
    	 **/			
		function get_status( $i, $format=true )
		{			
			if( !$format )  {
				return $i['public_album'];
			}
			else {
            	return ( $i['public_album'] ) ? $this->ipsclass->lang['album_public'] : $this->ipsclass->lang['album_private'];
			}
		}

    	/**
    	 * album_control::get_text_controls()
    	 * 
		 * Returns links used for controlling the album
		 * 
		 * @param array $i
    	 * @return none
    	 **/		
		function get_text_controls( $i )
		{
			$controls  = "<select name='albumcontrols{$i['id']}' onChange='do_op(this)'><option value='null'>{$this->ipsclass->lang['select_op']}</option>";
			$controls .= "<option value='act=module&amp;module=gallery&amp;cmd=albums&amp;op=edit&amp;album={$i['id']}'>{$this->ipsclass->lang['edit']}</option>";
			$controls .= "<option value='act=module&amp;module=gallery&amp;cmd=albums&amp;op=del&amp;album={$i['id']}'>{$this->ipsclass->lang['delete']}</option>";
			$controls .= "<option value='act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->ipsclass->member['id']}&amp;op=view_album&amp;album={$i['id']}'>{$this->ipsclass->lang['view']}</option>";
			$controls .= "<option value='act=module&amp;module=gallery&amp;cmd=post&amp;album={$i['id']}'>{$this->ipsclass->lang['upload']}</option>";

			// Multi Upload
			if( $this->ipsclass->member['g_multi_file_limit'] || $this->ipsclass->member['g_zip_file'] )
			{
				$controls .= "<option value='act=module&amp;module=gallery&amp;cmd=post&amp;album={$i['id']}&multi=1'>{$this->ipsclass->lang['bulk_upload']}</option>";
			}

			$controls .= "</select>";
		
		/*
			$controls  = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=albums&amp;op=edit&amp;album={$i['id']}'>{$this->ipsclass->lang['edit']}</a> ";
			$controls .= "&middot; <a href='javascript:delete_album(\"{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=albums&amp;op=del&amp;album={$i['id']}\")'>{$this->ipsclass->lang['delete']}</a> ";
			$controls .= "&middot; <a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$this->ipsclass->member['id']}&amp;op=view_album&amp;album={$i['id']}'>{$this->ipsclass->lang['view']}</a> ";
			$controls .= "&middot; <a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;album={$i['id']}'>{$this->ipsclass->lang['upload']}</a> ";
			
			// Multi Upload
			if( $this->ipsclass->member['g_multi_file_limit'] || $this->ipsclass->member['g_zip_file'] )
			{
				$controls .= "&middot; <a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;album={$i['id']}&multi=1'>{$this->ipsclass->lang['bulk_upload']}</a> ";
			}

			// Media Link
			if( is_media_allowed( array( 'allow_movies' => 1 ) ) )
			{
				$controls .= " &middot; <a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;album={$i['id']}&amp;album={$i['id']}&amp;media=1'>{$this->ipsclass->lang['upload_media']}</a>";
			}
		*/	
			return $controls;
		}

    	/**
    	 * album_control::delete_album()
    	 * 
		 * Removes the specified album and then deletes the physical image files
		 * 
		 * @param integer $album
    	 * @return none
    	 **/		
		function delete_album( $album )
		{
            // Delete the images
            $this->ipsclass->DB->simple_construct( array( 'select' => '*', 
                                          'from'   => 'gallery_images', 
                                          'where'  => "album_id={$album}" ) );
            $this->ipsclass->DB->simple_exec();
			
			// Remove the physical images
            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
                @unlink( $this->ipsclass->vars['gallery_images_path'] .'/'. $dir. $i['masked_file_name'] );
                if( $i['medium_file_name'] )
                {
                	@unlink( $this->ipsclass->vars['gallery_images_path'] .'/'.$dir.'med_'. $i['masked_file_name'] );
                }
                if( $i['thumbnail'] )
                {
                    @unlink( $this->ipsclass->vars['gallery_images_path'] .'/'.$dir.'tn_'. $i['masked_file_name'] );
                }
            }
			
			// Remove the album
            $this->ipsclass->DB->simple_construct( array( 'delete' => 'gallery_albums', 'where' => "id=$album" ) );
            $this->ipsclass->DB->simple_exec();
            
            // Remove the image db entries
            $this->ipsclass->DB->simple_construct( array( 'delete' => 'gallery_images', 'where' => "album_id=$album" ) );
            $this->ipsclass->DB->simple_exec();
		
		}
	}
?>
