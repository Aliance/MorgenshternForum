<?php

class cp_skin_components {

var $ipsclass;

//===========================================================================
// Member: validating
//===========================================================================
function welcome_page() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tablesubheader'>����������</div>
 <div class='tablerow1'>
 ��� ������ ������� ��� ����� �����������, ����� ��� Invision Gallery, Invision Chat � Invision Blog.
 </div>
</div>

EOF;

//--endhtml--//
return $IPBHTML;
}



}

?>