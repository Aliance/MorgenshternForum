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
* Admin/Album Manager
*
* Album admin manager
*
* @package		Gallery
* @subpackage 	Admin
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.1
*/

//---------------------------------------
// Security check
//---------------------------------------

if ( IPB_CALLED != 1 )
{
    print "You cannot access this module in this manner";
    exit();
}

//---------------------------------------
// Carry on!
//---------------------------------------

class ad_plugin_gallery_sub {

    var $ipsclass;
    var $glib;
    var $forumfunc;
    var $modules;

    /**
     * ad_plugin_gallery::ad_plugin_gallery_sub()
     * 
	 * Class Constructor
	 * 
     * @return void
     **/
    function auto_run()
    {
		$this->ipsclass->forums->forums_init();
		
		require ROOT_PATH.'sources/lib/admin_forum_functions.php';
		
		$this->forumfunc = new admin_forum_functions();

        //---------------------------------------
        // Kill globals - globals bad, Homer good.
        //---------------------------------------

        $tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

        foreach ( $tmp_in as $k => $v )
        {
            unset($$k);
        }

        //--------------------------------------------
        // Get the sync module
        //--------------------------------------------

        if ( USE_MODULES == 1 )
        {
            require ROOT_PATH."modules/ipb_member_sync.php";

            $this->modules = new ipb_member_sync();
        }

        switch( $this->ipsclass->input['pg'] )
        {
            case 'delalbum':
                $this->_do_del_album();
            break;

            case 'emptyalbum':
                $this->_do_empty_album();
            break;

            case 'editalbum':
                $this->edit_album_form();
            break;

            case 'doedit':
                $this->_do_edit_album();
            break;
            
            case 'recountallalbums':
            	$this->_do_recount_all();
            	$this->index_screen();
            break;            
            
            case 'recountalbum':
            	$this->_do_recount();
            	$this->index_screen();
            break;

			case 'rebuildalbum':
				$this->_do_rebuild();
			break;
			
			case 'createalbum':
			    $this->_do_create_album();

            default:
                $this->index_screen();
                break;

        }


    }

   /******************************************************************
    *
    * Album Management
    *
    **/

   /*
   * ::_do_create_album()
   * Mass create albums, or create just for one user
   */
   function _do_create_album()  {
   	  if( empty( $this->ipsclass->input[ 'album_name' ] ) ) {
   	  	 	/* Throw error */
   	  	 	$this->ipsclass->admin->error( "Please specify an album name." );
   	  }
   	  $mem = 'onlymem';
   	  /* One album, or many? */
   	  if( $mem == 'onlymem' )  {
   	  	 /* Just a single user */
   	  	 if( empty( $this->ipsclass->input[ 'mem_name' ] ) ) {
   	  	 	/* Throw error */
   	  	 	$this->ipsclass->admin->error( "Please specify the user that will own the album." );
   	  	 }
   	  	 
   	  	 /* Make sure user exists */
   	  	 $this->ipsclass->DB->simple_construct( array(
   	  	                      'select'   => 'id',
   	  	                      'from'     => 'members',
   	  	                      'where'    => "name = '{$this->ipsclass->input[ 'mem_name' ]}'" ) );
   	  	                      
         $member = $this->ipsclass->DB->simple_exec();
         if( $this->ipsclass->DB->get_num_rows( $member ) ) {
         	/* Member exists */
         	$member = $this->ipsclass->DB->fetch_row();
         	 $insert = array( 'member_id'   => $member['id'],
                      'public_album'=> ( ( $this->ipsclass->input['album_status'] == "public" ) ? 1 : 0 ), 
                      'name'        => $this->ipsclass->input['album_name'],
                      'description' => $this->ipsclass->input['album_desc'],
                      'category_id' => ( $this->ipsclass->input['album_cat'] ) ? intval( $this->ipsclass->input['album_cat'] ) : 0,
             );
            $this->ipsclass->DB->do_insert( 'gallery_albums', $insert );
            $done = "Album <i>{$this->ipsclass->input[ 'album_name' ]}</i> created for <i>{$this->ipsclass->input[ 'mem_name' ]}</i>";
            $this->ipsclass->admin->done_screen( $done , "Album Manager", "act=gallery&section=components&code=albums" );
         }
         else {
         	$this->ipsclass->admin->error( "Member <i>{$this->ipsclass->input[ 'mem_name' ]}</i> doesn't exist." );
         }
   	  }
   	  
   	  /* All users, queue up and only do 50 at a time */
   	  if( $this->ipsclass->input[ 'create_for' ] == 'allmem' )  {
   	  	/* Turned off until a later version of IG( 3.0 maybe? )
   	  	* too process intensive, especially for large boards */
   	  }
   	  
   }
   /**
    * ad_plugin_gallery::_do_rebuild()
    * 
	* Rebuilds all thumbnails in the gallery
	* 
    * @return void
    **/
    function _do_rebuild()
    {

        require( ROOT_PATH . 'modules/gallery/lib/image.php' );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, masked_file_name, thumbnail, directory', 
                                      'from'   => 'gallery_images', 
                                      'where'  => "album_id={$this->ipsclass->input['id']}",
                                       ) );
        $q = $this->ipsclass->DB->simple_exec();

        if( $this->ipsclass->DB->get_num_rows( $q ) )
        {
            while( $i = $this->ipsclass->DB->fetch_row( $q ) )
            {
                if( $i['media'] )
                {
                	continue;
                }            
            
                $total++;
                $dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
                $tn  = "{$this->ipsclass->vars['gallery_images_path']}/{$dir}tn_{$i['masked_file_name']}";

                // Check for an existing thumbnail
                if( file_exists( $tn ) )
                {
                    @unlink( $tn );
                }
                
                // Image Info
                $img_load = array( 'out_dir'  => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                                   'in_dir'   => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                                   'in_file'  => $i['masked_file_name'],
                                   'out_file' => "tn_{$i['masked_file_name']}",
                                 );
                
                // Create the thumbnail
                $img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                $img->lib_setup();

                if( $img->thumbnail( $this->ipsclass->vars['gallery_thumb_width'], $this->ipsclass->vars['gallery_thumb_height'] ) )
                {
                    $img->write_to_file();
                    $thumbnail = 1;
                    $this->ipsclass->DB->simple_update( 'gallery_images', 'thumbnail=1', "id={$i['id']}", 1 );
                    $this->ipsclass->DB->simple_exec();
                }
                else
                {
                    $this->ipsclass->DB->simple_update( 'gallery_images', 'thumbnail=0', "id={$i['id']}", 1 );
                    $this->ipsclass->DB->simple_exec();
                }
                unset( $img );

                // Image Info
                $img_load = array( 'out_dir'  => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                                   'in_dir'   => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                                   'in_file'  => $i['masked_file_name'],
                                   'out_file' => $i['masked_file_name'],
                                 );
                
                // Create the image
                $img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                $img->lib_setup();

                if( $img->resize_proportional( $this->ipsclass->vars['gallery_max_img_width'], $this->ipsclass->vars['gallery_max_img_height'], 1 ) )
                {
                    $img->write_to_file();
                }
                unset( $img );
            }            
        }
        else
        {
            $this->ipsclass->admin->error( "No images found" );
        }

         $this->ipsclass->admin->done_screen("Album rebuilt", "Gallery Manager", "act=gallery&section=components" );
    }

    
   /**
     * ad_plugin_gallery::_do_recount()
     * 
	 * Recount images and comments for an album
	 * 
     * @return void
     **/    
    function _do_recount_all()
    {

        
        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, member_id',
                                     'from'   => 'gallery_albums' ) );
                                     
		$this->ipsclass->DB->simple_exec();
		
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$albums[] = array( $i['id'], $i['member_id'] );
		}
				
		foreach( $albums as $a )
		{
			$album = $a[0];
			$mid   = $a[1];			
			unset( $ids );
      		$this->ipsclass->DB->simple_construct( array( 'select' => 'id', 
										  'from'   => 'gallery_images', 
            	                          'where'  => "album_id={$album}" ) );
		    $this->ipsclass->DB->simple_exec();

		    while( $i = $this->ipsclass->DB->fetch_row() )
      		{
          		$ids[] = $i['id'];
      		}
      
		    if( is_array( $ids ) )
		    {
				$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS comments', 
        		                                'from'   => 'gallery_comments', 
        		                                'where'  => "img_id IN ( ".implode( ",", $ids )." ) AND approved=1" ) );
		        $this->ipsclass->DB->simple_exec();
        		$info = $this->ipsclass->DB->fetch_row();

        		$this->ipsclass->DB->simple_update( 'gallery_albums', "comments={$info['comments']}", "id={$album}", 1 );
        		$this->ipsclass->DB->simple_exec();
      		}
      		else
      		{
        		$this->ipsclass->DB->simple_update( 'gallery_albums', "comments=0", "id={$album}", 1 );
        		$this->ipsclass->DB->simple_exec();      		
      		}

		    $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS images', 
        	                              'from'   => 'gallery_images', 
        	                              'where'  => "album_id={$album} AND approved=1" ) );
		    $this->ipsclass->DB->simple_exec();
      		$info = $this->ipsclass->DB->fetch_row();

			$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$mid}" ) );
			$this->ipsclass->DB->simple_exec();
			$mem = $this->ipsclass->DB->fetch_row();      		

		    $this->ipsclass->DB->simple_update( 'gallery_albums', "images={$info['images']}, last_name='{$mem['name']}'", "id={$album}", 1 );
		    $this->ipsclass->DB->simple_exec();
		}

        $this->ipsclass->main_msg = "Albums Recounted";

    }    
    
    
    /**
     * ad_plugin_gallery::_do_recount()
     * 
	 * Recount images and comments for an album
	 * 
     * @return void
     **/    
    function _do_recount()
    {


        $this->ipsclass->DB->simple_construct( array( 'select' => 'id', 
                                      'from'   => 'gallery_images', 
                                      'where'  => "album_id={$this->ipsclass->input['id']}" ) );
        $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $ids[] = $i['id'];
        }
        
        if( is_array( $ids ) )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS comments', 
                                          'from'   => 'gallery_comments', 
                                          'where'  => "img_id IN ( ".implode( ",", $ids )." ) AND approved=1" ) );
            $this->ipsclass->DB->simple_exec();
            $info = $this->ipsclass->DB->fetch_row();



            $this->ipsclass->DB->simple_update( 'gallery_albums', "comments={$info['comments']}", "id={$this->ipsclass->input['id']}", 1 );
            $this->ipsclass->DB->simple_exec();
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS images', 
                                      'from'   => 'gallery_images', 
                                      'where'  => "album_id={$this->ipsclass->input['id']} AND approved=1" ) );
        $this->ipsclass->DB->simple_exec();
        $info = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_update( 'gallery_albums', "images={$info['images']}", "id={$this->ipsclass->input['id']}", 1 );
        $this->ipsclass->DB->simple_exec();


        $this->ipsclass->main_msg = "Album Recounted";

    }    
    

   /**
    * ad_plugin_gallery::edit_album_form()
    * 
	* Edit user album form
	* 
    * @return void
    **/
    function edit_album_form()
    {
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $cats = new Categories;
        $cats->ipsclass =& $this->ipsclass;
        $cats->glib =& $this->glib;
        
        $cats->read_data( false, '', 0 );

        
        $this->ipsclass->DB->cache_add_query( 'get_mem_albums', array( 'id' => $this->ipsclass->input['id'] ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();
        $this->ipsclass->inputfo = $this->ipsclass->DB->fetch_row();

        /* Page Details */
        $this->ipsclass->admin->page_detail = "You can edit the album here.";
        $this->ipsclass->admin->page_title  = "Edit Album: {$this->ipsclass->inputfo['name']}";
        
        /* Table Headings */
        $this->ipsclass->adskin->td_header[] = array( "Attribute" , "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "65%" );
        
        /* Form Start */
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'albums'    ),
                                                  2 => array( 'act'   , 'gallery'   ),
                                                  3 => array( 'pg'    , 'doedit'    ),
                                                  4 => array( 'id'    , $this->ipsclass->input['id']   ),
                                                  5 => array( 'section', 'components' ),
                                     )  );

        /* Table Start */
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Edit Album: {$this->ipsclass->inputfo['name']}" );

        /* Table Rows */
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Album Owner</b>", "{$this->ipsclass->inputfo['member_name']} [ <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&view_mem={$this->ipsclass->inputfo['member_id']}' title='View all albums for this user'><b>View All Albums</b></a> ] [ <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=domemsrch&viewuser={$this->ipsclass->inputfo['member_id']}'><b>View Member Report</b></a> ]" ) );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Album Name</b>", $this->ipsclass->adskin->form_input( 'name', $this->ipsclass->inputfo['name'] ) ) );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Album Description</b>", $this->ipsclass->adskin->form_textarea( 'description', $this->ipsclass->inputfo['description'] ) ) );
        /* Move album*/
        $category_dropdown = $cats->build_dropdown( 'move_cat' );
        
        if( empty( $this->ipsclass->inputfo[ 'category_name' ] ) ) {
        	$this->ipsclass->inputfo['category_name'] = 'Not assigned to category.';
        }
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Currently in category: <i>{$this->ipsclass->inputfo['category_name']}</i></b><br />If you would like to move this album to a new category, select the new category from the drop down to the right.", $category_dropdown ) );
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Public Album?</b>", $this->ipsclass->adskin->form_yes_no( 'public_album', $this->ipsclass->inputfo['public_album'] ) ) );

        /* Table End */
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Edit" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();
    }

   /**
    * ad_plugin_gallery::_do_edit_album()
    * 
	* Process the edit album form
	* 
    * @return void
    **/
    function _do_edit_album()
    {
        
    	/*
    	* Do they want to move the album? */
    	$move_str = '';
    	if( $this->ipsclass->input['move_cat'] != 0 ) {
    		/* Update images in album first to correct category */
    		$update = $this->ipsclass->DB->compile_db_update_string( array( 'category_id'  => $this->ipsclass->input['move_cat'] ) );
    		$this->ipsclass->DB->simple_update( 'gallery_images', $update, "album_id = {$this->ipsclass->input['id']}", 1 );
    		$this->ipsclass->DB->simple_exec();
    		$move_str = "<br />Album <i>{$this->ipsclass->input['name']}</i> and it's images have been moved.";
    	}
        
        $str = $this->ipsclass->DB->compile_db_update_string( array( 'name'         => $this->ipsclass->input['name'],
                                                     'description'  => $this->ipsclass->input['description'],
                                                     'public_album' => $this->ipsclass->input['public_album'],
                                                     'category_id'  => $this->ipsclass->input['move_cat'] ) );

        $this->ipsclass->DB->simple_update( 'gallery_albums', $str, "id={$this->ipsclass->input['id']}", 1 );
        $this->ipsclass->DB->simple_exec();

        $this->ipsclass->admin->done_screen( "Album update successful.{$move_str}", "Invision Gallery Manager", "section=components&act=gallery" );
    }

   /**
    * ad_plugin_gallery::_do_del_album()
    * 
	* Removes an album from the gallery
	* 
    * @return void
    **/
    function _do_del_album()
    {


        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, masked_file_name, thumbnail, directory', 
                                      'from'   => 'gallery_images', 
                                      'where'  => "album_id={$this->ipsclass->input['id']}" ) );
        $q = $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row( $q ) )
        {
            $dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
            @unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.$i['masked_file_name'] );
            if( $this->img['thumbnail'] )
            {
                @unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.'tn_'.$i['masked_file_name'] );
            }
            $this->ipsclass->DB->simple_delete( 'gallery_comments' , "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_bandwidth', "file_name='{$i['masked_file_name']}'" ); $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_ratings'  , "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_favorites', "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_images'   , "id={$i['id']}" );                        $this->ipsclass->DB->simple_exec();
        }
        $this->ipsclass->DB->simple_delete( 'gallery_albums', "id={$this->ipsclass->input['id']}" ); $this->ipsclass->DB->simple_exec();
        $this->ipsclass->admin->done_screen( "The album has been removed and it's contents deleted", "Invision Gallery Manager", "section=components&act=gallery" );

    }

   /**
    * ad_plugin_gallery::_do_empty_album()
    * 
	* Removes an album from the gallery
	* 
    * @return void
    **/
    function _do_empty_album()
    {


        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, masked_file_name, thumbnail, directory', 
                                      'from'   => 'gallery_images', 
                                      'where'  => "album_id={$this->ipsclass->input['id']}" ) );
        $q = $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row( $q ) )
        {
            $dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
            @unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.$i['masked_file_name'] );
            if( $this->img['thumbnail'] )
            {
                @unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.'tn_'.$i['masked_file_name'] );
            }

            $this->ipsclass->DB->simple_delete( 'gallery_comments' , "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_bandwidth', "file_name='{$i['masked_file_name']}'" ); $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_ratings'  , "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_favorites', "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
            $this->ipsclass->DB->simple_delete( 'gallery_images'   , "id={$i['id']}" );                        $this->ipsclass->DB->simple_exec();
        }
        
        $this->ipsclass->DB->simple_update( 'gallery_albums', 'images=0,comments=0, last_pic=0', "id={$this->ipsclass->input['id']}", 1 );
        $this->ipsclass->DB->simple_exec();        

        $this->ipsclass->admin->done_screen( "The album's contents have been emptied", "Invision Gallery Manager", "act=gallery" );

    }


   /**
    * ad_plugin_gallery::index_screen()
    * 
	* Displays the album index.
	* 
    * @return void
    **/
    function index_screen()
    {
    	/* Do some requireeees */
    	require( ROOT_PATH . 'modules/gallery/categories.php' );
        $cats = new Categories;
        $cats->ipsclass =& $this->ipsclass;
        $cats->glib =& $this->glib;
        $cats->read_data( false, '', 0 );
        
        /* Page Details */
        $this->ipsclass->admin->page_detail = "This is where you can manage the user albums in your gallery.";
        $this->ipsclass->admin->page_title  = "Album Manager";
        
        /* Include for ajax type ahead */
        $this->ipsclass->html .= "<script type=\"text/javascript\" src='jscripts/ipb_xhr_findnames.js'></script>";
        $this->ipsclass->html .= "<div id='ipb-get-members' style='border:1px solid #000; background:#FFF; padding:2px;position:absolute;width:210px;display:none;z-index:1'></div>";
        
        /* Table Headings */
        $this->ipsclass->adskin->td_header[] = array( "Album"        , "23%" );
        $this->ipsclass->adskin->td_header[] = array( "Member Name"  , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Status"       , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Comments"     , "5%" );
		$this->ipsclass->adskin->td_header[] = array( "Images"       , "5%" );
        $this->ipsclass->adskin->td_header[] = array( "Disk Space"   , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Views"        , "5%" );
       // $this->ipsclass->adskin->td_header[] = array( "Options"      , "22%" );

        /* Table Start */
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Albums [ <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&pg=recountallalbums'>Recount All Albums</a> ]" );

        
        /* Do some query setup */
        $this->ipsclass->input['sort_key'] = ( $this->ipsclass->input['sort_key'] ) ? $this->ipsclass->input['sort_key'] : 'a.last_pic';
        $this->ipsclass->input['sort_by']  = ( $this->ipsclass->input['sort_by'] )  ? $this->ipsclass->input['sort_by']  : 'DESC';

        if( $this->ipsclass->input['view_mem'] )
        {
            $where   = " a.member_id={$this->ipsclass->input['view_mem']} AND ";
            $c_where = " a.member_id={$this->ipsclass->input['view_mem']}";
            $more    = "&view_mem={$this->ipsclass->input['view_mem']}";
        }
        else if( isset( $this->ipsclass->input['view_status'] ) )
        {
            $where   = " a.public_album={$this->ipsclass->input['view_status']} AND ";
            $c_where = " a.public_album={$this->ipsclass->input['view_status']}";
            $more    = "&view_status={$this->ipsclass->input['view_status']}";
        }
        
        if( $this->ipsclass->input['search'] )
        {
            if( in_array( $this->ipsclass->input['search_in'], array( 'a.name', 'a.description' ) ) )
            {
                $q = " {$this->ipsclass->input['search_in']} LIKE '%{$this->ipsclass->input['search_for']}%'";
            }
            else
            {
            	if( empty( $this->ipsclass->input['search_for'] ) )
            	{
            		$this->ipsclass->html = '';
            		$this->ipsclass->admin->error( "Please enter a value to search for in the 'Search For' field" );	
            	}
                $this->ipsclass->input['search_type'] = str_replace( "&gt;", ">", $this->ipsclass->input['search_type'] );
                $this->ipsclass->input['search_type'] = str_replace( "&lt;", "<", $this->ipsclass->input['search_type'] );

                $q = " {$this->ipsclass->input['search_in']} {$this->ipsclass->input['search_type']} {$this->ipsclass->input['search_for']}";
            }
            $where   .= "{$q} AND ";
            $c_where .= ( $c_where ) ? " AND {$q}" : " {$q} ";
            $more    .= "&search_in={$this->ipsclass->input['search_in']}&search_for={$this->ipsclass->input['search_for']}&search_type={$this->ipsclass->input['search_type']}&search=yes&sort_key={$this->ipsclass->input['sort_key']}&sort_by={$this->ipsclass->input['sort_by']}";
        }
        
        /* Page Spanning */
        $this->ipsclass->DB->simple_construct( array( "select" => 'COUNT(*) AS total', 'from' => 'gallery_albums a', 'where' => "{$c_where}" ) );
        $this->ipsclass->DB->simple_exec();
        $total = $this->ipsclass->DB->fetch_row();
        $total = ( $total['total'] ) ? $total['total'] : 0;

        $st = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;

        $links = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $total,
                                               'PER_PAGE'    => 20,
                                               'CUR_ST_VAL'  => $st,
                                               'L_SINGLE'    => "Single Page",
                                               'L_MULTI'     => "Pages: ",
                                               'BASE_URL'    => $this->ipsclass->base_url.'&section=components&act=gallery&code=albums'.$more
                                             )
                                      );

        $this->ipsclass->DB->cache_add_query( 'get_albums', array( 'where'    => $where,
                                                   'sort_key' => $this->ipsclass->input['sort_key'],
                                                   'sort_by'  => $this->ipsclass->input['sort_by'],
                                                   'st'       => $st            
                                                 ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        /* Loop through and display albums */
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( $links, 'right', 'catrow2' );
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $i['total_views']  = ( $i['total_views'] )  ? $i['total_views']  : 0;
            $i['album_status'] = ( $i['public_album'] ) ? 'Public' : 'Private';            
			
			// Edit, Delete, Empty, Delete, zip, directory
			$controls = "<table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td width='60%' align='left'><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&pg=editalbum&id={$i['id']}' title='Edit this album'><img src='{$this->ipsclass->adskin->img_url}/images/acp_edit.gif' border='0' alt='edit'></a>
                         <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&pg=recountalbum&id={$i['id']}' title='Recount images and posts in this album'><img src='{$this->ipsclass->adskin->img_url}/images/acp_resync.gif' border='0' alt='recount'></a>
 						 <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=bulkadd&album={$i['id']}' title='Import images from a directory into this album'><img src='{$this->ipsclass->adskin->img_url}/images/folder.gif' border='0' alt='directory import'></a>
						 <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=zipfile&album={$i['id']}' title='Import images from a zipfile into this album'><img src='{$this->ipsclass->adskin->img_url}/zip.gif' border='0' alt='zip import'></a>
						 </td><td width='40%' align='right'>
 			             <a href='javascript:checkdelete(\"{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&pg=emptyalbum&id={$i['id']}{$more}\")' title='Remove all images and comments in this album'><img src='{$this->ipsclass->adskin->img_url}/images/acp_trashcan.gif' border='0' alt='empty'></a>
						 <a href='javascript:checkdelete(\"{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&pg=delalbum&id={$i['id']}{$more}\")' title='Delete this album'><img src='{$this->ipsclass->adskin->img_url}/images/acp_delete.gif' border='0' alt='delete'></a>
						 </td></tr></table>
						 ";

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b><a href='{$this->ipsclass->vars['board_url']}/index.php?act=module&module=gallery&cmd=user&user={$i['member_id']}&op=view_album&album={$i['id']}'>{$i['name']}</a></b><br />{$i['description']}<br />$controls",
                                                      "{$i['member_name']} <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&view_mem={$i['member_id']}{$more}' title='View all albums for this user'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..view by member'></a>",
                                                      "<center><i>{$i['album_status']}</i> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&view_status={$i['public_album']}{$more}' title='View all albums with this status'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..with this status'></a></center>",
                                                      "<center>{$i['comments']}</center>",
                                                      "<center>{$i['images']} <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=albums&pg=rebuildalbum&id={$i['id']}' title='Rebuild all thumbnails and full size images for this album'><img src='{$this->ipsclass->adskin->img_url}/images/acp_resync.gif' border='0' alt='rebuild'></a></center>",
										 			  "<center>".$this->glib->byte_to_kb( $i['total_size'] )."</center>",
                                                      "<center>{$i['total_views']}</center>",
                                              )      );
        }

        /* Table End */
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( $links, 'right', 'catrow2' );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        /*
        * Create albums for members
        */
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;", "50%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;", "50%" );        
        
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Create Album" );
        
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 
                                      1 => array( "code", "albums" ),
                                      2 => array( "act", "gallery" ),
                                      3 => array( "section", "components" ),
                                      4 => array( "pg", "createalbum" ) ), "", "", 'createAlbum' );

        /* Jscript stuffs */
        $jscript = "<script language='javascript'>
                  function show_bad() {
                      toggleview( 'box-name' );
                  }
                  </script>";
        $this->ipsclass->html .= $jscript;
        
        /* Actual table 
        
        * Hold off on mass album making until 3.0 or so 
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                 "<b>Create album for...</b>", 
                                 $this->ipsclass->adskin->form_dropdown( 'create_for', array( 
                                                 array( "allmem", "All members" ),
                                                 array( "onlymem", "Single Member" ) ),
                                                 $this->ipsclass->input[ 'create_for' ],
                                                 "OnChange = 'javascript:show_mem();'" )
                                 ) );*/

        /* Ajax type ahead stuff 
        $dyn_texthtml = '<tr><td class="tablerow1"><div id=\'single_mem\' style=\'display:none;\'>';
        $dyn_texthtml .= '<i>For single member, start inputting username here</i></div></td>';
        $dyn_texthtml .= '<td class="tablerow2"><div id=\'single_mem2\' style=\'display:none;\'>' . $this->ipsclass->adskin->form_input( 'mem_name' ) . '</div></td></tr>';

        $this->ipsclass->html .= $dyn_texthtml;
        */
        
        /* Happy ajax */
        $ajax_txtbox .= "<input type='text' id='mem_name' name='mem_name' value='' autocomplete='off' style='width:210px;' class='textinput' />";
        $ajax_txtbox .= "<div class='input-warn-content' id='box-name' style='display:none;'><div id='msg-name'>User doesn't exist</div></div>";
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                     "<b>Create album for...</b><br />Start typing a username, and then select user from the drop down menu.",
                                      $ajax_txtbox ) );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                 "Album name", 
                                 $this->ipsclass->adskin->form_input( 'album_name' ) ) );
                                 
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                 "Album Description", 
                                 $this->ipsclass->adskin->form_input( 'album_desc' ) ) );
                                 
        /* Build category dropdown */
        $cats_drop = $cats->build_dropdown('album_cat', "dropdown" );
     
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                 "Album Category", 
                                  $cats_drop )
                                  );
                                 
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                 "Private or Public?", 
                                  $this->ipsclass->adskin->form_dropdown( 'album_status', array( 
                                                 array( "public", "Public Album" ),
                                                 array( "private", "Private Album" ) ) )
                                  ) );
                                 
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Create Album" );
        
        /* Last part */
        $this->ipsclass->html .= "<script type=\"text/javascript\">
                                    // Hooray!
                                   init_js( 'createAlbum', 'mem_name', 'get-member-names');
                                   setTimeout( 'main_loop()', 10 );
                                  </script>";
        
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        /********************************************
         * Search Table
         **/
        
        /* Table Headings */
        $this->ipsclass->adskin->td_header[] = array( "Attribute"    , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"        , "75%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Search" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'albums'    ),
                                                  2 => array( 'act'   , 'gallery'  ),
                                                  3 => array( 'search', 'yes' ),
                                                  4 => array( 'section', 'components' ),
                                     )  );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                    "<b>Search In</b>",
                                                    $this->ipsclass->adskin->form_dropdown( 'search_in', array( array( 'a.name'       , 'Album Name' ),
                                                                                              array( 'a.description', 'Album Description' ),
                                                                                              array( 'a.images'     , 'Total Images' ),
                                                                                              array( 'a.comments'   , 'Total Comments' ),
                                                   
                                                    ), $this->ipsclass->input['search_in'] )
                                         )       );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                    "<b>Search Comparison Type</b><br><i>If you choose a numeric field, then you may wish to search for entries that are greater or less than the search value.</i>",
                                                    $this->ipsclass->adskin->form_dropdown( 'search_type', array( array( '=' , 'Equal =' ),
                                                                                                array( '>' , 'Greater Than &gt;' ),
                                                                                                array( '<' , 'Less Than &lt;' ),
                                                   
                                                    ), $this->ipsclass->input['search_type'] )
                                         )       );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                    "<b>Search for</b>",
                                                    $this->ipsclass->adskin->form_input( 'search_for', $this->ipsclass->input['search_for'] ),
                                         )       );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                    "<b>Sort results</b>",
                                                    $this->ipsclass->adskin->form_dropdown( 'sort_key', array( array( 'a.name'       , 'Album Name' ),
                                                                                             array( 'a.description', 'Album Description' ),
                                                                                             array( 'a.images'     , 'Total Images' ),
                                                                                             array( 'a.comments'   , 'Total Comments' ),
                                                   
                                                    ), $this->ipsclass->input['sort_key'] ) . " in " .

                                                    $this->ipsclass->adskin->form_dropdown( 'sort_by', array( array(  'ASC' , 'Ascending (a-z)' ),
                                                                                             array( 'DESC', 'Descending (z-a)' ),
                                                   
                                                    ), $this->ipsclass->input['sort_by'] ) . ' order'
                                         )       );


        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Search" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        /* Output it all */
        $this->ipsclass->admin->output();
    }


}
?>
