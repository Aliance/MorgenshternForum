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
* Admin/Tools
*
* Gallery admin tools ( Orphan scan, etc )
*
* @package		Gallery
* @subpackage 	Admin
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
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
    var $forumfunc;
    var $modules;

    /*
    * Access to gallery lib */
    var $glib;

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

        switch( $this->ipsclass->input['tool'] )
        {
            case 'rethumbs':
                $this->rethumbs();
            break;

            case 'resize':
                $this->resize();
            break;

            case 'domemsrch':
                if( ! $this->ipsclass->input['viewuser'] )
                {
                    $this->do_member_search();
                }
                else
                {
                    $this->gen_mem_report( $this->ipsclass->input['viewuser'] );
                }
            break;

            case 'memsrch':
                $this->member_search();
            break;

            case 'domemact':
                $this->do_mem_act();
            break;

            case 'groupsrch':
                $this->group_search();
            break;

            case 'dogroupsrch':
                if( ! $this->ipsclass->input['viewgroup'] )
                {
                    $this->do_group_search();
                }
                else
                {
                    $this->gen_group_report( $this->ipsclass->input['viewgroup'] );
                }
            break;

            case 'filesrch':
                $this->file_search();
            break;

            case 'dofilesrch':
                if( ! $this->ipsclass->input['viewfile'] )
                {
                    $this->do_file_search();
                }
                else
                {
                    $this->gen_file_report( $this->ipsclass->input['viewfile'] );
                }
            break;

            case 'filesrch':
                $this->file_search();
            break;

            case 'dofileact':
                $this->do_file_act();
            break;

            case 'ecardlog':
                $this->ecard_logs();
            break;

            case 'showratings':
                $this->rating_log( $this->ipsclass->input['mid'] );
            break;

            case 'bulkadd':
                $this->bulk_add_form();
            break;

            case 'orphans':
                $this->find_orphans();
            break;

            case 'zipfile':
                $this->zipfile();
            break;
            
            case 'protect':
            	$this->protect();
           	break;
           	
           	case 'rebuildmaster':
           		$this->rebuildmaster();
            break;
            
            case 'importview':
                $this->zip_import_view();
            break;

        default:
            $this->tools();
        break;

        }
    }

   /******************************************************************
    *
    * Tool Menu Stuff
    *
    **/

    

   /**
    * ad_plugin_gallery::tools()
    * 
	* Tool Selection Screen
	* 
    * @return void
    **/
    function tools()
    {
        // Page Information
        $this->ipsclass->admin->page_title   = "Gallery Tools";
        $this->ipsclass->admin->page_detail  = "Here you will find various tools for maintaing your gallery";
        
        
        $this->ipsclass->html .= "<table border='0' width='100%'><tr><td width='50%' valign='top'>";

        // Table Headers
        $this->ipsclass->adskin->td_header[] = array( "{none}", "100%" );

        // Start o' the page
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Image Related Tools" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=rethumbs">Rebuild Thumbnails</a></b><BR>
                                                   This tool will recreate all the thumbnails in your gallery.  This tool if useful if you have changed your standard thumbnail size and now wish for the old thumbnails to use that size as well',
                                          )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=resize">
                                                  Resize Images</a></b><BR>This tool if useful if you have changed your maximum allowed image size and not want your old images to be the same size as well.',
                                          )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=orphans">Orphan Scanner</a></b><BR>
                                                  This tool will scan your database and file system for broken or unlinked images',
                                          )     );
                                          
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
												  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=protect">Hot Link Protection</a></b><BR>
												  This tool will create a .htaccess file in your images directory that will prevent your images from being hotlinked.  Before running this tool you must first add some sites to the allowed list in the gallery settings',
										  )     );
										  
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        // Table Headers
        $this->ipsclass->adskin->td_header[] = array( "{none}", "100%" );
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Reports and Logs" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=memsrch">Member Report</a></b><BR>
                                                This tool will allow you to look up a member and receive detailed information on their gallery usage',
                                        )     );
 
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=groupsrch">Group Report</a></b><BR>
                                                This tool will allow you to look up a group and receive detailed information on their gallery usage',
                                        )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=filesrch">File Report</a></b><BR>
                                                This tool will allow you to look up a file and receive detailed information on it',
                                        )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=ecardlog">E-Card Logs</a></b><BR>
                                                This tool will allow you to keep track of the e-cards being sent by this system',
                                        )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=stats">Statistics</a></b><BR>
                                                This tool will allow you to view statistics about your gallery',
                                        )     );                                        
                                        
                                        
                                        

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        $this->ipsclass->html .= "</td><td width='50%' valign='top'>";        

        // Table Headers
        $this->ipsclass->adskin->td_header[] = array( "{none}", "100%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Bulk Import" );
										  
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=bulkadd">... from Directory</a></b><BR>
                                                  This tool will allow you to add images from a specified directory',
                                          )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=zipfile">... from Zip File</a></b><BR>
                                                  This tool will allow you to add images from a zip file',
                                          )     );


        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();


        $this->ipsclass->adskin->td_header[] = array( "{none}", "100%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Skin Tools" );
										  
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b><a href="'.$this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=rebuildmaster">Rebuild Gallery Master Templates</a></b><BR>
                                                  This tool will rebuild only the gallery master templates and not the complete IPB template set',
                                          )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();


        $this->ipsclass->html .= "</td></tr></table>";


        $this->ipsclass->admin->output();
    }
    
	function rebuildmaster()
	{		
		$master  = array();
		$inserts = 0;
		$updates = 0;
		
		//-----------------------------------------
		// Template here?
		//-----------------------------------------
		
		if ( ! file_exists( ROOT_PATH.'ipb_templates.xml' ) )
		{
			$this->ipsclass->admin->error( "ipb_templates.xml cannot be found in the forums root directory. Please check, upload or try again" );
		}
		
		//-----------------------------------------
		// First, get all the default bits
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => 'suid,group_name,func_name', 'from' => 'skin_templates', 'where' => "set_id=1 AND group_name LIKE 'skin_gallery_%'" ) );
		$this->ipsclass->DB->simple_exec();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$master[ strtolower( $r['group_name'] ) ][ strtolower( $r['func_name'] ) ] = $r['suid'];
		}
		
		//-----------------------------------------
		// Get XML
		//-----------------------------------------
		
		require_once( KERNEL_PATH.'class_xml.php' );
		
		$xml = new class_xml();
		
		//-----------------------------------------
		// Get XML file (TEMPLATES)
		//-----------------------------------------
		
                if( ! file_exists( ROOT_PATH.'gallery_templates.xml' ) )  {
                   $this->ipsclass->admin->error( "gallery_templates.xml cannot be found in the forums root directory.  Please check, upload, or try again.");
                }
		$xmlfile = ROOT_PATH.'gallery_templates.xml';
		
		$setting_content = implode( "", file($xmlfile) );
		
		//-----------------------------------------
		// Unpack the datafile (TEMPLATES)
		//-----------------------------------------
		
		$xml->xml_parse_document( $setting_content );
		
		//-----------------------------------------
		// (TEMPLATES)
		//-----------------------------------------
		
		if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			$this->ipsclass->admin->error( "Error with ipb_templates.xml - could not process XML properly" );
		}
	
		foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $id => $entry )
		{
			$newrow = array();
			
			$newrow['group_name']      = $entry[ 'group_name' ]['VALUE'];
			$newrow['section_content'] = $entry[ 'section_content' ]['VALUE'];
			$newrow['func_name']       = $entry[ 'func_name' ]['VALUE'];
			$newrow['func_data']       = $entry[ 'func_data' ]['VALUE'];
			$newrow['set_id']          = 1;
			$newrow['updated']         = time();
			
			if ( $master[ strtolower( $newrow['group_name'] ) ][ strtolower( $newrow['func_name'] ) ] )
			{
				//-----------------------------------------
				// Update
				//-----------------------------------------
				
				$updates++;
				
				$this->ipsclass->DB->do_update( 'skin_templates', $newrow, 'suid='.$master[ strtolower( $newrow['group_name'] ) ][ strtolower( $newrow['func_name'] ) ] );
			}
			else
			{
				//-----------------------------------------
				// Insert
				//-----------------------------------------
				
				$inserts++;
				
				$this->ipsclass->DB->do_insert( 'skin_templates', $newrow );
			}
		}
		
                 /* All finished */
		$this->ipsclass->admin->done_screen( "Master gallery template set rebuilt!<br />$updates updated template bits, $inserts new template bits", "Invision Gallery Manager", "section=components&act=gallery" );
	}    
    
   /**
    * ad_plugin_gallery::protect()
    * 
	* Protects images with a .htaccess file
	* 
    * @return void
    **/
    function protect()
    {   
        if( $this->ipsclass->input['op'] == 'add' )
        {
            $this->_do_protect();
        }
        else if( $this->ipsclass->input['op'] == 'rem' )
        {
        	$this->_do_rem_protect();
        }

        $this->ipsclass->admin->page_title   = "Protect Images";
        $this->ipsclass->admin->page_detail  = "This tool will create a .htaccess file that will protect your images from hotlinking.";

        if( empty( $this->ipsclass->vars['gallery_allowed_domains'] ) )
        {
        	$this->ipsclass->admin->error( "You must first add domains to the allowed domains list via the gallery settings" );
        }
        
        $this->ipsclass->adskin->td_header[] = array( "Domain", "100%" );

        $domains_tbl .= $this->ipsclass->adskin->start_table( "Allowed Domains" );
        
        $domains = explode( "\n", $this->ipsclass->vars['gallery_allowed_domains'] );
        
        foreach( $domains as $domain )
        {
        	$domains_tbl .= $this->ipsclass->adskin->add_td_row( array( $domain ) );
        }
        
        $domains_tbl .= $this->ipsclass->adskin->end_table();
        
        if( file_exists( $this->ipsclass->vars['gallery_images_path'].'/.htaccess' ) )
        {
        	$color = "#CCFFCC";
        	$msg = "Your images are already being protected, would you like to 
        	        remove the protection?<br /><br />
        	        <b><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=protect&op=rem'>Yes, remove the protection</a>";
        }
        else
        {
        	$color = "#FFCCCC";
        	$msg = "Your images are not being protected, would you like to 
        	        activate the protection?<br /><br />
        	        <b><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=protect&op=add'>Yes, activate the protection</a>";
        
        }
        
        $this->ipsclass->html .="
        <table width='100%' border='0' cellspacing='1' cellpadding='4'>
        <tr>
        <td width='50%' valign='top'><div style='border:1px dotted #000;padding:6px;background-color:{$color}'>{$msg}</div></td>
        <td width='50%'>{$domains_tbl}</td>
        </tr>
        </table>";


        $this->ipsclass->admin->output();     
    }
    
   /**
    * ad_plugin_gallery::_do_protect()
	* 
    * @return void
    **/
    function _do_protect()
    {    
        $domains = explode( "\n", $this->ipsclass->vars['gallery_allowed_domains'] );
		
		$to_write  = "RewriteEngine on\n";
        $to_write .= "RewriteCond %{HTTP_REFERER} !^$\n";
        
        foreach( $domains as $domain )
        {
        	$domain = trim( $domain );
        	$to_write .= "RewriteCond %{HTTP_REFERER} !^{$domain}/.*$ [NC]\n";
        }
        
        $replace = ( $this->ipsclass->vars['gallery_antileech_image'] ) ? $this->ipsclass->vars['gallery_antileech_image'] . ' [L]' : '- [F]';
        
        $to_write .= "RewriteRule \.(gif|jpg|jpeg|png|GIF|JPG|JPEG|PNG)$ {$replace}";
        $fh = fopen( $this->ipsclass->vars['gallery_images_path'].'/.htaccess', "w" );
        fwrite( $fh, $to_write );
        fclose( $fh );        
    }
    
   /**
    * ad_plugin_gallery::_do_rem_protect()
	* 
    * @return void
    **/
    function _do_rem_protect()
    {         
        unlink( $this->ipsclass->vars['gallery_images_path'].'/.htaccess' );
       
    }    

   /***************************************************8
    *
    * Zip File Import Tool
    *
    **/

   /*
   * ad_plugin_gallery::zip_import_view()
   *
   * Extracts a single image and let's the admin preview it, then cleans up
   *
   * @return void
   */
   function zip_import_view()  {
	   /* Include the PCL lib and extract the file from it */
	   require( ROOT_PATH . 'modules/gallery/lib/pclzip.lib.php' );
	   
	   /* Setup UI */
	   $this->ipsclass->admin->page_title   = "Previewing Image.. ";
	   $this->ipsclass->admin->page_detail  = "";
	   
	   $this->ipsclass->adskin->td_header[] = array( "Image"  , "100%" );
	   $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Previewing Image.." );
	   
	   /* 
	   * Extract the file and display it
	   */
	   $dir = $this->ipsclass->vars['gallery_images_path'].'/';
	   $files_dir = $dir . 'temp/';
	   $zip = $this->ipsclass->input['zip'];

		if ( file_exists( $files_dir ) )
		{
			# Remove tmp dir and contents recursively
			# to allow a zip with folders to be removed
			$this->ipsclass->admin->rm_dir( $files_dir );
		}
        
        $zip = new PclZip( $dir.$zip );
        $zip->extractByIndex( $this->ipsclass->input[ 'view_image' ], PCLZIP_OPT_PATH, $files_dir );

        /************************************************************
         * Take a look in the directory
         ***********************************************************/
        
        $zipfiles = $this->_recurse_folder_lookup( $files_dir );
        
        foreach( $zipfiles as $id => $files )
        {
        	# Strip off file_dir as we know it
        	$files = str_replace( $files_dir, "", $files );
        	break;
        }
        
		/* Build link to image */
		$image = "<img src='" . $files_dir . $files . "' border='0' title='Click to close window.' />";
		$image_link = "<a href='' OnClick='javascript:window.close()'>" . $image . "</a>";
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $image_link ) );
		
		
		/* Display the popup */
		$this->ipsclass->admin->print_popup();
   }
   
	/**
	* Recursively look up images in folders
	*
	* @author	Matt
	* @since	2.0.3
	*/
	function _recurse_folder_lookup( $files_dir, $files=array() )
	{
		$files_dir = preg_replace( "#/$#", "", $files_dir );
		
		$dh = opendir( $files_dir );
        
        while( ( $filename = readdir( $dh ) ) )
        {
            if ( ( $filename != "." ) && ( $filename != ".." ) )
            {
            	@chmod( $files_dir.'/'.$filename, 0777 );
            	
            	if ( is_dir( $files_dir.'/'.$filename ) )
            	{
            		$files = array_merge( $files,  $this->_recurse_folder_lookup( $files_dir.'/'.$filename, $files ) );
            	}
            	else
            	{
					$temp = explode( ".", $filename );
					$type = strtolower( array_pop( $temp ) );
  
					$img_file = 0;
					
					if( $type == 'jpg' || $type == 'jpeg' )
					{
						$img_file = 1;
					}
					else if( $type == 'gif' )
					{
						$img_file = 1;
					}
					else if( $type == 'png' )
					{
					  $img_file = 1;
					}
			  
					if( $img_file )
					{
						$files[] = $files_dir.'/'.$filename;
					}
                }
            }
        }
        
        closedir( $dh );
        
        return $files;
	}
   
   /**
    * ad_plugin_gallery::zipfile()
    * 
	* Lists zip files that can be imported
	* 
    * @return void
    **/
    function zipfile()
    {
        $dir = $this->ipsclass->vars['gallery_images_path'].'/';

        switch( $this->ipsclass->input['pg'] )
        {
            case 'del':
                @unlink( $dir.$this->ipsclass->input['zip'] );
                $this->ipsclass->main_msg = "Zip File Deleted";
            break;

            case 'upload':
                $this->zip_upload();
            break;

            case 'list_all':
                $this->zip_list();
            break;

            case 'import_by_idx':
                $this->_do_zip_index_add();
            break;
        }
        
        $this->ipsclass->admin->page_title   = "ZIP Import";
        $this->ipsclass->admin->page_detail  = "This tool allows you to import images from a zip file";

        // Get the library
        require( ROOT_PATH . 'modules/gallery/lib/pclzip.lib.php' );
        
        // Table Top
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"        , "5%"  );
        $this->ipsclass->adskin->td_header[] = array( "Archive Name"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "File Count"    , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Archive Size"  , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Options"       , "35%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Select a zip archive to import files from '{$this->ipsclass->vars['gallery_images_path']}'" );

        $cat   = ( $this->ipsclass->input['cat'] )   ? "&cat={$this->ipsclass->input['cat']}"     : "";
        $album = ( $this->ipsclass->input['album'] ) ? "&album={$this->ipsclass->input['album']}" : "";

        // Find zip files
        if( is_dir( $dir ) ) 
        {
            if( $dh = opendir( $dir ) )
            {
                while( ( $file = readdir( $dh ) ) )
                {
                    if( strtolower( array_pop( explode( ".", $file ) ) ) == 'zip' )
                    {
                        $zip = new PclZip( $dir.$file );
                        $info = $zip->properties();
                        
                        $comment = "";
                        if( $info['comment'] )
                        {
                            $comment = "<br /><i>Comment: {$info['comment']}</i>";
                        }

                        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                "<center><img src='style_images/{$this->ipsclass->skin['_imagedir']}/folder_mime_types/zip.gif'></center>",
                                                $file.$comment,
                                                '<center>'.$info['nb'].'</center>',
                                                $this->glib->byte_to_kb( filesize( $dir.$file ) ),
                                                "<center><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=zipfile&pg=list_all&zip={$file}{$cat}{$album}' class='fauxbutton'>Import Files</a><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=zipfile&pg=del&zip={$file}' class='fauxbutton'><font color='red'>Delete</font></a></center>",
                                              )      );                        
                    }                               
                }
            }
    
           closedir($dh);
        }
        else
        {
            $this->ipsclass->admin->error( "Directory not present" );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		if ( SAFE_MODE_ON )
		{
			$this->ipsclass->admin->page_detail .= "<b>SAFE MODE ON:</b> The upload functions will not operate as you are running PHP in safe mode.";
			$this->ipsclass->html .= "</form>";
		}
		else
		{
            $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 
                                                               1 => array( 'code'  , 'tools' ),
			                                       2 => array( 'act'   , 'gallery'      ),
                                                               3 => array( 'tool'  , 'zipfile' ),
							       4 => array( 'MAX_FILE_SIZE', '10000000000' ),
                                                               5 => array( 'section', 'components' ),
													  ) , "uploadform", " enctype='multipart/form-data'"     );

			$this->ipsclass->html .= "<div class='tableborder'>
								 <div class='maintitle'>Upload Zip File</div>
								 <table width='100%' border='0' cellpadding='4' cellspacing='0'>
								 <tr>
								  <td width='100%' class='tdrow1' align='left'><input type='file' value='{$_POST['zipup']}' class='realbutton' name='zipup' size='30' /></td>
								 </tr>
								 </table><input type='hidden' name='pg' value='upload'>
								 <div class='pformstrip' align=''><input type='submit' value='Upload Zip File' class='realdarkbutton' /></form></div>
								</div>";
		}

        $this->ipsclass->admin->output();
    }

   /**
    * ad_plugin_gallery::zip_list()
    * 
	* Lists files in a zip that can be imported
	* 
    * @return void
    **/
    function zip_list()
    {
        $dir = $this->ipsclass->vars['gallery_images_path'].'/';
        $zip = $this->ipsclass->input['zip'];

        $this->ipsclass->admin->page_title   = "Listing files in '{$zip}'";
        $this->ipsclass->admin->page_detail  = "The following files were found";

        // Get the library
        require( ROOT_PATH . 'modules/gallery/lib/pclzip.lib.php' );
        
        // Table Top
        $chkall = "<a href='#' onclick=\"toggleselectall(); return false;\" title='Check/Uncheck all'><img src='{$this->ipsclass->adskin->img_url}/images/skineditor_tick.gif' border='0' alt='Check/Uncheck all' /></a>";
        $this->ipsclass->adskin->td_header[] = array( $chkall         , "15%"  );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"        , "5%"  );
        $this->ipsclass->adskin->td_header[] = array( "File Name"     , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "File Size"     , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "In Gallery?"   , "10%" );

         /* Include for ajax type ahead */
        $this->ipsclass->html .= "<script type=\"text/javascript\" src='jscripts/ipb_xhr_findnames.js'></script>";
        $this->ipsclass->html .= "<div id='ipb-get-members' style='border:1px solid #000; background:#FFF; padding:2px;position:absolute;width:210px;display:none;z-index:1'></div>";
		$this->ipsclass->html .= "<script language='javascript'>						
							var toggleon  = 0;

							function toggleselectall()
							{
								if ( toggleon )
								{
									toggleon = 0;
									dotoggleselectall(0);
								}
								else
								{
									toggleon = 1;
									dotoggleselectall(1);
								}
							}
							
							function dotoggleselectall(selectall)
							{	
								var fmobj = document.importZip;
								for (var i=0;i<fmobj.elements.length;i++)
								{
									var e = fmobj.elements[i];
									
									if (e.type=='checkbox')
									{
										if ( selectall ) {
										   e.checked = true;
										} else {
										   e.checked = false;
										}
									}
								}
							}
							</script>";


        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 
                                                               1 => array( 'code'  , 'tools' ),
							       							   2 => array( 'act'   , 'gallery'      ),
                                                               3 => array( 'tool'  , 'zipfile' ),
                                                               4 => array( 'zip'   , $zip ),
                                                               5 => array( 'pg'    , 'import_by_idx' ),
                                                               6 => array( 'section', 'components' ) ), "importZip", "", 'importZip' );

        /*
         * Beginning of file listing
         */

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Files Found" );

        $zip = new PclZip( $dir.$zip );

        $contents = $zip->listContent();

        foreach( $contents as $file )
        {
            // Is this a valid type?
            $type = strtolower( array_pop( explode( ".", $file['filename'] ) ) );

            $img_file = 0;
            if( $type == 'jpg' || $type == 'jpeg' )
            {
                $img_file = 1;
            }
            else if( $type == 'gif' )
            {
                $img_file = 1;
            }
            else if( $type == 'png' )
            {
               $img_file = 1;
            }
           
            if( ! $img_file )
            {
                continue;
            }

            $file_name = preg_replace( "/.gif/i", ".jpg", $file['filename'] );
            /*
            * Setup for viewing file inside zip before importing
            * ( Suggested by Keith Kacin )
            */
            $pop_url = "{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=importview&zip={$this->ipsclass->input['zip']}&view_image={$file[ 'index' ]}";
            $display_name = '<a href="#" OnClick="javascript:PopUp( \'' . $pop_url . '\', \'viewimport\', \'600\', \'400\' )" title="Preview Image before Import">' . $file[ 'filename' ] . '</a>';
            $this->ipsclass->DB->simple_construct( array( 'select' => 'id, file_size', 'from' => 'gallery_images', 'where' => "file_name='".$this->ipsclass->parse_clean_value( $file_name )."'" ) );
            $this->ipsclass->DB->simple_exec();

            if( $this->ipsclass->DB->get_num_rows() )
            {
                $i = $this->ipsclass->DB->fetch_row();

                $txt = ( $i['file_size'] == $file['size'] ) ? 'Yes' : 'Maybe';

                $in_gallery = "<a href='index.php?act=module&module=gallery&cmd=si&img={$i['id']}' target='_blank'>{$txt}</a>";
                $chk = 0;
            }
            else
            {
                $in_gallery = "No";
                $chk = 1;
            }
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                   '<center>'.$this->ipsclass->adskin->form_checkbox( 'extract[]', $chk, $file['index'] ).'</center>',
                                   "<center><img src='style_images/{$this->ipsclass->skin['_imagedir']}/folder_mime_types/gif.gif'></center>",
                                   $display_name,
                                   $this->glib->byte_to_kb( $file['size'] ),
                                   '<center>'.$in_gallery.'</center>',
                                  )      );                      
            
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "Checked images will be imported", 'left', 'catrow2' );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        /*
         * End of file listing
         */

        /*
         * Import Options Table
         */
        
        // Category Drop Down Information
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->glib;

        $this->category->read_data( false, 'Choose a category' );
        $this->category->current = $this->ipsclass->input['cat'];
        $cats = $this->category->build_dropdown( 'cat' );
        $cats = str_replace( "forminput", "dropdown", $cats );

        // Table Top
        $this->ipsclass->adskin->td_header[] = array( "Setting"  , "50%"  );
        $this->ipsclass->adskin->td_header[] = array( "Value"    , "50%"  );
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Import Options" );
       
        // Category Selector
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Which category?</b><br><small>All the images will be imported into the selected category</small>',
                                                  $cats,
                                          )      );

        // Album Selector
        $albums[] = array( 0, 'Choose an album' );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, name', 'from' => 'gallery_albums', 'order' => 'name ASC' ) );
        $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $albums[] = array( $i['id'], $i['name'] );
        }
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<font color='green'><b>-- OR --</font> Which Album?</b><br><small>All the images will be imported into the selected album</small>", $this->ipsclass->adskin->form_dropdown( 'album', $albums, $this->ipsclass->input['album'] ) ) );

        /*
        * ajax type ahead */
        $ajax_txt_box .= "<input type='text' id='mem_name' name='mem_name' value='' autocomplete='off' style='width:210px;' class='textinput' />";
        $ajax_txt_box .= "<div class='input-warn-content' id='box-name' style='display:none;'><div id='msg-name'>User doesn't exist</div></div>";
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>File Owner</B><br><small>All the images will be assigned to the specified user ( Begin typing in a username and then select the user from the drop down )</small>", $ajax_txt_box ) );
        
        /*
        * Add credit/copyright information to all imported images */
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
        	   "<b>Credit Information</b><br /><small>Tagline that will be added to image's information</small>",
        $this->ipsclass->adskin->form_textarea( "credit_info", "", 20, 2 ) ) );

       /*
       * Are we allowing copyright? Also, show default if set */
       if( $this->ipsclass->vars[ 'gallery_allow_usercopyright' ] != "disabled" ) {
        	$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
        	   "<b>Copyright Information</b><br /><small>Use &amp;copy; for the &copy; character ( ie &copy 2005 Site Owner )</small>",
        	$this->ipsclass->adskin->form_input( "copyright", "{$this->ipsclass->vars[ 'gallery_copyright_default']}" ) ) );
        }
        
        // End of table and form
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Import Images" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		
		// INIT find_names JS
		
		$this->ipsclass->html .= "<script type=\"text/javascript\">
									  // INIT find names
									  init_js( 'importZip', 'mem_name');
									  // Run main loop
									  var tmp = setTimeout( 'main_loop()', 10 );
								  </script>";
		
		
        $this->ipsclass->admin->output();
    }

    function _do_zip_index_add()
    {
        /************************************************************
         * Let's check the input first
         ***********************************************************/
        if( ! ( $this->ipsclass->input['cat'] || $this->ipsclass->input['album'] ) )
        {
            $this->ipsclass->admin->error( "You did not specify a category or album" );
        }

        if( $this->ipsclass->input['cat'] && $this->ipsclass->input['album'] )
        {
            $this->ipsclass->admin->error( "You can not choose both an album and a category" );
        }

        if( empty( $this->ipsclass->input['extract'] ) )
        {
            $this->ipsclass->admin->error( "You did not select any images" );
        }
        
        if( empty( $this->ipsclass->input['mem_name'] ) )
        {
        	$this->ipsclass->admin->error( "You did not select an owner." );
        }

        /************************************************************
         * Create a temp directory with the images to load
         ***********************************************************/

        // Get the library
        require( ROOT_PATH . 'modules/gallery/lib/pclzip.lib.php' );      
		
        $dir       = $this->ipsclass->vars['gallery_images_path'].'/';
        $files_dir = $dir . 'temp/';
        $zip       = $this->ipsclass->input['zip'];
		
		if ( file_exists( $files_dir ) )
		{
			# Remove tmp dir and contents recursively
			# to allow a zip with folders to be removed
			$this->ipsclass->admin->rm_dir( $files_dir );
		}
		
        @mkdir( $files_dir );
        @chmod( $files_dir, 0777 );
        
        $zip = new PclZip( $dir.$zip );

        foreach( $this->ipsclass->input['extract'] as $idx )
        {
            $zip->extractByIndex( $idx, PCLZIP_OPT_PATH, $files_dir );
        }

        /************************************************************
         * Take a look in the directory
         ***********************************************************/
         
        $zipfiles = $this->_recurse_folder_lookup( $files_dir );
        
        foreach( $zipfiles as $id => $name )
        {
        	# Strip off file_dir as we know it
        	$files[] = str_replace( $files_dir, "", $name );
        }
		
        /************************************************************
         * Get the category information
         ***********************************************************/
        if( $this->ipsclass->input['cat'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();
            $category = $this->ipsclass->DB->fetch_row();
        }
        else
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'member_id', 'from' => 'gallery_albums', 'where' => "id={$this->ipsclass->input['album']}" ) );
            $this->ipsclass->DB->simple_exec();

            $mem = $this->ipsclass->DB->fetch_row();
            $category['thumbnail']        = ( $this->ipsclass->vars['gallery_create_thumbs'] )  ? 1 : 0;
            $category['watermark_images'] = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? 1 : 0;
        }
        
        /************************************************************
         * Not set a member, but have an entered mem_name?
         ***********************************************************/
		
		if ( ! is_array( $mem ) && $this->ipsclass->input['mem_name'] )
		{
			$mem = $this->ipsclass->DB->build_and_exec_query( array( 'select' => 'id as member_id, members_display_name AS name',
																	 'from'   => 'members',
																	 'where'  => 'members_display_name="'.$this->ipsclass->input['mem_name'].'"' ) );
		}
		
        /************************************************************
         * Now we need to start importing the files
         ***********************************************************/
        /* Transaction/rollback */
        $trans = array();
        
        require( ROOT_PATH . 'modules/gallery/lib/image.php' );
        
        if( is_array( $files ) )
        {
            foreach( $files as $file )
            {
            	$caption  = ( strstr(  $file, "/" ) ) ? array_pop( explode( "/", $file ) ) : $file;
            	
                $temp     = array( 'file_name'     => $file,
                                   'file_size'     => filesize( $files_dir.$file ),
                                   'member_id'     => $mem['member_id'],
                                   'category_id'   => $this->ipsclass->input['cat'],
                                   'album_id'      => $this->ipsclass->input['album'],
                                   'caption'       => $caption,
                                   'description'   => '',
                                   'approved'      => 1,
                                   'views'         => 0,
                                   'comments'      => 0,
                                   'date'          => time(),
                                   'ratings_total' => 0,
                                   'ratings_count' => 0,
                                   'credit_info'   => $this->ipsclass->input['credit_info'],
                                   'copyright'     => $this->ipsclass->input['copyright'],
                                   
                                 );

               $trans[] = array_merge( $temp, $this->process_file( $file, $files_dir, $category['thumbnail'], $category['watermark_images'], $this->ipsclass->input['cat'], $this->ipsclass->input['album'] ) );
            }
        }
        /*
        * Now with happy rollback support!
        *
        * If everything went ok during file creation, etc, ONLY then
        * add into gallery
        */
        foreach( $trans as $key=>$insert )  {
           $this->ipsclass->DB->do_insert( 'gallery_images', $insert );
           if( $this->ipsclass->input['cat'] ) {
               $this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+1, last_name='{$mem['name']}', last_member_id='{$mem['member_id']}', last_pic=".$this->ipsclass->DB->get_insert_id(), "id={$this->ipsclass->input['cat']}", 1 );
               $this->ipsclass->DB->simple_exec();
           }
           else {
            $this->ipsclass->DB->simple_update( 'gallery_albums', "images=images+1, last_name='{$mem['name']}', last_pic=".$this->ipsclass->DB->get_insert_id(), "id={$this->ipsclass->input['album']}", 1 );
            $this->ipsclass->DB->simple_exec();
           }
        }
        @rmdir( $files_dir);

        $this->ipsclass->admin->done_screen( "Images Added", "Invision Gallery Manager", "section=components&act=gallery" );            
    }


	function zip_upload()
	{				
		$field     = 'zipup';
		
		$FILE_NAME = $_FILES[$field]['name'];
		$FILE_SIZE = $_FILES[$field]['size'];
		$FILE_TYPE = $_FILES[$field]['type'];
		
		//----------------------------------------------
		// Naughty Opera adds the filename on the end of the
		// mime type - we don't want this.
		//----------------------------------------------
		
		$FILE_TYPE = preg_replace( "/^(.+?);.*$/", "\\1", $FILE_TYPE );
		
		//----------------------------------------------					
		// Naughty Mozilla likes to use "none" to indicate an empty upload field.
		// I love universal languages that aren't universal.
		//----------------------------------------------
		if ( $FILE_NAME == "" or ! $FILE_NAME or ($FILE_NAME == "none") )
		{
			$this->ipsclass->main_msg = "You did not specify anything to upload...";
			return;
		}
		
		//-------------------------------------------------
		// Copy the upload to the uploads directory
		//-------------------------------------------------
		
		if ( ! @move_uploaded_file( $_FILES[ $field ]['tmp_name'], $this->ipsclass->vars['gallery_images_path'].'/'.$FILE_NAME) )
		{
			$this->ipsclass->main_msg = "The upload failed, sorry!";
		}

        chmod( $this->ipsclass->vars['gallery_images_path'].'/'.$FILE_NAME, 0777 );
		
		$this->ipsclass->main_msg = "Upload complete!";
	}

   /***************************************************8
    *
    * Orphan Scanner Tool
    *
    **/

   /**
    * ad_plugin_gallery::find_orphans()
    * 
	* Searches for unlinked and unrefernced files
	* 
    * @return void
    **/
    function find_orphans()
    {  
        $files = array();

        $this->ipsclass->admin->page_title   = "Orphan Scanner";
        $this->ipsclass->admin->page_detail  = "This tool displays broken and unlinked images, allowing you to easily delete them";

        /**********************************************
         * List all sub directories
         **/
        $this->ipsclass->DB->simple_construct( array( 'select' => 'DISTINCT directory', 'from' => 'gallery_images' ) );
        $this->ipsclass->DB->simple_exec();

        $dirs[] = $this->ipsclass->vars['gallery_images_path'].'/'.$i['directory'];
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $dir_short[$this->ipsclass->vars['gallery_images_path'].'/'.$i['directory']] = $i['directory'];
            $dirs[] = $this->ipsclass->vars['gallery_images_path'].'/'.$i['directory'];        
        }

        /**********************************************
         * Find all gallery uploads
         **/
        foreach( $dirs as $dir )
        {
            if( is_dir( $dir ) )
            {
                $handle = opendir( $dir );
			    
                while( ( $filename = readdir( $handle ) ) !== false )
	    		{
                    if( ( $filename != "." ) && ( $filename != "..") )
                    {
                        if( preg_match( "/^(gallery_)/", $filename ) )
                        {
                            $files[] = $filename;
                            $file_dir[$filename] = $dir;
                        }
                    }
                }
			
                closedir($handle); 	
            }
        }

        /**********************************************
         * Get all the files from the db now
         **/
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_images' ) );
        $this->ipsclass->DB->simple_exec();
        $this->ipsclass->DB_files = array();
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $this->ipsclass->DB_files[$i['id']] = $i['masked_file_name'];
            $this->ipsclass->DB_files_data[$i['id']] = $i;
        }

        /**********************************************
         * Find unlinked images
         **/

        $this->ipsclass->adskin->td_header[] = array( "File"          , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "File Size"     , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Thumbnail"     , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "Thumbnail Size", "20%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Unlinked files" );
				
        foreach( $files as $file )
        {
            if( ! in_array( $file, $this->ipsclass->DB_files ) )
            {
                if( $this->ipsclass->input['remove'] == 'all_unlinked' )
                {
                    @unlink( "{$file_dir[$file]}/{$file}" );
                    @unlink( "{$file_dir[$file]}/tn_{$file}" );
                    
                }
                else
                {
                    $found=1;
                    $url = ( $dir_short[$file_dir[$file]] ) ? $dir_short[$file_dir[$file]] .'/' : '';
                    if( file_exists( "{$file_dir[$file]}/tn_{$file}" ) )
                    {
                        $tn = "<a href='{$this->ipsclass->vars['gallery_images_url']}/{$url}tn_{$file}' target='_blank'>tn_{$file}</a>";
                        $tn_size = $this->glib->byte_to_kb( filesize( "{$file_dir[$file]}/tn_{$file}" ) );
                        $dp = $dp + filesize( "{$file_dir[$file]}/tn_{$file}" );
                    }
                    else
                    {
                        $tn = "<center>None</center>";
                        $tn_size = '<center>N/A</center>';
                    }
                
                    $dp = $dp + filesize( $file_dir[$file] .'/'. $file );
                    $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                      "<a href='{$this->ipsclass->vars['gallery_images_url']}/{$url}{$file}' target='_blank'>{$file}</a>",
                                                      $this->glib->byte_to_kb( filesize( $file_dir[$file] .'/'. $file ) ),
                                                      $tn,
                                                      $tn_size,
                                              )      );
                }
            }
        }
      
        $dp = $this->glib->byte_to_kb( $dp );
        
        if( $found )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=orphans&remove=all_unlinked' class='fauxbutton'>Click here</a> to remove these files and reclaim <i>{$dp}</i> of diskspace", 'left', 'catrow2' );
        }
        else
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "No unlinked files found", 'left', 'catrow2' );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        /**********************************************
         * Find broken images
         **/
        $this->ipsclass->adskin->td_header[] = array( "ID"          , "5%" );
        $this->ipsclass->adskin->td_header[] = array( "Missing File", "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Thumbnail"   , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Caption"     , "45%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Broken Images" );
        
        $found = 0;
        foreach( $this->ipsclass->DB_files as $id => $file )
        {
            if( ! in_array( $file, $files ) )
            {
                if( $this->ipsclass->input['remove'] == 'all_broken' )
                {
                    $this->ipsclass->DB->simple_delete( 'gallery_images', "id={$id}" );
                    $this->ipsclass->DB->simple_exec();
                }
                else
                {
                    $found = 1;
                    $info = $this->ipsclass->DB_files_data[$id];

                    if( file_exists( "{$dir}/tn_{$file}" ) )
                    {
                        $tn = "<a href='{$this->ipsclass->vars['gallery_images_url']}/tn_{$file}' target='_blank'>tn_{$file}</a>";
                    }
                    else
                    {
                        $tn = "<center>None</center>";
                    }

                    $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                      "<a href='index.php?automodule=gallery&cmd=si&img={$id}' target='_blank'>{$id}</a>",
                                                      $info['masked_file_name'],
                                                      $tn,
                                                      $info['caption'],
                                              )      );
                }
            }
        }
        
        if( $found )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=orphans&remove=all_broken' class='fauxbutton'>Click here</a> to remove the entries for the broken images", 'left', 'catrow2' );
        }
        else
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "No broken images found", 'left', 'catrow2' );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        $this->ipsclass->admin->output();        
    }


   /***************************************************8
    *
    * Bulk Addition Tool
    *
    **/
    
	function view_dir_images( $dir )
	{
		
       $this->ipsclass->admin->page_title   = "Bulk Import: Directory View";
        $this->ipsclass->admin->page_detail  = "";

        $this->ipsclass->adskin->td_header[] = array( "Image"    , "5%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Importable Images" );
		
		/* Find all the directories */
		$dirs = array();
		if( is_dir( $dir ) )
		{
			if( $dh = opendir( $dir ) )
			{
				while( ( $file = readdir( $dh ) ) )
				{
					if( $this->glib->is_valid_type( $file ) )
					{
						$files[] = $file;	
					}
				}
				closedir( $dh );
			}
		}
		
		/* Loop through and display the files */
		foreach( $files as $file )
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<img src='$dir/$file'>" ) );
		}
		
		$this->ipsclass-> html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();
	}    

   /**
    * ad_plugin_gallery::bulk_add_form()
    * 
	* 
    * @return void
    **/
	
	function bulk_add_form()
	{	
		if( isset( $this->ipsclass->input['directory'] ) )
		{
			$this->bulk_add_form_2();
			return;	
		}

        if( $this->ipsclass->input['op'] == 'do' )
        {
            $this->_do_bulk_add();
            return;
        }

        if( $this->ipsclass->input['viewdir'])
        {
            $this->view_dir_images( $this->ipsclass->input['viewdir'] );
            return;
        }	        
		
		/* Exclusions */
		$exclude = array( '.', '..' );
		
		/* Find all the directories */
		$dir = ( $this->ipsclass->input['lookin'] ) ? $this->ipsclass->input['lookin'] : './';
		$dirs = array();
		if( is_dir( $dir ) )
		{
			if( $dh = opendir( $dir ) )
			{
				while( ( $file = readdir( $dh ) ) )
				{
					if( is_dir( $dir.$file ) && ! in_array( $file, $exclude ) )
					{
						$dirs[] = $dir.$file;	

					}
				}
				closedir( $dh );
			}
		}

		/* Info */
        $this->ipsclass->admin->page_title   = "Bulk Import: Directory Browser";
        $this->ipsclass->admin->page_detail  = "Use the directory browser below to select the directory you wish to import images from<br /><br />
                                          The file counter next to each directory only counts the number of images in that directory that are
                                          valid files to be imported.<br /><br />Please note that all the images being imported should already be
                                          CHMOD to 777.  Also note that the images will be moved to a new location, so do not link to images that
                                          you do not wish to be moved.";

        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"    , "5%" );
        $this->ipsclass->adskin->td_header[] = array( "Directory" , "60%" );
        $this->ipsclass->adskin->td_header[] = array( "Files"     , "5%" );
        $this->ipsclass->adskin->td_header[] = array( "Size"      , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Importable", "5%" );
        $this->ipsclass->adskin->td_header[] = array( "View"      , "5%" );        
        $this->ipsclass->adskin->td_header[] = array( "Import?"   , "10%" );        
        
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Directory to import from" );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "Current Directory: {$dir}", 'left', 'catrow2' );				
		/* Show the row */
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( 
																$icon, 
																"<a href='{$this->ipsclass->base_url}&amp;section=components&amp;act=gallery&amp;code=tools&amp;tool=bulkadd&lookin=../' title='Click here to look in the parent directory'>Up a directory</a>", 
																"&nbsp;", 
																"&nbsp;", 
																"&nbsp;", 															
																"&nbsp;", 
																"&nbsp;", 
														) );		 

		foreach( $dirs as $dir )
		{
			/* Count the files */			
			$dh         = opendir( $dir );
			$count      = 0;
			$size       = 0;
			$importable = 1;
			$dir_clean  = array_pop( explode( "/", $dir ) );

			while( $file = readdir( $dh ) )
			{
				if( in_array( $file, $exclude ) || ! $this->glib->is_valid_type( $file ) )
				{
					continue;	
				}
				
				$count++;
				$size += filesize( $dir.'/'.$file );				
				if( ! is_writeable( $dir.'/'.$file ) )
				{
					$importable = 0;	
				}
			}
			
			/* Importable? */
			if( ! is_writeable( $dir ) )
			{
				$importable = 0;	
			}
			
			/* Default Directories */
			if( in_array( $dir_clean, array( 'skin_acp', 'style_avatars', 'style_emoticons', 'style_images', 'uploads' ) ) )
			{
				$importable = -1;	
			}
			/* Import */
			if( $importable == 1 )
			{
				$import = "<a href='{$this->ipsclass->base_url}&amp;section=components&amp;act=gallery&amp;code=tools&amp;tool=bulkadd&directory=$dir' title='Click here to import files from this directory'>Import</a>";
			}
			else
			{
				$import = "&nbsp;";	
			}
			
			/* View */
			if( $importable == 1 )
			{
				$view = "<a href='{$this->ipsclass->base_url}&amp;section=components&amp;act=gallery&amp;code=tools&amp;tool=bulkadd&viewdir=$dir' title='Click here to view the importable files in this directory'><img src='{$this->ipsclass->adskin->img_url}/images/skin_visible.gif' border='0'></a>";
			}
			else
			{
				$view = "<img src='{$this->ipsclass->adskin->img_url}/images/skin_invisible.gif'";	
			}			
			
			/* Importable formatting */
			if( $importable == 1 )
			{
				$importable = "<img src='{$this->ipsclass->adskin->img_url}/images/icon_can_write.gif' title='Files can be imported from this directory'>";	
			}
			else if( $importable == -1 )
			{
				$importable = "<img src='{$this->ipsclass->adskin->img_url}/images/icon_cannot_write.gif' title='Can not import because the directory is a default IPB directory'>";					
			}
			else
			{
				$importable = "<img src='{$this->ipsclass->adskin->img_url}/images/icon_cannot_write.gif' title='Can not import because the directory/file permissions are wrong'>";	
			}
			
			/* Icon */
			$icon = "<img src='{$this->ipsclass->adskin->img_url}/images/folder.gif' border='0' alt='folder'>";
			
			/* Lookin */
			$lookin = "<a href='{$this->ipsclass->base_url}&amp;section=components&amp;act=gallery&amp;code=tools&amp;tool=bulkadd&lookin=$dir/' title='Click here to look in this directory'>{$dir_clean}</a>";
			
			/* Show the row */
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( 
																	$icon, 
																	$lookin, 
																	"<center>{$count}</center>", 
																	"<center>".$this->glib->byte_to_kb( $size )."</center>", 
																	"<center>{$importable}</center>", 
																	"<center>{$view}</center>",
																	"<center>$import</center>", 
															) );	
		}
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
	
		$this->ipsclass->admin->output();

	}
	
    function bulk_add_form_2()
    {
        $this->ipsclass->admin->page_title   = "Bulk Import: Options";
        $this->ipsclass->admin->page_detail  = "This tool will allow you to add multiple images from a directory";

        $this->ipsclass->adskin->td_header[] = array( "Option"   , "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Value" , "65%" );
        
        // Category Selector
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->glib;

        $this->category->read_data( false, 'Choose a category' );
        $this->category->current = $this->ipsclass->input['cat'];

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'    ),
                                                  2 => array( 'act'   , 'gallery'  ),
                                                  3 => array( 'tool'  , 'bulkadd' ),
                                                  4 => array( 'op'    , 'do' ),
                                                  5 => array( 'dir', $this->ipsclass->input['directory'] ),
                                                  6 => array( 'section', 'components' ),
                                     )  );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Bulk Import Options" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>How many images should be processed per cycle?</b>',
                                                  $this->ipsclass->adskin->form_input( 'num', 5 ),
                                          )      );

        $cats = $this->category->build_dropdown( 'cat' );
        $cats = str_replace( "forminput", "dropdown", $cats );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Which category?</b><br><small>All the images will be imported into the selected category</small>',
                                                  $cats,
                                          )      );

        // Album Selector
        $albums[] = array( 0, 'Choose an album' );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, name', 'from' => 'gallery_albums', 'order' => 'name ASC' ) );
        $this->ipsclass->DB->simple_exec();
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $albums[] = array( $i['id'], $i['name'] );
        }
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<font color='green'><b>-- OR --</font> Which Album?</b><br><small>All the images will be imported into the selected album</small>", $this->ipsclass->adskin->form_dropdown( 'album', $albums, $this->ipsclass->input['album'] ) ) );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => 'id > 0', 'order' => 'name ASC' ) );
        $this->ipsclass->DB->simple_exec();
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $mems[] = array( $i['id'], $i['name'] );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>File Owner</B><br><small>All the images will be assigned to the specified user</small>", $this->ipsclass->adskin->form_dropdown( 'user', $mems, $this->ipsclass->member['id'] ) ) );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Import Images" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();

    }
	
    function _do_bulk_add()
    {
        /************************************************************
         * Let's check the input first
         ***********************************************************/
        if( ! ( $this->ipsclass->input['cat'] || $this->ipsclass->input['album'] ) )
        {
            $this->ipsclass->admin->error( "You did not specify a category or album" );
        }

        if( $this->ipsclass->input['cat'] && $this->ipsclass->input['album'] )
        {
            $this->ipsclass->admin->error( "You can not choose both an album and a category" );
        }

        if( ! $this->ipsclass->input['dir'] )
        {
            $this->ipsclass->admin->error( "You did not specify a directory" );
        }

        if( ! is_dir( $this->ipsclass->input['dir'] ) )
        {
            $this->ipsclass->admin->error( "The directory you specified does not exist" );
        }

        if( ! is_writable( $this->ipsclass->input['dir'] ) )
        {
            $this->ipsclass->admin->error( "Please CHMOD the directory and the files in it to 0777" );
        }

        if( ! $this->ipsclass->input['num'] )
        {
            $this->ipsclass->admin->error( "You did not specify how many images to process per cycle" );
        }

        /************************************************************
         * Build a media list
         ***********************************************************/
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types' ) );
        $this->ipsclass->DB->simple_exec();

        while( $j = $this->ipsclass->DB->fetch_row() )
        {
			$exts = explode( ",", $j['extension'] );
			
			foreach( $exts as $ext )
			{
				$media_cache[] = $ext;
			}
        }

         /************************************************************
         * If we're still here, let's take a look in the directory
         ***********************************************************/
        $dh = opendir( $this->ipsclass->input['dir'] );            
        while( ( $filename = readdir( $dh ) ) )
        {
            if( ( $filename != "." ) && ( $filename != ".." ) )
            {
                $temp = explode( ".", $filename );
                $type = "." . strtolower( array_pop( $temp ) );

				if( in_array( $type, $media_cache ) )
                {
                    $files[] = $filename;
                }
            }
        }
        closedir( $dh );

        /************************************************************
         * Get the category information
         ***********************************************************/
        if( $this->ipsclass->input['cat'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();
            $category = $this->ipsclass->DB->fetch_row();
        }
        else
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'member_id', 'from' => 'gallery_albums', 'where' => "id={$this->ipsclass->input['album']}" ) );
            $this->ipsclass->DB->simple_exec();
            $mem = $this->ipsclass->DB->fetch_row();
            $category['thumbnail']        = ( $this->ipsclass->vars['gallery_create_thumbs'] )  ? 1 : 0;
            $category['watermark_images'] = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? 1 : 0;
        }

        /************************************************************
         * Now we need to start importing the files
         ***********************************************************/
        require( ROOT_PATH . 'modules/gallery/lib/image.php' );
        $processed = 0;
        if( is_array( $files ) )
        {
            foreach( $files as $file )
            {
                $temp     = array( 'file_name'     => $file,
                                   'file_size'     => filesize( $this->ipsclass->input['dir'] .'/'. $file ),
                                   'member_id'     => ( $mem ) ? $mem['member_id'] : $this->ipsclass->input['user'],
                                   'category_id'   => $this->ipsclass->input['cat'],
                                   'album_id'      => $this->ipsclass->input['album'],
                                   'caption'       => $file,
                                   'description'   => '',
                                   'approved'      => 1,
                                   'views'         => 0,
                                   'comments'      => 0,
                                   'date'          => time(),
                                   'ratings_total' => 0,
                                   'ratings_count' => 0,
                                 );

                $temp = array_merge( $temp,  $this->process_file( $file, $this->ipsclass->input['dir'].'/', $category['thumbnail'], $category['watermark_images'], $this->ipsclass->input['cat'] ) );
    
                $this->ipsclass->DB->do_insert( 'gallery_images', $temp );
				
				/* Get member info */
				$mid = ( $mem ) ? $mem['member_id'] : $this->ipsclass->input['user'];
				$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$mid}" ) );
				$this->ipsclass->DB->simple_exec();
				$m = $this->ipsclass->DB->fetch_row();

				if( $this->ipsclass->input['cat'] )
                {
                    $this->ipsclass->DB->simple_update( 'gallery_categories', "last_member_id={$mid}, last_name='{$m['name']}', images=images+1, last_pic=".$this->ipsclass->DB->get_insert_id(), "id={$this->ipsclass->input['cat']}", 1 );
                    $this->ipsclass->DB->simple_exec();
                }
                else
                {
                    $this->ipsclass->DB->simple_update( 'gallery_albums', "last_name='{$m['name']}', images=images+1, last_pic=".$this->ipsclass->DB->get_insert_id(), "id={$this->ipsclass->input['album']}", 1 );
                    $this->ipsclass->DB->simple_exec();
                }

            
                /* Do we need to take a break from processing? */
                $processed++;
                if( $processed >= $this->ipsclass->input['num'] )
                {
                    break;
                }
            }
        }

        if( empty( $files ) )
        {
            $this->ipsclass->admin->done_screen( "Images Added", "Invision Gallery Manager", "section=components&act=gallery" );            
        }
        else
        {
            $this->ipsclass->admin->redirect( "{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=bulkadd&op=do&album={$this->ipsclass->input['album']}&dir={$this->ipsclass->input['dir']}&url={$this->ipsclass->input['url']}&cat={$this->ipsclass->input['cat']}&user={$this->ipsclass->input['user']}&num={$this->ipsclass->input['num']}", "<b>{$processed} images processed, moving on to the next batch...</b><BR>DO NOT exit the browser or press the stop button, or your images will not be imported" );
            $this->ipsclass->admin->output();
        }
    }

    /**
     * process_file()
     * 
	 * Moves the upload file to the images directory, checks all group settings,
	 * does any required image manipulation
     *
	 * @version 1.3
	 * @since 1.1
	 * @access public
     *
     * @param string $name
     * @param integer $create_thumb
     * @param integer $watermark
     * @param integer $container_id
     * @return array
     **/
    function process_file( $file_name, $file_dir, $create_thumb=1, $watermark=0, $container_id=0, $album_id=0 )
    {
        require_once( ROOT_PATH . 'modules/gallery/lib/image.php' );

        $container_id = ( $container_id ) ? $container_id : $album_id;
            
        // -------------------------------------------------------------
        // Image Details
        // -------------------------------------------------------------
        $file_size = filesize( $file_dir . $file_name );
        $ext  = '.' . strtolower( array_pop( explode( ".", $file_name ) ) );    	

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

			$file_type = $tmedia['mime_type'];

            if( ! $tmedia['allowed'] )
            {
                $this->ipsclass->admin->error( "Media type not allowed." );
            }
            
            $media = 0;
            if( ! $tmedia['default_type'] )
            {
            	$media = 1;
            }            
        }
        else
   		{			
            $this->ipsclass->admin->error( "Bad File Type" );
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
            }

            $dir = ( $dir ) ? "{$dir}/" : "";
        }

        // -------------------------------------------------------------
        // Generate a file name and attempt to copy to uploads directory
        // -------------------------------------------------------------
        $masked_name = "gallery_{$this->ipsclass->input['user']}_{$container_id}_".time()%$file_size.$ext;
        $masked_file = $this->ipsclass->vars['gallery_images_path'].'/'.$dir.$masked_name;

        $img_load = array( 'out_dir'  => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                           'in_dir'   => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                           'in_file'  => $masked_name );

        if( ! rename( $file_dir.$file_name, $masked_file ) )
        {
            $this->ipsclass->admin->error( "Failed to import image: {$file_name}" );
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
	        	/* Bug, call lib_setup AFTER passing ipsclass,
	           	otherwise whitescreen trying to use GD, IM funcs */
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
                /* Bug, call lib_setup AFTER passing ipsclass,
                  otherwise whitescreen trying to use GD, IM funcs */
                $img->lib_setup();
    	        if( $img->resize_proportional( $this->ipsclass->vars['gallery_max_img_width'], $this->ipsclass->vars['gallery_max_img_height'] ) )
    	        {
    	            $img->write_to_file();
   	        	}
  	        	unset( $img );
    	    }
    	    
    	    /**
    	    * Bug #something or other, medium sized image not created from ACP
    	    **/
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
                $img = new Image( $img_load );
                $img->ipsclass =& $this->ipsclass;
                $img->glib =& $this->glib;
                /* Bug, call lib_setup AFTER passing ipsclass,
                  otherwise whitescreen trying to use GD, IM funcs */
                $img->lib_setup();
    	        $img_load['out_file'] = $masked_name;
    	        if( $img->watermark() )
    	        {
    	            $img->write_to_file();
    	        }
  	        unset( $img );
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
	
    	        $new_jpg = $this->ipsclass->vars['gallery_images_path'] . '/' . $dir . $masked_name;
    	     
    	        $this->glib->gif_to_jpg( $old_gif, $new_jpg );
    	        
    	        /**
    	        * Check med_ image too 
    	        **/
    	        $medium_image = ( $medium_image ) ? preg_replace( "/.gif/i", ".jpg", $medium_image ) : $medium_image;
       	    }
		}
       
        return  array(  'file_name'        => $this->ipsclass->parse_clean_value( $file_name ),
                        'masked_file_name' => $masked_name,
                        'medium_file_name' => $medium_image,
                        'file_size'        => filesize( $masked_file ),
                        'file_type'        => $file_type,
                        'thumbnail'        => $thumbnail,
                        'media'            => $media,
                        'directory'        => str_replace( "/", "", $dir ),
                      );
    }


   /******************************************************************
    *
    * Thumbnail Rebuild Tool
    *
    **/

   /**
    * ad_plugin_gallery::rethumbs()
    * 
	* Rebuilds thumbnails options screen
	* 
    * @return void
    **/
    function rethumbs()
    {
        if( $this->ipsclass->input['op'] == 'do' )
        {
            $this->_do_thumbs();
            return;
        }

        // Thanks Mark!
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->gallery_lib;

        $this->category->read_data( false, 'Choose from the categories below:' );

        $options = $this->category->build_dropdown();

        $cat_select = "<select name='cats[]' class='dropdown' multiple='multiple' size='10'>{$options}</select>";

        $this->ipsclass->admin->page_title   = "Rebuild Thumbnails";
        $this->ipsclass->admin->page_detail  = "This tool will allow you to recreate the thumbnails in your gallery";

        $this->ipsclass->adskin->td_header[] = array( "Option"   , "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Value" , "65%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'    ),
                                                  2 => array( 'act'   , 'gallery'  ),
                                                  3 => array( 'tool'  , 'rethumbs' ),
                                                  4 => array( 'op'    , 'do' ),
                                                  5 => array( 'section', 'components' ),
                                     )  );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Rebuild Thumbnail Options" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Choose which categories to rebuild thumbnails for</b>',
                                                  $cat_select,
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Rebuild album thumbnails as well?</b>',
                                                  $this->ipsclass->adskin->form_yes_no( 'album', 1 ),
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Number to rebuild per cycle</b>',
                                                  $this->ipsclass->adskin->form_input( 'num', 100 ),
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Begin Thumbnail Rebuild" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();       
    }

   /**
    * ad_plugin_gallery::_do_thumbs()
    * 
	* Rebuilds all thumbnails in the gallery
	* 
    * @return void
    **/
    function _do_thumbs()
    {
        require( ROOT_PATH . 'modules/gallery/lib/image.php' );
        
        if( is_array( $this->ipsclass->input['cats'] ) )
        {
            $cats  = implode( ",", $this->ipsclass->input['cats'] );
        }
        else
        {
            $cats = $this->ipsclass->input['cats'];
        }

        $start = ( $this->ipsclass->input['start'] ) ? $this->ipsclass->input['start'] : 0;

        if( $this->ipsclass->input['album'] )
        {
            $album = " OR album_id > 0 ";
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, masked_file_name, thumbnail, directory', 
                                      'from'   => 'gallery_images', 
                                      'where'  => "category_id IN ( {$cats} ) {$album}",
                                      'limit'  => array( $start, $this->ipsclass->input['num'] ) ) );
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
                /* Bug, call lib_setup AFTER passing ipsclass,
                  otherwise whitescreen trying to use GD, IM funcs */
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
            }            
        }
        else
        {
            $this->ipsclass->admin->error( "No images match the options you specified, so no thumbnails were created" );
        }

        // Now we need to see if there are more images to do, or if we are done
        $this->ipsclass->DB->simple_construct( array( 'select' => 'count(id) AS images', 
                                      'from'   => 'gallery_images', 
                                      'where'   => "category_id IN ( {$cats} ) {$album}" ) );
        $this->ipsclass->DB->simple_exec();

        $count = $this->ipsclass->DB->fetch_row();

        $processed = $start + $this->ipsclass->input['num'];
        if( $processed >= $count['images'] )
        {
            $this->ipsclass->admin->save_log("Rebuilt Thumbnails");
            $this->ipsclass->admin->done_screen("Thumbnails have been rebuilt", "Gallery Manager", "section=components&act=gallery" );
        }
        else
        {
            $start = $start + $total;
            $this->ipsclass->admin->redirect( "{$this->ipsclass->base_url}&cats={$cats}&num={$this->ipsclass->input['num']}&section=components&act=gallery&code=tools&tool=rethumbs&op=do&album={$this->ipsclass->input['album']}&start={$start}", "<b>{$processed} images processed, moving on to the next batch...</b><BR>DO NOT exit the browser or press the stop button, or your thumbnails will not be finished" );
            $this->ipsclass->admin->output();
        }
    }

   /******************************************************************
    *
    * Resize Image Tool
    *
    **/

   /**
    * ad_plugin_gallery::resize()
    * 
	* Resizes all images in the gallery
	* 
    * @return void
    **/    
    function resize()
    {
        if( $this->ipsclass->input['op'] == 'do' )
        {
            $this->_do_resize();
            return;
        }

        // Thanks Mark!
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->gallery_lib;

        $this->category->read_data( false, 'Choose from the categories below:' );

        $options = $this->category->build_dropdown();

        $cat_select = "<select name='cats[]' class='dropdown' multiple='multiple' size='10'>{$options}</select>";

        $this->ipsclass->admin->page_title   = "Resize Images";
        $this->ipsclass->admin->page_detail  = "This tool will allow you to resize the images in your gallery";

        $this->ipsclass->adskin->td_header[] = array( "Option"   , "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Value" , "65%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'    ),
                                                  2 => array( 'act'   , 'gallery'  ),
                                                  3 => array( 'tool'  , 'resize' ),
                                                  4 => array( 'op'    , 'do' ),
                                                  5 => array( 'section', 'components' ),
                                     )  );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Resize Options" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Choose which categories to resize images in</b>',
                                                  $cat_select,
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Resize album images as well?</b>',
                                                  $this->ipsclass->adskin->form_yes_no( 'album', 1 ),
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Number to resize per cycle</b>',
                                                  $this->ipsclass->adskin->form_input( 'num', 100 ),
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Begin Image Resizing" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();     
    }

   /**
    * ad_plugin_gallery::_do_resize()
    * 
	* Resizes the images in the gallery
	* 
    * @return void
    **/
    function _do_resize()
    {
        require( ROOT_PATH . 'modules/gallery/lib/image.php' );
        
        if( is_array( $this->ipsclass->input['cats'] ) )
        {
            $cats  = implode( ",", $this->ipsclass->input['cats'] );
        }
        else
        {
            $cats = $this->ipsclass->input['cats'];
        }

        $start = ( $this->ipsclass->input['start'] ) ? $this->ipsclass->input['start'] : 0;

        if( $this->ipsclass->input['album'] )
        {
            $album = " OR album_id > 0 ";
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, masked_file_name, medium_file_name, directory, media',
                                      'from'   => 'gallery_images',
                                      'where'  => "category_id IN ( {$cats} ) {$album}",
                                      'limit'  => array( $start, $this->ipsclass->input['num'] ) ) );
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
                
                // Image Info
                $img_load = array( 'out_dir'  => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                                   'in_dir'   => $this->ipsclass->vars['gallery_images_path'].'/'.$dir,
                                   'in_file'  => $i['masked_file_name'],
                                   'out_file' => $i['masked_file_name'],
                                 );
                
                // Create the image
                $img = new Image( $img_load );
                $img->ipsclass = &$this->ipsclass;
                $img->glib = &$this->glib;
                $img->lib_setup();
                if( $img->resize_proportional( $this->ipsclass->vars['gallery_max_img_width'], $this->ipsclass->vars['gallery_max_img_height'], 1 ) )
                {
                    $img->write_to_file();
                }
                unset( $img );
                
                /**
                * Does the image have a medium sized file?
                **/
			   if( $this->ipsclass->vars['gallery_medium_width'] || $this->ipsclass->vars['gallery_medium_height'] )
    	   	   {
    	    		$img_load['out_file'] = 'med_' . $i[ 'masked_file_name' ];
    	    		$img = new Image( $img_load );
                	$img->ipsclass =& $this->ipsclass;
                	$img->glib =& $this->glib;
                	$img->lib_setup();
    	    	
    	    		if( $img->resize_proportional( $this->ipsclass->vars['gallery_medium_width'], $this->ipsclass->vars['gallery_medium_height'] ) )
    	    		{
    	    			$img->write_to_file();	
    	    		}
    	    		
    	    		/**
    	    		* Did the picture already have one? If not, update DB record
    	    		**/
    	    		if( empty( $i['medium_file_name'] ) )
    	    		{
    	    			$this->ipsclass->DB->do_update( "gallery_images", array( 'medium_file_name' => $img_load['out_file'] ), "id = {$i['id']}" );
    	    			$q2 = $this->ipsclass->DB->exec_query();
    	    		}
    	    		unset( $img );
    	    	}
            }            
        }
        else
        {
            $this->ipsclass->admin->error( "No images match the options you specified, so no images were resized" );
        }

        // Now we need to see if there are more images to do, or if we are done
        $this->ipsclass->DB->simple_construct( array( 'select' => 'count(id) AS images',
                                      'from'   => 'gallery_images',
                                      'where'  => "category_id IN ( {$cats} ) {$album}" ) );
        $this->ipsclass->DB->simple_exec();

        $count = $this->ipsclass->DB->fetch_row();

        $processed = $start + $this->ipsclass->input['num'];
        if( $processed >= $count['images'] )
        {
            $this->ipsclass->admin->save_log( "Resized Images" );
            $this->ipsclass->admin->done_screen("Images have been resized", "Gallery Manager", "section=components&act=gallery" );
        }
        else
        {
            $start = $start + $total;
            $this->ipsclass->admin->redirect( "cats={$cats}&num={$this->ipsclass->input['num']}&section=components&act=gallery&code=tools&tool=resize&op=do&album={$this->ipsclass->input['album']}&start={$start}", "<b>{$processed} images processed, moving on to the next batch...</b><BR>DO NOT exit the browser or press the stop button, or your images will not be finished resizing" );
            $this->ipsclass->admin->output();
        }
    }


   /******************************************************************
    *
    * Member Report Tool
    *
    **/

   /**
    * ad_plugin_gallery::member_search()
    * 
	* Displays the search for a member form
	* 
    * @return void
    **/
    function member_search()
    {
        $this->ipsclass->admin->page_title = "Member Report";

        $this->ipsclass->admin->page_detail = "Search for a member.";

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'   ),
                                                  2 => array( 'act'   , 'gallery' ),
                                                  3 => array( 'tool'  , 'domemsrch' ),
                                                  4 => array( 'section', 'components' ),
                                         )      );

        //+-------------------------------

        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Member Quick Search" );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Enter part or all of the usersname</b>" ,
                                                  $this->ipsclass->adskin->form_input( "search_term" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form("Find Member");

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::do_member_search()
    * 
	* Performs the member search
	* 
    * @return void
    **/
    function do_member_search()
    {
        if( $this->ipsclass->input['search_term'] == '' )
        {
            /* Cough error about not finding username
             * NTS: Possibly list members in future? */
            $this->ipsclass->admin->error( "You did not enter a search term." );            
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, name', 'from' => 'members', 'where' => "name LIKE '%{$this->ipsclass->input['search_term']}%'" ) );
        $this->ipsclass->DB->simple_exec();

        if( $this->ipsclass->DB->get_num_rows() < 1 )
        {
            $this->ipsclass->admin->error( "No results found" );
        }
        else if( $this->ipsclass->DB->get_num_rows() > 1 )
        {
            // Page Information
            $this->ipsclass->admin->page_title   = "Member Report";
            $this->ipsclass->admin->page_detail  = "Select which member you will like to view a report on";

            // Table Headers
            $this->ipsclass->adskin->td_header[] = array( "Member Name", "100%" );

            // Start o' the page
            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( $this->ipsclass->DB->get_num_rows()." Results" );

            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                          "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=domemsrch&viewuser={$i['id']}'>{$i['name']}</a>",
                                                  )     );
            }

               $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            $this->ipsclass->admin->output();
        }
        else
        {
           $i = $this->ipsclass->DB->fetch_row();
            $this->gen_mem_report( $i['id'] ); 
        }

    }

   /**
    * ad_plugin_gallery::gen_mem_report()
    * 
	* Generates the member report specified by the $mid param.
	* 
    * @param integer $mid
    * @return void
    **/
    function gen_mem_report( $mid )
    {
        // ----------------------------------------------------------------
        // Get database info first
        // ----------------------------------------------------------------

        // Let's grab some overall total stuff first
        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( file_size ) as total_size, AVG( file_size ) as total_avg_size, COUNT( file_size ) as total_uploads',
                                      'from'   => 'gallery_images' ) );
        $this->ipsclass->DB->simple_exec();

        $stats = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( size ) as total_transfer, COUNT( size ) as total_viewed',
                                      'from'   => 'gallery_bandwidth' ) );
        $this->ipsclass->DB->simple_exec();
        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );

        // Now let's get some indivisual stuff
        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( file_size ) as user_size, AVG( file_size ) as user_avg_size, COUNT( file_size ) as user_uploads',
                                      'from'   => 'gallery_images',
                                      'where'  => "member_id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();
        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( size ) as user_transfer, COUNT( size ) as user_viewed',
                                      'from'   => 'gallery_bandwidth',
                                      'where'  => "member_id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();
        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );

        // Now let's find out who we are talking about here
        $this->ipsclass->DB->simple_construct( array( 'select' => 'name, gallery_perms',
                                      'from'   => 'members',
                                      'where'  => "id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();
        $member = $this->ipsclass->DB->fetch_row();

        // ----------------------------------------------------------------
        // Let's take a break fom querying, and start showing some stuff ;)
        // ----------------------------------------------------------------

        // Page Information
        $this->ipsclass->admin->page_title   = "Member Report for {$member['name']}";
        $this->ipsclass->admin->page_detail  = "Here you will find detailed statistcs related to diskspace usage and bandwidth usage";

        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Gallery Usage Overview" );

        $dp_percent  = ( $stats['total_size'] ) ? ( round( $stats['user_size'] / $stats['total_size'], 2 ) * 100 ).'%' : '0%';
        $up_percent  = ( $stats['total_uploads'] ) ? ( round( $stats['user_uploads'] / $stats['total_uploads'], 2 ) * 100 ).'%' : '0%';

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'Diskspace Overview', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Diskspace</B>"              , $this->glib->byte_to_kb( $stats['user_size'] ),
                                                  "<B>Percent of all Diskspace used</B>", $dp_percent,
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Uploads</B>"         , $stats['user_uploads'],
                                                  "<B>Percent of all uploads</B>", $up_percent,
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Average File Size</B>"             , $this->glib->byte_to_kb( $stats['user_avg_size'] ),
                                                  "<B>Average File Size of all users</B>", $this->glib->byte_to_kb( $stats['total_avg_size'] ),
                                          )      );

        // Display bandwidth info, if we record such things
        if( $this->ipsclass->input['gallery_detailed_bandwidth'] )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'Bandwidth Overview for the past ' . $this->ipsclass->input['gallery_bandwidth_period'] . ' hours', 'left', 'catrow2' );

            $tr_percent  = ( $stats['total_transfer'] ) ? ( round( $stats['user_transfer'] / $stats['total_transfer'], 2 ) * 100 ).'%' : '0%';
            $vi_percent  = ( $stats['total_viewed'] )   ? ( round( $stats['user_viewed'] / $stats['total_viewed'], 2 ) * 100 ).'%' : '0%';

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Transfer</B>"         , $this->glib->byte_to_kb( $stats['user_transfer'] ),
                                                      "<B>Percent of all Transfer</B>", $tr_percent,
                                              )      );

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Views</B>"         , $stats['user_viewed'],
                                                      "<B>Percent of all views</B>", $vi_percent,
                                              )      );

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            // List their top 5 most viewed files
            $this->ipsclass->adskin->td_header[] = array( "File"                  , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Transfer"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of user transfer", "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Image views"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of user views", "20%" );

            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Top 5 files viewed in the past {$this->ipsclass->input['gallery_bandwidth_period']} hours" );

            $this->ipsclass->DB->cache_add_query( 'get_top5_files_time_limited', array( 'mid' => $mid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $dp_percent = ( $stats['user_transfer'] ) ? round( $i['transfer'] / $stats['user_transfer'], 2 ) * 100 : 0;
                $up_percent = ( $stats['user_viewed'] )   ? round( $i['total'] / $stats['user_viewed'], 2) * 100       : 0;

                if( preg_match( "/^(tn_)/", $i['m_file_name'] ) )
                {
                    $i['file_name'] = 'tn_'.$i['file_name'];
                }

                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['file_name']}</B> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dofilesrch&viewfile={$i['fid']}' title='View file report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                          $this->glib->byte_to_kb( $i['transfer'] ),
                                                          $dp_percent . '%',
                                                          $i['total'],
                                                          $up_percent.'%',
                                                  )     );
            }

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        }
        else
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        }

        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Other Information" );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'count(*) as ecards', 'from' => 'gallery_ecardlog', 'where' => "member_id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();
        $cnt = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS comments', 'from' => 'gallery_comments', 'where' => "author_id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();
        $cnt = array_merge( $cnt, $this->ipsclass->DB->fetch_row() );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>E-Card's Sent</b> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=ecardlog&mid={$mid}' title='Show all logs for this member'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                  $cnt['ecards'],
                                                  "<b>Total Comments</b>",
                                                  $cnt['comments'],
                                         )      );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(rate) AS total_rates, AVG(rate) AS avg_rate', 
                                      'from' => 'gallery_ratings', 
                                      'where' => "member_id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();

        $rate = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Total images rated by this user</b> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=showratings&mid={$mid}' title='Show all ratings from this member'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0'></a>",
                                                  $rate['total_rates'],
                                                  "<b>Average rating given by this user</b>",
                                                  round( $rate['avg_rate'], 2 ),
                                         )      );


        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();


        // Take action against this member?
        $this->ipsclass->adskin->td_header[] = array( "Disable Viewing"  , "33%" );
        $this->ipsclass->adskin->td_header[] = array( "Disable Uploading", "33%" );
        $this->ipsclass->adskin->td_header[] = array( "Disable Gallery"  , "33%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Take action against member?" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'   ),
                                                  2 => array( 'act'   , 'gallery' ),
                                                  3 => array( 'tool'  , 'domemact' ),
                                                  4 => array( 'mid'   , $mid ),
                                                  5 => array( 'section', 'components' ),
                                         )      );

        $perms = explode( ":", $member['gallery_perms'] );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( '<center>'.$this->ipsclass->adskin->form_yes_no( 'remove_viewing'  ,   ( $perms[0] == 1 ) ? 0 : 1 ) .'</center>',
                                                  '<center>'.$this->ipsclass->adskin->form_yes_no( 'remove_uploading', ( $perms[1] == 1 ) ? 0 : 1 ) .'</center>',
                                                  '<center>'.$this->ipsclass->adskin->form_yes_no( 'remove_gallery'  , ( $perms[2] == 1 ) ? 0 : 1 ) .'</center>',
                                         )     );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Do it" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();


           $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::do_mem_act()
    * 
	* Does the member actions, such as removing gallery rights.
	* 
    * @return void
    **/
    function do_mem_act()
    {
        $view    = ( $this->ipsclass->input['remove_viewing'] == 1 )   ? 0 : 1;
        $upload  = ( $this->ipsclass->input['remove_uploading'] == 1 ) ? 0 : 1;
        $gallery = ( $this->ipsclass->input['remove_gallery'] == 1 )   ? 0 : 1;

        $perms = "$view:$upload:$gallery";

        $this->ipsclass->DB->simple_update( 'members', "gallery_perms='{$perms}'", "id={$this->ipsclass->input['mid']}", 1 );
        $this->ipsclass->DB->simple_exec();

        $this->ipsclass->admin->save_log("Action taken against member id: {$mid}");
        $this->ipsclass->admin->done_screen("The member's new restriction settings are now in effect", "Gallery Manager", "section=components&act=gallery" );
    }

   /******************************************************************
    *
    * Group Report Tool
    *
    **/

   /**
    * ad_plugin_gallery::group_search()
    * 
	* Displays the search for a group form
	* 
    * @return void
    **/
    function group_search()
    {
        $this->ipsclass->admin->page_title = "Group Report";

        $this->ipsclass->admin->page_detail = "Search for a group.";

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'   ),
                                                  2 => array( 'act'   , 'gallery' ),
                                                  3 => array( 'tool'  , 'dogroupsrch' ),
                                                  4 => array( 'section', 'components' ),
                                         )      );

        //+-------------------------------

        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Member Quick Search" );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Enter part or all of the groupname</b>" ,
                                                  $this->ipsclass->adskin->form_input( "search_term" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form("Find Group");

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::do_group_search()
    * 
	* Does the group search
	* 
    * @return void
    **/
    function do_group_search()
    {
        if( $this->ipsclass->input['search_term'] == '' )
        {
            /* Throw error about username */
            $this->ipsclass->admin->error( "You did not enter a search term." );
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => 'g_id, g_title',
                                      'from'   => 'groups',
                                      'where'  => "g_title LIKE '%{$this->ipsclass->input['search_term']}%'" ) );
        $this->ipsclass->DB->simple_exec();

        if( $this->ipsclass->DB->get_num_rows() < 1 )
        {
            $this->ipsclass->admin->error( "No results found" );
        }
        else if( $this->ipsclass->DB->get_num_rows() > 1 )
        {
            // Page Information
            $this->ipsclass->admin->page_title   = "Group Report";
            $this->ipsclass->admin->page_detail  = "Select which group you will like to view a report on";

            // Table Headers
            $this->ipsclass->adskin->td_header[] = array( "Group Name", "100%" );

            // Start o' the page
            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( $this->ipsclass->DB->get_num_rows()." Results" );

            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                          "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dogroupsrch&viewgroup={$i['g_id']}'>{$i['g_title']}</a>",
                                                  )     );
            }

               $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            $this->ipsclass->admin->output();
        }
        else
        {
            $i = $this->ipsclass->DB->fetch_row();
            $this->gen_group_report( $i['g_id'] );
        }

    }

   /**
    * ad_plugin_gallery::gen_group_report()
    * 
	* Generates a report for the group specified by the $gid param
	* 
    * @param integer $gid
    * @return void
    **/
    function gen_group_report( $gid )
    {
        // ----------------------------------------------------------------
        // Get database info first
        // ----------------------------------------------------------------

        // Let's grab some overall total stuff first
        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( file_size ) as total_size, AVG( file_size ) as total_avg_size, COUNT( file_size ) as total_uploads',
                                      'from'   => 'gallery_images' ) );
        $this->ipsclass->DB->simple_exec();

        $stats = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( size ) as total_transfer, COUNT( size ) as total_viewed',
                                      'from'   => 'gallery_bandwidth' ) );
        $this->ipsclass->DB->simple_exec();

        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );

        // Now let's get some indivisual stuff
        $this->ipsclass->DB->cache_add_query( 'get_group_rep_diskspace', array( 'gid' => $gid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );

        $this->ipsclass->DB->cache_add_query( 'get_group_rep_bandwidth', array( 'gid' => $gid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

		if( $this->ipsclass->DB->get_num_rows() )
		{
        	$stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );
		}

        // Now let's find out who we are talking about here
        $this->ipsclass->DB->simple_construct( array( 'select' => 'g_title', 'from' => 'groups', 'where' => "g_id={$gid}" ) );
        $this->ipsclass->DB->simple_exec();
        $group = $this->ipsclass->DB->fetch_row();

        // ----------------------------------------------------------------
        // Let's take a break fom querying, and start showing some stuff ;)
        // ----------------------------------------------------------------

        // Page Information
        $this->ipsclass->admin->page_title   = "Group Report for {$group['g_title']}";
        $this->ipsclass->admin->page_detail  = "Here you will find detailed statistcs related to diskspace usage and bandwidth usage";

        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Gallery Usage Overview" );

        $dp_percent  = ( $stats['total_size'] ) ? ( round( $stats['group_size'] / $stats['total_size'], 2 ) * 100 ).'%' : '0%';
        $up_percent  = ( $stats['total_uploads'] ) ? ( round( $stats['group_uploads'] / $stats['total_uploads'], 2 ) * 100 ).'%' : '0%';

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'Diskspace Overview', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Diskspace</B>"              , $this->glib->byte_to_kb( $stats['group_size'] ),
                                                  "<B>Percent of all Diskspace used</B>", $dp_percent,
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Uploads</B>"         , $stats['group_uploads'],
                                                  "<B>Percent of all uploads</B>", $up_percent,
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Average File Size</B>"             , $this->glib->byte_to_kb( $stats['group_avg_size'] ),
                                                  "<B>Average File Size of all groups</B>", $this->glib->byte_to_kb( $stats['total_avg_size'] ),
                                          )      );

        // Display bandwidth info, if we record such things
        if( $this->ipsclass->input['gallery_detailed_bandwidth'] )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'Bandwidth Overview for the past ' . $this->ipsclass->input['gallery_bandwidth_period'] . ' hours', 'left', 'catrow2' );

            $tr_percent  = ( $stats['total_transfer'] ) ? ( round( $stats['group_transfer'] / $stats['total_transfer'], 2 ) * 100 ).'%' : '0%';
            $vi_percent  = ( $stats['total_viewed'] )   ? ( round( $stats['group_viewed'] / $stats['total_viewed'], 2 ) * 100 ).'%' : '0%';

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Transfer</B>"         , $this->glib->byte_to_kb( $stats['group_transfer'] ),
                                                      "<B>Percent of all Transfer</B>", $tr_percent,
                                              )      );

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Views</B>"         , $stats['group_viewed'],
                                                      "<B>Percent of all views</B>", $vi_percent,
                                              )      );

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        }
        else
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        }

     //   $this->ipsclass->html .= $this->ipsclass->adskin->add_td_spacer();

        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Other Information" );

        $this->ipsclass->DB->cache_add_query( 'get_group_ecard_count', array( 'gid' => $gid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();



        $cnt = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->cache_add_query( 'get_group_comment_count', array( 'gid' => $gid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        $cnt = array_merge( $cnt, $this->ipsclass->DB->fetch_row() );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>E-Card's Sent</b> ",
                                                  ( $cnt['ecards'] ) ? $cnt['ecards'] : 0 ,
                                                  "<b>Total Comments</b>",
                                                  ( $cnt['comments'] ) ? $cnt['comments'] : 0,
                                         )      );

        $this->ipsclass->DB->cache_add_query( 'get_group_rating_overview', array( 'gid' => $gid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        $rate = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Total images rated by this group</b> ",
                                                  ( $rate['total_rates'] ) ? $rate['total_rates'] : 0,
                                                  "<b>Average rating given by this group</b>",
                                                  round( $rate['avg_rate'], 2 ),
                                         )      );


        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

           $this->ipsclass->admin->output();

    }

    /******************************************************************
     *
     * File Report Tool
     *
     **/

   /**
    * ad_plugin_gallery::file_search()
    * 
	* Displays the search for a file form
	* 
    * @return void
    **/
    function file_search()
    {
        $this->ipsclass->admin->page_title = "File Report";

        $this->ipsclass->admin->page_detail = "Search for a file.";

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'   ),
                                                  2 => array( 'act'   , 'gallery' ),
                                                  3 => array( 'tool'  , 'dofilesrch' ),
                                                  4 => array( 'section', 'components' ),
                                         )      );

        //+-------------------------------

        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "File Quick Search" );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Enter part or all of the caption</b>" ,
                                                  $this->ipsclass->adskin->form_input( "search_term" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form("Find File");

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::do_file_search()
    * 
	* Performs the file search
	* 
    * @return void
    **/
    function do_file_search()
    {
        if( $this->ipsclass->input['search_term'] == '' )
        {
            $this->ipsclass->admin->error( "You did not enter a search term" );
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => '*',
                                      'from'   => 'gallery_images',
                                      'where'  => "caption LIKE '%{$this->ipsclass->input['search_term']}%'" ) );
        $res = $this->ipsclass->DB->simple_exec();

        if( $this->ipsclass->DB->get_num_rows() < 1 )
        {
            $this->ipsclass->admin->error( "No results found" );
        }

        else if( $this->ipsclass->DB->get_num_rows() > 1 )
        {
            // Page Information
            $this->ipsclass->admin->page_title   = "File Report";
            $this->ipsclass->admin->page_detail  = "Select which file you will like to view a report on";

            // Table Headers
            $this->ipsclass->adskin->td_header[] = array( "File Name", "100%" );

            // Start o' the page
            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( $this->ipsclass->DB->get_num_rows()." Results" );

            while( $i = $this->ipsclass->DB->fetch_row( $res ) )
            {
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $this->_img_row( $i ) )     );
            }

               $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            $this->ipsclass->admin->output();
        }
        else
        {
            $i = $this->ipsclass->DB->fetch_row();
            $this->gen_file_report( $i['id'] );
        }

    }
    
    function _img_row( $i )
    {
    	$html  = "<table width='5%' border='0'><tr><td width='25%'>".$this->glib->make_image_link( $i, $i['thumbnail'] );
    	$html .= "</td><td width='95%' align='left' valign='middle'><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dofilesrch&viewfile={$i['id']}'><b>{$i['caption']}</b></a><br /><i>{$i['file_name']}</i></td></tr></table>";
    	return $html;
    }

   /**
    * ad_plugin_gallery::gen_file_report()
    * 
	* Generates a report for the file specified by the $fid param
	* 
    * @param integer fid
    * @return void
    **/
    function gen_file_report( $fid )
    {
        // ----------------------------------------------------------------
        // Get database info first
        // ----------------------------------------------------------------
        $this->ipsclass->DB->cache_add_query( 'get_file_info', array( 'fid' => $fid ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        $file = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->admin->page_title   = "File Report for {$file['file_name']}";
        $this->ipsclass->admin->page_detail  = "Here you will find detailed statistcs related to diskspace usage and bandwidth usage";

        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "File Overview" );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'General Overview', 'left', 'catrow2' );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Uploaded By</B>", $file['mname'] . "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=domemsrch&viewuser={$file['mid']}' title='View member report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                  "<B>Approved</B>"   , ( $file['approved'] ) ? 'Yes' : 'no',
                                          )      );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>File Size</B>", $this->glib->byte_to_kb( $file['file_size'] ),
                                                  "<B>File Type</B>", $file['file_type'],
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Masked Name</B>", $file['masked_file_name'],
                                                  "<B>Thumbnail</B>", ( $file['thumbnail'] ) ? 'Yes' : 'No',
                                          )      );

        if( $file['category_id'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'name',
                                          'from'   => 'gallery_categories',
                                          'where'  => "id={$file['category_id']}" ) );
            $this->ipsclass->DB->simple_exec();
            $i = $this->ipsclass->DB->fetch_row();

            $local_name = "<b>Category</b>";
        }
        else
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'name',
                                          'from'   => 'gallery_albums',
                                          'where'  => "id={$file['album_id']}" ) );
            $this->ipsclass->DB->simple_exec();
            $i = $this->ipsclass->DB->fetch_row();
            $local_name = "<b>Album</b>";
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Date Added</B>", $this->ipsclass->get_date( $file['date'], 'LONG' ),
                                                  $local_name, $i['name'],
                                          )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Comments</B>", $file['comments'],
                                                  "<B>Views</B>", $file['views'],
                                          )      );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'AVG( rate ) AS avg_rate, SUM( rate ) AS total_rate',
                                      'from'   => 'gallery_ratings',
                                      'where'  => "img_id={$file['id']}" ) );
        $this->ipsclass->DB->simple_exec();

        $rate = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Ratings</B>", ( $rate['avg_rate'] ) ? round( $rate['avg_rate'], 2 ) : 0,
                                                  "<B>Average Rating</B>",( $rate['total_rate'] ) ? $rate['total_rate'] : 0,
                                          )      );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(img_id) AS total',
                                      'from'   => 'gallery_favorites',
                                      'where'  => "img_id={$file['id']}" ) );
        $this->ipsclass->DB->simple_exec();

        $fav = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(img_id) AS total',
                                      'from'   => 'gallery_ecardlog',
                                      'where'  => "img_id={$file['id']}" ) );
        $this->ipsclass->DB->simple_exec();
        $ecard = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Number of users who marked this image as a favorite</B>", ( $fav['total'] ) ? $fav['total'] : 0,
                                                  "<B>Number of times this image was sent as an E-Card</B>"   , ( $ecard['total'] ) ? $ecard['total'] : 0,
                                          )      );

        if( $this->ipsclass->input['gallery_detailed_bandwidth'] )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'Bandwidth Overview for the past '.$this->ipsclass->input['gallery_bandwidth_period'].' hours', 'left', 'catrow2' );

            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT( * ) AS views, SUM( size ) AS transfer',
                                          'from'   => 'gallery_bandwidth',
                                          'where'  => "file_name='{$file['masked_file_name']}'" ) );
            $this->ipsclass->DB->simple_exec();

            $bandwidth = $this->ipsclass->DB->fetch_row();
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Views</B>"    , ( $bandwidth['views'] ) ?    $bandwidth['views']    : 0,
                                                      "<B>Transfer</B>" , ( $bandwidth['transfer'] ) ? $this->glib->byte_to_kb( $bandwidth['transfer'] ) : 0,
                                          )      );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
/* FIXME */
        $this->ipsclass->html .= "<center>".$this->glib->make_image_link( $file, $file['thumbnail'] )."</center><br />";
//print "hooray";
        $this->ipsclass->adskin->td_header[] = array( "Definition", "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "65%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Take Action" );
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'tools'   ),
                                                  2 => array( 'act'   , 'gallery' ),
                                                  3 => array( 'tool'  , 'dofileact' ),
                                                  5 => array( 'section', 'components' ),
                                                  4 => array( 'fid'   , $fid ),
                                         )      );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, name',
                                      'from'   => 'members',
                                      'where'  => 'id > 0',
                                      'order'  => 'name ASC' ) );
        $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $mems[] = array( $i['id'], $i['name'] );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Change File Owner</B>", $this->ipsclass->adskin->form_dropdown( 'new_owner', $mems, $file['mid'] ) ) );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Clear related bandwidth logs?</B>", $this->ipsclass->adskin->form_yes_no( 'clear_bandwidth', 0 ) ) );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Clear related rating logs?</B>", $this->ipsclass->adskin->form_yes_no( 'clear_rating', 0 ) ) );
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Clear related ecard logs?</B>", $this->ipsclass->adskin->form_yes_no( 'clear_ecard', 0 ) ) );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Do it" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::do_file_act()
    * 
	* Performs actions on the file, such as clearing logs
	* 
    * @return void
    **/
    function do_file_act()
    {
        $this->ipsclass->DB->simple_update( 'gallery_images', "member_id={$this->ipsclass->input['new_owner']}", "id={$this->ipsclass->input['fid']}", 1 );
        $this->ipsclass->DB->simple_exec();

        if( $this->ipsclass->input['clear_rating'] )
        {
            $this->ipsclass->DB->simple_delete( 'gallery_ratings', "img_id={$this->ipsclass->input['fid']}" );
            $this->ipsclass->DB->simple_exec();

        }

        if( $this->ipsclass->input['clear_ecard'] )
        {
            $this->ipsclass->DB->simple_delete( 'gallery_ecardlog', "img_id={$this->ipsclass->input['fid']}" );
            $this->ipsclass->DB->simple_exec();
        }

        if( $this->ipsclass->input['clear_bandwidth'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'masked_file_name',
                                          'from'   => 'gallery_images',
                                          'where'  => "id={$this->ipsclass->input['fid']}" ) );
            $this->ipsclass->DB->simple_exec();

            $i = $this->ipsclass->DB->fetch_row();

            $this->ipsclass->DB->simple_delete( 'gallery_bandwidth', "file_name='{$i['masked_file_name']}'" );
            $this->ipsclass->DB->simple_exec();
        }

        $this->ipsclass->admin->done_screen("Actions Done", "Gallery Manager", "section=components&act=gallery" );

    }

   /******************************************************************
    *
    * E-Card Log Tool
    *
    **/

   /**
    * ad_plugin_gallery::ecard_logs()
    * 
	* Displays the ecards that have been logged
	* 
    * @return void
    **/
    function ecard_logs()
    {
        // Do we need to delete anything?
        if( $this->ipsclass->input['del'] )
        {
            $this->ipsclass->DB->simple_delete( 'gallery_ecardlog', "id={$this->ipsclass->input['del']}" );
            $this->ipsclass->DB->simple_exec();
        }
        else if( $this->ipsclass->input['view'] )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Viewing E-card" );
            $this->ipsclass->DB->simple_construct( array( 'select' => '*',
                                          'from'   => 'gallery_ecardlog',
                                          'where'  => "id={$this->ipsclass->input['view']}" ) );
            $this->ipsclass->DB->simple_exec();

            $img = $this->ipsclass->DB->fetch_row();
			
			# Fix up IMG urls and EMO DIRs
			$img['msg'] = str_replace( "<#IMG_DIR#>", $this->ipsclass->skin['_imagedir'], $img['msg'] );
			$img['msg'] = str_replace( "<#EMO_DIR#>", $this->ipsclass->skin['_emodir']  , $img['msg'] );
	
            $this->ipsclass->adskin->td_header[] = array( "Defintion", "25%" );
            $this->ipsclass->adskin->td_header[] = array( "Value"    , "75%"  );

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                        "Subject",
                                                        $img['title'],
                                            )     );

            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                        "Message",
                                                        $img['msg'],
                                            )     );
			
			//--------------------------------------------
			// Make image tag
			//--------------------------------------------
			
			require_once( ROOT_PATH . 'modules/gallery/lib/gallery_library.php' );
			$glib           =  new gallery_lib;
			$glib->ipsclass =& $this->ipsclass;
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'caption, category_id, masked_file_name, file_type, id, directory', 'from' => 'gallery_images', 'where' => "id={$img['img_id']}" ) );
    	    $this->ipsclass->DB->simple_exec();
    	    $data = $this->ipsclass->DB->fetch_row();
    	        
			$image_tag = $glib->make_image_tag( $data, 1 );
			
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                        "Image",
                                                        $image_tag
                                            )     );

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            $this->ipsclass->admin->print_popup();
            exit();
        }

        // Page Information
        $this->ipsclass->admin->page_title   = "E-Card Logs";
        $this->ipsclass->admin->page_detail  = "Here you will find records of e-cards sent";

        // Table Headers
        $this->ipsclass->adskin->td_header[] = array( "From Member", "20%" );
        $this->ipsclass->adskin->td_header[] = array( "To"         , "35%"  );
        $this->ipsclass->adskin->td_header[] = array( "Date"       , "20%"  );
        $this->ipsclass->adskin->td_header[] = array( "Image"      , "15%"  );
        $this->ipsclass->adskin->td_header[] = array( "Delete"     , "10%"  );


        /*
         * defunct?
         */
         //$this->ipsclass->html .= $this->ipsclass->adskin->js_pop_win();

        // Start
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "E-Card Logs" );

        // How many do we have?
        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS count',
                                      'from'   => 'gallery_ecardlog' ) );
        $this->ipsclass->DB->simple_exec();

        $count = $this->ipsclass->DB->fetch_row();

        // Where are we?
        $st = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;

        // More stuff?
        if( $this->ipsclass->input['mid'] )
        {
            $more = "&mid={$this->ipsclass->input['mid']}";
            $q    = "AND member_id={$this->ipsclass->input['mid']} ";
        }
        else if( $this->ipsclass->input['to'] )
        {
            $more = "&to={$this->ipsclass->input['to']}";
            $q    = "AND receiver_email='{$this->ipsclass->input['to']}' ";
        }
        else
        {
            $more = "";
            $q    = "";
        }

        // Page spanning fun
        $links = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $count['count'],
                                               'PER_PAGE'    => 25,
                                               'CUR_ST_VAL'  => $st,
                                               'L_SINGLE'    => "Single Page",
                                               'L_MULTI'     => "Pages: ",
                                               'BASE_URL'    => $this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=ecardlog'.$more
                                             )
                                      );

        $this->ipsclass->DB->cache_add_query( 'get_ecard', array( 'q' => $q, 'st' => $st ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                        "{$i['name']} <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=ecardlog&mid={$i['member_id']}' title='Show all from this member'><img src='{$this->ipsclass->skin_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                        "{$i['receiver_name']} ( {$i['receiver_email']} <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=ecardlog&to={$i['receiver_email']}' title='Show all sent to this address'><img src='{$this->ipsclass->skin_url}/images/acp_search.gif' border='0' alt='..by receiver email'></a> )",
                                                        $this->ipsclass->get_date( $i['date'], 'LONG' ),
                                                        "<center><b><a href='javascript:pop_win(\"&section=components&act=gallery&code=tools&tool=ecardlog&view={$i['id']}\",640,480)' title='View E-Card'>View E-Card</a></b></center>",
                                                        "<center><b><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=ecardlog&del={$i['id']}'>Delete</a></b></center>",
                                            )     );

        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( $links, 'right', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();
    }

   /******************************************************************
    *
    * Rating Log Tool
    *
    **/

   /**
    * ad_plugin_gallery::ecard_logs()
    * 
	* Displays the rating logs for the member specifed by the $mid param
	* 
    * @param integer $mid
    * @return void
    **/
    function rating_log( $mid )
    {
        // Page Information
        $this->ipsclass->admin->page_title   = "Rating Log";
        $this->ipsclass->admin->page_detail  = "Here you will find records of ratings given by this user";

        // Table Headers
        $this->ipsclass->adskin->td_header[] = array( "Image Rated"  , "33%" );
        $this->ipsclass->adskin->td_header[] = array( "Date"   , "33%"  );
        $this->ipsclass->adskin->td_header[] = array( "Rating Given" , "33%"  );

/* FIXME */
    //    $this->ipsclass->html .= $this->ipsclass->adskin->js_pop_win();

        // Start
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Rating Log" );

        // How many do we have?
        $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS count',
                                      'from'   => 'gallery_ratings',
                                      'where'  => "member_id={$mid}" ) );
        $this->ipsclass->DB->simple_exec();
        $count = $this->ipsclass->DB->fetch_row();

        // Where are we?
        $st = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;

        // Page spanning fun
        $links = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $count['count'],
                                               'PER_PAGE'    => 25,
                                               'CUR_ST_VAL'  => $st,
                                               'L_SINGLE'    => "Single Page",
                                               'L_MULTI'     => "Pages: ",
                                               'BASE_URL'    => $this->ipsclass->base_url.'&section=components&act=gallery&code=tools&tool=showratings&mid='.$mid
                                             )
                                      );

        $this->ipsclass->DB->cache_add_query( 'get_rating_log', array( 'mid' => $mid, 'st' => $st ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();
   
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                        "{$i['file_name']}",
                                                        $this->ipsclass->get_date( $i['date'], 'LONG' ),
                                                        "{$i['rate']}",
                                            )     );

        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( $links, 'right', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();
    }

}
?>
