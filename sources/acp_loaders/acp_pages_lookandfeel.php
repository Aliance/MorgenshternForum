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

$CATS[]  = array( 'Управление стилями' );

$PAGES[] = array(
					1 => array( 'Список стилей'            , 'section=lookandfeel&amp;act=sets'        ),
					2 => array( 'Инструменты'              , 'section=lookandfeel&amp;act=skintools'   ),
					3 => array( 'Поиск и замена'   , 'section=lookandfeel&amp;act=skintools&amp;code=searchsplash'   ),
					4 => array( 'Импорт и экспорт стилей'      , 'section=lookandfeel&amp;act=import'      ),
					5 => array( 'Сравнение'        , 'section=lookandfeel&amp;act=skindiff'      ),
					6 => array( 'Простая смена логотипа'       , 'section=lookandfeel&amp;act=skintools&amp;code=easylogo'   ),
			       );

$CATS[]  = array( 'Управление языками' );

$PAGES[] = array(
					 1 => array( 'Список языков'        , 'section=lookandfeel&amp;act=lang'             ),
					 2 => array( 'Импорт языка'       , 'section=lookandfeel&amp;act=lang&amp;code=import' ),
			     );

$CATS[]  = array( 'Управление смайликами' );

$PAGES[] = array(
					1 => array( 'Список директорий'      , 'section=lookandfeel&amp;act=emoticons&amp;code=emo'               ),
					2 => array( 'Импорт и экспорт'   , 'section=lookandfeel&amp;act=emoticons&amp;code=emo_packsplash'    ),
			       );



?>