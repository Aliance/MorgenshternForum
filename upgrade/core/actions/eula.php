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
|   Action controller for EULA page
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Action controller for EULA page
 */

class action_eula
{
	var $install;
	
	function action_eula( & $install )
	{
		$this->install =& $install;
	}
	
	function run()
	{
		if( isset($this->install->ipsclass->input['mysql_codepage']) AND $this->install->ipsclass->input['mysql_codepage'] != 'none' )
		{
			$this->install->saved_data['mysql_codepage'] = $this->install->ipsclass->input['mysql_codepage'];
		} 
		
		/* Page Output */
		$this->install->template->append( $this->install->template->eula_page( nl2br($this->install->product_license) ) );
		$this->install->template->next_action = '?p=install';
	}
}

?>