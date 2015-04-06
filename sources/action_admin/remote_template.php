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
|		          http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-11-06 17:01:30 -0600 (Mon, 06 Nov 2006) $
|   > $Revision: 715 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > Skin -> Templates pop up functions
|   > Module written by Matt Mecham
|   > Date started: 9th July 2002
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}


class ad_remote_template {

	var $base_url;

	function auto_run()
	{
		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'preview':
				$this->do_preview();
				break;
				
			case 'edit_bit':
				$this->edit_bit();
				break;
				
			case 'macro_one':
				$this->macro_one();
				break;
				
			case 'macro_two':
				$this->macro_two();
				break;
				
			case 'compare':
				$this->compare_frames();
				break;
				
			case 'dotop':
				$this->print_compare_top();
				break;
				
			case 'donew':
				$this->print_compare_new();
				break;
			
			//-----------------------------------------
			
			case 'search':
				$this->search_frames();
				break;
				
			case 'searchbox':
				$this->print_search_box();
				break;
				
			case 'searchlinks':
				$this->print_searchlinks();
				break;
			
			//-----------------------------------------
			
			case 'css_search':
				$this->css_search_frames();
				break;
				
			case 'csssearchlinks':
				$this->print_css_searchlinks();
				break;
				
			case 'css_diff':
				$this->css_differences();
				break;
				
			case 'previewstate':
				$this->do_message("Preview Window");
				break;
				
			//-----------------------------------------
			
			case 'css_preview':
				$this->css_preview();
				break;
				
			default:
				exit();
				break;
		}
		
	}
	
	//-----------------------------------------
	
	function css_search_frames()
	{
		print "<html>
				 <head><title>Search</title></head>
				   <frameset cols='200, *' frameborder='no' border='1' framespacing='0'>
					<frame name='links' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=csssearchlinks&id={$this->ipsclass->input['id']}&element={$_GET['element']}'>
					<frame name='preview' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=previewstate'>
				   </frameset>
			   </html>";
			   
		exit();
	}
	
	/*-------------------------------------------------------------------------*/
	// CSS differences
	/*-------------------------------------------------------------------------*/
	
	function css_differences()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$id       = intval( $this->ipsclass->input['id'] );
		$original = "";
		$new      = "";
		
		//-----------------------------------------
		// Load HTML
		//-----------------------------------------
		
		$this->html = $this->ipsclass->acp_load_template('cp_skin_lookandfeel');
		
		//-----------------------------------------
		// Get this skin set.. and master skin set..
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select' => '*',
												 'from'   => 'skin_sets',
												 'where'  => 'set_skin_set_id IN (1,'.$id.')' ) );
												
		$this->ipsclass->DB->exec_query();
	
		while( $row = $this->ipsclass->DB->fetch_row() )
		{
			if ( $row['set_skin_set_id'] == 1 )
			{
				$original = $row['set_css'];
			}
			else
			{
				$new = $row['set_css'];
			}
		}
		
		//-----------------------------------------
		// Get Diff library
		//-----------------------------------------
		
		require_once( KERNEL_PATH . 'class_difference.php' );
		$class_difference         = new class_difference();
		$class_difference->method = 'PHP';
		
		$difference = $class_difference->get_differences( $original, $new );
		
		if ( ! $class_difference->diff_found )
		{
			$difference = "No differences found";
		}
		
		$this->ipsclass->html = $this->html->skin_css_view_bit( nl2br( $difference ) );
		
		$this->ipsclass->admin->print_popup();
	}
	
	//-----------------------------------------
	
	function css_preview()
	{
		//-----------------------------------------
		// GET THE TEMPLATES THAT THIS CSS USES
		//-----------------------------------------
		
		$this_set = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.intval( $this->ipsclass->input['id']) ) ); 
		
		$skin = $this->ipsclass->DB->fetch_row();
		
		$element = trim(stripslashes($_GET['element']));
		
		list($type, $name) = explode( "|", $element );
		
		$like = "class='{$name}'";
		$first = '.';
		
		if ($type == 'id')
		{
			$like = "id='{$name}'";
			$first = '#';
		}
		
		preg_match( "/($first"."$name)\s{0,}\{(.+?)\}/s", $this_set['set_cache_css'], $match );
		
		$defs = explode( ";", str_replace( "\n\n", "\n", str_replace( "\r\n", "\n", trim($match[2]) ) ) );
		
		$def_output = "";
		
		foreach($defs as $bit)
		{
			list($type, $value) = explode( ":", trim($bit) );
			
			$type = trim($type);
			
			$value = trim($value);
			
			if ($type != "" and $value != "")
			{
			    $extra = "";
			    
				if ($type == 'color' or $type == 'background-color')
				{
					$extra = "&nbsp;&nbsp;&nbsp;<input type='text' size='6' style='background-color:$value' readonly>";
				}
			
				$def_output .= "<tr><td width='40%'><b>$type</b></td><td width='60%'>$value $extra</td></tr>\n";
			}
		}
	
		$css = "\n<style>\n<!--\n".preg_replace( "#url\(([\"'])?#i", "url(\\1style_images/", $this_set['set_cache_css'] )."\n//-->\n</style>";
		
    	$html = "<html>
    	           <head>
    	              <title>CSS Предпросмотр</title>
    	              $css
    	           </head>
    	           <body topmargin='0' leftmargin='0' rightmargin='0' marginwidth='0' marginheight='0' alink='#000000' vlink='#000000'>
    	           <table border='1' width='95%' cellspacing='0' cellpadding='4' align='center'>
    	           <tr>
    	            <td bgcolor='#EEEEEE' style='font-size:14px'><b>Предпросмотр CSS класса '$name'<br />Из стиля '{$this_set['set_name']}'</b></td>
    	           </tr>
    	           </table>
    	           <br />
    	           <table border='1' width='95%' cellspacing='0' cellpadding='4' align='center'>
    	           <tr>
    	            <td><b>Предпросмотр</b></td>
    	           </tr>
    	           <tr>
    	           	<td $like>Строка примера: Клара у Карла украла кораллы, а Карл у Клары украл кларнет</td>
    	           	</tr>
    	           	</table>
    	           	<br />
    	           	<table border='1' width='95%' cellspacing='0' cellpadding='4' align='center'>
    	            <tr>
    	             <td colspan='2'><b>Отформатированное определение CSS стиля</b></td>
    	            </tr>
    	              $def_output
    	           </table>
    	           </body>
    	         </html>
    	        ";
    	        
		print $html;
		
		exit();
    	        
    }
    	        
   	//-----------------------------------------
   	//---------------------------------------
	
	function print_css_searchlinks()
	{
		//-----------------------------------------
		// GET THE TEMPLATES THAT THIS CSS USES
		//-----------------------------------------
		
		$element = trim(stripslashes($_GET['element']));
		
		$this->ipsclass->input['id'] = intval( $this->ipsclass->input['id'] );
		
		list($type, $name) = explode( "|", $element );
		
		$like = "class=_{$name}";
		
		if ($type == 'id')
		{
			$like = "id=_{$name}";
		}
		
		require ROOT_PATH .'sources/lib/skin_info.php';
		
		//die("SELECT suid, set_id, group_name, func_name FROM ibf_skin_templates WHERE set_id='".$set['tmpl_id']."' AND section_content LIKE '%".$like."%' ORDER BY group_name");
		
		$this->ipsclass->DB->build_query( array( 'select' => 'suid, set_id, group_name, func_name', 'from' => 'skin_templates', 'where' => "set_id IN (1,".$this->ipsclass->input['id'].") AND section_content LIKE '%".$like."%'", 'order' => 'group_name' ) );
		$this->ipsclass->DB->exec_query();
				
		if (! $this->ipsclass->DB->get_num_rows() )
		{
			$this->do_message("Не найдено записей в шаблоне с ID {$set['set_id']}");
		}
		
		$results = array();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( ! isset($result[ $r['group_name'] ]) )
			{
				$result[ $r['group_name'] ] = array();
			}
			
			$result[ $r['group_name'] ][ $r['func_name'] ] = array( 'suid' => $r['suid'], 'func_name' => $r['func_name'] );
			
		}
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table();
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Результаты поиска</b>" )  , 'title' );
		
		foreach( $result as $group_name => $sub_array )
		{
			if ( isset($skin_names[ $group_name ]) )
			{
				$group_name = $skin_names[ $group_name ][0];
			}
			
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>$group_name</b>" )  , 'catrow' );
			
			if (is_array($sub_array) and count($sub_array) > 0 )
			{
				foreach( $sub_array as $data )
				{
					$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "+ <a href='{$this->ipsclass->base_url}&act=rtempl&code=preview&suid={$data['suid']}&type=text&hl=".urlencode($name)."' target='preview'>{$data['func_name']}</a>" )  );
				}
			}
		}
										 
		
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->print_popup();
		
	}
	
	/*-------------------------------------------------------------------------*/
	// PRINT THE SEARCH RESULT LINKS
	/*-------------------------------------------------------------------------*/
	
	function print_searchlinks()
	{
		//-----------------------------------------
		// Printing no results?
		//-----------------------------------------
		
		if ($this->ipsclass->input['bypass'] == 1)
		{
			$this->do_message("Ничего не найдено");
		}
		
		//-----------------------------------------
		// Clean up search post
		//-----------------------------------------
		
		$search_text = trim($this->unconvert_tags(stripslashes($_POST['search'])));
		
		$search_text = str_replace( "\$", "\\$", $search_text);
		$search_text = str_replace( "'" , "\\'", $search_text);
		
		if ($search_text == "")
		{
			$this->do_message("Вы должны ввести строку для поиска");
		}
		
		require ROOT_PATH .'sources/lib/skin_info.php';
		
		//-----------------------------------------
		// Get template bits
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select' => 'suid, set_id, group_name, func_name', 'from' => 'skin_templates', 'where' => "set_id=".$this->ipsclass->input['set_id']." AND section_content LIKE '%$search_text%'", 'order' => 'group_name' ) );
		$this->ipsclass->DB->exec_query();
		
		if ( ! $this->ipsclass->DB->get_num_rows() )
		{
			$this->do_message("Не найдено записей, содержащих искомую строку");
		}
		
		$results = array();
		
		//-----------------------------------------
		// Loop..
		//-----------------------------------------
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( ! isset($result[ $r['group_name'] ]) )
			{
				$result[ $r['group_name'] ] = array();
			}
			
			$result[ $r['group_name'] ][] = array( 'suid' => $r['suid'], 'func_name' => $r['func_name'] );
			
		}
		
		//-----------------------------------------
		// Print..
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table();
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Результаты поиска</b>" )  , 'title' );
		
		foreach( $result as $group_name => $sub_array )
		{
			if ( isset($skin_names[ $group_name ]) )
			{
				$group_name = $skin_names[ $group_name ][0];
			}
			
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>$group_name</b>" )  , 'catrow' );
			
			if (is_array($sub_array) and count($sub_array) > 0 )
			{
				foreach( $sub_array as $data )
				{
					$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "+ <a href='{$this->ipsclass->base_url}&act=rtempl&code=preview&suid={$data['suid']}&set_id={$this->ipsclass->input['set_id']}&type=text&hl=".urlencode(stripslashes($_POST['search']))."' target='preview'>{$data['func_name']}</a>" )  );
				}
			}
		}
										 
		//-----------------------------------------
		// Done..
		//-----------------------------------------
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->print_popup();
		
	}
	
	//-----------------------------------------
	
	function do_message($message="")
	{
		$this->ipsclass->html = "<tr><td id='tablerow1' height='100%' align='center' valign='middle'><br /><br /><b>$message</b><br /><br />&nbsp;</td></tr>";
		
		$this->ipsclass->admin->print_popup();
		
	}
	
	//-----------------------------------------
	
	function print_search_box()
	{
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'searchlinks'   ),
												                 2 => array( 'act'   , 'rtempl'        ),
												                 3 => array( 'set_id', $this->ipsclass->input['set_id']   )
									                    )  ,'theform', 'target="links"'    );
									     
		//-----------------------------------------
		
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table(  );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Введите запрос для поиска:</b><br />".
										                         $this->ipsclass->adskin->form_input( 'search' )
										                )      );
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Найти!");
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->print_popup();
		
	}
	
	//-----------------------------------------
	
	function search_frames()
	{
		$this->ipsclass->boink_it( $this->ipsclass->base_url.'&section=lookandfeel&act=skintools&code=searchsplash' );
		
		
		exit();
		print "<html>
				 <head><title>Search</title></head>
				   <frameset cols='200, *' frameborder='no' border='1' framespacing='0'>
					 <frameset rows='*, 100' frameborder='no' border='1' framespacing='0'>
					   <frame name='links' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=searchlinks&set_id={$this->ipsclass->input['set_id']}&bypass=1'>
					   <frame name='searchbox' scrolling='no' src='{$this->ipsclass->base_url}&act=rtempl&code=searchbox&set_id={$this->ipsclass->input['set_id']}'>
					 </frameset>
					<frame name='preview' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=previewstate'>
				   </frameset>
			   </html>";
			   
		exit();
	}
	
	//-----------------------------------------
	
	function compare_frames()
	{
		print "<html>
				 <head>
				  <title>Сравнение</title>
				 </head>
				   <frameset cols='50%, 50%' frameborder='yes' border='1' framespacing='0'>
				     <frameset rows='30, *' frameborder='yes' border='1' framespacing='0'>
				       <frame name='origtop' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=dotop&content=orig'>
				       <frame name='origbot' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=preview&suid={$this->ipsclass->input['suid']}&type=css'>
				     </frameset>
					<frameset rows='30, *' frameborder='yes' border='1' framespacing='0'>
				       <frame name='newtop' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=dotop&content=new'>
				       <frame name='newbot' scrolling='auto' src='{$this->ipsclass->base_url}&act=rtempl&code=donew&suid={$this->ipsclass->input['suid']}&pop=".intval($this->ipsclass->input['pop'])."'>
				     </frameset>
				   </frameset>
			   </html>";
			   
		exit();
	}
	
	//-----------------------------------------
	
	function print_compare_top()
	{
		$content = $this->ipsclass->input['content'] == 'orig' ? 'Стандартный шаблон' : 'Текущий шаблон';
		
		print "<html>
			   <body marginheight='0' marginwidth='0' leftmargin='0' topmargin='0' bgcolor='#000055'>
			   <center><font face='verdana' size='2' color='white'><b>$content</b></font></center>
			   </body></html>";
		
		exit();
	}
	
	//-----------------------------------------
	
	function print_compare_new()
	{
		$template = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "suid=".intval($this->ipsclass->input['suid']) ) );
		
		if ( ! $template['suid'] )
		{
			$this->ipsclass->admin->error("Вы должны выбрать ID шаблона!");
		}
		
		$skin = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$template['set_id'] ) );
		
		$css = $skin['set_cache_css'];
		
		$css_text = "\n<style>\n<!--\n".str_replace( "<#IMG_DIR#>", "style_images/".$r['set_image_dir'], $css )."\n//-->\n</style>";
		
		print "<html><head>
				$css_text
				
				</head>
				<body>
				<script type='text/javascript'>";
				
		if( !$this->ipsclass->input['pop'] )
		{
			print "		templatedata = window.parent.opener.document.theform.txt{$this->ipsclass->input['suid']}.value;";
		}
		else
		{
			print "		templatedata = window.parent.opener.document.theform.templatebit.value;";
		}
		print "			
					document.write( templatedata);
					document.close();
					
				</script>
				</body></html>
				";
				
		exit();
		
		
	}
	
	//-----------------------------------------
	
	function macro_one($msg="")
	{
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'macro_two'   ),
																 2 => array( 'act'   , 'rtempl'       ),
																 3 => array( 'suid'  , $this->ipsclass->input['suid']   )
														)      );
									     
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "40%" );

		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Поиск макросов" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Введите искомый макрос</b><br />(Например: {ibf.skin.set_image_dir})",
																 $this->ipsclass->adskin->form_input( 'lookup', $this->ipsclass->input['lookup'] )
														)      );
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Найти");
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->print_popup();
		
	}
	
	//-----------------------------------------
	
	function macro_two()
	{
		if ($this->ipsclass->input['lookup'] == "")
		{
			$this->ipsclass->admin->error("You must enter a macro to look up", 1);
		}
		
		$is_valid = 0;
		$macro    = "";
		$extra    = "";
		
		if ( preg_match( "/&lt;\{(\S+)\}&gt;/", $this->ipsclass->input['lookup'], $match ) )
		{
			$is_valid = 1;
			$macro    = $match[1];
		}
		
		else if ( preg_match( "/{?(?:ibf|ipb)\.(skin|lang|vars|member)(.+?)}?$/", $this->ipsclass->input['lookup'], $match ) )
		{
			$is_valid = 1;
			$macro    = $match[2];
			$extra    = $match[1];
			$macro    = str_replace( "&#39;", "'", $macro );
			$macro    = preg_replace( "#[\[\]\"'\.]#", "", $macro );
		}
		else
		{
			$is_valid = 0;
		}
		
		if ($is_valid != 1)
		{
			$this->macro_one("Неправильный формат");
		}
		
		if ($extra != "")
		{
			if ($extra == 'member')
			{
				if (isset($this->ipsclass->member[$macro]))
				{
					if ($this->ipsclass->member[$macro] == "")
					{
						$answer = "";
					}
					else
					{
						$answer = $this->ipsclass->member[$macro];
					}
				}
				$result = "Информация пользователя загружена";
			}
			else if ($extra == 'vars')
			{
				//-----------------------------------------
				// Filter out sensitive stuff
				//-----------------------------------------
				
				$safe_INFO['board_name'] = $this->ipsclass->vars['board_name'];
				$safe_INFO['board_url']  = $this->ipsclass->vars['board_url'];
				
				$answer = $safe_INFO[$macro];
				
				$result = "Системные переменные (могут быть защищены)";
			}
			else if ($extra == 'lang')
			{
				$result = "Языковой текст";
				
				$this->ipsclass->DB->simple_construct( array( 'select' => 'group_name', 'from' => 'skin_templates', 'where' => "suid='{$this->ipsclass->input['suid']}'" ) );
				$this->ipsclass->DB->simple_exec();
				
				if ( $r = $this->ipsclass->DB->fetch_row() )
				{
					$filename = preg_replace( "/^skin_/", "lang_", $r['group_name'] );
					
					if ( @file_exists( ROOT_PATH. "cache/lang_cache/ru/$filename".".php" ) )
					{
						require ROOT_PATH. "cache/lang_cache/ru/$filename".".php";
						
						$answer = $lang[$macro];
					}
				}
			}
		}
		else
		{
			//-----------------------------------------
			// Is macro
			//-----------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'set_id', 'from' => 'skin_templates', 'where' => "suid='{$this->ipsclass->input['suid']}'" ) );
			$this->ipsclass->DB->simple_exec();
				
			$template = $this->ipsclass->DB->fetch_row();
			
			$this->ipsclass->DB->simple_construct( array( 'select' => 'macro_value, macro_replace',
										  'from'   => 'skin_macro',
										  'where'  => "macro_set IN( 1,{$template['set_id']}) AND macro_value='$macro'",
										  'order'  => "macro_set DESC" ) );
			$this->ipsclass->DB->simple_exec();
			
			if ($val = $this->ipsclass->DB->fetch_row())
			{
				$answer = htmlentities($val['macro_replace']);
				$result = "Найденные макросы";
			}
			else
			{
				$answer = "Макрос не найден";
			}
		}
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"    , "60%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Результаты поиска макроса <a href='{$this->ipsclass->base_url}&act=rtempl&code=macro_one&suid={$this->ipsclass->input['suid']}'>Перейт</a>" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Введенный макрос</b>", $this->ipsclass->input['lookup'] ) );
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Тип макроса</b>", $result )      );
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Результат</b><br />Может быть пустым, если нет информации", $answer )      );
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->print_popup();
		
		
		
	}
	
	/*-------------------------------------------------------------------------*/
	// SHOW PREVIEW
	/*-------------------------------------------------------------------------*/
	
	function do_preview()
	{
		//-----------------------------------------
		// Check...
		//-----------------------------------------
		
		if ($this->ipsclass->input['suid'] == "")
		{
			$this->ipsclass->admin->error("Не выбран ID шаблона, вернитесь назад и повторите попытку");
		}
		
		//-----------------------------------------
		// Get from DB
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_templates', 'where' => "suid='".$this->ipsclass->input['suid']."'" ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( ! $template = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Шаблон с этим ID не найден, вернитесь назад и повторите попытку");
		}
		
		//-----------------------------------------
		// Get skin set details
		//-----------------------------------------
		
		$this_set = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'skin_sets', 'where' => 'set_skin_set_id='.$template['set_id'] ) ); 
		
		$macros = unserialize(stripslashes($this_set['set_cache_macro']));
		
		if ( is_array($macros) and count($macros) )
		{
			foreach( $macros as $mc )
			{
				$macro_orig[] = "<{".$mc['macro_value']."}>";
				$macro_repl[] = $mc['macro_replace'];
			}
		}
		
		$table = "<table width='100%' bgcolor='black' cellpadding='4' style='font-family:verdana, arial;font-size:11px;color:white'>
				  <tr>
				   <td align='center' style='font-family:verdana, arial;font-size:11px;color:white'>Группа шаблонов: {$template['group_name']} . Шаблон: {$template['func_name']}</td>
				  </tr>
				  <tr>
				   <td align='center' style='font-family:verdana, arial;font-size:11px;color:white'>Смотреть как [ <a href='{$this->ipsclass->base_url}&act=rtempl&code=preview&suid={$this->ipsclass->input['suid']}&type=text' style='font-family:verdana, arial;font-size:11px;color:white'>Текст</a> | <a href='{$this->ipsclass->base_url}&act=rtempl&code=preview&suid={$this->ipsclass->input['suid']}&type=html' style='font-family:verdana, arial;font-size:11px;color:white'>HTML</a> | <a href='{$this->ipsclass->base_url}&act=rtempl&code=preview&suid={$this->ipsclass->input['suid']}&type=css' style='font-family:verdana, arial;font-size:11px;color:white'>HTML с CSS</a> ]</td>
				  </tr>
				  </table>
				  <br><br>
				  ";
		
		if ($this->ipsclass->input['type'] == 'text')
		{
			@header("Content-type: text/html; charset={$this->ipsclass->vars['gb_char_set']}");
			print $table;
			$html = $this->convert_tags($template['section_content']);
			
			$html = str_replace( "<" , "&lt;"  , $html);
			$html = str_replace( ">" , "&gt;"  , $html);
			$html = str_replace( "\"", "&quot;", $html);
			
			if ($_GET['hl'] != "")
			{
				$hl = str_replace( '{-22-}', '"', urldecode(stripslashes($_GET['hl'])) );
				
				$hl = str_replace( "<" , "&lt;"  , $hl);
				$hl = str_replace( ">" , "&gt;"  , $hl);
				$hl = str_replace( "\"", "&quot;", $hl);
				
				$html = str_replace( $hl, "<span style='color:red;font-weight:bold;background-color:yellow'>$hl</span>", $html );
			}
			
			$html = preg_replace( "!&lt;\!--(.+?)(//)?--&gt;!s"                    , "&#60;&#33;<span style='color:red'>--\\1--\\2</span>&#62;", $html );
			$html = preg_replace( "#&lt;([^&<>]+)&gt;#s"                           , "&lt;<span style='color:blue'>\\1</span>&gt;"        , $html );   //Matches <tag>
			$html = preg_replace( "#&lt;([^&<>]+)=#s"                              , "&lt;<span style='color:blue'>\\1</span>="           , $html );   //Matches <tag
			$html = preg_replace( "#&lt;/([^&]+)&gt;#s"                            , "&lt;/<span style='color:blue'>\\1</span>&gt;"       , $html );   //Matches </tag>
			$html = preg_replace( "!=(&quot;|')([^<>])(&quot;|')(\s|&gt;)!s"   , "=\\1<span style='color:purple'>\\2</span>\\3\\4"       , $html );   //Matches ='this'
			
			$html = str_replace( "\n", "<br>", str_replace("\r\n", "\n", $html ) );
			
			print "<pre>".$html."</pre>";
			exit();
			
		}
		else if ($this->ipsclass->input['type'] == 'html')
		{
			@header("Content-type: text/html; charset={$this->ipsclass->vars['gb_char_set']}");
			print $table;
			print $this->convert_tags($template['section_content']);
			
			exit();
		}
		else if($this->ipsclass->input['type'] == 'css')
		{
			$css = $this_set['set_cache_css'];
			
			$css_text = "\n<style>\n<!--\n".str_replace( "<#IMG_DIR#>", "style_images/".$r['img_dir'], $css)."\n//-->\n</style>";
			
			@header("Content-type: text/html; charset={$this->ipsclass->vars['gb_char_set']}");
			print "<html><head><title>Предпросмотр</title>$css_text</head><body>$table \n";
			print str_replace( $macro_orig, $macro_repl, $this->convert_tags($template['section_content']) );
			
			exit();
		
		}
			
		
		
	}
	
	
	//-----------------------------------------
	
	
	
	function convert_tags($t="")
	{
		if ($t == "")
		{
			return "";
		}
		
		$t = preg_replace( "/{?\\\$this->ipsclass->base_url}?/"            , "{ibf.script_url}"   , $t );
		$t = preg_replace( "/{?\\\$this->ipsclass->session_id}?/"          , "{ibf.session_id}"   , $t );
		$t = preg_replace( "/{?\\\$this->ipsclass->skin\['?(\w+)'?\]}?/"   , "{ibf.skin.\\1}"      , $t );
		$t = preg_replace( "/{?\\\$this->ipsclass->lang\['?(\w+)'?\]}?/"   , "{ibf.lang.\\1}"      , $t );
		$t = preg_replace( "/{?\\\$this->ipsclass->vars\['?(\w+)'?\]}?/"   , "{ibf.vars.\\1}"      , $t );
		$t = preg_replace( "/{?\\\$this->ipsclass->member\['?(\w+)'?\]}?/" , "{ibf.member.\\1}"    , $t );
		
		return $t;
		
	}
	
	function unconvert_tags($t="")
	{
		if ($t == "")
		{
			return "";
		}
		
		$t = preg_replace( "/{ibf\.script_url}/i"   , '{$this->ipsclass->base_url}'         , $t);
		$t = preg_replace( "/{ibf\.session_id}/i"   , '{$this->ipsclass->session_id}'       , $t);
		$t = preg_replace( "/{ibf\.skin\.(\w+)}/"   , '{$this->ipsclass->skin[\''."\\1".'\']}'   , $t);
		$t = preg_replace( "/{ibf\.lang\.(\w+)}/"   , '{$this->ipsclass->lang[\''."\\1".'\']}'   , $t);
		$t = preg_replace( "/{ibf\.vars\.(\w+)}/"   , '{$this->ipsclass->vars[\''."\\1".'\']}'   , $t);
		$t = preg_replace( "/{ibf\.member\.(\w+)}/" , '{$this->ipsclass->member[\''."\\1".'\']}' , $t);
		
		return $t;
		
	}
	
	
}


?>