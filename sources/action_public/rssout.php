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
|   > XML OUT Functions for XmlHttpRequest functions
|   > Module written by Matt Mecham
|   > Date started: Friday 18th March 2005
|
|	> Module Version Number: 1.1.0
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class rssout
{
	# Classes
	var $ipsclass;
	
    /*-------------------------------------------------------------------------*/
    // Constructor
    /*-------------------------------------------------------------------------*/
    
    function rssout()
    {
    	$this->xml_header = '<?xml version="1.0" encoding="WINDOWS-1251"?'.'>';
    }
    
    /*-------------------------------------------------------------------------*/
    // Auto run
    /*-------------------------------------------------------------------------*/
    
    function auto_run()
    {
    	//-----------------------------------------
    	// INIT
    	//-----------------------------------------
    	
    	if ( $this->ipsclass->input['j_do'] )
    	{
    		$this->ipsclass->input['do'] = $this->ipsclass->input['j_do'];
    	}
    	
    	//-----------------------------------------
    	// What shall we do?
    	//-----------------------------------------
    	
    	switch( $this->ipsclass->input['do'] )
    	{
    		default:
    			$this->ipsclass->input['type'] == 'calendar' ? $this->rss_calendar_out_show() : $this->rss_out_show();
    			break;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
	// Get new posts
	/*-------------------------------------------------------------------------*/
      
    function rss_calendar_out_show()
    {
    	//-----------------------------------------
    	// INIT
    	//-----------------------------------------
    	
    	$cal_id   = intval( $_REQUEST['id'] );
    	$rss_data = array();
    	$to_print = '';
    	$expires  = time();
    	
    	//-----------------------------------------
    	// Get RSS export
    	//-----------------------------------------
    	
    	$rss_data = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
    																  'from'   => 'cal_calendars',
    																  'where'  => 'cal_id='.$cal_id ) );
    	
    	//-----------------------------------------
    	// Got one?
    	//-----------------------------------------
    	
    	if ( $rss_data['cal_id'] AND $rss_data['cal_rss_export'] )
    	{
    		//-----------------------------------------
    		// Correct expires time
    		//-----------------------------------------
    		
    		$expires += $rss_data['cal_rss_update'] * 60;
    		
    		//-----------------------------------------
    		// Need to recache?
    		//-----------------------------------------
    		
    		$time_check = time() - ( $rss_data['cal_rss_update'] * 60 );
    		
    		if ( $time_check > $rss_data['cal_rss_update_last'] )
    		{
    			//-----------------------------------------
    			// Yes
    			//-----------------------------------------
    			
    			define( 'IN_ACP', 1 );
    			
    			require_once( ROOT_PATH.'sources/action_admin/calendars.php' );
    			$rss_export           =  new ad_calendars();
    			$rss_export->ipsclass =& $this->ipsclass;
    			
    			$to_print = $rss_export->calendar_rss_cache( $rss_data['cal_id'], 0 );
    		}
    		else
    		{
    			//-----------------------------------------
    			// No
    			//-----------------------------------------
    			
    			$to_print = $rss_data['cal_rss_cache'];
    		}
    	}
    	
    	@header('Content-Type: text/xml');
		@header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
		@header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		@header('Pragma: public');
		print $to_print;
		exit();
    }
   
    
    /*-------------------------------------------------------------------------*/
	// Get new posts
	/*-------------------------------------------------------------------------*/
      
    function rss_out_show()
    {
    	//-----------------------------------------
    	// INIT
    	//-----------------------------------------
    	
    	$rss_export_id = intval( $_REQUEST['id'] );
    	$rss_data      = array();
    	$to_print      = '';
    	$expires       = time();
    	
    	//-----------------------------------------
    	// Get RSS export
    	//-----------------------------------------
    	
    	$rss_data = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
    																  'from'   => 'rss_export',
    																  'where'  => 'rss_export_id='.$rss_export_id ) );
    	
    	//-----------------------------------------
    	// Got one?
    	//-----------------------------------------
    	
    	if ( $rss_data['rss_export_id'] AND $rss_data['rss_export_enabled'] )
    	{
    		//-----------------------------------------
    		// Correct expires time
    		//-----------------------------------------
    		
    		$expires += $rss_data['rss_export_cache_time'] * 60;
    		
    		//-----------------------------------------
    		// Need to recache?
    		//-----------------------------------------
    		
    		$time_check = time() - ( $rss_data['rss_export_cache_time'] * 60 );
    		
    		if ( $time_check > $rss_data['rss_export_cache_last'] )
    		{
    			//-----------------------------------------
    			// Yes
    			//-----------------------------------------
    			
    			define( 'IN_ACP', 1 );
    			
    			require_once( ROOT_PATH.'sources/action_admin/rssexport.php' );
    			$rss_export           =  new ad_rssexport();
    			$rss_export->ipsclass =& $this->ipsclass;
    			
    			$to_print = $rss_export->rssexport_rebuild_cache( $rss_data['rss_export_id'], 0 );
    		}
    		else
    		{
    			//-----------------------------------------
    			// No
    			//-----------------------------------------
    			
    			$to_print = $rss_data['rss_export_cache_content'];
    		}
    	}
    	
    	@header('Content-Type: text/xml; charset='.$this->ipsclass->vars['gb_char_set'] );
		@header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
		@header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		@header('Pragma: public');
		print $to_print;
		exit();
    }
   
        
}

?>