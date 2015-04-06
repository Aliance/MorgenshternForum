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
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-12-05 09:12:45 -0600 (Tue, 05 Dec 2006) $
|   > $Revision: 765 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Admin "welcome" screen functions
|   > Module written by Matt Mecham
|   > Date started: 1st march 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}
class ad_index
{
	# Global
	var $ipsclass;
	var $html;
	
	var $mysql_version = "";
	
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
	var $perm_child = "splash";
		
	function auto_run()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		define( 'IPS_NEWS_URL'         , 'http://www.ibresource.ru/forums/ibr_news.php' );
		define( 'IPS_VERSION_CHECK_URL', 'http://external.iblink.ru/latestversioncheck/version.php' );
		
		$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':' );
		
		$content         = array();
		$thiscontent     = "";
		$upgrade_history = array();
   		$latest_version  = array();
   		$reg_end         = "";
   		$sm_install      = 0;
		$lock_file       = 0;
		$converter		 = 0;
		
		if ( @file_exists( ROOT_PATH . 'install/index.php' ) )
		{
			$sm_install = 1;
		}
		
		if ( @file_exists( ROOT_PATH . 'install/installfiles/lock.php' ) )
		{
			$lock_file = 1;
		}
		
		if ( @file_exists( ROOT_PATH . 'convert/index.php' ) )
		{
			$converter = 1;
		}		
		
		if ( @file_exists( ROOT_PATH . 'upgrade/core/class_installer.php' ) )
		{
			define( 'INS_ROOT_PATH', ROOT_PATH.'upgrade/' );
			
			require_once ROOT_PATH . 'upgrade/core/class_installer.php';
			require_once ROOT_PATH . 'upgrade/custom/app.php';
			
			$upgrade = new application_installer();
			$upgrade->ipsclass =& $this->ipsclass;
			
			$upgrade->get_version_latest();
			
			if( $upgrade->last_poss_id > $upgrade->current_version )
			{
				$unfinished_upgrade = 1;
			}
		}
		
		//-----------------------------------------
		// LOAD HTML
		//-----------------------------------------
		
		$this->html = $this->ipsclass->acp_load_template('cp_skin_index');
		
		//-----------------------------------------
		// continue...
		//-----------------------------------------
		
		$this->ipsclass->admin->page_title  = "Добро пожаловать в админцентр вашего Invision Power Board";
		$this->ipsclass->admin->page_detail = "";
		
		//-----------------------------------------
		// PHP INFO?
		//-----------------------------------------
		
		if ( isset($this->ipsclass->input['phpinfo']) AND $this->ipsclass->input['phpinfo'] )
		{
			@ob_start();
			phpinfo();
			$parsed = @ob_get_contents();
			@ob_end_clean();
			
			preg_match( "#<body>(.*)</body>#is" , $parsed, $match1 );
			
			$php_body  = $match1[1];
			
			# PREVENT WRAP: Most cookies
			$php_body  = str_replace( "; " , ";<br />"   , $php_body );
			# PREVENT WRAP: Very long string cookies
			$php_body  = str_replace( "%3B", "<br />"    , $php_body );
			# PREVENT WRAP: Serialized array string cookies
			$php_body  = str_replace( ";i:", ";<br />i:" , $php_body );
			# PREVENT WRAP: LS_COLORS env
			$php_body  = str_replace( ":*.", "<br />:*." , $php_body );
			# PREVENT WRAP: PATH env
			$php_body  = str_replace( "bin:/", "bin<br />:/" , $php_body );
			# PREVENT WRAP: Cookie %2C split
			$php_body  = str_replace( "%2C", "%2C<br />" , $php_body );
			#PREVENT WRAP: Cookie , split
			$php_body  = preg_replace( "#,(\d+),#", ",<br />\\1," , $php_body );
			
			
			$php_style = "<style type='text/css'>
						  .center {text-align: center;}
						  .center table { margin-left: auto; margin-right: auto; text-align: left; }
						  .center th { text-align: center; }
						  h1 {font-size: 150%;}
						  h2 {font-size: 125%;}
						  .p {text-align: left;}
						  .e {background-color: #ccccff; font-weight: bold;}
						  .h {background-color: #9999cc; font-weight: bold;}
						  .v {background-color: #cccccc; white-space: normal;}
						  </style>\n";
						  
			$this->ipsclass->html = $php_style . $php_body;
			$this->ipsclass->admin->output();
		}
		
		//-----------------------------------------
		// Get MySQL & PHP Version
		//-----------------------------------------
		
		$this->ipsclass->DB->sql_get_version();
   		
   		//-----------------------------------------
   		// Upgrade history?
   		//-----------------------------------------
   		
   		$latest_version = array( 'upgrade_version_id' => NULL );
   		
   		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'upgrade_history', 'order' => 'upgrade_version_id DESC', 'limit' => array(0, 5) ) );
   		$this->ipsclass->DB->simple_exec();
   		
   		while( $r = $this->ipsclass->DB->fetch_row() )
   		{
   			if ( $r['upgrade_version_id'] > $latest_version['upgrade_version_id'] )
   			{
   				$latest_version = $r;
   			}
   			
   			$upgrade_history[] = $r;
   		}

		//-----------------------------------------
		// Resetting security image?
		//-----------------------------------------
		
		if ( isset($_REQUEST['reset_security_flag']) AND $_REQUEST['reset_security_flag'] == 1 AND $_REQUEST['new_build'] )
		{
			$new_build   = intval( $_REQUEST['new_build'] );
			$new_reason  = trim( substr( $_REQUEST['new_reason'], 0, 1 ) );
			$new_version = $latest_version['upgrade_version_id'].'.'.$new_build.'.'.$new_reason;
			
			$this->ipsclass->DB->do_update( 'upgrade_history', array( 'upgrade_notes' => $new_version ), 'upgrade_version_id='.$latest_version['upgrade_version_id'] );
		
			$latest_version['upgrade_notes'] = $new_version;
		}

		//-----------------------------------------
		// Got real version number?
		//-----------------------------------------
		
		$this->ipsclass->version = 'v'.$latest_version['upgrade_version_human'];
		$this->ipsclass->vn_full = ( isset($latest_version['upgrade_notes']) AND $latest_version['upgrade_notes'] ) ? $latest_version['upgrade_notes'] : $this->ipsclass->vn_full;
		
		$content['update_img'] = $this->html->update_img( IPS_VERSION_CHECK_URL . '?' . base64_encode( $this->ipsclass->vn_full.'|^|'.$this->ipsclass->vars['board_url'] ) );
		
		//-----------------------------------------
		// Licensed?
		//-----------------------------------------
		
		$content['reg_html'] = $this->html->acp_licensed();
		
		//-----------------------------------------
		// Notepad
		//-----------------------------------------
		
		if ( isset($this->ipsclass->input['save']) AND $this->ipsclass->input['save'] == 1 )
		{
			$this->ipsclass->update_cache( array( 'value' => $this->ipsclass->txt_stripslashes($_POST['notes']), 'name' => 'adminnotes', 'donow' => 1, 'deletefirst' => 0, 'array' => 0 ) );
		}
		
		$text = "Вы можете использовать этот блок для записи какой-либо информации для всех администраторов.";
		
		$this->ipsclass->init_load_cache( array( 'adminnotes', 'skinpanic' ) );
		
		if ( !isset($this->ipsclass->cache['adminnotes']) OR !$this->ipsclass->cache['adminnotes'] )
		{
			$this->ipsclass->update_cache( array( 'value' => $text, 'name' => 'adminnotes', 'donow' => 1, 'deletefirst' => 0, 'array' => 0 ) );
		
			$this->ipsclass->cache['adminnotes'] = $text;
		}
		
		$this->ipsclass->cache['adminnotes'] = htmlspecialchars($this->ipsclass->cache['adminnotes'], ENT_QUOTES);
		$this->ipsclass->cache['adminnotes'] = str_replace( "&amp;#", "&#", $this->ipsclass->cache['adminnotes'] );
		
		$content['ad_notes'] = $this->html->acp_notes( $this->ipsclass->cache['adminnotes'] );
		
		//-----------------------------------------
		// Quick clicks
		//-----------------------------------------
		
		$content['quick_clicks'] = $this->html->acp_quick_clicks();
		
		//-----------------------------------------
		// Version History
		//-----------------------------------------
		
		foreach( $upgrade_history as $r )
		{
			$r['_date'] = $this->ipsclass->get_date( $r['upgrade_date'], 'SHORT' );
			
			$thiscontent .= $this->html->acp_version_history_row( $r );
		}
		
		$content['version_history'] = $this->html->acp_version_history_wrapper( $thiscontent );
		
		//-----------------------------------------
		// ADMINS USING CP
		//-----------------------------------------
		
		$t_time    = time() - 60*10;
		$time_now  = time();
		$seen_name = array();
		$acponline = "";
		
		$this->ipsclass->DB->build_query( array( 'select'   => 's.session_member_name, s.session_location, s.session_log_in_time, s.session_running_time, s.session_ip_address',
												 'from'     => array( 'admin_sessions' => 's' ),
												 'add_join' => array( 0 => array( 'select' => 'm.members_display_name',
																				  'from'   => array( 'members' => 'm' ),
																				  'where'  => "m.id=s.session_member_id",
																				  'type'   => 'left' ) ) ) );
		
		$this->ipsclass->DB->exec_query();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( isset($seen_name[ $r['session_member_name'] ]) AND $seen_name[ $r['session_member_name'] ] == 1 )
			{
				continue;
			}
			else
			{
				$seen_name[ $r['session_member_name'] ] = 1;
			}
			
			$r['_log_in'] = $time_now - $r['session_log_in_time'];
			$r['_click']  = $time_now - $r['session_running_time'];
			
			if ( ($r['_log_in'] / 60) < 1 )
			{
				$r['_log_in'] = sprintf("%0d", $r['_log_in']) . " секунд назад";
			}
			else
			{
				$r['_log_in'] = sprintf("%0d", ($r['_log_in'] / 60) ) . " минут назад";
			}
			
			if ( ($r['_click'] / 60) < 1 )
			{
				$r['_click'] = sprintf("%0d", $r['_click']) . " секунд назад";
			}
			else
			{
				$r['_click'] = sprintf("%0d", ($r['_click'] / 60) ) . " минут назад";
			}
			
			$r['session_location'] = $r['session_location'] ? $r['session_location'] : 'index';
			
			$acponline .= $this->html->acp_onlineadmin_row( $r );
		}
		
		$content['acp_online'] = $this->html->acp_onlineadmin_wrapper( $acponline );
		
		//-----------------------------------------
		// IPS latest news
		//-----------------------------------------
		
		$content['latest_news'] = $this->html->acp_ips_news( IPS_NEWS_URL );
		
		//-----------------------------------------
		// Stats
		//-----------------------------------------
		
		$reg	= $this->ipsclass->DB->simple_exec_query( array( 'select' => 'COUNT(*) as reg'  , 'from' => 'validating', 'where' => 'lost_pass <> 1' ) );
		
		if( $this->ipsclass->vars['ipb_bruteforce_attempts'] )
		{
			$lock	= $this->ipsclass->DB->simple_exec_query( array( 'select' => 'COUNT(*) as mems'  , 'from' => 'members', 'where' => 'failed_login_count >= ' . $this->ipsclass->vars['ipb_bruteforce_attempts'] ) );
		}
		
		$coppa	= $this->ipsclass->DB->simple_exec_query( array( 'select' => 'COUNT(*) as coppa', 'from' => 'validating', 'where' => 'coppa_user=1' ) );
		
		$content['stats'] = $this->html->acp_stats_wrapper( array( 'topics'      => intval($this->ipsclass->cache['stats']['total_topics']),
																   'replies'     => intval($this->ipsclass->cache['stats']['total_replies']),
																   'members'     => intval($this->ipsclass->cache['stats']['mem_count']),
																   'validate'    => intval( $reg['reg'] ),
																   'locked'		 => intval( $lock['mems'] ),
																   'coppa'       => intval( $coppa['coppa'] ),
																   'sql_driver'  => strtoupper(SQL_DRIVER),
																   'sql_version' => $this->ipsclass->DB->true_version,
																   'php_version' => phpversion(),
																   'php_sapi'    => @php_sapi_name(),
																   'ipb_version' => $this->ipsclass->version,
																   'ipb_id'      => $this->ipsclass->vn_full ) );
		
		//-----------------------------------------
		// Piece it together
		//-----------------------------------------
		
		$this->ipsclass->html .= $this->html->acp_main_template( $content );
		
		//-----------------------------------------
		// Update img
		//-----------------------------------------
		
		$this->ipsclass->html = str_replace( '<!--updateimg-->', $content['update_img'], $this->ipsclass->html );
		
		//-----------------------------------------
		// Trashed skin?
		//-----------------------------------------
		
		if ( isset($this->ipsclass->cache['skinpanic']) AND $this->ipsclass->cache['skinpanic'] == 'rebuildemergency' )
		{
			$this->ipsclass->html = str_replace( '<!--warningskin-->', $this->html->warning_rebuild_emergency(), $this->ipsclass->html );
		}
		
		if ( isset($this->ipsclass->cache['skinpanic']) AND $this->ipsclass->cache['skinpanic'] == 'rebuildupgrade' )
		{
			$this->ipsclass->html = str_replace( '<!--warningskin-->', $this->html->warning_rebuild_upgrade(), $this->ipsclass->html );
		}
		
		//-----------------------------------------
		// IN DEV stuff...
		//-----------------------------------------
	
		if ( IN_DEV )
		{
			$last_update = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																			 'from'   => 'cache_store',
																			 'where'  => "cs_key='in_dev_setting_update'" ) );
																			
			if ( ! $last_update['cs_value'] )
			{
				$this->ipsclass->DB->do_delete( 'cache_store', "cs_key='in_dev_setting_update'" );
				$this->ipsclass->DB->do_insert( 'cache_store', array( 'cs_value' => time(), 'cs_key' => "in_dev_setting_update" ) );
				$last_update = time();
			}
			
			$last_settings_save = intval( @filemtime( ROOT_PATH . 'resources/settings.xml' ) );
			
			if ( $last_settings_save > $last_update['cs_value'] )
			{
				$_mtime  = $this->ipsclass->get_date( $last_settings_save     , 'JOINED' );
				$_dbtime = $this->ipsclass->get_date( $last_update['cs_value'], 'JOINED' );
				
				$_html = $this->ipsclass->skin_acp_global->warning_box( "settings.xml File Updated",
																		"The 'resources/settings.xml' file has been updated. Please visit <a href='{$this->ipsclass->base_url}&amp;section=tools'>this page</a> to re-import it to make sure your settings are up-to-date
																		<br />Last modified time for 'settings.xml': $_mtime.
																		<br />Last import run: $_dbtime" ) . "<br />";
				
				$this->ipsclass->html = str_replace( '<!--in_dev_check-->', $_html, $this->ipsclass->html );	
			}
			
			if ( @file_exists( ROOT_PATH . '_dev_notes.txt' ) )
			{
				$_notes = @file_get_contents( ROOT_PATH . '_dev_notes.txt' );
				
				if ( $_notes )
				{
					$_html = $this->ipsclass->skin_acp_global->information_box( "Developers' Notes", nl2br($_notes) ) . "<br />";
					$this->ipsclass->html = str_replace( '<!--in_dev_notes-->', $_html, $this->ipsclass->html );
				}
			}
		}
		
		//-----------------------------------------
		// INSTALLER PRESENT?
		//-----------------------------------------
		
		if ( $sm_install == 1 ) 
		{
			if ( $lock_file != 1 )
			{
				$this->ipsclass->html = str_replace( '<!--warninginstaller-->', $this->html->warning_unlocked_installer(), $this->ipsclass->html );
			}
			else
			{
				$this->ipsclass->html = str_replace( '<!--warninginstaller-->', $this->html->warning_installer(), $this->ipsclass->html );
			}
		}
		else if( $converter )
		{
			$this->ipsclass->html = str_replace( '<!--warninginstaller-->', $this->html->warning_converter(), $this->ipsclass->html );            
		}		
		
		//-----------------------------------------
		// UNFINISHED UPGRADE?
		//-----------------------------------------
		
		if ( $unfinished_upgrade == 1 ) 
		{
			$this->ipsclass->html = str_replace( '<!--warningupgrade-->', $this->html->warning_upgrade(), $this->ipsclass->html );
		}		
		
		//-----------------------------------------
		// INSUFFICIENT PHP VERSION?
		//-----------------------------------------
		
		if ( PHP_VERSION < '4.3.0' )
		{
			$this->ipsclass->html = str_replace( '<!--phpversioncheck-->', $this->html->acp_php_version_warning( PHP_VERSION ), $this->ipsclass->html );
		}
		
		//-----------------------------------------
		// BOARD OFFLINE?
		//-----------------------------------------
		
		if ( $this->ipsclass->vars['board_offline'] )
		{
			$this->ipsclass->html = str_replace( '<!--boardoffline-->', $this->html->acp_board_offline(), $this->ipsclass->html );
		}
		
		//-----------------------------------------
		// ROOT ADMIN?
		//-----------------------------------------
		
		$lastactions = "";
		
		if ( $this->ipsclass->member['mgroup'] == $this->ipsclass->vars['admin_group'] )
		{
			//-----------------------------------------
			// LAST 5 Admin Actions
			//-----------------------------------------
			
			$this->ipsclass->DB->cache_add_query( 'index_admin_logs', array() );
			$this->ipsclass->DB->cache_exec_query();
			
			while ( $rowb = $this->ipsclass->DB->fetch_row() )
			{
				$rowb['_ctime'] = $this->ipsclass->admin->get_date( $rowb['ctime'] );
				
				$lastactions .= $this->html->acp_lastactions_row( $rowb );
			}
			
			$this->ipsclass->html = str_replace( '<!--acpactions-->', $this->html->acp_lastactions_wrapper( $lastactions ), $this->ipsclass->html );
			
			//-----------------------------------------
			// Last 5 log in attempts
			//-----------------------------------------
			
			$this->ipsclass->DB->build_query( array( 'select' => '*',
													 'from'   => 'admin_login_logs',
													 'order'  => 'admin_time DESC',
													 'limit'  => array( 0, 5 ) ) );
			
			$this->ipsclass->DB->exec_query();
			
			while ( $rowb = $this->ipsclass->DB->fetch_row() )
			{
				$rowb['_admin_time'] = $this->ipsclass->admin->get_date( $rowb['admin_time'] );
				$rowb['_admin_img']  = $rowb['admin_success'] ? 'aff_tick.png' : 'aff_cross.png';
				
				$logins .= $this->html->acp_last_logins_row( $rowb );
			}
			
			$this->ipsclass->html = str_replace( '<!--acplogins-->', $this->html->acp_last_logins_wrapper( $logins ), $this->ipsclass->html );
		}
		
		$this->ipsclass->admin->output();
	}
	
}


?>