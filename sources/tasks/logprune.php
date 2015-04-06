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
|   > $Date: 2006-05-24 13:53:54 -0400 (Wed, 24 May 2006) $
|   > $Revision: 275 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > TASK SCRIPT: Prune logs
|   > Script written by Matt Mecham
|   > Date started: 28th January 2004
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// THIS TASKS OPERATIONS:
// Prunes logs based on ACP settings
//+--------------------------------------------------------------------------

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
		// Spider Logs
		//-----------------------------------------		
		
		if( $this->ipsclass->vars['ipb_prune_spider'] )
		{
			$this->ipsclass->DB->do_delete( "spider_logs" );
		}
		
		//-----------------------------------------
		// Task Logs
		//-----------------------------------------		
		
		if( $this->ipsclass->vars['ipb_prune_task'] )
		{
			$this->ipsclass->DB->do_delete( "task_logs" );
		}
		
		//-----------------------------------------
		// Admin Logs
		//-----------------------------------------		
		
		if( $this->ipsclass->vars['ipb_prune_admin'] )
		{
			$this->ipsclass->DB->do_delete( "admin_logs" );
		}
		
		//-----------------------------------------
		// Mod Logs
		//-----------------------------------------		
		
		if ( $this->ipsclass->vars['ipb_prune_mod'] )
		{
			$this->ipsclass->DB->do_delete( "moderator_logs" );
		}
		
		//-----------------------------------------
		// Email Logs
		//-----------------------------------------		
		
		if ( $this->ipsclass->vars['ipb_prune_email'] )
		{
			$this->ipsclass->DB->do_delete( "email_logs" );
		}
		
		//-----------------------------------------
		// Email Error Logs
		//-----------------------------------------		
		
		if ( $this->ipsclass->vars['ipb_prune_emailerror'] )
		{
			$this->ipsclass->DB->do_delete( "mail_error_logs" );
		}		
		
		//-----------------------------------------
		// SQL Error Logs
		// --Only prune older than 30 days
		//-----------------------------------------		
		
		if ( $this->ipsclass->vars['ipb_prune_sql'] )
		{
			if( $dh = @opendir( ROOT_PATH . 'cache' ) )
			{
				while( false !== ( $file = readdir( $dh ) ) )
				{
					if( preg_match( "#^sql_error_log_(\d+)_(\d+)_(\d+).cgi$#", $file, $matches ) )
					{
						$month 	= $matches[1];
						$day	= $matches[2];
						$year	= $matches[3];
						
						if( $year <= date( "y" ) )
						{
							$how_old = mktime( 0, 0, 0, $month, $day, $year );
							
							if( time() - $how_old > 2592000 )
							{
								@unlink( ROOT_PATH . 'cache/' . $file );
							}
						}
					}
				}
			}
		}	
		
		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------
		
		$this->class->append_task_log( $this->task, 'Журналы сообщений очищены' );
		
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