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
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > TASK SCRIPT: Test
|   > Script written by Matt Mecham
|   > Date started: 28th January 2004
|
+--------------------------------------------------------------------------
*/

//-----------------------------------------
// THIS TASKS OPERATIONS:
// Prunes back subscribed topics...
//+--------------------------------------------------------------------------

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
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
		// Delete old subscriptions
		//-----------------------------------------
		
		$deleted = 0;
		$trids   = array();
		
		if ($this->ipsclass->vars['subs_autoprune'] > 0)
 		{
			$time = time() - ($this->ipsclass->vars['subs_autoprune'] * 86400);
			
			$this->ipsclass->DB->cache_add_query( 'ucp_tracker_prune', array( 'time' => $time ) );
			$this->ipsclass->DB->cache_exec_query();
			
			while ( $r = $this->ipsclass->DB->fetch_row() )
			{
				$trids[] = $r['trid'];
			}
			
			if (count($trids) > 0)
			{
				$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'tracker', 'where' => "trid IN (".implode(",",$trids).")" ) );
			}
			
			$deleted = intval( count($trids) );
 		}
 		
 		//-----------------------------------------
 		// Remove read topics
 		//-----------------------------------------
 		
 		$this->ipsclass->vars['db_topic_read_cutoff'] = intval($this->ipsclass->vars['db_topic_read_cutoff']);
 		
 		if ( $this->ipsclass->vars['db_topic_read_cutoff'] > 0 )
 		{
 			$time = time() - ( $this->ipsclass->vars['db_topic_read_cutoff'] * 86400 );
 			
 			$this->ipsclass->DB->simple_exec_query( array( 'delete' => 'topics_read', 'where' => "read_date < $time" ) );
 			
 			$topics_deleted = $this->ipsclass->DB->get_affected_rows();
 		}

		//-----------------------------------------
		// Delete old unattached uploads
		//-----------------------------------------
		
		$time_cutoff = time() - 7200;
		$deadid      = array();
		
		$this->ipsclass->DB->simple_construct( array( "select" => '*', 'from' => 'attachments',  'where' => "attach_rel_id=0 AND attach_date < $time_cutoff") );
		$this->ipsclass->DB->simple_exec();
		
		while( $killmeh = $this->ipsclass->DB->fetch_row() )
		{
			if ( $killmeh['attach_location'] )
			{
				@unlink( $this->ipsclass->vars['upload_dir']."/".$killmeh['attach_location'] );
			}
			if ( $killmeh['attach_thumb_location'] )
			{
				@unlink( $this->ipsclass->vars['upload_dir']."/".$killmeh['attach_thumb_location'] );
			}
			
			$deadid[] = $killmeh['attach_id'];
		}
		
		$_attach_count = count( $deadid );
		
		if ( $_attach_count )
		{
			$this->ipsclass->DB->simple_construct( array( 'delete' => 'attachments', 'where' => "attach_id IN(".implode( ",",$deadid ).")" ) );
			$this->ipsclass->DB->simple_exec();
		}
		
		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------
		
		$this->class->append_task_log( $this->task, "�������: $_attach_count ���������� ������������� ������, $deleted ������������� �������� �� ���� � {$topics_deleted} ������ ����� ��������� ���" );
		
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