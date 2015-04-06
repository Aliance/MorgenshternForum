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
* Library/Comment View
*
* Handles comment display
*
* @package		Gallery
* @subpackage 	Library
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		2.0
*/

class comment_view
{
        var $ipsclass;
        var $glib;

	var $output;
	var $info;
	var $html;
        var $ipb_html;

        var $custom_fields;
        var $qpids;
        var $pids;
        var $parser;
        var $mem_titles;

        var $data;
        var $is_moderator;
        var $no_comments = false;

        function comment_view() {
        	
        }

	function init()
	{	
		/**
		* Get the html and language files
		*/
		/*
        * Fatal error bug fix */
        if( !is_object( $this->ipsclass->compiled_templates['skin_gallery_comments'] ) ) {
        	$this->ipsclass->load_template( 'skin_gallery_comments' );
        }
        $this->html = $this->ipsclass->compiled_templates[ 'skin_gallery_comments' ];
        
        if( !is_object( $this->ipsclass->compiled_templates['skin_global'] ) ) {
        	$this->ipsclass->load_template( 'skin_global' );
        }
        
        /*
        * Fatal error bug fix */
        if( !is_object( $this->ipsclass->compiled_templates['skin_topic'] ) ) {
        	$this->ipsclass->load_template( 'skin_topic' );
        }
        $this->ipb_html = $this->ipsclass->compiled_templates[ 'skin_topic' ];
        
		/**
		* Grab the parser
		*/
		require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $this->parser                      =  new parse_bbcode();
        $this->parser->ipsclass            =& $this->ipsclass;
        $this->parser->allow_update_caches = 1;
        
        $this->parser->parse_html    = 0;
        $this->parser->parse_nl2br   = 1;
        $this->parser->parse_smilies = 1;
        $this->parser->parse_bbcode  = 1;

        $this->bbcode->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);

		/**
		* Get cached info
		*/
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key IN ( 'multimod','ranks','profilefields' )" ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( $r['cs_array'] )
			{
				$this->ipsclass->cache[ $r['cs_key'] ] = unserialize(stripslashes($r['cs_value']));
			}
			else
			{
				$this->ipsclass->cache[ $r['cs_key'] ] = $r['cs_value'];
			}
		}

		$this->mem_titles = $this->ipsclass->cache['ranks'];

		/**
		* Custom Profile Fields
		*/
		if ( $this->ipsclass->vars['custom_profile_topic'] == 1 )
		{
			require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
			$this->custom_fields = new custom_fields( $this->ipsclass->DB );

			$this->custom_fields->member_id  = $this->ipsclass->member['id'];
			$this->custom_fields->cache_data = $this->ipsclass->cache['profilefields'];
			$this->custom_fields->admin      = intval($this->ipsclass->member['g_access_cp']);
			$this->custom_fields->supmod     = intval($this->ipsclass->member['g_is_supmod']);
		}

		/**
		* Multi Quote
		*/
		$this->qpids = $this->ipsclass->my_getcookie( 'gal_pids' );
		
		/**
		* Multi Mod
		*/
		$this->ipsclass->input['selectedpids'] = $this->ipsclass->my_getcookie( 'gallerymodpids' );
		
		$this->ipsclass->input['selectedpidcount'] = intval( count( preg_split( "/,/", $this->ipsclass->input['gallerymodpids'], -1, PREG_SPLIT_NO_EMPTY ) ) );
		
		$this->ipsclass->my_setcookie('gallerymodpids', '', 0);		

	}

	/**
	* comment_view::get()
	*
	* Displays the comments for the current image, much
	* of the code for this method is taken from the IPB
	* source files for displaying posts.
	*
	* @author Joshua Williams
	* @author Matt Mecham
	* @return none
	**/
	function get()
	{
		//-----------------------------------------
		// Grab the posts we'll need
		//-----------------------------------------

		$first = ( isset( $this->ipsclass->input['st'] ) ) ? intval( $this->ipsclass->input['st'] ) : 0;

		$query_type = 'topics_get_posts';

		if ( $this->ipsclass->vars['post_order_column'] != 'post_date' )
		{
			$this->ipsclass->vars['post_order_column'] = 'pid';
		}

		if ( $this->ipsclass->vars['post_order_sort'] != 'desc' )
		{
			$this->ipsclass->vars['post_order_sort'] = 'asc';
		}

		if ($this->ipsclass->vars['au_cutoff'] == "")
		{
			$this->ipsclass->vars['au_cutoff'] = 15;
		}

		if ( $this->ipsclass->vars['custom_profile_topic'] == 1 )
		{
			$query_type = 'topics_get_posts_with_join';
		}

		//-----------------------------------------
		// Moderator?
		//-----------------------------------------

		if ( !$this->is_moderator )
		{
			$queued_query_bit = ' AND approved=1';
		}

		//-----------------------------------------
		// Run query
		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array (
		'select' => 'pid',
		'from'   => 'gallery_comments',
		'where'  => 'img_id='.$this->data['id']. $queued_query_bit,
		'order'  => $this->ipsclass->vars['post_order_column'].' '.$this->ipsclass->vars['post_order_sort'],
		'limit'  => array( $first, 10 )
		)        );

		$this->ipsclass->DB->simple_exec();
		$total = $this->ipsclass->DB->get_num_rows();
		if( $total < 1 )
		{
			$this->no_comments = true;
			return;	
		}

		while( $p = $this->ipsclass->DB->fetch_row() )
		{
			$this->pids[] = $p['pid'];
		}


		//-----------------------------------------
		// Do we have any PIDS?
		//-----------------------------------------

		if ( ! count( $this->pids ) )
		{
			if ( $first )
			{
				//-----------------------------------------
				// Add dummy PID, AUTO FIX
				// will catch this below...
				//-----------------------------------------

				$this->pids[] = 0;
			}
		}

		//-----------------------------------------
		// Attachment PIDS
		//-----------------------------------------

		$this->lib->attach_pids = $this->pids;

		//-----------------------------------------
		// Fail safe
		//-----------------------------------------

		if ( ! is_array( $this->pids ) or ! count( $this->pids ) )
		{
			$this->pids = array( 0 => 0 );
		}

		//-----------------------------------------
		// Get posts
		//-----------------------------------------

		$this->ipsclass->DB->cache_add_query( 'get_comments', array( 'pids' => $this->pids,
		'scol' => $this->ipsclass->vars['post_order_column'],
		'sord' => $this->ipsclass->vars['post_order_sort'] ), 'gallery_sql_queries' );

		$oq = $this->ipsclass->DB->simple_exec();

		//-----------------------------------------
		// Render the page top
		//-----------------------------------------
		if( !isset( $this->ipsclass->input['total'] ) )  {
			$this->ipsclass->DB->simple_select( "COUNT( pid ) AS total", "gallery_comments", "img_id={$this->data['id']}{$queued_query_bit}" );
			$this->ipsclass->DB->simple_exec();
			$_t = $this->ipsclass->DB->fetch_row();
		}
		$total = ( isset( $this->ipsclass->input['total'] ) ) ? $this->ipsclass->input['total'] : $_t['total'];
		$this->ipsclass->input['st'] = ( $this->ipsclass->input['st'] ) ? $this->ipsclass->input['st'] : 0;
		$show_pages = $this->ipsclass->build_pagelinks( array( 'TOTAL_POSS'  => $total,
		                                                   'PER_PAGE'    => 10,
		                                                   'CUR_ST_VAL'  => $this->ipsclass->input['st'],
		                                                   'BASE_URL'    => $this->base_url."?automodule=gallery&cmd=si&img={$this->data['id']}&total={$total}"
		)     );
		$this->output .= $this->html->CommentTop( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum, 'SHOW_PAGES' => $show_pages ) );

		//-----------------------------------------
		// Format and print out the topic list
		//-----------------------------------------

		while ( $row = $this->ipsclass->DB->fetch_row( $oq ) )
		{
			$return = $this->parse_row( $row );

			$poster = $return['poster'];
			$row    = $return['row'];

			//-----------------------------------------
			// Are we giving this bloke a good ignoring?
			//-----------------------------------------

			if ( $this->ipsclass->member['ignored_users'] )
			{
				if ( strstr( $this->ipsclass->member['ignored_users'], ','.$poster['id'].',' ) and $this->ipsclass->input['p'] != $row['pid'] )
				{
					if ( ! strstr( $this->ipsclass->vars['cannot_ignore_groups'], ','.$poster['mgroup'].',' ) )
					{
						$this->output .= $this->html->render_row_hidden( $row, $poster );
						continue;
					}
				}
			}

			$this->output .= $this->html->CommentRow( $row, $poster, $this->is_moderator );
		}

		//-----------------------------------------
		// Print the footer
		//-----------------------------------------

		$this->output .= $this->html->CommentFooter( array( 'TOPIC' => $this->topic, 'FORUM' => $this->forum, 'SHOW_PAGES' => $show_pages ) );

		return $this->output;

	}

	/*-------------------------------------------------------------------------*/
	// Parse post
	/*-------------------------------------------------------------------------*/

	function parse_row( $row = array() )
	{
		$poster = array();

		//-----------------------------------------
		// Cache member
		//-----------------------------------------

		if ($row['author_id'] != 0)
		{
			//-----------------------------------------
			// Is it in the hash?
			//-----------------------------------------

			if ( isset($this->cached_members[ $row['author_id'] ]) )
			{
				//-----------------------------------------
				// Ok, it's already cached, read from it
				//-----------------------------------------

				$poster = $this->cached_members[ $row['author_id'] ];
				$row['name_css'] = 'normalname';
			}
			else
			{
				$row['name_css'] = 'normalname';
				$poster = $this->parse_member( $row );

				//-----------------------------------------
				// Add it to the cached list
				//-----------------------------------------

				$this->cached_members[ $row['author_id'] ] = $poster;
			}
		}
		else
		{
			//-----------------------------------------
			// It's definitely a guest...
			//-----------------------------------------

			$poster = $this->ipsclass->set_up_guest( $row['author_name'] );
			$row['name_css'] = 'unreg';
		}

		//-----------------------------------------

		if ( ! $row['approved'] )
		{
			$row['post_css'] = $this->post_count % 2 ? 'post1shaded' : 'post2shaded';
			$row['altrow']   = 'row4shaded';
		}
		else
		{
			$row['post_css'] = $this->post_count % 2 ? 'post1' : 'post2';
			$row['altrow']   = 'row4';
		}

		//-----------------------------------------

		if ( ($row['append_edit'] == 1) && ($row['edit_time'] != "") && ($row['edit_name'] != "") )
		{
			$e_time = $this->ipsclass->get_date( $row['edit_time'] , 'LONG' );

			$row['post'] .= "<br /><br /><span class='edit'>".sprintf($this->ipsclass->lang['edited_by'], $row['edit_name'], $e_time)."</span>";
		}

		//-----------------------------------------

		if (!$this->ipsclass->member['view_img'])
		{
			//-----------------------------------------
			// unconvert smilies first, or it looks a bit crap.
			//-----------------------------------------

			$row['post'] = preg_replace( "#<!--emo&(.+?)-->.+?<!--endemo-->#", "\\1" , $row['post'] );

			$row['post'] = preg_replace( "/<img src=[\"'](.+?)[\"'].+?".">/", "(IMG:<a href='\\1' target='_blank'>\\1</a>)", $row['post'] );
		}

		//-----------------------------------------
		// Highlight...
		//-----------------------------------------

		if ($this->ipsclass->input['hl'])
		{
			$this->ipsclass->input['hl'] = urldecode($this->ipsclass->input['hl']);
			$loosematch = strstr( $this->ipsclass->input['hl'], '*' ) ? 1 : 0;
			$keywords   = str_replace( '*', '', str_replace( "+", " ", str_replace( '-', '', trim($this->ipsclass->input['hl']) ) ) );
			$word_array = array();
			$endmatch1  = "";
			$endmatch2  = "(.)";

			if ( preg_match("/,(and|or),/i", $keywords) )
			{
				while ( preg_match("/,(and|or),/i", $keywords, $match) )
				{
					$word_array = explode( ",".$match[1].",", $keywords );
				}
			}
			else
			{
				$word_array[] = $keywords;
			}

			if ( ! $loosematch )
			{
				$endmatch1 = "(\s|,|\.|!|<br|&|$)";
				$endmatch2 = "(\<|\s|,|\.|!|<br|&|$)";
			}

			if (is_array($word_array))
			{
				foreach ($word_array as $keywords)
				{
					while( preg_match( "/(^|\s|;)(".preg_quote($keywords, '/')."){$endmatch1}/i", $row['post'] ) )
					{
						$row['post'] = preg_replace( "/(^|\s|;|\>)(".preg_quote($keywords, '/')."){$endmatch2}/is", "\\1<span class='searchlite'>\\2</span>\\3", $row['post'] );
					}
				}
			}
		}

		//-----------------------------------------
		// Online, offline?
		//-----------------------------------------

		if ( $row['author_id'] )
		{
			$time_limit = time() - $this->ipsclass->vars['au_cutoff'] * 60;

			$poster['online_status_indicator'] = '<{PB_USER_OFFLINE}>';

			list( $be_anon, $loggedin ) = explode( '&', $row['login_anonymous'] );

			if ( ( $row['last_visit'] > $time_limit or $row['last_activity'] > $time_limit ) AND $be_anon != 1 AND $loggedin == 1 )
			{
				$poster['online_status_indicator'] = '<{PB_USER_ONLINE}>';
			}
		}
		else
		{
			$poster['online_status_indicator'] = '';
		}

		//-----------------------------------------
		// Multi Quoting?
		//-----------------------------------------

		$row['mq_start_image'] = $this->html->mq_image_add($row['pid']);

		if ( $this->qpids )
		{
			if ( strstr( ','.$this->qpids.',', ','.$row['pid'].',' ) )
			{
				$row['mq_start_image'] = $this->html->mq_image_remove($row['pid']);
			}
		}

		//-----------------------------------------
		// Multi PIDS?
		//-----------------------------------------

		if ( $this->is_moderator )
		{
			$row['pid_start_image'] = $this->html->pid_image_unselected($row['pid']);

			if ( $this->ipsclass->input['selectedpids'] )
			{
				if ( strstr( ','.$this->ipsclass->input['selectedpids'].',', ','.$row['pid'].',' ) )
				{
					$row['pid_start_image'] = $this->html->pid_image_selected($row['pid']);
				}
			}
		}

		//-----------------------------------------
		// Delete button..
		//-----------------------------------------

		if ( $row['pid'] != $this->topic['topic_firstpost'] )
		{
			$row['delete_button'] = $this->delete_button($row['pid'], $poster);
		}


		$row['edit_button']   = $this->edit_button($row['pid'], $poster, $row['post_date']);

		$row['post_date']     = $this->ipsclass->get_date( $row['post_date'], 'LONG' );

		$row['post_icon']     = $row['icon_id']
		? $this->html->post_icon( $row['icon_id'] )
		: "";

		$row['ip_address']    = $this->view_ip($row, $poster);

		$row['report_link']   = (($this->ipsclass->vars['disable_reportpost'] != 1) and ( $this->ipsclass->member['id'] ))
		? $this->html->report_link($row['pid'])
		: "";

		//-----------------------------------------
		// Siggie stuff
		//-----------------------------------------

		$row['signature'] = "";

		if ($poster['signature'] and $this->ipsclass->member['view_sigs'])
		{
			if ($row['use_sig'] == 1)
			{
				$this->parser->parse_html  = intval($this->ipsclass->vars['sig_allow_html']);
				$this->parser->parse_nl2br = 1;

				$row['signature'] = $this->ipsclass->compiled_templates['skin_global']->signature_separator( $this->parser->pre_display_parse($poster['signature']) );
			}
		}

		//-----------------------------------------
		// Fix up the membername so it links to the members profile
		//-----------------------------------------

		if ($poster['id'])
		{
			$poster['name'] = ( !empty( $poster['members_display_name'] ) ) ? $poster['members_display_name'] : $poster['name'];
			$poster['name'] = "<a href='{$this->ipsclass->base_url}showuser={$poster['id']}'>{$poster['name']}</a>";
		}

		//-----------------------------------------
		// Parse HTML tag on the fly
		//-----------------------------------------

		$this->parser->pp_do_html  = ( $this->forum['use_html'] and $this->ipsclass->cache['group_cache'][ $poster['mgroup'] ]['g_dohtml'] and $row['post_htmlstate'] ) ? 1 : 0;
		$this->parser->pp_wordwrap = $this->ipsclass->vars['post_wordwrap'];
		$this->parser->pp_nl2br    = $row['post_htmlstate'] == 2 ? 1 : 0;

		$row['post'] = $this->parser->pre_display_parse( $row['post'] );

		//-----------------------------------------
		// Post number
		//-----------------------------------------
		$this->post_count++;
		$row['post_count'] = intval($this->ipsclass->input['st']) + $this->post_count;
		$row['img'] = intval( $this->ipsclass->input['img'] );
		return array( 'row' => $row, 'poster' => $poster );
	}
	
	function mod_form()
	{
		if ($this->ipsclass->member['id'] == "" || $this->ipsclass->member['id'] == 0)
		{
			return "";
		}
		
		return( ( $this->is_moderator ) ? $this->html->mod_form() : "" );
	}

	/*-------------------------------------------------------------------------*/
	// Render the delete button
	/*-------------------------------------------------------------------------*/

	function delete_button($post_id, $poster)
	{
		if ($this->ipsclass->member['id'] == "" or $this->ipsclass->member['id'] == 0)
		{
			return "";
		}

		$button = $this->html->button_delete( $post_id, $this->pclass->data['id'] );

		if( $this->ipsclass->check_perms( $this->pclass->cat['perms_moderate'] ) ) return $button;
		if( $this->ipsclass->member['g_mod_albums']) return $button;
		if( $poster['id'] == $this->ipsclass->member['id'] and ($this->ipsclass->member['g_del_own'])) return $button;

		return "";
	}

	/*-------------------------------------------------------------------------*/
	// Render the edit button
	/*-------------------------------------------------------------------------*/

	function edit_button($post_id, $poster, $post_date)
	{
		if ($this->ipsclass->member['id'] == "" or $this->ipsclass->member['id'] == 0)
		{
			return "";
		}

		$button = $this->html->button_edit( $post_id, $this->pclass->data['id'] );

		if( $this->ipsclass->check_perms( $this->pclass->cat['perms_moderate'] ) ) return $button;
		if( $this->ipsclass->member['g_mod_albums']) return $button;

		if ($poster['id'] == $this->ipsclass->member['id'] and ($this->ipsclass->member['g_edit_own']))
		{
			// Have we set a time limit?

			if ($this->ipsclass->member['g_edit_cutoff'] > 0)
			{
				if ( $post_date > ( time() - ( intval($this->ipsclass->member['g_edit_cutoff']) * 60 ) ) )
				{
					return $button;
				}
				else
				{
					return "";
				}
			}
			else
			{
				return $button;
			}
		}

		return "";
	}

	/*-------------------------------------------------------------------------*/
	// Parse the member info
	/*-------------------------------------------------------------------------*/

	function parse_member( $member=array() )
	{
		$member['avatar'] = $this->ipsclass->get_avatar( $member['avatar_location'], $this->ipsclass->member['view_avs'], $member['avatar_size'], $member['avatar_type'] );
		$member['name'] = ( !empty( $member['members_display_name'] ) ) ? $member['members_display_name'] : $member['name'];
		$pips = 0;

		foreach($this->mem_titles as $k => $v)
		{
			if ($member['posts'] >= $v['POSTS'])
			{
				if (!$member['title'])
				{
					$member['title'] = $this->mem_titles[ $k ]['TITLE'];
				}

				$pips = $v['PIPS'];
				break;
			}
		}

		if ( $this->ipsclass->cache['group_cache'][ $member['mgroup'] ]['g_icon'] )
		{
			$member['member_rank_img'] = $this->ipb_html->member_rank_img($this->ipsclass->cache['group_cache'][ $member['mgroup'] ]['g_icon']);
		}
		else if ( $pips )
		{
			if ( is_numeric( $pips ) )
			{
				for ($i = 1; $i <= $pips; ++$i)
				{
					$member['member_rank_img'] .= "<{A_STAR}>";
				}
			}
			else
			{
				$member['member_rank_img'] = $this->ipb_html->member_rank_img( 'style_images/<#IMG_DIR#>/folder_team_icons/'.$pips );
			}
		}

		$member['member_joined'] = $this->ipb_html->member_joined( $this->ipsclass->get_date( $member['joined'], 'JOINED' ) );

		$member['member_group']  = $this->ipb_html->member_group( $this->ipsclass->cache['group_cache'][ $member['mgroup'] ]['g_title'] );

		$member['member_posts']  = $this->ipb_html->member_posts( $this->ipsclass->do_number_format($member['posts']) );

		$member['member_number'] = $this->ipb_html->member_number( $this->ipsclass->do_number_format($member['id']) );

		$member['profile_icon']  = $this->ipb_html->member_icon_profile( $member['id'] );

		$member['message_icon']  = $this->ipb_html->member_icon_msg( $member['id'] );

		if ($member['location'])
		{
			$member['member_location']  = $this->ipb_html->member_location( $member['location'] );
		}

		if (! $member['hide_email'])
		{
			$member['email_icon'] = $this->ipb_html->member_icon_email( $member['id'] );
		}

		if ( $member['id'] )
		{
			$member['addresscard'] = $this->ipb_html->member_icon_vcard( $member['id'] );
		}

		//-----------------------------------------
		// Warny porny?
		//-----------------------------------------

		if ( $this->ipsclass->vars['warn_on'] and ( ! strstr( ','.$this->ipsclass->vars['warn_protected'].',', ','.$member['mgroup'].',' ) ) )
		{
			if (   ( $this->ipsclass->vars['warn_mod_ban'] AND $this->ipsclass->member['_moderator'][ $this->topic['forum_id'] ]['allow_warn'] )
			or ( $this->ipsclass->member['g_is_supmod'] == 1 )
			or ( $this->ipsclass->vars['warn_show_own'] and ( $this->ipsclass->member['id'] == $member['id'] ) )
			)
			{
				// Work out which image to show.

				if ( ! $this->ipsclass->vars['warn_show_rating'] )
				{
					if ( $member['warn_level'] <= $this->ipsclass->vars['warn_min'] )
					{
						$member['warn_img']     = '<{WARN_0}>';
						$member['warn_percent'] = 0;
					}
					else if ( $member['warn_level'] >= $this->ipsclass->vars['warn_max'] )
					{
						$member['warn_img']     = '<{WARN_5}>';
						$member['warn_percent'] = 100;
					}
					else
					{

						$member['warn_percent'] = $member['warn_level'] ? sprintf( "%.0f", ( ($member['warn_level'] / $this->ipsclass->vars['warn_max']) * 100) ) : 0;

						if ( $member['warn_percent'] > 100 )
						{
							$member['warn_percent'] = 100;
						}

						if ( $member['warn_percent'] >= 81 )
						{
							$member['warn_img'] = '<{WARN_5}>';
						}
						else if ( $member['warn_percent'] >= 61 )
						{
							$member['warn_img'] = '<{WARN_4}>';
						}
						else if ( $member['warn_percent'] >= 41 )
						{
							$member['warn_img'] = '<{WARN_3}>';
						}
						else if ( $member['warn_percent'] >= 21 )
						{
							$member['warn_img'] = '<{WARN_2}>';
						}
						else if ( $member['warn_percent'] >= 1 )
						{
							$member['warn_img'] = '<{WARN_1}>';
						}
						else
						{
							$member['warn_img'] = '<{WARN_0}>';
						}
					}

					if ( $member['warn_percent'] < 1 )
					{
						$member['warn_percent'] = 0;
					}

					$member['warn_text']  = $this->ipb_html->warn_level_warn($member['id'], $member['warn_percent'] );
				}
				else
				{
					// Ratings mode..

					$member['warn_text']  = $this->ipsclass->lang['tt_rating'];
					$member['warn_img']   = $this->ipb_html->warn_level_rating($member['id'], $member['warn_level'], $this->ipsclass->vars['warn_min'], $this->ipsclass->vars['warn_max']);
				}

				if ( ( $this->ipsclass->vars['warn_mod_ban'] AND $this->ipsclass->member['_moderator'][ $this->topic['forum_id'] ]['allow_warn'] ) or ( $this->ipsclass->member['g_is_supmod'] == 1 ) )
				{
					$member['warn_add']   = "<a href='{$this->ipsclass->base_url}act=warn&amp;type=add&amp;mid={$member['id']}&amp;t={$this->topic['tid']}&amp;st=".intval($this->ipsclass->input['st'])."' title='{$this->ipsclass->lang['tt_warn_add']}'><{WARN_ADD}></a>";
					$member['warn_minus'] = "<a href='{$this->ipsclass->base_url}act=warn&amp;type=minus&amp;mid={$member['id']}&amp;t={$this->topic['tid']}&amp;st=".intval($this->ipsclass->input['st'])."' title='{$this->ipsclass->lang['tt_warn_minus']}'><{WARN_MINUS}></a>";
				}
			}
		}

		//-----------------------------------------
		// Profile fields stuff
		//-----------------------------------------

		if ( $this->ipsclass->vars['custom_profile_topic'] == 1 )
		{
			if ( $this->custom_fields )
			{
				$this->custom_fields->member_data = $member;
				$this->custom_fields->init_data();
				$this->custom_fields->parse_to_view( 1 );

				if ( count( $this->custom_fields->out_fields ) )
				{
					foreach( $this->custom_fields->out_fields as $i => $data )
					{
						if ( $data )
						{
							$member['custom_fields'] .= "\n".$this->custom_fields->method_format_field_for_topic_view( $i );
						}
					}
				}
			}
		}

		return $member;
	}

	function view_ip($row, $poster)
	{
		if ($this->ipsclass->member['g_is_supmod'] != 1 && $this->moderator['view_ip'] != 1)
		{
			return "";
		}
		else
		{
			$row['ip_address'] = $poster['mgroup'] == $this->ipsclass->vars['admin_group']
			? "[ {$this->ipsclass->lang['gal_private']} ]"
			: "[ <a href='{$this->ipsclass->base_url}act=UserCP&amp;CODE=doiptool&amp;iptool=resolve&amp;ip={$row['ip_address']}' target='_blank'>{$row['ip_address']}</a> ]";
			return $this->ipb_html->ip_show($row['ip_address']);
		}

	}

}
?>
