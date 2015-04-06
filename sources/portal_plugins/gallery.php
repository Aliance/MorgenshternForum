<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.1.7
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Wednesday 27th of September 2006 08:13:32 AM
|   Release: 2871a4c8b602386260eeb8bf9da57e29
|   Licence Info: http://www.invisionboard.com/?license
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2005-12-30 23:02:19 +0000 (Fri, 30 Dec 2005) $
|   > $Revision: 109 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > PORTAL PLUG IN MODULE: Gallery
|   > Module written by Matt Mecham
|   > Date started: Tuesday 2nd August 2005 (12:56)
+--------------------------------------------------------------------------
*/

/**
* Portal Plug In Module
*
* Portal Gallery functions
*
* @package		InvisionPowerBoard
* @subpackage	PortalPlugIn
* @author		Matt Mecham
* @copyright	Invision Power Services, Inc.
* @version		2.1
*/

/**
* Portal Gallery In Module
*
* Portal Blog functions
* Each class name MUST be in the format of:
* ppi_{file_name_minus_dot_php}
*
* @package		InvisionPowerBoard
* @subpackage	PortalPlugIn
* @author		Matt Mecham
* @copyright	Invision Power Services, Inc.
* @version		2.1
*/
class ppi_gallery
{
	/**
	* IPS Global object
	*
	* @var string
	*/
	var $ipsclass;
	var $glib;
	var $img_list;
	var $category;

	/**
	* Array of portal objects including:
	* good_forum, bad_forum
	*
	* @var array
	*/
	var $portal_object = array();
	
	/*-------------------------------------------------------------------------*/
 	// INIT
	/*-------------------------------------------------------------------------*/
 	/**
	* This function must be available always
	* Add any set up here, such as loading language and skins, etc
	*
	*/
 	function init()
 	{
 		/*
 		* Root path  */
 		$this->ipsclass->gallery_root = "./modules/gallery/";
 		
 		if( !is_dir( $this->ipsclass->gallery_root ) )
 		{
	 		return;
 		}
 		
 		/*
 		* Load up gallery's library file */
 		require( $this->ipsclass->gallery_root.'lib/gallery_library.php' );
        $this->glib = new gallery_lib();
        $this->glib->ipsclass = &$this->ipsclass;
        
		/*
		* Image listing */
		require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );
		$this->img_list = new ImageListing();
        	$this->img_list->ipsclass =& $this->ipsclass;
            $this->img_list->glib =& $this->glib;
        $this->img_list->init();
        
        /*
        * Lang and template */
        $this->ipsclass->load_language( 'lang_gallery' );
        $this->ipsclass->load_template( 'skin_gallery_portal' );
 	}
 	
 	/*-------------------------------------------------------------------------*/
	// MAIN FUNCTION
	/*-------------------------------------------------------------------------*/
	/**
	* Main function
	*
	* @return VOID
	*/
	function gallery_show_random_image()
	{
 		if( !is_dir( $this->ipsclass->gallery_root ) )
 		{
	 		return;
 		}
 				
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		$this->ipsclass->DB->build_query( array(
				'select'	=>	'*',
				'from'		=>	'gallery_images',
				'where'		=>	'approved=1',
				'order'		=>	'RAND()',
				'limit'		=>	array( 0,1 ) ) 
		);
		$this->ipsclass->DB->exec_query();
		$i = $this->ipsclass->DB->fetch_row();
		$image = $this->glib->make_image_link( $i, $i['thumbnail'] );
 		return $this->ipsclass->compiled_templates['skin_gallery_portal']->tmpl_random_image_wrap( $image );
  	}

}

?>