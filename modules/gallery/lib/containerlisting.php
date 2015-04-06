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
* Library/Container Listing
*
* Handles printing  image listings
* and such.
*
* @package		Gallery
* @subpackage	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.3
*/

    class ContainerListing
    {
        var $res = '';
        var $ipsclass;
        var $glib;
        var $img_html;
        var $category_cache = array();

        function ContainerListing()
        {
         
        }
        
        function ContainerInit() {
        	/*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_imagelisting'] ) ) {
        	   	$this->ipsclass->load_template('skin_gallery_imagelisting');                       
            }
            $this->img_html = $this->ipsclass->compiled_templates['skin_gallery_imagelisting'];
        }

        /**
         * ImageListing::get_listing_data()
         * 
         * @param array $params
         * @return nothing
         **/
        function get_listing_data( $params )
        {	
			if( $params['album'] )
			{
				$this->album_mode = 1;
	            // Get Albums
    	        $this->ipsclass->DB->cache_add_query( 'get_album_list', 
				 			   array( 'pre'              => "a",
								 	  'SORT_KEY'         => 'name',
    	                              'ORDER_KEY'        => 'asc',
    	                              'st'               => intval( $ibforums->input['st'] ),
    	                              'gallery_user_row' => 100,
    	                              'mid'              => " AND a.member_id={$params['member_id']}",
				 				   ), 'gallery_sql_queries'      );									 
			}
			else
			{
				$this->album_mode = 0;
   	        	$this->ipsclass->DB->cache_add_query( 'get_mem_posted_categories', 
				 			   array( 
				 			          'mid'        => $params['member_id'],
				 			          'allow_cats' => $params['category_cache'],
				 				   ), 'gallery_sql_queries'      );									 
					
			}
            $this->res = $this->ipsclass->DB->simple_exec();
            
            $this->total_items = $DB->get_num_rows( $this->res );
        }

        /**
         * ImageListing::get_html_listing()
		 * 
         * @param array $options
         * @return string
         **/
        function get_html_listing( $options=array() )
        {
  			if( $this->total_items )
  			{
                $output .= $this->img_html->container_row_top();  			
                $output .= $this->img_html->view_begin_row();
                
                while( $i = $this->ipsclass->DB->fetch_row( $this->res ) )
                {
                    if( $col_count >= 2 )
                    {
                        $output .= $this->img_html->view_end_row();
                        $output .= $this->img_html->view_begin_row();
                        $col_count = 0;
                    }
                    $col_count++;
                    
                    $i['image'] = $this->glib->make_image_link( $i, $i['thumbnail'] );
                    $i['date']    = $this->ipsclass->get_date( $i['date'], 'LONG' );
                    $i['member']    = $this->glib->make_name_link( $i['member_id'], $i['mname'] );
					
					if( $this->album_mode )
					{				
	                    $output .= $this->img_html->view_album_row( $i );
	                }
	                else
	                {
	                    $output .= $this->img_html->view_cat_row( $i );	                
	                }

                }
                
                if( $col_count != 0 )
                {
                    $output .= $this->img_html->view_end_row();
                } 
                
                $output .= $this->img_html->container_row_bottom();  			
                
  			}
  			else
  			{
                $output .= $this->img_html->basic_row( 'none_found' );  			
  			}
  			
  			return $output;
        }
    }
?>
