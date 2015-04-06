<?php
/*
СДЕЛАНО BY ELFET (Медведев Антон)
Можите делать всё что хотите с этим
чатом, только оставте эти строчки
мыло для связи - elfet@yandex.ru
*/



//файл конфигурации

define('DN', 'morgens_forum');             //database name
define('H', 'mysql3.hostportal.biz');         //database host
define('UN', 'morgens_ipb');             //database user name
define('P', 'Ajhev');                  //database password

define('IPB_PREF', 'ibf_');     //префикс форума в БД (тот что вы указывали при создании)

define('TIME_OUT', '4');          //время обновления окна чата (сек.)
define('TIME_OUT_HERE', '30');    //время обновления списка "в комнате" (сек.)
define('SAVE_TIME', '4');         //время хранения строк в окне чата (мин.)
define('K_WIDE', '3');            //количиство видов смайликов, в одном сообщении (кол.)
define('FLUD', '5');              //влуд контроль (сек.)
define('LOGO', 'logo5.jpg');      //логотип
define('PON', 'вним');            //имя тегов красного текста
define('LIL', 'LIL');             //имя папки со смайликами

//Языковые настройки
define('ONLY', 'private');
define('YOU', 'вам');
define('INROOM', 'В комнате');
define('ANOVER', 'Другие >');
define('WELCOME', 'Привет!');
define('RESET', 'Обновить');
define('LOGOALT', '(с)');
define('INTOIN', '('.date("H:i",time()).') - вхожу...');
define('NOWID', 'Необходимо выполнить вход или зарегистрироваться на форуме!');

?>