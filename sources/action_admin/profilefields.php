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
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
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
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
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
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, '�������������� ����' );
		
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
			$this->ipsclass->admin->error("�� ������ ������ ID ��������������� ����");
		}
		
		$this->ipsclass->admin->page_title = "�������� ��������������� ����";
		
		$this->ipsclass->admin->page_detail = "���������, ��� �� ������������� ������ ������� ������ ��� �������������� ���� �������, ��� ��� <b>��� ���������� ����������� �������������� � ��� ���� ����� �������</b>!";
		
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( ! $field = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("���������� ����� �������������� ���� � ����� ID");
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
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������������� ��������" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>��������� �������������� ���� �������:</b>" ,
												                 "<b>".$field['pf_title']."</b>",
									                   )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("������� ��� ����");
										 
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();
	}
	
	
	
	function do_delete()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("�� ������ ������ ID ��������������� ����");
		}
		
		//-----------------------------------------
		// Check to make sure that the relevant groups exist.
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
		$this->ipsclass->DB->simple_exec();
		
		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("���������� ����� �������������� ���� � ����� ID");
		}
		
		$this->ipsclass->DB->sql_drop_field( 'pfields_content', "field_{$row['pf_id']}" );
		
		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'pfields_data', 'where' => "pf_id=".intval($this->ipsclass->input['id']) ) );
		
		$this->rebuild_cache();
		
		$this->ipsclass->admin->done_screen("������� �������������� ���� �������", "�������������� ���� �������", "{$this->ipsclass->form_code}", 'redirect' );
		
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
			$this->ipsclass->admin->error("�� ������ ������ �������� ��������������� ���� �������");
		}
		
		//-----------------------------------------
		// check-da-motcha
		//-----------------------------------------
		
		if ($type == 'edit')
		{
			if ($this->ipsclass->input['id'] == "")
			{
				$this->ipsclass->admin->error("�� ������ ������ ID ��������������� ����");
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
			
			$this->ipsclass->main_msg = "�������� �������������� ���� �������";
			$this->main_screen();
			
		}
		else
		{
			$this->ipsclass->DB->do_insert( 'pfields_data', $db_string );
			
			$new_id = $this->ipsclass->DB->get_insert_id();
			
			$this->ipsclass->DB->sql_add_field( 'pfields_content', "field_$new_id", 'text' );
			
			$this->ipsclass->DB->sql_optimize_table( 'pfields_content' );
			
			$this->rebuild_cache();
			
			$this->ipsclass->main_msg = "�������������� ���� ������� ��������";
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
		$this->ipsclass->admin->nav[] = array( '', '����������/��������� ��������������� ����' );
		
		if ($type == 'edit')
		{
			if ( ! $this->ipsclass->input['id'] )
			{
				$this->ipsclass->admin->error("�� ���� ID ������ �� ������, ��������� � ���������� �����.");
			}
			
			$form_code = 'doedit';
			$button    = '�������� ��� �������������� ����';
				
		}
		else
		{
			$form_code = 'doadd';
			$button    = '�������� ��� �������������� ����';
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
			$this->ipsclass->admin->page_title = "��������� ��������������� ���� ".$fields['pf_title'];
		}
		else
		{
			$this->ipsclass->admin->page_title = '���������� ��������������� ����';
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
		
		$this->ipsclass->admin->page_detail = "����������, ������ ��������� ��������� ��� ���������� ������, ��� ����������� ��� �������������� ���� �������";
		
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
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������� �������������� ����� �������" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>��������</b><div class='graytext'>������������ ���������� ��������: 200</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_title", $fields['pf_title'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>��������</b><div class='graytext'>������������ ���������� ��������: 250.<br />����� ���� ������������ ��� ����������.</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_desc", $fields['pf_desc'] )
									                    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>��� ����</b>" ,
																 $this->ipsclass->adskin->form_dropdown("pf_type",
																					  array(
																							   0 => array( 'text' , '������������ ��������� (input)' ),
																							   1 => array( 'drop' , '���������� ���� (drowdown)' ),
																							   2 => array( 'area' , '������������� ��������� (textarea)' ),
																						   ),
																					  $fields['pf_type'] )
														)      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������������ �����</b><div class='graytext'>��� ��������� ����� (������ �����)</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_max_input", $fields['pf_max_input'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������� ����������</b><div class='graytext'>��� �������������� � �����������.<br />����� 1 � ����� ������ �������.</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_position", $fields['pf_position'] )
									                    )      );
									                    
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������ ����</b><div class='graytext'>�������������: <b>a</b> � �����<br /><b>n</b> � �����.<br />��������, ��������� �����: nnnn-nnnn-nnnn-nnnn<br />��������, ���� ��������: nn-nn-nnnn<br />�������� ���� ������ ��� ������������� ����� ��������.</div>" ,
												                 $this->ipsclass->adskin->form_input("pf_input_format", $fields['pf_input_format'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>���������� ����� ��� ����������� ����</b><div class='graytext'>�������������: <b>����=��������</b> (�� ������ �������� �� ������)<br />������ ��� ���� <b>���</b>:<br /><b>m=�������<br />f=�������<br />u=�� �����</b><br />���������: <select name='pants' class='dropdown'><option value='m'>�������</option><option value='f'>�������</option><option value='u'>�� �����</option></select><br />���� �� ��������: <b>m</b>, <b>f</b> ��� <b>u</b> ����� ��������� � ���� ������.<br />��� ����������� � ������� ��������� <b>���: �� �����</b> (��� ������ ������������� �������� <b>�� �����</b>)</div>" ,
												                 $this->ipsclass->adskin->form_textarea("pf_content", $fields['pf_content'] )
									                    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>�������� �� �������� �����������?</b><div class='graytext'>��� ������ ���, ��� �������������� ���� ����� ���������� ��������� ��� �����������.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_show_on_reg", $fields["pf_show_on_reg"] )
									                    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>��� ���� ����������� � ����������?</b><div class='graytext'>��� ������ ���, ����� ���������� ��������� �� ������ �� ��� ���, ���� ������������ ��� �� ��������.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_not_null", $fields['pf_not_null'] )
									                    )      );
									                    
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>���� ����� ���� �������� �������������?</b><div class='graytext'>��� ������ ����, ������������ �� ������ ������������� ��� ����, �� ������ ��������������� � ��������������.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_member_edit", $fields['pf_member_edit'] )
									                    )      );
									     
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������� ��� ���� ������?</b><div class='graytext'>��� ������ ���, ���� ����� ������� ������ ��� ������ ������������, ���������������� � ���������������.<br />��� ������ ����, ��������� ������������ ������ ������ �� ����� ����.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_member_hide", $fields['pf_member_hide'] )
									                    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������� ������� � ���������� ������ ���������������� � ���������������?</b><div class='graytext'>��� ������ ���, ��� ����� ������� ����������, ��� ��� ������ ��������������� � �������������� ������ ������ � �������� ��� ����.</div>" ,
												                 $this->ipsclass->adskin->form_yes_no("pf_admin_only", $fields['pf_admin_only'] )
									                    )      );
									                    
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>����������� ���� � ���������:</b><div class='graytext'>�������� ���� ������, ���� �� �� ������, ����� ��� �������������� ���� ����������� ����� ���������� �� ������ ��� ����������� ���������.<br />��������� ����:<br /><b>{title}</b> � �������� ��������������� ����<br /><b>{content}</b> � ���������� ��������������� ����<br /><b>{key}</b> � ����� ������������ �� ����������� ����<br />������: <b>{title}:{content}&lt;br /&gt;</b><br />������: <b>{title}:&lt;img src='imgs/{key}.gif'&gt;</b></div>" ,
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
		$this->ipsclass->admin->page_title   = "�������������� ���� �������";
		
		$this->ipsclass->admin->page_detail  = "�������������� ���� ������� ����� �������������� ��� ��������� �������������� ���������� � ����� �������������, ��� ����� ���������� ��� ����������� ��� �� ����� �������������� �������.<br />��� ����� ����� �������, ���� �� ������ ������� �� ���������� � �������������, ������� ��� ����������, �� ��� ���� �� �������� � ������� ������ ������.";
		
		$this->ipsclass->adskin->td_header[] = array( "��������"    , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "���"           , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "������������?"       , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "������?"     , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "���������� ��� �����������?"       , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "������ �����"     , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "��������"           , "10%" );
		$this->ipsclass->adskin->td_header[] = array( "�������"         , "10%" );
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������ �������������� ����� �������" );
		
		$real_types = array( 'drop' => '���������� ����',
							 'area' => '��������� ����',
							 'text' => '��������� ����',
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
					$hide = '<center><span style="color:red">��</span></center>';
				}
				
				//-----------------------------------------
				// Required?
				//-----------------------------------------
				
				if ($r['pf_not_null'] == 1)
				{
					$req = '<center><span style="color:red">��</span></center>';
				}
				
				//-----------------------------------------
				// Show on reg?
				//-----------------------------------------
				
				if ($r['pf_show_on_reg'] == 1)
				{
					$regi = '<center><span style="color:red">��</span></center>';
				}
				
				//-----------------------------------------
				// Admin only...
				//-----------------------------------------
				
				if ($r['pf_admin_only'] == 1)
				{
					$admin = '<center><span style="color:red">��</span></center>';
				}
				
				
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>{$r['pf_title']}</b><div class='graytext'>{$r['pf_desc']}</div>" ,
																		 "<center>{$real_types[$r['pf_type']]}</center>",
																		 $req,
																		 $hide,
																		 $regi,
																		 $admin,
																		 "<center><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=edit&id=".$r['pf_id']."'>��������</a></center>",
																		 "<center><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=delete&id=".$r['pf_id']."'>�������</a></center>",
															)      );
											 
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("�� �������", "center", "tablerow1");
		}
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<div class='fauxbutton-wrapper'><span class='fauxbutton'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=add'>��������</a></span></div>", 'center', 'tablefooter' );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		
		$this->ipsclass->admin->output();
		
		
	}
}


?>