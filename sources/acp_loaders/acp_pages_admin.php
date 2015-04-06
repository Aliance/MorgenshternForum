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

$CATS[]  = array( 'Безопасность' );

$PAGES[] = array(
					1 => array( 'Центр безопасности'		 , 'section=admin&amp;act=security' ),
					2 => array( 'Список администраторов', 'section=admin&amp;act=security&amp;code=list_admins'  ),
			       );

$CATS[]  = array( 'Компоненты' );

$PAGES[] = array(
					1 => array( 'Список компонентов'      , 'section=admin&amp;act=components'   ),
					2 => array( 'Добавить компонент' , 'section=admin&amp;act=components&amp;code=component_add' ),
			       );

$CATS[]  = array( 'Статистика' );

$PAGES[] = array(
					1 => array( 'Регистраций' , 'section=admin&amp;act=stats&amp;code=reg'   ),
					2 => array( 'Новых тем'    , 'section=admin&amp;act=stats&amp;code=topic' ),
					3 => array( 'Сообщений'         , 'section=admin&amp;act=stats&amp;code=post'  ),
					4 => array( 'Личных сообщений'   , 'section=admin&amp;act=stats&amp;code=msg'   ),
					5 => array( 'Просмотров тем'        , 'section=admin&amp;act=stats&amp;code=views' ),
			       );

$CATS[]  = array( 'Ограничения доступа в&nbsp;АЦ' );

$PAGES[] = array(
					1 => array( 'Управление ограничениями' , 'section=admin&amp;act=acpperms&amp;code=acpp_list'   ),
			       );                    


$CATS[]  = array( 'Управление SQL' );

$PAGES[] = array(
					1 => array( 'Инструменты'     , 'section=admin&amp;act=sql'           ),
					2 => array( 'Резервное копирование'     , 'section=admin&amp;act=sql&amp;code=backup'    ),
					3 => array( 'Информация о сервере', 'section=admin&amp;act=sql&amp;code=runtime'   ),
					4 => array( 'Системные переменные' , 'section=admin&amp;act=sql&amp;code=system'    ),
					5 => array( 'Процессы'   , 'section=admin&amp;act=sql&amp;code=processes' ),
			       );

$CATS[]  = array( 'Журналы операций' );

$PAGES[] = array(
					1 => array( 'Модерирование'  , 'section=admin&amp;act=modlog'    ),
					2 => array( 'Администратирование'      , 'section=admin&amp;act=adminlog'  ),
					3 => array( 'E-mail отправления'      , 'section=admin&amp;act=emaillog'  ),
					4 => array( 'E-mail ошибки', 'section=admin&amp;act=emailerror' ),
					5 => array( 'Поисковые роботы'        , 'section=admin&amp;act=spiderlog' ),
					6 => array( 'Предупреждения'       , 'section=admin&amp;act=warnlog'   ),
					7 => array( 'Попытки авторизации в АЦ' , 'section=admin&amp;act=loginlog'   ),
			       );


?>