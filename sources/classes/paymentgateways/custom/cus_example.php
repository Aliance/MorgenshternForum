<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Subscription Custom Module (EXAMPLE)
|   > Module written by Matt Mecham
|   > Date started: 21th August 2003
|
| NEVER CALL EXIT IN THIS MODULE - IT MUST RETURN BACK TO
| IPB TO CONTINUE THE SUBSCRIPTION PROCESS OR IT WILL FAIL
+--------------------------------------------------------------------------
*/

//---------------------------------------
// Security check
//---------------------------------------
		
if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
    exit();
}

class customsubs {

	//---------------------------------------
	// Subscription Paid
	//---------------------------------------
	
	function subs_paid($sub_array, $member=array(), $trx_id="")
	{
	
	
	}
	
	
	//---------------------------------------
	// Subscription Failed / Cancelled
	//---------------------------------------
	
	function subs_failed($sub_array, $member=array(), $trx_id="")
	{
	
	
	}
	
	
	
	
	
	
}

 
?>