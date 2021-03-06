<?php
include "include.php";

header("Pragma: no-cache");
header("Cache-control: no-cache");

?>
<html>
<head>
  <title></title>
<STYLE type=text/css>
<!--
HTML {
	OVERFLOW-X: auto
}
BODY {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-SIZE: 11px; PADDING-BOTTOM: 0px; MARGIN: 0px; COLOR: #000; PADDING-TOP: 0px; FONT-FAMILY: Verdana, Tahoma, Arial, sans-serif; BACKGROUND-COLOR: #fff; TEXT-ALIGN: center
}
TABLE {
	FONT-SIZE: 11px; COLOR: #000; FONT-FAMILY: Verdana, Tahoma, Arial, sans-serif
}
TR {
	FONT-SIZE: 11px; COLOR: #000; FONT-FAMILY: Verdana, Tahoma, Arial, sans-serif
}
TD {
	FONT-SIZE: 11px; COLOR: #000; FONT-FAMILY: Verdana, Tahoma, Arial, sans-serif
}
#ipbwrapper {
	MARGIN-LEFT: auto; WIDTH: 95%; MARGIN-RIGHT: auto; TEXT-ALIGN: left
}
A:link {
	COLOR: #000; TEXT-DECORATION: underline
}
A:visited {
	COLOR: #000; TEXT-DECORATION: underline
}
A:active {
	COLOR: #000; TEXT-DECORATION: underline
}
A:hover {
	COLOR: #465584; TEXT-DECORATION: underline
}
FIELDSET.search {
	PADDING-RIGHT: 6px; PADDING-LEFT: 6px; PADDING-BOTTOM: 6px; LINE-HEIGHT: 150%; PADDING-TOP: 6px
}
LABEL {
	CURSOR: pointer
}
FORM {
	DISPLAY: inline
}
IMG {
	BORDER-RIGHT: 0px; BORDER-TOP: 0px; VERTICAL-ALIGN: middle; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px
}
IMG.attach {
	BORDER-RIGHT: #eef2f7 2px outset; PADDING-RIGHT: 2px; BORDER-TOP: #eef2f7 2px outset; PADDING-LEFT: 2px; PADDING-BOTTOM: 2px; BORDER-LEFT: #eef2f7 2px outset; PADDING-TOP: 2px; BORDER-BOTTOM: #eef2f7 2px outset
}
OPTION.cat {
	FONT-WEIGHT: bold
}
OPTION.sub {
	FONT-WEIGHT: bold; COLOR: #555
}
.caldate {
	PADDING-RIGHT: 4px; PADDING-LEFT: 4px; FONT-WEIGHT: bold; FONT-SIZE: 11px; PADDING-BOTTOM: 4px; MARGIN: 0px; COLOR: #777; PADDING-TOP: 4px; BACKGROUND-COLOR: #dfe6ef; TEXT-ALIGN: right
}
.warngood {
	COLOR: green
}
.warnbad {
	COLOR: red
}
#padandcenter {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; PADDING-BOTTOM: 14px; MARGIN-LEFT: auto; MARGIN-RIGHT: auto; PADDING-TOP: 14px; TEXT-ALIGN: center
}
#profilename {
	FONT-WEIGHT: bold; FONT-SIZE: 28px
}
#calendarname {
	FONT-WEIGHT: bold; FONT-SIZE: 22px
}
#photowrap {
	PADDING-RIGHT: 6px; PADDING-LEFT: 6px; PADDING-BOTTOM: 6px; PADDING-TOP: 6px
}
#phototitle {
	FONT-SIZE: 24px; BORDER-BOTTOM: black 1px solid
}
#photoimg {
	MARGIN-TOP: 15px; TEXT-ALIGN: center
}
#ucpmenu {
	BORDER-RIGHT: #345487 1px solid; BORDER-TOP: #345487 1px solid; BORDER-LEFT: #345487 1px solid; WIDTH: 22%; LINE-HEIGHT: 150%; BORDER-BOTTOM: #345487 1px solid; BACKGROUND-COLOR: #f5f9fd
}
#ucpmenu P {
	PADDING-RIGHT: 5px; PADDING-LEFT: 9px; PADDING-BOTTOM: 6px; MARGIN: 0px; PADDING-TOP: 2px
}
#ucpcontent {
	BORDER-RIGHT: #345487 1px solid; BORDER-TOP: #345487 1px solid; BORDER-LEFT: #345487 1px solid; WIDTH: auto; LINE-HEIGHT: 150%; BORDER-BOTTOM: #345487 1px solid; BACKGROUND-COLOR: #f5f9fd
}
#ucpcontent P {
	PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; MARGIN: 0px; PADDING-TOP: 10px
}
#ipsbanner {
	RIGHT: 5%; POSITION: absolute; TOP: 1px
}
#logostrip {
	BORDER-RIGHT: #345487 1px solid; PADDING-RIGHT: 0px; BORDER-TOP: #345487 1px solid; PADDING-LEFT: 0px; BACKGROUND-IMAGE: url(img/header_tile.gif); PADDING-BOTTOM: 0px; MARGIN: 0px; BORDER-LEFT: #345487 1px solid; PADDING-TOP: 0px; BORDER-BOTTOM: #345487 1px solid; BACKGROUND-COLOR: #3860bb
}
#submenu {
	BORDER-RIGHT: #bcd0ed 1px solid; BORDER-TOP: #bcd0ed 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 10px; MARGIN: 3px 0px; BORDER-LEFT: #bcd0ed 1px solid; COLOR: #3a4f6c; BORDER-BOTTOM: #bcd0ed 1px solid; BACKGROUND-COLOR: #dfe6ef
}
#submenu A:link {
	FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #3a4f6c; TEXT-DECORATION: none
}
#submenu A:visited {
	FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #3a4f6c; TEXT-DECORATION: none
}
#submenu A:active {
	FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #3a4f6c; TEXT-DECORATION: none
}
#userlinks {
	BORDER-RIGHT: #c2cfdf 1px solid; BORDER-TOP: #c2cfdf 1px solid; BORDER-LEFT: #c2cfdf 1px solid; BORDER-BOTTOM: #c2cfdf 1px solid; BACKGROUND-COLOR: #f0f5fa
}
#navstrip {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: bold; PADDING-BOTTOM: 6px; PADDING-TOP: 6px
}
.pformstrip {
	PADDING-RIGHT: 7px; MARGIN-TOP: 1px; PADDING-LEFT: 7px; FONT-WEIGHT: bold; PADDING-BOTTOM: 7px; COLOR: #3a4f6c; PADDING-TOP: 7px; BACKGROUND-COLOR: #d1dceb
}
.pformleft {
	BORDER-RIGHT: #c2cfdf 1px solid; PADDING-RIGHT: 6px; BORDER-TOP: #c2cfdf 1px solid; MARGIN-TOP: 1px; PADDING-LEFT: 6px; PADDING-BOTTOM: 6px; WIDTH: 25%; PADDING-TOP: 6px; BACKGROUND-COLOR: #f5f9fd
}
.pformleftw {
	BORDER-RIGHT: #c2cfdf 1px solid; PADDING-RIGHT: 6px; BORDER-TOP: #c2cfdf 1px solid; MARGIN-TOP: 1px; PADDING-LEFT: 6px; PADDING-BOTTOM: 6px; WIDTH: 40%; PADDING-TOP: 6px; BACKGROUND-COLOR: #f5f9fd
}
.pformright {
	PADDING-RIGHT: 6px; BORDER-TOP: #c2cfdf 1px solid; MARGIN-TOP: 1px; PADDING-LEFT: 6px; PADDING-BOTTOM: 6px; PADDING-TOP: 6px; BACKGROUND-COLOR: #f5f9fd
}
.signature {
	FONT-SIZE: 10px; COLOR: #339; LINE-HEIGHT: 150%
}
.postdetails {
	FONT-SIZE: 10px
}
.postcolor {
	FONT-SIZE: 12px; LINE-HEIGHT: 160%
}
.normalname {
	FONT-WEIGHT: bold; FONT-SIZE: 12px; COLOR: #003
}
.normalname A:link {
	FONT-SIZE: 12px
}
.normalname A:visited {
	FONT-SIZE: 12px
}
.normalname A:active {
	FONT-SIZE: 12px
}
.unreg {
	FONT-WEIGHT: bold; FONT-SIZE: 11px; COLOR: #900
}
.post1 {
	BACKGROUND-COLOR: #f5f9fd
}
.post2 {
	BACKGROUND-COLOR: #eef2f7
}

.row1 {
	BACKGROUND-COLOR: #30557A
}
.row2 {
	BACKGROUND-COLOR: #dfe6ef
}
.row3 {
	BACKGROUND-COLOR: #eef2f7
}
.row4 {
	BACKGROUND-COLOR: #e4eaf2
}
.darkrow1 {
	COLOR: #4c77b6; BACKGROUND-COLOR: #c2cfdf
}
.darkrow2 {
	COLOR: #3a4f6c; BACKGROUND-COLOR: #bcd0ed
}
.darkrow3 {
	COLOR: #3a4f6c; BACKGROUND-COLOR: #d1dceb
}

.titlemedium A:link {
	COLOR: #3a4f6c; TEXT-DECORATION: underline
}
.titlemedium A:visited {
	COLOR: #3a4f6c; TEXT-DECORATION: underline
}
.titlemedium A:active {
	COLOR: #3a4f6c; TEXT-DECORATION: underline
}
.maintitle A:link {
	COLOR: #fff; TEXT-DECORATION: none
}
.maintitle A:visited {
	COLOR: #fff; TEXT-DECORATION: none
}
.maintitle A:active {
	COLOR: #fff; TEXT-DECORATION: none
}
.maintitle A:hover {
	TEXT-DECORATION: underline
}
.plainborder {
	BORDER-RIGHT: #345487 1px solid; BORDER-TOP: #345487 1px solid; BORDER-LEFT: #345487 1px solid; BORDER-BOTTOM: #345487 1px solid; BACKGROUND-COLOR: #f5f9fd
}
.tableborder {
	BORDER-RIGHT: #345487 1px solid; PADDING-RIGHT: 0px; BORDER-TOP: #345487 1px solid; PADDING-LEFT: 0px; PADDING-BOTTOM: 0px; MARGIN: 0px; BORDER-LEFT: #345487 1px solid; PADDING-TOP: 0px; BORDER-BOTTOM: #345487 1px solid; BACKGROUND-COLOR: #fff
}
.tablefill {
	BORDER-RIGHT: #345487 1px solid; PADDING-RIGHT: 6px; BORDER-TOP: #345487 1px solid; PADDING-LEFT: 6px; PADDING-BOTTOM: 6px; BORDER-LEFT: #345487 1px solid; PADDING-TOP: 6px; BORDER-BOTTOM: #345487 1px solid; BACKGROUND-COLOR: #f5f9fd
}
.en {
	COLOR: #ffffff; BACKGROUND-COLOR: #0000ff
}
-->
</STYLE>
<META http-equiv=Content-Type content="text/html; charset=windows-1251">

<script>
<!--
dom = (document.getElementById) ? true : false;
nn4 = (document.layers) ? true : false;
ie = (document.all) ? true : false;
ie4 = ie && !dom;

function getRef(id) {
  if (dom) return document.getElementById(id);
  if (ie4) return document.all[id];
  if (nn4) return document.layers[id];
}

var can=0;

function cantrue()
{
 can=0;
}

function send()
{
if(can==0){
can=1;
parent.out.location.replace("add.php?txt= "+getRef("txt").value+"&idw="+getRef("idw").value);
getRef("txt").value = "";
document.focus();
getRef("txt").focus();
setTimeout("cantrue();", <?php echo(FLUD); ?>*1000);
};
}

function senden()
{
if(event.keyCode==13)
if(can==0){
can=1;
parent.out.location.replace("add.php?txt= "+getRef("txt").value+"&idw="+getRef("idw").value);
getRef("txt").value = "";
document.focus();
getRef("txt").focus();
setTimeout("cantrue();", <?php echo(FLUD); ?>*1000);
};
}


function tofor(t) {
getRef("txt").value=getRef("txt").value+" [::"+t+"::] ";
document.focus();
getRef("txt").focus();
}

-->
</script>
</head>
<body>
<table width="100%" height="100%" cellspacing="0" cellpadding="3" border="0">
<tr><td class=row1 align="left" height="1" valign="top">
</td></tr>
<tr><td class=row3 align="left" valign="top">

<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td valign="top" align="left">

<input type="text" name="idw" value="" size="10" maxlength="40" onclick="javascript:getRef('idw').value='';">&nbsp;
<input type="text" name="txt" value="" size="100" maxlength="150" onkeypress="senden();">&nbsp;
<a href="javascript:send();">OK</a><br>

	</td>
    <td align="right" valign="top">
<a href="javascript:location.href='send.php';"><?php echo(ANOVER); ?></a>&nbsp;&nbsp;&nbsp;
<?php
/*
function init($c, $k)
{
$r=false;
foreach($k as $v)if($v==$c)$r=true;
return $r;
}


$kollil=-1;
$d=opendir(LIL);
while(($e=readdir($d))!==false)if($e != ".")if($e != "..")if($e != "Thumbs.db")$kollil++;

$klo=array();
$n=0;
$i=0;
do{
$n=$i*12.5 * 10000000;
mt_srand(time() + (double)microtime() * $n);
do $civ = mt_rand(0, $kollil); while(init($civ, $klo));
$klo[$i]=$civ;
$n++;
$i++;
}while($i<10);

$nklo=array();
$d=opendir(LIL);
$i=0;
$nrl=0;
while(($e=readdir($d))!==false)if($e != ".")if($e != "..")if($e != "Thumbs.db"){
$nklo[$i]=$e;
$i++;
};

$ib=0;
for($i=0; $i<10; $i++){
if($ib==5){$ib=0; echo "<br>";}
print "<img onclick=\"tofor('".str_replace(".gif", "", $nklo[$klo[$i]])."')\" src=\"lil/".$nklo[$klo[$i]]."\" border=0>\n";
$ib++;
}
*/
?>
	</td>
  </tr>

</table>
</td></tr></table>
</body>
</html>