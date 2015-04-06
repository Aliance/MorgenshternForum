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
|
|   > CONTROL PANEL (COMPONENTS) PAGES FILE
|   > Script written by Matt Mecham
|   > Date started: Tue. 15th February 2005
|
+---------------------------------------------------------------------------
*/

//===========================================================================
// ��������� ����������, �������������� ��� ������ �����������
// ���� ����� ��������: ��������� ���� �� ��
//===========================================================================

if ( ! defined( 'IN_ACP' ) )
{
    print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}

global $ipsclass;

$CATS  = array();
$PAGES = array();

//--------------------------------
// Get info from DB
//--------------------------------

foreach( $ipsclass->menu_components as $r )
{
	//--------------------------------
	// Process data
	//--------------------------------
	
	$menu_data = unserialize( $r['com_menu_data'] );
	$tmp_pages = array();
	
	//--------------------------------
	// Do we have any menu links?
	//--------------------------------

	if ( is_array( $menu_data ) and count( $menu_data ) )
	{
		//--------------------------------
		// First item is title...
		//--------------------------------	

		$CATS[] = array( $r['com_title'] );

		foreach( $menu_data as $menu )
		{
			if ( $menu['menu_text'] AND $menu['menu_url'] )
			{
				if ( $menu['menu_redirect'] )
				{
					$tmp_pages[] = array( $menu['menu_text'], $menu['menu_url'], "", 0, 1 );
				}
				else
				{
					$tmp_pages[] = array( $menu['menu_text'], 'section=components&amp;act='.$r['com_section'].'&amp;'.$menu['menu_url'] );
				}
			}
		}
		
		$PAGES[] = $tmp_pages;
	}
}

?>