<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Thu, 24 May 2007 13:16:02 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_forum_3 {

 var $ipsclass;
//===========================================================================
// <ips:announcement_row:desc::trigger:>
//===========================================================================
function announcement_row($data="",$inforum=1) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
	<td class=\"row1\"><{B_PIN}></td>
	<td class=\"row1\">&nbsp;</td>
	<td class=\"row1\"><b>{$this->ipsclass->lang['announce_row']}: <a href=\"{$this->ipsclass->base_url}act=announce&amp;f={$data['forum_id']}&amp;id={$data['announce_id']}\">{$data['announce_title']}</a></b></td>
	<td class=\"row1\" align=\"center\">-</td>
	<td class=\"row1\" align=\"center\"><a href=\"{$this->ipsclass->base_url}showuser={$data['member_id']}\">{$data['member_name']}</a></td>
	<td class=\"row1\" align=\"center\">{$data['announce_views']}</td>
	<td class=\"row1\"><span class=\"desc\">{$data['announce_start']}
	<br /><a href=\"{$this->ipsclass->base_url}act=announce&amp;f={$data['forum_id']}&amp;id={$data['announce_id']}\">{$this->ipsclass->lang['last_post_by']}</a> <b><a href=\"{$this->ipsclass->base_url}showuser={$data['member_id']}\">{$data['member_name']}</a></b></span>
	</td>
" . (($this->ipsclass->member['is_mod'] and $inforum == 1) ? ("
<td align=\"center\" class=\"row1\">&nbsp;</td>
") : ("")) . "
</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:announcement_wrap:desc::trigger:>
//===========================================================================
function announcement_wrap($announce="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
	<td class=\"darkrow1\" colspan=\"8\"><b>{$this->ipsclass->lang['announce_start']}</b></td>
</tr>
$announce";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forum_active_users:desc::trigger:>
//===========================================================================
function forum_active_users($active=array()) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"borderwrap\" style='padding-bottom:1px;'>
	<div class=\"formsubtitle\" style=\"padding: 4px;\">{$this->ipsclass->lang['active_users_title']} ({$this->ipsclass->lang['active_users_detail']})</div>
	<div class=\"row1\" style=\"padding: 4px;\">{$this->ipsclass->lang['active_users_members']} {$active['names']}</div>
</div>
<br />";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forum_password_log_in:desc::trigger:>
//===========================================================================
function forum_password_log_in($fid="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<form action=\"{$this->ipsclass->base_url}\" method=\"post\">
	<input type=\"hidden\" name=\"act\" value=\"SF\" />
	<input type=\"hidden\" name=\"f\" value=\"$fid\" />
	<input type=\"hidden\" name=\"L\" value=\"1\" />
	<div class=\"borderwrap\">
		<div class=\"maintitle\"><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['need_password']}</div>
		<div class=\"tablepad\">{$this->ipsclass->lang['need_password_txt']}</div>
		<div class=\"tablepad\">
			<b>{$this->ipsclass->lang['enter_pass']}</b>
			<input type=\"password\" size=\"20\" name=\"f_password\" />
		</div>
		<div class=\"pformstrip\" align=\"center\"><input type=\"submit\" value=\"{$this->ipsclass->lang['f_pass_submit']}\" class=\"button\" /></div>
	</div>
</form>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forums_attachments_bottom:desc::trigger:>
//===========================================================================
function forums_attachments_bottom() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- inbox folder -->
	</table>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forums_attachments_row:desc::trigger:>
//===========================================================================
function forums_attachments_row($data="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr id=\"{$data['attach_id']}\">
	<td align=\"center\" class=\"row1\"><img src=\"{$this->ipsclass->vars['mime_img']}/{$data['image']}\" alt=\"{$this->ipsclass->lang['attached_file']}\" /></td>
	<td class=\"row2\">
		<a href=\"{$this->ipsclass->base_url}act=Attach&amp;type=post&amp;id={$data['attach_id']}\" title=\"{$data['attach_file']}\" target=\"_blank\">{$data['short_name']}</a>
		<div class=\"desc\">( {$this->ipsclass->lang['attach_hits']}: {$data['attach_hits']} )<br />( {$this->ipsclass->lang['attach_post_date']} {$data['attach_date']} )</div>
	</td>
	<td align=\"center\" class=\"row1\">{$data['real_size']}</td>
	<td class=\"row2\" align=\"center\"><a href=\"#\" onclick=\"opener.location='{$this->ipsclass->base_url}act=findpost&amp;pid={$data['pid']}';\">{$data['pid']}</a></td>
</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:forums_attachments_top:desc::trigger:>
//===========================================================================
function forums_attachments_top($title="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- inbox folder -->
<div class=\"borderwrap\">
	<div class=\"maintitle\">{$this->ipsclass->lang['attach_page_title']}: $title</div>
	<table class='ipbtable' cellspacing=\"1\">
		<tr>
			<th width=\"2%\">&nbsp;</th>
			<th width=\"73%\"><b>{$this->ipsclass->lang['attach_title']}</b></th>
			<th width=\"5%\">{$this->ipsclass->lang['attach_size']}</b></a></th>
			<th width=\"15%\"><b>{$this->ipsclass->lang['attach_post']}</b></th>
		</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:mm_end:desc::trigger:>
//===========================================================================
function mm_end() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- end multimod -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:mm_entry:desc::trigger:>
//===========================================================================
function mm_entry($id="",$title="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<option value=\"t_{$id}\">--  $title</option>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:mm_start:desc::trigger:>
//===========================================================================
function mm_start() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<option value=\"-1\">------------------------------</option>
<option value=\"-1\">{$this->ipsclass->lang['mm_title']}</option>
<option value=\"-1\">------------------------------</option>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:OLD_render_pinned_row:desc::trigger:>
//===========================================================================
function OLD_render_pinned_row($data="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- Begin Pinned Topic Entry {$data['tid']} -->
<tr> 
	<td align=\"center\" class=\"row2\">{$data['folder_img']}</td>
	<td align=\"center\" class=\"row2\">{$data['topic_icon']}</td>
	<td class=\"row2\">
	<div>
		{$data['go_new_post']}{$data['prefix']} {$data['attach_img']}<a href=\"{$this->ipsclass->base_url}showtopic={$data['tid']}\" title=\"{$this->ipsclass->lang['topic_started_on']} {$data['start_date']}\">{$data['title']}</a> {$data[PAGES]}
		<div class=\"desc\">{$data['description']}</div>
	</div>
	<td align=\"center\" class=\"row2\">{$data['posts']}</td>
	<td align=\"center\" class=\"row2\">{$data['starter']}</td>
	<td align=\"center\" class=\"row2\">{$data['views']}</td>
	<td class=\"row2\"><span class=\"desc\">{$data['last_post']}<br /><a href=\"{$this->ipsclass->base_url}showtopic={$data['tid']}&amp;view=getlastpost\">{$data['last_text']}</a> <b>{$data['last_poster']}</b></span></td>
</tr>
<!-- End Pinned Topic Entry {$data['tid']} -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:page_title:desc::trigger:>
//===========================================================================
function page_title($title="",$pages="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div><span class=\"pagetitle\">$title</span>$pages</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:PageTop:desc::trigger:>
//===========================================================================
function PageTop($data="",$can_edit=0,$can_open=0,$can_close=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<script language=\"javascript\" type=\"text/javascript\">
<!--
var unselectedbutton  = \"{$this->ipsclass->vars['img_url']}/topic_unselected.gif\";
var selectedbutton    = \"{$this->ipsclass->vars['img_url']}/topic_selected.gif\";
var lang_gobutton     = \"{$this->ipsclass->lang['f_go']}\";
var lang_suredelete   = \"{$this->ipsclass->lang['cp_js_delete']}\";
var lang_otherpage    = \"{$this->ipsclass->lang['jscript_otherpage']}\";
var lang_clickhold    = \"{$this->ipsclass->lang['js_clickhold']}\";
var perm_can_edit     = $can_edit;
var perm_can_open     = $can_open;
var perm_can_close    = $can_close;
var perm_max_length   = {$this->ipsclass->vars['topic_title_max_len']};
// Set up img vars
var img_markers = {
	'bc_new.gif' : 'bc_nonew.gif',
	'bf_new.gif' : 'bf_nonew.gif',
	'br_new.gif' : 'br_nonew.gif'
};
var regex_markers = 'bc_new.gif|bf_new.gif|br_new.gif';
//-->
</script>
<script type=\"text/javascript\" src=\"jscripts/ipb_forum.js\"></script>
<!--IBF.SUBFORUMS-->
<table class='ipbtable' cellspacing=\"0\">
	<tr>
		<td style='padding-left:0px' width=\"60%\">{$data['SHOW_PAGES']}</td>
		<td class='nopad' style='padding:0px 0px 5px 0px' align=\"right\" nowrap=\"nowrap\"><a href=\"{$this->ipsclass->base_url}act=post&amp;do=new_post&amp;f={$data['id']}\"><{A_POST}></a></td>
	</tr>
</table>
<div class=\"borderwrap\">
	<div class=\"maintitle\" style='padding:4px'>
	<!-- TABLE FIX FOR MOZILLA WRAPPING-->
	<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
	 <td width='99%'><div><{CAT_IMG}>&nbsp;{$data['name']}</div></td>
	 <td width='1%' nowrap='nowrap' align='right'>
	  <div class='popmenubutton' id='forummenu-options'><a href='#forumoptions'>{$this->ipsclass->lang['forum_options']}</a> <img src='{$this->ipsclass->vars['img_url']}/menu_action_down.gif' alt='V' title='{$this->ipsclass->lang['global_open_menu']}' border='0' /></div>
	 </td>
	</tr>
	</table>
   </div>
   <table class='ipbtable' cellspacing=\"1\">
	<tr> 
		<th align=\"center\">&nbsp;</th>
		<th align=\"center\">&nbsp;</th>
		<th width=\"50%\" nowrap=\"nowrap\">{$this->ipsclass->lang['h_topic_title']}</th>
		<th width=\"7%\" style=\"text-align:center\" nowrap=\"nowrap\">{$this->ipsclass->lang['h_replies']}</th>
		<th width=\"14%\" style=\"text-align:center\" nowrap=\"nowrap\">{$this->ipsclass->lang['h_topic_starter']}</th>
		<th width=\"7%\" style=\"text-align:center\" nowrap=\"nowrap\">{$this->ipsclass->lang['h_hits']}</th>
		<th width=\"22%\" nowrap=\"nowrap\">{$this->ipsclass->lang['h_last_action']}</th>
" . (($this->ipsclass->member['is_mod'] == 1) ? ("
<th width=\"1%\" align=\"center\"><a href=\"#\" title=\"{$this->ipsclass->lang['click_for_mod']}\" onclick=\"forum_select_all(); return false;\"><img name=\"imgall\" id='ipb-topics-all' src=\"{$this->ipsclass->vars['img_url']}/topic_unselected.gif\" alt='' /></th>
") : ("")) . "
	</tr>
	<!-- Forum page unique top -->
	<!--IBF.ANNOUNCEMENTS-->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_show_lastpage:desc::trigger:>
//===========================================================================
function pagination_show_lastpage($tid="",$st="",$page="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<span class=\"minipagelinklast\"><a href=\"{$this->ipsclass->base_url}showtopic={$tid}&amp;st=$st&amp;start=$st\">&raquo; $page</a></span>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_show_page:desc::trigger:>
//===========================================================================
function pagination_show_page($tid="",$st="",$page="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<span class=\"minipagelink\"><a href=\"{$this->ipsclass->base_url}showtopic={$tid}&amp;st=$st&amp;start=$st\">$page</a></span>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:pagination_wrap_pages:desc::trigger:>
//===========================================================================
function pagination_wrap_pages($tid="",$pages="",$posts="",$perpage="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "&nbsp;<a href=\"javascript:multi_page_jump('{$this->ipsclass->base_url}showtopic={$tid}', $posts, $perpage );\" title=\"{$this->ipsclass->lang['multipage_alt']}\"><img src='{$this->ipsclass->vars['img_url']}/pages_icon.gif' alt='*' border='0' /></a> $pages";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:render_forum_row:desc::trigger:>
//===========================================================================
function render_forum_row($data="",$class1='row2',$class2='row1',$classposts='row2',$inforum=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- Begin Topic Entry {$data['tid']} -->
<tr> 
	<td align=\"center\" class=\"$class2\" id='tid-folder-{$data['tid']}' onclick='return topic_toggle_folder(\"{$data['tid']}\", \"{$data['state']}\");'>{$data['folder_img']}</td>
	<td align=\"center\" class=\"$class2\">{$data['topic_icon']}</td>
	<td class=\"$class2\" valign=\"middle\">
	    <div style='float:right'>{$data['_rate_img']}</div>
		<div>
			{$data['go_new_post']}{$data['prefix']} {$data['attach_img']}<span id='tid-span-{$data['tid']}'><a id=\"tid-link-{$data['tid']}\" href=\"{$this->ipsclass->base_url}showtopic={$data['tid']}\" title=\"{$this->ipsclass->lang['topic_started_on']} {$data['start_date']}\">{$data['title']}</a></span> {$data['PAGES']}
			<div class=\"desc\"><span onclick='return span_desc_to_input(\"{$data['tid']}\");' id='tid-desc-{$data['tid']}'>{$data['description']}</span></div>
		</div>
	</td>
	<td align='center' class=\"$classposts\">
     {$data['posts']}
" . (($data['_hasqueued'] == 1 and $inforum == 1) ? ("
&nbsp;<a href=\"{$this->ipsclass->base_url}showtopic={$data['tid']}&amp;modfilter=invisible_posts\"><{BC_QUEUED_POSTS}></a>
") : ("")) . "
    </td>
	<td align=\"center\" class=\"$class1\">{$data['starter']}</td>
	<td align=\"center\" class=\"$class1\">{$data['views']}</td>
	<td class=\"$class1\"><span class=\"lastaction\">{$data['last_post']}<br /><a href=\"{$this->ipsclass->base_url}showtopic={$data['tid']}&amp;view=getlastpost\">{$data['last_text']}</a> <b>{$data['last_poster']}</b></span></td>
" . (($this->ipsclass->member['is_mod'] == 1 and $inforum == 1 and $data['tidon'] == 1) ? ("
<td align=\"center\" class=\"$class1\"><input type='hidden' name='tid_{$data['real_tid']}' id='tid_{$data['real_tid']}' /><a href=\"#\" title=\"{$this->ipsclass->lang['click_for_mod']}\" onclick=\"forum_toggle_tid('{$data['real_tid']}'); return false;\"><img name=\"img{$data['real_tid']}\" id='ipb-topic-{$data['real_tid']}' src=\"{$this->ipsclass->vars['img_url']}/topic_selected.gif\" alt='' /></a></td>
") : ("")) . "
" . (($this->ipsclass->member['is_mod'] == 1 and $inforum == 1 and $data['tidon'] == 0) ? ("
<td align=\"center\" class=\"$class1\"><input type='hidden' name='tid_{$data['real_tid']}' id='tid_{$data['real_tid']}' /><a href=\"#\" title=\"{$this->ipsclass->lang['click_for_mod']}\" onclick=\"forum_toggle_tid('{$data['real_tid']}'); return false;\"><img name=\"img{$data['real_tid']}\" id='ipb-topic-{$data['real_tid']}' src=\"{$this->ipsclass->vars['img_url']}/topic_unselected.gif\" alt='' /></a></td>
") : ("")) . "
</tr>
<!-- End Topic Entry {$data['tid']} -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:render_pinned_end:desc::trigger:>
//===========================================================================
function render_pinned_end() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!-- END PINNED -->
<tr>
   <td class=\"darkrow1\" colspan=\"8\"><b>{$this->ipsclass->lang['regular_topics']}</b></td>
</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:render_pinned_start:desc::trigger:>
//===========================================================================
function render_pinned_start($show=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<!--PINNED-->
" . (($show == 1) ? ("
<tr>
	<td class=\"darkrow1\" colspan=\"8\"><b>{$this->ipsclass->lang['pinned_start']}</b></td>
</tr>
") : ("")) . "
    <!-- END PINNED -->";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:show_no_matches:desc::trigger:>
//===========================================================================
function show_no_matches() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr> 
	<td class=\"row2\" colspan=\"8\" align=\"center\">
		<br />
		<b>{$this->ipsclass->lang['no_topics']}</b>
		<br /><br />
	</td>
</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:show_page_jump:desc::trigger:>
//===========================================================================
function show_page_jump($total="",$pp="",$qe="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<a href=\"javascript:multi_page_jump( $total, $pp, '$qe' )\" title=\"{$this->ipsclass->lang['tpl_jump']}\">{$this->ipsclass->lang['multi_page_forum']}</a>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:show_rules:desc::trigger:>
//===========================================================================
function show_rules($rules="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"borderwrap\">
	<h3><{CAT_IMG}>&nbsp;{$rules['title']}, <a href=\"{$this->ipsclass->base_url}act=SF&amp;f={$rules['fid']}\">{$this->ipsclass->lang['back_to_forum']}</a></h3>
	<p>{$rules['body']}</p>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:TableEnd:desc::trigger:>
//===========================================================================
function TableEnd($data="",$auth_key="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
			<td colspan=\"8\" class=\"darkrow1\">
				<table class='ipbtable' cellspacing=\"0\">
					<tr>
						<td width=\"50%\" class=\"nopad\">
							<form action=\"{$this->ipsclass->base_url}\" method=\"post\" name=\"search\">
								<input type=\"hidden\" name=\"forums\" value=\"{$data['id']}\" />
								<input type=\"hidden\" name=\"cat_forum\" value=\"forum\" />
								<input type=\"hidden\" name=\"act\" value=\"Search\" />
								<input type=\"hidden\" name=\"joinname\" value=\"1\" />
								<input type=\"hidden\" name=\"CODE\" value=\"01\" />
								<input type=\"text\" size=\"30\" name=\"keywords\" value=\"{$this->ipsclass->lang['enter_keywords']}\" onfocus=\"this.value = '';\" /> <input type=\"submit\" value=\"{$this->ipsclass->lang['search_forum']}\" class=\"button\" />
							</form>
						</td>
" . (($this->ipsclass->member['is_mod'] == 1) ? ("
<td width=\"50%\" align=\"right\" nowrap=\"nowrap\" class=\"nopad\">
							<form name=\"modform\" method=\"post\" action=\"{$this->ipsclass->base_url}\" onsubmit=\"return checkdelete();\">
								<input type=\"hidden\" name=\"act\" value=\"mod\" />
								<input type=\"hidden\" name=\"CODE\" value=\"topicchoice\" />
								<input type=\"hidden\" name=\"f\" value=\"{$data['id']}\" />
								<input type=\"hidden\" name=\"auth_key\" value=\"{$auth_key}\" />
								<input type=\"hidden\" name=\"modfilter\" value=\"{$this->ipsclass->input['modfilter']}\" />
								<input type=\"hidden\" value=\"{$this->ipsclass->input['selectedtids']}\" name=\"selectedtids\" />
								<select name=\"tact\">
									<option value=\"close\">{$this->ipsclass->lang['cpt_close']}</option>
									<option value=\"open\">{$this->ipsclass->lang['cpt_open']}</option>
									<option value=\"pin\">{$this->ipsclass->lang['cpt_pin']}</option>
									<option value=\"unpin\">{$this->ipsclass->lang['cpt_unpin']}</option>
									<option value=\"move\">{$this->ipsclass->lang['cpt_move']}</option>
									<option value=\"merge\">{$this->ipsclass->lang['cpt_merge']}</option>
									<option value=\"delete\">{$this->ipsclass->lang['cpt_delete']}</option>
									<option value=\"approve\">{$this->ipsclass->lang['cpt_approve']}</option>
									<option value=\"unapprove\">{$this->ipsclass->lang['cpt_unapprove']}</option>
									<!--IBF.MMOD-->
								</select>&nbsp;
								<input type=\"submit\" name=\"gobutton\" value=\"{$this->ipsclass->lang['f_go']}\" class=\"button\" />
							</form>
						</td>
") : ("")) . "
					</tr>
				</table>
			</td>
		</tr>
		<tr> 
			<td class=\"catend\" colspan=\"8\"><!-- no content --></td>
		</tr>
	</table>
</div>
<table class='ipbtable' cellspacing=\"0\">
	<tr>
		<td style='padding-left:0px' width=\"50%\" nowrap=\"nowrap\">{$data['SHOW_PAGES']}</td>
		<td class='nopad' style='padding:5px 0px 5px 0px' align=\"right\" width=\"50%\"><a href=\"{$this->ipsclass->base_url}act=post&amp;do=new_post&amp;f={$data['id']}\"><{A_POST}></a></td>
	</tr>
</table>
<!--IBF.FORUM_ACTIVE-->
<div class=\"activeusers\">
	<div class=\"row2\">
		<table class='ipbtable' cellspacing=\"0\">
			<tr>
				<td width=\"5%\" nowrap=\"nowrap\">
					<{B_NEW}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_open_new']}</span>
					<br /><{B_NORM}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_open_no']}</span>
					<br /><{B_HOT}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_hot_new']}</span>
					<br /><{B_HOT_NN}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_hot_no']}</span>&nbsp;
				</td>
				<td width=\"5%\" nowrap=\"nowrap\">
					<{B_POLL}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_poll']}</span>
					<br /><{B_POLL_NN}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_poll_no']}</span>
					<br /><{B_LOCKED}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_locked']}</span>
					<br /><{B_MOVED}>&nbsp;&nbsp;<span class=\"desc\">{$this->ipsclass->lang['pm_moved']}</span>
				</td>
				<td align=\"right\" width=\"90%\">
					{$data['FORUM_JUMP']}<br /><br />
					<form action=\"{$this->ipsclass->base_url}act=SF&amp;f={$data['id']}&amp;st={$this->ipsclass->input['st']}&amp;changefilters=1\" method=\"post\">
						<select name=\"sort_key\">{$this->ipsclass->show['sort_by']}</select>
						<select name=\"sort_by\">{$this->ipsclass->show['sort_order']}</select>
						<select name=\"prune_day\">{$this->ipsclass->show['sort_prune']}</select>
						<select name=\"topicfilter\">{$this->ipsclass->show['topic_filter']}</select>
						<input type=\"submit\" value=\"{$this->ipsclass->lang['sort_submit']}\" class=\"button\" />
					<br /> <input type='checkbox' value='1' name='remember' class='checkbox' /> {$this->ipsclass->lang['remember_options']}
					</form>
				</td>
			</tr>
		</table>
	</div>
</div>
<script type=\"text/javascript\">
<!--
  menu_build_menu(
  \"forummenu-options\",
  new Array(
" . (($this->ipsclass->member['is_mod'] == 1) ? ("
\"~~NODIV~~<div class='popupmenu-category' align='center'>{$this->ipsclass->lang['moderator_options']}</div>\",
			img_item + \" <a href='{$this->ipsclass->base_url}showforum={$data['id']}&amp;modfilter=invisible_topics'>{$this->ipsclass->lang['mod_showallinvisible']}</a>\",
			img_item + \" <a href='{$this->ipsclass->base_url}showforum={$data['id']}&amp;modfilter=invisible_posts'>{$this->ipsclass->lang['mod_showallposts']}</a>\",
			img_item + \" <a href='{$this->ipsclass->base_url}act=mod&amp;CODE=resync&amp;f={$data['id']}&amp;auth_key={$this->ipsclass->md5_check}'>{$this->ipsclass->lang['mod_resync']}</a>\",
			img_item + \" <a href='#' onclick='forum_mod_pop({$data['id']});'>{$this->ipsclass->lang['mod_prune']}</a>\",
			 \"~~NODIV~~<div class='popupmenu-category' align='center'>{$this->ipsclass->lang['forum_options']}</div>\",
") : ("")) . "
  			 img_item + \" <a href='{$this->ipsclass->base_url}act=Login&amp;CODE=04&amp;f={$data['id']}&amp;fromforum={$this->ipsclass->input['f']}'>{$this->ipsclass->lang['mark_as_read']}</a>\",
  			 img_item + \" <a href='{$this->ipsclass->base_url}act=Login&amp;CODE=04&amp;f={$data['id']}&amp;fromforum=0'>{$this->ipsclass->lang['mark_as_read2']}</a>\",
  			 img_item + \" <a href='{$this->ipsclass->base_url}act=usercp&amp;CODE=start_subs&amp;method=forum&amp;fid={$data['id']}'>{$this->ipsclass->lang['ft_title']}</a>\"
		    ) );
//-->
 </script>
<br clear=\"all\" />
" . (($this->ipsclass->member['is_mod']) ? ("
<br />
<a name='forumoptions'></a>
<div align=\"center\" id='forumoptionsjs'>
	<a href=\"{$this->ipsclass->base_url}showforum={$data['id']}&amp;modfilter=invisible_topics\">{$this->ipsclass->lang['mod_showallinvisible']}</a>
	&middot;
	<a href=\"{$this->ipsclass->base_url}showforum={$data['id']}&amp;modfilter=invisible_posts\">{$this->ipsclass->lang['mod_showallposts']}</a>
	&middot;
	<a href=\"{$this->ipsclass->base_url}act=mod&amp;CODE=resync&amp;f={$data['id']}&amp;auth_key={$auth_key}\">{$this->ipsclass->lang['mod_resync']}</a>
	&middot;
	<a href=\"javascript:PopUp('{$this->ipsclass->base_url}act=mod&amp;CODE=prune_start&amp;f={$data['id']}&amp;auth_key={$auth_key}', 'PRUNE', 600,500)\">{$this->ipsclass->lang['mod_prune']}</a>
</div>
<script type='text/javascript'>
//<![CDATA[
 document.getElementById('forumoptionsjs').style.display = 'none';
//]]>
</script>
") : ("")) . "
<script type='text/javascript'>
//<![CDATA[
 // INIT links for editing
 
 if ( use_enhanced_js && perm_can_edit )
 {
 	forum_init_topic_links();
 }
//]]>
</script>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:topic_attach_icon:desc::trigger:>
//===========================================================================
function topic_attach_icon($tid="",$count=0) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<a href=\"#\" onclick=\"PopUp('{$this->ipsclass->base_url}act=attach&amp;code=showtopic&amp;tid={$tid}', 'Attach{$tid}', 500,400); return false;\" title=\"{$count} {$this->ipsclass->lang['topic_attach']}\"><{ATTACH_ICON}></a>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:topic_rating_image:desc::trigger:>
//===========================================================================
function topic_rating_image($rating_id=1) {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<img src='{$this->ipsclass->vars['img_url']}/rating_{$rating_id}_mini.gif' border='0' alt='{$rating_id}' />&nbsp;";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:who_link:desc::trigger:>
//===========================================================================
function who_link($tid="",$posts="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<a href=\"javascript:who_posted($tid);\">$posts</a>";
//--endhtml--//
return $IPBHTML;
}



}

/*--------------------------------------------------*/
/*<changed bits>

</changed bits>*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>