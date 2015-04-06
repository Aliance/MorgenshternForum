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
|                  http://www.ibresource.ru/license
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
|   > Post Handler
|   > Module written by Matt Mecham
|   > Date started: Wednesday 9th March 2005 (15:23)
|
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
    exit();
}

class han_post
{
	# Global
	var $ipsclass;
	var $class_post;
	
	# Method
	var $method;
	
	# Forum
	var $forum;
	var $md5_check;
	
	var $modules = array();
	
	var $obj = array();
	
    /*-------------------------------------------------------------------------*/
    // INIT
    /*-------------------------------------------------------------------------*/
    
    function init()
    {
    	//-----------------------------------------
    	// INIT
    	//-----------------------------------------
    	
    	$class = "";
    	
    	//-----------------------------------------
    	// Which class
    	//-----------------------------------------
    	
    	switch( $this->method )
    	{
    		case 'new':
    			$class = 'class_post_new.php';
    			break;
    		case 'reply':
    			$class = 'class_post_reply.php';
    			break;
    		case 'poll':
    			$class = 'class_post_poll.php';
    			break;
    		case 'edit':
    			$class = 'class_post_edit.php';
    			break;
    		
    		default:
    			$class = 'class_post_new.php';
    	}
    	
		//-----------------------------------------
		// Load classes
		//-----------------------------------------
		
		require_once( ROOT_PATH . 'sources/classes/post/class_post.php' );
		require_once( ROOT_PATH . 'sources/classes/post/'.$class );
		
		$this->class_post             =  new post_functions();
		$this->class_post->ipsclass   =& $this->ipsclass;
		$this->class_post->forum      =& $this->forum;
		$this->class_post->md5_check  = $this->md5_check;
		$this->class_post->obj        = $this->obj;
		$this->class_post->modules    = $this->modules;
		
		//-----------------------------------------
		// Init class
		//-----------------------------------------
		
        $this->class_post->main_init();
    }
    
    /*-------------------------------------------------------------------------*/
    // Mode: Save post in DB
    /*-------------------------------------------------------------------------*/
  
  	function show_form()
  	{
  		return $this->class_post->show_form();
  	}
  	
    /*-------------------------------------------------------------------------*/
    // Mode: Save post in DB
    /*-------------------------------------------------------------------------*/
  
  	function save_post()
  	{
  		return $this->class_post->save_post();
  	}

    /*-------------------------------------------------------------------------*/
    // Mode: Process
    /*-------------------------------------------------------------------------*/
  
  	function process_post()
  	{
  		return $this->class_post->process_post();
  	}

	
	
}

?>