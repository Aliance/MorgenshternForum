<?php

class cp_skin_management {

var $ipsclass;


//===========================================================================
// Menu manage:Blank Pos
//===========================================================================
function calendar_position_blank($cal_id) {

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
function calendar_position_up($cal_id) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_move&amp;move=up&amp;cal_id={$cal_id}' title='Поднять'><img src='{$this->ipsclass->skin_acp_url}/images/arrow_up.png' width='12' height='12' border='0' style='vertical-align:middle' /></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Menu manage:Blank Down
//===========================================================================
function calendar_position_down($cal_id) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_move&amp;move=down&amp;cal_id={$cal_id}' title='Опустить'><img src='{$this->ipsclass->skin_acp_url}/images/arrow_down.png' width='12' height='12' border='0' style='vertical-align:middle' /></a>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Component FORM
//===========================================================================
function calendar_form($form, $title, $formcode, $button, $calendar) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type='text/javascript'>
//<![CDATA[

function perm_check( obj )
{
	var real_obj      = document.getElementById( obj );
	var total_options = real_obj.options.length;
	var total_checked = 0;
	
	for( var i = 0 ; i < real_obj.options.length ; i++ )
	{
		if ( real_obj.options[i].selected )
		{
			total_checked++;
		}
	}
	
	if ( total_checked == total_options )
	{
		document.getElementById( obj+'_all' ).checked = true;
	}
	else
	{
		document.getElementById( obj+'_all' ).checked = false;
	}
}

function perm_check_all( obj )
{
	var real_obj   = document.getElementById( obj );
	var isselected = document.getElementById( obj+'_all').checked ? true : false;
	
	for( var i = 0 ; i < real_obj.options.length ; i++ )
	{
		real_obj.options[i].selected = isselected;
	}
	
	document.getElementById( obj+'_all').checked = isselected;
}


//]]>
</script>
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=$formcode&amp;cal_id={$calendar['cal_id']}' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>$title</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td width='40%' class='tablerow1'><strong>Название календаря</strong></td>
   <td width='60%' class='tablerow2'>{$form['cal_title']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Включить модерацию</strong><div class='desctext'>Если включено, события от всех групп пользователей будут попадать на проверку модераторами перед публикацией.</div></td>
   <td width='60%' class='tablerow2'>{$form['cal_moderate']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Лимит событий</strong><div class='desctext'>Число показываемых событий за день, если событий будет больше, то будет отображаться ссылка на все события дня</div></td>
   <td width='60%' class='tablerow2'>{$form['cal_event_limit']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Лимит именинников</strong><div class='desctext'>Число показываемых именинников за день, если их будет больше, то будет отображаться ссылка на всех именинников<br />Введите 0, чтобы не показывать именинников вообще</div></td>
   <td width='60%' class='tablerow2'>{$form['cal_bday_limit']}</td>
 </tr>
 <tr>
   <td colspan='2' class='tablerow1'>
    <fieldset>
     <legend><strong>RSS Опции</strong></legend>
     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
	 <tr>
	   <td width='40%' class='tablerow1'><strong>Включить</strong><div class='desctext'>Если включено, то все <em>n</em> будущих событий (GMT время), которые имеют маску доступа для чтения гостями будут экспортированы в RSS</div></td>
	   <td width='60%' class='tablerow2'>{$form['cal_rss_export']}</td>
	 </tr>
	 <tr>
	   <td width='40%' class='tablerow1'><strong>Будущие события</strong><div class='desctext'>За сколько дней до события выводить его?</div></td>
	   <td width='60%' class='tablerow2'>{$form['cal_rss_export_days']}</td>
	 </tr>
	 <tr>
	   <td width='40%' class='tablerow1'><strong>Количество событий</strong><div class='desctext'>Максимальное количество экспортируемых событий</div></td>
	   <td width='60%' class='tablerow2'>{$form['cal_rss_export_max']}</td>
	 </tr>
   	 <tr>
	   <td width='40%' class='tablerow1'><strong>Частота обновления</strong><div class='desctext'>Количество минут между обновлением кеша RSS</div></td>
	   <td width='60%' class='tablerow2'>{$form['cal_rss_update']}</td>
	 </tr>
    </table>
   </fieldset>
   </td>
 </tr>
 <tr>
   <td colspan='2' class='tablerow1'>
    <fieldset>
     <legend><strong>Доступ к календарям</strong></legend>
     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
	 <tr>
	   <td width='40%' class='tablerow1' valign='top'><strong>Чтение событий</strong><div class='desctext'>Выберите группы, которым будет разрешено читать этот календарь.<br />Не забудьте выбрать маску гостей, если вы хотите экспортировать календарь в RSS.</div></td>
	   <td width='60%' class='tablerow2'>
	   	<input type='checkbox' name='perm_read_all' id='perm_read_all' value='1' onclick='perm_check_all("perm_read")' {$form['perm_read_all']} /> Выбратьв се текущие и будущие маски
	   	<br /><select onchange='perm_check("perm_read")' id='perm_read' name='perm_read[]' size='6' multiple='true'>{$form['perm_read']}</select>
	   </td>
	 </tr>
	 <tr>
	   <td width='40%' class='tablerow1' valign='top'><strong>Создание событий</strong><div class='desctext'>Выберите группы, которым будет разрешено создавать события в этот календарь</div></td>
	   <td width='60%' class='tablerow2'>
	   	<input type='checkbox' name='perm_post_all' id='perm_post_all' value='1' onclick='perm_check_all("perm_post")' {$form['perm_post_all']} /> Выбрать все текущие и будущие маски
	   	<br /><select onchange='perm_check("perm_post")' id='perm_post' name='perm_post[]' size='6' multiple='true'>{$form['perm_post']}</select>
	   </td>
	 </tr>
	 <tr>
	   <td width='40%' class='tablerow1' valign='top'><strong>Публикация без проверки</strong><div class='desctext'>Выберите группы, создаваемые события которых будут публиковаться в этот календарь без проверки модератором</div></td>
	   <td width='60%' class='tablerow2'>
	   <input type='checkbox' name='perm_nomod_all' id='perm_nomod_all' value='1' onclick='perm_check_all("perm_nomod")' {$form['perm_nomod_all']} /> Выбрать все текущие и будущие маски
	   <br /><select onchange='perm_check("perm_nomod")' id='perm_nomod' name='perm_nomod[]' size='6' multiple='true'>{$form['perm_nomod']}</select>
	   </td>
	 </tr>
    </table>
   </fieldset>
   </td>
 </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='$button' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// COMPONENTS: Overview
//===========================================================================
function calendar_overview( $content ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Календари</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='90%'>Название</td>
  <td class='tablesubheader' width='5%' align='center'>Позиция</td>
  <td class='tablesubheader' width='5%'><img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
 <script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_add   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_add'>Добавить новый календарь</a>",
  			 img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_rebuildcache'>Обновление кеша календарных событий</a>" ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// COMPONENTS: row
//===========================================================================
function calendar_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'><strong>{$data['cal_title']}</strong></td>
 <td class='tablerow2' align='center' nowrap='nowrap'>{$data['_pos_up']} &nbsp; {$data['_pos_down']}</td>
 <td class='tablerow1'><img id="menu{$data['cal_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['cal_id']}",
  new Array(
           img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_edit&amp;cal_id={$data['cal_id']}'>Изменить</a>",
           img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_rss_cache&amp;cal_id={$data['cal_id']}'>Обновление RSS кеша</a>",
           img_delete   + " <a href='#' onclick='confirm_action(\"{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=calendar_delete&amp;cal_id={$data['cal_id']}\"); return false;'>Удалить</a>"
  		) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// calendar_rss_recurring
//===========================================================================
function calendar_rss_recurring( $event ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<p>{$event['event_content']}</p>
<br />
<p>Повторяющееся событие
<br />От: {$event['_from_month']}/{$event['_from_day']}/{$event['_from_year']}
<br />До: {$event['_to_month']}/{$event['_to_day']}/{$event['_to_year']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// calendar_rss_recurring
//===========================================================================
function calendar_rss_range( $event ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<p>{$event['event_content']}</p>
<br />
<p>Многодневное событие
<br />От: {$event['_from_month']}/{$event['_from_day']}/{$event['_from_year']}
<br />До: {$event['_to_month']}/{$event['_to_day']}/{$event['_to_year']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// calendar_rss_recurring
//===========================================================================
function calendar_rss_single( $event ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<p>{$event['event_content']}</p>
<br />
<p>Однодневное событие с датой: {$event['_from_month']}/{$event['_from_day']}/{$event['_from_year']}</p>
EOF;

//--endhtml--//
return $IPBHTML;
}




}

?>