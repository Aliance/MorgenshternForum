<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.0 PDR 5
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services, Inc.
|   =============================================
|   
|   Nullfied by SneakerXZ
|   
+--------------------------------------------------------------------------
*/



class gallery_admin_sql_queries extends db_driver
{

     var $db  = "";
     var $tbl = "";
     
    /*========================================================================*/
    // Set up...             
    /*========================================================================*/  
                   
    function sql_queries( $obj )
    {
    	$this->db = $obj;
    	
    	if ( ! $this->db->obj['sql_tbl_prefix'] )
    	{
    		$this->db->obj['sql_tbl_prefix'] = '".SQL_PREFIX."';
    	}
    	
    	$this->tbl = $this->db->obj['sql_tbl_prefix'];
    }
    
    /*========================================================================*/
    
    /* SQL Queries for ad_albums.php */

    function get_mem_albums( $a )
    {
    	return "SELECT a.*, m.name AS member_name, c.name AS category_name 
                FROM ".SQL_PREFIX."gallery_albums a, ".SQL_PREFIX."members m
                LEFT JOIN ".SQL_PREFIX."gallery_categories c ON ( c.id=a.category_id )
                WHERE a.id={$a['id']} AND m.id=a.member_id";
    } 
           
    // AHHHH, YUCK! Evil Query....must replace soon
    function get_albums( $a )
    {
    	return "SELECT a.*, m.name AS member_name, SUM( i.views ) AS total_views, SUM( i.file_size ) AS total_size
                FROM ".SQL_PREFIX."gallery_albums a, ".SQL_PREFIX."members m
                LEFT JOIN ".SQL_PREFIX."gallery_images i ON ( i.album_id=a.id ) 
                WHERE {$a['where']} m.id=a.member_id
                GROUP BY a.id
                ORDER BY {$a['sort_key']} {$a['sort_by']}
                LIMIT {$a['st']}, 20";
    }

    /* SQL Queries for ad_groups.php */

    function get_groups( $a )
    {
        return "SELECT *, COUNT(".SQL_PREFIX."members.id) as count FROM ".SQL_PREFIX."groups
		        LEFT JOIN ".SQL_PREFIX."members ON (".SQL_PREFIX."members.mgroup = ".SQL_PREFIX."groups.g_id)
		        GROUP BY ".SQL_PREFIX."groups.g_id ORDER BY ".SQL_PREFIX."groups.g_title";
    }

    /* SQL Queries for ad_postform.php */

    function postform_drop( $a )
    {
        return "ALTER TABLE ".SQL_PREFIX."gallery_images DROP field_{$a['id']}";
    }

    function postform_add( $a )
    {
        return "ALTER TABLE ".SQL_PREFIX."gallery_images ADD field_{$a['new_id']} text default ''";
    }

    /* SQL Queries for ad_stats.php */

    function get_group_stats( $a )
    {
        return "SELECT g.g_title, g.g_id, SUM( i.file_size ) as diskspace, COUNT( i.file_size ) as uploads
                     FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_images i
                     WHERE i.member_id=m.id AND m.mgroup=g.g_id GROUP BY m.mgroup ORDER BY diskspace DESC
                     ";
    }

    function get_cat_stats( $a )
    {
        return "SELECT c.name, SUM( i.file_size ) as diskspace, COUNT( i.file_size ) as uploads
                     FROM ".SQL_PREFIX."gallery_categories c, ".SQL_PREFIX."gallery_images i
                     WHERE i.category_id=c.id GROUP BY c.id ORDER BY diskspace DESC ";
    }

    function get_top5_diskspace( $a )
    {
        return "SELECT m.name, m.id AS mid, SUM( i.file_size ) as diskspace, COUNT( i.file_size ) as uploads
                    FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_images i
                    WHERE i.member_id=m.id GROUP BY m.id, m.name ORDER BY diskspace DESC LIMIT 0, 5
                    ";
    }

    function get_group_overview( $a )
    {
        return "SELECT m.name, m.id AS mid, SUM( b.size ) as transfer, COUNT( b.size ) as total, g.g_title, g.g_id 
                         FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_bandwidth b
                         WHERE b.member_id=m.id AND m.mgroup=g.g_id GROUP BY m.mgroup ORDER BY transfer DESC";
    }

    function get_top5_bandwidth( $a )
    {
        return "SELECT m.name, m.id AS mid, SUM( b.size ) as transfer, COUNT( b.size ) as total
                         FROM ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_bandwidth b
                         WHERE b.member_id=m.id GROUP BY m.id, m.name ORDER BY transfer DESC LIMIT 0, 5
                         ";
    }

    function get_top5_files( $a )
    {
        return "SELECT i.file_name, i.id as fid, SUM( b.size ) as transfer, COUNT( b.size ) as total, b.file_name AS m_file_name
                         FROM ".SQL_PREFIX."gallery_images i, ".SQL_PREFIX."gallery_bandwidth b
                         WHERE b.file_name = i.masked_file_name GROUP BY b.file_name ORDER BY transfer DESC LIMIT 0, 5
                         ";
    }

    /* SQL Queries for ad_tools.php */


    function get_top5_files_time_limited( $a )
    {
        return "SELECT i.file_name, i.id as fid, SUM( b.size ) as transfer, COUNT( b.size ) as total, b.file_name AS m_file_name
                         FROM ".SQL_PREFIX."gallery_images i, ".SQL_PREFIX."gallery_bandwidth b
                         WHERE b.member_id={$a['mid']} AND b.file_name = i.masked_file_name GROUP BY b.file_name ORDER BY transfer DESC LIMIT 0, 5
                         ";
    }
    
    function get_group_rep_diskspace( $a )
    {
        return "SELECT g.g_title, g.g_id, SUM( i.file_size ) as group_size, AVG( file_size ) as group_avg_size, COUNT( i.file_size ) as group_uploads
                FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_images i
                WHERE i.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']} GROUP BY m.mgroup
                     ";
    }

    function get_group_rep_bandwidth( $a )
    {
        return "SELECT g.g_title, g.g_id, SUM( b.size ) as group_transfer, COUNT( b.size ) as group_viewed
                FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_bandwidth b
                WHERE b.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']} GROUP BY m.mgroup
                    ";
    }

    function get_group_ecard_count( $a )
    {
        return "SELECT COUNT(*) AS ecards
                FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_ecardlog e
                WHERE e.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']} GROUP BY m.mgroup
                    ";
    }

    function get_group_comment_count( $a )
    {
        return "SELECT COUNT(*) AS comments
                FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_comments c
                WHERE c.author_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']} GROUP BY m.mgroup
                     ";
    }

    function get_group_rating_overview( $a )
    {
        return  "SELECT COUNT(rate) AS total_rates, AVG(rate) AS avg_rate
                 FROM ".SQL_PREFIX."groups g, ".SQL_PREFIX."members m, ".SQL_PREFIX."gallery_ratings r
                 WHERE r.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']} GROUP BY m.mgroup
                    ";
    }

    function get_file_info( $a )
    {
        return "SELECT i.*, m.name as mname, m.id as mid 
                FROM ".SQL_PREFIX."gallery_images i, ".SQL_PREFIX."members m 
                WHERE i.id={$a['fid']} AND m.id=i.member_id";
    }

    function get_ecard( $a )
    {
        return "SELECT e.*, m.name
                FROM ".SQL_PREFIX."gallery_ecardlog e, ".SQL_PREFIX."members m
                WHERE m.id=e.member_id {$a['q']} ORDER BY e.date DESC LIMIT {$a['st']}, 25
                    ";
    }

    function get_rating_log( $a )
    {
        return "SELECT r.*, i.file_name
                FROM ".SQL_PREFIX."gallery_ratings r, ".SQL_PREFIX."gallery_images i
                WHERE r.member_id={$a['mid']} AND i.id=r.img_id ORDER BY r.date DESC LIMIT {$a['st']}, 25
                    ";
    }

} // end class


?>
