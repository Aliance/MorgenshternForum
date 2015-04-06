<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
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
+--------------------------------------------------------------------------
|
|   > Script written by Matthew Mecham
|   > Date started: 12th August 2004
|   > MYSQL EXTRA CONFIG / INSTALL FILE
+--------------------------------------------------------------------------
*/

$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('skin_id_cache', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('bbcode', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('moderators', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('multimod', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('banfilters', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('attachtypes', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('emoticons', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('forum_cache', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('badwords', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('systemvars', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('adminnotes', '', '', 0);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('ranks', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('group_cache', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('stats', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('profilefields', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('settings','', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('languages', '', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('birthdays', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('calendar', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('calendars', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('chatting', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('components', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('rss_export', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('rss_calendar', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('announcements', 'a:0:{}', '', 1);";
$INSERT[] = "INSERT INTO ibf_cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('portal', 'a:6:{s:5:\"blogs\";a:5:{s:8:\"pc_title\";s:30:\"Invision Power Community Blogs\";s:7:\"pc_desc\";s:39:\"Вывод информации из IPB Blog на портале\";s:19:\"pc_settings_keyword\";s:0:\"\";s:18:\"pc_exportable_tags\";a:1:{s:25:\"blogs_show_last_updated_x\";a:2:{i:0;s:25:\"blogs_show_last_updated_x\";i:1;s:54:\"Вывести блок с названиями последних обновленных блогов\";}}s:6:\"pc_key\";s:5:\"blogs\";}s:8:\"calendar\";a:5:{s:8:\"pc_title\";s:16:\"Календарь форума\";s:7:\"pc_desc\";s:37:\"Вывод мини-календаря на текущий месяц\";s:19:\"pc_settings_keyword\";s:0:\"\";s:18:\"pc_exportable_tags\";a:1:{s:27:\"calendar_show_current_month\";a:2:{i:0;s:27:\"calendar_show_current_month\";i:1;s:42:\"Вывести блок с календарем на текущий месяц\";}}s:6:\"pc_key\";s:8:\"calendar\";}s:7:\"gallery\";a:5:{s:8:\"pc_title\";s:22:\"Invision Power Gallery\";s:7:\"pc_desc\";s:53:\"Вывод информации из Invision Power Gallery на портале\";s:19:\"pc_settings_keyword\";s:0:\"\";s:18:\"pc_exportable_tags\";a:1:{s:25:\"gallery_show_random_image\";a:2:{i:0;s:25:\"gallery_show_random_image\";i:1;s:63:\"Вывести блок со случайными изображениями из галерей пользоватей\";}}s:6:\"pc_key\";s:7:\"gallery\";}s:12:\"online_users\";a:5:{s:8:\"pc_title\";s:22:\"Пользователи в он-лайн\";s:7:\"pc_desc\";s:58:\"Вывод числа пользователей и их имен, находящихся в он-лайн\";s:19:\"pc_settings_keyword\";s:0:\"\";s:18:\"pc_exportable_tags\";a:1:{s:17:\"online_users_show\";a:2:{i:0;s:17:\"online_users_show\";i:1;s:36:\"Вывести блок пользователей в он-лайн\";}}s:6:\"pc_key\";s:12:\"online_users\";}s:4:\"poll\";a:5:{s:8:\"pc_title\";s:14:\"Опрос с форума\";s:7:\"pc_desc\";s:43:\"Выводит выбранный с форума опрос на портале\";s:19:\"pc_settings_keyword\";s:11:\"portal_poll\";s:18:\"pc_exportable_tags\";a:1:{s:14:\"poll_show_poll\";a:2:{i:0;s:14:\"poll_show_poll\";i:1;s:22:\"Вывести блок с опросом\";}}s:6:\"pc_key\";s:4:\"poll\";}s:13:\"recent_topics\";a:5:{s:8:\"pc_title\";s:23:\"Последние темы с форума\";s:7:\"pc_desc\";s:49:\"Выводит последние темы с первым сообщением из них\";s:19:\"pc_settings_keyword\";s:20:\"portal_recent_topics\";s:18:\"pc_exportable_tags\";a:2:{s:20:\"recent_topics_last_x\";a:2:{i:0;s:20:\"recent_topics_last_x\";i:1;s:56:\"Вывести блок с последними темами из определенных форумов\";}s:32:\"recent_topics_discussions_last_x\";a:2:{i:0;s:32:\"recent_topics_discussions_last_x\";i:1;s:48:\"Вывести блок с последними темами из всех форумов\";}}s:6:\"pc_key\";s:13:\"recent_topics\";}}', '', 1);";


$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (1, ':mellow:', 'mellow.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (2, ':huh:', 'huh.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (3, '^_^', 'happy.gif', 0, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (4, ':o', 'ohmy.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (5, ';)', 'wink.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (6, ':P', 'tongue.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (7, ':D', 'biggrin.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (8, ':lol:', 'laugh.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (9, 'B)', 'cool.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (10, ':rolleyes:', 'rolleyes.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (11, '-_-', 'sleep.gif', 0, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (12, '&lt;_&lt;', 'dry.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (13, ':)', 'smile.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (14, ':wub:', 'wub.gif', 0, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (15, ':angry:', 'angry.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (16, ':(', 'sad.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (17, ':unsure:', 'unsure.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (18, ':wacko:', 'wacko.gif', 0, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (19, ':blink:', 'blink.gif', 1, 'default');";
$INSERT[] = "INSERT INTO ibf_emoticons (id, typed, image, clickable, emo_set) VALUES (20, ':ph34r:', 'ph34r.gif', 0, 'default');";

# FORUMS: Last field: newest_id
$INSERT[] = "INSERT INTO ibf_forums VALUES (2, 1, 1, <%time%>, 1, '<%admin_name%>', 'Тестовый форум', 'Тестовый форум может быть удален в любое время', 1, 1, 0, 1, '', '', 'Добро пожаловать!', 1, 'last_post', 'Z-A', 100, 'all', 0, 0, 1, 1, 1, NULL, 1, 1, '', 0, 0, '', '', '', '', '', 1, '', 'a:6:{s:11:\"start_perms\";s:1:\"*\";s:11:\"reply_perms\";s:1:\"*\";s:10:\"read_perms\";s:1:\"*\";s:12:\"upload_perms\";s:1:\"*\";s:14:\"download_perms\";s:1:\"*\";s:10:\"show_perms\";s:1:\"*\";}', 0, 0, 0, 0, 1, '', 0);";
$INSERT[] = "INSERT INTO ibf_forums VALUES (1, 0, 0, 0, 0, '', 'Тестовая категория', 'Тестовая категория может быть удалена в любое время'				, 1, 1, 0, 1, '', '', '', 0, 'last_post', 'Z-A', 30, 'all', 0, 0, 1, 1, 1, NULL, -1, 0, '', 0, 0, '', '', '', '', '', 0, '', 'a:5:{s:11:\"start_perms\";s:0:\"\";s:11:\"reply_perms\";s:0:\"\";s:10:\"read_perms\";s:0:\"\";s:12:\"upload_perms\";s:0:\"\";s:10:\"show_perms\";s:1:\"*\";}', 1, 0, 0, 1, 1, '', 0);";

$INSERT[] = "INSERT INTO ibf_forum_perms SET perm_name='Маска неактивированных', perm_id=1";
$INSERT[] = "INSERT INTO ibf_forum_perms SET perm_name='Маска пользователей', perm_id=3";
$INSERT[] = "INSERT INTO ibf_forum_perms SET perm_name='Маска гостей', perm_id=2";
$INSERT[] = "INSERT INTO ibf_forum_perms SET perm_name='Маска администраторов', perm_id=4";
$INSERT[] = "INSERT INTO ibf_forum_perms SET perm_name='Маска заблокированных', perm_id=5";

$INSERT[] = "INSERT INTO ibf_languages (lid, ldir, lname, lauthor, lemail) VALUES (1, 'ru', 'Русский', 'IBResource.ru', 'support@ibresource.ru');";
$INSERT[] = "INSERT INTO ibf_languages (lid, ldir, lname, lauthor, lemail) VALUES (2, 'en', 'English', 'Invision Power Board', 'languages@invisionboard.com');";

$INSERT[] = "INSERT INTO ibf_posts (pid, append_edit, edit_time, author_id, author_name, use_sig, use_emo, ip_address, post_date, icon_id, post, queued, topic_id, post_title, new_topic, edit_name, post_key, post_parent, post_htmlstate) VALUES (1, 0, NULL, 1, 'IBResource Team', 0, 1, '127.0.0.1', <%time%>, 0, 'Добро пожаловать в ваш новый форум &#151; Invision Power Board&#33; <br /><br /> Спасибо за приобретение русской версии Invision Power Board.  Пожалуйста, ознакомьтесь с документацией администатора IP.Board.  В ней вы найдете всю информацию о возможностях и настройках форума. <br /><br /> Созданные раздел, форум и это сообщение - тестовые, вы можете удалить их в любое время. <br /><br /> <a href=\"http://external.iblink.ru/docs-ipb\" target=\"_blank\">Перейти к документации...</a>', 0, 1, NULL, 1, NULL, '0', 0, 0);";

$INSERT[] = "INSERT INTO ibf_titles (id, posts, title, pips) VALUES (1, 0, 'Новичок', '1');";
$INSERT[] = "INSERT INTO ibf_titles (id, posts, title, pips) VALUES (2, 10, 'Участник', '2');";
$INSERT[] = "INSERT INTO ibf_titles (id, posts, title, pips) VALUES (4, 30, 'Активный участник', '3');";

# TOPICS: topic_rating_hits
$INSERT[] = "INSERT INTO ibf_topics VALUES (1, 'Добро пожаловать', '', 'open', 0, 1, <%time%>, 1, <%time%>, 0, '<%admin_name%>', '<%admin_name%>', '0', 0, 1, 2, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0);";

$INSERT[] = "INSERT INTO ibf_subscription_currency SET subcurrency_code='USD', subcurrency_desc='United States Dollars', subcurrency_exchange='1.00', subcurrency_default=1;";
$INSERT[] = "INSERT INTO ibf_subscription_currency SET subcurrency_code='GBP', subcurrency_desc='United Kingdom Pounds', subcurrency_exchange=' 0.550776', subcurrency_default=0;";
$INSERT[] = "INSERT INTO ibf_subscription_currency SET subcurrency_code='CAD', subcurrency_desc='Canada Dollars', subcurrency_exchange='1.37080', subcurrency_default=0;";
$INSERT[] = "INSERT INTO ibf_subscription_currency SET subcurrency_code='EUR', subcurrency_desc='Euro', subcurrency_exchange='0.901517', subcurrency_default=0;";


$INSERT[] = "INSERT INTO ibf_subscription_methods (submethod_id, submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES (1, 'PayPal', 'paypal', '', '', '', '', '', '', '', 0, 1, 'All major credit cards accepted. See <a href=\"https://www.paypal.com\" target=\"_blank\">PayPal</a> for more information.', '', 1, 'USD');";
$INSERT[] = "INSERT INTO ibf_subscription_methods (submethod_id, submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES (2, 'NOCHEX', 'nochex', '', '', '', '', '', '', '', 0, 1, 'UK debit and credit cards, such as Switch, Solo and VISA Delta. All prices will be convereted into GBP (UK Pounds) upon ordering.', NULL, 1, 'GBP');";
$INSERT[] = "INSERT INTO ibf_subscription_methods (submethod_id, submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES (3, 'Post Service', 'manual', '', '', '', '', '', '', '', 0, 0, 'You can use this method if you wish to send us a check, postal order or international money order.', NULL, 1, 'USD');";
$INSERT[] = "INSERT INTO ibf_subscription_methods (submethod_id, submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES (4, '2CheckOut', '2checkout', '', '', '', '', '', '', '', 1, 1, 'All major credit cards accepted. See <a href=\'http://www.2checkout.com/cgi-bin/aff.2c?affid=28376\' target=\'_blank\'>2CheckOut</a> for more information.', NULL, 1, 'USD');";
$INSERT[] = "INSERT INTO ibf_subscription_methods (submethod_id, submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES (5, 'Safshop', 'safshop', '', '', NULL, NULL, NULL, NULL, NULL, 0, 1, 'Accepts all major credit cards', NULL, 1, 'USD');";
$INSERT[] = "INSERT INTO ibf_subscription_methods (submethod_id, submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES (6, 'Protx', 'protx', '', '', '', '', '', '', '', 1, 1, 'Accepts all major credit cards', '', 1, 'GBP');";

$INSERT[] ="INSERT INTO ibf_cal_calendars (cal_id, cal_title, cal_moderate, cal_position, cal_event_limit, cal_bday_limit, cal_rss_export, cal_rss_export_days, cal_rss_export_max, cal_rss_update, cal_rss_update_last, cal_rss_cache, cal_permissions) VALUES (1, 'Основной календарь', 1, 0, 2, 1, 1, 14, 20, 1440, <%time%>, '', 'a:3:{s:9:\"perm_read\";s:1:\"*\";s:9:\"perm_post\";s:3:\"4,3\";s:10:\"perm_nomod\";s:0:\"\";}');";

$INSERT[] = "INSERT INTO ibf_upgrade_history (upgrade_version_id, upgrade_version_human, upgrade_date, upgrade_mid, upgrade_notes) VALUES ('".IPB_LONG_VERSION."', '".IPBVERSION."', '0', '0', '');";

?>