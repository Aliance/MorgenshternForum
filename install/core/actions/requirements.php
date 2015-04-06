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
|   Action controller for requirements page
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Action controller for requirements page
 */

class action_requirements
{
	var $install;
	
	function action_requirements( & $install )
	{
		$this->install =& $install;
	}
	
	function run()
	{
		/* Set App Specific Requirements */
		$this->install->set_requirements();
		
		/* Page Output */
		$this->install->template->append( $this->install->template->requirements_page( $this->install->version_php_min, $this->install->version_mysql_min ) );		
		
		/* Check Requirements */
		$errors = $this->install->check_requirements();

		/* Check for errors */	
		if( count( $errors ) )
		{
			$this->install->template->warning( $errors );	
			$this->install->template->next_action = 'disabled';
		}
		else 
		{
			$this->install->template->next_action = '?p=eula';
		}
	}
}

?>