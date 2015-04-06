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
* Admin/Category Manager
*
* Category admin manager
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
	$this->forumfunc->ipsclass =& $this->ipsclass;

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

            case 'catcreateform':
                $this->create_cat_form();
            break;

            case 'docatcreate':
                $this->create_cat();
            break;

            case 'cateditform':
                $this->edit_cat_form();
            break;

            case 'docatedit':
                $this->edit_cat();
            break;

            case 'catdown':
                $this->cat_down();
            break;
            
            case 'catup':
                $this->cat_up();
            break;

            case 'deletecat':
                $this->del_cat_form();
            break;

            case 'dodeletecat':
                $this->do_del_cat();
            break;

            case 'emptycat':
                $this->empty_cat_form();
            break;

            case 'doemptycat':
                $this->do_empty_cat();
            break;

            case 'permform':
                $this->cat_perm_form();
            break;

            case 'docatperms':
                $this->do_cat_perms();
            break;

			case 'recountall':
				$this->recount_all();
				$this->index_screen();
			break;

            case 'recount':
                $this->recount();
				$this->index_screen();
            break;

            case 'doreorder':
                $this->reorder();
                $this->index_screen();
            break;
            
            case 'editallperms':
            	$this->multi_perm_edit();
            break;
            
            case 'doeditallperms':
            	$this->do_multi_perm_edit();
            break;
            
            case 'precat':
            	$this->precat_form();
            break;

			default:
				$this->index_screen();
			break;
		}
    }
   
    
    /**
     * ad_plugin_gallery::do_multi_perm_edit()
     * 
	 * Form for editing multiple category permissions at one time
	 * 
     * @return void
     **/    
    function do_multi_perm_edit()
    {           
        // Loop through the input
        foreach( $this->ipsclass->input as $k => $v )
        {
        	// Is this a thumb permission?
			if( preg_match( "#^thumb_#", $k ) )
			{
				$temp = explode( "_", $k );
				$thumb[$temp[2]][] = $temp[1];
			}
			
        	// Is this a view permission?
			if( preg_match( "#^view_#", $k ) )
			{
				$temp = explode( "_", $k );
				$view[$temp[2]][] = $temp[1];
			}
			
        	// Is this a image permission?
			if( preg_match( "#^image_#", $k ) )
			{
				$temp = explode( "_", $k );
				$image[$temp[2]][] = $temp[1];
			}
			
        	// Is this a comment permission?
			if( preg_match( "#^comment_#", $k ) )
			{
				$temp = explode( "_", $k );
				$comment[$temp[2]][] = $temp[1];
			}
			
        	// Is this a moderate permission?
			if( preg_match( "#^moderate_#", $k ) )
			{
				$temp = explode( "_", $k );
				$moderate[$temp[2]][] = $temp[1];
			}			
        }
        
        // --------------------------------------------
        // Do the updating
        // --------------------------------------------
        
        // Update thumb permissions
        if( is_array( $thumb ) )
        {
        	foreach( $thumb as $cid => $perm_list )
        	{
        		$this->ipsclass->DB->do_update( 'gallery_categories', array( 'perms_thumbs' => implode( ",", $perm_list ) ) , "id={$cid}" ); 
        	}
        }
        
        // Update view permissions
        if( is_array( $view ) )
        {
        	foreach( $view as $cid => $perm_list )
        	{
        		$this->ipsclass->DB->do_update( 'gallery_categories', array( 'perms_view' => implode( ",", $perm_list ) ) , "id={$cid}" ); 
        	}
        }
        
        // Update image permissions
        if( is_array( $image ) )
        {
        	foreach( $image as $cid => $perm_list )
        	{
        		$this->ipsclass->DB->do_update( 'gallery_categories', array( 'perms_images' => implode( ",", $perm_list ) ) , "id={$cid}" ); 
        	}
        }
        
        // Update comment permissions
        if( is_array( $comment ) )
        {
        	foreach( $comment as $cid => $perm_list )
        	{
        		$this->ipsclass->DB->do_update( 'gallery_categories', array( 'perms_comments' => implode( ",", $perm_list ) ) , "id={$cid}" ); 
        	}
        }        
		
        // Update moderate permissions
        if( is_array( $moderate ) )
        {
        	foreach( $moderate as $cid => $perm_list )
        	{
        		$this->ipsclass->DB->do_update( 'gallery_categories', array( 'perms_moderate' => implode( ",", $perm_list ) ) , "id={$cid}" ); 
        	}
        }
        
		// Done and done
        $this->ipsclass->admin->redirect( $this->ipsclass->base_url . "&section=components&act=gallery", "New permissions applied" );
    }    
    
    /**
     * ad_plugin_gallery::multi_perm_edit()
     * 
	 * Form for editing multiple category permissions at one time
	 * 
     * @return void
     **/    
    function multi_perm_edit()
    {
  	//-----------------------------------------
		
		$this->ipsclass->admin->page_title = "Multiple Category Permission Editor";
		
		$this->ipsclass->admin->page_detail = "You can manage your category permissions from this section.";
		
		// ---------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms' ) );
		$this->ipsclass->DB->simple_exec();
		
		while( $i = $this->ipsclass->DB->fetch_row() )
		{
			$groups[$i['perm_id']] = $i['perm_name'];	
		}

		// ---------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_categories', 'order' => 'c_order ASC' ) );
		$this->ipsclass->DB->simple_exec();
		
		// ---------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'cats'           ),
												                 2 => array( 'act'   , 'gallery'        ),
												                 3 => array( 'pg'    , 'doeditallperms' ),
                                                                                                                 4 => array( 'section', 'components' ),
									                    )      );

		$this->ipsclass->adskin->td_header[] = array( "Group Name"  , "25%" );		
		$this->ipsclass->adskin->td_header[] = array( "View Thumbnails", "10%" );
		$this->ipsclass->adskin->td_header[] = array( "View Images"    , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Post Images"    , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Post Comments"  , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Moderate"       , "10%" );
		
		// Javascript
		$this->ipsclass->html .= "
		
				<script language='Javascript1.1'>
				<!--
				
				function checkcol(perm,status) {
				
					var f = document.theAdminForm;
					regex = new RegExp( '^' + perm );
					
					if ( status == 1 )
					{
						mystat = true;
					}
					else
					{
						mystat = false;
					}					

	   				for(i=0; i< f.elements.length; i++)
   					{
      					if(f.elements[i].type == \"checkbox\")
      					{
							if( regex.test( f.elements[i].name ) )
							{
								f.elements[i].checked = mystat;
							}
	      				}
	   				}					
					
						
				}
				
				function checkrow(groupid,catid,status) {

					var f = document.theAdminForm;
					
					str_part = '';
					
					if ( status == 1 )
					{
						mystat = 'true';
					}
					else
					{
						mystat = 'false';
					}

					eval( 'f.thumb_'+groupid+'_'+catid+'.checked='+mystat );
					eval( 'f.view_'+groupid+'_'+catid+'.checked='+mystat );
					eval( 'f.image_'+groupid+'_'+catid+'.checked='+mystat );
					eval( 'f.comment_'+groupid+'_'+catid+'.checked='+mystat );
					eval( 'f.moderate_'+groupid+'_'+catid+'.checked='+mystat );

				}
				
				//-->
				
				</script>
				
				";		
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Permissions" );
		
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
																		 '&nbsp;',
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"thumb\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"thumb\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"view\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"view\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"image\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"image\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"comment\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"comment\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"moderate\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"moderate\",0)' /></center>",
											 					)  );			
		
		// ----------------------------------------
		$total_rows = $this->ipsclass->DB->get_num_rows() * count( $groups );
		while( $i = $this->ipsclass->DB->fetch_row() )
		{					
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "<b>{$i['name']}</b>", "left", "tdrow3");
			
			$thumb_perms    = explode( ",", $i['perms_thumbs']   );
			$view_perms     = explode( ",", $i['perms_view']     );
			$image_perms    = explode( ",", $i['perms_images']   );
			$comment_perms  = explode( ",", $i['perms_comments'] );
			$moderate_perms = explode( ",", $i['perms_moderate'] );

			foreach( $groups as $id => $name )
			{
				$j++;
				// Initialize vars
				$thumb     = '';
				$view     = '';
				$image    = '';
				$comment  = '';
				$moderate = '';
				
				// --------------------------------------------------------
				// Figure out the permissions
				// -------------------------------------------------------

				// Thumb permissions
				if( in_array( $id, $thumb_perms ) )
				{
					$thumb = "<center id='mgyellow'><input type='checkbox' name='thumb_".$id."_".$i['id']."' value='1' checked></center>";
				}
				else
				{
					$thumb = "<center id='mgyellow'><input type='checkbox' name='thumb_".$id."_".$i['id']."' value='1'></center>";
				}
				
				// View Permissions
				if( in_array( $id, $view_perms ) )
				{
					$view = "<center id='mgblue'><input type='checkbox' name='view_".$id."_".$i['id']."' value='1' checked></center>";
				}
				else
				{
					$view = "<center id='mgblue'><input type='checkbox' name='view_".$id."_".$i['id']."' value='1'></center>";
				}
				
				// Image Permissions
				if( in_array( $id, $image_perms ) )
				{
					$image = "<center id='mgred'><input type='checkbox' name='image_".$id."_".$i['id']."' value='1' checked></center>";
				}
				else
				{
					$image = "<center id='mgred'><input type='checkbox' name='image_".$id."_".$i['id']."' value='1'></center>";
				}
				
				// Comment Permissions
				if( in_array( $id, $comment_perms ) )
				{
					$comment = "<center id='mggreen'><input type='checkbox' name='comment_".$id."_".$i['id']."' value='1' checked></center>";
				}
				else
				{
					$comment = "<center id='mggreen'><input type='checkbox' name='comment_".$id."_".$i['id']."' value='1'></center>";
				}
				
				// Moderate Permissions
				if( in_array( $id, $moderate_perms ) )
				{
					$moderate = "<center id='memgroup'><input type='checkbox' name='moderate_".$id."_".$i['id']."' value='1' checked></center>";
				}
				else
				{
					$moderate = "<center id='memgroup'><input type='checkbox' name='moderate_".$id."_".$i['id']."' value='1'></center>";
				}
				
				// Buttons
				$name = "<table border='0' width='100%'>
						   <tr>
						     <td width='50%' align='left'>$name</td>
				             <td width='50%' align='right'>
				               <input type='button' id='button' value='+' onclick='checkrow($id,{$i['id']},1)' /> 
				               <input type='button' id='button' value='-' onclick='checkrow($id,{$i['id']},0)' />
				             </td>
				           </tr>
				         </table>";	
				
				// Output
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
																		 $name,
																		 $thumb,
																		 $view,
																		 $image,
																		 $comment,
																		 $moderate
											 					)  );				
			}
		}
		
		// ----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Update Category Permissions");
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();		
		       
        
    }

    /**
     * ad_plugin_gallery::recount()
     * 
	 * Recount images and comments for a category
	 * 
     * @return void
     **/    
    function recount()
    {
        
        // What kind of category is this?
        $this->ipsclass->DB->simple_construct( array( 'select' => 'album_mode', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
        $this->ipsclass->DB->simple_exec();
        $cat = $this->ipsclass->DB->fetch_row();
        
        // Get the image ids
        if( $cat['album_mode'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'id',
                                          'from'   => 'gallery_albums',
                                          'where'   => "category_id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();

            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $albums[] = $i['id'];
            }
			if( ! $albums ) $albums[] = 0;

            $this->ipsclass->DB->simple_construct( array( 'select' => 'id', 
                                          'from'   => 'gallery_images', 
                                          'where'  => "album_id IN ( ".implode( ",", $albums )." )" ) );

            $this->ipsclass->DB->simple_exec();
        }
           
        else
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'id', 
                                          'from'   => 'gallery_images', 
                                          'where'  => "category_id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();
        }

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $ids[] = $i['id'];
        }
		
		// Recount comments
        if( is_array( $ids ) )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS comments', 
                                          'from'   => 'gallery_comments', 
                                          'where'  => "img_id IN ( ".implode( ",", $ids )." ) AND approved=1" ) );
            $this->ipsclass->DB->simple_exec();
            $info = $this->ipsclass->DB->fetch_row();



            $this->ipsclass->DB->simple_update( 'gallery_categories', "comments={$info['comments']}", "id={$this->ipsclass->input['cat']}", 1 );
            $this->ipsclass->DB->simple_exec();
        }
	
		// Recount images
        if( $cat['album_mode'] )
        {
			if( ! $albums ) $albums[] = 0;
            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS images', 
                                          'from'   => 'gallery_images', 
                                          'where'  => "album_id IN ( ".implode( ",", $albums )." )" ) );

        }
        else
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) AS images', 
                                          'from'   => 'gallery_images', 
                                          'where'  => "category_id={$this->ipsclass->input['cat']} AND approved=1" ) );
        }

        $this->ipsclass->DB->simple_exec();
        $info = $this->ipsclass->DB->fetch_row();

		// Get last poster info
		if( ! $cat['album_mode'] )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'member_id', 
										  'from'   => 'gallery_images', 
						  				  'where'  => "category_id={$this->ipsclass->input['cat']}", 
										  'order'  => 'id desc', 
										  'limit'  => array( 0,1 ) ) );
			$this->ipsclass->DB->simple_exec();
		}
		else
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'member_id',
				                          'from'   => 'gallery_albums',
				                          'where'  => "category_id={$this->ipsclass->input['cat']}",
										  'order'  => 'last_pic desc',
										  'limit'  => array( 0,1 ) ) );
			$this->ipsclass->DB->simple_exec();
		}

		$mid = $this->ipsclass->DB->fetch_row();

		if( $mid['member_id'] )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$mid['member_id']}" ) );
			$this->ipsclass->DB->simple_exec();
			$member = $this->ipsclass->DB->fetch_row();
		}
		else
		{
			$mid['member_id'] = 0;
			$member['name'] = '';
		}
		

        $this->ipsclass->DB->simple_update( 'gallery_categories', "images={$info['images']}, last_name='{$member['name']}', last_member_id={$mid['member_id']}", "id={$this->ipsclass->input['cat']}", 1 );
        $this->ipsclass->DB->simple_exec();


        $this->ipsclass->main_msg = "Category Recounted";

    }


    /**
     * ad_plugin_gallery::reorder()
     * 
	 * Re-order categories
	 * 
     * @return void
     **/    
    function reorder()
    {

        foreach( $this->ipsclass->input['order'] as $id => $pos )
        {
            $this->ipsclass->DB->simple_update( 'gallery_categories', "c_order={$pos}", "id={$id}", 1 );
            $this->ipsclass->DB->simple_exec();
        }

        $this->ipsclass->main_msg = "Categories Re-Ordered";

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
        $this->ipsclass->admin->page_title   = "Category Management";
        $this->ipsclass->admin->page_detail  = "This is where you can view and edit your categories";
        
        $this->ipsclass->adskin->td_header[] = array( "Category" , "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Options"  , "35%" );
        $this->ipsclass->adskin->td_header[] = array( "Stats"    , "25%" );
        

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg'    , 'doreorder'  ),
                                                  4 => array( 'code'  , 'cats' ),
                                                  3 => array( 'section', 'components' ),
                                                  2 => array( 'act'   , 'gallery'    ),
                                         )  );

        // Start o' the page
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Categories [ <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=editallperms'>Edit all permissions</a> ]" );

        // Categories
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->glib;

        $this->category->read_data( false, '', 0 );
        $this->_build_category_row();

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( 
                                          array( 
                                                 "( <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=catcreateform'>Create a new top level category</a> )",
                                                 "&nbsp;",
                                                 "&nbsp;",
                                          )    );

        // End o' the page
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Re-Order Categories" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();
    }

    /**
     * ad_plugin_gallery::_gen_order_drop()
     * 
	 * Generates a dropdown used in re-ordering categories
	 * 
     * @param string $name
     * @param integer $num
     * @param integer $sel
     * @return void
     **/
    function _gen_order_drop( $name, $num, $sel )
    {

        for( $i = 1; $i < $num + 1; $i++ )
        {
            $elements[] = array( $i, $i );
        }

        return $this->ipsclass->adskin->form_dropdown( $name, $elements, $sel, '', 'realbutton' );
    }

    /**
     * ad_plugin_gallery::_build_category_row()
     * 
	 * Recursive function to list categories and sub categories
	 * 
     * @param integer $parent
     * @param string $this->ipsclass->inputdent
     * @param integer $visible
     * @return void
     **/
     
    function _build_category_row()
    {
        
        if( $this->ipsclass->input['parent'] )
        {
         	$parent = $this->ipsclass->input['parent'];
            if( is_array( $this->category->data[$parent]['child'] ) )
            {
                foreach( $this->category->data[$parent]['child'] as $cid )
                {
                    $show_cats[$cid] = $this->category->data[$cid];
                }
            }
        }
        else
        {
        	$parent = 0;
			$show_cats = array();
			if( is_array( $this->category->ordered ) )
			{
				foreach( $this->category->ordered as $data )
				{
					if( is_array( $data ) )
					{
						if( ! $data['parent'] && $data['id'] )
						{
							$show_cats[$data['id']] = $data;
						}
					}
				}
			}
		}
		
        $this->ipsclass->DB->simple_construct( array( "select" => 'COUNT(*) AS num', 
                                      'from'   => 'gallery_categories', 
    		                          'where'  => "parent={$parent}" ) );
        $this->ipsclass->DB->simple_exec();
        $total = $this->ipsclass->DB->fetch_row();
		
		foreach( $show_cats as $data )
		{
			$data['description'] = html_entity_decode( $data['description'] );
			$stats = "<center><i>Images: {$data['images']} Comments: {$data['comments']}</i><br/> ( <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=recount&cat={$data['id']}'>Recount</a> )</center>";
    		$order = $this->_gen_order_drop( "order[{$data['id']}]", $total['num'], $data['c_order'] );
			
			if( $this->category->data[$data['id']]['child'] )
			{
				$name = "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&parent={$data['id']}' title='Click for sub children'>{$data['name']}</a> ( ". count( $this->category->data[$data['id']]['child'] ) . " sub categories ) ";
			}
			else
			{
				$name = $data['name'];
			}
			
    		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( 
    		                                          array( 
    		                                                 "<b>{$name}</b><BR>{$indent} <i>{$data['description']}</i><br>{$indent}{$order} ( <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=catcreateform&parent={$data['id']}'>Create a new child category</a> )",
    		                                                 "<center>[ <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=cateditform&cat={$data['id']}' title='Configure this category by clicking here'><b>Settings</b></a> &middot; 
    		                                                            <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=permform&cat={$data['id']}' title='Set who can view this category, make comments, and moderate by clicking here'><b>Permissions</b></a> &middot; 
    		                                                            <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=deletecat&cat={$data['id']}' title='Remove this category from the gallery by clicking here'><font color='red'><b>Delete</b></font></a> &middot; 
    		                                                            <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=emptycat&cat={$data['id']}' title='Remove all the images and comments within this category by clicking here'><font color='red'><b>Empty</a></b></font> ]</center><br/>
    		                                                            <center>Bulk import from <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=zipfile&cat={$data['id']}'>zip</a> or <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=bulkadd&cat={$data['id']}'>directory</a></center>",
    		                                                 $stats,
    		                                          )    );
		}		
	}

   /**
    * ad_plugin_gallery::del_cat_form()
    * 
	* Displays the delete a category form
	* 
    * @return void
    **/
    function del_cat_form()
    {

        // Get category info
        $this->ipsclass->DB->simple_construct( array( "select" => '*', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
        $this->ipsclass->DB->simple_exec();

        $cat = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->admin->page_title   = "Delete Category: {$cat['name']}";
        $this->ipsclass->admin->page_detail  = "This will remove the category, including all images and comments found within";

           $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "35%" );
           $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "65%" );

        // Build a category dropdown
        $all_cats[] = array( 'no', 'No, remove them all' );
        $this->ipsclass->DB->simple_construct( array( 'select' => 'id,name', 'from' => 'gallery_categories', 'order' => 'c_order' ) );
        $this->ipsclass->DB->simple_exec();

        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $all_cats[] = array( $i['id'], '&middot; '.$i['name'] );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg'    , 'dodeletecat'  ),
                                                  4 => array( 'code'  , 'cats' ),
                                                  2 => array( 'act'   , 'gallery'    ),
                                                  5 => array( 'section', 'components' ),
                                                  3 => array( 'cat'   , $this->ipsclass->input['cat'] )
                                         )  );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Category Deletion" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  "<b>Stats</b>",
                                                  "Images: {$cat['images']}<br />Comments: {$cat['comments']}",
                                         )      );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  "<b>Move Images/Comments to another category?</b>",
                                                  $this->ipsclass->adskin->form_dropdown( 'move_cat', $all_cats, 'no' ),
                                         )      );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Do Deletion" );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();
    }

   /**
    * ad_plugin_gallery::empty_cat_form()
    * 
	* Displays the empty a category form
	* 
    * @return void
    **/
    function empty_cat_form()
    {

        // Get category info
        $this->ipsclass->DB->simple_construct( array( "select" => '*', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
        $this->ipsclass->DB->simple_exec();

        $cat = $this->ipsclass->DB->fetch_row();


        $this->ipsclass->admin->page_title   = "Empty Category: {$cat['name']}";
        $this->ipsclass->admin->page_detail  = "This will remove tall images and comments found within the category";

           $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "35%" );
           $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "65%" );

        // Build a category dropdown
        $all_cats[] = array( 'no', 'No, remove them all' );
        $this->ipsclass->DB->simple_construct( array( 'select' => 'id,name', 'from' => 'gallery_categories', 'order' => 'c_order' ) );
        $this->ipsclass->DB->simple_exec();
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $all_cats[] = array( $i['id'], '&middot; '.$i['name'] );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg'  , 'doemptycat'  ),
                                                  4 => array( 'code'  , 'cats' ),
                                                  5 => array( 'section', 'components' ),
                                                  2 => array( 'act'   , 'gallery'    ),
                                                  3 => array( 'cat'   , $this->ipsclass->input['cat'] )
                                         )  );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Empty this Category" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  "<b>Stats</b>",
                                                  "Images: {$cat['images']}<br />Comments: {$cat['comments']}",
                                         )      );


        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  "<b>Move Images/Comments to another category?</b>",
                                                  $this->ipsclass->adskin->form_dropdown( 'move_cat', $all_cats, 'no' ),
                                         )      );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Empty this category" );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();
    }

   /**
    * ad_plugin_gallery::do_del_cat()
    * 
	* Removes a category
	* 
    * @return void
    **/
    function do_del_cat()
    {

        // Are we moving the images?
        if( $this->ipsclass->input['move_cat'] != 'no' )
        {
            $this->ipsclass->DB->simple_update( 'gallery_images', "category_id={$this->ipsclass->input['move_cat']}", "category_id={$this->ipsclass->input['cat']}", 1 );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->DB->simple_construct( array( "select" => 'images, comments, last_pic', 
                                          'from' => 'gallery_categories', 
                                          'where' => "id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->inputfo = $this->ipsclass->DB->fetch_row();

            $this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+{$this->ipsclass->inputfo['images']}, comments=comments+{$this->ipsclass->inputfo['comments']}, last_pic={$this->ipsclass->inputfo['last_pic']}", "id={$this->ipsclass->input['move_cat']}", 1 );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->DB->simple_delete( 'gallery_categories', "id={$this->ipsclass->input['cat']}" );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->admin->done_screen( "The category has been removed and it's contents moved to the selected category", "Invision Gallery Manager", "section=components&act=gallery" );
        }
        else
        {
            $this->ipsclass->DB->simple_construct( array( "select" => 'id, masked_file_name, thumbnail, directory', 
                                          'from' => 'gallery_images', 
                                          'where' => "category_id={$this->ipsclass->input['cat']}" ) );
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

            $this->ipsclass->DB->simple_update( 'gallery_categories', 'parent=0', "parent={$this->ipsclass->input['cat']}", 1 );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->DB->simple_delete( 'gallery_categories', "id={$this->ipsclass->input['cat']}" );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->admin->done_screen( "The category has been removed and it's contents deleted", "Invision Gallery Manager", "section=components&act=gallery" );
        }

    }

   /**
    * ad_plugin_gallery::do_empty_cat()
    * 
	* Empties a category
	* 
    * @return void
    **/
    function do_empty_cat()
    {

        // Are we moving the images?
        if( $this->ipsclass->input['move_cat'] != 'no' )
        {
            $this->ipsclass->DB->simple_update( 'gallery_images', "category_id={$this->ipsclass->input['move_cat']}", "category_id={$this->ipsclass->input['cat']}", 1 );
            $this->ipsclass->DB->simple_exec();


            $this->ipsclass->DB->simple_construct( array( "select" => 'images, comments, last_pic', 
                                          'from' => 'gallery_categories', 
                                          'where' => "id={$this->ipsclass->input['cat']}" ) );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->inputfo = $this->ipsclass->DB->fetch_row();

            $this->ipsclass->DB->simple_update( 'gallery_categories', "images=images+{$this->ipsclass->inputfo['images']}, comments=comments+{$this->ipsclass->inputfo['comments']}, last_pic={$this->ipsclass->inputfo['last_pic']}", "id={$this->ipsclass->input['move_cat']}", 1 );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->DB->simple_update( 'gallery_categories', "images=0, comments=0, last_pic=0, last_name='', last_member_id=0", "id={$this->ipsclass->input['cat']}", 1 );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->admin->done_screen( "The category has been emptied and it's contents moved to the selected category", "Invision Gallery Manager", "section=components&act=gallery" );
        }
        else
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'id, masked_file_name, medium_file_name, thumbnail, directory',
                                          'from'   => 'gallery_images',
                                          'where'  => "category_id={$this->ipsclass->input['cat']}" ) );

            $q = $this->ipsclass->DB->simple_exec();

            while( $i = $this->ipsclass->DB->fetch_row( $q ) )
            {
                $dir = ( $i['directory'] ) ? "{$i['directory']}/" : "";
                @unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.$i['masked_file_name'] );
                if( $i['thumbnail'] )
                {
                    @unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.'tn_'.$i['masked_file_name'] );
                }
                if( !empty( $i['medium_file_name'] ) )
                {
                	@unlink( $this->ipsclass->input['gallery_images_path'].'/'.$dir.'med_'.$i['masked_file_name'] );
                }
                  $this->ipsclass->DB->simple_delete( 'gallery_comments' , "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
                  $this->ipsclass->DB->simple_delete( 'gallery_bandwidth', "file_name='{$i['masked_file_name']}'" ); $this->ipsclass->DB->simple_exec();
                  $this->ipsclass->DB->simple_delete( 'gallery_ratings'  , "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
                  $this->ipsclass->DB->simple_delete( 'gallery_favorites', "img_id={$i['id']}" );                    $this->ipsclass->DB->simple_exec();
                  $this->ipsclass->DB->simple_delete( 'gallery_images'   , "id={$i['id']}" );                        $this->ipsclass->DB->simple_exec();
            }

            $this->ipsclass->DB->simple_update( 'gallery_categories', "images=0, comments=0, last_pic=0, last_name='', last_member_id=0", "id={$this->ipsclass->input['cat']}", 1 );
			$this->ipsclass->DB->simple_exec();
            
            $this->ipsclass->admin->done_screen( "The category has been emptied and it's contents deleted", "Invision Gallery Manager", "section=components&act=gallery" );
        }

    }

   /**
    * ad_plugin_gallery::cat_perm_form()
    * 
	* Displays the edit permissions form for a category
	* 
    * @return void
    **/
    function cat_perm_form()
    {
  //      require_once( ROOT_PATH.'sources/action_admin/forums.php' );
//        $forums = new ad_forums();
  //      $forums->ipsclass =& $this->ipsclass;

        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'forum_perms', 'order' => 'perm_name' ) );
	$this->ipsclass->DB->simple_exec();
		
	while( $i = $this->ipsclass->DB->fetch_row() )
	{
		$groups[$i['perm_id']] = $i['perm_name'];	
	}
        // Category Information
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
        $this->ipsclass->DB->simple_exec();

        // Page Information
        $this->ipsclass->admin->page_title   = "Category Permissions";
        $this->ipsclass->admin->page_detail  = "This is where you can edit the categories permissions";

        // Table Headers
        $this->ipsclass->adskin->td_header[] = array( "Group"           , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "View Thumbnails" , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "View Images"     , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Post Images"     , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Post Comments"   , "15%" );
        $this->ipsclass->adskin->td_header[] = array( "Moderate"        , "15%" );

        // Begin Form
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg', 'docatperms' ),
                                                  4 => array( 'code'  , 'cats' ),
                                                  2 => array( 'act' , 'gallery'    ),
                                                  5 => array( 'section', 'components' ),
                                                  3 => array( 'cat' , $this->ipsclass->input['cat']   ) ) );

         // Javascript
	$this->ipsclass->html .= "
		
				<script language='Javascript1.1'>
				<!--
				
				function checkcol(perm,status) {
				
					var f = document.theAdminForm;
					regex = new RegExp( '^' + perm );
					
					if ( status == 1 )
					{
						mystat = true;
					}
					else
					{
						mystat = false;
					}					

	   				for(i=0; i< f.elements.length; i++)
   					{
      					if(f.elements[i].type == \"checkbox\")
      					{
							if( regex.test( f.elements[i].name ) )
							{
								f.elements[i].checked = mystat;
							}
	      				}
	   				}					
					
						
				}
				
				function checkrow(groupid,status) {

					var f = document.theAdminForm;
					
					str_part = '';
					
					if ( status == 1 )
					{
						mystat = 'true';
					}
					else
					{
						mystat = 'false';
					}

					eval( 'f.thumb_'+groupid+'.checked='+mystat );
					eval( 'f.view_'+groupid+'.checked='+mystat );
					eval( 'f.image_'+groupid+'.checked='+mystat );
					eval( 'f.comment_'+groupid+'.checked='+mystat );
					eval( 'f.moderate_'+groupid+'.checked='+mystat );

				}
				
				//-->
				
				</script>
				
				";		

        // Start o' the page
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Permissions" );		
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
																		 '&nbsp;',
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"thumb\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"thumb\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"view\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"view\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"image\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"image\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"comment\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"comment\",0)' /></center>",
																		 "<center><input type='button' id='button' value='+' onclick='checkcol(\"moderate\",1)' /> <input type='button' id='button' value='-' onclick='checkcol(\"moderate\",0)' /></center>",
											 					)  );			
		
		// ----------------------------------------
		$total_rows = $this->ipsclass->DB->get_num_rows() * count( $groups );
		while( $i = $this->ipsclass->DB->fetch_row() )
		{					
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "<b>{$i['name']}</b>", "left", "tdrow3");
			
			$thumb_perms    = explode( ",", $i['perms_thumbs']   );
			$view_perms     = explode( ",", $i['perms_view']     );
			$image_perms    = explode( ",", $i['perms_images']   );
			$comment_perms  = explode( ",", $i['perms_comments'] );
			$moderate_perms = explode( ",", $i['perms_moderate'] );

			foreach( $groups as $id => $name )
			{
				$j++;
				// Initialize vars
				$thumb     = '';
				$view     = '';
				$image    = '';
				$comment  = '';
				$moderate = '';
				
				// --------------------------------------------------------
				// Figure out the permissions
				// -------------------------------------------------------

				// Thumb permissions
				if( in_array( $id, $thumb_perms ) )
				{
					$thumb = "<center id='mgyellow'><input type='checkbox' name='thumb_".$id."' value='1' checked></center>";
				}
				else
				{
					$thumb = "<center id='mgyellow'><input type='checkbox' name='thumb_".$id."' value='1'></center>";
				}
				
				// View Permissions
				if( in_array( $id, $view_perms ) )
				{
					$view = "<center id='mgblue'><input type='checkbox' name='view_".$id."' value='1' checked></center>";
				}
				else
				{
					$view = "<center id='mgblue'><input type='checkbox' name='view_".$id."' value='1'></center>";
				}
				
				// Image Permissions
				if( in_array( $id, $image_perms ) )
				{
					$image = "<center id='mgred'><input type='checkbox' name='image_".$id."' value='1' checked></center>";
				}
				else
				{
					$image = "<center id='mgred'><input type='checkbox' name='image_".$id."' value='1'></center>";
				}
				
				// Comment Permissions
				if( in_array( $id, $comment_perms ) )
				{
					$comment = "<center id='mggreen'><input type='checkbox' name='comment_".$id."' value='1' checked></center>";
				}
				else
				{
					$comment = "<center id='mggreen'><input type='checkbox' name='comment_".$id."' value='1'></center>";
				}
				
				// Moderate Permissions
				if( in_array( $id, $moderate_perms ) )
				{
					$moderate = "<center id='memgroup'><input type='checkbox' name='moderate_".$id."' value='1' checked></center>";
				}
				else
				{
					$moderate = "<center id='memgroup'><input type='checkbox' name='moderate_".$id."' value='1'></center>";
				}
				
				// Buttons
				$name = "<table border='0' width='100%'>
						   <tr>
						     <td width='50%' align='left'>$name</td>
				             <td width='50%' align='right'>
				               <input type='button' id='button' value='+' onclick='checkrow($id,1)' /> 
				               <input type='button' id='button' value='-' onclick='checkrow($id,0)' />
				             </td>
				           </tr>
				         </table>";	
				
				// Output
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
																		 $name,
																		 $thumb,
																		 $view,
																		 $image,
																		 $comment,
																		 $moderate
											 					)  );				
			}
		}
		
		// ----------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Change Permissions" );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->admin->output();


    }

   /**
    * ad_plugin_gallery::do_cat_perms()
    * 
	* Updates the permissions for a category
	* 
    * @return void
    **/
    function do_cat_perms()
    {

        $perms = $this->compile_cat_perms();

        $this->ipsclass->DB->simple_update( 'gallery_categories', "perms_thumbs='{$perms['PERMS_THUMBS']}', perms_view='{$perms['PERMS_VIEW']}', perms_images='{$perms['PERMS_IMAGES']}', perms_comments='{$perms['PERMS_COMMENTS']}', perms_moderate='{$perms['PERMS_MODERATE']}'", "id={$this->ipsclass->input['cat']}", 1 );
        $this->ipsclass->DB->simple_exec();

        $this->ipsclass->admin->done_screen( "Permissions Updated", "Gallery Manager", "section=components&act=gallery" );
    }

   /**
    * ad_plugin_gallery::compile_cat_perms()
    * 
	* Compiles the permissions to make updating the db easier
	* 
    * @return void
    **/
    function compile_cat_perms() 
    {
        $this->ipsclass->DB->simple_construct( array( 'select' => 'perm_id, perm_name', 'from' => 'forum_perms', 'order' => 'perm_id' ) );
        $this->ipsclass->DB->simple_exec();

        while ( $data = $this->ipsclass->DB->fetch_row() )
        {
            if ($this->ipsclass->input[ 'thumb_'.$data['perm_id'] ] == 1)
            {
                $r_array['PERMS_THUMBS'] .= $data['perm_id'].",";
            }

            if ($this->ipsclass->input[ 'view_'.$data['perm_id'] ] == 1)
            {
                $r_array['PERMS_VIEW'] .= $data['perm_id'].",";
            }

            if ($this->ipsclass->input[ 'image_'.$data['perm_id'] ] == 1)
            {
                $r_array['PERMS_IMAGES'] .= $data['perm_id'].",";
            }

            if ($this->ipsclass->input[ 'comment_'.$data['perm_id'] ] == 1)
            {
                $r_array['PERMS_COMMENTS'] .= $data['perm_id'].",";
            }
            if ($this->ipsclass->input[ 'moderate_'.$data['perm_id'] ] == 1)
            {
                $r_array['PERMS_MODERATE'] .= $data['perm_id'].",";
            }
        }

        $r_array['PERMS_THUMBS']   = preg_replace( "/,$/", "", $r_array['PERMS_THUMBS'] );
        $r_array['PERMS_VIEW']     = preg_replace( "/,$/", "", $r_array['PERMS_VIEW']    );
        $r_array['PERMS_IMAGES']   = preg_replace( "/,$/", "", $r_array['PERMS_IMAGES']   );
        $r_array['PERMS_COMMENTS'] = preg_replace( "/,$/", "", $r_array['PERMS_COMMENTS'] );
        $r_array['PERMS_MODERATE'] = preg_replace( "/,$/", "", $r_array['PERMS_MODERATE'] );

        return $r_array;

    }

   /**
    * ad_plugin_gallery::create_cat_form()
    * 
	* Display's the create new category form
	* 
    * @return void
    **/
    function create_cat_form()
    {

        // Page information
        $this->ipsclass->admin->page_title = "Create a new gallery category";
        $this->ipsclass->admin->page_detail = "This section will allow you to add a new category to your gallery.";

        // Start the form
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg', 'docatcreate' ),
                                                  4 => array( 'code'  , 'cats' ),
                                                  3 => array( 'section', 'components' ),
                                                  2 => array( 'act' , 'gallery' ) ) );
        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        // Start the table
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Category Information' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category Name</b>",
                                                  $this->ipsclass->adskin->form_input( "cat_name", '')
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category Description</b>",
                                                  $this->ipsclass->adskin->form_textarea( "cat_desc", '')
                                         )      );

        // Thanks Mark!
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->glib;

        $this->category->read_data( false, 'Do not make this a sub category' );
        $this->category->current = $this->ipsclass->input['parent'];

        $sub = $this->category->build_dropdown( 'parent' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Make this a sub category?</b>",
                                                  $sub . "<br><br><b><small>Inherit permissions/settings from parent?</b> " . $this->ipsclass->adskin->form_yes_no( 'inherit' ) . "</small>"
                                         )      );

        // End the table
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Create Category" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table( "Create Category" );

        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::create_cat()
    * 
	* Creates a new category
	* 
    * @return void
    **/
    function create_cat()
    {

        $this->ipsclass->input['cat_name'] = trim( $this->ipsclass->input['cat_name'] );

        if( $this->ipsclass->input['cat_name'] == "" )
        {
            $this->ipsclass->admin->error( "You must enter a category title" );
        }
        
        $this->ipsclass->input['parent'] = ( $this->ipsclass->input['parent'] ) ? $this->ipsclass->input['parent'] : 0;

        $this->ipsclass->DB->simple_construct( array( 'select' => 'count(id) as total', 'from' => 'gallery_categories', 'where' => "parent={$this->ipsclass->input['parent']}" ) );
        $this->ipsclass->DB->simple_exec();

        $i = $this->ipsclass->DB->fetch_row();
        $i['total']++;

        $insert = array (
                            'name'        => $this->ipsclass->input['cat_name'],
                            'description' => $this->ipsclass->input['cat_desc'],
                            'c_order'     => $i['total'],
                            'parent'      => $this->ipsclass->input['parent'],
                        );

        if( $this->ipsclass->input['parent'] && $this->ipsclass->input['inherit'] )
        {
            $this->ipsclass->DB->simple_construct( array( 'select' => 'perms_thumbs, perms_view, perms_moderate, perms_images, perms_comments, allow_ibfcode,
                                    password, approve_images, imgs_per_col, imgs_per_row, watermark_images,
                                    thumbnail, allow_comments, approve_comments, inc_post_count, def_view', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['parent']}" ) );
            $this->ipsclass->DB->simple_exec();

            $insert = array_merge( $insert, $this->ipsclass->DB->fetch_row() );
        }

        $this->ipsclass->DB->do_insert( 'gallery_categories', $insert );
		$link = "{$this->ipsclass->base_url}&section=components&act=gallery&code=cats&pg=permform&cat=".$this->ipsclass->DB->get_insert_id();

        $this->ipsclass->admin->save_log( "Added Gallery Category '{$this->ipsclass->input['cat_name']}'");
        $this->ipsclass->admin->redirect( $link, "Gallery Category {$this->ipsclass->input['cat_name']} created", "Gallery Manager", "section=components&act=gallery" );

    }
    
    function precat_form()
    {
		
		/* Page Setup */
		$this->ipsclass->admin->page_title = "Create new predefined category";
		
		$this->ipsclass->admin->page_detail = "Predefined categories can not be posted directly too, they are only for displaying images based on
		                                 the specified settings.";
		                                 
		$this->ipsclass->adskin->td_header[] = array( "{none}", "25%" );		
		$this->ipsclass->adskin->td_header[] = array( "{none}"  , "75%" );		                                 
		                                 
		/* Category Information Table */
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Category Information" );
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "General information about this category", "left", "catrow2");
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category Name</b>",
                                                  $this->ipsclass->adskin->form_input( "cat_name", $cat['name'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category Description</b>",
                                                  $this->ipsclass->adskin->form_textarea( "cat_desc", $cat['description'] )
                                         )      );
                                         
        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->glib;

        $this->category->read_data( false, 'Do not make this a sub category' );
        $this->category->current = $cat['parent'];
        $sub = $this->category->build_dropdown( 'parent' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Make this a sub category?</b>",
                                                  $sub
                                         )      );                                         		
		
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		/* Image Location Table */
		$this->ipsclass->adskin->td_header[] = array( "{none}", "25%" );		
		$this->ipsclass->adskin->td_header[] = array( "{none}"  , "75%" );			
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Image Location Settings" );
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "Sets where images will be pulled from for this category", "left", "catrow2");	
		
		$options = $this->category->build_dropdown();
        $options = str_replace( "Do not make this a sub category", "Do not pull images from categories", $options );		
        $cat_select = "<select name='cats[]' class='dropdown' multiple='multiple' size='10'>{$options}</select>";
        
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Categories</b>',
                                                  $cat_select,
                                          )      );
                                          
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
                                                  '<b>Pull images from albums?</b>',
                                                  $this->ipsclass->adskin->form_yes_no( 'album', 1 ),
                                          )      );                                             
		
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();	
		
		/* Display Settings Table */
		$this->ipsclass->adskin->td_header[] = array( "{none}", "25%" );		
		$this->ipsclass->adskin->td_header[] = array( "{none}"  , "75%" );			
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Display Settings" );
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "Control how the images will be displayed", "left", "catrow2");
 
        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Uploads to show per column?</b>",
                                                  $this->ipsclass->adskin->form_input( "img_col", $cat['imgs_per_col'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Uploads to show per row?</b>",
                                                  $this->ipsclass->adskin->form_input( "img_row", $cat['imgs_per_row'] )
                                         )      );

        
        $sort_options = array(
                               array( 'caption' , 'Caption' ),
                               array( 'name'    , 'Member' ),
                               array( 'date'    , 'Uploaded Date' ),
                               array( 'views'   , 'Views' ),
                               array( 'comments','Comments' ),
                             );

        $order_options = array(
                                array( 'ASC' , 'Ascending (a-z)'  ),
                                array( 'DESC', 'Descending (z-a)' ),
                               );

         $prune_options = array(
                                array( '1' , 'the last 24 hours' ),
                                array( '5' , 'the last 5 days' ),
                                array( '7' , 'the last week' ),
                                array( '10', 'the last 10 days' ),
                                array( '15', 'the last 15 days' ),
                                array( '20', 'the last 20 days' ),
                                array( '25', 'the last 25 days' ),
                                array( '30', 'the last 30 days' ),
                                array( '60', 'the last 60 days' ),
                                array( '90', 'the last 90 days' ),
                                array( '*' , 'the beginning' ),
                              );

        $def_views = explode( ":", $cat['def_view'] );
        $sort  = $def_views[0];
        $order = $def_views[1];
        $prune = $def_views[2];

        $def_view_field = "Sort by " . $this->ipsclass->adskin->form_dropdown( 'view_sort', $sort_options, $sort ) .
                          " in " . $this->ipsclass->adskin->form_dropdown( 'view_order', $order_options, $order ) .
                          "  from " . $this->ipsclass->adskin->form_dropdown( 'view_prune', $prune_options, $prune );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Default Sorting/Pruning</b>",
                                                  $def_view_field
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
			                                 
		                                 
		
		/* Page Output */    
		$this->ipsclass->admin->output();
    	
    		
    }    

   /**
    * ad_plugin_gallery::edit_cat_form()
    * 
	* Display's the edit category form
	* 
    * @return void
    **/
    function edit_cat_form()
    {

        // Get Gallery Info
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_categories', 'where' => "id={$this->ipsclass->input['cat']}" ) );
        $this->ipsclass->DB->simple_exec();

        $cat = $this->ipsclass->DB->fetch_row();

        // Page information
        $this->ipsclass->admin->page_title = "Edit '{$cat['name']}' gallery category";
        $this->ipsclass->admin->page_detail = "This section will allow you to edit a category in your gallery.";

        // Start the form
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg', 'docatedit' ),
                                                  4 => array( 'code'  , 'cats' ),
                                                  2 => array( 'act' , 'gallery' ),
                                                  5 => array( 'section', 'components' ),
                                                  3 => array( 'cat' , $this->ipsclass->input['cat'] )) );
        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        // Info Table
        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Category Information' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category Name</b>",
                                                  $this->ipsclass->adskin->form_input( "cat_name", $cat['name'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category Description</b>",
                                                  $this->ipsclass->adskin->form_textarea( "cat_desc", $this->ipsclass->my_br2nl( $cat['description'] ) )
                                         )      );

        require( ROOT_PATH . 'modules/gallery/categories.php' );
        $this->category = new Categories;
        $this->category->ipsclass =& $this->ipsclass;
        $this->category->glib =& $this->glib;

        $this->category->read_data( false, 'Do not make this a sub category' );
        $this->category->current = $cat['parent'];
        $sub = $this->category->build_dropdown( 'parent' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Make this a sub category?</b>",
                                                  $sub
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Category only mode?</b><BR>If set to yes, gallery will only display sub categories of this category.  Images will not be able to be posted here.",
                                                  $this->ipsclass->adskin->form_yes_no( "category_only", $cat['category_only'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // Settings Table

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Category Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow IBF CODE to be posted?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_ibfcode", $cat['allow_ibfcode'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow HTML CODE to be posted?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_htmlcode", $cat['allow_html'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Password Protect?</b><BR>If you wish to require users to enter a password before entering this category, then enter the password here.",
                                                  $this->ipsclass->adskin->form_input( "password", $cat['password'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Approve new uploads?</b><BR>If you want to look over uploads before your members do, then select this option.  Uploads will not be displayed until you approve them.",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_approve_images", $cat['approve_images'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow ratings?</b><BR>If you want your users to be able to rate images in this category, then set this to yes.",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_rate", $cat['rate'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Album Mode?</b><BR>This will allow users to create albums within this category.  In this mode users will not be able to post directly to the category, but only within the albums.",
                                                  $this->ipsclass->adskin->form_yes_no( "album_mode", $cat['album_mode'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // Display Table
        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Display Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Uploads to show per column?</b>",
                                                  $this->ipsclass->adskin->form_input( "img_col", $cat['imgs_per_col'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Uploads to show per row?</b>",
                                                  $this->ipsclass->adskin->form_input( "img_row", $cat['imgs_per_row'] )
                                         )      );

        
        $sort_options = array(
                               array( 'caption' , 'Caption' ),
                               array( 'name'    , 'Member' ),
                               array( 'date'    , 'Uploaded Date' ),
                               array( 'views'   , 'Views' ),
                               array( 'comments','Comments' ),
                               array( 'rating', 'Rating' ),
                             );

        $order_options = array(
                                array( 'ASC' , 'Ascending (a-z)'  ),
                                array( 'DESC', 'Descending (z-a)' ),
                               );

         $prune_options = array(
                                array( '1' , 'the last 24 hours' ),
                                array( '5' , 'the last 5 days' ),
                                array( '7' , 'the last week' ),
                                array( '10', 'the last 10 days' ),
                                array( '15', 'the last 15 days' ),
                                array( '20', 'the last 20 days' ),
                                array( '25', 'the last 25 days' ),
                                array( '30', 'the last 30 days' ),
                                array( '60', 'the last 60 days' ),
                                array( '90', 'the last 90 days' ),
                                array( '*' , 'the beginning' ),
                              );

        $def_views = explode( ":", $cat['def_view'] );
        $sort  = $def_views[0];
        $order = $def_views[1];
        $prune = $def_views[2];

        $def_view_field = "Sort by " . $this->ipsclass->adskin->form_dropdown( 'view_sort', $sort_options, $sort ) .
                          " in " . $this->ipsclass->adskin->form_dropdown( 'view_order', $order_options, $order ) .
                          "  from " . $this->ipsclass->adskin->form_dropdown( 'view_prune', $prune_options, $prune );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Default Sorting/Pruning</b>",
                                                  $def_view_field
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // Image Table

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Image Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow new images?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_allow_images", $cat['allow_images'] )
                                         )      );
        
        // Is watermarking enabled?
        if( $this->ipsclass->vars['gallery_watermark_path'] )
        {
            $watermark = $this->ipsclass->adskin->form_yes_no( "img_watermark", $cat['watermark_images'] );
        }
        else
        {        
            $watermark = "<i>This option is disabled, as you have not specified a watermark image.  <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=overview&pg=settings'>Click here</a> to configure your watermark settings.</i>";
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Watermark images in this category?</b>",
                                                          $watermark
                                         )      );

        // Is thumbnailing enabled?
        if( $this->ipsclass->vars['gallery_create_thumbs'] && ( $this->ipsclass->vars['gallery_thumb_width'] || $this->ipsclass->vars['gallery_thumb_height'] ) )
        {
            $thumbs = $this->ipsclass->adskin->form_yes_no( "img_thumbs", $cat['thumbnail'] );
        }
        else
        {        
            $thumbs = "<i>This option is disabled, you either have turned off thumbnailing or have not specified a thumbnail dimension..  <a href='{$this->ipsclass->base_url}&act=op&code=setting_view&search=thumbnail'>Click here</a> to configure your thumbnail settings.</i>";
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Create thumbnails for images posted in this category?</b>",
                                                  $thumbs
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Multimedia Settings' );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow new multimedia uploads?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_allow_movies", $cat['allow_movies'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // Comment Settings Table

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Comment Settings' );

           $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Allow new comments?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_allow_comments", $cat['allow_comments'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Approve new comments?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_approve_comments", $cat['approve_comments'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Comments in this category increase member's cumulative post count?</b>",
                                                  $this->ipsclass->adskin->form_yes_no( "cat_inc_post_count", $cat['inc_post_count'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        
        // Category Rules Settings Table

        // Table Cell Sizes
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "30%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Category Rules' );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Display method</b>" ,
																 $this->ipsclass->adskin->form_dropdown( "cat_rule_method",
																					   array( 
																							   0 => array( '0' , 'Don\'t Show' ),
																							  // 1 => array( '1' , 'Show Link Only' ),
																							   2 => array( '2' , 'Show full text' )
																							),
																					   $cat['cat_rule_method']
																					 )
														)      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Rules Title</b>",
                                                  $this->ipsclass->adskin->form_input( "cat_rule_title", $cat['cat_rule_title'] )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Rules Text</b>",
                                                  $this->ipsclass->adskin->form_textarea( "cat_rule_text", $this->ipsclass->my_br2nl( $cat['cat_rule_text'] ) )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();        


        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( 'Save Changes' );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Save Changes" );
        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::edit_cat()
    * 
	* Process the edit category form and updates the category
	* 
    * @return void
    **/
    function edit_cat()
    {

        $this->ipsclass->input['cat_name'] = trim( $this->ipsclass->input['cat_name'] );

        if( $this->ipsclass->input['cat_name'] == "" )
        {
            $this->ipsclass->admin->error( "You must enter a category title" );
        }

        if( $this->ipsclass->input['parent'] == $this->ipsclass->input['cat'] )
        {
            $this->ipsclass->admin->error( "You can not make a category a parent of itself, such things make the gallery very unhappy" );
        }

        $this->ipsclass->DB_arr =  array (
                                                            'name'             => $this->ipsclass->input['cat_name'],
                                                            'description'      => $this->ipsclass->input['cat_desc'],
                                                            'allow_ibfcode'    => $this->ipsclass->input['cat_ibfcode'],
                                                            'allow_html'       => $this->ipsclass->input['cat_htmlcode'],
                                                            'password'         => $this->ipsclass->input['password'],
                                                            'imgs_per_col'     => $this->ipsclass->input['img_col'],
                                                            'imgs_per_row'     => $this->ipsclass->input['img_row'],
                                                            'watermark_images' => $this->ipsclass->input['img_watermark'],
                                                            'thumbnail'        => $this->ipsclass->input['img_thumbs'],
                                                            'allow_comments'   => $this->ipsclass->input['cat_allow_comments'],
                                                            'approve_comments' => $this->ipsclass->input['cat_approve_comments'],
                                                            'inc_post_count'   => $this->ipsclass->input['cat_inc_post_count'],
                                                            'approve_images'   => $this->ipsclass->input['cat_approve_images'],
                                                            'parent'           => $this->ipsclass->input['parent'],
                                                            'def_view'         => "{$this->ipsclass->input['view_sort']}:{$this->ipsclass->input['view_order']}:{$this->ipsclass->input['view_prune']}",
                                                            'allow_images'     => $this->ipsclass->input['cat_allow_images'],
                                                            'allow_movies'     => $this->ipsclass->input['cat_allow_movies'],
                                                            'rate'             => $this->ipsclass->input['cat_rate'],
                                                            'album_mode'       => $this->ipsclass->input['album_mode'],
                                                            'category_only'    => $this->ipsclass->input['category_only'],
                                                            'cat_rule_method'  => $this->ipsclass->input['cat_rule_method'],
                                                            'cat_rule_title'   => $this->ipsclass->input['cat_rule_title'],
                                                            'cat_rule_text'    => $this->ipsclass->input['cat_rule_text'],
                            );
        $this->ipsclass->DB->do_update( 'gallery_categories', $this->ipsclass->DB_arr, "id={$this->ipsclass->input['cat']}");

        $this->ipsclass->admin->save_log("Edited Gallery Category '{$this->ipsclass->input['cat_name']}'");
        $this->ipsclass->admin->done_screen("Gallery Category '{$this->ipsclass->input['cat_name']}' was modified", "Gallery Manager", "section=components&act=gallery" );

    }

}
?>
