<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Services Kernel [DB Abstraction]
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
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|
|   > MySQLi Driver Module
|   > Module written by Brandon Farber
|   > Date started: Monday 28th February 2005 16:46 
|
|	> Module Version Number: 2.1.0
+--------------------------------------------------------------------------
*/

/**
* IPS Kernel Pages: Database Object Driver
*
* @package		IPS_KERNEL
* @subpackage	DatabaseAbstraction
* @author		Brandon Farber
* @copyright	Invision Power Services, Inc.
* @version		2.1
*/

/**
 * Handle base class definitions
 *
 */

if ( ! defined('KERNEL_PATH') )
{
	define( 'KERNEL_PATH', dirname(__FILE__) . '/' );
}

if ( ! class_exists( 'db_main' ) )
{
	require_once( KERNEL_PATH.'class_db.php' );
}

/**
 * Allow < 4.3.0 PHP client access
 *
 */
if ( ! defined('IPS_MAIN_DB_CLASS_LEGACY') )
{
	define('IPS_MAIN_DB_CLASS_LEGACY', ( PHP_VERSION < '4.3.0' ) ? TRUE : FALSE );
}

/**
* DB Class: Driver Methods
*
* Base class for database abstraction
*
* @package		IPS_KERNEL
* @subpackage	DatabaseAbstraction
* @author   	Brandon Farber
* @version		2.1
*/
class db_driver_mysql extends db_main
{
	
	/*-------------------------------------------------------------------------*/
	// Set up required vars
	/*-------------------------------------------------------------------------*/
	 
	function db_driver_mysql()
	{
		//--------------------------
		// Set up any required connect
		// vars here:
		// Will be populated by obj
		// caller
		//--------------------------
		
     	$this->connect_vars['mysql_tbl_type'] = "";
	}
	
    /*-------------------------------------------------------------------------*/
    // Connect to the database  
    /*-------------------------------------------------------------------------*/  
                   
	function connect()
	{
		//-----------------------------------------
     	// Done SQL prefix yet?
     	//-----------------------------------------
     	
     	$this->_set_prefix();
     	
    	//-----------------------------------------
    	// Load query file
    	//-----------------------------------------
    	
    	$this->_load_cache_file();
     	
     	//-----------------------------------------
     	// Connect
     	//-----------------------------------------
     	
    	if ( $this->obj['persistent'] AND ! IPS_MAIN_DB_CLASS_LEGACY )
    	{
    	    $this->connection_id = @mysql_pconnect( $this->obj['sql_host'] ,
												   $this->obj['sql_user'] ,
												   $this->obj['sql_pass'] ,
												   $this->obj['force_new_connection']
												);
        }
        else
        { 
			if ( IPS_MAIN_DB_CLASS_LEGACY )
        	{
				$this->connection_id = @mysql_connect( $this->obj['sql_host'] ,
													  $this->obj['sql_user'] ,
													  $this->obj['sql_pass']
													);
			}
			else
			{
				$this->connection_id = @mysql_connect( $this->obj['sql_host'] ,
													  $this->obj['sql_user'] ,
													  $this->obj['sql_pass'] ,
													  $this->obj['force_new_connection']
													);
			}
		}
		
		if ( ! $this->connection_id )
		{
			$this->fatal_error();
			return FALSE;
		}
		
        if ( ! mysql_select_db($this->obj['sql_database'], $this->connection_id) )
        {
        	$this->fatal_error();
        	return FALSE;
        }
        
        $this->sql_set_collation_and_cp();
        
        return TRUE;
    }
    
    function sql_set_collation_and_cp()
    {
    	$this->sql_get_version();

		if ( $this->mysql_version >= 40101 )
		{
			$res = mysql_query( "SHOW CHARSET LIKE '" . $this->obj['mysql_codepage']  .  "'", $this->connection_id );
			
			$charset = mysql_fetch_row($res);

        	mysql_query( "SET NAMES " . $this->obj['mysql_codepage'], $this->connection_id );
        	mysql_query( "SET CHARACTER SET " . $this->obj['mysql_codepage'], $this->connection_id );      
        	mysql_query( "SET character_set_connection = " . $this->obj['mysql_codepage'], $this->connection_id );       	      
        	mysql_query( "SET collation_connection = " . $charset[2], $this->connection_id );
		}
        
        return TRUE;
    }
        
    function sql_can_subquery()
    {
		$this->sql_get_version();
		
		if ( $this->mysql_version >= 41000 )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
    
    /*-------------------------------------------------------------------------*/
    // Quick function: DO UPDATE
    /*-------------------------------------------------------------------------*/
    
    function do_update( $tbl, $arr, $where="", $shutdown=FALSE )
    {
    	//-----------------------------------------
    	// Form query
    	//-----------------------------------------
    	
    	$dba   = $this->compile_db_update_string( $arr );
    	$query = "UPDATE ".$this->obj['sql_tbl_prefix']."$tbl SET $dba";
    	
    	if ( $where )
    	{
    		$query .= " WHERE ".$where;
    	}
    	
    	//-----------------------------------------
    	// Shut down query?
    	//-----------------------------------------
    	
    	$this->no_prefix_convert = 1;
    	
    	if ( $shutdown )
    	{
    		if ( ! $this->obj['use_shutdown'] )
			{
				$this->is_shutdown = 1;
				$return = $this->query( $query );
				$this->no_prefix_convert = 0;
				$this->is_shutdown = 0;
				return $return;
			}
			else
			{
				$this->obj['shutdown_queries'][] = $query;
				$this->no_prefix_convert = 0;
				$this->cur_query = "";
			}
    	}
    	else
    	{
    		$return = $this->query( $query );
    		$this->no_prefix_convert = 0;
    		return $return;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // Quick function: DO INSERT
    /*-------------------------------------------------------------------------*/
    
    function do_insert( $tbl, $arr, $shutdown=FALSE )
    {
    	//-----------------------------------------
    	// Form query
    	//-----------------------------------------
    	
    	$dba   = $this->compile_db_insert_string( $arr );
    	$query = "INSERT INTO ".$this->obj['sql_tbl_prefix']."$tbl ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_VALUES']})";
    	
    	//-----------------------------------------
    	// Shut down query?
    	//-----------------------------------------
    	
    	$this->no_prefix_convert = 1;
    	
    	if ( $shutdown )
    	{
    		if ( ! $this->obj['use_shutdown'] )
			{
				$this->is_shutdown = 1;
				$return = $this->query( $query );
				$this->no_prefix_convert = 0;
				$this->is_shutdown = 0;
				return $return;
			}
			else
			{
				$this->obj['shutdown_queries'][] = $query;
				$this->no_prefix_convert = 0;
				$this->cur_query = "";
			}
    	}
    	else
    	{
    		$return = $this->query( $query );
    		$this->no_prefix_convert = 0;
    		return $return;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: UPDATE
    /*-------------------------------------------------------------------------*/
    
    function simple_update( $tbl, $set, $where='', $low_pro='' )
    {
    	if ( $low_pro )
    	{
    		$low_pro = ' LOW_PRIORITY ';
    	}
    	
    	$this->cur_query .= "UPDATE ". $low_pro . $this->obj['sql_tbl_prefix']."$tbl SET $set";
    	
    	if ( $where )
    	{
    		$this->cur_query .= " WHERE $where";
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: DELETE
    /*-------------------------------------------------------------------------*/
    
    function simple_delete( $tbl, $where='' )
    {
	    if( !$where )
	    {
		    $this->cur_query .= "TRUNCATE TABLE ".$this->obj['sql_tbl_prefix']."$tbl";
	    }
	    else
	    {
    		$this->cur_query .= "DELETE FROM ".$this->obj['sql_tbl_prefix']."$tbl WHERE $where";
		}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: ORDER
    /*-------------------------------------------------------------------------*/
    
    function simple_order( $a )
    {
    	if ( $a )
    	{
    		$this->cur_query .= ' ORDER BY '.$a;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: GROUP
    /*-------------------------------------------------------------------------*/
    
    function simple_group( $a )
    {
    	if ( $a )
    	{
    		$this->cur_query .= ' GROUP BY '.$a;
    	}
    }    
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: LIMIT WITH CHECK
    /*-------------------------------------------------------------------------*/
    
    function simple_limit_with_check( $offset, $limit="" )
    {
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$offset = intval( $offset );
		$offset = ( $offset < 0 ) ? 0 : $offset;
		$limit  = intval( $limit );
		#$limit  = ( $limit < 0 ) ? 0 : $limit;
			
    	if ( ! preg_match( "#LIMIT\s+?\d+,#i", $this->cur_query ) )
		{
			$this->simple_limit( $offset, $limit );
		}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: LIMIT
    /*-------------------------------------------------------------------------*/
    
    function simple_limit( $offset, $limit="" )
    {
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$offset = intval( $offset );
		$offset = ( $offset < 0 ) ? 0 : $offset;
		$limit  = intval( $limit );
		#$limit  = ( $limit < 0 ) ? 0 : $limit;
		
    	if ( $limit )
    	{
    		$this->cur_query .= ' LIMIT '.$offset.','.$limit;
    	}
    	else
    	{
    		$this->cur_query .= ' LIMIT '.$offset;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: SELECT
    /*-------------------------------------------------------------------------*/
    
    function simple_select( $get, $table, $where="" )
    {
    	$this->cur_query .= "SELECT $get FROM ".$this->obj['sql_tbl_prefix']."$table";
    	
    	if ( $where != "" )
    	{
    		$this->cur_query .= " WHERE ".$where;
    	}
    }
    
    /*-------------------------------------------------------------------------*/
    // SIMPLE: SELECT WITH JOIN
    /*-------------------------------------------------------------------------*/
    
    function simple_select_with_join( $get, $table, $where="", $add_join=array() )
    {
    	//-----------------------------------------
    	// OK, here we go...
    	//-----------------------------------------
    	
    	$select_array   = array();
    	$from_array     = array();
    	$joinleft_array = array();
    	$where_array    = array();
    	$final_from     = array();
    	
    	$select_array[] = $get;
    	$from_array[]   = $table;
    	
    	if ( $where )
    	{
    		$where_array[]  = $where;
    	}
    	
    	//-----------------------------------------
    	// Loop through JOINs and sort info
    	//-----------------------------------------
    	
    	if ( is_array( $add_join ) and count( $add_join ) )
    	{
    		foreach( $add_join as $join )
    		{
    			# Push join's select to stack
    			if ( $join['select'] )
    			{
    				$select_array[] = $join['select'];
    			}
    			
    			if ( $join['type'] == 'inner' )
    			{
    				# Join is inline
    				$from_array[]  = $join['from'];
    				
    				if ( $join['where'] )
    				{
    					$where_array[] = $join['where'];
    				}
    			}
    			else if ( $join['type'] == 'left' )
    			{
    				# Join is left
    				$tmp = " LEFT JOIN ";
    				
    				foreach( $join['from'] as $tbl => $alias )
					{
						$tmp .= $this->obj['sql_tbl_prefix'].$tbl.' '.$alias;
					}
		
    				if ( $join['where'] )
    				{
    					$tmp .= " ON ( ".$join['where']." ) ";
    				}
    				
    				$joinleft_array[] = $tmp;
    				
    				unset( $tmp );
    			}
    			else
    			{
    				# Not using any other type of join
    			}
    		}
    	}
    	
    	//-----------------------------------------
    	// Build it..
    	//-----------------------------------------
    	
    	foreach( $from_array as $i )
		{
			foreach( $i as $tbl => $alias )
			{
				$final_from[] = $this->obj['sql_tbl_prefix'].$tbl.' '.$alias;
			}
		}
    	
    	$get   = implode( ","     , $select_array   );
    	$table = implode( ","     , $final_from     );
    	$where = implode( " AND " , $where_array    );
    	$join  = implode( "\n"    , $joinleft_array );
    	
    	$this->cur_query .= "SELECT $get FROM $table";
    	
    	if ( $join )
    	{
    		$this->cur_query .= " ".$join." ";
    	}
    	
    	if ( $where != "" )
    	{
    		$this->cur_query .= " WHERE ".$where;
    	}
    }
   
    
    /*-------------------------------------------------------------------------*/
    // Process a manual query
    /*-------------------------------------------------------------------------*/
    
    function query($the_query, $bypass=0)
    {
    	//-----------------------------------------
        // Change the table prefix if needed
        //-----------------------------------------
        
        if ( $this->no_prefix_convert )
        {
        	$bypass = 1;
        }
        
        if ( ! $bypass )
        {
			if ( $this->obj['sql_tbl_prefix'] != "ibf_" and ! $this->prefix_changed )
			{
			   $the_query = preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->obj['sql_tbl_prefix']."\\1\\2", $the_query);
			}
        }
        
        //-----------------------------------------
        // Debug?
        //-----------------------------------------
        
        if ( $this->obj['debug'] OR ($this->obj['use_debug_log'] AND $this->obj['debug_log']) )
        {
    		global $Debug;
    		
    		$Debug->startTimer();
    	}

		//-----------------------------------------
		// Stop sub selects? (UNION)
		//-----------------------------------------
		
		if ( ! IPS_DB_ALLOW_SUB_SELECTS )
		{
			# On the spot allowance?
			
			if ( ! $this->allow_sub_select )
			{
				$_tmp = strtolower( $this->remove_all_quotes($the_query) );
				
				if ( preg_match( "#(?:/\*|\*/)#i", $_tmp ) )
				{
					$this->fatal_error( "��������� �������� �� ��������� ������������� ������������ � SQL �������.\n�������� ������� allow_sub_select �� 1 � ������� �������� ��� ���������� ���� ��������." );
					return false;
				}
				
				if ( preg_match( "#[^_a-zA-Z]union[^_a-zA-Z]#s", $_tmp ) )
				{
					$this->fatal_error( "��������� �������� �� ��������� ������������ UNION-�������.\n�������� ������� allow_sub_select �� 1 � ������� �������� ��� ���������� ���� ��������." );
					return false;
				}
				else if ( preg_match_all( "#[^_a-zA-Z](select)[^_a-zA-Z]#s", $_tmp, $matches ) )
				{
					if ( count( $matches ) > 1 )
					{
						$this->fatal_error( "��������� �������� �� ��������� ������������ ��������� SELECT � ��������.\n�������� ������� allow_sub_select �� 1 � ������� �������� ��� ���������� ���� ��������." );
						return false;
					}
				}
			}
		}
    	
    	//-----------------------------------------
    	// Run the query
    	//-----------------------------------------
    	
        $this->query_id = mysql_query($the_query, $this->connection_id);
      	
      	//-----------------------------------------
      	// Reset array...
      	//-----------------------------------------
      	
      	$this->force_data_type  = array();
      	$this->allow_sub_select = 0;

        if (! $this->query_id )
        {
            $this->fatal_error($the_query);
        }
        
        //-----------------------------------------
        // Debug?
        //-----------------------------------------
        
		if ( $this->obj['use_debug_log'] AND $this->obj['debug_log'] )
		{
			$endtime  = $Debug->endTimer();
			
			if ( preg_match( "/^(?:\()?select/i", $the_query ) )
        	{
        		$eid = mysql_query("EXPLAIN $the_query", $this->connection_id);
        		
				while( $array = mysql_fetch_array($eid) )
				{
					$_data .= "\n+------------------------------------------------------------------------------+";
					$_data .= "\n|Table: ". $array['table'];
					$_data .= "\n|Type: ". $array['type'];
					$_data .= "\n|Possible Keys: ". $array['possible_keys'];
					$_data .= "\n|Key: ". $array['key'];
					$_data .= "\n|Key Len: ". $array['key_len'];
					$_data .= "\n|Ref: ". $array['ref'];
					$_data .= "\n|Rows: ". $array['rows'];
					$_data .= "\n|Extra: ". $array['extra'];
					$_data .= "\n+------------------------------------------------------------------------------+";
				}
			
				$this->write_debug_log( $query, $_data, $endtime );
			}
			else
			{
				$this->write_debug_log( $the_query, $_data, $endtime );
			}
		}
        else if ( $this->obj['debug'] )
        {
        	$endtime  = $Debug->endTimer();
        	
        	$shutdown = $this->is_shutdown ? 'SHUTDOWN QUERY: ' : '';
        	
        	if ( preg_match( "/^(?:\()?select/i", $the_query ) )
        	{
        		$eid = mysql_query("EXPLAIN $the_query", $this->connection_id);
        		
        		$this->debug_html .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FFE8F3' align='center'>
										   <tr>
										   	 <td colspan='8' style='font-size:14px' bgcolor='#FFC5Cb'><b>{$shutdown}Select Query</b></td>
										   </tr>
										   <tr>
										    <td colspan='8' style='font-family:courier, monaco, arial;font-size:14px;color:black'>$the_query</td>
										   </tr>
										   <tr bgcolor='#FFC5Cb'>
											 <td><b>table</b></td><td><b>type</b></td><td><b>possible_keys</b></td>
											 <td><b>key</b></td><td><b>key_len</b></td><td><b>ref</b></td>
											 <td><b>rows</b></td><td><b>Extra</b></td>
										   </tr>\n";
				while( $array = mysql_fetch_array($eid) )
				{
					$type_col = '#FFFFFF';
					
					if ($array['type'] == 'ref' or $array['type'] == 'eq_ref' or $array['type'] == 'const')
					{
						$type_col = '#D8FFD4';
					}
					else if ($array['type'] == 'ALL')
					{
						$type_col = '#FFEEBA';
					}
					
					$this->debug_html .= "<tr bgcolor='#FFFFFF'>
											 <td>$array[table]&nbsp;</td>
											 <td bgcolor='$type_col'>$array[type]&nbsp;</td>
											 <td>$array[possible_keys]&nbsp;</td>
											 <td>$array[key]&nbsp;</td>
											 <td>$array[key_len]&nbsp;</td>
											 <td>$array[ref]&nbsp;</td>
											 <td>$array[rows]&nbsp;</td>
											 <td>$array[Extra]&nbsp;</td>
										   </tr>\n";
				}
				
				$this->sql_time += $endtime;
				
				if ($endtime > 0.1)
				{
					$endtime = "<span style='color:red'><b>$endtime</b></span>";
				}
				
				$this->debug_html .= "<tr>
										  <td colspan='8' bgcolor='#FFD6DC' style='font-size:14px'><b>MySQL time</b>: $endtime</b></td>
										  </tr>
										  </table>\n<br />\n";
			}
			else
			{
			  $this->debug_html .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FEFEFE'  align='center'>
										 <tr>
										  <td style='font-size:14px' bgcolor='#EFEFEF'><b>{$shutdown}Non Select Query</b></td>
										 </tr>
										 <tr>
										  <td style='font-family:courier, monaco, arial;font-size:14px'>$the_query</td>
										 </tr>
										 <tr>
										  <td style='font-size:14px' bgcolor='#EFEFEF'><b>MySQL time</b>: $endtime</span></td>
										 </tr>
										</table><br />\n\n";
			}
		}
		
		$this->query_count++;
        
        $this->obj['cached_queries'][] = $the_query;
        
        return $this->query_id;
    }
    
    /*-------------------------------------------------------------------------*/
    // Fetch a row based on the last query
    /*-------------------------------------------------------------------------*/
    
    function fetch_row($query_id = "")
    {
    	if ($query_id == "")
    	{
    		$query_id = $this->query_id;
    	}
    	
        $this->record_row = mysql_fetch_array($query_id, MYSQL_ASSOC);
        
        return $this->record_row;
    }
    
	/*-------------------------------------------------------------------------*/
	// DROP TABLE
	/*-------------------------------------------------------------------------*/
	
	function sql_drop_table( $table )
	{
		$this->query( "DROP TABLE if exists ".$this->obj['sql_tbl_prefix']."{$table}" );
	}
	
	/*-------------------------------------------------------------------------*/
	// DROP FIELD
	/*-------------------------------------------------------------------------*/
	
	function sql_drop_field( $table, $field )
	{
		$this->query( "ALTER TABLE ".$this->obj['sql_tbl_prefix']."{$table} DROP $field" );
	}
	
	/*-------------------------------------------------------------------------*/
	// ADD FIELD
	/*-------------------------------------------------------------------------*/
	
	function sql_add_field( $table, $field_name, $field_type, $field_default="''" )
	{
		$this->query( "ALTER TABLE ".$this->obj['sql_tbl_prefix']."{$table} ADD $field_name $field_type default {$field_default}" );
	}
	
	/*-------------------------------------------------------------------------*/
	// CHANGE FIELD
	/*-------------------------------------------------------------------------*/
	
	function sql_change_field( $table, $original_field, $field_name, $field_type, $field_default="''" )
	{
		$this->query( "ALTER TABLE ".$this->obj['sql_tbl_prefix']."{$table} CHANGE $original_field $field_name $field_type default {$field_default}" );
	}
	
	/*-------------------------------------------------------------------------*/
	// OPTIMIZE TABLE
	/*-------------------------------------------------------------------------*/
	
	function sql_optimize_table( $table )
	{
		$this->query( "OPTIMIZE TABLE ".$this->obj['sql_tbl_prefix']."{$table}" );
	}
	
	/*-------------------------------------------------------------------------*/
	// ADD FULLTEXT INDEX
	/*-------------------------------------------------------------------------*/
	
	function sql_add_fulltext_index( $table, $field )
	{
		$this->query( "alter table ".$this->obj['sql_tbl_prefix']."{$table} ADD FULLTEXT({$field})" );
	}
	
	/*-------------------------------------------------------------------------*/
	// GET TABLE SCHEMATIC
	/*-------------------------------------------------------------------------*/
	
	function sql_get_table_schematic( $table )
	{
		$qid = $this->query( "SHOW CREATE TABLE ".$this->obj['sql_tbl_prefix']."{$table}" );
		
		if( $qid )
		{
			return $this->fetch_row($qid);
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// IS ALREADY TABLE FULLTEXT?
	/*-------------------------------------------------------------------------*/
	
	function sql_is_currently_fulltext( $table )
	{
		$result = $this->sql_get_table_schematic( $table );
		
		if ( preg_match( "/FULLTEXT KEY/i", $result['Create Table'] ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// Return the version number of the SQL server
	// Should return 'true' version string (ie: 3.23.0)
	// And formatted string (ie: 3230 )
	/*-------------------------------------------------------------------------*/
	
	function sql_get_version()
	{
		if ( ! $this->mysql_version and ! $this->true_version )
		{
			$this->query("SELECT VERSION() AS version");
			
			if ( ! $row = $this->fetch_row() )
			{
				$this->query("SHOW VARIABLES LIKE 'version'");
				$row = $this->fetch_row();
			}
			
			$this->true_version = $row['version'];
			$tmp                = explode( '.', preg_replace( "#[^\d\.]#", "\\1", $row['version'] ) );
			
			$this->mysql_version = sprintf('%d%02d%02d', $tmp[0], $tmp[1], $tmp[2] );
   		}
	}
	
	/*-------------------------------------------------------------------------*/
	// sql_can_fulltext
	// returns whether SQL engine has fulltext abilities
	// returns TRUE or FALSE
	/*-------------------------------------------------------------------------*/
	
	function sql_can_fulltext()
	{
		$this->sql_get_version();
		
		if ( $this->mysql_version >= 32323 AND strtolower($this->connect_vars['mysql_tbl_type']) == 'myisam' )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/*-------------------------------------------------------------------------*/
	// sql_can_fulltext_boolean
	// returns whether SQL engine has boolean fulltext abilities
	// (+word -word, etc)
	// returns TRUE or FALSE
	/*-------------------------------------------------------------------------*/
	
	function sql_can_fulltext_boolean()
	{
		$this->sql_get_version();
		
		if ( $this->mysql_version >= 40010 AND strtolower($this->connect_vars['mysql_tbl_type']) == 'myisam' )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/*-------------------------------------------------------------------------*/
    // Fetch the number of rows affected by the last query
    /*-------------------------------------------------------------------------*/
    
    function get_affected_rows()
    {
        return mysql_affected_rows($this->connection_id);
    }
    
    /*-------------------------------------------------------------------------*/
    // Fetch the number of rows in a result set
    /*-------------------------------------------------------------------------*/
    
    function get_num_rows( $query_id="" )
    {
		if ( ! $query_id )
   		{
    		$query_id = $this->query_id;
    	}

        return mysql_num_rows( $query_id );
    }
    
    /*-------------------------------------------------------------------------*/
    // Fetch the last insert id from an sql autoincrement
    /*-------------------------------------------------------------------------*/
    
    function get_insert_id()
    {
        return mysql_insert_id($this->connection_id);
    }  
    
    /*-------------------------------------------------------------------------*/
    // Free the result set from mySQLs memory
    /*-------------------------------------------------------------------------*/
    
    function free_result($query_id="")
    {
   		if ($query_id == "")
   		{
    		$query_id = $this->query_id;
    	}
    	
    	@mysql_free_result($query_id);
    }
    
    /*-------------------------------------------------------------------------*/
    // Shut down the database
    /*-------------------------------------------------------------------------*/
    
    function close_db()
    { 
    	if ( $this->connection_id )
    	{
        	return @mysql_close( $this->connection_id );
        }
    }
    
    /*-------------------------------------------------------------------------*/
    // Return an array of tables
    /*-------------------------------------------------------------------------*/
    
    function get_table_names()
    {
		$result     = mysql_list_tables($this->obj['sql_database']);
		$num_tables = @mysql_numrows($result);
		for ($i = 0; $i < $num_tables; $i++)
		{
			$tables[] = mysql_tablename($result, $i);
		}
		
		// free result docs say only for select, explain, describe, show queries
		//mysql_free_result($result);
		
		return $tables;
   	}
   	
    /*-------------------------------------------------------------------------*/
    // Check if table exists
    /*-------------------------------------------------------------------------*/
    
    function table_exists( $table )
    {
	    $table_names = $this->get_table_names();
	    
	    $return = 0;
	    
	    if ( in_array( SQL_PREFIX.$table, $table_names ) )
	    {
		    $return = 1;
	    }
	    
	    unset($table_names);
	    
	    return $return;
    }
   	
   	
   	/*-------------------------------------------------------------------------*/
    // Return an array of fields
    /*-------------------------------------------------------------------------*/
    
    function get_result_fields($query_id="")
    {
    
   		if ($query_id == "")
   		{
    		$query_id = $this->query_id;
    	}
    
		while ($field = mysql_fetch_field($query_id))
		{
            $Fields[] = $field;
		}
		
		return $Fields;
   	}
    
    /*-------------------------------------------------------------------------*/
    // INTERNAL: Get error number
    /*-------------------------------------------------------------------------*/
    
    function _get_error_number()
    {
	    $conid = $this->connection_id ? $this->connection_id : '';
	    
    	return @mysql_errno( $conid );
    }
    
    /*-------------------------------------------------------------------------*/
    // INTERNAL: Get error number
    /*-------------------------------------------------------------------------*/
    
    function _get_error_string()
    {
	    $conid = $this->connection_id ? $this->connection_id : '';
	    
    	return @mysql_error( $conid );
    }
    
	/*-------------------------------------------------------------------------*/
	// Use different escape method for different SQL engines
	/*-------------------------------------------------------------------------*/
	
	function add_slashes( $t )
	{
		return ( IPS_MAIN_DB_CLASS_LEGACY ) ? mysql_escape_string($t) : mysql_real_escape_string($t, $this->connection_id );
	}
	
	/*-------------------------------------------------------------------------*/
	// Use different escape method for different SQL engines
	/*-------------------------------------------------------------------------*/
	
	function remove_slashes( $t )
	{
		# Not required for MySQL because we use the mysql_real_escape_string
		
		/*if ( get_magic_quotes_gpc() )
		{
    		$t = stripslashes($t);
    	}*/
    	
    	return $t;
	}
	  
} // end class


?>