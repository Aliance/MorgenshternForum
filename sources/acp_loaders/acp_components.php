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


class acp_components
{
	# Globals
	var $ipsclass;
	
	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/
	
	function acp_components()
	{
	}
	
	/*-------------------------------------------------------------------------*/
	// AUTO RUN
	/*-------------------------------------------------------------------------*/
	
	function auto_run()
	{
		$this->ipsclass->html_title = "IPB: Компоненты";
		
		//--------------------------------
		// Get info from DB (Special case)
		//--------------------------------
		
		$this->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'components', 'order' => 'com_position ASC' ) );
		$this->ipsclass->DB->simple_exec();
		
		$this->ipsclass->menu_components = array();
		$another_choice  = array();
		$first_choice    = "";
		$menu_data       = array();
		
		while ( $r = $this->ipsclass->DB->fetch_row() )
		{
			if ( $r['com_section'] AND $r['com_filename'] )
			{
				$another_choice[ $r['com_section'] ] = str_replace( ".php", "", $r['com_filename'] );
				
				if ( ! $first_choice && $r['com_section'] != 'copyright' )
				{
					$first_choice = $r['com_section'];
				}
				
				//--------------------------------
				// Set the default...
				//--------------------------------
				
				$this->ipsclass->menu_components[] = $r;
				
				//--------------------------------
				// Grab menu data
				//--------------------------------
				
				$menu_data[ $r['com_section'] ] = unserialize( $r['com_menu_data'] );
			}
		}
		
		//-----------------------------------------
		// Get default action
		//-----------------------------------------
		
		if ( ! isset( $another_choice[ $this->ipsclass->input['act'] ] ) )
		{
			 $this->ipsclass->input['act'] = $first_choice;
		}
		
		//-----------------------------------------
		// Still got nothing?
		//-----------------------------------------
		
		if ( ! $this->ipsclass->input['act'] )
		{
			 $this->ipsclass->input['act'] = 'default';
			 $another_choice['default']    = 'default';
		}
		
		
		$this->ipsclass->form_code    = 'section=components&amp;act=' . $this->ipsclass->input['act'];
		$this->ipsclass->form_code_js = 'section=components&act='     . $this->ipsclass->input['act'];
		$this->ipsclass->section_code = 'components';
		
		//-----------------------------------------
		// Quick perm check
		//-----------------------------------------
		
		$this->ipsclass->admin->cp_permission_check( $this->ipsclass->section_code.'|' );
		$this->ipsclass->admin->cp_permission_check( $this->ipsclass->section_code.'|'.$this->ipsclass->input['act'] );
		
		//-----------------------------------------
		// Got a code?
		//-----------------------------------------
		
		if ( $this->ipsclass->input['code'] )
		{
			$this->ipsclass->input['code'] = $this->ipsclass->txt_alphanumerical_clean( $this->ipsclass->input['code'] );
			
			if ( is_array( $menu_data[ $this->ipsclass->input['act'] ] ) and count( $menu_data[ $this->ipsclass->input['act'] ] ) )
			{
				foreach( $menu_data[ $this->ipsclass->input['act'] ] as $menu_array )
				{
					if ( preg_match( "/code={$this->ipsclass->input['code']}(&|$)/i", $menu_array['menu_url'] ) )
					{
						$this->ipsclass->admin->cp_permission_check( $this->ipsclass->section_code.'|'.$this->ipsclass->input['act'].':'.$menu_array['menu_permbit'] );
						break;
					}
				}
			}
		}
		
		//-----------------------------------------
		// Require and run (again)
		//-----------------------------------------
		
		require_once( ROOT_PATH.'sources/components_acp/'.$another_choice[ $this->ipsclass->input['act'] ].'.php' );
		$constructor          = 'ad_'.$another_choice[ $this->ipsclass->input['act'] ];
		$runmeagain           = new $constructor;
		$runmeagain->ipsclass =& $this->ipsclass;
		$runmeagain->auto_run();
	}
	
	
	
	
	
}


?>