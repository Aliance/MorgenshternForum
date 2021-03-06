<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Thu, 24 May 2007 13:16:02 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_portal_3 {

 var $ipsclass;
//===========================================================================
// <ips:csite_css_external:desc::trigger:>
//===========================================================================
function csite_css_external($css="",$img="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<style type=\"text/css\" media=\"all\">
	@import url({$this->ipsclass->vars['board_url']}/css.php?d={$css}_{$img}.css);
</style>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:csite_css_inline:desc::trigger:>
//===========================================================================
function csite_css_inline($css="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<style type=\"text/css\">
	{$css}
</style>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:csite_skeleton_template:desc::trigger:>
//===========================================================================
function csite_skeleton_template($component_links='') {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<script type=\"text/javascript\" src=\"jscripts/ipb_topic.js\"></script>
<table class='ipbtable' cellspacing=\"0\">
 <tr>
	 <td width=\"30%\" class=\"nopad\" valign=\"top\">
		 <!-- LEFT -->
		 <!--::BASIC:SITENAV::-->
		 <!--::online_users_show::-->
		 <!--::blogs_show_last_updated_x::-->
		 <!--::gallery_show_random_image::-->
		 <!--::calendar_show_current_month::-->
		 <!--::poll_show_poll::-->
		 <!--::recent_topics_discussions_last_x::-->
		 <!--::BASIC:AFFILIATES::-->
		 <!--END LEFT-->
	 </td>
	 <!--SPACER-->
	 <td width=\"1%\" class=\"nopad\">&nbsp;</td>
	 <td width=\"70%\" class=\"nopad\" valign=\"top\">
		 <!--MAIN-->
		 <!--::recent_topics_last_x::-->
		 <!--END MAIN-->
	 </td>
 </tr>
 <!--End Main Content-->
</table>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_affiliates:desc::trigger:>
//===========================================================================
function tmpl_affiliates($links="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['aff_title']}</div>
	<div class=\"tablepad\">
		$links
	</div>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_articles:desc::trigger:>
//===========================================================================
function tmpl_articles($articles="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "$articles";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_articles_row:desc::trigger:>
//===========================================================================
function tmpl_articles_row($entry="",$bottom_string="",$top_string="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"borderwrap\">
<table class='ipbtable' cellspacing=\"0\">
	<tr>
		<td class=\"maintitle\" colspan=\"2\"><{CAT_IMG}>&nbsp;<a href=\"{$this->ipsclass->base_url}showtopic={$entry['tid']}\">{$entry['title']}</a></td>
	</tr>
	<tr>
		<td class=\"row1\" colspan=\"2\">$top_string</td>
	</tr>
	<tr>
		<td class=\"post1\" width=\"5%\" valign=\"top\">{$entry['avatar']}</td>
		<td class=\"post1\" width=\"95%\" valign=\"top\"><div style=\"height: 200px; overflow: auto;\">{$entry['post']}
		<!--IBF.ATTACHMENT_{$entry['pid']}--></div></td>
	</tr>
	<tr>
		<td class=\"row1\" colspan=\"2\" align=\"right\">
		$bottom_string
		&nbsp;&nbsp;<a href=\"{$this->ipsclass->base_url}act=Print&amp;client=printer&amp;f={$entry['forum_id']}&amp;t={$entry['tid']}\"><img src='style_images/<#IMG_DIR#>/cs_print.gif' border='0' alt='' /></a>
		<a href=\"{$this->ipsclass->base_url}act=Forward&amp;f={$entry['forum_id']}&amp;t={$entry['tid']}\"><img src='style_images/<#IMG_DIR#>/cs_email.gif' border='0' alt='' /></a>
		</td>
	</tr>
</table>
</div>
<br />";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_calendar_wrap:desc::trigger:>
//===========================================================================
function tmpl_calendar_wrap($content="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
$content";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_comment_link:desc::trigger:>
//===========================================================================
function tmpl_comment_link($tid="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<a href=\"{$this->ipsclass->base_url}showtopic=$tid&amp;view=getlastpost\">{$this->ipsclass->lang['article_comment']}</a>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_latestposts:desc::trigger:>
//===========================================================================
function tmpl_latestposts($posts="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['discuss_title']}</div>
	$posts
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_links_wrap:desc::trigger:>
//===========================================================================
function tmpl_links_wrap($link="",$name="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "&middot; <a href=\"$link\">$name</a><br />";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_onlineusers:desc::trigger:>
//===========================================================================
function tmpl_onlineusers($breakdown="",$split="",$names="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;<a href=\"{$this->ipsclass->base_url}act=Online\">{$this->ipsclass->lang['online_title']}</a></div>
	<div class=\"tablepad\">
		<span class=\"desc\">$breakdown<br />$split<br />$names</span>
	</div>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_poll_wrapper:desc::trigger:>
//===========================================================================
function tmpl_poll_wrapper($content="",$tid="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;<a href=\"{$this->ipsclass->base_url}showtopic=$tid\">{$this->ipsclass->lang['poll_title']}</a></div>
	$content
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_readmore_link:desc::trigger:>
//===========================================================================
function tmpl_readmore_link($tid="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "...<a href=\"{$this->ipsclass->base_url}showtopic=$tid\">{$this->ipsclass->lang['article_readmore']}</a>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_recentarticles:desc::trigger:>
//===========================================================================
function tmpl_recentarticles($articles="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<br />
<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['recent_title']}</div>
	$articles
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_sitenav:desc::trigger:>
//===========================================================================
function tmpl_sitenav($links="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"borderwrap\">
	<div class=\"maintitle\"><{CAT_IMG}>&nbsp;{$this->ipsclass->lang['links_title']}</div>
	<div class=\"tablepad\">
		$links
	</div>
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_skin_select_bottom:desc::trigger:>
//===========================================================================
function tmpl_skin_select_bottom() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "</select>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_skin_select_top:desc::trigger:>
//===========================================================================
function tmpl_skin_select_top() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<select name=\"skinid\" onchange=\"window.location='{$this->ipsclass->vars['dynamiclite']}&amp;s={$this->ipsclass->session_id}&amp;setskin=1&amp;skinid=' + this.value\">";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_topic_row:desc::trigger:>
//===========================================================================
function tmpl_topic_row($tid="",$title="",$posts="",$views="",$mid="",$mname="",$date="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"formsubtitle\">
	<b><a href=\"{$this->ipsclass->base_url}showtopic=$tid\">$title</a></b>
</div>
<div class=\"tablepad\">
	<a href=\"{$this->ipsclass->base_url}showuser=$mid\">$mname</a> &#064; $date
	<br />{$this->ipsclass->lang['recent_read']}: $views &nbsp; {$this->ipsclass->lang['recent_comments']}: $posts
</div>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:tmpl_wrap_avatar:desc::trigger:>
//===========================================================================
function tmpl_wrap_avatar($avatar="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "$avatar";
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