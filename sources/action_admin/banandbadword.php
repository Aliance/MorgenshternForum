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
|   > $Date: 2006-11-03 16:54:54 -0600 (Fri, 03 Nov 2006) $
|   > $Revision: 709 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > Administration Module
|   > Module written by Matt Mecham
|   > Date started: 27th January 2004
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

class ad_banandbadword {

	var $functions = "";
	var $ipsclass;
	var $html;

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
	var $perm_child = "babw";

	function auto_run()
	{
		$this->html = $this->ipsclass->acp_load_template('cp_skin_bbcode_badword');

		//-----------------------------------------
		// Require and RUN !! THERES A BOMB
		//-----------------------------------------

		$this->ipsclass->admin->page_detail = "";
		$this->ipsclass->admin->page_title  = "������� ���������� (ban)";

		//-----------------------------------------
		// What to do...
		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			//-----------------------------------------
			// Badword
			//-----------------------------------------

			case 'badword':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
				$this->badword_start();
				break;

			case 'badword_add':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':bw-add' );
				$this->badword_add();
				break;

			case 'badword_remove':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':bw-remove' );
				$this->badword_remove();
				break;

			case 'badword_edit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':bw-edit' );
				$this->badword_edit();
				break;

			case 'badword_doedit':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':bw-edit' );
				$this->badword_doedit();
				break;

			case 'badword_export':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':bw-export' );
				$this->badword_export();
				break;

			case 'badword_import':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':bw-import' );
				$this->badword_import();
				break;

			//-----------------------------------------
			// BAN (d-aid)
			//-----------------------------------------

			case 'ban':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':ban-view' );
				$this->ban_start();
				break;
			case 'ban_add':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':ban-add' );
				$this->ban_add();
				break;
			case 'ban_delete':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':ban-remove' );
				$this->ban_delete();
				break;
		}
	}

 	//-----------------------------------------
	// BAN: Rebuild cache
	//-----------------------------------------

	function ban_rebuildcache()
	{
		require_once ROOT_PATH.'sources/classes/bbcode/class_bbcode_core.php';

		$this->ipsclass->cache['banfilters'] = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => 'ban_content', 'from' => 'banfilters', 'where' => "ban_type='ip'" ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['banfilters'][] = $r['ban_content'];
		}

		usort( $this->ipsclass->cache['banfilters'] , array( 'class_bbcode_core', 'word_length_sort' ) );

		$this->ipsclass->update_cache( array( 'name' => 'banfilters', 'array' => 1, 'deletefirst' => 1 ) );
	}

	//-----------------------------------------
	// BAN: DELETE
	//-----------------------------------------

	function ban_delete()
	{
		$ids = array();

		foreach ($this->ipsclass->input as $key => $value)
		{
			if ( preg_match( "/^id_(\d+)$/", $key, $match ) )
			{
				if ( $this->ipsclass->input[$match[0]] )
				{
					$ids[] = $match[1];
				}
			}
		}

		$ids = $this->ipsclass->clean_int_array( $ids );

		if ( count( $ids ) )
		{
			$this->ipsclass->DB->simple_construct( array( 'delete' => 'banfilters', 'where' => 'ban_id IN('.implode( ",",$ids ).')' ) );
			$this->ipsclass->DB->simple_exec();
		}

		$this->ban_rebuildcache();

		$this->ipsclass->main_msg = "������ �� ���������� ������� ������";
		$this->ban_start();
	}

	//-----------------------------------------
	// BAN: ADD
	//-----------------------------------------

	function ban_add()
	{
		if ( ! $this->ipsclass->input['bantext'] )
		{
			$this->ipsclass->main_msg = "�� ������ ������ ���������� �����!";
			$this->ban_start();
		}

		//-----------------------------------------
		// Check for existing entry
		//-----------------------------------------

		$result = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'banfilters', 'where' => "ban_content='{$this->ipsclass->input['bantext']}' and ban_type='{$this->ipsclass->input['bantype']}'" ) );

		if ( $result['ban_id'] )
		{
			$this->ipsclass->main_msg = "����� �������� ��� ����.";
			$this->ban_start();
		}

		$this->ipsclass->DB->do_insert( 'banfilters', array( 'ban_type' => $this->ipsclass->input['bantype'], 'ban_content' => trim($this->ipsclass->input['bantext']), 'ban_date' => time() ) );

		$this->ban_rebuildcache();

		$this->ipsclass->main_msg = "����� ������ ���������� ������� ��������.";

		$this->ban_start();

	}

	//-----------------------------------------
	// BAN: START
	//-----------------------------------------

	function ban_start()
	{
		$this->ipsclass->admin->page_title = "���������� ��������� ����������";
		$this->ipsclass->admin->nav[] 		= array( $this->ipsclass->form_code.'&code=ban', 'Ban Filters' );

		$this->ipsclass->admin->page_detail = "��� ������ ��������� ��� ��������, ������� � ��������� ip-������, e-mail � ����������������� ����� � ������ ��� ����������.
										 <br /><strong>�� ������ ������������ ������� ��������� � * , ��� ������(��) �������(��) ip-������ ��� e-mail. (��������: 127.0.*, *@mail.ru)</strong>";

		//-----------------------------------------
		// Get things
		//-----------------------------------------

		$ban = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'banfilters', 'order' => 'ban_date desc' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$ban[ $r['ban_type'] ][ $r['ban_id'] ] = $r;
		}

		//-----------------------------------------
		// SHOW THEM!
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'ban_delete'),
												 				 2 => array( 'act'   , 'babw'  ),
												 				 4 => array( 'section', $this->ipsclass->section_code ),
									     			    )      );

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"   , "1%" );
		$this->ipsclass->adskin->td_header[] = array( "������"    , "80%" );
		$this->ipsclass->adskin->td_header[] = array( "���� ����������"     , "20%" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "���������� ��������� ����������" );

		//-----------------------------------------
		// Banned IP Addresses
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("��������������� ip-������", "left", "tablesubheader");

		if ( isset($ban['ip']) AND  is_array( $ban['ip'] ) AND count( $ban['ip'] ) )
		{
			foreach ( $ban['ip'] as $entry )
			{
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<input type='checkbox' name='id_{$entry['ban_id']}' value='1' />",
																		 $entry['ban_content'],
																	 	 $this->ipsclass->get_date( $entry['ban_date'], 'SHORT' ),
																)      );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("��������������� IP-������� ���", "left", "tablerow1");
		}

		//-----------------------------------------
		// Banned Email Addresses
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("��������������� e-mail ������", "left", "tablesubheader");

		if ( isset($ban['email']) AND  is_array( $ban['email'] ) AND count( $ban['email'] ) )
		{
			foreach ( $ban['email'] as $entry )
			{
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<input type='checkbox' name='id_{$entry['ban_id']}' value='1' />",
																		 $entry['ban_content'],
																	 	 $this->ipsclass->get_date( $entry['ban_date'], 'SHORT' ),
																)      );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("��������������� e-mail ������� ���", "left", "tablerow1");
		}

		//-----------------------------------------
		// Banned Names
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("����������� � ����������� ����� �������������", "left", "tablesubheader");

		if ( isset($ban['name']) AND is_array( $ban['name'] ) AND count( $ban['name'] ) )
		{
			foreach ( $ban['name'] as $entry )
			{
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<input type='checkbox' name='id_{$entry['ban_id']}' value='1' />",
																		 $entry['ban_content'],
																	 	 $this->ipsclass->get_date( $entry['ban_date'], 'SHORT' ),
																)      );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic("����������� � ����������� ���� ������������� ���", "left", "tablerow1");
		}

		$end_it_now = "<div align='left' style='float:left;width:auto;'>
		 			   <input type='submit' value='������� ���������' class='realdarkbutton' />
					   </div></form>
					   <div align='center'><form method='post' action='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=ban_add'><input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' /><input type='text' size='30' class='textinput' value='' name='bantext' />
					   <select class='dropdown' name='bantype'><option value='ip'>IP �����</option><option value='email'>E-mail �����</option><option value='name'>���</option></select>
					   <input type='submit' value='�������� ����� ������' class='realdarkbutton' /></form></div>";

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_basic( $end_it_now, "center", "tablesubheader");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();


	}

	//-----------------------------------------
	// BADWORDS: Import
	//-----------------------------------------

	function badword_import()
	{
		$content = $this->ipsclass->admin->import_xml( 'ipb_badwords.xml' );

		//-----------------------------------------
		// Got anything?
		//-----------------------------------------

		if ( ! $content )
		{
			$this->ipsclass->main_msg = "�������������� ������� �� ���������, ��� ��� ���� �ipb_badwords.xml� �������� ������ ���� �� ����";
			$this->badword_start();
			return;
		}

		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Unpack the datafile
		//-----------------------------------------

		$xml->lite_parser = 1;
		$xml->xml_parse_document( $content );

		if( !is_array($xml->xml_array['badwordexport']['badwordgroup']['badword']) OR !count($xml->xml_array['badwordexport']['badwordgroup']['badword']) )
		{
			$this->ipsclass->main_msg = "�������������� ������� �� ���������, ��� ��� ���� �ipb_badwords.xml� �������� ������ ���� �� ����";
			$this->badword_start();
			return;
		}

		//-----------------------------------------
		// Get current badwords
		//-----------------------------------------

		$words = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'badwords', 'order' => 'type' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$words[ $r['type'] ] = 1;
		}

		//-----------------------------------------
		// pArse
		//-----------------------------------------

		foreach( $xml->xml_array['badwordexport']['badwordgroup']['badword'] as $entry )
		{
			$type    = $entry['type']['VALUE'];
			$swop    = $entry['swop']['VALUE'];
			$m_exact = $entry['m_exact']['VALUE'];

			if ( $words[ $type ] )
			{
				continue;
			}

			if ( $type )
			{
				$this->ipsclass->DB->do_insert( 'badwords', array( 'type' => $type, 'swop' => $swop, 'm_exact' => $m_exact ) );
			}
		}

		$this->badword_rebuildcache();

		$this->ipsclass->main_msg = "�������������� ������� ������� ���������";

		$this->badword_start();

	}

	//-----------------------------------------
	// BADWORD: Export
	//-----------------------------------------

	function badword_export()
	{
		//-----------------------------------------
		// Get xml mah-do-dah
		//-----------------------------------------

		require_once( KERNEL_PATH.'class_xml.php' );

		$xml = new class_xml();

		//-----------------------------------------
		// Start...
		//-----------------------------------------

		$xml->xml_set_root( 'badwordexport', array( 'exported' => time() ) );

		//-----------------------------------------
		// Get emo group
		//-----------------------------------------

		$xml->xml_add_group( 'badwordgroup' );

		$this->ipsclass->DB->simple_construct( array( 'select' => 'type, swop, m_exact', 'from' => 'badwords', 'order' => 'type' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$content = array();

			foreach ( $r as $k => $v )
			{
				$content[] = $xml->xml_build_simple_tag( $k, $v );
			}

			$entry[] = $xml->xml_build_entry( 'badword', $content );
		}

		$xml->xml_add_entry_to_group( 'badwordgroup', $entry );

		$xml->xml_format_document();

		//-----------------------------------------
		// Send to browser.
		//-----------------------------------------

		$this->ipsclass->admin->show_download( $xml->xml_document, 'ipb_badwords.xml' );
	}

	//-----------------------------------------
	// BADWORD: Start
	//-----------------------------------------

	function badword_start()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$badword_html = "";

		$this->ipsclass->admin->page_detail = "� ���� ������ �� ������ ���������, �������� � ������� ������� ����������� ����.<br />������ ����������� ���� ��������� ��� �������� ����������� ����� � ���������� �������������, � �������� � � ��������� ���.<br /><br /><b>���������� �� �����</b>: ��� ������ ������� ������, ��������� ���� ����� ����� ���������� ���� � ������, ���������� � ���� ��� �����. ��������, ���� �� ������� � ������ ����� �����, �� ��� ����� ����� �������� �� ��������� ���� ���������� � � ����� ���������. ���� �� �� ������� ����������, �� ����������� ����� ����� �������� �� ����� �������� �������� � #<br /><br /><b>������ ����������:</b> ��� ������ ������� �������, ��������� ���� ����� ����� ���������� �� ���������� ������ ��� ������ ���������� ����� �����. ��������, ���� �� ������� � ������ ����� �����, �� ��� ����� ����� �������� �� ��������� ���� ����������, ������ � ����� �����. ���� �� �� ������� ����������, �� ����������� ����� ����� �������� �� ����� �������� �������� � #.<br /><br />������ ����� �� ������������� � ��������.";
		$this->ipsclass->admin->page_title  = "������ ����������� ����";
		$this->ipsclass->admin->nav[] 		= array( $this->ipsclass->form_code.'&code=badword', '������ ����������� ����' );

		//-----------------------------------------
		// Start form
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'badword_add' ),
												  			     		  	 2 => array( 'act'   , 'babw'       ),
															  			     4 => array( 'section', $this->ipsclass->section_code ),
												     			  )      );

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'badwords', 'order' => 'type' ) );
		$this->ipsclass->DB->simple_exec();

		if ( $this->ipsclass->DB->get_num_rows() )
		{
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$words[] = $r;
			}

			foreach($words as $r)
			{
				$r['replace'] = $r['swop']    ? stripslashes($r['swop']) : '######';
				$r['method']  = $r['m_exact'] ? '������' : '�����';
				$r['type'] 	= stripslashes($r['type']);

				$badword_html .= $this->html->badword_row( $r );
			}

		}

		$this->ipsclass->html .= $this->html->badword_wrapper( $badword_html );

		//-----------------------------------------
		// IMPORT: Start table
		//-----------------------------------------

		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "60%" );
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;" , "40%" );

		//-----------------------------------------
		// IMPORT: Start output
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'badword_import' ),
															   2 => array( 'act'   , 'babw'      ),
															   3 => array( 'MAX_FILE_SIZE', '10000000000' ),
															   4 => array( 'section', $this->ipsclass->section_code ),
													  ) , "uploadform", " enctype='multipart/form-data'"     );


		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������������� ������ � ��������� ����������� ����" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array(
													 		 "<b>�������� XML-����� � ���������</b><div style='color:gray'>�������� ���� �ipb_badwords.xml� ��� �ipb_badwords.xml.gz� �� ����� ����������. ������ ��� ������������ � ���� �� ����� �������������.</div>",
													  		$this->ipsclass->adskin->form_upload(  )
													   )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form("�������������");

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
	}

	//-----------------------------------------
	// BADWORD: Complete Edit
	//-----------------------------------------

	function badword_doedit()
	{
		if ($this->ipsclass->input['before'] == "")
		{
			$this->ipsclass->admin->error("�� �� ������� ���������� �����.");
		}

		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("�� �� ������� ID �������.");
		}

		$this->ipsclass->input['match'] = $this->ipsclass->input['match'] ? 1 : 0;

		strlen($this->ipsclass->input['swop']) > 1 ?  $this->ipsclass->input['swop'] : "";

		$this->ipsclass->DB->do_update( 'badwords', array( 'type'    => trim($this->ipsclass->input['before']),
										   'swop'    => trim($this->ipsclass->input['after']),
										   'm_exact' => $this->ipsclass->input['match'],
								  ), "wid='".$this->ipsclass->input['id']."'"  );

		$this->badword_rebuildcache();

		$this->ipsclass->main_msg = "������ �������";

		$this->badword_start();
	}

	//-----------------------------------------
	// BADWORD:  Edit
	//-----------------------------------------

	function badword_edit()
	{
		$this->ipsclass->admin->page_detail = "�� ������ �������� ��������� ������ � ����� ����";
		$this->ipsclass->admin->page_title  = "������ ����������� ����";
		$this->ipsclass->admin->nav[] 		= array( $this->ipsclass->form_code.'&code=badword', '������ ����������� ����' );
		$this->ipsclass->admin->nav[] 		= array( '', '��������� ������� ����������� ����' );

		//-----------------------------------------

		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("�� �� ������� ID �������.");
		}

		//-----------------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'badwords', 'where' => "wid='".$this->ipsclass->input['id']."'" ) );
		$this->ipsclass->DB->simple_exec();

		if ( ! $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->admin->error("��������� ���� ������ �� ������ � ���� ������.");
		}

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'badword_doedit' ),
												 			     2 => array( 'act'   , 'babw'     ),
												  			     3 => array( 'id'    , $this->ipsclass->input['id'] ),
												  			     4 => array( 'section', $this->ipsclass->section_code ),
									                    )      );



		$this->ipsclass->adskin->td_header[] = array( "�����"  , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "������"   , "40%" );
		$this->ipsclass->adskin->td_header[] = array( "�����"  , "20%" );

		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������������� �������" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $this->ipsclass->adskin->form_input('before', stripslashes($r['type']) ),
												  			     $this->ipsclass->adskin->form_input('after' , stripslashes($r['swop']) ),
												  			     $this->ipsclass->adskin->form_dropdown( 'match', array( 0 => array( 1, '������'  ), 1 => array( 0, '�����' ) ), $r['m_exact'] )
													    )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form('��������');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();

	}

	//-----------------------------------------
	// BADWORD: Remove badowrd
	//-----------------------------------------

	function badword_remove()
	{
		if ($this->ipsclass->input['id'] == "")
		{
			$this->ipsclass->admin->error("�� �� ������� ID �������.");
		}

		$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'badwords', 'where' => "wid='".$this->ipsclass->input['id']."'" ) );

		$this->badword_rebuildcache();

		$this->ipsclass->main_msg = "������ ������.";

		$this->badword_start();
		return;
	}

	//-----------------------------------------
	// BADWORD: Add badword
	//-----------------------------------------

	function badword_add()
	{
		if ($this->ipsclass->input['before'] == "")
		{
			$this->ipsclass->admin->error("�� �� ������� ����� ��� ������.");
		}

		$this->ipsclass->input['match'] = $this->ipsclass->input['match'] ? 1 : 0;

		strlen($this->ipsclass->input['swop']) > 1 ?  $this->ipsclass->input['swop'] : "";

		$this->ipsclass->DB->do_insert( 'badwords', array( 'type'    => trim($this->ipsclass->input['before']),
														   'swop'    => trim($this->ipsclass->input['after']),
														   'm_exact' => $this->ipsclass->input['match'],
												  )      );

		$this->badword_rebuildcache();

		$this->ipsclass->main_msg = "������ ������� ��������.";

		$this->badword_start();
	}

	//-----------------------------------------
	// BADWORD Rebuild Cache
	//-----------------------------------------

	function badword_rebuildcache()
	{
		$this->ipsclass->cache['badwords'] = array();

		$this->ipsclass->DB->simple_construct( array( 'select' => 'type,swop,m_exact', 'from' => 'badwords' ) );
		$this->ipsclass->DB->simple_exec();

		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['badwords'][] = $r;
		}

		$this->ipsclass->update_cache( array( 'name' => 'badwords', 'array' => 1, 'deletefirst' => 1 ) );
	}


	function perly_length_sort($a, $b)
	{
		if ( strlen($a['typed']) == strlen($b['typed']) )
		{
			return 0;
		}
		return ( strlen($a['typed']) > strlen($b['typed']) ) ? -1 : 1;
	}

	function perly_word_sort($a, $b)
	{
		if ( strlen($a['type']) == strlen($b['type']) )
		{
			return 0;
		}
		return ( strlen($a['type']) > strlen($b['type']) ) ? -1 : 1;
	}






}


?>