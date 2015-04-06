<?php
/*
+--------------------------------------------------------------------------
|   Invision Gallery Module v<#VERSION#>
|   ========================================
|   by Adam Kinder
|   (c) 2001 - 2005 Invision Power Services
|   ========================================
|   
|   Nullfied by SneakerXZ
|   
+---------------------------------------------------------------------------
*/


/**
* Admin/Stats
*
* Shows Gallery stats
*
* @package		Gallery
* @subpackage 	Admin
* @author   	Adam Kinder
* @version		<#VERSION#>
* @since 		1.0
*/

//---------------------------------------
// Security check
//---------------------------------------

if ( IPB_CALLED != 1 )
{
    print "You cannot access this module in this manner";
    exit();
}

//---------------------------------------
// Carry on!
//---------------------------------------

class ad_plugin_gallery_sub {

    var $ipsclass;
    var $glib;
    var $forumfunc;
    var $modules;
    /**
     * ad_plugin_gallery::ad_plugin_gallery_sub()
     * 
	 * Class Constructor
	 * 
     * @return void
     **/
    function auto_run()
    {
		$this->ipsclass->forums->forums_init();
		
		require ROOT_PATH.'sources/lib/admin_forum_functions.php';
		
		$this->forumfunc = new admin_forum_functions();

        //---------------------------------------
        // Kill globals - globals bad, Homer good.
        //---------------------------------------

        $tmp_in = array_merge( $_GET, $_POST, $_COOKIE );

        foreach ( $tmp_in as $k => $v )
        {
            unset($$k);
        }

        //--------------------------------------------
        // Get the sync module
        //--------------------------------------------

        if ( USE_MODULES == 1 )
        {
            require ROOT_PATH."modules/ipb_member_sync.php";

            $this->modules = new ipb_member_sync();
        }

		switch( $this->ipsclass->input['pg'] )
		{
            case 'get_chart':
                $this->_get_chart();
            break;
            default:
                $this->stats();
            break;
        }

    }

   /******************************************************************
    *
    * Statistics
    *
    **/

   /**
    * ad_plugin_gallery::stats()
    * 
	* Displays the bandwidth/diskspace stats screen
	* 
    * @return void
    **/
    function stats()
    {
        // Page Information
        $this->ipsclass->admin->page_title   = "Statistics Overview";
        $this->ipsclass->admin->page_detail  = "Here you will find detailed statistcs related to diskspace usage and bandwidth usage";

        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Definition", "25%" );
        $this->ipsclass->adskin->td_header[] = array( "Value"     , "25%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Overall Overview" );

        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( file_size ) as total_size, COUNT( file_size ) as total_uploads', 'from' => 'gallery_images' ) );
        $this->ipsclass->DB->simple_exec();

        $stats = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( size ) as total_transfer, COUNT( size ) as total_viewed', 'from' => 'gallery_bandwidth' ) );
        $this->ipsclass->DB->simple_exec();


        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );

        $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Gallery Diskspace</B>" , $this->glib->byte_to_kb( $stats['total_size'] ),
                                                  "<B>Total Gallery Uploads</B>"   , $stats['total_uploads'],
                                          )      );
        if( $this->ipsclass->vars['gallery_detailed_bandwidth'] )
        {
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>Total Gallery Transfer in the past {$this->ipsclass->vars['gallery_bandwidth_period']} hours</B>" , $this->glib->byte_to_kb( $stats['total_transfer'] ),
                                              "<B>Total Gallery Views in the past {$this->ipsclass->vars['gallery_bandwidth_period']} hours</B>"   , $stats['total_viewed']
                                      )      );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        $this->ipsclass->html .= "<font size='5'><b>Diskspace Usage</b></font><BR><BR>";

        //+-----------------------------------------------------------

        $this->ipsclass->adskin->td_header[] = array( "Group"                  , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Diskspace Usage"       , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Percent of total usage", "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Uploaded files"       , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Percent of total files", "20%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Group Overview" );

        $this->ipsclass->DB->cache_add_query( 'get_group_stats', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $dp_percent = round( $i['diskspace'] / $stats['total_size'], 2 ) * 100;
            $up_percent = round( $i['uploads'] / $stats['total_uploads'], 2) * 100;
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['g_title']}</B> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dogroupsrch&viewgroup={$i['g_id']}' title='View group report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                      $this->glib->byte_to_kb( $i['diskspace'] ),
                                                      $dp_percent . '%',
                                                      $i['uploads'],
                                                      $up_percent.'%',
                                              )     );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        //+-----------------------------------------------------------

        $this->ipsclass->adskin->td_header[] = array( "Category"                  , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Diskspace Usage"       , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Percent of total usage", "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Uploaded files"       , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Percent of total files", "20%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Category Overview" );

        $this->ipsclass->DB->cache_add_query( 'get_cat_stats', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $dp_percent = round( $i['diskspace'] / $stats['total_size'], 2 ) * 100;
            $up_percent = round( $i['uploads'] / $stats['total_uploads'], 2) * 100;
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['name']}</B>",
                                                      $this->glib->byte_to_kb( $i['diskspace'] ),
                                                      $dp_percent . '%',
                                                      $i['uploads'],
                                                      $up_percent.'%',
                                              )     );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        //+-----------------------------------------------------------

        $this->ipsclass->adskin->td_header[] = array( "Member"                  , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Diskspace Usage"       , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Percent of total usage", "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Uploaded files"       , "20%" );
        $this->ipsclass->adskin->td_header[] = array( "Percent of total files", "20%" );

        //+-----------------------------------------------------------

        $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Top 5 diskspace users" );

        $this->ipsclass->DB->cache_add_query( 'get_top5_diskspace', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $dp_percent = round( $i['diskspace'] / $stats['total_size'], 2 ) * 100;
            $up_percent = round( $i['uploads'] / $stats['total_uploads'], 2) * 100;
            $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['name']}</B> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=domemsrch&viewuser={$i['mid']}' title='View member report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                      $this->glib->byte_to_kb( $i['diskspace'] ),
                                                      $dp_percent . '%',
                                                      $i['uploads'],
                                                      $up_percent.'%',
                                              )     );
        }

        $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

        //+-----------------------------------------------------------
        if( $this->ipsclass->vars['gallery_detailed_bandwidth'] )
        {
            $this->ipsclass->html .= "<font size='5'><b>Bandwidth Usage</b></font><BR><BR>";

            //+-----------------------------------------------------------

            $this->ipsclass->adskin->td_header[] = array( "Group"                  , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Transfer"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of total transfer", "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Image Loads"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of total loads", "20%" );

            //+-----------------------------------------------------------

            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Group Overview" );

            $this->ipsclass->DB->cache_add_query( 'get_group_overview', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $dp_percent = round( $i['transfer'] / $stats['total_transfer'], 2 ) * 100;
                $up_percent = round( $i['total'] / $stats['total_viewed'], 2) * 100;
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['g_title']}</B> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dogroupsrch&viewgroup={$i['g_id']}' title='View group report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                          $this->glib->byte_to_kb( $i['transfer'] ),
                                                          $dp_percent . '%',
                                                          $i['total'],
                                                          $up_percent.'%',
                                                  )     );
            }

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            //+-----------------------------------------------------------

            $this->ipsclass->adskin->td_header[] = array( "Member"                  , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Transfer"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of total transfer", "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Image Loads"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of total loads", "20%" );

            //+-----------------------------------------------------------


            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Top 5 bandwidth users" );

            $this->ipsclass->DB->cache_add_query( 'get_top5_bandwidth', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();


            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $dp_percent = round( $i['transfer'] / $stats['total_transfer'], 2 ) * 100;
                $up_percent = round( $i['total'] / $stats['total_viewed'], 2) * 100;
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['name']}</B> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=domemsrch&viewuser={$i['mid']}' title='View member report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                          $this->glib->byte_to_kb( $i['transfer'] ),
                                                          $dp_percent . '%',
                                                          $i['total'],
                                                          $up_percent.'%',
                                                  )     );
            }

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();

            //+-----------------------------------------------------------

            $this->ipsclass->adskin->td_header[] = array( "File"                  , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Transfer"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of total transfer", "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Image Loads"       , "20%" );
            $this->ipsclass->adskin->td_header[] = array( "Percent of total loads", "20%" );

            //+-----------------------------------------------------------


            $this->ipsclass->html .= $this->ipsclass->adskin->start_table( "Top 5 files" );

           $this->ipsclass->DB->cache_add_query( 'get_top5_files', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();

            while( $i = $this->ipsclass->DB->fetch_row() )
            {
                $dp_percent = round( $i['transfer'] / $stats['total_transfer'], 2 ) * 100;
                $up_percent = round( $i['total'] / $stats['total_viewed'], 2) * 100;

                if( preg_match( "/^(tn_)/", $i['m_file_name'] ) )
                {
                    $i['file_name'] = 'tn_'.$i['file_name'];
                }
                
                $this->ipsclass->html .= $this->ipsclass->adskin->add_td_row( array( "<B>{$i['file_name']}</B> <a href='{$this->ipsclass->base_url}&section=components&act=gallery&code=tools&tool=dofilesrch&viewfile={$i['fid']}' title='View file report'><img src='{$this->ipsclass->adskin->img_url}/images/acp_search.gif' border='0' alt='..by sender name'></a>",
                                                          $this->glib->byte_to_kb( $i['transfer'] ),
                                                          $dp_percent . '%',
                                                          $i['total'],
                                                          $up_percent.'%',
                                                  )     );
            }

            $this->ipsclass->html .= $this->ipsclass->adskin->end_table();
            $this->ipsclass->html .= "<img src='{$this->ipsclass->base_url}&section=components&act=gallery&code=stats&pg=get_chart'>";
        }

        $this->ipsclass->admin->output();
    }
    /*
    function _get_chart()
    {
        global $this->ipsclass->DB;
        
        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( file_size ) as total_size, COUNT( file_size ) as total_uploads', 'from' => 'gallery_images' ) );
        $this->ipsclass->DB->simple_exec();

        $stats = $this->ipsclass->DB->fetch_row();

        $this->ipsclass->DB->simple_construct( array( 'select' => 'SUM( size ) as total_transfer, COUNT( size ) as total_viewed', 'from' => 'gallery_bandwidth' ) );
        $this->ipsclass->DB->simple_exec();

        $stats = array_merge( $stats, $this->ipsclass->DB->fetch_row() );        
        
        $this->ipsclass->DB->cache_add_query( 'get_top5_files', array(), 'gallery_admin_sql_queries' ); $this->ipsclass->DB->simple_exec();
        
        require( ROOT_PATH.'modules/gallery/lib/class_chart.php' );
        $chart = new class_chart();
        $chart->chart_init( array( 'title' => 'Top 5 Files', 'font' => ROOT_PATH.'Decker.ttf'  ) ); 
        
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $dp_percent = round( $i['transfer'] / $stats['total_transfer'], 2 ) * 100;
            $up_percent = round( $i['total'] / $stats['total_viewed'], 2) * 100;

            if( preg_match( "/^(tn_)/", $i['m_file_name'] ) )
            {
                $i['file_name'] = 'tn_'.$i['file_name'];
            }
            
            $chart_data[$i['file_name']] = $i['transfer'];

        }
        
        $chart->barchart_draw( $chart_data );
 
    }
    */
    
    function _get_chart()
    {   
        require( ROOT_PATH.'modules/gallery/lib/class_chart.php' );
        $chart = new class_chart();
        $chart->chart_init( array( 'title' => "Bandwidth Useage ( Previous {$this->ipsclass->vars['gallery_bandwidth_period']} hours )", 'font' => ROOT_PATH.'Decker.ttf'  ) ); 
        
        $bdp = $this->ipsclass->vars['gallery_bandwidth_period'] * 60 * 60;
        
        $this->ipsclass->DB->simple_construct( array( 'select' => '*', 
                                      'from'   => 'gallery_bandwidth', 
                                      'where'  => "date > " . time() - $bdp,
                                      'order'  => 'date ASC') );
        $this->ipsclass->DB->simple_exec();
        
        while( $i = $this->ipsclass->DB->fetch_row() )
        {
            $t_data = date("D", $i['date']);
            
            $chart_data[$t_data] += round( ( $i['size'] / 1024 ), 2 );
            
            //$chart_data[$i['date']] = $i['size'];
        }
        
        $chart->barchart_draw( $chart_data );
 
    } 

}
?>
