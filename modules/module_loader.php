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
|                  http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2005-10-10 14:03:20 +0100 (Mon, 10 Oct 2005) $
|   > $Revision: 22 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > MODULE LOADER
|   > Module written by Matt Mecham
|   > Date started: Thu 14th April 2005 (17:55)
|
+--------------------------------------------------------------------------
| USAGE:
| ------
|
| This is a module loader file
| example: index.php?act=module&module=register&var=foo
| 
| Looks for a file called "mod_register.php" and runs it
|
+--------------------------------------------------------------------------
*/

class module_loader
{
	var $ipsclass;
	var $class;
	var $module;
	
	function run_loader()
	{
		$this->module = $this->ipsclass->txt_alphanumerical_clean($this->ipsclass->input['module']);
			
		if ( $this->module == "" )
		{
			$this->_return_dead();
		}
		
		//----------------------------------
		// Does module file exist?
		//----------------------------------
		
		if ( ! @file_exists( ROOT_PATH.'modules/mod_'.$this->module.'.php' ) )
		{
			$this->_return_dead();
		}
		
		//----------------------------------
		// Require and run
		//----------------------------------
		
		require_once( ROOT_PATH.'modules/mod_'.$this->module.'.php' );
		
		$mod_run           = new module();
		$mod_run->ipsclass =& $this->ipsclass;
		$mod_run->run_module();
		
		exit();
	}
	
	//------------------------------------------
	// _return_dead
	// 
	// Return to board index
	//
	//------------------------------------------
	
	function _return_dead()
	{
		header("Location: ".$this->ipsclass->base_url);
		
		exit();
	}
	
}


?>