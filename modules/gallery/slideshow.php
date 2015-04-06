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
* Main/Slideshow
*
* Display slideshows
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.2
*/

    class slideshow
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;

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
            // Check input
            if( $this->ipsclass->input['cat'] )
            {
                $this->ipsclass->input['cat'] = intval( $this->ipsclass->input['cat'] );

                if( ! $this->ipsclass->input['cat'] ) 
                {
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                }
            }

            // Check input
            if( $this->ipsclass->input['album'] )
            {
                $this->ipsclass->input['album'] = intval( $this->ipsclass->input['album'] );

                if( ! $this->ipsclass->input['album'] ) 
                {
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                }
            }

            /*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_cats'] ) ) {
            	$this->ipsclass->load_template('skin_gallery_cats');
            }
            $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_cats' ];

            if( $this->ipsclass->input['show'] )
            {
                $this->slide_show();
            }
            else
            {
                $this->slide_show_splash();
            }

            $this->ipsclass->print->add_output( $this->output );

            $this->ipsclass->print->do_output( array( 
                                      'TITLE'    => $this->title,
            					 	  'NAV'      => $this->nav,
                             )       );


        }

    	/**
    	 * slideshow::slide_show_splash()
    	 * 
		 * Displays slide show options
		 * 
    	 * @return none
    	 **/
        function slide_show_splash()
        {
            // Grab the category information
            if( $this->ipsclass->input['cat'] )
            {
                $info = $this->setup_cat();
            }
            else
            {
                $info = $this->setup_album();
            }
                        
            // Show the form
            $this->output .= $this->html->ss_form();

            $sort = $this->glib->build_sort_order_info( $info['def_view'] );

            $this->output = preg_replace( "/<#SORT_KEY_HTML#>/", $sort['SORT_KEY_HTML'], $this->output );
            $this->output = preg_replace( "/<#ORDER_HTML#>/"   , $sort['ORDER_KEY_HTML'], $this->output );
            $this->output = preg_replace( "/<#PRUNE_HTML#>/"   , $sort['PRUNE_KEY_HTML'], $this->output );
            
            // Page Info
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav[] = "{$this->ipsclass->lang['ss_start']} {$info['name']}";
        }

    	/**
    	 * slideshow::setup_cat()
    	 * 
		 * Gets category info and authorizes permissions
		 * 
    	 * @return none
    	 **/
        function setup_cat()
        {
            $this->ipsclass->DB->simple_construct( array( "select" => 'perms_view, def_view, id, name', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();        
            $cat = $this->ipsclass->DB->fetch_row();

            // Are we allowed to view this category?
            if( ! $this->ipsclass->check_perms( $cat['perms_view'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );                
            }

            return $cat;
        }

    	/**
    	 * slideshow::setup_album()
    	 * 
		 * Gets album info and authorizes permissions
		 * 
    	 * @return none
    	 **/
        function setup_album()
        {
            $this->ipsclass->DB->simple_construct( array( "select" => 'id, name, member_id, public_album', 'from' => 'gallery_albums', 'where' => "id={$this->ipsclass->input['album']}" ) );
            $this->ipsclass->DB->simple_exec();        
            $info = $this->ipsclass->DB->fetch_row();

            // Are we allowed to view this album?
            if( $info['member_id'] == $this->ipsclass->member['id'] )
            {
                $own = true;
            }

            if( ! $info['public_album'] && ! $own )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }

            return $info;
        }
		
    	/**
    	 * slideshow::slide_show()
    	 * 
		 * Generates a slide show based on the current category/album
		 * 
    	 * @return none
    	 **/
    	function slide_show()
    	{
            // Setup show
            $show = ( $this->ipsclass->input['show'] == 'first' ) ? 0 : $this->ipsclass->input['show'];

            // Figure out if the user has chosen a value, or if we should go with a default
            $sort_key  = ( $this->ipsclass->input['sort_key'] )  ? $this->ipsclass->input['sort_key']  : 'date';
            $order_key = ( $this->ipsclass->input['order_key'] ) ? $this->ipsclass->input['order_key'] : 'DESC';
            $prune_key = ( $this->ipsclass->input['prune_key'] ) ? $this->ipsclass->input['prune_key'] : '30';

            if( ! empty( $this->ipsclass->input['cat'] ) )
            {
                $where = "category_id=". intval( $this->ipsclass->input['cat'] );
            }
            else
            {
                $where = "album_id=". intval( $this->ipsclass->input['album'] );
            }

            // Get the picture
            $this->ipsclass->DB->cache_add_query( 'slideshow_image', 
							  array( 'where'       => $where,
									 'prune'       => $prune,
                                     'sort_key'    => $sort_key,
                                     'order_key'   => $order_key,
                                     'show'        => $show,
							), 'gallery_sql_queries'      );									 
            $this->ipsclass->DB->simple_exec();
            
            // Show it
            $i = $this->ipsclass->DB->fetch_row();

            if( $this->ipsclass->DB->get_num_rows() )
            {
                $show++;
                $photo = $this->glib->make_image_link( $i );
                $type =  ( $this->ipsclass->input['cat']         ) ? "&amp;cat={$this->ipsclass->input['cat']}" : "&amp;album={$this->ipsclass->input['album']}&amp;mid={$i['mid']}";
                $hfoff = ( $this->ipsclass->input['hfoff']       ) ? "&hfoff=1" : "";
				$close = ( $this->ipsclass->input['closewindow'] ) ? "&closewindow=1" : "";
				$url = "{$this->ipsclass->base_url}automodule=gallery&cmd=slideshow&show={$show}&duration={$this->ipsclass->input['duration']}&sort_key={$this->ipsclass->input['sort_key']}&order_key={$this->ipsclass->input['order_key']}&prune_key={$this->ipsclass->input['prune_key']}{$type}{$hfoff}{$close}";
                $this->output .= $this->html->ss_slide( $photo, $url, $i['id'] );
            }
            else
            {
                if( ! empty( $this->ipsclass->input['cat'] ) )
                {
                    $url = "automodule=gallery&cmd=sc&cat={$this->ipsclass->input['cat']}";
                }
                else
                {
                    $url = "automodule=gallery&cmd=user&user={$this->ipsclass->input['mid']}&op=view_album&album={$this->ipsclass->input['album']}";
                }

				if( $this->ipsclass->input['closewindow'] )
				{
					echo '<html><body onload="javascript:window.close();"></html>';
					die();
				}
                
                $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['ss_end'], $url );
            }

            // Page Info
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];
            $this->nav[] = "<a href='".$this->ipsclass->base_url."automodule=gallery'>Gallery</a>";
            $this->nav[] = $i['caption'];
			
            // -------------------------------------------------------
            // Stat Update!
            // -------------------------------------------------------
            $this->ipsclass->DB->simple_update( 'gallery_images', 'views=views+1', "id={$i['id']}", 1 );
            $this->ipsclass->DB->simple_exec();

			if( $this->ipsclass->input['hfoff'] )
			{
				$this->ipsclass->print->pop_up_window( $this->title, $this->output );
			}
        }            
    }
?>
