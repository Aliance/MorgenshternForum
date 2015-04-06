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
* Admin/Overview
*
* Main page for Gallery ACP
*
* @package		Gallery
* @subpackage 	Admin
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.3
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

      /*
      * Vars
      */
      var $ipsclass;
      var $glib;
      var $forumfunc;

    /**
     * ad_plugin_gallery::ad_plugin_gallery()
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
	$this->ipsclass->gallery_root = './modules/gallery/';
        //---------------------------------------
        // Kill globals - globals bad, Homer good.
        //---------------------------------------

        $tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

        foreach ( $tmp_in as $k => $v )
        {
            unset($$k);
        }

		switch( $this->ipsclass->input['pg'] )
		{
			case 'settings':
				$this->gallery_settings();
			break;
			
			default:
				if( isset( $this->ipsclass->input['process'] ) )  {
					$this->process_unapproved();
				}
				$this->index_screen();
				break;
		}
    }
    
    /**
    * Gallery settings
    * @since 2.0.1
    **/
    function gallery_settings()  {
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$settings             =  new ad_settings();
		$settings->ipsclass   =& $this->ipsclass;
		 
		$settings->get_by_key        = 'invisiongallerysettings';
		$settings->return_after_save = $this->ipsclass->form_code.'&code=overview&pg=settings';
		
		$settings->setting_view();		
    }
    
    /**
     * ad_plugin_gallery::index_screen()
     * 
	 * Prints out a list of categories, with related options
	 * 
     * @return void
     **/
    function index_screen()
    {
        $this->ipsclass->admin->page_title   = "Gallery Overview";
        $this->ipsclass->admin->page_detail  = "";
        
        require( ROOT_PATH . '/modules/module_loader.php' );
        require( ROOT_PATH . '/modules/mod_gallery.php' );
                
        /* Check for unapproved images */
        $this->ipsclass->html .= $this->get_unapproved_images();
        
        $this->ipsclass->html .= "<table width='100%' cellspacing='2' cellpadding='0' border='0'>
                              <tr>
                                <td width='50%' align='left' valign='top'>";
        
        $this->ipsclass->html .= $this->get_searches();
		$this->ipsclass->html .= $this->get_common_stuff();

        $this->ipsclass->html .= "</td>
                                <td width='50%' align='left' valign='top'>";

        $this->ipsclass->html .= $this->get_stats( $version );

        $this->ipsclass->html .= $this->get_upgrade_history();
                                
        $this->ipsclass->html .= "     </td>
                               </tr>
                            </table>";                                                                                        

        $this->ipsclass->admin->output();
    }

    /* 
    * Process unapproved images 
    */
    function process_unapproved()  {
    	/*
    	* Grab mod library */
    	require( $this->ipsclass->gallery_root . "mod.php" );
    	$mod = new mod();
    	$mod->ipsclass = &$this->ipsclass;
    	$mod->glib = &$this->glib;
    	
       $what = explode( ":", $this->ipsclass->input['process'] );
       /*
       * Make sure the image exists first, fixes refresh bug */
       $this->ipsclass->DB->simple_construct( array( 'select' => 'id', 'from' => 'gallery_images', 'where' => "id = {$what[1]}" ) );
		$this->ipsclass->DB->simple_exec();
    	if( !$this->ipsclass->DB->get_num_rows() )  {
    		/*
    		* Image already approved/deleted, exit */
    		return;
    	}
       if( $what[0] == "a" )  {
       		$mod->approve_image( $what[1] );
       }
       		/* Approve 
       		$this->ipsclass->DB->do_update( 'gallery_images', array(
       						'approved' => '1' ), "id={$what[1]}" );*/
       
       if( $what[0] == "d" )  {
       		$mod->delete_image( $what[1], 0 );
       	/*
       		$this->ipsclass->DB->simple_delete( 'gallery_images', "id={$what[1]}" );
       		$this->ipsclass->DB->exec_query();*/
       }
    }
    /*
    * Show unapproved images
    */
    function get_unapproved_images() {
    	
       $this->ipsclass->adskin->td_header   = array();
       
       $where = "approved = 0";
       $this->ipsclass->DB->simple_construct( array( "select" => 'COUNT(*) AS total', 'from' => 'gallery_images', 'where' => "{$where}" ) );
       $this->ipsclass->DB->simple_exec();
       $total = $this->ipsclass->DB->fetch_row();
       $total = ( $total['total'] ) ? $total['total'] : 0;
       
       /* Doh */
       $images = $this->ipsclass->adskin->start_table( "Images awaiting approval" );
       if( $total > 0 )  {
       	$this->ipsclass->adskin->td_header[] = array( "Preview Image"  , "20%" );
       	$this->ipsclass->adskin->td_header[] = array( "Image Information"  , "30%" );
       	$this->ipsclass->adskin->td_header[] = array( "Username"  , "20%" );
       	$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "10%" );

       	/*
       	* Happy fun page spanning
       	*/
       	$st = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;

       	$links = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $total,
       	'PER_PAGE'    => 5,
       	'CUR_ST_VAL'  => $st,
       	'L_SINGLE'    => "Single Page",
       	'L_MULTI'     => "Pages: ",
       	'BASE_URL'    => $this->ipsclass->base_url.'&section=components&act=gallery&code=overview'
       	)
       	);
       	/*
       	* Grab 5 images */
       	$this->ipsclass->DB->build_query( array(
       	"select"  => 'img.*',
       	"from"    => array( 'gallery_images' => 'img' ),
       	"where"   => "img.approved = 0",
       	"order"   => "img.date DESC",
       	"limit"   => array( $st, 5 ),
       	"add_join" => array( array(
       	"select"   => 'mem.name',
       	"from"     => array( 'members' => 'mem' ),
       	"where"    => "mem.id = img.member_id",
       	"type"     => "left" )
       	)
       	)
       	);
       	$this->ipsclass->DB->exec_query();
       	$x = 0;
       	while( $unapproved = $this->ipsclass->DB->fetch_row() ) {
       		/* Build */
       		$img_info = '<b>' . $unapproved[ 'file_name' ] . "</b> ( " . $this->glib->byte_to_kb( $unapproved[ 'file_size' ] ) . " ) <br />";
       		$img_info .= "Uploaded on " . date( "M d, Y @ h:ia", $unapproved[ 'date' ] );

       		$approve = "<center><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=overview&process=a:{$unapproved[ 'id' ]}'><img src='style_images/{$this->ipsclass->skin['_imagedir']}/aff_tick.gif' border='0' title='Approve this image' /></a>&nbsp;&nbsp;&nbsp;";
       		$delete = "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=overview&process=d:{$unapproved[ 'id' ]}'><img src='style_images/{$this->ipsclass->skin['_imagedir']}/aff_cross.gif' border='0' title='Delete this image' /></a>&nbsp;&nbsp;</center>";
       		$options = $approve . $delete;

       		/* Custom TD creation for color swapping */
       		$images .= "<tr><td class='tdrow1' id='app_row_" . $x . "'><center>" . $this->glib->make_image_tag( $unapproved, $unapproved[ 'thumbnail' ], 0, true ) . "</center></td>";
       		$images .= "<td class='tdrow2' id='app_row_" . $x . "'>" . $img_info . "</td>";
       		$images .= "<td class='tdrow1' id='app_row_" . $x . "'>" . $unapproved[ 'name' ] . "</td>";
       		$images .= "<td class='tdrow2' id='app_row_" . $x . "'>" . $options . "</td></tr>";
       		$x++;
       	}

       	/* Fin */
       	$images .= "<tr><td class='tdrow1' colspan='4' align='right'>{$links}</td></tr>";
       }
       else {
       		/* No images */
       		$this->ipsclass->adskin->td_header[] = array( "Information"  , "100%" );
       		$images .= $this->ipsclass->adskin->add_td_row( array( "No images awaiting approval." ) );
       }
	   $images .= $this->ipsclass->adskin->end_table();
		
	   return( $images );
    }
    
	function get_common_stuff()
	{

        $common .= $this->ipsclass->adskin->start_table( "Common Tasks" );

        $common .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=cats&pg=catcreateform">Create a category</a></b>',
                                          )     );

        $common .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=media&pg=add">Add new media type</a></b>',
                                          )     );
										  
        $common .= $this->ipsclass->adskin->end_table();

		return $common;
	}
    
    function get_searches()
    {	
		return "
<div class='tableborder'>
  <div class='maintitle'>Quick Searches</div>

<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'><tr>
<form action='{$this->ipsclass->base_url}' method='post' name='theAdminForm' >	
<input type='hidden' name='section' value='components'>			 
<input type='hidden' name='code' value='tools'>
<input type='hidden' name='act' value='gallery'>
<input type='hidden' name='tool' value='domemsrch'>
</tr>
<tr>
<td class='tdrow1' width='40%' valign='middle'><b>Member Search</b></td>
<td class='tdrow2' width='40%' valign='middle'><input type='text' name='search_term' value='' size='30' class='textinput'></td>
<td class='tdrow1' width='20%' valign='middle'><input type='submit' value='Find Member' id='button' accesskey='s'></td>
</tr>
</form>

<form action='{$this->ipsclass->base_url}' method='post' name='theAdminForm' >		
<input type='hidden' name='section' value='components'>			 
<input type='hidden' name='code' value='tools'>
<input type='hidden' name='act' value='gallery'>
<input type='hidden' name='tool' value='dogroupsrch'>
<tr>
<td class='tdrow1' width='40%' valign='middle'><b>Group Search</b></td>
<td class='tdrow2' width='60%' valign='middle'><input type='text' name='search_term' value='' size='30' class='textinput'></td>
<td class='tdrow1' width='20%' valign='middle'><input type='submit' value='Find Group' id='button' accesskey='s'></td>
</tr>
</form>
<form action='{$this->ipsclass->base_url}' method='post' name='theAdminForm' >				 
<input type='hidden' name='section' value='components'>	
<input type='hidden' name='code' value='tools'>
<input type='hidden' name='act' value='gallery'>
<input type='hidden' name='tool' value='dofilesrch'>
<tr>
<td class='tdrow1' width='40%' valign='middle'><b>File Search</b></td>
<td class='tdrow2' width='60%' valign='middle'><input type='text' name='search_term' value='' size='30' class='textinput'></td>
<td class='tdrow1' width='20%' valign='middle'><input type='submit' value='Find File' id='button' accesskey='s'></td>
</tr>
</form>

</table>
</div><BR>";    	
    }
    
    function get_stats( $version )
    {	
    	// Get Total Images
    	$this->ipsclass->DB->simple_construct( array( 'select' => 'count(*) AS total', 'from' => 'gallery_images' ) );
    	$this->ipsclass->DB->simple_exec();
    	$t = $this->ipsclass->DB->fetch_row();
    	$total_images = ( $t['total'] ) ? $t['total'] : 0;
    	
    	// Get Total Diskspace
    	$this->ipsclass->DB->simple_construct( array( 'select' => 'SUM(file_size) AS total', 'from' => 'gallery_images' ) );
    	$this->ipsclass->DB->simple_exec();
    	$t = $this->ipsclass->DB->fetch_row();
    	$total_diskspace = ( $t['total'] ) ? $this->glib->byte_to_kb( $t['total'] ) : 0;
    	
    	// Get Total Comments
    	$this->ipsclass->DB->simple_construct( array( 'select' => 'count(*) AS total', 'from' => 'gallery_comments' ) );
    	$this->ipsclass->DB->simple_exec();
    	$t = $this->ipsclass->DB->fetch_row();
    	$total_comments = ( $t['total'] ) ? $t['total'] : 0;

		// Get Total Albums
    	$this->ipsclass->DB->simple_construct( array( 'select' => 'count(*) AS total', 'from' => 'gallery_albums' ) );
    	$this->ipsclass->DB->simple_exec();
    	$t = $this->ipsclass->DB->fetch_row();
    	$total_albums = ( $t['total'] ) ? $t['total'] : 0;		
    	
    	// Set widths
    	$this->ipsclass->adskin->td_header   = array();
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );    	
    	
    	// Build table
		$stats  = $this->ipsclass->adskin->start_table( "Gallery Quick Stats" );
		$stats .= $this->ipsclass->adskin->add_td_row( array( '<b>Invision Gallery Version</b>', "<b>$version</b>"	 ) );		
		$stats .= $this->ipsclass->adskin->add_td_row( array( 'Total Images'   , $total_images ) );
		$stats .= $this->ipsclass->adskin->add_td_row( array( 'Total Diskspace', $total_diskspace ) );
		$stats .= $this->ipsclass->adskin->add_td_row( array( 'Total Comments' , $total_comments ) );
		$stats .= $this->ipsclass->adskin->add_td_row( array( 'Total Albums'   , $total_albums ) );
		$stats .= $this->ipsclass->adskin->add_td_row( array( "<a href='{$this->ipsclass->adskin->base_url}&section=components&act=gallery&code=stats'>Extended Statistics</a>", '&nbsp;' ) );
		$stats .= $this->ipsclass->adskin->end_table();
		
		return $stats;
    }
	
	function get_upgrade_history()
	{	
		// Get the info
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_upgrade_history', 'order' => 'gallery_upgrade_id ASC' ) );
		$this->ipsclass->DB->simple_exec();

    	// Set widths
    	$this->ipsclass->adskin->td_header   = array();
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );   

		$upgrade = $this->ipsclass->adskin->start_table( "Gallery Upgrade History" );
		
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$upgrade .= $this->ipsclass->adskin->add_td_row( array( $i['gallery_version_human'] . " ( {$i['gallery_version_id']} ) ", $this->ipsclass->get_date( $i['gallery_upgrade_date'], 'LONG' ) ) );
		}
		
		$upgrade .= $this->ipsclass->adskin->end_table();

		return $upgrade;
	}

   /**
    * ad_plugin_gallery::get_sys_check()
    * 
	* Checks for common erros
	* 
    * @param integer $mid
    * @return void
    **/
    function get_sys_check( )
    {
		// Directory exists?
		if( ! is_dir( $this->ipsclass->vars['gallery_images_path'] ) )
		{
			$errors[] = array( 'Image directory dosn\'t exist', 
			                   "<a href='{$this->ipsclass->base_url}&section=components&act=op&code=setting_view&search=".urlencode('Directory to store images?')."'>Directory to store images?</a>",
			                   'Ensure that the path is correct' );
		}
		else if( ! is_writeable( $this->ipsclass->vars['gallery_images_path'] ) )
		{
			$errors[] = array( 'Image directory is not writeable',
			                   "<a href='{$this->ipsclass->base_url}&section=components&act=op&code=setting_view&search=".urlencode('Directory to store images?')."'>Directory to store images?</a>",
			                   'CHMOD the directory to 0777' );
		}
		
		if( empty( $this->ipsclass->vars['gallery_images_url'] ) )
		{
			$errors[] = array( 'No Image URL',
			                   "<a href='{$this->ipsclass->base_url}&section=components&act=op&code=setting_view&search=".urlencode('URL to stored images?')."'>URL to stored images?</a>",			
			                   'Enter the URL for your images directory' );
		}
		
		if( SAFE_MODE_ON && $this->ipsclass->vars['gallery_images_dir'] )
		{
			$errors[] = array( 'SAFE MODE',
			                   'Images per directory?',
			                   "<a href='{$this->ipsclass->base_url}&section=components&act=op&code=setting_view&search=".urlencode('Images per directory?')."'>Images per directory?</a>",			                   
			                   'You may need to set this to 0, due to SAFE MODE limitations' );
		
		}
		
		if( $this->ipsclass->vars['gallery_img_suite'] == 'im' )
		{
			if( ! file_exists( $this->ipsclass->vars['gallery_im_path'] ) )
			{
				$errors[] = array( 'Image Magick Error',
  			                        "<a href='{$this->ipsclass->base_url}&section=components&act=op&code=setting_view&search=".urlencode('Full path to Image Magick')."'>Full path to Image Magick</a>",			                 				
				                   'Ensure that the IM is installed and that the path is right' );				                   
			}
			
		}
		else
		{
			$extensions = get_loaded_extensions();
			
			foreach( $extensions as $ext )
			{
				if( $ext == 'gd' )
				{
					if( function_exists( "gd_info" ) )
					{
						$info = gd_info();
						$ver = $info['GD Version'];
					
						if( $this->ipsclass->vars['gallery_img_suite'] == 'gd' )
						{
							if( ! preg_match( "/2/", $ver ) )
							{
								$errors[] = array( 'GD2 Error',
			                   "<a href='{$this->ipsclass->base_url}&section=components&act=op&code=setting_view&search=".urlencode('Select an Image Suite to use')."'>Select an Image Suite to use</a>",
 			                   'Ensure that GD is installed and you have chosen the correct version,
			                    GD reports your version as:<i> '.$ver.'</i>' );
							}
						}
						else
						{
							if( preg_match( "/2/", $ver ) )
							{
								$errors[] = array( 'GD2 Detected',
								                   'Select an Image Suite to use',
								                   'While your gallery will work, it is recommend you switch to GD2 as it will produce better results,
								                    GD reports your version as:<i> '.$ver.'</i>' );
							}					
							else if( ! preg_match( "/1/", $ver ) )
							{
								$errors[] = array( 'GD1 Error',
								                   'Select an Image Suite to use',
								                   'Ensure that GD is installed and you have chosen the correct version,
								                    GD reports your version as:<i> '.$ver.'</i>' );
							}
						
						}
					}
				}
			}
		}
		
		if( ! empty( $this->ipsclass->vars['gallery_watermark_path'] ) )
		{		
			if( ! file_exists( $this->ipsclass->vars['gallery_watermark_path'] ) )
			{
				$errors[] = array( 'Incorrect Watermark Path',
				                   'Full path to the watermark image?',
			    	               'Make sure the watermark is uploaded, and that you have entered the correct path - not URL' );
			                   
			}
		}
	
		if( is_array( $errors ) )
		{
    	    // Table Headers
    	    $this->ipsclass->adskin->td_header[] = array( "Problem"         , "33%" );
    	    $this->ipsclass->adskin->td_header[] = array( "Affected Setting", "33%"  );
    	    $this->ipsclass->adskin->td_header[] = array( "Possible Fix(es)", "33%"  );

    	    // Start
    	    $html .= "<div style='color:red;border:1px solid red;background:#FFC0C3;padding:2px'>";
    	    $html .= "<span style='font-size:20px;font-weight:bold'>Warning: Possible errors found in gallery settings</span>";
    	    $html .= "<br /><br /><table border='0' cellspacing='2' cellpadding='4' width='100%'>";

			$html .= "<tr>
			            <td width='33%'><b>Type of Error</b></td>
			            <td width='33%'><b>Affected Setting</b></td>
			            <td width='33%'><b>Possible Solution</b></td>				            
					  </tr>
			";        
			foreach( $errors as $e )
			{
				$html .= "<tr>
				            <td width='33%'>{$e['0']}</td>
				            <td width='33%'>{$e['1']}</td>
				            <td width='33%'>{$e['2']}</td>				            
						  </tr>
				";
			}
	        $html .= $this->ipsclass->adskin->end_table()."</div>";
	        
	        return $html;			
		}
    }

}
?>
