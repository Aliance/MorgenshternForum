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
 <div class='tablesubheader'>Информация</div>
 <div class='tablerow1'>
 Эта секция создана для любых компонентов, таких как Invision Gallery, Invision Chat и Invision Blog.
 </div>
</div>

EOF;

//--endhtml--//
return $IPBHTML;
}



}

?>