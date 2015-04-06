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
|   > LOG IN MODULE: INTERNAL (IPB AUTH)
|   > Script written by Matt Mecham
|   > Date started: 12:25 Fri. 4th February 2005 (AD)
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
| EXAMPLE USAGE
|
| $class = new login_method();
| $class->is_admin_auth = 0; // Boolean (0,1) Use different queries if desired
|							 // if logging into CP.
| $class->allow_create = 0;
| // $allow_create. Boolean flag (0,1) to tell the module whether its allowed
| // to create a member in the IPS product's database if the user passed authentication
| // but don't exist in the IPS product's database. Optional.
|
| $return_code = $class->authenticate( $username, $plain_text_password );
|
| if ( $return_code == 'SUCCESS' )
| {
|     print $class->member['member_name'];
| }
| else
| {
| 	  print "NO USER";
| }
+---------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class login_method extends login_core
{
	# Globals
	var $ipsclass;
	
	/**
	* Make admin use different auth?
	* @var int
	*/
	var $allow_admin_login = 1;
	
	/**
	* Is admin log in ?
	* @var int
	*/
	var $is_admin_auth     = 0;
	
	var $api_server;

	
	/*-------------------------------------------------------------------------*/
	// Constructor
	/*-------------------------------------------------------------------------*/
	
	function login_method()
	{
		
	}
	
	/*-------------------------------------------------------------------------*/
	// Authentication
	/*-------------------------------------------------------------------------*/
	
	function authenticate( $username, $password )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$md5_once_pass = md5( $password );
		
		//-----------------------------------------
		// ADMIN log in?
		//-----------------------------------------
		
		if ( $this->is_admin_auth AND $this->login_method['login_type'] == 'passthrough' )
		{
			// Try local first, so as to not block locally created admins
			$this->admin_auth_local( $username, $password );
			
  			if ( $this->return_code == 'SUCCESS' )
  			{
  				return;
  			}
		}
		
		//-----------------------------------------
		// Get product ID and code from API
		//-----------------------------------------
		
		$converge = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																	  'from'   => 'converge_local',
																	  'where'  => 'converge_active=1' ) );
																	
		if ( ! $converge['converge_api_code'] )
		{
			$this->return_code = 'WRONG_AUTH';
			return;
		}
		
		//-----------------------------------------
		// Auth against converge...
		//-----------------------------------------
		
		if ( ! is_object( $this->api_server ) )
		{
			require_once( KERNEL_PATH . 'class_api_server.php' );
			$this->api_server = new class_api_server();
		}
		
		$request = array( 'auth_key'          => $converge['converge_api_code'],
						  'product_id'        => $converge['converge_product_id'],
						  'email_address'     => $username,
						  'md5_once_password' => $md5_once_pass
						);

		$url     = $converge['converge_url'] . '/converge_master/converge_server.php';

		//-----------------------------------------
		// Send request
		//-----------------------------------------
		
		$this->api_server->auth_user = $converge['converge_http_user'];
		$this->api_server->auth_pass = $converge['converge_http_pass'];
		
		$this->api_server->api_send_request( $url, 'convergeAuthenticate', $request );

		//-----------------------------------------
		// Handle errors...
		//-----------------------------------------

		if ( count( $this->api_server->errors ) )
		{
			$this->return_code = 'WRONG_AUTH';
			return;
		}
		
		//-----------------------------------------
		// Get member...
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array(
												  'select'   => 'm.*',
												  'from'     => array( 'members' => 'm' ),
												  'where'    => "email='".strtolower($username)."'",
												  'add_join' => array( 0 => array( 'select' => 'g.*',
																				   'from'   => array( 'groups' => 'g' ),
																				   'where'  => 'm.mgroup=g.g_id',
																				   'type'   => 'inner'
																				 )
																	)
										 )     );
												 
		$this->ipsclass->DB->exec_query();

		$this->member      = $this->ipsclass->DB->fetch_row();
		
		$this->return_code = $this->api_server->params['response'];
		return;
		
	}
}

?>