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
* Admin/Media Manager
*
* Allows admins to manage media types
*
* @package		Gallery
* @subpackage 	Admin
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.2
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

		switch($this->ipsclass->input['pg'])
		{
            case 'add':
                $this->add_type();
            break;

            case 'doadd':
                $this->do_add();
            break;

            case 'edit':
                $this->edit_type();
            break;

            case 'doedit':
                $this->do_edit();
            break;

            case 'del':
                $this->do_del();
            break;

            default:
                $this->media_overview();
            break;           
        }
    }

    function do_add()
    {
        $insert = array( 'icon'             => $this->ipsclass->input['icon'],
                         'title'            => $this->ipsclass->input['title'],
                         'mime_type'        => $this->ipsclass->input['mime_type'],
                         'extension'        => $this->ipsclass->input['extension'],
                         'allowed'          => $this->ipsclass->input['allowed'],
                         'allow_user_thumb' => $this->ipsclass->input['allow_user_thumb'],
                         'thumb_width'      => $this->ipsclass->input['thumb_width'],
                         'thumb_height'     => $this->ipsclass->input['thumb_height'],
                         'thumb_prop'       => $this->ipsclass->input['thumb_prop'],
                         'display_code'     => $this->ipsclass->my_br2nl( $this->ipsclass->input['display_code'] ),
                       );
        
        $this->ipsclass->DB->do_insert( 'gallery_media_types', $insert );

        $this->ipsclass->admin->save_log( "Added Media" );
        $this->ipsclass->admin->redirect( "{$this->ipsclass->base_url}&section=components&act=gallery&code=media", "Media Type Added" );

    }

    function add_type()
    {
        $this->ipsclass->admin->page_title = "Multimedia Management ( Add Type )";
        $this->ipsclass->admin->page_detail = "This section will allow you to add a new multimedia type";

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg', 'doadd' ),
                                                                 2 => array( 'code'  , 'media' ),
                                                                 3 => array( 'act' , 'gallery' ),
                                                                 4 => array( 'section', 'components' ) ) );
        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        // Start the table
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Media Information' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Icon</b><br>This icon will be used as the thumbnail if no other thumbnail is present",
                                                  $this->ipsclass->adskin->form_input( "icon" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Media Title</b>",
                                                  $this->ipsclass->adskin->form_input( "title" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Mime Type</b>",
                                                  $this->ipsclass->adskin->form_input( "mime_type" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Media Extension</b>",
                                                  $this->ipsclass->adskin->form_input( "extension" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow this type to be posted?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "allowed" )
                                         )      );
/*
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow users to upload their own thumbnail?</b><br /> This will allow users to upload an image to be used as a thumbnail, rather than using the default icon",
                                                  $this->ipsclass->adskin->form_yes_no( "allow_user_thumb" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Thumbnail Dimensions</b><br>Make sure that you specify both a width and height",
                                                  $this->ipsclass->adskin->form_input( "thumb_width" ) . ' x ' . $this->ipsclass->adskin->form_input( "thumb_height" )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Use Proportional Thumbnails</b><br>This option will create thumbnail dimensions based on the image dimensions",
                                                  $this->ipsclass->adskin->form_yes_no( "thumb_prop" )
                                         )      );
*/
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Display Code</b><br>This is the html code needed to properly display the media type",
                                                  $this->ipsclass->adskin->form_textarea( "display_code" )
                                         )      );

        // End the table
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Add Media Type" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();


        $this->ipsclass->admin->output();        
    }

    function do_edit()
    {
        $update = array( 'icon'             => $this->ipsclass->input['icon'],
                         'title'            => $this->ipsclass->input['title'],
                         'mime_type'        => $this->ipsclass->input['mime_type'],
                         'extension'        => $this->ipsclass->input['extension'],
                         'allowed'          => $this->ipsclass->input['allowed'],
                         'allow_user_thumb' => $this->ipsclass->input['allow_user_thumb'],
                         'thumb_width'      => $this->ipsclass->input['thumb_width'],
                         'thumb_height'     => $this->ipsclass->input['thumb_height'],
                         'thumb_prop'       => $this->ipsclass->input['thumb_prop'],
                         'display_code'     => $this->ipsclass->my_br2nl( $this->ipsclass->input['display_code'] ),
                       );

        $this->ipsclass->DB->do_update( 'gallery_media_types', $update, "id={$this->ipsclass->input['id']}" );

        $this->ipsclass->admin->save_log( "Edited Media" );
        $this->ipsclass->admin->redirect( "{$this->ipsclass->base_url}&section=components&act=gallery&code=media", "Media Type Edited" );

    }

    function do_del()
    {
        $this->ipsclass->DB->simple_delete( 'gallery_media_types', "id={$this->ipsclass->input['id']}" );
        $this->ipsclass->DB->simple_exec();

        $this->ipsclass->admin->save_log( "Deleted Media" );
        $this->ipsclass->admin->done_screen( "Media Type Removed", "Invision Gallery Manager", "section=components&act=gallery" );            
    }

    function edit_type()
    {
        $this->ipsclass->admin->page_title = "Multimedia Management ( Edit Type )";
        $this->ipsclass->admin->page_detail = "This section will allow you to modify the settings for the selected media";

        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types', 'where' => "id={$this->ipsclass->input['id']}" ) );
        $this->ipsclass->DB->simple_exec();

        $info = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg', 'doedit' ),
                                                                 2 => array( 'code'  , 'media' ),
                                                                 3 => array( 'act' , 'gallery' ),
                                                                 4 => array( 'id', $this->ipsclass->input['id'] ),
                                                                 5 => array( 'section', 'components' ) ) );
        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        // Start the table
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Media Information' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Icon</b><br>This icon will be used as the thumbnail if no other thumbnail is present",
                                                  $this->ipsclass->adskin->form_input( "icon", $info['icon']) . " <img src='style_images/{$this->ipsclass->skin['_imagedir']}/{$info['icon']}'>"
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Media Title</b>",
                                                  $this->ipsclass->adskin->form_input( "title", $info['title'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Mime Type</b>",
                                                  $this->ipsclass->adskin->form_input( "mime_type", $info['mime_type'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Media Extension</b>",
                                                  $this->ipsclass->adskin->form_input( "extension", $info['extension'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow this type to be posted?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "allowed", $info['allowed'] )
                                         )      );
/*
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow users to upload their own thumbnail?</b><br /> This will allow users to upload an image to be used as a thumbnail, rather than using the default icon",
                                                  $this->ipsclass->adskin->form_yes_no( "allow_user_thumb", $info['allow_user_thumb'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Thumbnail Dimensions</b><br>Make sure that you specify both a width and height",
                                                  $this->ipsclass->adskin->form_input( "thumb_width", $info['thumb_width'] ) . ' x ' . $this->ipsclass->adskin->form_input( "thumb_height", $info['thumb_height'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Use Proportional Thumbnails</b><br>This option will create thumbnail dimensions based on the image dimensions",
                                                  $this->ipsclass->adskin->form_yes_no( "thumb_prop", $info['thumb_prop'] )
                                         )      );
*/
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Display Code</b><br>This is the html code needed to properly display the media type",
                                                  $this->ipsclass->adskin->form_textarea( "display_code", $info['display_code'] )
                                         )      );

        // End the table
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Edit Media Type" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();


        $this->ipsclass->admin->output();        
    }

    function media_overview()
    {
        $this->ipsclass->admin->page_title = "Multimedia Management";
        $this->ipsclass->admin->page_detail = "This section will allow you to configure various multimedia file types.";

        // -----------------------------------------
        // Custom Types
        // -----------------------------------------        

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "Icon"      , "5%" );
        $this->ipsclass->adskin->td_header[] = array( "Name"      , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "Mime Type" , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Extension" , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Allowed"   , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Options"   , "30%" );

		$basic_title = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						<tr>
						 <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Custom Multimedia Types</td>
						 <td align='right' width='60%'><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=media&pg=add' class='realdarkbutton'><font color='black'>Add New Type</a></a></td>
						</tr>
						</table>";

        // Info Table
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( $basic_title );

        // Get the info
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types', 'where' => 'default_type=0' ) );
        $this->ipsclass->DB->simple_exec();
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( 
                                                     "<center><img src='style_images/{$this->ipsclass->skin['_imagedir']}/{$i['icon']}'></center>" ,
                                                     $i['title'],
                                                     $i['mime_type'],
                                                     $i['extension'],
                                                     ( $i['allowed'] ) ? "<center><img src='{$this->ipsclass->adskin->img_url}/images/acp_check.gif' border='0' alt='X' /></center>" : "",
                                                     "<center><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=media&pg=edit&id={$i['id']}' class='fauxbutton'>Edit</a> &nbsp;&nbsp;&nbsp; <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=media&pg=del&id={$i['id']}' class='fauxbutton'><font color='red'>Remove</a></a></center>",
                                              )    );
            
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        // -----------------------------------------
        // Default Types
        // ----------------------------------------- 

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "Icon"      , "5%" );
        $this->ipsclass->adskin->td_header[] = array( "Name"      , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "Mime Type" , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Extension" , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Allowed"   , "10%" );
        $this->ipsclass->adskin->td_header[] = array( "Options"   , "30%" ); 
        
        // Info Table
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Default Types" );

        // Get the info
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_media_types', 'where' => 'default_type=1' ) );
        $this->ipsclass->DB->simple_exec();
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( 
                                                     "<center><img src='style_images/{$this->ipsclass->skin['_imagedir']}/{$i['icon']}'></center>" ,
                                                     $i['title'],
                                                     $i['mime_type'],
                                                     $i['extension'],
                                                     ( $i['allowed'] ) ? "<center><img src='{$this->ipsclass->adskin->img_url}/images/acp_check.gif' border='0' alt='X' /></center>" : "",
                                                     "--",
                                              )    );
            
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        
        $this->ipsclass->admin->output();
    }
}
?>
