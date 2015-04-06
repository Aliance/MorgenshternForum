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
|   > $Date: 2006-08-01 17:02:55 +0100 (Tue, 01 Aug 2006) $
|   > $Revision: 425 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Settings Plug In
|   > Module written by Matt Mecham
|   > Date started: 27th September 2006
|
+--------------------------------------------------------------------------
*/

/**
* Main content
*
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}

class setting_trashcan
{
	/**
	* Global IPSCLASS
	* @var	object
	*/
	var $ipsclass;
	
	/*-------------------------------------------------------------------------*/
	// Pre-parse
	/*-------------------------------------------------------------------------*/
	
	/**
	* Allow one to modify the values before the setting is parsed
	* This function is passed an array of settings of which the index
	* of the array is the configuration ID.
	* array( index => array(
	* 						  conf_id
	* 						  conf_title
	* 						  conf_description
	* 						  conf_type
	* 						  conf_key
	* 						  conf_value
	* 						  conf_default
	* 						  conf_extra
	* 						  conf_evalphp ) );
    *
	*
	* @param	array  Settings
	* @param    array  Settings
	*
	*/
	function settings_pre_parse( $settings=array() ) 
	{
		
		//-----------------------------------------
		// Return
		//-----------------------------------------
	
		return $settings;
	}
	
	/*-------------------------------------------------------------------------*/
	// Post-parse
	/*-------------------------------------------------------------------------*/
	
	/**
	* Allow one to modify the values just before being saved to the DB
	* If an error occurs, please set the relevant index's '_error' flag.
	*
	*
	* For example:
	* if ( ! $true )
	* {
	*	$settings[ $conf_id ]['_error'] = 'Not true!'
	* }
	* This will then show the form again with the error in the relevant
	* setting box.
	*
	* The user entered value for the key is held in $settings[ $conf_id ]['_value']
	*
	* This function is passed an array of settings of which the index
	* of the array is the configuration ID.
	* array( index => array(
	* 						  conf_id
	* 						  conf_title
	* 						  conf_description
	* 						  conf_type
	* 						  conf_key
	* 						  conf_value
	* 						  conf_default
	* 						  conf_extra
	* 						  _error
	*						  _value ) );
    *
	*
	* @param	array  Settings
	* @param    array  Settings
	*
	*/
	function settings_post_parse( $settings=array() ) 
	{
		//-----------------------------------------
		// Check 'em
		//-----------------------------------------
		
		foreach( $settings as $id => $data )
		{
			//-----------------------------------------
			// Check to make sure we haven't got a category
			// forum..
			//-----------------------------------------
			
			if ( $data['conf_key'] == 'forum_trash_can_id' )
			{
				$forum = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																		   'from'   => 'forums',
																		   'where'  => 'id='.intval( $data['_value'] ) ) );
				
				if ( ! $forum['sub_can_post'] OR $forum['redirect_on'] )
				{
					$settings[ $id ]['_error'] = '�� �� ������ ������� ��������� ��� �����-�������� � �������� �������.';
				}
			}
		}
		
		//-----------------------------------------
		// Return
		//-----------------------------------------
		
		return $settings;
	}
	
}


?>