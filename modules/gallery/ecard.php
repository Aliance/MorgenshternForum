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
* Main/Ecard
*
* Manages, sends, creates eCards
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

    class ecard
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;
        var $post_html;
        var $parser;
        var $title;
        var $nav;

        var $data;
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
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_img'] ) ) {
            	$this->ipsclass->load_template('skin_gallery_img');
            }
            
            /* Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_post'] ) ) {
            	$this->ipsclass->load_template('skin_gallery_post');
            }

            $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_img' ];
            $this->post_html = $this->ipsclass->compiled_templates[ 'skin_gallery_post' ];

			/* 
			* Load additional language ( bug fix ) */
			$this->ipsclass->load_language( 'lang_post' );

			require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
			$this->parser                      =  new parse_bbcode();
			$this->parser->ipsclass            =& $this->ipsclass;
			$this->parser->allow_update_caches = 1;
			
			$this->parser->parse_html    = 1;
			$this->parser->parse_nl2br   = 1;
			$this->parser->parse_smilies = 1;
			$this->parser->parse_bbcode  = 1;
            
            //-----------------------------------------
			// Load and config the std/rte editors
			//-----------------------------------------
			
			require_once( ROOT_PATH."sources/handlers/han_editor.php" );
			$this->han_editor           = new han_editor();
			$this->han_editor->ipsclass =& $this->ipsclass;
			$this->han_editor->init();
        
            // -------------------------------------------------------
            // Check Auth
            // -------------------------------------------------------
            if( $this->ipsclass->member['id'] )
            {
	            $perms = explode( ':', $this->ipsclass->member['gallery_perms'] );            
    	        if( ! $perms[0] )
        	    {
            	    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            	}
            }
            else
            {
            	if( ! $this->ipsclass->vars['gallery_guests_ecards'] )
            	{
            	    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );            		
            	}
            }

            // -------------------------------------------------------
            // Security Checks
            // -------------------------------------------------------        
            if( ! $this->ipsclass->vars['gallery_use_ecards'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }

            if( ! $this->ipsclass->member['g_ecard'] )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
            }

            // -------------------------------------------------------
            // Image Info
            // -------------------------------------------------------
            
            // Check input
            if( $param != 'getcard' )
            {
	            $this->ipsclass->input['img'] = intval( $this->ipsclass->input['img'] );
    	        if( ! $this->ipsclass->input['img'] && $param != "colorpicker" )
    	        {
    	            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	        }
    	       
            
	            $this->ipsclass->DB->simple_construct( array( 'select' => 'caption, category_id, masked_file_name, file_type, id, directory', 'from' => 'gallery_images', 'where' => "id={$this->ipsclass->input['img']}" ) );
    	        $this->ipsclass->DB->simple_exec();
    	        $this->data = $this->ipsclass->DB->fetch_row();
    	    }
    	    else
    	    {
    	    	$this->ipsclass->input['card'] = intval( $this->ipsclass->input['card'] );
    	        if( ! $this->ipsclass->input['card'] )
    	        {
    	            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    	        }
    	    }

            // -------------------------------------------------------
            // What's our entry point?
            // -------------------------------------------------------    
            switch( $param )
            {
            	case 'getcard':
            		$this->ecard_display();
            	break;
            	
                case 'ecardpreview':
                    $this->ecard_preview( $img );
                break;

                case 'sendecard':
                    $this->send_ecard( $img );
                break;
                
                case 'form':
                default:
                    $this->ecard_form( $img );
            }

            $this->ipsclass->print->add_output( $this->output );

            $this->ipsclass->print->do_output( array( 
                                      'TITLE'    => $this->title,
            					 	  'NAV'      => $this->nav,
                             )       );
        }
        
        /**
         * ecard::ecard_display()
         * 
		 * Displays the specified E-Card
		 * 
         * @return none
         **/
        function ecard_display()
        {
            // -------------------------------------------------------
            // Category/Album Info
            // -------------------------------------------------------        
            if( $this->data['category_id'] )
            {
                $this->ipsclass->DB->simple_construct( array( 'select' => 'id, password, perms_view', 'from' => 'gallery_categories', 'where' => "id={$this->data['category_id']}" ) );
                $this->ipsclass->DB->simple_exec();
                $cat = $this->ipsclass->DB->fetch_row();

                // -------------------------------------------------------
                // Check Permissions
                // -------------------------------------------------------
                $this->glib->check_cat_auth( $cat['id'], $cat['password'], $cat['perms_view'] );
            }
            
            $this->ipsclass->DB->simple_construct( array( 'select' => '*',
                                          'from'   => 'gallery_ecardlog',
                                          'where'  => "id={$this->ipsclass->input['card']}" ) );                                          
			$this->ipsclass->DB->simple_exec();
			
			// Did we find a card?
			if( ! ( $card = $this->ipsclass->DB->fetch_row() ) )
			{
   	            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );			
			}
			
			// Is the access has valid?
			if( $this->ipsclass->input['access'] !=  md5( $card['img_id'] . $card['member_id'] . $card['date'] ) )
			{
   	            $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );					
			}
			
			// Get the image info
            $this->ipsclass->DB->simple_construct( array( 'select' => 'caption, category_id, masked_file_name, file_type, id, directory', 'from' => 'gallery_images', 'where' => "id={$card['img_id']}" ) );
   	        $this->ipsclass->DB->simple_exec();   	      
   	        $img = $this->ipsclass->DB->fetch_row();
   	        
   	        // Get Sender Name
   	        $this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$card['member_id']}" ) );
   	        $this->ipsclass->DB->simple_exec();
   	        $name = $this->ipsclass->DB->fetch_row();
   	        
   	        // Foramt send line
   	        $send_line = $this->ipsclass->lang['ecard_send_line'];
   	        $send_line = str_replace( "<#name#>", $this->ipsclass->make_profile_link( $name['name'], $card['member_id'] ), $send_line );
   	        $send_line = str_replace( "<#time#>", $this->ipsclass->get_date( $card['date'], 'LONG' ), $send_line );

            $this->output .= $this->html->ecard_preview(
                                                   		array( 'lang'    => $this->ipsclass->lang['ecard_view'],
															   'image'   => $this->glib->make_image_link( $img ),
															   'font'    => $card['font'],
															   'bg'      => $card['bg'],
															   'border'  => $card['border'],
															   'title'   => $card['title'],
															   'msg'     => $card['msg'],
															   'caption' => $img['caption'],
															   'sender'  => $send_line,
															 )
                                                       );

            // -------------------------------------------------------
            // Page Stuff
            // -------------------------------------------------------
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['ecard_view'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav[] = $this->ipsclass->lang['ecard_view'];
        }        

        /**
         * ecard::send_ecard()
         * 
		 * Send's the E-Card to the specified email address and logs
		 * the E-Card.
		 * 
         * @param integer $img
         * @return none
         **/
        function send_ecard( $img )
        {
            // -------------------------------------------------------
            // Load the emailer
            // -------------------------------------------------------        
            require KERNEL_PATH . "class_email.php";		
		    $email = new class_email();
		    $email->html_email = 1;
		    $email->ipsclass = &$this->ipsclass;

		    /**
		    * Set other options
		    **/
		    if( $this->ipsclass->vars['mail_method'] == 'smtp' )
		    {
		    	/**
		    	* Set host, user, etc 
		    	**/
		    	$email->smtp_host = $this->ipsclass->vars['smtp_host'];
		    	$email->smtp_port = $this->ipsclass->vars['smtp_port'];
		    	$email->smtp_user = $this->ipsclass->vars['smtp_user'];
		    	$email->smtp_pass = $this->ipsclass->vars['smtp_pass'];
		    	$email->wrap_brackets = $this->ipsclass->vars['mail_wrap_brackets'];
		    	$email->extra_opts    = $this->ipsclass->vars['php_mail_extra'];
		    	
		    	/**
		    	* Set method
		    	**/
		    	$email->mail_method = $this->ipsclass->vars['mail_method'];
		    }
		    
            // -------------------------------------------------------
            // Error Checking
            // -------------------------------------------------------        
            if( empty( $this->ipsclass->input['receiver_name'] ) )
            {           
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_name' ) );
            }

            if( ! $this->ipsclass->clean_email( $this->ipsclass->input['receiver_email'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_email' ) );
            }

            if( empty( $this->ipsclass->input['subject'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_subject' ) );
            }

            if( empty( $this->ipsclass->input['Post'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_msg' ) );
            }

            // -------------------------------------------------------
            // Setup sender/receiver
            // -------------------------------------------------------                        
            $email->to   = $this->ipsclass->input['receiver_email'];
            $email->from = ( !$this->ipsclass->member['id'] ) ? $this->ipsclass->vars['email_out'] : $this->ipsclass->member['email'];
			
			$this->ipsclass->input['Post'] = $this->han_editor->process_raw_post( 'Post' );
			
            $this->data['msg'] = $this->parser->pre_display_parse( $this->parser->pre_db_parse( $this->ipsclass->input['Post'] ) );

            // -------------------------------------------------------
            // Log it
            // -------------------------------------------------------
            $senton = time();    
            $insert = array(
                             'img_id'         => $this->data['id'],
                             'date'           => $senton,
                             'member_id'      => $this->ipsclass->member['id'],
                             'receiver_name'  => $this->ipsclass->input['receiver_name'],
                             'receiver_email' => $this->ipsclass->input['receiver_email'],
                             'title'          => $this->ipsclass->input['subject'],
                             'msg'            => $this->data['msg'],
                             'bg'             => $this->ipsclass->input['bg'],
                             'font'           => $this->ipsclass->input['font'],
                             'border'         => $this->ipsclass->input['border'],
                            );

            $this->ipsclass->DB->do_insert( 'gallery_ecardlog', $insert );
			$cid = $this->ipsclass->DB->get_insert_id();
			
			// Format the subject
            $subject = $this->ipsclass->lang['ecard_m_subject'];
            $subject = preg_replace( "/<#USER#>/", $this->ipsclass->member['members_display_name'], $subject );
            
            // Format the message
            $msg = $this->ipsclass->lang['ecard_m_body'];
            $card_url = "{$this->ipsclass->base_url}automodule=gallery&cmd=ecard&op=getcard&card={$cid}&access=" . md5( $this->data['id'] . $this->ipsclass->member['id'] . $senton );
            $msg = str_replace( "<#url#>", "<a href='{$card_url}'>{$this->ipsclass->lang['ecard_click_here']}</a><br /><br />{$this->ipsclass->lang['ecard_cant_see']}<br />{$card_url}", $msg );                       
          	
            // Send the mail
            $email->subject = $subject;
            $email->message = $msg;
            $email->send_mail();
            
            if( !empty( $email->error_msg ) )  {
            	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_no_send', 'EXTRA' => "{$email->error_msg}<br />{$email->error_help}" ) );
            }
            
            $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['ecard_sent'], "act=module&amp;module=gallery&amp;cmd=si&amp;img={$this->ipsclass->input['img']}" );
        }

        /**
         * ecard::ecard_preview()
         * 
		 * Previews the current E-Card
		 * 
         * @param integer $img
         * @return none
         **/
        function ecard_preview( $img )
        {
            // -------------------------------------------------------
            // Category/Album Info
            // -------------------------------------------------------        
            if( $this->data['category_id'] )
            {
                $this->ipsclass->DB->simple_construct( array( 'select' => 'id, password, perms_view', 'from' => 'gallery_categories', 'where' => "id={$this->data['category_id']}" ) );
                $this->ipsclass->DB->simple_exec();
                $cat = $this->ipsclass->DB->fetch_row();


                // -------------------------------------------------------
                // Check Permissions
                // -------------------------------------------------------
                $this->glib->check_cat_auth( $cat['id'], $cat['password'], $cat['perms_view'] );
            }

            // -------------------------------------------------------
            // Error Checking
            // -------------------------------------------------------        
            if( empty( $this->ipsclass->input['receiver_name'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_name' ) );
            }

            if( ! $this->ipsclass->clean_email( $this->ipsclass->input['receiver_email'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_email' ) );
            }

            if( empty( $this->ipsclass->input['subject'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_subject' ) );
            }

            if( empty( $this->ipsclass->input['Post'] ) )
            {
                $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'ecard_e_msg' ) );
            }
			
			// -------------------------------------------------------
			// Process post
			// -------------------------------------------------------
			
			$this->ipsclass->input['Post'] = $this->han_editor->process_raw_post( 'Post' );
			
            $this->data['msg']     = $this->parser->pre_display_parse( $this->parser->pre_db_parse( $this->ipsclass->input['Post'] ) );
			$this->data['msg_raw'] = $this->ipsclass->input['Post'];

            $this->data['image']   = $this->glib->make_image_tag( $this->data );
   	        $this->data['lang']    = $this->ipsclass->lang['ecard_preview'];
   	        $this->data['title']   = $this->ipsclass->input['subject'];
   	        $this->data['bg']      = $this->ipsclass->input['bg'];
   	        $this->data['border']  = $this->ipsclass->input['border'];
   	        $this->data['font']    = $this->ipsclass->input['font'];   	      
            
            $this->output .= $this->html->ecard_preview( $this->data );
            $this->output .= $this->html->ecard_preview_options( $this->data );

            // -------------------------------------------------------
            // Page Stuff
            // -------------------------------------------------------
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['ecard_form'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav[] = $this->ipsclass->lang['ecard_form'];
        }


        /**
         * ecard::ecard_form()
         * 
		 * Displays the form for sending a E-Card
		 * 
         * @param integer $img
         * @return none
         **/
        function ecard_form( $img )
        {
            // -------------------------------------------------------
            // Category/Album Info
            // -------------------------------------------------------
            
            if( $this->data['category_id'] )
            {
                $this->ipsclass->DB->simple_construct( array( 'select' => 'id, password, perms_view', 'from' => 'gallery_categories', 'where' => "id={$this->data['category_id']}" ) );
                $this->ipsclass->DB->simple_exec();
                $cat = $this->ipsclass->DB->fetch_row();

                // -------------------------------------------------------
                // Check Permissions
                // -------------------------------------------------------
                
                $this->glib->check_cat_auth( $cat['id'], $cat['password'], $cat['perms_view'] );
            }

            $this->data['sender_name']  = $this->ipsclass->member['name'];
            $this->data['sender_email'] = $this->ipsclass->member['email'];            
            $this->data['image']        = $this->glib->make_image_tag( $this->data, 1 );
            
            /*
            * If modifying, retain style ( bug #111 )  */
            $color_array =  array( 'aqua', 'black', 'blue', 'fuchsia', 'gray', 'green', 'lime',
            						'maroon', 'navy', 'olive', 'purple', 'red', 'silver',
            						'teal', 'white', 'yellow' );
           
            $this->data['bg']     = "<select name='bg'><option value='#FFFFFF'>{$this->ipsclass->lang['bg']}</option";
            $this->data['border'] = "<select name='border'><option value='#000000'>{$this->ipsclass->lang['border']}</option";
            $this->data['font']   = "<select name='font'><option value='#000000'>{$this->ipsclass->lang['fontcolor']}</option";
            
            foreach( $color_array as $color )  
            {
            	$bg_extra     = ( $color == $this->ipsclass->input['bg'] )     ? ' selected="SELECTED"' : '';
            	$border_extra = ( $color == $this->ipsclass->input['border'] ) ? ' selected="SELECTED"' : '';
            	$font_extra   = ( $color == $this->ipsclass->input['font'] )   ? ' selected="SELECTED"' : '';
            	
            	$this->data['bg']     .= "<option style='background: {$color}' value='{$color}'{$bg_extra}>"     . ucwords( $color ) . "</option>";
            	$this->data['border'] .= "<option style='background: {$color}' value='{$color}'{$border_extra}>" . ucwords( $color ) . "</option>";
            	$this->data['font']   .= "<option style='color: {$color}' value='{$color}'{$font_extra}>"        . ucwords( $color ) . "</option>";
            }
            
            // -------------------------------------------------------
            // Sort out text editor
            // -------------------------------------------------------
            
            # Fix up HTML encoded stuff
            $_POST['Post'] = $this->ipsclass->txt_UNhtmlspecialchars( $_POST['Post'] );
            $_POST['Post'] = str_replace( '&#39;'  , "'", $_POST['Post'] );
			$_POST['Post'] = str_replace( '&#039;' , "'", $_POST['Post'] );
			$_POST['Post'] = str_replace( '&#33;'  , "!", $_POST['Post'] );
			
			# Convert IPB code BBTags into RTE HTML
			if ( $this->han_editor->method == 'rte' )
			{
				$this->parser->parse_html    = 1;
				$this->parser->parse_nl2br   = 0;
				$this->parser->parse_smilies = 1;
				$this->parser->parse_bbcode  = 1;
				$_POST['Post'] = $this->parser->pre_db_parse( $_POST['Post'] );
			}
		
            $this->data['editor_html'] = $this->han_editor->show_editor( $_POST['Post'], 'Post' );
            
            /*
            * Close */
            $this->data['bg'] .= '</select>';
            $this->data['border'] .= '</select>';
            $this->data['font'] .= '</select>';
            
            $this->output .= $this->html->ecard( $this->data );
            $this->html_add_smilie_box();

            // -------------------------------------------------------
            // Page Stuff
            // -------------------------------------------------------        
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['ecard_form'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav[] = $this->ipsclass->lang['ecard_form'];            
        }
        
    	/**
    	 * html_add_smilie_box()
    	 * 
		 * Adds a smiely box to the current form
		 * Function taken from Post.php in source files
		 * 
		 * @author Matt Mecham
    	 * @return none
    	 **/
    	function html_add_smilie_box($in_html="")
    	{
			//-----------------------------------------
			// Get post class and skin stuff
			//-----------------------------------------
			
			if ( ! is_object( $this->class_post ) )
			{
				require_once( ROOT_PATH.'sources/classes/post/class_post.php' );
				$this->class_post           =  new class_post;
				$this->class_post->ipsclass =& $this->ipsclass;
			}
			
			if ( ! is_object( $this->ipsclass->compiled_templates['skin_post'] ) )
			{
				$this->ipsclass->load_language('lang_post');
        		$this->ipsclass->load_template('skin_post');
			}
			
			//-----------------------------------------
			// Do it!
			//-----------------------------------------
			
			$this->output = $this->class_post->html_add_smilie_box( $this->output );
		}
    	
		function smilie_alpha_sort($a, $b)
		{
			return strcmp( $a['typed'], $b['typed'] );
		}        

    }
?>
