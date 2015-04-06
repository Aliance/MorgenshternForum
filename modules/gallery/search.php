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
* Main/Search
*
* Search and search results.  We're all searchingggg
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		2.0
*/

    class search
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;

    	/**
    	 * search::start()
    	 * 
		 * Begins execution of this module, $param is used as an entry point into the
		 * module.
		 * 
    	 * @param string $param
    	 * @return none
    	 **/		
    	function start( $param="" )
    	{			
			/* Make sure search is not globally disabled */
    		if( ! $this->ipsclass->vars['gallery_allow_search'] )
    		{
    			$this->ipsclass->Error( array( LEVEL => 1, MSG => 'search_off') );
    		}
			
			/* Make sure this group is allowed to search */
    		if( ! $this->ipsclass->member['g_can_search_gallery'] )
 			{
	 			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
    		}
    		
    		/* Figure out what method we are using and what driver */
	    	$method = isset($this->ipsclass->vars['search_sql_method']) ? $this->ipsclass->vars['search_sql_method'] : 'man';
    		$sql    = isset($this->ipsclass->vars['sql_driver'])        ? $this->ipsclass->vars['sql_driver']        : 'mysql';

	    	//$load_lib = 'search_' . strtolower( $sql ) . '_'.  $method . '.php';
	    	$load_lib = 'search_' . strtolower( $sql ) . '_man.php';
			
			/* Load the library */
    		require( $this->ipsclass->gallery_root . "lib/".$load_lib );
    		$this->lib = new search_lib( &$this );
                $this->lib->ipsclass = &$this->ipsclass;
                $this->lib->glib = &$this->glib;

			/* Load lang and templates */
			$this->ipsclass->lang = $this->ipsclass->load_words( $this->ipsclass->lang, 'lang_search', $this->ipsclass->lang_id );
			
			/*
            * Fatal error bug fix */
            if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_search'] ) ) {
    			$this->ipsclass->load_template( 'skin_gallery_search' );
            }
    		$this->html = $this->ipsclass->compiled_templates['skin_gallery_search'];
    		
    		/* What are we doing? */
    		switch( $param )
    		{
    			default:
    				$this->do_search();
    			break;	
    		}
    		
		    /* Page Stuff */
			$this->nav[] = "<a href='".$this->ipsclass->base_url."automodule=gallery'>".$this->ipsclass->lang['gallery']."</a>";
	   		$this->nav[] = $this->ipsclass->lang['gallery_search'];
			$this->page_title = $this->ipsclass->vars['board_name']." &gt; ".$this->ipsclass->lang['gallery_search'];
    		$this->ipsclass->print->add_output( $this->output );
        	$this->ipsclass->print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 1, NAV => $this->nav ) );    		
    	}
    	
    	function do_search()
    	{
			/* Flood Control */
			if( $this->ipsclass->member['g_search_flood'] > 0 )
			{
				$flood_time = time() - $this->ipsclass->member['g_search_flood'];

				$this->ipsclass->DB->simple_construct( array( 'select' => 'id',
											  'from'   => 'search_results',
											  'where'  => "(member_id='".$this->ipsclass->member['id']."' OR ip_address='".$this->ipsclass->input['IP_ADDRESS']."') AND search_date > '$flood_time'" ) );
				$this->ipsclass->DB->simple_exec();

				if( $this->ipsclass->DB->get_num_rows() )
				{
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'search_flood', 'EXTRA' => $this->ipsclass->member['g_search_flood'] ) );
				}
			}
			
			/* Limit search to category or album */
			if( $this->ipsclass->input['search_where'] == 'cat' )
			{
				$refine_search = " AND category_id={$this->ipsclass->input['search_where_id']} ";	
			}
			else if( $this->ipsclass->input['search_where'] == 'album' )
			{
				$refine_search = " AND album_id={$this->ipsclass->input['search_where_id']} ";	
			}
			else
			{
				$refine_search = '';	
			}
			
			/* Pass the search off to our library */
			$search_results = $this->lib->search( $refine_search );
			
			if( !is_array( $search_results ) ) {
				/*
				* No results, doh */
				$this->output .= $this->html->no_results();
			}
			else {
			  /*
			  * Hooray results! */
			  $this->output .= $this->html->search_top();
			  
			  /*
			  * Start parsing through results */
			  foreach( $search_results as $key=>$result )  {
			  	
			  	/*
			  	* Create image tag */
			  	$result['image'] = $this->glib->make_image_link( $result, $result['thumbnail'] );
			  	
			  	/*
			  	* Now user, and build location */
			  	$result['stuff'] = "{$this->ipsclass->lang['gall_caption']} {$result['caption']}<br />";
			  	$result['user'] = $this->glib->make_name_link( $result['member_id'], $result['name'] );
			  	if( !empty( $result[ 'description' ] ) ) {
			  		$result[ 'stuff' ] .= "{$this->ipsclass->lang['gall_description']} {$result['description']}";
			  	}
			  	
			  	$this->output .= $this->html->search_row( $result );
			  	
			  }
			}
			
			$this->output .= $this->html->search_end();
    	}
    	
    /*-------------------------------------------------------------------------*/
    // Filter keywords
    /*-------------------------------------------------------------------------*/

    function filter_keywords($words="", $name=0)
    {
    	// force to lowercase and swop % into a safer version

    	$words = trim( strtolower( str_replace( "%", "\\%", $words) ) );

    	// Remove trailing boolean operators

    	$words = preg_replace( "/\s+(and|or)$/" , "" , $words );

    	// Swop wildcard into *SQL percent

    	//$words = str_replace( "*", "%", $words );

    	// Make safe underscores

    	$words = str_replace( "_", "\\_", $words );

    	$words = str_replace( '|', "&#124;", $words );

    	// Remove crap

    	if ($name == 0)
    	{
    		$words = preg_replace( "/[\|\[\]\{\}\(\)\,:\\\\\/\"']|&quot;/", "", $words );
    	}

    	// Remove common words.. (should be expanded upon in a later release to return 'not searchable word'

    	$words = preg_replace( "/^(?:img|quote|code|html|javascript|a href|color|span|div|border|style)$/", "", $words );

    	return " ".preg_quote($words)." ";
    }    	
    	
}  	
?>
