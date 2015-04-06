<?php

class cp_skin_index {

var $ipsclass;


//===========================================================================
// Index
//===========================================================================
function update_img( $url ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<iframe id='ips-security-check' src="$url" scrolling='auto' frameborder="0" style='border:0px;width:100%;padding:0px;margin:0px;height:88px;background:transparent'></iframe>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_licensed() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder' style='height:125px'>
 <div class='tableheaderalt'>Invision Power Board</div>
 <div class='tablerow1' style='padding:2px;overflow:auto'>
  <!--updateimg-->
 </div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function warning_converter() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
<td colspan='3'>
   <div style='color:red;border:1px solid red;background:#FFE1E2;padding:10px'>
   <span style='font-size:14px;font-weight:bold'>��������: ���������� ������� ��� ��������������� �������</span>
   <br /><br /><span style='font-size:10px;'>�� ����������� ������� ������� ��������������� � ����� ������������.
   <br />������� ���� <b>convert/index.php</b> � ��� ��������� ������ �� ����� ������������.</span></div>
</td>
</tr>
<tr>
<td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_unlicensed() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder' style='height:125px'>
 <div class='tableheaderalt'>�������������������� ������ Invision Power Board</div>
 <div class='tablerow1'>
  <!--updateimg-->
  <br /><strong>������ ������ Invision Power Board <span style='color:red'>�� ����������������.</span></strong>
  <br />������ ��������� � ����������� ����� <a href='http://external.iblink.ru/ipb' target='_blank'>�����</a>!
 </div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_ips_news($news_url) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder' style='height:125px'>
 <div class='tableheaderalt'>��������� ������� <a href="http://www.ibresource.ru/" target="blank">�������� IBResource</a></div>
 <div class='tablerow1' style='overflow:auto'>
  <iframe src="{$news_url}" scrolling='auto' frameborder="0" style='border:0px;width:100%;padding:0px;margin:0px;height:80px;background:#E4EAF2'></iframe></div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_notes($notes) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}&act=index&save=1' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<textarea name='notes' style='background-color:#F9FFA2;border:1px solid #CCC;width:95%;font-family:verdana;font-size:10px' rows='8' cols='25'>{$notes}</textarea>
<div align='center'><br /><input type='submit' value='���������' class='realdarkbutton' /></div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_board_offline() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
  <div class='tableborder'>
   <div class='tablesubheader'>����� ��������</div>
   <div class='tablerow2'>��� ����� � ������ ������ ��������<br><br>&raquo; <a href='{$this->ipsclass->base_url}&section=tools&act=op&code=findsetting&key=boardoffline'>�������� �����</a></div>
  </div>
 </td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_quick_clicks() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script language='javascript'>
//<![CDATA[
  function edit_member()
  {
	if (document.getElementById('DOIT').members_display_name.value == "")
	{
		alert("�� ������ ������ ��� ������������!");
		return false;
	}
  }

  function new_forum()
  {
	if (document.getElementById('DOIT1').forum_name.value == "")
	{
		alert("�� ������ ������ �������� ������!");
		return false;
	}
	else
	{
		window.location = '{$this->ipsclass->adskin->base_url}' + '&section=content&act=forum&code=new&name=' + escape(document.getElementById('DOIT1').forum_name.value);
	}
  }

  function phplookup()
  {
	if (document.getElementById('DOIT2').phpfunc.value == "")
	{
		alert("�� ������ ������ �������� ������� PHP!");
		return false;
	}
	else
	{
		window.location = 'http://www.php.net/' + escape(document.getElementById('DOIT2').phpfunc.value);
	}
  }
//]]>
</script>
<script type="text/javascript" src='{$this->ipsclass->vars['board_url']}/jscripts/ipb_xhr_findnames.js'></script>
<div id='ipb-get-members' style='border:1px solid #000; background:#FFF; padding:2px;position:absolute;width:120px;display:none;z-index:100'></div>
<div class='tableborder'>
 <div class='tableheaderalt'>������� ��������</div>
 <table width='100%' cellpadding='4' cellspacing='0'>
 <tr>
  <td class='tablerow1' width='40%'>�������������� ������������</td>
  <td class='tablerow2' width='60%'><form name='DOIT' id='DOIT' action='{$this->ipsclass->adskin->base_url}&section=content&act=mem&code=searchresults&searchtype=normal&' method='post'><input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' /><input type='text' size='20' class='textinput' id='members_display_name' name='members_display_name' value='' > <input type='submit' value='��������' class='realbutton' onclick='edit_member()'></form></td>
 </tr>
 <tr>
  <td class='tablerow1' width='40%'>�������� ������</td>
  <td class='tablerow2' width='60%'><form name='DOIT1' id='DOIT1' action='' onsubmit='new_forum();return false;'><input type='text' size='20' name='forum_name' class='textinput' value='�������� ������' onfocus='this.value=""'><input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' /> <input type='submit' value='�������' class='realbutton' onclick='new_forum();return false;'></form></td>
 </tr>
 <tr>
  <td class='tablerow1' width='40%'>������ �� PHP-��������</td>
  <td class='tablerow2' width='60%'><form name='DOIT2' id='DOIT2' action='' onsubmit='phplookup(); return false;'><input type='text' size='20'  name='phpfunc' class='textinput' value='�������� �������' onfocus='this.value=""'><input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' /> <input type='submit' value='�����' class='realbutton' onclick='phplookup(); return false;'></form></td>
 </tr>
 </table>
</div>
<script type="text/javascript">
	// INIT find names
	init_js( 'DOIT', 'members_display_name');
	// Run main loop
	var tmp = setTimeout( 'main_loop()', 10 );
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_version_history_row( $r ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>{$r['upgrade_version_human']} ({$r['upgrade_version_id']})</td>
 <td class='tablerow2'>{$r['_date']}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_version_history_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>������� ���������� (��������� 5)</div>
 <table width='100%' cellpadding='4' cellspacing='0'>
 $content
 </table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_last_logins_row( $r ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1' width='1' valign='middle'>
	<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/index/user.png' border='0' alt='-' class='ipd' />
 </td>
 <td class='tablerow1'>
	<strong>{$r['admin_username']}</strong>
	<div class='desctext'>IP: {$r['admin_ip_address']}</div>
 </td>
 <td class='tablerow2' align='center'>{$r['_admin_time']}</td>
 <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$r['_admin_img']}' border='0' alt='-' class='ipd' /></td>
 <td class='tablerow1' width='1' valign='middle'>
 	<a href='#' onclick="return ipsclass.pop_up_window('{$this->ipsclass->base_url}section=admin&amp;act=loginlog&amp;code=view_detail&amp;detail={$r['admin_id']}', 400, 400)" title='�������� �������'><img src='{$this->ipsclass->skin_acp_url}/images/folder_components/index/view.png' border='0' alt='-' class='ipd' /></a>
 </td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_last_logins_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>��������� 5 ������� ����������� � ��</div>
 <table width='100%' cellpadding='4' cellspacing='0'>
 <tr>
  <td class='tablesubheader' width='1%'>&nbsp;</td>
  <td class='tablesubheader' width='40%'>���</td>
  <td class='tablesubheader' width='49%' align='center'>�����</td>
  <td class='tablesubheader' width='5%' align='center'>������</td>
  <td class='tablesubheader' width='5%' align='center'>&nbsp;</td>
 </tr>
 $content
 </table>
 <div class='tablefooter' align='right'>
   <a href='{$this->ipsclass->base_url}section=admin&amp;act=loginlog' style='text-decoration:none'>����� &raquo;</a>
 </div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Index
//===========================================================================
function acp_onlineadmin_row( $r ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>{$r['members_display_name']}</td>
 <td class='tablerow2' align='center'>{$r['session_ip_address']}</td>
 <td class='tablerow2' align='center'>{$r['_log_in']}</td>
 <td class='tablerow2' align='center'>{$r['_click']}</td>
 <td class='tablerow2' align='center'>{$r['session_location']}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_onlineadmin_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>��������������, ����������� � ��</div>
 <table width='100%' cellpadding='4' cellspacing='0'>
 <tr>
  <td class='tablesubheader' width='20%'>���</td>
  <td class='tablesubheader' width='20%' align='center'>IP-�����</td>
  <td class='tablesubheader' width='20%' align='center'>�����</td>
  <td class='tablesubheader' width='20%' align='center'>��������� ��������</td>
  <td class='tablesubheader' width='20%' align='center'>��������</td>
 </tr>
 $content
 </table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_lastactions_row( $rowb ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1' width='1' valign='middle'>
	<img src='{$this->ipsclass->skin_acp_url}/images/folder_components/index/user.png' border='0' alt='-' class='ipd' />
 </td>
 <td class='tablerow1'>
	 <b>{$rowb['members_display_name']}</b>
	<div class='desctext'>IP: {$rowb['ip_address']}</div>
</td>
 <td class='tablerow2'>{$rowb['_ctime']}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_lastactions_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>��������� 5 �������� � ��</div>
 <table width='100%' cellpadding='4' cellspacing='0'>
 <tr>
  <td class='tablesubheader' width='1%'>&nbsp;</td>
  <td class='tablesubheader' width='44'>���</td>
  <td class='tablesubheader' width='55%'>�����</td>
 </tr>
 $content
 </table>
 <div class='tablefooter' align='right'>
   <a href='{$this->ipsclass->base_url}section=admin&amp;act=adminlog' style='text-decoration:none'>����� &raquo;</a>
 </div>
</div>

</form>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Index
//===========================================================================
function acp_stats_wrapper($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'><a href='{$this->ipsclass->base_url}&amp;section=help&amp;act=diag'>����� �������</a></div>
 <table width='100%' cellpadding='4' cellspacing='0'>
 <tr>
  <td class='tablerow1' width='40%'><strong>������ IPB</strong></td>
  <td class='tablerow2' width='60%'><span style='color:red'>{$content['ipb_version']} (ID: {$content['ipb_id']})</span></td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>������ SQL</strong></td>
  <td class='tablerow2'><span style='color:red'>{$content['sql_driver']} ({$content['sql_version']})</span></td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>������ PHP</strong></td>
  <td class='tablerow2'><span style='color:red'>{$content['php_version']} ({$content['php_sapi']})</span> ( <a href='{$this->ipsclass->base_url}&phpinfo=1'>PHP INFO</a> )</td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>���������� ������</strong></td>
  <td class='tablerow2'>���: <strong><em>{$content['topics']}</em></strong> <br />���������: <strong><em>{$content['replies']}</em></strong> </td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>�������������</strong></td>
  <td class='tablerow2'>������������������: <strong><em>{$content['members']}</em></strong>
  						<br /><a href='{$this->ipsclass->base_url}&amp;section=content&amp;act=mtools&amp;code=mod'>��������� ���������: <strong><em>{$content['validate']}</em></strong></a>
EOF;

if( $this->ipsclass->vars['ipb_bruteforce_attempts'] > 0 )
{
$IPBHTML .= <<<EOF
  						<br /><a href='{$this->ipsclass->base_url}&amp;section=content&amp;act=mtools&amp;code=lock'>���������������: <strong><em>{$content['locked']}</em></strong></a>
EOF;
}

$IPBHTML .= <<<EOF
  						<!--<br />COPPA Pending: <strong><em>{$content['coppa']}</em></strong>-->
  </td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>������ ���������� ��������</strong></td>
  <td class='tablerow2'><strong><em><div id='uploads-size'><i>�������</i></div></em></strong></td>
 </tr>
 </table>
</div>
</form>

<script type='text/javascript'>

function get_uploads_size()
{
	var content = document.getElementById( 'uploads-size' );

	/*--------------------------------------------*/
	// Main function to do on request
	// Must be defined first!!
	/*--------------------------------------------*/

	do_request_function = function()
	{
		//----------------------------------
		// Ignore unless we're ready to go
		//----------------------------------

		if ( ! xmlobj.readystate_ready_and_ok() )
		{
			// Could do a little loading graphic here?
			return;
		}

		//----------------------------------
		// INIT
		//----------------------------------

		var returned = xmlobj.xmlhandler.responseText;
		content.innerHTML = returned;
	}

	//----------------------------------
	// LOAD XML
	//----------------------------------

	xmlobj = new ajax_request();
	xmlobj.onreadystatechange( do_request_function );

	xmlobj.process( ipb_var_base_url + '&act=xmlout&do=get-dir-size' );

	return false;
}

get_uploads_size();
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function acp_php_version_warning( $phpversion ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
  <div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
  <span style='font-size:20px;font-weight:bold'>��������: ������ PHP �� ������� ($phpversion) �� ������������� �����������.</span>
  <br /><br /><span style='font-size:14px;'>��� ������ Invision Power Board ��������� ��� ������� PHP ������ 4.3.0.<br />��������� ������� ����������� ����� �� �������� ��� �������� �� �����, ���� �� �� �������� PHP �� ����� ����� ������.</span></div>
 </td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function warning_unlocked_installer() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
  <div style='color:red;border:1px solid red;background:#FFE1E2;padding:10px'>
  <span style='font-size:14px;font-weight:bold'>��������: ��������� ����������������� ������ ��������� ������</span>
  <br /><br /><span style='font-size:10px;'>������� ���� <b>install/index.php</b> � �������!
  <br />������� ���� ���� �� �������, �� ������ ��������� ��� ������ ������������ ������.</span></div>
 </td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Index
//===========================================================================
function warning_upgrade() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
  <div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
  <span style='font-size:20px;font-weight:bold'>��������: ���������� ������������� ����������</span>
  <br /><br /><span style='font-size:14px;'>IPB ��������� ����������, ������� �� ���� ���������.  ������� <a href='{$this->ipsclass->vars['board_url']}/upgrade/index.php'>�����</a> ��� ���������� ����� ����������.</span></div>
 </td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function warning_installer() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
   <div style='color:red;border:1px solid red;background:#FFE1E2;padding:10px'>
   <span style='font-size:14px;font-weight:bold'>��������: ������ ��������� ������ ��� ��� �� ������!</span>
   <br /><br /><span style='font-size:10px;'>������ ��������� ������ ������������ (���� ���� install.lock �� ����� ������ � ��������� ������ ����������), �� ������������ �� �������, �� ����������� ������� ��� ��� ��������� ������������ ������ ������!
   <br />��� �������� ������� ��������� ���������� ������� ���� <b>/install/index.php</b>, � ����� �������������� �� ������ �� �������.</span></div>
 </td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function warning_rebuild_emergency() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
   <div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
   <span style='font-size:20px;font-weight:bold'>��������: ������ �����</span>
   <br /><br />
								� ��������� ������������� ��������� �������� �� �������. ��� ����� ��������� ������������� �� ��������� ��������:
   <ul>
								 <li>��� ��������� �������� ��������� �����.</li>
								 <li>��� ���������� �������� ������ � ����������� ������.</li>
								 <li>���������� ��������� ������ ����� � ������� ����� � ���� ������� �, ��������, ������ ��� �������� ���������� ����� ������.</li>
   </ul>
								<b>��� ������ ����� � ����� �������:</b>
   <ul>
								 <li>���� �� �� ������ ������������ ���������� ����� ������, ��������� ����� ������� (CHMOD) � ���������� /skin_cache/, ����� ��������� � ����������� ������ ���� ������ � ���������.</li>
	<li>If the permissions are correct, check your 'System Settings -&gt; General Configuration' settings to check the value of 'Safe Mode Skins' - disable if not required</li>
								 <li>� ���������� �������� ��� ���� ������, ������ �� ������ ����.</li>
   </ul>
   <b>&gt;&gt; <a href='{$this->ipsclass->base_url}&section=lookandfeel&act=sets&code=rebuildalltemplates&removewarning=1'>�������� ��� ���� �������� � ������� ��� ��������������</a> &lt;&lt;</b>
   </div>
 </td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index
//===========================================================================
function warning_rebuild_upgrade() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td colspan='3'>
   <div style='color:red;border:1px solid red;background:#FFC0C3;padding:10px'>
   <span style='font-size:20px;font-weight:bold'>������� ����������, ��� ���� ��������� ���������� ������</span>
   <br /><br />
   ��� ���������� �������� ��� ���� ������ ������ ��� �������� ����, ��� ��� ����� ������ � �������� ��������� ���������.
   <br /><br /><b>&gt;&gt; <a href='{$this->ipsclass->base_url}&section=lookandfeel&act=sets&code=rebuildalltemplates&removewarning=1'>�������� ��� ���� �������� � ������� ��� ��������������</a> &lt;&lt;</b>
   </div>
 </td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Index http://www.invisionboard.com/acp-ipb/getnews.php
//===========================================================================
function acp_main_template($content) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<!--in_dev_notes-->
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr>
 <td width='49%' nowrap='nowrap' valign='middle'>{$content['reg_html']}</td>
 <td width='2%'>&nbsp;</td>
 <td width='49%' nowrap='nowrap' valign='middle'>{$content['latest_news']}</td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
<tr>
 <td width='49%' nowrap='nowrap' valign='middle'>{$content['stats']}</td>
 <td width='2%'>&nbsp;</td>
 <td width='49%' nowrap='nowrap' align='center' valign='top' class='tableborder' style='background:#F5F9FD'><div class='tableheaderalt'>�������</div><br />{$content['ad_notes']}</td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
<!--in_dev_check-->
<!--phpversioncheck-->
<!--boardoffline-->
<!--warninginstaller-->
<!--warningupgrade-->
<!--warningskin-->
<tr>
 <td width='49%' nowrap='nowrap' valign='top'>{$content['quick_clicks']}</td>
 <td width='2%'>&nbsp;</td>
 <td width='49%' nowrap='nowrap' valign='top'>{$content['version_history']}</td>
</tr>
<tr>
 <td colspan='3'>&nbsp;</td>
</tr>
</table>
{$content['acp_online']}
<br />
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr>
 <td width='49%' nowrap='nowrap' valign='top'>
	<!--acpactions-->
 </td>
 <td width='2%'>&nbsp;</td>
 <td width='49%' nowrap='nowrap' valign='top'>
	<!--acplogins-->
</td>
</tr>
</table>
EOF;

if ( IN_DEV )
{
$IPBHTML .= <<<EOF
<br />
<div class='tableborder'>
 <div class='tableheaderalt'>����� ������������: ������� ������</div>
 <div class='tablepad'>
	<a href='{$this->ipsclass->base_url}&amp;section=admin&amp;act=components&amp;code=master_xml_export'>����������</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=tools&amp;act=loginauth&amp;code=master_xml_export'>������ �����������</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=content&amp;act=group&amp;code=master_xml_export'>������</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=content&amp;act=attach&amp;code=master_xml_export'>���� ������</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=lookandfeel&amp;act=sets&amp;code=master_xml_export'>�����</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=tools&amp;act=task&amp;code=master_xml_export'>������</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=tools&amp;act=help&amp;code=master_xml_export'>������</a>
	&middot; <a href='{$this->ipsclass->base_url}&amp;section=content&amp;act=bbcode&amp;code=bbcode_export'>BB-����</a>
 </div>
</div>
EOF;
}

//--endhtml--//
return $IPBHTML;
}

}

?>