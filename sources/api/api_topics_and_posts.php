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
|                 http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD IS NOT FREE / OPEN SOURCE!
+---------------------------------------------------------------------------
|   INVISION POWER BOARD НЕ ЯВЛЯЕТСЯ БЕСПЛАТНЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ!
|   Права на ПО принадлежат Invision Power Services
|   Права на перевод IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-10-19 17:06:53 -0500 (Thu, 19 Oct 2006) $
|   > $Revision: 661 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > API: Topics and Posts
|   > Module written by Matt Mecham
|   > Date started: Wednesday 20th July 2005 (14:48)
|
+--------------------------------------------------------------------------
*/

/**
* API: Создание сообщений и тем
*
* ВНИМАНИЕ: Методы данного API НЕ ПРОВЕРЯЮТ права пользователей
* По этому при неправильном использовании методов возможны ответы
* пользователя в закрытые темы, а так же создание новых тем и 
* сообщений в форумах, к которым пользователь нет имеет доступа.
* Для избежания таких эффектов ОБЯЗАТЕЛЬНО проверяйте права 
* пользователя прежде чем вызывать метод.
* 
* ПРИМЕР ИСПОЛЬЗОВАНИЯ
* <code>
* $api = new api_topics_and_posts();
* $api->ipsclass =& $this->ipsclass;
* // ДОБАВЛЕНИЕ ОТВЕТА
* $api->set_author_by_name('matt');
* $api->set_post_content("<b>Hello World!</b> :D");
* $api->set_topic_id( 100 );
* # По-умолчанию флаг show_signature всегда 1, здесь добавлено для полноты формы
* $api->post_settings['show_signature'] = 1;
* # Опционально вы можете отключить пересчет тем, сообщений и статистики
* # достаточно расскоментировать строчку ниже
* # $api->delay_rebuild = 1;
* $api->create_new_reply();
* // СОЗДАНИЕ НОВОЙ ТЕМЫ
* $api->set_author_by_name('matt');
* $api->set_post_content("<b>Hello World!</b> :D");
* $api->set_forum_id( 10 );
* $api->set_topic_title('Hello World');
* $api->set_topic_description('I am the description');
* $api->set_topic_state('open');
* $api->create_new_topic();
* </code>
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author		Matt Mecham
* @copyright	Invision Power Services, Inc.
* @version		2.1
*/

if ( ! defined( 'IPS_API_PATH' ) )
{
	/**
	* Define classes path
	*/
	define( 'IPS_API_PATH', dirname(__FILE__) ? dirname(__FILE__) : '.' );
}

if ( ! class_exists( 'api_core' ) )
{
	require_once( IPS_API_PATH.'/api_core.php' );
}

/**
* API: Создание сообщений и тем
*
* Класс предоставляет все методы API для создания тем и сообщений.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Matt Mecham
* @version		2.1
* @since		2.1.0
*/
class api_topics_and_posts extends api_core
{
	/**
	* Информация об авторе для БД
	*
	* @var array
	*/
	var $author = array();
	
	/**
	* Содержимое сообщения
	*
	* @var string
	*/
	var $post_content = "";
	
	/**
	* ID темы
	*
	* @var integer
	*/
	var $topic_id = "";
	
	/**
	* ID форума
	*
	* @var integer
	*/
	var $forum_id = "";
	
	/**
	* Информация о теме для БД
	*
	* @var array
	*/
	var $topic = array();
	
	/**
	* Информация о форуме 
	*
	* @var array
	*/
	var $forum = array();
	
	/**
	* Объект редактора
	*
	* @var object
	*/
	var $editor;
	
	/**
	* Объект обработчика отправки сообщений/тем
	*
	* @var object
	*/
	var $post;
	
	/**
	* Объект мейлера системы
	*
	* @var object
	*/
	var $email;	
	
	/**
	* Объект парсера сообщений
	*
	* @var object
	*/
	var $parser;
	
	/**
	* Объект модераторских функций
	*
	* @var object
	*/
	var $func_mod;
	
	/**
	* Флаг загруженности классов
	*
	* @var 	integer	boolean
	*/
	var $classes_loaded = 0;
	
	/**
	* Флаг отложенного обновления кеша
	*
	* Если флаг установлен в 1, вам будет необходимо
    * самим обновить все счетчики тем, форумов и статистики.
	*
	* @var 	integer	boolean
	*/
	var $delay_rebuild = 0;
	
	/**
	* Настройки сообщения
	*
	* @var	array	show_signature, show_emoticons, post_icon_id, is_invisible, post_date, ip_address, parse_html
	*/
	var $post_settings = array( 'show_signature' => 1,
								'show_emoticons' => 1,
								'post_icon_id'   => 0,
								'is_invisible'   => 0,
								'post_date'      => 0,
								'ip_address'     => 0,
								'post_htmlstate' => 0,
								'parse_html'     => 0  );
								
	/**
	* Настройки темы
	*
	* @var	array	topic_date, post_icon_id
	*/
	var $topic_settings = array( 'topic_date'     => 0,
								 'post_icon_id'   => 0 );
	
	/**
	* Название темы
	*
	* @var string
	*/
	var $topic_title;
	
	/**
	* Описание темы
	*
	* @var string
	*/
	var $topic_desc;
	
	/**
	* Статус темы  (открыта/закрыта)
	*
	* @var string (open or closed)
	*/
	var $topic_state = 'open';
	
	/**
	* Важность темы (прикреплена/нет)
	*
	* @var integer
	*/
	var $topic_pinned = 0;
	
	/**
	* Скрытость темы (скрыта/видима)
	*
	* @var integer
	*/
	var $topic_invisible = 0;
	
	/*-------------------------------------------------------------------------*/
	// Создание новой темы
	/*-------------------------------------------------------------------------*/
	/**
	* Сохраняет новую тему и сообщение
	*
	* @return	void 
	*/
	function create_new_topic()
	{
		//-------------------------------
		// Got anything?
		//-------------------------------
		
		if ( ! $this->author OR ! $this->post_content OR ! $this->forum_id OR ! $this->topic_title )
		{
			$this->api_error[]  = 'not_all_data_ready';
			$this->post_content = '';
			return FALSE;
		}
		
		$this->api_tap_load_classes();
		
		$this->topic_invisible = $this->topic_invisible ? $this->topic_invisible : $this->post_settings['is_invisible'];
		
		//-------------------------------
		// Attempt to format
		//-------------------------------
		
		$this->topic = array(
							  'title'            => $this->ipsclass->parse_clean_value( $this->topic_title ),
							  'description'      => $this->ipsclass->parse_clean_value( $this->topic_desc ),
							  'state'            => $this->topic_state,
							  'posts'            => 0,
							  'starter_id'       => $this->author['id'],
							  'starter_name'     => $this->author['members_display_name'],
							  'start_date'       => $this->topic_settings['topic_date'] ? $this->topic_settings['topic_date'] : time(),
							  'last_poster_id'   => $this->author['id'],
							  'last_poster_name' => $this->author['members_display_name'],
							  'last_post'        => $this->topic_settings['topic_date'] ? $this->topic_settings['topic_date'] : time(),
							  'icon_id'          => $this->topic_settings['post_icon_id'],
							  'author_mode'      => 1,
							  'poll_state'       => 0,
							  'last_vote'        => 0,
							  'views'            => 0,
							  'forum_id'         => $this->forum_id,
							  'approved'         => $this->topic_invisible ? 0 : 1,
							  'pinned'           => $this->topic_pinned );
		
		//-------------------------------
		// Insert topic
		//-------------------------------
		
		$this->ipsclass->DB->do_insert( 'topics', $this->topic );
					
		$this->topic_id     = $this->ipsclass->DB->get_insert_id();
		$this->topic['tid'] = $this->topic_id;
		
		//-----------------------------------------
		// Re-do member info
		//-----------------------------------------
		
		$temp_store = $this->ipsclass->member;
		
		$this->ipsclass->member = $this->author;
		
		//-----------------------------------------
		// Tracker?
		//-----------------------------------------
		
		$this->post->forum_tracker( $this->topic['forum_id'], $this->topic_id, $this->topic['title'], $this->forum['name'], $this->post_content );
		
		//-------------------------------
		// Add post
		//-------------------------------
		
		$return_val = $this->create_new_reply();
		
		$this->ipsclass->member = $temp_store;
		
		return $return_val;
	}
	
	/*-------------------------------------------------------------------------*/
	// Создание нового ответа в тему
	/*-------------------------------------------------------------------------*/
	/**
	* Создает новую тему
	*
	* @return	void 
	*/
	function create_new_reply()
	{
		//-------------------------------
		// Got anything?
		//-------------------------------
		
		if ( ! $this->author OR ! $this->post_content OR ! $this->topic_id )
		{
			$this->api_error[]  = 'no_post_content';
			$this->post_content = '';
			return FALSE;
		}
		
		$this->api_tap_load_classes();
		
		//-------------------------------
		// Attempt to format
		//-------------------------------
		
		$post = array(
						'author_id'      => $this->author['id'],
						'use_sig'        => $this->post_settings['show_signature'],
						'use_emo'        => $this->post_settings['show_emoticons'],
						'ip_address'     => $this->post_settings['ip_address'] ? $this->post_settings['ip_address'] : $this->ipsclass->ip_address,
						'post_date'      => $this->post_settings['post_date']  ? $this->post_settings['post_date']  : time(),
						'icon_id'        => $this->post_settings['post_icon_id'],
						'post'           => $this->post_content,
						'author_name'    => $this->author['members_display_name'],
						'topic_id'       => $this->topic_id,
						'queued'         => $this->post_settings['is_invisible'],
						'post_htmlstate' => $this->post_settings['post_htmlstate'],
						'post_key'       => md5( time() ),
					 );
					 
		//-----------------------------------------
		// Add post to DB
		//-----------------------------------------
		
		$this->ipsclass->DB->do_insert( 'posts', $post );
		
		//-----------------------------------------
		// Rebuild topic
		//-----------------------------------------
		
		$this->func_mod->rebuild_topic( $this->topic_id, 0 );
		
		//-----------------------------------------
		// Increment member?
		//-----------------------------------------
		
		if ( $this->forum['inc_postcount'] )
		{
			$this->ipsclass->DB->simple_update( 'members', 'posts=posts+1', 'id='.intval( $this->author['id'] ) );
			$this->ipsclass->DB->simple_exec();
		}
		
		//-----------------------------------------
		// Re-do member info
		//-----------------------------------------
		
		$temp_store = $this->ipsclass->member;
		
		$this->ipsclass->member = $this->author;
		
		//-----------------------------------------
		// Tracker? (Only run when not creating topic)
		//-----------------------------------------
		
		if ( ! $this->topic_title )
		{
			$this->post->topic_tracker( $this->topic_id, $this->post_content, $this->author['members_display_name'], time() );
		}
		
		//-----------------------------------------
		// Rebuild
		//-----------------------------------------
		
		if ( ! $this->delay_rebuild )
		{
			$this->tap_rebuild_forum( $this->topic['forum_id'] );
			$this->tap_rebuild_stats();
		}
		
		$this->ipsclass->member = $temp_store;
		
		return TRUE;
	}
		
	/*-------------------------------------------------------------------------*/
	// Установка названия темы
	/*-------------------------------------------------------------------------*/
	/**
	* Устанавливает название для создаваемой темы
	*
	* @param	string	Название темы
	* @return	void 
	*/
	function set_topic_title( $data )
	{
		$this->topic_title = $data;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка описания темы
	/*-------------------------------------------------------------------------*/
	/**
	* Устанавливает описание для создаваемой темы
	*
	* @param	string	Описание темы
	* @return	void 
	*/
	function set_topic_description( $data )
	{
		$this->topic_desc = $data;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка статуса темы
	/*-------------------------------------------------------------------------*/
	/**
	* Устанавливает статуса для создаваемой темы
	*
	* @param	string	Статус (open/closed)
	* @return	void 
	*/
	function set_topic_state( $data )
	{
		$this->topic_state = $data;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка важности темы
	/*-------------------------------------------------------------------------*/
	/**
	* Устанавливает важность для создаваемой темы
	*
	* @param	string	Прикреплена / не прикреплена (1 / 0)
	* @return	void 
	*/
	function set_topic_pinned( $data )
	{
		$this->topic_pinned = intval($data);
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка видимости темы
	/*-------------------------------------------------------------------------*/
	/**
	* Устанавливает видимость для создаваемой темы
	*
	* @param	string	Тема опубликована / не опубликована (0 / 1)
	* @return	void 
	*/
	function set_topic_invisible( $data )
	{
		$this->topic_invisible = intval($data);
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка содержимого сообщения
	/*-------------------------------------------------------------------------*/
	/**
	* Устанавливает содержимое сообщения для создаваемой темы/сообщения
	*
	* @param	string	содержимое сообщения
	* @return	boolean	инициализация $this->post_content
	*/
	function set_post_content( $post )
	{
		//-------------------------------
		// Got anything?
		//-------------------------------
		
		if ( ! $post )
		{
			$this->api_error[]  = 'no_post_content';
			$this->post_content = '';
			return FALSE;
		}
																		  
		//-------------------------------
		// Attempt to format
		//-------------------------------
		
		$this->api_tap_load_classes();
		
		$this->parser->parse_smilies = $this->post_settings['show_emoticons'];
		$this->parser->parse_html    = $this->post_settings['parse_html'];
		$this->parser->parse_bbcode  = 1;
		
		$this->post_content = $this->parser->pre_db_parse( $this->editor->_rte_html_to_bbcode( $post ) );
		
		return TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка автора по имени пользователя
	/*-------------------------------------------------------------------------*/
	/**
    * Получает информацию о пользователе и устанавливает автора 
	* темы/сообщения по имени пользователя
	*
	* @param	string	имя пользователя
	* @return	boolean	инициализация $this->author
	*/
	function set_author_by_name( $user_name )
	{
		$this->author = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																		  'from'   => 'members',
																		  'where'  => "members_l_username = '".$this->ipsclass->DB->add_slashes(strtolower($user_name))."'",
																		  'limit'  => array( 0, 1 ) ) );
																		  
		if ( ! $this->author['id'] )
		{
			$this->api_error[] = 'no_user_found';
			$this->author      = array();
			return FALSE;
		}
		
		return TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка автора по id пользователя
	/*-------------------------------------------------------------------------*/
	/**    
    * Получает информацию о пользователе и устанавливает автора 
	* темы/сообщения по ID пользователя
	*
	* @param	integer	ID пользователя
	* @return	boolean	инициализация $this->author
	*/
	function set_author_by_id( $id )
	{
		$this->author = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																		  'from'   => 'members',
																		  'where'  => "id=".intval($id),
																		  'limit'  => array( 0, 1 ) ) );
																		  
		if ( ! $this->author['id'] )
		{
			$this->api_error[] = 'no_user_found';
			$this->author      = array();
			return FALSE;
		}
		
		return TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка автора по отображаемому имени пользователя
	/*-------------------------------------------------------------------------*/
	/**
    * Получает информацию о пользователе и устанавливает автора 
    * темы/сообщения по отображаемому имени пользователя
	*
	* @param	string	отображаемое имя пользователя
	* @return	boolean	инициализация $this->author
	*/
	function set_author_by_display_name( $display_name )
	{
		$this->author = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																		  'from'   => 'members',
																		  'where'  => "members_l_display_name = '".$this->ipsclass->DB->add_slashes(strtolower($display_name))."'",
																		  'limit'  => array( 0, 1 ) ) );
																		  
		if ( ! $this->author['id'] )
		{
			$this->api_error[] = 'no_user_found';
			$this->author      = array();
		}
		
		return TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка темы по id
	/*-------------------------------------------------------------------------*/
	/**
    * Получает информацию о теме из базы и устанавливает 
    * текущую тему по ID темы
	*
	* @param	integer	ID темы
	* @return	boolean	инициализация $this->author
	*/
	function set_topic_id( $id )
	{
		$this->topic_id = intval($id);
		
		//-----------------------------------------
		// Verify it exists
		//-----------------------------------------
		
		$this->topic = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																		 'from'   => 'topics',
																		 'where'  => "tid=".intval( $this->topic_id ),
																		 'limit'  => array( 0, 1 ) ) );
																		 
		if ( ! $this->topic['tid'] )
		{
			$this->api_error[] = 'no_topic_found';
			$this->topic       = array();
			return FALSE;
		}
		
		//-----------------------------------------
		// Set and get forum ID
		//-----------------------------------------
		
		$this->set_forum_id( $this->topic['forum_id'] );
		
		return TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// Установка форума по id
	/*-------------------------------------------------------------------------*/
	/**
	* Получает информацию о форуме и устанавливает
    * текущий форум по ID форума
	*
	* @param	integer	ID форума
	* @return	boolean	инициализация $this->author
	*/
	function set_forum_id( $id )
	{
		$this->forum_id = intval($id);
		
		//-----------------------------------------
		// Verify it exists
		//-----------------------------------------
		
		$this->forum = $this->ipsclass->DB->build_and_exec_query( array( 'select' => '*',
																		 'from'   => 'forums',
																		 'where'  => "id=".intval( $this->forum_id ),
																		 'limit'  => array( 0, 1 ) ) );
																		 
		if ( ! $this->forum['id'] )
		{
			$this->api_error[] = 'no_forum_found';
			$this->forum       = array();
			return FALSE;
		}
		
		$perms = unserialize( stripslashes( $this->forum['permission_array'] ) );
		
		$this->forum['read_perms']   	= $perms['read_perms'];
		$this->forum['reply_perms']  	= $perms['reply_perms'];
		$this->forum['start_perms']  	= $perms['start_perms'];
		$this->forum['upload_perms'] 	= $perms['upload_perms'];
		$this->forum['download_perms']  = $perms['download_perms'];
		$this->forum['show_perms']   	= $perms['show_perms'];
		
		return TRUE;
	}
	
	/*-------------------------------------------------------------------------*/
	// Обновление счетчиков форума
	/*-------------------------------------------------------------------------*/
	/**
	* Обновляет кеши счетчиков тем и сообщений форума с указанным ID
	*
    * @param    integer ID форума 
	* @return	void 
	*/
	function tap_rebuild_forum( $forum_id )
	{
		//-----------------------------------------
		// Load classes...
		//-----------------------------------------
		
		$this->api_tap_load_classes();
		
		//-----------------------------------------
		// Send to rebuild
		//-----------------------------------------
		
		$this->func_mod->forum_recount( $forum_id );
	}
	
	/*-------------------------------------------------------------------------*/
	// Обновление статистики форума
	/*-------------------------------------------------------------------------*/
	/**
	* Обновляет кеш глобальной статистики форума
	*
	* @return	void 
	*/
	function tap_rebuild_stats()
	{
		//-----------------------------------------
		// Load classes...
		//-----------------------------------------
		
		$this->api_tap_load_classes();
		
		//-----------------------------------------
		// Send to rebuild
		//-----------------------------------------
		
		$this->func_mod->stats_recount();
	}
	
	/*-------------------------------------------------------------------------*/
	// Подключение классов системы
	/*-------------------------------------------------------------------------*/
	/**
	* Подключает необходимые классы для работы системы отправки сообщений/тем
	*
	* @return void;
	*/
	function api_tap_load_classes()
	{
		if ( ! $this->classes_loaded )
		{
			//-----------------------------------------
			// Force RTE editor
			//-----------------------------------------
			
			if ( ! is_object( $this->editor ) )
			{
				require_once( ROOT_PATH."sources/classes/editor/class_editor.php" );
				require_once( ROOT_PATH."sources/classes/editor/class_editor_rte.php" );
				$this->editor           =  new class_editor_module();
				$this->editor->ipsclass =& $this->ipsclass;
				$this->editor->allow_html = $this->post_settings['parse_html'];
		 	}
		 	
	        //-----------------------------------------
	        // Load the email libby
	        //-----------------------------------------
	        
	        if( ! is_object( $this->email ) )
			{
		        require_once( ROOT_PATH."sources/classes/class_email.php" );
				$this->email = new emailer();
		        $this->email->ipsclass =& $this->ipsclass;
		        $this->email->email_init();
	        }		 	
		 	
			//-----------------------------------------
			// Load and config POST class
			//-----------------------------------------
			
			if ( ! is_object( $this->post ) )
			{
				require_once( ROOT_PATH."sources/classes/post/class_post.php" );
				$this->post           =  new class_post();
				$this->post->ipsclass =& $this->ipsclass;
				$this->post->email =& $this->email;
				//^^ Required for forum tracker
			}
			
			//-----------------------------------------
			// Load and config the post parser
			//-----------------------------------------
			
			if ( ! is_object( $this->parser ) )
			{
				require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
				$this->parser                      =  new parse_bbcode();
				$this->parser->ipsclass            =& $this->ipsclass;
				$this->parser->allow_update_caches = 0;
				
				$this->parser->bypass_badwords = intval($this->ipsclass->member['g_bypass_badwords']);
			}
        
			//--------------------------------------------
			// Not loaded the func?
			//--------------------------------------------
			
			if ( ! is_object( $this->func_mod ) )
			{
				require_once( ROOT_PATH.'sources/lib/func_mod.php' );
				$this->func_mod           =  new func_mod();
				$this->func_mod->ipsclass =& $this->ipsclass;
			}
			
			$this->classes_loaded = 1;
		}
	}
	
}



?>