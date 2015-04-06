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
|   > Diagnostics Center
|   > Module written by Brandon Farber
|   > Date started: 19th April 2006
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>�������� ����</h1> � ��� ��� ������� � ����� �����. ���� ����������� ����������, ���������, ��� �� ������ �������� ���� admin.php.";
	exit();
}


class ad_diagnostics
{
	var $base_url;
	var $dir_split = "/";

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
	var $perm_child = "diag";

	function auto_run()
	{
		//-----------------------------------------
		// Load skin
		//-----------------------------------------

		$this->html = $this->ipsclass->acp_load_template( 'cp_skin_diagnostics' );

		//-----------------------------------------
		// Set default nav
		//-----------------------------------------

		$this->ipsclass->admin->nav[] = array( $this->ipsclass->form_code, '�����������' );

		if ( strtoupper( substr(PHP_OS, 0, 3) ) === 'WIN' )
		{
			$this->dir_split = "\\";
		}

		//-----------------------------------------

		switch($this->ipsclass->input['code'])
		{
			case 'dbindex':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':do' );
				$this->db_index_check();
				break;

			case 'dbchecker':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':do' );
				$this->db_check();
				break;

			case 'whitespace':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':do' );
				$this->whitespace_check();
				break;

			case 'filepermissions':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':do' );
				$this->permissions_check();
				break;

			case 'fileversions':
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':do' );
				$this->version_check();
				break;

			//-----------------------------------------
			default:
				$this->ipsclass->admin->cp_permission_check( $this->perm_main.'|'.$this->perm_child.':do' );
				$this->list_functions();
				break;
		}
	}

	/*-------------------------------------------------------------------------*/
	// Version Check
	/*-------------------------------------------------------------------------*/

	function version_check()
	{
		$this->ipsclass->admin->page_detail = "������ ������ ������ ������ ������������ ����������� ����� �������� � ������ ����������� �����������.<br />��������, ����� ������ 2.1 �� ���������� � ������� ����� ������ ������. ���� �������� ������-���� ����� �� ������� ������� �������, ������������� �������� ���� ���� �� ����� �����.";
		$this->ipsclass->admin->page_title  = "����������� IPB";

		$this->ipsclass->admin->nav[] = array( '', '��������� �������� ������������� ������ ������' );
		set_time_limit(0);

		$dir 	= preg_replace( "#^(.+?)\/$#", "\\1", ROOT_PATH );

		$file_versions   = array();
		$upgrade_history = array();
		$latest_version  = array( 'upgrade_version_id' => '' );
		$file_versions   = $this->version_recur_dir( $dir );

		$this->ipsclass->adskin->td_header[] = array( ""  , "30%" );
		$this->ipsclass->adskin->td_header[] = array( ""  , "70%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "���������� � ������" );

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
		// Got real version number?
		//-----------------------------------------

		if ( $this->ipsclass->version == 'v<{%dyn.down.var.human.version%}>' )
		{
			$this->ipsclass->version = 'v'.$latest_version['upgrade_version_human'];
		}

		if ( $this->ipsclass->acpversion == '<{%dyn.down.var.long_version_id%}>' )
		{
			$this->ipsclass->acpversion = $latest_version['upgrade_version_id'];
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "������ IPB", $this->ipsclass->version.' (ID: '.$this->ipsclass->acpversion.')' ) );

		if( !count($upgrade_history) )
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "������� ����������", '<i>�� ��������</i>' ) );
		}
		else
		{
			foreach( $upgrade_history as $history_row )
			{
				$history_row['_date'] = $this->ipsclass->get_date( $history_row['upgrade_date'], 'SHORT' );

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���������� {$history_row['_date']}", "{$history_row['upgrade_version_human']} ({$history_row['upgrade_version_id']})" ) );
			}
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->adskin->td_header[] = array( "����"  	, "70%" );
		$this->ipsclass->adskin->td_header[] = array( "������" , "30%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������� �������� ������������� ������ ������" );

		if( is_array($file_versions) && count($file_versions) )
		{
			foreach( $file_versions as $file => $version )
			{
				if( 'v'.$version == $this->ipsclass->version )
				{
					$version = "<span class='rss-feed-valid'>{$version}</span>";
				}
				else
				{
					$version = "<span class='rss-feed-invalid'>{$version}</span>";
				}

				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $file, $version ) );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���������� ���������� ������ ������." ) );
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
	}

	/*-------------------------------------------------------------------------*/
	// Version check, recursive
	/*-------------------------------------------------------------------------*/

	function version_recur_dir($dir)
	{
		$skip_dirs = array( 'jscripts', 'cache', 'ssi_templates', 'style_avatars', 'style_emoticons', 'style_images', 'upgrade', 'uploads', 'images', 'i18n', 'PEAR', 'components_acp',
							 'components_init', 'components_location', 'components_ucp', 'components_public' );
		$skip_files = array( 'conf_global.php', 'conf.php', $this->ipsclass->vars['sql_driver'].'_fulltext.php', $this->ipsclass->vars['sql_driver'].'_tables.php',
								$this->ipsclass->vars['sql_driver'].'_inserts.php', $this->ipsclass->vars['sql_driver'].'_install.php' );

		$files	= array();
		$dh		= @opendir($dir);

		while (false !== ($file = readdir($dh)))
		{
	    	if ( preg_match( "#^[_\.]#", $file ) )
	    	{
		    	continue;
		    }

			if ( $file != '.' && $file != '..' )
			{
				$newpath = $dir.$this->dir_split.$file;
				$level = explode( $this->dir_split, $newpath );

				if ( is_dir($newpath) && !in_array( $file, $skip_dirs ) && ($newpath.'../' != 'modules') )
				{
					$files = array_merge( $files, $this->version_recur_dir($newpath) );
				}
				else
				{
					if ( strpos( $file, ".php" ) !== false && !is_dir( $newpath ) && !in_array( $file, $skip_files ) )
					{
						$file = file_get_contents($newpath);

						preg_match( "#Invision Power Board v(.+?)\s+?#i", $file, $matches);

						$files[$newpath] = isset($matches[1]) ? $matches[1] : '';
					}
			  	}
			}
		}

		closedir($dh);
		return $files;
	}

	/*-------------------------------------------------------------------------*/
	// Permission Checks
	/*-------------------------------------------------------------------------*/

	function permissions_check()
	{
		$this->ipsclass->admin->page_detail = "������������ ����� � ���������� ������ ����� ���������� �������� ������� ��� ���������� ������ IPB.<br />�� ������������ �������� Windows � �������� �������� NTFS ����� ������ ����� �������� �������/�������, � �� ����� ��� �� Unix/Linux � �CHMOD 0777�.";
		$this->ipsclass->admin->page_title  = "����������� IPB";

		$this->ipsclass->admin->nav[] = array( '', '��������� �������� ��������� ������� � ������' );

		$checkdirs = array( 'style_images', 'style_emoticons', 'cache', 'cache'.$this->dir_split.'skin_cache', 'cache'.$this->dir_split.'lang_cache', 'uploads' );
		$langfiles = array( 'lang_boards', 'lang_buddy', 'lang_calendar', 'lang_emails', 'lang_email_content', 'lang_error',
								'lang_forum', 'lang_global', 'lang_help', 'lang_legends', 'lang_login', 'lang_mlist',
							 	'lang_mod', 'lang_msg', 'lang_online', 'lang_portal', 'lang_post', 'lang_printpage',
							 	'lang_profile', 'lang_register', 'lang_search', 'lang_stats', 'lang_subscriptions',
							 	'lang_topic', 'lang_ucp', 'lang_chatpara' , 'lang_editors', 'lang_chatsigma',
							 	'acp_lang_acpperms', 'acp_lang_member', 'acp_lang_portal', 'lang_tar'
						   );

		$root_dir 	= preg_replace( "#^(.+?)\/$#", "\\1".$this->dir_split, ROOT_PATH );

		//-----------------------------------------
		// Get language directories
		//-----------------------------------------

		$this->ipsclass->init_load_cache( array( 'languages' ) );

		if( is_array( $this->ipsclass->cache['languages'] ) && count( $this->ipsclass->cache['languages'] ) )
		{
			foreach( $this->ipsclass->cache['languages'] as $v )
			{
				$checkdirs[] = 'cache'.$this->dir_split.'lang_cache'.$this->dir_split.$v['ldir'];

				foreach( $langfiles as $filename )
				{
					$checkdirs[] = 'cache'.$this->dir_split.'lang_cache'.$this->dir_split.$v['ldir'].$this->dir_split.$filename.'.php';
				}
			}
		}
		else
		{
			$this->ipsclass->DB->build_query( array( 'select' => 'ldir', 'from' => 'languages' ) );
			$this->ipsclass->DB->exec_query();

			while( $v = $this->ipsclass->DB->fetch_row() )
			{
				$checkdirs[] = 'cache'.$this->dir_split.'lang_cache'.$this->dir_split.$v['ldir'];

				foreach( $langfiles as $filename )
				{
					$checkdirs[] = 'cache'.$this->dir_split.'lang_cache'.$this->dir_split.$v['ldir'].$this->dir_split.$filename.'.php';
				}
			}
		}

		//-----------------------------------------
		// Get emoticon directories
		//-----------------------------------------

		if( is_array( $this->ipsclass->cache['emoticons'] ) && count( $this->ipsclass->cache['emoticons'] ) )
		{
			foreach( $this->ipsclass->cache['emoticons'] as $v )
			{
				$checkdirs[] = 'style_emoticons'.$this->dir_split.$v['emo_set'];
			}
		}
		else
		{
			$this->ipsclass->DB->build_query( array( 'select' => 'emo_set', 'from' => 'emoticons' ) );
			$this->ipsclass->DB->exec_query();

			while( $v = $this->ipsclass->DB->fetch_row() )
			{
				$checkdirs[] = 'style_emoticons'.$this->dir_split.$v['emo_set'];
			}
		}

		//-----------------------------------------
		// Get skin directories
		//-----------------------------------------

		$skin_dirs = array();

		if( is_array( $this->ipsclass->cache['skin_id_cache'] ) && count( $this->ipsclass->cache['skin_id_cache'] ) )
		{
			foreach( $this->ipsclass->cache['skin_id_cache'] as $k => $v )
			{
				if( $k == 1 && !IN_DEV )
				{
					continue;
				}

				$checkdirs[] = 'cache'.$this->dir_split.'skin_cache'.$this->dir_split.'cacheid_'.$v['set_skin_set_id'];
				$skin_dirs[] = $v['set_skin_set_id'];
			}
		}
		else
		{
			$this->ipsclass->DB->build_query( array( 'select' => 'set_skin_set_id', 'from' => 'skin_sets' ) );
			$this->ipsclass->DB->exec_query();

			while( $v = $this->ipsclass->DB->fetch_row() )
			{
				$checkdirs[] = 'cache'.$this->dir_split.'skin_cache'.$this->dir_split.'cacheid_'.$v['set_skin_set_id '];
				$skin_dirs[] = $v['set_skin_set_id'];
			}
		}

		//-----------------------------------------
		// Get skin files
		//-----------------------------------------

		$this->ipsclass->DB->load_cache_file( ROOT_PATH.'sources/sql/'.SQL_DRIVER.'_extra_queries.php', 'sql_extra_queries' );

		$this->ipsclass->DB->cache_add_query( 'diag_distinct_skins', array(), 'sql_extra_queries' );
		$this->ipsclass->DB->cache_exec_query();

		while( $v = $this->ipsclass->DB->fetch_row() )
		{
			foreach( $skin_dirs as $dir )
			{
				$checkdirs[] = 'cache'.$this->dir_split.'skin_cache'.$this->dir_split.'cacheid_'.$dir.$this->dir_split.$v['group_name'].'.php';
			}
		}

		set_time_limit(0);

		$checkdirs 	= array_unique($checkdirs);
		$output 	= array();

		foreach( $checkdirs as $dir_to_check )
		{
			if( !file_exists( $root_dir.$dir_to_check ) )
			{
				$output[] = "<span class='rss-feed-invalid'>���������� ����� ���� ��� ���������� ".$root_dir.$dir_to_check."</span>";
			}
			else if( !is_writeable( $root_dir.$dir_to_check ) )
			{
				$output[] = "<span class='rss-feed-invalid'>���������� ��� ���� ".$root_dir.$dir_to_check." ���������� ��� ������</span>";
			}
			else if( is_writeable( $root_dir.$dir_to_check ) )
			{
				$output[] = "<span class='rss-feed-valid'>".$root_dir.$dir_to_check." �������� ��� ������</span>";
			}
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������� �������� ��������� ������� � ������" );

		if( is_array($output) && count($output) )
		{
			foreach( $output as $html_row )
			{
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $html_row ) );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "� ���������, ���������� ����� ��������� ����� ��� ����������." ) );
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
	}


	/*-------------------------------------------------------------------------*/
	// WHITE SPACE CHECK
	/*-------------------------------------------------------------------------*/

	function whitespace_check()
	{
		$this->ipsclass->admin->page_detail = "���� � ������, ������� ������������ �������, ����� ������������ <?php � ����� ?> ������� ������� ��� �������� �����, �� ��� ����� �������� � ������������ ������ ������.<br />���� � ���������� �������� ����� ����� ����� �������, �� ��� ���������� ������� ��������� ����� � ������� ��� ������� �� <?php � ����� ?>.";
		$this->ipsclass->admin->page_title  = "����������� IPB";

		$this->ipsclass->admin->nav[] = array( '', '���������� �������� �� ������ ������� � �������� �����' );

		set_time_limit(0);

		$dir 	= preg_replace( "#^(.+?)\/$#", "\\1", ROOT_PATH );

		$files_with_junk = array();
		$files_with_junk = $this->recur_dir( $dir );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "���������� �������� �� ������ ������� � �������� �����" );

		if( is_array($files_with_junk) && count($files_with_junk) )
		{
			foreach( $files_with_junk as $html_row )
			{
				$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $html_row." ����� ������ ������� ��� �������� ����� � ������ ��� ����� �����." ) );
			}
		}
		else
		{
			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "��� ����� ������� ��������� � ���������� ���������." ) );
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
	}


	function recur_dir($dir)
	{
		$skip_dirs = array( 'uploads', 'style_images', 'gallery_setup', 'blog_setup', 'style_emoticons', 'style_avatars',
							'jscripts', 'clientscripts', 'images', 'acp_js_skin' );

		$files	= array();
		$dh		= @opendir($dir);

		while (false !== ($file = readdir($dh)))
		{
	    	if ( preg_match( "#^[_\.]#", $file ) )
	    	{
		    	continue;
		    }

			if ( $file != '.' && $file != '..' )
			{
				$newpath = $dir.$this->dir_split.$file;
				$level = explode( $this->dir_split, $newpath );

				if ( is_dir($newpath) && !in_array( $file, $skip_dirs ) )
				{
						$files = array_merge( $files, $this->recur_dir($newpath) );
				}
				else
				{
					if ( strpos( $file, ".php" ) !== false && !is_dir( $newpath ) )
					{
						$file = file_get_contents($newpath);

						$current_length = strlen($file);

						$file = trim($file);

						$actual_length  = strlen($file);

						if ( $current_length != $actual_length )
						{
							$files[] = $newpath;
						}
					}
			  	}
			}
		}

		closedir($dh);
		return $files;
	}

	/*-------------------------------------------------------------------------*/
	// CHECK DB INDEXES
	/*-------------------------------------------------------------------------*/

	function db_index_check()
	{
		$this->ipsclass->admin->page_detail = "������� ���� ������ ��������� ������ ������ �������� ����� ����������.<br />� ������, ���� �����-���� ������ �����������, ��� ����� ����� �������� ���������. ���� ���������� �������� ��� ������� � � ������ ������������� ������� ��� ��������� ������.";
		$this->ipsclass->admin->page_title  = "����������� IPB";

		$this->ipsclass->admin->nav[] = array( '', '��������� �������� �������� ��' );

		//-----------------------------------------
		// Get current table definitions
		//-----------------------------------------

		if( !file_exists( ROOT_PATH."/install/sql/{$this->ipsclass->vars['sql_driver']}_tables.php" ) )
		{
			$this->ipsclass->admin->error( "�� ������ ��������� ���� /install/sql/{$this->ipsclass->vars['sql_driver']}_tables.php �� ������������ IPB ��� ������� ����� �����������." );
		}

		require ROOT_PATH."sources/action_admin/sql_{$this->ipsclass->vars['sql_driver']}.php";

		$this->sql_driver = new ad_sql_module();
		$this->sql_driver->ipsclass =& $this->ipsclass;

		$output = array();
		$output = $this->sql_driver->db_index_diag();

		if( $this->sql_driver->db_has_issues )
		{
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );

			$this->ipsclass->html .= $this->html->dbindexer_javascript();

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������: ������� ������" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( array( "<span class='rss-feed-invalid'>��� �������� �������� ���� ������ ���� ������� ������. ����������, ������� <a href='#' onclick='fix_all_dberrors();'>�����</a> ��� ����������� ���� ������.", 2 ) ) );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}
		else
		{
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������ �� �������" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( array( "<span class='rss-feed-valid'>��� �������� �������� ����� ���� ������ ������ �� �������.", 2 ) ) );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		$this->ipsclass->adskin->td_header[] = array( "�������"    	, "20%" );
		$this->ipsclass->adskin->td_header[] = array( "�������� �������"  , "20%" );
		$this->ipsclass->adskin->td_header[] = array( "�����������"       	, "60%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������� �������� �������� ��" );

		foreach( $output as $html_row )
		{
			$this->ipsclass->html .= $html_row;
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
    }

    /*-------------------------------------------------------------------------*/
	// DB TABLES
	/*-------------------------------------------------------------------------*/

	function db_check()
	{
		$this->ipsclass->admin->page_detail = "������ ���������� �������� ��� ������� � ���� ����� ���� ������.<br />��� ������� � ��� �������, ����� ��� ��������� IPB �� ��������� ������ SQL ��� �� ���������� � ���, ��� ��������� ����������� ����� �����������.<br />��������! ���������� ������������ �������� ������ ����������� ������ � �����. ��� ��������, ��� ������ � ����������� �������������� ������������� ��� �������� �� �� ��������.";
		$this->ipsclass->admin->page_title  = "����������� IPB";

		$this->ipsclass->admin->nav[] = array( '', '��������� �������� ��' );

		//-----------------------------------------
		// Get current table definitions
		//-----------------------------------------

		if( !file_exists( ROOT_PATH."/install/sql/{$this->ipsclass->vars['sql_driver']}_tables.php" ) )
		{
			$this->ipsclass->admin->error( "�� ������ ��������� ���� /install/sql/{$this->ipsclass->vars['sql_driver']}_tables.php �� ������������ IPB ��� ������� ����� �����������." );
		}

		require ROOT_PATH."sources/action_admin/sql_{$this->ipsclass->vars['sql_driver']}.php";

		$this->sql_driver = new ad_sql_module();
		$this->sql_driver->ipsclass =& $this->ipsclass;

		$output = array();
		$output = $this->sql_driver->db_table_diag();

		if( $this->sql_driver->db_has_issues )
		{
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );

			$this->ipsclass->html .= $this->html->dbchecker_javascript();

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������: ������� ������" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( array( "<span class='rss-feed-invalid'>��� �������� ������ � ����� ����� ���� ������ ���� ������� ������. ����������, ������� <a href='#' onclick='fix_all_dberrors();'>�����</a> ��� ����������� ���� ������.", 2 ) ) );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}
		else
		{
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );
			$this->ipsclass->adskin->td_header[] = array( "{none}"    	, "50%" );

			$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "������ �� �������" );

			$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( array( "<span class='rss-feed-valid'>��� �������� ������ � ����� ����� ���� ������ ������ �� �������.", 2 ) ) );

			$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		}

		$this->ipsclass->adskin->td_header[] = array( "�������"    	, "30%" );
		$this->ipsclass->adskin->td_header[] = array( "������"  	, "20%" );
		$this->ipsclass->adskin->td_header[] = array( "�����������"       	, "50%" );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "��������� �������� ��" );

		foreach( $output as $html_row )
		{
			$this->ipsclass->html .= $html_row;
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->admin->output();
    }

	/*-------------------------------------------------------------------------*/
	// SHOW FUNCTIONS
	/*-------------------------------------------------------------------------*/

	function list_functions()
	{
		$this->ipsclass->admin->page_detail = "�� ���� �������� �� ������ �������� ����������� �������.<br /><br /><b>�������, ��� ������ ���� ������������ � ���������� �������, � ��� �� ���������� ��������� ��������� �����.";
		$this->ipsclass->admin->page_title  = "����������� IPB";

		$this->ipsclass->DB->sql_get_version();
		$sql_version = strtoupper(SQL_DRIVER)." ".$this->ipsclass->DB->true_version;

		$php_version = phpversion()." (".@php_sapi_name().")  ( <a href='{$this->ipsclass->base_url}&phpinfo=1'>PHP INFO</a> )";
		$server_software = php_uname();

		$load_limit = "--";
        $server_load_found = 0;

        //-----------------------------------------
        // Check cache first...
        //-----------------------------------------

        if( $this->ipsclass->cache['systemvars']['loadlimit'] )
        {
	        $loadinfo = explode( "-", $this->ipsclass->cache['systemvars']['loadlimit'] );

	        if ( intval($loadinfo[1]) > (time() - 10) )
	        {
		        //-----------------------------------------
		        // Cache is less than 2 minutes old, use it
		        //-----------------------------------------

		        $server_load_found = 1;

    			$load_limit = $loadinfo[0];
			}
		}

        //-----------------------------------------
        // No cache or it's old, check real time
        //-----------------------------------------

		if( !$server_load_found )
		{
	        # @ supressor fixes warning in >4.3.2 with open_basedir restrictions

        	if ( @file_exists('/proc/loadavg') )
        	{
        		if ( $fh = @fopen( '/proc/loadavg', 'r' ) )
        		{
        			$data = @fread( $fh, 6 );
        			@fclose( $fh );

        			$load_avg = explode( " ", $data );

        			$load_limit = trim($load_avg[0]);
        		}
        	}
        	else if( strstr( strtolower(PHP_OS), 'win' ) )
        	{
		        /*---------------------------------------------------------------
		        | typeperf is an exe program that is included with Win NT,
		        |	XP Pro, and 2K3 Server.  It can be installed on 2K from the
		        |	2K Resource kit.  It will return the real time processor
		        |	Percentage, but will take 1 second processing time to do so.
		        |	This is why we shall cache it, and check only every 2 mins.
		        |
		        |	Can also be obtained from COM, but it's extremely slow...
		        ---------------------------------------------------------------*/

	        	$serverstats = @shell_exec("typeperf \"Processor(_Total)\% Processor Time\" -sc 1");

	        	if( $serverstats )
	        	{
					$server_reply = explode( "\n", str_replace( "\r", "", $serverstats ) );
					$serverstats = array_slice( $server_reply, 2, 1 );

					$statline = explode( ",", str_replace( '"', '', $serverstats[0] ) );

					$load_limit = round( $statline[1], 4 );
				}
			}
        	else
        	{
				if ( $serverstats = @exec("uptime") )
				{
					preg_match( "/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $serverstats, $load );

					$load_limit = $load[1];
				}
			}

			if( $load_limit )
			{
				$this->ipsclass->cache['systemvars']['loadlimit'] = $load_limit."-".time();

				$this->ipsclass->update_cache(  array( 'name' => 'systemvars', 'array' => 1, 'deletefirst' => 0 ) );
			}
		}

		$total_memory = $avail_memory = "--";

		if( strstr( strtolower(PHP_OS), 'win' ) )
		{
			$mem = @shell_exec('systeminfo');

			if( $mem )
			{
				$server_reply = explode( "\n", str_replace( "\r", "", $mem ) );

				if( count($server_reply) )
				{
					foreach( $server_reply as $info )
					{
						if( strstr( $info, "����� ���������� ������" ) )
						{
							$total_memory =  trim( str_replace( ":", "", strrchr( $info, ":" ) ) );
						}

						if( strstr( $info, "�������� ���������� ������" ) )
						{
							$avail_memory =  trim( str_replace( ":", "", strrchr( $info, ":" ) ) );
						}
					}
				}
			}
		}
		else
		{
			$mem = @shell_exec("free -m");
			$server_reply = explode( "\n", str_replace( "\r", "", $mem ) );
			$mem = array_slice( $server_reply, 1, 1 );
			$mem = preg_split( "#\s+#", $mem[0] );

			$total_memory = $mem[1].' MB';
			$avail_memory = $mem[3].' MB';
		}

		$disabled_functions = @ini_get('disable_functions') ? @ini_get('disable_functions') : "<i>��� ����������</i>";

		$this->ipsclass->adskin->td_header[] = array( ""  	, "40%" );
		$this->ipsclass->adskin->td_header[] = array( "" 	, "60%" );
		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "���������� � �������" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "������ IPB",
																				$this->ipsclass->version." (ID:".$this->ipsclass->acpversion.")" ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "������ ".strtoupper(SQL_DRIVER),
																				$sql_version ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "������ PHP",
																				$php_version ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "����������� ������� PHP",
																				$disabled_functions ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���������� �����",
																				SAFE_MODE_ON == 1 ? "<span style='color:red;font-weight:bold;'>�������</span>" : "<span style='color:green;font-weight:bold;'>��������</span>" ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "�� �������",
																				$server_software ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "������� �������� �������",
																				$load_limit ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "����� ����� ������ �������",
																				$total_memory ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "�������� ���������� ������",
																				$avail_memory ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������� �������" );

		if( strstr( strtolower(PHP_OS), 'win' ) )
		{
			$tasks = @shell_exec( "tasklist" );
			$tasks = str_replace( " ", "&nbsp;", $tasks );
		}
		else
		{
			$tasks = @shell_exec( "top -b -n 1" );
			$tasks = str_replace( " ", "&nbsp;", $tasks );
		}

		if( !$tasks )
		{
			$tasks = "<i>���������� �������� ���������� � ���������</i>";
		}
		else
		{
			$tasks = "<pre>".$tasks."</pre>";
		}

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( $tasks ) );
		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// File Version Checker
		//-----------------------------------------
		/*
		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'fileversions' ),
												                 			 2 => array( 'act'   , 'diag' ),
												                 			 4 => array( 'section', $this->ipsclass->section_code ),
									                    			 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������� ������ ������" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "This tool will check your file versions to verify they match.  Running different versions of core files can result in unexpected behavior." ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form('���������');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();
		*/
		//-----------------------------------------
		// Whitespace Checker
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'whitespace' ),
												                 			 2 => array( 'act'   , 'diag' ),
												                 			 4 => array( 'section', $this->ipsclass->section_code ),
									                    			 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������� �� ������� � �������� �����" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���� ���� php-����� �������� ������� ��� �������� ����� � ����� ��� ������, ��� ����� ��������� ��������� �� ������ ������ IPB.<br />���� ���������� �������� ����� ����� ������� �� ������ ������� � ��������." ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form('���������');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// File Permissions Checker
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'filepermissions' ),
												                 			 2 => array( 'act'   , 'diag' ),
												                 			 4 => array( 'section', $this->ipsclass->section_code ),
									                    			 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������� ��������� ������" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���� ���������� �������� �������� ������ � ����� �� ����������� ������ ������. ��� ����������� ������������� ��������� ���������� ���������� ��������� ����� ������, �������� ������� � ������ ��������� ������." ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form('���������');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// DB Table/Column Checker
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'dbchecker' ),
												                 			 2 => array( 'act'   , 'diag' ),
												                 			 4 => array( 'section', $this->ipsclass->section_code ),
									                    			 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������� ������ � ����� ���� ������" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���� ���������� �������� �� ����������� ������� � ���� ����� ���� ������." ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form('���������');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------
		// DB Index Checker
		//-----------------------------------------

		$this->ipsclass->html .= $this->ipsclass->adskin->start_form( array( 1 => array( 'code'  , 'dbindex' ),
												                 			 2 => array( 'act'   , 'diag' ),
												                 			 4 => array( 'section', $this->ipsclass->section_code ),
									                    			 )      );

		$this->ipsclass->html .= $this->ipsclass->adskin->start_table( "�������� �������� �������" );

		$this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "���� ���������� �������� �� ����������� ������� ����� ���� ������. ���� ������� �� ����������� ������� �������, ��� ����� �������� � ����� ����������� ������ ����� ���� ������." ) );

		$this->ipsclass->html .= $this->ipsclass->adskin->end_form('���������');

		$this->ipsclass->html .= $this->ipsclass->adskin->end_table();

		//-----------------------------------------

		$this->ipsclass->admin->output();

	}


}


?>