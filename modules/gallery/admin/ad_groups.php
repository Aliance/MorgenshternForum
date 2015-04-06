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
* Admin/Group Permission Manager
*
* Manages group permissions
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

		switch($this->ipsclass->input['pg'])
		{
            case 'editgroup':
                $this->edit_group();
            break;

            case 'doeditgroup':
                $this->do_edit_group();
            break;

            default:
                $this->groups_overview();
            break;           
        }

    }

   /******************************************************************
    *
    * Group Permission Stuff
    *
    **/

   /**
    * ad_plugin_gallery::groups_overview()
    * 
	* Displays all groups with a general overview of their settings.  Really
    * not very happy with the looks of this, will be redoing it soon - I think.
	* 
    * @todo Make the overview nicer and more useful
    * @return void
    **/
    function groups_overview()
    {
        // Page Information
        $this->ipsclass->admin->page_title   = "Groups Overview";
        $this->ipsclass->admin->page_detail  = "Here you will find a quick overview of your current group settings";

        // Table headers
        $this->ipsclass->adskin->td_header[] = array( "Group"     , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Members"   , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Overview"  , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Report"    , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Edit"      , "20%" );

        $this->ipsclass->html .= "<script language='javascript'>
function toggle_ele( chooser, eleName )
{
    var block  = document.getElementById( eleName );

    if( block.style.display == 'none' )
    {
        block.style.display = 'block';
    }
    else
    {
        block.style.display = 'none';
    }
}
</script>";


        // Start o' the page
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Group Overview" );

        $this->ipsclass->DB->cache_add_query( 'get_groups', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
        	// Figure out restrictions
           $dp = ( ! empty( $i['g_max_diskspace'] ) ) ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : '';
           $up = ( ! empty( $i['g_max_upload'] ) )    ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : '';
           $tr = ( ! empty( $i['g_max_transfer'] ) )  ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : '';
           $vi = ( ! empty( $i['g_max_views'] ) )     ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : '';
           $ca = ( ! empty( $i['g_create_albums'] ) ) ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : "<img src='{$this->ipsclass->adskin->img_url}/images/aff_cross.png' border='0' alt='X' />";
           $al = ( ! empty( $i['g_album_limit'] ) )   ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : '';
           $gs = ( ! empty( $i['g_slideshows'] ) )    ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : "<img src='{$this->ipsclass->adskin->img_url}/images/aff_cross.png' border='0' alt='X' />";
           $gf = ( ! empty( $i['g_favorites'] ) )     ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : "<img src='{$this->ipsclass->adskin->img_url}/images/aff_cross.png' border='0' alt='X' />";
           $gc = ( ! empty( $i['g_comment'] ) )       ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : "<img src='{$this->ipsclass->adskin->img_url}/images/aff_cross.png' border='0' alt='X' />";
           $gr = ( ! empty( $i['g_rate'] ) )          ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : "<img src='{$this->ipsclass->adskin->img_url}/images/aff_cross.png' border='0' alt='X' />";
           $ge = ( ! empty( $i['g_ecard'] ) )         ? "<img src='{$this->ipsclass->adskin->img_url}/images/aff_tick.png' border='0' alt='X' />" : "<img src='{$this->ipsclass->adskin->img_url}/images/aff_cross.png' border='0' alt='X' />";


            $divs .= "<div class='popupmenu' style='display:none;position:absolute;' id='ele_{$i['g_title']}'>
            <b>Group: {$i['g_title']}</b><br />
                <table class='ipbtable' width='300px'>
               
                <tr>
                    <td width='2%'>{$dp}</td>
                    <td width='95%' align='left'>Diskspace Restriction: {$i['g_max_diskspace']} KB</td>
                </tr>
                <tr>
                    <td width='2%'>{$up}</td>
                    <td width='95%' align='left'>Upload size Restriction: {$i['g_max_upload']} KB</td>
                </tr>
                <tr>
                    <td width='2%'>{$tr}</td>
                    <td width='95%' align='left'>Transfer Restriction: {$i['g_max_transfer']} KB</td>
                </tr>
                <tr>
                    <td width='2%'>{$vi}</td>
                    <td width='95%' align='left'>Views Restriction: {$i['g_max_views']} views</td>
                </tr>
                <tr>
                    <td width='2%'>{$ca}</td>
                    <td width='95%' align='left'>Album Creation</td>
                </tr>
                <tr>
                    <td width='2%'>{$ar}</td>
                    <td width='95%' align='left'>Album Restriction: {$i['g_album_limit']}</td>
                </tr>
                <tr>
                    <td width='2%'>{$gs}</td>
                    <td width='95%' align='left'>Slideshows</td>
                </tr>
                <tr>
                    <td width='2%'>{$gf}</td>
                    <td width='95%' align='left'>Favorites System</td>
                </tr>
                <tr>
                    <td width='2%'>{$gc}</td>
                    <td width='95%' align='left'>Comment</td>
                </tr>
                <tr>
                    <td width='2%'>{$gr}</td>
                    <td width='95%' align='left'>Rate</td>
                </tr>
                <tr>
                    <td width='2%'>{$ge}</td>
                    <td width='95%' align='left'>E-Card</td>
                </tr>
                </table>
            </div>";
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( '<b>'.$i['g_title'].'</b>',
                                                      "<center>{$i['count']}</center>",
                                                      "<center><a href='#' onClick='toggleview(\"ele_{$i['g_title']}\")'>Overview</a></center>{$divs}",
                                                      "<center><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dogroupsrch&viewgroup={$i['g_id']}' title='View group report'>Report</a></center>",
                                                      "<center><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=groups&pg=editgroup&group={$i['g_id']}'>Edit</a></center>"
                                              )     );
        }
        
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->html .= $divs;

        $this->ipsclass->admin->output();
    }

   /**
    * ad_plugin_gallery::edit_group()
    * 
	* Displays the edit group form
	* 
    * @return void
    **/
    function edit_group()
    {
        // ---------------------------------------------------------------------
        // Get Group Info
        // ---------------------------------------------------------------------
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'groups', 'where' => "g_id={$this->ipsclass->input['group']}" ) );
        $this->ipsclass->DB->simple_exec();

        $group = $this->ipsclass->DB->fetch_row();

        // ----------------------------------------------------------------------
        // Group Settings Page Top
        // ----------------------------------------------------------------------
        $this->ipsclass->admin->page_detail = "This is where you can edit group settings";
        $this->ipsclass->admin->page_title  = "Group Settings for {$group['g_title']}";

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg'    , 'doeditgroup' ),
                                                  4 => array( 'code'  , 'groups' ),
                                                  2 => array( 'act'   , 'gallery'  ),
                                                  3 => array( 'group' , $this->ipsclass->input['group'] ),
                                                  5 => array( 'section', 'components' ),
                                         )      );

        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        // ----------------------------------------------------------------------
        // Diskspace Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Diskspace Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section allows you to set restrictions on this groups disk space usage', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Maximum diskspace for this group in KB?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_max_diskspace", $group['g_max_diskspace']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Maximum upload size for this group in KB?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_max_upload", $group['g_max_upload']  )
                                 )      );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ----------------------------------------------------------------------
        // Bandwidth Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Bandwidth Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section allows you to set restrictions on this groups bandwidth usage', 'left', 'catrow2' );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Maximum daily transfer in KB?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_max_transfer", $group['g_max_transfer']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Maximum daily image views?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_max_views", $group['g_max_views']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ----------------------------------------------------------------------
        // Multiple File Uploads Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Multiple File Upload Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section allows you to set if this group can upload multiple files at one time', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Number of upload boxes to show?</b><br>Set to 0 to disallow this feature ( <i>Limited to 20</i> )" ,
                                          $this->ipsclass->adskin->form_input( "g_multi_file_limit", $group['g_multi_file_limit']  )
                                 )      );
/*
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can upload zip files?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_zip_upload", $group['g_zip_upload']  )
                                 )      );
*/
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ----------------------------------------------------------------------
        // Album Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Album Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section allows you to set this groups album privileges', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can create albums?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_create_albums", $group['g_create_albums']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Limit number of albums?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_album_limit", $group['g_album_limit']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Limit number of images in a album?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_img_album_limit", $group['g_img_album_limit']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can moderate albums?</b><br />This group would be able to see private albums, as well as delete images from other's albums" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_mod_albums", $group['g_mod_albums']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ----------------------------------------------------------------------
        // Access Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Access Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section allows you to restrict what this group can edit/delete ', 'left', 'catrow2' );

//        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can view actual image URL?</b><br>By default the image URL is hidden from your members, if you would like this group to see the real URL then set this option to yes." ,
//                                          $this->ipsclass->adskin->form_yes_no( "g_img_local", $group['g_img_local']  )
//                                 )      );
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can edit own images and comments?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_edit_own", $group['g_edit_own']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can delete own images and comments?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_del_own", $group['g_del_own']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can move own images?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_move_own", $group['g_move_own']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ----------------------------------------------------------------------
        // Feature Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Feature Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section allows you to enable or disable various features for this group', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can view slideshows?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_slideshows", $group['g_slideshows']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can use favorite image system?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_favorites", $group['g_favorites']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can comment on images?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_comment", $group['g_comment']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can rate images?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_rate", $group['g_rate']  )
                                 )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can send E-cards?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_ecard", $group['g_ecard']  )
                                 )      );
                                 
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can search?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_can_search_gallery", $group['g_can_search_gallery']  )
                                 )      );                                 

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ----------------------------------------------------------------------
        // Multimedia Settings
        // ----------------------------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "{none}"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Multimedia Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( 'This section lets you restrict this groups multimedia privileges', 'left', 'catrow2' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Can post movies?</b>" ,
                                          $this->ipsclass->adskin->form_yes_no( "g_movies", $group['g_movies']  )
                                 )      );
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Upload size limit?</b><br>Leave blank for no restriction" ,
                                          $this->ipsclass->adskin->form_input( "g_movie_size", $group['g_movie_size']  )
                                 )      );

        // ----------------------------------------------------------------------

        // ----------------------------------------------------------------------
        // Settings Page Bottom
        // ----------------------------------------------------------------------
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( 'Save Settings' );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();
        // ----------------------------------------------------------------------
    }

   /**
    * ad_plugin_gallery::do_edit_group()
    * 
	* Performs the group edit
	* 
    * @return void
    **/
    function do_edit_group()
    {
        $this->ipsclass->DB->simple_construct( array( 'select' => 'g_title', 'from' => 'groups', 'where' => "g_id={$this->ipsclass->input['group']}" ) );
        $this->ipsclass->DB->simple_exec();
        
        if( $this->ipsclass->DB->get_num_rows() )
        {
            $group = $this->ipsclass->DB->fetch_row();
        }
        else
        {
            $this->ipsclass->admin->error( "Invalid Group" );
        }
        
        /*
        * Setting upload boxes to 32,000 baaad */
        if( $this->ipsclass->input['g_multi_file_limit'] > 20 )
        {
        	$this->ipsclass->admin->error( "Number of upload boxes cannot exceed 20." );
        }

        $update = array (
                                                            'g_max_diskspace'    => $this->ipsclass->input['g_max_diskspace'],
                                                            'g_max_upload'       => $this->ipsclass->input['g_max_upload'],
                                                            'g_max_transfer'     => $this->ipsclass->input['g_max_transfer'],
                                                            'g_max_views'        => $this->ipsclass->input['g_max_views'],
                                                            'g_create_albums'    => $this->ipsclass->input['g_create_albums'],
                                                            'g_album_limit'      => $this->ipsclass->input['g_album_limit'],
                                                            'g_img_album_limit'  => $this->ipsclass->input['g_img_album_limit'],
                                                            'g_slideshows'       => $this->ipsclass->input['g_slideshows'],
                                                            'g_favorites'        => $this->ipsclass->input['g_favorites'],
                                                            'g_rate'             => $this->ipsclass->input['g_rate'],
                                                            'g_comment'          => $this->ipsclass->input['g_comment'],
                                                            'g_ecard'            => $this->ipsclass->input['g_ecard'],
                                                            'g_del_own'          => $this->ipsclass->input['g_del_own'],
                                                            'g_edit_own'         => $this->ipsclass->input['g_edit_own'],
                                                            'g_move_own'         => $this->ipsclass->input['g_move_own'],
                                                            'g_mod_albums'       => $this->ipsclass->input['g_mod_albums'],
                                                            'g_img_local'        => $this->ipsclass->input['g_img_local'],
                                                            'g_movies'           => $this->ipsclass->input['g_movies'],
                                                            'g_movie_size'       => $this->ipsclass->input['g_movie_size'],
                                                            'g_multi_file_limit' => $this->ipsclass->input['g_multi_file_limit'],
                                                            'g_zip_upload'       => $this->ipsclass->input['g_zip_upload'],
                                                            'g_can_search_gallery' => $this->ipsclass->input['g_can_search_gallery'],
                         );

        $this->ipsclass->DB->do_update( 'groups', $update, "g_id={$this->ipsclass->input['group']}");

        $this->rebuild_group_cache();

        $this->ipsclass->admin->save_log( "Edited Group Settings for '{$group['g_title']}'" );
        $this->ipsclass->admin->redirect( "{$this->ipsclass->base_url}&section=components&act=gallery&code=groups", "Group Settings for '{$group['g_title']}' was modified" );
    }

	//---------------------------------------------------------------------------------
	// Rebuild group cache
	//---------------------------------------------------------------------------------
	
	function rebuild_group_cache()
	{	
		$this->ipsclass->cache['group_cache'] = array();
			
		$this->ipsclass->DB->simple_construct( array( 'select' => "*",
									  'from'   => 'groups'
							 )      );
		
		$this->ipsclass->DB->simple_exec();
		
		while ( $i = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['group_cache'][ $i['g_id'] ] = $i;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );
	}
}
?>
