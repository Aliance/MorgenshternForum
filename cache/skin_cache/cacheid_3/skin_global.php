<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Tue, 17 Nov 2009 17:56:47 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_global_3 {

 var $ipsclass;
//===========================================================================
// <ips:bbcode_wrap_end:desc::trigger:>
//===========================================================================
function bbcode_wrap_end() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:bbcode_wrap_start:desc::trigger:>
//===========================================================================
function bbcode_wrap_start() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class='<!--css.top-->'><!--title--><!--extra--></div><div class='<!--css.main-->'>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:board_offline:desc::trigger:>
//===========================================================================
function board_offline($message="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
<form action=\"{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}\" method=\"post\">
	<input type=\"hidden\" name=\"act\" value=\"Login\" />
	<input type=\"hidden\" name=\"CODE\" value=\"01\" />
	<input type=\"hidden\" name=\"s\" value=\"{$this->ipsclass->session_id}\" />
	<input type=\"hidden\" name=\"referer\" value=\"\" />
	<input type=\"hidden\" name=\"CookieDate\" value=\"1\" />
	<div class=\"borderwrap\">
		<h3><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['offline_title']}</h3>
		<p>$message</p>
		<div class=\"fieldwrap\">
			<h4>{$this->ipsclass->lang['erl_enter_name']}</h4>
			<input type=\"text\" size=\"20\" maxlength=\"64\" name=\"UserName\" />
			<h4>{$this->ipsclass->lang['erl_enter_pass']}</h4>
			<input type=\"password\" size=\"20\" name=\"PassWord\" />
		</div>
		<p class=\"formbuttonrow\"><input class=\"button\" type=\"submit\" name=\"submit\" value=\"{$this->ipsclass->lang['erl_log_in_submit']}\" /></p>
	</div>
</form>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:css_external:desc::trigger:>
//===========================================================================
function css_external($css="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<style type=\"text/css\" media=\"all\">
" . (($this->ipsclass->vars['board_url'] != '' AND $this->ipsclass->vars['board_url'] != '.') ? ("
@import url({$this->ipsclass->vars['board_url']}/style_images/css_{$css}.css);
") : ("
@import url(style_images/css_{$css}.css);
")) . "
</style>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:css_inline:desc::trigger:>
//===========================================================================
function css_inline($css="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<style type=\"text/css\">
	$css
</style>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:end_nav:desc::trigger:>
//===========================================================================
function end_nav() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:Error:desc::trigger:>
//===========================================================================
function Error($message="",$ad_email_one="",$ad_email_two="",$show_top_msg=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<script language=\"JavaScript\" type=\"text/javascript\">
<!--
function contact_admin() {
  // Very basic spam bot stopper
	  
  admin_email_one = '$ad_email_one';
  admin_email_two = '$ad_email_two';
  
  window.location = 'mailto:'+admin_email_one+'@'+admin_email_two+'?subject={$this->ipsclass->lang['mailto_erroronforums']}';
  
}
//-->
</script>
<br />
<div class=\"borderwrap\">
	<h3><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['error_title']}</h3>
" . (($show_top_msg == 1) ? ("
<p>{$this->ipsclass->lang['exp_text']}</p>
	<div class=\"errorwrap\">
		<h4>{$this->ipsclass->lang['msg_head']}</h4>
") : ("")) . "
		<p>$message</p>
" . (($show_top_msg == 1) ? ("
</div>
") : ("")) . "
	<!--IBF.LOG_IN_TABLE-->
	<!--IBF.POST_TEXTAREA-->
	<h4>{$this->ipsclass->lang['er_links']}</h4>
	<ul>
		<li><a href=\"{$this->ipsclass->base_url}act=Reg&amp;CODE=10\">{$this->ipsclass->lang['er_lost_pass']}</a></li>
		<li><a href=\"{$this->ipsclass->base_url}act=Reg&amp;CODE=00\">{$this->ipsclass->lang['er_register']}</a></li>
		<li><a href=\"{$this->ipsclass->base_url}act=Help&amp;CODE=00\">{$this->ipsclass->lang['er_help_files']}</a></li>
		<li><a href=\"javascript:contact_admin();\">{$this->ipsclass->lang['er_contact_admin']}</a></li>
	</ul>
	<p class=\"formbuttonrow\"><b><a href=\"javascript:history.go(-1)\">{$this->ipsclass->lang['error_back']}</a></b></p>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:error_log_in:desc::trigger:>
//===========================================================================
function error_log_in($q_string="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<form action=\"{$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}\" method=\"post\">
	<input type=\"hidden\" name=\"act\" value=\"Login\" />
	<input type=\"hidden\" name=\"CODE\" value=\"01\" />
	<input type=\"hidden\" name=\"s\" value=\"{$this->ipsclass->session_id}\" />
	<input type=\"hidden\" name=\"referer\" value=\"$q_string\" />
	<input type=\"hidden\" name=\"CookieDate\" value=\"1\" />
	<h4>{$this->ipsclass->lang['er_log_in_title']}</h4>
	<div class=\"fieldwrap\">
" . (($this->ipsclass->vars['ipbli_usertype'] == 'username') ? ("
<h4>{$this->ipsclass->lang['erl_enter_name']}</h4>
		<input type=\"text\" size=\"20\" maxlength=\"64\" name=\"UserName\" />
") : ("
<h4>{$this->ipsclass->lang['erl_email']}</h4>
		<input type=\"text\" size=\"20\" maxlength=\"128\" name=\"UserName\" />
")) . "
		<h4>{$this->ipsclass->lang['erl_enter_pass']}</h4>
		<input type=\"password\" size=\"20\" name=\"PassWord\" />
		<p class=\"formbuttonrow1\"><input class=\"button\" type=\"submit\" name=\"submit\" value=\"{$this->ipsclass->lang['erl_log_in_submit']}\" /></p>
	</div>
</form>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:error_post_textarea:desc::trigger:>
//===========================================================================
function error_post_textarea($post="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<h4>{$this->ipsclass->lang['err_title']}</h4>
<p>{$this->ipsclass->lang['err_expl']}</p>
<div class=\"fieldwrap\">
	<h4>{$this->ipsclass->lang['err_title']}</h4>
	<form name=\"mehform\">
		<textarea cols=\"70\" rows=\"5\" name=\"saved\" tabindex=\"2\">$post</textarea>
	</form>
	<p class=\"formbuttonrow1\"><input class=\"button\" type=\"button\" tabindex=\"1\" value=\"{$this->ipsclass->lang['err_select']}\" onclick=\"document.mehform.saved.select()\" /></p>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forum_show_rules_full:desc::trigger:>
//===========================================================================
function forum_show_rules_full($rules="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- Show FAQ/Forum Rules -->
<div class=\"borderwrap\" style='margin-bottom:6px;'>
	<h3><{CAT_IMG}>&nbsp;{$rules['title']}</h3>
	<p>{$rules['body']}</p>
</div>
<!-- End FAQ/Forum Rules -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forum_show_rules_link:desc::trigger:>
//===========================================================================
function forum_show_rules_link($rules="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- Show FAQ/Forum Rules -->
<div class=\"ruleswrap\">
	&nbsp;<{F_RULES}>&nbsp;<b><a href=\"{$this->ipsclass->base_url}act=SR&amp;f={$rules['fid']}\">{$rules['title']}</a></b>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:get_rte_css:desc::trigger:>
//===========================================================================
function get_rte_css() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<style type='text/css'>
@import url( \"{$this->ipsclass->vars['board_url']}/style_images/<#IMG_DIR#>/folder_editor_images/css_rte.css\" );
</style>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_board_footer:desc::trigger:>
//===========================================================================
function global_board_footer($time="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<table cellspacing=\"0\" id=\"gfooter\">
	<tr>
		<td width=\"45%\"><% SYNDICATION %><% SKINCHOOSER %> <% LANGCHOOSER %></td>
		<td width=\"10%\" align=\"center\" nowrap=\"nowrap\"><a href=\"lofiversion/index.php<% LOFIVERSION %>\"><b>{$this->ipsclass->lang['global_lofi']}</b></a></td>
		<td width=\"45%\" align=\"right\" nowrap=\"nowrap\"><% QUICKSTATS %>{$this->ipsclass->lang['global_timeisnow']}: {$time}</td>
	</tr>
</table>
<script type='text/javascript'>
//<![CDATA[
menu_do_global_init();
show_inline_messages();
// Uncomment this to fix IE png images
// causes page slowdown, and some missing images occasionally
// if ( is_ie )
// {
//	 ie_fix_png();
// }
" . (($this->ipsclass->member['members_auto_dst'] == 1 AND $this->ipsclass->vars['time_dst_auto_correction'] AND $this->ipsclass->input['_low_act'] == 'idx') ? ("
global_dst_check(parseInt(\"{$this->ipsclass->member['time_offset']}\"),parseInt(\"{$this->ipsclass->member['dst_in_use']}\") );
") : ("")) . "
//]]>
</script>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_board_header:desc::trigger:>
//===========================================================================
function global_board_header($component_links="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!--ipb.javascript.start-->
<script type=\"text/javascript\">
 //<![CDATA[
 var ipb_var_st            = \"{$this->ipsclass->input['st']}\";
 var ipb_lang_tpl_q1       = \"{$this->ipsclass->lang['tpl_q1']}\";
 var ipb_var_s             = \"{$this->ipsclass->session_id}\";
 var ipb_var_phpext        = \"{$this->ipsclass->vars['php_ext']}\";
 var ipb_var_base_url      = \"{$this->ipsclass->js_base_url}\";
 var ipb_var_image_url     = \"{$this->ipsclass->vars['img_url']}\";
 var ipb_input_f           = \"{$this->ipsclass->input['f']}\";
 var ipb_input_t           = \"{$this->ipsclass->input['t']}\";
 var ipb_input_p           = \"{$this->ipsclass->input['p']}\";
 var ipb_var_cookieid      = \"{$this->ipsclass->vars['cookie_id']}\";
 var ipb_var_cookie_domain = \"{$this->ipsclass->vars['cookie_domain']}\";
 var ipb_var_cookie_path   = \"{$this->ipsclass->vars['cookie_path']}\";
 var ipb_md5_check         = \"{$this->ipsclass->md5_check}\";
 var ipb_new_msgs          = {$this->ipsclass->member['new_msg']};
 var use_enhanced_js       = {$this->ipsclass->can_use_fancy_js};
 var use_charset           = \"{$this->ipsclass->vars['gb_char_set']}\";
 var ipb_myass_chars_lang  = \"{$this->ipsclass->lang['myass_chars']}\";
 var ajax_load_msg		   = \"{$this->ipsclass->lang['ajax_loading_msg_new']}\";
 //]]>
</script>
<script type=\"text/javascript\" src='jscripts/ips_ipsclass.js'></script>
<script type=\"text/javascript\" src='jscripts/ipb_global.js'></script>
<script type=\"text/javascript\" src='jscripts/ips_menu.js'></script>
<script type=\"text/javascript\" src='{$this->ipsclass->vars['img_url']}/folder_js_skin/ips_menu_html.js'></script>
<script type=\"text/javascript\" src='cache/lang_cache/{$this->ipsclass->lang_id}/lang_javascript.js'></script>
<script type=\"text/javascript\">
//<![CDATA[
var ipsclass = new ipsclass();
ipsclass.init();
ipsclass.settings['do_linked_resize'] = parseInt( \"{$this->ipsclass->vars['resize_linked_img']}\" );
ipsclass.settings['resize_percent']   = parseInt( \"{$this->ipsclass->vars['resize_img_percent']}\" );
//]]>
</script>
<!--ipb.javascript.end-->
<div class=\"borderwrap\">
	<div id=\"logostrip\"><a href='{$this->ipsclass->base_url}'><!--ipb.logo.start--><img src='http://forum.morgenshtern.com/style_images/logo_blue.jpg' alt='Morgenshtern' title='' style='vertical-align:top' border='0' /><!--ipb.logo.end--></a></div>
	<div id=\"submenu\">
		<!--ipb.leftlinks.start-->
		" . (($this->ipsclass->vars['home_url']) ? ("
			<div class='ipb-top-left-link'><a href=\"{$this->ipsclass->vars['home_url']}\" target=\"_blank\">{$this->ipsclass->vars['home_name']}</a></div>
		") : ("")) . "
		<!--" . (($this->ipsclass->vars['csite_on']) ? ("
			<div class='ipb-top-left-link'><a href=\"{$this->ipsclass->base_url}act=home\">{$this->ipsclass->vars['csite_title']}</a></div>
		") : ("")) . "-->
		" . (($this->ipsclass->member['id']) ? ("
			<div class='ipb-top-left-link'><a href=\"http://www.morgenshtern.com/dungeon-items/index/id/{$this->ipsclass->member['id']}\">{$this->ipsclass->lang['dungeon_items']}</a></div>
		") : ("
			<div class='ipb-top-left-link'><a href=\"http://www.morgenshtern.com/dungeon-items/\">{$this->ipsclass->lang['dungeon_items']}</a></div>
		")) . "
		" . ((($this->ipsclass->member['mgroup'] >= 4 AND $this->ipsclass->member['mgroup'] <= 8 AND $this->ipsclass->member['mgroup'] != 5) OR ($this->ipsclass->member['mgroup'] >= 11 AND $this->ipsclass->member['mgroup'] <= 18 AND $this->ipsclass->member['mgroup'] != 14)) ? ("
			<div class='ipb-top-left-link'><a href=\"http://www.morgenshtern.com/staff/allies/\" style='color: #f00;'>���������� ���������</a></div>
		") : ("")) . "
		<!--IBF.RULES-->
		<!--ipb.leftlinks.end-->
		<!--ipb.rightlinks.start-->
		<div class='ipb-top-right-link'><a href=\"{$this->ipsclass->base_url}act=Help\">{$this->ipsclass->lang['tb_help']}</a></div>
		<div class='ipb-top-right-link' id=\"ipb-tl-search\"><a href=\"{$this->ipsclass->base_url}act=Search&amp;f={$this->ipsclass->input['f']}\">{$this->ipsclass->lang['tb_search']}</a></div>
		<div class='ipb-top-right-link'><a href=\"{$this->ipsclass->base_url}act=Members\">{$this->ipsclass->lang['tb_mlist']}</a></div>
		<div class='ipb-top-right-link'><a href=\"{$this->ipsclass->base_url}act=calendar\">{$this->ipsclass->lang['tb_calendar']}</a></div>
		" . (($component_links != "") ? ("
			{$component_links}
		") : ("")) . "
		<div class='popupmenu-new' id='ipb-tl-search_menu' style='display:none;width:210px'>
			<form action=\"{$this->ipsclass->base_url}act=Search&amp;CODE=01\" method=\"post\">
				<input type='hidden' name='forums' id='gbl-search-forums' value='all' /> 
				<input type=\"text\" size=\"20\" name=\"keywords\" id='ipb-tl-search-box' />
				<input class=\"button\" type=\"image\" style='border:0px' src=\"{$this->ipsclass->vars['img_url']}/login-button.gif\" />
				" . ((($this->ipsclass->input['act'] == 'sf' OR $this->ipsclass->input['act'] == 'st') AND $this->ipsclass->input['f']) ? ("
					<br /><input type='checkbox' id='gbl-search-checkbox' value='1' onclick='gbl_check_search_box()' checked='checked' /> {$this->ipsclass->lang['gbl_forum_search']}
				") : ("")) . "
			</form>
			<div style='padding:4px'>
				<a href='{$this->ipsclass->base_url}act=Search'>{$this->ipsclass->lang['gbl_more_search']}</a>
			</div>
		</div>
		<script type=\"text/javascript\">
			ipsmenu.register( \"ipb-tl-search\", 'document.getElementById(\"ipb-tl-search-box\").focus();' );
			gbl_check_search_box();
		</script>
		<!--ipb.rightlinks.end-->
	</div>
</div>
" . (($this->ipsclass->can_use_fancy_js != 0) ? ("
<script type=\"text/javascript\" src='jscripts/ips_xmlhttprequest.js'></script>
<script type=\"text/javascript\" src='jscripts/ipb_global_xmlenhanced.js'></script>
<script type=\"text/javascript\" src='jscripts/dom-drag.js'></script>
<div id='get-myassistant' style='display:none;width:606px;text-align:left;'>
<div class=\"borderwrap\">
 <div class='maintitle' id='myass-drag' title='{$this->ipsclass->lang['myass_drag']}'>
  <div style='float:right'><a href='#' onclick='document.getElementById(\"get-myassistant\").style.display=\"none\"'>[X]</a></div>
  <div>{$this->ipsclass->lang['myass_title']}</div>
 </div>
 <div id='myass-content' style='overflow-x:auto;'></div>
 </div>
</div>
<!-- Loading Layer -->
<div id='loading-layer' style='display:none'>
	<div id='loading-layer-shadow'>
	   <div id='loading-layer-inner'>
	 	<img src='style_images/<#IMG_DIR#>/loading_anim.gif' border='0' alt='{$this->ipsclass->lang['ajax_loading_msg']}' />
		<span style='font-weight:bold' id='loading-layer-text'>{$this->ipsclass->lang['ajax_loading_msg']}</span>
	    </div>
	</div>
</div>
<!-- / Loading Layer -->
<!-- Msg Layer -->
<div id='ipd-msg-wrapper'>
	<div id='ipd-msg-title'>
		<a href='#' onclick='document.getElementById(\"ipd-msg-wrapper\").style.display=\"none\"; return false;'><img src='style_images/<#IMG_DIR#>/close.png' alt='X' title='Close Window' class='ipd' /></a> &nbsp; <strong>{$this->ipsclass->lang['gbl_sitemsg_header']}</strong>
	</div>
	<div id='ipd-msg-inner'><span style='font-weight:bold' id='ipd-msg-text'></span><div class='pp-tiny-text'>{$this->ipsclass->lang['gbl_auto_close']}</div></div>
</div>
<!-- Msg Layer -->
") : ("")) . "
<!-- / End board header -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_board_header_component_link:desc::trigger:>
//===========================================================================
function global_board_header_component_link($url="",$title="",$component_db_row=array()) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class='ipb-top-right-link'><a href=\"{$url}\">{$title}</a></div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_footer_synd_link:desc::trigger:>
//===========================================================================
function global_footer_synd_link($data="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "\"<a href='{$data['url']}' style='color:black'>{$data['title']}</a>\",";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_footer_synd_wrapper:desc::trigger:>
//===========================================================================
function global_footer_synd_wrapper($content="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<img id=\"rsssyndication\" src='{$this->ipsclass->vars['img_url']}/rss.png' border='0' alt='-' class='ipd' />
<script type=\"text/javascript\">
//<![CDATA[
  menu_build_menu(
  \"rsssyndication\",
  new Array( $content
           ) );
//]]>
</script>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_lang_chooser:desc::trigger:>
//===========================================================================
function global_lang_chooser($data="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<form action=\"{$this->ipsclass->base_url}setlanguage=1\" name=\"langselectorbox\" method=\"post\">
	<input type=\"hidden\" name=\"langurlbits\" value=\"{$this->ipsclass->query_string_safe}&amp;cal_id={$this->ipsclass->input['cal_id']}\" />
	<select name=\"langid\" onchange=\"chooselang(this)\">
		<optgroup label=\"{$this->ipsclass->lang['global_language']}\">
			$data
		</optgroup>
	</select>
</form>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_quick_stats:desc::trigger:>
//===========================================================================
function global_quick_stats($time="",$gzip="",$load="",$sql="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<img src='{$this->ipsclass->vars['img_url']}/stat_time.gif' border='0' style='vertical-align:middle' alt='' /> {$time} {$this->ipsclass->lang['stats_sec']}
&nbsp;&nbsp;<img src='{$this->ipsclass->vars['img_url']}/stat_load.gif' border='0' style='vertical-align:middle' alt='' /> $load
&nbsp;&nbsp;<img src='{$this->ipsclass->vars['img_url']}/stat_sql.gif' border='0' style='vertical-align:middle' alt='' /> $sql <a href='{$this->ipsclass->base_url}{$this->ipsclass->query_string_safe}&amp;debug=1'>{$this->ipsclass->lang['stats_queries']}</a>
&nbsp;&nbsp;<img src='{$this->ipsclass->vars['img_url']}/stat_gzip.gif' border='0' style='vertical-align:middle' alt='' /> $gzip
<br />";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_rss_link:desc::trigger:>
//===========================================================================
function global_rss_link($data="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"{$data['title']}\" href=\"{$data['url']}\" />";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:global_skin_chooser:desc::trigger:>
//===========================================================================
function global_skin_chooser($data="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<form action=\"{$this->ipsclass->base_url}setskin=1\" name=\"skinselectorbox\" method=\"post\">
	<input type=\"hidden\" name=\"skinurlbits\" value=\"{$this->ipsclass->query_string_safe}&amp;cal_id={$this->ipsclass->input['cal_id']}\" />
	<select name=\"skinid\" onchange=\"chooseskin(this)\">
		<optgroup label=\"{$this->ipsclass->lang['global_skinselector']}\">
			$data
		</optgroup>
	</select>
</form>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:member_bar:desc::trigger:>
//===========================================================================
function member_bar($msg="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "" . (($this->ipsclass->member['id']) ? ("
<div id=\"userlinks\">
	<p class=\"home\"><b>{$this->ipsclass->lang['logged_in_as']} <a href=\"{$this->ipsclass->base_url}showuser={$this->ipsclass->member['id']}\">{$this->ipsclass->member['members_display_name']}</a></b> ( <a href=\"{$this->ipsclass->base_url}act=Login&amp;CODE=03&amp;k={$this->ipsclass->md5_check}\">{$this->ipsclass->lang['log_out']}</a> )</p>
	<p>
") : ("
<div id=\"userlinksguest\">
	<p class=\"pcen\"><b>{$this->ipsclass->lang['guest_stuff']}</b> ( <a href=\"{$this->ipsclass->base_url}act=Login&amp;CODE=00\">{$this->ipsclass->lang['log_in']}</a> | <a href=\"{$this->ipsclass->base_url}act=Reg&amp;CODE=00\">{$this->ipsclass->lang['register']}</a> )
")) . "
" . (($this->ipsclass->member['mgroup'] == $this->ipsclass->vars['auth_group']) ? ("
	<b><a href=\"{$this->ipsclass->base_url}act=reg&amp;CODE=reval\">{$this->ipsclass->lang['resend_val']}</a></b> &middot;
") : ("")) . "
" . (($this->ipsclass->member['g_access_cp'] == 1 AND $this->ipsclass->vars['security_remove_acp_link'] == 0) ? ("
	<b><a href=\"{$this->ipsclass->vars['_admin_link']}\" target=\"_blank\">{$this->ipsclass->lang['admin_cp']}</a></b> &middot;
") : ("")) . "
<!--
" . (($this->ipsclass->member['has_blog'] == 1) ? ("
	<b><a href=\"{$this->ipsclass->base_url}automodule=blog&amp;req=showblog&amp;mid={$this->ipsclass->member['id']}\">{$this->ipsclass->lang['myblog']}</a></b> &middot;
") : ("")) . "
" . (($this->ipsclass->member['has_gallery'] == 1) ? ("
	<b><a href=\"{$this->ipsclass->base_url}automodule=gallery&amp;req=user&amp;user={$this->ipsclass->member['id']}\">{$this->ipsclass->lang['submenu_albums']}</a></b> &middot;
") : ("")) . "
-->
" . (($this->ipsclass->member['id']) ? ("
	<b><a href=\"{$this->ipsclass->base_url}act=UserCP&amp;CODE=00\" title=\"{$this->ipsclass->lang['cp_tool_tip']}\">{$this->ipsclass->lang['your_cp']}</a></b> &middot;&nbsp;<a href=\"{$this->ipsclass->base_url}act=Search&amp;CODE=getnew\">{$this->ipsclass->lang['view_new_posts']}</a>
	" . (($this->ipsclass->member['g_view_board']) ? ("
		&middot;&nbsp;<a href=\"javascript:buddy_pop();\" title=\"{$this->ipsclass->lang['bb_tool_tip']}\">{$this->ipsclass->lang['l_qb']}</a>
	") : ("")) . "
	&middot;&nbsp;<a href=\"javascript:friends_pop();\">{$this->ipsclass->lang['gbl_my_friends']}</a>	
") : ("")) . "
" . (($this->ipsclass->member['g_use_pm'] AND $this->ipsclass->member['members_disable_pm'] == 0) ? ("
	&middot;&nbsp;<a href=\"{$this->ipsclass->base_url}act=Msg&amp;CODE=01\">{$msg['TEXT']}</a>
") : ("")) . "
	</p>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:member_bar_disabled:desc::trigger:>
//===========================================================================
function member_bar_disabled() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div id=\"userlinksguest\">
	<p class=\"pcen\">{$this->ipsclass->lang['mb_disabled']}</p>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:msg_get_new_pm_notification:desc::trigger:>
//===========================================================================
function msg_get_new_pm_notification($msg="",$xml=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div>
" . (($xml != 1) ? ("
<div class='borderwrap' id='pmpop-nojs'>
 <div class='maintitle'>
  <div style='float:right;'><a href='{$this->ipsclass->base_url}act=Msg&amp;CODE=03&amp;MSID={$msg['mt_id']}&amp;VID=in'>{$this->ipsclass->lang['global_pm_read']} &raquo;</a></div>
  <div>{$this->ipsclass->lang['global_pm_title']}</div>
 </div>
") : ("")) . "
  <div class='pp-contentbox-back' id='pmpop-ajax'>
	<div class='pp-contentbox-entry-noheight'>
   	<div class='borderwrap'>
   	 <table width='100%' style='padding:0px;'>
   	  <tr>
   	   <td class='row2' style='padding:4px;'>
   	   " . (($xml == 1) ? ("
   	   <div style='float:right;'><a href='#' onclick='xml_myassistant_init(\"newpms\")'>{$msg['_cur_num']}/{$msg['_msg_total']}</a></div>
   	   ") : ("")) . "
   	   <strong><a href='{$this->ipsclass->base_url}act=Msg&amp;CODE=03&amp;MSID={$msg['mt_id']}&amp;VID=in'>{$msg['mt_title']}</a></strong> ( {$this->ipsclass->lang['global_pm_from']}: {$msg['members_display_name']} )
   	   </td>
	  </tr>
	 </table>
	<div class='post1' style='padding:4px;'>{$msg['msg_post']}</div>
	<div align='center' class='row2' style='padding:4px;'>
	  {$msg['members_display_name']} {$this->ipsclass->lang['pmp_part1']} {$msg['g_title']} {$this->ipsclass->lang['pmp_part2']} {$msg['posts']} {$this->ipsclass->lang['pmp_part3']}
	  <br />
	  {$this->ipsclass->lang['global_pm_sent']}: {$msg['msg_date']}
	</div>
  </div>
 </div>
</div>
" . (($xml != 1) ? ("
 </div>
") : ("")) . "
<script type='text/javascript'>
//<![CDATA[
 if ( use_enhanced_js )
 {
 	document.getElementById('pmpop-nojs').style.display = 'none';
 	xml_myassistant_init(\"newpms\");
 }
 
//]]>
</script>
<!-- END PM -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_compile:desc::trigger:>
//===========================================================================
function pagination_compile($start="",$previous_link="",$start_dots="",$pages="",$end_dots="",$next_link="",$total_pages="",$per_page="",$base_link="",$no_dropdown=0,$st='st') {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "{$start}{$start_dots}{$previous_link}{$pages}{$next_link}{$end_dots}
" . ((! $no_dropdown) ? ("
	<script type=\"text/javascript\">
	//<![CDATA[
	ipb_pages_shown++;
	var pgjmp = document.getElementById( 'page-jump' );
	pgjmp.id  = 'page-jump-'+ipb_pages_shown;
	ipb_pages_array[ ipb_pages_shown ] = new Array( '{$base_link}', $per_page, $total_pages );
	menu_build_menu(
		pgjmp.id,
		new Array(  \"~~NODIV~~<div onmouseover='pages_st_focus(\"+ipb_pages_shown+\")' class='popupmenu-category' align='center'>{$this->ipsclass->lang['global_page_jump']}</div>\",
					\"<input type='hidden' id='st-type-\"+ipb_pages_shown+\"' value='{$st}' /><input type='text' size='5' name='st' id='st-\"+ipb_pages_shown+\"' onkeydown='check_enter(\"+ipb_pages_shown+\", event);' /> <input type='button' class='button' onclick='do_multi_page_jump(\"+ipb_pages_shown+\");' value='{$this->ipsclass->lang['jmp_go']}' />\" ) );
	//]]>
	</script>
") : ("")) . "";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_current_page:desc::trigger:>
//===========================================================================
function pagination_current_page($page="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "&nbsp;<span class=\"pagecurrent\">{$page}</span>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_end_dots:desc::trigger:>
//===========================================================================
function pagination_end_dots($url="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "&nbsp;<span class=\"pagelinklast\"><a href=\"$url\" title=\"{$this->ipsclass->lang['tpl_gotolast']}\">&raquo;</a></span>&nbsp;";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_make_jump:desc::trigger:>
//===========================================================================
function pagination_make_jump($pages=1,$no_dropdown=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "" . (($no_dropdown) ? ("
	<span class=\"pagelink\" id='page-jump'>$pages {$this->ipsclass->lang['tpl_pages']}</span>&nbsp;
") : ("
	<span class=\"pagelink\" id='page-jump'>$pages {$this->ipsclass->lang['tpl_pages']} <img src='{$this->ipsclass->vars['img_url']}/menu_action_down.gif' alt='V' title='{$this->ipsclass->lang['global_open_menu']}' border='0' /></span>&nbsp;
")) . "";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_next_link:desc::trigger:>
//===========================================================================
function pagination_next_link($url="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "&nbsp;<span class=\"pagelink\"><a href=\"$url\" title=\"{$this->ipsclass->lang['tpl_next']}\">&gt;</a></span>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_page_link:desc::trigger:>
//===========================================================================
function pagination_page_link($url="",$page="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "&nbsp;<span class=\"pagelink\"><a href=\"$url\" title=\"$page\">$page</a></span>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_previous_link:desc::trigger:>
//===========================================================================
function pagination_previous_link($url="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<span class=\"pagelink\"><a href=\"$url\" title=\"{$this->ipsclass->lang['tpl_prev']}\">&lt;</a></span>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_start_dots:desc::trigger:>
//===========================================================================
function pagination_start_dots($url="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<span class=\"pagelinklast\"><a href=\"$url\" title=\"{$this->ipsclass->lang['tpl_gotofirst']}\">&laquo;</a></span>&nbsp;";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pop_up_window:desc::trigger:>
//===========================================================================
function pop_up_window($title="",$css="",$text="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> 
<html xml:lang=\"en\" lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\"> 
	<head> 
		<meta http-equiv=\"content-type\" content=\"text/html; charset=<% CHARSET %>\" /> 
		<title>$title</title>
		$css
		<script type=\"text/javascript\">
		 //<![CDATA[
		 var ipb_var_st            = \"{$this->ipsclass->input['st']}\";
		 var ipb_lang_tpl_q1       = \"{$this->ipsclass->lang['tpl_q1']}\";
		 var ipb_var_s             = \"{$this->ipsclass->session_id}\";
		 var ipb_var_phpext        = \"{$this->ipsclass->vars['php_ext']}\";
		 var ipb_var_base_url      = \"{$this->ipsclass->js_base_url}\";
		 var ipb_var_image_url     = \"{$this->ipsclass->vars['img_url']}\";
		 var ipb_input_f           = \"{$this->ipsclass->input['f']}\";
		 var ipb_input_t           = \"{$this->ipsclass->input['t']}\";
		 var ipb_input_p           = \"{$this->ipsclass->input['p']}\";
		 var ipb_var_cookieid      = \"{$this->ipsclass->vars['cookie_id']}\";
		 var ipb_var_cookie_domain = \"{$this->ipsclass->vars['cookie_domain']}\";
		 var ipb_var_cookie_path   = \"{$this->ipsclass->vars['cookie_path']}\";
		 var ipb_md5_check         = \"{$this->ipsclass->md5_check}\";
		 var ipb_new_msgs          = parseInt(\"{$this->ipsclass->member['new_msg']}\");
		 var use_enhanced_js       = {$this->ipsclass->can_use_fancy_js};
		 var use_charset           = \"{$this->ipsclass->vars['gb_char_set']}\";
		 var ipb_myass_chars_lang  = \"{$this->ipsclass->lang['myass_chars']}\";
		 //]]>
		</script>
		<script type=\"text/javascript\" src='jscripts/ips_ipsclass.js'></script>
		<script type=\"text/javascript\" src='jscripts/ipb_global.js'></script>
		<script type=\"text/javascript\" src='jscripts/ips_menu.js'></script>
		<script type=\"text/javascript\" src='{$this->ipsclass->vars['img_url']}/folder_js_skin/ips_menu_html.js'></script>
		<script type=\"text/javascript\" src='cache/lang_cache/{$this->ipsclass->lang_id}/lang_javascript.js'></script>
		<script type=\"text/javascript\">
		//<![CDATA[
		var ipsclass = new ipsclass();
		ipsclass.init();
		//]]>
		</script>
		" . (($this->ipsclass->can_use_fancy_js != 0) ? ("
		<script type=\"text/javascript\" src='jscripts/ips_xmlhttprequest.js'></script>
		<script type=\"text/javascript\" src='jscripts/ipb_global_xmlenhanced.js'></script>
		<script type=\"text/javascript\" src='jscripts/dom-drag.js'></script>
	</head>
	<body<{__body_extra__}>>
	<div id='get-myassistant' style='display:none;width:606px;text-align:left;'>
	<div class=\"borderwrap\">
	 <div class='maintitle' id='myass-drag' title='{$this->ipsclass->lang['myass_drag']}'>
	  <div style='float:right'><a href='#' onclick='document.getElementById(\"get-myassistant\").style.display=\"none\"'>[X]</a></div>
	  <div>{$this->ipsclass->lang['myass_title']}</div>
	 </div>
	 <div id='myass-content' style='overflow-x:auto;'></div>
	 </div>
	</div>
	<!-- Loading Layer -->
	<div id='loading-layer' style='display:none'>
		<div id='loading-layer-shadow'>
		   <div id='loading-layer-inner'>
		 	<img src='style_images/<#IMG_DIR#>/loading_anim.gif' border='0' />
			<span style='font-weight:bold' id='loading-layer-text'>{$this->ipsclass->lang['ajax_loading_msg']}</span>
		    </div>
		</div>
	</div>
	<!-- / Loading Layer -->
	<!-- Msg Layer -->
	<div id='ipd-msg-wrapper'>
		<div id='ipd-msg-title'>
			<a href='#' onclick='document.getElementById(\"ipd-msg-wrapper\").style.display=\"none\"; return false;'><img src='style_images/<#IMG_DIR#>/close.png' alt='X' title='Close Window' class='ipd'></a> &nbsp; <strong>{$this->ipsclass->lang['gbl_sitemsg_header']}</strong>
		</div>
		<div id='ipd-msg-inner'><span style='font-weight:bold' id='ipd-msg-text'></span><div class='pp-tiny-text'>{$this->ipsclass->lang['gbl_auto_close']}</div></div>
	</div>
	<!-- Msg Layer -->
	") : ("")) . "
		<div style='text-align:left'> 
			$text
		</div>
		<script type='text/javascript'>
		//<![CDATA[
		menu_do_global_init();
		show_inline_messages();
		if ( is_ie )
		{
			ie_fix_png();
		}
		//]]>
		</script>
	</body>
</html>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:Redirect:desc::trigger:>
//===========================================================================
function Redirect($Text="",$Url="",$css="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> 
<html xml:lang=\"en\" lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\"> 
	<head>
	    <meta http-equiv=\"content-type\" content=\"text/html; charset=<% CHARSET %>\" /> 
		<title>{$this->ipsclass->lang['stand_by']}</title>
		<meta http-equiv=\"refresh\" content=\"2; url=$Url\" />
		$css
	<script type='text/javascript'>
	//<![CDATA[
	// Fix Mozilla bug: 209020
	if ( navigator.product == 'Gecko' )
	{
		navstring = navigator.userAgent.toLowerCase();
		geckonum  = navstring.replace( /.*gecko\/(\d+)/, \"$1\" );
		
		setTimeout(\"moz_redirect()\",1500);
	}
	
	function moz_redirect()
	{
		var url_bit     = \"{$Url}\";
		window.location = url_bit.replace( new RegExp( \"&amp;\", \"g\" ) , '&' );
	}
	//>
	</script>
	</head>
	<body>
		<div id=\"redirectwrap\">
			<h4>{$this->ipsclass->lang['thanks']}</h4>
			<p>$Text<br /><br />{$this->ipsclass->lang['transfer_you']}</p>
			<p class=\"redirectfoot\">(<a href=\"$Url\">{$this->ipsclass->lang['dont_wait']}</a>)</p>
		</div>
	</body>
</html>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:rules_link:desc::trigger:>
//===========================================================================
function rules_link($url="",$title="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "" . (($url AND $title) ? ("
	<div class='ipb-top-left-link'><a href=\"$url\">$title</a></div>
") : ("")) . "";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:signature_separator:desc::trigger:>
//===========================================================================
function signature_separator($sig="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br /><br />--------------------<br />
<div class=\"signature\">$sig</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:start_nav:desc::trigger:>
//===========================================================================
function start_nav() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!--
<div id='adv'><a href='http://www.morgenshtern.com/encyclopedia'>������������</a> ��������� ����������� ����� ������ � � ��� �� �����.</div>
-->

<div id=\"navstrip\"><{F_NAV}>&nbsp;";
//--endhtml--//
return $IPBHTML;
}



}

/*--------------------------------------------------*/
/*<changed bits>
global_board_header,member_bar,start_nav
</changed bits>*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>