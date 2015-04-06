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
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
*/

//===========================================================================
// Небольшая библиотека, контролирующая все ссылки админцентра
// Invision Power Board
//===========================================================================

// CAT_ID => array(  PAGE_ID  => (PAGE_NAME, URL ) )

// $PAGES[ $cat_id ][$page_id][0] = Название страницы
// $PAGES[ $cat_id ][$page_id][1] = Ссылка
// $PAGES[ $cat_id ][$page_id][2] = Просмотр директории перед отображением
// $PAGES[ $cat_id ][$page_id][3] = Тип ссылки: 1 = форум 0 = админцентр
// $PAGES[ $cat_id ][$page_id][4] = Иконка меню: 1 = переадресация 0 = нормальная

$PAGES = array(
				# НАСТРОЙКИ
				
				100 => array (
							
							 1 => array( 'Список настроек', 'act=op' ),
							 2 => array( 'Добавить настройку'  , 'act=op&code=settingnew' ),
							 7 => array( 'Включение/выключение'      , 'act=op&code=findsetting&key='.urlencode('boardoffline/online'), '', 0, 1 ),
							 8 => array( 'Правила форума'         , 'act=op&code=findsetting&key='.urlencode('boardguidelines'), '', 0, 1 ),
							 9 => array( 'Основные настройки'    , 'act=op&code=findsetting&key='.urlencode('generalconfiguration'), '', 0, 1 ),
							 10 => array( 'Настройки быстродействия'              , 'act=op&code=findsetting&key='.urlencode('cpusaving&optimization'), '', 0, 1 ),
							 //11 => array( 'IP Chat'                 , 'act=pin&code=ipchat'  ),
							 12 => array( 'Регистрация IPB'             , 'act=pin&code=reg'     ),
							 //14 => array( 'IPB Удаление копирайта'   , 'act=pin&code=copy'    ),
						   ),
						   
			    # УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ
						   
				200 => array (
							 1 => array( 'Создать форум'             , 'act=forum&code=new'       ),
							 2 => array( 'Список форумов'         , 'act=forum'                ),
							 3 => array( 'Маски доступа'      , 'act=group&code=permsplash'),
							 6 => array( 'Модераторы'            , 'act=mod'                  ),
							 7 => array( 'Мульти-модерация тем', 'act=multimod'          ),
							 8 => array( 'Настройки корзины'      , 'act=op&code=findsetting&key=trashcanset-up', '', 0, 1 ),
						   ),
						   
						   
				300 => array (
				            1  => array ( 'Поиск и редактирование'        , 'act=mem&code=search' ),
							2  => array ( 'Добавить пользователя'        , 'act=mem&code=add'  ),
							6  => array ( 'Звания'          , 'act=mem&code=title'),
							7  => array ( 'Группы'    , 'act=group'         ),
							8  => array ( 'Неактивированные'     , 'act=mem&code=mod'  ),
							9  => array ( 'Дополнительные поля' , 'act=field'         ),
							11 => array ( 'Инструменты IP'       , 'act=mtools'        ),
							12 => array ( 'Профили пользователей'       , 'act=op&code=findsetting&key=userprofiles', '', 0, 1 ),
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

				# УПРАВЛЕНИЕ ПУБЛИКАЦИЕЙ
				
				500 => array (
							1 => array( 'Типы файлов'      , 'act=attach&code=types'  ),
							2 => array( 'Статистика'      , 'act=attach&code=stats'  ),
							3 => array( 'Поиск'     , 'act=attach&code=search'  ),
				  			),
				  			
				  			
				600 => array(
							1 => array( 'Список BB-кодов' , 'act=admin&code=bbcode'        ),
							2 => array( 'Добавить BB-код'        , 'act=admin&code=bbcode_add'    ),
						   ),
						   
				700 => array(
							1 => array( 'Список папок со смайликами'      , 'act=admin&code=emo'               ),
							2 => array( 'Импорт и экспорт'   , 'act=admin&code=emo_packsplash'    ),
						   ),		   
						   
				800 => array (
							1 => array( 'Нецензурные слова', 'act=admin&code=badword'     ),
							6 => array( 'Блокировка'    , 'act=admin&code=ban'  ),
							),		
				
				# СТИЛИ И ЯЗЫКИ
				
				900 => array (
							1 => array( 'Список стилей'            , 'act=sets'        ),
							2 => array( 'Инструменты'              , 'act=skintools'   ),
							3 => array( 'Поиск и замена'   , 'act=skintools&code=searchsplash'   ),
							4 => array( 'Импорт и экспорт'      , 'act=import'      ),
							5 => array( 'Простая смена логотипа'       , 'act=skintools&code=easylogo'   ),
						   ),
						   			
				1000 => array (
							1 => array( 'Список языков'        , 'act=lang'             ),
							2 => array( 'Импортирование'       , 'act=lang&code=import' ),
						   ),
				
				
				# АДМИНИСТРАТИРОВАНИЕ
						   
				1100 => array (
							1 => array( 'Разделы помощи'     , 'act=help'                   ),
							2 => array( 'Управление кешем'         , 'act=admin&code=cache'       ),
							3 => array( 'Пересчет и обновление'     , 'act=rebuild'                ),
							4 => array( 'Инструменты очистки'        , 'act=rebuild&code=tools'     ),
						   ),
						   
			    1200 => array(
			    			1  => array( 'Список рассылок'      , 'act=postoffice'                    ),
			    			2  => array( 'Создать рассылку'      , 'act=postoffice&code=mail_new'      ),
	    					3  => array( 'Журнал e-mail отправлений'       , 'act=emaillog', '', 0, 1 ),
			    			4  => array( 'Журнал e-mail ошибок' , 'act=emailerror', '', 0, 1 ),
			    			5  => array( 'Настройки e-mail '        , 'act=op&code=findsetting&key=emailset-up', '', 0, 1 ),
			    		    ),
			    
			    1300 => array (
							 1 => array( 'Список задач'        , 'act=task'                ),
							 2 => array( 'Журнал выполненных задач '      , 'act=task&code=log'       ),
						   ),
				
				
				1400 => array(
							 1 => array( 'Invision Gallery'        , 'act=gallery' ),
							 2 => array( '|-- Настройки'            , 'act=op&code=findsetting&key='.urlencode('Настройки галереи'), '', 0, 0 ),
							 3 => array( '|-- Менеджер альбомов'       , 'act=gallery&code=albums'  , 'modules/gallery' ),
							 4 => array( '|-- Менеджер мультимедиа'  , 'act=gallery&code=media'   , 'modules/gallery' ),
							 5 => array( '|-- Группы'              , 'act=gallery&code=groups'  , 'modules/gallery' ),
							 6 => array( '|-- Статистика'               , 'act=gallery&code=stats'   , 'modules/gallery' ),
							 7 => array( '|-- Инструменты'               , 'act=gallery&code=tools'   , 'modules/gallery' ),
							 8 => array( '&#039;-- Поля загрузки'      , 'act=gallery&code=postform', 'modules/gallery' ),
						   ),
						   
				1450 => array(
							 1 => array( 'Community Blog'          , 'act=blog' ),
							 2 => array( 'Настройки'           , 'act=op&code=findsetting&key='.urlencode('communityblog'), '', 0, 1 ),
							 3 => array( 'Группы'				   , 'act=blog&amp;cmd=groups' ),
							 4 => array( 'Блоки'		   , 'act=blog&amp;cmd=cblocks' ),
							 5 => array( 'Инструменты'				   , 'act=blog&amp;cmd=tools' ),
						   ),
				
				1500 => array (
							1 => array( 'Регистраций' , 'act=stats&code=reg'   ),
							2 => array( 'Новых тем'    , 'act=stats&code=topic' ),
							3 => array( 'Сообщений'         , 'act=stats&code=post'  ),
							4 => array( 'Личных сообщений'   , 'act=stats&code=msg'   ),
							5 => array( 'Просмотров тем'        , 'act=stats&code=views' ),
						   ),
						   
				1600 => array (
							1 => array( 'Инструменты'     , 'act=mysql'           ),
							2 => array( 'Резервная копия'     , 'act=mysql&code=backup'    ),
							3 => array( 'Информация о сервере', 'act=mysql&code=runtime'   ),
							4 => array( 'Системные переменные' , 'act=mysql&code=system'    ),
							5 => array( 'Процессы'   , 'act=mysql&code=processes' ),
						   ),
				
				1700 => array(
							1 => array( 'Модерирование'  , 'act=modlog'    ),
							2 => array( 'Администратирование'      , 'act=adminlog'  ),
							3 => array( 'E-mail отправления'      , 'act=emaillog'  ),
							4 => array( 'E-mail ошибки', 'act=emailerror' ),
							5 => array( 'Поисковые роботы'        , 'act=spiderlog' ),
							6 => array( 'Предупреждения'       , 'act=warnlog'   ),
						   ),
			   );
			   
			   
$CATS = array (   
				  100 => array( "Системные настройки"   , '#caf2d9;margin-bottom:12px;' ),
				  
				  200 => array( 'Управление форумами'     , '#F9FFA2' ),
				  300 => array( 'Пользователи и группы'  , '#F9FFA2' ),

				  //400 => array( "Подписки"     , '#F9FFA2;margin-bottom:12px;' ),

				  500 => array( "Прикрепляемые файлы"       , '#f5cdcd' ),
				  600 => array( "Дополнительные BB-коды"     , '#f5cdcd' ),
				  700 => array( "Смайлики"         , '#f5cdcd' ),
				  800 => array( "Фильтры", '#f5cdcd;margin-bottom:12px;' ),
				  
				  900 => array( 'Стили' , '#DFE6EF' ),
				  1000 => array( 'Языки'        , '#DFE6EF;margin-bottom:12px;' ),
				  
				  1100 => array( 'Дополнительно'      , '#caf2d9' ),
				  1200 => array( 'Работа с e-mail'      , '#caf2d9' ),
				  1300 => array( 'Менеджер задач'     , '#caf2d9;margin-bottom:12px;' ),
				  
				  1400 => array( "Invision Gallery" , '#F9FFA2;' ),
				  1450 => array( "Community Blog"   , '#F9FFA2;margin-bottom:12px;' ),
				  
				  1500 => array( 'Статистика' , '#f5cdcd' ),
				  1600 => array( 'Управление SQL'   , '#f5cdcd' ),
				  1700 => array( 'Журналы операций'       , '#f5cdcd' ),
			  );
			  

			  
$DESC = array (
				  100 => "Настройки форума, такие как настройка cookies, возможности по безопасности, возможности публикации сообщений и т.д.",
				  
				  200 => "Создание, изменение, удаление и сортировка категорий, форумов и модераторов",
				  300 => "Управление пользователями, группами и званиями",

				  //400 => "Управление подписками ваших пользователей",

				  500 => "Управление прикрепляемыми файлами",
				  600 => "Управление дополнительнами BB-кодами",
				  700 => "Управление смайликами, экспорт и импорт групп смайликов",
				  800 => "Управление фильтрами банов и нецензурных слов",
				  
				  900 => "Управление шаблонами, стилями, цветами и изображениями",
				  1000 => "Управление языковыми файлами",
				  
				  1100 => "Управление разделами помощи, фильтрами нецензурных слов и смайликами",
				  1200 => "Управление e-mail и рассылками",
				  1300 => "Управление запланированными задачами",
				  
		  		  1400 => "Управление вашей галереей",
				  1450 => "Управление вашими блогами",
				  
				  1500 => "Получение статистики регистраций и публикации сообщений",
				  1600 => "Управление вашей базой данных, починка, оптимизация и экспорт данных",
				  1700 => "Просмотр журнала операций администраторов, модераторов и e-mail (только главные администраторы)", 			  );
?>