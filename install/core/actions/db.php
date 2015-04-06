<?php
/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   Invision Power Board INSTALLER
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
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
+----------------------------------------------------------------------------
|   Action controller for DB page
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Action controller for DB page
 */

class action_db
{
	var $install;
	
	function action_db( & $install )
	{
		$this->install =& $install;
	}
	
	function run()
	{
		/* Check input? */
		if ( $this->install->ipsclass->input['sub'] == 'check' )
		{
			/* Make sure the fields were filled out */			
			if ( ! $this->install->ipsclass->input['db_host'] || ! $this->install->ipsclass->input['db_name'] || ! $this->install->ipsclass->input['db_user'] )
			{
				$errors[] = 'Форма должна быть полностью заполнена';
			}
			
			//-----------------------------------------
			// Error check
			//-----------------------------------------
			
			if ( is_array( $errors ) )
			{
				$this->install->template->warning( $errors );
				return;
			}
			
			//-----------------------------------------
			// Load DB driver..
			//-----------------------------------------
			
			require_once( INS_KERNEL_PATH . 'class_db_' . $this->install->saved_data['sql_driver'] . '.php' );
			
			$classname = "db_driver_".$this->install->saved_data['sql_driver'];

			$DB = new $classname;

			$DB->obj['sql_database']   = $this->install->ipsclass->input['db_name'];
			$DB->obj['sql_user']	   = $this->install->ipsclass->input['db_user'];
			$DB->obj['sql_pass']	   = $_REQUEST['db_pass'];
			$DB->obj['sql_host']	   = $this->install->ipsclass->input['db_host'];
			$DB->obj['sql_tbl_prefix'] = $this->install->ipsclass->input['db_pre'];
			
			//--------------------------------------------------
			// Any "extra" configs required for this driver?
			//--------------------------------------------------

			if ( file_exists( INS_ROOT_PATH.'sql/'.$this->install->saved_data['sql_driver'].'_install.php' ) )
			{
				require_once( INS_ROOT_PATH.'sql/'.$this->install->saved_data['sql_driver'].'_install.php' );

				$extra_install           =  new install_extra();
				$extra_install->ipsclass =& $this->install->ipsclass;

				$extra_install->install_form_process();

				if ( count( $extra_install->errors ) )
				{
					$this->install->template->warning( implode( "<br />", $extra_install->errors ) );
				}
				
				if ( is_array( $extra_install->info_extra ) and count( $extra_install->info_extra ) )
				{ 
					foreach( $extra_install->info_extra as $k => $v )
					{
						$this->install->saved_data[ '__sql__' . $k ] = $v;
					}
				}
				
				if ( is_array( $DB->connect_vars ) and count( $DB->connect_vars ) )
				{
					foreach( $DB->connect_vars as $k => $v )
					{
						$DB->connect_vars[ $k ] = $extra_install->info_extra[ $k ];
					}
				}
			}

			//-----------------------------------------
			// Error check
			//-----------------------------------------
			
			if ( is_array( $errors ) )
			{
				$this->install->template->warning( $errors );
				return;
			}
			
			//--------------------------------
			// Make CONSTANT
			//--------------------------------
			
			define( 'SQL_DRIVER'              , $this->install->saved_data['sql_driver'] );
			define( 'IPS_MAIN_DB_CLASS_LOADED', TRUE );

			//--------------------------------
			// Try a DB connection
			//--------------------------------
			
			$DB->return_die = 1;

			if ( ! $DB->connect() )
			{
				$errors[] = $DB->error;
				
				if( !count($errors) )
				{
					$errors[] = "Не получилось соединиться с сервером Базы Данных. Пожалуйста перепроверьте всю информацию и попробуйте снова.";
				}
			}
			
			//-----------------------------------------
			// Error check
			//-----------------------------------------
			
			if ( is_array( $errors ) )
			{
				$this->install->template->warning( $errors );
				return;
			}
			
			/* Save Form Data */
			$this->install->saved_data['db_host']       = $this->install->ipsclass->input['db_host'];
			$this->install->saved_data['db_name'] 	    = $this->install->ipsclass->input['db_name'];
			$this->install->saved_data['db_user'] 		= $this->install->ipsclass->input['db_user'];
			$this->install->saved_data['db_pass'] 		= $_REQUEST['db_pass'];
			$this->install->saved_data['db_pre']  		= $this->install->ipsclass->input['db_pre'];
			$this->install->saved_data['_drop_tables']  = $this->install->ipsclass->input['_drop_tables'];
			
			//-----------------------------------------
			// Tables exist?
			//-----------------------------------------
			
			if ( ! $this->install->saved_data['_drop_tables'] AND $DB->field_exists( 'id', 'members' ) )
			{
				/* Output */
				$this->install->saved_data['_show_overwrite'] = 1;
				$this->install->template->append( $this->install->template->db_page( $drivers ) );
				$this->install->template->next_action = '?p=db&sub=check';
				
				//-----------------------------------------
				// Show overwrite?
				//-----------------------------------------

				$html = "<tr>
                            <td colspan='2'>
 	                                <div class='warning'>
 	                                    <p><strong style='color:red'>Вы пытаетесь установить приложение в базу где уже есть таблицы от предыдущей установки.</strong>
 	                                    <br />Если вы хотите продолжить установку, то помните что все существующие таблицы с данными будут удалены.
 	                                    Если вы хотите установить приложение с использованием этой базы и не потерять существующие таблицы, используйте другой перфикс таблиц.</p>
 	                                    <input type='checkbox' value='1' name='_drop_tables' /><strong>УДАЛИТЬ СУЩЕСТВУЮЩИЕ ТАБЛИЦЫ ПРИ УСТАНОВКЕ</strong>
 	                                </div>
 	                            </td>
						</tr>";

				$this->install->template->page_content = str_replace( '<!--{TOP.SQL}-->', $html, $this->install->template->page_content );
				
				//--------------------------------------------------
				// Any "extra" configs required for this driver?
				//--------------------------------------------------

				if ( file_exists( INS_ROOT_PATH.'sql/'.$this->install->saved_data['sql_driver'].'_install.php' ) )
				{
					require_once( INS_ROOT_PATH.'sql/'.$this->install->saved_data['sql_driver'].'_install.php' );
					$extra_install = new install_extra();

					$this->install->template->page_content = str_replace( '<!--{EXTRA.SQL}-->', $extra_install->install_form_extra(), $this->install->template->page_content );
				}
						
				return;
			}
			
			/* Next Action */
			$this->install->template->page_current = 'admin';
			$this->install->ipsclass->input['sub'] = '';
			require_once( INS_ROOT_PATH . 'core/actions/admin.php' );	
			$action = new action_admin( $this->install );
			$action->run();
			return;
		}
		
		//--------------------------------------------------
		// DO WE HAVE A DB DRIVER SET?
		//--------------------------------------------------

		$this->install->saved_data['sql_driver'] = ( $this->install->saved_data['sql_driver'] == "" ) ? $_REQUEST['sql_driver'] : $this->install->saved_data['sql_driver'];

		if ( ! $this->install->saved_data['sql_driver'] )
		{
			//----------------------------------------------
			// Test to see how many DB driver's we've got..
			//----------------------------------------------

			$dh = opendir( INS_KERNEL_PATH );

			while ( false !== ( $file = @readdir( $dh ) ) )
			{
				if ( preg_match( "/^class_db_([a-zA-Z0-9]*)\.php/i", $file, $driver ) )
				{
					$drivers[] = $driver[1];
				}
			}

	 		@closedir( $dh );

	 		//----------------------------------------------
	 		// Got more than one?
	 		//----------------------------------------------

	 		if ( count($drivers) > 1 )
	 		{
	 			//------------------------------------------
	 			// Show choice screen first...
	 			//------------------------------------------
				
				/* Output */
				$this->install->template->append( $this->install->template->db_check_page( $drivers ) );		
				$this->install->template->next_action = '?p=db';
				return;
	 		}
	 		else
	 		{
	 			//------------------------------------------
	 			// Use only driver installed
	 			//------------------------------------------

	 			$this->install->saved_data['sql_driver'] = $drivers[0];
	 		}
		}
		
		/* Output */
		$this->install->template->append( $this->install->template->db_page() );		
		$this->install->template->next_action = '?p=db&sub=check';
		
		//--------------------------------------------------
		// Any "extra" configs required for this driver?
		//--------------------------------------------------

		if ( file_exists( INS_ROOT_PATH.'sql/'.$this->install->saved_data['sql_driver'].'_install.php' ) )
		{
			require_once( INS_ROOT_PATH.'sql/'.$this->install->saved_data['sql_driver'].'_install.php' );
			$extra_install = new install_extra();

			$this->install->template->page_content = str_replace( '<!--{EXTRA.SQL}-->', $extra_install->install_form_extra(), $this->install->template->page_content );
		}
	}
	
}

?>