<?php

class cp_skin_member {

var $ipsclass;

//===========================================================================
// MEMBER FORM
//===========================================================================
function member_form($mem, $form) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript" src='{$this->ipsclass->vars['board_url']}/skin_acp/clientscripts/ipd_form_functions.js'></script>
<script type="text/javascript" src='{$this->ipsclass->vars['board_url']}/skin_acp/clientscripts/ipd_tab_factory.js'></script>
<script type="text/javascript">
//<![CDATA[
// INIT FORM FUNCTIONS stuff
var formfunctions = new form_functions();
// INIT TAB FACTORY stuff
var tabfactory    = new tab_factory();

var show   = '';
{$form['_perm_masks_js']};
function saveit(f)
{
	show = '';
	for (var i = 0 ; i < f.options.length; i++)
	{
		if (f.options[i].selected)
		{
			tid  = f.options[i].value;
			show += '\\n' + eval('perms_'+tid);
		}
	}
	
	if ( show != '' )
	{
		document.forms[0].override.checked = true;
	}
	else
	{
		document.forms[0].override.checked = false;
	}
}

function show_me()
{
	if (show == '')
	{
		show = 'Изменения не обнаружены\\nНажмите на поле мульти-выбора для активации';
	}
	
	alert('Выбранные маски доступа\\n---------------------------------\\n' + show);
}
//]]>
</script>
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=doedit&amp;mid={$mem['id']}' id='mainform' onsubmit='ValidateForm()' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<input type='hidden' name='curemail' value='{$mem['email']}' />
<input type='hidden' name='curgroup' value='{$mem['mgroup']}' />
{$form['_custom_hidden_fields']}
<div class='tabwrap'>
	<div id='tabtab-1' class='taboff'>Основные настройки</div>
	<div id='tabtab-2' class='taboff'>Настройки доступа</div>
	<div id='tabtab-3' class='taboff'>Настройки форума</div>
	<div id='tabtab-4' class='taboff'>Подпись</div>
	<div id='tabtab-5' class='taboff'>Дополнительные поля</div>
</div>
<div class='tabclear'>Изменение: {$mem['members_display_name']} <span style='font-weight:normal'>(ID: {$mem['id']})</span></div>
<div class='tableborder'>
<div id='tabpane-1' class='formmain-background'>
 	<table cellpadding='0' cellspacing='0' border='0' width='100%'>
	 <tr>
	   <td>
		<fieldset class='formmain-fieldset'>
		    <legend><strong>{$mem['members_display_name']}</strong></legend>
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			 <tr>
				<td width='1%' class='tablerow1'>
					<div style='border:1px solid #000;background:#FFF;width:{$mem['pp_main_width']}px; padding:15px'>
						<img src="{$mem['pp_main_photo']}" width='{$mem['pp_main_width']}' height='{$mem['pp_main_height']}' />
					</div>
				</td>
				<td>
					
					     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
						 <tr>
						   <td width='40%' class='tablerow1'><strong>IP-адрес в момент регистрации</strong></td>
						   <td width='60%' class='tablerow2'>
								<a href='{$this->ipsclass->base_url}&amp;section={$this->ipsclass->section_code}&amp;act=mtools&amp;code=learnip&amp;ip={$mem['ip_address']}' title='Узнать больше об этом IP'>{$mem['ip_address']}</a>
								[ <a href='{$this->ipsclass->base_url}&amp;section={$this->ipsclass->section_code}&amp;act=mtools&amp;code=showallips&amp;member_id={$mem['id']}'>Показать все IP-адреса</a> ]
						   </td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>E-mail адрес</strong></td>
							<td width='60%' class='tablerow2'>{$form['email']}</td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>Количество сообщений</strong></td>
							<td width='60%' class='tablerow2'>{$form['posts']}</td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>Удалить фотографию?</strong></td>
							<td width='60%' class='tablerow2'>{$form['remove_photo']}</td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>Удалить аватар?</strong></td>
							<td width='60%' class='tablerow2'>{$form['remove_avatar']}</td>
						 </tr>
						 <tr>
							<td width='40%' class='tablerow1'><strong>Уровень предупреждений</strong></td>
							<td width='60%' class='tablerow2'>
								{$form['warn_level']}
								[ <a href='#' onclick="ipsclass.pop_up_window('{$this->ipsclass->vars['board_url']}/index.php?act=warn&amp;mid={$mem['id']}&amp;CODE=view','500','450'); return false;">Просмотр записей</a> ]
								[ <a href='#' onclick="ipsclass.pop_up_window('{$this->ipsclass->vars['board_url']}/index.php?act=warn&amp;mid={$mem['id']}&amp;CODE=add_note','500','450'); return false;">Добавить запись</a> ]
							</td>
						 </tr>
						 <tr>
							<td width='40%' class='tablerow1'><strong>Личное звание</strong></td>
							<td width='60%' class='tablerow2'>{$form['member_title']}</td>
						 </tr>
						 </table>
					   
				</td>
			 </tr>
			</table>
			
			
		</fieldset>
		
		<br />
		
		<fieldset class='formmain-fieldset'>
		    <legend><strong>Обзор групп пользователя</strong></legend>
EOF;
if ( $form['_show_fixed'] != TRUE )
{
$IPBHTML .= <<<EOF
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			 <tr>
			   <td width='40%' class='tablerow1'><strong>Основная группа</strong><div style='color:gray'>Пользователь принадлежит указанной группе.</div></td>
			   <td width='60%' class='tablerow2'>{$form['mgroup']}</td>
			  </tr>
			  <tr>
				<td width='40%' class='tablerow1'><strong>Второстепенные группы</strong><br />Вы можете выбрать несколько групп.<div style='color:gray'>Пользователь получит «лучшие» возможности всех выбранных второстепенных групп.</div></td>
				<td width='60%' class='tablerow2'>{$form['mgroup_others']}</td>
			 </tr>
			 </table>
EOF;
}
else
{
$IPBHTML .= <<<EOF
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			 <tr>
			   <td width='40%' class='tablerow1'><strong>Primary Member Group</strong></td>
			   <td width='60%' class='tablerow2'>{$form['_mgroup']}<b>Root Admin or Administrator</b> (Can't Change)</td>
			  </tr>
			 </table>
EOF;
}


$IPBHTML .= <<<EOF
		</fieldset>
		</td>
	</tr>
	</table>
</div>
<div id='tabpane-2' class='formmain-background'>
	<table cellpadding='0' cellspacing='0' border='0' width='100%'>
	 <tr>
	   <td>
		<fieldset class='formmain-fieldset'>
		    <legend><strong>Настройки доступа и публикации сообщений</strong></legend>
		     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
			 <tr>
			   <td width='40%' class='tablerow1'><strong>Изменить маски доступа группы на</strong><br />Вы можете выбрать несколько масок доступа.<div style='color:gray'>Выбранные маски доступа заменят маски выставленные от выбранных групп (от основной и второстепенных).</div></td>
			   <td width='60%' class='tablerow2'>
					<input type='checkbox' name='override' {$form['_permid_tick']} value='1' > <b>Изменить маски доступа группы на</b><br />
					{$form['permid']}
					<br><input style='margin-top:5px' id='editbutton' type='button' onclick='show_me();' value='Показать выбранные маски доступа'>
			   </td>
			  </tr>
			  <tr>
				<td width='40%' class='tablerow1'><strong>Требуется проверка модератором всех сообщений пользователя?</strong><div style='color:gray'>Если «Да», все сообщения пользователя будут проверяться модераторами. Оставьте поле пустым и снимите галочку для отключения ограничения.</div></td>
				<td width='60%' class='tablerow2'>
					<input type='checkbox' name='mod_indef' value='1' {$form['_mod_tick']}> всегда проверять
					<br />
					<strong>Или на</strong>
					{$form['mod_timespan']} {$form['mod_units']} {$form['_mod_extra']}
				</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>Запретить пользователю {$mem['members_display_name']} публиковать сообщения?</strong><div style='color:gray'>Оставьте поле пустым и снимите галочку для отключения ограничения.</div></td>
				<td width='60%' class='tablerow2'>
					<input type='checkbox' name='post_indef' value='1' {$form['_post_tick']}> навсегда запретить
					<br />
					<strong>Или на</strong>
					{$form['post_timespan']} {$form['post_units']} {$form['_post_extra']}
				</td>
			 </tr>
			 </table>
		 </fieldset>
		</td>
	</tr>
	</table>
</div>
<div id='tabpane-3' class='formmain-background'>
	<table cellpadding='0' cellspacing='0' border='0' width='100%'>
	 <tr>
	   <td>
		<fieldset class='formmain-fieldset'>
		    <legend><strong>Настройки форума</strong></legend>
		     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
			  <tr>
				<td width='40%' class='tablerow1'><strong>Язык интерфейса</strong></td>
				<td width='60%' class='tablerow2'>{$form['language']}</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>Стиль</strong></td>
				<td width='60%' class='tablerow2'><select name='skin' class='dropdown'><option value='0'>--Нет / По умолчанию--</option>{$form['_skin_list']}</select></td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>Скрыть e-mail адрес от остальных пользователей?</strong></td>
				<td width='60%' class='tablerow2'>{$form['hide_email']}</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>Отправлять письмо на email при получении личного сообщения?</strong></td>
				<td width='60%' class='tablerow2'>{$form['email_pm']}</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>{$this->ipsclass->acp_lang['mem_edit_pm_title']}</strong></td>
				<td width='60%' class='tablerow2'>{$form['members_disable_pm']}</td>
			 </tr>
			 </table>
		   </fieldset>
		
			<br />
			
			<fieldset class='formmain-fieldset'>
			    <legend><strong>Контактная информация</strong></legend>
			     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
					<td width='40%' class='tablerow1'><strong>Имя в AIM</strong></td>
					<td width='60%' class='tablerow2'>{$form['aim_name']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Имя в MSN</strong></td>
					<td width='60%' class='tablerow2'>{$form['msnname']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Имя в Yahoo!</strong></td>
					<td width='60%' class='tablerow2'>{$form['yahoo']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Номер ICQ</strong></td>
					<td width='60%' class='tablerow2'>{$form['icq_number']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Адрес домашней страницы</strong></td>
					<td width='60%' class='tablerow2'>{$form['website']}</td>
				 </tr>
				 </table>
		   </fieldset>
		
			<br />
			
			<fieldset class='formmain-fieldset'>
			    <legend><strong>Другая информация</strong></legend>
			     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
					<td width='40%' class='tablerow1'><strong>Аватар</strong></td>
					<td width='60%' class='tablerow2'>{$form['avatar']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Типы аватара</strong></td>
					<td width='60%' class='tablerow2'>{$form['avatar_type']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Размер аватара</strong></td>
					<td width='60%' class='tablerow2'>{$form['avatar_size']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Место жительства</strong></td>
					<td width='60%' class='tablerow2'>{$form['location']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Интересы</strong></td>
					<td width='60%' class='tablerow2'>{$form['interests']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>Пол</strong></td>
					<td width='60%' class='tablerow2'>{$form['pp_gender']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>О себе</strong></td>
					<td width='60%' class='tablerow2'>{$form['pp_bio_content']}</td>
				 </tr>
				 </table>
		   </fieldset>
		
	   </td>
     </tr>
    </td>
	</table>
</div>
<div id='tabpane-4' class='formmain-background'>
	<fieldset class='formmain-fieldset'>
	    <legend><strong>Подпись пользователя</strong></legend>
		{$form['signature']}
	</fieldset>
</div>
<div id='tabpane-5' class='formmain-background'>
	<fieldset class='formmain-fieldset'>
	    <legend><strong>Дополнительные поля профиля</strong></legend>
		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			{$form['_custom_fields']}
		</table>
	</fieldset>
</div>
<div align='center' class='tablefooter'>
 	<div class='formbutton-wrap'>
 		<div id='button-save'><img src='{$this->ipsclass->skin_acp_url}/images/icons_form/save.gif' border='0' alt='Сохранить'  title='Сохранить' class='ipd-alt' /> Сохранить</div>
	</div>
</div>
</div>
</form>
<script type="text/javascript">
//<![CDATA[
// Init form functions, grab stuff
formfunctions.init();
// Pass ID name of FORM tag
formfunctions.name_form = 'mainform';
formfunctions.add_submit_event( 'button-save' );
// Stuff. Well done Matt
tabfactory.init_tabs();
//]]>
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Member: delete stuff start
//===========================================================================
function member_delete_posts_start( $member, $topics, $posts ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=deleteposts_process&amp;mid={$member['id']}' method='POST'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>{$this->ipsclass->acp_lang['mem_delete_posts_title']} {$member['members_display_name']}</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablerow1' width='90%'><strong>{$this->ipsclass->acp_lang['mem_delete_delete_posts']}</strong><div class='desctext'>{$this->ipsclass->acp_lang['mem_delete_delete_posts_desc']}</div></td>
  <td class='tablerow2' width='10%'><input type='checkbox' value='1' name='dposts' /></td>
 </tr>
 <tr>
  <td class='tablerow1' width='90%'><strong>{$this->ipsclass->acp_lang['mem_delete_delete_topics']}</strong><div class='desctext'>{$this->ipsclass->acp_lang['mem_delete_delete_topics_desc']}</div></td>
  <td class='tablerow2' width='10%'><input type='checkbox' value='1' name='dtopics' /></td>
 </tr>
 <tr>
  <td class='tablerow1' width='90%'><strong>{$this->ipsclass->acp_lang['mem_delete_posts_trash']}</strong><div class='desctext'>{$this->ipsclass->acp_lang['mem_delete_posts_trash_desc']}</div></td>
  <td class='tablerow2' width='10%'><input type='checkbox' value='1' name='use_trash_can' /></td>
 </tr> 
 <tr>
  <td class='tablerow1' width='90%'><strong>{$this->ipsclass->acp_lang['mem_delete_delete_pergo']}</strong><div class='desctext'>{$this->ipsclass->acp_lang['mem_delete_delete_pergo_desc']}</div></td>
  <td class='tablerow2' width='10%'><input type='input' value='50' size='3' name='dpergo' /></td>
 </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='{$this->ipsclass->acp_lang['mem_delete_process']}' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Member: validating
//===========================================================================
function member_validating_wrapper($content, $st, $new_ord, $links) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript">
//<![CDATA[
 function check_boxes()
 {
 	var ticked = document.getElementById('maincheckbox').checked;
 	
 	var checkboxes = document.getElementsByTagName('input');

	for ( var i = 0 ; i <= checkboxes.length ; i++ )
	{
		var e = checkboxes[i];
		
		if ( e.type == 'checkbox')
		{
			var boxname  = e.id;
			var boxcheck = boxname.replace( /^(.+?)_.+?$/, "$1" );
			
			if ( boxcheck == 'mid' )
			{
				e.checked = ticked;
			}
		}
	}
 }

//]]>
</script>
<div class='tableborder'>
 <div class='tableheaderalt'>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Неактивированные учетные записи</td>
  <td align='right' width='60%'>
   <form name='selectform' id='selectform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=mod' method='post'>
   <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
   <select name='filter' class='dropdown'>
    <option value='all'>Показать всех</option>
    <option value='reg_user_validate'>Зарегистрированные (подтверждение пользователем)</option>
    <option value='reg_admin_validate'>Зарегистрированные (подтверждение администратором)</option>
    <option value='email_chg'>Изменение e-mail адреса</option>
   </select>
   <input type='submit' class='realbutton' value=' Показать &gt;' />
   </form>
  </td>
 </tr>
 </table>
 </div>
 <form name='theAdminForm' id='adminform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=domod' method='post'>
 <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
 <table cellpadding='4' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='20%'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=mod&amp;st=$st&amp;sort=mem&amp;ord=$new_ord'>Отображаемое имя</a></td>
  <td class='tablesubheader' width='15%'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=email&ord=$new_ord'>E-mail</a></td>
  <td class='tablesubheader' width='20%'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=sent&ord=$new_ord'>Email отправл.</a></td>
  <td class='tablesubheader' width='5%' align='center'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=posts&ord=$new_ord'>Сообщений</a></td>
  <td class='tablesubheader' width='15%'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=reg&ord=$new_ord'>Зарегистрирован</a></td>
  <td class='tablesubheader' width='1%'><input type='checkbox' id='maincheckbox' onclick='check_boxes()' /></td>
 </tr>
 {$content}
 <tr>
  <td class='tablesubheader' colspan='2' align='left'>{$links}</td>
  <td class='tablesubheader' colspan='4' align='right'>
   <select name='type' class='dropdown'><option value='approve'>Активировать</option><option value='delete'>Удалить</option><option value='resend'>Отправить повторно письмо на e-mail</option></select>
   <input type='submit' class='realbutton' value=' Выполнить &gt;&gt;' />
  </td>
 </tr>
 </table>
 </form>
</div>

EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Member: validating
//===========================================================================
function member_validating_row( $r="" ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
  <td class='tablerow2'><a href='{$this->ipsclass->vars['board_url']}/index.php?showuser={$r['id']}'><strong>{$r['members_display_name']}</strong></a>{$r['_coppa']}<div class='desctext'>IP: <a href='{$this->ipsclass->base_url}&section=content&act=mtools&code=learnip&ip={$r['ip_address']}'>{$r['ip_address']}</a></div></td>
  <td class='tablerow1'>{$r['email']}</td>
  <td class='tablerow1'><span style='color:green'>{$r['_where']}</span><br />{$r['_entry']}<div class='desctext'>{$r['_days']} дней и {$r['_rhours']} часов назад</div></td>
  <td class='tablerow1' align='center'>{$r['posts']}</td>
  <td class='tablerow1'>{$r['_joined']}</td>																
  <td class='tablerow1' align='center'><input type='checkbox' id="mid_{$r['member_id']}" name='mid_{$r['member_id']}' value='1' /></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Member: locked account
//===========================================================================
function member_locked_wrapper($content, $st, $links) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript">
//<![CDATA[
 function check_boxes()
 {
 	var ticked = document.getElementById('maincheckbox').checked;
 	
 	var checkboxes = document.getElementsByTagName('input');

	for (var i in checkboxes )
	{
		var e = checkboxes[i];
		
		if ( e.type == 'checkbox')
		{
			var boxname  = e.id;
			var boxcheck = boxname.replace( /^(.+?)_.+?$/, "$1" );
			
			if ( boxcheck == 'mid' )
			{
				e.checked = ticked;
			}
		}
	}
 }

//]]>
</script>
<div class='tableborder'>
 <div class='tableheaderalt'>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td align='left' width='100%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Заблокированные учетные записи</td>
 </tr>
 </table>
 </div>
 <form name='theAdminForm' id='adminform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=unlock' method='post'>
 <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
 <table cellpadding='4' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='20%'>Отображаемое имя</td>
  <td class='tablesubheader' width='15%'>E-mail адрес</td>
  <td class='tablesubheader' width='20%'>Неудачные попытки</td>
  <td class='tablesubheader' width='5%' align='center'>Сообщений</td>
  <td class='tablesubheader' width='15%'>Регистрация</td>
  <td class='tablesubheader' width='1%'><input type='checkbox' id='maincheckbox' onclick='check_boxes()' /></td>
 </tr>
 {$content}
 <tr>
  <td class='tablesubheader' colspan='2' align='left'>{$links}</td>
  <td class='tablesubheader' colspan='4' align='right'>
   <select name='type' class='dropdown'><option value='unlock'>Разблокировать</option><option value='ban'>Заблокировать</option></select>
   <input type='submit' class='realbutton' value=' Выполнить &gt;&gt;' />
  </td>
 </tr>
 </table>
 </form>
</div>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Member: No rows/ setting disabled
//===========================================================================
function member_locked_no_rows( $lang ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
  <td class='tablerow2' colspan='6'><strong>{$lang}</strong></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Member: Locked account row
//===========================================================================
function member_locked_row( $r="" ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
  <td class='tablerow2'><strong>{$r['members_display_name']}</strong><div class='desctext'>Группа: {$r['group_title']}</div><div class='desctext'>{$r['ip_addresses']}</div></td>
  <td class='tablerow1'>{$r['email']}</td>
  <td class='tablerow1'>Старые: {$r['oldest_fail']}<br />Новые: {$r['newest_fail']}<br />Всего: {$r['failed_login_count']}</td>
  <td class='tablerow1' align='center'>{$r['posts']}</td>
  <td class='tablerow1'>{$r['_joined']}</td>																
  <td class='tablerow1' align='center'><input type='checkbox' id="mid_{$r['id']}" name='mid_{$r['id']}' value='1' /></td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


}

?>