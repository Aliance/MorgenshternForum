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
|
|   > CP "MYCP" PAGE CLASS
|   > Script written by Matt Mecham
|   > Date started: Wed. 18th August 2004
|
+---------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}


class acp_admin
{
	# Globals
	var $ipsclass;
	
	/**
	* Main choice array
	*
	* @var	array
	*/
	var $another_choice = array();
	
	/**
	* Section title name
	*
	* @var	string
	*/
	var $section_title  = "Администрирование";
	
	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/
	
	function acp_admin()
	{
		
	}
	
	/*-------------------------------------------------------------------------*/
	// AUTO RUN
	/*-------------------------------------------------------------------------*/
	
	function auto_run()
	{
		$this->ipsclass->html_title = "IPB: Администрирование";
		
		$another_choice = array(
								'idx'       => 'index',
								'stats'     => 'statistics',
								'sql'       => 'sql',
								'emaillog'  => 'emaillogs',
								'spiderlog' => 'spiderlogs',
								'warnlog'   => 'warnlogs',
								'modlog'    => 'modlogs',
								'adminlog'  => 'adminlogs',
								'emailerror'=> 'emailerror',
								'components'=> 'components',
								'acpperms'  => 'acppermissions',
								'diag'		=> 'diagnostics',
								'loginlog'  => 'logs_login',
								'security'  => 'security',
							  );
									
		if ( !isset($this->ipsclass->input['act']) OR !isset($another_choice[ $this->ipsclass->input['act'] ]) OR !$another_choice[ $this->ipsclass->input['act'] ] )
		{
			 $this->ipsclass->input['act'] = 'idx';
		}
		
		$this->ipsclass->form_code    = 'section=admin&amp;act=' . $this->ipsclass->input['act'];
		$this->ipsclass->form_code_js = 'section=admin&act='     . $this->ipsclass->input['act'];
		$this->ipsclass->section_code = 'admin';
		
		//-----------------------------------------
		// Quick perm check
		//-----------------------------------------
		
		if ( $this->ipsclass->input['act'] != 'idx' AND $this->ipsclass->input['act'] != 'acpperms' AND $this->ipsclass->input['act'] != 'sql' )
		{
			$this->ipsclass->admin->cp_permission_check( $this->ipsclass->section_code.'|' );
		}
		
		//-----------------------------------------
		// Require and run (again)
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/action_admin/'.$another_choice[ $this->ipsclass->input['act'] ].'.php' );
		$constructor          = 'ad_'.$another_choice[ $this->ipsclass->input['act'] ];
		$runmeagain           = new $constructor;
		$runmeagain->ipsclass =& $this->ipsclass;
		$runmeagain->auto_run();
	}
}


?>