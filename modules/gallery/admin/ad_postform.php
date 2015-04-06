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
* Admin/Post Form
*
* Allows admins to control what shows
* up on image upload form
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
            default:
                $this->post_form();
            break;

        }


    }

   /******************************************************************
     *
     * Post Form Editor
     *
     **/
    
   /**
    * ad_plugin_gallery::post_form()
    * 
	* This method controls the post form editor feature
	* 
    * @return void
    **/
    function post_form()
    {
        // ---------------------------------------------------
        // Check to see if any processing is needed
        // ---------------------------------------------------
        switch( $this->ipsclass->input['op'] )
        {
            case 'up':
                $this->field_up( $this->ipsclass->input['id'] );
            break;

            case 'down':
                $this->field_down( $this->ipsclass->input['id'] );
            break;

            case 'addfield':
                $this->add_field();
            break;

            case 'editfields':
                $this->edit_fields();
            break;

            case 'del':
                $this->del_field();
            break;
        }

        $this->ipsclass->admin->page_title = "Edit Posting Form";

        $this->ipsclass->admin->page_detail = "This is where you can create and modify the fields for image posting.<BR><BR>Entries that are marked in <font color='red'>red</font> are special fields and can no be deleted or edited.  They may also have additional elements included, such as code buttons for the description field.";

        // ---------------------------------------------------
        // Display current entries and edit stuff
        // ---------------------------------------------------
        $this->ipsclass->adskin->td_header[] = array( "Order"          , "8%" );
        $this->ipsclass->adskin->td_header[] = array( "Title"          , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Description"    , "22%" );
        $this->ipsclass->adskin->td_header[] = array( "Type"           , "16%" );
        $this->ipsclass->adskin->td_header[] = array( "Values"         , "18%" );
        $this->ipsclass->adskin->td_header[] = array( "Required"       , "16%" );

        //+-------------------------------


        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Current Post Form Entries" );

        $real_types = array( 'drop' => 'Drop Down Box',
                             'area' => 'Text Area',
                             'text' => 'Text Input',
                             'date' => 'Date Field',
                             'file' => 'File Field',
                             'sepr' => 'Seperator',
                           );

        $types = array( 1 => array( 'drop', 'Drop Down Box' ),
                        2 => array( 'area', 'Text Area' ),
                        3 => array( 'text', 'Text Input' ),
                        4 => array( 'date', 'Date Field' ),
                        5 => array( 'sepr', 'Seperator' ),
                      );

        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'gallery_form_fields', 'order' => 'position' ) );
        $this->ipsclass->DB->simple_exec();

        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg'  , 'postform'  ),
                                                  4 => array( 'code', 'postform' ),
                                                  2 => array( 'act'   , 'gallery'    ),
                                                  3 => array( 'op'    , 'editfields' ),
                                                  5 => array( 'section', 'components' ),
                                         )  );

        if ( $this->ipsclass->DB->get_num_rows() )
        {
            while ( $r = $this->ipsclass->DB->fetch_row($q) )
            {
                $values = str_replace( "|", "<BR>", $r['content'] );
                $values = ( $values ) ? $values : '&nbsp;';

                $r['description'] = ( $r['description'] ) ? $r['description'] : '&nbsp;';

                if( $r['id'] <= 3 )
                {
                    $name_col = "<b><font color='red'>{$r['name']}</font></b>";
                    $desc_col = "<b><font color='red'>{$r['description']}</font></b>";
                    $type_col = "<center>{$real_types[$r['type']]}</center>";
                    $val_col  = "<center>{$values}</center>";
                    $req_col  = ( $r['required'] ) ? '<center><b>Yes</b></center>' : '<center><b>No</b></center>';
                    $del = '';
                    $tag = '';
                }
                else
                {
                    $r['content'] = str_replace( '|', "\n", $r['content'] );

                    $name_col = $this->ipsclass->adskin->form_input( "name[{$r['id']}]"           , $r['name'] );
                    $desc_col = $this->ipsclass->adskin->form_input( "description[{$r['id']}]"    , $r['description'] );
                    $type_col = $this->ipsclass->adskin->form_dropdown( "type[{$r['id']}]"        , $types, $r['type'] );
                    $val_col  = $this->ipsclass->adskin->form_textarea( "content[{$r['id']}]"     , $r['content'], 20, 2 );
                    $req_col  = $this->ipsclass->adskin->form_yes_no( "required[{$r['id']}]"      , $r['required'] );
                    $del      = "<BR><BR><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=postform&pg=postform&op=del&id={$r['id']}' class='fauxbutton'>Delete</a>";
                    $tag      = ( $r['type'] != 'sepr') ? '<BR>Refer to as <b>{$info[\'field_'.$r['id'].'\']}</b> in skin' : '';
                }

                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=postform&pg=postform&op=up&id={$r['id']}' class='fauxbutton'>&#8657;</a><a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=postform&pg=postform&op=down&id={$r['id']}' class='fauxbutton'>&#8659;</a>{$del}",
                                                          $name_col.$tag,
                                                          $desc_col,
                                                          $type_col,
                                                          $val_col,
                                                          $req_col,
                                             )      );

            }
        }
        else
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( "None found", "center", "pformstrip");
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( "Edit Field(s)" );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        // ---------------------------------------------------
        // Let's do the add new entry form
        // ---------------------------------------------------
        $this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'pg'  , 'postform'  ),
                                                  4 => array( 'code', 'postform' ),
                                                  2 => array( 'act'   , 'gallery'    ),
                                                  3 => array( 'op'    , 'addfield' ),
                                                  5 => array( 'section', 'components' ),
                                         )  );

        //+-------------------------------

        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
        $this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

        //+-------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Create a new entry" );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Field Title</b><br>Max characters: 60" ,
                                                  $this->ipsclass->adskin->form_input( "title", '' )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Field Description</b><br>Max characters: 120" ,
                                                  $this->ipsclass->adskin->form_input( "description", '' )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Field Type</b>" ,
                                                  $this->ipsclass->adskin->form_dropdown( "type", $types, 'text' )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Field Values (for drop downs and check boxes)</b><br>In sets, one set per line<br>Example for 'Gender' field:<br>m=Male<br>f=Female<br>u=Not Telling<br>Will produce:<br><select name='pants'><option value='m'>Male</option><option value='f'>Female</option><option value='u'>Not Telling</option></select><br>m,f or u stored in database. When showing field in profile, will use value from pair (f=Female, shows 'Female')" ,
                                                  $this->ipsclass->adskin->form_textarea( "content", '' )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Required Field</b><BR>If set to yes, then the user will not be allowed to leave this field blank" ,
                                                  $this->ipsclass->adskin->form_yes_no( "required", 1 )
                                         )      );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_form( 'Add this Field' );

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();



        $this->ipsclass->admin->output();

    }

   /**
    * ad_plugin_gallery::del_field()
    * 
	* Deletes a field
	* 
    * @return void
    **/
    function del_field()
    {        
        $this->ipsclass->DB->simple_construct( array( 'select' => 'type', 'from' => 'gallery_form_fields', 'where' => "id={$this->ipsclass->input['id']}" ) );
        $this->ipsclass->DB->simple_exec();

        $i = $this->ipsclass->DB->fetch_row();

        // Move the field down so we can preserve ordering
        while( $this->field_down( $this->ipsclass->input['id'] ) );

        $this->ipsclass->DB->simple_delete( 'gallery_form_fields', "id={$this->ipsclass->input['id']}" );
        $this->ipsclass->DB->simple_exec();

        if( $i['type'] != 'sepr' )
        {
            $this->ipsclass->DB->cache_add_query( 'postform_drop', array( 'id' => $this->ipsclass->input['id'] ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

        }
    }

   /**
    * ad_plugin_gallery::edit_fields()
    * 
	* Process a edit fields request
	* 
    * @return void
    **/
    function edit_fields()
    {
        // ---------------------------------------------------
        // We need to rearrange the data a bit before doing anything else
        // ---------------------------------------------------
        foreach( $this->ipsclass->input['name'] as $id => $value )
        {
            $update[$id]['name'] = $value;
        }

        foreach( $this->ipsclass->input['description'] as $id => $value )
        {
            $update[$id]['description'] = $value;
        }

        foreach( $this->ipsclass->input['type'] as $id => $value )
        {
            $update[$id]['type'] = $value;
        }

        foreach( $this->ipsclass->input['content'] as $id => $value )
        {
            if ($this->ipsclass->input['content'][$id] != "")
            {
                $value = str_replace( "\n", '|', str_replace( "\n\n", "\n", trim( $this->ipsclass->input['content'][$id] ) ) );
            }

            $update[$id]['content'] = $value;
        }

        foreach( $this->ipsclass->input['required'] as $id => $value )
        {
            $update[$id]['required'] = $value;
        }

        // ---------------------------------------------------
        // Now we can do the editing
        // ---------------------------------------------------

        foreach( $update as $id => $fields )
        {
            $this->ipsclass->DB->do_update( 'gallery_form_fields', $fields, "id={$id}" );
        }

    }

   /**
    * ad_plugin_gallery::add_field()
    * 
	* Adds a new custom field to the post form
	* 
    * @return void
    **/
    function add_field()
    {
        if( empty( $this->ipsclass->input['title'] ) )
        {
            $this->ipsclass->admin->error( "You did not enter a title" );
        }

        if( $_POST['content'] != "" )
        {
            $content = str_replace( "\n", '|', str_replace( "\n\n", "\n", trim( $_POST['content'] ) ) );
        }

        $this->ipsclass->DB->simple_construct( array( 'select' => 'MAX( position ) AS top', 'from' => 'gallery_form_fields' ) );
        $this->ipsclass->DB->simple_exec();

        $top = $this->ipsclass->DB->fetch_row();
        $position = $top['top'] + 1;

        $insert = array( 'name'        => $this->ipsclass->input['title'],
                         'description' => $this->ipsclass->input['description'],
                         'type'        => $this->ipsclass->input['type'],
                         'content'     => $content,
                         'position'    => $position,
                         'required'    => $this->ipsclass->input['required'],
                         'deleteable'   => 1 );

        $this->ipsclass->DB->do_insert( 'gallery_form_fields', $insert );

        $new_id = $this->ipsclass->DB->get_insert_id();

        if( $this->ipsclass->input['type'] != 'sepr' )
        {
            $this->ipsclass->DB->cache_add_query( 'postform_add', array( 'new_id' => $new_id ), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();
        }

    }

   /**
    * ad_plugin_gallery::field_down()
    * 
	* Moves a custom field up the form
	* 
    * @param integer $fid
    * @return void
    **/
    function field_down( $fid )
    {
        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, position', 'from' => 'gallery_form_fields', 'where' => "id={$fid}" ) );
        $this->ipsclass->DB->simple_exec();

        $down = $this->ipsclass->DB->fetch_row();

        $id = $down['position'] + 1;

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, position', 'from' => 'gallery_form_fields', 'where' => "position={$id}" ) );
        $this->ipsclass->DB->simple_exec();        

        if( $this->ipsclass->DB->get_num_rows() )
        {
            $up = $this->ipsclass->DB->fetch_row();
            $this->ipsclass->DB->simple_update( 'gallery_form_fields', "position={$up['position']}", "id={$down['id']}", 1 );
            $this->ipsclass->DB->simple_exec();
            
            $this->ipsclass->DB->simple_update( 'gallery_form_fields', "position={$down['position']}", "id={$up['id']}", 1 );
            $this->ipsclass->DB->simple_exec();

            return true;
        }
        return false;
    }

   /**
    * ad_plugin_gallery::field_up()
    * 
	* Moves a custom field down the form
	* 
    * @param integer $fid
    * @return void
    **/
    function field_up( $fid )
    {
        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, position', 'from' => 'gallery_form_fields', 'where' => "id={$fid}" ) );
        $this->ipsclass->DB->simple_exec();

        $down = $this->ipsclass->DB->fetch_row();

        $id = $down['position'] - 1;

        $this->ipsclass->DB->simple_construct( array( 'select' => 'id, position', 'from' => 'gallery_form_fields', 'where' => "position={$id}" ) );
        $this->ipsclass->DB->simple_exec();        

        if( $this->ipsclass->DB->get_num_rows() )
        {
            $up = $this->ipsclass->DB->fetch_row();

            $this->ipsclass->DB->simple_update( 'gallery_form_fields', "position={$up['position']}", "id={$down['id']}", 1 );
            $this->ipsclass->DB->simple_exec();

            $this->ipsclass->DB->simple_update( 'gallery_form_fields', "position={$down['position']}", "id={$up['id']}", 1 );
            $this->ipsclass->DB->simple_exec();
            return true;
        }
        return false;
    }
}
?>
