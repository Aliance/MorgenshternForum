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
* Library/Image functions
*
* Gives some lovin' to the images
*
* @package		Gallery
* @subpackage 	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

    class Image
    {
       var $ipsclass;
       var $glib;

    	var $out_file_name     = '';
    	var $out_file_dir      = '';
        var $out_file_complete = '';
    
        var $in_file_dir      = '.';
    	var $in_file_name     = '';
        var $in_file_complete = '';

        var $in_file_width    = 0;
        var $in_file_height   = 0;

	    var $file_extension   = '';
        var $file_type        = '';

        var $img_library      = '';

        /**
         * Image::Image()
		 * 
         * Constructor
		 * 
		 * $params(
		 * 			'out_dir'    => Directory to store the image in
		 * 			'out_file'   => name for the image output
		 * 			'in_dir'    => Directory to read the image from
		 * 			'in_file'   => name of the image to read
		 * )
         * 
         * @param array $params
         * @return nothing
         **/
        function Image( $params )
        {
            global $INFO;

            // Directories
    		$this->in_file_dir  = preg_replace( "#/$#", "", $params['in_dir'] );
	    	$this->out_file_dir = preg_replace( "#/$#", "", $params['out_dir'] );
            
            // Files
            $this->in_file_name  = $params['in_file'];
            $this->out_file_name = ( $params['out_file'] ) ? $params['out_file'] : $params['in_file'];
		    
            // Complete File Names
    		if ( $this->in_file_dir and $this->in_file_name )
	    	{
		    	$this->in_file_complete = $this->in_file_dir.'/'.$this->in_file_name;
    		}
	    	else
		    {
			    $this->in_file_complete = $this->in_file_name;
    		}

    		if ( $this->out_file_dir and $this->out_file_name )
	    	{
		    	$this->out_file_complete = $this->out_file_dir.'/'.$this->out_file_name;
    		}
	    	else
		    {
			    $this->out_file_complete = $this->out_file_name;
    		}
            
            // Get some information
            $img_size = @GetImageSize( $this->in_file_complete );

            $this->in_file_width  = $img_size[0];
            $this->in_file_height = $img_size[1];

            $remap  = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG' );
            $this->file_extension = strtolower( $remap[ $img_size[2] ] );
            $this->file_type = $this->find_mime_type( $this->file_extension );
        }

        /*
        *  Image::lib_setup()
        *  Call after creating instance of Image
        *
        * @return void
        */
        function lib_setup()  {
            global $INFO;
            //Figure out the library
            $this->img_library = ( $this->ipsclass->vars['gallery_img_suite'] ) ? $this->ipsclass->vars['gallery_img_suite'] : $INFO['gallery_img_suite'];

            // If this is GD, then we need to load the image
            if( $this->img_library != 'im' )
            {
        		switch( $this->file_type )
	        	{
		        	case 'image/gif':
                        $this->img = @imagecreatefromgif( $this->in_file_complete );
				        break;
        			case 'image/jpeg':
                        $this->img = @imagecreatefromjpeg( $this->in_file_complete );
		        		break;
    			    case 'image/pjpeg':
                        $this->img = @imagecreatefromjpeg( $this->in_file_complete );
    		    		break;
    	    		case 'image/x-png':
                        $this->img = @imagecreatefrompng( $this->in_file_complete );
			        	break;
        			case 'image/png':
                        $this->img = @imagecreatefrompng( $this->in_file_complete );
		        		break;
		        }
            }
        }

        /**
         * Image::thumbnail()
         * 
		 * Creates a thumbnail based on the loaded image
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function thumbnail( $width, $height )
        {
            if( $this->in_file_width > $width || $this->in_file_height > $height )
            {
                $func = "_thumbnail_{$this->img_library}";
                $sizes = $this->get_proportional_values( $width, $height );
                return $this->$func( $sizes['newx'], $sizes['newy'] );
            }
            else
            {
                return false;
            }
        }

        /**
         * Image::write_to_file()
         * 
		 * Writes the manipulated image to disk, using the correct library
		 * 
         * @return void
         **/
        function write_to_file()
        {
            $func = "_write_file_{$this->img_library}";
            $this->$func( $width, $height );

            return $this->get_file_info();
        }

        /**
         * Image::resize_proportional()
         * 
		 * Generates values for width and height that won't distort the image
		 * 
         * @param integer $new_width
         * @param integer $new_height
         * @return bool
         **/
        function resize_proportional( $new_width, $new_height, $override=0 )
        {
            $sizes = $this->get_proportional_values( $new_width, $new_height );

            if( ( $this->in_file_width > $sizes['newx'] || $this->in_file_height > $sizes['newy'] ) || $override )
            {            
                $func = "_resize_{$this->img_library}";
                return $this->$func( $sizes['newx'], $sizes['newy'] );
            }
            else
            {
                return false;
            }
        }

        /**
         * Image::watermark()
		 * 
		 * Applies the watermark specified in the ACP to the current
		 * image
         * 
         * @return bool
         **/
        function watermark() 
        {
            global $INFO;
            
            // Watermark Dimensions
            $watermark = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? $this->ipsclass->vars['gallery_watermark_path'] : $INFO['gallery_watermark_path'];
            $img_info = getimagesize( $watermark, $img_info );
            $water_width  = $img_info[0];
            $water_height = $img_info[1];
            
            // Figure out where to put the mark
            $c_x = $this->in_file_width - $water_width;
            $c_y = $this->in_file_height - $water_height;

            $func = "_watermark_{$this->img_library}";

            return $this->$func( $c_x, $c_y );
        }

        /***********************************************************
         *
         * Image Magick Related Functions
         *
         **********************************************************/

		/**
		 * Image::_resize_im()
		 * 
		 * Resizes an image to the specified width and height
		 * 
		 * @param integer $width
		 * @param integer $height
		 * @return bool
		 **/
		function _resize_im( $width, $height )
		{
            global $INFO;
            
            $im = ( $this->ipsclass->vars['gallery_im_path'] ) ? $this->ipsclass->vars['gallery_im_path'] : $INFO['gallery_im_path'];

            system("{$im}convert -geometry {$width}x{$height} {$this->in_file_complete} {$this->out_file_complete}.temp" );

            // Was the image resized successfully?
            if( file_exists( "{$this->out_file_complete}.temp" ) )
            {
                return true;
            }
            else
            {
                return false;             
            }
        }


        /**
         * Image::_thumbnail_im()
         * 
		 * Creates a thumbnail using Image Magick
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function _thumbnail_im( $width, $height )
        {
            global $INFO;
            
            $im = ( $this->ipsclass->vars['gallery_im_path'] ) ? $this->ipsclass->vars['gallery_im_path'] : $INFO['gallery_im_path'];

            system( "{$im}convert -geometry {$width}x{$height} {$this->in_file_complete} {$this->out_file_complete}.temp" );
            
            // Was the thumbnail created successfully?
            if( file_exists( "{$this->out_file_complete}.temp" ) )
            {
                return true;
            }
            else
            {
                return false;             
            }
        }

        /**
         * Image::_write_file_im()
         * 
		 * Writes the outfile to disk using Image Magick
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function _write_file_im()
        {
            if( file_exists( "{$this->out_file_complete}.temp" ) )
            {
                if( file_exists( $this->out_file_complete ) )
                {   
                    @unlink( $this->out_file_complete );
                }
			
                @rename( "{$this->out_file_complete}.temp", $this->out_file_complete );
                @chmod( $this->out_file_complete, 0777 );
                if( ! file_exists( $this->out_file_complete ) )
                {
                    /**
                	* Die gracefully
                	**/
                	if( IPB_THIS_SCRIPT == 'admin' )
                	{
                    	$this->ipsclass->admin->error( "Unable to save image using ImageMagick." );
                	}
                	else 
                	{
                		$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'unable_write' ) );
                	}
                }
            }
        }

        /**
         * Image::_watermark_im()
		 * 
		 * Applies the watermark specified in the ACP to the current
		 * image using Image Magick
         * 
         * @return none
         **/
        function _watermark_im( $c_x, $c_y ) 
        {
            global $INFO;
            
            $im        = ( $this->ipsclass->vars['gallery_im_path'] ) ? $this->ipsclass->vars['gallery_im_path'] : $INFO['gallery_im_path'];
            $watermark = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? $this->ipsclass->vars['gallery_watermark_path'] : $INFO['gallery_watermark_path'];

            system("{$im}composite -geometry +{$c_x}+{$c_y} {$watermark} {$this->in_file_complete} {$this->out_file_complete}.temp" );

            // Was the thumbnail created successfully?
            if( file_exists( "{$this->out_file_complete}.temp" ) )
            {
                return true;
            }
            else
            {
                return false;             
            }

        }

        /***********************************************************
         *
         * GD 2 Related Functions
         *
         **********************************************************/

		/**
		 * Image::_resize_gd()
		 * 
		 * Resizes an image to the specified width and height
		 * 
		 * @param integer $width
		 * @param integer $height
		 * @return bool
		 **/
		function _resize_gd( $width, $height )
		{
            $new_img = @imagecreatetruecolor( $width, $height ); 
            @imagecopyresampled( $new_img, $this->img, 0, 0, 0 ,0, $width, $height, $this->in_file_width, $this->in_file_height ); 

            $this->img = $new_img;
            return true;
		}

        /**
         * Image::_thumbnail_gd()
         * 
		 * Creates a thumbnail using GD
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function _thumbnail_gd( $width, $height )
        {
            if( ! ( $width && $height ) )
            {
                $sizes  = $this->get_proportional_values( $width, $height );                
                $width  = $sizes['newx'];
                $height = $sizes['newy'];
            }

            $new_img = imagecreatetruecolor( $width, $height ); 
            imagecopyresampled( $new_img, $this->img, 0, 0, 0 ,0, $width, $height, $this->in_file_width, $this->in_file_height ); 

            $this->img = $new_img;

            return true;
        }

        /**
         * Image::_watermark_gd()
		 * 
		 * Applies the watermark specified in the ACP to the current
		 * image using GD
         * 
         * @return none
         **/
        function _watermark_gd( $c_x, $c_y ) 
        {
            global $INFO;

            $watermark = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? $this->ipsclass->vars['gallery_watermark_path'] : $INFO['gallery_watermark_path'];

            // ---------------------------------------------------------------
            // Load the watermark
            // ---------------------------------------------------------------
            $temp = explode( ".", $watermark );
            $type = strtolower( array_pop( $temp ) );
            // Open the image
            if( $type == 'jpg' || $type == 'jpeg' )
            {
                $mark = @imagecreatefromjpeg( $watermark );
            }
            else if( $type == 'gif' )
            {
                $mark = @imagecreatefromgif( $watermark );
            }
            else if( $type == 'png' )
            {
                $mark = @imagecreatefrompng( $watermark );
            }
            // ---------------------------------------------------------------

            // Watermark Dimensions
            $img_info = getimagesize( $watermark, $img_info );
            $water_width  = $img_info[0];
            $water_height = $img_info[1];

            $opacity = ( $this->ipsclass->vars['gallery_watermark_opacity'] ) ? $this->ipsclass->vars['gallery_watermark_opacity'] : $INFO['gallery_watermark_opacity'];
            
            if( $opacity  )
            {
                imagecopymerge( $this->img, $mark, $c_x, $c_y, 0, 0, $water_width, $water_height, $opacity  );
            }
            else
            {
                imagecopy( $this->img, $mark, $c_x, $c_y, 0, 0, $water_width, $water_height );
            }
            
            @imagedestroy( $mark );
            return true;
        }

        /**
         * Image::_write_file_gd()
         * 
		 * Writes the outfile to disk using GD
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function _write_file_gd()
        {
            $map = array( 'jpg'  => 'imagejpeg',
                          'jpeg' => 'imagejpeg',
                          'gif'  => 'imagejpeg',
                          'png'  => 'imagepng',
                        );

            if( $this->file_extension == 'gif' )
            {
                $this->out_file_complete = preg_replace( "/(.gif)$/", ".jpg", $this->out_file_complete );
            }

            if( $map[$this->file_extension] )
            {
            	/**
            	* "Hack" around the PHP4.4.1 image* safe mode bug
            	* Should remove this after enough people have
            	* upgraded to 4.4.2
            	**/
            	if( ( strstr( phpversion(), '4.4.1' ) ) && ini_get( 'safe_mode' ) )
            	{
            		/**
            		* Touch file first
            		**/
            		@touch( $this->out_file_complete );
            		@chmod( $this->out_file_complete, 0777 );
            	}
            	
                @$map[$this->file_extension]( $this->img, $this->out_file_complete );
                @chmod( $this->out_file_complete, 0777 );
             
                if( ! file_exists( $this->out_file_complete ) )
                {
                	/**
                	* Die gracefully
                	**/
                	if( IPB_THIS_SCRIPT == 'admin' )
                	{
                    	$this->ipsclass->admin->error( "Unable to save image using GD." );
                	}
                	else 
                	{
                		$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'unable_write' ) );
                	}
                }
            }
            else
            {
                /**
                * Die gracefully
                **/
                if( IPB_THIS_SCRIPT == 'admin' )
                {
                   	$this->ipsclass->admin->error( "Unable to save image using GD." );
                }
                else 
                {
                	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'unable_write' ) );
                }
            }
            
            @imagedestroy( $this->img );

            return true;

        }

        /***********************************************************
         *
         * GD 1.6.2 Related Functions
         *
         **********************************************************/

		/**
		 * Image::_resize_gd()
		 * 
		 * Resizes an image to the specified width and height
		 * 
		 * @param integer $width
		 * @param integer $height
		 * @return bool
		 **/
		function _resize_gd1( $width, $height )
		{
            $new_img = @imagecreate( $width, $height ); 
            @imagecopyresized( $new_img, $this->img, 0, 0, 0 ,0, $width, $height, $this->in_file_width, $this->in_file_height ); 
            $this->img = $new_img;
            return true;
		}

        /**
         * Image::_thumbnail_gd()
         * 
		 * Creates a thumbnail using GD
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function _thumbnail_gd1( $width, $height )
        {
            if( ! ( $width && $height ) )
            {
                $sizes  = $this->get_proportional_values( $width, $height );                
                $width  = $sizes['newx'];
                $height = $sizes['newy'];
            }

            $new_img = @imagecreate( $width, $height ); 
            @imagecopyresized( $new_img, $this->img, 0, 0, 0 ,0, $width, $height, $this->in_file_width, $this->in_file_height ); 
            $this->img = $new_img;

            return true;
        }

        /**
         * Image::_watermark_gd()
		 * 
		 * Applies the watermark specified in the ACP to the current
		 * image using GD
         * 
         * @return none
         **/
        function _watermark_gd1( $c_x, $c_y ) 
        {
            global $INFO;

            $watermark = ( $this->ipsclass->vars['gallery_watermark_path'] ) ? $this->ipsclass->vars['gallery_watermark_path'] : $INFO['gallery_watermark_path'];

            // ---------------------------------------------------------------
            // Load the watermark
            // ---------------------------------------------------------------
            $temp = explode( ".", $watermark );
            $type = strtolower( array_pop( $temp ) );
            // Open the image
            if( $type == 'jpg' || $type == 'jpeg' )
            {
                $mark = @imagecreatefromjpeg( $watermark );
            }
            else if( $type == 'gif' )
            {
                $mark = @imagecreatefromgif( $watermark );
            }
            else if( $type == 'png' )
            {
                $mark = @imagecreatefrompng( $watermark );
            }
            // ---------------------------------------------------------------

            // Watermark Dimensions
            $img_info = getimagesize( $watermark, $img_info );
            $water_width  = $img_info[0];
            $water_height = $img_info[1];

            $opacity = ( $this->ipsclass->vars['gallery_watermark_opacity'] ) ? $this->ipsclass->vars['gallery_watermark_opacity'] : $INFO['gallery_watermark_opacity'];
            
            if( $opacity  )
            {
                @imagecopymerge( $this->img, $mark, $c_x, $c_y, 0, 0, $water_width, $water_height, $opacity  );
            }
            else
            {
                @imagecopy( $this->img, $mark, $c_x, $c_y, 0, 0, $water_width, $water_height );
            }
            
            @imagedestroy( $mark );
            return true;
        }

        /**
         * Image::_write_file_gd()
         * 
		 * Writes the outfile to disk using GD
		 * 
         * @param integer $width
         * @param integer $height
         * @return void
         **/
        function _write_file_gd1()
        {
            $map = array( 'jpg'  => 'imagejpeg',
                          'jpeg' => 'imagejpeg',
                          'gif'  => 'imagejpeg',
                          'png'  => 'imagepng',
                        );

            if( $this->file_extension == 'gif' )
            {
                $this->out_file_complete = preg_replace( "/(.gif)$/", ".jpg", $this->out_file_complete );
            }

            if( $map[$this->file_extension] )
            {
                @$map[$this->file_extension]( $this->img, $this->out_file_complete );
                @chmod( $this->out_file_complete, 0777 );
             
                if( ! file_exists( $this->out_file_complete ) )
                {            
                    /**
                	* Die gracefully
                	**/
                	if( IPB_THIS_SCRIPT == 'admin' )
                	{
                    	$this->ipsclass->admin->error( "Unable to save image using GD." );
                	}
                	else 
                	{
                		$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'unable_write' ) );
                	}
                }
            }
            else
            {
                /**
                * Die gracefully
                **/
                if( IPB_THIS_SCRIPT == 'admin' )
                {
                   	$this->ipsclass->admin->error( "Unable to save image using GD." );
                }
                else 
                {
                	$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'unable_write' ) );
                }
            }

            @imagedestroy( $this->img );

            return true;

        }



        /***********************************************************
         *
         * Image Utility Functions
         *
         **********************************************************/

        /**
         * Image::find_mime_type()
         * 
		 * Tries to determine what the mime type of the image
		 * is based on extension
		 * 
         * @param string $filename
         * @return mime type(string) on success, false on failure
         **/       
		function find_mime_type( $type )
        {
            if( $type == 'jpg' || $type == 'jpeg' )
            {
                return 'image/jpeg';
            }
            else if( $type == 'gif' )
            {
                return 'image/gif';
            }
            else if( $type == 'png' )
            {
                return 'image/png';
            }
            else
            {
                return false;
            }
        }

        /**
         * Image::get_file_info()
         * 
		 * Returns various information about the current image
		 * 
         * @return array
         **/
        function get_file_info()
        {			
            return( array(
                            'file_name' => $this->out_file_complete,
                            'file_ext'  => $this->file_extension,
                            'file_type' => $this->file_type,
                            'width'     => $this->vars['in_file_width'],
                            'height'    => $this->vars['in_file_height'],
                  )       );
        }

        function get_proportional_values( $newx, $newy )
        {
            if( $this->in_file_width > $this->in_file_height )
            {
                $ratio = $this->in_file_width / $this->in_file_height;
                $newy  = ceil( $newx / $ratio );            
            }
            else
            {
                $ratio = $this->in_file_width / $this->in_file_height;
                $newx  = ceil( $ratio * $newy );            
            }

            return array( 'newx' => $newx, 'newy' => $newy );
        }
    }
?>
