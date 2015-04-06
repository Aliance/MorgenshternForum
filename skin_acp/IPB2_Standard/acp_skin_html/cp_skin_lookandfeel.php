<?php

class cp_skin_lookandfeel {

var $ipsclass;


//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function template_bits_bit_row_image( $id, $image ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<img id='img-item-{$id}' src='{$this->ipsclass->skin_acp_url}/images/{$image}' border='0' alt='' />
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function template_bits_overview_row_normal( $group, $folder_blob, $count_string ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tablerow1' id='dv-{$group['group_name']}'>
 <div style='float:right'>
  ($count_string)&nbsp;{$group['easy_preview']}
 </div>
 <div align='left'>
   <img src='{$this->ipsclass->skin_acp_url}/images/folder.gif' alt='Группа шаблонов' style='vertical-align:middle' />
   {$folder_blob}&nbsp;<a style='font-size:11px' id='gn-{$group['group_name']}' onclick='template_load_bits("{$group['group_name']}", event)' title='{$group['easy_desc']}' href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=template-bits-list&id={$group['_id']}&p={$group['_p']}&group_name={$group['group_name']}&'>{$group['easy_name']}</a>
   <span id='match-{$group['group_name']}'></span>
 </div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function template_bits_bit_row( $sec, $custom_bit, $remove_button, $altered_image ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tablerow1' id='dvb-{$sec['func_name']}' title='Выберите нужные ячейки для редактирования сразу нескольких шаблонов' onclick='parent.template_toggle_bit_row("{$sec['func_name']}")' >
 <div style='float:right;width:auto;'>
  $remove_button
  <a style='text-decoration:none' title='Предпросмотр в виде текста' href='#' onclick='pop_win("act=rtempl&code=preview&suid={$sec['suid']}&type=text"); parent.template_cancel_bubble( event, true );'><img src='{$this->ipsclass->skin_acp_url}/images/te_text.gif' border='0' alt='Предпросмотр в виде текста'></a>
  <a style='text-decoration:none' title='Предпросмотр в виде HTML' href='#' onclick='pop_win("act=rtempl&code=preview&suid={$sec['suid']}&type=css");  parent.template_cancel_bubble( event, true );'><img src='{$this->ipsclass->skin_acp_url}/images/te_html.gif' border='0' alt='Предпросмотр в виде HTML'>&nbsp;</a>
 </div>
 <div align='left'>
   <img src='{$this->ipsclass->skin_acp_url}/images/file.gif' title='Шаблон: {$sec['set_id']}' alt='Шаблон' style='vertical-align:middle' />
   {$altered_image}
   <a id='bn-{$sec['func_name']}' onclick='parent.template_load_editor("{$sec['func_name']}", event)' href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=template-edit-bit&bitname={$sec['func_name']}&p={$sec['_p']}&id={$sec['_id']}&group_name={$sec['group_name']}&type=single' title='название шаблона: {$sec['func_name']}'>{$sec['easy_name']}</a>
   {$custom_bit}
 </div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function template_bits_bit_overview( $group, $content, $add_button ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tablerow3'>
 <div style='float:right;padding-top:3px'><strong>{$group['easy_name']}</strong></div>
 <div>
  <a href='#' onclick="parent.template_close_bits(); return false;" title='Закрыть блок'><img src='{$this->ipsclass->skin_acp_url}/images/skineditor_close.gif' border='0' alt='Закрыть' /></a>&nbsp;
  <!--<a href='#' onclick="toggleselectall(); return false;" title='Пометить все/Снять отметку со всех'><img src='{$this->ipsclass->skin_acp_url}/images/skineditor_tick.gif' border='0' alt='Отметить/снять отметку' /></a>-->
 </div>
</div>
<div id='template-bits-container'>
{$content}
</div>
 <div style='background:#CCC'>
   <div align='left' style='padding:5px;margin-left:25px'>
   <div style='float:right'>$add_button</div>
   <div><input type='button' onclick='parent.template_load_bits_to_edit("{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=template-edit-bit&id={$this->ipsclass->input['id']}&p={$this->ipsclass->input['p']}&group_name={$group['group_name']}&type=multiple")' class='realbutton' value='Изменить выбранное' /></div>
 </div>
</div>
<script type="text/javascript">
//<![CDATA[
parent.template_bits_onload();
//]]>
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
//  LOOK AND FEEL: TEMPLATES
//===========================================================================
function template_bits_overview($content, $javascript) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript">
//<![CDATA[
var lang_matches = "различий";
$javascript
//]]>
</script>
<script type="text/javascript" src='{$this->ipsclass->vars['board_url']}/jscripts/ipb_xhr_findnames.js'></script>
<script type="text/javascript" src='{$this->ipsclass->skin_acp_url}/acp_template.js'></script>
<div id='ipb-get-members' style='border:1px solid #000; background:#FFF; padding:2px;position:absolute;width:210px;display:none;z-index:10'></div>
<div class='tableborder'>
 <div class='tableheaderalt'>
  <table cellpadding='0' cellspacing='0' border='0' width='100%'>
  <tr>
  <td align='left' width='100%'>
   <div id='quick-search-box'>
    <form id='quick-search-form'>
     <input type='text' size='20' class='realwhitebutton' style='width:210px' name='searchkeywords' id='entered_template' autocomplete="off" value='' />&nbsp;<input type='button' onclick='template_find_bits(event)' class='realbutton' value='Искать в шаблонах' />
    </form>
   </div>
  </td>
  </tr>
  </table>
</div>
<div id='template-edit' style='height:0px;width:100%;display:none;z-index:1'><iframe id='te-iframe' name='te-iframe' onload='template_iframe_loaded( "te" )' style='width:0;height:0px;display:none' src='javascript:;'></iframe></div>
<table id='template-main-wrap' width='100%'>
	<tr>
   		<td valign='top' id='template-sections' style='width:100%;height:476px;max-height:476px;z-index:3;'>
    		<div style='margin:0px;padding:0px;width:100%;overflow:auto;height:476px;max-height:476px;'>
	 			$content
			</div>
   		</td>

   		<td valign='top' id='template-bits' style='width:0%;display:none;height:476px;max-height:476px;'>
   			<iframe id='tb-iframe' name='tb-iframe' onload='template_iframe_loaded( "tb" )' style='width:0%;display:none;height:476px;max-height:476px;' src='javascript:;'></iframe>
  		</td>
 	</tr>
 	<tr>
 		<td colspan='2' align='center' class='tablefooter'>&nbsp;</td>
 	</tr>
</table>
<br clear='all' />
<br />
<div style='padding:4px;'><strong>Информация:</strong><br />
<img id='img-altered' src='{$this->ipsclass->skin_acp_url}/images/skin_item_altered.gif' border='0' alt='+' title='Изменен от начального варианта шаблона' /> Изменен от начального варианта шаблона.
<br /><img id='img-unaltered' src='{$this->ipsclass->skin_acp_url}/images/skin_item_unaltered.gif' border='0' alt='-' title='Не изменен от начального варианта шаблона' /> Не изменен от начального варианта шаблона.
<br /><img id='img-inherited' src='{$this->ipsclass->skin_acp_url}/images/skin_item_inherited.gif' border='0' alt='|' title='Унаследованный от начального варианта шаблона' /> Унаследованный от начального варианта шаблона.
</div>
<script type='text/javascript'>
//<![CDATA[
// INIT images
img_revert_blank = '{$this->ipsclass->skin_acp_url}/images/blank.gif';
img_revert_real  = '{$this->ipsclass->skin_acp_url}/images/te_revert.gif';
img_revert_width  = 44;
img_revert_height = 16;

template_init();
// INIT find names
init_js( 'quick-search-form', 'entered_template', 'get-template-names&id={$this->ipsclass->input['id']}' );
// Run main loop
setTimeout( 'main_loop()', 10 );
//]]>
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
//  LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_sets_overview($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td align='left' width='95%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Стили</td>
  <td align='right' width='5%' nowrap='nowrap'>
   <img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /> &nbsp;
 </td>
 </tr>
</table>
 </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 $content
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
<br />
<div><strong>Информация:</strong><br />
<img src='{$this->ipsclass->skin_acp_url}/images/skin_item_altered.gif' border='0' alt='+' title='Изменен от начального варианта шаблона' /> Изменен от начального варианта шаблона.
<br /><img src='{$this->ipsclass->skin_acp_url}/images/skin_item_unaltered.gif' border='0' alt='-' title='Не изменен от начального варианта шаблона' /> Не изменен от начального варианта шаблона.
<br /><img src='{$this->ipsclass->skin_acp_url}/images/skin_item_inherited.gif' border='0' alt='|' title='Унаследованный от начального варианта шаблона' /> Унаследованный от начального варианта шаблона.
</div>
<div id='menumainone_menu' style='display:none' class='popupmenu'>
	<form action='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=addset&id=-1' method='POST'>
	<div align='center'><strong>Создать новый стиль</strong></div>
	<div align='center'><input type='text' name='set_name' size='20' value='Введите название стиля' onfocus='this.value=""'></center></div>
	<div align='center'><input type='submit' value='Создать' class='realdarkbutton' /></div>
	</form>
</div>
<script type="text/javascript">
	ipsmenu.register( "menumainone" );
</script>
EOF;
if ( IN_DEV == 1 )
{
$IPBHTML .= <<<EOF
<br />
<div align='center'>
  Экспорт данных: <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=exportmaster'>Все HTML шаблоны главного стиля</a>
  &middot; <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=exportbitschoose'>Выбрать HTML шаблоны</a>
  &middot; <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=exportmacro'>Макросы главного стиля</a>
</div>
<br />
EOF;
}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_sets_overview_row( $r, $forums, $hidden, $default, $menulist, $i_sets, $no_sets, $folder_icon, $line_image, $css_extra ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
   <!--$i_sets,$no_sets-->{$line_image}<!--ID:{$r['set_skin_set_id']}--><img src='{$this->ipsclass->skin_acp_url}/images/{$folder_icon}' border='0' alt='Стиль' style='vertical-align:middle' />
   <strong style='{$css_extra}'>{$r['set_name']}</strong>
 </td>
 <td class='tablerow1' width='5%' nowrap='nowrap' align='center'>{$forums} {$hidden} {$default}</td>
 <td class='tablerow1' width='5%'><img id="menu{$r['set_skin_set_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$r['set_skin_set_id']}",
  new Array(
			$menulist
  		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_sets_overview_row_menulist( $r ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
img_edit   + " <!--ALTERED.wrappper--><a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=wrap&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Изменить общий шаблон форума</a>",
img_edit   + " <!--ALTERED.templates--><a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=templ&code=template-sections-list&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Изменить HTML шаблоны</a>",
img_edit   + " <!--ALTERED.css--><a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=style&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Изменить таблицу стилей (CSS — расширенный режим)</a>",
img_edit   + " <!--ALTERED.css--><a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=style&code=colouredit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Изменить цвета (CSS — простой режим)</a>",
img_edit   + " <!--ALTERED.macro--><a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=image&code=edit&id={$r['set_skin_set_id']}&p={$r['set_skin_set_parent']}'>Изменить макросы</a>",
img_edit   + " <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=edit&id={$r['set_skin_set_id']}'>Настройки</a>",
EOF;
if ( $r['set_skin_set_id'] != 1 )
{
$IPBHTML .= <<<EOF

img_item   + " <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=revertallform&id={$r['set_skin_set_id']}'>Удалить все внесенные изменения</a>",
img_export   + " <a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=import&code=showexportpage&id={$r['set_skin_set_id']}'>Экспортировать</a>",
img_view   + " <a href='{$this->ipsclass->base_url}&section={$this->ipsclass->section_code}&act=skindiff&code=skin_diff_from_skin&skin_id={$r['set_skin_set_id']}'>Создать отчет сравнения</a>",
img_delete   + " <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=remove&id={$r['set_skin_set_id']}'>Удалить</a>",
EOF;
}
if ( $r['set_skin_set_id'] != 1 AND ! $r['set_skin_set_parent'] )
{
$IPBHTML .= <<<EOF

img_add   + " <a  href='#' onclick=\"addnewpop('{$r['set_skin_set_id']}','menu_{$r['set_skin_set_id']}')\">Добавить подстиль с настройками текущего</a>",
EOF;
}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
//  LOOK AND FEEL: SKIN DIFF: MAIN
//===========================================================================
function skin_diff_main_overview($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript" src='{$this->ipsclass->skin_acp_url}/acp_template.js'></script>
<div class='tableborder'>
 <div class='tableheaderalt'>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td align='left' width='95%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Отчет сравнения стилей</td>
 </tr>
</table>
 </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='90%'><strong>Название</strong></td>
  <td class='tablesubheader' width='5%'>Создано</a>
  <td class='tablesubheader' width='5%'>&nbsp;</a>
 </tr>
 $content
 </table>
 <div align='right' class='tablefooter'>&nbsp;</div>
</div>
<br />
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=skin_diff' enctype='multipart/form-data' method='POST'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>Создать новый отчет сравнения стилей</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td class='tablerow1'><strong>Введите название</strong><div class='desctext'>Это название будет использовано в списке выше, когда сравнение будет завершено</div></td>
  <td class='tablerow2'><input class='textinput' type='text' size='30' name='diff_session_title' /></td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>Пропускать все новые/ошибочные шаблоны?</strong><div class='desctext'>Если вы сравниваете старый ipb_templates.xml из более старого IPB, вы можете отключить этот пункт. Если вы сравниваете с XML файлом из измененного стиля, вы должны включить этот пункт.</div></td>
  <td class='tablerow2'><input class='textinput' type='checkbox' value='1' name='diff_session_ignore_missing' /></td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>Выберите корректный файл «ipb_templates.xml».</strong><div class='desctext'>Будет произведено сравнение с HTML шаблонами главного стиля</div></td>
  <td class='tablerow2'><input class='textinput' type='file' size='30' name='FILE_UPLOAD' /></td>
 </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='Импортировать' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_diff_main_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'> <strong>{$data['diff_session_title']}</strong></td>
 <td class='tablerow1' width='5%' nowrap='nowrap' align='center'>{$data['_date']}</td>
 <td class='tablerow1' width='5%'><img id="menu{$data['diff_session_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['diff_session_id']}",
  new Array(
			img_view   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=skin_diff_view&amp;diff_session_id={$data['diff_session_id']}'>Просмотр результатов сравнения</a>",
			img_delete   + " <a href='#' onclick=\"checkdelete('{$this->ipsclass->form_code}&code=skin_diff_remove&diff_session_id={$data['diff_session_id']}')\">Удалить результаты сравнения</a>",
			img_export   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=skin_diff_export&amp;diff_session_id={$data['diff_session_id']}'>Создать HTML экспорт</a>"
  		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
//  LOOK AND FEEL: SKIN DIFF
//===========================================================================
function skin_diff_overview($content, $missing, $changed) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type="text/javascript" src='{$this->ipsclass->skin_acp_url}/acp_template.js'></script>
<div class='tableborder'>
 <div class='tableheaderalt'>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td align='left' width='95%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Сравнение стилей</td>
  <td align='right' width='5%' nowrap='nowrap'>
  &nbsp;
 </td>
 </tr>
</table>
 </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='90%'><strong>Название шаблона</strong></td>
  <td class='tablesubheader' width='5%'>Различия</a>
  <td class='tablesubheader' width='5%'>Размер</a>
  <td class='tablesubheader' width='5%'>&nbsp;</a>
 </tr>
 $content
 </table>
 <div align='right' class='tablefooter'>$missing новых шаблонов и $changed измененных</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES: NEW GROUP
//===========================================================================
function skin_diff_row_newgroup( $group_name ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='4' class='tablerow3'>
   <strong>{$group_name}</strong>
 </td>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_diff_row( $template_bit_name, $template_bit_size, $template_bit_id, $diff_is, $template_bit_id_safe ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
   <strong>{$template_bit_name}</strong>
 </td>
 <td class='tablerow1' width='5%' nowrap='nowrap' align='center'>{$diff_is}</td>
 <td class='tablerow1' width='5%' nowrap='nowrap' align='center'>{$template_bit_size}</td>
 <td class='tablerow1' width='5%'><img id="menu{$template_bit_id_safe}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$template_bit_id_safe}",
  new Array(
			img_view   + " <a href='#' onclick=\"return template_view_diff('$template_bit_id')\">Просмотр различий</a>"
  		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_css_view_bit( $diff ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
$diff
</div>
<div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
 <span class='diffred'>Удален HTML код</span> &middot; <span class='diffgreen'>Добавлен HTML код</span>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_diff_view_bit( $template_bit_name, $template_group, $content ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
<strong>$template_group &gt; $template_bit_name</strong>
</div>
<div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
$content
</div>
<div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
 <span class='diffred'>Удален HTML код</span> &middot; <span class='diffgreen'>Добавлен HTML код</span>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_diff_export_row( $func_name, $func_group, $content ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
<h2>$func_group <span style='color:green'>&gt;</span> $func_name</h2>
<hr>
$content
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// LOOK AND FEEL: TEMPLATES
//===========================================================================
function skin_diff_export_overview( $content, $missing, $changed, $title, $date ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset={$this->ipsclass->vars['gb_char_set']}" />
  <title>$title export</title>
  <style type="text/css">
   BODY
   {
   	font-family: verdana;
   	font-size:11px;
   	color: #000;
   	background-color: #CCC;
   }

   del,
   .diffred
   {
	   background-color: #D7BBC8;
	   text-decoration:none;
   }

   ins,
   .diffgreen
   {
	   background-color: #BBD0C8;
	   text-decoration:none;
   }

   h1
   {
   	font-size: 18px;
   }

   h2
   {
   	font-size: 18px;
   }
  </style>
 </head>
<body>
  <div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
  <h1>$title (экспортировано: $date)</h1>
  <strong>$missing новых шаблонов и $changed измененных</strong>
  </div>
  <br />
  $content
  <br />
  <div style='padding:4px;border:1px solid #000;background-color:#FFF;margin:4px;'>
   <span class='diffred'>Удален HTML код</span> &middot; <span class='diffgreen'>Добавлен HTML код</span>
  </div>
</body>
<html>
EOF;

//--endhtml--//
return $IPBHTML;
}


function emoticon_overview_wrapper_addform( )
{

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type='text/javascript'>
function addfolder()
{
	document.macroform.emoset.value       = '';
	document.macroform.code.value         = 'emo_setadd';
	document.macroform.submitbutton.value = 'Создать';
	scroll(0,0);
	togglediv( 'popbox', 1 );
	return false;
}

function editfolder(id)
{
	document.macroform.submitbutton.value = 'Изменить название';
	document.macroform.id.value     = id;
	document.macroform.code.value   = 'emo_setedit';
	document.macroform.emoset.value = id;
	scroll(0,0);
	togglediv( 'popbox', 1 );
	return false;
}
</script>
<div align='center' style='position:absolute;display:none;text-align:center' id='popbox'>
 <form name='macroform' action='{$this->ipsclass->base_url}' method='post'>
 <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
 <input type='hidden' name='act'     value='emoticons' />
 <input type='hidden' name='section' value='lookandfeel' />
 <input type='hidden' name='code'    value='emo_setadd' />
 <input type='hidden' name='id' value='' />

 <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
 <tr>
  <td width='1%' nowrap='nowrap' valign='top' align='center'>
   <b>Имя директории</b><br><input class='textinput' name='emoset' type='text' size='40' />
   <br /><br />
   <center><input type='submit' class='realbutton' value='Создать' name='submitbutton' /> <input type='button' class='realdarkbutton' value='Закрыть' onclick="togglediv('popbox');" /></center>
  </td>
 </tr>

 </table>
 </form>
</div>

EOF;

return $IPBHTML;
}


function emoticon_overview_wrapper( $content )
{

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}' method='post' name='uploadform'  enctype='multipart/form-data' id='uploadform'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<input type='hidden' name='code' value='emo_upload'>
<input type='hidden' name='act' value='emoticons'>
<input type='hidden' name='MAX_FILE_SIZE' value='10000000000'>
<input type='hidden' name='dir_default' value='1'>
<input type='hidden' name='section' value='lookandfeel'>
<div class='tableborder'>
<div class='tableheaderalt'>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
  <td align='left' width='95%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>Текущие директории со смайликами</td>
  <td align='right' width='5%' nowrap='nowrap'>
   <img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /> &nbsp;
 </td>
 </tr>
</table>
</div>

<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'><tr>
<td class='tablesubheader' width='50%' align='center'>Название директории</td>

<td class='tablesubheader' width='5%' align='center'>Загрузка</td>
<td class='tablesubheader' width='20%' align='center'>№ директории</td>
<td class='tablesubheader' width='20%' align='center'>№ группы смайликов</td>
<td class='tablesubheader' width='5%' align='center'>Опции</td>
</tr>

{$content}

</table>
</div>
<br />

EOF;

if( SAFE_MODE_ON )
{
$IPBHTML .= <<<EOF
	</form>
EOF;
}
else
{
$IPBHTML .= <<<EOF
<div class='tableborder'>
	 <div class='tableheaderalt'>Загрузка смайликов</div>
	 <table width='100%' border='0' cellpadding='4' cellspacing='0'>
	 <tr>
	  <td width='50%' class='tablerow1' align='center'><input type='file' value='' class='realbutton' name='upload_1' size='30' /></td>

	  <td width='50%' class='tablerow2' align='center'><input type='file' class='realbutton' name='upload_2' size='30' /></td>
	 </tr>
	 <tr>
	  <td width='50%' class='tablerow1' align='center'><input type='file' class='realbutton' name='upload_3' size='30' /></td>
	  <td width='50%' class='tablerow2' align='center'><input type='file' class='realbutton' name='upload_4' size='30' /></td>
	 </tr>
	 </table>
	 <div class='tablesubheader' align='center'><input type='submit' value='Загрузить смайлики в выбранные директории' class='realdarkbutton' /></form></div>
</div>
EOF;
}

$IPBHTML .= <<<EOF

<script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array(
  			 img_add + " <a href='#' onclick='addfolder(); return false;' style='color:#000;'><strong>Создать новую директорию</strong></a>"
           ) );
</script>
EOF;

return $IPBHTML;
}


function emoticon_overview_row( $data=array() )
{

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
	<tr>

	 <td class='tablerow2' valign='middle'>
	 	<div style='width:auto;float:right;'><img src='{$this->ipsclass->skin_acp_url}/images/{$data['icon']}' title='{$data['title']}' alt='{$data['icon']}' /></div>
	 	{$data['line_image']}<img src='{$this->ipsclass->skin_acp_url}/images/emoticon_folder.gif' border='0'>&nbsp;<a href='{$this->ipsclass->base_url}&section=lookandfeel&amp;act=emoticons&code=emo_manage&id={$data['dir']}' title='Управление смайликами, которые находятся в этой директории'><b>{$data['dir']}</b></a>
	 </td>

	 <td class='tablerow1' valign='middle'><center>{$data['checkbox']}</center></td>
	 <td class='tablerow2' valign='middle'><center>{$data['count']}</center></td>
	 <td class='tablerow1' valign='middle'><center>{$data['dir_count']}</center></td>
	 <td class='tablerow2' valign='middle'><center><img id="menu{$data['dir']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
	</tr>

<script type="text/javascript">
  menu_build_menu(
  "menu{$data['dir']}",
  new Array(
			img_item   + " <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=emo_manage&id={$data['dir']}'>Менеджер смайликов</a>",
EOF;

if( $data['dir'] != 'default' OR IN_DEV == 1 )
{
$IPBHTML .= <<<EOF
  			img_edit   + " <a href='#' onclick=\"editfolder('{$data['dir']}')\">Переименовать</a>",
  			img_delete   + " <a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=emo_setremove&id={$data['dir']}'>Удалить</a>"
EOF;
}
else
{
$IPBHTML .= <<<EOF
	img_delete + " <i>Эту директорию нельзя удалить</i>"
EOF;
}

$IPBHTML .= <<<EOF
  		    ) );
 </script>

EOF;

return $IPBHTML;
}

}


?>
