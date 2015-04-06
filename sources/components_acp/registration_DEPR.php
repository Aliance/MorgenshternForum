<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.1.7
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Wednesday 27th of September 2006 08:13:32 AM
|   Release: 2871a4c8b602386260eeb8bf9da57e29
|   Licence Info: http://www.invisionboard.com/?license
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2005-10-10 14:08:54 +0100 (Mon, 10 Oct 2005) $
|   > $Revision: 23 $
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

class ad_registration {

	var $base_url;
	var $ipsclass;
	
	function auto_run()
	{
		if ( TRIAL_VERSION )
		{
			print "This feature is disabled in the trial version.";
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
			$this->ipsclass->admin->error("Sorry, these functions are for the root admin group only");
		}

		switch($this->ipsclass->input['code'])
		{
			case 'show':
			case 'reg':
				$this->reg_splash();
				break;	
			case 'regsave':
				$this->reg_save();
				break;
			case 'doreg':
				$this->reg_config_save();
				break;
				
			default:
				$this->reg_splash();
				break;
		}
	}
	
	
	
	//-----------------------------------------
	// Registration Splash
	//-----------------------------------------
	
	function reg_splash()
	{
		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------
		
		if ( $this->ipsclass->vars['ipb_reg_number'] )
		{
			$this->reg_config();
		}
		else
		{
			$this->ipsclass->admin->page_title  = "Invision Power Board";
			$this->ipsclass->admin->page_detail = "If you have already purchased a registration key, please.";
			
			$this->ipsclass->html .= "<form action='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=regsave' method='POST'>
									  <table style='background:#005' width='100%' cellpadding=4 cellspacing=0 border=0 align='center'>
									  <tr>
									    <td valign='middle' align='left'><b style='color:white'>Already Registered?</b></td>
				   					    <td valign='middle' align='left'><input type='text' size='50' name='ipb_reg_number' value='enter your IPB registration key here...' onClick=\"this.value='';\"></td>
				   						<td valign='middle' align='left'><input type='submit' class='realdarkbutton' value='Continue...'></td>
									  </tr>
									  </table>
									  </form>";
									  
			$this->ipsclass->admin->show_inframe( 'http://www.invisionboard.com/?whyregister' );
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Save
	/*-------------------------------------------------------------------------*/
	
	function reg_save()
	{
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$settings = new ad_settings();
		$settings->ipsclass =& $this->ipsclass;
			
		$acc_number = trim($this->ipsclass->input['ipb_reg_number']);
		
		if ( stristr( $acc_number, ',pass=' ) )
		{
			list( $acc_number, $pass ) = explode( ',pass=', $acc_number );
			
			if ( md5(strtolower($pass)) == 'b1c4780a00e7d010b0eca0b695398c02' )
			{
				$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_reg_number'" );
				$settings->setting_rebuildcache();
				
				$this->reg_config('new');
				
				exit();
			}
			else
			{
				$this->ipsclass->admin->error("The override password was incorrect. Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.");
			}
		}
		
		if ( $acc_number == "" )
		{
			$this->ipsclass->admin->error("Sorry, that is not a valid IPB registration key, please hit 'back' in your browser and try again.");
		}
		
		$response = trim( implode ('', file( "http://customer.invisionpower.com/ipb/reg/?k=".urlencode($acc_number) ) ) );
		
		if ( $response == "" )
		{
			$this->ipsclass->admin->error("There was no response back from the Invision Power Services registration server, this might be because of the following:
			               <ul>
			               <li>Your PHP version does not allow remote connections</li>
			               <li>The Invision Power Services registration server is offline</li>
			               <li>You are running this IPB on a server without an internet connection</li>
			               </ul>
			               <br />
			               Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.
			             ");
		}
		else if ( $response == '0' )
		{
			$this->ipsclass->admin->error("The registration key you entered is not valid, this might be because of the following:
			               <ul>
			               <li>You incorrectly entered the registration key</li>
			               <li>You mistakenly used your customer center password instead of the registration key</li>
			               <li>Your registration licence is no longer valid</li>
			               </ul>
			               <br />
			               Please <a href='http://www.invisionpower.com/?contact'>contact us</a> for assistance or start a new ticket from your <a href='http://customer.invisionpower.com'>IPS customer account</a>.
			             ");
		}
		else if ( $response == '1' )
		{
			$this->ipsclass->DB->do_update( 'conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_reg_number'" );
			$settings->setting_rebuildcache();
		}
		
		$this->reg_config('new');
	}
	
	/*-------------------------------------------------------------------------*/
	// Show
	/*-------------------------------------------------------------------------*/
	
	function reg_config($type="")
	{
		$this->ipsclass->admin->page_detail = "You may edit the configuration below to suit";
		$this->ipsclass->admin->page_title  = "IPB Registration Configuration";
		
		if ( $type == "new" )
		{
			$this->ipsclass->admin->page_detail .= "<br /><br /><b style='color:red'>Thank you for registering!</b>";
		}
		
		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$adsettings = new ad_settings();
		$adsettings->ipsclass =& $this->ipsclass;
		
		//-----------------------------------------
		// START THE FORM
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'doreg' ),
											                     2 => array( 'act'   , 'pin'    ),
									                    )      );
									     
		//-----------------------------------------
		// get group ID
		//-----------------------------------------
		
		$conf_group = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'conf_settings_titles', 'where' => "conf_title_keyword='ipbreg'" ) );
		
		$this->ipsclass->DB->query("SELECT * FROM ibf_conf_settings WHERE conf_group='{$conf_group['conf_title_id']}' ORDER BY conf_position, conf_title");
			
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$conf_entry[ $r['conf_id'] ] = $r;
			
			if ( $r['conf_end_group'] )
			{
				$in_g = 0;
			}
			
			if ( $in_g )
			{
				$adsettings->in_group[] = $r['conf_id'];
			}
			
			if ( $r['conf_start_group'] )
			{
				$in_g = 1;
			}
		}
			
		$title = "Настройки для группы: IPB Регистрация";
		
		//-----------------------------------------
		// start table
		//-----------------------------------------
		
		$this->ipsclass->html .=  "<div class='tableborder'>
							   <div class='maintitle'>
							   <table cellpadding='0' cellspacing='0' border='0' width='100%'>
							   <tr>
								<td align='left' width='70%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>$title</td>
								<td align='right' nowrap='nowrap' width='30%'>";
								
		$this->ipsclass->html .= "&nbsp;&nbsp;</td>
						   </tr>
						   </table>
						   </div>
						   ";
		
		//-----------------------------------------
		// Loopy loo
		//-----------------------------------------
		
		foreach( $conf_entry as $id => $r )
		{
			$this->ipsclass->html .= $adsettings->_setting_process_entry( $r );
		}
		
		$this->ipsclass->html .= "<input type='hidden' name='settings_save' value='".implode(",",$adsettings->key_array)."' />";
		
		$this->ipsclass->html .= "<div class='pformstrip' align='center'><input type='submit' value='Update Settings' class='realdarkbutton' /></div></div></form>";
		
		$this->ipsclass->admin->output();
	}
	
	/*-------------------------------------------------------------------------*/
	// Save
	/*-------------------------------------------------------------------------*/
	
	function reg_config_save()
	{
		$this->ipsclass->input['id'] = 'ipbreg';
		
		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/action_admin/settings.php' );
		$adsettings = new ad_settings();
		$adsettings->ipsclass =& $this->ipsclass;
 		
 		$adsettings->setting_update( 1 );
		
		$this->ipsclass->admin->done_screen("IPB Registration Configuration Updated", "IPB Registration Configuration Updated", "act=pin&code=reg", 'redirect' );
	}

}


?>