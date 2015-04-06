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
* Main/Category 
*
* Displays categories
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

class category
{
        var $ipsclass;
        var $glib;
	var $output;
	var $info;
	var $html;
        var $category;

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
		/**
		* Check input
		*/
		$this->ipsclass->input['cat'] = $this->glib->validate_int( $this->ipsclass->input['cat'] );
		$this->ipsclass->input['cat']  = ( $this->ipsclass->input['cat'] ) ? $this->ipsclass->input['cat'] : 0;
		$this->ipsclass->input['st'] = $this->glib->validate_int( $this->ipsclass->input['st'] );

		/**
		* Load the skin
		*/
        if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_cats'] ) ) 
        {
        	$this->ipsclass->load_template('skin_gallery_cats');
        }
		$this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_cats' ];

		/**
		* Load the categories
		*/
		require( $this->ipsclass->gallery_root .  'categories.php' );
		$this->category = new Categories;
                $this->category->ipsclass =& $this->ipsclass;
                $this->category->glib =& $this->glib;
		$this->category->read_data( true, 'Select a category', $this->ipsclass->input['cat'] );
		$this->category->current = $this->ipsclass->input['cat'];

		/**
		* Grab the parser
		*/
    	require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
    	$this->parser                      =  new parse_bbcode();
    	$this->parser->ipsclass            =& $this->ipsclass;
    	$this->parser->allow_update_caches = 1;

    	/**
    	* Parser options
    	**/
    	$this->parser->parse_html    = 1;
    	$this->parser->parse_nl2br   = 1;
    	$this->parser->parse_smilies = 1;
    	$this->parser->parse_bbcode  = 1;
    	$this->parser->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);
    	
		/**
		* Decide what to do
		*/
		switch( $param )
		{
			case 'show':
			$this->display_cat();
			break;

			case 'list':
			default:
			$this->show_cats();
			break;
		}

		/**
		* Output
		*/

		$this->ipsclass->print->add_output( $this->output );

		$this->ipsclass->print->do_output( array(
		'TITLE'    => $this->title,
                'JS'         => 0,
		'NAV'      => $this->nav,
		)       );


	}

	/**
	* category::display_cat()
	*
	* Displays images within a category, as well as sub categories
	* within a category
	*
	* @return none
	**/
	function display_cat()
	{
		// --------------------------------------------------
		// Are we trying to view the special member category?
		// --------------------------------------------------
		if( $this->ipsclass->input['op'] == 'user' )
		{
			$this->list_mem_albums();
			return;
		}	

		// --------------------------------------------------
		// Grab the current category information
		// --------------------------------------------------
		$cat = $this->category->data[$this->ipsclass->input['cat']];

		// --------------------------------------------------
		// Check for password
		// --------------------------------------------------
		if( $cat['password'] )
		{
			$this->check_password( $cat['id'], $cat['password'], $cat['name'] );
		}

		// --------------------------------------------------
		// Are we allowed to view this category?
		// --------------------------------------------------
		if( ! $this->ipsclass->check_perms( $cat['perms_thumbs'] ) )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}
		
		/*
		* Are we trying to see unapproved images and aren't allowed? */
		if( $this->ipsclass->input['unapproved'] && ! $this->ipsclass->check_perms( $cat['perms_moderate'] ) )  {
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		// --------------------------------------------------
		// Is this an album category?
		// --------------------------------------------------
		if( $cat['album_mode'] )
		{
			$this->list_albums( $cat );
			return;
		}

		// --------------------------------------------------
		// Do we get any buttons?
		// --------------------------------------------------

		// Post Button
		if( $this->ipsclass->check_perms( $cat['perms_images'] ) && $cat['allow_images'] )
		{
			$cat['post_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;cat={$cat['id']}'><{GALLERY_UPLOAD}></a>";
		}

		// Slideshow Button
		$cat['ss_button'] = ( $this->ipsclass->member['g_slideshows'] ) ? "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=slideshow&amp;cat={$cat['id']}'><{A_SSBUTTON}></a>" : "";

		// Media Button
		if( $this->glib->is_media_allowed( $cat ) )
		{
			$cat['media_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;cat={$cat['id']}&amp;media=1'><{MEDIA_UPLOAD}></a>";
		}

		// Multiple Files Upload Button
		if( $this->ipsclass->member['g_multi_file_limit'] || $this->ipsclass->member['g_zip_upload'] )
		{
			/**
			* Allowing images?
			**/
			if( $cat['allow_images'] ) 
			{
				$cat['multi_upload_button'] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=post&amp;cat={$cat['id']}&multi=1'><{GALLERY_MULTI_UPLOAD}></a>";
			}
		}

		// --------------------------------------------------
		// Check to see if we have moderator access
		// --------------------------------------------------
		$approve = 1;
		if( $this->ipsclass->check_perms( $cat['perms_moderate'] ) )
		{
			$approve = 0;

			// Do we need to approve an image?
			if( $this->ipsclass->input['approve_image'] )
			{
				require( $this->ipsclass->gallery_root . 'mod.php' );
				$moderate = new mod;
                                $moderate->ipsclass =& $this->ipsclass;
                                $moderate->glib =& $this->glib;

				if( $this->ipsclass->input['approve'] )
				{
					$moderate->approve_image( $this->ipsclass->input['approve_image'] );
				}
				else
				{
					$moderate->decline_image( $this->ipsclass->input['approve_image'] );
				}
			}
		}

		// --------------------------------------------------
		// Grab our sort/order/prune information
		// --------------------------------------------------
		$sort = $this->glib->build_sort_order_info( $cat['def_view'] );

		// --------------------------------------------------
		// Do page spanning stuff
		// --------------------------------------------------
		$show = $cat['imgs_per_col']*$cat['imgs_per_row'];
		$this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;
		$cat['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $cat['images'],
		                                                   'PER_PAGE'    => $show,
		                                                   'CUR_ST_VAL'  => $this->ipsclass->input['st'],
		                                                   'BASE_URL'    => $this->base_url."?automodule=gallery&cmd=sc&cat={$cat['id']}&sort_key={$sort['SORT_KEY']}&order_key={$sort['ORDER_KEY']}&prune_key={$sort['PRUNE_KEY']}"
		)     );

		// -----------------------------------------------------
		// Grab and output images
		// -----------------------------------------------------
		require( $this->ipsclass->gallery_root .  'lib/imagelisting.php' );
			$this->img_list = new ImageListing();
                        $this->img_list->ipsclass =& $this->ipsclass;
                        $this->img_list->glib =& $this->glib;
                        $this->img_list->init();
		if( $cat['perms_view'] )
		{
			$this->img_list->get_listing_data( array(
														'category'  => $this->ipsclass->input['cat'],
														'st'        => $this->ipsclass->input['st'],
														'show'      => $show,
														'approve'   => 1,
														'sort_key'  => $sort['SORT_KEY'],
														'prune_key' => $sort['PRUNE_KEY'],
														'order_key' => $sort['ORDER_KEY'],
														'pinned'    => 1,
			)     );
			$show = $this->img_list->total_images;
		}

		/**
		* List rules first, run rule_text through the parser
		**/
		if( $cat['cat_rule_method'] == 2 )	
		{
			$rules['title'] = $cat['cat_rule_title'];
			$rules['body']  = $this->parser->pre_display_parse( $cat['cat_rule_text'] );
			$this->output .= $this->ipsclass->compiled_templates['skin_global']->forum_show_rules_full( $rules );
		}
		
		// -----------------------------------------------------
		// Do we need to list some subcategories?
		// -----------------------------------------------------
		if( is_array( $this->category->data[$cat['id']]['child'] ) )
		{
			foreach( $this->category->ordered as $temp )
			{
				$cid = $temp['id'];
				$show_cats[$cid] = $this->category->data[$cid];

				if( is_array( $this->category->data[$cid]['descendants'] ) )
				{
					foreach( $this->category->data[$cid]['descendants'] as $child_id )
					{
						$show_cats[$cid]['images']   += $this->category->data[$child_id]['images'];
						$show_cats[$cid]['comments'] += $this->category->data[$child_id]['comments'];
					}
				}
			}
			$this->list_cats( $show_cats );
		}

		// -----------------------------------------------------
		// Start displaying
		// -----------------------------------------------------
		if( ! $cat['category_only'] )
		{
			// Multimoderation?
			if( $this->ipsclass->check_perms( $cat['perms_moderate'] ) )
			{
				/*
				* Grab category drop down for moving */
            	$dropdown = $this->category->build_dropdown( 'move_cat' );
				$cat['multi_mod'] = $this->html->multi_mod_form( $dropdown );	
				$this->img_list->mod = true;
			}
			else {
				$cat['multi_mod'] = '';
			}
			
			$this->output .= $this->html->cat_view_top( $cat );
			$this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => $cat['imgs_per_col'] ) );
			$this->output .= $this->html->cat_view_end( $cat );

			// -----------------------------------------------------
			// Show the sorting drop downs
			// -----------------------------------------------------
			$this->output = preg_replace( "/<#SORT_KEY_HTML#>/", $sort['SORT_KEY_HTML'], $this->output );
			$this->output = preg_replace( "/<#ORDER_HTML#>/"   , $sort['ORDER_KEY_HTML'], $this->output );
			$this->output = preg_replace( "/<#PRUNE_HTML#>/"   , $sort['PRUNE_KEY_HTML'], $this->output );
			$this->output = preg_replace( "/<#NOW_SHOWING#>/"  , $show, $this->output );
			$this->output = preg_replace( "/<#TOTAL#>/"        , $cat['images'], $this->output );
		}
		
		# Resize JS
		$this->output .= $this->html->cat_page_end();
		
		// -----------------------------------------------------
		// Page Stuff
		// -----------------------------------------------------
		$this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];
		$this->nav[] = "<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>";

		$this->nav = array_merge( $this->nav, $this->category->_build_category_breadcrumbs( $cat['id'] ) );
	}


	/**
	* category::show_cats()
	*
	* This is the "main index" of the gallery
	*
	* @return none
	**/
	function show_cats()
	{
		// ------------------------------------------------
		// Build an array of top level categories
		// ------------------------------------------------
		if( is_array( $this->category->ordered ) )
		{
			foreach( $this->category->ordered as $t_cat )
			{
				if( ! $t_cat['parent'] )
				{
					$top_cats[] = $t_cat;
				}
			}
		}
			
		// ------------------------------------------------
		// Show the category
		// ------------------------------------------------
		if( is_array( $top_cats ) )
		{
			foreach( $top_cats as $i )
			{
				if( is_array( $this->category->data[$i['id']]['descendants'] ) )
				{
					foreach( $this->category->data[$i['id']]['descendants'] as $child_id )
					{
						$i['images']   += $this->category->data[$child_id]['images'];
						$i['comments'] += $this->category->data[$child_id]['comments'];
					}
				}

				if( $i['id'] )
				{
					$cats[$i['c_order']] = $i;
				}
			}
		}

		// ------------------------------------------------
		// Show Stats?
		// ------------------------------------------------
		if( $this->ipsclass->vars['gallery_stats'] )
		{	
			if( ! $this->stats )
			{
				$this->ipsclass->DB->simple_construct( array( 'select' => 'count( * ) AS IMG_TOTAL, sum( file_size ) as TOTAL_SIZE, sum( views ) as TOTAL_VIEWS, sum( comments ) as COM_TOTAL, MAX( id ) as LAST_PIC',
				'from'   => 'gallery_images',
				'where'  => 'approved=1' ) );
				$this->ipsclass->DB->simple_exec();

				$this->stats = ( $this->stats ) ? $this->stats :$this->ipsclass->DB->fetch_row();
			}

			$stats = $this->ipsclass->lang['show_stats'];
			$stats = preg_replace( "/<#IMG_TOTAL#>/"  , $this->stats['IMG_TOTAL']  , $stats );
			$stats = preg_replace( "/<#TOTAL_SIZE#>/" , $this->glib->byte_to_kb( $this->stats['TOTAL_SIZE'] ), $stats );
			$stats = preg_replace( "/<#TOTAL_VIEWS#>/", $this->stats['TOTAL_VIEWS'], $stats );
			$stats = preg_replace( "/<#COM_TOTAL#>/"  , $this->stats['COM_TOTAL'], $stats );

			$stats = $this->html->stats( $stats );
			
			/*
			* Online listage */
			$active = $this->glib->get_active_users();
			
			$totals = $this->ipsclass->lang[ 'gallery_stats' ];
			$totals = str_replace( "<#USERS#>", $active['TOTAL'], $totals );
			
			/*  Guests, anon */
			$str['guests'] = str_replace( "<#USERS#>", $active['GUESTS'], $this->ipsclass->lang['gallery_stats_guests'] );
			$str['reg'] = str_replace( "<#USERS#>", ( $active['MEMBERS'] ), $this->ipsclass->lang['gallery_stats_reg'] );
			$str['anon'] = str_replace( "<#USERS#>", $active['ANON'], $this->ipsclass->lang['gallery_stats_anon'] );
		
			$stats .= $this->html->active_users( $active['NAMES'], $totals, $str );

			$num = $this->ipsclass->vars['gallery_stats_rows'] * $this->ipsclass->vars['gallery_stats_cols'];
			$stats = str_replace( "<#NUM#>", $num, $stats );			
		}
		
		/* Searching offline? */
		if( $this->ipsclass->vars['gallery_allow_search'] )  {
			$this->output .= $this->html->search_form( $this->ipsclass->lang['search_for'], 'all', 0 );
		}

		// ------------------------------------------------
		// Figure out ordering
		// ------------------------------------------------
		for( $i = 1; $i <=4; $i++ )
		{
			switch( $this->_idx_order( $i ) )
			{
				case 'gallery_cat_index_pos':
				$this->list_cats( $cats, $this->ipsclass->vars['gallery_user_category'] );
				break;

				case 'gallery_stats_index_pos':
				$this->output .= $stats;
				break;

				case 'gallery_last5_index_pos':

				$this->output .= $this->_get_last5();
				break;

				case 'gallery_rand5_index_pos':
				$this->output .= $this->_get_rand5();
				break;
			}
		}
		
		$this->output .= $this->html->cat_page_end();
		
		// Page Stuff
		$this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];
		$this->nav[] = $this->ipsclass->lang['gallery'];
	}


	/**
	* category::list_mem_albums()
	*
	* This is the special member category, which essentially lists all
	* images that a user has access to
	*
	* @return none
	**/
	function list_mem_albums()
	{
		// ---------------------------------------------
		// Can we access the members category?
		// ---------------------------------------------
		if( ! $this->check_memcat_acccess() )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}

		// ---------------------------------------------
		// Is this an album listing only?
		// ---------------------------------------------
		if( $this->ipsclass->vars['gallery_album_where'] == 'album' && $this->ipsclass->vars['gallery_album_list'] )
		{
			$this->list_albums( array( 'id'           => 0,
			                           'imgs_per_row' => $this->ipsclass->vars['gallery_user_row'],
			                           'name'         => $this->ipsclass->lang['mem_gallery'],

			)      );
			return;
		}

		// ---------------------------------------------
		// What categories can we view pics from?
		// ---------------------------------------------
		if( $this->ipsclass->vars['gallery_album_where'] == 'both' || $this->ipsclass->vars['gallery_album_where'] == 'cat' )
		{
			$allow_cats   = $this->glib->get_allowed_cats();
			if( is_array( $allow_cats ) )
			{
				$where[] = 'category_id IN ( '.implode( ",", $allow_cats ).' )';
			}
		}
		else
		{
			$where[] = 'category_id=0';
			$show_cats = 'no';
		}

		// ---------------------------------------------
		// What albums can we view pics from?
		// ---------------------------------------------
		if( $this->ipsclass->vars['gallery_album_where'] == 'both' || $this->ipsclass->vars['gallery_album_where'] == 'album' )
		{
			$allow_albums = $this->glib->get_allowed_albums();
			if( is_array( $allow_albums ) )
			{
				$where[] = 'album_id IN ( '.implode( ",", $allow_albums ).' )';
			}
		}
		else
		{
			$where[] = 'album_id=0';
			$show_albums = 'no';
		}

		// ---------------------------------------------
		// Do page spanning stuff
		// ---------------------------------------------
		if( is_array( $where ) )
		{
			foreach( $where as $w )
			{
				$q_where .= " {$w} OR ";
			}

			$q_where = preg_replace( "/( OR )$/", "", $q_where );

			$this->ipsclass->DB->simple_construct( array( 'select' => 'count(id) AS total', 'from' => 'gallery_images', 'where' => $q_where ) );
			$this->ipsclass->DB->simple_exec();
			$all = $this->ipsclass->DB->fetch_row();
		}

		$this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;
		$show = $this->ipsclass->vars['gallery_user_col'] * $this->ipsclass->vars['gallery_user_row'];

		// ---------------------------------------------
		// Sort Stuff
		// ---------------------------------------------
		$sort = $this->glib->build_sort_order_info( "{$this->ipsclass->vars['gallery_album_sort']}:{$this->ipsclass->vars['gallery_album_order']}:{$this->ipsclass->vars['gallery_album_prune']}" );

		$all_imgs['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'   => $all['total'],
		'PER_PAGE'    => $show,
		'CUR_ST_VAL'  => $this->ipsclass->input['st'],
		'BASE_URL'    => $this->base_url."?automodule=gallery&cmd=sc&op=user&sort_key={$sort['SORT_KEY']}&order_key={$sort['ORDER_KEY']}&prune_key={$sort['PRUNE_KEY']}"
		)     );

		// ---------------------------------------------
		// Display the images
		// ---------------------------------------------
		$all_imgs['name'] = $this->ipsclass->lang['mem_gallery'];
		$this->output .= $this->html->cat_view_top( $all_imgs );


		require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );
		$this->img_list = new ImageListing();
                $this->img_list->ipsclass =& $this->ipsclass;
                $this->img_list->glib =& $this->glib;
                $this->img_list->init();

		$this->img_list->get_listing_data( array(
													'st'           => $this->ipsclass->input['st'],
													'show'         => $show,
													'approve'      => 1,
													'sort_key'     => $sort['SORT_KEY'],
													'order_key'    => $sort['ORDER_KEY'],
													'prune_key'    => $sort['PRUNE_KEY'],
													'category'     => $show_cats,
													'album'        => $show_albums,
													'allow_cats'   => $allow_cats,
													'allow_albums' => $allow_albums,
		)     );

		// ---------------------------------------------
		// Start displaying
		// ---------------------------------------------
		$this->output .= $this->img_list->get_html_listing( array( 'imgs_per_col' => $this->ipsclass->vars['gallery_user_col'] ) );

		// ---------------------------------------------
		// Finish up output
		// ---------------------------------------------
		
		/*
		* 5555 fix */
		$arr = array( 'ss_button', 'multi_upload_button', 'post_button', 'media_button', 'CATS', 'SHOW_PAGES', 'multi_mod' );
		foreach( $arr as $arr ) {
			if( !isset( $all_imgs[ $arr ] ) ) {
				$all_imgs[ $arr ] = '';
			}
		}
				
		$this->output .= $this->html->cat_view_end( $all_imgs );

		$this->output = preg_replace( "/<#SORT_KEY_HTML#>/", $sort['SORT_KEY_HTML'] , $this->output );
		$this->output = preg_replace( "/<#ORDER_HTML#>/"   , $sort['ORDER_KEY_HTML'], $this->output );
		$this->output = preg_replace( "/<#PRUNE_HTML#>/"   , $sort['PRUNE_KEY_HTML'], $this->output );
		$this->output = preg_replace( "/<#NOW_SHOWING#>/", ( $showing ) ? $showing : $this->ipsclass->DB->get_num_rows(), $this->output );
		$this->output = preg_replace( "/<#TOTAL#>/",       $all['total'] , $this->output );

		// ---------------------------------------------
		// Page Stuff
		// ---------------------------------------------
		$this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];
		$this->nav[] = "<a href='{$this->ipsclass->base_url}automodule=gallery'>{$this->ipsclass->lang['gallery']}</a>";
		$this->nav[] = $this->ipsclass->lang['mem_gallery'];

	}

	/**
	* category::list_albums()
	*
	* Displays an album category
	*
	* @return none
	**/
	function list_albums( $cat )
	{
		/**
		 * Can we view private albums?
		 */
		if( $this->ipsclass->member['g_mod_albums'] )
		{
			$album_mod = "";	
		}
		else
		{
			$album_mod = " AND a.public_album=1";
		}

		/**
		 * Count the number of albums there are
		 */		
		$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(*) as total',
		                              'from'   => 'gallery_albums a',
		                              'where'  => "a.category_id={$cat['id']} AND a.images > 0 {$album_mod}" ) );
		$this->ipsclass->DB->simple_exec();
		$all = $this->ipsclass->DB->fetch_row();
		
		/*
		* Sort stuff */
		$sort = ( isset( $this->ipsclass->input['sort_key'] ) ) ? $this->ipsclass->input['sort_key'] : $this->ipsclass->vars['gallery_album_sort'];
		$order = ( isset( $this->ipsclass->input['order_key'] ) ) ? $this->ipsclass->input['order_key'] : $this->ipsclass->vars['gallery_album_order'];
		$prune = ( isset( $this->ipsclass->input['prune_key'] ) ) ? $this->ipsclass->input['prune_key'] : $this->ipsclass->vars['gallery_album_prune'];
		$cat['def_view'] = join( ":", array( $sort, $order, $prune ) );
		$sort = $this->glib->build_sort_order_info( $cat['def_view'] );

		/**
		 * Build the page span links
		 */	
		$this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;
		
		$op_cat = ( isset( $this->ipsclass->input['op'] ) ) ? '&op=user' : "&cat={$cat['id']}";
		$all_imgs['SHOW_PAGES'] = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $all['total'],
		                                                        'PER_PAGE'    => $this->ipsclass->vars['gallery_albums_page'],
		                                                        'CUR_ST_VAL'  => $this->ipsclass->input['st'],
		                                                        'BASE_URL'    => $this->base_url."?automodule=gallery&cmd=sc{$op_cat}&sort_key={$sort['SORT_KEY']}&order_key={$sort['ORDER_KEY']}&prune_key={$sort['PRUNE_KEY']}"
		)     );

		/**
		 * Create new album button
		 */	
		if( $this->ipsclass->check_perms( $cat['perms_images'] ) && $this->ipsclass->member['g_create_albums'] )
		{
			$all_imgs['post_button'] = "<a href='{$this->ipsclass->base_url}act=module&module=gallery&cmd=albums&op=addnew&cat={$cat['id']}'><{GALLERY_NEW_ALBUM}></a>";
		}

		/**
		 * Top of the album listing
		 */	
		$all_imgs['name'] = $cat['name'];
		$this->output .= $this->html->cat_view_top( $all_imgs );

		/**
		 * Set a default
		 */		
		$this->ipsclass->vars['gallery_albums_page'] = ( $this->ipsclass->vars['gallery_albums_page'] ) ? $this->ipsclass->vars['gallery_albums_page'] : 5;

		/**
		 * Query for the albums
		 */	
		$pre = ( $sort['SORT_KEY'] == 'name' ) ? 'm' : 'i';
		$this->ipsclass->DB->cache_add_query( 'get_album_list',
												array( 'pre'              => $pre,
		       										   'SORT_KEY'         => $sort['SORT_KEY'],
			   										   'ORDER_KEY'        => $sort['ORDER_KEY'],
		       										   'st'               => intval( $this->ipsclass->input['st'] ),
									    		       'gallery_user_row' => $this->ipsclass->vars['gallery_albums_page'],
												       'cat'              => " AND a.category_id={$cat['id']}",
												       'album_mod'        => $album_mod
							), 'gallery_sql_queries'      );
		$q = $this->ipsclass->DB->simple_exec();
		$showing = $this->ipsclass->DB->get_num_rows();

		/**
		 * Output the albums we found
		 */	
		$this->output .= $this->html->album_top();
		while( $i = $this->ipsclass->DB->fetch_row( $q ) )
		{
			$this->output .= $this->_get_album_entry( $i );
		}

		/**
		 * Finisn of the album listing
		 */	
		$this->output .= $this->html->album_end();

		/*
		* 5555 fix */
		$arr = array( 'ss_button', 'multi_upload_button', 'post_button', 'media_button', 'CATS', 'SHOW_PAGES', 'multi_mod' );
		foreach( $arr as $arr ) {
			if( !isset( $all_imgs[ $arr ] ) ) {
				$show[ $arr ] = '';
			}
		}
		
		$this->output .= $this->html->cat_view_end( $show );

		$this->output = preg_replace( "/<#SORT_KEY_HTML#>/", $sort['SORT_KEY_HTML'] , $this->output );
		$this->output = preg_replace( "/<#ORDER_HTML#>/"   , $sort['ORDER_KEY_HTML'], $this->output );
		$this->output = preg_replace( "/<#PRUNE_HTML#>/"   , $sort['PRUNE_KEY_HTML'], $this->output );
		$this->output = preg_replace( "/<#NOW_SHOWING#>/", ( $showing ) ? $showing : $this->ipsclass->DB->get_num_rows(), $this->output );
		$this->output = preg_replace( "/<#TOTAL#>/",       $all['total'] , $this->output );

		/**
		 * General page stuff
		 */	
		$this->title = $this->ipsclass->vars['board_name'].$this->ipsclass->lang['sep'].$this->ipsclass->lang['gallery'];
		$this->nav[] = "<a href='{$this->ipsclass->base_url}automodule=gallery'>{$this->ipsclass->lang['gallery']}</a>";
		$this->nav = array_merge( $this->nav, $this->category->_build_category_breadcrumbs( $cat['id'] ) );

	}

	/**********************************************************************
	*
	* Utility Functions
	*
	**/

	/**
	* category::_get_album_entry()
	*
	* Formats and returns an album row for displaying
	*
	* @return none
	**/
	function _get_album_entry( $i )
	{
		/*
		* Do we have private permissions to this album? */
		if ( !$i['public_album'] )  {
			
		}
		
		/**
		* Do we have a new image in this album?
		*/
		$i['img_new_post'] = ( $this->ipsclass->member['last_activity'] < $i['date'] ) ? "<{C_ON}>" : "<{C_OFF}>";
		if( ! $this->ipsclass->member['id'] )
		{
			$i['img_new_post'] = "<{C_OFF}>";
		}

		/**
		* Make the member name link
		*/
		$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$i['member_id']}" ) );
		$this->ipsclass->DB->simple_exec();
		$temp = $this->ipsclass->DB->fetch_row();
		$i['last_name'] = $temp['name'];
		$i['member'] = $this->glib->make_name_link( $i['member_id'], $i['last_name'] );

		/**
		* Has there been a picture posted in this album?
		*/
		if( $i['last_pic'] )
		{
			$i['date']        = $this->ipsclass->get_date( $i['date'], 'LONG' );
			$i['last_pic']    = $this->glib->make_image_link( $i, $i['thumbnail'], $i['last_pic'] );
			$i['last_poster'] = $this->glib->make_name_link( $i['member_id'], $i['last_name'] );
		}
		else
		{
			$i['date']        = $this->ipsclass->lang['none'];
			$i['last_pic']    = $this->ipsclass->lang['none'];
			$i['last_poster'] = $this->ipsclass->lang['none'];
		}
		
		/**
		 * Hidden album?
		 */
		if( ! $i['public_album'] )
		{
			$i['name'] .= " ( {$this->ipsclass->lang['album_private']} )";
			$i['css'] = 'darkrow3';
		}
		else
		{
			$i['css'] = "row2";	
		}

		/**
		* Link to this album
		*/
		$i['link'] = "{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;cmd=user&amp;user={$i['member_id']}&amp;op=view_album&album={$i['id']}";

		return $this->html->album_row( $i );
	}



	/**
	* category::list_cats()
	*
	* Renders the html for all categoreis passed in through the
	* $res variable.  $user_cat is set if the special member category
	* should be shown.
	*
	* @param array $res
	* @param integer $user_cat
	* @return none
	**/
	function list_cats( $res, $user_cat=0 )
	{
		// ------------------------------------------------
		// Top o' the cats
		// ------------------------------------------------
		if( $this->ipsclass->vars['gallery_display_category'] == 'legacy' )  {
			$this->output .= '<br />' . $this->html->legacy_cat_header();
		}
		else {
			$this->output .= $this->html->cat_top();
		}
		
        $x = 0;
		// ------------------------------------------------
		// Are we showing the special category?
		// ------------------------------------------------
		if( $user_cat )
		{
			if( $this->ipsclass->vars['gallery_album_where'] == 'cat' )
			{
				$where = " AND i.album_id=0 ";
			}
			else if ( $this->ipsclass->vars['gallery_album_where'] == 'album' )
			{
				$where = " AND i.category_id=0 ";
			}

			$this->ipsclass->DB->cache_add_query( 'get_cat_stats', array( 'where' => $where ), 'gallery_sql_queries' );
			$this->ipsclass->DB->simple_exec();

			$stats = $this->ipsclass->DB->fetch_row();

			$info['name']         = $this->ipsclass->lang['mem_gallery'];
			$info['description']  = $this->ipsclass->lang['mem_gallery_desc'];
			$info['images']       = ( $stats['IMG_TOTAL'] ) ? $stats['IMG_TOTAL'] : 0;
			$info['comments']     = ( $stats['COM_TOTAL'] ) ? $stats['COM_TOTAL'] : 0;

			$info['img_new_post'] = ( $this->ipsclass->member['last_activity'] < $info['date'] ) ? "<{C_ON}>" : "<{C_OFF}>";
			if( ! $this->ipsclass->member['id'] )
			{
				$info['img_new_post'] = "<{C_OFF}>";
			}

			if( $stats['LAST_PIC'] )
			{
				$this->ipsclass->DB->cache_add_query( 'get_last_pic_info', array( 'LAST_PIC' => $stats['LAST_PIC'] ), 'gallery_sql_queries' );
				$this->ipsclass->DB->simple_exec();

				$info = array_merge( $info, $this->ipsclass->DB->fetch_row() );

				$info['date']        = $this->ipsclass->get_date( $stats['LAST_TIME'], 'LONG' );
				$info['last_pic']    = ( $this->glib->last_pic( $info['id'], "&op=user" ) );
				$info['last_poster'] = $this->glib->make_name_link( $info['mid'], $info['mname'] );
			}
			else
			{
				$info['date']        = "";//$this->ipsclass->lang['none'];
				$info['last_pic']    = "";//$this->ipsclass->lang['none'];
				$info['last_poster'] = "";//$this->ipsclass->lang['none'];
			}
			$info['link'] = "{$this->ipsclass->base_url}automodule=gallery&cmd=sc&op=user";
		}

		if( $user_cat && $this->ipsclass->vars['gallery_album_position'] == 'top' )
		{
			if( $this->check_memcat_acccess() )
			{
				if( $this->ipsclass->vars['gallery_display_photobar'] == "show" )  {
	            	$info['photo_info'] = $this->glib->make_info_div( $info['id'], true );
				}
				$info['cat_width'] = '100%';
				$info['colspan'] = "colspan='" . count( $res ) . "' ";
				if( $this->ipsclass->vars['gallery_display_category'] == 'legacy' )  {
					if( !$this->ipsclass->vars['gallery_show_lastpic'] )  {
							$info['last_pic'] = '';
					}
					$this->output .= $this->html->legacy_cat_row( $info );
				}
				else {
					$this->output .= $this->html->cat_start_tr();
					$this->output .= $this->html->cat_row( $info );
					$this->output .= $this->html->cat_end_tr();
				}
			}
		}
        $x++;
		// ------------------------------------------------
		// Start showing the cats
		// ------------------------------------------------
		if( is_array( $res ) )
		{
			$y = count( $res );
			
			foreach( $res as $i )
			{
				// Hmm, can we view this category?
				if( $this->ipsclass->check_perms( $i['perms_thumbs'] ) )
				{
					if( $this->ipsclass->vars['gallery_display_category'] == 'block' && $this->ipsclass->input['cmd'] != 'sc' ) {
					   if( ( $x % 3 ) == 1 )  {
    						/* Start row */
						   $this->output .= $this->html->cat_start_tr();
					   }
					}
					if( $this->ipsclass->vars['gallery_display_category'] == 'forum' ) {
						$this->output .= $this->html->cat_start_tr();
					}
					
					$subcat = ( is_array( $i['child'] ) ) ? '_CAT' : '';
					$i['img_new_post'] = ( $this->ipsclass->member['last_activity'] < $i['date'] ) ? "<{C_ON{$subcat}}>" : "<{C_OFF{$subcat}}>";
					if( ! $this->ipsclass->member['id'] )
					{
						$i['img_new_post'] = "<{C_OFF{$subcat}}>";
					}

					// Check to see if have a last pic link to show
					if( $i['last_pic'] )
					{
						$i['date']        = $this->ipsclass->get_date( $i['date'], 'LONG' );
						$last = $i['last_pic'];
						$i['last_pic']    = ( $this->glib->last_pic( $i['last_pic'], "&cat={$i['id']}" ) ); 
						
						/*
					     * Generate photo div */
						if( $this->ipsclass->vars['gallery_display_photobar'] == "show" )  {
					     	$i[ 'photo_info' ] = $this->glib->make_info_div( $last );
						}
					}
					else
					{
						$i['date']        = "";
						$i['last_pic']    = $this->ipsclass->lang['none_last'];
					}
					// Do we have images to be approved?
					if( $i['mod_images'] && $this->ipsclass->check_perms( $i['perms_moderate'] ) )
					{
						$i['mod_images'] = str_replace( "<#NUM#>", $i['mod_images'], $this->ipsclass->lang['approve_img'] );
						/*
						* Build URL  */
						$approve_url = "{$this->ipsclass->base_url}automodule=gallery&cmd=sc&cat={$i['id']}&unapproved=1";
						$i['approve_images'] = $this->html->approve_images_line( $i['mod_images'], $approve_url );
					}
					else
					{
						$i['approve_images'] = '';
					}
					$i['description'] = html_entity_decode( $i['description'] );
					// Check for subcats
					if( is_array( $i['child'] ) )
					{
						$subs = array();
						foreach( $i['child'] as $child )
						{
							$subs[] = "<a href='{$this->ipsclass->base_url}automodule=gallery&cmd=sc&cat={$child}'>{$this->category->data[$child]['name']}</a>";
						}
						/*
						* Drop down or list? */
						if( $this->ipsclass->vars['gallery_display_subcats'] == "dropdown" && $this->ipsclass->vars['gallery_display_category'] != 'legacy' )  {
                        	$i['has_subcats'] = "<a href=\"javascript:toggleview('sub_{$i['id']}');\"><{E_PLUS}></a>&nbsp;";
							$i['sub_cats'] = $this->ipsclass->lang['sub_cats'] . implode( "<br />", $subs );
						}
						else {
							$i['desc_subcats'] = '<br />' . $this->ipsclass->lang['sub_cats'] . implode( ",&nbsp;", $subs );
						}
					}

					$i['link'] = "{$this->ipsclass->base_url}automodule=gallery&cmd=sc&cat={$i['id']}";

					$this->ipsclass->DB->simple_construct( array( 'select' => 'members_display_name AS name', 'from' => 'members', 'where' => "id={$i['last_member_id']}" ) );
					$this->ipsclass->DB->simple_exec();
					$temp = $this->ipsclass->DB->fetch_row();
					$i['last_name'] = $temp['name'];
					$i['last_poster'] = $this->glib->make_name_link( $i['last_member_id'], $i['last_name'] );
					$i['cat_width'] = ( $this->ipsclass->vars['gallery_display_category'] == 'block' ) ? '33%' : '100%';
					
					/*
					* Generate photo div 
					$photo = $this->glib->make_info_div( $last );  */
					if( $this->ipsclass->vars['gallery_display_category'] == 'legacy' )  {
						if( !$this->ipsclass->vars['gallery_show_lastpic'] )  {
							$i['last_pic'] = '';
						}
						$this->output .= $this->html->legacy_cat_row( $i );
					}
					else {
						$this->output .= $this->html->cat_row( $i );
					}
					
					if( $this->ipsclass->vars['gallery_display_category'] == 'block' ) {
					   if( ( $x % 3 ) == 0 )  {
						  /* End row */
						  $this->output .= $this->html->cat_end_tr();
					   }
					}
					if( $this->ipsclass->vars['gallery_display_category'] == 'forum' ) {
						$this->output .= $this->html->cat_end_tr();
					}
					$x++;
				}
			}
		}

		if( $user_cat && $this->ipsclass->vars['gallery_album_position'] == 'bottom' )
		{
			if( $this->check_memcat_acccess() )
			{
				if( $this->ipsclass->vars['gallery_display_photobar'] == "show" ) {
					$info[ 'photo_info' ] = $this->glib->make_info_div( $info['id'] );
				}
				if( $this->ipsclass->vars['gallery_display_category'] == 'legacy' )  {
					if( !$this->ipsclass->vars['gallery_show_lastpic'] )  
					{
							$info['last_pic'] = '';
					}
					$this->output .= $this->html->legacy_cat_row( $info );
				}
				else {
					$info['cat_width'] = '100%';
					$info['colspan'] = "colspan='" . count( $res ) . "' ";
					$this->output .= $this->html->cat_end_tr();
					$this->output .= $this->html->cat_start_tr();
					$this->output .= $this->html->cat_row( $info );
					$this->output .= $this->html->cat_end_tr();
				}
			}
		}

		if( $this->ipsclass->vars['gallery_display_category'] != 'legacy' )  {
			$this->output .= $this->html->cat_end();
		}
		else {
			$this->output .= $this->html->legacy_cat_end();
		}
	}

	/**
	* category::_get_rand5()
	*
	* Returns 5 random images
	*
	* @return string
	**/
	function _get_rand5()
	{
		// ------------------------------------------------
		// Show 5 random images?
		// ------------------------------------------------
		if( $this->ipsclass->vars['gallery_random_images'] )
		{
			if( $this->ipsclass->vars['gallery_stats_where'] == 'both' || $this->ipsclass->vars['gallery_stats_where'] == 'cat' )
			{
				$allow_cats = ( $allow_cats ) ? $allow_cats : $this->glib->get_allowed_cats( 1, $this->category->data );
			}
			else
			{
				$show_cats = 'no';
			}

			if( $this->ipsclass->vars['gallery_stats_where'] == 'both' || $this->ipsclass->vars['gallery_stats_where'] == 'album' )
			{
				$allow_albums = ( $allow_albums ) ? $allow_albums : $this->glib->get_allowed_albums();
			}
			else
			{
				$show_albums = 'no';
			}

			if( ! $this->img_list )
			{
				require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );
				$this->img_list = new ImageListing();
                                $this->img_list->ipsclass =& $this->ipsclass;
                                $this->img_list->glib =& $this->glib;
                $this->img_list->init();
			}
			
			$total = $this->ipsclass->vars['gallery_idx_num_row'] * $this->ipsclass->vars['gallery_idx_num_col'];			

			$this->img_list->get_listing_data( array(
														'st'           => 0,
														'show'         => $total,
														'approve'      => 1,
														'sort_key'     => 'RAND()',
														'album'        => $show_albums,
														'category'     => $show_cats,
														'allow_cats'   => $allow_cats,
														'allow_albums' => $allow_albums,
											)     );

			$rand5 .= $this->html->index_list_top( str_replace( "<#NUM#>", $total, $this->ipsclass->lang['random5'] ) );
			$rand5 .= $this->img_list->get_html_listing( array( 'imgs_per_col' => $this->ipsclass->vars['gallery_idx_num_col'],
			                                                    'imgs_per_row' => $this->ipsclass->vars['gallery_idx_num_row']  ) );
			$rand5 .= $this->html->index_list_end();

			return $rand5;
		}
	}

	/**
	* category::_get_last5()
	*
	* Returns the last 5 images posted
	*
	* @return string
	**/
	function _get_last5()
	{
		if( $this->ipsclass->vars['gallery_last5_images'] )
		{
			if( ! $this->img_list )
			{
				require( $this->ipsclass->gallery_root . 'lib/imagelisting.php' );

				$this->img_list = new ImageListing();
                $this->img_list->ipsclass =& $this->ipsclass;
                $this->img_list->glib =& $this->glib;
                $this->img_list->init();
			}

			if( $this->ipsclass->vars['gallery_stats_where'] == 'both' || $this->ipsclass->vars['gallery_stats_where'] == 'cat' )
			{
				$allow_cats = ( $allow_cats ) ? $allow_cats : $this->glib->get_allowed_cats( 1, $this->category->data );
			}
			else
			{
				$show_cats = 'no';
			}

			if( $this->ipsclass->vars['gallery_stats_where'] == 'both' || $this->ipsclass->vars['gallery_stats_where'] == 'album' )
			{
				$allow_albums = ( $allow_albums ) ? $allow_albums : $this->glib->get_allowed_albums();
			}
			else
			{
				$show_albums = 'no';
			}
			
			$total = $this->ipsclass->vars['gallery_idx_num_row'] * $this->ipsclass->vars['gallery_idx_num_col'];

			$this->img_list->get_listing_data( array(
														'st'           => 0,
														'show'         => $total,
														'approve'      => 1,
														'sort_key'     => 'i.id',
														'order_key'    => 'desc',
														'album'        => $show_albums,
														'category'     => $show_cats,
														'allow_cats'   => $allow_cats,
														'allow_albums' => $allow_albums,
			)     );

			$last5 .= $this->html->index_list_top( str_replace( "<#NUM#>", $total, $this->ipsclass->lang['last5'] ) );
			$last5 .= $this->img_list->get_html_listing( array( 'imgs_per_col' => $this->ipsclass->vars['gallery_idx_num_col'],
			                                                    'imgs_per_row' => $this->ipsclass->vars['gallery_idx_num_row']  ) );
			$last5 .= $this->html->index_list_end();

			return $last5;
		}
	}

	/**
	* category::_idx_order()
	*
	* Used for ordering the index page
	*
	* @param integer $pos
	*
	* @return bool
	**/
	function _idx_order( $pos )
	{
		$elements = array( 'gallery_cat_index_pos',
		'gallery_stats_index_pos',
		'gallery_last5_index_pos',
		'gallery_rand5_index_pos', );

		foreach( $elements as $element )
		{
			if( $this->ipsclass->vars[$element] == $pos )
			{
				return $element;
			}
		}
	}

	/**
	* category::check_memcat_acccess()
	*
	* Checks to see if the user is allowed to view the members category

	* @return bool
	**/
	function check_memcat_acccess()
	{
		if( $this->ipsclass->vars['gallery_user_gview'] )
		{
			return true;
		}

		if( $this->ipsclass->member['mgroup'] == 1 )
		{
			return false;
		}

		if( ! $this->ipsclass->member['id'] )
		{
			return false;
		}

		return true;

	}

	/**
	* category::check_password()
	*
	* Checks to see if a password has been set.  $cid is the
	* id for the category, $password the passowerd, and $name
	* the name of the category.
	*
	* @param integer $cid
	* @param integer $password
	* @param $name $name
	* @return void
	**/
	function check_password( $cid, $password, $name )
	{
		global $_COOKIE;

		if( ! $_COOKIE[ $this->ipsclass->vars['cookie_id'].'IG'.$cid ] == $password )
		{
			if( $this->ipsclass->input['login'] )
			{
				$this->check_login( $password, $cid );
			}
			else
			{
				$this->forum_login( $cid, $name );
			}
		}
	}


	/**
	* category::check_login()
	*
	* Process a login attempt
	*
	* @param string $password
	* @param integer $cid
	* @return void
	**/
	function check_login( $password, $cid )
	{
		if( $this->ipsclass->input['f_password'] == "" )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'pass_blank' ) );
		}

		if( $this->ipsclass->input['f_password'] != $password )
		{
			$this->ipsclass->Error( array( 'LEVEL' => 1, 'MSG' => 'wrong_pass' ) );
		}

		$this->ipsclass->my_setcookie( "IG".$cid, $this->ipsclass->input['f_password'] );

		$this->ipsclass->print->redirect_screen( $this->ipsclass->lang['logged_in'] , "act=module&amp;module=gallery&amp;cmd=sc&amp;cat={$cid}" );
	}



	/**
	* category::forum_login()
	*
	* Shows the forum login
	*
	* @param integer $cid
	* @param string $name
	* @return void
	**/
	function forum_login( $cid, $name )
	{
		$this->output = $this->html->cat_log_in( $cid );

		$this->ipsclass->print->add_output( "$this->output" );

		$this->ipsclass->print->do_output( array( 'TITLE'    => $this->ipsclass->vars['board_name']." -> ".$name,
		'JS'       => 0,
		'NAV'      => array(
		"<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery'>{$this->ipsclass->lang['gallery']}</a>",
		"<a href='{$this->ipsclass->base_url}act=module&amp;module=gallery&amp;sc={$cid}'>{$name}</a>",
		),
		) );

	}



}
?>
