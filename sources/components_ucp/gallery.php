<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.1.7
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2005 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Wednesday 27th of September 2006 08:13:32 AM
|   Release: 2871a4c8b602386260eeb8bf9da57e29
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
|   > UCP: LIBRARY
|   > Module written by Matt Mecham
|   > Date started: 18th April 2005
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

//-----------------------------------------
// Must be "components_ucp_{component name}
//-----------------------------------------

class components_ucp_gallery
{
	/*-------------------------------------------------------------------------*/
	// Build menu
	// Use: $content .= menu_bar_new_link( $url, $name ) for the links
	// Use: menu_bar_new_category( 'Blog', $content ) for the content
	/*-------------------------------------------------------------------------*/

	function ucp_build_menu()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$content = "";
		
		//-----------------------------------------
		// Get links
		//-----------------------------------------
		
		if ( $this->ipsclass->vars['gallery_images_path'] != "" )
		{
			$content .= $this->ipsclass->compiled_templates['skin_ucp']->menu_bar_new_link( "{$this->ipsclass->base_url}automodule=gallery&cmd=albums",
																							$this->ipsclass->lang['m_gallery_albums'] );
			$content .= $this->ipsclass->compiled_templates['skin_ucp']->menu_bar_new_link( "{$this->ipsclass->base_url}automodule=gallery&cmd=favs",
																							$this->ipsclass->lang['m_gallery_favs'] );
		}
		
		if ( $content )
		{
			return $this->ipsclass->compiled_templates['skin_ucp']->menu_bar_new_category( $this->ipsclass->lang['m_gallery'], $content );
		}
		else
		{
			return '';
		}
	}
}

?>