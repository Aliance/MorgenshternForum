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
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-12-05 09:12:45 -0600 (Tue, 05 Dec 2006) $
|   > $Revision: 765 $
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
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class ad_copyright
{
	var $ipsclass;
	var $base_url;

	function auto_run()
	{
		if ( TRIAL_VERSION )
		{
			print "Эта функция отключена в триал-версии.";
			exit();
		}
		
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
		
		if ($this->ipsclass->member['mgroup'] != $this->ipsclass->vars['admin_group'])
		{
			$this->ipsclass->admin->error("Извините, эти функции доступны только главным администраторам");
		}

		switch($this->ipsclass->input['code'])
		{
			case 'show':
			case 'copy':
				$this->copy_splash();
				break;	
			case 'copysave':
				$this->copy_save();
				break;
			case 'docopy':
				$this->copy_config_save();
				break;
				
			default:
				exit();
				break;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Copyright removal Splash
	/*-------------------------------------------------------------------------*/
	
	function copy_splash()
	{
		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------
		
		if ( $this->ipsclass->vars['ipb_copy_number'] )
		{
			$this->copy_config();
		}
		else
		{
			$this->ipsclass->admin->page_title  = "Удаление упоминаний об авторских правах в Invision Power Board";
			$this->ipsclass->admin->page_detail = "";
			
			$this->ipsclass->html .= "<form action='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=copysave' method='POST'>
									  <table style='background:#005' width='100%' cellpadding=4 cellspacing=0 border=0 align='center'>
									  <tr>
									   <td valign='middle' align='left'><b style='color:white'>У вас куплено удаление упоминаний об авторских правах?</b></td>
									   <td valign='middle' align='left'><input type='text' size=50 name='ipb_copy_number' value='введите специальный ключ...' onClick=\"this.value='';\"></td>
									   <td valign='middle' align='left'><input type='submit' class='realdarkbutton' value='Дальше...'></td>
									  </tr>
									  </table>
									  </form>";
									  
			$this->ipsclass->admin->show_inframe( 'http://external.ipslink.com/ipboard22/landing/?p=clientarea' );
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Save...
	/*-------------------------------------------------------------------------*/
	
	function copy_save()
	{
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$settings = new ad_settings();
		$settings->ipsclass =& $this->ipsclass;
		
		$acc_number = trim($this->ipsclass->input['ipb_copy_number']);
		
		if( !preg_match( "#^\d+?\-\d+?\-\d+?\-\d+?#", $acc_number ) )
		{
			$acc_number = "";
		}
		
		if ( stristr( $acc_number, ',pass=' ) )
		{
			list( $acc_number, $pass ) = explode( ',pass=', $acc_number );
			
			if ( md5(strtolower($pass)) == 'b1c4780a00e7d010b0eca0b695398c02' )
			{
				$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_copy_number'" );
				$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => 1           ), "conf_key='ips_cp_purchase'" );
				$settings->setting_rebuildcache();
				
				$this->copy_config('new');
				
				exit();
			}
			else
			{
				$this->ipsclass->admin->error("Пароль некорректный. Пожалуйста, <a href='http://www.ibresource.ru/contacts/'>свяжитесь с нами</a> для получения помощи.");
			}
		}

		
		if ( $acc_number == "" )
		{
			$this->ipsclass->admin->error("Извините, введен некорректный ключ, пожалуйста, вернитесь назад и попробуйте снова.");
		}
		
		$response = trim( @implode ('', @file( "http://www.invisionpower.com/customer/ipb/copy/?k=".urlencode($acc_number) ) ) );

		if ( $response == '1' )
		{
			$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_copy_number'" );
			$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => 1           ), "conf_key='ips_cp_purchase'" );
			
			$settings->setting_rebuildcache();
			
			$this->copy_config('new');
			return;
		}
		else if ( $response == '0' )
		{
			$this->ipsclass->admin->error("Введенный ключ некорректный, это возможно могло случиться из-за следующего:
			               <ul>
			               <li>Вы ошибочно ввели ключ лицензии</li>
			               <li>Вы ошибочно ввели пароль доступа в клиент-центр, вместо</li>
			               <li>Ваша лицензия уже не действует</li>
			               </ul>
			               <br />
			               Пожалуйста, <a href='http://www.ibresource.ru/contacts/'>свяжитесь с нами</a> для получения помощи.
			             ");
		}
		else
		{
			$this->ipsclass->admin->error("Нет ответа от регистрационного сервера компании Invision Power Services, это могло случиться из-за следующего:
			               <ul>
			               <li>Версия вашего PHP не поддерживает удаленные подключения</li>
			               <li>Регистрационный сервер компании Invision Power Services отключен</li>
			               <li>Ваш IPB форум не имеет доступа в интернет</li>
			               </ul>
			               <br />
			               Пожалуйста, <a href='http://www.ibresource.ru/contacts/'>свяжитесь с нами</a> для получения помощи.
			             ");
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Show...
	/*-------------------------------------------------------------------------*/
	
	function copy_config($type="")
	{
		$this->ipsclass->admin->page_detail = "&nbsp;";
		$this->ipsclass->admin->page_title  = "Подтверждение удаления упоминаний об авторских правах";
		
		if ( $type == "new" )
		{
			$this->ipsclass->admin->page_detail .= "<br /><br /><b style='color:red'>Спасибо за регистрацию вашего права на удаление упоминаний!</b>";
		}
		
		//-----------------------------------------
		
		$this->ipsclass->adskin->td_header[] = array( "&nbsp;"    , "100%" );
		
		
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Настройка" );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "Упоминания об авторских правах должны быть удалены из нижней части всех страниц форума.<br /><br />Если этого не произошло, обязательно свяжитесь с нами."
													    )      );
		
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		
		$this->ipsclass->admin->output();
	}
					
}


?>