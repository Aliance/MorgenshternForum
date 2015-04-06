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
|   > $Date: 2006-03-23 07:34:25 -0500 (Thu, 23 Mar 2006) $
|   > $Revision: 177 $
|   > $Author: brandon $
+---------------------------------------------------------------------------
|
|   > Support Module
|   > Module written by Brandon Farber
|   > Date started: 19th April 2006
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
    print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}


class ad_support
{
	var $base_url;

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_main = "help";

	/**
	* Section title name
	*
	* @var	string
	*/
	var $perm_child = "support";

	function auto_run()
	{
		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, '������ � ���������' );

		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'doctor':
				$this->ipsclass->admin->page_detail = "���������������� ����� �������������, �� ������� ������ ����� �������� � ���, ��� ������������ �� ��� ���� ������� � Invision Power Board.";
				$this->ipsclass->admin->page_title  = "������������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/docs-ipb' );
				break;
			break;

			case 'kb':
				$this->ipsclass->admin->page_detail = "�������������� ����� ����� ������ ��� ������ ������� ����� ������� � �������, ��������� �� ������� ��������� Invision Power Board. � ������ ���� �� ����� ������� ����� ��������, ��� ������������ ��������� ������� ������ ������������ �����������.";
				$this->ipsclass->admin->page_title  = "���� ������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/wiki' );
				break;

			case 'support':
				$this->ipsclass->admin->page_detail = "���� � ��� �������� �������� ��� ������� ��� ������������� ������������ ����������� �������� Invision Power Services, ��, ����������, �������������� ������ ����, ����� �������� ����������������� ������. �������, ��� ����� ������ � ������ ������� ������� �� ��������� ������ �������.<br /><br /><i>��� ���������� ����� �������� ��������, ����� ��������������� ������-�������.</i>";
				$this->ipsclass->admin->page_title  = "������ � ���������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/clientarea' );
				break;

			case 'ibresource':
				$this->ipsclass->admin->page_detail = "������ IBResource � ��� ������, ��� ����� ����� ����� �������� ������ � �������, ����������� � �������, ������ � �������������� �������, � ��� �� �������� ������ �� ������ ��������. �������, ��� ���������� �� ����� ������� ��������������� ���� ����� � �� ������������ ����������� ������� �������� �Invision Power Services, Inc.� � ��� ��������, ��������� ������� ��������������, �� ������ ������ �� ���� ����� � ����.
                                                        <br /><br />��������� ���� ������� ������ �� ����� ������� � ������ &laquo;�������&raquo; �� ������ ����� ������-�����.";
				$this->ipsclass->admin->page_title  = "������ � ���������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/forums' );
				break;

			case 'contact':
				$this->ipsclass->admin->page_detail = "���� �� ������ ��������� � ����, ����������, ������������ � ����� ���������� ����������� � ������ ������ ����.";
				$this->ipsclass->admin->page_title  = "������ � ���������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/contact' );
				break;

			case 'features':
				$this->ipsclass->admin->page_detail = "���� �� ������ ���������� ���, ��� �������� ���� ��������, �� ������ �������� ���� ����������� � ����� �� ������� ����.";
				$this->ipsclass->admin->page_title  = "����������� � ���������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/suggestfeatures' );
				break;

			case 'bugs':
				$this->ipsclass->admin->page_detail = "�� ������ ��������� ����� � ��������� ������, � ��� �� ������ �� ������������ ������ ������, ��������� �Bugtracker� ����.";
				$this->ipsclass->admin->page_title  = "������ � ���������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'http://external.iblink.ru/bugtrack' );
				break;


			//-----------------------------------------
			default:
				$this->ipsclass->admin->page_detail = "���� � ��� �������� �������� ��� ������� ��� ������������� ������������ ����������� �������� Invision Power Services, ��, ����������, �������������� ������ ����, ����� �������� ����������������� ������. �������, ��� ����� ������ � ������ ������� ������� �� ��������� ������ �������.<br /><br /><i>��� ���������� ����� �������� ��������, ����� ��������������� ������-�������.</i>";
				$this->ipsclass->admin->page_title  = "������ � ���������";

				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':view' );
				$this->ipsclass->admin->show_inframe( 'https://www.ibresource.ru/clientarea/index.php' );
				break;
		}
	}

}


?>