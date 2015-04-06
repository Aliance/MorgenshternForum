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

$CATS[]  = array( '��������� ���������' );

$PAGES[] = array(
					 1 => array( '������ ��������', 'section=tools&amp;act=op' ),
					 2 => array( '�������� ���������'  , 'section=tools&amp;act=op&amp;code=settingnew' ),
					 7 => array( '���������/����������'      , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('boardoffline'), '', 0, 1 ),
					 8 => array( '������� ������'         , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('boardguidelines'), '', 0, 1 ),
					 9 => array( '�������� ���������'    , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('general'), '', 0, 1 ),
					 10 => array( '��������� ��������������'              , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('cpusaving'), '', 0, 1 ),
					 //11 => array( 'IP Chat'                 , 'section=tools&amp;act=pin&amp;code=ipchat'  ),
					 //12 => array( 'IPB ��������'             , 'section=tools&amp;act=pin&amp;code=reg'     ),
					 //14 => array( 'IPB �������� ���������'   , 'section=tools&amp;act=pin&amp;code=copy'    ),
				);

$CATS[]  = array( '������������' );

$PAGES[] = array(
					1 => array( '������� ������'     , 'section=tools&amp;act=help'                   ),
					2 => array( '���������� �����'         , 'section=tools&amp;act=admin&amp;code=cache'       ),
					3 => array( '�������� � ����������'     , 'section=tools&amp;act=rebuild'                ),
					4 => array( '����������� �������'        , 'section=tools&amp;act=rebuild&amp;code=tools'     ),
			       );

$CATS[]  = array( '������ � e-mail' );

$PAGES[] = array(
					1  => array( '������ ��������'      , 'section=tools&amp;act=postoffice'                    ),
			    	2  => array( '������� ��������'      , 'section=tools&amp;act=postoffice&amp;code=mail_new'      ),
			    	3  => array( '������ e-mail �����������'       , 'section=admin&amp;act=emaillog', '', 0, 1 ),
			    	4  => array( '������ e-mail ������' , 'section=admin&amp;act=emailerror', '', 0, 1 ),
			    	5  => array( 'E-mail ���������'        , 'section=tools&amp;act=op&amp;code=findsetting&amp;key=emailset-up', '', 0, 1 ),
			       );

$CATS[]  = array( '���������� ��������' );

$PAGES[] = array(
					1 => array( '����������', 'section=tools&amp;act=portal' ),
			       );

$CATS[]  = array( '������ �����������' );

$PAGES[] = array(
					1 => array( '������ �������'    , 'section=tools&amp;act=loginauth'                    ),
					2 => array( '������� ����� �����' , 'section=tools&amp;act=loginauth&amp;code=login_add' ),
			       );

$CATS[]  = array( '�������� �����' );

$PAGES[] = array(
					1 => array( '������ �����'        , 'section=tools&amp;act=task'                ),
					2 => array( '������ ����������� �����'      , 'section=tools&amp;act=task&amp;code=log'       ),
			       );



?>