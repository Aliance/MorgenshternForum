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
|   > Email Logs Stuff
|   > Module written by Matt Mecham
|   > Date started: 11nd September 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}


class ad_emaillogs
{

	var $base_url;
	var $colours = array();
	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "admin";

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "emaillog";

	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, '������ e-mail �����������' );

		// Make sure we're a root admin, or else!

		if ($this->ipsclass->member['mgroup'] != $this->ipsclass->vars['admin_group'])
		{
			//$this->ipsclass->admin->error("��������, ��� ������� �������� ������ ������� ���������������.");
		}

		switch($this->ipsclass->input['code'])
		{
			case 'list':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->list_current();
				break;

			case 'remove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->remove_entries();
				break;

		    case 'viewemail':
		    	$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
		    	$this->view_email();
		    	break;

			//-----------------------------------------
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// View a single email.
	//-----------------------------------------

	function view_email()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("���������� ���������� ID ������ email, ��������� � ���������� ��� ���");
		}

		$id = intval($this->ipsclass->input['id']);

		$this->ipsclass->DB->cache_add_query( 'emaillogs_view_email', array( 'id' => $id ) );
		$this->ipsclass->DB->cache_exec_query();

		if ( ! $row = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("���������� ���������� ID ������ email, ��������� � ���������� ��� ��� ($id)");
		}

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "100%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( $row['email_subject'] );



		$row['email_date'] = $this->ipsclass->admin->get_date( $row['email_date'], 'LONG' );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													"<strong>��:</strong> {$row['name']} &lt;{$row['from_email_address']}&gt;
													<br /><strong>����:</strong> {$row['to_name']} &lt;{$row['to_email_address']}&gt;
													<br /><strong>����������:</strong> {$row['email_date']}
													<br /><strong>IP-�����:</strong> {$row['from_ip_address']}
													<br /><strong>����:</strong> {$row['email_subject']}
													<hr>
													<br />{$row['email_content']}
												    "
										 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->print_popup();
	}



	//-----------------------------------------
	// Remove row(s)
	//-----------------------------------------

	function remove_entries()
	{
		if ( $this->ipsclass->input['type'] == 'all' )
		{
			$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'email_logs' ) );
		}
		else
		{
			$ids = array();

			foreach ($this->ipsclass->input as $k => $v)
			{
				if ( preg_match( "/^id_(\d+)$/", $k, $match ) )
				{
					if ($this->ipsclass->input[ $match[0] ])
					{
						$ids[] = $match[1];
					}
				}
			}

			$ids = $this->ipsclass->clean_int_array( $ids );

			//-----------------------------------------

			if ( count($ids) < 1 )
			{
				$this->ipsclass->admin->error("�� ������ ������� ���� ���� ������ �� ������� ��� ��������");
			}

			$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'email_logs', 'where' => " email_id IN (".implode(',', $ids ).")" ) );
		}

		$this->ipsclass->admin->save_log("������� ������ �� ������� e-mail �����������");

		$this->ipsclass->boink_it($this->ipsclass->base_url."&act=emaillog");
		exit();


	}





	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		$this->ipsclass->html .= ""; // removed js popwin

		$form_array = array();

		$start = intval($this->ipsclass->input['st']) >=0 ? intval($this->ipsclass->input['st']) : 0;

		$this->ipsclass->admin->page_detail = "����������� ������ ������� e-mail �����������";
		$this->ipsclass->admin->page_title  = "���������� �������� e-mail �����������";

		//-----------------------------------------
		// Check URL parameters
		//-----------------------------------------

		$url_query = array();
		$db_query  = array();

		if ( isset($this->ipsclass->input['type']) AND $this->ipsclass->input['type'] != "" )
		{
			$this->ipsclass->admin->page_title .= " (���������� ������)";

			switch( $this->ipsclass->input['type'] )
			{
				case 'fromid':
					$url_query[] = 'type=fromid';
					$url_query[] = 'id='.intval($this->ipsclass->input['id']);
					$db_query[]  = 'email.from_member_id='.intval($this->ipsclass->input['id']);
					break;
				case 'toid':
					$url_query[] = 'type=toid';
					$url_query[] = 'id='.intval($this->ipsclass->input['id']);
					$db_query[]  = 'email.to_member_id='.intval($this->ipsclass->input['id']);
					break;
				case 'subject':
					$string = urldecode($this->ipsclass->input['string']);
					if ( $string == "" )
					{
						$this->ipsclass->admin->error("�� ������ ������ ���� ���-������");
					}
					$url_query[] = 'type='.$this->ipsclass->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $this->ipsclass->input['match'] == 'loose' ? "email.email_subject LIKE '%{$string}%'" : "email.email_subject='{$string}'";
					break;
				case 'content':
					$string = urldecode($this->ipsclass->input['string']);
					if ( $string == "" )
					{
						$this->ipsclass->admin->error("�� ������ ������ ���� ���-������");
					}
					$url_query[] = 'type='.$this->ipsclass->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $this->ipsclass->input['match'] == 'loose' ? "email.email_content LIKE '%{$string}%'" : "email.email_content='{$string}'";
					break;
				case 'email_from':
					$string = urldecode($this->ipsclass->input['string']);
					if ( $string == "" )
					{
						$this->ipsclass->admin->error("�� ������ ������ ���� ���-������");
					}
					$url_query[] = 'type='.$this->ipsclass->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $this->ipsclass->input['match'] == 'loose' ? "email.from_email_address LIKE '%{$string}%'" : "email.from_email_address='{$string}'";
					break;
				case 'email_to':
					$string = urldecode($this->ipsclass->input['string']);
					if ( $string == "" )
					{
						$this->ipsclass->admin->error("�� ������ ������ ���� ���-������");
					}
					$url_query[] = 'type='.$this->ipsclass->input['type'];
					$url_query[] = 'string='.urlencode($string);
					$db_query[]  = $this->ipsclass->input['match'] == 'loose' ? "email.to_email_address LIKE '%{$string}%'" : "email.to_email_address='{$string}'";
					break;
				case 'name_from':
					$string = urldecode($this->ipsclass->input['string']);
					if ( $string == "" )
					{
						$this->ipsclass->admin->error("�� ������ ������ ���� ���-������");
					}

					if ( $this->ipsclass->input['match'] == 'loose' )
					{
						$this->ipsclass->DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name LIKE '%{$string}%'" ) );
						$this->ipsclass->DB->simple_exec();

						if ( ! $this->ipsclass->DB->get_num_rows() )
						{
							$this->ipsclass->admin->error("�� ������� ������� �������, ��������������� ������� � ������� e-mail �����������");
						}

						$ids = array();

						while ( $r = $this->ipsclass->DB->fetch_row() )
						{
							$ids[] = $r['id'];
						}

						$db_query[] = 'email.from_member_id IN('.implode( ',', $ids ).')';
					}
					else
					{
						$this->ipsclass->DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name='{$string}'" ) );
						$this->ipsclass->DB->simple_exec();

						if ( ! $this->ipsclass->DB->get_num_rows() )
						{
							$this->ipsclass->admin->error("�� ������� ������� �������, ��������������� ������� � ������� e-mail �����������");
						}

						$r = $this->ipsclass->DB->fetch_row();

						$db_query[] = 'email.from_member_id IN('.$r['id'].')';
					}

					$url_query[] = 'type='.$this->ipsclass->input['type'];
					$url_query[] = 'string='.urlencode($string);
					break;
				case 'name_to':
					$string = urldecode($this->ipsclass->input['string']);
					if ( $string == "" )
					{
						$this->ipsclass->admin->error("�� ������ ������ ���� ���-������");
					}

					if ( $this->ipsclass->input['match'] == 'loose' )
					{
						$this->ipsclass->DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name LIKE '%{$string}%'" ) );
						$this->ipsclass->DB->simple_exec();

						if ( ! $this->ipsclass->DB->get_num_rows() )
						{
							$this->ipsclass->admin->error("�� ������� ������� �������, ��������������� ������� � ������� e-mail �����������");
						}

						$ids = array();

						while ( $r = $this->ipsclass->DB->fetch_row() )
						{
							$ids[] = $r['id'];
						}

						$db_query[] = 'email.to_member_id IN('.implode( ',', $ids ).')';
					}
					else
					{
						$this->ipsclass->DB->simple_construct( array( 'select' => 'id,name', 'from' => 'members', 'where' => "name='{$string}'" ) );
						$this->ipsclass->DB->simple_exec();

						if ( ! $this->ipsclass->DB->get_num_rows() )
						{
							$this->ipsclass->admin->error("�� ������� ������� �������, ��������������� ������� � ������� e-mail �����������");
						}

						$r = $this->ipsclass->DB->fetch_row();

						$db_query[] = 'email.to_member_id IN('.$r['id'].')';
					}

					$url_query[] = 'type='.$this->ipsclass->input['type'];
					$url_query[] = 'string='.urlencode($string);
					break;
				default:
					//
					break;
			}
		}

		if( isset($this->ipsclass->input['match']) )
		{
			$url_query[] = 'match='.$this->ipsclass->input['match'];
		}

		//-----------------------------------------
		// LIST 'EM
		//-----------------------------------------

		$dbe = "";
		$url = "";

		if ( count($db_query) > 0 )
		{
			$dbe = implode(' AND ', $db_query );
		}

		if ( count($url_query) > 0 )
		{
			$url = '&'.implode( '&', $url_query);
		}

		$this->ipsclass->DB->simple_construct( array( 'select' => 'count(email.email_id) as cnt',
													  'from'   => 'email_logs email',
													  'where'  => $dbe ) );
		$this->ipsclass->DB->simple_exec();

		$count = $this->ipsclass->DB->fetch_row();

		$links = $this->ipsclass->adskin->build_pagelinks( array( 'TOTAL_POSS'  => $count['cnt'],
														  'PER_PAGE'    => 25,
														  'CUR_ST_VAL'  => $start,
														  'L_SINGLE'    => "���� ��������",
														  'L_MULTI'     => "��������: ",
														  'BASE_URL'    => $this->ipsclass->base_url."&{$this->ipsclass->form_code}".$url,
														)
												 );
		if ( $dbe )
		{
			$dbe = 'WHERE '.$dbe;
		}

		$this->ipsclass->DB->cache_add_query( 'emaillogs_list_current', array( 'dbe' => $dbe, 'limit_a' => $start ) );
		$this->ipsclass->DB->cache_exec_query();

		$this->ipsclass->html .= "<script type='text/javascript'>
									function checkall( )
									{
										var formobj = document.getElementById('theAdminForm');
										var checkboxes = formobj.getElementsByTagName('input');

										for ( var i = 0 ; i <= checkboxes.length ; i++ )
										{
											var e = checkboxes[i];
											var docheck = formobj.checkme.checked;

											if ( e && (e.type == 'checkbox') && (! e.disabled) && (e.id != 'checkme') && (e.name != 'type') )
											{
												if( docheck == false )
												{
													e.checked = false;
												}
												else
												{
													e.checked = true;
												}
											}
										}

										return false;
									}
								  </script>";

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'remove'     ),
																			 2 => array( 'act'   , 'emaillog'       ),
																			 3 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		$this->ipsclass->adskin->td_header[] = array( "<input type='checkbox' onclick='checkall();' id='checkme' />"         , "5%" );
		$this->ipsclass->adskin->td_header[] = array( "��"    , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "����"        , "30%" );
		$this->ipsclass->adskin->td_header[] = array( "����"      , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "����"      , "25%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������ ������������ �����" );

		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $row = $this->ipsclass->DB->fetch_row() )
			{

				$row['email_date'] = $this->ipsclass->admin->get_date( $row['email_date'], 'SHORT' );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
														  "<center><input type='checkbox' class='checkbox' name='id_{$row['email_id']}' value='1' /></center>",
														  "<a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=list&type=fromid&id={$row['id']}' title='�������� ��� ������������ ������'><img src='{$this->ipsclass->skin_acp_url}/images/acp_search.gif' border='0' alt='..by id'></a>&nbsp;<b><a href='{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}?showuser={$row['id']}' title='������� ������������ (��������� � ����� ����)' target='blank'>{$row['name']}</a></b>",
														  "<a href='javascript:pop_win(\"&{$this->ipsclass->form_code_js}&code=viewemail&id={$row['email_id']}\",\"Log\",400,400)' title='��������� ������'>{$row['email_subject']}</a>",
														  "<a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=list&type=toid&id={$row['to_id']}' title='�������� ��� ���������� ������'><img src='{$this->ipsclass->skin_acp_url}/images/acp_search.gif' border='0' alt='..by id'></a>&nbsp;<a href='{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}?showuser={$row['to_id']}'  title='������� ������������ (��������� � ����� ����)' target='blank'>{$row['to_name']}</a>",
														  "{$row['email_date']}",
												 )      );


			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<center>������� �� �������.</center>");
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic('<div style="float:left;width:auto"><input type="submit" value="������� ����������" class="realbutton" />&nbsp;<input type="checkbox" id="checkbox" name="type" value="all" />&nbsp;������� ���?</div><div align="right">'.$links.'</div></form>', 'left', 'tablesubheader');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();




		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'list'     ),
																			 2 => array( 'act'   , 'emaillog'       ),
																			 3 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "����� �������" );

		$form_array = array(
							  0 => array( 'subject'    , '���� ������'    ),
							  1 => array( 'content'    , '����������' ),
							  2 => array( 'email_from' , '�� (E-mail)' ),
							  3 => array( 'email_to'   , '���� (E-mail)'   ),
							  4 => array( 'name_from'  , '�� (��� ������������)'),
							  5 => array( 'name_to'    , '���� (��� ������������)' ),
						   );

		$type_array = array(
							  0 => array( 'exact'      , '����������' ),
							  1 => array( 'loose'      , '��������'   ),
						   );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>����� �</b> &nbsp;"
												  . $this->ipsclass->adskin->form_dropdown( "type", $form_array) ." "
												  . $this->ipsclass->adskin->form_dropdown( "match", $type_array) ." "
												  . $this->ipsclass->adskin->form_input( "string"),

								 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("������");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}



}


?>