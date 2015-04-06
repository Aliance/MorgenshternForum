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
|   > CONTROL PANEL PAGES FILE
|   > Script written by Matt Mecham
|   > Date started: Fri 8th April 2005 (12:07)
|
+---------------------------------------------------------------------------
*/

//===========================================================================
// ��������� ����������, �������������� ��� ������ �����������
//===========================================================================

// CAT_ID => array(  PAGE_ID  => (PAGE_NAME, URL ) )

// $PAGES[ $cat_id ][$page_id][0] = �������� ��������
// $PAGES[ $cat_id ][$page_id][1] = ������
// $PAGES[ $cat_id ][$page_id][2] = �������� ���������� ����� ������������
// $PAGES[ $cat_id ][$page_id][3] = ��� ������: 1 = ����� 0 = ����������
// $PAGES[ $cat_id ][$page_id][4] = ������ ����: 1 = ������������� 0 = ����������

$CATS[]  = array( '������������' );

$PAGES[] = array(
					1 => array( '����� ������������'		 , 'section=admin&amp;act=security' ),
					2 => array( '������ ���������������', 'section=admin&amp;act=security&amp;code=list_admins'  ),
			       );

$CATS[]  = array( '����������' );

$PAGES[] = array(
					1 => array( '������ �����������'      , 'section=admin&amp;act=components'   ),
					2 => array( '�������� ���������' , 'section=admin&amp;act=components&amp;code=component_add' ),
			       );

$CATS[]  = array( '����������' );

$PAGES[] = array(
					1 => array( '�����������' , 'section=admin&amp;act=stats&amp;code=reg'   ),
					2 => array( '����� ���'    , 'section=admin&amp;act=stats&amp;code=topic' ),
					3 => array( '���������'         , 'section=admin&amp;act=stats&amp;code=post'  ),
					4 => array( '������ ���������'   , 'section=admin&amp;act=stats&amp;code=msg'   ),
					5 => array( '���������� ���'        , 'section=admin&amp;act=stats&amp;code=views' ),
			       );

$CATS[]  = array( '����������� ������� �&nbsp;��' );

$PAGES[] = array(
					1 => array( '���������� �������������' , 'section=admin&amp;act=acpperms&amp;code=acpp_list'   ),
			       );                    


$CATS[]  = array( '���������� SQL' );

$PAGES[] = array(
					1 => array( '�����������'     , 'section=admin&amp;act=sql'           ),
					2 => array( '��������� �����������'     , 'section=admin&amp;act=sql&amp;code=backup'    ),
					3 => array( '���������� � �������', 'section=admin&amp;act=sql&amp;code=runtime'   ),
					4 => array( '��������� ����������' , 'section=admin&amp;act=sql&amp;code=system'    ),
					5 => array( '��������'   , 'section=admin&amp;act=sql&amp;code=processes' ),
			       );

$CATS[]  = array( '������� ��������' );

$PAGES[] = array(
					1 => array( '�������������'  , 'section=admin&amp;act=modlog'    ),
					2 => array( '�������������������'      , 'section=admin&amp;act=adminlog'  ),
					3 => array( 'E-mail �����������'      , 'section=admin&amp;act=emaillog'  ),
					4 => array( 'E-mail ������', 'section=admin&amp;act=emailerror' ),
					5 => array( '��������� ������'        , 'section=admin&amp;act=spiderlog' ),
					6 => array( '��������������'       , 'section=admin&amp;act=warnlog'   ),
					7 => array( '������� ����������� � ��' , 'section=admin&amp;act=loginlog'   ),
			       );


?>