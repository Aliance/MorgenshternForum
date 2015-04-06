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
* Main/Rate
*
* Used to rate an image
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		2.0
*/

class rate
{
       var $ipsclass;
       var $glib;

	var $output;
	var $info;
	var $html;

        var $cat_id;
        var $img_id;
        var $album_id;

        var $image;
        var $album;
        var $category;

        var $can_rate;
        
	/**
	* rate::start()
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
		* Check input
		*/
		$this->cat_id = $this->glib->validate_int( $this->ipsclass->input['cat'] );
		$this->cat_id  = ( $this->ipsclass->input['cat'] ) ? $this->ipsclass->input['cat'] : 0;
		
		$this->album_id = $this->glib->validate_int( $this->ipsclass->input['album'] );
		$this->album_id  = ( $this->ipsclass->input['album'] ) ? $this->ipsclass->input['album'] : 0;
		
		$this->img_id = $this->glib->validate_int( $this->ipsclass->input['img'] );

		/**
		* Decide what to do
		*/
		switch( $param )
		{
			case 'dorate':
				$this->rate_image();
			break;
		}
	}

	/**
	* rate::rate_image()
	*
	* Determines if the image can be rated and then if it belongs to an album or category
	*
	* @return none
	**/	
	function rate_image()
	{		
		/**
		 * Determine if this is a category or album, and load the relevent info
		 */
		$this->image   = $this->glib->get_image_info( $this->img_id );
		 
		if( $this->cat_id )
		{
			$this->category = $this->glib->get_category_info( $this->cat_id );				
		}
		else if( $this->album_id )
		{
			$this->album   = $this->glib->get_album_info( $this->album_id );	
		}
		else
		{			
			if( $this->image['category_id'] )
			{
				$this->category = $this->glib->get_category_info( $this->image['category_id'] );
			}
			else
			{
				$this->album    = $this->glib->get_album_info( $this->image['album_id'] );	
			}
		}
		
		/***
		 * Now we need to determine if we are allowed to rate image here
		 **/
		$this->can_rate = 0;
		if( $this->ipsclass->member['g_rate'] && $this->ipsclass->vars['gallery_use_rate'] )
		{
			if( $this->category )
			{
				if( $this->category['rate'] )
				{
					$this->can_rate = 1;	
				}	
			}
			else
			{
				if( $this->ipsclass->vars['gallery_use_rate'] )
				{
					$this->can_rate = 1;	
				}	
			}
		}
		
		if( ! $this->can_rate )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );			
		}
		
		/***
		 * Have we already rated this image?
		 **/		
		if( ! $this->image['rated'] )
		{
			/***
		 	* Now we can actually apply a rating
		 	**/
		 	$this->_do_rate_image();
		}
		else
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );			
		}	
	}

	/**
	* rate::_do_rate_image()
	*
	* Applies the rating to the image
	*
	* @return none
	**/		
	function _do_rate_image()
	{	
		/**
		* Ensure that the rating is between 1-5
		**/
		if( intval( $this->ipsclass->input['rating'] ) > 5 || intval( $this->ipsclass->input['rating'] ) <= 0 )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}
			
		/***
		 * Log the rating
		 **/
		$this->ipsclass->DB->do_insert( 'gallery_ratings', array( 'member_id' => $this->ipsclass->member['id'], 
		                                          'img_id'    => $this->img_id, 
		                                          'date' => time(), 
		                                          'rate'      => intval( $this->ipsclass->input['rating'] ) ) );

		/***
		 * Update image rating
		 **/			
		 $this->ipsclass->DB->simple_update( "gallery_images",
		                     "ratings_total=ratings_total+{$this->ipsclass->input['rating']}, ratings_count=ratings_count+1",
		                     "id={$this->img_id}",
				                    '' );
		$this->ipsclass->DB->simple_exec();
		
		/***
		 * Redirect the user somewhere
		 **/
		if( $this->cat_id )
		{
			$url = "automodule=gallery&cmd=sc&cat={$this->cat_id}"; 	
		}
		else if( $this->album_id )
		{
			$url = "automodule=gallery&cmd=user&user={$this->album['member_id']}&op=view_album&album={$this->album_id}";
		}
		else
		{
			$url = "automodule=gallery&cmd=si&img={$this->img_id}";	
		}
		
		/*
		* Bug #49 */
		if( !empty( $this->ipsclass->input['st'] ) )  {
			$url .= "&st={$this->ipsclass->input['st']}";
		}
		$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['rated'], $url );
	}

	/**
	* rate::_display_rating()
	*
	* Formats and returns the image rating
	*
	* @param array data
	* @return mixed
	**/		
	function _display_rating( $data )
	{		
		// Get the rating
		if( $data['ratings_count'] )
		{
			$rate = $data['ratings_total'] / $data['ratings_count'];
		}

		// Format the rating
		switch( $this->ipsclass->vars['gallery_rate_display'] )
		{
			case 'text':
				return $this->ipsclass->lang['avg_rate'] . round( $rate, 2 );
			break;
	
			case 'graphical':
				return $this->ipsclass->lang['avg_rate'] . '<{GALLERY_RATE_'.round( $rate, 0 ).'}>';
			break;

			case 'both':
				return $this->ipsclass->lang['avg_rate'] . round( $rate, 2 ) . ' <{GALLERY_RATE_'.round( $rate, 0 ).'}>';
			break;
		}		
	}

	/**
	* rate::rating_display()
	*
	* Determines if the form or rating needs to be displayed
	*
	* @param object $html
	* @param object $image
	* @return none
	**/		
	function rating_display( $html, $image )
	{	
		/**
		 * Set the category and album id for the form
		 */
		$this->ipsclass->input['cat']   = $image['category_id'];
		$this->ipsclass->input['album'] = $image['album_id'];

		/**
		 * Are we showing the form or the rating?
		 */		
		if( $this->ipsclass->vars['gallery_use_rate'] )
		{               
            if( $image['rated'] )
            {
				return $this->_display_rating( $image );
			}
			else
			{
            	return $html->rate_form( $image['id'].'-'.uniqid(0) );
			}
		}		
	}
}
?>
