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
|   > CONTROL PANEL PAGES FILE
|   > Script written by Matt Mecham
|   > Date started: Fri 8th April 2005 (12:07)
|
+---------------------------------------------------------------------------
*/

//===========================================================================
// Небольшая библиотека, контролирующая все ссылки админцентра
//===========================================================================

// CAT_ID => array(  PAGE_ID  => (PAGE_NAME, URL ) )

// $PAGES[ $cat_id ][$page_id][0] = Название страницы
// $PAGES[ $cat_id ][$page_id][1] = Ссылка
// $PAGES[ $cat_id ][$page_id][2] = Просмотр директории перед отображением
// $PAGES[ $cat_id ][$page_id][3] = Тип ссылки: 1 = форум 0 = админцентр
// $PAGES[ $cat_id ][$page_id][4] = Иконка меню: 1 = переадресация 0 = нормальная

$CATS[]  = array( 'Управление форумами' );

$PAGES[] = array(
					 1 => array( 'Список форумов'         	, 'section=content&amp;act=forum'                ),
					 2 => array( 'Создать категорию'         , 'section=content&amp;act=forum&amp;code=new&amp;type=category'       ),
					 3 => array( 'Создать форум'            , 'section=content&amp;act=forum&amp;code=new&amp;type=forum'       ),
					 4 => array( 'Маски доступа'      	, 'section=content&amp;act=group&amp;code=permsplash'),
					// 6 => array( 'Модераторы'            , 'section=content&amp;act=mod'                  ),
					 7 => array( 'Мульти-модерация тем', 'section=content&amp;act=multimod'          ),
					 8 => array( 'Настройки корзины'      	, 'section=tools&amp;act=op&amp;code=findsetting&amp;key=trashcanset-up', '', 0, 1 ),
			       );
			       
$CATS[]  = array( 'Пользователи и группы' );

$PAGES[] = array(
					 1  => array ( 'Поиск и редактирование'        , 'section=content&amp;act=mem&amp;code=search' ),
					 2  => array ( 'Добавить пользователя'        , 'section=content&amp;act=mem&amp;code=add'  ),
					 3  => array ( 'Звания'          , 'section=content&amp;act=mem&amp;code=title'),
					 4  => array ( 'Группы'    , 'section=content&amp;act=group'         ),
					 5  => array ( 'Неактивированные'     , 'section=content&amp;act=mtools&amp;code=mod'  ),
					 6  => array ( 'Заблокированные'     	   , 'section=content&amp;act=mtools&amp;code=lock'  ),
					 9  => array ( 'Дополнительные поля' , 'section=content&amp;act=field'         ),
					 11 => array ( 'Инструменты IP'       , 'section=content&amp;act=mtools'        ),
					 12 => array ( 'Профили пользователей'       , 'section=tools&amp;act=op&amp;code=findsetting&amp;key=userprofiles', '', 0, 1 ),
			       );

// Меню «Подписки» неактуально для Российской Федерации

/*$CATS[]  = array( 'Подписки' );

$PAGES[] = array(
					 1 => array( 'Manage Payment Gateways'   , 'section=content&amp;act=msubs&amp;code=index-gateways' ),
					 2 => array( 'Manage Packages'           , 'section=content&amp;act=msubs&amp;code=index-packages' ),
					 3 => array( 'Manage Transactions'       , 'section=content&amp;act=msubs&amp;code=index-tools' ),
					 4 => array( 'Manage Currencies'         , 'section=content&amp;act=msubs&amp;code=currency' ,  ),
					 5 => array( 'Manually Add Transaction'  , 'section=content&amp;act=msubs&amp;code=addtransaction' ),
					 6 => array( 'Install Payment Gateways'  , 'section=content&amp;act=msubs&amp;code=install-index' ),
					 9 => array( 'Subscription Settings'     , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('subscriptionsmanager'), '', 0, 1 ),
				  
			       );*/
			       
$CATS[]  = array( 'Календари' );

$PAGES[] = array(
					1 => array( 'Список календарей' , 'section=content&amp;act=calendars&amp;code=calendar_list' ),
					2 => array( 'Добавить календарь' , 'section=content&amp;act=calendars&amp;code=calendar_add'  ),
			       );
			       
$CATS[]  = array( 'Управление RSS' );

$PAGES[] = array(
					1 => array( 'Экспорт потоков' , 'section=content&amp;act=rssexport&amp;code=rssexport_overview'        ),
					2 => array( 'Импорт потоков' , 'section=content&amp;act=rssimport&amp;code=rssimport_overview'    ),
			       );
			       
$CATS[]  = array( 'Дополнительные BB-коды' );

$PAGES[] = array(
					1 => array( 'Список BB-кодов' , 'section=content&amp;act=bbcode&amp;code=bbcode'        ),
					2 => array( 'Добавить BB-код'        , 'section=content&amp;act=bbcode&amp;code=bbcode_add'    ),
			       );
			       
$CATS[]  = array( 'Фильтры' );

$PAGES[] = array(
					1 => array( 'Нецензурные слова', 'section=content&amp;act=babw&amp;code=badword'     ),
					2 => array( 'Блокировка пользователей'    , 'section=content&amp;act=babw&amp;code=ban'  ),
			       );
			       
$CATS[]  = array( 'Прикрепляемые файлы' );

$PAGES[] = array(
					1 => array( 'Типы файлов'      , 'section=content&amp;act=attach&amp;code=types'  ),
					2 => array( 'Статистика'      , 'section=content&amp;act=attach&amp;code=stats'  ),
					3 => array( 'Поиск'     , 'section=content&amp;act=attach&amp;code=search'  ),
			       );
			  

?>