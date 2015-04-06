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
|   > TASK SCRIPT: Test
|   > Script written by Matt Mecham
|   > Date started: 28th January 2004
|
+--------------------------------------------------------------------------
*/

//+--------------------------------------------------------------------------
// THIS TASKS OPERATIONS:
// Sends out an email if the subspackage is about to expire
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
		// GET EMAIL CLASS
		//-----------------------------------------
		
		require ( $this->root_path."sources/classes/class_email.php" );
		$this->email = new emailer( $this->root_path );
        $this->email->ipsclass =& $this->ipsclass;
        $this->email->email_init();
        
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$members = array();
		$ids     = array();
		$expired = time() + 86400 + 3600; // Tomorrow + 1 hour
		$now     = time() - 3600;
		
		//-----------------------------------------
		// Get members
		//-----------------------------------------
		
		$this->ipsclass->DB->build_query( array( 'select'   => 'st.subtrans_member_id',
												 'from'     => array( 'subscription_trans' => 'st' ),
												 'where'    => "st.subtrans_state='paid' AND st.subtrans_end_date >= $now AND st.subtrans_end_date <= $expired",
												 'add_join' => array( 0 => array( 'select' => 'm.id, m.name, m.members_display_name, m.email, m.sub_end',
																				  'from'   => array( 'members' => 'm' ),
																				  'where'  => 'm.id=st.subtrans_member_id',
																				  'type'   => 'left' ) ) ) );
	
		$this->ipsclass->DB->exec_query();
			
		while( $r = $this->ipsclass->DB->fetch_row() )
		{
			$members[ $r['id'] ] = $r;
			$ids[] = $r['id'];
		}
		
		//-----------------------------------------
		// Get subscription packages
		//-----------------------------------------
		
		if ( count( $ids ) )
		{
			$this->ipsclass->DB->build_query( array( 'select'   => 'st.subtrans_sub_id, st.subtrans_member_id',
													 'from'     => array( 'subscription_trans' => 'st' ),
													 'where'    => 'st.subtrans_member_id IN ('.implode( ",", $ids ) . ") AND st.subtrans_state='paid'",
													 'add_join' => array( 0 => array( 'select' => 's.sub_title',
																					  'from'   => array( 'subscriptions' => 's' ),
																					  'where'  => 's.sub_id=st.subtrans_sub_id',
																					  'type'   => 'left' ) ) ) );
		
			$this->ipsclass->DB->exec_query();
			
			while( $r = $this->ipsclass->DB->fetch_row() )
			{
				$members[ $r['subtrans_member_id'] ]['sub_title'] = $r['sub_title'];
			}
		
			//-----------------------------------------
			// Send out the EMAILS
			//-----------------------------------------
			
			foreach( $members as $member )
			{
				$this->email->get_template("subscription_expires");
				$this->email->build_message( array(
													'PACKAGE'  => $member['sub_title'],
													'EXPIRES'  => $this->ipsclass->get_date( $member['sub_end'], 'DATE' ),
													'LINK'     => $this->ipsclass->vars['board_url'].'/index.'.$this->ipsclass->vars['php_ext'].'?act=paysubs&CODE=index',
										   )     );
				
				$this->email->to = trim( $member['email'] );
				$this->email->send_mail();
			}
		}
		
		//-----------------------------------------
		// Log to log table - modify but dont delete
		//-----------------------------------------
		
		$this->class->append_task_log( $this->task, '' . intval(count($ids)) . '' );
		
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