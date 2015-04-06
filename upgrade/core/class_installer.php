<?php
/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   Invision Power Board INSTALLER
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
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
+----------------------------------------------------------------------------
|   Invision Installer Framework
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Invision Installer Framework
 */
 
class class_installer
{
	var $product_name     = '';
	var $product_license  = '';
	var $product_version  = '';
	var $product_key      = '';
	
	var $build_id         = '';
	
	var $version_php_min   = '';
	var $version_mysql_min = '';
	
	var $required_dirs  = array();
	var $required_files = array();
	
	var $writeable_dirs  = array();
	var $writeable_files = array();
	
	/**
	 * class_installer::set_product_info
	 * 
	 * Set basic information about the product being installed
	 *
	 * @param  string $name
	 * @param  string $license
	 * @param  array  $versions
	 * @param  string $key
	 * @return void
	 */
	function set_product_info( $name, $license, $versions, $key )
	{
		$this->product_name     = $name;
		$this->product_license  = $license;
		$this->product_versions = $versions;
		$this->product_key      = $key;
	}
	
	/**
	 * class_installer:set_software_versions
	 *
	 * Set required software versions of php and mysql
	 * 
	 * @param string $php_min      Example: 3.0.0
	 * @param string $mysql_min    Example: 4.4.0
	 */
	function set_software_versions( $php_min, $mysql_min )
	{
		$this->version_php_min   = explode( '.', $php_min );
		$this->version_mysql_min = $mysql_min;
	}
	
	/**
	 * class_installer::set_required
	 * 
	 * Set what files and directories must be present for an installation
	 *
	 * @param array $dirs
	 * @param array $files
	 */
	function set_required( $dirs, $files )
	{
		$this->required_dirs  = $dirs;
		$this->required_files = $files;
	}
	
	/**
	 * class_installer::set_writeable
	 * 
	 * Set what files and directories must be writeable
	 *
	 * @param array $dirs
	 * @param array $files
	 */
	function set_writeable( $dirs=array(), $files=array() )
	{
		$this->writeable_dirs  = $dirs;
		$this->writeable_files = $files;
	}
	
	/**
	 * class_installer::check_requirements
	 * 
	 * Checks to make sure that all requirements are met, including software versions,
	 * directories, and files.  
	 *
	 * @return array $errors
	 */
	function check_requirements()
	{
		/* Errors Array */
		$errors = array();
		
		/**
		 * PHP Version Checking
		 */
	
		/* Check Min PHP Version */
		$check_php = explode( '.', $this->version_php_min );
		
		if ( PHP_VERSION < $this->version_php_min )
		{
			$errors[] = 'Версия PHP, установленная на вашем сервере, не удовлетворяет системным требованиям продукта. Для продолжения установки необходимо обновить PHP.';
		}
	
		/**
		 * SQL Version Checking
		 */
		
		/**
		 * Required Directories/Files Checking
		 */
		if( count( $this->required_dirs ) )
		{
			foreach( $this->required_dirs as $_d )
			{
				if( ! ( is_dir( $_d) && is_readable( $_d ) ) )
				{
					$errors[] = 'Не возможно найти требуемую директорию: "' . $_d . '"';
				}
			}
		}
		
		if( count( $this->required_files ) )
		{
			foreach( $this->required_files as $_f )
			{
				if( ! ( file_exists( $_f) && is_readable( $_f ) ) )
				{
					$errors[] = 'Не возможно найти требуемый файл: "' . $_f . '"';
				}
			}
		}
		
		/**
		 * Writeable Directories/File Checking
		 */
		if( count( $this->writeable_dirs ) )
		{
			foreach( $this->writeable_dirs as $_d )
			{
				if( ! is_writeable( $_d ) )
				{
					$errors[] = 'Директория: "' . str_replace( INS_DOC_ROOT_PATH, '', $_d ) . '" не доступна для записи, пожалуйста измените права доступа (CHMOD) на 777';
				}
			}
				
			foreach( $this->writeable_files as $_f )
			{
				if( ! is_writeable( $_f ) )
				{
					$errors[] = 'Файл: "' . str_replace( INS_DOC_ROOT_PATH, '', $_f ) . '" не доступен для записи, пожалуйста измените права доступа (CHMOD) на 777';
				}
			}
		}
		
		return $errors;
	}
	
	/**
	 * class_installer::unpack_config
	 * 
	 * Reads and stores the information from the config.xml file
	 *
	 */	
	function read_config()
	{
		// Get Configuration File
		$config = implode( '', file( INS_ROOT_PATH . 'installfiles/config.xml' ) );
		$xml = new class_xml();
	
		$config = $xml->xml_parse_document( $config );
	
		// Set Info
		$this->product_name         = $this->ipsclass->txt_convert_charsets($xml->xml_array['installdata']['package']['name']['VALUE'], 'UTF-8');
		$this->product_license      = $this->ipsclass->txt_convert_charsets($xml->xml_array['installdata']['package']['license']['VALUE'], 'UTF-8');
		$this->product_version      = $this->ipsclass->txt_convert_charsets($xml->xml_array['installdata']['package']['version']['VALUE'], 'UTF-8');
		$this->product_long_version = $this->ipsclass->txt_convert_charsets($xml->xml_array['installdata']['package']['versionlong']['VALUE'], 'UTF-8');
		$this->build_id             = $this->ipsclass->txt_convert_charsets($xml->xml_array['installdata']['package']['build']['VALUE'], 'UTF-8');
	}
	
	/**
	 * class_installer::write_file
	 * 
	 * Simple method for writing a file
	 *
	 * @param string $name Full name, including path, of file to write
	 * @param string $data Contents of file
	 */	
	function write_file( $name, $data )
	{
		$fh = @fopen( $name, "w" );
		@fwrite( $fh, $data, strlen( $data ) );
		@fclose( $fh );		
	}
	
	/**
	 * class_installer::read_file
	 * 
	 * Simple method for reading a file
	 *
	 * @param string $name Full name, including path, of file to read
	 * @return string $content file's body
	 */	
	function read_file( $name )
	{
		$fh = @fopen( $name, "r" );
		$content = @fread( $fh, filesize( $name ) );
		@fclose( $fh );	
		
		return $content;
	}	
	/**
	 * class_installer::create_admin_converge
	 * 
	 * Builds converge entry for admin
	 *
	 * @return integer $id Converge ID
	 */			
	function create_admin_converge()
	{
		/* Build Entry */
		$salt     = $this->ipsclass->converge->generate_password_salt( 5 );
		$passhash = $this->ipsclass->converge->generate_compiled_passhash( $salt, md5( $this->saved_data['admin_pass'] ) );
						   
		$converge = array( 'converge_email'     => $this->saved_data['admin_email'],
						   'converge_joined'    => time(),
						   'converge_pass_hash' => $passhash,
						   'converge_pass_salt' => str_replace( '\\', "\\\\", $salt ),
						 );
							 
		/* Insert */
		$this->ipsclass->DB->do_insert( 'members_converge', $converge );
		
		/* Return ID */
		return $this->ipsclass->DB->get_insert_id();	
	}
	
	/**
	 * class_installer::chmod_dir
	 * 
	 * chmods all files and directories in the given path
	 *
	 * @param string  $path
	 * @param integer $mode
	 */		
	function chmod_dir( $path, $mode )
	{
		/* Open directory */
		$dh = @opendir( $path );
		
		while( false !== ( $f = @readdir( $dh ) ) )
		{
			if( $f != '.' && $f != '..' )
			{
				/* Full file path */
				$_path = $path . '/'. $f;
				
				/* CHMOD directory and contents */
				if( @is_dir( $_path ) )
				{
					@chmod( $_path, $mode );
					$this->chmod_dir( $_path, $mode );
				}
				/* CHMOD file */
				else 
				{
					@chmod( $_path, $mode );
				}					
			}
		}
		
		/* Close directory */
		@closedir( $dh );		
	}
	
	/**
	 * class_installer::process
	 * 
	 * Figures out what action to run
	 *
	 */		
	function process()
	{
		/* Require Action */
		require_once( INS_ROOT_PATH . 'core/actions/' . $this->template->page_current . '.php' );
		
		/* Initialize */
		$class = 'action_' . $this->template->page_current;
		$this->action = new $class( $this );
		$this->action->run();
	}
	
	/*-------------------------------------------------------------------------*/
	// Lock installer
	/*-------------------------------------------------------------------------*/
	/**
	* Lock Installer
	* Writes a lock file
	*
	* @return void
	*/
	function lock_installer()
	{
		//-----------------------------------------
		// Write!
		//-----------------------------------------
		
		$this->write_file( INS_ROOT_PATH . 'installfiles/lock.php', 'LOCKED' );
	}
	
	/*-------------------------------------------------------------------------*/
	// Check lock
	/*-------------------------------------------------------------------------*/
	/**
	* Lock Installer
	* Writes a lock file
	*
	* @return void
	*/
	function check_lock()
	{
		//-----------------------------------------
		// Write!
		//-----------------------------------------
		
		return file_exists( INS_ROOT_PATH . 'installfiles/lock.php' ) ? 1 : 0;
	}

	/*-------------------------------------------------------------------------*/
	// Authorise da membah
	/*-------------------------------------------------------------------------*/

	function get_member()
	{
		$member = array( 'id' => 0 );

		$this->loginkey  = $this->check_md5( $this->saved_data['loginkey']  );
		$this->securekey = $this->check_md5( $this->saved_data['securekey'] );
		$this->member_id = trim(intval($this->saved_data['mid'] ) );

		if ( ! $this->loginkey or ! $this->securekey )
		{
			return $member;
		}
		
		if( !$this->ipsclass->DB->field_exists( 'converge_id', 'members_converge' ) OR  
			( isset($this->saved_data['vid']) AND $this->saved_data['vid'] <= 10003 ) )
		{
			$this->ipsclass->DB->query( "SELECT m.*, g.* FROM ibf_members m
						  LEFT JOIN ibf_groups g ON ( m.mgroup=g.g_id )
						 WHERE m.id='{$this->member_id}'" );

			$member = $this->ipsclass->DB->fetch_row();

			if( !md5($member['password']) == $this->loginkey )
			{
				if( !md5($member['legacy_password']) == $this->loginkey )
				{
					$this->ipsclass->DB->query( "SELECT m.*, g.* FROM ibf_members m
								  LEFT JOIN ibf_groups g ON ( m.mgroup=g.g_id )
								 WHERE m.member_login_key='{$this->loginkey}' and m.id='{$this->member_id}'" );
		
					$member = $this->ipsclass->DB->fetch_row();
				}
			}			
		}
		else
		{
			$this->ipsclass->DB->query( "SELECT m.*, g.* FROM ibf_members m
						  LEFT JOIN ibf_groups g ON ( m.mgroup=g.g_id )
						 WHERE m.member_login_key='{$this->loginkey}' and m.id='{$this->member_id}'" );

			$member = $this->ipsclass->DB->fetch_row();
		}

		return $member;
	}

	/*-------------------------------------------------------------------------*/
	// Check to see if its a 'real' MD5
	/*-------------------------------------------------------------------------*/

	function check_md5($t)
	{
		$t = preg_replace( "#[^a-z0-9]#", "", trim($t) );

		if ( strlen($t) != 32 )
		{
			return '';
		}
		else
		{
			return $t;
		}
	}
	
	/* Overridden by application specific class */
	function pre_process() {}
	function post_process() {}
	function set_requirements() {}
		
}

?>