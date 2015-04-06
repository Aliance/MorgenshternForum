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
* Library/Search MSSQL
*
* Search abstraction ( MSSQL )
*
* @package		Gallery
* @subpackage 	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		2.0
*/

    class search_lib
    {
        var $ipsclass;
        var $glib;
        var $output;
        var $info;
        var $html;

    	/* Constructor */
    	function search_lib( $_parent  )
    	{
                $this->parent = &$_parent;
	 	}

    	/**
    	 * search_lib::search()
    	 *
		 * Does the search
		 *
    	 * @param string $refine_search
    	 * @return none
    	 **/
    	function search( $refine_search="" )
    	{
	   		/* Filter the search term(s) */
	   		$search_terms = $this->parent->filter_keywords( $this->ipsclass->input['search_for'] );
	   		$search_terms = trim( $search_terms );
			$search_terms = str_replace( "%", "", $search_terms );

			/* Do we still have something to search for? */
			if( ! $search_terms )
			{
				$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_search_words' ) );
			}

			/* Minimum Search Length */
			if( $this->ipsclass->vars['min_search_word'] < 1 )
			{
				$this->ipsclass->vars['min_search_word'] = 4;
			}

			/* Search by what column? */
			if( $this->ipsclass->input['search_in'] == 'caption' )
			{
				$search_column = "i.caption";
			}
			else
			{
				$search_column = "i.description";
			}

	   		/* Get authorized categories */
	   		$cat_auth = $this->glib->get_allowed_cats();

	   		if( $this->ipsclass->input['search_where'] == 'cat' ) {
				if( !in_array( $this->ipsclass->input[ 'search_where_id' ], $cat_auth ) )  {
					/* Not allowed to search private cats */
					$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'gallery_unauth' ) );
				}
	   		}

	   		/* Handle and/or */
	   		if( preg_match( "/and|or/i", $search_terms ) )
	   		{
				preg_match_all( "/and|or/i", $search_terms, $matches );
	   		}
	   		
	   		/*
	   		* Build the where & like statement */
	   		if( $this->ipsclass->input['search_for'] == "SHOWALL" ) {
	   			$where = "i.id >= 0";
	   		}
	   		else {
	   		    $where = "{$search_column} LIKE '%{$search_terms}%'";
	   		}

	   		/*
	   		* Run the search */
	   		$this->ipsclass->DB->build_query( array(
	   		        "select"  =>  "i.*",
	   		        "from"    =>  array( "gallery_images" => "i" ),
	   		        "limit"   =>  array( 0,5 ),
	   		        "where"   =>  "{$where}",
	   		        "add_join" => array( array(
	   		                   "select"  => "mem.members_display_name AS name",
	   		                   "from"    => array( "members" => "mem" ),
	   		                   "where"   => "mem.id = i.member_id",
	   		                   "type"    => "left" ) ) ) );

	   		$this->ipsclass->DB->exec_query();
	   		if( $this->ipsclass->DB->get_num_rows() == 0 ) {
	   			return false;
	   		}

	   		$whatwhat = ( $this->ipsclass->input['search_in'] == "caption" ) ? "caption" : "description";
			/*
			* TODO:
			* boolean support, pronto */
			$replace = "\\1<span class='searchlite'>{$search_terms}</span>\\3";
	   		$search = "/{$search_terms}/i";

	   		while( $row = $this->ipsclass->DB->fetch_row() ) {
	   			/*
	   			* Highlight.. yes... no? */
	   			if( preg_match( $search, $row[ $whatwhat ], $match ) ) {
	   		       $row[ $whatwhat ] = preg_replace( $search, "\\1<span class='searchlite'>{$match[0]}</span>\\3", $row[ $whatwhat ] );
	   			}

	   		    $results[] = $row;
	   		}

	   		return $results;
    	}

    }
?>
