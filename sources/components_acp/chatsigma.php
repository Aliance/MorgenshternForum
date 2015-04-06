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
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Admin Framework for IPS Services
|   > Module written by Matt Mecham
|   > Date started: 17 February 2003
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}

class ad_chatsigma
{
	var $ipsclass;
	var $base_url;
	
	/*-------------------------------------------------------------------------*/
	// IPB CONSTRUCTOR
	/*-------------------------------------------------------------------------*/
	
	function auto_run()
	{
		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------
		
		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );
		
		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}
		
		//-----------------------------------------
		// Make sure we're a root admin, or else!
		//-----------------------------------------
		
		switch($this->ipsclass->input['code'])
		{
			case 'ipchat04':
				$this->chat_splash();
				break;
			case 'chatsettings':
				$this->chat04_config();
				break;
			case 'chatsave':
				$this->chat_save();
				break;
			case 'dochat':
				$this->chat_config_save();
				break;
			default:
				$this->chat_splash();
				break;
		}
	}
		
	
	/*-------------------------------------------------------------------------*/
	// CHAT SPLASH
	/*-------------------------------------------------------------------------*/
	
	function chat_splash()
	{
		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------
		
		if ( $this->ipsclass->vars['chat_account_no'] )
		{
			$this->chat04_config();
		}
		else
		{
			$this->ipsclass->admin->page_title  = "AddOnChat";
			$this->ipsclass->admin->page_detail = "���� �� ��� ������ AddOnChat, ������ ������� ��� Customer Account ID � ���� ����.";
			
			$this->ipsclass->html .= "<form action='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=chatsave' method='POST'>
									  <table style='background:#005' width='100%' cellpadding=4 cellspacing=0 border=0 align='center'>
									  <tr>
									   <td valign='middle' align='left'><b style='color:white'>�������� AddOnChat?</b></td>
									   <td valign='middle' align='left'><input type='text' size=35 name='account_no' value='������� ��� Customer Account ID �����...' onClick=\"this.value='';\"></td>
									   <td valign='middle' align='left'><input type='submit' class='realdarkbutton' value='����������...'></td>
									  </tr>
									  </table>
									  </form>";
									  
			$this->ipsclass->admin->show_inframe( 'http://external.ipslink.com/ipboard22/landing/?p=addonchat' );
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// CHAT SAVE
	/*-------------------------------------------------------------------------*/
	
	function chat_save()
	{
		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$adsettings           =  new ad_settings();
		$adsettings->ipsclass =& $this->ipsclass;
		
		$acc_number = $this->ipsclass->input['account_no'];
		
		if ( $acc_number == "" )
		{
			$this->ipsclass->admin->error("��������, �� ��������� ���� ����� ������������");
		}
		
		$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='chat_account_no'" );
		
		$adsettings->setting_rebuildcache();
		
		//-----------------------------------------
		// Update this component
		//-----------------------------------------
		
		require_once( ROOT_PATH . 'sources/api/api_core.php' );
		require_once( ROOT_PATH . 'sources/api/api_components.php' );
		
		$api           =  new api_components();
		$api->ipsclass =& $this->ipsclass;
		
		$fields = array( 'com_enabled'    => 1,
						 'com_menu_data'  => array( 0 => array( 'menu_text'    => '��������� ����',
						 										'menu_url'     => 'code=chatsettings',
						 										'menu_permbit' => 'edit' ) ) );
		
		$api->acp_component_update( 'chatsigma', $fields );
		
		//-----------------------------------------
		// Show config
		//-----------------------------------------
		
		$this->chat04_config();
	}
	
	/*-------------------------------------------------------------------------*/
	// NEW CHAT
	/*-------------------------------------------------------------------------*/
	
	function chat04_config()
	{
		$this->ipsclass->admin->page_detail = "�� ������ �������� ��������� ����.";
		$this->ipsclass->admin->page_title  = "��������� AddOnChat";
		
		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$settings           =  new ad_settings();
		$settings->ipsclass =& $this->ipsclass;
		
		$settings->get_by_key        = 'chat';
		$settings->return_after_save = 'section=components&act=chatsigma&code=show';
		
		$settings->setting_view();
	}

}

?>