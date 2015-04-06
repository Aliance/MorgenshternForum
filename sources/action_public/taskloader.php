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
|   > Task loader module
|   > Module written by Matt Mecham
|   > Date started: 28th January 2004
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Thu 20 May 2004
|   > Quality Checked: Wed 15 Sept. 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class taskloader
{
	# Global
	var $ipsclass;
	
	/*-------------------------------------------------------------------------*/
	// Run..
	/*-------------------------------------------------------------------------*/
	
    function auto_run()
    {
    	@set_time_limit(1200);
    	
    	//-----------------------------------------
		// Require and run
		//-----------------------------------------
		
		chdir( ROOT_PATH );
		$ROOT_PATH = getcwd() .'/';
		
		require_once( ROOT_PATH.'sources/lib/func_taskmanager.php' );
		
		$functions            =  new func_taskmanager();
		$functions->ipsclass  =& $this->ipsclass;
    	$functions->root_path = $ROOT_PATH;
    	
    	//-----------------------------------------
		// Check shutdown functions
		//-----------------------------------------
		
    	if ( USE_SHUTDOWN )
		{
			register_shutdown_function( array( &$functions, 'run_task') );
		}
    	else
    	{
    		$functions->run_task();
    	}
    	
    	if ( $functions->type != 'cron' )
    	{
    		//-----------------------------------------
    		// Print out the 'blank' gif
    		//-----------------------------------------
    		
    		@header( "Content-Type: image/gif" );
    		print base64_decode( "R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" );
    	}
 	}
}

?>