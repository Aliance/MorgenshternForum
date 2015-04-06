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
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Custom profile field functions
|   > Module written by Matt Mecham
|   > Date started: 24th June 2002
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


class ad_profilefields {

	var $base_url;
	var $func;
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "content";
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "field";
	
	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, 'Дополнительные поля' );
		
		//-----------------------------------------
		// get class
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/classes/class_custom_fields.php' );
		$this->func = new custom_fields( $DB );
		
		//-----------------------------------------
		// switch-a-magoo
		//-----------------------------------------
		
		switch($this->ipsclass->input['code'])
		{
			case 'add':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->main_form('add');
				break;
				
			case 'doadd':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':add' );
				$this->main_save('add');
				break;
				
			case 'edit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->main_form('edit');
				break;
				
			case 'doedit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':edit' );
				$this->main_save('edit');
				break;
				
			case 'delete':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->delete_form();
				break;
				
			case 'dodelete':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->do_delete();
				break;
						
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->main_screen();
				break;
		}
		
	}
	
	//-----------------------------------------
	//
	// Rebuild cache
	//
	//-----------------------------------------
	
	function rebuild_cache()
	{
		$this->ipsclass->cache['profilefields'] = array();
				
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'order' => 'pf_position' ) );
						 
		$this->ipsclass->DB->simple_exec();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['profilefields'][ $r['pf_id'] ] = $r;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'profilefields', 'array' => 1, 'deletefirst' => 1 ) );	
	}
	
	//-----------------------------------------
	//
	// Delete a group
	//
	//-----------------------------------------
	
	function delete_form()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Вы должны ввести ID дополнительного поля");
		}
		
		$this->ipsclass->admin->page_title = "Удаление дополнительного поля";
		
		$this->ipsclass->admin->page_detail = "Убедитесь, что вы действительно хотите удалить именно это дополнительное поле профиля, так как <b>вся информация добавленная пользователями в это поле будет утеряна</b>!";
		
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( ! $field = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти дополнительное поле с таким ID");
		}
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'dodelete'  ),
																 2 => array( 'act'   , 'field'     ),
																 3 => array( 'id'    , $this->ipsclass->input['id']   ),
																 4 => array( 'section', $this->ipsclass->section_code ),
														)      );
									     
		
		
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Подтверждение удаления" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Удаляемое дополнительное поле профиля:</b>" ,
												                 "<b>".$field['pf_title']."</b>",
									                   )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("Удалить это поле");
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();
	}
	
	
	
	function do_delete()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("Вы должны ввести ID дополнительного поля");
		}
		
		//-----------------------------------------
		// Check to make sure that the relevant groups exist.
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("Невозможно найти дополнительное поле с таким ID");
		}
		
		$this->ipsclass->DB->sql_drop_field( 'pfields_content', "field_{$row['pf_id']}" );
		
		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
		
		$this->rebuild_cache();
		
		$this->ipsclass->admin->done_screen("Удалено дополнительное поле профиля", "Дополнительные поля профиля", "{$this->ipsclass->form_code}", 'redirect' );
		
	}
	
	
	//-----------------------------------------
	//
	// Save changes to DB
	//
	//-----------------------------------------
	
	function main_save($type='edit')
	{
		$this->ipsclass->input['id'] = intval($this->ipsclass->input['id']);
		
		if ($this->ipsclass->input['pf_title'] == "")
		{
			$this->ipsclass->admin->error("Вы должны ввести название дополнительного поля профиля");
		}
		
		//-----------------------------------------
		// check-da-motcha
		//-----------------------------------------
		
		if ($type == 'edit')
		{
			if ($this->ipsclass->input['id'] == "")
			{
				$this->ipsclass->admin->error("Вы должны ввести ID дополнительного поля");
			}
			
		}
		
		$content = "";
		
		if ( $_POST['pf_content'] != "")
		{
			$content = $this->func->method_format_content_for_save( $_POST['pf_content'] );
		}
		
		$db_string = array( 'pf_title'        => $this->ipsclass->input['pf_title'],
						    'pf_desc'         => $this->ipsclass->input['pf_desc'],
						    'pf_content'      => $this->ipsclass->txt_stripslashes($content),
						    'pf_type'         => $this->ipsclass->input['pf_type'],
						    'pf_not_null'     => intval($this->ipsclass->input['pf_not_null']),
						    'pf_member_hide'  => intval($this->ipsclass->input['pf_member_hide']),
						    'pf_max_input'    => intval($this->ipsclass->input['pf_max_input']),
						    'pf_member_edit'  => intval($this->ipsclass->input['pf_member_edit']),
						    'pf_position'     => intval($this->ipsclass->input['pf_position']),
						    'pf_show_on_reg'  => intval($this->ipsclass->input['pf_show_on_reg']),
						    'pf_input_format' => $this->ipsclass->input['pf_input_format'],
						    'pf_admin_only'   => intval($this->ipsclass->input['pf_admin_only']),
						    'pf_topic_format' => $this->ipsclass->txt_stripslashes( $_POST['pf_topic_format']),
						  );
		
						  
		if ($type == 'edit')
		{
			$this->ipsclass->DB->do_update( 'pfields_data', $db_string, 'pf_id='.$this->ipsclass->input['id'] );
			
			$this->rebuild_cache();
			
			$this->ipsclass->main_msg = "Изменено дополнительное поле профиля";
			$this->main_screen();
			
		}
		else
		{
			$this->ipsclass->DB->do_insert( 'pfields_data', $db_string );
			
			$new_id = $this->ipsclass->DB->get_insert_id();
			
			$this->ipsclass->DB->sql_add_field( 'pfields_content', "field_$new_id", 'text' );
			
			$this->ipsclass->DB->sql_optimize_table( 'pfields_content' );
			
			$this->rebuild_cache();
			
			$this->ipsclass->main_msg = "Дополнительное поле профиля изменено";
			$this->main_screen();
		}
	}
	
	
	//-----------------------------------------
	//
	// Add / edit group
	//
	//-----------------------------------------
	
	function main_form($type='edit')
	{
		$this->ipsclass->input['id'] = intval($this->ipsclass->input['id']);
		$this->ipsclass->admin->nav[] = array( '', 'Добавление/изменение дополнительного поля' );
		
		if ($type == 'edit')
		{
			if ( ! $this->ipsclass->input['id'] )
			{
				$this->ipsclass->admin->error("Ни один ID группы не выбран, вернитесь и попробуйте снова.");
			}
			
			$form_code = 'doedit';
			$button    = 'Изменить это дополнительное поле';
				
		}
		else
		{
			$form_code = 'doadd';
			$button    = 'Добавить это дополнительное поле';
		}
		
		//-----------------------------------------
		// get field from db
		//-----------------------------------------
		
		if ( $this->ipsclass->input['id'] )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
			$this->ipsclass->DB->simple_exec();
		
			$fields = $this->ipsclass->DB->fetch_row();
		}
		else
		{
			$fields = array( 'pf_topic_format' => '{title}: {content}<br />' );
		}
		
		//-----------------------------------------
		// Top 'o 'the mornin'
		//-----------------------------------------
		
		if ($type == 'edit')
		{
			$this->ipsclass->admin->page_title = "Изменение дополнительного поля ".$fields['pf_title'];
		}
		else
		{
			$this->ipsclass->admin->page_title = 'Добавление дополнительного поля';
			$fields = array( 'pf_title'			=> '',
							 'pf_content'		=> '',
							 'pf_desc'			=> '',
							 'pf_type'			=> '',
							 'pf_max_input'		=> '',
							 'pf_position'		=> '',
							 'pf_input_format' 	=> '',
							 'pf_topic_format'	=> '',
							 'pf_show_on_reg'	=> '',
							 'pf_not_null'		=> '',
							 'pf_member_edit'	=> '',
							 'pf_member_hide'	=> '',
							 'pf_admin_only'	=> '' );
		}
		
		//-----------------------------------------
		// Wise words
		//-----------------------------------------
		
		$this->ipsclass->admin->page_detail = "Пожалуйста, дважды тщательно проверьте всю информацию прежде, чем подтвердить это дополнительное поле профиля";
		
		//-----------------------------------------
		// Start form
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , $form_code  ),
												                 2 => array( 'act'   , 'field'     ),
												                 3 => array( 'id'    , $this->ipsclass->input['id']   ),
												                 4 => array( 'section', $this->ipsclass->section_code ),
									                    )     );
		
		//-----------------------------------------
		// Format...
		//-----------------------------------------
									     
		$fields['pf_content'] = $this->func->method_format_content_for_edit($fields['pf_content'] );
		
		//-----------------------------------------
		// Tbl (no ae?)
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Настройка дополнительных полей профиля" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Название</b><div class='graytext'>Максимальное количество символов: 200</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_title", $fields['pf_title'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Описание</b><div class='graytext'>Максимальное количество символов: 250.<br />Может быть использовано для примечаний.</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_desc", $fields['pf_desc'] )
									                    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Тип поля</b>" ,
																 $this->ipsclass->adskin->form_dropdown("pf_type",
																					  array(
																							   0 => array( 'text' , 'Однострочное текстовое (input)' ),
																							   1 => array( 'drop' , 'Выпадающее меню (drowdown)' ),
																							   2 => array( 'area' , 'Многострочное текстовое (textarea)' ),
																						   ),
																					  $fields['pf_type'] )
														)      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Максимальная длина</b><div class='graytext'>Для текстовых полей (только цифры)</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_max_input", $fields['pf_max_input'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Порядок сортировки</b><div class='graytext'>При редактировании и отображении.<br />Цифра 1 — самая низкая позиция.</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_position", $fields['pf_position'] )
									                    )      );
									                    
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Формат поля</b><div class='graytext'>Использование: <b>a</b> — буквы<br /><b>n</b> — цифры.<br />Например, кредитная карта: nnnn-nnnn-nnnn-nnnn<br />Например, дата рождения: nn-nn-nnnn<br />Оставьте поле пустым для использования любых символов.</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_input_format", $fields['pf_input_format'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Содержимое опций для выпадающего меню</b><div class='graytext'>Использование: <b>ключ=значение</b> (по одному значению на строку)<br />Пример для поля <b>Пол</b>:<br /><b>m=мужской<br />f=женский<br />u=не скажу</b><br />Результат: <select name='pants' class='dropdown'><option value='m'>мужской</option><option value='f'>женский</option><option value='u'>не скажу</option></select><br />Одно из значений: <b>m</b>, <b>f</b> или <b>u</b> будет сохранено в базе данных.<br />При отображении в профиле выводится <b>Пол: не скажу</b> (при выборе пользователем значения <b>не скажу</b>)</div>" ,
												                 $this->ipsclass->adskin->form_textarea("pf_content", $fields['pf_content'] )
									                    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Добавить на страницу регистрации?</b><div class='graytext'>При выборе «Да», это дополнительное поле будет предложено заполнить при регистрации.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_show_on_reg", $fields["pf_show_on_reg"] )
									                    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Это поле обязательно к заполнению?</b><div class='graytext'>При выборе «Да», будет выводиться сообщение об ошибке до тех пор, пока пользователь его не заполнит.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_not_null", $fields['pf_not_null'] )
									                    )      );
									                    
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Поле может быть изменено пользователем?</b><div class='graytext'>При выборе «Нет», пользователь не сможет редактировать это поле, но смогут супермодераторы и администраторы.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_member_edit", $fields['pf_member_edit'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Сделать это поле личным?</b><div class='graytext'>При выборе «Да», поле будет видимым только для самого пользователя, супермодераторов и администраторов.<br />При выборе «Нет», остальные пользователи смогут искать по этому полю.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_member_hide", $fields['pf_member_hide'] )
									                    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Сделать видимым и изменяемым только супермодераторам и администраторам?</b><div class='graytext'>При выборе «Да», эта опция отменит предыдущие, так что только супермодераторы и администраторы смогут видеть и изменять это поле.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_admin_only", $fields['pf_admin_only'] )
									                    )      );
									                    
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>Отображение поля в сообщении:</b><div class='graytext'>Оставьте поле пустым, если вы не хотите, чтобы это дополнительное поле добавлялось после информации об авторе при отображении сообщений.<br />Доступные теги:<br /><b>{title}</b> — название дополнительного поля<br /><b>{content}</b> — содержимое дополнительного поля<br /><b>{key}</b> — выбор пользователя из выпадающего меню<br />Пример: <b>{title}:{content}&lt;br /&gt;</b><br />Пример: <b>{title}:&lt;img src='imgs/{key}.gif'&gt;</b></div>" ,
												                 $this->ipsclass->adskin->form_textarea("pf_topic_format", $fields['pf_topic_format'] )
									                    )      );					     							     
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form($button);
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();


	}

	//-----------------------------------------
	//
	// Show "Management Screen
	//
	//-----------------------------------------
	
	function main_screen()
	{
		$this->ipsclass->admin->page_title   = "Дополнительные поля профиля";
		
		$this->ipsclass->admin->page_detail  = "Дополнительные поля профиля могут использоваться для получения дополнительной информации о ваших пользователях, они могут изменяться при регистрации или во время редактирования профиля.<br />Эта опция очень полезна, если вы хотите собрать ту информацию о пользователях, которая вам необходима, но при этом не доступна в базовой версии форума.";
		
		$this->ipsclass->adskin->td_header[] = array( "Название"    , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "Тип"           , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Обязательное?"       , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Личное?"     , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Показывать при регистрации?"       , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Только админ"     , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Изменить"           , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "Удалить"         , "10%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Список дополнительных полей профиля" );
		
		$real_types = array( 'drop' => 'выпадающее меню',
							 'area' => 'текстовый блок',
							 'text' => 'текстовое поле',
						   );
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'order' => 'pf_position' ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
			
				$hide   = '&nbsp;';
				$req    = '&nbsp;';
				$regi   = '&nbsp;';
				$admin  = '&nbsp;';
				
				//-----------------------------------------
				// Hidden?
				//-----------------------------------------
				
				if ($r['pf_member_hide'] == 1)
				{
					$hide = '<center><span style="color:red">Да</span></center>';
				}
				
				//-----------------------------------------
				// Required?
				//-----------------------------------------
				
				if ($r['pf_not_null'] == 1)
				{
					$req = '<center><span style="color:red">Да</span></center>';
				}
				
				//-----------------------------------------
				// Show on reg?
				//-----------------------------------------
				
				if ($r['pf_show_on_reg'] == 1)
				{
					$regi = '<center><span style="color:red">Да</span></center>';
				}
				
				//-----------------------------------------
				// Admin only...
				//-----------------------------------------
				
				if ($r['pf_admin_only'] == 1)
				{
					$admin = '<center><span style="color:red">Да</span></center>';
				}
				
				
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>{$r['pf_title']}</b><div class='graytext'>{$r['pf_desc']}</div>" ,
																		 "<center>{$real_types[$r['pf_type']]}</center>",
																		 $req,
																		 $hide,
																		 $regi,
																		 $admin,
																		 "<center><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=edit&id=".$r['pf_id']."'>Изменить</a></center>",
																		 "<center><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=delete&id=".$r['pf_id']."'>Удалить</a></center>",
															)      );
											 
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("Не найдено", "center", "tablerow1");
		}
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<div class='fauxbutton-wrapper'><span class='fauxbutton'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=add'>Добавить</a></span></div>", 'center', 'tablefooter' );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		
		$this->ipsclass->admin->output();
		
		
	}
}


?>