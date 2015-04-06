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
|                  http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-11-03 16:54:54 -0600 (Fri, 03 Nov 2006) $
|   > $Revision: 709 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > Task Manager functions
|   > Script written by Matt Mecham
|   > Date started: 29th September 2003
|   > DBA Checked: Fri 21 May 2004
|
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_IPB' ) )
{
    print "<h1>Неверный вход</h1> У Вас нет доступа к директиве этого файла. Если вы проводили обновления, убедитесь, что не забыли обновить 'admin.php'.";
	exit();
}


class func_taskmanager
{
	# Global
	var $ipsclass;
	
	var $type      = 'internal';
	var $root_path = './';
	var $time_now  = 0;
	var $date_now  = array();
	var $cron_key  = "";
	var $date;
	
	/*-------------------------------------------------------------------------*/
	//
	// CONSTRUCTOR
	//
	/*-------------------------------------------------------------------------*/
	
	function func_taskmanager()
	{
		$this->time_now = time();
		
		$this->date_now['minute']      = intval( gmdate( 'i', $this->time_now ) );
		$this->date_now['hour']        = intval( gmdate( 'H', $this->time_now ) );
		$this->date_now['wday']        = intval( gmdate( 'w', $this->time_now ) );
		$this->date_now['mday']        = intval( gmdate( 'd', $this->time_now ) );
		$this->date_now['month']       = intval( gmdate( 'm', $this->time_now ) );
		$this->date_now['year']        = intval( gmdate( 'Y', $this->time_now ) );
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Run the task
	//
	/*-------------------------------------------------------------------------*/
	
	function run_task()
	{
		if ( isset( $_REQUEST['ck'] ) AND $_REQUEST['ck'] )
		{
			$this->type     = 'cron';
			$this->cron_key = substr( trim(stripslashes($this->ipsclass->txt_alphanumerical_clean($_REQUEST['ck']))), 0, 32 );
		}
				
		if ( $this->type == 'internal' )
		{
			//-----------------------------------------
			// Loaded by our image...
			// ... get next job
			//-----------------------------------------
			
			$this_task = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*',
																		'from'   => 'task_manager',
																		'where'  => 'task_enabled = 1 AND task_next_run <= '.$this->time_now,
																		'order'  => 'task_next_run ASC',
																		'limit'  => array(0,1)
															   )      );
		}
		else
		{
			//-----------------------------------------
			// Cron.. load from cron key
			//-----------------------------------------
			
			$this_task = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*',
																		'from'   => 'task_manager',
																		'where'  => "task_cronkey='".$this->cron_key."'",
															   )      );
		
		}
		
											   
		if ( $this_task['task_id'] )
		{
			//-----------------------------------------
			// Locked?
			//-----------------------------------------
			
			if ( $this_task['task_locked'] > 0 )
			{
				# Yes - now, how long has it been locked for?
				# If longer than 30 mins, unlock as something
				# has gone wrong.
				
				if ( $this_task['task_locked'] < time() - 1800 )
				{
					$newdate = $this->generate_next_run($this_task);
					
					$this->ipsclass->DB->do_update( 'task_manager', array( 'task_next_run' => $newdate, 'task_locked' => 0 ), "task_id=".$this_task['task_id'] );
					
					$this->save_next_run_stamp();
				}
				
				# Cancel and return
				return;
			}
				
			//-----------------------------------------
			// Got it, now update row, lock and run..
			//-----------------------------------------
			
			$newdate = $this->generate_next_run($this_task);
			
			$this->ipsclass->DB->do_update( 'task_manager', array( 'task_next_run' => $newdate, 'task_locked' => time() ), "task_id=".$this_task['task_id'] );
			
			$this->save_next_run_stamp();
			
			if ( file_exists( $this->root_path.'sources/tasks/'.$this_task['task_file'] ) )
			{
				require_once( $this->root_path.'sources/tasks/'.$this_task['task_file'] );
				$myobj = new task_item();
				$myobj->register_class( $this );
				$myobj->pass_task( $this_task );
				$myobj->run_task();
				
				//-----------------------------------------
				// Any shutdown queries
				//-----------------------------------------
				
				$this->ipsclass->DB->return_die = 0;
				
				if ( count( $this->ipsclass->DB->obj['shutdown_queries'] ) )
				{
					foreach( $this->ipsclass->DB->obj['shutdown_queries'] as $q )
					{
						$this->ipsclass->DB->query( $q );
					}
				}
				
				$this->ipsclass->DB->return_die = 1;
				
				$this->ipsclass->DB->obj['shutdown_queries'] = array();
			}
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Unlock task
	/*-------------------------------------------------------------------------*/
	
	function unlock_task($task=array())
	{
		if ( !isset($task['task_id']) OR !$task['task_id'] )
		{
			return;
		}
					 
		$this->ipsclass->DB->do_update( 'task_manager', array( 'task_locked' => 0 ), 'task_id='.$task['task_id'] );
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Update next run variable in the systemvars cache
	//
	/*-------------------------------------------------------------------------*/
	
	function save_next_run_stamp()
	{
		$this_task = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'task_next_run', 'from' => 'task_manager', 'where' => 'task_enabled = 1', 'order' => 'task_next_run ASC', 'limit' => array(0,1) ) );
		
		if ( ! $this_task['task_next_run'] )
		{
			//-----------------------------------------
			// Fail safe...
			//-----------------------------------------
			
			$this_task['task_next_run'] = $this->time_now + 3600;
		}
		
		$cache_array = array();
		
		$cache       = $this->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'cache_store', 'where' => "cs_key='systemvars'" ) );
		
		$cache_array = unserialize(stripslashes($cache['cs_value']));
		
		$cache_array['task_next_run'] = $this_task['task_next_run'];
		
		$this->ipsclass->update_cache( array( 'array' => 1, 'name' => 'systemvars', 'donow' => 1, 'deletefirst' => 0 ) );
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Generate next_run unix timestamp
	//
	/*-------------------------------------------------------------------------*/
	
	function generate_next_run($task=array())
	{
		//-----------------------------------------
		// Did we set a day?
		//-----------------------------------------
		
		$day_set       = 1;
		$min_set       = 1;
		$day_increment = 0;
		
		$this->run_day    = $this->date_now['wday'];
		$this->run_minute = $this->date_now['minute'];
		$this->run_hour   = $this->date_now['hour'];
		$this->run_month  = $this->date_now['month'];
		$this->run_year   = $this->date_now['year'];
		
		if ( $task['task_week_day'] == -1 and $task['task_month_day'] == -1 )
		{
			$day_set = 0;
		}
		
		if ( $task['task_minute'] == -1 )
		{
			$min_set = 0;
		}
		
		if ( $task['task_week_day'] == -1 )
		{
			if ( $task['task_month_day'] != -1 )
			{
				$this->run_day = $task['task_month_day'];
				$day_increment = 'month';
			}
			else
			{
				$this->run_day = $this->date_now['mday'];
				$day_increment = 'anyday';
			}
		}
		else
		{
			//-----------------------------------------
			// Calc. next week day from today
			//-----------------------------------------
			
			$this->run_day = $this->date_now['mday'] + ( $task['task_week_day'] - $this->date_now['wday'] );
			
			$day_increment = 'week';
		}
		
		//-----------------------------------------
		// If the date to run next is less
		// than today, best fetch the next
		// time...
		//-----------------------------------------
		
		if ( $this->run_day < $this->date_now['mday'] )
		{
			switch ( $day_increment )
			{
				case 'month':
					$this->_add_month();
					break;
				case 'week':
					$this->_add_day(7);
					break;
				default:
					$this->_add_day();
					break;
			}
		}
				
		//-----------------------------------------
		// Sort out the hour...
		//-----------------------------------------
		
		if ( $task['task_hour'] == -1)
		{
			$this->run_hour = $this->date_now['hour'];
		}
		else
		{
			//-----------------------------------------
			// If ! min and ! day then it's
			// every X hour
			//-----------------------------------------
			
			if ( ! $day_set and ! $min_set )
			{
				$this->_add_hour( $task['task_hour'] );
			}
			else
			{
				$this->run_hour = $task['task_hour'];
			}
		}
		
		//-----------------------------------------
		// Can we run the minute...
		//-----------------------------------------
		
		if ( $task['task_minute'] == -1 )
		{
			$this->_add_minute();
		}
		else
		{
			if ( $task['task_hour'] == -1 and ! $day_set )
			{
				//-----------------------------------------
				// Runs every X minute..
				//-----------------------------------------
				
				$this->_add_minute($task['task_minute']);
			}
			else
			{
				//-----------------------------------------
				// runs at hh:mm
				//-----------------------------------------
				
				$this->run_minute = $task['task_minute'];
			}
		}
		
		if ( $this->run_hour <= $this->date_now['hour'] and $this->run_day == $this->date_now['mday'] )
		{
			if ( $task['task_hour'] == -1 )
			{
				//-----------------------------------------
				// Every hour...
				//-----------------------------------------
				
				if ( $this->run_hour == $this->date_now['hour'] and $this->run_minute <= $this->date_now['min'] )
				{
 					$this->_add_hour();
 				}
 			}
 			else
 			{
 				//-----------------------------------------
 				// Every X hour, try again in x hours
 				//-----------------------------------------
 				
 				if ( ! $day_set and ! $min_set )
 				{
 					$this->_add_hour($task['task_hour'] );
 				}
 				
 				//-----------------------------------------
 				// Specific hour, try tomorrow
 				//-----------------------------------------
 				
 				else if ( ! $day_set )
 				{
 					$this->_add_day();
 				}
 				else
 				{
 					//-----------------------------------------
 					// Oops, specific day...
 					//-----------------------------------------
 					
 					switch ( $day_increment )
					{
						case 'month':
							$this->_add_month();
							break;
						case 'week':
							$this->_add_day(7);
							break;
						default:
							$this->_add_day();
							break;
					}
 				}
 			}
		}
		
		//-----------------------------------------
		// Return stamp...
		//-----------------------------------------
		
		$next_run = gmmktime( $this->run_hour, $this->run_minute, 0, $this->run_month, $this->run_day, $this->run_year );
		
		return $next_run;
	
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Add to the log file
	//
	/*-------------------------------------------------------------------------*/
	
	function append_task_log($task,$desc)
	{
		if ( ! $task['task_log'] )
		{
			return;
		}
	
		$save = array( 'log_title' => $task['task_title'],
					   'log_date'  => time(),
					   'log_ip'    => $this->ipsclass->my_getenv('REMOTE_ADDR'),
					   'log_desc'  => $desc
					 );
					 
		$this->ipsclass->DB->do_insert( 'task_logs', $save );
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Add on a month for the next run time..
	//
	/*-------------------------------------------------------------------------*/
	
	function _add_month()
	{
		if ($this->date_now['month'] == 12)
		{
			$this->run_month = 1;
			$this->run_year++;
		}
		else
		{
			$this->run_month++;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Add on a day for the next run time..
	//
	/*-------------------------------------------------------------------------*/
	
	function _add_day($days=1)
	{
		if ( $this->date['mday'] >= ( gmdate( 't', $this->time_now ) - $days ) )
		{
			$this->run_day = ($this->date['mday'] + $days) - date( 't', $this->time_now );
			$this->_add_month();
		}
		else
		{
			$this->run_day += $days;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Add on a hour for the next run time...
	//
	/*-------------------------------------------------------------------------*/
	
	function _add_hour($hour=1)
	{
		if ($this->date_now['hour'] >= (24 - $hour ) )
		{
			$this->run_hour = ($this->date_now['hour'] + $hour) - 24;
			$this->_add_day();
		}
		else
		{
			$this->run_hour += $hour;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	//
	// Add on a minute...
	//
	/*-------------------------------------------------------------------------*/
	
	function _add_minute($mins=1)
	{
		if ( $this->date_now['minute'] >= (60 - $mins) )
		{
			$this->run_minute = ( $this->date_now['minute'] + $mins ) - 60;
			$this->_add_hour();
		}
		else
		{
			$this->run_minute += $mins;
		}
	}
}



?>