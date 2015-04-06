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

?>
<html>
<body>
<?php
//chat_txt(id text, time text, who text, who_id text, txt text)
$txt=substr($txt, 0, 150);
$txt=htmlspecialchars($txt);
mysql_query("insert into chat_txt(id , time , who , who_id , txt ) values('".$idw."', '".time()."', '".$name."', '".$id."', '".$txt."')");
?>
ok
</body>
</html>
<?php
};
?>