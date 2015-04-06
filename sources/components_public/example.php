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
|   > MODULE PUBLIC FILE: EXAMPLE
|   > Module written by Matt Mecham
|   > Date started: Fri 12th August 2005 (17:16)
|
+--------------------------------------------------------------------------
*/

/**
* MODULE: Public Example File (IPB 3.0 Methods)
* "modules" is depreciated in IPB 3.0
*
* @package		InvisionPowerBoard
* @subpackage	Components
* @author  		Matt Mecham
* @version		2.1
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

/**
* MODULE: Public Example File (IPB 3.0 Methods)
*
* This class must ALWAYS be called "component_public"
*
* @package		InvisionPowerBoard
* @subpackage	Components
* @author  		Matt Mecham
* @version		2.1
*/
class component_public
{
	/**
	* IPSclass object
	*
	* @var object
	*/
	var $ipsclass;
	
	
	/**
	* Main function that's run from index.php
	*
	*/
	function run_component()
	{
		print "hello";
		exit();
	}
		
	

}

?>