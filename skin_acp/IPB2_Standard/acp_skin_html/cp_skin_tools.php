<?php

class cp_skin_tools {

var $ipsclass;


//===========================================================================
// Menu manage:Blank Pos
//===========================================================================
function components_position_blank($com_id) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<img src='{$this->ipsclass->skin_acp_url}/images/blank.gif' width='12' height='12' border='0' style='vertical-align:middle' />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Menu manage:Blank Pos
//===========================================================================
function components_position_up($com_id) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_move&amp;move=up&amp;com_id={$com_id}' title='Поднять'><img src='{$this->ipsclass->skin_acp_url}/images/arrow_up.png' width='12' height='12' border='0' style='vertical-align:middle' /></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Menu manage:Blank Down
//===========================================================================
function components_position_down($com_id) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_move&amp;move=down&amp;com_id={$com_id}' title='Опустить'><img src='{$this->ipsclass->skin_acp_url}/images/arrow_down.png' width='12' height='12' border='0' style='vertical-align:middle' /></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Component FORM
//===========================================================================
function components_form($form, $title, $formcode, $button, $component, $menu_text, $menu_url, $menu_redirect, $menu_permbit, $menu_permlang) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript" src='{$this->ipsclass->skin_acp_url}/acp_components.js'></script>
<script type="text/javascript">

  var comp = new components();
  // Title
  var menu_text     =
  {
{$menu_text}
  };
  
  var menu_url      = 
  {
{$menu_url}
  };
  
  var menu_redirect = 
  {
{$menu_redirect}
  };
  
   var menu_permbit = 
  {
{$menu_permbit}
  };
  
   var menu_permlang = 
  {
{$menu_permlang}
  };
  
  // HTML elements
  var html_add_menu_row     = "<br />[<a href='#' title='Добавить новый пункт меню' style='color:green;font-weight:bold' onclick='return comp.add_menu_row()'>Добавить новый пункт меню</a>]";
  var html_box_text         = "<tr><td width='20%'>Текст меню</td><td><input type='text' id='menu_text_<%1>' name='menu_text[<%1>]' size='30' class='forminput' value='<%2>' /></td></tr>";
  var html_box_url          = "<tr><td width='20%'>URL меню</td><td><input type='text' id='menu_url_<%1>' name='menu_url[<%1>]' size='30' class='forminput' value='<%2>' /></td></tr>";
  var html_box_redirect     = "<tr><td width='20%'>Переадресация?</td><td><input type='checkbox' id='menu_redirect_<%1>' name='menu_redirect[<%1>]' class='forminput' value='1' /></td></tr>";
  var html_box_permbit      = "<tr><td width='20%'>Название переменной</td><td><input type='text' id='menu_permbit_<%1>' name='menu_permbit[<%1>]' size='30' class='forminput' value='<%2>' /></td></tr>";
  var html_box_permlang     = "<tr><td width='20%'>Текст переменной</td><td><input type='text' id='menu_permlang_<%1>' name='menu_permlang[<%1>]' size='30' class='forminput' value='<%2>' /></td></tr>";
  var html_menu_wrap        = "<div><fieldset style='padding:4px'><table cellpadding='2' cellspacing='0' width='100%'><tr><%1></tr></table>[<a href='#' title='Удалить ячейку меню' style='color:red;font-weight:bold' onclick='return comp.remove_menu_row("+'"'+'<%2>'+'"'+")'>Удалить ячейку меню</a>]</fieldset></div>";
 </script>
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=$formcode&amp;com_id={$component['com_id']}' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>$title</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td width='40%' class='tablerow1'><strong>Название компонента</strong></td>
   <td width='60%' class='tablerow2'>{$form['com_title']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Версия компонента</strong></td>
   <td width='60%' class='tablerow2'>{$form['com_version']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Описание компонента</strong></td>
   <td width='60%' class='tablerow2'>{$form['com_description']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Автор компонента</strong></td>
   <td width='60%' class='tablerow2'>{$form['com_author']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>URL разработчика компонента</strong></td>
   <td width='60%' class='tablerow2'>{$form['com_url']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1' valign='top'><strong>Данные меню компонента</strong>
   	<div class='desctext'>
   	<a href='#' onclick='pop_win("act=quickhelp&id=comp_menu", "help", 250, 400 )'>Подробнее о параметрах меню</a>
   	</div>
   </td>
   <td width='60%' class='tablerow2'>
   <div id='components-menu-box'>
   
   </div>
   <!--{$form['com_menu_data']}-->
   
   </td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>URL компонента на страницах форума</strong><div class='desctext'>{ipb.base_url} - будет заменено на URL форума (включая ID сессии, если есть)</div></td>
   <td width='60%' class='tablerow2'>{$form['com_url_uri']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Заголовок компонента на страницах форума</strong><div class='desctext'>{ipb.lang['some_words']} - будет заменено на текст из лэнгов из переменной "some_words"</div></td>
   <td width='60%' class='tablerow2'>{$form['com_url_title']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Включить компонент?</strong></td>
   <td width='60%' class='tablerow2'>{$form['com_enabled']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Секция кода компонента</strong><div class='desctext'>Эта секция кода, которая загружает соответствующий файл. Код каждого компонента должен быть уникальным и название файла должно быть - component_* (не включая .php).</div></td>
   <td width='60%' class='tablerow2'>{$form['com_section']}</td>
 </tr>
 <!--<tr>
   <td width='40%' class='tablerow1'><strong>PHP файл компонента</strong><div class='desctext'>Имя соответствующего php-файла в  'sources/components_*/'</div></td>
   <td width='60%' class='tablerow2'>{$form['com_filename']}<strong>.php</strong></td>
 </tr>-->
EOF;
//startif
if ( $form['com_safemode'] != '' )
{		
$IPBHTML .= <<<EOF
<tr>
   <td width='40%' class='tablerow1'><strong>Включить безопасный режим?</strong><div class='desctext'>Будет невозможно удалить или редактировать</div></td>
   <td width='60%' class='tablerow2'>{$form['com_safemode']}</td>
 </tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='$button' /></div>
</div>
 <script type="text/javascript">
  comp.init();
 </script>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// COMPONENTS: Overview
//===========================================================================
function component_overview( $content ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Зарегистрированные компоненты</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='30%'>Название</td>
  <td class='tablesubheader' width='25%'>Автор</td>
  <td class='tablesubheader' width='5%' align='center'>Позиция</td>
  <td class='tablesubheader' width='5%'>Статус</td>
  <td class='tablesubheader' width='5%'><img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
<br />
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_import' enctype='multipart/form-data' method='POST'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<input type='hidden' name='MAX_FILE_SIZE' value='10000000000' />
<div class='tableborder'>
 <div class='tableheaderalt'>Импортирование XML файла компонента</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td class='tablerow1'><strong>Загрузить XML файл компонентов с вашего компьютера</strong><div class='desctext'>Одинаковые записи не будут перезаписаны, но настройки по умолчанию будут обновлены. Файл обязан начинаться с 'ipd_' и заканчиваться на '.xml' или '.xml.gz'</div></td>
  <td class='tablerow2'><input class='textinput' type='file' size='30' name='FILE_UPLOAD' /></td>
 </tr>
 <tr>
  <td class='tablerow1'><strong><u>ИЛИ</u> введите имя файла с XML настройками</strong><div class='desctext'>Файл должен быть загружен в корневую директорию форума</div></td>
  <td class='tablerow2'><input class='textinput' type='text' size='30' name='file_location' /></td>
 </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='Импортировать' /></div>
</div>
</form>

 <script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_add   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_add'>Добавить новый компонент</a>" ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// COMPONENTS: row
//===========================================================================
function component_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'><strong>{$data['_fullname']}</strong><div class='desctext'>{$data['com_description']}</td>
 <td class='tablerow2'>{$data['_fullauthor']}</td>
 <td class='tablerow2' align='center' nowrap='nowrap'>{$data['_pos_up']} &nbsp; {$data['_pos_down']}</td>
 <td class='tablerow2' align='center'>
  <a href={$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_toggle_enabled&amp;com_id={$data['com_id']}' title='Включить/Выключить'><img src='{$this->ipsclass->skin_acp_url}/images/{$data['_enabled_img']}' border='0' alt='ДН' class='ipd' /></a>
 </td>
 <td class='tablerow1'><img id="menu{$data['com_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['com_id']}",
  new Array(
EOF;
//startif
if ( ! $data['com_safemode'] OR ( $data['com_safemode'] AND IN_DEV ) )
{		
$IPBHTML .= <<<EOF
			img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_edit&amp;com_id={$data['com_id']}'>Изменить</a>",
			img_delete   + " <a href='#' onclick='maincheckdelete(\"{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_delete&amp;com_id={$data['com_id']}\",\"Вы действительно хотите удалить этот компонент?\")'>Удалить</a>",
EOF;
}//endif
$IPBHTML .= <<<EOF
  			 img_export   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=component_export&amp;com_id={$data['com_id']}'>Экспортировать в формате XML</a>"
  			 ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// CACHE: Overview
//===========================================================================
function cache_overview( $content ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Системные кеши</div>
  <div class='tablesubheader' style='padding-right:0px;height:25px'>
    <div style='float:right;padding-right:12px'>
     <span class='desctext'>Размер</span> &nbsp;&nbsp;
     <img id='menumainone' src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' />
    </div>
    <div style='padding-top:6px'>Тип кеша</div>
  </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
 <script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=cache_update_all'>Обновить все кеши</a>" ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// CACHE: row
//===========================================================================
function cache_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
 	<div style='float:left;'>
 	 <img src='{$this->ipsclass->skin_acp_url}/images/menu_item.gif' class='ipd' /> <strong>{$data['cs_key']}</strong><div class='desctext'>{$data['_desc']}</div>
 	</div>
 	 <div align='right' style='height:18px;padding:0px 5px 2px 0px;'>
	   <span class='desctext'>{$data['_size']}kb</span> &nbsp;
	   <img id="menu{$data['cs_key']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' />
	</div>
  </td>
 <script type="text/javascript">
 menu_build_menu(
  "menu{$data['cs_key']}",
  new Array( img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=cacheend&amp;cache={$data['cs_key']}'>Обновить кеш</a>",
			 img_view   + " <a href='#' onclick='pop_win(\"{$this->ipsclass->form_code}&amp;code=viewcache&amp;cache={$data['cs_key']}\",\"Просмотр\", 400,600)'>Просмотр содержимого кеша</a>"
		    ) );
 </script>
 </tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOGIN: Overview
//===========================================================================
function login_overview( $content ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Методы авторизации</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='1%'>&nbsp;</td>
  <td class='tablesubheader' width='90%'>Название</td>
  <td class='tablesubheader' width='5%' nowrap='nowrap'>Установлено?</td>
  <td class='tablesubheader' width='5%' nowrap='nowrap'>Включено?</td>
  <td class='tablesubheader' width='5%'><img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>

 <script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_add   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=login_add'>Добавить новый метод</a>" ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOGIN: Overview
//===========================================================================
function login_diagnostics( $login=array() ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Отчет о проверке «{$login['login_title']}»</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
   <td width='49%' valign='top'>
   <table cellpadding='0' cellspacing='0' width='100%'>
   <tr>
	<td class='tablesubheader' width='70%'>&nbsp;</td>
	<td class='tablesubheader' width='30%'>&nbsp;</td>
   </tr>
   <tr>
    <td class='tablerow1'><strong>Авторизация включена?</strong></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_enabled_img']}' border='0' alt='*' class='ipd' /></td>
   </tr>
   <tr>
    <td class='tablerow1'><strong>Авторизация установлена?</strong></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_installed_img']}' border='0' alt='*' class='ipd' /></td>
   </tr>
   <tr>
    <td class='tablerow1'><strong>Авторизация имеет настройки?</strong></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_has_settings']}' border='0' alt='*' class='ipd' /></td>
   </tr>
   </table>
   <div align='center' class='tablefooter'>&nbsp;</div>
  </td>
  <td width='2%' class='tablefooter'>&nbsp;</td>
  <td width='49%' valign='top'>
   <table cellpadding='0' cellspacing='0' width='100%'>
   <tr>
	<td class='tablesubheader' width='60%'>Название файла</td>
	<td class='tablesubheader' width='10%' align='center'>Существует?</td>
	<td class='tablesubheader' width='30%' align='center'>Запись в файл?</td>
   </tr>
   <tr>
    <td class='tablerow1'><strong>./sources/loginauth/{$login['login_folder_name']}/auth.php</strong></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_file_auth_exists']}' border='0' alt='*' class='ipd' /></td>
    <td class='tablerow2' align='center'>-</td>
   </tr>
   <tr>
    <td class='tablerow1'><strong>./sources/loginauth/{$login['login_folder_name']}/acp.php</strong></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_file_acp_exists']}' border='0' alt='*' class='ipd' /></td>
    <td class='tablerow2' align='center'>-</td>
   </tr>
   <tr>
    <td class='tablerow1'><strong>./sources/loginauth/{$login['login_folder_name']}/conf.php</strong></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_file_conf_exists']}' border='0' alt='*' class='ipd' /></td>
    <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$login['_file_conf_write']}' border='0' alt='*' class='ipd' /></td>
   </tr>
   </table>
   <div align='center' class='tablefooter'>&nbsp;</div>
  </td>
 </tr>
 </table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOGIN FORM
//===========================================================================
function login_form($form, $title, $formcode, $button, $login) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=$formcode&amp;login_id={$login['login_id']}' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>$title</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td width='40%' class='tablerow1'><strong>Название метода</strong></td>
   <td width='60%' class='tablerow2'>{$form['login_title']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Описание</strong><div class='desctext'>Краткое описание метода авторизации</div></td>
   <td width='60%' class='tablerow2'>{$form['login_description']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Имя директории</strong><div class='desctext'>Основная директория с PHP файлами.<br />Пример: Если ./sources/loginauth/<strong>internal</strong>/auth.php тогда вводите: internal</div></td>
   <td width='60%' class='tablerow2'>{$form['login_folder_name']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Служебный URL для пользователей</strong><div class='desctext'>URL места, где пользователи смогут изменить их пароль и e-mail.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_maintain_url']} <div class='desctext'>(Опционально для совместного типа)</div></td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>URL регистрации</strong><div class='desctext'>URL места, где будет расположена регистрация пользователей.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_register_url']} <div class='desctext'>(Опционально для совместного типа)</div></td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Тип авторизации?</strong><div class='desctext'>Удаленный: Этот метот всегда использует запросы к удаленной базе для авторизации пользователя. Все учетные записи должны быть сделаны удаленно.<br />Совместный: Если данные о пользователе не представлены в базе данных IPB, форум будет запрашивать удаленную базу.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_type']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>HTML форма авторизации</strong><div class='desctext'>Введите HTML код формы авторизации для ее использования при входе.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_alt_login_html']}</td>
 </tr>
 <!--<tr>
   <td width='40%' class='tablerow1'><strong>Заменить HTML формой выше?</strong><div class='desctext'>Если «Да», то введенный выше HTML код заменит обычную форму авторизации. Если 'Нет', код будет добавлен рядом с обычной формой авторизации.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_replace_form']}</td>
 </tr>-->
 <tr>
   <td width='40%' class='tablerow1'><strong>Ссылка авторизации для пользователей</strong><div class='desctext'>Ссылка места, где будет происходить авторизация.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_login_url']} <div class='desctext'></div></td>
 </tr>
<tr>
   <td width='40%' class='tablerow1'><strong>Ссылка завершения сеанса для пользователей</strong><div class='desctext'>Ссылка места, где будет происходить завершение сеанса.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_logout_url']} <div class='desctext'></div></td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Включить метод?</strong><div class='desctext'>Если «Да», этот метод авторизации будет включен, а любой текущий отключится автоматически.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_enabled']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Разрешить создание пользователя?</strong><div class='desctext'>Если «Да», авторизованный пользователь, которого нет в базе данных IPB, будет туда внесен автоматически.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_user_id']}</td>
 </tr>
  <!--<tr>
   <td width='40%' class='tablerow1'><strong>Наличие настроек</strong><div class='desctext'>Если «Да», acp.php и conf.php обязаны существовать и быть доступны для записи форумом (смотрите диагностику для проверки).</div></td>
   <td width='60%' class='tablerow2'>{$form['login_settings']}</td>
 </tr>-->
EOF;
//startif
if ( $form['login_safemode'] != '' )
{		
$IPBHTML .= <<<EOF
<tr>
   <td width='40%' class='tablerow1'><strong>Включить безопасный режим?</strong><div class='desctext'>Невозможно удаление или редактирование.</div></td>
   <td width='60%' class='tablerow2'>{$form['login_safemode']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Флаг установлен?</strong><div class='desctext'>Невозможно удаление или редактирование</div></td>
   <td width='60%' class='tablerow2'>{$form['login_installed']}</td>
 </tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='$button' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOGIN: row
//===========================================================================
function login_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'><img src='{$this->ipsclass->skin_acp_url}/images/lock_close.gif' border='0' alt='*' class='ipd' /></td>
 <td class='tablerow1'><strong>{$data['login_title']}</strong><div class='desctext'>{$data['login_description']}</div></td>
 <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$data['_installed_img']}' border='0' alt='YN' class='ipd' /></td>
 <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$data['_enabled_img']}' border='0' alt='YN' class='ipd' /></td>
 <td class='tablerow1'><img id="menu{$data['login_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['login_id']}",
  new Array(
EOF;
//startif
if ( ($data['login_safemode'] AND IN_DEV) or $data['login_installed'] )
{		
$IPBHTML .= <<<EOF
			 img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=login_edit_details&amp;login_id={$data['login_id']}'>Изменить</a>",
EOF;
}//endif
//startif
if ( $data['login_installed'] != 1 )
{		
$IPBHTML .= <<<EOF
			 img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=login_install&amp;login_id={$data['login_id']}'>Установка</a>",
EOF;
}//endif
//startif
if ( $data['login_installed'] == 1 ) // NOT USED??
{		
//$IPBHTML .= <<<EOF
//			 img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=login_edit_settings&amp;login_id={$data['login_id']}'>Настройки</a>",
// 			 img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=login_export&amp;login_id={$data['login_id']}'>Экспортировать</a>"
//EOF;
}//endif
$IPBHTML .= <<<EOF
			 img_view   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=login_diagnostics&amp;login_id={$data['login_id']}'>Диагностика</a>"
  			 ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// PORTAL: Overview
//===========================================================================
function portal_pop_overview( $title, $content ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>{$this->ipsclass->acp_lang['portal_pop_tags']} {$title}</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='30%'>{$this->ipsclass->acp_lang['portal_pop_name']}</td>
  <td class='tablesubheader' width='70%'>{$this->ipsclass->acp_lang['portal_pop_desc']}</td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// PORTAL: row
//===========================================================================
function portal_pop_row( $tag, $desc ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>&lt;!--::<strong>{$tag}</strong>::--&gt;</td>
 <td class='tablerow1'><div class='desctext'>{$desc}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// PORTAL: Overview
//===========================================================================
function portal_overview( $content ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>{$this->ipsclass->acp_lang['portal_main_title']}</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='1%'>&nbsp;</td>
  <td class='tablesubheader' width='95%'>{$this->ipsclass->acp_lang['portal_main_key']}</td>
  <td class='tablesubheader' width='5%'>&nbsp;</td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// PORTAL: row
//===========================================================================
function portal_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'><img src='{$this->ipsclass->skin_acp_url}/images/menu.png' border='0' alt='Опции' class='ipd' /></td>
 <td class='tablerow1'><strong>{$data['pc_title']}</strong><div class='desctext'>{$data['pc_desc']}</td>
 <td class='tablerow1'><img id="menu{$data['pc_key']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['pc_key']}",
  new Array(
EOF;
//startif
if ( $data['pc_settings_keyword'] )
{		
$IPBHTML .= <<<EOF
			img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=portal_settings&amp;pc_key={$data['pc_key']}'>{$this->ipsclass->acp_lang['portal_row_menu_settings']}</a>",
EOF;
}//endif
$IPBHTML .= <<<EOF
  			 img_export   + " <a href='#'  onclick=\"menu_action_close(); pop_win('{$this->ipsclass->form_code}&amp;code=portal_viewtags&amp;pc_key={$data['pc_key']}', '{$data['pc_key']}', 400,200)\">{$this->ipsclass->acp_lang['portal_row_menu_view_tags']}</a>"
  			 ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}


}

?>