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
* Library/Main Gallery Library
*
* Tis Important mmhmm
*
* @package		Gallery
* @subpackage 	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

class gallery_lib  {

    var $ipsclass;
    
    /*
    * Seperation character for gallery online list */
    var $sep_char = ',';

    function gallery_lib()  {
    }
   
    /**
     * debug()
     * 
	 * Prints out an array in a 'nice' format
	 * 
	 * @version 1.0
	 * @since 1.0
	 * @access public
	 * 
     * @param $var
     * @return 
     **/
    function debug( $var )
    {
        echo "<div align='left'><PRE>"; print_r( $var ); echo "</PRE></div>";
    }

    /**
     * check_cat_auth()
     * 
	 * Checks to see if we are authorized to view the
	 * category.  Group permissions and category passwords
	 * are both checked, execution is halted if the user
	 * is unauthorized.
	 * 
	 * @version 1.0
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $cid
     * @param integer $password
     * @param array $perms_view
     * @return nothing
     **/
    function check_cat_auth( $cid, $password, $perms_view )
    {
        global $_COOKIE;

        if( ! $this->ipsclass->check_perms( $perms_view ) )
        {         
            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        }

        if( $_COOKIE[ $this->ipsclass->vars['cookie_id'].'IG'.$cid ] != $password )
        {
            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        }
    }
    
    /**
    * get_copyright()
    * 
    * Returns copyright string for Gallery
    *
    * @since 2.0.3
    * @version 1.0
    **/
    function get_copyright()
    {
    	/*
    	* Copyright stuff */
    	if ($this->ipsclass->vars['ipb_copy_number'])
        {
            $copyright = "";
        }
        else
        {
            $copyright = "<!-- Copyright Information -->
                          <div align='center' class='copyright'>
                              Invision Gallery
                          ".GALLERY_VERSION." &copy; ".date("Y")." &nbsp;IPS, Inc.";

            if ( $this->ipsclass->vars['ipb_reg_show'] and $this->ipsclass->vars['ipb_reg_name'] )
            {
                $copyright .= "<br />Licensed to: ". $this->ipsclass->vars['ipb_reg_name']."</div>";
            }
            else
            {
                $copyright .= "</div>";
            }
            $copyright .= "<!-- / Copyright -->";
        }

        return $copyright;
    }

    /**
     * get_allowed_albums()
     * 
	 * Returns an array of albums that the user is allowed
	 * to see
	 * 
	 * @version 2.0.5
	 * @since 1.0
     **/
    function get_allowed_albums()
    {
    	/**
    	* Are we allowed to see private albums?
    	**/
    	$where = ( $this->ipsclass->member['g_mod_albums'] ) ? "" : "public_album = 1";
    	
    	/**
    	* Build query
    	**/
        $this->ipsclass->DB->build_query( array(
        				"select"	=>	"a.id, a.category_id",
        				"from"		=>	array( "gallery_albums" => "a" ),
        				"where"		=>	$where,
        				"add_join"	=>	array( array(
        									"select" => "c.id AS cat_id,c.password,c.perms_thumbs",
        									"from"	=>	array( "gallery_categories" => "c" ),
        									"where"	=>	"a.category_id = c.id",
        									"type"	=>	"left" ) )
        ) );
       	$this->ipsclass->DB->exec_query();

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
        	/**
        	* If it has a parent cat, check the 
        	* thumbnail perm, and the password
        	**/
        	if( $i['category_id'] )
        	{
        		if( $this->ipsclass->check_perms( $i['perms_thumbs'] ) )
        		{
        			/**
        			* Check pass
        			**/
        			if( !empty( $i['password'] ) )
        			{
        				if( $_COOKIE[ $this->ipsclass->vars['cookie_id'].'IG'.$i['cat_id'] ] == $i['password'] )
        				{
        					/**
        					* Add album
        					**/
            				$album_cache[] = $i['id'];
        				}
        			}
        			else 
        			{
        				/**
        				* Add album
        				**/
        				$album_cache[] = $i['id'];
        			} 
        		}
        		else 
        		{
					/**
        			* Skip this album
        			**/
        			continue;
        		}
        	}
        	else 
        	{
        		/**
        		* Member's Gallery
        		**/
        		$album_cache[] = $i['id'];
        	}
        }

        return $album_cache;
    }

    /**
     * get_allowed_cats()
	 * 
	 * Generates an array of category id's that the user
	 * is allowed to see.  If $pass_chk is set then it will make
	 * sure we are authorized to view the password protectd category
     * 
	 * @version 1.0
	 * @since 1.0
     **/
    function get_allowed_cats( $pass_chk=1, $category_data=array() )
    {       
        // Grab a list of categories
        if( empty( $category_data ) )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'id, perms_thumbs, password', 'from' => 'gallery_categories' ) );
            $this->ipsclass->DB->simple_exec();

            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $category_data[] = $i;
            }
        }

        foreach( $category_data as $i )
        {
            // Check to see if we can view this category
            if( $this->ipsclass->check_perms( $i['perms_thumbs'] ) )
            {
                // We can, but does it have a password?
                if( empty( $i['password'] ) )
                {
                    $category_cache[] = $i['id'];
                }
                else
                {
                    // Should we check the password?
                    if( $pass_chk )
                    {
                        // Check password
                        if( $_COOKIE[ $this->ipsclass->vars['cookie_id'].'IG'.$i['id'] ] == $i['password'] )
                        {
                            $category_cache[] = $i['id'];
                        }
                    }
                    else
                    {
                        $category_cache[] = $i['id'];
                    }
                }
            }
        }

        return $category_cache;
    }

    /**
     * is_media_allowed()
     * 
	 * Returns true if the user can upload media
	 * 
	 * @version 1.2
	 * @since 1.2
	 * @access public
	 * 
     * @param array $cat
     * @return bool
     **/
    function is_media_allowed( $cat )
    {

        if( ! $cat['allow_movies'] )
        {
            return false;
        }

        if( ! $this->ipsclass->member['g_movies'] )
        {
            return false;
        }

        return true;
    }

    /**
     * get_extension()
     * 
	 * Generates an extension for the given image type
	 * 
	 * @version 1.0
	 * @since 1.0
	 * @access public
	 * 
     * @param string $file_type
     * @return string
     **/
    function get_extension( $file_type )
    {
       	switch( $file_type )
	 	{
	    	case 'image/gif':
		    	$ext = '.gif';
			    break;
   	    	case 'image/jpeg':
	 			$ext = '.jpg';
	    		break;
   		    case 'image/pjpeg':
	 			$ext = '.jpg';
	    		break;
       		case 'image/x-png':
	 			$ext = '.png';
	    		break;
   	    	case 'image/png':
	 			$ext = '.png';
	    		break;
       	}

        return $ext;
    }
    
    /**
    * get_image_type()
    *
    * Return the correct mime type for an image
    *
    * @since 2.0.4
    * @version 1.0
    **/
    function get_image_type( $img )
    {
    	$ext = @strtolower( array_pop( explode( ".", $img ) ) );
    	
    	switch( $ext )
    	{
    		case 'gif':
    			$file_type = 'image/gif';
    		break;
    		
    		case 'jpg':
    		case 'jpeg':
    		case 'jpe':
    			$file_type = 'image/jpeg';
    		break;
    		
    		case 'png':
    			$file_type = 'image/png';
    		break;
    	}
    	
    	return $file_type;
    }
    
    /**
    * last_pic()
    *
    * Generate a tag for the last uploaded image to a certain cat or album
    * 
    * @since 2.0
    * @author ze Kinder
    **/
    function last_pic( $image, $calling )  {
    	if( empty( $image ) ) {
    		/* EUH */
    		return false;
    	}
    	/*
    	* Thumbnail, yes.. no? */
    	$this->ipsclass->DB->simple_construct( array(
    	               "select" => "*",
    	               "from"   => "gallery_images",
    	               "where" => "id = {$image}" ) );
    	               
        $this->ipsclass->DB->simple_exec();
        if( $i = $this->ipsclass->DB->fetch_row() ) {
        	$return = $this->make_image_link( $i, $i[ 'thumbnail' ], 0, 0, 1, $calling );
        	return $return;
        }
        else {
        	return false;
        }
    }
    
    /**
    * get_user_buddies()
    *
    * Grabs all the user's "buddies" on their contact list
    * 
    * @since 2.0
    * @author ze Kinder
    **/
    function get_user_buddies( $mid )  {
    	$this->ipsclass->DB->simple_construct( array(
    			"select"	=>	"*",
    			"from"		=>	"contacts",
    			"where"		=>	"member_id = {$mid}"
    			)
    	);
    	$this->ipsclass->DB->simple_exec();
    	if( $this->ipsclass->DB->get_num_rows() == 0 )  {
    		/*
    		* Aw, nobody loves them */
    		return false;
    	}
    	else {
    		while( $row = $this->ipsclass->DB->fetch_row() )  {
    			$this->user->friends[] = $row;
    		}
    	}
    	return true;
    }
    
    /**
	* category::_idx_order()
	*
	* Used for ordering the thumbnail listing
	*
	* @param integer $pos
	*
	* @return bool
	**/
	function _idx_order( $pos )
	{
		$elements = array( 
							'gallery_img_order_username',
							'gallery_img_order_date',
							'gallery_img_order_size',
							'gallery_img_order_comment', 
							'gallery_img_order_view'
						);

		foreach( $elements as $element )
		{
			if( $this->ipsclass->vars[$element] == $pos )
			{
				return $element;
			}
		}
	}
    
    /**
    * make_info_div()
    *
    * Generates the div info for imagelistings
    *
    * @since 2.0
    * @author ze Kinder
    **/
    function make_info_div( $image, $memcat=false ) {
    	if( empty( $image ) ) {
    		/* EUH */
    		return false;
    	}
    	$ex = '';
    	if( $memcat )  {
    		$ex = '_mem';
    	}
         $div = "<div class='popupmenu' id='info_{$image}{$ex}' style='position: absolute; display: none;'>";
        /*
        * Weeee */
		$this->ipsclass->DB->build_query( array(
                   "select"  => 'img.*',
                   "from"    => array( 'gallery_images' => 'img' ),
                   "where"   => "img.id = {$image} ",
                   "add_join" => array( array(
                                 "select"   => 'mem.members_display_name AS name',
                                 "from"     => array( 'members' => 'mem' ),
                                 "where"    => "mem.id = img.member_id",
                                 "type"     => "left" )
                                 )
                   ) );
		$this->ipsclass->DB->exec_query();
		$i = $this->ipsclass->DB->fetch_row();
		/*
	    * Image tag  */
		$i['image'] = $this->make_image_link( $i, $i['thumbnail'], 0, 0, 0 );			

		/*
		* Honor order settings  */
		for( $j = 1; $j <=6; $j++ )
		{
			switch( $this->_idx_order( $j ) )
			{
				/*
				* Ze username */					
				case 'gallery_img_order_username':
					if( $this->ipsclass->vars['gallery_img_show_user'] )
					{
						$name = $this->make_name_link( $i['member_id'], ( $i['name'] ) ? $i['name'] : $this->ipsclass->lang['guest'] );					
						$img_view_elements[] = array( $this->ipsclass->lang['uploaded_by'], $name );				
					}						
				break;
				/**
				* Date
				**/	
				case 'gallery_img_order_date':
					if( $this->ipsclass->vars['gallery_img_show_date'] )
					{		
						$img_view_elements[] = array( $this->ipsclass->lang['on'], $this->ipsclass->get_date( $i['date'], 'LONG' ) );											
					}						
				break;

				/**
				* Filesize
				**/	
				case 'gallery_img_order_size':
					if( $this->ipsclass->vars['gallery_img_show_filesize'] )
					{
						$img_view_elements[] = array( $this->ipsclass->lang['filesize'], $this->byte_to_kb( $i['file_size'] ) );					
					}						
				break;
				/**
				* Comment Count
				**/	
				case 'gallery_img_order_comment':
					if( $this->ipsclass->vars['gallery_img_show_comments'] )
					{
						$img_view_elements[] = array( $this->ipsclass->lang['l_comments'], $i['comments'] );	
					}						
				break;
						
				/**
				* View Count
				**/							
				case 'gallery_img_order_view':
					if( $this->ipsclass->vars['gallery_img_show_views'] )
					{
						$img_view_elements[] = array( $this->ipsclass->lang['l_views'], $i['views'] );	
					}						
     			break;
			} 
		}
		$div .= "<div class='popupmenu-category' align='center'>{$i['caption']}</div>";
		if( !empty( $img_view_elements ) )  {
			foreach( $img_view_elements as $key=>$element )  {
				$div .= "<div class='popupmenu-item'><i>" . $element[ 0 ] . "&nbsp;" . $element[ 1 ] . "</i></div>";
			}
		}
		$div .= "</div>";
		
		$opt = "<div class='popupmenu' id='options_{$image}{$ex}' style='position: absolute; display: none;'>"
				."<div class='popupmenu-item' align='right'><a class='camera' onclick='javascript:toggleview( \"options_{$image}{$ex}\" );'><b>{$this->ipsclass->lang['gallery_close']} [x]</b></a></div>"
				."<div class='popupmenu-category' align='center'>Quick Change</div>"
				."<div class='popupmenu-item'><a href='{$this->ipsclass->base_url}&automodule=gallery&cmd=qkch&code=avatar&id={$image}'><b>Set as my Avatar</b></a></div>"
				."<div class='popupmenu-item-last'><a href='{$this->ipsclass->base_url}&automodule=gallery&cmd=qkch&code=photo&id={$image}'><b>Set as my Personal Photo</b></a></div>"
				."</div>";
		
		/*
		* Build photo bar */
		$photo_bar = "<a OnClick='javascript:toggleview( \"info_{$image}{$ex}\" );'>
		<img class='camera' src='style_images/{$this->ipsclass->skin['_imagedir']}/gallery_img_info.gif' /></a>{$div}";
		
		/*$photo_bar .= "{$opt}<a OnClick='javascript:toggleview( \"options_{$image}{$ex}\" );'>
					<img class='camera' src='style_images/{$this->ipsclass->skin['_imagedir']}/gallery_img_options.gif' /></a>";*/
				
		return ( $photo_bar );
    }

    /**
     * make_image_tag()
	 * 
	 * Generates the tag for an image. If $tn is set, then it will
	 * generate a tag for the image thumbnail
     * 
	 * @version 1.2
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $id
     * @param integer $tn
     * @return string
     **/
    function make_image_tag( $i, $tn=0, $med_link=0 )
    {       
        // Thumbnail?
        $tn     = ( $tn ) ? '&amp;tn=1' : '';
		$thumb  = ( $tn ) ? 'tn_' : '';
		$attach = 'class="galattach"';

		// Directory?
        $dir = ( $i['directory'] ) ? "&amp;dir={$i['directory']}/" : "";
        $directory = ( $i['directory'] ) ? "{$i['directory']}/" : "";
        
        // Update bandwidth, if required
        if( $this->ipsclass->vars['gallery_detailed_bandwidth'] && $this->ipsclass->input['cmd'] == 'si' )
        {
            if( ! ( $tn && ! $this->ipsclass->vars['gallery_bandwidth_thumbs'] ) )
            {
				$cut = time() - ( $this->ipsclass->vars['gallery_bandwidth_period'] * 60 * 60 );
				
				if( ! $this->ipsclass->del_bandwidth );
				{
	      	        $this->ipsclass->DB->simple_delete( 'gallery_bandwidth', "date < {$cut}" ); $this->ipsclass->DB->simple_exec();
	      	        $this->ipsclass->del_bandwidth = 1;
	      	    }
          	    $insert = array( 'member_id' => $this->ipsclass->member['id'], 
          	                     'file_name' => $i['masked_file_name'],
          	                     'date'      => time(),
          	                     'size'      => filesize( $this->ipsclass->vars['gallery_images_path'].'/'.$directory.$thumb.$i['masked_file_name'] )
          	                   );
          	    
          	    $this->ipsclass->DB->do_insert( 'gallery_bandwidth', $insert );
            }
        }
        
        // Is this a multimedia thumbnail?
        if( $i['media'] ) {
                // Do we already have the media info cached?
                if( ! $this->ipsclass->media_thumb_cache )
                {
                    $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types' ) );
                    $this->ipsclass->DB->simple_exec();

                    while( $j = $this->ipsclass->DB->fetch_row() )
                    {
						$exts = explode( ",", $j['extension'] );
						
						foreach( $exts as $ext )
						{
							$this->ipsclass->media_thumb_cache[$ext] = $j;
						}
                    }
                }
                
                // What type of media do we have?
                $_ext = explode( ".", $i['masked_file_name'] );
                $ext  = '.' . strtolower( array_pop( $_ext ) );
                $media = $this->ipsclass->media_thumb_cache[$ext];

                return( "<img src='style_images/<#IMG_DIR#>/{$media['icon']}'>" );
            }
        else {
		/**
		 * Show an actual image url or serve through php?		
		 */
        if( $this->ipsclass->vars['gallery_web_accessible'] == 'yes' )
        {
        	// Base url of the image
			$img_url = "{$this->ipsclass->vars['gallery_images_url']}/{$directory}{$thumb}";
        	
        	// Medium Sized Image
        	if( $i['medium_file_name'] && $med_link )
        	{
        		// The full image tag
        		$img_tag = "<img src='{$img_url}{$i['medium_file_name']}' {$attach} alt='{$thumb}{$i['masked_file_name']}' />";
        		
         		return "<a href='{$img_url}{$i['masked_file_name']}' target='_blank'>{$img_tag}</a>";
        	}
        	// Full Sized Image
        	else
        	{
        		return "<img src='{$img_url}{$i['masked_file_name']}' {$attach} alt='{$thumb}{$i['masked_file_name']}' />";		
        	}
 			
        }
        else
        {
        	// Base url of the image
        	$img_url = "{$this->ipsclass->vars['board_url']}/index.php?automodule=gallery&amp;cmd=viewimage&amp;img={$i['id']}{$tn}";
        	
        	// Medium Sized Image
        	if( $i['medium_file_name'] && $med_link )
        	{
 				$img_tag = "<img src='{$this->ipsclass->vars['board_url']}/index.php?automodule=gallery&amp;cmd=viewimage&amp;img={$i['id']}&amp;file=med' {$attach} title='{$thumb}{$i['masked_file_name']}'/>";

 				return "<a href='{$img_url}' target='_blank'>{$img_tag}</a>";
        	}
        	// Full Sized Image
        	else
        	{
 				return "<img src='{$img_url}'/>";        		
 			}
 		}
        }

    }

    /**
     * make_image_link()
     * 
	 * Generates a link to an image.  If $tn is set, then
	 * it will generate a thumbnail tag.  Checks bandwith too
	 * 
	 * @version 1.2
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $id
     * @param integer $tn
     * @return string
     **/
    function make_image_link( $i, $tn=0, $override=0, $acp_override=0, $wrap_override=0, $calling_loci='img' )
    {
        $show = true;
        
        if( $override )
        {
        	$i['id'] = $override;
        }

        /* unapproved image override ( only from ACP ) */
        if( !$acp_override ) {
        // Do we need to limit bandwidth useage?
        if( $this->ipsclass->vars['gallery_detailed_bandwidth'] )
        {
            // Remove old entries
            if( ! $this->ipsclass->bandwidth_purged )
            {
	            $cut = time() - ( $this->ipsclass->vars['gallery_bandwidth_period'] * 60 * 60 );
    	        $this->ipsclass->DB->simple_delete( 'gallery_bandwidth', "date < {$cut}" );
        	    $this->ipsclass->DB->simple_shutdown_exec();
        	    $this->ipsclass->bandwidth_purged = true;
        	}
            
            if( $this->ipsclass->member['g_max_transfer'] )
            {
                $q = " SUM( size ) AS transfer, ";
            }

            if( $this->ipsclass->member['g_max_views'] )
            {
                $q .= " COUNT( size ) AS views, ";
            }

            if( $q )
            {
                if( ! $this->ipsclass->member['bandwidth'] )
                {
                    $q = preg_replace( "/(, )$/", "", $q );

                    $this->ipsclass->DB->simple_construct( array( 'select' => $q, 'from' => 'gallery_bandwidth', 'where' => "member_id={$this->ipsclass->member['id']}" ) );
                    $this->ipsclass->DB->simple_exec();            

                    $this->ipsclass->member['bandwidth'] = $this->ipsclass->DB->fetch_row();
                }

                if( ! empty( $this->ipsclass->member['g_max_transfer'] ) && $this->ipsclass->member['bandwidth']['transfer'] > $this->ipsclass->member['g_max_transfer']*1024 )
                {
                    $show = false;
                }

                if( ! empty( $this->ipsclass->member['g_max_views'] ) && $this->ipsclass->member['bandwidth']['views'] > $this->ipsclass->member['g_max_views'] )
                {
                    $show = false;
                }
            }
         }
        }
        
        if( $show )
        {
            // Is this a multimedia thumbnail?
            if( $i['media'] )
            {
                // Do we already have the media info cached?
                if( ! $this->ipsclass->media_thumb_cache )
                {
                    $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types' ) );
                    $this->ipsclass->DB->simple_exec();

                    while( $j = $this->ipsclass->DB->fetch_row() )
                    {
						$exts = explode( ",", $j['extension'] );
						
						foreach( $exts as $ext )
						{
							$this->ipsclass->media_thumb_cache[$ext] = $j;
						}
                    }
                }
                
                // What type of media do we have?
                /*
                * Ugggh... php5 makes this look horrible */
                $fileext = explode( ".", $i['masked_file_name'] );
                $ext  = '.' . strtolower( array_pop( $fileext ) );
                $media = $this->ipsclass->media_thumb_cache[$ext];

                $thumbnail = "<img {$wrap_cl} src='style_images/<#IMG_DIR#>/{$media['icon']}'>";

                if( $acp_override )  {
                  return "<a href='' OnClick=\"PopUp('{$this->ipsclass->vars['board_url']}/index.php?act=module&amp;module=gallery&amp;cmd=si&amp;img={$i['id']}', 'approveimage', '600', '500' )\">{$thumbnail}</a>";
                }
                if( $wrap_override ) { 
                	$cat = ( $i['category_id'] == 0 ) ? '&op=user' : "&cat={$i['category_id']}";
                 	return "<a href='{$this->ipsclass->vars['board_url']}/index.php?automodule=gallery&cmd=sc{$cat}'>{$thumbnail}</a>";
                }
                if( !$acp_override && !$wrap_override )  {
                	return "<a href='{$this->ipsclass->vars['board_url']}/index.php?act=module&amp;module=gallery&amp;cmd=si&amp;img={$i['id']}'>{$thumbnail}</a>";
                }
            }
            // Regular image
            else
            {
            	if( $acp_override )  {
                  return "<a href='' OnClick=\"PopUp('{$this->ipsclass->vars['board_url']}/index.php?act=module&amp;module=gallery&amp;cmd=si&amp;img={$i['id']}', 'approveimage', '600', '500' )\">" . $this->make_image_tag( $i, $tn ) . "</a>";
                }
                if( $wrap_override ) { 
                	$cat = ( $i['category_id'] == 0 ) ? '&op=user' : "&cat={$i['category_id']}";
                	/**
                	* Ugly tmp
                	**/
                	if( $calling_loci != 'img' )
                	{
                		$cat = $calling_loci;
                	}
                	/*if( !$i['category_id'] && !$i['album_id'] )
                	{
                		$cat = '&op=user';
                	}
                	else if( $i['album_id'] )
                	{
                		$cat = "&cat={$i['category_id']}";
                	}*/
                 	return "<a href='{$this->ipsclass->vars['board_url']}/index.php?automodule=gallery&cmd=sc{$cat}'>".$this->make_image_tag( $i, $tn, 0 )."</a>";
                }
                if( !$acp_override && !$wrap_override ) {
                	 return "<a href='{$this->ipsclass->vars['board_url']}/index.php?act=module&amp;module=gallery&amp;cmd=si&amp;img={$i['id']}'>".$this->make_image_tag( $i, $tn, 0 )."</a>";
                }
            }
        }
        else
        {
            return $this->ipsclass->lang['bwlimit'];
        }
    }

    /**
     * make_name_link()
     * 
	 * Generates a link to a user name
	 * 
	 * @version 1.0
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $id
     * @param string $name
     * @return string
     **/
    function make_name_link( $id, $name )
    {		
		if( $id > 0 )
		{
			return "<a href='{$this->ipsclass->base_url}automodule=gallery&cmd=user&user={$id}'>{$name}</a>";
		}
		else
		{
			return $name;
		}
    }

    /**
     * check_perms()
     * 
	 * Checks to see if the given group is found in the
	 * given permission array
	 * 
	 * @version 1.0
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $group
     * @param array $perms
     * @return bool
     **/
    function check_perms( $group, $perms )
    {
        $chk = explode( ",", $perms );
        return in_array( $group, $chk );
    }
    
    /**
     * kb_to_byte()
     * 
	 * Converts the size in KB to bytes
	 * 
	 * @version 1.0
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $kb
     * @return integer
     **/
    function kb_to_byte( $kb )
    {
        return $kb*1024;
    }

    /**
     * byte_to_kb()
     * 
	 * Converts bytes to kilobytes, if $format is set, then
	 * it will tack on the KB
	 * 
	 * @version 1.2
	 * @since 1.0
	 * @access public
	 * 
     * @param integer $byte
     * @param integer $format
     * @return integer or string
     **/
    function byte_to_kb( $byte, $format=1 )
    {
    	if( $format )
    	{
    		if( round( $byte/1024/1024, 2 ) > 1 )
    		{
				$ret .= round( $byte/1024/1024, 2 ) . ' MB';
    		}
    		else
    		{
    			$ret  = round( $byte/1024, 2 ) . ' KB ';
    		}    		
    	}
    	else
    	{
    		$ret = round( $byte/1024, 2 );
    	}
    	
    	return $ret;
    }

    /**
     * process_uploaded_file()
     * 
	 * Moves the upload file to the images directory, checks all group settings,
	 * does any required image manipulation
     *
	 * @version 1.2
	 * @since 1.1
	 * @access public
     *
     * @param string $name
     * @param integer $create_thumb
     * @param integer $watermark
     * @param integer $container_id
     * @return array
     **/
    function process_uploaded_file( $name='', $create_thumb=1, $watermark, $containter_id=0, $allow_media=false )
    {             
        // -------------------------------------------------------------
        // Image Details
        // -------------------------------------------------------------
        $file_name = $this->ipsclass->parse_clean_value( $_FILES[$name][ 'name' ] );
        $file_size = $_FILES[$name]['size'];
        $file_type = $_FILES[$name]['type'];
    	$file_type = preg_replace( "/^(.+?);.*$/", "\\1", $file_type );
    	$_ext = explode( ".", $file_name );
        $ext  = '.' . strtolower( array_pop( $_ext ) );    	
    	    	
        // -------------------------------------------------------------
        // Make sure we have a file
        // -------------------------------------------------------------
    	if( ! $file_size )
    	{
            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'img_up_fail' ) );    	
    	}

        // -------------------------------------------------------------
        // Get Media Information
        // -------------------------------------------------------------
        $this->ipsclass->DB->simple_construct( array( "select" => '*', 'from' => 'gallery_media_types', 'where' => "extension LIKE '%{$ext}%'" ) );
        $this->ipsclass->DB->simple_exec();        

        // -------------------------------------------------------------
        // File Type Check
        // -------------------------------------------------------------
        if( $this->ipsclass->DB->get_num_rows() )
        {
            $tmedia = $this->ipsclass->DB->fetch_row();

            if( ! $tmedia['allowed'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'bad_file_type' ) );
            }
            
            $media = 0;
            if( ! $tmedia['default_type'] )
            {
            	/*
            	* Are we allowed to post movies? */
            	if ( !$allow_media )  
            	{
            		$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'gallery_no_movies' ) );
            	}
            	$media = 1;
            }            
        }
        else
   		{			
            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'bad_file_type' ) );
   		}
   		
   		/**
   		* Don't check media dur
   		**/
   		if( !$media )
   		{
   			/**
   			* Is this an actual image?
   			**/
   			$image_attributes = @getimagesize( $_FILES[ $name ][ 'tmp_name' ] );
   			if( !is_array( $image_attributes ) or !count( $image_attributes ) )
   			{
   				/**
   				* Not valid image
   				**/
   				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'bad_file_type' ) );
   			}
   			else if( !$image_attributes[ 2 ] )
   			{
   				/**
   				* Possible XSS attack ( Add monitor here? )
   				**/
   				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'image_not_valid' ) );
   			}
   		}

        // -------------------------------------------------------------
        // Group Settings Check
        // -------------------------------------------------------------

        // Check max diskspace for this user
        if( $this->ipsclass->member['g_max_diskspace'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( file_size ) as diskspace', 'from' => 'gallery_images', 'where' => "member_id={$this->ipsclass->member['id']}" ) );
            $this->ipsclass->DB->simple_exec();
            $total = $this->ipsclass->DB->fetch_row();

            if( $total['diskspace'] + $file_size > $this->kb_to_byte( $this->ipsclass->member['g_max_diskspace'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'out_of_diskspace' ) );
            }
        }

        // Check max upload size for this user
        if( $media )
        {
        if( $this->ipsclass->member['g_movie_size'] )
        {
            if( $file_size > $this->kb_to_byte( $this->ipsclass->member['g_movie_size'] ) )
    	        {
    	            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'file_too_big' ) );
    	        }
	        }        
        }
        else
        {
	        if( $this->ipsclass->member['g_max_upload'] )
    	    {
    	        if( $file_size > $this->kb_to_byte( $this->ipsclass->member['g_max_upload'] ) )
    	        {
    	            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'file_too_big' ) );
    	        }
    	    }
    	}

        // -------------------------------------------------------------
        // Figure out what directory we are gonna use
        // -------------------------------------------------------------
        $dir = "";
        if( $this->ipsclass->vars['gallery_dir_images'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'directory', 
                                          'from'   => 'gallery_images', 
                                          'order'  => "id DESC",
                                          'limit'  => array( 0, 1 ) ) );
            $this->ipsclass->DB->simple_exec();            
            $dir = $this->ipsclass->DB->fetch_row();

            $dir = $dir['directory'];

            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(directory) AS files', 
                                          'from'   => 'gallery_images', 
                                          'where'  => "directory='{$dir}'" ) );
            $this->ipsclass->DB->simple_exec(); 

            $total = $this->ipsclass->DB->fetch_row();

            if( $total['files'] >= $this->ipsclass->vars['gallery_dir_images'] || ! $total['files'] )
            {
                $dir = time();
                mkdir( $this->ipsclass->vars['gallery_images_path'].'/'.$dir, 0777 );
                chmod( $this->ipsclass->vars['gallery_images_path'].'/'.$dir, 0777 );
                
                /**
                * For security, create a blank index.html 
                **/
                touch( $this->ipsclass->vars['gallery_images_path'].'/'.$dir.'/index.html' );
            }

            $dir = ( $dir ) ? "{$dir}/" : "";
        }

        // -------------------------------------------------------------
        // Generate a file name and attempt to copy to uploads directory
        // -------------------------------------------------------------
        $masked_name = "gallery_{$this->ipsclass->member['id']}_{$containter_id}_".time()%$file_size.$ext;
        $masked_file = $this->ipsclass->vars['gallery_images_path'].'/'.$dir.$masked_name;

        $img_load = array( 'out_dir'  => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                           'in_dir'   => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                           'in_file'  => $masked_name );

        if( ! move_uploaded_file( $_FILES[$name]['tmp_name'], $masked_file ) )
        {
            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'img_up_fail' ) );
        }
        
        @chmod( $masked_file, 0777 );

        // -------------------------------------------------------------
        // Generate a thumbnail, if necessary
        // -------------------------------------------------------------
        if( ! $media )
        {
	        $thumbnail = 0;
    	    if( $create_thumb )
    	    {
    	        $img_load['out_file'] = 'tn_' . $masked_name;
    	        $img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                $img->lib_setup();

    	        if( $img->thumbnail( $this->ipsclass->vars['gallery_thumb_width'], $this->ipsclass->vars['gallery_thumb_height'] ) )
    	        {
    	            $img->write_to_file();
    	            $thumbnail = 1;
    	        }
    	        unset( $img );
    	    }
	
    	    // -------------------------------------------------------------
    	    // Resize the main image, if necessary
    	    // -------------------------------------------------------------
    	    if( $this->ipsclass->vars['gallery_max_img_width'] || $this->ipsclass->vars['gallery_max_img_height'] )
	        {
    	        $img_load['out_file'] = $masked_name;
    	        $img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                $img->lib_setup();
	
    	        if( $img->resize_proportional( $this->ipsclass->vars['gallery_max_img_width'], $this->ipsclass->vars['gallery_max_img_height'] ) )
    	        {
    	            $img->write_to_file();
    	        }
    	        unset( $img );
    	    }

    	    // -------------------------------------------------------------
    	    // Create a medium sized image, if necessary
    	    // -------------------------------------------------------------
    	    $medium_image =  ''; 	    
    	    if( $this->ipsclass->vars['gallery_medium_width'] || $this->ipsclass->vars['gallery_medium_height'] )
    	    {
    	    	$img_load['out_file'] = 'med_' . $masked_name;
    	    	$img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                $img->lib_setup();
    	    	
    	    	if( $img->resize_proportional( $this->ipsclass->vars['gallery_medium_width'], $this->ipsclass->vars['gallery_medium_height'] ) )
    	    	{
    	    		$medium_image = 'med_' . $masked_name;    	    		
    	    		$img->write_to_file();	
    	    	}
    	    	unset( $img );
    	    }
	
    	    // -------------------------------------------------------------
    	    // Watermark the image, if necessary
    	    // -------------------------------------------------------------
	
    	    if( $watermark )
    	    {
    	        $img_load['out_file'] = $masked_name;
    	        $img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                $img->lib_setup();

    	        if( $img->watermark() )
    	        {
    	            $img->write_to_file();
    	        }
    	        unset( $img );
    	        
    	        /**
    	        * Do we have a medium sized image?  If so, watermark it too
    	        **/
    	        if( $medium_image )
    	        {
    	        	$img_load['out_file']= "med_{$masked_name}";
    	        	$img = new Image( $img_load );
    	        	$img->ipsclass = &$this->ipsclass;
    	        	$img->glib = &$this->glib;
    	        	$img->lib_setup();
    	        	
    	        	/**
    	        	* Do it
    	        	**/
    	        	if( $img->watermark() )
    	        	{
    	        		$img->write_to_file();
    	        	}
    	        	unset( $img );
    	        }
    	    }
	
    	    // -------------------------------------------------------------
    	    // Need to do some gif handling if we are working with GD
    	    // -------------------------------------------------------------
    	    if( $this->ipsclass->vars['gallery_img_suite'] != 'im' && $file_type == 'image/gif' )
    	    {
    	        $old_gif = $this->ipsclass->vars['gallery_images_path'] . '/' . $dir . $masked_name;
	
    	        $file_type = 'image/jpeg';
    	        $file_name   = preg_replace( "/.gif/i", ".jpg", $file_name );
    	        $masked_name = preg_replace( "/.gif/i", ".jpg", $masked_name );
    	        $masked_file = preg_replace( "/.gif/i", ".jpg", $masked_file );
    	        /*
    	        * bug fix, medium image linking to .gif */
    	        $medium_image = preg_replace( "/.gif/i", ".jpg", $medium_image );
	
    	        $new_jpg = $this->ipsclass->vars['gallery_images_path'] . '/' . $dir . $masked_name;
    	     
    	        $this->gif_to_jpg( $old_gif, $new_jpg );
    	    }
		}

        return  array(  'file_name'         => $file_name,
                        'masked_file_name' => $masked_name,
                        'file_size'        => filesize( $masked_file ),
                        'file_type'        => $file_type,
                        'thumbnail'        => $thumbnail,
                        'media'            => $media,
                        'directory'        => str_replace( "/", "", $dir ),
                        'medium_file_name' => $medium_image,
                      );
    }
    
    /**
     * get_full_path()
     * 
	 * Generates the full path of an image
	 * 
	 * @version 2.0
	 * @since 2.0
	 * @access public
	 * 
     * @param array $i
     * @return string
     **/
	function get_full_path( $i )
	{
		$dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
		$filename = $this->ipsclass->vars['gallery_images_path'].'/'.$dir.$i['masked_file_name'];	
		
		return $filename;
	} 
	
    /**
     * get_full_tn_path()
     * 
	 * Generates the full path of an image
	 * 
	 * @version 2.0
	 * @since 2.0
	 * @access public
	 * 
     * @param array $i
     * @return string
     **/
	function get_full_tn_path( $i )
	{
		$dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
		$filename = $this->ipsclass->vars['gallery_images_path'].'/'.$dir.'tn_'.$i['masked_file_name'];	
		
		return $filename;
	} 

	/**
	* get_full_med_path()
	*
	* Return the full path of a mediumsized image
	* @since 2.0.5
	**/
	function get_full_med_path( $i )
	{
		/**
		* Diirrr
		**/
		$dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
		return( "{$this->ipsclass->vars['gallery_images_path']}/{$dir}{$i['medium_file_name']}" );
	}
		

    function gif_to_jpg( $gif_file, $jpg_file )
    {
        $img = imagecreatefromgif( $gif_file );
        imagejpeg( $img, $jpg_file );
        imagedestroy( $img );

        unlink( $gif_file );
    }

   /**
    * build_sort_order_info()
    * 
    * Returns sort, order, prune keys, along with drop downs for each
	* 
	* @version 1.2
	* @since 1.2
	* @access public
    *
    * @param string $def_view
    * @return string
    **/
    function build_sort_order_info( $def_view='date:DESC:30' )
    {

        // Build up the various arrays first
        $sort_check   = array( 'caption', 'name', 'date', 'views', 'comments', 'rating' );
        $sort_options = array(
                               'caption'   => $this->ipsclass->lang['sort_caption'],
                               'name'      => $this->ipsclass->lang['sort_name'],
                               'date'      => $this->ipsclass->lang['sort_date'],
                               'views'     => $this->ipsclass->lang['sort_views'],
                               'comments'  => $this->ipsclass->lang['sort_comments'],
                               'rating'    => $this->ipsclass->lang['sort_rating'],
                             );
        
        $order_options = array(
                                'ASC'  => $this->ipsclass->lang['sort_asc'],
                                'DESC' => $this->ipsclass->lang['sort_desc'],
                               );
        
        $prune_check   = array( 1, 5, 7, 10, 15, 20, 25, 30, 60, 90, '*' );
        $prune_options = array( 
                                '1'    => $this->ipsclass->lang['sort_1'],
                                '5'    => $this->ipsclass->lang['sort_5'],
                                '7'    => $this->ipsclass->lang['sort_7'],
                                '10'    => $this->ipsclass->lang['sort_10'],
                                '15'    => $this->ipsclass->lang['sort_15'],
                                '20'    => $this->ipsclass->lang['sort_20'],
                                '25'    => $this->ipsclass->lang['sort_25'],
                                '30'    => $this->ipsclass->lang['sort_30'],
                                '60'    => $this->ipsclass->lang['sort_60'],
                                '90'    => $this->ipsclass->lang['sort_90'],
                                '*'     => $this->ipsclass->lang['sort_all'],
                              );
   
         // Figure out if the user has chosen a value, or if we should go with a default
         $views = explode( ":", $def_view );
         $sort_key  = ( $this->ipsclass->input['sort_key'] )  ? $this->ipsclass->input['sort_key']  : $views[0];
         $order_key = ( $this->ipsclass->input['order_key'] ) ? $this->ipsclass->input['order_key'] : $views[1];
         $prune_key = ( $this->ipsclass->input['prune_key'] ) ? $this->ipsclass->input['prune_key'] : $views[2];

         // Check it
         $order_key = ( $order_key == 'ASC' || $order_key == 'DESC' ) ? $order_key : 'DESC';
         $sort_key  = ( in_array( $sort_key, $sort_check ) )   ? $sort_key  : 'date';
         $prune_key = ( in_array( $prune_key, $prune_check ) ) ? $prune_key : '*';

         // Make up some sorting html
         $sort_key_html = "<select name='sort_key' class='forminput'>";
         foreach( $sort_options as $option => $name )
         {
             $sel = ( $option == $sort_key ) ? 'selected="selected"' : '';
             $sort_key_html .= "<option value='{$option}' {$sel}>{$name}</option>";
         }
         $sort_key_html .= "</select>";
 
         $order_key_html = "<select name='order_key' class='forminput'>";
         foreach( $order_options as $option => $name )
         {
             $sel = ( $option == $order_key ) ? 'selected="selected"' : '';
             $order_key_html .= "<option value='{$option}' {$sel}>{$name}</option>";
         }
         $order_key_html .= "</select>";

         $prune_key_html = "<select name='prune_key' class='forminput'>";
         foreach( $prune_options as $option => $name )
         {
             $sel = ( $option == $prune_key ) ? 'selected="selected"' : '';
             $prune_key_html .= "<option value='{$option}' {$sel}>{$name}</option>";
         }
         $prune_key_html .= "</select>";

         // Setup time pruning
         if( $prune_key != '*' )
         {
             $cut = $prune_key * 60 * 60 * 24;
             $prune = " AND date > {$cut}";
         }

         return array(
                       'SORT_KEY'       => $sort_key,
                       'ORDER_KEY'      => $order_key,
                       'PRUNE_KEY'      => $prune_key,
                       'SORT_KEY_HTML'  => $sort_key_html,
                       'ORDER_KEY_HTML' => $order_key_html,
                       'PRUNE_KEY_HTML' => $prune_key_html,
                     );
    }

   /**
    * validate_int()
    * 
    * Ensures that it is a number and returns it back
	* 
	* @version 2.0
	* @since 2.0
	* @access public
    *
    * @param int $num
    * @return int
    **/
	function validate_int( $num )
	{

		// Check input
        if( $num )
        {
            $num = intval( $num );

			if( ! $num ) 
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }
        }

		return $num;
	}

   /**
    * get_image_info( $id )
    * 
    * Generic method for getting an image record
	* 
	* @version 2.0.5
	* @since 2.0
	* @access public
    *
    * @param int $id
    * @return array
    **/
	function get_image_info( $id )
	{
		/**
		* Grab image, member's name
		**/
		$this->ipsclass->DB->build_query( array(
					"select"	=>	"i.*",
					"from"		=>	array( "gallery_images" =>	"i" ),
					"where"		=>	"i.id = {$id}",
					"add_join"	=>	array( array(
									"select"	=>	"mem.members_display_name",
									"from"		=>	array( "members" => "mem" ),
									"where"		=>	"mem.id = i.member_id",
									"type"		=>	"left" ) )
						) );
        $this->ipsclass->DB->simple_exec();

        return $this->ipsclass->DB->fetch_row();
	}

   /**
    * get_category_info( $id )
    * 
    * Generic method for getting a category record
	* 
	* @version 2.0
	* @since 2.0
	* @access public
    *
    * @param int $id
    * @return array
    **/
	function get_category_info( $id )
	{
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 
                                      'from'   => 'gallery_categories', 
                                      'where'  => "id={$id}" ) );
        $this->ipsclass->DB->simple_exec();
        $i = $this->ipsclass->DB->fetch_row();

        return $i;
	}

   /**
    * get_album_info( $id )
    * 
    * Generic method for getting an album record
	* 
	* @version 2.0
	* @since 2.0
	* @access public
    *
    * @param int $id
    * @return array
    **/
	function get_album_info( $id )
	{
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 
                                      'from'   => 'gallery_albums', 
                                      'where'  => "id={$id}" ) );
        $this->ipsclass->DB->simple_exec();

        return $this->ipsclass->DB->fetch_row();
	}

   /**
    * is_valid_type( $file )
    * 
    * Determines if the file type is recognized by gallery
	* 
	* @version 2.0
	* @since 2.0
	* @access public
    *
    * @param string $file
    * @return bool
    **/	
	function is_valid_type( $file )
	{	
		/* Build the media cache, if necessary */
		if( ! $this->ipsclass->media_type_cache )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types' ) );
    	    $this->ipsclass->DB->simple_exec();

        	while( $j = $this->ipsclass->DB->fetch_row() )
	        {
				$exts = explode( ",", $j['extension'] );
			
				foreach( $exts as $ext )
				{
					$this->ipsclass->media_type_cache[] = $ext;
				}
        	}
        }
		/* Check the file */
        $type = "." . strtolower( array_pop( explode( ".", $file ) ) );

		return in_array( $type, $this->ipsclass->media_type_cache );
	}
	
	/*
	* Grab active users ( borrowed from Wizzy )  */
	function get_active_users()
	{
		//-----------------------------------------
		// Get the users from the DB
		//-----------------------------------------
		$cut_off = $this->ipsclass->vars['au_cutoff'] * 60;
		$time    = time() - $cut_off;
		$rows    = array();
		$ar_time = time();

		if ( $this->ipsclass->member['id'] )
		{
			$rows = array( $ar_time.'.'.$this->ipsclass->member['id'] => array(
											  'login_type'   => substr($this->ipsclass->member['login_anonymous'],0, 1),
											  'running_time' => $ar_time,
											  'member_id'    => $this->ipsclass->member['id'],
											  'member_name'  => $this->ipsclass->member['members_display_name'],
											  'member_group' => $this->ipsclass->member['mgroup'] ) );
		}

		$this->ipsclass->DB->simple_construct( array( 'select' => 'id, member_id, member_name, login_type, running_time, member_group',
													  'from'   => 'sessions',
													  'where'  => "location LIKE 'mod:gallery' AND running_time > $time",
											 )      );

		$this->ipsclass->DB->simple_exec();

		//-----------------------------------------
		// FETCH...
		//-----------------------------------------

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( $r['member_id'] > 0 && $r['member_id'] == $this->ipsclass->member['id'] ) continue;
			$rows[ $r['running_time'].'.'.$r['id'] ] = $r;
		}

		krsort( $rows );

		//-----------------------------------------
		// cache all printed members so we
		// don't double print them
		//-----------------------------------------

		$cached = array();
		$active['GUESTS'] = 0;
		$active['MEMBERS'] = 0;
		$active['ANON'] = 0;

		foreach ( $rows as $result )
		{
			$last_date = $this->ipsclass->get_time( $result['running_time'] );

			//-----------------------------------------
			// Bot?
			//-----------------------------------------

			if ( strstr( $result['id'], '_session' ) )
			{
				//-----------------------------------------
				// Seen bot of this type yet?
				//-----------------------------------------

				$botname = preg_replace( '/^(.+?)=/', "\\1", $result['id'] );

				if ( ! $cached[ $result['member_name'] ] )
				{
					if ( $this->ipsclass->vars['spider_anon'] )
					{
						if ( $this->ipsclass->member['mgroup'] == $this->ipsclass->vars['admin_group'] )
						{
							$active['NAMES'] .= "{$result['member_name']}*{$this->sep_char} \n";
						}
					}
					else
					{
						$active['NAMES'] .= "{$result['member_name']}{$this->sep_char} \n";
					}

					$cached[ $result['member_name'] ] = 1;
				}
				else
				{
					//-----------------------------------------
					// Yup, count others as guest
					//-----------------------------------------

					$active['GUESTS']++;
				}
			}

			//-----------------------------------------
			// Guest?
			//-----------------------------------------

			else if ( ! $result['member_id'] )
			{
				$active['GUESTS']++;
			}

			//-----------------------------------------
			// Member?
			//-----------------------------------------

			else
			{
				if ( empty( $cached[ $result['member_id'] ] ) )
				{
					$cached[ $result['member_id'] ] = 1;

					$result['prefix'] = $this->ipsclass->cache['group_cache'][ $result['member_group'] ]['prefix'];
					$result['suffix'] = $this->ipsclass->cache['group_cache'][ $result['member_group'] ]['suffix'];

					if ($result['login_type'])
					{
						if ( ($this->ipsclass->member['mgroup'] == $this->ipsclass->vars['admin_group']) and ($this->ipsclass->vars['disable_admin_anon'] != 1) )
						{
							$active['NAMES'] .= "<a href='{$this->ipsclass->base_url}showuser={$result['member_id']}' title='$last_date'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>*{$this->sep_char} \n";
							$active['ANON']++;
						}
						else
						{
							$active['ANON']++;
						}
					}
					else
					{
						$active['MEMBERS']++;
						$active['NAMES'] .= "<a href='{$this->ipsclass->base_url}showuser={$result['member_id']}' title='$last_date'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>{$this->sep_char} \n";
					}
				}
			}
		}

		$active['NAMES'] = preg_replace( "/".preg_quote($this->sep_char)."$/", "", trim($active['NAMES']) );

		$active['TOTAL'] = $active['MEMBERS'] + $active['GUESTS'] + $active['ANON'];

		return $active;
	}  
}
?>
