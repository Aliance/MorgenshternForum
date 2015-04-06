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

// CAT_ID => array(  PAGE_ID  => (PAGE_NAME, URL ) )

// $PAGES[ $cat_id ][$page_id][0] = �������� ��������
// $PAGES[ $cat_id ][$page_id][1] = ������
// $PAGES[ $cat_id ][$page_id][2] = �������� ���������� ����� ������������
// $PAGES[ $cat_id ][$page_id][3] = ��� ������: 1 = ����� 0 = ����������
// $PAGES[ $cat_id ][$page_id][4] = ������ ����: 1 = ������������� 0 = ����������

global $ipsclass;

$CATS  = array();
$PAGES = array();

$CATS[]  = array( '������ � ���������' );

$PAGES[] = array(
					0 => array( '������ ������'                 , 'section=help&amp;act=support&amp;code=support'   ),
                    1 => array( '������������ IPB'                 , 'section=help&amp;act=support&amp;code=doctor'   ),
					2 => array( '���� ������ IPB' 	            , 'section=help&amp;act=support&amp;code=kb'   ),					
					3 => array( '������ �������� IBResource' 	, 'section=help&amp;act=support&amp;code=ibresource'   ),
					4 => array( '���������� ����������' 		, 'section=help&amp;act=support&amp;code=contact'  ),
					5 => array( '����������� � ���������' 	    , 'section=help&amp;act=support&amp;code=features'   ),
					6 => array( '�������� �� ������' 			, 'section=help&amp;act=support&amp;code=bugs'   ),
			       );

$CATS[]  = array( '�����������' );

$PAGES[] = array(
					0 => array( '����� �������'		, "section=help&amp;act=diag' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					//2 => array( '�������� ������' 		, "section=help&amp;act=diag&amp;code=fileversions' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					3 => array( '�������� ���� ������' 		, "section=help&amp;act=diag&amp;code=dbchecker' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					4 => array( '�������� �������� ��' , "section=help&amp;act=diag&amp;code=dbindex' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					5 => array( '�������� ��������� ������' , "section=help&amp;act=diag&amp;code=filepermissions' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					6 => array( '�������� �� ������ �������' 	, "section=help&amp;act=diag&amp;code=whitespace' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					7 => array( '����� ������������' 		, "section=admin&amp;act=security", 0, 0, 1   ),
			       );	

?>