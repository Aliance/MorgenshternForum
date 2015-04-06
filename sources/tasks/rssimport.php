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
|   > TASK SCRIPT: Update Views
|   > Script written by Matt Mecham
|   > Date started: Monday 9th May 2005 (12:06)
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// THIS TASKS OPERATIONS:
// Imports awaiting RSS articles
//+----------------------------------------

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
    exit();
}

class task_item
{
	var $class     = "";
	var $root_path = "";
	var $task      = "";
	
	/*-------------------------------------------------------------------------*/
	// Our 'auto_run' function
	// ADD CODE HERE
	/*-------------------------------------------------------------------------*/
	
	function run_task()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$feeds_to_update = array();
		
		$time       = time();
		$t_minus_30 = time() - ( 30 * 60 );
		
		//-----------------------------------------
		// Got any to update?
		// 30 mins is RSS friendly.
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select' => '*', 'from' => 'rss_import', 'where' => 'rss_import_enabled=1 AND rss_import_last_import <= '.$t_minus_30 ) );
		$rss_main_query = $this->ipsclass->DB->exec_query();
		
		if ( $this->ipsclass->DB->get_num_rows( $rss_main_query ) )
		{
			require ( ROOT_PATH . 'sources/action_admin/rssimport.php' );
			$admin            =  new ad_rssimport();
			$admin->ipsclass  =& $this->ipsclass;
			
			while( $rss_feed = $this->ipsclass->DB->fetch_row( $rss_main_query ) )
			{
				$this_check = time() - ( $rss_feed['rss_import_time'] * 60 );
				
				if ( $rss_feed['rss_import_last_import'] <= $this_check )
				{
					//-----------------------------------------
					// Set the feeds we need to update...
					//-----------------------------------------
					
					$feeds_to_update[] = $rss_feed['rss_import_id'];
				}
			}
			
			//-----------------------------------------
			// Do the update now...
			//-----------------------------------------
			if ( count( $feeds_to_update ) )
			{
				$admin->rssimport_rebuild_cache( implode( ",", $feeds_to_update), 0, 1 );
			}
		}

		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------

		$this->class->append_task_log( $this->task, 'Выполнено импортирование RSS потоков ('. count($feeds_to_update) .')' );
		
		//-----------------------------------------
		// Unlock Task: DO NOT MODIFY!
		//-----------------------------------------
		
		$this->class->unlock_task( $this->task );
	}
	
	/*-------------------------------------------------------------------------*/
	// register_class
	// LEAVE ALONE
	/*-------------------------------------------------------------------------*/
	
	function register_class(&$class)
	{
		$this->class     = &$class;
		$this->ipsclass  =& $class->ipsclass;
		$this->root_path = $this->class->root_path;
	}
	
	/*-------------------------------------------------------------------------*/
	// pass_task
	// LEAVE ALONE
	/*-------------------------------------------------------------------------*/
	
	function pass_task( $this_task )
	{
		$this->task = $this_task;
	}
	
	
}
?>