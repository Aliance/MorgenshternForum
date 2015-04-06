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
|   > $Date: 2005-10-10 14:08:54 +0100 (Mon, 10 Oct 2005) $
|   > $Revision: 23 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > API: Languages
|   > Module written by Matt Mecham
|   > Date started: Wednesday 30th November 2005 (11:40)
|
+--------------------------------------------------------------------------
*/

/**
* API: Форумов
*
* ПРИМЕР ИСПОЛЬЗОВАНИЯ
* <code>
  $api = new api_topic_view();
  $api->ipsclass =& $this->ipsclass;
  $api->topic_list_config['forums'] = array( 1,2,3,4 );
  $api->topic_list_config['limit'] = 10;
  $topics = $api->return_topic_list_data();
  // Пройти массив $topics и вывести данные
* </code>
*
* API возвращает информацию о прикрепленных файлах в массиве
* attachment_data
*
* Структура массива прикрепленных файлов:

            [attachment_data] => Array
                (
                    [0] => Array
                        (
                            [size] => 41.29
                            [method] => post
                            [id] => 24
                            [file] => somefile.jpg
                            [hits] => 0
                            [thumb_location] => 
                            [type] => image
                            [thumb_x] => 0
                            [thumb_y] => 0
                            [ext] => jpg
                        )

                )
* Перебирая ключи массива attachment_data (если они 
* существуют - если нет, то нет и прикрепленных файлов)
* можно обработать каждый прикрепленный файл.
*
* Типы прикрепленных файлов ( значения types): 
* thumb = эскиз, 
* image = изображение,
* reg   = файл
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
* API: Форумов
*
* Класс предоставляет все методы API для работы с выводом тем.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Matt Mecham
* @version		2.1
* @since		2.1.0
*/
class api_topic_view extends api_core
{
	/**
	* Объект супер-класса IPS 
	*
	* @var object
	*/
	//var $ipsclass;

	/**
	* Конструктор, принимающий значение для limit (c) Aliance
	*
	* @var integer
	*/
	function api_topic_view($limit = 10)
	{
		$this->topic_list_config['limit'] = $limit;
	}
	
	/**
	* Конфигурация вывода списка тем
	*
	* @var array
	*/
	var $topic_list_config = array( 'offset'      => 0,					// с какой темы начинать вывод
					'limit'       => 10,					// количество выводимых тем
					'forums'      => array("23, 32, 33, 34, 35, 52"),	// из каких форумов брать темы (может быть массивом)
					'order_field' => 'started',				// поле по которому сортировать темы
					'order_by'    => 'DESC' );				// порядок сортировки
									
	var $attach_pids = array();
									
	/*-------------------------------------------------------------------------*/
	// Получение массива данных тем
	/*-------------------------------------------------------------------------*/
	/**
	* Возвращает массив с данными всех тем
    * 
	* ВНИМАНИЕ: метод возвращает ВСЕ темы, права пользователя не учитываются, т.е.
    * доступны все форумы!
	*
	* @return   array	Массив с данными тем
	*/
	function return_topic_list_data()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$topics = array();
		
		$this->ipsclass->init_load_cache( array( 'bbcode','emoticons','attachtypes' ) );
		
		//-----------------------------------------
		// Load parser
		//-----------------------------------------
		
		require_once( ROOT_PATH."sources/handlers/han_parse_bbcode.php" );
        $parser                      =  new parse_bbcode();
        $parser->ipsclass            =& $this->ipsclass;
        $parser->allow_update_caches = 0;
        $parser->bypass_badwords     = 0;
		
		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$this->topic_list_config['order_field'] = ( $this->topic_list_config['order_field'] == 'started' )  ? 'start_date' : $this->topic_list_config['order_field'];
		$this->topic_list_config['order_field'] = ( $this->topic_list_config['order_field'] == 'lastpost' ) ? 'last_post'  : $this->topic_list_config['order_field'];
		$this->topic_list_config['forums']      = ( is_array( $this->topic_list_config['forums'] ) ) ? implode( ",", $this->topic_list_config['forums'] ) : $this->topic_list_config['forums'];
		
		//-----------------------------------------
		// Get from the DB
		//-----------------------------------------

// 'where'    => 't.approved=1 AND t.forum_id IN (0,'.$this->topic_list_config['forums'].')',
// 'order'    => $this->topic_list_config['order_field'].' '.$this->topic_list_config['order_by'],
		
		$this->ipsclass->DB->build_query( array( 'select'   => 't.*',
												 'from'     => array( 'topics' => 't' ),
												 'where'    => 't.approved=1 AND t.forum_id IN (0,'.$this->topic_list_config['forums'].') AND t.state!="link"',
												 'order'    => 'pinned DESC, ' . $this->topic_list_config['order_field'] . ' ' . $this->topic_list_config['order_by'],
												 'limit'    => array( $this->topic_list_config['offset'], $this->topic_list_config['limit'] ),
												 'add_join' => array( 
																	  0 => array( 'select' => 'p.*',
																				  'from'   => array( 'posts' => 'p' ),
																				  'where'  => 't.topic_firstpost=p.pid',
																				  'type'   => 'left' ),
																	  1 => array( 'select' => 'm.id as member_id, m.members_display_name as member_name, m.mgroup, m.email',
																	  			  'from'   => array( 'members' => 'm' ),
																				  'where'  => "m.id=p.author_id",
																				  'type'   => 'left' ),
																	  2 => array( 'select' => 'f.id as forum_id, f.name as forum_name, f.use_html',
																	  			  'from'   => array( 'forums' => 'f' ),
																				  'where'  => "t.forum_id=f.id",
																				  'type'   => 'left' ) )
										)      );
		
		$this->ipsclass->DB->exec_query();
		
		while( $row = $this->ipsclass->DB->fetch_row() )
		{
			//-----------------------------------------
			// Format posts
			//-----------------------------------------

			$parser->parse_html     = ( $row['use_html'] AND $row['post_htmlstate'] ) ? 1 : 0;
			$parser->parse_wordwrap = $this->ipsclass->vars['post_wordwrap'];
			$parser->parse_nl2br    = $row['post_htmlstate'] == 2 ? 1 : 0;
			
			$row['post'] = $parser->pre_display_parse( $row['post'] );
			
			if( $row['topic_hasattach'] )
			{
				$this->attach_pids[] = $row['pid'];
			}
			
			//-----------------------------------------
			// Guest name?
			//-----------------------------------------
			
			$row['member_name']    = $row['member_name'] ? $row['member_name'] : $row['author_name'];
			
			//-----------------------------------------
			// Topic link
			//-----------------------------------------
			
			$row['link-topic'] = $this->ipsclass->base_url.'showtopic='.$row['tid'];
			$row['link-forum'] = $this->ipsclass->base_url.'showforum='.$row['forum_id'];
			
			$topics[] = $row;
		}
		
		if( count( $this->attach_pids ) )
		{
			$final_attachments = array();
			
			$this->ipsclass->DB->simple_construct( array( 'select' => '*',
														  'from'   => 'attachments',
														  'where'  => "attach_pid IN (".implode(",", $this->attach_pids).")"
												 )      );

			$this->ipsclass->DB->simple_exec();
			
			while ( $a = $this->ipsclass->DB->fetch_row() )
			{
				$final_attachments[ $a[ 'attach_pid' ] ][ $a['attach_id'] ] = $a;
			}
			
			$final_topics = array();
			
			foreach( $topics as $mytopic )
			{
				$this_topic_attachments = array();
				
				foreach ( $final_attachments as $pid => $data )
				{
					if( $pid <> $mytopic['pid'] )
					{
						continue;
					}
					
					$temp_out = "";
					$temp_hold = array();
					
					foreach( $final_attachments[$pid] as $aid => $row )
					{
						//-----------------------------------------
						// Is it an image, and are we viewing the image in the post?
						//-----------------------------------------
						
						if ( $this->ipsclass->vars['show_img_upload'] and $row['attach_is_image'] )
						{
							if ( $this->ipsclass->vars['siu_thumb'] AND $row['attach_thumb_location'] AND $row['attach_thumb_width'] )
							{ 
								$this_topic_attachments[] = array( 'size' 		=> $this->ipsclass->size_format( $row['attach_filesize'] ),
																	'method' 	=> 'post',
																	'id'		=> $row['attach_id'],
																	'file'		=> $row['attach_file'],
																	'hits'		=> $row['attach_hits'],
																	'thumb_location'	=> $row['attach_thumb_location'],
																	'type'		=> 'thumb',
																	'thumb_x'	=> $row['attach_thumb_width'],
																	'thumb_y'	=> $row['attach_thumb_height'],
																	'ext'		=> $row['attach_ext'],
																);
							}
							else
							{
								$this_topic_attachments[] = array( 'size' 		=> $this->ipsclass->size_format( $row['attach_filesize'] ),
																	'method' 	=> 'post',
																	'id'		=> $row['attach_id'],
																	'file'		=> $row['attach_file'],
																	'hits'		=> $row['attach_hits'],
																	'thumb_location'	=> $row['attach_thumb_location'],
																	'type'		=> 'image',
																	'thumb_x'	=> $row['attach_thumb_width'],
																	'thumb_y'	=> $row['attach_thumb_height'],
																	'ext'		=> $row['attach_ext'],
																);
							}
						}
						else
						{
								$this_topic_attachments[] = array( 'size' 		=> $this->ipsclass->size_format( $row['attach_filesize'] ),
																	'method' 	=> 'post',
																	'id'		=> $row['attach_id'],
																	'file'		=> $row['attach_file'],
																	'hits'		=> $row['attach_hits'],
																	'thumb_location'	=> $row['attach_thumb_location'],
																	'type'		=> 'reg',
																	'thumb_x'	=> $row['attach_thumb_width'],
																	'thumb_y'	=> $row['attach_thumb_height'],
																	'ext'		=> $row['attach_ext'],
																);
						}
					}
				}

				if( count( $this_topic_attachments ) )
				{
					$mytopic['attachment_data'] = $this_topic_attachments;
				}
				
				$final_topics[] = $mytopic;
			}
		}
		
		//-----------------------------------------
		// Return...
		//-----------------------------------------
				
		if( count( $final_topics ) )
		{
			return $final_topics;
		}
		else
		{
			return $topics;
		}			
	}
	
}
?>