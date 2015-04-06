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
* Main Gallery Component
*
* Base file for Gallery
*
* @package		Gallery
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since		1.0
*/

   /**
    * This is the current version of IG you are running, it is used
    * internally and should not be modified.
    *
    * @var  string    $version
    */
    $version = "2.0.5";
    define( 'GALLERY_VERSION', $version );


class module extends module_loader
{	
        var $ipsclass;	
        var $glib;
        var $class  = "";
	var $module = "";
	var $html   = "";	
	var $result = "";

   /**
    * If you are expereincing errors related to open_basedir restrictions,
    * then set this to be the path to your forums.
    *
    * @var  string    $root
    */
   var $root = "./modules/gallery/";

        function module()  {
          /* Empty for now*/
        }

        /* Called first from module_loader */
	function run_module()
	{
		//--------------------------------
		//  Is the board offline?
		//--------------------------------
	
		if ($this->ipsclass->vars['board_offline'] == 1)
		{
			if ($this->ipsclass->member['g_access_offline'] != 1)
			{
				$this->ipsclass->vars['no_reg'] == 1;
				$this->ipsclass->board_offline();
			}
		
		}
		
		/**************************************
		 * Make sure that the user is authorized to use the gallery
		 **/
		$this->ipsclass->DB->load_cache_file( ROOT_PATH . 'sources/sql/'.SQL_DRIVER.'_gallery_queries.php', 'gallery_sql_queries' );
        
        if( $this->ipsclass->member['id'] )
        {
            $this->ipsclass->DB->simple_construct( array( "select" => 'gallery_perms', 'from' => 'members', 'where' => "id={$this->ipsclass->member['id']}" ) );
            $this->ipsclass->DB->simple_exec();        
            $this->ipsclass->member = array_merge( $this->ipsclass->member, $this->ipsclass->DB->fetch_row() );

            $perms = explode( ':', $this->ipsclass->member['gallery_perms'] );

            if( ! $perms[2] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }
        }
        else if ( ! $this->ipsclass->vars['gallery_guest_access'] )
        {
            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        }
		
		// ------------------------------------
		// Load language and html
		// ------------------------------------		
		$this->ipsclass->load_language('lang_gallery');
        $this->ipsclass->gallery_root = $this->root;

        // ------------------------------------
        // Grab our library
        // ------------------------------------
        require( $this->ipsclass->gallery_root.'lib/gallery_library.php' );
        $this->glib = new gallery_lib();
        $this->glib->ipsclass =& $this->ipsclass;
        
		/**************************************
		 * Is the gallery offline? 
		 **/
		if( $this->ipsclass->vars['gallery_offline'] && ! $this->ipsclass->member['g_access_offline'] )
		{
			// Language setup
	    	$this->ipsclass->lang = $this->ipsclass->load_words($this->ipsclass->lang, "lang_error", $this->ipsclass->lang_id);
	    	$this->ipsclass->lang['offline_title'] = $this->ipsclass->lang['gallery_offline'];
			
			// Display
			if( !is_object( $this->ipsclass->compiled_templates['skin_global'] ) ) {
				$this->ipsclass->load_template( 'skin_global' );
			}
    		$html = $this->ipsclass->compiled_templates['skin_global']->board_offline( $this->ipsclass->vars['gallery_offline_text'] );
    	    
    	    // Output	
    		$this->ipsclass->print->add_output( $html );
    		
	    	$this->ipsclass->print->do_output( array(
    									OVERRIDE   => 1,
    									TITLE      => $this->ipsclass->lang['offline_title'],
    								 )
    						  );
		}        

		// ------------------------------------
		// Set up structure
		// -----------------------------------
        $actions = array(
                          'idx'         => array( 'lib' => 'category' , 'param' => 'list'      ),
                          'sc'          => array( 'lib' => 'category' , 'param' => 'show'      ),
                          'slideshow'   => array( 'lib' => 'slideshow', 'param' => 'slides'    ),
                          'viewimage'   => array( 'lib' => 'img_ctrl' , 'param' => 'view'      ),
                          'si'          => array( 'lib' => 'img_view' , 'param' => 'show'      ),

                          'dopost'      => array( 'lib' => 'post'     , 'param' => 'proc'      ),
                          'editimg'     => array( 'lib' => 'post'     , 'param' => 'editimg'   ),
                          'delimg'      => array( 'lib' => 'mod'      , 'param' => 'delimg'    ),
                          'moveimg'     => array( 'lib' => 'mod'      , 'param' => 'moveimg'   ),
                          'postcomment' => array( 'lib' => 'post'     , 'param' => 'reply'     ),
                          'editcomment' => array( 'lib' => 'post'     , 'param' => 'editreply' ),
                          'delcomment'  => array( 'lib' => 'mod'      , 'param' => 'delreply'  ),  
                          'post'        => array( 'lib' => 'post'     , 'param' => 'form' ),
                          'rate'        => array( 'lib' => 'rate'     , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : 'dorate' ),                          
                          'ecard'       => array( 'lib' => 'ecard'    , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : 'form' ),
                          'favs'        => array( 'lib' => 'fav'      , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : 'index' ),
                          'albums'      => array( 'lib' => 'album'    , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : 'index' ),
                          'user'        => array( 'lib' => 'user'     , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : 'index' ),
                          'stats'       => array( 'lib' => 'stats'    , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : 'toprated' ),
                          'mod'         => array( 'lib' => 'mod'      , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : '' ),                          
						  'gallerymmod' => array( 'lib' => 'mod'      , 'param' => 'multi' ),
						  'search'      => array( 'lib' => 'search'   , 'param' => ( $this->ipsclass->input['op'] ) ? $this->ipsclass->input['op'] : '' ),
                        );
        
        $keys = array_keys( $actions );
        
		// ------------------------------------
		// Check and run the proper command
		// -----------------------------------
        $cmd = $this->ipsclass->input['cmd'];
        $cmd = ( empty( $cmd ) )             ? 'idx' : $cmd;
        $cmd = ( ! in_array( $cmd, $keys ) ) ? 'idx' : $cmd;

        
        /*
        * Print out the Gallery's version number and stuff in the footer */
        if( $cmd != 'albums' && $cmd != 'favs' )  
        {
        	$this->ipsclass->skin[ '_wrapper' ] = str_replace( '<% COPYRIGHT %>', $this->glib->get_copyright(), $this->ipsclass->skin[ '_wrapper' ] );
        }
        
        require( $this->ipsclass->gallery_root . "{$actions[$cmd]['lib']}.php" );
        $obj = new $actions[$cmd]['lib'];
        $obj->ipsclass =& $this->ipsclass;
        $obj->glib =& $this->glib;
        
        $obj->start( $actions[$cmd]['param'] );

	}
}
?>
