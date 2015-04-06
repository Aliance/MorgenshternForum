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
|   > CONTROL PANEL (COMPONENTS) PAGES FILE
|   > Script written by Matt Mecham
|   > Date started: Tue. 15th February 2005
|
+---------------------------------------------------------------------------
*/

//===========================================================================
// Небольшая библиотека, контролирующая все ссылки админцентра
// ЭТОТ КЛАСС СТРАНИЦЫ: Генерация меню из БД
//===========================================================================

// CAT_ID => array(  PAGE_ID  => (PAGE_NAME, URL ) )

// $PAGES[ $cat_id ][$page_id][0] = Название страницы
// $PAGES[ $cat_id ][$page_id][1] = Ссылка
// $PAGES[ $cat_id ][$page_id][2] = Просмотр директории перед отображением
// $PAGES[ $cat_id ][$page_id][3] = Тип ссылки: 1 = форум 0 = админцентр
// $PAGES[ $cat_id ][$page_id][4] = Иконка меню: 1 = переадресация 0 = нормальная

global $ipsclass;

$CATS  = array();
$PAGES = array();

$CATS[]  = array( 'Помощь и поддержка' );

$PAGES[] = array(
					0 => array( 'Задать вопрос'                 , 'section=help&amp;act=support&amp;code=support'   ),
                    1 => array( 'Документация IPB'                 , 'section=help&amp;act=support&amp;code=doctor'   ),
					2 => array( 'База знаний IPB' 	            , 'section=help&amp;act=support&amp;code=kb'   ),					
					3 => array( 'Форумы компании IBResource' 	, 'section=help&amp;act=support&amp;code=ibresource'   ),
					4 => array( 'Контактная информация' 		, 'section=help&amp;act=support&amp;code=contact'  ),
					5 => array( 'Предложения и пожелания' 	    , 'section=help&amp;act=support&amp;code=features'   ),
					6 => array( 'Сообщить об ошибке' 			, 'section=help&amp;act=support&amp;code=bugs'   ),
			       );

$CATS[]  = array( 'Диагностика' );

$PAGES[] = array(
					0 => array( 'Обзор системы'		, "section=help&amp;act=diag' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					//2 => array( 'Проверка версии' 		, "section=help&amp;act=diag&amp;code=fileversions' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					3 => array( 'Проверка базы данных' 		, "section=help&amp;act=diag&amp;code=dbchecker' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					4 => array( 'Проверка индексов БД' , "section=help&amp;act=diag&amp;code=dbindex' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					5 => array( 'Проверка атрибутов файлов' , "section=help&amp;act=diag&amp;code=filepermissions' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					6 => array( 'Проверка на пустые символы' 	, "section=help&amp;act=diag&amp;code=whitespace' onclick='xmlobj = new ajax_request();xmlobj.show_loading()'"   ),
					7 => array( 'Центр безопасности' 		, "section=admin&amp;act=security", 0, 0, 1   ),
			       );	

?>