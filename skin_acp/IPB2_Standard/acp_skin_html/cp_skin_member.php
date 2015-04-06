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
		show = '��������� �� ����������\\n������� �� ���� ������-������ ��� ���������';
	}
	
	alert('��������� ����� �������\\n---------------------------------\\n' + show);
}
//]]>
</script>
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=doedit&amp;mid={$mem['id']}' id='mainform' onsubmit='ValidateForm()' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<input type='hidden' name='curemail' value='{$mem['email']}' />
<input type='hidden' name='curgroup' value='{$mem['mgroup']}' />
{$form['_custom_hidden_fields']}
<div class='tabwrap'>
	<div id='tabtab-1' class='taboff'>�������� ���������</div>
	<div id='tabtab-2' class='taboff'>��������� �������</div>
	<div id='tabtab-3' class='taboff'>��������� ������</div>
	<div id='tabtab-4' class='taboff'>�������</div>
	<div id='tabtab-5' class='taboff'>�������������� ����</div>
</div>
<div class='tabclear'>���������: {$mem['members_display_name']} <span style='font-weight:normal'>(ID: {$mem['id']})</span></div>
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
						   <td width='40%' class='tablerow1'><strong>IP-����� � ������ �����������</strong></td>
						   <td width='60%' class='tablerow2'>
								<a href='{$this->ipsclass->base_url}&amp;section={$this->ipsclass->section_code}&amp;act=mtools&amp;code=learnip&amp;ip={$mem['ip_address']}' title='������ ������ �� ���� IP'>{$mem['ip_address']}</a>
								[ <a href='{$this->ipsclass->base_url}&amp;section={$this->ipsclass->section_code}&amp;act=mtools&amp;code=showallips&amp;member_id={$mem['id']}'>�������� ��� IP-������</a> ]
						   </td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>E-mail �����</strong></td>
							<td width='60%' class='tablerow2'>{$form['email']}</td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>���������� ���������</strong></td>
							<td width='60%' class='tablerow2'>{$form['posts']}</td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>������� ����������?</strong></td>
							<td width='60%' class='tablerow2'>{$form['remove_photo']}</td>
						  </tr>
						  <tr>
							<td width='40%' class='tablerow1'><strong>������� ������?</strong></td>
							<td width='60%' class='tablerow2'>{$form['remove_avatar']}</td>
						 </tr>
						 <tr>
							<td width='40%' class='tablerow1'><strong>������� ��������������</strong></td>
							<td width='60%' class='tablerow2'>
								{$form['warn_level']}
								[ <a href='#' onclick="ipsclass.pop_up_window('{$this->ipsclass->vars['board_url']}/index.php?act=warn&amp;mid={$mem['id']}&amp;CODE=view','500','450'); return false;">�������� �������</a> ]
								[ <a href='#' onclick="ipsclass.pop_up_window('{$this->ipsclass->vars['board_url']}/index.php?act=warn&amp;mid={$mem['id']}&amp;CODE=add_note','500','450'); return false;">�������� ������</a> ]
							</td>
						 </tr>
						 <tr>
							<td width='40%' class='tablerow1'><strong>������ ������</strong></td>
							<td width='60%' class='tablerow2'>{$form['member_title']}</td>
						 </tr>
						 </table>
					   
				</td>
			 </tr>
			</table>
			
			
		</fieldset>
		
		<br />
		
		<fieldset class='formmain-fieldset'>
		    <legend><strong>����� ����� ������������</strong></legend>
EOF;
if ( $form['_show_fixed'] != TRUE )
{
$IPBHTML .= <<<EOF
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			 <tr>
			   <td width='40%' class='tablerow1'><strong>�������� ������</strong><div style='color:gray'>������������ ����������� ��������� ������.</div></td>
			   <td width='60%' class='tablerow2'>{$form['mgroup']}</td>
			  </tr>
			  <tr>
				<td width='40%' class='tablerow1'><strong>�������������� ������</strong><br />�� ������ ������� ��������� �����.<div style='color:gray'>������������ ������� ������� ����������� ���� ��������� �������������� �����.</div></td>
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
		    <legend><strong>��������� ������� � ���������� ���������</strong></legend>
		     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
			 <tr>
			   <td width='40%' class='tablerow1'><strong>�������� ����� ������� ������ ��</strong><br />�� ������ ������� ��������� ����� �������.<div style='color:gray'>��������� ����� ������� ������� ����� ������������ �� ��������� ����� (�� �������� � ��������������).</div></td>
			   <td width='60%' class='tablerow2'>
					<input type='checkbox' name='override' {$form['_permid_tick']} value='1' > <b>�������� ����� ������� ������ ��</b><br />
					{$form['permid']}
					<br><input style='margin-top:5px' id='editbutton' type='button' onclick='show_me();' value='�������� ��������� ����� �������'>
			   </td>
			  </tr>
			  <tr>
				<td width='40%' class='tablerow1'><strong>��������� �������� ����������� ���� ��������� ������������?</strong><div style='color:gray'>���� ���, ��� ��������� ������������ ����� ����������� ������������. �������� ���� ������ � ������� ������� ��� ���������� �����������.</div></td>
				<td width='60%' class='tablerow2'>
					<input type='checkbox' name='mod_indef' value='1' {$form['_mod_tick']}> ������ ���������
					<br />
					<strong>��� ��</strong>
					{$form['mod_timespan']} {$form['mod_units']} {$form['_mod_extra']}
				</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>��������� ������������ {$mem['members_display_name']} ����������� ���������?</strong><div style='color:gray'>�������� ���� ������ � ������� ������� ��� ���������� �����������.</div></td>
				<td width='60%' class='tablerow2'>
					<input type='checkbox' name='post_indef' value='1' {$form['_post_tick']}> �������� ���������
					<br />
					<strong>��� ��</strong>
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
		    <legend><strong>��������� ������</strong></legend>
		     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
			  <tr>
				<td width='40%' class='tablerow1'><strong>���� ����������</strong></td>
				<td width='60%' class='tablerow2'>{$form['language']}</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>�����</strong></td>
				<td width='60%' class='tablerow2'><select name='skin' class='dropdown'><option value='0'>--��� / �� ���������--</option>{$form['_skin_list']}</select></td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>������ e-mail ����� �� ��������� �������������?</strong></td>
				<td width='60%' class='tablerow2'>{$form['hide_email']}</td>
			 </tr>
			 <tr>
				<td width='40%' class='tablerow1'><strong>���������� ������ �� email ��� ��������� ������� ���������?</strong></td>
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
			    <legend><strong>���������� ����������</strong></legend>
			     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
					<td width='40%' class='tablerow1'><strong>��� � AIM</strong></td>
					<td width='60%' class='tablerow2'>{$form['aim_name']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>��� � MSN</strong></td>
					<td width='60%' class='tablerow2'>{$form['msnname']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>��� � Yahoo!</strong></td>
					<td width='60%' class='tablerow2'>{$form['yahoo']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>����� ICQ</strong></td>
					<td width='60%' class='tablerow2'>{$form['icq_number']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>����� �������� ��������</strong></td>
					<td width='60%' class='tablerow2'>{$form['website']}</td>
				 </tr>
				 </table>
		   </fieldset>
		
			<br />
			
			<fieldset class='formmain-fieldset'>
			    <legend><strong>������ ����������</strong></legend>
			     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
				  <tr>
					<td width='40%' class='tablerow1'><strong>������</strong></td>
					<td width='60%' class='tablerow2'>{$form['avatar']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>���� �������</strong></td>
					<td width='60%' class='tablerow2'>{$form['avatar_type']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>������ �������</strong></td>
					<td width='60%' class='tablerow2'>{$form['avatar_size']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>����� ����������</strong></td>
					<td width='60%' class='tablerow2'>{$form['location']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>��������</strong></td>
					<td width='60%' class='tablerow2'>{$form['interests']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>���</strong></td>
					<td width='60%' class='tablerow2'>{$form['pp_gender']}</td>
				 </tr>
				 <tr>
					<td width='40%' class='tablerow1'><strong>� ����</strong></td>
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
	    <legend><strong>������� ������������</strong></legend>
		{$form['signature']}
	</fieldset>
</div>
<div id='tabpane-5' class='formmain-background'>
	<fieldset class='formmain-fieldset'>
	    <legend><strong>�������������� ���� �������</strong></legend>
		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			{$form['_custom_fields']}
		</table>
	</fieldset>
</div>
<div align='center' class='tablefooter'>
 	<div class='formbutton-wrap'>
 		<div id='button-save'><img src='{$this->ipsclass->skin_acp_url}/images/icons_form/save.gif' border='0' alt='���������'  title='���������' class='ipd-alt' /> ���������</div>
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
  <td align='left' width='40%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>���������������� ������� ������</td>
  <td align='right' width='60%'>
   <form name='selectform' id='selectform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=mod' method='post'>
   <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
   <select name='filter' class='dropdown'>
    <option value='all'>�������� ����</option>
    <option value='reg_user_validate'>������������������ (������������� �������������)</option>
    <option value='reg_admin_validate'>������������������ (������������� ���������������)</option>
    <option value='email_chg'>��������� e-mail ������</option>
   </select>
   <input type='submit' class='realbutton' value=' �������� &gt;' />
   </form>
  </td>
 </tr>
 </table>
 </div>
 <form name='theAdminForm' id='adminform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=domod' method='post'>
 <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
 <table cellpadding='4' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='20%'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=mod&amp;st=$st&amp;sort=mem&amp;ord=$new_ord'>������������ ���</a></td>
  <td class='tablesubheader' width='15%'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=email&ord=$new_ord'>E-mail</a></td>
  <td class='tablesubheader' width='20%'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=sent&ord=$new_ord'>Email �������.</a></td>
  <td class='tablesubheader' width='5%' align='center'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=posts&ord=$new_ord'>���������</a></td>
  <td class='tablesubheader' width='15%'><a href='{$this->ipsclass->base_url}&{$this->ipsclass->form_code}&code=mod&st=$st&sort=reg&ord=$new_ord'>���������������</a></td>
  <td class='tablesubheader' width='1%'><input type='checkbox' id='maincheckbox' onclick='check_boxes()' /></td>
 </tr>
 {$content}
 <tr>
  <td class='tablesubheader' colspan='2' align='left'>{$links}</td>
  <td class='tablesubheader' colspan='4' align='right'>
   <select name='type' class='dropdown'><option value='approve'>������������</option><option value='delete'>�������</option><option value='resend'>��������� �������� ������ �� e-mail</option></select>
   <input type='submit' class='realbutton' value=' ��������� &gt;&gt;' />
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
  <td class='tablerow1'><span style='color:green'>{$r['_where']}</span><br />{$r['_entry']}<div class='desctext'>{$r['_days']} ���� � {$r['_rhours']} ����� �����</div></td>
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
  <td align='left' width='100%' style='font-size:12px; vertical-align:middle;font-weight:bold; color:#FFF;'>��������������� ������� ������</td>
 </tr>
 </table>
 </div>
 <form name='theAdminForm' id='adminform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=unlock' method='post'>
 <input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
 <table cellpadding='4' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='20%'>������������ ���</td>
  <td class='tablesubheader' width='15%'>E-mail �����</td>
  <td class='tablesubheader' width='20%'>��������� �������</td>
  <td class='tablesubheader' width='5%' align='center'>���������</td>
  <td class='tablesubheader' width='15%'>�����������</td>
  <td class='tablesubheader' width='1%'><input type='checkbox' id='maincheckbox' onclick='check_boxes()' /></td>
 </tr>
 {$content}
 <tr>
  <td class='tablesubheader' colspan='2' align='left'>{$links}</td>
  <td class='tablesubheader' colspan='4' align='right'>
   <select name='type' class='dropdown'><option value='unlock'>��������������</option><option value='ban'>�������������</option></select>
   <input type='submit' class='realbutton' value=' ��������� &gt;&gt;' />
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
  <td class='tablerow2'><strong>{$r['members_display_name']}</strong><div class='desctext'>������: {$r['group_title']}</div><div class='desctext'>{$r['ip_addresses']}</div></td>
  <td class='tablerow1'>{$r['email']}</td>
  <td class='tablerow1'>������: {$r['oldest_fail']}<br />�����: {$r['newest_fail']}<br />�����: {$r['failed_login_count']}</td>
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