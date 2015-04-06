<?php
include "include.php";

mysql_connect(H, UN, P);
mysql_select_db(DN);

if(isset($_COOKIE["member_id"]))
{
?>
<frameset rows="0%, 0%,*" border="0">
    <frame name="adi" src="out.php" marginwidth="10" marginheight="10" scrolling="no" frameborder="0" noresize>
    <frame name="out" src="add.php?txt=<?Php echo(INTOIN); ?>&idw=" marginwidth="10" marginheight="10" scrolling="no" frameborder="0" noresize>
    <frameset rows="85%,*" border="0">
        <frameset cols="80%,*" border="0">
            <frame name="tv" src="tv.php" marginwidth="10" marginheight="10" scrolling="yes" frameborder="0" noresize>
            <frame name="into" src="into.php" marginwidth="10" marginheight="10" scrolling="yes" frameborder="0" noresize>
        </frameset>
        <frame name="send" src="send.php" marginwidth="10" marginheight="10" scrolling="no" frameborder="0" noresize>
    </frameset>
</frameset>
<?php
}
else
{
print NOWID;
}
?>