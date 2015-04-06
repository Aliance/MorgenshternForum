<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v<{%dyn.down.var.human.version%}>
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   =============================================
|   
|   Nullfied by SneakerXZ
|   
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

/*
+--------------------------------------------------------------------------
|   This module has two functions:
|   get_session_variables: Return the session variables for the class_session functions
|   parse_online_entries: Parses an array from online.php
|   See each function for more information
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// This must always be 'components_location'
//-----------------------------------------

class components_location_gallery
{
	var $ipsclass;

	//						Cmd list				location_type
	var	$cmdlist = array (
							'idx'			=>	'list',
							'sc'			=>	'categories',
							'si'            =>  'image',
							'slideshow'		=>	'slideshow'
						 );

	/*-------------------------------------------------------------------------*/
	// get_session_variables
	// Returns:
	// array( '1_type' => {location type #1} [ char(10) ]
	//        '1_id'   => {location ID #1}   [ int(10)  ]
	//        '2_type' => {location type #2} [ char(10) ]
	//        '2_id'   => {location ID #2}   [ int(10)  ]
	//		  '3_type' => {location type #3} [ char(10) ]
	//        '3_id'   => {location ID #3}   [ int(10)  ]
	//      );
	// All are optional.
	// Use this to populate the 'module_id_*' fields in the session table
	// so you can check in your own scripts it the member is active in your module
	// {variable} can be 30 chrs long and alpha numerical
	// "location" in the sessions table will be the name of the module called
	/*-------------------------------------------------------------------------*/
	
	function get_session_variables()
	{
		$return_array = array();

		if ( !$this->ipsclass->input['cmd'] )  {
			$this->ipsclass->input['cmd'] = 'idx';
		}
		$cmd = ( isset( $this->cmdlist[ $this->ipsclass->input['cmd'] ] ) ? strtolower($this->ipsclass->input['cmd']) : 'idx' );

		$return_array['1_type'] = $this->cmdlist[$cmd];

		if ( intval($this->ipsclass->input['img']) )
		{
			$return_array['1_id'] = intval( $this->ipsclass->input['img'] );
		}

		return $return_array;
	}

	/*-------------------------------------------------------------------------*/
	// parse_online_entries
	// INPUT: $array IS:
	// $array[ $session_id ] = $session_array;
	// Session array is DB row from ibf_sessions
	// EXPECTED RETURN ------------------------------------
	// $array[ $session_id ]['_parsed'] = 1;
	// $array[ $session_id ]['_url']    = {Location url}
	// $array[ $session_id ]['_text']   = {Location text}
	// $array[ $session_id ] = $session_array...
	//
	// YOU ARE RESPONSIBLE FOR PERMISSION CHECKS. IF THE MEMBER DOESN'T
	// HAVE PERMISSION RETURN '_url'    => $this->ipsclass->base_url,
	// 						  '_text'   => $this->ipsclass->lang['board_index'],
	//						  '_parsed' => 1 { as well as the rest of $session_array }
	/*-------------------------------------------------------------------------*/

	function parse_online_entries( $array=array() )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$return = array();
		$gallery_cache = array();
		$image_cache = array();

		//-----------------------------------------
		// Load language file
		//-----------------------------------------
		if ( ! isset( $this->ipsclass->lang['gallery_loc_idx'] ) )
		{
			$this->ipsclass->load_language( 'lang_gallery_location' );
		}

		//-----------------------------------------
		// LOOP
		//-----------------------------------------
			foreach( $array as $session_id => $session_array )
			{
				if ( $session_array['location'] == 'mod:gallery' )
				{
					/*
					* Viewing an image? */
					if ( $session_array['location'] == 'mod:gallery' && $session_array['location_1_type'] == 'image' && intval($session_array['location_1_id']) ) {
						$image = intval($session_array['location_1_id']);
						/*
	     		   	    * Grab image name only */
	     		   	    $this->ipsclass->DB->simple_construct( array( "select" => "id,caption", "from" => "gallery_images", "where" => "id = '{$image}' AND approved=1" ) );
	     		   	    $this->ipsclass->DB->simple_exec();
	     		   	    if( $this->ipsclass->DB->get_num_rows() ) {
	     		   	    	$img = $this->ipsclass->DB->fetch_row();
	     		   	    	$location = "{$this->ipsclass->base_url}automodule=gallery&cmd=si&img={$img['id']}";
	     		   	    	$text = $this->ipsclass->lang['gallery_loci_si'];
	     		    		$text = str_replace( "<#IMG#>", $img[ 'caption' ], $text );
	     		   	    }
	     		   	    else {
	     		   	    	/*
	     		   	    	* Probably viewing unapproved image */
	     		   	    	$location = "{$this->ipsclass->base_url}automodule=gallery";
	     		   			$text = $this->ipsclass->lang['gallery_loci_si'];
	     		   	    }
					}
					else if ( $session_array['location'] == 'mod:gallery' )
					{
						$location = "{$this->ipsclass->base_url}automodule=gallery";
	     		   		$text = $this->ipsclass->lang['gallery_loci_idx'];
					}
	     		   
				}
				$return[ $session_id ] = array_merge( $session_array, array( '_url' => $location, '_text' => $text, '_parsed' => 1 ) );
			}

		return $return;
	}

}
?>
