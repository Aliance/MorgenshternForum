<?php

/*
+--------------------------------------------------------------------------
|   Invision Blog Module
|   =============================================
|   by Remco Wilting
|   (c) 2001 - 2006 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.vuboys.nl
|   Email: remco@invisionboard.com
+--------------------------------------------------------------------------
|
|   > Blog AdminCP script wrapper
|   > Script written by Remco Wilting
|   > Date started: 27st August 2004
|   > Module version: 0.1.001
|
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>������������ �����</h1>�� �� ������ ������� � ����� ����� ��������. ���� �� ������� ��������� �����, �� ������ �������� ��� ��������������� �����.";
	exit();
}


class ad_blog {

	var $base_url;

	function auto_run()
	{
		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------
		
		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );
		
		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}

		//-----------------------------------------
		// Do some set up
		//-----------------------------------------
		
		if ( ! @is_dir( ROOT_PATH.'/modules/blog' ) )
		{
			$this->ipsclass->admin->show_inframe("http://external.ipslink.com/ipboard22/landing/?p=blog");
		}
		else
		{
			require ROOT_PATH.'modules/blog/admin/ad_blog.php';
			
			$adblog           =  new ad_blog_plugin();
			$adblog->ipsclass =& $this->ipsclass;
            $adblog->run_me();
		}
		
	}

}

?>