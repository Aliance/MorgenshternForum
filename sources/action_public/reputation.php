<?php

/*
+--------------------------------------------------------------------------
| IPB 2.1.x eXTended Reputation System
| based on Simple Reputation System by Daniil Khoroshko dan@iandi.ru
| www.reggae-vibes.ru
| www.diablozone.net
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
        print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
        exit();
}


class reputation
{
        var $ipsclass;
        var $type = "";
        var $mid = "";
        var $message = "";
        var $post = "";
        var $st = 0;
        var $cid = "";
        var $anonym = 0;

        var $output = "";


        function auto_run()
        {

                $this->ipsclass->load_language('lang_reputation');

                $this->type = $this->ipsclass->input['type'];
                $this->mid = intval($this->ipsclass->input['mid']);
                $this->cid = intval($this->ipsclass->input['cid']);
                $this->message = $this->ipsclass->input['message'];
                $this->post = intval($this->ipsclass->input['p']);
                $this->st = intval($this->ipsclass->input['st']);
                $this->order = $this->ipsclass->input['order'];
                $this->anonym = intval($this->ipsclass->input['anonym']);

                switch($this->type)
                {
                        case 'minus':
                              $this->check_permissions();
                              if($this->ipsclass->member['id'] != $mid)
                                 $this->change_reputation(-1);
                              break;
                        case 'add':
                              $this->check_permissions();
                              if($this->ipsclass->member['id'] != $mid)
                                 $this->change_reputation(1);
                              break;
                        case 'win_minus':
                              $this->check_permissions();
                              if($this->ipsclass->member['id'] != $mid)
                                 $this->change_reputation_window();
                              break;
                        case 'win_add':
                              $this->check_permissions();
                              if($this->ipsclass->member['id'] != $mid)
                                 $this->change_reputation_window();
                              break;
                        case 'delete':
                              if($this->cid)
                                 $this->delete_rep();
                        case 'history':
                              if($this->mid)
                                 $this->view_rep_history();
                              break;
                        case 'rating':
                              $this->view_rep_top();
                              break;
                }
        }

        function change_reputation_window()
        {
                //CSS Stuff
                if($this->ipsclass->skin['_usecsscache'] == 1)
                        $css = "<style type='text/css'>@import url(".$this->ipsclass->vars['board_url'].'/style_images/css_'.$this->ipsclass->skin['_csscacheid'].".css);</style>";
                else
                        $css = "<style type='text/css'>".$this->ipsclass->skin['_css']."</style>";

                $css = str_replace('<#IMG_DIR#>',$this->ipsclass->skin['_imagedir'],$css);

                $anonym = $this->ipsclass->vars['rep_anonym'] ? " style='padding: 0px;'><input type='checkbox' name='anonym' value='1'/>" : ">".$this->ipsclass->lang['error_12'];

                $member = $this->get_member_by_id($this->mid);

                $txt = "<html>
                         <head>
                          <title>".$this->ipsclass->lang['change_rep']." ".$member['members_display_name']."</title>
                           ".$css."
                           <meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />
                          </head>
                         <body>
                          <form action='index.php'>
                          <div align='center' class='borderwrap'>
                          <div class='maintitle'>".($this->type == 'win_add'?$this->ipsclass->lang['rep_inc']:$this->ipsclass->lang['rep_dec'])." ".$member['members_display_name']."</div>

                        <table class='ipbtable' cellspacing='1'>
                          <tr>
                                <td class='row2' align='left'><b>Пользователь</b></td>
                                <td class='row2' align='left'>".$member['members_display_name']."</td>
                          </tr>
                          <tr>
                                <td class='row2' align='left' valign='top'><b>Сообщение</b><br>(Причина)</td>
                                <td class='row2' align='left' style='padding: 0px;'><textarea name='message' style='width:400px;height:80px;'></textarea></td>
                          </tr>
                          <tr>
                                <td class='row2' align='left'><b>Анонимно</b></td>
                                <td class='row2' align='left'$anonym</td>
                          </tr>
                          <tr>
                                <td class='row2' align='left'>&nbsp;</td>
                                <td class='row2' align='left' style='padding: 0px;'><input type='submit' name='submit' value='".$this->ipsclass->lang['rep_submit']."'></td>
                          </tr>
                          <tr><td class='catend' colspan='2'><!-- no content --></td></tr>
                        </table>
                        </div>
                          <input type='hidden' name='act' value='rep'/>
                          <input type='hidden' name='p' value='".$this->post."'/>
                          <input type='hidden' name='mid' value='".$this->mid."'/>
                          <input type='hidden' name='type' value='".($this->type == 'win_add'?'add':'minus')."'/>
                          </form>
                         </body>
                        </html>
                       ";
                print $txt;
        }

        function change_reputation($num)
        {
                $this->message = strip_tags($this->message);

                if(!$this->mid)
                        $this->error_window(10);

                if($this->message == '')
                        $this->error_window(4);

                if($this->ipsclass->vars['rep_maxlen'] && strlen($this->message) > $this->ipsclass->vars['rep_maxlen'])
                        $this->error_window(8, $this->ipsclass->vars['rep_maxlen']);

                if($this->anonym && !$this->ipsclass->vars['rep_anonym'])
                        $this->error_window(12);

                //Get the Topic ID
                if($this->post) {
                        $this->ipsclass->DB->simple_construct( array('select' => 'topic_id',
                                                                     'from'   => 'posts',
                                                                     'where'  => 'pid = '.$this->post,
                                                                    )
                                                             );
                        $this->ipsclass->DB->simple_exec();
                        $row = $this->ipsclass->DB->fetch_row();
                        $topic_id = $row['topic_id'];
                }
                else $topic_id = 0;

                $this->ipsclass->DB->do_insert('reputation',array('from_user' => $this->ipsclass->member['id'],
                                                                  'to_user'   => $this->mid,
                                                                  'post'      => $this->post,
                                                                  'topic'     => $topic_id,
                                                                  'message'   => $this->message,
                                                                  'rating'    => $num,
                                                                  'anonym'    => $this->anonym,
                                                                  'created'   => time()));

                $this->ipsclass->DB->simple_update('members',
                                                      "reputation=reputation+$num",
                                                      'id='.$this->mid
                                                     );
                $this->ipsclass->DB->simple_exec();

                $member = $this->get_member_by_id($this->mid);
                if ($member['rep_notify'])
                {
                        $rep_from = $this->anonym ? $this->ipsclass->vars['rep_anon_title'] : "[url={$this->ipsclass->base_url}showuser={$this->ipsclass->member['id']}][b]{$this->ipsclass->member['members_display_name']}[/b][/url]";
                        $change = ($num > 0) ? $this->ipsclass->lang['rep_plus'] : $this->ipsclass->lang['rep_minus'];

                        require_once( ROOT_PATH.'sources/lib/func_msg.php' );

                        $this->lib = new func_msg();
                        $this->lib->ipsclass =& $this->ipsclass;

                        $this->lib->init();

                        $this->lib->to_by_id    = $this->mid;
                         $this->lib->from_member['id'] = $this->ipsclass->vars['rep_pmid'];
                         $this->lib->msg_title   = $this->ipsclass->lang['rep_notify_title'];
                         $this->lib->msg_post    = sprintf($this->ipsclass->lang['rep_notify_text'], $member['members_display_name'], $rep_from, $change, $this->message);
                        $this->lib->force_pm    = 0;

                        $this->lib->send_pm();

                        if ( $this->lib->error )
                        {
                                print $this->error;
                                exit();
                        }
                }

                $this->result();

        }

        function delete_rep()
        {
                if(!$this->check_moderate())
                        $this->error_window(11);

                $this->ipsclass->DB->simple_construct( array( 'select'   => '*',
                                                              'from'     => 'reputation',
                                                              'where'    => 'id='.$this->cid,
                                                            )
                                                     );
                $this->ipsclass->DB->simple_exec();
                $row = $this->ipsclass->DB->fetch_row();

                if($row['id'])
                {
                        $this->ipsclass->DB->build_and_exec_query( array( 'delete' => 'reputation', 'where' => 'id='.$this->cid ) );

                        $this->ipsclass->DB->simple_update(     'members',
                                                                "reputation=reputation - {$row['rating']}",
                                                                'id='.$row['to_user']
                                                          );
                        $this->ipsclass->DB->simple_exec();
                        $this->ipsclass->print->redirect_screen( $this->ipsclass->lang['rep_del_message'], "act=rep&type=history&mid={$row['to_user']}");
                }
        }


        function result($msg="")
        {
                if($msg == "" )
                {
                        $txt = "<html>
                                 <head>
                                  <title>".$this->ipsclass->lang['changes_done']."</title>
                                  <style type='text/css' media='all'>@import url(".$this->ipsclass->vars['board_url']."/style_images/css_3.css);</style>                            <meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />
                                 </head>
                                 <body>
                                  <script>
                                   window.opener.location.reload(true);
                                   window.close();
                                  </script>
                                  <div class='maintitle'>".$this->ipsclass->lang['changes_done']."</div>
                                  <br/><br/><br/><br/><br/><a href='javascript:window.opener.location.reload(true);window.close();'>".$this->ipsclass->lang['close']."</a>
                                 </body>
                               </html>
                               ";
                        print $txt;
                        return;
                }
        }

        function check_permissions()
        {

                //Guest cannot vote!
                if(!$this->ipsclass->member['id'])
                    $this->error_window(1);

                //Trying to change own reputaion? Cheating!
                if($this->ipsclass->member['id'] == $this->mid)
                   $this->error_window(0);

                if(!$this->ipsclass->member['rep_allow'])
                   $this->error_window(9);

                if($this->ipsclass->member['posts'] < $this->ipsclass->vars['rep_posts'])
                   $this->error_window(6, $this->ipsclass->vars['rep_posts']);

                if($this->ipsclass->vars['rep_bad'])
                {
                        //Cannot change reputation with baaad reputation (rep < -20)
                        $member = $this->get_member_by_id($this->ipsclass->member['id']);
                        if(intval($member['reputation']) < $this->ipsclass->vars['rep_bad'])
                                $this->error_window(5);
                }

                if($this->ipsclass->vars['rep_maxperday'])
                {
                        //Cannot vote more than X times every 24 hours
                        $ctime = time();
                        $timezero = $ctime - (24*60*60);

                        $this->ipsclass->DB->simple_construct( array( 'select' => 'id',
                                                                      'from'   => 'reputation',
                                                                      'where'  => 'from_user='.$this->ipsclass->member['id'].' AND created > '.$timezero
                                                                    )
                                                             );

                        $this->ipsclass->DB->simple_exec();
                        if($this->ipsclass->DB->get_num_rows() >= $this->ipsclass->vars['rep_maxperday'])
                                $this->error_window(2, $this->ipsclass->vars['rep_maxperday']);
                }

                //Cannot vote more 1 member more then 1 time in a X days
                if ($this->ipsclass->vars['rep_time'])
                {
                        $ctime = time();
                        $timezero = $ctime - ($this->ipsclass->vars['rep_time']*24*3600);

                        $this->ipsclass->DB->simple_construct( array( 'select' => 'id',
                                                                      'from'   => 'reputation',
                                                                      'where'  => 'from_user='.$this->ipsclass->member['id'].' AND to_user='.$this->mid.' AND created > '.$timezero
                                                                    )
                                                             );

                        $this->ipsclass->DB->simple_exec();
                        if($this->ipsclass->DB->get_num_rows())
                                $this->error_window(7, $this->ipsclass->vars['rep_time']);
                }

                if (!$this->ipsclass->vars['rep_onepost'] && $this->post)
                {
                        //Cannot change reputation a few times for one and the same post
                        $this->ipsclass->DB->simple_construct( array( 'select' => 'id',
                                                                      'from'   => 'reputation',
                                                                      'where'  => 'post = '.$this->post.' AND from_user = '.$this->ipsclass->member['id']
                                                                      )
                                                             );

                        $this->ipsclass->DB->simple_exec();
                        if ($this->ipsclass->DB->get_num_rows())
                                $this->error_window(3);
                }


        }

        function error_window($code, $conf = 0)
        {
                $errors = array($this->ipsclass->lang['error_00'],
                                $this->ipsclass->lang['error_01'],
                                $this->ipsclass->lang['error_02'],
                                $this->ipsclass->lang['error_03'],
                                $this->ipsclass->lang['error_04'],
                                $this->ipsclass->lang['error_05'],
                                $this->ipsclass->lang['error_06'],
                                $this->ipsclass->lang['error_07'],
                                $this->ipsclass->lang['error_08'],
                                $this->ipsclass->lang['error_09'],
                                $this->ipsclass->lang['error_10'],
                                $this->ipsclass->lang['error_11'],
                                $this->ipsclass->lang['error_12']);

                $txt = "<html>
                         <head>
                          <title>".$this->ipsclass->lang['error']."</title>
                          <style type='text/css' media='all'>@import url(".$this->ipsclass->vars['board_url']."/style_images/css_3.css);</style>
                          <meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />
                         </head>
                         <body>
                          <div class='maintitle'>".sprintf($errors[$code], $conf)."</div>
                          <br/><br/><br/><br/><br/><a href='javascript:window.close();'>".$this->ipsclass->lang['close']."</a>
                        </body>
                      </html>
                      ";

                print $txt;
                exit();


        }

        function get_member_by_id($id)
        {
                $this->ipsclass->DB->simple_construct( array( 'select' => '*',
                                                              'from'   => 'members',
                                                              'where'  => "id=".$id
                                                            )
                                                     );
                $this->ipsclass->DB->simple_exec();
                if ($this->ipsclass->DB->get_num_rows())
                {                        $row = $this->ipsclass->DB->fetch_row();
                }

                return $row;
        }

        function check_moderate()
        {
                if (    ($this->ipsclass->member['mgroup'] == $this->ipsclass->vars['admin_group'])
                        || $this->ipsclass->member['g_access_cp']
                        || ($this->ipsclass->member['g_is_supmod'] && $this->ipsclass->vars['rep_smod']) )
                   return 1;
                return 0;
        }

        function view_rep_history()
        {
                $txt = "";
                $rows = array();
                $mod_th = "";

                //Get member info
                $member = $this->get_member_by_id($this->mid);

                if ($this->check_moderate())
                {
                        $mod_th = "<th align='center'>&nbsp;</th>";
                }

                $order = 'created';
                if ($this->ipsclass->vars['rep_sort'] == 'desc')
                        $order .= ' DESC';

                $this->ipsclass->DB->simple_construct( array( 'select'   => 'count(*) as count',
                                                              'from'     => 'reputation',
                                                              'where'    => 'to_user = '.$this->mid,
                                                            )
                                                     );
                $this->ipsclass->DB->simple_exec();
                $count = $this->ipsclass->DB->fetch_row();
                $count = intval($count['count']);

                $query = array( 'select'   => 'r.*, m.members_display_name, t.title',
                                'from'     => 'reputation r LEFT JOIN '.SQL_PREFIX.'members m on m.id=r.from_user LEFT JOIN '.SQL_PREFIX.'topics t ON t.tid=r.topic',
                                'where'    => 'to_user = '.$this->mid,
                                'order'    => $order
                              );

                if ($this->ipsclass->vars['rep_perpage'])
                        $query['limit'] = array( $this->st, $this->ipsclass->vars['rep_perpage']);

                //Get reputation rows
                $this->ipsclass->DB->simple_construct( $query );

                $this->ipsclass->DB->simple_exec();
                {
                        while($row = $this->ipsclass->DB->fetch_row())
                        {
                                $rows[] = $row;
                        }
                }

                $pluses = ceil(($count + $member['reputation'])/2);

                //Lets start with output
                $txt .= "<div class='borderwrap'>
                         <div class='maintitle'>".$this->ipsclass->lang['page_title']." ".$member['members_display_name']." [+{$pluses}/-".($count-$pluses)."]</div>

                         <table class='ipbtable' cellspacing='1'>
                          <tr>
                           <th align='center' width='15%'>".$this->ipsclass->lang['from_user']."</th>
                           <th align='center' width='25%'>".$this->ipsclass->lang['from_topic']."</th>
                           <th align='center' width='40%'>".$this->ipsclass->lang['explanation']."</th>
                           <th align='center'>".$this->ipsclass->lang['level']."</th>
                           <th align='center' width='12%'>".$this->ipsclass->lang['date']."</th>
                           $mod_th
                          </tr>
                       ";

                foreach($rows as $row)
                {
                        if($mod_th) $mod_td = "<td class='row1' align='center'><b><a href='".$this->ipsclass->base_url."act=rep&type=delete&cid={$row['id']}'>".$this->ipsclass->lang['rep_delete']."</a></b></td>";

                        $changelink = $row['topic'] ? "<b><a href='".$this->ipsclass->base_url."showtopic=".$row['topic']."&view=findpost&p=".$row['post']."'>".$row['title']."</a></b>" : "<a href='".$this->ipsclass->base_url."showuser=".$this->mid."'>В профиле</a>";

                        $anon = ($mod_th && $this->ipsclass->vars['rep_view_anon']) ? "<a href='".$this->ipsclass->base_url."showuser=".$row['from_user']."'><font color='gray'>".$row['members_display_name']."</font></a>" : "<font color='gray'>".$this->ipsclass->vars['rep_anon_title']."</font>";

                        $userlink = $row['anonym'] ? "<i>$anon</i>" : "<b><a href='".$this->ipsclass->base_url."showuser=".$row['from_user']."'>".$row['members_display_name']."</a></b>";

                        $txt .= "<tr>
                                  <td class='row2' align='left'>$userlink</td>
                                  <td class='row2' align='left'>$changelink</td>
                                  <td class='row2' align='left'>".$row['message']."</td>
                                  <td class='row1' align='center'><img border='0' src='style_images/".$this->ipsclass->skin['_imagedir']."/".(($row['rating']>0)?'up.gif':'down.gif')."' /></td>
                                  <td class='row1' align='center'>".$this->ipsclass->get_date($row['created'],'LONG')."</td>
                                  $mod_td
                                 </tr>
                                ";
                }

                $this->ipsclass->load_template('skin_global');
                if ($this->ipsclass->vars['rep_perpage']) {
                $links = $this->ipsclass->build_pagelinks(  array( 'TOTAL_POSS'  => $count,
                                                                   'PER_PAGE'    => $this->ipsclass->vars['rep_perpage'],
                                                                   'CUR_ST_VAL'  => $this->st,
                                                                   'L_SINGLE'     => "",
                                                                   'L_MULTI'      => $this->ipsclass->lang['pages'],
                                                                   'BASE_URL'     => $this->ipsclass->base_url."act=rep&amp;type=history&amp;mid={$this->mid}"
                                                                                                                 )
                                                                                                  );
                }

                $txt .= "   <tr><td class='catend' colspan='6'><!-- no content --></td></tr>
                           </table>
                          </div><br />
                          $links
                        ";

                $this->output .= $txt;
                $this->ipsclass->print->add_output($this->output);
                $this->ipsclass->print->do_output( array( 'TITLE' => $this->ipsclass->lang['page_title'].' - '. $this->ipsclass->vars['board_name'], 'JS' => 0, 'NAV' => array( $this->ipsclass->lang['page_title'] ) ) );
        }


        function view_rep_top()
        {
                $txt = "";
                $rows = array();

                $pp = $this->ipsclass->vars['rep_pp_rating'] > 0 ? $this->ipsclass->vars['rep_pp_rating'] : 30;

                //Get member info
                $member = $this->get_member_by_id($this->mid);

                $order = 'created';
                if ($this->ipsclass->vars['rep_sort'] == 'desc')
                        $order .= ' DESC';

                $this->ipsclass->DB->simple_construct( array( 'select'   => 'COUNT(DISTINCT to_user) as count',
                                                              'from'     => 'reputation',
                                                            )
                                                     );
                $this->ipsclass->DB->simple_exec();
                $count = $this->ipsclass->DB->fetch_row();
                $count = intval($count['count']);

                if($this->order == "asc")
                {
                        $order = "m.reputation";
                        $opts = "
                        <option value='desc'>убыванию</option>
                        <option value='asc' selected='selected'>возрастанию</option>
                        ";
                }
                else
                {
                        $order = "m.reputation DESC";
                        $opts = "
                        <option value='desc' selected='selected'>убыванию</option>
                        <option value='asc'>возрастанию</option>
                        ";
                }

                $query = array( 'select'   => 'm.*, COUNT(r.rating) as count, SUM(r.rating) as summ',
                                'from'     => 'reputation r LEFT JOIN '.SQL_PREFIX.'members m ON r.to_user = m.id',
                                'where'    => '1 GROUP BY to_user',
                                'order'    => $order,
                                'limit'    => array( $this->st, $pp)
                              );

                $this->ipsclass->DB->simple_construct( $query );

                $this->ipsclass->DB->simple_exec();
                {
                        while($row = $this->ipsclass->DB->fetch_row())
                        {
                                $rows[] = $row;
                        }
                }



                //Lets start with output
                $txt .= "<div class='borderwrap'>
                         <div class='maintitle'>".$this->ipsclass->lang['top_title']."</div>

                         <table class='ipbtable' cellspacing='1'>
                          <tr>
                           <th align='center'>".$this->ipsclass->lang['rep_user']."</th>
                           <th align='center' width='15%'>".$this->ipsclass->lang['rep_joined']."</th>
                           <th align='center' width='10%'>".$this->ipsclass->lang['rep_posts']."</th>
                           <th align='center' width='15%'>".$this->ipsclass->lang['rep_value']."</th>
                          </tr>
                       ";

                foreach($rows as $row)
                {
                        $pluses = ceil(($row['count']+$row['summ'])/2);
                        $txt .= "<tr>
                                  <td class='row2' align='left'><b><a href='".$this->ipsclass->base_url."showuser=".$row['id']."'>".$row['members_display_name']."</a></b></td>
                                  <td class='row2' align='center'>".$this->ipsclass->get_date( $row['joined'], 'JOINED' )."</td>
                                  <td class='row2' align='center'>{$row['posts']}</td>
                                  <td class='row1' align='center'><b><a href='".$this->ipsclass->base_url."act=rep&type=history&mid=".$row['id']."'>{$row['reputation']}</a> (<font color='green'>+$pluses</font>/<font color='red'>-".abs($row['summ']-$pluses)."</font>)</b></td>
                                 </tr>
                                ";
                }
                $this->ipsclass->load_template('skin_global');

                $links = $this->ipsclass->build_pagelinks(  array( 'TOTAL_POSS'  => $count,
                                                                   'PER_PAGE'    => $pp,
                                                                   'CUR_ST_VAL'  => $this->st,
                                                                   'L_SINGLE'     => "",
                                                                   'L_MULTI'      => $this->ipsclass->lang['pages'],
                                                                   'BASE_URL'     => $this->ipsclass->base_url."act=rep&amp;type=rating"
                                                                                                                 )
                                                                                                  );


                $txt .= "   <td class='catend' colspan='4'><!-- no content --></td>
                           </table>
<form action='{$this->ipsclass->base_url}' method='get'>
<div class='formsubtitle' align='center'>
<input type='hidden' name='act' value='rep'>
<input type='hidden' name='type' value='rating'>
".$this->ipsclass->lang['rep_sort_by']."
<select name='order' class='forminput'>
$opts
</select>
<input value='ОК!' class='button' type='submit'>
</div>
</form>

                          </div><br />
                          $links
                        ";

                $this->output .= $txt;
                $this->ipsclass->print->add_output($this->output);
                $this->ipsclass->print->do_output( array( 'TITLE' => $this->ipsclass->lang['top_title'].' - '.$this->ipsclass->vars['board_name'], 'JS' => 0, 'NAV' => array( $this->ipsclass->lang['top_title'] ) ) );
        }



}