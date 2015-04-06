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
* Library/Image Listing
*
* Handles listing images and schtuff
*
* @package		Gallery
* @subpackage 	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

class ImageListing
{
        var $ipsclass;
        var $glib;

	var $res = '';
        var $img_html;
	var $category_cache = array();

	function ImageListing()
	{
             /* Empty */
	}

        function init()  {
			/*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_imagelisting'] ) ) {
            	$this->ipsclass->load_template('skin_gallery_imagelisting');
            }
            $this->img_html = $this->ipsclass->compiled_templates[ 'skin_gallery_imagelisting' ];
        }

	/**
	* ImageListing::get_listing_data()
	*
	* This methond queries the data for the images we want to retrieve
	* there are a lot of parameters, so let's go through them.
	*
	* $params(
	* 			'category'     => If set, this parameter will limit the output to a specified category,
	*
	* 			'album'        => If set to no, it will exclude all albums, if set to an album id
	*                            it will return images from that album only,
	*
	* 			'favorite'     => If set, then the user's favorite images will be retrieved,
	*
	* 			'pinned'       => If set, then then pinned images will be returned before other images,
	*
	* 			'allow_cats'   => If set, then only images in the specifed categories will be returned,
	*
	* 			'allow_albums' => If set, then only images in the specified album will be returned,
	*
	* 			'member'       => If set, then only images for the specifed member will be returned,
	*
	* 			'st'           => Starting position for images, used in LIMIT clause for page spanning,
	*
	* 			'show'         => Ending postion for images, used in LIMIT clause for page spanning,
	*
	* 			'approve'      => If set, then only approved images will be returned,
	*							-2.0.1- If set to 2, will show only UNapproved images
	*
	* 			'sort_key'     => Field to sort by,
	*
	* 			'order_key'    => Order to sort in...ASC or DESC,
	*
	* 			'prune_key'    => Limits the images returned to the specified time frame
	*
	* )
	*
	* @param array $params
	* @return void
	**/
	function get_listing_data( $params )
	{
		if( $params['allow_albums'] == -1 && $params['allow_cats'] == -1 )
		{
			$this->total_images = 0;
			return;
		}

		// ------------------------------------------------------
		// Are we listing images from a category?
		// ------------------------------------------------------
		if( $params['category'] == 'no' )
		{
			$where = "category_id=0 ";
		}
		else if( $params['category'] )
		{
			$where = "category_id={$params['category']} ";
		}

		// ------------------------------------------------------
		// Is this an album?
		// ------------------------------------------------------
		if( $params['album'] == 'no' )
		{
			$where = "album_id=0 ";
		}
		else if( $params['album'] > 0 )
		{
			$where = "album_id={$params['album']} ";
		}

		// ------------------------------------------------------
		// Is this a favorte?
		// ------------------------------------------------------
		if( $params['favorite'] )
		{
			$fav_tbl = ', ibf_gallery_favorites f';
			$where   = " f.member_id={$params['favorite']} AND i.id=f.img_id ";
			$this->favorite = true;
		}

		// ------------------------------------------------------
		// Pin Images?
		// ------------------------------------------------------
		if( $params['pinned'] )
		{
			$pin = "pinned DESC,";
			$this->pin = 1;
		}

		// ------------------------------------------------------
		// Restrict which categories images are pulled from?
		// ------------------------------------------------------
		if( is_array( $params['allow_cats'] ) && $params['category'] != 'no' )
		{
			$cats = implode( ",", $params['allow_cats'] );
			$cat_restrict = " i.category_id IN ( {$cats} ) ";
		}
		else if( $params['allow_cats'] == -1 )
		{
			$cat_restrict = "category_id=0";
		}

		// ------------------------------------------------------
		// Restrict which albums images are pulled from?
		// ------------------------------------------------------
		if( is_array( $params['allow_albums'] ) && $params['album'] != 'no' )
		{
			$album = implode( ",", $params['allow_albums'] );
			$album_restrict = " i.album_id IN ( {$album} ) ";
		}
		else if( $params['allow_albums'] == -1 )
		{
			$album_restrict = "album_id=0 ";
		}

		// ------------------------------------------------------
		// Format the restrict sql
		// ------------------------------------------------------
		if( $album_restrict && $cat_restrict )
		{
			$restrict = " ( {$cat_restrict} OR {$album_restrict} ) ";
		}
		else if( $album_restrict || $cat_restrict )
		{
			$restrict = " {$album_restrict} {$cat_restrict} ";
		}

		// ------------------------------------------------------
		// Only show a certain members images?
		// ------------------------------------------------------
		if( $params['member'] )
		{
			$where = "i.member_id={$params['member']} ";
		}

		// ------------------------------------------------------
		// How many rows are we getting?
		// ------------------------------------------------------
		if( isset( $params['st'] ) && isset( $params['show'] ) )
		{
			$limit = "LIMIT {$params['st']}, {$params['show']}";
		}

		// ------------------------------------------------------
		// Show unapproved images?
		// ------------------------------------------------------
		$params['approve'] = ( $params['approve'] ) ? ' i.approved=1' : "";
		//$this->mod         = ( $params['approve'] ) ? 0 : 1 ;

		/*
		* Recheck mod status */
		//$this->mod = $this->ipsclass->member['is_mod'];
		/*
		* If input['unapproved'] is set, show only unapproved images */
		$params['approve'] = ( isset( $this->ipsclass->input['unapproved'] ) ) ? 'i.approved=0' : $params['approve'];
		
		// ------------------------------------------------------
		// Image Sorting
		// ------------------------------------------------------
		$sort_key  = ( $params['sort_key'] )  ? $params['sort_key']  : 'date';
		$order_key = ( $params['order_key'] ) ? $params['order_key'] : 'DESC';
		$prune_key = ( $params['prune_key'] ) ? $params['prune_key'] : '30';
		
		if( $sort_key == 'rating' )
		{
			$sort_key = '(ratings_total/ratings_count)';	
		}

		if( $sort_key == 'date' )
		{
			$sort_extra = ", i.id {$order_key} ";
		}

		// ------------------------------------------------------
		// Final SQL Formatting
		// ------------------------------------------------------
		if( $where )
		{
			$str = "{$where} ";
		}

		if( !empty( $params['approve'] ) )
		{
			$str = ( $str ) ? " {$str} AND {$params['approve']} " : $params['approve'];
		}

		if( $restrict )
		{
			$str = ( $str ) ? " {$str} AND {$restrict} " : $restrict;
		}

		// ------------------------------------------------------
		// Query the images
		// ------------------------------------------------------
		$this->ipsclass->DB->cache_add_query( 'get_images', array( 'fav_tbl'   => $fav_tbl,
		'member_id' => $this->ipsclass->member['id'],
		'where'     => $str,
		'pin'       => $pin,
		'sort_key'  => $sort_key,
		'order_key' => $order_key,
		'sort_xtra' => $sort_extra,
		'limit'     => $limit ), 'gallery_sql_queries' );

		// ------------------------------------------------------
		// Store the results?
		// ------------------------------------------------------
		$this->res = $this->ipsclass->DB->simple_exec();

		// ------------------------------------------------------
		// Store the total images found
		// ------------------------------------------------------
		$this->total_images = $this->ipsclass->DB->get_num_rows( $this->res );
	}

	/**
	* ImageListing::get_html_listing()
	*
	* Returns the html for images retreived by the get_listing_data method
	*
	* Currently the only 'option' is 'imgs_per_col'
	*
	* @param array $options
	* @return string
	**/
	function get_html_listing( $options=array() )
	{
		/**
		* Set a default for how many columns to show
		**/
		$options['imgs_per_col'] = ( isset( $options['imgs_per_col'] ) ) ? $options['imgs_per_col'] : 5;

		/**
		* Do we actually have images to show?
		**/
		if( $this->total_images )
		{
			/**
			* Output the beginning of the row
			**/
			$output .= $this->img_html->view_begin_row();
			

			/**
			* Loop through and display images
			**/
			while( $i = $this->ipsclass->DB->fetch_row( $this->res ) )
			{
				/**
				* Reset some variables
				**/				
				$alt = "";
				$img_view_elements = array();
				
				/**
				* If we're showing 4 or more cols per row, shorten the caption
				**/
				
				if ( $options['imgs_per_col'] >= 4 )
				{
					$i['caption'] = $this->ipsclass->txt_truncate( $i['caption'], 25 );
				}	
				
				/**
				* Do we need to start listing images on the next row?
				**/					
				if( $col_count >= $options['imgs_per_col'] )
				{
					$output .= $this->img_html->view_end_row();
					$output .= $this->img_html->view_begin_row();
					$col_count = 0;
				}
				$col_count++;

				/**
				* Make an image tag
				**/
				$i['image'] = $this->glib->make_image_link( $i, $i['thumbnail'] );			
				
				/**
				* Figure out ordering
				**/
				for( $j = 1; $j <=6; $j++ )
				{
					switch( $this->_idx_order( $j ) )
					{
						/**
						* User Name
						**/							
						case 'gallery_img_order_username':
							if( $this->ipsclass->vars['gallery_img_show_user'] )
							{
								$name = $this->glib->make_name_link( $i['mid'], ( $i['name'] ) ? $i['name'] : $this->ipsclass->lang['guest'] );					
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
								$img_view_elements[] = array( $this->ipsclass->lang['filesize'], $this->glib->byte_to_kb( $i['file_size'] ) );					
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
						
						/**
						* Ratings
						**/	
						case 'gallery_img_order_rating':
							if( $this->ipsclass->vars['gallery_img_show_rate'] )
							{
								if( ! class_exists( "rate" ) )
								{
									require( $this->ipsclass->gallery_root . 'rate.php' );	
								}
								$rate = new rate;
                                $rate->ipsclass =& $this->ipsclass;
                                $rate->glib =& $this->glib;
								$img_view_elements[] = array( $rate->rating_display( $this->img_html, $i ), "", 'rate' );
							}						
						break;
					} 
				}

				/**
				* Multi-moderation
				**/	
				
				if( $this->mod && ( $this->ipsclass->input['cmd'] == 'sc' || $this->ipsclass->input['cmd'] == 'si' ) )
				{
					$i['extra'] = "<a href='#' title='' onclick='gallery_toggle_img(\"{$i['id']}\"); return false;'><img name='img{$i['id']}' src='style_images/{$this->ipsclass->skin['_imagedir']}/topic_unselected.gif' /></a>";
				}
				
				/**
				* Display the remove link if this is a favorite image
				**/	
				if( $this->favorite )
				{
					$i['extra'] = "<div class='fauxbutton' style='width:auto' align='center'><img src='{$this->ipsclass->vars['img_url']}/aff_cross.gif' style='vertical-align:middle' alt='x' /> <a href='{$this->ipsclass->base_url}automodule=gallery&cmd=favs&op=del&img={$i['id']}'>{$this->ipsclass->lang['rem_fav']}</a></div>";
				}	
				
				/**
				* Loop through the image information and add it to our output
				**/	
				foreach( $img_view_elements as $element )
				{
					$alt = ( $alt == 'class="alt"' ) ? '' : 'class="alt"';
					
					# Matt hack to make sure rating display is the same height as the rating dropdown
					$alt .= ( $element[2] == 'rate' AND ! strstr( $element[0], 'menu_build_menu(') ) ? " style='height:25px'" : "";
					
					$i['info'] .= $this->img_html->image_info_line( $element[0],  $element[1],  $alt );
				}

				/**
				* What kind of image are we displaying?
				**/	
				if( $i['pinned'] && $this->pin )
				{
					$output .= $this->img_html->view_row_img_pinned( $i );
				}
				else if( $i['approved'] )
				{
					$output .= $this->img_html->view_row_img( $i );
				}
				else
				{
					$output .= $this->img_html->view_row_img_hidden( $i );
				}
			}

			if( $col_count != 0 )
			{
				$output .= $this->img_html->view_end_row();
			}
		}
		else
		{
			$output .= $this->img_html->basic_row( 'none_found' );
		}

		return $output;
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
							'gallery_img_order_view',
							'gallery_img_order_rating',
						);

		foreach( $elements as $element )
		{
			if( $this->ipsclass->vars[$element] == $pos )
			{
				return $element;
			}
		}
	}	

}
?>
