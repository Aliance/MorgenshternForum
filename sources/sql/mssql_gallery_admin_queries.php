<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v<{%dyn.down.var.human.version%}>
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

    function gallery_admin_sql_queries( &$obj )
    {
    	$this->db = &$obj;

    	if ( ! $this->db->obj['sql_tbl_prefix'] )
    	{
    		$this->db->obj['sql_tbl_prefix'] = 'ibf_';
    	}

    	$this->tbl = $this->db->obj['sql_tbl_prefix'];
    }

    /*========================================================================*/

    /* SQL Queries for ad_albums.php */

    function get_mem_albums( $a )
    {
    	return "SELECT a.*, m.name AS member_name
                FROM ibf_gallery_albums a, ibf_members m
                WHERE a.id={$a['id']} AND m.id=a.member_id";
    }

    // AHHHH, YUCK! Evil Query....must replace soon
    function get_albums( $a )
    {
        $this->db->cur_startrow = $a['st'];
    	return "SELECT TOP ".($a['st']+20)." a.id, a.public_album, a.name, a.description, a.images, a.comments, a.last_pic, m.name AS member_name, SUM( i.views ) AS total_views, SUM( i.file_size ) AS total_size
                FROM ibf_gallery_albums a
                INNER JOIN ibf_members m ON m.id=a.member_id
                LEFT JOIN ibf_gallery_images i ON ( i.album_id=a.id )
                WHERE {$a['where']} 1=1
                GROUP BY a.id, a.public_album, a.name, a.description, a.images, a.comments, a.last_pic, m.name
                ORDER BY {$a['sort_key']} {$a['sort_by']}";
    }

    /* SQL Queries for ad_groups.php */

    function get_groups( $a )
    {
      return "SELECT ibf_groups.g_id, ibf_groups.g_title, g_max_diskspace, g_max_upload, g_max_transfer, g_max_views, g_create_albums, g_album_limit, g_img_album_limit, g_slideshows, g_favorites, g_comment, g_rate, g_ecard, g_edit_own, g_del_own, g_move_own, g_mod_albums, g_img_local, g_movies, g_movie_size, COUNT(ibf_members.id) as count FROM ibf_groups
		      LEFT JOIN ibf_members ON (ibf_members.mgroup = ibf_groups.g_id)
		      GROUP BY ibf_groups.g_id, ibf_groups.g_title, g_max_diskspace, g_max_upload, g_max_transfer, g_max_views, g_create_albums, g_album_limit, g_img_album_limit, g_slideshows, g_favorites, g_comment, g_rate, g_ecard, g_edit_own, g_del_own, g_move_own, g_mod_albums, g_img_local, g_movies, g_movie_size ORDER BY ibf_groups.g_title";
    }

    /* SQL Queries for ad_postform.php */

    function postform_drop( $a )
    {
        return "ALTER TABLE ibf_gallery_images DROP field_{$a['id']}";
    }

    function postform_add( $a )
    {
        return "ALTER TABLE ibf_gallery_images ADD field_{$a['new_id']} text default ''";
    }

    /* SQL Queries for ad_stats.php */

    function get_group_stats( $a )
    {
        return "SELECT g.g_title, g.g_id, SUM( i.file_size ) as diskspace, COUNT( i.file_size ) as uploads
                FROM ibf_groups g, ibf_members m, ibf_gallery_images i
                WHERE i.member_id=m.id AND m.mgroup=g.g_id GROUP BY g.g_title, g.g_id ORDER BY diskspace DESC";
    }

    function get_cat_stats( $a )
    {
        return "SELECT c.id, c.name, SUM( i.file_size ) as diskspace, COUNT( i.file_size ) as uploads
                FROM ibf_gallery_categories c, ibf_gallery_images i
                WHERE i.category_id=c.id GROUP BY c.id, c.name ORDER BY diskspace DESC ";
    }

    function get_top5_diskspace( $a )
    {
        return "SELECT TOP 5 m.name, m.id AS mid, SUM( i.file_size ) as diskspace, COUNT( i.file_size ) as uploads
                FROM ibf_members m, ibf_gallery_images i
                WHERE i.member_id=m.id GROUP BY m.id, m.name ORDER BY diskspace DESC";
    }

    function get_group_overview( $a )
    {
        return "SELECT SUM( b.size ) as transfer, COUNT( b.size ) as total, g.g_title, g.g_id
                FROM ibf_groups g, ibf_members m, ibf_gallery_bandwidth b
                WHERE b.member_id=m.id AND m.mgroup=g.g_id GROUP BY g.g_id, g.g_title ORDER BY transfer DESC";
    }

    function get_top5_bandwidth( $a )
    {
        return "SELECT TOP 5 m.name, m.id AS mid, SUM( b.size ) as transfer, COUNT( b.size ) as total
                FROM ibf_members m, ibf_gallery_bandwidth b
                WHERE b.member_id=m.id GROUP BY m.id, m.name ORDER BY transfer DESC";
    }

    function get_top5_files( $a )
    {
        return "SELECT TOP 5 i.file_name, i.id as fid, SUM( b.size ) as transfer, COUNT( b.size ) as total, b.file_name AS m_file_name
                FROM ibf_gallery_images i, ibf_gallery_bandwidth b
                WHERE b.file_name = i.masked_file_name GROUP BY b.file_name, i.file_name, i.id ORDER BY transfer DESC";
    }

    /* SQL Queries for ad_tools.php */


    function get_top5_files_time_limited( $a )
    {
        return "SELECT TOP 5 i.file_name, i.id as fid, SUM( b.size ) as transfer, COUNT( b.size ) as total, b.file_name AS m_file_name
                FROM ibf_gallery_images i, ibf_gallery_bandwidth b
                WHERE b.member_id={$a['mid']} AND b.file_name = i.masked_file_name
                GROUP BY b.file_name, i.file_name, i.id ORDER BY transfer DESC";
    }

    function get_group_rep_diskspace( $a )
    {
        return "SELECT g.g_title, g.g_id, SUM( i.file_size ) as group_size, AVG( file_size ) as group_avg_size, COUNT( i.file_size ) as group_uploads
                FROM ibf_groups g, ibf_members m, ibf_gallery_images i
                WHERE i.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']}
                GROUP BY g.g_title, g.g_id";
    }

    function get_group_rep_bandwidth( $a )
    {
        return "SELECT g.g_title, g.g_id, SUM( b.size ) as group_transfer, COUNT( b.size ) as group_viewed
                FROM ibf_groups g, ibf_members m, ibf_gallery_bandwidth b
                WHERE b.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']}
                GROUP BY g.g_title, g.g_id";
    }

    function get_group_ecard_count( $a )
    {
        return "SELECT COUNT(*) AS ecards
                FROM ibf_groups g, ibf_members m, ibf_gallery_ecardlog e
                WHERE e.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']}";
    }

    function get_group_comment_count( $a )
    {
        return "SELECT COUNT(*) AS comments
                FROM ibf_groups g, ibf_members m, ibf_gallery_comments c
                WHERE c.author_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']}";
    }

    function get_group_rating_overview( $a )
    {
        return  "SELECT COUNT(rate) AS total_rates, AVG(rate) AS avg_rate
                 FROM ibf_groups g, ibf_members m, ibf_gallery_ratings r
                 WHERE r.member_id=m.id AND m.mgroup=g.g_id AND m.mgroup={$a['gid']}";
    }

    function get_file_info( $a )
    {
        return "SELECT i.*, m.name as mname, m.id as mid
                FROM ibf_gallery_images i, ibf_members m
                WHERE i.id={$a['fid']} AND m.id=i.member_id";
    }

    function get_ecard( $a )
    {
        $this->db->cur_startrow = $a['st'];
        return "SELECT TOP ".($a['st']+25)." e.*, m.name
                FROM ibf_gallery_ecardlog e, ibf_members m
                WHERE m.id=e.member_id {$a['q']} ORDER BY e.date DESC";
    }

    function get_rating_log( $a )
    {
        $this->db->cur_startrow = $a['st'];
        return "SELECT TOP ".($a['st']+25)." r.*, i.file_name
                FROM ibf_gallery_ratings r, ibf_gallery_images i
                WHERE r.member_id={$a['mid']} AND i.id=r.img_id ORDER BY r.date DESC";
    }

} // end class


?>
