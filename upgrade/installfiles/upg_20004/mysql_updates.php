<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > IPB UPGRADE MODULE:: IPB 2.0.0 PDR1 -> PDR 2
|   > Script written by Matt Mecham
|   > Date started: 23rd April 2004
|   > "So what, pop is dead - it's no great loss.
	   So many facelifts, it's face flew off"
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}


$SQL = array();

$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES ('IPB Портал', 'Эти настройки позволят Вам включить/отключить и настроить IPB Портал.', 20, 0, 'ipbportal');";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Включить IPB портал?', 'Если «Да», IPB Портал будет доступен, используя следующую запись в URL строке — 'index.php?act=home', или через специальный php файл, который можно взять из директории /Tools_and_Scripts/ дистрибутива с форумом.', '22', 'yes_no', 'csite_on', '', '1', '', '', 1, 1, '', 0, '', 1);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Заголовок страницы портала', 'Это название будет выводиться в теге <title>, то есть, в заголовке окна браузера.', '22', 'input', 'csite_title', '', 'Портал IPB', '', '', 1, 2, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Формат времени для новостей', 'Подробнее о формате даты и времени на официальном сайте PHP: <a href=\'http://www.php.net/date\'>функция date</a>', '22', 'input', 'csite_article_date', '', 'm-j-y H:i', '', '', 1, 3, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Показывать панель пользователей/гостей?', '', '22', 'yes_no', 'csite_pm_show', '', '1', '', '', 1, 12, 'Компоненты портала', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Показывать блок с формой поиска?', '', '22', 'yes_no', 'csite_search_show', '', '1', '', '', 1, 14, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Показывать блок с меню выбора стиля?', '', '22', 'yes_no', 'csite_skinchange_show', '', '1', '', '', 1, 15, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Показывать навигационное меню?', '', '22', 'yes_no', 'csite_nav_show', '', '1', '', '', 1, 18, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Ссылки навигационного меню', 'Используйте одну ссылку на строку, вводите в следующем формате:<br />http://www.ibresource.ru [IBResource]<br /><br />Запись {board_url} будет заменена на URL вашего форума', '22', 'textarea', 'csite_nav_contents', '', '{board_url} [Форумы]\r\n{board_url}act=Search&CODE=getactive [Активные темы]\r\n{board_url}act=Stats [Лучшие сегодня]\r\n{board_url}act=Stats&CODE=leaders [Администрация форума]', '', 'if ( $show == 1)\r\n{\r\n    $value = preg_replace( \"/&(middot|quot|copy|amp)/\", \"&\\\\1\", $value );\r\n}', 1, 19, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Показывать блок с рекомендуемыми ссылками?', '', '22', 'yes_no', 'csite_fav_show', '', '0', '', '', 1, 20, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('Содержание блока с рекомендуемыми ссылками', 'Например, ссылки на сайты ваших партнеров.', '22', 'textarea', 'csite_fav_contents', '', '', '', 'if ( $show == 1)\r\n{\r\n $value = preg_replace( \"/&(middot|quot|copy|amp)/\", \"&\\\\1\", $value );\r\n}', 1, 21, '', 1, '', 0);";



?>