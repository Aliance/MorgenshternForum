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
|   > Admin Logs Stuff
|   > Module written by Matt Mecham
|   > Date started: 11nd September 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Mon 24th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>�������� ����</h1> � ��� ��� ������� � ����� �����. ���� ����������� ����������, ���������, ��� �� ������ �������� ���� admin.php.";
	exit();
}

class ad_adminlogs
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
	var $perm_child = "adminlog";

	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, '������ �����������������' );

		//-----------------------------------------
		// Make sure we're a root admin, or else!
		//-----------------------------------------

		if ($this->ipsclass->member['mgroup'] != $this->ipsclass->vars['admin_group'])
		{
			//$this->ipsclass->admin->error("Sorry, these functions are for the root admin group only");
		}

		$this->colours  = array(
								"cat"      => "green",
								"forum"    => "darkgreen",
								"mem"      => "red",
								'group'    => "purple",
								'mod'      => 'orange',
								'op'       => 'darkred',
								'help'     => 'darkorange',
								'modlog'   => 'steelblue',
				   			   );


		switch($this->ipsclass->input['code'])
		{
			case 'view':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->view();
				break;

			case 'remove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':remove' );
				$this->remove();
				break;

			//-----------------------------------------
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->list_current();
				break;
		}

	}

	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function view()
	{
		$start = intval($this->ipsclass->input['st']) >=0 ? intval($this->ipsclass->input['st']) : 0;

		$this->ipsclass->admin->page_detail = "�������� ���� ��������, ������������� ����������������";
		$this->ipsclass->admin->page_title  = "������ �����������������";

		if ( ( !isset($this->ipsclass->input['search_string']) OR !$this->ipsclass->input['search_string'] ) AND ( !isset($this->ipsclass->input['mid']) OR !$this->ipsclass->input['mid'] ) )
		{
			$this->ipsclass->main_msg = "�� �� ������� ��������, ������� ���������� �����.";
			$this->list_current();
			return;
		}

		if ( !isset($this->ipsclass->input['search_string']) OR !$this->ipsclass->input['search_string'] )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => 'COUNT(id) as count', 'from' => 'admin_logs', 'where' => "member_id=".intval($this->ipsclass->input['mid']) ) );
			$this->ipsclass->DB->simple_exec();

			$row = $this->ipsclass->DB->fetch_row();

			$row_count = $row['count'];

			$query = "&{$this->ipsclass->form_code}&mid={$this->ipsclass->input['mid']}&code=view";

			$this->ipsclass->DB->cache_add_query( 'adminlogs_view_one', array( 'mid' => intval($this->ipsclass->input['mid']), 'limit_a' => $start ) );
			$this->ipsclass->DB->cache_exec_query();

		}
		else
		{
			$this->ipsclass->input['search_string'] = urldecode($this->ipsclass->input['search_string']);

			$dbq = "m.".$this->ipsclass->input['search_type']." LIKE '%".$this->ipsclass->input['search_string']."%'";

			$row = $this->ipsclass->DB->build_and_exec_query( array( 'select' => 'COUNT(m.id) as count', 'from' => 'admin_logs m', 'where' => $dbq ) );

			$row_count = $row['count'];

			$query = "&act=adminlog&code=view&search_type={$this->ipsclass->input['search_type']}&search_string=".urlencode($this->ipsclass->input['search_string']);

			$this->ipsclass->DB->cache_add_query( 'adminlogs_view_two', array( 'dbq' => $dbq, 'limit_a' => $start ) );
			$this->ipsclass->DB->cache_exec_query();
		}

		$links = $this->ipsclass->adskin->build_pagelinks( array( 'TOTAL_POSS'  => $row_count,
														  'PER_PAGE'    => 20,
														  'CUR_ST_VAL'  => $start,
														  'L_SINGLE'    => "��������",
														  'L_MULTI'     => "��������: ",
														  'BASE_URL'    => $this->ipsclass->base_url.$query,
														)
												 );

		$this->ipsclass->admin->page_detail = "� ���� ������ �� ������ ������������� � ��������� �������� �����������������";
		$this->ipsclass->admin->page_title  = "������ �����������������";

		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "�������������"            , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "��������"        , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "�����"         , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "IP-�����"             , "20%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������ ��������" );
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic($links, 'center', 'tablesubheader');

		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $row = $this->ipsclass->DB->fetch_row() )
			{
				$row['ctime'] = $this->ipsclass->admin->get_date( $row['ctime'], 'LONG' );

				$this->colours[$row['act']] = isset($this->colours[$row['act']]) ? $this->colours[$row['act']] : 'black';

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>{$row['members_display_name']}</b>",
														  "<span style='color:{$this->colours[$row['act']]}'>{$row['note']}</span>",
														  "{$row['ctime']}",
														  "{$row['ip_address']}",
												 )      );


			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<center>��� �������</center>");
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic($links, 'center', 'tablesubheader');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}

	//-----------------------------------------
	// Remove archived files
	//-----------------------------------------

	function remove()
	{
		if ($this->ipsclass->input['mid'] == "")
		{
			$this->ipsclass->admin->error("�� �� ������� ID ��������������");
		}

		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'admin_logs', 'where' => "member_id=".intval($this->ipsclass->input['mid']) ) );

		$this->ipsclass->boink_it($this->ipsclass->base_url."&{$this->ipsclass->form_code}");
	}


	//-----------------------------------------
	// SHOW ALL LANGUAGE PACKS
	//-----------------------------------------

	function list_current()
	{
		$form_array = array();

		$this->ipsclass->admin->page_detail = "� ���� ������ �� ������ ������������� � ��������� �������� �����������������";
		$this->ipsclass->admin->page_title  = "������ �����������������";

		//-----------------------------------------
		// LAST FIVE ACTIONS
		//-----------------------------------------

		$this->ipsclass->DB->cache_add_query( 'adminlogs_view_list_current', array() );
		$this->ipsclass->DB->cache_exec_query();

		$this->ipsclass->adskin->td_header[] = array( "�������������"            , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "��������"        , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "�����"         , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "IP-�����"             , "20%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "5 ��������� ��������" );

		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $row = $this->ipsclass->DB->fetch_row() )
			{

				$row['ctime'] = $this->ipsclass->admin->get_date( $row['ctime'], 'LONG' );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>{$row['name']}</b>",
														  "<span style='color:{$this->colours[$row['act']]}'>{$row['note']}</span>",
														  "{$row['ctime']}",
														  "{$row['ip_address']}",
												 )      );


			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("<center>��� �������</center>");
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "�������������"            , "30%" );
		$this->ipsclass->adskin->td_header[] = array( "�������� ���������"       , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "�������� ���� ��������"     , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "�������� ���� ��������"   , "30%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������ �����������������" );

		$this->ipsclass->DB->cache_add_query( 'adminlogs_view_list_current_two', array() );
		$this->ipsclass->DB->cache_exec_query();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>{$r['name']}</b>",
													  "<center>{$r['act_count']}</center>",
													  "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=view&mid={$r['member_id']}'>����������</a></center>",
													  "<center><a href='".$this->ipsclass->base_url."&{$this->ipsclass->form_code}&code=remove&mid={$r['member_id']}'>�������</a></center>",
											 )      );
		}



		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();


		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'view'     ),
																			 2 => array( 'act'   , 'adminlog'       ),
																			 3 => array( 'section', $this->ipsclass->section_code ),
																	)      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"  , "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "����� �������� � �������" );

		$form_array = array(
							  0 => array( 'note'        , '��������' ),
							  1 => array( 'ip_address'  , 'IP-�����'  ),
							  2 => array( 'member_id'   , 'ID ��������������' ),
							  3 => array( 'act'         , '��������� ACT'  ),
							  4 => array( 'code'        , '��������� CODE'  ),
						   );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������ </b>" ,
										  		  $this->ipsclass->adskin->form_input( "search_string")
								 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<b>������ � </b>" ,
										  		  $this->ipsclass->adskin->form_dropdown( "search_type", $form_array)
								 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("������");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		//-------------------------------

		$this->ipsclass->admin->output();

	}



}


?>