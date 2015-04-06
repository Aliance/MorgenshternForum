<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2006 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   ========================================
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
+--------------------------------------------------------------------------
|
|   > Script written by Matthew Mecham
|   > Date started: 12th August 2004
|   > MYSQL EXTRA CONFIG / INSTALL FILE
+--------------------------------------------------------------------------
*/

class install_extra
{
	var $errors     = array();
	var $info_extra = array();
	var $ipsclass   = "";

	function install_extra()
	{

	}

	/*-------------------------------------------------------------------------*/
	// process_query_create: Alter the query before it goes back $DB->query
	// table prefix already changed at this point: CREATE TABLE
	/*-------------------------------------------------------------------------*/

	function process_query_create( $query )
	{
		//-----------------------------------------
		// Tack on the end the chosen table type
		//-----------------------------------------

		$table_type = $this->ipsclass->vars['mysql_tbl_type'];

		if ( !$table_type )
		{
			$table_type = 'MyISAM';
		}
        
		$codepage = $this->ipsclass->vars['mysql_codepage'];

		return preg_replace( "#\);$#", ") TYPE=".$table_type." /*!40100 DEFAULT CHARACTER SET ".$codepage." */;", $query );
	}

	/*-------------------------------------------------------------------------*/
	// process_query_index: Alter the query before it goes back $DB->query
	// table prefix already changed at this point: INDEX
	/*-------------------------------------------------------------------------*/

	function process_query_index( $query )
	{
		return $query;
	}

	/*-------------------------------------------------------------------------*/
	// process_query_index: Alter the query before it goes back $DB->query
	// table prefix already changed at this point: INSERT
	/*-------------------------------------------------------------------------*/

	function process_query_insert( $query )
	{
		return $query;
	}

	/*-------------------------------------------------------------------------*/
	// WHEN SHOWING THE FORM....
	/*-------------------------------------------------------------------------*/

	function install_form_extra()
	{
		$extra = "<tr>
					<td class='title'><b>Кодировка MySQL</b><div style='color:gray; font-weight:normal;'>Не изменяте, если не уверены.</div></td>
					<td class='content'><input type='text' name='mysql_codepage' class='sql_form' value='cp1251'></td>
				  </tr>
                  <tr>
					<td class='title'><b>Тип MySQL таблиц</b><div style='color:gray; font-weight:normal;'>Не изменяте, если не уверены.</div></td>
					<td class='content'><select name='mysql_tbl_type' class='sql_form'><option value='MyISAM'>MYISAM</option><option value='INNODB'>INNODB</option></select></td>
				  </tr>";

		return $extra;

	}

	/*-------------------------------------------------------------------------*/
	// WHEN SAVING TO CONF GLOBAL
	// Return errors in $errors[]
	/*-------------------------------------------------------------------------*/

	function install_form_process()
	{
		//-----------------------------------------
		// When processed, return all vars to save
		// in conf_global in the array $this->info_extra
		// This will also be saved into $INFO[] for
		// the installer
		//-----------------------------------------

		if ( ! $_REQUEST['mysql_tbl_type'] OR ! $_REQUEST['mysql_codepage'] )
		{
			$this->errors[] = 'Вы обязаны заполнить всю информацию о SQL сервере!';
			return;
		}

		$this->info_extra['mysql_tbl_type'] = $_REQUEST['mysql_tbl_type'];
		$this->info_extra['mysql_codepage'] = $_REQUEST['mysql_codepage'];
	}

}

?>