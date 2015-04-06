<?php
include "include.php";

mysql_connect(H, UN, P);
mysql_select_db(DN);

header("Pragma: no-cache");
header("Cache-control: no-cache");


if(isset($_COOKIE["member_id"]))
{
$r=mysql_query("select * from ".IPB_PREF."members where id=".$_COOKIE["member_id"]."");
$g=mysql_fetch_array($r);
$name=$g["name"];
$id=$_COOKIE["member_id"];

mysql_query("create table chat_txt(id text, time text, who text, who_id text, txt text)");
?>
<html>
<head>
<meta http-equiv="refresh" content="<?php echo(TIME_OUT); ?>; url=out.php?ok=ok">
<script>
function done()
{
window.parent.tv.document.all("iepag").innerHTML=document.body.innerHTML;
window.parent.tv.scroll(0,0);
}

</script>
</head>
<body
<?php
if(isset($ok))echo "onload=\"done();\"";
echo ">\n";
//chat_txt(id text, time text, who text, who_id text, txt text)
$r=mysql_query("select * from chat_txt ORDER BY time DESC");
while($g=mysql_fetch_array($r))
if(date("d", $g["time"])==date("d", time()) &&
   date("H", $g["time"])==date("H", time()) &&
   date("i", $g["time"])+SAVE_TIME>date("i",time()))
{

preg_match_all("%\[::([^::\]]*)::\]%",$g["txt"],$Tof);
$i=0;
foreach(($Tof[1]) as $v){
if(file_exists("lil/".$v.".gif")){
if($i<K_WIDE)
$g["txt"]=str_replace("[::".$v."::]", "<img src=\"lil/".$v.".gif\">", $g["txt"]);
else
$g["txt"]=str_replace("[::".$v."::]", "", $g["txt"]);
$i+=1;
}
}

$g["txt"]=str_replace("(".PON.")", "<font color=\"#CA0000\"><b>", $g["txt"]);
$g["txt"]=str_replace("(/".PON.")", "</b></font>", $g["txt"]);

if(strpos($g["txt"], "<font color=\"#CA0000\"><b>"))
if(!(strpos($g["txt"], "</b></font>")))$g["txt"] .= "</b></font>";

$toyou=0;
if(strpos($g["txt"], $name))$toyou=1;
if($g["id"]==''||$g["id"]==$name||$g["who"]==$name){
if($toyou==1||$g["id"]==$name)echo "<font color=\"#008000\">";
print "<a href=\"javascript:addm(' ".$g["who"].", ');\"><b>".$g["who"]."</b></a>";
if($g["id"]==$name)echo "&nbsp;".ONLY;
elseif($g["id"]!=''&&$g["who"]==$name)echo "&nbsp;".ONLY;
if($g["id"]!=''&&$g["who"]==$name)echo "&nbsp;".$g["id"]."";
elseif($toyou==1||$g["id"]==$name)echo "&nbsp;".YOU."</font>";

print ":&nbsp;".$g["txt"]."\n";
print "<hr align=\"center\" width=\"100%\" size=\"1\" noshade color=\"#FFFFFF\">\n";
                                 }
flush();
}
else
{
mysql_query("delete from chat_txt where time='".$g["time"]."'");
};
?>
</body>
</html>
<?php
};
?>