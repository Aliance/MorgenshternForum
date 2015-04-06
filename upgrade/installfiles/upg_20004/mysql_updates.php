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
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}


$SQL = array();

$SQL[] = "INSERT INTO ibf_conf_settings_titles (conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES ('IPB ������', '��� ��������� �������� ��� ��������/��������� � ��������� IPB ������.', 20, 0, 'ipbportal');";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('�������� IPB ������?', '���� ���, IPB ������ ����� ��������, ��������� ��������� ������ � URL ������ � 'index.php?act=home', ��� ����� ����������� php ����, ������� ����� ����� �� ���������� /Tools_and_Scripts/ ������������ � �������.', '22', 'yes_no', 'csite_on', '', '1', '', '', 1, 1, '', 0, '', 1);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('��������� �������� �������', '��� �������� ����� ���������� � ���� <title>, �� ����, � ��������� ���� ��������.', '22', 'input', 'csite_title', '', '������ IPB', '', '', 1, 2, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('������ ������� ��� ��������', '��������� � ������� ���� � ������� �� ����������� ����� PHP: <a href=\'http://www.php.net/date\'>������� date</a>', '22', 'input', 'csite_article_date', '', 'm-j-y H:i', '', '', 1, 3, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('���������� ������ �������������/������?', '', '22', 'yes_no', 'csite_pm_show', '', '1', '', '', 1, 12, '���������� �������', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('���������� ���� � ������ ������?', '', '22', 'yes_no', 'csite_search_show', '', '1', '', '', 1, 14, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('���������� ���� � ���� ������ �����?', '', '22', 'yes_no', 'csite_skinchange_show', '', '1', '', '', 1, 15, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('���������� ������������� ����?', '', '22', 'yes_no', 'csite_nav_show', '', '1', '', '', 1, 18, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('������ �������������� ����', '����������� ���� ������ �� ������, ������� � ��������� �������:<br />http://www.ibresource.ru [IBResource]<br /><br />������ {board_url} ����� �������� �� URL ������ ������', '22', 'textarea', 'csite_nav_contents', '', '{board_url} [������]\r\n{board_url}act=Search&CODE=getactive [�������� ����]\r\n{board_url}act=Stats [������ �������]\r\n{board_url}act=Stats&CODE=leaders [������������� ������]', '', 'if ( $show == 1)\r\n{\r\n    $value = preg_replace( \"/&(middot|quot|copy|amp)/\", \"&\\\\1\", $value );\r\n}', 1, 19, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('���������� ���� � �������������� ��������?', '', '22', 'yes_no', 'csite_fav_show', '', '0', '', '', 1, 20, '', 0, '', 0);";
$SQL[] = "INSERT INTO ibf_conf_settings (conf_title, conf_description, conf_group, conf_type, conf_key, conf_value, conf_default, conf_extra, conf_evalphp, conf_protected, conf_position, conf_start_group, conf_end_group, conf_help_key, conf_add_cache) VALUES ('���������� ����� � �������������� ��������', '��������, ������ �� ����� ����� ���������.', '22', 'textarea', 'csite_fav_contents', '', '', '', 'if ( $show == 1)\r\n{\r\n $value = preg_replace( \"/&(middot|quot|copy|amp)/\", \"&\\\\1\", $value );\r\n}', 1, 21, '', 1, '', 0);";



?>