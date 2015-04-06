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
|   Action controller for Adresses page
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Action controller for Adresses page
 */

class action_address
{
	var $install;
	
	function action_address( & $install )
	{
		$this->install =& $install;
	}
	
	function run()
	{
		/* Check input? */
		if( $this->install->ipsclass->input['sub'] == 'check' )
		{
			/* Check Directory */
			if( ! $this->install->ipsclass->input['install_dir'] OR ! ( is_dir( $this->install->ipsclass->input['install_dir'] ) ) )
			{
				$errors[] = 'Указанный путь не существует в системе';
			}
			
			/* Check URL */
			if( ! $this->install->ipsclass->input['install_url'] )
			{
				$errors[] = 'Адрес установки не указан';	
			}

			if( is_array( $errors ) )
			{
				$this->install->template->warning( $errors );	
			}
			else 
			{
				/* Save Form Data */
				$this->install->saved_data['install_dir'] = preg_replace( "#(//)$#", "", str_replace( '\\', '/', $this->install->ipsclass->input['install_dir'] ) . '/' );
				$this->install->saved_data['install_url'] = preg_replace( "#(//)$#", "", str_replace( '\\', '/', $this->install->ipsclass->input['install_url'] ) . '/' );
				
				/* Next Action */
				$this->install->template->page_current = 'db';
				$this->install->ipsclass->input['sub'] = '';
				require_once( INS_ROOT_PATH . 'core/actions/db.php' );	
				$action = new action_db( &$this->install );
				$action->run();
				return;
			}
		}
		
		/* Guess at directory */
		$dir = str_replace( 'installer', '' , getcwd() );
		$dir = str_replace( 'install'  , '' , getcwd() );
		$dir = str_replace( '\\'       , '/', $dir );

		/* Guess at URL */
		$url = str_replace( "/installer/index.php"   , "", $this->install->ipsclass->my_getenv('HTTP_REFERER') );
		$url = str_replace( "/installer/"            , "", $url);
		$url = str_replace( "/installer"             , "", $url);
		$url = str_replace( "/install/index.php"     , "", $this->install->ipsclass->my_getenv('HTTP_REFERER') );
		$url = str_replace( "/install/"              , "", $url);
		$url = str_replace( "/installr"              , "", $url);
		$url = str_replace( "index.php"              , "", $url);
		$url = preg_replace( "!\?(.+?)*!"            , "", $url );	
		$url = "{$url}/";
		
		/* Page Output */
		$this->install->template->append( $this->install->template->address_page( $dir, $url ) );
		$this->install->template->next_action = '?p=address&sub=check';
	}
}

?>