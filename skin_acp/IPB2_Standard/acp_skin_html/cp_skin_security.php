<?php

class cp_skin_security {

var $ipsclass;

//===========================================================================
// DIAGNOSTICS
//===========================================================================
function list_admin_overview($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Пользователи, имеющие доступ в АЦ</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
	<td class='tablesubheader' width='30%'>Пользователь</td>
	<td class='tablesubheader' width='30%'>Основная группа</td>
	<td class='tablesubheader' width='30%'>Второстепенная группа</td>
	<td class='tablesubheader' width='30%'>IP-адрес</td>
	<td class='tablesubheader' width='30%'>E-mail адрес</td>
	<td class='tablesubheader' width='30%'>Сообщений</td>
	<td class='tablesubheader' width='30%'>&nbsp;</td>
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
// DIAGNOSTICS
//===========================================================================
function list_admin_row( $member ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
	<strong>{$member['members_display_name']}</strong>
	<div class='desctext'>{$member['_joined']}</div>
 </td>
 <td class='tablerow1'>
	{$member['_mgroup']}
 </td>
 <td class='tablerow2'>
	{$member['_mgroup_others']}&nbsp;
 </td>
 <td class='tablerow1'>
	<div class='desctext'>{$member['ip_address']}</div>
 </td>
 <td class='tablerow2'>
	{$member['email']}
 </td>
 <td class='tablerow2'>
	{$member['posts']}
 </td>
 <td class='tablerow1'>
	<a href='{$this->ipsclass->base_url}&amp;section=content&amp;act=mem&amp;code=doform&amp;mid={$member['id']}'>Изменить</a>
 </td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}



//===========================================================================
// DIAGNOSTICS
//===========================================================================
function deep_scan_bad_files_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=deep_scan' method='POST'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div style='padding-bottom:10px'>Показать:
	<select name='filter'>
	    <option value='all'>Показать всё</option>
		<option value='score-5'>Рейтинг 5 и более</option>
		<option value='score-6'>Рейтинг 6 и более</option>
		<option value='score-7'>Рейтинг 7 и более</option>
		<option value='score-8'>Рейтинг 8 и более</option>
		<option value='score-9'>Рейтинг 9 и более</option>
		<option value='large'>Размер файла 55k и более</option>
		<option value='recent'>Измененные за последние 30 дней</option>
	</select>
	<input type='submit' value=' Фильтр ' />
</div>
</form>
<div class='tableborder'>
 <div class='tableheaderalt'>Проверенные файлы</div>
 <div class='tablesubheader' style='padding-right:0px'>
  <div align='right' style='padding-right:10px'>
     (Размер файла) &nbsp; Последние изменение
    </div>
  </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
	$content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// DIAGNOSTICS
//===========================================================================
function deep_scan_bad_files_row( $file_path, $file_name, $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
	<div style='float:right'>
		<div class='desctext'>({$data['human']}k) &nbsp; {$data['mtime']}</div>
	</div>
	<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/bad_file.png' border='0' alt='-' class='ipd' />
	<span style='border:1px solid #555;background-color:#FFFFFF'>
		<span style='width:{$data['left_width']}px;background-color:{$data['color']}'>
			<img src='{$this->ipsclass->skin_acp_url}/images/blank.gif' height='20' width='{$data['left_width']}' alt='' />
		</span>
		<img src='{$this->ipsclass->skin_acp_url}/images/blank.gif' height='20' width='{$data['right_width']}' alt='' />
	</span>
	&nbsp; <span class='desctext'>[ {$data['score']} ]</span> <a target='_blank' href='{$this->ipsclass->vars['board_url']}/{$file_path}'>$file_name</a>
 </td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// DIAGNOSTICS
//===========================================================================
function anti_virus_checked_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<br />
<div class='tableborder'>
 <div class='tableheaderalt'>Список проверенных директорий</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
	$content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// DIAGNOSTICS
//===========================================================================
function anti_virus_checked_row( $file_path ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
	<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/checked_folder.png' border='0' alt='-' class='ipd' />
	$file_path
 </td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}



//===========================================================================
// DIAGNOSTICS
//===========================================================================
function anti_virus_bad_files_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Подозрительные файлы</div>
 <div class='tablesubheader' style='padding-right:0px'>
  <div align='right' style='padding-right:10px'>
     (Размер файла) &nbsp; Последние изменение
    </div>
  </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
	$content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// DIAGNOSTICS
//===========================================================================
function anti_virus_bad_files_row( $file_path, $file_name, $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
	<div style='float:right'>
		<div class='desctext'>({$data['human']}k) &nbsp; {$data['mtime']}</div>
	</div>
	<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/bad_file.png' border='0' alt='-' class='ipd' />
	<span style='border:1px solid #555;background-color:#FFFFFF'>
		<span style='width:{$data['left_width']}px;background-color:{$data['color']}'>
			<img src='{$this->ipsclass->skin_acp_url}/images/blank.gif' height='20' width='{$data['left_width']}' alt='' />
		</span>
		<img src='{$this->ipsclass->skin_acp_url}/images/blank.gif' height='20' width='{$data['right_width']}' alt='' />
	</span>
	&nbsp; <span class='desctext'>[ {$data['score']} ]</span> <a target='_blank' href='{$this->ipsclass->vars['board_url']}/{$file_path}'>$file_name</a>
 </td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// OVERVIEW
//===========================================================================
function security_overview( $content ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript" src='{$this->ipsclass->vars['board_url']}/skin_acp/clientscripts/ipd_form_functions.js'></script>
<script type="text/javascript">
 //<![CDATA[
 // INIT FORM FUNCTIONS stuff
 var formfunctions = new form_functions();
 //]]>
</script>
<form id='mainform'>
<div class='information-box'>
	<table cellpadding='0' cellspacing='0'>
	<tr>
		<td width='1%' valign='top'>
 			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/id_card_ok.png' alt='information' />
		</td>
		<td width='100%' valig='top' style='padding-left:10px'>
 			<h2 style='margin:0px'>Центр безопасности Invision Power Board</h2>
			 <p style='margin:0px'>
			 	<br />
			 	Центр безопасности — это центральное хранилище инструментов и настроек.
				<br />
				При помощи настроек и инструментов ниже вы сможете проверить и повысить безопасность вашего IPB.
				<br />
				<br />
				{$content['bad']}
				{$content['ok']}
				{$content['good']}
			 </p>
		</td>
	</tr>
	</table>
</div>
</form>
<script type="text/javascript">
//<![CDATA[
// Init form functions, grab stuff
formfunctions.init();
//]]>
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// BAD
//===========================================================================
function security_item_bad( $title, $desc, $button, $url, $key ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='bad-box' style='margin-bottom:10px'>
<table cellpadding='0' cellspacing='0'>
<tr>
	<td width='1%' valign='middle'>
			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/lock_error.png' alt='information' />
	</td>
	<td width='71%' valig='top' style='margin-left:10px'>
		 <div style='font-size:14px;font-weight:bold;border-bottom:1px solid #000;padding-bottom:5px;margin-bottom:5px;margin-right:5px'>{$title}</div>
		 <div>$desc</div>
	</td>
	<td width='18%' valign='middle'>
		<div class='formbutton-wrap'>
		 	<div id='button-link-{$key}'><img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/run_tool.png' border='0' alt='Запустить'  title='Запустить' class='ipd' /> $button</div>
		 </div>
	</td>
</tr>
</table>
</div>
<script type="text/javascript">
//<![CDATA[
formfunctions.add_link_event( 'button-link-{$key}', '{$this->ipsclass->base_url}&$url' );
//]]>
</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// GOOD
//===========================================================================
function security_item_good( $title, $desc, $button, $url, $key ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='good-box' style='margin-bottom:10px'>
<table cellpadding='0' cellspacing='0'>
<tr>
	<td width='1%' valign='middle'>
			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/lock_ok.png' alt='information' />
	</td>
	<td width='71%' valig='top' style='margin-left:10px'>
		 <div style='font-size:14px;font-weight:bold;border-bottom:1px solid #000;padding-bottom:5px;margin-bottom:5px;margin-right:5px'>{$title}</div>
		 <div>$desc</div>
	</td>
	<td width='18%' valign='middle'>
		<div class='formbutton-wrap'>
		 	<div id='button-link-{$key}'><img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/run_tool.png' border='0' alt='Запустить'  title='Запустить' class='ipd' /> $button</div>
		 </div>
	</td>
</tr>
</table>
</div>
<script type="text/javascript">
//<![CDATA[
formfunctions.add_link_event( 'button-link-{$key}', '{$this->ipsclass->base_url}&$url' );
//]]>
</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// OK
//===========================================================================
function security_item_ok( $title, $desc, $button, $url, $key ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='ok-box' style='margin-bottom:10px'>
<table cellpadding='0' cellspacing='0'>
<tr>
	<td width='1%' valign='middle'>
			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/lock_information.png' alt='information' />
	</td>
	<td width='71%' valig='top' style='margin-left:10px'>
		 <div style='font-size:14px;font-weight:bold;border-bottom:1px solid #000;padding-bottom:5px;margin-bottom:5px;margin-right:5px'>{$title}</div>
		 <div>$desc</div>
	</td>
	<td width='18%' valign='middle'>
		<div class='formbutton-wrap'>
		 	<div id='button-link-{$key}'><img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/run_tool.png' border='0' alt='Запустить'  title='Запустить' class='ipd' /> $button</div>
		 </div>
	</td>
</tr>
</table>
</div>
<script type="text/javascript">
//<![CDATA[
formfunctions.add_link_event( 'button-link-{$key}', '{$this->ipsclass->base_url}&$url' );
//]]>
</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// HTaccess form
//===========================================================================
function htaccess_form() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form id='mainform' method='post' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=acphtaccess_do'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='information-box'>
	<table cellpadding='0' cellspacing='0'>
	<tr>
		<td width='1%' valign='top'>
 			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/id_card_ok.png' alt='information' />
		</td>
		<td width='100%' valig='top' style='padding-left:10px'>
 			<h2 style='margin:0px'>Защита АЦ с помощью .htaccess</h2>
			 <p style='margin:0px'>
			 	<br />
			 	Invision Power Board может создать файл «.htaccess», который защитит директорию админцентра.
				<br />
				<br />
				<strong>Помните</strong>
				<br />
				Используя этот инструмент, вы перезапишите любой имеющийся файл защиты в директории админцентра. После сохранения данных, вам будет предложено ввести указанные имя пользователя и пароль. Хотим заметить, что если вы переименуете директорию админцентра, вам так же будет необходимо удалить этот файл при помощи FTP-программы — в противном случае доступ в админцентр для вас будет заблокирован.
				<br />
				<br />
				<fieldset>
					<legend><strong>Имя пользователя</strong></legend>
					<input type='text' name='name' size='40' value='{$_POST['name']}' />
				</fieldset>
				<br />
				<fieldset>
					<legend><strong>Пароль</strong></legend>
					<input type='password' name='pass' size='40' value='{$_POST['pass']}' />
				</fieldset>
				<br />
				<input type='submit' value=' Создать файл ' />
			 </p>
		</td>
	</tr>
	</table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// HTaccess data
//===========================================================================
function htaccess_data( $htaccess_pw, $htaccess_auth ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form id='mainform'>
<div class='information-box'>
	<table cellpadding='0' cellspacing='0'>
	<tr>
		<td width='1%' valign='top'>
 			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/id_card_ok.png' alt='information' />
		</td>
		<td width='100%' valig='top' style='padding-left:10px'>
 			<h2 style='margin:0px'>Защита АЦ с помощью .htaccess</h2>
			 <p style='margin:0px'>
			 	<br />
			 	<strong>Invision Power Board не может произвести запись в директорию админцентра.</strong>
				<br />
				<br />
				Пожалуйста, создайте файл с названием «.htpasswd» и скопируйте в него содержимое, указанное ниже. После этого, загрузите созданный файл в директорию админцентра.
				<br />
				<textarea rows='5' cols='70' style='width:98%;height:100px'>$htaccess_pw</textarea>
				<br />
				<br />
				Пожалуйста, создайте файл с названием «.htaccess» и скопируйте в него содержимое, указанное ниже. После этого, загрузите созданный файл в директорию админцентра.
				<br />
				<textarea rows='5' cols='70' style='width:98%;height:100px'>$htaccess_auth</textarea>
			 </p>
		</td>
	</tr>
	</table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Rename the admin directory
//===========================================================================
function rename_admin_dir() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form id='mainform'>
<div class='information-box'>
	<table cellpadding='0' cellspacing='0'>
	<tr>
		<td width='1%' valign='top'>
 			<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/security/id_card_ok.png' alt='information' />
		</td>
		<td width='100%' valig='top' style='padding-left:10px'>
 			<h2 style='margin:0px'>Изменение директории АЦ</h2>
			 <p style='margin:0px'>
			 	<br />
			 	Invision Power Board обнаружил директорию админцентра. В целях повышения безопасности, вы можете изменить эту директорию.
				<br />
				<br />
				<strong>Шаг 1:</strong>
				<br />
				Во-первых, вы должны переименовать директорию. Подключитесь к серверу при помощи FTP и найдите директорию с IPB (директория, в котором
				находится файл «index.php»).
				<br/ >
				Найдите директорию «admin». Он содержит один файл — «index.php». Выберите команду «Переименовать» в вашей FTP-программе и укажите новое название. В этой инструкции
				мы будем использовать название «admin_secret».  <b>Если у вас в указанной директории создан файл защиты «.htaccess», вы ДОЛЖНЫ его удалить.  В противном случае вы не сможете авторизироваться в админцентр.  Вы сможете создать этот файл заного после выполнения следующего шага.</b>
				<br />
				<br />
				<strong>Шаг 2:</strong>
				Найдите файл «init.php». Он находится в корневой директории IPB. Скачайте его и откройте в текстовом редакторе.
				<br />В самом верху файла вы увидите следующие строки:
				<br />
				<pre>//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------
/**
* ROOT PATH
*
* If __FILE__ is not supported, try ./ and
* turn off "USE_SHUTDOWN" or manually add in
* the full path
* @since 2.0.0.2005-01-01
*/
define( 'ROOT_PATH', dirname( __FILE__ ) ."/" );

<strong>/**
* Directory name for the admin folder
* @since 2.2.0.2006-06-30
*/
define( 'IPB_ACP_DIRECTORY', 'admin' );</strong></pre>
				
				<br />
				Измените строку: <pre>define( 'IPB_ACP_DIRECTORY', '<strong>admin</strong>' );</pre> на <pre>define( 'IPB_ACP_DIRECTORY', '<strong>admin_secret</strong>' );</pre>
				<br />
				<br />
				<strong>Теперь директория вашего админцентра изменена.</strong>
				<br />Не забудте удалить ссылку на админцентр при помощи «Центра безопасности», чтобы увеличить безопасность вашего форума.
			 </p>
		</td>
	</tr>
	</table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

}


?>