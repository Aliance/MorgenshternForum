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
|   > $Date: 2006-10-05 11:04:08 -0500 (Thu, 05 Oct 2006) $
|   > $Revision: 609 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Skin -> Image Macro functions
|   > Module written by Matt Mecham
|   > Date started: 4th April 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}


class ad_skin_macros {

	var $base_url;
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "lookandfeel";
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "image";


	function auto_run()
	{
		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'edit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->show_macros();
				break;
				
			case 'doedit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->edit_set_name();
				break;
				
			case 'macroremove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->macro_remove();
				break;
				
			case 'doeditmacro':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->macro_edit();
				break;
				
			case 'doaddmacro':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->macro_add();
				break;
				
			default:
				print "Действие не выбрано"; exit();
				break;
		}
	}
	
	//-----------------------------------------
	// Remove macro
	//-----------------------------------------
	
	function macro_remove()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("Вы должны выбрать ID макроса, вернитесь назад и попробуйте еще раз");
		}
		
		$id = intval($this->ipsclass->input['id']);
		$p  = intval($this->ipsclass->input['p']);
		
		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'skin_macro', 'where' => "macro_id=".intval($this->ipsclass->input['mid']) ) );
		
		//-----------------------------------------
		// Recache macros
		//-----------------------------------------
		
		$this->ipsclass->cache_func->_recache_macros($id, $p);
		
		//-----------------------------------------
		// Bounce back
		//-----------------------------------------
		
		$this->ipsclass->input['id'] = $id;
		$this->ipsclass->main_msg = "Макрос удален!";
		$this->show_macros();
	}
	
	//-----------------------------------------
	// Apply the edit to the DB
	//-----------------------------------------
	
	function macro_edit()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("Вы должны выбрать ID макроса, вернитесь назад и попробуйте еще раз");
		}
		
		$id = intval($this->ipsclass->input['id']);
		$p  = intval($this->ipsclass->input['p']);
		
		$key = $this->ipsclass->DB->add_slashes( $this->ipsclass->txt_safeslashes($_POST['variable']) );
		$val = $this->ipsclass->DB->add_slashes( $this->ipsclass->txt_safeslashes($_POST['replacement']) );
		
		//-----------------------------------------
		// Get macro for examination..
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_macro', 'where' => "macro_id=".intval($this->ipsclass->input['mid']) ) );
		$this->ipsclass->DB->simple_exec();
		
 		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно получить запись из базы данных");
		}
		
		//-----------------------------------------
		// Is this our macro set?
		//-----------------------------------------
		
		if ( $row['macro_set'] == $id )
		{
			//-----------------------------------------
			// Okay, update...
			//-----------------------------------------
			
			$this->ipsclass->DB->simple_construct( array( 'update' => 'skin_macro', 'set' => "macro_value='$key', macro_replace='$val'", 'where' => "macro_id=".intval($this->ipsclass->input['mid']) ) );
			$this->ipsclass->DB->simple_exec();
		}
		else
		{
			//-----------------------------------------
			// No? OK - best add it as a 'new' macro
			//-----------------------------------------
			
			$this->ipsclass->DB->manual_addslashes = 1;
			$this->ipsclass->DB->do_insert( 'skin_macro', array (
												'macro_value'         => $key,
												'macro_replace'       => $val,
												'macro_can_remove'    => 1,
												'macro_set'           => $id
										)      );
										
			$this->ipsclass->DB->manual_addslashes = 0;
		}
		
		
		//-----------------------------------------
		// Recache macros
		//-----------------------------------------
		
		$this->ipsclass->cache_func->_recache_macros($id, $p);
		
		//-----------------------------------------
		// Bounce back
		//-----------------------------------------
		
		$this->ipsclass->input['id'] = $id;
		$this->show_macros();
	}
	
	//-----------------------------------------
	// ADD MACRO
	//-----------------------------------------
	
	function macro_add()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("Вы должны выбрать ID макроса, вернитесь назад и попробуйте еще раз");
		}
		
		$id = intval($this->ipsclass->input['id']);
		$p  = intval($this->ipsclass->input['p']);
		
		$this->ipsclass->DB->do_insert( 'skin_macro', array (
											 'macro_value'         => $this->ipsclass->txt_safeslashes($_POST['variable']),
											 'macro_replace'       => $this->ipsclass->txt_safeslashes($_POST['replacement']),
											 'macro_can_remove'    => 1,
											 'macro_set'           => $id
									 )      );
		
		//-----------------------------------------
		// Recache macros
		//-----------------------------------------
		
		$this->ipsclass->cache_func->_recache_macros($id, $p);
		
		//-----------------------------------------
		// Bounce back
		//-----------------------------------------
		
		$this->ipsclass->input['id'] = $id;
		$this->show_macros();
		
	}
	
	
	
	//-----------------------------------------
	// Show macros
	//-----------------------------------------
	
	function show_macros()
	{
		//-----------------------------------------
		
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Вы должны выбрать ID макроса, вернитесь назад и попробуйте еще раз");
		}
		
		//-----------------------------------------
		// check tree...
		//-----------------------------------------
		
		$this_set      = "";
		
		if ( $this->ipsclass->input['p'] > 0 )
		{
			$in = ','.$this->ipsclass->input['p'];
		}
		
		//-----------------------------------------
		// Get macros
		//-----------------------------------------
		
		$macros = $this->ipsclass->cache_func->_get_macros($this->ipsclass->input['id'], $this->ipsclass->input['p']);
		
		//-----------------------------------------
		// Get img_dir this set is using...
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skin_sets', 'where' => "set_skin_set_id=".intval($this->ipsclass->input['id']) ) );
		$this->ipsclass->DB->simple_exec();
		
		$skin = $this->ipsclass->DB->fetch_row();
		
		$this->ipsclass->admin->page_detail = "Для изменения макроса, нажмите на ссылку 'Изменить' напротив выбранного макроса.";
										 
		$this->ipsclass->admin->page_title  = "Управление заменами макросов шаблона: {$skin['set_name']}";
		
		//-----------------------------------------
		// Start output
		//-----------------------------------------
															  
		$this->ipsclass->html .= "<script type='text/javascript'>
							function editmacro(id, variable, replace)
							{
								document.macroform.code.value         = 'doeditmacro';
								document.macroform.submitbutton.value = 'Изменить этот макрос';
								document.macroform.mid.value          = id;
								document.macroform.variable.value     = variable;
								document.macroform.replacement.value  = replace;
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}
							function addmacro(id)
							{
								document.macroform.code.value         = 'doaddmacro';
								document.macroform.submitbutton.value = 'Добавить этот макрос';
								document.macroform.mid.value          = id;
								document.macroform.variable.value     = '';
								document.macroform.replacement.value  = '';
								scroll(0,0);
								togglediv( 'popbox', 1 );
								return false;
							}
							function removemacro(url)
							{
								checkdelete(url);
							}
							</script>
							<div class='tableborder'>
							<div class='tableheaderalt'>
							  <table cellpadding='0' cellspacing='0' border='0' width='100%'>
							  <tr>
							  <td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Управление заменами макросов</td>
							  <td align='right' nowrap='nowrap' style='padding-right:2px'><input type='button' class='realdarkbutton' value='Добавить макрос' onclick=\"addmacro('{$this->ipsclass->input['id']}');\" /></td>
							  </tr>
							  </table>
							  <div align='center' style='position:absolute;display:none;text-align:center;' id='popbox'>
							   <form name='macroform' action='{$this->ipsclass->base_url}' method='post'>
							   <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
							   <input type='hidden' name='act' value='image' />
							   <input type='hidden' name='section' value='{$this->ipsclass->section_code}' />
							   <input type='hidden' name='code' value='' />
							   <input type='hidden' name='mid' value='' />
							   <input type='hidden' name='id' value='{$this->ipsclass->input['id']}' />
							   <input type='hidden' name='p' value='{$this->ipsclass->input['p']}' />
							   <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
							   <tr>
								<td width='1%' nowrap='nowrap' valign='top'>
								 <b>Переменная</b><br /><input class='textinput' name='variable' type='text' size='20' />
								 <br /><br />
								 <center><input type='submit' class='realbutton' value='Изменить макрос' name='submitbutton' /> <input type='button' class='realdarkbutton' value='Закрыть' onclick=\"togglediv('popbox');\" /></center>
								</td>
								<td width='99%'><b>Замена</b><br /><textarea class='textinput' name='replacement' style='width:99%;height:50px'></textarea></td>
							   </tr>
							   </table>
							   </form>
							  </div>
							</div>
							</div>
							
						   ";
			
		$this->ipsclass->html .= "<div class='tableborder'>\n<div class='tablepad'>\n<table width='100%' cellspacing='0' cellpadding='0' border='0'>";											  
		
		$this->unaltered    = "<img src='{$this->ipsclass->skin_acp_url}/images/skin_item_unaltered.gif' border='0' alt='-' title='Не изменен от начального варианта шаблона' />&nbsp;";
		$this->altered      = "<img src='{$this->ipsclass->skin_acp_url}/images/skin_item_altered.gif' border='0' alt='+' title='Изменен от начального варианта шаблона' />&nbsp;";
		$this->inherited    = "<img src='{$this->ipsclass->skin_acp_url}/images/skin_item_inherited.gif' border='0' alt='|' title='Унаследованный от начального варианта шаблона' />&nbsp;";
		
		//-----------------------------------------
		// Loop and print
		//-----------------------------------------
		
		foreach( $macros as $row )
		{
			$real = $this->ipsclass->txt_htmlspecialchars( $row['macro_replace'] );
			$real = str_replace("\n", '\n', str_replace( "\r", '\r', $real) );
			
			//-----------------------------------------
			// Altered?
			//-----------------------------------------
			
			if ( $row['macro_set'] == $this->ipsclass->input['id'] )
			{
				$altered_image = $this->altered;
				$css_info      = '#FFDCD8';
			}
			else if ( $row['macro_set'] == 1 )
			{
				$altered_image = $this->unaltered;
				$css_info      = '#EEE';
			}
			else
			{
				$altered_image = $this->inherited;
				$css_info      = '#FFF2D3';
			}
			
			//-----------------------------------------
			// Figure out quotes
			//-----------------------------------------
			
			$out_quote = '"';
			$in_quote  = "'";
			
			if ( preg_match( "/&#039;/", $real ) )
			{
				$out_quote = "'";
				$in_quote  = '"';
			}
			
			if( $in_quote == '"' )
			{
				$real = str_replace( "&quot;", '\"', $real );
			}
			
			if( $in_quote == "'" )
			{
				$real = str_replace( "'", "\\'", $real );
			}
			
			$preview = str_replace( "<#IMG_DIR#>", $skin['set_image_dir'], $row['macro_replace'] );
			
			if ( $row['macro_set'] > 1 and $row['macro_set'] == $this->ipsclass->input['id'] )
			{
				$remove_button = "<input type='button' class='realbutton' name='remove' value='Вернуть начальное значение' onclick=\"removemacro('{$this->ipsclass->form_code_js}&code=macroremove&mid={$row['macro_id']}&id={$this->ipsclass->input['id']}&p={$this->ipsclass->input['p']}');\" />";
			}
			else
			{
				$remove_button = "";
			}
			
			//-----------------------------------------
			// Not an image?
			//-----------------------------------------
			
			if ( ! preg_match( "#img\s{1,}src=#i", $row['macro_replace'] ) )
			{
				$preview = substr( $real, 0, 200 );
			}
			else
			{
				$preview = preg_replace( "#style_images#", $this->ipsclass->vars['board_url'].'/style_images', $preview );
			}
			
			$edit_button = "<input type='button' class='realbutton' value='Изменить' onclick={$out_quote}editmacro( {$in_quote}{$row['macro_id']}{$in_quote}, {$in_quote}{$row['macro_value']}{$in_quote}, {$in_quote}$real{$in_quote});{$out_quote} />";
			
			//-----------------------------------------
			// Render row
			//-----------------------------------------
			
			$style = "padding:4px;border-bottom:1px solid #DDD;background:{$css_info}";
			
			$this->ipsclass->html .= "<tr>
								 <!--<td style='$style' align='center' width='1%'><img src='{$this->ipsclass->skin_acp_url}/images/skin_macro.gif' alt='Macro' title='ID: {$row['macro_id']}' style='vertical-align:middle' /></td>-->
								 <td style='$style' align='left' width='1%' nowrap='nowrap'>$altered_image
								  &nbsp;&lt;{<span style='font-size:11px;font-weight:bold' title='ID: {$row['macro_id']}. SET: {$row['macro_set']}' href='#' >{$row['macro_value']}</span>}&gt;
								  </td>
								 <td style='$style;padding-right:3px;text-align:center;' align='center' width='99%' align='center'>$preview</td>
								 <td style='$style' align='right' width='40%' nowrap='nowrap'>$remove_button $edit_button</td>
								</tr>
								
								";
		}
									     
										 
		$this->ipsclass->html .= "</table>
						   </div>
						   <div class='tablesubheader' align='center'>
						    <input type='button' class='realdarkbutton' value='Добавить макрос' onclick=\"addmacro('{$this->ipsclass->input['id']}');\" />
						   </div>
						   </div>";
		
		$this->ipsclass->html .= $this->ipsclass->adskin->skin_jump_menu_wrap();
		
		//-----------------------------------------
		// Show altered / unaltered
		// legend
		//-----------------------------------------
		
		$this->ipsclass->html .= "<br />
							<div><strong>Пример работы макроса</strong><br />
							Если вы добавили макрос 'green_font' и замену для него в виде '&lt;font color='green'>', каждый элемент текста <span style='color:red'><b>&lt;{green_font}&gt;</b></span> будет отображен как &lt;font color='green'>
							<br /><b>&lt;#IMG_DIR#></b> доступен в любом макросе. Во время вывода текста на экран будет произведена замена данного тега на путь прописанный в настройках к директории с изображениями.
							</div><br />
							<div><strong>Описание обозначений:</strong><br />
							{$this->altered} Этот макрос был изменен.
							<br />{$this->unaltered} Этот макрос не был изменен.
							<br />{$this->inherited} Этот макрос построен на базе существующего макрос.
							</div>";
		
		$this->ipsclass->admin->nav[] = array( 'section='.$this->ipsclass->section_code.'&act=sets' ,'Управление стилями' );
		$this->ipsclass->admin->nav[] = array( '' ,'Управление заменами макросов  '.$skin['set_name'] );
		
		$this->ipsclass->admin->output();
		
	}
	
	
	
}


?>