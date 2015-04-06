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



class gallery_sql_queries extends db_driver
{

     var $db  = "";
     var $tbl = "";

    /*========================================================================*/
    // Set up...
    /*========================================================================*/

    function gallery_sql_queries( &$obj )
    {
    	$this->db = &$obj;

    	if ( ! $this->db->obj['sql_tbl_prefix'] )
    	{
    		$this->db->obj['sql_tbl_prefix'] = 'ibf_';
    	}

    	$this->tbl = $this->db->obj['sql_tbl_prefix'];
    }

    /*========================================================================*/

    /**************************************************
     *
     * Gallery Queries
     *
     **/

    function slideshow_image( $a )
    {
        $this->db->cur_startrow = $a['show'];
        return "SELECT TOP ".($a['show']+1)." i.file_type, i.masked_file_name, i.caption, i.id, i.directory, m.name, m.id AS mid
                FROM ibf_gallery_images i, ibf_members m
                WHERE {$a['where']} AND m.id=i.member_id {$a['prune']}
                ORDER BY {$a['sort_key']} {$a['order_key']}";
    }

    function get_top_level_categories()
    {
        return "SELECT c.*, i.caption, i.date
                         FROM ibf_gallery_categories c
                         LEFT JOIN ibf_gallery_images i ON (i.id=c.last_pic)
                         WHERE parent=0
                         ORDER BY c_order ASC";
    }

    function get_all_categories()
    {
        return "SELECT c.*, i.caption, i.date
                     FROM ibf_gallery_categories c
                     LEFT JOIN ibf_gallery_images i ON (i.id=c.last_pic)
                     ORDER BY c_order ASC";
    }

    function get_album_list( $a )
    {
        $this->db->cur_startrow = $a['st'];
        return "SELECT TOP ".($a['st']+$a['gallery_user_row'])." a.*, i.masked_file_name, i.directory, i.media, i.date, i.thumbnail, m.name as mname
                FROM ibf_gallery_albums a, ibf_gallery_images i, ibf_members m
                WHERE  m.id=a.member_id AND i.id=a.last_pic {$a['cat']} {$a['mid']} {$a['album_mod']}
                ORDER BY {$a['pre']}.{$a['SORT_KEY']} {$a['ORDER_KEY']}
               ";
    }

    function get_all_albums( $a=array() )
    {
        return "SELECT a.*, i.masked_file_name, i.directory, i.media, i.date, i.thumbnail
                FROM ibf_gallery_albums a
                LEFT JOIN ibf_gallery_images i ON ( i.id=a.last_pic )
               ";
    }

    function get_cat_stats( $a )
    {
        return "SELECT count( i.id ) AS IMG_TOTAL,
                SUM( i.comments ) as COM_TOTAL, MAX( i.id ) as LAST_PIC, MAX( date ) as LAST_TIME
                FROM ibf_gallery_images i
                LEFT JOIN ibf_gallery_albums a ON a.id=i.album_id
                WHERE i.approved=1 {$a['where']} AND ( a.id=0 OR a.public_album=1 OR i.category_id>0 )";
    }

    function get_last_pic_info( $a )
    {
        return "SELECT i.id, i.caption, m.id AS mid, m.name AS mname
                FROM ibf_gallery_images i, ibf_members m
                WHERE i.member_id=m.id AND i.id={$a['LAST_PIC']}";
    }

    function get_member_info( $a )
    {
        return "SELECT m.*, me.notes,me.ta_size,me.photo_type,me.photo_location,me.photo_dimensions
        		FROM ibf_members m
        		LEFT JOIN ibf_member_extra me ON(me.id=m.id)
        		WHERE m.id={$a['mid']}";
    }

    function get_favorites( $a )
    {
        return "SELECT i.masked_file_name, i.file_name, i.directory
                FROM ibf_gallery_favorites f, ibf_gallery_images i
                WHERE i.id=f.img_id AND f.member_id={$a['mid']}";
    }

    function get_ipb_images( $a )
    {
        $this->db->cur_startrow = $a['st'];
        return "SELECT TOP ".($a['st']+9)." a.*, f.permission_array, f.name, p.author_id, p.author_name, p.post_date, p.topic_id
                FROM ibf_attachments a, ibf_posts p, ibf_topics t, ibf_forums f
                WHERE attach_ext IN ( 'gif', 'jpeg', 'pjpeg', 'png', 'jpg' ) AND a.attach_member_id={$a['mid']}
                  AND a.attach_pid=p.pid AND p.topic_id=t.tid AND t.forum_id=f.id {$a['forum_filter']}";
    }

    function count_ipb_images( $a )
    {
        return "SELECT count(attach_id) as total
                FROM ibf_attachments a, ibf_posts p, ibf_topics t, ibf_forums f
                WHERE attach_ext IN ( 'gif', 'jpeg', 'pjpeg', 'png', 'jpg' ) AND a.attach_member_id={$a['mid']} AND a.attach_pid=p.pid AND p.topic_id=t.tid AND t.forum_id=f.id {$a['forum_filter']}";
    }

    function get_image( $a )
    {
        return "SELECT i.*, m.name AS mname, m.id AS mid FROM ibf_gallery_images i, ibf_members m WHERE i.id={$a['img']} AND m.id=i.member_id";
    }

    function get_prev_album_image( $a )
    {
        return "SELECT i.*, m.name AS mname, m.id AS mid FROM ibf_gallery_images i, ibf_members m WHERE i.id < {$a['id']} AND album_id={$a['album_id']} AND m.id=i.member_id ORDER BY i.id DESC";
    }

    function get_next_album_image( $a )
    {
        return "SELECT i.*, m.name AS mname, m.id AS mid FROM ibf_gallery_images i, ibf_members m WHERE i.id > {$a['id']} AND album_id={$a['album_id']} AND m.id=i.member_id ORDER BY i.id ASC";
    }

    function get_prev_cat_image( $a )
    {
        return "SELECT i.*, m.name AS mname, m.id AS mid FROM ibf_gallery_images i, ibf_members m WHERE i.id < {$a['id']} AND category_id={$a['category_id']} AND m.id=i.member_id ORDER BY i.id DESC";
    }

    function get_next_cat_image( $a )
    {
        return "SELECT i.*, m.name AS mname, m.id AS mid FROM ibf_gallery_images i, ibf_members m WHERE i.id > {$a['id']} AND category_id={$a['category_id']} AND m.id=i.member_id ORDER BY i.id ASC";
    }

	function get_comment_thumbs()
	{
		return "SELECT TOP 10 c.*, m.name, i.directory, i.masked_file_name, i.thumbnail, i.media, i.id
		        FROM ibf_gallery_comments c
				LEFT JOIN ibf_members m ON (c.author_id=m.id)
				LEFT JOIN ibf_gallery_images i ON ( c.img_id=i.id )
				ORDER BY pid DESC";
	}

    function get_comments( $a )
    {
    	return "SELECT c.*,
				m.id,m.name,m.mgroup,m.email,m.joined,m.posts, m.last_visit, m.last_activity,m.login_anonymous,m.title,m.hide_email, m.warn_level, m.warn_lastwarn,
				me.msnname,me.aim_name,me.icq_number,me.signature, me.website,me.yahoo,me.location, me.avatar_location, me.avatar_type, me.avatar_size,
				pc.*
				FROM ".SQL_PREFIX."gallery_comments c
				  LEFT JOIN ".SQL_PREFIX."members m ON (c.author_id=m.id)
				  LEFT JOIN ".SQL_PREFIX."member_extra me ON (me.id=m.id)
				  LEFT JOIN ".SQL_PREFIX."pfields_content pc ON (pc.member_id=c.author_id)
				WHERE c.pid IN(".implode(',', $a['pids']).") ORDER BY {$a['scol']} {$a['sord']}
		";
    }

    function get_images( $a )
    {
		$toprows = "";
		if ($a['limit']) {
			preg_match("/LIMIT[\t\n ]+([0-9]+)[\t\n ]*[,]?[\t\n ]*([0-9]*)/i", $a['limit'], $limit_list);
			if (isset($limit_list[2]) && $limit_list[2]>0) {
				$this->db->cur_startrow = $limit_list[1];
				$toprows = "TOP ".($limit_list[1]+$limit_list[2])." ";
			}
		}

		switch ($a['sort_key']) {
			case "RAND()" :
				$a['sort_key'] = "RAND((( DATEPART(mm, GETDATE()))*(DATEPART(ss, GETDATE()))* DATEPART(ms, GETDATE()))/i.id*(DATEPART(ss, GETDATE())))";
				break;
			case "name" :
				$a['sort_key'] = "m.name";
				break;
			case "id" :
				$a['sort_key'] = "i.id";
				break;
			case "date" :
				$a['sort_key'] = "i.date";
				break;
		}

		if ( $a['sort_xtra']==', id DESC ' ) $a['sort_xtra']=', i.id DESC';

		if ( $a['fav_tbl'] ) $a['fav_tbl'] = "INNER JOIN ".str_replace(",", "", $a['fav_tbl'])." ON (".$a['where'].")";

        return "SELECT ".$toprows." i.*, m.name, m.id AS mid, r.id as rated
                FROM ibf_gallery_images i {$a['fav_tbl']}
                LEFT JOIN ibf_members m ON (m.id=i.member_id)
				LEFT JOIN ( SELECT img_id, min(id) as id FROM ibf_gallery_ratings GROUP BY img_id ) r ON ( r.img_id=i.id )
                WHERE {$a['where']} {$a['approve']} {$a['prune']} {$a['restrict']}
                ORDER BY {$a['pin']} {$a['sort_key']} {$a['order_key']} {$a['sort_xtra']}";
    }

	function comments_get_quoted( $a )
	{
		return "select c.*,i.category_id FROM ".SQL_PREFIX."gallery_comments c LEFT JOIN ".SQL_PREFIX."gallery_images i ON (i.id=c.img_id)
				WHERE pid IN (".implode(",", $a['quoted_pids']).")";
	}


} // end class


?>
