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
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|
|   > CP "MYCP" PAGE CLASS
|   > Script written by Matt Mecham
|   > Date started: Wed. 18th August 2004
|
+---------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}


class acp_tools
{
	# Globals
	var $ipsclass;
	
	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/
	
	function acp_tools()
	{
	}
	
	/*-------------------------------------------------------------------------*/
	// AUTO RUN
	/*-------------------------------------------------------------------------*/
	
	function auto_run()
	{
		$this->ipsclass->html_title = "IPB: ������� � ���������";
		
		$another_choice = array(
								 
								 'op'        => 'settings',
								 'pin'       => 'plugins',
								 'cache'     => 'cache',
								 'rebuild'   => 'rebuild',
								 'help'      => 'help',
								 'ips'       => 'ips',
								 'task'      => 'task_manager',
								 'admin'     => 'administration',
								 'postoffice'=> 'postoffice',
								 'loginauth' => 'login_auth',
								 'portal'    => 'portal',
							   );
							   
		if ( ! isset( $another_choice[ $this->ipsclass->input['act'] ] ) )
		{
			 $this->ipsclass->input['act'] = 'op';
		}
		
		$this->ipsclass->form_code    = 'section=tools&amp;act=' . $this->ipsclass->input['act'];
		$this->ipsclass->form_code_js = 'section=tools&act='     . $this->ipsclass->input['act'];
		$this->ipsclass->section_code = 'tools';
		
		//-----------------------------------------
		// Quick perm check
		//-----------------------------------------
		
		$this->ipsclass->admin->cp_permission_check( $this->ipsclass->section_code.'|' );
		
		//-----------------------------------------
		// Require and run (again)
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/action_admin/'.$another_choice[ $this->ipsclass->input['act'] ].'.php' );
		$constructor          = 'ad_'.$another_choice[ $this->ipsclass->input['act'] ];
		$runmeagain           = new $constructor;
		$runmeagain->ipsclass =& $this->ipsclass;
		$runmeagain->auto_run();
	}
	
	
	
	
	
}


?>