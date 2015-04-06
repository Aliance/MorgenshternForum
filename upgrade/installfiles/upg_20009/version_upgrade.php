<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > IPB UPGRADE MODULE:: IPB 2.0.0 PF3 -> PF 4
|   > Script written by Matt Mecham
|   > Date started: 23rd April 2004
|   > "So what, pop is dead - it's no great loss.
	   So many facelifts, it's face flew off"
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class version_upgrade
{
	var $install;

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function version_upgrade( & $install )
	{
		$this->install = & $install;
	}

	/*-------------------------------------------------------------------------*/
	// Auto run..
	/*-------------------------------------------------------------------------*/

	function auto_run()
	{
		//--------------------------------
		// Upgrade templates
		//--------------------------------

		require_once( ROOT_PATH.'upgrade/installfiles/upg_20009/components.php' );

		$this->install->ipsclass->DB->do_update( 'skin_sets', array( 'set_css'           => $CSS,
										    'set_cache_css'     => $CSS,
										    'set_wrapper'       => $WRAPPER,
										    'set_cache_wrapper' => $WRAPPER,
										  ), 'set_skin_set_id=1' );

		$this->install->message = "Общий шаблон форума и CSS обновлены...";
		return true;
	}

}


?>