<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2006 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Tuesday 27th of March 2007 07:00:16 AM
|   Release: 936d62a249c0dc8fd81438cdbc911b98
|   Licence Info: http://www.invisionboard.com/?license
|		          http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > IPS Remote Call thingy
|   > Module written by Matt Mecham
|   > Date started: 17th October 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}


class ad_ips {

	var $base_url;
	
	var $colours = array();
	
	var $url = "http://www.invisionboard.com/acp/";
	
	var $version = "1.1";

	function auto_run()
	{
		//-----------------------------------------
		
		switch($this->ipsclass->input['code'])
		{
		
			case 'news':
				$this->news();
				break;
				
			case 'updates':
				$this->updates();
				break;
				
			case 'docs':
				$this->docs();
				break;
				
			case 'support':
				$this->support();
				break;
			
			case 'host':
				$this->host();
				break;
				
			case 'purchase':
				$this->purchase();
				break;
				
			//-----------------------------------------
			default:
				exit();
				break;
		}
		
	}
	


	
	function news()
	{
		@header("Location: ".$this->url."?news");
		exit();
	}
	
	function updates()
	{
		//@header("Location: ".$this->url."?updates&version=".$this->version);
		@header("Location: ".$this->url."?updates");
		exit();
	}
	
	function docs()
	{
		@header("Location: http://www.invisionpower.com/documentation/showdoc.php");
		exit();
	}
	
	function support()
	{
		@header("Location: ".$this->url."?support");
		exit();
	}
	
	function host()
	{
		@header("Location: ".$this->url."?host");
		exit();
	}
	
	function purchase()
	{
		@header("Location: ".$this->url."?purchase");
		exit();
	}
}
?>