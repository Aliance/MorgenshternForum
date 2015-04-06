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
* Main/Stats
*
* Various gallery stats
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.2
*/

    class stats
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;

    	/**
    	 * stats::start()
    	 * 
		 * Begins execution of this module, $param is used as an entry point into the
		 * module.
		 * 
    	 * @param string $param
    	 * @return nothing
    	 **/
    	function start( $param="" )
    	{
            /* Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_cats'] ) ) {
            	$this->ipsclass->load_template('skin_gallery_cats');
            }
            $this->html = $this->ipsclass->compiled_templates['skin_gallery_cats'];
			
			// Get our image listing class
            require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );
            $this->img_list = new ImageListing();    
            $this->img_list->ipsclass =& $this->ipsclass;
            $this->img_list->glib =& $this->glib;
            $this->img_list->init();    
			
			// Category Stuff
            require( $this->ipsclass->gallery_root .  'categories.php' );
            $this->category = new Categories;
            $this->category->ipsclass =& $this->ipsclass;
            $this->category->glib =& $this->glib;
            $this->category->read_data( true, 'Select a category' );
            
            // Rows/Cols
            $this->rows = ( $this->ipsclass->vars['gallery_stats_rows'] ) ? $this->ipsclass->vars['gallery_stats_rows'] : 2;
            $this->cols = ( $this->ipsclass->vars['gallery_stats_cols'] ) ? $this->ipsclass->vars['gallery_stats_cols'] : 5;
            $this->num  = $this->rows * $this->cols;
			
			// Entry point
            switch( $param )
            {
				case 'comments':
					$this->new_comments();
				break;

				case 'views':
					$this->most_viewed();
				break;

				case 'ratings':
                default:
                    $this->top_rated();
                break;
            }
			
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];	
		
           	$this->ipsclass->print->add_output("$this->output");
            $this->ipsclass->print->do_output( array( 'TITLE' => $this->title, 'JS' => 1, NAV => $this->nav ) );
        }

    	/**
    	 * stats::top_rated()
    	 * 
		 * Displays the top 10 rated images
		 * 
    	 * @return void
    	 **/
		function top_rated()
		{
			$allow_cats = ( $allow_cats ) ? $allow_cats : $this->glib->get_allowed_cats( 1, $this->category->data );
			$allow_albums = ( $allow_albums ) ? $allow_albums : $this->glib->get_allowed_albums();
			
			$this->ipsclass->lang['top10'] = str_replace( "<#NUM#>", $this->num, $this->ipsclass->lang['top10']);			
                								
			$this->img_list->get_listing_data( array(
                                                  'st'           => 0,
                                                  'show'         => $this->num,
                                                  'approve'      => 1,
                                                  'sort_key'     => '(ratings_total/ratings_count)',
				                                  'sort_order'   => 'DESC',
                                                  'allow_cats'   => $allow_cats,
                                                  'allow_albums' => $allow_albums,
                                          )     );
			$this->output .= $this->html->index_list_top( $this->ipsclass->lang['top10'] );               
			$this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => $this->cols, 'imgs_per_row' => $this->row ) );   
			$this->output .= $this->html->index_list_end();
			
			$this->output .= $this->html->cat_page_end();
			
		    $this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery'>".$this->ipsclass->lang['gallery']."</a>";
			$this->nav[] = $this->ipsclass->lang['top10'];
		}

    	/**
    	 * stats::most_viewed()
    	 * 
		 * Displays the top 10 most viewed images
		 * 
    	 * @return void
    	 **/
		function most_viewed()
		{
			$allow_cats = ( $allow_cats ) ? $allow_cats : $this->glib->get_allowed_cats( 1, $this->category->data );
			$allow_albums = ( $allow_albums ) ? $allow_albums : $this->glib->get_allowed_albums();
			
			$this->ipsclass->lang['views10'] = str_replace( "<#NUM#>", $this->num, $this->ipsclass->lang['views10']);			
                								
			$this->img_list->get_listing_data( array(
                                                  'st'           => 0,
                                                  'show'         => $this->num,
                                                  'approve'      => 1,
                                                  'sort_key'     => 'views',
				                                  'sort_order'   => 'DESC',
                                                  'allow_cats'   => $allow_cats,
                                                  'allow_albums' => $allow_albums,
                                          )     );

			$this->output .= $this->html->index_list_top( $this->ipsclass->lang['views10'] );               
			$this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => $this->cols, 'imgs_per_row' => $this->row ) );   
			$this->output .= $this->html->index_list_end();
			
			$this->output .= $this->html->cat_page_end();
			
		    $this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery'>".$this->ipsclass->lang['gallery']."</a>";
			$this->nav[] = $this->ipsclass->lang['views10'];

		}

    	/**
    	 * stats::new_comments()
    	 * 
		 * Displays the last 10 comments made
		 * 
    	 * @return void
    	 **/
		function new_comments()
		{
			// Get the comments
			$this->ipsclass->DB->cache_add_query( 'get_comment_thumbs', array( 'total' => $this->num ), 'gallery_sql_queries' );
			$res = $this->ipsclass->DB->simple_exec();

			$this->ipsclass->lang['comments10'] = str_replace( "<#NUM#>", $this->num, $this->ipsclass->lang['comments10']);						

			$this->output .= $this->html->index_list_top( $this->ipsclass->lang['comments10'] );               

			// Loop through the comments
			while( $i = $this->ipsclass->DB->fetch_row( $res ) )
			{
				$i['name'] = $this->glib->make_name_link( $i['author_id'], $i['name'] );
				$i['image'] = $this->glib->make_image_link( $i, $i['thumbnail'] );
				$i['date'] = $this->ipsclass->get_date( $i['post_date'], 'LONG' );
				$this->output .= $this->html->stats_comment_row( $i );
			}

			$this->output .= $this->html->index_list_end();
			
			$this->output .= $this->html->cat_page_end();
			
		    $this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery'>".$this->ipsclass->lang['gallery']."</a>";
			$this->nav[] = $this->ipsclass->lang['comments10'];
		}
    }
?>
