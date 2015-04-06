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
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
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
		$this->ipsclass->cache_array[] = 'chatting';
	}
		
	

}

?>