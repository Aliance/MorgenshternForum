<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 3                     */
/* CACHE FILE: Generated: Thu, 24 May 2007 13:16:02 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_gallery_favs_3 {

 var $ipsclass;
//===========================================================================
// <ips:error:desc::trigger:>
//===========================================================================
function error($type="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"formsubtitle\">{ipb.lang[$type.'_title']}</div>
<p>{ipb.lang[$type.'_text']}</p>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:fav_view_end:desc::trigger:>
//===========================================================================
function fav_view_end($span="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr> 
			<td class=\"formbuttonrow\" colspan=\"{$span}\" align=\"center\">{ipb.lang['showing']}</td>
		</tr>
	</table>
<script type=\"text/javascript\" src='jscripts/ipb_galleryjs.js'></script>
<script type=\"text/javascript\">
//<![CDATA[
// Resize thumb divs...
var use_thumbs = parseInt(\"{ipb.vars['gallery_create_thumbs']}\");
var thumbs_x   = parseInt(\"{ipb.vars['gallery_thumb_width']}\");
var thumbs_y   = parseInt(\"{ipb.vars['gallery_thumb_height']}\");
html_resize_thumb_divs( use_thumbs, thumbs_x, thumbs_y );
//]]>
</script>";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:fav_view_top:desc::trigger:>
//===========================================================================
function fav_view_top($info="") {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<div class=\"formsubtitle\">{ipb.lang['your_favs']}</div>
		<table cellspacing=\"1\" class=\"ipbtable\" width=\"100%\">
		<tr>
			<td width=\"20%\" nowrap=\"nowrap\">{$info['SHOW_PAGES']}</td>
			<td align=\"right\" width=\"80%\" nowrap=\"nowrap\">{$info['download_favs']}</td>
		</tr>
	</table>
		<table cellspacing=\"1\" class=\"ipbtable\" width=\"100%\">";
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// <ips:no_favs:desc::trigger:>
//===========================================================================
function no_favs() {
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= "<tr>
		<td class=\"row1\">{ipb.lang['no_favs']}</td>
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