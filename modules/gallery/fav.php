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
* Main/Fav
*
* Used for adding, removing, and viewing
* favorite images
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/
   
    class fav
    {
        var $ipsclass;
        var $glib;

        var $output;
        var $nav;
        var $info;
        var $html;
        var $ucp_html;
        var $img_list;

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
    		/*
    		* Check some input
    		*/
    		$this->ipsclass->input['st'] = $this->glib->validate_int( $this->ipsclass->input['st'] );
    		
            if( $this->ipsclass->input['img'] )
            {
                $this->ipsclass->input['img'] = intval( $this->ipsclass->input['img'] );

                if( ! $this->ipsclass->input['img'] )
                {
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                }
            }

            if( ! $this->ipsclass->member['g_favorites'] )
            {
                $param = "not_authorized";
            }

	    	$this->ipsclass->load_language( 'lang_ucp' );

	    	/*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_favs'] ) ) {
                $this->ipsclass->load_template( 'skin_gallery_favs' );
            }
            
            /*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_ucp'] ) ) {
        		$this->ipsclass->load_template( 'skin_ucp' );
            }

            $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_favs' ];
            $this->ucp_html = $this->ipsclass->compiled_templates[ 'skin_ucp' ];

            require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );
            $this->img_list = new ImageListing();
            $this->img_list->ipsclass =& $this->ipsclass;
            $this->img_list->glib =& $this->glib;
            $this->img_list->init();

            switch( $param )
            {
                case 'not_authorized':
                    $this->output .= $this->html->error( 'dead_section' );
                break;

                case 'download':
                    $this->download_favs();
                break;

                case 'add':
                    $this->add_fav( $this->ipsclass->input['img'] );
                break;

                case 'del':
                    $this->del_fav( $this->ipsclass->input['img'] );
                break;

                case 'index':
                    $this->index( $this->ipsclass->member['id'] );
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
		
		    $this->nav[] = "<a href='".$this->ipsclass->base_url."act=UserCP&amp;CODE=00'>".$this->ipsclass->lang['t_title']."</a>";
    		$this->nav[] = "<a href='".$this->ipsclass->base_url."act=module&amp;module=gallery&amp;cmd=albums'>".$this->ipsclass->lang['m_gallery_albums']."</a>";
    	
        	$this->ipsclass->print->add_output( $this->output );
            $this->ipsclass->print->do_output( array( 'TITLE' => $this->ipsclass->lang['m_gallery'], 'JS' => 1, NAV => $this->nav ) );
        }


    	/**
    	 * fav::index()
    	 * 
		 * Lists the users favorite images
		 * 
    	 * @param integer $mid
    	 * @return none
    	 **/
    	function index( $mid )
    	{
            // Do page spanning stuff
            $this->ipsclass->DB->simple_construct( array( 'select' => 'count(id) AS total', 'from' => 'gallery_favorites', 'where' => "member_id={$this->ipsclass->member['id']}" ) );
            $this->ipsclass->DB->simple_exec();
            $all = $this->ipsclass->DB->fetch_row();

            $this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;
            $show = 9;
		    $favs['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $all['total'],
                                                                'PER_PAGE'    => $show,
                                                                'CUR_ST_VAL'  => $this->ipsclass->input['st'],
                                                                'BASE_URL'    => $this->base_url."?act=module&amp;module=gallery&amp;cmd=favs"
                                                        )     );

            // Get the data
            $this->img_list->get_listing_data( array(
                                            'st'        => $this->ipsclass->input['st'],
                                            'show'      => $show,
                                            'approve'   => 1,
                                            'sort_key'  => 'i.id',
                                            'order_key' => 'DESC',
                                            'favorite'  => $this->ipsclass->member['id'],
                                    )     );

            if( $this->img_list->total_images > 0 )
            {
                $favs['download_favs'] = "<div class='fauxbutton' style='width:250px' align='center'>
                							<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=favs&amp;op=download'><b><img src='style_images/{$this->ipsclass->skin['_imagedir']}/folder_mime_types/zip.gif' border='0' /> {$this->ipsclass->lang['zip_favs']}</b></a>
                						 </div>";
            }

            // Start displaying
            $this->output .= $this->html->fav_view_top( $favs );
            $this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => 3 ) );
            $this->output .= $this->html->fav_view_end( 3 );

            $this->output = preg_replace( "/<#NOW_SHOWING#>/", $this->img_list->total_images, $this->output );
            $this->output = preg_replace( "/<#TOTAL#>/",       $all['total']      , $this->output );
        }


    	/**
    	 * fav::download_favs()
    	 * 
		 * Zips the users favorite images and then sends the zip file
		 * to the browser.
		 * 
    	 * @return none
    	 **/
    	function download_favs()
    	{
            // ----------------------------------
            // Get favorites
            // ----------------------------------
            $this->ipsclass->DB->cache_add_query( 'get_favorites', array( 'mid' => $this->ipsclass->member['id'] ), 'gallery_sql_queries' );									 
            $this->ipsclass->DB->simple_exec();  
            
            // ---------------------------------
            // Need our zip library
            // ---------------------------------
            require( $this->ipsclass->gallery_root .  'lib/zip.lib.php' );
            $zip = new zipfile;

            while( $i = $this->ipsclass->DB->fetch_row() )
            {
	            $dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";            
                $fh   = fopen( $this->ipsclass->vars['gallery_images_path'].'/'.$dir.$i['masked_file_name'], "rb" );
                $data = fread( $fh, filesize( $this->ipsclass->vars['gallery_images_path'].'/'.$dir.$i['masked_file_name'] ) );
                fclose( $fh );

                $zip->add_file( $data, $i['file_name'] );
            }

            // ---------------------------------
            // Send to browser!
            // ---------------------------------
            if( function_exists( "ob_clean" ) )
            {
                ob_clean();
            }
            header("Content-Type: application/zip");
            header("Content-Disposition: inline; filename=favorites.zip");            
            echo $zip->file();
            exit();
        }


    	/**
    	 * fav::add_fav()
    	 * 
		 * Adds an image to the user's favorites list
		 * 
    	 * @param integer $img
    	 * @return none
    	 **/
    	function add_fav( $img )
    	{
            // Check to see if this is already a favorite
            $this->ipsclass->DB->simple_construct( array( 'select' => 'id', 'from' => 'gallery_favorites', 'where' => "img_id={$img} and member_id={$this->ipsclass->member['id']}" ) );
            $this->ipsclass->DB->simple_exec();
            if( $this->ipsclass->DB->get_num_rows() )
            {
                $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['fav_exists'], "act=module&amp;module=gallery&amp;cmd=si&amp;img={$img}" );
            }

            // We can add it now
            $this->ipsclass->DB->do_shutdown_insert( 'gallery_favorites', array( 'img_id' => $img, 'member_id' => $this->ipsclass->member['id'] ) );
            
            $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['fav_added'], "act=module&amp;module=gallery&amp;cmd=si&amp;img={$img}" );

        }

    	/**
    	 * fav::del_fav()
    	 * 
		 * Removes an image from the users favoirte list
		 * 
    	 * @param $img
    	 * @return void
    	 **/
    	function del_fav( $img )
    	{
            $this->ipsclass->DB->simple_construct( array( 'delete' => 'gallery_favorites', 'where' => "member_id={$this->ipsclass->member['id']} AND img_id={$img}" ) );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['fav_removed'], "act=module&amp;module=gallery&amp;cmd=favs" );
        }
    }
?>
