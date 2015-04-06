<?php

class cp_skin_bbcode_badword {

var $ipsclass;

//===========================================================================
// BBCODE: Wrapper
//===========================================================================
function bbcode_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

 <div class="tableborder">
						<div class="tableheaderalt">
						<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
				  <td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Дополнительные BB-коды</td>
				  <td align='right' nowrap='nowrap' style='padding-right:6px'><img id='menumainone' src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
				  </tr>
				  </table> </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='45%'>Название</td>
  <td class='tablesubheader' width='50%'>Тег</td>
  <td class='tablesubheader' width='5%'>Опции</td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'><div class='fauxbutton-wrapper'><span class='fauxbutton'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=bbcode_add'>Добавить BB-код</a></span></div></div>
</div>
<br />

 <script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_export   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=bbcode_export'>Экспортировать BB-коды</a>" ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// BBCODE: Row
//===========================================================================
function bbcode_row( $row ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>{$row['bbcode_title']}</td>
 <td class='tablerow1'>{$row['bbcode_fulltag']}</td>
 <td class='tablerow2'><img id="menu{$row['bbcode_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$row['bbcode_id']}",
  new Array( img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=bbcode_edit&id={$row['bbcode_id']}'>Изменить</a>",
  			 img_delete   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=bbcode_delete&id={$row['bbcode_id']}'>Удалить</a>"
		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// BADWORD: Wrapper
//===========================================================================
function badword_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

 <div class="tableborder">
						<div class="tableheaderalt">
						<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
				  <td align='left' width='100%' style='font-weight:bold;font-size:11px;color:#FFF'>Список фильтров</td>
				  <td align='right' nowrap='nowrap' style='padding-right:6px'><img id='menumainone' src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
				  </tr>
				  </table> </div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='40%'>Слово</td>
  <td class='tablesubheader' width='40%'>Замена</td>
  <td class='tablesubheader' width='15%'>Метод</td>
  <td class='tablesubheader' width='5%'>Опции</td>
 </tr>
 $content
 </table>
</div>
<br />
<div class="tableborder">
						<div class="tableheaderalt">Добавление нового фильтра</div>

<table align="center" border="0" cellpadding="5" cellspacing="0" width="100%"><tbody><tr>
<td class="tablesubheader" align="center" width="40%">Слово</td>
<td class="tablesubheader" align="center" width="40%">Замена</td>
<td class="tablesubheader" align="center" width="20%">Метод</td>
</tr>
<tr>

<td class="tablerow1" valign="middle" width="40%"><input name="before" value="" size="30" class="textinput" type="text"></td>
<td class="tablerow2" valign="middle" width="40%"><input name="after" value="" size="30" class="textinput" type="text"></td>
<td class="tablerow1" valign="middle" width="20%"><select name="match" class="dropdown">
<option value="1">Точный</option>
<option value="0">Маска</option>
</select>

</td>
</tr>
<tr><td class="tablesubheader" colspan="3" align="center"><input value="Добавить" class="realbutton" accesskey="s" type="submit"></form></td></tr>
</table></div><br />

 <script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_export   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=badword_export'>Экспортировать фильтры</a>" ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// BADWORD: Row
//===========================================================================
function badword_row( $row ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>{$row['type']}</td>
 <td class='tablerow1'>{$row['replace']}</td>
 <td class='tablerow1'>{$row['method']}</td>
 <td class='tablerow2'><img id="menu{$row['wid']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$row['wid']}",
  new Array( img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=badword_edit&id={$row['wid']}'>Изменить</a>",
  			 img_delete   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=badword_remove&id={$row['wid']}'>Удалить</a>"
		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}


}


?>