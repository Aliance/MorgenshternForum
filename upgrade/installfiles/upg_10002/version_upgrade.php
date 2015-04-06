<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > IPB UPGRADE 1.1 -> 2.0 SQL STUFF!
|   > Script written by Matt Mecham
|   > Date started: 21st April 2004
|   > Interesting fact: Turin Brakes are also good
+--------------------------------------------------------------------------
*/

class version_upgrade
{
	var $install;

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function version_upgrade( & $install )
	{
		$this->install = & $install;
	}

	/*-------------------------------------------------------------------------*/
	// Auto run..
	/*-------------------------------------------------------------------------*/

	function auto_run()
	{
		//--------------------------------
		// What are we doing?
		//--------------------------------

		switch( $this->install->saved_data['workact'] )
		{
			case 'step_1':
				$this->step_1();
				break;
			case 'step_2':
				$this->step_2();
				break;
			case 'step_3':
				$this->step_3();
				break;
			case 'step_4':
				$this->step_4();
				break;
			case 'step_5':
				$this->step_5();
				break;
			case 'step_6':
				$this->step_6();
				break;
			case 'step_7':
				$this->step_7();
				break;
			case 'step_8':
				$this->step_8();
				break;
			case 'step_9':
				$this->step_9();
				break;
			case 'step_10':
				$this->step_10();
				break;
			case 'step_11':
				$this->step_11();
				break;
			case 'step_12':
				$this->step_12();
				break;
			case 'step_13':
				$this->step_13();
				break;
			case 'step_14':
				$this->step_14();
				break;
			case 'step_15':
				$this->step_15();
				break;
			case 'step_16':
				$this->step_16();
				break;
			case 'step_17':
				$this->step_17();
				break;
			case 'step_18':
				$this->step_18();
				break;
			case 'step_19':
				$this->step_19();
				break;
			case 'step_20':
				$this->step_20();
				break;
			case 'step_21':
				$this->step_21();
				break;
			case 'step_22':
				$this->step_22();
				break;
			case 'step_23':
				$this->step_23();
				break;
			case 'step_24':
				$this->step_24();
				break;

			default:
				$this->step_1();
				break;
		}
		if ( $this->install->saved_data['workact'] )
		{
			$this->install->saved_data['vid'] = $this->install->current_version;

			return false;
		}
		else
		{
			return true;
		}
	}

	/*-------------------------------------------------------------------------*/
	// STEP 1: COPY AND POPULATE BACK UP FORUMS TABLE
	/*-------------------------------------------------------------------------*/

	function step_1()
	{
		$this->install->saved_data['st'] = 0;

		$SQL[] = "DROP TABLE if exists ".SQL_PREFIX."forums_perms;";

		$SQL[]="CREATE TABLE ".SQL_PREFIX."forum_perms(
			perm_id int(10) auto_increment NOT NULL,
			perm_name varchar(250) NOT NULL,
			PRIMARY KEY(perm_id)
		);";

		$SQL[] = "DROP TABLE if exists ".SQL_PREFIX."forums_bak;";

		$table = $this->install->ipsclass->DB->sql_get_table_schematic( 'forums' );

		$SQL[] = str_replace( SQL_PREFIX."forums", SQL_PREFIX."forums_bak", $table['Create Table'] );

		$SQL[] = "INSERT INTO ".SQL_PREFIX."forums_bak SELECT * FROM ".SQL_PREFIX."forums";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= '';

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$oq = $this->install->ipsclass->DB->query("SELECT g_id, g_title FROM ".SQL_PREFIX."groups ORDER BY g_id");

		while( $r = $this->install->ipsclass->DB->fetch_row($oq) )
		{
			$nq = $this->install->ipsclass->DB->query("REPLACE INTO ".SQL_PREFIX."forum_perms SET perm_id={$r['g_id']}, perm_name='����� {$r['g_title']}'");
			$bq = $this->install->ipsclass->DB->query("UPDATE ".SQL_PREFIX."groups SET g_perm_id={$r['g_id']} WHERE g_id={$r['g_id']}");
		}

		$this->install->ipsclass->DB->query("update ".SQL_PREFIX."messages SET read_state=1 where vid='sent'");

		$this->install->ipsclass->DB->query("DROP TABLE if exists ".SQL_PREFIX."attachments");

		$this->install->message = "������� ������� ���������, �������� �������� ����� ������...<br /><br />��������� $this->sqlcount ��������...";

		$this->install->saved_data['workact'] = 'step_2';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 2: DROP FORUMS TABLE, CREATE NEW TABLES
	/*-------------------------------------------------------------------------*/

	function step_2()
	{
		$SQL[] = "CREATE TABLE ".SQL_PREFIX."attachments (
		   attach_id int(10) NOT NULL auto_increment,
		   attach_ext varchar(10) NOT NULL default '',
		   attach_file varchar(250) NOT NULL default '',
		   attach_location varchar(250) NOT NULL default '',
		   attach_thumb_location varchar(250) NOT NULL default '',
		   attach_thumb_width smallint(5) NOT NULL default '0',
		   attach_thumb_height smallint(5) NOT NULL default '0',
		   attach_is_image tinyint(1) NOT NULL default '0',
		   attach_hits int(10) NOT NULL default '0',
		   attach_date int(10) NOT NULL default '0',
		   attach_temp tinyint(1) NOT NULL default '0',
		   attach_pid int(10) NOT NULL default '0',
		   attach_post_key varchar(32) NOT NULL default '0',
		   attach_msg int(10) NOT NULL default '0',
		   attach_member_id mediumint(8) NOT NULL default '0',
		   attach_approved int(10) NOT NULL default '1',
		   attach_filesize int(10) NOT NULL default '0',
		PRIMARY KEY (attach_id),
		KEY attach_pid (attach_pid),
		KEY attach_msg (attach_msg),
		KEY attach_post_key (attach_post_key),
		KEY attach_mid_size (attach_member_id, attach_filesize)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."message_text (
		 msg_id int(10) NOT NULL auto_increment,
		 msg_date int(10) default NULL,
		 msg_post text NULL,
		 msg_cc_users text NULL,
		 msg_sent_to_count smallint(5) NOT NULL default '0',
		 msg_deleted_count smallint(5) NOT NULL default '0',
		 msg_post_key varchar(32) NOT NULL default '0',
		 msg_author_id mediumint(8) NOT NULL default '0',
		PRIMARY KEY (msg_id),
		KEY msg_date (msg_date),
		KEY msg_sent_to_count (msg_sent_to_count),
		KEY msg_deleted_count (msg_deleted_count)
		);";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."message_topics (
		   mt_id int(10) NOT NULL auto_increment,
		   mt_msg_id int(10) NOT NULL default '0',
		   mt_date int(10) NOT NULL default '0',
		   mt_title varchar(255) NOT NULL default '',
		   mt_from_id mediumint(8) NOT NULL default '0',
		   mt_to_id mediumint(8) NOT NULL default '0',
		   mt_owner_id mediumint(8) NOT NULL default '0',
		   mt_vid_folder varchar(32) NOT NULL default '',
		   mt_read tinyint(1) NOT NULL default '0',
		   mt_hasattach smallint(5) NOT NULL default '0',
		   mt_hide_cc tinyint(1) default '0',
		   mt_tracking tinyint(1) default '0',
		   mt_user_read int(10) default '0',
		PRIMARY KEY (mt_id),
		KEY mt_from_id (mt_from_id),
		KEY mt_owner_id (mt_owner_id, mt_to_id, mt_vid_folder)
		);";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."skin_sets (
		   set_skin_set_id int(10) NOT NULL auto_increment,
		   set_name varchar(150) NOT NULL default '',
		   set_image_dir varchar(200) NOT NULL default '',
		   set_hidden tinyint(1) NOT NULL default '0',
		   set_default tinyint(1) NOT NULL default '0',
		   set_css_method varchar(100) NOT NULL default 'inline',
		   set_skin_set_parent smallint(5) NOT NULL default '-1',
		   set_author_email varchar(255) NOT NULL default '',
		   set_author_name varchar(255) NOT NULL default '',
		   set_author_url varchar(255) NOT NULL default '',
		   set_css mediumtext NULL,
		   set_wrapper mediumtext NULL,
		   set_css_updated int(10) NOT NULL default '0',
		   set_cache_css mediumtext NULL,
		   set_cache_macro mediumtext NULL,
		   set_cache_wrapper mediumtext NULL,
		   set_emoticon_folder varchar(60) NOT NULL default 'default',
		   PRIMARY KEY(set_skin_set_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."skin_templates_cache (
		   template_id varchar(32) NOT NULL default '',
		   template_group_name varchar(255) NOT NULL default '',
		   template_group_content mediumtext NULL,
		   template_set_id int(10) NOT NULL default '0',
		   primary key (template_id),
		   KEY template_set_id (template_set_id),
		   KEY template_group_name (template_group_name)
	   );";


	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."mail_queue(
		   mail_id int(10) auto_increment NOT NULL,
		   mail_date int(10) NOT NULL default '0',
		   mail_to varchar(255) NOT NULL default '',
		   mail_from varchar(255) NOT NULL default '',
		   mail_subject text NULL,
		   mail_content text NULL,
		   mail_type varchar(200) NOT NULL default '',
		   PRIMARY KEY (mail_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."task_manager (
		   task_id int(10) auto_increment NOT NULL,
		   task_title varchar(255) NOT NULL default '',
		   task_file varchar(255) NOT NULL default '',
		   task_next_run int(10) NOT NULL default 0,
		   task_week_day tinyint(1) NOT NULL default '-1',
		   task_month_day smallint(2) NOT NULL default '-1',
		   task_hour smallint(2) NOT NULL default '-1',
		   task_minute smallint(2) NOT NULL default '-1',
		   task_cronkey varchar(32) NOT NULL default '',
		   task_log tinyint(1) NOT NULL default '0',
		   task_description text NULL,
		   task_enabled tinyint(1) NOT NULL default '1',
		   task_key varchar(30) NOT NULL default '',
		   task_safemode tinyint(1) NOT NULL default 0,
		   PRIMARY KEY(task_id),
		   KEY task_next_run (task_next_run)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."task_logs (
		   log_id int(10) auto_increment NOT NULL,
		   log_title varchar(255) NOT NULL default '',
		   log_date int(10) NOT NULL default '0',
		   log_ip varchar(16) NOT NULL default '0',
		   log_desc text NULL,
		   PRIMARY KEY(log_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."custom_bbcode (
		   bbcode_id int(10) NOT NULL auto_increment,
		   bbcode_title varchar(255) NOT NULL default '',
		   bbcode_desc text NULL,
		   bbcode_tag varchar(255) NOT NULL default '',
		   bbcode_replace text NULL,
		   bbcode_useoption tinyint(1) NOT NULL default 0,
		   bbcode_example text NULL,
		   PRIMARY KEY (bbcode_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."conf_settings (
		   conf_id int(10) NOT NULL auto_increment,
		   conf_title varchar(255) NOT NULL default '',
		   conf_description text NULL,
		   conf_group smallint(3) NOT NULL default 0,
		   conf_type varchar(255) NOT NULL default '',
		   conf_key varchar(255) NOT NULL default '',
		   conf_value text NULL,
		   conf_default text NULL,
		   conf_extra text NULL,
		   conf_evalphp text NULL,
		   conf_protected tinyint(1) NOT NULL default '0',
		   conf_position smallint(3) NOT NULL default '0',
		   conf_start_group varchar(255) NOT NULL default '',
		   conf_end_group tinyint(1) NOT NULL default '0',
		   conf_help_key varchar(255) NOT NULL default '0',
		   conf_add_cache tinyint(1) NOT NULL default '1',
		   PRIMARY KEY (conf_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."conf_settings_titles (
		   conf_title_id smallint(3) NOT NULL auto_increment,
		   conf_title_title varchar(255) NOT NULL default '',
		   conf_title_desc text NULL,
		   conf_title_count smallint(3) NOT NULL default '0',
		   conf_title_noshow tinyint(1) NOT NULL default '0',
		   conf_title_keyword varchar(200) NOT NULL default '',
		   PRIMARY KEY(conf_title_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."topics_read (
		 read_tid int(10) NOT NULL default '0',
		 read_mid mediumint(8) NOT NULL default '0',
		 read_date int(10) NOT NULL default '0',
		 UNIQUE KEY read_tid_mid( read_tid, read_mid )
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."banfilters (
		   ban_id int(10) NOT NULL auto_increment,
		   ban_type varchar(10) NOT NULL default 'ip',
		   ban_content text NULL,
		   ban_date int(10) NOT NULL default '0',
		   PRIMARY KEY (ban_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."attachments_type (
		   atype_id int(10) NOT NULL auto_increment,
		   atype_extension varchar(18) NOT NULL default '',
		   atype_mimetype varchar(255) NOT NULL default '',
		   atype_post tinyint(1) NOT NULL default '1',
		   atype_photo tinyint(1) NOT NULL default '0',
		   atype_img text NULL,
		   PRIMARY KEY (atype_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."members_converge (
		   converge_id int(10) auto_increment NOT NULL,
		   converge_email varchar(250) NOT NULL default '',
		   converge_joined int(10) NOT NULL default 0,
		   converge_pass_hash varchar(32) NOT NULL default '',
		   converge_pass_salt varchar(5) NOT NULL default '',
		   PRIMARY KEY( converge_id )
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."announcements (
		announce_id int(10) UNSIGNED NOT NULL auto_increment,
		announce_title varchar(255) NOT NULL default '',
		announce_post text NULL,
		announce_forum text NULL,
		announce_member_id mediumint(8) UNSIGNED NOT NULL default '0',
		announce_html_enabled tinyint(1) NOT NULL default '0',
		announce_views int(10) UNSIGNED NOT NULL default '0',
		announce_start int(10) UNSIGNED NOT NULL default '0',
		announce_end int(10) UNSIGNED NOT NULL default '0',
		announce_active tinyint(1) NOT NULL default '1',
	   PRIMARY KEY (announce_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."mail_error_logs (
		   mlog_id int(10) auto_increment NOT NULL,
		   mlog_date int(10) NOT NULL default '0',
		   mlog_to varchar(250) NOT NULL default '',
		   mlog_from varchar(250) NOT NULL default '',
		   mlog_subject varchar(250) NOT NULL default '',
		   mlog_content varchar(250) NOT NULL default '',
		   mlog_msg text NULL,
		   mlog_code varchar(200) NOT NULL default '',
		   mlog_smtp_msg text NULL,
		   PRIMARY KEY (mlog_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."bulk_mail (
		   mail_id int(10) NOT NULL auto_increment,
		   mail_subject varchar(255) NOT NULL default '',
		   mail_content mediumtext NULL,
		   mail_groups mediumtext NULL,
		   mail_honor tinyint(1) NOT NULL default '1',
		   mail_opts mediumtext NULL,
		   mail_start int(10) NOT NULL default '0',
		   mail_updated int(10) NOT NULL default '0',
		   mail_sentto int(10) NOT NULL default '0',
		   mail_active tinyint(1) NOT NULL default '0',
		   mail_pergo smallint(5) NOT NULL default '0',
		   PRIMARY KEY (mail_id)
	   );";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."upgrade_history (
		   upgrade_id int(10) NOT NULL auto_increment,
		   upgrade_version_id int(10) NOT NULL default 0,
		   upgrade_version_human varchar(200) NOT NULL default '',
		   upgrade_date int(10) NOT NULL default '0',
		   upgrade_mid int(10) NOT NULL default '0',
		   upgrade_notes text NULL,
		   PRIMARY KEY (upgrade_id)
	   );";

	   $SQL[] = "DROP TABLE ".SQL_PREFIX."forums;";

	   $SQL[] = "CREATE TABLE ".SQL_PREFIX."forums (
				 id smallint(5) NOT NULL default '0',
				 topics mediumint(6) default '0',
				 posts mediumint(6) default '0',
				 last_post int(10) default NULL,
				 last_poster_id mediumint(8) NOT NULL default '0',
				 last_poster_name varchar(32) default NULL,
				 name varchar(128) NOT NULL default '',
				 description text NULL,
				 position tinyint(2) default NULL,
				 use_ibc tinyint(1) default NULL,
				 use_html tinyint(1) default NULL,
				 status varchar(10) default NULL,
				 password varchar(32) default NULL,
				 last_title varchar(128) default NULL,
				 last_id int(10) default NULL,
				 sort_key varchar(32) default NULL,
				 sort_order varchar(32) default NULL,
				 prune tinyint(3) default NULL,
				 show_rules tinyint(1) default NULL,
				 preview_posts tinyint(1) default NULL,
				 allow_poll tinyint(1) NOT NULL default '1',
				 allow_pollbump tinyint(1) NOT NULL default '0',
				 inc_postcount tinyint(1) NOT NULL default '1',
				 skin_id int(10) default NULL,
				 parent_id mediumint(5) default '-1',
				 sub_can_post tinyint(1) default '1',
				 quick_reply tinyint(1) default '0',
				 redirect_url varchar(250) default '',
				 redirect_on tinyint(1) NOT NULL default '0',
				 redirect_hits int(10) NOT NULL default '0',
				 redirect_loc varchar(250) default '',
				 rules_title varchar(255) NOT NULL default '',
				 rules_text text NULL,
				 topic_mm_id varchar(250) NOT NULL default '',
				 notify_modq_emails text NULL,
				 permission_custom_error text NULL,
				 permission_array mediumtext NULL,
				 permission_showtopic tinyint(1) NOT NULL default '0',
				 queued_topics mediumint(6) NOT NULL default '0',
				 queued_posts  mediumint(6) NOT NULL default '0',
				 PRIMARY KEY  (id),
				 KEY position (position, parent_id)
			   );";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscriptions (
		 sub_id smallint(5) NOT NULL auto_increment,
		 sub_title varchar(250) NOT NULL default '',
		 sub_desc text NULL,
		 sub_new_group mediumint(8) NOT NULL default 0,
		 sub_length smallint(5) NOT NULL default '1',
		 sub_unit varchar(2) NOT NULL default 'm',
		 sub_cost decimal(10,2) NOT NULL default '0.00',
		 sub_run_module varchar(250) NOT NULL default '',
		 PRIMARY KEY (sub_id)
		);";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_extra (
		 subextra_id smallint(5) NOT NULL auto_increment,
		 subextra_sub_id smallint(5) NOT NULL default '0',
		 subextra_method_id smallint(5) NOT NULL default '0',
		 subextra_product_id varchar(250) NOT NULL default '0',
		 subextra_can_upgrade tinyint(1) NOT NULL default '0',
		 subextra_recurring tinyint(1) NOT NULL default '0',
		 subextra_custom_1 text NULL,
		 subextra_custom_2 text NULL,
		 subextra_custom_3 text NULL,
		 subextra_custom_4 text NULL,
		 subextra_custom_5 text NULL,
		 PRIMARY KEY(subextra_id)
		);";


		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_trans (
		 subtrans_id int(10) NOT NULL auto_increment,
		 subtrans_sub_id smallint(5) NOT NULL default '0',
		 subtrans_member_id mediumint(8) NOT NULL default '0',
		 subtrans_old_group smallint(5) NOT NULL default '0',
		 subtrans_paid decimal(10,2) NOT NULL default '0.00',
		 subtrans_cumulative decimal(10,2) NOT NULL default '0.00',
		 subtrans_method varchar(20) NOT NULL default '',
		 subtrans_start_date int(11) NOT NULL default '0',
		 subtrans_end_date int(11) NOT NULL default '0',
		 subtrans_state varchar(200) NOT NULL default '',
		 subtrans_trxid varchar(200) NOT NULL default '',
		 subtrans_subscrid varchar(200) NOT NULL default '',
		 subtrans_currency varchar(10) NOT NULL default 'USD',
		 PRIMARY KEY (subtrans_id)
		);";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_logs (
		 sublog_id int(10) NOT NULL auto_increment,
		 sublog_date int(10) NOT NULL default '0',
		 sublog_member_id mediumint(8) NOT NULL default '0',
		 sublog_transid int(10) NOT NULL default '0',
		 sublog_ipaddress varchar(16) NOT NULL default '',
		 sublog_data text NULL,
		 sublog_postdata text NULL,
		 PRIMARY KEY (sublog_id)
		);";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_methods (
		 submethod_id smallint(5) NOT NULL auto_increment,
		 submethod_title varchar(250) NOT NULL default '',
		 submethod_name varchar(20) NOT NULL default '',
		 submethod_email varchar(250) NOT NULL default '',
		 submethod_sid text NULL,
		 submethod_custom_1 text NULL,
		 submethod_custom_2 text NULL,
		 submethod_custom_3 text NULL,
		 submethod_custom_4 text NULL,
		 submethod_custom_5 text NULL,
		 submethod_is_cc tinyint(1) NOT NULL default '0',
		 submethod_is_auto tinyint(1) NOT NULL default '0',
		 submethod_desc text NULL,
		 submethod_logo text NULL,
		 submethod_active tinyint(1) NOT NULL default '0',
		 submethod_use_currency varchar(10) NOT NULL default 'USD',
		 PRIMARY KEY (submethod_id)
		);";

		$SQL[] = "CREATE TABLE ".SQL_PREFIX."subscription_currency (
		 subcurrency_code varchar(10) NOT NULL,
		 subcurrency_desc varchar(250) NOT NULL default '',
		 subcurrency_exchange decimal(16, 8) NOT NULL,
		 subcurrency_default tinyint(1) NOT NULL default '0',
		 PRIMARY KEY(subcurrency_code)
		);";

		$SQL[]="CREATE TABLE ".SQL_PREFIX."validating (
		vid varchar(32) NOT NULL,
		member_id mediumint(8) NOT NULL,
		real_group smallint(3) NOT NULL default '0',
		temp_group smallint(3) NOT NULL default '0',
		entry_date int(10) NOT NULL default '0',
		coppa_user tinyint(1) NOT NULL default '0',
		lost_pass tinyint(1) NOT NULL default '0',
		new_reg tinyint(1) NOT NULL default '0',
		email_chg tinyint(1) NOT NULL default '0',
		ip_address varchar(16) NOT NULL default '0',
		PRIMARY KEY(vid),
		KEY new_reg(new_reg)
		);";

		$SQL[]="create table ".SQL_PREFIX."cache_store (
		  cs_key varchar(255) NOT NULL default '',
		  cs_value text NULL,
		  cs_extra varchar(255) NOT NULL default '',
		  PRIMARY KEY(cs_key)
		);";

		$SQL[]="create table ".SQL_PREFIX."email_logs (
		email_id int(10) NOT NULL auto_increment,
		email_subject varchar(255) NOT NULL,
		email_content text NULL,
		email_date int(10) NOT NULL default '0',
		from_member_id mediumint(8) NOT NULL default '0',
		from_email_address varchar(250) NOT NULL,
		from_ip_address varchar(16) NOT NULL default '127.0.0.1',
		to_member_id mediumint(8) NOT NULL default '0',
		to_email_address varchar(250) NOT NULL,
		topic_id int(10) NOT NULL default '0',
		PRIMARY KEY(email_id),
		KEY from_member_id(from_member_id),
		KEY email_date(email_date)
		);";

		$SQL[]="CREATE TABLE ".SQL_PREFIX."topic_mmod (
		mm_id smallint(5) NOT NULL auto_increment,
		mm_title varchar(250) NOT NULL,
		mm_enabled tinyint(1) NOT NULL default '0',
		topic_state varchar(10) NOT NULL default 'leave',
		topic_pin varchar(10) NOT NULL default 'leave',
		topic_move smallint(5) NOT NULL default '0',
		topic_move_link tinyint(1) NOT NULL default '0',
		topic_title_st varchar(250) NOT NULL default '',
		topic_title_end varchar(250) NOT NULL default '',
		topic_reply tinyint(1) NOT NULL default '0',
		topic_reply_content text NULL,
		topic_reply_postcount tinyint(1) NOT NULL default '0',
		PRIMARY KEY(mm_id)
		);";

		$SQL[]="create table ".SQL_PREFIX."spider_logs (
		sid int(10) NOT NULL auto_increment,
		bot varchar(255) NOT NULL default '',
		query_string text NULL,
		entry_date int(10) NOT NULL default '0',
		ip_address varchar(16) NOT NULL default '',
		PRIMARY KEY(sid)
		);";



		$SQL[]="CREATE TABLE ".SQL_PREFIX."warn_logs (
		wlog_id int(10) auto_increment NOT NULL,
		wlog_mid mediumint(8) NOT NULL default '0',
		wlog_notes text NULL,
		wlog_contact varchar(250) NOT NULL default 'none',
		wlog_contact_content text NULL,
		wlog_date int(10) NOT NULL default '0',
		wlog_type varchar(6) NOT NULL default 'pos',
		wlog_addedby mediumint(8) NOT NULL default '0',
		PRIMARY KEY(wlog_id)
		);";


		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if ( preg_match("/CREATE TABLE (\S+) \(/", $query) )
			{
				//-----------------------------------
				// Pass to handler
				//-----------------------------------
				$extra_install = $this->install->extra_install;

				if ( $extra_install AND method_exists( $extra_install, 'process_query_create' ) )
				{
					$query = $extra_install->process_query_create( $query );
				}
			}

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$oq = $this->install->ipsclass->DB->query("SELECT * FROM ".SQL_PREFIX."rules");

		if( !$this->install->ipsclass->DB->failed )
		{
			while ( $r = $this->install->ipsclass->DB->fetch_row($oq) )
			{
				$nq = $this->install->ipsclass->DB->query("UPDATE ".SQL_PREFIX."forums SET rules_title='{$r['title']}', rules_text='{$r['body']}' WHERE id={$r['fid']}");
			}
		}

		$this->install->ipsclass->DB->query("drop table if exists ".SQL_PREFIX."rules");

		$this->install->message = "����� ������� �������. ����������� ����� ������ ���� 1 (������� ���������)<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_3';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 3: ALTER POST TABLE
	/*-------------------------------------------------------------------------*/

	function step_3()
	{
		$SQL[] = "alter table ".SQL_PREFIX."posts add post_parent int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts ADD post_key varchar(32) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts add post_htmlstate smallint(1) NOT NULL default '0';";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "���� � ������� ��������� ��������, �������� ������� ���...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_4';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 4: ALTER TOPIC TABLE
	/*-------------------------------------------------------------------------*/

	function step_4()
	{
		$SQL[] = "alter table ".SQL_PREFIX."topics ADD topic_hasattach smallint(5) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topics change posts posts int(10) default null, change views views int(10) default '0';";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "���� � ������� ��� ��������, �������� ������� �������������...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_5';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 5: ALTER MEMBERS TABLE
	/*-------------------------------------------------------------------------*/

	function step_5()
	{
		$SQL[] = "alter table ".SQL_PREFIX."members add login_anonymous varchar(3) NOT NULL default '0&0';";
		$SQL[] = "alter table ".SQL_PREFIX."members add ignored_users text NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."members add mgroup_others varchar(255) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."member_extra
		ADD aim_name varchar(40) NOT NULL default '',
		ADD icq_number int(15) NOT NULL default '0',
		ADD website varchar(250) NOT NULL default '',
		ADD yahoo varchar(40) NOT NULL default '',
		ADD interests text NULL,
		ADD msnname varchar(200) NOT NULL default '',
		ADD vdirs text NULL,
		ADD location varchar(250) NOT NULL default '',
		ADD signature text NULL,
		ADD avatar_location varchar(128) NOT NULL default '',
		ADD avatar_size varchar(9) NOT NULL default '',
		ADD avatar_type varchar(15) NOT NULL default 'local';";
		$SQL[] = "alter table ".SQL_PREFIX."members add member_login_key varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."members change password legacy_password varchar(32) NOT NULL default '';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members ADD org_supmod TINYINT(1) DEFAULT '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members ADD org_perm_id varchar(255) DEFAULT '';";
		$SQL[] = "alter table ".SQL_PREFIX."members drop validate_key;";
		$SQL[] = "alter table ".SQL_PREFIX."members drop new_pass;";
		$SQL[] = "alter table ".SQL_PREFIX."members drop prev_group;";
		$SQL[] = "alter table ".SQL_PREFIX."members add temp_ban varchar(100);";
		$SQL[] = "alter table ".SQL_PREFIX."members change allow_post restrict_post varchar(100) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."members change mod_posts mod_posts varchar(100) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."members add warn_lastwarn int(10) not null default '0' after warn_level;";
		$SQL[] = "alter table ".SQL_PREFIX."member_extra add photo_type varchar(10) default '';";
		$SQL[] = "alter table ".SQL_PREFIX."member_extra add photo_location varchar(255) default '';";
		$SQL[] = "alter table ".SQL_PREFIX."member_extra add photo_dimensions varchar(200) default '';";
		$SQL[] = "alter table ".SQL_PREFIX."members add integ_msg varchar(250) default '';";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "���� � ������� ������������� ��������, �������� ���������� �������...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_6';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 6: ALTER OTHER TABLES
	/*-------------------------------------------------------------------------*/

	function step_6()
	{
		$SQL[] = "alter table ".SQL_PREFIX."macro rename ibf_skin_macro;";
		$SQL[] = "alter table ".SQL_PREFIX."skin_macro change can_remove macro_can_remove tinyint(1) default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."groups add g_bypass_badwords tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."cache_store change cs_value cs_value mediumtext NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."cache_store add cs_array tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."sessions add in_error tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topic_mmod add mm_forums text NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."groups change g_icon g_icon text NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."emoticons add emo_set varchar(64) NOT NULL default 'default';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change ID session_id varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change IP_ADDRESS session_ip_address varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change MEMBER_NAME session_member_name varchar(250) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change MEMBER_ID session_member_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change SESSION_KEY session_member_login_key varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change LOCATION session_location varchar(64) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change LOG_IN_TIME session_log_in_time int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."admin_sessions change RUNNING_TIME session_running_time int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."forum_tracker add forum_track_type varchar(100) NOT NULL default 'delayed';";
		$SQL[] = "alter table ".SQL_PREFIX."tracker add topic_track_type varchar(100) NOT NULL default 'delayed';";

		$SQL[] = "delete from ".SQL_PREFIX."members where id=0 LIMIT 1;";
		$SQL[] = "delete from ".SQL_PREFIX."member_extra where id=0 limit 1;";

		$SQL[] = "ALTER TABLE ".SQL_PREFIX."groups ADD g_perm_id varchar(255) NOT NULL;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."groups ADD g_photo_max_vars VARCHAR(200) DEFAULT '100:250:250';";
		$SQL[] = "alter table ".SQL_PREFIX."groups add g_dohtml tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."groups add g_edit_topic tinyint(1) NOT NULL DEFAULT '0';";
		$SQL[] = "alter table ".SQL_PREFIX."groups add g_email_limit varchar(15) NOT NULL DEFAULT '10:15';";

		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add end_day int(2), add end_month int(2), add end_year int(4);";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add end_unix_stamp int(10);";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add event_ranged tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add event_repeat tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add repeat_unit char(2) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add event_bgcolor varchar(32) NOT NULL default '';";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events add event_color varchar(32) NOT NULL default '';";

		$SQL[] = "alter table ".SQL_PREFIX."skins add css_method varchar(100) default 'inline';";
		$SQL[] = "alter table ".SQL_PREFIX."css add updated int(10) default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."search_results add query_cache text NULL;";

		$SQL[] = "alter table ".SQL_PREFIX."moderators add can_mm tinyint(1) NOT NULL default '0';";

		$this->install->error   = array();
		$this->sqlcount 		= 0;

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			$this->install->ipsclass->DB->query( $query );

			if ( $this->install->ipsclass->DB->error )
			{
				$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}

		$this->install->message = "��������� ����� ������ ���������, ��������� � �������������� �������...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_7';
	}


	/*-------------------------------------------------------------------------*/
	// STEP 7: IMPORT FORUMS
	/*-------------------------------------------------------------------------*/

	function step_7()
	{
		$this->install->ipsclass->DB->return_die = 1;

		//-----------------------------------------
		// Convert existing forums
		//-----------------------------------------

		$o = $this->install->ipsclass->DB->query("SELECT * FROM ".SQL_PREFIX."forums_bak ORDER BY id");

		while( $r = $this->install->ipsclass->DB->fetch_row( $o ) )
		{
			$perm_array = addslashes(serialize(array(
													  'start_perms'  => $r['start_perms'],
													  'reply_perms'  => $r['reply_perms'],
													  'read_perms'   => $r['read_perms'],
													  'upload_perms' => $r['upload_perms'],
													  'show_perms'   => $r['read_perms']
									)		  )     );

			$this->install->ipsclass->DB->do_insert( 'forums', array (
											  'id'                      => $r['id'],
											  'position'                => $r['position'],
											  'topics'                  => $r['topics'],
											  'posts'                   => $r['posts'],
											  'last_post'               => $r['last_post'],
											  'last_poster_id'          => $r['last_poster_id'],
											  'last_poster_name'        => $r['last_poster_name'],
											  'name'                    => $r['name'],
											  'description'             => $r['description'],
											  'use_ibc'                 => $r['use_ibc'],
											  'use_html'                => $r['use_html'],
											  'status'                  => $r['status'],
											  'password'                => $r['password'],
											  'last_id'                 => $r['last_id'],
											  'last_title'              => $r['last_title'],
											  'sort_key'                => $r['sort_key'],
											  'sort_order'              => $r['sort_order'],
											  'prune'                   => $r['prune'],
											  'show_rules'              => $r['show_rules'],
											  'preview_posts'           => $r['preview_posts'],
											  'allow_poll'              => $r['allow_poll'],
											  'allow_pollbump'          => $r['allow_pollbump'],
											  'inc_postcount'           => $r['inc_postcount'],
											  'parent_id'               => $r['parent_id'],
											  'sub_can_post'            => $r['sub_can_post'],
											  'quick_reply'             => $r['quick_reply'],
											  'redirect_on'             => $r['redirect_on'],
											  'redirect_hits'           => $r['redirect_hits'],
											  'redirect_url'            => $r['redirect_url'],
											  'redirect_loc'		    => $r['redirect_loc'],
											  'rules_title'			    => $r['rules_title'],
  											  'rules_text'			    => $r['rules_text'],
											  'notify_modq_emails'      => $r['notify_modq_emails'],
											  'permission_array'        => $perm_array,
											  'permission_showtopic'    => '',
											  'permission_custom_error' => '',
									)       );
		}

		//-----------------------------------------
		// Convert categories
		//-----------------------------------------

		$this->install->ipsclass->DB->query("SELECT MAX(id) as max FROM ".SQL_PREFIX."forums");
		$max = $this->install->ipsclass->DB->fetch_row();

		$fid = $max['max'];

		$o = $this->install->ipsclass->DB->query("SELECT * FROM ".SQL_PREFIX."categories WHERE id > 0");

		while( $r = $this->install->ipsclass->DB->fetch_row( $o ) )
		{
			$fid++;

			$perm_array = addslashes(serialize(array(
													  'start_perms'  => '*',
													  'reply_perms'  => '*',
													  'read_perms'   => '*',
													  'upload_perms' => '*',
													  'show_perms'   => '*',
									)		  )     );

			$this->install->ipsclass->DB->do_insert( 'forums', array(
											 'id'               => $fid,
											 'position'         => $r['position'],
											 'name'             => $r['name'],
											 'sub_can_post'     => 0,
											 'permission_array' => $perm_array,
											 'parent_id'        => -1,
						  )                );

			//-----------------------------------------
			// Update old categories
			//-----------------------------------------

			$n = $this->install->ipsclass->DB->query("SELECT id FROM ".SQL_PREFIX."forums_bak WHERE category={$r['id']} AND parent_id = -1");

			$ids = array();

			while( $c = $this->install->ipsclass->DB->fetch_row($n) )
			{
				$ids[] = $c['id'];
			}

			if ( count($ids) )
			{
				$this->install->ipsclass->DB->do_update( 'forums', array( 'parent_id' => $fid ), 'id IN ('.implode(',',$ids).')' );
			}
		}

		$this->install->message = "����������� ������� ���������, ��������� � ����������� ������������� ������...<br /><br />������� ���������������: $fid...";
		$this->install->saved_data['workact'] = 'step_8';
	}


	/*-------------------------------------------------------------------------*/
	// STEP 8: CONVERT ATTACHMENTS
	/*-------------------------------------------------------------------------*/

	function step_8()
	{
		$this->install->ipsclass->DB->return_die = 1;

		$start = intval($this->install->saved_data['st']) ? intval($this->install->saved_data['st']) : 0;
		$lend  = 300;
		$end   = $start + $lend;

		//-----------------------------------------
		// In steps...
		//-----------------------------------------

		$this->install->ipsclass->DB->simple_construct( array( "select" => '*',
									  'from'   => 'posts',
									  'where'  => "attach_file != ''",
									  'limit'  => array( $start, $lend ) ) );

		$outer = $this->install->ipsclass->DB->simple_exec();

		//-----------------------------------------
		// Do it...
		//-----------------------------------------

		if ( $this->install->ipsclass->DB->get_num_rows() )
		{
			//-----------------------------------------
			// Got some to convert!
			//-----------------------------------------

			while( $r = $this->install->ipsclass->DB->fetch_row( $outer ) )
			{
				$image   = 0;
				$ext     = strtolower( str_replace( ".", "", substr( $r['attach_file'], strrpos( $r['attach_file'], '.' ) ) ) );
				$postkey = md5( $r['post_date'].','.$r['pid'] );

				if ( in_array( $ext, array( 'gif', 'jpeg', 'jpg', 'png' ) ) )
				{
					$image = 1;
				}

				$this->install->ipsclass->DB->do_insert( 'attachments', array( 'attach_ext'       => $ext,
													  'attach_file'      => $r['attach_file'],
													  'attach_location'  => $r['attach_id'],
													  'attach_is_image'  => $image,
													  'attach_hits'      => $r['attach_hits'],
													  'attach_date'      => $r['post_date'],
													  'attach_pid'       => $r['pid'],
													  'attach_post_key'  => $postkey,
													  'attach_member_id' => $r['author_id'],
													  'attach_approved'  => 1,
													  'attach_filesize'  => @filesize( ROOT_PATH.'uploads/'.$r['attach_id'] ),
							 )                      );

				$this->install->ipsclass->DB->do_update( 'posts', array( 'post_key' => $postkey ), 'pid='.$r['pid'] );
				$this->install->ipsclass->DB->simple_exec_query( array( 'update' => 'topics', 'set' => 'topic_hasattach=topic_hasattach+1', 'where' => 'tid='.$r['topic_id'] ) );
			}

			$this->install->saved_data['st'] = $end;
			$this->install->message = "����� � $start �� $end ���������������...";
			$this->install->saved_data['workact'] = 'step_8';
		}
		else
		{
			$this->install->message = "��� ������������� ����� ���������������, �������� ��������������� �������������...";
			$this->install->saved_data['workact'] = 'step_9';
			$this->install->saved_data['st'] 	  = 0;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 9: CONVERT MEMBERS
	/*-------------------------------------------------------------------------*/

	function step_9()
	{
		$this->install->ipsclass->DB->return_die = 1;

		$start = intval($this->install->saved_data['st']) ? intval($this->install->saved_data['st']) : 0;
		$lend  = 300;
		$end   = $start + $lend;

		//-----------------------------------------
		// In steps...
		//-----------------------------------------

		$o = $this->install->ipsclass->DB->query( $this->sql_members( $start, $end ) );

		//-----------------------------------------
		// Do it...
		//-----------------------------------------

		if ( $this->install->ipsclass->DB->get_num_rows() )
		{
			//-----------------------------------------
			// Got some to convert!
			//-----------------------------------------

			while ( $r = $this->install->ipsclass->DB->fetch_row($o) )
			{
				if ( $r['mextra'] )
				{
					$this->install->ipsclass->DB->do_update( 'member_extra',
									array( 'aim_name'        => $r['aim_name'],
										   'icq_number'      => $r['icq_number'],
										   'website'         => $r['website'],
										   'yahoo'           => $r['yahoo'],
										   'interests'       => $r['interests'],
										   'msnname'         => $r['msnname'],
										   'vdirs'           => $r['vdirs'],
										   'location'        => $r['location'],
										   'signature'       => $r['signature'],
										   'avatar_location' => $r['avatar'],
										   'avatar_size'     => $r['avatar_size'],
										   'avatar_type'     => preg_match( "/^upload\:/", $r['avatar'] ) ? 'upload' : ( preg_match( "#^http://#", $r['avatar'] ) ? 'url' : 'local' )
								 ), 'id='.$r['mextra']        );
				}
				else
				{
					$this->install->ipsclass->DB->do_insert( 'member_extra',
									array( 'id'              => $r['id'],
										   'aim_name'        => $r['aim_name'],
										   'icq_number'      => $r['icq_number'],
										   'website'         => $r['website'],
										   'yahoo'           => $r['yahoo'],
										   'interests'       => $r['interests'],
										   'msnname'         => $r['msnname'],
										   'vdirs'           => $r['vdirs'],
										   'location'        => $r['location'],
										   'signature'       => $r['signature'],
										   'avatar_location' => $r['avatar'],
										   'avatar_size'     => $r['avatar_size'],
										   'avatar_type'     => preg_match( "/^upload\:/", $r['avatar'] ) ? 'upload' : ( preg_match( "#^http://#", $r['avatar'] ) ? 'url' : 'local' )
								 )  );
				}
			}

			$this->install->saved_data['st'] = $end;
			$this->install->message = "��������������� ������������ � $start �� $end...";
			$this->install->saved_data['workact'] = 'step_9';
		}
		else
		{
			$this->install->message = "����������� ������������� ���������, �������������� email ������������� ��� ������� converge...";
			$this->install->saved_data['workact'] = 'step_10';
			$this->install->saved_data['st'] 	  = 0;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 10: CHECK EMAIL ADDRESSES
	/*-------------------------------------------------------------------------*/

	function step_10()
	{
		$this->install->ipsclass->DB->return_die = 1;

		$start = intval($this->install->saved_data['st']) ? intval($this->install->saved_data['st']) : 0;
		$lend  = 300;
		$end   = $start + $lend;

		//-----------------------------------------
		// In steps...
		//-----------------------------------------

		$o = $this->install->ipsclass->DB->query( $this->sql_members_email( $lend ) );

		//-----------------------------------------
		// Do it...
		//-----------------------------------------

		while ( $r = $this->install->ipsclass->DB->fetch_row($o) )
		{
			if ( $r['count'] < 2 )
			{
				break;
			}
			else
			{
				$dupe_emails[] = $r['email'];
			}
		}

		if ( count( $dupe_emails ) )
		{
			foreach( $dupe_emails as $email )
			{
				$first = 0;

				$this->install->ipsclass->DB->build_query( array( 'select' => 'id,name,email', 'from' => 'members', 'where' => "email='{$email}'", 'order' => 'joined' ) );
				$this->install->ipsclass->DB->exec_query();

				while( $r = $this->install->ipsclass->DB->fetch_row() )
				{
					// First?

					if ( ! $first )
					{
						$first = 1;
						continue;
					}
					else
					{
						// later dupe..

						$push_auth[] = $r['id'];
					}
				}
			}

			if ( count( $push_auth ) )
			{
				$this->install->ipsclass->DB->do_update( 'member_extra', array( 'bio' => 'dupemail' ), 'id IN ('.implode(",", $push_auth).")" );
				$this->install->ipsclass->DB->query( $this->sql_members_email_update( $push_auth ) );
			}

			$this->install->saved_data['st'] = $end;
			$this->install->message = "��������� email'� ������������� c $start �� $end...";
			$this->install->saved_data['workact'] = 'step_10';
		}
		else
		{
			$this->install->message = "��� ���������������� email ���������, ��������� � ������������� coverge-�������...";
			$this->install->saved_data['workact'] = 'step_11';
			$this->install->saved_data['st'] 	  = 0;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 11: CONVERGE
	/*-------------------------------------------------------------------------*/

	function step_11()
	{
		$this->install->ipsclass->DB->return_die = 1;

		$start = intval($this->install->saved_data['st']) ? intval($this->install->saved_data['st']) : 0;
		$lend  = 300;
		$end   = $start + $lend;

		$max = 0;

		$this->install->ipsclass->DB->build_query( array( 'select' => 'id', 'from' =>'members', 'where' => "id > {$end}" ) );
		$this->install->ipsclass->DB->exec_query();

		$max = $this->install->ipsclass->DB->fetch_row();

		$o = $this->install->ipsclass->DB->query( $this->sql_members_converge( $start, $end ) );

		$found = 0;

		//-----------------------------------------
		// Do it...
		//-----------------------------------------

		while ( $r = $this->install->ipsclass->DB->fetch_row($o) )
		{
			if ( ! $r['cid'] or ! $r['id'] )
			{
				$r['password'] = $r['password'] ? $r['password'] : $r['legacy_password'];

				$salt = $this->install->ipsclass->converge->generate_password_salt(5);
				$salt = str_replace( '\\', "\\\\", $salt );

				$this->install->ipsclass->DB->do_insert( 'members_converge',
								array( 'converge_id'        => $r['id'],
									   'converge_email'     => strtolower($r['email']),
									   'converge_joined'    => $r['joined'],
									   'converge_pass_hash' => md5( md5($salt) . $r['password'] ),
									   'converge_pass_salt' => $salt
							 )       );

				$member_login_key = $this->install->ipsclass->converge->generate_auto_log_in_key();

				$this->install->ipsclass->DB->do_update( 'members', array( 'member_login_key' => $member_login_key, 'email' => strtolower($r['email']) ), 'id='.$r['id'] );

				if( $r['id'] == $this->install->saved_data['mid'] )
				{
					// Reset loginkey

					$this->install->saved_data['loginkey'] 					= $member_login_key;
					$this->install->ipsclass->member['member_login_key']	= $member_login_key;
					$this->install->saved_data['securekey'] 				= $this->install->ipsclass->return_md5_check();
				}
			}

			$found++;
		}

		if ( ! $found and ! $max['id'] )
		{
			$this->install->message = "Converge-������ ���������������, �������� ��������������� ������ ���������...";
			$this->install->saved_data['workact'] = 'step_12';
			$this->install->saved_data['st'] 	  = 0;
		}
		else
		{
			$this->install->saved_data['st'] = $end;
			$this->install->message = "Converge-������� ��������������� ��� ������������� � $start �� $end...";
			$this->install->saved_data['workact'] = 'step_11';
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 12: CONVERT PMs
	/*-------------------------------------------------------------------------*/

	function step_12()
	{
		$this->install->ipsclass->DB->return_die = 1;

		$start = $start = intval($this->install->saved_data['st']) ? intval($this->install->saved_data['st']) : 0;
		$lend  = 300;
		$end   = $start + $lend;

		//-----------------------------------------
		// In steps...
		//-----------------------------------------

		$this->install->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'messages', 'limit' => array( $start, $lend ) ) );
		$o = $this->install->ipsclass->DB->simple_exec();

		//-----------------------------------------
		// Do it...
		//-----------------------------------------

		if ( $this->install->ipsclass->DB->get_num_rows() )
		{
			//-----------------------------------------
			// Got some to convert!
			//-----------------------------------------

			while ( $r = $this->install->ipsclass->DB->fetch_row($o) )
			{
				if ( ! $r['msg_date'] )
				{
					$r['msg_date'] = $r['read_date'];
				}

				if ( $r['vid'] != 'sent' )
				{
					$this->install->ipsclass->DB->do_insert( 'message_text',
									array( 'msg_date'          => $r['msg_date'],
										   'msg_post'          => stripslashes($r['message']),
										   'msg_cc_users'      => $r['cc_users'],
										   'msg_author_id'     => $r['from_id'],
										   'msg_sent_to_count' => 1,
										   'msg_deleted_count' => 0,
								  )      );

					$msg_id = $this->install->ipsclass->DB->get_insert_id();

					$this->install->ipsclass->DB->do_insert( 'message_topics',
									array( 'mt_msg_id'     => $msg_id,
										   'mt_date'       => $r['msg_date'],
										   'mt_title'      => $r['title'],
										   'mt_from_id'    => $r['from_id'],
										   'mt_to_id'      => $r['recipient_id'],
										   'mt_vid_folder' => $r['vid'],
										   'mt_read'       => $r['read_state'],
										   'mt_tracking'   => $r['tracking'],
										   'mt_owner_id'   => $r['recipient_id'],
								 )        );
				}
				else
				{
					$this->install->ipsclass->DB->do_insert( 'message_text',
									array( 'msg_date'          => $r['msg_date'],
										   'msg_post'          => stripslashes($r['message']),
										   'msg_cc_users'      => $r['cc_users'],
										   'msg_author_id'     => $r['from_id'],
										   'msg_sent_to_count' => 1,
										   'msg_deleted_count' => 0,
								  )      );

					$msg_id = $this->install->ipsclass->DB->get_insert_id();

					$this->install->ipsclass->DB->do_insert( 'message_topics',
									array( 'mt_msg_id'     => $msg_id,
										   'mt_date'       => $r['msg_date'],
										   'mt_title'      => $r['title'],
										   'mt_from_id'    => $r['from_id'],
										   'mt_to_id'      => $r['recipient_id'],
										   'mt_vid_folder' => $r['vid'],
										   'mt_read'       => $r['read_state'],
										   'mt_tracking'   => $r['tracking'],
										   'mt_owner_id'   => $r['from_id'],
								 )        );
				}

			}

			$this->install->saved_data['st'] = $end;
			$this->install->message = "���������� ������ ��������� � $start �� $end...";
			$this->install->saved_data['workact'] = 'step_12';
		}
		else
		{
			$this->install->message = "������ ��������� ���������������, ��������� � ���������� ������-��������� ���...";
			$this->install->saved_data['workact'] = 'step_13';
			$this->install->saved_data['st'] 	  = 0;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 13: CONVERT TOPIC MULTI_MODS
	/*-------------------------------------------------------------------------*/

	function step_13()
	{
		$this->install->ipsclass->DB->return_die = 1;

		$this->install->ipsclass->DB->build_query( array( 'select' => '*', 'from' => 'forums' ) );
		$f = $this->install->ipsclass->DB->exec_query();

		$final = array();

		while ( $r = $this->install->ipsclass->DB->fetch_row($f) )
		{
			$mmids = preg_split( "/,/", $r['topic_mm_id'], -1, PREG_SPLIT_NO_EMPTY );

			if ( is_array( $mmids ) )
			{
				foreach( $mmids as $m )
				{
					$final[ $m ][] = $r['id'];
				}
			}
		}

		$real_final = array();

		foreach( $final as $id => $forums_ids )
		{
			$ff = implode( ",",$forums_ids );

			$this->install->ipsclass->DB->do_update( 'topic_mmod', array( 'mm_forums' => $ff ), 'mm_id='.$id );
		}

		$this->install->message = "������-��������� ��� ���������������, �������� ��������� ������ - ���� 2...";
		$this->install->saved_data['workact'] = 'step_14';
	}

	/*-------------------------------------------------------------------------*/
	// STEP 14: ALTER POST TABLE II
	/*-------------------------------------------------------------------------*/

	function step_14()
	{
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."posts DROP attach_id, DROP attach_hits, DROP attach_type, DROP attach_file;";
		$SQL[] = "alter table ".SQL_PREFIX."posts change queued queued tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts drop forum_id;";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "���� ������� ��������� ��������, ��������� � ������� ���...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_15';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 15: ALTER TOPIC TABLE II
	/*-------------------------------------------------------------------------*/

	function step_15()
	{
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics add topic_firstpost int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topics add topic_queuedposts int(10) NOT NULL default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics DROP INDEX forum_id;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics ADD INDEX forum_id(forum_id,approved,pinned);";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics ADD INDEX(last_post);";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "���� ������� ��� ��������, �������� ������� �������������...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_16';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 16: ALTER MEMBERS TABLE II
	/*-------------------------------------------------------------------------*/

	function step_16()
	{
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members add has_blog TINYINT(1) NOT NULL default '0'";
		$SQL[] = "alter table ".SQL_PREFIX."members add sub_end int(10) NOT NULL default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members DROP msg_from_id, DROP msg_msg_id;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members DROP org_supmod, DROP integ_msg;";
		$SQL[] = "alter table ".SQL_PREFIX."members DROP aim_name, DROP icq_number, DROP website, DROP yahoo, DROP interests,
				  DROP msnname, DROP vdirs, DROP signature, DROP location, DROP avatar, DROP avatar_size;";
		$SQL[] = "alter table ".SQL_PREFIX."members change auto_track auto_track varchar(50) default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."members add subs_pkg_chosen smallint(3) NOT NULL default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members change temp_ban temp_ban varchar(100) default '0'";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."members change msg_total msg_total smallint(5) default '0'";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "���� ������� ������������� ��������, ��������� � ��������� ���������� ������...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_17';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 17: ALTER OTHERS TABLE II
	/*-------------------------------------------------------------------------*/

	function step_17()
	{
		$SQL[] = "alter table ".SQL_PREFIX."groups add g_attach_per_post int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topic_mmod add topic_approve tinyint(1) NOT NULL default '0';";

		$SQL[] = "alter table ".SQL_PREFIX."groups add g_can_msg_attach tinyint(1) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."pfields_data
				change fid pf_id smallint(5) NOT NULL auto_increment,
				change ftitle pf_title varchar(250) NOT NULL default '',
				change fdesc pf_desc varchar(250) NOT NULL default '',
				change fcontent pf_content text NULL,
				change ftype pf_type varchar(250) NOT NULL default '',
				change freq pf_not_null tinyint(1) NOT NULL default '0',
				change fhide pf_member_hide tinyint(1) NOT NULL default '0',
				change fmaxinput pf_max_input smallint(6) NOT NULL default '0',
				change fedit pf_member_edit tinyint(1) NOT NULL default '0',
				change forder pf_position smallint(6) NOT NULL default '0',
				change fshowreg pf_show_on_reg tinyint(1) NOT NULL default '0',
				add pf_input_format text NULL,
				add pf_admin_only tinyint(1) NOT NULL default '0',
				add pf_topic_format text NULL;";

		$this->install->error   = array();
		$this->sqlcount = 0;

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			$this->install->ipsclass->DB->query( $query );

			if ( $this->install->ipsclass->DB->error )
			{
				$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}

		$this->install->message = "���������� ������� ��������, �������� ���������� ��������� ������...<br /><br />��������� $this->sqlcount ��������...";
		$this->install->saved_data['workact'] = 'step_18';
	}


	/*-------------------------------------------------------------------------*/
	// STEP 18: SAFE INSERTS
	/*-------------------------------------------------------------------------*/

	function step_18()
	{
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (1, '������� ������ (��� � ���)', 'cleanout.php', 1076074920, -1, -1, -1, 59, '2a7d083832daa123b73a68f9c51fdb29', 1, '�������� ������ ������, �������� ��������������� �����, ��������� ����������� ������',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (2, '�������� ���������� (��� � ����)', 'rebuildstats.php', 1076112000, -1, -1, 0, 0, '640b9a6c373ff207bc1b1100a98121af', 1, '�������� ���������� ������',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (3, '������� ������ (��� � ����)', 'dailycleanout.php', 1076122800, -1, -1, 3, 0, 'e71b52f3ff9419abecedd14b54e692c4', 1, '�������� ������ �������� �� ���� � ������ ����� ��������� ���',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager VALUES (4, '����������� ����������� � �������', 'calendarevents.php', 1076100960, -1, -1, 12, -1, '2c148c9bd754d023a7a19dd9b1535796', 1, '�������� ���� ����������� ������� � ���� ��������',1,'',0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_id, task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled) VALUES (9, '���������� ����������', 'announcements.php', 1080747660, -1, -1, 4, -1, 'e82f2c19ab1ed57c140fccf8aea8b9fe', 1, '�������� ������ ���������� � ���������� ���� ����������', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('�������� ��������', 'bulkmail.php', 1086706080, -1, -1, -1, -1, '61359ac93eb93ebbd935a4e275ade2db', 0, '����������� ������������� ��� ��������� �������� ��� ��������. �� ��������!', 0, 'bulkmail', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('����� ��� � ������ (��� � ����)', 'dailydigest.php', 1086912600, -1, -1, 0, 10, '723cab2aae32dd5d04898b1151038846', 1, '�������� ���������� �������� ��� � ������ ������', 1, 'dailydigest', 0);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."task_manager (task_title, task_file, task_next_run, task_week_day, task_month_day, task_hour, task_minute, task_cronkey, task_log, task_description, task_enabled, task_key, task_safemode) VALUES ('����� ��� � ������ (��� � ������)', 'weeklydigest.php', 1087096200, 0, -1, 3, 10, '7e7fccd07f781bdb24ac108d26612931', 1, '�������� ������������ �������� ��� � ������ ������', 1, 'weeklydigest', 0);";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('��������� ���������', '���� ��� ���������� ��������� �����������, ������� �������� ������� �� ���������� ��������� � ������������, ����� ���������� ��������� � ������. ����������� �� ��������� � ��� �� ����.', 'snapback', '<a href=\"index.php?act=findpost&amp;pid={content}\"><{POST_SNAPBACK}></a>', 0, '[snapback]100[/snapback]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('�� ������� ����', '������������ ������ �� ������� ����.', 'right', '<div align=\'right\'>{content}</div>', 0, '[right]����� ����� �����[/right]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('�� ������ ����', '������������ ������ �� ������ ����.', 'left', '<div align=\'left\'>{content}</div>', 0, '[left]���� ����� ����� ������������ ������������ �� ������ ����[/left]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('�� ������', '������������ ������ �� ������.', 'center', '<div align=\'center\'>{content}</div>', 0, '[center]������������ ������ ����� �� ������[/center]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('������ �� ����', '���� ��� ��������� ����� ������������ ������ �� ����, ���� ���� �� �����.', 'topic', '<a href=\'index.php?showtopic={option}\'>{content}</a>', 1, '[topic=100]����� �����![/topic]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('������ �� ���������', '���� ��� ��������� ����� ������������ ������ �� ���������, ���� ���� ��� �����.', 'post', '<a href=\'index.php?act=findpost&pid={option}\'>{content}</a>', 1, '[post=100]����� �����![/post]');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."custom_bbcode (bbcode_title, bbcode_desc, bbcode_tag, bbcode_replace, bbcode_useoption, bbcode_example) VALUES ('���� ����', '���� ��� ����� ���������� ���� ���� � ������� ���������. ����������� ��� ������� ������ � �������� ����� ���������.', 'codebox', '<div class=\'codetop\'>CODE</div><div class=\'codemain\' style=\'height:200px;white-space:pre;overflow:auto\'>{content}</div>', 0, '[codebox]long_code_here = '';[/codebox]');";



		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='USD', subcurrency_desc='United States Dollars', subcurrency_exchange='1.00', subcurrency_default=1;";
		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='GBP', subcurrency_desc='United Kingdom Pounds', subcurrency_exchange=' 0.630776', subcurrency_default=0;";
		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='CAD', subcurrency_desc='Canada Dollars', subcurrency_exchange='1.37080', subcurrency_default=0;";
		$SQL[] = "insert into ".SQL_PREFIX."subscription_currency SET subcurrency_code='EUR', subcurrency_desc='Euro', subcurrency_exchange='0.901517', subcurrency_default=0;";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('PayPal', 'paypal', '', '', '', '', '', '', '', 0, 1, 'All major credit cards accepted. See <a href=\"https://www.paypal.com/affil/pal=9DJEWQQKVB6WL\" target=\"_blank\">PayPal</a> for more information.', '', 1, 'USD');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('NOCHEX', 'nochex', '', '', '', '', '', '', '', 0, 1, 'UK debit and credit cards, such as Switch, Solo and VISA Delta. All prices will be convereted into GBP (UK Pounds) upon ordering.', NULL, 1, 'GBP');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('Post Service', 'manual', '', '', '', '', '', '', '', 0, 0, 'You can use this method if you wish to send us a check, postal order or international money order.', NULL, 1, 'USD');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('2CheckOut', '2checkout', '', '', '', '', '', '', '', 1, 1, 'All major credit cards accepted. See <a href=\'http://www.2checkout.com/cgi-bin/aff.2c?affid=28376\' target=\'_blank\'>2CheckOut</a> for more information.', NULL, 1, 'USD');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."subscription_methods (submethod_title, submethod_name, submethod_email, submethod_sid, submethod_custom_1, submethod_custom_2, submethod_custom_3, submethod_custom_4, submethod_custom_5, submethod_is_cc, submethod_is_auto, submethod_desc, submethod_logo, submethod_active, submethod_use_currency) VALUES ('Authorize.net', 'authorizenet', '', '', '', '', '', '', '', '1', '1', NULL, NULL, '1', 'USD');";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (1, 'pdf', 'application/pdf', 1, 0, 'folder_mime_types/pdf.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (2, 'png', 'image/png', 1, 1, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (3, 'viv', 'video/vivo', 1, 0, 'folder_mime_types/win_player.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (4, 'wmv', 'video/x-msvideo', 1, 0, 'folder_mime_types/win_player.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (5, 'html', 'application/octet-stream', 1, 0, 'folder_mime_types/html.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (6, 'ram', 'audio/x-pn-realaudio', 1, 0, 'folder_mime_types/real_audio.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (7, 'gif', 'image/gif', 1, 1, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (8, 'mpg', 'video/mpeg', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (9, 'ico', 'image/ico', 1, 0, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (10, 'tar', 'application/x-tar', 1, 0, 'folder_mime_types/zip.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (11, 'bmp', 'image/x-MS-bmp', 1, 0, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (12, 'tiff', 'image/tiff', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (13, 'rtf', 'text/richtext', 1, 0, 'folder_mime_types/rtf.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (14, 'hqx', 'application/mac-binhex40', 1, 0, 'folder_mime_types/stuffit.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (15, 'aiff', 'audio/x-aiff', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (31, 'zip', 'application/zip', 1, 0, 'folder_mime_types/zip.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (17, 'ps', 'application/postscript', 1, 0, 'folder_mime_types/eps.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (18, 'doc', 'application/msword', 1, 0, 'folder_mime_types/doc.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (19, 'mov', 'video/quicktime', 1, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (20, 'ppt', 'application/powerpoint', 1, 0, 'folder_mime_types/ppt.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (21, 'wav', 'audio/x-wav', 1, 0, 'folder_mime_types/music.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (22, 'mp3', 'audio/x-mpeg', 1, 0, 'folder_mime_types/music.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (23, 'jpg', 'image/jpeg', 1, 1, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (24, 'txt', 'text/plain', 1, 0, 'folder_mime_types/txt.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (25, 'xml', 'text/xml', 1, 0, 'folder_mime_types/script.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (26, 'css', 'text/css', 1, 0, 'folder_mime_types/script.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (27, 'swf', 'application/x-shockwave-flash', 0, 0, 'folder_mime_types/quicktime.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (32, 'php', 'application/octet-stream', 1, 0, 'folder_mime_types/php.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (28, 'htm', 'application/octet-stream', 1, 0, 'folder_mime_types/html.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (29, 'jpeg', 'image/jpeg', 1, 1, 'folder_mime_types/gif.gif');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."attachments_type (atype_id, atype_extension, atype_mimetype, atype_post, atype_photo, atype_img) VALUES (33, 'gz', 'application/x-gzip', 1, 0, 'folder_mime_types/zip.gif');";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('skin_id_cache', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('bbcode', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('moderators', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('multimod', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('banfilters', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('attachtypes', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('emoticons', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('forum_cache', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('badwords', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('systemvars', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('ranks', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('group_cache', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('stats', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('profilefields', 'a:0:{}', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('settings','', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('languages', '', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('birthdays', 'a:0:{}', '', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."cache_store (cs_key, cs_value, cs_extra, cs_array) VALUES ('calendar', 'a:0:{}', '', 1);";

		$SQL[] = "INSERT INTO ".SQL_PREFIX."skin_sets (set_skin_set_id, set_name, set_image_dir, set_hidden, set_default, set_css_method, set_skin_set_parent, set_author_email, set_author_name, set_author_url, set_css, set_cache_macro, set_wrapper, set_css_updated, set_cache_css, set_cache_wrapper, set_emoticon_folder) VALUES (1, 'IPB Master Skin Set', '1', 0, 0, '0', -1, '', '', '', '', '', '', 1079109298, '', '', 'default');";
		//$SQL[] = "INSERT INTO ".SQL_PREFIX."skin_sets (set_skin_set_id, set_name, set_image_dir, set_hidden, set_default, set_css_method, set_skin_set_parent, set_author_email, set_author_name, set_author_url, set_css, set_cache_macro, set_wrapper, set_css_updated, set_cache_css, set_cache_wrapper, set_emoticon_folder) VALUES (2, 'IPB Default Skin', '1', 0, 1, '0', -1, 'ipbauto@invisionboard.com', 'Invision Power Services', 'www.invisionboard.com', '', '', '', 1074679074, '', '', 'default');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."skin_sets (set_skin_set_id, set_name, set_image_dir, set_hidden, set_default, set_css_method, set_skin_set_parent, set_author_email, set_author_name, set_author_url, set_css, set_cache_macro, set_wrapper, set_css_updated, set_cache_css, set_cache_wrapper, set_emoticon_folder) VALUES (3, 'IPB Pre-2.0 Skins', '1', 0, 0, '0', -1, 'ipbauto@invisionboard.com', 'Invision Power Services', 'www.invisionboard.com', '', '', '', 1074679074, '', '', 'default');";


		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (1, '�������� ��������� ������', '������ �����, ����������� ������ ������� ��������� �������, ����� ���: �������� ������, �����, ���� � �. �.', 17);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (2, '�������������� � ����������� ������� ', '���������� ������������� ��������� �������. ����� ���� ������ ��������� ������� �������� �� ������.', 16);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (3, '������ ���� � �������', '��������� ������� ����������� ���� � ������� �� ������.', 7);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (4, '���������������� ����������������', '���������� ��������� ���������������� ��������� �������������', 22);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (5, '����, ��������� � ������', '������ ����� ��� ��������� ���������� ����������� ���, ����������� ��� �������� ���������, �������.', 32);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (6, '������������ ������', '��������� ������ ������������ ������, ������ �� �����, ������ �� ������� ������.', 18);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (7, '�������� Cookies', '������ ����� �� ��������� cookies. ������� �������� ��� ������������ ������ ����������� �� ������.', 3);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow) VALUES (8, '������� COPPA', '���� ���� ���������, ���� �� ������ �������� ������������� <a href=\'http://www.ftc.gov/ogc/coppa1.htm\'>COPPA</a>. � ������ �� ���������.', 3, 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (9, '��������� � ��� ��������', '������ ����� �� ��������� ��������� � ����������� ������� ������.', 8);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (10, '������� ������', '��������� ��������� ������� ������.', 2);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (11, '������ ���������', '���������� ��������� ������� ������ ��������� ������.', 3);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (12, '��������� e-mail', '������ ����� ��� ���������� �������� ������� ������ (��������� e-mail ������, ������ �������� ����� � ������).', 7);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (13, '������� ��������������/��������', '������ �����, ����������� ��������� �������� �������������� �������������.', 15);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (14, '��������� �������', '������� &mdash; ����������� �����, � ������� ������������ ���� ����� ��������, ��� ����� �������� ����������� ������������ ���� ��� �� ��������� ��������.', 6);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (15, '���������/���������� ������', '������ ����� ��� ���������� ���������� ������. ���� ����� ��������� � ����������� ���������, �� ������ � ���� ����� ����� ������ ��������������. ������� ��� ���������� ����������� ����� �� ������.', 2);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (16, '������� ������������ ��������� �������', '������ �����, ���������� �� ��������� ������� �������� �� ���������� ��������. ��������� ��������� ����� �������, �������� � ��������� ������� ������� ��������� �������.', 7);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (17, '������� ������', '���������, ����������� ������ ������ ���������� ������ ������ � ������, ���������� ��� �����������.', 4);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (18, '������������ IP.Converge', '���������� �������� ����������� IP.Converge.', 1);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count) VALUES (19, '��������� ������� ������', '������ ����� ��� ������ ��������� ��������� �������.', 2);";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (20,'��������� Invision Chat (��������� ������)', '��� ��������� ��������� ���������� ����� ��������� ������ Invision Chat.', 14, 1, 'chat');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (21,'��������� Invision Chat', '��� ��������� ��������� ���������� ������ Invision Chat (������ 2004+)', 10, 1, 'chat04');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (22,'IPB ������', '��������� IPB �������.', 20, 0, 'ipbportal');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (23,'������� ��������', '���������� �������� ����������. ��� ������ �� ���������.', 3, 1, 'subsmanager');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (24,'��������', '��������� �������� ��������.', 3, 1, 'ipbreg');";
		$SQL[] = "INSERT INTO ".SQL_PREFIX."conf_settings_titles (conf_title_id, conf_title_title, conf_title_desc, conf_title_count, conf_title_noshow, conf_title_keyword) VALUES (25,'�������� ���������', '���� ���� ��������� ������������ ������ �������� ���������.', 2, 1, 'ipbcopyright');";


		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (1, '��� ������������ ������������� ���� ������� ������, ������������� ������� ������������������ �� ������. ����������� �� ������ ��������� ��������� � ������� � ��� ����� ���� ���� �����. <br /> <br />��� ����������� ���������� ����������� ������� ��� �������� e-mail �����. ������������� ����� ����������� �� ��� ������������� ����� ����������� �� ���������� ���� e-mail ������. ���� ������ ���������� �������������� ��������, �� ������ �������� �� ���� �� ����� �����������. ���� �� �� �����-�� ������� �� �������� ������ ��� ���������, ������� �� ����� � � ������ ������� ����� ������, �������������� ������� �������� �������� ������ ��� ���������. <br /><br />����� ��������, ��� ������������� ��� ������ ����� �������� ���� �����������. �� ���� �� ����� ������ �������� �� ����� �����������. <br /> <br />����� ���������� ����������� � �������� ����������� �� ������, �� �������� ������ � ����� ������ ���������� � ����� ������ ��������� ������.<br /><br />��� ��������� �������������� ���������� �� ���� �������� � ��������������� ������, ����������, �������� ��������������� ������� ������ �� ������.', '��� ������������������ � �������� �������������� ������������ ������������������ �������������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (2, '������������� cookies �� �����������, �� ������������ �������������. Cookies ������������ ��� ������������� ������������ ��������� ���������� �� ������ ��� ������ ��������� ������, � ��� �� ����������� ��������������� ����� �� ����� ��� ����� �����������.<br /><br />���� ��� ��������� �� ����� ������������ cookie ��� ��������� ������, �� ������� ������������� ����� ��������� �� ���� ������� �� ������ ������������� ������, ��� �������� ������ ��������� ����������� ���� ��������.<br /><br /><br /><b>������� Cookies</b><br /><br />� ����� ������ �� ������ �������� cookies, ����� �� ������, ����������� � ������ ����� ������� �������� ������ (��� ����� ������ �������� ������). ���� ��� �� ���������, �� ��� ���������� ������� cookies �������.<br /><br /><i>Internet Explorer 7:</i><ul><li>������� ���� &laquo;������&raquo; � ����� ��������.</li><li>�������� ����� &laquo;�������� ������������&raquo;.</li><li>��� ������� &laquo;������&raquo; ������� ������ &laquo;�������&raquo;.</li><li>�������� ����� �������� cookies.</li></ul><i>Internet Explorer 6:</i><ul><li>������� ���� &laquo;������&raquo; � ����� ��������.</li><li>�������� ����� &laquo;�������� ������������&raquo;.</li><li>��� ������� &laquo;��������� ����� ���������&raquo; ������� ������ &laquo;������� Cookie&raquo;.</li></ul><i>Firefox:</i><ul><li>������� ���� &laquo;������&raquo; � ����� ��������.</li><li>�������� ����� &laquo;�������� ������ ������&raquo;.</li><li>����� &laquo;Cookies&raquo; ������ ���� ������.</li><li>������� &laquo;�������� ������ ������ ������&raquo;.</li></ul><i>Opera:</i><ul><li>������� ���� &laquo;������&raquo; � ����� ��������.</li><li>�������� ����� &laquo;���������&raquo;.</li><li>�������� ����� &laquo;�����������&raquo;.</li><li>�������� ����� &laquo;Cookies&raquo;.</li><li>�������� &laquo;���������� Cookies&raquo;.</li><li>�������� ����, cookies �������� �� ������ �������.</li><li>�������� ����� &laquo;�������&raquo;.</li></ul><i>Safari:</i><ul><li>�� ���� &laquo;Safari&raquo; �������� ����� &laquo;���������&raquo;.</li><li>�� ����������� ���� ������������ �������� &laquo;��������&raquo;.</li><li>������� &laquo;�������� Cookies&raquo;.</li><li>�������� cookie (��� ���������) ��� �������� ��� ������� ������ &laquo;������� ���&raquo;.</li></ul>', '������������ ������������� cookies � �������� cookies, ������������� �������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (3, '�������������� ���������� ��� �������� ������', '������� ��������������� ������ �������� ������������, � ������� ��� ������ ������������� ���������� ��� �����������.<br>��� ��������, ��� �� �� � ��������� ������� ���, ��� ������� ������, �.�. ������������� ������ ����������. ��, ��� �� �����, �� ������ ������������ ���� ������.<br><br>��� �������������� ������, ������� �� ������ <a href='index.php?act=Reg&CODE=10'>�������������� �������� ������</a>, ������� ��������� �� �������� �����������.<br><br>� ���������� ����������� �� ��������������, �� ������ ������������ ��� ���.', '��� ������������ ������� ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (4, '���� ������ ���������� (������ ������)', '� ������ ���������� �� ������ ������ ���� ��������� ��� ������. �� ������ �������, ��� ����� ���������, � �������� ����� ��� ���.<br /><br /><b>������ ����</b><br />������ ������ �� ������������� ������� ����� �� ������ ����� � ��������������� ������� ������ �� ������.<br /><br /><b>��������</b><br /><br /><i>����:</i><br />����� ����������� ������ ���, �� ������� �� ���������. �� ������ �������� ��� ����������� ��� ������ ����. ����� �� �� ������ ������� �������� �� ��������� ����.<br /><br /><i>������:</i><br />����� ����������� ������ �������, �� ������� �� ���������. �� ������ �������� ��� ����������� ��� ������� ������. ����� �� �� ������ ������� �������� �� ��������� ������.<br /><br /><b>��� �������</b><br /><br /><i>�������� ������ ������:</i><br />����� �� ������ ������� ������ ����������, �������� ���� ������ �������� � ���������� ����������.<br /><br /><i>�������� ��������� ������:</i><br />����� �� ������ �������� ���������� � ����, ���������� ����� ����������� � ��������� ������������ � ������.<br /><br /><i>�������� �������:</i><br />����� �� ������ ������� � �������� ���� �������. � ��� ���� ����������� ������������ � ������� ���� ������ � �������� HTML ����, ���� ������������� ������ ������������ ���.<br /><br /><i>��������� �������:</i><br />����� �� ������ ������� � �������� ���� ������. � ����������� �� ��������� ������, ����������� ���������������, ����� ������� ������ � ������� �������� ������, ���� ������ ������ �� ������, ����������� �� ������ �����, ���� ��������� ������ ������ � ������ ����������. �����������, ����������� � ������ ����������, ����� ���������������� �� ������������� ��������������� ��������.<br /><br /><i>��������� ����������:</i><br />���� ������������� ������ ��������� ����� ������ ������������� ��������� ������������ ����������, �� �� ������ ��������� �����������, ������� ����� ���������� � ����� �������.<br /><br /><i>�������� ������������ ���:</i><br />���� �������������� ������� ������������ ����, �� � ���� ������ �� ������ ������ ��� ��� ������ ����������� �� ������.<br /><br /><b>���������:</b><br /><br /><i>������� ������������ ��������:</i><br />� ���� ������ ������������� ������� ������������ ��������, ���� ��� �������������� �� ������. �� ������ ������� ������ �������� � �������� �� ��� �������� ������������ ��������.<br /><br /><i>������������� ����� (attach'�):</i><br />����� �� ������ �������� � ������� ���� ������������� �����.<br /><br /><i>������ ������:</i><br />����� �� ������ ����������� ������� ������������� ��������� ���������� � ��� ������ ������ (��� �������, ��� �� �������� ����� ��������� ������ � ���������� ����� ������).<br /><br /><i>������������� �������������:</i><br />����� �� ������ ��������� ������� �������������� �������������.<br /><br /><i>��������� e-mail:</i><br />�� ���� �������� �������� ��������� ���������� ��������.<ul><li><u>������� ���������</u><ul><li><i>������ ��� ����� e-mail �� ������ �������������</i> � �� ������ ��������� ���������� ��� e-mail ����� ������������� ������.</li><li><i>�������� ��� ��� ���� ����������, ���������� ��������������� ������</i> � ������������� ����� ������������ ������ �������� �������������, ����� �������� �� ���������� �� ������.</li></ul></li><li><u>��������� ������</u><ul><li><i>��������� ����� ��������� �� ����������� ��� � ������������ ������-�����������</i> � ������� ��� �������, ����� ����� ���� ��� ��������� ����� ������� ��� �� e-mail.</li><li><i>���������� ���� �� e-mail ��� ��������� ����� ������ ���������</i> � ������� ��� �������, �� ������ ���������� �� e-mail � ��������� ������� ���������.</li><li><i>�������� ����������� �� e-mail �� ���������?</i> � ��� ��������� ���� ����� �� ������ ������������� ��������� �� ��� ����� � ����������� ����. ����������� ���������� ���� ��� ������ ������� �����������.</li></ul></li></ul><i>��������� ������:</i><br />� ���� ������ �� ������ ��������� ����������� �� �������� ���� ������. �� ������ ������� ������� ���� � ��������� ����� ����������� ������. ��� �� �������� ����� ���� ���������� ��������� ��� ���������� ���������.<br /><br /><i>�������� E-mail:</i><br />����� ����� �������� ���� e-mail �����.<br /><br /><i>�������� ������:</i><br />����� �� ������ �������� ���� ������ ��� ����� �� �����.', '�������������� ���������� � ������������ ����������, ��������, �������, �������� ������, ����� ����� � ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (5, '�� ������ ���������� ����������� ���������� ��� � ����� ���������� � �����. ������ ������������ � ������� ���������� ��� ������� ��� �������������� ��������� � ���, ��� � ���� ������� ����� ���������.<br /> <br />���������� ��� ������� �������� �� ����: <ul><li>����� �� ������ ������, ������� ��������� � ������� ����� ������ ����, � ������ ����� ��������� �� ����.</li><li>� ������� ��������� ������� � ������ ��������� ����������� �� e-mail ��� �������?�, ����������� ��� ������ ������.</li><li>����� ����� ���������� e-mail� � ����� ������ ���������� (������ ������), ��������� ������� �� ����� ��������� E-mail ����������� �� ���������?�, ����� ���� �� ������ ������������� ������������� �� ��� ����, � ������� �� ��������.</li></ul>��� �� ���������� ������ ������ ����� ��������:<br /><br /><b>����������� �����������</b><br />��� ����� ��������� ���������� ����������� ����� �� ����� ����, ��� ����� ���� ��� ����� ��� ������, ���������� �� ����� ����������.<br /><br /><b>����������� � ���������</b><br />��� ����� ��������� ���������� ����������� � ����� ���� ��� ���������, ���� ��� �� ���� �� ������.<br /><br /><b>���������� �����������</b><br />��� ����� �������� ���������� �������� ������ ����� ��� � ��������� �� ����.<br /><br /><b>������������ �����������</b><br />��� ����� ��������� ���������� ������������ ����� ����� ��� � ��������� �� ������.<br /><br /> <br />�� ����� ������ ����������� �� ����� �� �������� ������ � ��� ��������� ����� ���� � ���� �������, �� ������ ���������� �� ����. ��� �������� �� ������, ������� ������ ������ ������, ����������� � ������� ����� ������� ������, � ������ ����� ������������ �� �����.<br /> <br />����� ������� �������� �� ����� ������� ��� ����, ��� ����� ����� � ������� ������, ����� � ������� ��������� �������� ��.', '��� ����������� �� ���� ��� ����������� �� e-mail � ����� �������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (6, '������ ���� (PM)', '��� ������ ���� �� ������, ��������� ���������� e-mail �����. ����� �� ������ �������� � ���������� ������ ������������� ������, ������� ���� ������ � ������, ���������, ������������� � ������� �����.<br /><br /><b>����� ������ ���������</b><br />������ �� ������ ��������� ��������� ��������� ������������ ������. ���� � ��� ���� ������������ � ������ ������, �� �� ������ ������� ��� ���������� ������ ��� ������ ��� ������������ � ��������������� ���� ����� �������� ���������. ��� ���� ������������� ����������, ���� �� ������� � �����-���� ��������� � ����� ��� � ������ ������������� �� ������ ������� ����� � �������������.<br />��� �� ���������� ����� ������ ���� �������������, ������� ������������� ���������� ����� ������������� �� ���� ������ ��� ������� � ���� ���������� ���������.<br /><br />���� ������������� ������ �������� ���������� ������ ���������� ������������� ������������, �� ������ ������ ����� ����� ���������� �������������, � �� ������ ����� �� ������, � ���� ������ �������������. �� ��� �� ������ ��������������� ������ ������� ����������� ����� ��������� �� ������ �������������.<br /><br />� ��������� �������, ���� ��� ��������� ���������������, �� ���� ������ ������������ ���� ������ � HTML � ������ ����� �����. �� ����� ������ ������� ����� ��������� ����� ����� ��������� � ����� ������������廻 ��� ���������� ����� ������ � ����� ������������ �����. ��� ��������� ������� � ������ ����������� �� ���� ����������?�, �� ������� ������������ ����� ����� ���� ��������� �� ����������� ��������� ����, ��� ��������� �� ���� ������ ���������.<br /><br />�������, ���� ��������, �� �� ������ �������� ���������� � ������� ���������, �������� ����������� ��� ����.<br /><br /><b>��������</b><br />��� ����� ��� �������� ������ ���������, ������������ � ���. ����� �� ��������� ���������, �� ������� ���������� ������� ��������� � ��������� ���� ��������� ��� �� ������. �� ��� �� ������ ������� ��� ����������� ��������� �� �����.<br /><br /><b>������������</b><br />� ���� ����� �������� ����� ������������ ���������, ������� �������� ���� ��� ������ ����� ����������� ��������� ��� �����������.<br /><br /><b>��������������</b><br />����� �� ������ ��������� � ������ ������, ������� �� ������ ���������, ��� �������� ��� �����.<br /><br /><b>������� �����</b><br />����� ��� �����, �� ������ ������ ������� ��� ������ �� ����� ����� �����.<br /><br /><b>������������� �����</b><br />����� �� ������ ���������������, ������� ��� ��������� ����� ����� ��� ���������� ����� ����� ���, ��� ��� ����� ����� ������. �� �� ������ ������� ������ ��� �����, ��������� ����� ������� � ��� ����� ������������� � ���������.<br /><br /><b>������ ������</b><br />���� �� ������ ��������� ����� ������������� ��� ������������� ��� ���������. ��� ������ ������������ ��� ������ ������ �������������, �� ������� �� �� ������ �������� ������ ���������.<br /><br /><b>������������� ���������</b><br />���� ��� ������ ���� ����������, � � ����� � ���� �� �� � ��������� �������� ����� ������, �� ����� ���������� ��������� �����. ��� ���� ������ ����� �������������� �� ����� �������� � ������� HTML ��� Microsoft Excel, � ����� ������� �� ��� e-mail �����, ��������� ���� ��� ����������� �� ������.<br /><br /><b>�������� �� �����������</b><br />�� ���� �������� ���������� ��� ������, �� �������� �� ������ ����������. ��� ������ � ���, ��������� �� ���� ������ ������������ � ����� ��� ���������, � ���������� �����. �����, ���� �� ����������, � ���� ������ ���� �� ��������� �����������, �� ������ ������� ��� ������.', '�������� ������ ���������, �������������� ����� ������� �����, �������� �� ��������, ��������� ����������� ���������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (7, '������� � �������������� � ������ ���������� � ����������', '<b>������� � ��������������</b>
<br>
<br>��� ����� � ����������� ��� ������ ��� ��������� ������ ������������� ������, ������� �� ������ ���������������, ����������� � ������ ����� ������� �������� ������ ��� �� �������� ���������.
<br>
<br>� ���� ������ �� ������� ����� ���� ��������������� (��� ������� �������������� ������, ������� ������ �� ���� ��������), ���������������� (��� ������� �����������, ������� ����������� ������������� ���� �������� ������) � ����������� (��� ����������, ������������ �������������� ������� ������, � ������� ��� ���������).
<br>
<br>��� ����� �� ������ ���������������� ������, ���������� ��������� ��������������� � ��������������� ������, � ��� ����� �� ������ ��� ��� ���������, ����������� � ���������������� ��� �����������.
<br>
<br><b>������ ���������� � ���������</b>
<br>
<br>���� ������������� ������ ������� ��� �������, �� ������� ������ �������, ����� ������� ��������� ����. ��� ������ ���� �������, �� ������� ������ �������� � ��������� ���������� ������� ������� ������ (��� ��������������, � ���� � ������ ������ ��� ����������� �����������). ����������� ��� ������� ������ � ��� ������, ���� �� ������ �����-���� ��������� ������ ������ � ������ ���������. ��������� ������������ ��� ������� <b>��� ������� ����������� � �����������</b>. ��� �����������, �� ������ ������������ PM (������ ���� ������).', '��� ����� ������ ��������������� � ����������� ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (8, '�������� ���������� ������� �������������', '����� ���������� ������� ������������, ��� ����� ������ �� ��� ��� � ����� ����� �� ������. ���� �� ������ ���������� ����������� �������, �� �������� �� ���� ��� � ������� ����� ������.<br /><br />�� ������� �������� ������� ��������� ��� ����������� �������, � �� �������� � ������, ���������� ����������.<br /><br /><b>������������ ����������</b><br />��� ������, ������������� �����, ���������� ��������� ���������� ������������. � ��� ������: ������������ ���������� (���� ��� ���������), ������� ������������, ����� ��������� ����� (��������, ����� ��������� ������������), ������������ ���������� � ���������� ��������, ���� � ��������������. ���� �� ������ ������� ���������� ������������ (����� ���������, ���� ����������� � ������) � ���������� ��� ����� � ���.<br /><br />� ������ ��������� ������ � ������� ����������. ����� �� ������ ����� ��������� ��������� ������������, ����������� � ������ ��� ������ (�� ������ ������� ������� ������, ����� �� �� �����). ��� ��, ���� �� ������ ����������� ������� Invision Power Gallery � Blog, �� �� ������� �������������� ��������. ������ ������� ������� ��������� ����� ����������� �����������, � �� ����� ��� ������ ����� ������� ��������� ���� ������� � �����.<br /><br />� ������ ������� �� ������ �������, ��� ������� ���� �������, ����������� ����������� � ������ ������ ������������.<br /><br /><b>���������� �����������</b><br />� ��� ���� ����������� ��������� ����������� � ������ �������������. ����� �� �������������� �������, ������ �� ������ ������� ������ � ���������� �������������. ���� �� ������� �� ������ ��������� �����������, �� � ����������� ���� �� ������ �������� �����������.<br /><br /><b>��������� ������������</b><br />���� ������ ��� �� �������������� ����������� �������� ������ �������������, �� �� ������ ������� ����������� ����� ���������.<br /><br />������, �� ������ ������ �� �������� ������������, ����� �����������, ��������� �������  ��������, ����� ������� �� ����������� ���� �������� ��������� �����������. ������, �� ������ ������ �� ��������� ������ �ջ � ������ ��������� ����������� ������������.<br /><br />�� ������ ������������ ����������� ������� �������. ���������������� ����������� �� ����� ����������, � �� ����� ��� �������������� ����������� ����� �������� ��� ����. ������ ������� �������� ��������� ����������� �������� ����� ����������� ��� �� ����������� ��������� �����������.<br /><br /><b>������� ������������</b><br />������� ������������ ������� �������� �������������. ��� ���� ����������� �������� ������� ���������� ������������� ��� �������� ������ ������� �������������. ��� ��������� �������� �������� ������� ������������, ����� �������� �� ���������, ������� �������.<br /><br /><b>��������� ����� ������������ ��������</b><br />������ �������������� �� ������ �� ����� ��������� ������ �������, �� ������ ������� �������������� �������� � ��������, ������� ���������� ����������. ������ ��� ��������, �� ������� ��������� ���������� �� ��������� �������� ������ �������. ����� �� ������ ��������� ������������ ���������� ��� ������� ��. ���� ������� ����� ��������, �� ���������� ����� �������, ����� �� ������� ������ ���������� ��������� ��� �� �� ������ �������� ���������� � ����. ��� ���� ����� ������ �� ���� �������, �� ��� ��������� ��������������� � ��� �������, � �� � ����� ���������� ��� �������. ������� ���� �� ������ ������� ������� ����������� � ����� �������������, ���������� ��� � ������ � �� ����������� ������������. �� ������ ��������� ����������� ��� �������� ����������� �� e-mail ��� ������ ����������. ����� �� ������ ������� ����� ��������� ������������, �� ���� ��� ��� ���� ������������ � �������, � ��� �� ������� ��� ��������� ��� ���������� � ������ ������.<br /><br />� ����� ������� �������� ������� �� ������ �������� ���� ������������ ����������. �� ������ ������ ��� ���, ���� ��������, ��������������, � ��� �� �������� ������ ��� �����.', '��� ���������� ���������� ���������� ������������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (9, '�������� �������� ��� � ����� ���������', '�� ������ ������ ����������� ������ ����������� �������� ��� (��� ����, ������� ����� ������), ����� �� ������ ��������� �����, ����������� � ������ ����� ������� �������� ������. ��� ��, �� ������ ���������� ���� �������� ������ �� ����, ��� ������ �������� ���, ����������� � ������� ���������� ����.
<br>
<br>� ������� ����� ������ �� ������ ����� ������ ������ ����������, ������� �� ������� �� ������ ������� ������ ��� � ������ �������� � ������� ������ ���������� ��������� ������.', '��� ����������� ������ ����������� �������� ��� � ����� ���������, ����������� � ������� ������ ���������� ��������� ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (10, '����� ��� � ���������', '������� ������, ������������� ��� �������� ������ ��� � ���������, �� ��������� ���� �������� ������.
<br>
<br>������� ��� ��������� ���� ������, ������� ����� � ����������� �����. �� ������ ������������� ����� ����, ��� ������ ������ ������������ ����� � �������� �����.
<br>
<br><b>������� �����</b>
<br>
<br>����� �� ������ ������ �������� ����� ��� ������ � ������� �����(�), � ������� ������ ����� ���-�� �� ����� �����, (��� ������ ���������� ������� ������������, ���������� ������� �� �� ���������, ��������� ������ �Ctrl�, � ������������ Mac, ������ ���������� ������ �Shift�/�Apple�), ����� ������� ������� ���������� ���������� � ������.
<br>
<br><b>����������� �����</b>
<br>
<br>�� �������� ������������ ������, � ��� �������� ����������� ������������� �������������� ��������� ������, ����������� ���������� ������� ������. ������������� � ������ �� ��������� �����, �� ������� ���������� ����� �� ����� ������������, � ����� �������� ������������� ����� ����� ����������. ����� ��������� �������� ������, ����������� ������ ��������� ���, � ����� ������� ������� ���������� ����������. ����� ������� ��� ���� ����������� ����������� ������ - � ���� ����������� ������� ������ ��������� � � ���� ������ �� ����. ��� �����������, �� ������ ������� �������, �� �������� ������.
<br>
<br>������������� ����� �������� ����������� �����, ��������� ��� ��������� �����-���������, ������� �� ������ ������ �������� ��� ���������� ������.
<br>
<br>�� ������ ������� ����� ��������� �����, ��������� ��� ��������� ������� ������, ����������� � ������ ����� ������� �������, ����� ������� �� ������� ���������� ������� ����� � ���������� ������� ������.', '��� ������������ �������� ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (11, '����������� � ����� �� ������', '���� �� ������� �� ���������� ���� ������ �� ����� ����������� � cookies ��� �� ����� �� ����� � ������� ����������, �� �� ������ ���������������� �� ������ ��� ������� � ������ ������� � �������� ��������� ��� ����� ������.<br /><br />��� ����� �� ����� �� ������ ��������� cookies, ������� ������������ ��� ������������� ��� ��������� ���������. �� ����������� ��� ����� ��� ��������� � ������ ���������� � ����� ������������.<br /><br />�� ������ ����� ������� ������� �������, �������� ������� � ���� ������������. ����� ���� ��� �� ����� ������������ � ������ ���� � �������, ���� ������������� ������ ������� ��� �����������.<br /><br />����� �� ������ ������������ ������� �������� �� ������ ������. ���� ����� �� ����������, ��� ����� �� ��������, ��������, �� ������ ������ ������� ���� cookies. ������� ������ ������ ��� �Cookies� ��� �������������� ����������.', '��� ��������������, ����� �� ������, � ����� ��� ������ ���� ��� �� ������ �������������, ����������� �� ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (13, '��������', '� ������� ����� ������ ��������� ������ ���������. ���� ���������� ��������� ������ ������� �� ������ ������ ������ ������.<br /><br />�������� �������� �� ������ ������:<ul><li>���������� � ��� ������ ���������� ���������� � ����� ���������� � ������ ������������� �� ���������� ���������, ���������� ������ �� ���� ��������� ���������. ��� �� �� ������ ������� ������ ������������� ������.</li></ul><ul><li>����� ��������� � ���������� ������ �� ����� ���������, ����������� �� ������ ����� ������ ���������� ��������� � ��� ����� ��� ���� ��������.</li></ul><ul><li>����� � ��������� ��� ������, �� ������ ������ ������� ����� �� ������. � ���� ����� ��������� ��������� ������, ����� �� ������� �� ������ ���������� ��������� ������ ����������.</li></ul><ul><li>������ ��������� � ����� �� ������ ������� ��������� 5 ������ ���������, � ����� ��� ���� ��������.</li></ul>�������� ��� �� ������������ ����� �������� ������ ���������. ���� �� ������� ����� ���������� � ����� ������ ����������, �� ���� ��������� �������� �� ����� ������.', '������ ���������� �� ������������� ���� ���������, �� ������� �������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (12, '���������', '�� ������ �������� ��� ������ ����� �������� ���������. ������ �������� ����, ����������� � ������ ������� ������ � �� �������� ���, ������������� ��� �������� ����� ����. ������ ��������� ����� (���� ������������� �������� ���) ����������� ������� ����� ����� �� �����, ��� �� �������� ��� ��������� ��� � �������. ��� ��������� ����, �� ������� ������ ����������, ������� ������������� ��� ���������� ������ ��������� � ������ ����.<br /><br /><b>�������� ����� ���� � ����� � ����</b><br /><br />��� ��������� ���������, ���, ��������, ������������ ���� ������. ������ ����, ������������� ��� ���������� �������������� ������� ����� ���������. ��� ��������� ����� ��������� ���������� �� ���� ������� ������ �BB-���� ������ ��� ������ ���������, ����� ������� ���� � �������.<br /><br />�Rich Text� �������� (RTE) � ��� WYSIWYG (What You See Is What You Get) ��������, ������� �� WordPad. ������ ���������� BB-����� �� �������, ��� ��������� ����� ��������� � �����, ��� � ����� ���������. �� ������ ������������ ������������ ����� ���������, ���� � ��� ���� ������� Internet Explorer 6 � ���� ��� Firefox 1.5 � ����.<br /><br /><b>������ ������������<br /><br /></b>����� �� �����, ������ ����:<ul><li>������� ��������������: ��� ������ ��� �������������� �� ���������.</li><li>����������� ��������: ���� ���� ��������� ��� �������� ����������� ���, �������� ������ �� ���� ��� ���������, � ������ �������������� BB-����, ������� �������� �������������.</li><li>�����: ����� �� ������ ������� ����� ������ ������. ���� �� ����������� �Rich Text� ��������, �� ��������� ������ ����� ���������� � ����� ���������.</li><li>������: ����� �� ������ ������� ���� �� ���� ������ �������� ������ ������.</li><li>������ � ������: ��� ��� ��������� ������� ��� �������� ���� ��������� ��������.</li><li>������ ���� �����: ����� �� ������ ��������� ��� ��������� ������ ���� ��� ����� ���������.</li><li>������������� ����� ����������� � �Rich Text� ����������: ��� ������ ��������� ������ ������������� ����� ����������� � �Rich Text� ����������. �� ��� �� ������ �������� ��� � ���������� ������, � ������ �������.</li><li>������ �����: ����������� ������������� ������������ ����� � ������, ������ � ������������.</li><li>������ ������: ��� ������ ��������� ������� �����������, ����������� � ����������� ������ ������.</li><li>���� ������: ���� ���� ���������� ������� ������ ��� ������.</li><li>��������: ������ ���� � ������� �������� ������� ������ ���� ��������� ������ � �������� ��� �������� ������ � ���������.</li><li>������: ��� ������ �������� ��� ��������� ��� ��������� ������.</li><li>������������ ������: ��� ������ �������� ��� ��������� ��������� ����� �� ������ ����, �� ������� ����, �� ������ ��� �� ������.</li><li>������: ��� ��� ������ ��������� ������� ������������ ��� ������������� ������.</li></ul><b>������� ������<br /><br /></b>����� �� ������ �������� �������, �� ������ �������� ��� ������ ������ �� ���������� � ��������������� BB-������. ������� ������ �����, �� ������������ ���� ��� ����� ���������.<br /><br /><b>����� ���������<br /><br /></b>�� ������ �������� ��� ����� ��� �������� ���� ��� ������ � ����. ���� ������� ��������� ������?� �� �������, �� �������� ��������� � ��������� ��������� �� �����. ���� �� ������� ��������� �������?� �� �������, �� ���� ������� �� ����� ��������� � ����� ���������. ������� ��������� ����������� �� e-mail ��� �������?� ��������� ��� �������� ����������� �� e-mail ��� ������� � ��� ����. ��� �� ���� ����� ������������� HTML � ���������, ���� ������������� ������ ��������� ���.<br /><br />�� ��� �� ������ ������� ������ � ����� ���� ��� ���������. ���� �� �������� ����, �� ��� ������ �������� ����� � ���������� ����. ���� �� ��������� � ����, �� ��� ������ �������� ����� � ����� ��������� ���������.<br /><br /><b>����� ������</b><br /><br />��� �������� ����� ����, ��� ������ ��� ����� �������� � �������� ����, �� ������� ������ �������� ���� ��� ���������� ������� � ���� ����. ����� �� ��� ������, ��� ��������� ����� ������. ������ ���� ������ ��� ����� � ��� ������� ������ �����������. ����� �������� ������ � ������ ������, ����� �������� �� ��������� ��� ������ � �����. ���������� ��������� ������� � ����� ���� � �������������� ��������� ������� ��������������� ��������������� ������.<br /><br /><b>����������� ���������</b><br /><br />��� ������ ���������� � ���� ������� ������ ����������. ��� ������� �� ��� ������, �� ������� �������� � ����, � ������������ ���� ���������, ��� ������� ������ ������ ������.<br /><br />��� �� ��� ������ ���������� ���� ������ �������, ������� ��������� �������� ������� � ����� ��������� ���������. ��� ����� ����� ������� ��������� ��������� ��� �����������. ����� ����� ��� ������ �������� ������ ����������, �� �������� � ����� ������ �� ������������ ����������� � ���� �����, ������� �� ��������. ��� ��������, ��� ��������� ������ ������� �������������� ��� ������ ���.<br /><br /><b>�������������� ���������</b><br /><br />��� ������ ����������, ���������� ����, �� ������� ������ ����������. ��� ������� �� ��� ������, ��� ����� ���������� ������� ���� �� �������� ��������������: ������� �������������� ��� �������� ��������������. �������� �������������� ��������� ��� ��������������� ��������� ��� ������������ ��������. �� ������� ��������������� ����� ������ ��������� ���������� �� ����, ����� �� ��� ��������� ������.<br /><br />��� �������������� ������ ��������� �� ������ ������� ����� ��������� ������� ����������������...� � ��� ���������?�. ��� ��������� ������� � ���� �����, ��� ����� ����������������� ���������� �������� �������, ������������� � ���, ��� �� ������������� ��� ���������, � ����� ��������������. ���� �� �� ������ ��� �����, ������, ������ ���������� � �������������� ����� ��������� �������������.<br /><br />���� �� �� ������ ������ ��� �������������� ������ ���������, ��, ��������, ��� ������������� ������ �������� ������ �������, ���� ��������� ����� �������, �� ��������� ��������, �������������� ��������� ��� ����������.<br /><br /><b>������� �����</b><br /><br />���� ��� ����������� ��������, �� � ������ ���� ����� ������ &laquo;������� �����&raquo;. ������� �� ���, � ����� �������� ��������� ��������� ����� �� �������� � �����. �� ������ ������� ����� �������� ������ ��������� �������������, ��� ����� �������� ��������������� ����� � ���������� ������ � ����� ������ ������. ����� �� ������ &laquo;����������� �����&raquo; ��� ����� �������� ������� ����� ������ � ����.', '����������� �� ��������, ������������ ��� �������� ����, ������ � ������ � ����.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (14, '������ �������������, ��������� ����� ������ �������������, ����������� � ������� ����� ������ �������� ������, � ��� ������ ������ ���� �������������, ������������������ �� ������. <br /><br /><b>�������� �����</b><br />���� �� ������ ����� ������������ �� �����, �� ������ ������ ��� ��� � ���� ������. ����� ����������� � ��������� ������ �������������, ��� ��� ���������� � ��������� ��������. ����� ��������� ����� ������ ����� ���������� ��������� �������.<br /><br />���� �� ������ ����� ������������� ������ � ����������� � �������, �� ��������� ������� ����� � ��������� ������������� � �����������. �� ������ ��� �� ������� ����� �� ������� �������������� ������������� � �� ��������.<br /><br /><b>����������� �����</b><br />����� �� ������ ��������������� ���������...� �� ������ ������ ������ ����� ��� ������ �������������.<br /><br />�������� �������� ��������: ����� ����� ������ ����� ��� ������ �� ������� �������� ������� �������������.<br /><br />������� ��������: ��������� ������ ����� �����, ������������� � ������� ������������.<br /><br />��������: � ���� ������ �� ������ ������ ����� �� ������������ ������ � ��������� �������� �� ������ ����������� �����������.<br /><br />���������� �� ���������� � �����: ��� ������ ��������� ������ ����� �� ����������� ����������. ��������, ����� ������������� ��������������������, ���������� ���������, ���������� ����� �� ��� ����� ��������� ����.<br /><br /><b>����������</b><br />���� ��������� ������ ������ ���������� ����������. �� ������ ������� ������ (��� ���� �������������) � ������ ����, ����� ����������� �� �����, ����������, ���� ����������� ��� �������� (���� ��������), � ��� �� � ������������ ��� ��������� ����. � ��������� ���� �� ������ ������ ����� ��������� ������������� ��������� �� ������ ��������.<br /><br /><b>����������� �������������</b><br />����� ���������� �������� ��������� �������������. ��� ���������� ����������� ���������� � ������������, ������� ��� ����������, ���� ����. ����� �� ���������� ������������ ��� ���, �� ������ ������� �������� ������� �������, �������� ��� � ������ ��� ����� ��� ���������.', '��������� ������� ���������� � ������ ������������� �� ������.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (15, '����� ����', '� ������� ����� ������ ���� ������� ������ ������. ��� ������� �� ��� ������ ���������� ���� � ������� ����. <br /><br />�� ����� ���� �� ������ ������������ ��������� �����: <ul><li>����������� �� ���� � ��� ����� ������ ��� ��������� ����������� �� e-mail, �� ���������� ����. ��� ����� ��������� ���������� �� ���� �����, ������� ������ ������ ������������ �� e-mail � ����� �����������.</li></ul><ul><li>�������� ����� � ������ ����� �������� ��� ��������� ������ �� ���� ������ ����� �� e-mail �����.</li><li>������ ��� ������ � ��� ���������� ���� � ��������� �������� ��������, ����� ����������� � ��������� ����������� ������������.</li><li>������� ���� � ���� ����������� ������� ����� ���� � ��� �� ���������. ������ ��� ������ ��������� ���������� ���� � ������� ���� ��� ������. HTML ������ ��������� ������� ����� ���� �� ��� ������� ����, � ��� ����� �������� ��� ��������� � �������� ��� ��������� ������. ������ � ������� Microsoft Word ��������� ������� ���� � ���� �� ��� ������� ���� � ������� ��� � ���������� ��������� Microsoft Word ��� ���������.</li></ul><ul><li>�������� �� ���� ����� � ��� ����� ������ ��� ��������� ����������� �� e-mail, � ����� ����� �����, ��������� � ���� ������. ��� ����� ��������� ���������� �� ���� �����, ������� ������ ������ ������������ �� e-mail � ����� �����������.</li></ul><div align=left>�� ����� ������ ������� ���� �� ���� ������� ����������� ����:<br /><br /><div align='left'><ul><li>����������� � ������� ��������� �� ������� ������� � ���� � ����������� ����.</li></ul><ul><li>����������� � ��������� ���� � ����������� ���� ������ ��������� ��� ������, ������������� ���������������.</li></ul><ul><li>�������� � ��� � �����������, �� � ������ ����������� ����������� ������� ��������� �� ����� �������� ��� ����������� �� � ������.</li></ul></div></div>', '����������� �� ��������� ������, ��� ��������� ���.');";
		$SQL[] = "REPLACE INTO ".SQL_PREFIX."faq VALUES (16, '���������', '�� ������ ���� ���� ���������, ������ �� ������� ��������� � ������� ����� ������.<br><br>�� ������ ��������� ������������ ������� � ���������, ������� ����� ����� ������ ���. ��� ���������� ������ �������, ����������� ������ ��������� ����� �������. ���������� ����������� �������� ������� ��� �����:<ul><li>����������� ������� � ��� ���������� ����� ����, ����������� ������ ����, ������ � �� ���� �������.</li><br><li>������������ ������� � ��� ��� �������, ��������������� �� ��������� ����. ��� ���������� ����� ����, ��� ����� �������� �������� ���� ����� ��� ������� �� ��������� ���� ����, �� ������ ������� ��������� ������. ����� ����� �������� ����, ��� ����� ����� ������� ��������� ���� � ��������� �����.</li><br><li>������������� ������� � ����������� �������, ��������� �������� �� ������ ������� � ��������� ����������� � ���������, ������ ������, �����, ���.</li><br></ul>���� ������������� ������ �������� ��� ��������� ��������� ������� � ���������, �� ������� ��������� ���� ����� ����� �� ������ ���, �� � ���� ���������.<br><br>�����, ���� �� ������� ���� ������ �������� � ����� ������������ ����������, �� � ���� ������ �������� �� ������� �������� ������ � ��������� �������� ������ �� ��� �������, ��� �������, ��� ������������� ������ ������� ��� �����.', '���������� �� ������������� ������� ��������� ������.');";


		$this->install->error   = array();
		$this->sqlcount = 0;

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			$this->install->ipsclass->DB->query( $query );

			if ( $this->install->ipsclass->DB->error )
			{
				$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}

		$this->install->message = "������� ���������, ����� ����������� ������ ������..<br /><br />$this->sqlcount �������� ���������....";
		$this->install->saved_data['workact'] = 'step_19';
	}


	/*-------------------------------------------------------------------------*/
	// STEP 19: DROPPING TABLES
	/*-------------------------------------------------------------------------*/

	function step_19()
	{
		$SQL[] = "DROP TABLE ".SQL_PREFIX."tmpl_names;";
		$SQL[] = "DROP TABLE ".SQL_PREFIX."forums_bak;";
		$SQL[] = "DROP TABLE ".SQL_PREFIX."categories;";
		$SQL[] = "DROP TABLE ".SQL_PREFIX."messages;";

		$this->install->error   = array();
		$this->sqlcount = 0;

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			$this->install->ipsclass->DB->query( $query );

			if ( $this->install->ipsclass->DB->error )
			{
				$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
			}
			else
			{
				$this->sqlcount++;
			}
		}

		$this->install->message = "������ ������� ����������, ����� �����������...<br /><br />$this->sqlcount �������� ���������....";
		$this->install->saved_data['workact'] = 'step_20';
	}

	/*-------------------------------------------------------------------------*/
	// STEP 20: OPTIMIZATION
	/*-------------------------------------------------------------------------*/

	function step_20()
	{
		$SQL[] = "alter table ".SQL_PREFIX."tracker change topic_id topic_id int(10) NOT NULL default '0';";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."tracker ADD INDEX(topic_id);";

		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics CHANGE pinned pinned TINYINT( 1 ) DEFAULT '0' NOT NULL;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics CHANGE approved approved TINYINT( 1 ) DEFAULT '0' NOT NULL;";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."topics ADD INDEX(topic_firstpost);";

		$SQL[] = "alter table ".SQL_PREFIX."calendar_events change eventid eventid mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."calendar_events change userid userid mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."contacts change id id mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."contacts change contact_id contact_id mediumint(8) NOT NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."contacts change member_id member_id mediumint(8) NOT NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."faq change id id mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."forum_tracker change frid frid mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."forum_tracker change forum_id forum_id smallint(5) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."forums change last_poster_id last_poster_id mediumint(8) NOT NULL DEFAULT '0';";
		$SQL[] = "alter table ".SQL_PREFIX."languages change lid lid mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."member_extra change id id mediumint(8) NOT NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."members change id id mediumint(8) NOT NULL;";
		$SQL[] = "alter table ".SQL_PREFIX."members change mgroup mgroup smallint(3) NOT NULL;";


		$SQL[] = "alter table ".SQL_PREFIX."moderator_logs change id id int(10) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."moderator_logs change topic_id topic_id int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."moderator_logs change post_id post_id int(10);";
		$SQL[] = "alter table ".SQL_PREFIX."moderator_logs change member_id member_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."moderator_logs change ip_address ip_address VARCHAR(16) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."moderators change mid mid mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."moderators change member_id member_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."pfields_content change member_id member_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."polls change pid pid mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."polls change tid tid int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."polls change starter_id starter_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."polls change votes votes smallint(5) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."polls change forum_id forum_id smallint(5) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts change pid pid int(10) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."posts change author_id author_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts change topic_id topic_id int(10) NOT NULL default '0';";

		$SQL[] = "alter table ".SQL_PREFIX."posts change ip_address ip_address varchar(16) NOT NULL DEFAULT '';";
		$SQL[] = "alter table ".SQL_PREFIX."posts change use_sig use_sig tinyint(1) NOT NULL DEFAULT '0';";
		$SQL[] = "alter table ".SQL_PREFIX."posts change use_emo use_emo tinyint(1) NOT NULL DEFAULT '0';";
		$SQL[] = "alter table ".SQL_PREFIX."sessions change member_id member_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."sessions change in_forum in_forum smallint(5) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."stats change TOTAL_REPLIES TOTAL_REPLIES int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."stats change TOTAL_TOPICS TOTAL_TOPICS int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."stats change LAST_MEM_ID LAST_MEM_ID mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."stats change MEM_COUNT MEM_COUNT mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topics change tid tid int(10) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."topics change starter_id starter_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."topics change last_poster_id last_poster_id mediumint(8) NOT NULL DEFAULT '0';";
		$SQL[] = "alter table ".SQL_PREFIX."tracker change trid trid mediumint(8) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."tracker change member_id member_id mediumint(8) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."voters change vid vid int(10) NOT NULL auto_increment;";
		$SQL[] = "alter table ".SQL_PREFIX."voters change tid tid int(10) NOT NULL default '0';";
		$SQL[] = "alter table ".SQL_PREFIX."voters change forum_id forum_id smallint(5) NOT NULL default '0';";

		$SQL[] = "UPDATE ".SQL_PREFIX."members SET language=''";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "�������� �����������...<br /><br />$this->sqlcount �������� ���������....";
		$this->install->saved_data['workact'] = 'step_21';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 21: OPTIMIZATION II
	/*-------------------------------------------------------------------------*/

	function step_21()
	{
		$SQL[] = "alter table ".SQL_PREFIX."posts drop index topic_id;";
		$SQL[] = "alter table ".SQL_PREFIX."posts drop index author_id;";
		$SQL[] = "alter table ".SQL_PREFIX."posts add index topic_id (topic_id, queued, pid);";
		$SQL[] = "alter table ".SQL_PREFIX."posts add index author_id( author_id, topic_id);";
		$SQL[] = "ALTER TABLE ".SQL_PREFIX."posts DROP INDEX forum_id, ADD INDEX(post_date);";

		$SQL[] = "update ".SQL_PREFIX."members SET restrict_post=0";

		$this->install->error   = array();
		$this->sqlcount 		= 0;
		$output					= "";

		$this->install->ipsclass->DB->return_die = 1;

		foreach( $SQL as $query )
		{
			$this->install->ipsclass->DB->allow_sub_select 	= 1;
			$this->install->ipsclass->DB->error				= '';

			if( $this->install->saved_data['man'] )
			{
				$output .= preg_replace("/\sibf_(\S+?)([\s\.,]|$)/", " ".$this->install->ipsclass->DB->obj['sql_tbl_prefix']."\\1\\2", preg_replace( "/\s{1,}/", " ", $query ) )."\n\n";
			}
			else
			{
				$this->install->ipsclass->DB->query( $query );

				if ( $this->install->ipsclass->DB->error )
				{
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
				else
				{
					$this->sqlcount++;
				}
			}
		}

		$this->install->message = "����������� ���������, ����� �������������� ����� ������...<br /><br />$this->sqlcount �������� ���������....";
		$this->install->saved_data['workact'] = 'step_22';

		if( $this->install->saved_data['man'] AND $output )
		{
			$this->install->message .= "<br /><br /><h3><b>��� ����������� ���������� ���������� ��������� ��������� MySQL �������:</b></h3><br /><div class='eula'>".nl2br(htmlspecialchars($output))."</div>";
			$this->install->do_man	 = 1;
		}
	}


	/*-------------------------------------------------------------------------*/
	// STEP 22: IMPORT SKINS & SETTINGS
	/*-------------------------------------------------------------------------*/

	function step_22()
	{
		//-----------------------------------------
		// Get old skins data
		//-----------------------------------------

		$this->install->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'skins' ) );
		$outer = $this->install->ipsclass->DB->simple_exec();

		while( $r = $this->install->ipsclass->DB->fetch_row( $outer ) )
		{
			//-----------------------------------------
			// Get CSS
			//-----------------------------------------

			$css = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'css', 'where' => 'cssid='.$r['css_id'] ) );

			//-----------------------------------------
			// Get Wrapper
			//-----------------------------------------

			$wrapper = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => '*', 'from' => 'templates', 'where' => 'tmid='.$r['tmpl_id'] ) );

			//-----------------------------------------
			// Insert...
			//-----------------------------------------

			$this->install->ipsclass->DB->do_insert( 'skin_sets', array(
												'set_name'            => $r['sname'],
												'set_image_dir'       => $r['img_dir'],
												'set_hidden'          => 1,
												'set_default'         => 0,
												'set_css_method'      => 0,
												'set_skin_set_parent' => 3,
												'set_author_email'    => '',
												'set_author_name'     => 'IPB 2.0 Import',
												'set_author_url'      => '',
												'set_css'             => stripslashes($css['css_text']),
												'set_wrapper'         => stripslashes($wrapper['template']),
												'set_emoticon_folder' => 'default',
						 )                    );

			$new_id = $this->install->ipsclass->DB->get_insert_id();

			//-----------------------------------------
			// Update templates
			//-----------------------------------------

			$this->install->ipsclass->DB->do_update( 'skin_templates', array( 'set_id' => $new_id ), 'set_id='.$r['set_id'] );

			//-----------------------------------------
			// Update macros
			//-----------------------------------------

			$this->install->ipsclass->DB->do_update( 'skin_macro', array( 'macro_set' => $new_id ), 'macro_set='.$r['set_id'] );
		}

		//-----------------------------------
		// Get XML
		//-----------------------------------

		$xml = new class_xml();
		$xml->lite_parser = 1;

		//-----------------------------------
		// Get XML file (TEMPLATES)
		//-----------------------------------

		$xmlfile = ROOT_PATH.'resources/ipb_templates.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-------------------------------
		// Unpack the datafile (TEMPLATES)
		//-------------------------------

		$xml->xml_parse_document( $setting_content );

		//-------------------------------
		// (TEMPLATES)
		//-------------------------------

		if ( ! is_array( $xml->xml_array['templateexport']['templategroup']['template'] ) )
		{
			$this->install->error[] = "������ � ipb_templates.xml &mdash; ���������� ��������� ���� ���� ���������";
		}
		else
		{
			foreach( $xml->xml_array['templateexport']['templategroup']['template'] as $entry )
			{
				$this->install->ipsclass->DB->allow_sub_select 	= 1;
				$this->install->ipsclass->DB->error				= '';

				$newrow = array();

				$newrow['group_name']      = $entry[ 'group_name' ]['VALUE'];
				$newrow['section_content'] = $entry[ 'section_content' ]['VALUE'];
				$newrow['func_name']       = $entry[ 'func_name' ]['VALUE'];
				$newrow['func_data']       = $entry[ 'func_data' ]['VALUE'];
				$newrow['set_id']          = 1;
				$newrow['updated']         = time();

				$this->install->ipsclass->DB->do_insert( "skin_templates", $newrow );

				if ( $this->install->ipsclass->DB->failed )
				{
					$query = array_pop( $this->install->ipsclass->DB->obj['cached_queries'] );
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
			}
		}

		//-------------------------------
		// GET MACRO
		//-------------------------------

		$xmlfile = ROOT_PATH.'resources/macro.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-------------------------------
		// Unpack the datafile (MACRO)
		//-------------------------------

		$xml->xml_parse_document( $setting_content );

		//-------------------------------
		// (MACRO)
		//-------------------------------

		if ( ! is_array( $xml->xml_array['macroexport']['macrogroup']['macro'] ) )
		{
			$this->install->error[] = "������ � macro.xml &mdash; ���������� ��������� ���� ���� ���������";
		}
		else
		{
			foreach( $xml->xml_array['macroexport']['macrogroup']['macro'] as $entry )
			{
				$this->install->ipsclass->DB->allow_sub_select 	= 1;
				$this->install->ipsclass->DB->error				= '';

				$newrow = array();

				$newrow['macro_value']   = $entry[ 'macro_value' ]['VALUE'];
				$newrow['macro_replace'] = $entry[ 'macro_replace' ]['VALUE'];
				$newrow['macro_set']     = 1;

				$this->install->ipsclass->DB->do_insert( "skin_macro", $newrow );

				if ( $this->install->ipsclass->DB->failed )
				{
					$query = array_pop( $this->install->ipsclass->DB->obj['cached_queries'] );
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
			}
		}

		//-------------------------------
		// WRAPPER / CSS
		//-------------------------------

		$xmlfile = ROOT_PATH.'resources/skinsets.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-------------------------------
		// Unpack the datafile (WRAPPER/CSS)
		//-------------------------------

		$xml->xml_parse_document( $setting_content );

		//-------------------------------
		// (WRAPPER/CSS)
		//-------------------------------

		if ( ! is_array( $xml->xml_array['export']['group']['row'] ) )
		{
			$this->install->error[] = "������ � skinsets.xml &mdash; ���������� ��������� ���� ���� ���������";
		}
		else
		{
			foreach( $xml->xml_array['export']['group']['row'] as $entry )
			{
				$this->install->ipsclass->DB->allow_sub_select 	= 1;
				$this->install->ipsclass->DB->error				= '';

				if( $entry['set_skin_set_id'] <> 1 )
				{
					continue;
				}

				$newrow = array();

				$newrow['set_css']   = $entry[ 'set_css' ]['VALUE'];
				$newrow['set_werapper'] = $entry[ 'set_wrapper' ]['VALUE'];

				$this->install->ipsclass->DB->do_update( "skin_sets", $wrapper_record, "set_skin_set_id=1" );

				if ( $this->install->ipsclass->DB->failed )
				{
					$query = array_pop( $this->install->ipsclass->DB->obj['cached_queries'] );
					$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
				}
			}
		}

		//-----------------------------------------
		// Cleanup XML
		//-----------------------------------------

		unset($xml);

		$this->install->message = "����� ������� �������������, ������� ��������...";
		$this->install->saved_data['workact'] = 'step_23';
	}


	/*-------------------------------------------------------------------------*/
	// STEP 23: IMPORT SETTINGS
	/*-------------------------------------------------------------------------*/

	function step_23()
	{
		global $INFO;

		//-----------------------------------
		// Get XML
		//-----------------------------------

		$xml = new class_xml();
		$xml->lite_parser = 1;

		//-----------------------------------
		// Get XML file
		//-----------------------------------

		$xmlfile = ROOT_PATH.'resources/settings.xml';

		$setting_content = implode( "", file($xmlfile) );

		//-------------------------------
		// Unpack the datafile
		//-------------------------------

		$xml->xml_parse_document( $setting_content );

		//-------------------------------
		// pArse
		//-------------------------------

		$fields = array( 'conf_title', 'conf_description', 'conf_group', 'conf_type', 'conf_key', 'conf_value', 'conf_default',
						 'conf_extra', 'conf_evalphp', 'conf_protected', 'conf_position', 'conf_start_group', 'conf_end_group', 'conf_help_key', 'conf_add_cache' );


		if ( ! is_array( $xml->xml_array['settingexport']['settinggroup']['setting'] ) )
		{
			$this->install->error[] = "������ � settings.xml &mdash; ���������� ��������� ���� ���� ���������";
		}

		foreach( $xml->xml_array['settingexport']['settinggroup']['setting'] as $entry )
		{
			if ( ! $entry['conf_key']['VALUE'] )
			{
				continue;
			}

			$newrow = array();

			$entry['conf_value']['VALUE'] = "";

			if ( $INFO[ $entry['conf_key']['VALUE'] ] != "" and $INFO[ $entry['conf_key']['VALUE'] ] != $entry['conf_default']['VALUE'] )
			{
				$entry['conf_value']['VALUE'] = $INFO[ $entry['conf_key']['VALUE'] ];
			}

			//-----------------------------------
			// Special considerations?
			//-----------------------------------

			if ( $entry['conf_key']['VALUE'] == 'img_ext' )
			{
				$entry['conf_value']['VALUE'] = str_replace( '|', ',', $entry['conf_value']['VALUE'] );
			}
			else if ( $entry['conf_key']['VALUE'] == 'photo_ext' )
			{
				$entry['conf_value']['VALUE'] = str_replace( '|', ',', $entry['conf_value']['VALUE'] );
			}
			else if ( $entry['conf_key']['VALUE'] == 'avatar_ext' )
			{
				$entry['conf_value']['VALUE'] = str_replace( '|', ',', $entry['conf_value']['VALUE'] );
			}

			//-----------------------------------
			// Make PHP slashes safe
			//-----------------------------------

			$entry['conf_evalphp']['VALUE'] = str_replace( '\\', '\\\\', $entry['conf_evalphp']['VALUE'] );

			foreach( $fields as $f )
			{
				$newrow[$f] = $entry[ $f ]['VALUE'];
			}

			$this->install->ipsclass->DB->do_insert( "conf_settings", $newrow );

			if ( $this->install->ipsclass->DB->failed )
			{
				$query = array_pop( $this->install->ipsclass->DB->obj['cached_queries'] );
				$this->install->error[] = $query."<br /><br />".$this->install->ipsclass->DB->error;
			}
		}

		unset($xml);

		$this->install->message = "��������� ���������, ����� ���������� �����...";
		$this->install->saved_data['workact'] = 'step_24';
	}

	/*-------------------------------------------------------------------------*/
	// STEP 24: RECACHE & REBUILD
	/*-------------------------------------------------------------------------*/

	function step_24()
	{
		//-----------------------------------
		// Get ACP library
		//-----------------------------------

		require_once( ROOT_PATH.'sources/lib/admin_cache_functions.php' );
		$acp           =  new admin_cache_functions();
		$acp->ipsclass =& $this->install->ipsclass;

		//-----------------------------------
		// Cache skins and shit
		//-----------------------------------

		$acp->_rebuild_all_caches( array(2,3) );

		//-------------------------------------------------------------
		// Forum cache
		//-------------------------------------------------------------

		$this->install->ipsclass->update_forum_cache();

		//-------------------------------------------------------------
		// Group Cache
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['group_cache'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => "*",
									  'from'   => 'groups'
							 )      );

		$this->install->ipsclass->DB->simple_exec();

		while ( $i = $this->install->ipsclass->DB->fetch_row() )
		{
			$this->install->ipsclass->cache['group_cache'][ $i['g_id'] ] = $i;
		}

		$this->install->ipsclass->update_cache( array( 'name' => 'group_cache', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Systemvars
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['systemvars'] = array();

		$result = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => 'count(*) as cnt', 'from' => 'mail_queue' ) );

		$this->install->ipsclass->cache['systemvars']['mail_queue'] = intval( $result['cnt'] );
		$this->install->ipsclass->cache['systemvars']['task_next_run'] = time() + 3600;

		$this->install->ipsclass->update_cache( array( 'name' => 'systemvars', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Stats
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['stats'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'count(pid) as posts', 'from' => 'posts', 'where' => "queued <> 1" ) );
		$this->install->ipsclass->DB->simple_exec();

		$r = $this->install->ipsclass->DB->fetch_row();
		$stats['total_replies'] = intval($r['posts']);

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'count(tid) as topics', 'from' => 'topics', 'where' => "approved = 1" ) );
		$this->install->ipsclass->DB->simple_exec();

		$r = $this->install->ipsclass->DB->fetch_row();
		$stats['total_topics']   = intval($r['topics']);
		$stats['total_replies'] -= $stats['total_topics'];

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'count(id) as members', 'from' => 'members', 'where' => "mgroup <> '".$this->install->ipsclass->vars['auth_group']."'" ) );
		$this->install->ipsclass->DB->simple_exec();

		$r = $this->install->ipsclass->DB->fetch_row();
		$stats['mem_count'] = intval($r['members']);

		$this->install->ipsclass->cache['stats']['total_replies'] = $stats['total_replies'];
		$this->install->ipsclass->cache['stats']['total_topics']  = $stats['total_topics'];
		$this->install->ipsclass->cache['stats']['mem_count']     = $stats['mem_count'];

		$r = $this->install->ipsclass->DB->simple_exec_query( array( 'select' => 'id, name',
											'from'   => 'members',
											'order'  => 'id DESC',
											'limit'  => '0,1'
								   )      );

		$this->install->ipsclass->cache['stats']['last_mem_name'] = $r['name'];
		$this->install->ipsclass->cache['stats']['last_mem_id']   = $r['id'];

		$this->install->ipsclass->update_cache( array( 'name' => 'stats', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// Ranks
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['ranks'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'id, title, pips, posts',
									  'from'   => 'titles',
									  'order'  => "posts DESC",
							)      );

		$this->install->ipsclass->DB->simple_exec();

		while ($i = $this->install->ipsclass->DB->fetch_row())
		{
			$this->install->ipsclass->cache['ranks'][ $i['id'] ] = array(
														  'TITLE' => $i['title'],
														  'PIPS'  => $i['pips'],
														  'POSTS' => $i['posts'],
														);
		}

		$this->install->ipsclass->update_cache( array( 'name' => 'ranks', 'array' => 1, 'deletefirst' => 1 ) );


		//-------------------------------------------------------------
		// SETTINGS
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['settings'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => '*', 'from' => 'conf_settings', 'where' => 'conf_add_cache=1' ) );
		$info = $this->install->ipsclass->DB->simple_exec();

		while ( $r = $this->install->ipsclass->DB->fetch_row($info) )
		{
			$this->install->ipsclass->cache['settings'][ $r['conf_key'] ] = $r['conf_value'] != "" ? $r['conf_value'] : $r['conf_default'];
		}

		$this->install->ipsclass->update_cache( array( 'name' => 'settings', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// EMOTICONS
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['emoticons'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'typed,image,clickable,emo_set', 'from' => 'emoticons' ) );
		$this->install->ipsclass->DB->simple_exec();

		while ( $r = $this->install->ipsclass->DB->fetch_row() )
		{
			$this->install->ipsclass->cache['emoticons'][] = $r;
		}

		$this->install->ipsclass->update_cache( array( 'name' => 'emoticons', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// LANGUAGES
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['languages'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'ldir,lname', 'from' => 'languages' ) );
		$this->install->ipsclass->DB->simple_exec();

		while ( $r = $this->install->ipsclass->DB->fetch_row() )
		{
			$this->install->ipsclass->cache['languages'][] = $r;
		}

		$this->install->ipsclass->update_cache( array( 'name' => 'languages', 'array' => 1, 'deletefirst' => 1 ) );

		//-------------------------------------------------------------
		// ATTACHMENT TYPES
		//-------------------------------------------------------------

		$this->install->ipsclass->cache['attachtypes'] = array();

		$this->install->ipsclass->DB->simple_construct( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img', 'from' => 'attachments_type', 'where' => "atype_photo=1 OR atype_post=1" ) );
		$this->install->ipsclass->DB->simple_exec();

		while ( $r = $this->install->ipsclass->DB->fetch_row() )
		{
			$this->install->ipsclass->cache['attachtypes'][ $r['atype_extension'] ] = $r;
		}

		$this->install->ipsclass->update_cache( array( 'name' => 'attachtypes', 'array' => 1, 'deletefirst' => 1 ) );

		$this->install->message = "����� ���� �������...";
		unset($this->install->saved_data['workact']);
		unset($this->install->saved_data['st']);
		unset($this->install->saved_data['vid']);
	}


	//#------------------------------------------------------------------------
	// OTHER SQL WORK
	//#------------------------------------------------------------------------

	function sql_members($a, $b)
	{
		return "SELECT m.*, me.id as mextra FROM ibf_members m LEFT JOIN ibf_member_extra me ON ( me.id=m.id ) LIMIT $a, $b";
	}

	function sql_members_email( $a )
	{
		return "select id, name, email, count(email) as count from ibf_members group by email order by count desc LIMIT 0, $a";
	}

	function sql_members_email_update( $push_auth )
	{
		return "UPDATE ibf_members SET email=concat( id, '-', email ) where id IN(".implode(",", $push_auth).")";
	}

	function sql_members_converge( $start, $end )
	{
		return "SELECT m.*, c.converge_id as cid FROM ibf_members m LEFT JOIN ibf_members_converge c ON ( c.converge_id=m.id ) WHERE id >= $start and id < $end";
	}

}

?>