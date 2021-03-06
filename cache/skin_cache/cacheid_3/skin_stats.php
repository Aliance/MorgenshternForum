<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Thu, 24 May 2007 13:16:02 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_stats_3 {

 var $ipsclass;
//===========================================================================
// <ips:close_strip:desc::trigger:>
//===========================================================================
function close_strip() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
		<td class=\"catend\" colspan=\"4\"><!-- no content --></td>
	</tr>
</table>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:group_strip:desc::trigger:>
//===========================================================================
function group_strip($group="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;$group</div>
		<table class='ipbtable' cellspacing=\"1\">
			<tr>
				<th width=\"30%\" valign=\"middle\">{$this->ipsclass->lang['leader_name']}</th>
				<th width=\"40%\" align=\"center\" valign=\"middle\">{$this->ipsclass->lang['leader_forums']}</th>
				<th align=\"center\" width=\"25%\" valign=\"middle\">{$this->ipsclass->lang['leader_location']}</th>
				<th align=\"center\" width=\"5%\"  valign=\"middle\">&nbsp;</th>
			</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:leader_row:desc::trigger:>
//===========================================================================
function leader_row($info="",$forums="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
				<td class=\"row1\">
				" . (($info['id'] > 0) ? ("
					<a href=\"{$this->ipsclass->base_url}showuser={$info['id']}\">{$info['members_display_name']}</a>
				") : ("
					{$info['members_display_name']}
				")) . "
				</td>
				<td align=\"center\" class=\"row1\">$forums</td>
				<td align=\"center\" class=\"row1\">{$info['location']}</td>
				<td align=\"center\" class=\"row1\">{$info['msg_icon']}</td>
			</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:leader_row_forum_end:desc::trigger:>
//===========================================================================
function leader_row_forum_end() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "</select>
</form>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:leader_row_forum_entry:desc::trigger:>
//===========================================================================
function leader_row_forum_entry($id="",$name="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<option value=\"$id\">$name</option>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:leader_row_forum_start:desc::trigger:>
//===========================================================================
function leader_row_forum_start($id="",$count_string="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<form method=\"post\" onsubmit=\"if(document.jmenu$id.f.value == -1){return false;}\" action=\"{$this->ipsclass->base_url}\" name=\"jmenu$id\">
	<select name=\"showforum\" onchange=\"if(this.options[this.selectedIndex].value != -1){ document.jmenu$id.submit() }\">
		<option value=\"-1\">$count_string</option>
		<option value=\"-1\">--------------------------------------------------------</option>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:page_title:desc::trigger:>
//===========================================================================
function page_title($title="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<table class='ipbtable' cellspacing=\"0\">
	<tr>
		<td><span class=\"pagetitle\">{$title}</td>
	</tr>
</table>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:top_poster_footer:desc::trigger:>
//===========================================================================
function top_poster_footer() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
		<td class=\"catend\" colspan=\"5\"><!-- no content --></td>
	</tr>
</table>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:top_poster_header:desc::trigger:>
//===========================================================================
function top_poster_header() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['todays_posters']}</div>
	<table class='ipbtable' cellspacing=\"1\">
		<tr>
			<th width=\"30%\" valign=\"middle\">{$this->ipsclass->lang['member']}</th>
			<th width=\"20%\" align=\"center\" valign=\"middle\">{$this->ipsclass->lang['member_joined']}</th>
			<th align=\"center\" width=\"15%\" valign=\"middle\">{$this->ipsclass->lang['member_posts']}</th>
			<th align=\"center\" width=\"15%\" valign=\"middle\">{$this->ipsclass->lang['member_today']}</th>
			<th align=\"center\" width=\"20%\" valign=\"middle\">{$this->ipsclass->lang['member_percent']}</th>
		</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:top_poster_no_info:desc::trigger:>
//===========================================================================
function top_poster_no_info() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
			<td colspan=\"5\" align=\"center\" class=\"row1\" valign=\"middle\">{$this->ipsclass->lang['no_info']}</td>
		</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:top_poster_row:desc::trigger:>
//===========================================================================
function top_poster_row($info="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
			<td class=\"row1\" valign=\"middle\">
				" . (($info['id'] > 0) ? ("
					<a href=\"{$this->ipsclass->base_url}showuser={$info['id']}\">{$info['members_display_name']}</a>
				") : ("
					{$info['members_display_name']}
				")) . "
			</td>
			<td align=\"center\" class=\"row1\" valign=\"middle\">{$info['joined']}</td>
			<td align=\"center\" class=\"row1\" valign=\"middle\">{$info['posts']}</td>
			<td align=\"center\" class=\"row1\" valign=\"middle\">{$info['tpost']}</td>
			<td align=\"center\" class=\"row1\" valign=\"middle\">{$info['today_pct']}%</td>
		</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:who_end:desc::trigger:>
//===========================================================================
function who_end() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
			<td class=\"formbuttonrow\" colspan=\"2\"><a href=\"javascript:bog_off();\">{$this->ipsclass->lang['who_go']}</a></td>
		</tr>
			<tr>
		<td class=\"catend\" colspan=\"2\"><!-- no content --></td>
	</tr>
	</table>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:who_header:desc::trigger:>
//===========================================================================
function who_header($fid="",$tid="",$title="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<script language=\"javascript\">
<!--
	function bog_off(){
		var tid = \"$tid\";
		var fid = \"$fid\";
		
		opener.location= \"{$this->ipsclass->base_url}\" + \"showtopic=\" + tid;
		self.close();
	}
-->
</script>
<div class=\"borderwrap\">
	<div class=\"maintitle\" align=\"center\">{$this->ipsclass->lang['who_farted']} $title</div>
	<table class='ipbtable' cellspacing=\"1\">
		<tr>
			<th width=\"70%\" valign=\"middle\">{$this->ipsclass->lang['who_poster']}</th>
			<th width=\"30%\" align=\"center\" valign=\"middle\">{$this->ipsclass->lang['who_posts']}</th>
		</tr>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:who_name_link:desc::trigger:>
//===========================================================================
function who_name_link($id="",$name="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<a href=\"#\" onclick=\"opener.ipsclass.location_jump( '{$this->ipsclass->base_url}showuser=$id', 1);\">$name</a>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:who_row:desc::trigger:>
//===========================================================================
function who_row($row="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
			<td class=\"row1\" valign=\"middle\">{$row['author_name']}</td>
			<td align=\"center\" class=\"row1\" valign=\"middle\">{$row['pcount']}</td>
		</tr>";
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