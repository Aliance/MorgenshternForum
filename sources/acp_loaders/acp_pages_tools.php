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

$CATS[]  = array( 'Системные настройки' );

$PAGES[] = array(
					 1 => array( 'Список настроек', 'section=tools&amp;act=op' ),
					 2 => array( 'Добавить настройку'  , 'section=tools&amp;act=op&amp;code=settingnew' ),
					 7 => array( 'Включение/выключение'      , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('boardoffline'), '', 0, 1 ),
					 8 => array( 'Правила форума'         , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('boardguidelines'), '', 0, 1 ),
					 9 => array( 'Основные настройки'    , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('general'), '', 0, 1 ),
					 10 => array( 'Настройки быстродействия'              , 'section=tools&amp;act=op&amp;code=findsetting&amp;key='.urlencode('cpusaving'), '', 0, 1 ),
					 //11 => array( 'IP Chat'                 , 'section=tools&amp;act=pin&amp;code=ipchat'  ),
					 //12 => array( 'IPB Лицензия'             , 'section=tools&amp;act=pin&amp;code=reg'     ),
					 //14 => array( 'IPB Удаление копирайта'   , 'section=tools&amp;act=pin&amp;code=copy'    ),
				);

$CATS[]  = array( 'Обслуживание' );

$PAGES[] = array(
					1 => array( 'Разделы помощи'     , 'section=tools&amp;act=help'                   ),
					2 => array( 'Управление кешем'         , 'section=tools&amp;act=admin&amp;code=cache'       ),
					3 => array( 'Пересчет и обновление'     , 'section=tools&amp;act=rebuild'                ),
					4 => array( 'Инструменты очистки'        , 'section=tools&amp;act=rebuild&amp;code=tools'     ),
			       );

$CATS[]  = array( 'Работа с e-mail' );

$PAGES[] = array(
					1  => array( 'Список рассылок'      , 'section=tools&amp;act=postoffice'                    ),
			    	2  => array( 'Создать рассылку'      , 'section=tools&amp;act=postoffice&amp;code=mail_new'      ),
			    	3  => array( 'Журнал e-mail отправлений'       , 'section=admin&amp;act=emaillog', '', 0, 1 ),
			    	4  => array( 'Журнал e-mail ошибок' , 'section=admin&amp;act=emailerror', '', 0, 1 ),
			    	5  => array( 'E-mail настройки'        , 'section=tools&amp;act=op&amp;code=findsetting&amp;key=emailset-up', '', 0, 1 ),
			       );

$CATS[]  = array( 'Управление порталом' );

$PAGES[] = array(
					1 => array( 'Дополнения', 'section=tools&amp;act=portal' ),
			       );

$CATS[]  = array( 'Методы авторизации' );

$PAGES[] = array(
					1 => array( 'Список методов'    , 'section=tools&amp;act=loginauth'                    ),
					2 => array( 'Создать новый метод' , 'section=tools&amp;act=loginauth&amp;code=login_add' ),
			       );

$CATS[]  = array( 'Менеджер задач' );

$PAGES[] = array(
					1 => array( 'Список задач'        , 'section=tools&amp;act=task'                ),
					2 => array( 'Журнал выполненных задач'      , 'section=tools&amp;act=task&amp;code=log'       ),
			       );



?>