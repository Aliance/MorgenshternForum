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
* Main/Quick Change
*
* Functions for changing avatar/personal photo
* from Gallery
*
* NOT USED YET ( 2.1 Feature )
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		2.1
*/
    class quickch
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
            if( $this->ipsclass->input['id'] )
            {
                $this->ipsclass->input['id'] = intval( $this->ipsclass->input['id'] );

                if( ! $this->ipsclass->input['id'] )
                {
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                }
            }

          /*
          *
          * Make sure they are allowed to even use avatars/photos or change them */
          if( !$this->ipsclass->vars['avatars_on'] && $this->ipsclass->input['code'] == "avatar" )  {
          	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
          }
          
          /*
          * Guest? */
          if( $this->ipsclass->member['name'] == "Guest" )  {
          	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
          }
          
          /*
          * Templates stuff  */
          
          /*
          * Fatal error bug fix */
          if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_quickch'] ) ) {
          	$this->ipsclass->load_template('skin_gallery_quickch');
          }
          $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_quickch' ];

            switch( $param )
            {
                case 'qkch':
                    if( $this->ipsclass->input['code'] == "avatar" )  {
                    	/*
                    	* Change out their avatar  */
                    	$this->change_avatar();
                    }
                    if( $this->ipsclass->input['code'] == "photo" )  {
                    	/*
                    	* Change out their personal photo  */
                    	$this->change_photo();
                    }
                break;

                default:
                    $this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
                break;
            }
		
            $this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['qkch_av'];
            $this->nav[] = "<a href='{$this->ipsclass->base_url}automodule=gallery'>{$this->ipsclass->lang['gallery']}</a>";
            $this->nav[] = $this->ipsclass->lang['qkch_av'];
    	
    		$this->ipsclass->print->add_output( $this->output );
            $this->ipsclass->print->do_output( array( 'TITLE' => $this->ipsclass->lang['m_gallery'], 'JS' => 1, NAV => $this->nav ) );
        }


        /**
        * change_avatar()
        * @since 2.0
        **/
        function change_avatar()  {
        	/*
        	* Pull up the image, load up height/width limitations */
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        }
        
        /**
        * change_photo()
        * @since 2.0
        **/
        function change_photo()  {
        	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
        }
    }
?>
