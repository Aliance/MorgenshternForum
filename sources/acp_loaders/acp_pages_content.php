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

$CATS[]  = array( '���������� ��������' );

$PAGES[] = array(
					 1 => array( '������ �������'         	, 'section=content&amp;act=forum'                ),
					 2 => array( '������� ���������'         , 'section=content&amp;act=forum&amp;code=new&amp;type=category'       ),
					 3 => array( '������� �����'            , 'section=content&amp;act=forum&amp;code=new&amp;type=forum'       ),
					 4 => array( '����� �������'      	, 'section=content&amp;act=group&amp;code=permsplash'),
					// 6 => array( '����������'            , 'section=content&amp;act=mod'                  ),
					 7 => array( '������-��������� ���', 'section=content&amp;act=multimod'          ),
					 8 => array( '��������� �������'      	, 'section=tools&amp;act=op&amp;code=findsetting&amp;key=trashcanset-up', '', 0, 1 ),
			       );
			       
$CATS[]  = array( '������������ � ������' );

$PAGES[] = array(
					 1  => array ( '����� � ��������������'        , 'section=content&amp;act=mem&amp;code=search' ),
					 2  => array ( '�������� ������������'        , 'section=content&amp;act=mem&amp;code=add'  ),
					 3  => array ( '������'          , 'section=content&amp;act=mem&amp;code=title'),
					 4  => array ( '������'    , 'section=content&amp;act=group'         ),
					 5  => array ( '����������������'     , 'section=content&amp;act=mtools&amp;code=mod'  ),
					 6  => array ( '���������������'     	   , 'section=content&amp;act=mtools&amp;code=lock'  ),
					 9  => array ( '�������������� ����' , 'section=content&amp;act=field'         ),
					 11 => array ( '����������� IP'       , 'section=content&amp;act=mtools'        ),
					 12 => array ( '������� �������������'       , 'section=tools&amp;act=op&amp;code=findsetting&amp;key=userprofiles', '', 0, 1 ),
			       );

// ���� ��������� ����������� ��� ���������� ���������

/*$CATS[]  = array( '��������' );

$PAGES[] = array(
					 1 => array( 'Manage Payment Gateways'   , 'section=content&amp;act=msubs&amp;code=index-gateways' ),
					 2 => array( 'Manage Packages'           , 'section=content&amp;act=msubs&amp;code=index-packages' ),
					 3 => array( 'Manage Transactions'       , 'section=content&amp;act=msubs&amp;code=index-tools' ),
					 4 => array( 'Manage Currencies'         , 'section=content&amp;act=msubs&amp;code=currency' ,  ),
					 5 => array( 'Manually Add Transaction'  , 'section=content&amp;act=msubs&amp;code=addtransaction' ),
					 6 => array( 'Install Payment Gateways'  , 'section=content&amp;act=msubs&amp;code=install-index' ),
					 9 => array( 'Subscription Settings'     , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('subscriptionsmanager'), '', 0, 1 ),
				  
			       );*/
			       
$CATS[]  = array( '���������' );

$PAGES[] = array(
					1 => array( '������ ����������' , 'section=content&amp;act=calendars&amp;code=calendar_list' ),
					2 => array( '�������� ���������' , 'section=content&amp;act=calendars&amp;code=calendar_add'  ),
			       );
			       
$CATS[]  = array( '���������� RSS' );

$PAGES[] = array(
					1 => array( '������� �������' , 'section=content&amp;act=rssexport&amp;code=rssexport_overview'        ),
					2 => array( '������ �������' , 'section=content&amp;act=rssimport&amp;code=rssimport_overview'    ),
			       );
			       
$CATS[]  = array( '�������������� BB-����' );

$PAGES[] = array(
					1 => array( '������ BB-�����' , 'section=content&amp;act=bbcode&amp;code=bbcode'        ),
					2 => array( '�������� BB-���'        , 'section=content&amp;act=bbcode&amp;code=bbcode_add'    ),
			       );
			       
$CATS[]  = array( '�������' );

$PAGES[] = array(
					1 => array( '����������� �����', 'section=content&amp;act=babw&amp;code=badword'     ),
					2 => array( '���������� �������������'    , 'section=content&amp;act=babw&amp;code=ban'  ),
			       );
			       
$CATS[]  = array( '������������� �����' );

$PAGES[] = array(
					1 => array( '���� ������'      , 'section=content&amp;act=attach&amp;code=types'  ),
					2 => array( '����������'      , 'section=content&amp;act=attach&amp;code=stats'  ),
					3 => array( '�����'     , 'section=content&amp;act=attach&amp;code=search'  ),
			       );
			  

?>