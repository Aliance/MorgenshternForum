<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.1.7
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Wednesday 27th of September 2006 08:13:32 AM
|   Release: 2871a4c8b602386260eeb8bf9da57e29
|   Licence Info: http://www.invisionboard.com/?license
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2005-10-10 14:08:54 +0100 (Mon, 10 Oct 2005) $
|   > $Revision: 23 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > MODULE INIT FILE
|   > Module written by Matt Mecham
|   > Date started: Wed 20th April 2005 (16:28)
|
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

/*
+--------------------------------------------------------------------------
|   This module has one function:
|   run_init: Do any work you want to do before the caches are loaded and
|             processed
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// This must always be 'component_init'
//-----------------------------------------

class component_init
{
	var $ipsclass;
	
	/*-------------------------------------------------------------------------*/
	// run_init
	// Do any work before the caches are loaded.
	// ADD to $this->ipsclass->cache_array()
	// DO NOT overwrite it or call $this->ipsclass->cache_array = array(...);
	// As the array has already been started off by IPB in index.php
	/*-------------------------------------------------------------------------*/
	
	function run_init()
	{
		$this->ipsclass->cache_array[] = 'birthdays';
		$this->ipsclass->cache_array[] = 'calendar';
	}
		
	

}

?>