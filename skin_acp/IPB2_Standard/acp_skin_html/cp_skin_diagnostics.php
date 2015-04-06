<?php

class cp_skin_diagnostics {

var $ipsclass;

function dbchecker_javascript()
{
$IPBHTML = "";
$IPBHTML = <<<EOF

<script type='text/javascript'>
var all_queries = new Array();

function fix_all_dberrors()
{
	var url = ipb_var_base_url + 'section=help&act=diag&code=dbchecker';
	
	for( var i=0; i<all_queries.length; i++ )
	{
		url += '&query'+i+'='+all_queries[i];
	}
	
	window.location = url;
	return false;
}
</script>
EOF;

return $IPBHTML;
}


function dbindexer_javascript()
{
$IPBHTML = "";
$IPBHTML = <<<EOF

<script type='text/javascript'>
var all_queries = new Array();

function fix_all_dberrors()
{
	var url = ipb_var_base_url + 'section=help&act=diag&code=dbindex';
	
	for( var i=0; i<all_queries.length; i++ )
	{
		url += '&query'+i+'='+all_queries[i];
	}
	
	window.location = url;
	return false;
}
</script>
EOF;

return $IPBHTML;
}


}


?>