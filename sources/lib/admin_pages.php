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
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
*/

//===========================================================================
// ��������� ����������, �������������� ��� ������ �����������
// Invision Power Board
//===========================================================================

// CAT_ID => array(  PAGE_ID  => (PAGE_NAME, URL ) )

// $PAGES[ $cat_id ][$page_id][0] = �������� ��������
// $PAGES[ $cat_id ][$page_id][1] = ������
// $PAGES[ $cat_id ][$page_id][2] = �������� ���������� ����� ������������
// $PAGES[ $cat_id ][$page_id][3] = ��� ������: 1 = ����� 0 = ����������
// $PAGES[ $cat_id ][$page_id][4] = ������ ����: 1 = ������������� 0 = ����������

$PAGES = array(
				# ���������
				
				100 => array (
							
							 1 => array( '������ ��������', 'act=op' ),
							 2 => array( '�������� ���������'  , 'act=op&code=settingnew' ),
							 7 => array( '���������/����������'      , 'act=op&code=findsetting&key='.urlencode('boardoffline/online'), '', 0, 1 ),
							 8 => array( '������� ������'         , 'act=op&code=findsetting&key='.urlencode('boardguidelines'), '', 0, 1 ),
							 9 => array( '�������� ���������'    , 'act=op&code=findsetting&key='.urlencode('generalconfiguration'), '', 0, 1 ),
							 10 => array( '��������� ��������������'              , 'act=op&code=findsetting&key='.urlencode('cpusaving&optimization'), '', 0, 1 ),
							 //11 => array( 'IP Chat'                 , 'act=pin&code=ipchat'  ),
							 12 => array( '����������� IPB'             , 'act=pin&code=reg'     ),
							 //14 => array( 'IPB �������� ���������'   , 'act=pin&code=copy'    ),
						   ),
						   
			    # ���������� ��������������
						   
				200 => array (
							 1 => array( '������� �����'             , 'act=forum&code=new'       ),
							 2 => array( '������ �������'         , 'act=forum'                ),
							 3 => array( '����� �������'      , 'act=group&code=permsplash'),
							 6 => array( '����������'            , 'act=mod'                  ),
							 7 => array( '������-��������� ���', 'act=multimod'          ),
							 8 => array( '��������� �������'      , 'act=op&code=findsetting&key=trashcanset-up', '', 0, 1 ),
						   ),
						   
						   
				300 => array (
				            1  => array ( '����� � ��������������'        , 'act=mem&code=search' ),
							2  => array ( '�������� ������������'        , 'act=mem&code=add'  ),
							6  => array ( '������'          , 'act=mem&code=title'),
							7  => array ( '������'    , 'act=group'         ),
							8  => array ( '����������������'     , 'act=mem&code=mod'  ),
							9  => array ( '�������������� ����' , 'act=field'         ),
							11 => array ( '����������� IP'       , 'act=mtools'        ),
							12 => array ( '������� �������������'       , 'act=op&code=findsetting&key=userprofiles', '', 0, 1 ),
						   ),
				
					   
                /*400 => array(
							 1 => array( 'Manage Payment Gateways'   , 'act=msubs&code=index-gateways' ),
							 2 => array( 'Manage Packages'           , 'act=msubs&code=index-packages' ),
							 3 => array( 'Manage Transactions'       , 'act=msubs&code=index-tools' ),
							 4 => array( 'Manage Currencies'         , 'act=msubs&code=currency' ,  ),
							 5 => array( 'Manually Add Transaction'  , 'act=msubs&code=addtransaction' ),
							 6 => array( 'Install Payment Gateways'  , 'act=msubs&code=install-index' ),
							 9 => array( 'Subscription Settings'     , 'act=op&code=findsetting&key='.urlencode('subscriptionsmanager'), '', 0, 1 ),
						   ),*/

				# ���������� �����������
				
				500 => array (
							1 => array( '���� ������'      , 'act=attach&code=types'  ),
							2 => array( '����������'      , 'act=attach&code=stats'  ),
							3 => array( '�����'     , 'act=attach&code=search'  ),
				  			),
				  			
				  			
				600 => array(
							1 => array( '������ BB-�����' , 'act=admin&code=bbcode'        ),
							2 => array( '�������� BB-���'        , 'act=admin&code=bbcode_add'    ),
						   ),
						   
				700 => array(
							1 => array( '������ ����� �� ����������'      , 'act=admin&code=emo'               ),
							2 => array( '������ � �������'   , 'act=admin&code=emo_packsplash'    ),
						   ),		   
						   
				800 => array (
							1 => array( '����������� �����', 'act=admin&code=badword'     ),
							6 => array( '����������'    , 'act=admin&code=ban'  ),
							),		
				
				# ����� � �����
				
				900 => array (
							1 => array( '������ ������'            , 'act=sets'        ),
							2 => array( '�����������'              , 'act=skintools'   ),
							3 => array( '����� � ������'   , 'act=skintools&code=searchsplash'   ),
							4 => array( '������ � �������'      , 'act=import'      ),
							5 => array( '������� ����� ��������'       , 'act=skintools&code=easylogo'   ),
						   ),
						   			
				1000 => array (
							1 => array( '������ ������'        , 'act=lang'             ),
							2 => array( '��������������'       , 'act=lang&code=import' ),
						   ),
				
				
				# �������������������
						   
				1100 => array (
							1 => array( '������� ������'     , 'act=help'                   ),
							2 => array( '���������� �����'         , 'act=admin&code=cache'       ),
							3 => array( '�������� � ����������'     , 'act=rebuild'                ),
							4 => array( '����������� �������'        , 'act=rebuild&code=tools'     ),
						   ),
						   
			    1200 => array(
			    			1  => array( '������ ��������'      , 'act=postoffice'                    ),
			    			2  => array( '������� ��������'      , 'act=postoffice&code=mail_new'      ),
	    					3  => array( '������ e-mail �����������'       , 'act=emaillog', '', 0, 1 ),
			    			4  => array( '������ e-mail ������' , 'act=emailerror', '', 0, 1 ),
			    			5  => array( '��������� e-mail '        , 'act=op&code=findsetting&key=emailset-up', '', 0, 1 ),
			    		    ),
			    
			    1300 => array (
							 1 => array( '������ �����'        , 'act=task'                ),
							 2 => array( '������ ����������� ����� '      , 'act=task&code=log'       ),
						   ),
				
				
				1400 => array(
							 1 => array( 'Invision Gallery'        , 'act=gallery' ),
							 2 => array( '|-- ���������'            , 'act=op&code=findsetting&key='.urlencode('��������� �������'), '', 0, 0 ),
							 3 => array( '|-- �������� ��������'       , 'act=gallery&code=albums'  , 'modules/gallery' ),
							 4 => array( '|-- �������� �����������'  , 'act=gallery&code=media'   , 'modules/gallery' ),
							 5 => array( '|-- ������'              , 'act=gallery&code=groups'  , 'modules/gallery' ),
							 6 => array( '|-- ����������'               , 'act=gallery&code=stats'   , 'modules/gallery' ),
							 7 => array( '|-- �����������'               , 'act=gallery&code=tools'   , 'modules/gallery' ),
							 8 => array( '&#039;-- ���� ��������'      , 'act=gallery&code=postform', 'modules/gallery' ),
						   ),
						   
				1450 => array(
							 1 => array( 'Community Blog'          , 'act=blog' ),
							 2 => array( '���������'           , 'act=op&code=findsetting&key='.urlencode('communityblog'), '', 0, 1 ),
							 3 => array( '������'				   , 'act=blog&amp;cmd=groups' ),
							 4 => array( '�����'		   , 'act=blog&amp;cmd=cblocks' ),
							 5 => array( '�����������'				   , 'act=blog&amp;cmd=tools' ),
						   ),
				
				1500 => array (
							1 => array( '�����������' , 'act=stats&code=reg'   ),
							2 => array( '����� ���'    , 'act=stats&code=topic' ),
							3 => array( '���������'         , 'act=stats&code=post'  ),
							4 => array( '������ ���������'   , 'act=stats&code=msg'   ),
							5 => array( '���������� ���'        , 'act=stats&code=views' ),
						   ),
						   
				1600 => array (
							1 => array( '�����������'     , 'act=mysql'           ),
							2 => array( '��������� �����'     , 'act=mysql&code=backup'    ),
							3 => array( '���������� � �������', 'act=mysql&code=runtime'   ),
							4 => array( '��������� ����������' , 'act=mysql&code=system'    ),
							5 => array( '��������'   , 'act=mysql&code=processes' ),
						   ),
				
				1700 => array(
							1 => array( '�������������'  , 'act=modlog'    ),
							2 => array( '�������������������'      , 'act=adminlog'  ),
							3 => array( 'E-mail �����������'      , 'act=emaillog'  ),
							4 => array( 'E-mail ������', 'act=emailerror' ),
							5 => array( '��������� ������'        , 'act=spiderlog' ),
							6 => array( '��������������'       , 'act=warnlog'   ),
						   ),
			   );
			   
			   
$CATS = array (   
				  100 => array( "��������� ���������"   , '#caf2d9;margin-bottom:12px;' ),
				  
				  200 => array( '���������� ��������'     , '#F9FFA2' ),
				  300 => array( '������������ � ������'  , '#F9FFA2' ),

				  //400 => array( "��������"     , '#F9FFA2;margin-bottom:12px;' ),

				  500 => array( "������������� �����"       , '#f5cdcd' ),
				  600 => array( "�������������� BB-����"     , '#f5cdcd' ),
				  700 => array( "��������"         , '#f5cdcd' ),
				  800 => array( "�������", '#f5cdcd;margin-bottom:12px;' ),
				  
				  900 => array( '�����' , '#DFE6EF' ),
				  1000 => array( '�����'        , '#DFE6EF;margin-bottom:12px;' ),
				  
				  1100 => array( '�������������'      , '#caf2d9' ),
				  1200 => array( '������ � e-mail'      , '#caf2d9' ),
				  1300 => array( '�������� �����'     , '#caf2d9;margin-bottom:12px;' ),
				  
				  1400 => array( "Invision Gallery" , '#F9FFA2;' ),
				  1450 => array( "Community Blog"   , '#F9FFA2;margin-bottom:12px;' ),
				  
				  1500 => array( '����������' , '#f5cdcd' ),
				  1600 => array( '���������� SQL'   , '#f5cdcd' ),
				  1700 => array( '������� ��������'       , '#f5cdcd' ),
			  );
			  

			  
$DESC = array (
				  100 => "��������� ������, ����� ��� ��������� cookies, ����������� �� ������������, ����������� ���������� ��������� � �.�.",
				  
				  200 => "��������, ���������, �������� � ���������� ���������, ������� � �����������",
				  300 => "���������� ��������������, �������� � ��������",

				  //400 => "���������� ���������� ����� �������������",

				  500 => "���������� �������������� �������",
				  600 => "���������� ��������������� BB-������",
				  700 => "���������� ����������, ������� � ������ ����� ���������",
				  800 => "���������� ��������� ����� � ����������� ����",
				  
				  900 => "���������� ���������, �������, ������� � �������������",
				  1000 => "���������� ��������� �������",
				  
				  1100 => "���������� ��������� ������, ��������� ����������� ���� � ����������",
				  1200 => "���������� e-mail � ����������",
				  1300 => "���������� ���������������� ��������",
				  
		  		  1400 => "���������� ����� ��������",
				  1450 => "���������� ������ �������",
				  
				  1500 => "��������� ���������� ����������� � ���������� ���������",
				  1600 => "���������� ����� ����� ������, �������, ����������� � ������� ������",
				  1700 => "�������� ������� �������� ���������������, ����������� � e-mail (������ ������� ��������������)", 			  );
?>