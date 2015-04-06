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

$CATS[]  = array( '���������� �������' );

$PAGES[] = array(
					1 => array( '������ ������'            , 'section=lookandfeel&amp;act=sets'        ),
					2 => array( '�����������'              , 'section=lookandfeel&amp;act=skintools'   ),
					3 => array( '����� � ������'   , 'section=lookandfeel&amp;act=skintools&amp;code=searchsplash'   ),
					4 => array( '������ � ������� ������'      , 'section=lookandfeel&amp;act=import'      ),
					5 => array( '���������'        , 'section=lookandfeel&amp;act=skindiff'      ),
					6 => array( '������� ����� ��������'       , 'section=lookandfeel&amp;act=skintools&amp;code=easylogo'   ),
			       );

$CATS[]  = array( '���������� �������' );

$PAGES[] = array(
					 1 => array( '������ ������'        , 'section=lookandfeel&amp;act=lang'             ),
					 2 => array( '������ �����'       , 'section=lookandfeel&amp;act=lang&amp;code=import' ),
			     );

$CATS[]  = array( '���������� ����������' );

$PAGES[] = array(
					1 => array( '������ ����������'      , 'section=lookandfeel&amp;act=emoticons&amp;code=emo'               ),
					2 => array( '������ � �������'   , 'section=lookandfeel&amp;act=emoticons&amp;code=emo_packsplash'    ),
			       );



?>