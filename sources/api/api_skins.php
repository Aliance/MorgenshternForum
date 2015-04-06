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
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-05-25 10:15:22 -0400 (Thu, 25 May 2006) $
|   > $Revision: 278 $
|   > $Author: bfarber $
+---------------------------------------------------------------------------
|
|   > API: Skins
|   > Module written by Brandon Farber
|   > Date started: Tuesday June 6th 2006 (11:12)
|
+--------------------------------------------------------------------------
*/

/**
* API: ������
*
* ������ �������������
* <code>
* $api =  new api_skins();
* # ����������� - ���� $ipsclass �� ���������� � ������, �� ����� ��������������� ��������������
* $api->ipsclass =& $this->ipsclass;
* $api->api_init();
* $api->skin_add_bits( $path_to_xml_file );
* $api->skin_add_macros( $path_to_xml_file );
* $messages = $api->skin_rebuild_caches( 0 );
* print implode( "<br />", $messages[1] );
* $messages = $api->skin_rebuild_caches( $messages[0] );
* </code>
*
* ������ XML-����� �������� ����� �������� ��������������� ��
* ��� � ������� ����������� "������ � ������� ������"
*
* XML-���� � ��������� ����� �������� ��������������� �� ��� �
* IN_DEV ������. ���������� ����� � ���������� ������� ->
* ������ ������ � ��������������� ������������� �� �������
* ��������������� ������.
*
* ��� �� ����� �������������� ������� ������ ������������ ������
* ����� ������������: ��������������� ������ �����
* �������� ���� �� ������ ��� ������� ������ skin_links, ��
* ������� � ���� �������� 'links'
*
* ����� skin_rebuild_caches ��������� ������� ����� ��� ����� (php
* ����� ����� �� �����) ��� ������ ����� �� ���. ����� �������������
* ����� id �������� ����, ��� id ����������� � �������.
* ��� ������ ������ ������, ����������� ��� ������� �������� �� ����.
* �� ���� ����������� ������� ��������� �� ���� 1-� �������� ��
* ������� (������ 0), ������� ��� ��������� ���������� ������� ������.
* ������ ������ ��������� ������������� ��� ����� ������ � ��������
* ����-����� ��� ���������� ���� ��������. �������� ������������ ���
* ����������� � ����� �������� ��� - ������ ������ ������ �������� ��
* ���� ������ ����� ������� ������������ �����, ��� ���� � ����������
* ��������� ���������� ���������� ���������, ������� ������ �����
* skin_rebuild_caches. ����� ������ �������� � ����������� �������
* ����� ����� �������� 0 ��� ������� ���� ������ ����� �����������,
* � ������ ����� ��������� ������ �������. ��� �� ����� �������, ���
* ������ �������� � ������������ ������� (������ 1) �������� ������
* ���������.
*
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
* API: ������
*
* ����� ������������� ��� ������ API ��� ���������� ������.
*
* @package		InvisionPowerBoard
* @subpackage	APIs
* @author  	 	Brandon Farber
* @version		2.2
* @since		2.2.0
*/
class api_skins extends api_core
{
	/**
	* IPS Class Object
	*
	* @var object
	*/
	//var $ipsclass;

	var $xml;
	var $cache_func;


	/*-------------------------------------------------------------------------*/
	// ���������� �������� ����� IPB
	/*-------------------------------------------------------------------------*/
	/**
	* ��������� ������� � ������� ����� IPB, ������� ����� �������� � ������-�����������
	*
	* @param	string	���� � xml, ����������� ������� (���������: ������ xml
	*					����� �������� ��������������� �� ��� � IN_DEV ������)
	* @return 	void;
	*/
	function skin_add_bits( $xml_file_path )
	{
		//-------------------------------
		// Check?
		//-------------------------------

		if ( ! $xml_file_path )
		{
			$this->api_error[] = "input_missing_fields";
			return;
		}

		require_once( KERNEL_PATH.'class_xml.php' );

		$this->xml = new class_xml();
		$this->xml->lite_parser = 1;

		//-------------------------------
		// Get file contents
		//-------------------------------

		$skin_content = implode( "", file($xml_file_path) );

		//-------------------------------
		// Unpack the datafile (TEMPLATES)
		//-------------------------------

		$this->xml->xml_parse_document( $skin_content );

		//-------------------------------
		// (TEMPLATES)
		//-------------------------------

		if ( ! is_array( $this->xml->xml_array['templateexport']['templategroup']['template'][0] ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $this->xml->xml_array['templateexport']['templategroup']['template'];

			unset($this->xml->xml_array['templateexport']['templategroup']['template']);

			$this->xml->xml_array['templateexport']['templategroup']['template'][0] = $tmp;
		}

		if ( ! is_array( $this->xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			$this->api_error[] = "xml_file_not_valid";
			return;
		}

		foreach( $this->xml->xml_array['templateexport']['templategroup']['template'] as $id => $entry )
		{
			$this->ipsclass->DB->allow_sub_select = 1;

			$row = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'suid',
																  'from'   => 'skin_templates',
																  'where'  => "group_name='{$entry['group_name']['VALUE']}' AND func_name='{$entry['func_name']['VALUE']}' and set_id=1"
														 )      );

			if ( $row['suid'] )
			{
				$this->ipsclass->DB->allow_sub_select = 1;

				$this->ipsclass->DB->do_update( 'skin_templates', array( 'func_data'       => $entry[ 'func_data' ]['VALUE'],
																		 'section_content' => $entry[ 'section_content' ]['VALUE'],
																		 'updated'         => time()
																	   )
											    , 'suid='.$row['suid'] );
			}
			else
			{
				$this->ipsclass->DB->allow_sub_select = 1;

				$this->ipsclass->DB->do_insert( 'skin_templates', array( 'func_data'       => $entry[ 'func_data' ]['VALUE'],
																		 'func_name'       => $entry[ 'func_name' ]['VALUE'],
																		 'section_content' => $entry[ 'section_content' ]['VALUE'],
																		 'group_name'      => $entry[ 'group_name' ]['VALUE'],
																		 'updated'         => time(),
																		 'set_id'          => 1
											  )                        );
			}
		}
	}


	/*-------------------------------------------------------------------------*/
	// ���������� �������� IPB
	/*-------------------------------------------------------------------------*/
	/**
	* ��������� ������� � ������� ����� IPB, ������� ����� �������� � ������-�����������
	*
	* @param	string	���� � xml, ����������� ������� (���������: ������ xml
    *                   ����� �������� ��������������� �� ��� � IN_DEV ������)
	* @return 	void;
	*/
	function skin_add_macros( $xml_file_path )
	{
		//-------------------------------
		// Check?
		//-------------------------------

		if ( !$xml_file_path )
		{
			$this->api_error[] = "input_missing_fields";
			return;
		}

		require_once( KERNEL_PATH.'class_xml.php' );

		$this->xml = new class_xml();
		$this->xml->lite_parser = 1;

		//-------------------------------
		// Get file contents
		//-------------------------------

		$macro_content = implode( "", file($xml_file_path) );

		//-------------------------------
		// Unpack the datafile (MACROS)
		//-------------------------------

		$this->xml->xml_parse_document( $macro_content );

		if ( ! is_array( $this->xml->xml_array['macroexport']['macrogroup']['macro'][0] ) )
		{
			//-----------------------------------------
			// Ensure [0] is populated
			//-----------------------------------------

			$tmp = $this->xml->xml_array['macroexport']['macrogroup']['macro'];

			unset($this->xml->xml_array['macroexport']['macrogroup']['macro']);

			$this->xml->xml_array['macroexport']['macrogroup']['macro'][0] = $tmp;
		}


		//-------------------------------
		// (MACRO)
		//-------------------------------

		if ( ! is_array( $this->xml->xml_array['macroexport']['macrogroup']['macro'] ) )
		{
			$this->api_error[] = "xml_file_not_valid";
			return;
		}

		foreach( $this->xml->xml_array['macroexport']['macrogroup']['macro'] as $id => $entry )
		{
			$this->ipsclass->DB->allow_sub_select = 1;

			$row = $this->ipsclass->DB->simple_exec_query( array( 'select' => 'macro_id',
																  'from'   => 'skin_macro',
																  'where'  => "macro_value='{$entry['macro_value']['VALUE']}' and macro_set=1"
										 )      );
			if ( $row['macro_id'] )
			{
				$this->ipsclass->DB->allow_sub_select = 1;

				$this->ipsclass->DB->do_update( 'skin_macro', array( 'macro_replace' => $entry['macro_replace']['VALUE'] ), "macro_value='{$entry['macro_value']['VALUE']}' and macro_set=1" );
			}
			else
			{
				$this->ipsclass->DB->allow_sub_select = 1;

				$this->ipsclass->DB->do_insert( 'skin_macro', array( 'macro_value'		=> $entry['macro_value']['VALUE'],
																	 'macro_replace'	=> $entry['macro_replace']['VALUE'],
																	 'macro_set'		=> 1 ) );
			}
		}
	}


	/*-------------------------------------------------------------------------*/
	// ������������ ���� ������
	/*-------------------------------------------------------------------------*/
	/**
	* ������� ����� .php ����� ���� ��� �������� �����.
	*
	* @param	int		id ���������� �������������� �����
	* @return 	array	���������� ������, ����������:
    *                   - id ������������� ����� (� ����� ����� ���� ��������
    *                   �� ���� ���� ������� ��� ����������� ���������� ����� ������
    *                   ������)
    *                   - ������ ���������
	*/
	function skin_rebuild_caches( $completed=1 )
	{
		$this->ipsclass->DB->load_cache_file( ROOT_PATH.'sources/sql/'.SQL_DRIVER.'_api_queries.php', 'sql_api_queries' );

		//-----------------------------------
		// Get ACP library
		//-----------------------------------

		require_once( ROOT_PATH.'sources/lib/admin_cache_functions.php' );
		$this->cache_func = new admin_cache_functions();
		$this->cache_func->ipsclass =& $this->ipsclass;

		//-----------------------------------
		// Image cache url
		//-----------------------------------

		$row = $this->ipsclass->DB->simple_exec_query ( array ( 'select' => 'conf_value, conf_default', 'from' => 'conf_settings', 'where' => "conf_key='ipb_img_url'" ) );

		$this->ipsclass->vars['ipb_img_url'] = $row['conf_value'] != "" ? $row['conf_value'] : $row['conf_default'];

		if ( $this->ipsclass->vars['ipb_img_url'] == "{blank}" )
		{
			$this->ipsclass->vars['ipb_img_url'] = "";
		}

		//-------------------------------
		// Next skin to do?
		//-------------------------------

		$completed = $completed > 0 ? intval($completed) : 1;

		//-----------------------------------
		// Get skins
		//-----------------------------------

		$this->ipsclass->DB->simple_construct( array( 'select' => '*',
													  'from'   => 'skin_sets',
													  'where'  => 'set_skin_set_id > '.$completed,
													  'order'  => 'set_skin_set_id',
													  'limit'  => array( 0, 1 )
						     )      );

		$this->ipsclass->DB->simple_exec();

		//-----------------------------------
		// Got a biggun?
		//-----------------------------------

		$r = $this->ipsclass->DB->fetch_row();

		if ( $r['set_skin_set_id'] )
		{
			$this->cache_func->_rebuild_all_caches( array($r['set_skin_set_id']) );

			return array( 'completed' => $r['set_skin_set_id'], 'messages' => $this->cache_func->messages );
		}
		else
		{
			return array( 'completed' => 0, 'messages' => array( '��� ������ ������ ��� ������������ ����' ) );
		}
	}

	/*-------------------------------------------------------------------------*/
	// ���������� ���� �������� �� ����� ������
	/*-------------------------------------------------------------------------*/
	/**
	* ��������� ������� ���������� ������ �� �������
	* ����������: ����� �������� ������ ������ ��������, �� �� �������� ��� HTML
    * ��������
	*
	* @param	string	HTML-���� �������
	* @return 	string	HTML-���� ������� ����� ���������
	*/
	function skin_update_template_bit( $html='' )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$debug = 0;
		$_html = $html;

		//-----------------------------------------
		// Fix up basic tags
		//-----------------------------------------

		$html = preg_replace( "/{ipb\.script_url}/i", '{$this->ipsclass->base_url}'  , $html);
		$html = preg_replace( "/{ipb\.session_id}/i", '{$this->ipsclass->session_id}', $html);

		//-----------------------------------------
		// Fix up the IF statements
		//-----------------------------------------

		# IF / ELSE IF / ELSE
		$html = preg_replace_callback( "#(?:\s+?)?<(if=[\"'].+?[\"'])>(.+?)</if>\s+?<else (if=[\"'].+?[\"'])>(.+?)</if>\s+?<else>(.+?)</else>#is", array( &$this, '_func_fix_if_elseif_else' ), $html );
		# IF / ELSE IF
		$html = preg_replace_callback( "#(?:\s+?)?<(if=[\"'].+?[\"'])>(.+?)</if>\s+?<else (if=[\"'].+?[\"'])>(.+?)</if>#is", array( &$this, '_func_fix_if_elseif_else' ), $html );
		# IF / ELSE
		$html = preg_replace_callback( "#(?:\s+?)?<(if=[\"'].+?[\"'])>(.+?)</if>\s+?<else>(.+?)</else>#is", array( &$this, '_func_fix_if_elseif_else' ), $html );

		//-----------------------------------------
		// Sort out the IF content
		//-----------------------------------------

		$html = preg_replace_callback( "#<if=([\"'])(.+?)[\"']>#is", array( &$this, '_func_check_if_statement' ), $html );

		//-----------------------------------------
		// Sort out the rest of the tags...
		//-----------------------------------------

		$html = preg_replace( "#ipb\.(member|vars|skin|lang|input)#i", '$this->ipsclass->\\1', $html );

		#print "<pre>". htmlspecialchars( $html ); exit();

		if ( $debug )
		{
			$_string  = "\n===================================================";
			$_string .= "\n Date: ". date( 'r' );
			$_string .= "\n---ORIGINAL----------------------------------------";
			$_string .= "\n".$_html;
			$_string .= "\n---CONVERTED---------------------------------------";
			$_string .= "\n".$html;

			if ( $FH = @fopen( ROOT_PATH . 'cache/template_update_debug_log_'.date('m_d_y').'.cgi', 'a' ) )
			{
				@fwrite( $FH, $_string );
				@fclose( $FH );
			}
		}

		return $html;
	}

	/*-------------------------------------------------------------------------*/
	// Fix up an if / else if / else statement
	/*-------------------------------------------------------------------------*/

	/*
	* <if="">
	*	MAIN IF
	* <else />
	*   <if="">
	*		ELSE IF
	*	<else />
	*		ELSE
	*	</if>
	* </if>
	*/
	function _func_fix_if_elseif_else( $matches=array() )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$if           = '';
		$if_html      = '';
		$else_if      = '';
		$else_if_html = '';
		$else_html    = '';
		$formatted    = '';

		//-----------------------------------------
		// What are we doing?
		//-----------------------------------------

		if ( count( $matches ) == 6 )
		{
			$if           = trim( $matches[1] );
			$if_html      = trim( $matches[2] );
			$else_if      = trim( $matches[3] );
			$else_if_html = trim( $matches[4] );
			$else_html    = trim( $matches[5] );
		}
		else if ( count( $matches ) == 5 )
		{
			$if           = trim( $matches[1] );
			$if_html      = trim( $matches[2] );
			$else_if      = trim( $matches[3] );
			$else_if_html = trim( $matches[4] );
		}
		else
		{
			$if           = trim( $matches[1] );
			$if_html      = trim( $matches[2] );
			$else_html    = trim( $matches[3] );
		}

		//-----------------------------------------
		// OK...
		//-----------------------------------------

		if ( $if AND $else_if AND $else_html )
		{
			$formatted  = "<".$if.">\n";
			$formatted .= $if_html . "\n";
			$formatted .= "<else />\n";
			$formatted .= "\t<".$else_if.">\n";
			$formatted .= $else_if_html."\n";
			$formatted .= "\t<else />\n";
			$formatted .= $else_html . "\n";
			$formatted .= "\t</if>\n";
			$formatted .= "</if>\n";
		}
		else if ( $if AND $else_if )
		{
			$formatted  = "<".$if.">\n";
			$formatted .= $if_html . "\n";
			$formatted .= "<else />\n";
			$formatted .= "\t<".$else_if.">\n";
			$formatted .= $else_if_html."\n";
			$formatted .= "\t</if>\n";
			$formatted .= "</if>\n";
		}
		else if ( $if AND $else_html )
		{
			$formatted  = "<".$if.">\n";
			$formatted .= $if_html . "\n";
			$formatted .= "<else />\n";
			$formatted .= $else_html."\n";
			$formatted .= "</if>\n";
		}

		return $formatted;
	}

	//===================================================
	// Sort out left bit of comparison
	//===================================================

	function _func_fix_if_statement($left, $andor="", $fs="", $ls="")
	{
		$left = trim($this->_trim_slashes($left));

		if ( preg_match( "/^ipb\./", $left ) )
		{
			$left = preg_replace( "/^ipb\.(.+?)$/", '$this->ipsclass->'."\\1", $left );
		}
		else
		{
			$left = '$'.$left;
		}

		return $andor.$fs.$left.$ls;
	}

	//===================================================
	// Statement: Prep AND OR, etc
	//===================================================

	function _func_check_if_statement( $matches=array() )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------

		$quotes = $matches[1];
		$code   = $this->_trim_slashes( $matches[2] );

		$code = preg_replace( "/(^|and|or)(\s+)(.+?)(\s|$)/ise", "\$this->_func_fix_if_statement('\\3', '\\1', '\\2', '\\4')", ' '.$code );

		$code = preg_replace( '#\${1,}#i', '$', $code );
		$code = str_replace( '$($', '($', $code );

		return "<if=".$quotes.trim($code).$quotes.">";
	}

	//===================================================
	// Remove leading and trailing newlines
	//===================================================

	function _trim_slashes($code)
	{
		$code = str_replace( '\"' , '"', $code );
		$code = str_replace( "\\'", "'", $code );
		return $code;
	}

}

?>