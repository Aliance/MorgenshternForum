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
* Main/Image Control
*
* Sends an image to the browser, permissions and settings
* are checked to ensure that the user can view the image
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

    class img_ctrl
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;

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
            // -------------------------------------------------------
            // Check Auth
            // -------------------------------------------------------            
	        if( $this->ipsclass->member['id'] )
    	    {
    	        $perms = explode( ':', $this->ipsclass->member['gallery_perms'] );           
    	        if( ! $perms[0] ) exit;
    	    }     

            // -------------------------------------------------------
            // What's our entry point?
            // -------------------------------------------------------
            switch( $param )
            {
                case 'view':
                    $this->display_image();
                break;

            }

        }

        /**
         * img_ctrl::display_image()
		 * 
		 * Sends the image to a browser.  Checks for hotlinking, bandwidth useage,
		 * permissions.
         * 
         * @return none
         **/
        function display_image()
        {
        	/**
        	* Get image information
        	**/		
        	$this->ipsclass->input['img'] = $this->glib->validate_int( $this->ipsclass->input['img'] );
        	$image = $this->glib->get_image_info( $this->ipsclass->input['img'] );
        	
        	/**
        	* Physical location
        	**/
        	$image_loci = "{$this->ipsclass->vars['gallery_images_path']}/{$image['directory']}/";
        	
        	/**
        	* Thumbnail, med, or full image?
        	**/
        	if( !empty( $this->ipsclass->input['tn'] ) )
        	{
        		$theimg = "{$image_loci}tn_{$image['masked_file_name']}";
        	}
        	else 
        	{
        		$theimg = ( $this->ipsclass->input['file'] == 'med' ) ? "{$image_loci}{$image['medium_file_name']}" : "{$image_loci}{$image['masked_file_name']}";
        	}
	        
      
            /**
            * Finally, display the image
            **/
            header("Content-Type: {$image['file_type']}");
            header("Content-Disposition: inline; filename={$theimg}");
            readfile( $theimg );

            /**
            * Exit out 
            **/
            exit();
        }

    }
?>
