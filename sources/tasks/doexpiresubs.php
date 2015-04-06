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
|   > TASK SCRIPT: Actually expire subs
|   > Script written by Matt Mecham
|   > Date started: 12th July 2005 (Tuesday)
|
+--------------------------------------------------------------------------
*/

/*-------------------------------------------------------------------------*/
// THIS TASKS OPERATIONS:
// Actually expire subscriptions
/*-------------------------------------------------------------------------*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
    exit();
}

class task_item
{
	var $class     = "";
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
		
		$expire_ids = array();
		
		//-----------------------------------------
		// Get all subs to expire
		//-----------------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*',
													  'from'   => 'subscription_trans',
													  'where'  => "subtrans_state='paid' AND subtrans_end_date < ".time()
											 )      );
											 
		$outer = $this->ipsclass->DB->simple_exec();
		
		while ( $row = $this->ipsclass->DB->fetch_row( $outer ) )
		{
			$query = array( "sub_end" => 0 );
			
			if ( $row['subtrans_old_group'] > 0 )
			{
				//---------------------------------------
				// Group still exist?
				//---------------------------------------
				
				if ( is_array( $this->ipsclass->cache['group_cache'][ $row['subtrans_old_group'] ] ) )
				{
					$query['mgroup'] = $row['subtrans_old_group'];
				}
				else
				{
					//---------------------------------------
					// Group has been deleted, reset back to base member group
					//---------------------------------------
					
					$query['mgroup'] = $this->ipsclass->vars['member_group'];
				}
			}
			
			$expire_ids[ $row['subtrans_id'] ] = $row['subtrans_id'];
			
			//---------------------------------------
			// Update member
			//---------------------------------------
			
			$this->ipsclass->DB->do_update( 'members', $query, "id=".intval($row['subtrans_member_id']) );
		}
		
		//---------------------------------------
		// Update rows...
		//---------------------------------------
		
		if ( count( $expire_ids ) )
		{
			$this->ipsclass->DB->do_update( 'subscription_trans', array( 'subtrans_state' => "expired" ), "subtrans_id IN (".implode(",",$expire_ids ).")" );
		}
		
		//-----------------------------------------
		// Unlock Task: DO NOT MODIFY!
		//-----------------------------------------
		
		$this->class->append_task_log( $this->task, "Заврешено " . intval(count($expire_ids))." платных подписок" );
		
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