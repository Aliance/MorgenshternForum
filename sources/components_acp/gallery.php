<?php

/*
+--------------------------------------------------------------------------
|   Invision Gallery Module
|   ========================================
|   by Joshua Williams
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisiongallery.com
|   Email: josh@invisiongallery.com
+---------------------------------------------------------------------------
|
|   > Main Admin Module
|   > Script written by Joshua Williams
|   $Id: gallery.php,v 1.1.1.1 2005/07/11 20:43:50 kinderstod Exp $
+--------------------------------------------------------------------------
*/

define( 'GALLERY_PATH'	 , ROOT_PATH.'modules/gallery/' );

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Некорректный адрес</h1>Вы не имеете доступа к этому файлу напрямую. Если вы недавно обновляли форум, вы должны обновить все соответствующие файлы.";
	exit();
}

class ad_gallery {

	var $base_url;
        var $ipsclass;
        var $gallery_lib;

	function auto_run()
	{ 
		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------
		
		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );
		
		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}
		
		$this->ipsclass->admin->page_title = "Управление Invision Gallery";
		
		$this->ipsclass->admin->page_detail = "Эта секция позволяет вам управлять галереей IP.Gallery .";
		
		$this->ipsclass->admin->nav[] = array( 'section=components&act=gallery'              , 'Управление Invision Gallery' );
		
		//-----------------------------------------
		// Do some set up
		//-----------------------------------------
		
		if ( ! @is_dir( ROOT_PATH.'/modules/gallery' ) )
		{
			$this->ipsclass->admin->show_inframe("http://www.invisiongallery.com/?why");
		}
		else
		{
			define( 'IPB_CALLED', 1 );
    		$this->ipsclass->DB->load_cache_file( ROOT_PATH . 'sources/sql/'.SQL_DRIVER.'_gallery_queries.php', 'gallery_sql_queries' );
    		$this->ipsclass->DB->load_cache_file( ROOT_PATH . 'sources/sql/'.SQL_DRIVER.'_gallery_admin_queries.php', 'gallery_admin_sql_queries' );
           
            $section = ( $this->ipsclass->input['code'] ) ? "ad_{$this->ipsclass->input['code']}" : "ad_overview";

            if( !in_array( $section, array( 'ad_overview', 'ad_albums', 'ad_cats', 'ad_groups', 'ad_media', 'ad_postform', 'ad_stats', 'ad_tools' ) ) )
            {
	            $section = 'ad_overview';
            }

			require ROOT_PATH.'modules/gallery/lib/gallery_library.php';
                        $this->gallery_lib = new gallery_lib();
                        $this->gallery_lib->ipsclass =& $this->ipsclass;

			require ROOT_PATH.'modules/gallery/admin/'.$section.'.php';
			
	           $PLUGIN = new ad_plugin_gallery_sub();
                   $PLUGIN->ipsclass =& $this->ipsclass;
                   $PLUGIN->glib =& $this->gallery_lib;
                   $PLUGIN->auto_run();
		}		
	}		
}

?>