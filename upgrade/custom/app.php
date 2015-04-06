<?php
/**
 * Invision Power Board
 * Invision Installer Framework
 */
 
class application_installer extends class_installer
{
	var $versions			= array();
	var $db_contents		= array();
	var $dir_contents		= array();
	var $error				= array();
	var $current_version	= '';
	var $last_poss_id		= '';
	
	/**
	 * application_installer::set_requirements
	 * 
	 * Sets the requirements for this app
	 *
	 */		
	function pre_process()
	{
		if ( isset( $this->ipsclass->input['p'] ) && $this->ipsclass->input['p'] != 'overview' )
		{
			$this->ipsclass->member = $this->get_member();

			if ( ! $this->ipsclass->member['id'] )
			{
				$this->template->page_current = 'login';
				$this->template->message = "Вы не имеете прав доступа к мастеру обновления.";
			}
			
			if ( $this->ipsclass->return_md5_check() != $this->saved_data['securekey'] )
			{
				$this->template->page_current = 'login';
				$this->template->message = "Вы не имеете прав доступа к мастеру обновления.";
			}
			
			if ( ! $this->ipsclass->member['g_access_cp'] )
			{
				$this->template->page_current = 'login';
				$this->template->message = "Для использования мастера обновления вы должны обладать правами администратора форума.";
			}
			
			if( $this->ipsclass->DB->field_exists( 'converge_id', 'members_converge' ) )
			{
				if( !isset($this->saved_data['vid']) OR !($this->saved_data['vid'] < 10003) )
				{
					$this->ipsclass->converge->converge_load_member( $this->ipsclass->member['email'] );
				}
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// Get the current version and the next version to upgrade to..
	/*-------------------------------------------------------------------------*/

	function get_version_latest()
	{
		$this->current_version = '';
		$this->current_upgrade = '';

		$this->db_contents  = $this->get_db_structure();
		$this->dir_contents = $this->get_dir_structure();

		//--------------------------------
		// Get latest ID...
		//--------------------------------
		$dir_versions = array_reverse($this->dir_contents);
		$this->last_poss_id = array_shift( $dir_versions );

		//--------------------------------
		// Get datafile
		//--------------------------------
		
		if ( file_exists( ROOT_PATH.'resources/version_history.php' ) )
		{
			require_once( ROOT_PATH.'resources/version_history.php' );
		
			$this->versions = $import_versions;
		}
	
		//------------------------------------------
		// Copy & pop DB array and get next
		// upgrade script
		//------------------------------------------

		$tmp = $this->db_contents;

		$this->current_version = array_pop( $tmp );
		
		if ( ! $this->current_version )
		{
			if ( $this->saved_data['vid'] )
			{
				$this->current_version = intval($this->saved_data['vid']);
			}
			else if ( $this->ipsclass->DB->field_exists( 'sub_id', 'subscriptions' ) )
			{
				$this->current_version = '10003';
			}
			else if ( $this->ipsclass->DB->field_exists( 'perm_id', 'forum_perms' ) )
			{
				$this->current_version = '10002';
			}
			else
			{
				$this->current_version = '10001';
			}
		}
		
		//------------------------------------------
		// Get the next upgrade script
		//------------------------------------------

		ksort( $this->dir_contents );

		foreach( $this->dir_contents as $i => $a )
		{
			if ( $this->current_version == '00000' )
			{
				if ( $a > $this->current_upgrade )
				{
					$this->current_upgrade  = $a;
					$this->modules_to_run[0] = $this->versions[ $a ];
				}
			}
			elseif ( $a > $this->current_version )
			{
				if ( ! $this->current_upgrade )
				{
					$this->current_upgrade  = $a;
				}

				$this->modules_to_run[] = $this->versions[ $a ];
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// GET INFO FROM THE DERTABASTIC
	/*-------------------------------------------------------------------------*/

	function get_db_structure()
	{
		$vers = array();

		if ( $this->ipsclass->DB->field_exists ( "upgrade_id", "upgrade_history" ) )
		{
			$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'upgrade_history', 'order' =>  'upgrade_version_id ASC' ) );
			$this->ipsclass->DB->simple_exec();

			while( $r = $this->ipsclass->DB->fetch_row() )
			{
				$vers[ $r['upgrade_version_id'] ] = $r['upgrade_version_id'];
			}
		}

		return $vers;
	}

	function get_db_codepage()
	{
		if ( $this->ipsclass->vars['sql_driver'] == 'mysql' ) 
		{
            $this->ipsclass->DB->sql_get_version();
            
			if ( $this->ipsclass->DB->mysql_version >= 40101 ) 
			{
				$table_scheme = $this->ipsclass->DB->sql_get_table_schematic ( "topics" );
				
				$q = $table_scheme['Create Table'];
				
				$match = array();				
                
				if ( preg_match("/CHARSET=([a-zA-Z0-9]+)/", $q, $match) ) 
				{
					return $match[1];
				}
				
				if ( preg_match("/CHARACTER SET ([a-zA-Z0-9]+)/", $q, $match) )
				{
					return $match[1];
				}
			} 
		}
		
		return false;
	}
		
	/*-------------------------------------------------------------------------*/
	// Get dir structure..
	/*-------------------------------------------------------------------------*/

	function get_dir_structure()
	{
		$return = array();

		//------------------------------------------
 		// Get the folder names
 		//------------------------------------------

 		$dh = opendir( INS_ROOT_PATH.'installfiles' );

 		while ( false !== ( $file = readdir( $dh ) ) )
 		{
			if ( is_dir( INS_ROOT_PATH.'installfiles/'.$file ) )
			{
				if ( $file != "." && $file != ".." )
				{
					if ( strstr( $file, 'upg_' ) )
					{
						$tmp = str_replace( "upg_", "", $file );
						$return[ $tmp ] = $tmp;
					}
				}
			}
 		}

 		closedir( $dh );

 		sort($return);

 		return $return;
	}

	
	/**
	 * application_installer::cache_and_cleanup
	 * 
	 * Final install step, allows for any remaining app specific functions
	 *
	 */		
	function cache_and_cleanup()
	{
		//-------------------------------------------------------------
		// BBCODE
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша BB-кодов...";
		
		$this->ipsclass->cache['bbcode'] = array();
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'custom_bbcode' ) );
		$bbcode = $this->ipsclass->DB->simple_exec();
	
		while ( $r = $this->ipsclass->DB->fetch_row($bbcode) )
		{
			$this->ipsclass->cache['bbcode'][] = $r;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'bbcode', 'array' => 1, 'deletefirst' => 1 ) );
		
		//-------------------------------------------------------------
		// Forum cache
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша форумов...";
		
		$this->ipsclass->update_forum_cache();
			
		//-------------------------------------------------------------
		// Group Cache
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша групп...";
		
		$this->ipsclass->cache['group_cache'] = array();
	
		$this->ipsclass->DB->simple_construct( array( 'select' => "*",
									  'from'   => 'groups'
							 )      );
		
		$this->ipsclass->DB->simple_exec();
		
		while ( $i = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['group_cache'][ $i['g_id'] ] = $i;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );
		
		//-------------------------------------------------------------
		// Systemvars
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша системных переменных";
		
		$this->ipsclass->cache['systemvars'] = array();
		
		$result = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'count(*) as cnt', 'from' => 'mail_queue' ) );
		
		$this->ipsclass->cache['systemvars']['mail_queue'] = intval( $result['cnt'] );
		$this->ipsclass->cache['systemvars']['task_next_run'] = time() + 3600;
		
		$this->ipsclass->update_cache( array( 'name' => 'systemvars', 'array' => 1, 'deletefirst' => 1 ) );
		
		//-----------------------------------------
		// Moderators
		//-----------------------------------------
		
		$output[] = "Создание кеша модераторов форумов";
		
		$this->ipsclass->cache['moderators'] = array();
		
		require_once( ROOT_PATH.'sources/action_admin/moderator.php' );
		$mod           =  new ad_moderator();
		$mod->ipsclass =& $this->ipsclass;
		
		$mod->rebuild_moderator_cache();
		
		//-----------------------------------------
		// Cal events / Birthdays
		//-----------------------------------------
		
		$output[] = "Создание кеша дней рождений/событий...";
		
		require_once( ROOT_PATH . 'sources/action_admin/calendars.php' );
		$calendars           =  new ad_calendars();
		$calendars->ipsclass =& $this->ipsclass;
		
		$calendars->calendar_rebuildcache( 0 );
		$calendars->calendars_rebuildcache( 0 );
				
		//-------------------------------------------------------------
		// Ranks
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша званий пользователей...";
		
		$this->ipsclass->cache['ranks'] = array();
	
		$this->ipsclass->DB->simple_construct( array( 'select' => 'id, title, pips, posts',
													  'from'   => 'titles',
													  'order'  => "posts DESC",
											)      );
							
		$this->ipsclass->DB->simple_exec();
					
		while ($i = $this->ipsclass->DB->fetch_row())
		{
			$this->ipsclass->cache['ranks'][ $i['id'] ] = array(
																'TITLE' => $i['title'],
																'PIPS'  => $i['pips'],
																'POSTS' => $i['posts'],
															  );
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'ranks', 'array' => 1, 'deletefirst' => 1 ) );
			
		
		//-------------------------------------------------------------
		// SETTINGS
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша настроек форума...";
		
		require_once( ROOT_PATH . 'sources/action_admin/settings.php' );
		$settings           =  new ad_settings();
		$settings->ipsclass =& $this->ipsclass;
		
		$settings->setting_rebuildcache();
		
		//-------------------------------------------------------------
		// EMOTICONS
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша смайликов...";
		
		require_once( ROOT_PATH . 'sources/action_admin/emoticons.php' );
		$emoticons           =  new ad_emoticons();
		$emoticons->ipsclass =& $this->ipsclass;
		
		$emoticons->emoticon_rebuildcache();
		
		//-------------------------------------------------------------
		// LANGUAGES
		//-------------------------------------------------------------
		
		$output[] = "Создание языкового кеша...";
		
		$this->ipsclass->cache['languages'] = array();
	
		$this->ipsclass->DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
		$this->ipsclass->DB->simple_exec();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['languages'][] = $r;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );
			
		//-------------------------------------------------------------
		// ATTACHMENT TYPES
		//-------------------------------------------------------------
		
		$output[] = "Создание кеша типов файлов...";
			
		$this->ipsclass->cache['attachtypes'] = array();
	
		$this->ipsclass->DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$this->ipsclass->DB->simple_exec();
	
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			$this->ipsclass->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}
		
		$this->ipsclass->update_cache( array( 'name' => 'attachtypes', 'array' => 1, 'deletefirst' => 1 ) );

		return $output;
	}
	
	/**
	 * application_installer::write_codepage_configuration
	 * 
	 * Writes the configuration codepage for this app
	 *
	 */		
	function write_codepage_configuration()
	{
        $this->ipsclass->vars['mysql_codepage'] = $this->saved_data['mysql_codepage'];
        
		$core_conf = $this->read_file( ROOT_PATH. "conf_global.php" );
		$cp = '$INFO['."'mysql_codepage'".']'."\t\t\t=\t'".$this->saved_data['mysql_codepage']."';\n";		
		$core_conf = str_replace("\n".'?'.'>', $cp, $core_conf);
		$core_conf .= "\n".'?'.'>';

		/* Write Configuration Files */
		$output = 'Обновление конфигурационных файлов...<br />';
		
		$this->write_file( ROOT_PATH. "conf_global.php"  , $core_conf );
        
		return $output;
	}
}

?>