<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2006 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Tuesday 27th of March 2007 07:00:16 AM
|   Release: 936d62a249c0dc8fd81438cdbc911b98
|   Licence Info: http://www.invisionboard.com/?license
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-10-19 09:37:14 -0500 (Thu, 19 Oct 2006) $
|   > $Revision: 657 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Show all emo's / BB Tags module
|   > Module written by Matt Mecham
|   > Date started: 18th April 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class legends {

    var $output    = "";
    var $base_url  = "";
    var $html      = "";

    function auto_run() {
    
    	//-----------------------------------------
    	// $is_sub is a boolean operator.
    	// If set to 1, we don't show the "topic subscribed" page
    	// we simply end the subroutine and let the caller finish
    	// up for us.
    	//-----------------------------------------
    
        $this->ipsclass->load_language('lang_legends');

    	$this->ipsclass->load_template('skin_legends');
    	
    	$this->base_url        = $this->ipsclass->base_url;
    	
    	
    	
    	//-----------------------------------------
    	// What to do?
    	//-----------------------------------------
    	
    	switch($this->ipsclass->input['CODE'])
    	{
    		case 'emoticons':
    			$this->show_emoticons();
    			break;
    			
    		case 'finduser_one':
    			$this->find_user_one();
    			break;
    			
    		case 'finduser_two':
    			$this->find_user_two();
    			break;
    			
    		case 'bbcode':
    			$this->show_bbcode();
    			break;
    			
    		default:
    			$this->show_emoticons();
    			break;
    	}
    	
    	// If we have any HTML to print, do so...
    	
        $this->ipsclass->print->pop_up_window( $this->page_title, $this->output );
    		
 	}
 	
 	//-----------------------------------------
 	
 	function find_user_one()
 	{
		// entry=textarea&name=carbon_copy&sep=comma
 		
 		$entry = (isset($this->ipsclass->input['entry'])) ? $this->ipsclass->input['entry'] : 'textarea';
 		$name  = (isset($this->ipsclass->input['name']))  ? $this->ipsclass->input['name']  : 'carbon_copy';
 		$sep   = (isset($this->ipsclass->input['sep']))   ? $this->ipsclass->input['sep']   : 'line';
 		
 		$this->output .= $this->ipsclass->compiled_templates['skin_legends']->find_user_one($entry, $name, $sep);
 		
 		$this->page_title = $this->ipsclass->lang['fu_title'];
 		
 	}
 	
 	//-----------------------------------------
 	
 	function find_user_two()
 	{
		$entry = (isset($this->ipsclass->input['entry'])) ? $this->ipsclass->input['entry'] : 'textarea';
 		$name  = (isset($this->ipsclass->input['name']))  ? $this->ipsclass->input['name']  : 'carbon_copy';
 		$sep   = (isset($this->ipsclass->input['sep']))   ? $this->ipsclass->input['sep']   : 'line';
 		
 		//-----------------------------------------
 		// Check for input, etc
 		//-----------------------------------------
 		
 		$this->ipsclass->input['username'] = strtolower(trim($this->ipsclass->input['username']));
 		
 		if ($this->ipsclass->input['username'] == "")
 		{
 			$this->find_user_error('fu_no_data');
 			return;
 		}
 		
 		//-----------------------------------------
 		// Attempt a match
 		//-----------------------------------------
 		
 		$this->ipsclass->DB->simple_construct( array( 'select' => 'id, name, members_display_name',
													  'from'   => 'members',
													  'where'  => "members_l_display_name LIKE '".$this->ipsclass->input['username']."%'",
													  'limit'  => array( 0,101) ) );
		$this->ipsclass->DB->simple_exec();
		
 		if ( ! $this->ipsclass->DB->get_num_rows() )
 		{
 			$this->find_user_error('fu_no_match');
 			return;
 		}
 		else if ( $this->ipsclass->DB->get_num_rows() > 99 )
 		{
 			$this->find_user_error('fu_kc_loads');
 			return;
 		}
 		else
 		{
 			$select_box = "";
 			
 			while ( $row = $this->ipsclass->DB->fetch_row() )
 			{
 				if ($row['id'] > 0)
 				{
 					$select_box .= "<option value='{$row['members_display_name']}'>{$row['members_display_name']}</option>\n";
 				}
 			}
 		
 			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->find_user_final($select_box, $entry, $name, $sep);
 		
 			$this->page_title = $this->ipsclass->lang['fu_title'];
 		}
 	}
 	
 	
 	//-----------------------------------------
 	
 	function find_user_error($error)
 	{
		$this->page_title = $this->ipsclass->lang['fu_title'];
 		
 		$this->output = $this->ipsclass->compiled_templates['skin_legends']->find_user_error($this->ipsclass->lang[$error]);
 		
 		return;
 		
 	}
 	
 	
 	//-----------------------------------------
 	
 	function show_emoticons()
 	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
	
		$this->page_title = $this->ipsclass->lang['emo_title'];
 		$smilie_id        = 0;
 		$editor_id        = $this->ipsclass->txt_alphanumerical_clean( $this->ipsclass->input['editor_id'] );

		//-----------------------------------------
		// Start output...
		//-----------------------------------------
		
 		$this->output .= $this->ipsclass->compiled_templates['skin_legends']->emoticon_javascript( $editor_id );
 		$this->output .= $this->ipsclass->compiled_templates['skin_legends']->page_header( $this->ipsclass->lang['emo_title'], $this->ipsclass->lang['emo_type'], $this->ipsclass->lang['emo_img'] );
 		
 		$this->ipsclass->DB->simple_construct( array( 'select' => 'typed, image', 'from' => 'emoticons', 'where' => "emo_set='".$this->ipsclass->skin['_emodir']."'" ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$smilie_id++;
				
				if (strstr( $r['typed'], "&quot;" ) )
				{
					$in_delim  = "'";
					$out_delim = '"';
				}
				else
				{
					$in_delim  = '"';
					$out_delim = "'";
				}
			
				$this->output .= $this->ipsclass->compiled_templates['skin_legends']->emoticons_row( stripslashes($r['typed']), stripslashes($r['image']), $in_delim, $out_delim, $smilie_id );
											
			}
		}
		
		$this->output .= $this->ipsclass->compiled_templates['skin_legends']->page_footer();
 	}
 	
 	//-----------------------------------------
 	// Show BBCode Helpy file
 	//-----------------------------------------
 	
 	function show_bbcode()
 	{
		//-----------------------------------------
        // Load and config the post parser
        //-----------------------------------------
        
        require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $this->parser                      = new parse_bbcode();
        $this->parser->ipsclass            = $this->ipsclass;
        $this->parser->allow_update_caches = 0;
        
        $this->parser->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);
        
 		$this->parser->parse_html    = 0;
		$this->parser->parse_nl2br   = 1;
		$this->parser->parse_smilies = 1;
		$this->parser->parse_bbcode  = 1;
		
 		//-----------------------------------------
 		// Array out or stuff here
 		//-----------------------------------------
 		
 		$bbcode = array(
						0  => array('[b]', '[/b]', $this->ipsclass->lang['bbc_ex1'] ),
						1  => array('[s]', '[/s]', $this->ipsclass->lang['bbc_ex1'] ),
						2  => array('[i]', '[/i]', $this->ipsclass->lang['bbc_ex1'] ),
						3  => array('[u]', '[/u]', $this->ipsclass->lang['bbc_ex1'] ),
						4  => array('[email]', '[/email]', 'user@domain.ru' ),
						5  => array('[email=user@domain.ru]', '[/email]', $this->ipsclass->lang['bbc_ex2'] ),
						6  => array('[url]', '[/url]', 'http://www.domain.ru' ),
						7  => array('[url=http://www.domain.ru]', '[/url]', $this->ipsclass->lang['bbc_ex2'] ),
						8  => array('[size=7]', '[/size]'    , $this->ipsclass->lang['bbc_ex1'] ),
						9  => array('[font=times]', '[/font]', $this->ipsclass->lang['bbc_ex1'] ),
						10 => array('[color=red]', '[/color]', $this->ipsclass->lang['bbc_ex1'] ),
						11 => array('[img]', '[/img]', $this->ipsclass->vars['board_url'].'/'.$this->ipsclass->vars['img_url'].'/folder_post_icons/icon11.gif' ),
						12 => array('[list]', '[/list]', '[*]'.$this->ipsclass->lang['bbc_li'].' [*]'.$this->ipsclass->lang['bbc_li'] ),
						13 => array('[list=1]', '[/list]', '[*]'.$this->ipsclass->lang['bbc_li'].' [*]'.$this->ipsclass->lang['bbc_li'] ),
						14 => array('[list=a]', '[/list]', '[*]'.$this->ipsclass->lang['bbc_li'].' [*]'.$this->ipsclass->lang['bbc_li'] ),
						15 => array('[list=i]', '[/list]', '[*]'.$this->ipsclass->lang['bbc_li'].' [*]'.$this->ipsclass->lang['bbc_li'] ),
						16 => array('[quote]', '[/quote]', $this->ipsclass->lang['bbc_ex1'] ),
						17 => array('[code]', '[/code]', '$this_var = "'.$this->ipsclass->lang['bbc_helloworld'].'!";' ),
						18 => array('[sql]', '[/sql]', 'SELECT t.tid FROM a_table t WHERE t.val="This Value"' ),
						19 => array('[html]', '[/html]', '&lt;a href=&quot;test/page.html&quot;&gt;'.$this->ipsclass->lang['bbc_testpage'].'&lt;/a&gt;' ),
					  );
 		
 		$this->page_title = $this->ipsclass->lang['bbc_title'];
 		
 		$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_header();
 		
		foreach( $bbcode as $bbc )
		{
			$open    = $bbc[0];
			$close   = $bbc[1];
			$content = $bbc[2];
		
			$before = $this->ipsclass->compiled_templates['skin_legends']->wrap_tag($open) . $content . $this->ipsclass->compiled_templates['skin_legends']->wrap_tag($close);
			
			$after = $this->parser->pre_db_parse( $open.$content.$close );
			
			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_row_header( $this->ipsclass->lang['bbc_title']);
			
			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_row( $before, stripslashes($after) );
			
			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_row_footer();
		}
		
		//-----------------------------------------
 		// Add in custom bbcode
 		//-----------------------------------------
 		
 		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode' ) );
		$this->ipsclass->DB->simple_exec();
			
		while ( $row = $this->ipsclass->DB->fetch_row() )
		{
			$before  = $row['bbcode_example'];
			
			$t       = $before;
			
			$replace  = explode( '{content}', $row['bbcode_replace'] );
			$preg_tag = preg_quote($row['bbcode_tag'], '#' );
			
			//-----------------------------------------
			// Parse the BBcode
			//-----------------------------------------
			
			if ( $row['bbcode_useoption'] )
			{
				preg_match_all( "#(\[".$preg_tag."=(?:&quot;|&\#39;|\"\')?(.+?)(?:&quot;|&\#39;|\"\')?\])(.+?)(\[/".$preg_tag."\])#si", $t, $match );
				
				for ( $i = 0; $i < count($match[0]); $i++)
				{
					//-----------------------------------------
					// Does the option tag come first?
					//-----------------------------------------
					
					$_option  = 2;
					$_content = 3;
					
					$_o = strpos( $row['bbcode_replace'], '{option}'  );
					$_c = strpos( $row['bbcode_replace'], '{content}' );
					
					if ( $_c < $_o )
					{
						$_option  = 3;
						$_content = 2;
					}
					
					# XSS Check: Bug ID: 980
					if ( $row['bbcode_tag'] == 'post' OR $row['bbcode_tag'] == 'topic' OR $row['bbcode_tag'] == 'snapback' )
					{
						$match[ $_option ][$i] = intval( $match[ $_option ][$i] );
					}
					
					$tmp = $row['bbcode_replace'];
					$tmp = str_replace( '{option}' , $match[ $_option  ][$i], $tmp );
					$tmp = str_replace( '{content}', $match[ $_content ][$i], $tmp );
					$t   = str_replace( $match[0][$i], $tmp, $t );
				}
			}
			else
			{
				# Tricky.. match anything that's not a closing tag, or nothing
				preg_match_all( "#(\[$preg_tag\])(.+?)?(\[/$preg_tag\])#si", $t, $match );
				
				for ( $i = 0; $i < count($match[0]); $i++)
				{
					# XSS Check: Bug ID: 980
					if ( $row['bbcode_tag'] == 'snapback' )
					{
						$match[2][$i] = intval( $match[2][$i] );
					}
					
					$tmp = $row['bbcode_replace'];
					$tmp = str_replace( '{content}', $match[2][$i], $tmp );
					$t   = str_replace( $match[0][$i], $tmp, $t );
				}
			}
			
			$before = preg_replace( "#(\[".$row['bbcode_tag']."(?:[^\]]+)?\])#is", $this->ipsclass->compiled_templates['skin_legends']->wrap_tag("\\1"), $before );
			$before = preg_replace( "#(\[/".$row['bbcode_tag']."\])#is"          , $this->ipsclass->compiled_templates['skin_legends']->wrap_tag("\\1"), $before );
			
			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_row_header( $row['bbcode_title'], $row['bbcode_desc'] );
			
			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_row( $before, $t );
			
			$this->output .= $this->ipsclass->compiled_templates['skin_legends']->bbcode_row_footer();
		}
 	}
}

?>