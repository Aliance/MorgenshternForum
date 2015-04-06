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
|   > Admin Quick Help System
|   > Module written by Matt Mecham
|   > Date started: 1st march 2002
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


class ad_quickhelp {

	var $help_text = array();
	
	function init_help_array()
	{
	
		return array(	'mg_dohtml' => array( 'title' => "������������� [doHTML] �����",
											  'body'  => "��� ������������� [doHTML], ��� ������������ ������ ������������ ������ HTML, � ����������. �� ������ �������� ��� ��������� ��� ������� ��� ������� ������ � ������ ���������� ��������.<br />
											  			  <br /><b>��������������!</b><br />
											  			  ���������� ������������� ������������  HTML, �������� ������������ ��������, ������� ����� ��������� � ������������� ���� �������, ������ ���������� ��� ������ �������������, ������� �� ����� ������������ ��� � ��������� �����. ���� IPB � ��������� ��������� ������������� �������� � ����������,
											  			  �� �� �����, ������� ������������ ������, ������������ ����� ������, �������� ������ cookies, ����������� ��������� � ���������� �� ������ �����, ������� ������ ����������� ��� � �.�.
											  			  <br />Invision Power Board � Invision Power Services �� ����� ������� ��������������� �� ����� ���������, ������������� ��-�� ������������� ����� �����.
											  			  <br /><br /><b>����������� ��� � ����!</b>
														 ",
											 ),
											 
						'mod_mmod' =>  array( 'title' => "��������� � ������-��������� ���",
											  'body'  => "������ �����, ��� ��� ������� ����������� � ������-��������� ���, ��� ������ ��������� ��������������� ���� ��������.
											  			  <br />��������, ���� ���� �� ����� ������ ������-���������, ��������� �� ����������� ���, �� �� ��������� � ���������� ������ �� ����������� ���������� ����,
											  			  ������ ��������� �� ����� ������ ���������� ���� ��� ������ ������� ������-���������.
														 ",
											 ),
											 
						'set_spider' => array( 'title' => "��� ����� ��������� ������?",
											  'body'  => "��������� ������, ����� ��� Google, ���������� ������ ���������, ��� ������ � ���������� ������ � ���� ���� ������.<br />Invision Power ����� ������������ �����, ��� ������ �������������, �������� ����������� ������������� ���������� ��� ������������� ������.
											  			  <br />
											  			  <br />
											  			  <b>��������������!</b>
											  			  <br />
											  			  Invision Power Board ��������� ������ ��������� ������ � ���������� �� ������ user_agent. ��, � ����� ������ ������ �����,
											  			  ��� �������� � �������������, � ��������� ������������ ������ ����������� ���� � ���� ���������� ��������� �����.
											  			  <br />��� �� ����������� ��� ������� ��������, �.�. ��� ����� ����� ������ �����, �� � ����� ������, ������ ���������.
											  			  <br /><br />����� ������ �����, ��� ������ ��������� ������, �� �����������
											  			  ����������� ��������������� ��������, �� ��������� ���������� �������, ��� ��������� ������ ���.
														 ",
											 ),
		
		
		
						'mg_upload' => array( 'title' => "���������� ��������",
											  'body'  => "���� �� ������ ��������� ���� ������ ������������ (���������) ����� � ����������, �� ������ ��������� ��������� ������:
											  			  <ul>
											  			  <li>������� �������� �������� �������� � ���� '������������ ������ ����������� ������.
											  			  <li>��������� ���������� ������ ��� ���� ������ � �������� ���� '��������'.
											  			  </ul>
											  			  �� ������ �������, � ����� ������� ��� ������ ������������� ����� ��������� �����.
														  <br /><br /><b>��������������!</b><br />���� ��� �������� ���������, ��������� �� ����� ���������� � ��� ����� ���������� �� ������� �������� ������, ��������� ����������� �������� ��� ���� ������, ����� 0 � ��� ����. ���� ����� �� ��������� ����������� ��������.
														 ",
											 ),
		
		
						'mg_promote' => array( 'title' => "����������� �� �������",
											   'body'  => "��� ��������� ���� �������, (���������� ������� ������ � ���-�� ���������), 
											    		   ������������ ���� ������, ����� ���������� � �����-���� ������ ������, ��� ������ ������������ ���-�� ���������.
											    		   <br /><br />
											    		   ������ �������������� ���������� ��� ������� � ����� ��������� �������� ������������� ������, ��� ����������� ���� ������������� � ����� ����������� ������ �������������, ������� �������� ����������� ������� ������, ����������� ������������� ������� ������ (�������� ��� ���������� ������������� ���� ������ ��������� � ��������� �����, ��������� ����������� ������� � �.�.), � � ��������� �������
											    		   � ��� ������� ���� ������ � ��������� �������. ����� ������� ����� ������� ������� ������ ������������� � ���������� ��� ��� ��������� ����������� � ���
											    		   ������ ������������ ���������� ���������, ���������� ���� ������������� � ������� ������, � ������� ��� ������� �����������.
											    		 <br /><br /><b>��������������!</b><br />����� ���������� ���� ��������, ��������� ��������� �������� ���� ������.<br />�� ������ �� ������ �������� ���������� ��������������� � ����������������� ������, ��� �����������.
											   			  ",
											 ),
						's_reg_antispam' => array ( 'title' => "�������� ��� �����������",
													'body'  => "��� �������������� �������������� ����������� �����������-������ (������� ����� ������� �� ����� ��������� ����� �������� ����������� � ��������� ����� ������������ � ������ ������� �����),
													            �� ������ �������� ��� �������.
													            <br /><br />���� �������� ��� �������, ��  ����� �������������� ��������� 6-������� ����� � ������������ � ���� ������� (��� �������� ������������ ��� ��������-�����, ������� �� ������ �������� ��� �����). ������������, ��� �����������
													            ������ ���� ������ ��� ����� � ��������, ����� �� ����������� ����� ���������.",
											 ),
											 
						'm_bulkemail'    => array ( 'title' => "�������� Email ��������",
												    'body' => "<b>�����</b><br />� ������� �������� e-mail ��������, �� ������� ���������� ������������� ����������� ����� ��� ���� ����� ������������� � ��������� ������������ � �������� ������.
												    <br /><br /><b>���������</b><br />�� ������ ��������� ������ ����������� ������ ������������� ��� ������ �������������, ����������� �� '����������� �� ��������������'. �� ������������� ������������ ��� ���
												    �����.<hr>
												    <b>��������� ����</b><br />� ������ ������ �� ������ ������������ ��������� ����:
												    <br />{board_name} ����� ������������������ � �������� ������ ������
													<br />{reg_total} ��������� ���-�� ������������������ �������������
													<br />{total_posts} ��������� ����� ���-�� ��������� ������
													<br />{busy_count} ��������� ������ ������������ ������ � ���-��� �����������
													<br />{busy_time} ��������� ���� ������� ������������ ������
													<br />{board_url} ��������� ������ �� ��� �����
													<br /><br />��� ������������, ������ � ������ ������ ��������, ������������ �� ������������.",
												),
						'comp_menu' => array ( 'title' => "���� ������� �����������",
											   'body'  => "<strong>����� ����</strong> �������� ����� ����. <em>������: ������� � ���������.</em><br />
   														   <strong>URL ����</strong> &laquo;��������&raquo; ����� URL. ��� ����� ������� ������ ���� URL. <em>������: code={code}</em><br />
														   <strong>�������������?</strong> ���� ��, �� ����� 'act={}&section={}&code={}' ���� � ���� URL ��� ������������� ����� �� ��������.<br />
														   <strong>�������� � ����� ����������</strong> � ������� ����������� ������� � ���������. �������: add, remove, edit, rebuild, recount, recache - ��� �����
														   �������� ����������. ����� ������ ���������� ��� ������� ����������� ������� ������� ���������� �� � ���� &laquo;�������� ����������&raquo;. ������: ���� �������� - 'tools', � ���� &laquo;����� ����������&raquo; �� ������ ������ - '��������� ������ � ��������?'.<br />",
											 ),
					);
	
	}

	function auto_run()
	{
		$id = $this->ipsclass->input['id'];
		
		$this->help_text = $this->init_help_array();
		
		if ($this->help_text[$id]['title'] == "")
		{
			$this->ipsclass->admin->error("��� ���� ������� ��� ��������� ���������� ������");
		}
		
		print "<html>
				<head>
				 <title>������� ������</title>
				</head>
				<body leftmargin='0' topmargin='0' bgcolor='#F5F9FD'>
				 <table width='95%' align='center' border='0' cellpadding='6'>
				 <tr>
				  <td style='font-family:verdana, arial, tahoma;color:#4C77B6;font-size:16px;letter-spacing:-1px;font-weight:bold'>{$this->help_text[$id]['title']}</td>
				 </tr>
				 <tr>
				  <td style='font-family:verdana, arial, tahoma;color:black;font-size:9pt'>{$this->help_text[$id]['body']}</td>
				 </tr>
				 </table>
				</body>
				</html>";
		
		
		exit();
		
	}
	
	
	
}


?>