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
|   Action controller for admin page
+----------------------------------------------------------------------------
*/

/**
 * Invision Power Board
 * Action controller for admin page
 */

class action_admin
{
	var $install;
	
	function action_admin( & $install )
	{
		$this->install =& $install;
	}
	
	function run()
	{
		/* Check input? */
		if( $this->install->ipsclass->input['sub'] == 'check' )
		{
			if( ! $this->install->ipsclass->input['username'] )
			{
				$errors[] = 'Необходимо указать имя пользователя ';	
			}
			
			if( ! $this->install->ipsclass->input['password'] )
			{
				$errors[] = 'Необходимо указать пароль';	
			}
			else 
			{
				if( $this->install->ipsclass->input['password'] != $this->install->ipsclass->input['confirm_password']	)
				{
					$errors[] = 'Введенные пароли не совпадают';	
				}
			}
			
			if( ! $this->install->ipsclass->input['email'] )
			{
				$errors[] = 'Необходимо указать e-mail адрес';	
			}
			
			if( is_array( $errors ) )
			{
				$this->install->template->warning( $errors );	
			}
			else 
			{
				/* Save Form Data */
				$this->install->saved_data['admin_user']  = $this->install->ipsclass->input['username'];
				$this->install->saved_data['admin_pass']  = $this->install->ipsclass->input['password'];
				$this->install->saved_data['admin_email'] = $this->install->ipsclass->input['email'];

				/* Next Action */
				$this->install->template->page_current = 'install';
				require_once( INS_ROOT_PATH . 'core/actions/install.php' );	
				$action = new action_install( &$this->install );
				$action->run();
				return;				
			}		
		}

		/* Output */
		$this->install->template->append( $this->install->template->admin_page() );		
		$this->install->template->next_action = '?p=admin&sub=check';
	}
}

?>