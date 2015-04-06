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
|
|   > LOG IN MODULE: EXTERNAL DB (IPB AUTH)
|   > Script written by Matt Mecham
|   > Date started: 11:33 Monday 13th June 2005 (AD)
|
+---------------------------------------------------------------------------
| NOTES:
| This module is part of the authentication suite of modules. It's designed
| to enable different types of authentication.
|
| RETURN CODES
| 'ERROR': Error, check array: $class->auth_errors
| 'NO_USER': No user found in LOCAL record set but auth passed in REMOTE dir
| 'WRONG_AUTH': Wrong password or username
| 'SUCCESS': Success, user and password matched
|
+---------------------------------------------------------------------------
*/

/*-------------------------------------------------------------------------*/
// Off we go!
/*-------------------------------------------------------------------------*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class login_method extends login_core
{
	# Globals
	var $ipsclass;
	
	
	/*-------------------------------------------------------------------------*/
	// Constructor
	/*-------------------------------------------------------------------------*/
	
	function login_method()
	{
		
	}
	
	/*-------------------------------------------------------------------------*/
	// Compare passwords
	// $password is the plain text password
	// $remote_member is the DB row from the remote DB table
	// Return TRUE or FALSE
	/*-------------------------------------------------------------------------*/
	
	function _compare_passwords( $password, $remote_member )
	{
		if ( md5($password) == $remote_member[ REMOTE_FIELD_PASS ] )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Authentication
	/*-------------------------------------------------------------------------*/
	
	function authenticate( $username, $password )
	{
		if( $this->is_admin_auth AND $this->login_method['login_type'] == 'passthrough' )
		{
			// Try local first, so as to not block locally created admins
			$this->admin_auth_local( $username, $password );
			
  			if ( $this->return_code == 'SUCCESS' )
  			{
  				return;
  			}
		}
		
		/*-------------------------------------------------------------------------*/
		// SET UP: Edit DB details to suit
		/*-------------------------------------------------------------------------*/
		
		define( 'REMOTE_DB_SERVER'  , $this->login_conf['REMOTE_DB_SERVER']   );
		define( 'REMOTE_DB_PORT'    , $this->login_conf['REMOTE_DB_PORT']     );
		define( 'REMOTE_DB_DATABASE', $this->login_conf['REMOTE_DB_DATABASE'] );
		define( 'REMOTE_DB_USER'    , $this->login_conf['REMOTE_DB_USER']     );
		define( 'REMOTE_DB_PASS'    , $this->login_conf['REMOTE_DB_PASS']     );
		
		/*-------------------------------------------------------------------------*/
		// SET UP: Edit these DB tables to suit
		/*-------------------------------------------------------------------------*/
		
		define( 'REMOTE_TABLE_NAME'  , $this->login_conf['REMOTE_TABLE_NAME']  );
		define( 'REMOTE_FIELD_NAME'  , $this->login_conf['REMOTE_FIELD_NAME']  );
		define( 'REMOTE_FIELD_PASS'  , $this->login_conf['REMOTE_FIELD_PASS']  );
		define( 'REMOTE_EXTRA_QUERY' , $this->login_conf['REMOTE_EXTRA_QUERY'] );
		define( 'REMOTE_TABLE_PREFIX', $this->login_conf['REMOTE_TABLE_PREFIX'] );
		
		//-----------------------------------------
		// GET DB object
		//-----------------------------------------
		
		if ( ! class_exists( 'db_main' ) )
		{ 
			require_once( KERNEL_PATH.'class_db.php' );
			require_once( KERNEL_PATH.'class_db_'.$this->ipsclass->vars['sql_driver'].".php" );
		}
		
		$classname = "db_driver_".$this->ipsclass->vars['sql_driver'];
		
		$RDB = new $classname;
		
		$RDB->obj['sql_database']         = REMOTE_DB_DATABASE;
		$RDB->obj['sql_user']             = REMOTE_DB_USER;
		$RDB->obj['sql_pass']             = REMOTE_DB_PASS;
		$RDB->obj['sql_host']             = REMOTE_DB_SERVER;
		$RDB->obj['sql_tbl_prefix']       = REMOTE_TABLE_PREFIX;
		$RDB->obj['use_shutdown']         = 0;
		$RDB->obj['force_new_connection'] = 1;
		
		//--------------------------------
		// Get a DB connection
		//--------------------------------
		
		$RDB->connect();
		
		//-----------------------------------------
		// Get member from remote DB
		//-----------------------------------------
		
		$remote_member = $RDB->build_and_exec_query( array( 'select' => '*',
														    'from'   => REMOTE_TABLE_NAME,
														    'where'  => REMOTE_FIELD_NAME.'="'.addslashes($username).'" '.REMOTE_EXTRA_QUERY ) );
		
		$RDB->close_db();
		
		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( ! $remote_member[ REMOTE_FIELD_NAME ] )
		{
			$this->return_code = 'NO_USER';
			return;
		}
		
		//-----------------------------------------
		// Check password
		//-----------------------------------------
		
		if ( ! $this->_compare_passwords( $password, $remote_member ) )
		{
			$this->return_code = 'WRONG_AUTH';
			return;
		}
		
		//-----------------------------------------
		// Still here? Then we have a username
		// and matching password.. so get local member
		// and see if there's a match.. if not, create
		// one!
		//-----------------------------------------
		
		$this->_load_member( $username );
				
		if ( $this->member['id'] )
		{
			$this->return_code = 'SUCCESS';
			return;
		}
		else
		{
			//-----------------------------------------
			// Got no member - but auth passed - create?
			//-----------------------------------------
			
			if ( $this->allow_create )
			{
				$this->create_local_member( $username, $password );
			}
			else
			{
				$this->return_code = 'NO_USER';
				return;
			}
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Load member from DB
	/*-------------------------------------------------------------------------*/
	
	function _load_member( $username )
	{
		$this->ipsclass->DB->cache_add_query( 'login_getmember', array( 'username' => strtolower($username) ) );
		$this->ipsclass->DB->cache_exec_query();
	
		$this->member = $this->ipsclass->DB->fetch_row();
	}
}

?>