<?php

class cp_skin_rss {

var $ipsclass;


//===========================================================================
// RSS
//===========================================================================
function rss_export_overview($content, $page_links) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Потоки RSS экспортирования</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='90%'>Название потока</td>
  <td class='tablesubheader' width='5%' align='center'>Статус</td>
  <td class='tablesubheader' width='5%'><img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
 </tr>
EOF;

if( $page_links != "" )
{
$IPBHTML .= <<<EOF
 <tr>
  <td class='tablesubheader' colspan='3' align='right'>
  	{$page_links}
  </td>
 </tr>
EOF;
}

$IPBHTML .= <<<EOF
 {$content}
EOF;

if( $page_links != "" )
{
$IPBHTML .= <<<EOF
 <tr>
  <td class='tablesubheader' colspan='3' align='right'>
  	{$page_links}
  </td>
 </tr>
EOF;
}

$IPBHTML .= <<<EOF
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
<script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_add   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssexport_add'>Создать новый поток</a>",
  			 img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssexport_recache&amp;rss_export_id=all'>Обновить все кеши RSS экспортирования</a>"
           ) );
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// RSS
//===========================================================================
function rss_export_overview_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
   <a target='_blank' href='{$this->ipsclass->vars['board_url']}/index.php?act=rssout&amp;id={$data['rss_export_id']}'><img src='{$this->ipsclass->skin_acp_url}/images/rss.png' border='0' alt='RSS' style='vertical-align:top' /></a>
   <strong>{$data['rss_export_title']}</strong>
 </td>
 <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$data['_enabled_img']}' border='0' alt='YN' class='ipd' /></td>
 <td class='tablerow1'><img id="menu{$data['rss_export_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['rss_export_id']}",
  new Array(
			img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssexport_edit&amp;rss_export_id={$data['rss_export_id']}'>Изменить</a>",
  			img_delete + " <a href='#' onclick='maincheckdelete(\"{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssexport_delete&amp;rss_export_id={$data['rss_export_id']}\");'>Удалить</a>",
  			img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssexport_recache&amp;rss_export_id={$data['rss_export_id']}'>Обновить кеш</a>"
  		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// RSS FORM
//===========================================================================
function rss_export_form($form, $title, $formcode, $button, $rssstream) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=$formcode&amp;rss_export_id={$rssstream['rss_export_id']}' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>$title</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td width='40%' class='tablerow1'><strong>Название</strong></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_title']}</td>
 </tr>
<tr>
   <td width='40%' class='tablerow1'><strong>Описание</strong></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_desc']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Изображение потока</strong><div class='desctext'>Для отображения изображения в программах для чтения RSS</div></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_image']} <span class='desctext'>* Опционально</span></td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Включен?</strong></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_enabled']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>В RSS Экспорт включать первое сообщение темы?</strong></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_include_post']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Количество тем RSS Экспорта</strong><div class='desctext'>Количество экспортируемых тем</div></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_count']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Сортировать RSS Экспорт</strong></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_order']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Сортировать RSS Экспорт</strong></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_sort']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Экспортировать темы из форумов</strong><div class='desctext'>ВАЖНО: Маски доступа не работают здесь. Темы будут экспортированы из выбранных форум, независимо от масок доступа к форумам.</div></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_forums']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>RSS Экспорт: Частота обновления кеша</strong><div class='desctext'>Обновлять RSS кеш каждые <em>n</em> минут</div></td>
   <td width='60%' class='tablerow2'>{$form['rss_export_cache_time']} <span class='desctext'>минут</span></td>
 </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='$button' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// RSS FORM
//===========================================================================
function rss_import_remove_articles_form( $rssstream, $article_count ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_remove_complete&amp;rss_import_id={$rssstream['rss_import_id']}' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>Удалить темы из потока: {$rssstream['rss_import_title']}</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td colspan='2' class='tablerow1'>Вы можете удалить темы, которые были опубликованы, из потока RSS Импорта. Этот поток был создан с <strong>{$article_count}</strong> темами.</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Удалить последние <em>n</em> импортированных тем</strong><div class='desctext'>Оставьте поле пустым для удаления всех</div></td>
   <td width='60%' class='tablerow2'><input type='text' name='remove_count' value='10' /></td>
 </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='Удалить' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// RSS FORM
//===========================================================================
function rss_import_form($form, $title, $formcode, $button, $rssstream) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<script type="text/javascript" src='{$this->ipsclass->vars['board_url']}/jscripts/ipb_xhr_findnames.js'></script>
<script type='text/javascript'>
<!--
function enable_auth_boxes()
{
	auth_req = document.getElementById('rss_import_auth_userinfo');
	if( auth_req.style.display == 'none' )
	{
		auth_req.style.display = '';
	}
	else
	{
		auth_req.style.display = 'none';
	}
}

function do_validate()
{
	formobj = document.getElementById('rssimport_validate');
	formobj.value = "1";
	document.getElementById('rssimport_form').submit();
}

-->
</script>
<form id='rssimport_form' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=$formcode&amp;rss_import_id={$rssstream['rss_import_id']}' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<input id='rssimport_validate' type='hidden' name='rssimport_validate' value='0' />
<div class='tableborder'>
 <div class='tableheaderalt'>$title</div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td class='tablerow1'>
      <fieldset>
       <legend><strong>RSS Импорт</strong></legend>
 		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
 		<tr>
   		  <td width='40%' class='tablerow1'><strong>Название</strong></td>
   		  <td width='60%' class='tablerow2'>{$form['rss_import_title']}</td>
 		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Ссылка на поток</strong><div class='desctext'>Это должно быть RDF или RSS поток.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_url']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Кодировка потока</strong><div class='desctext'>Пример: ISO-8859-1, UTF-8. Используйте «UTF-8», если сомневаетесь.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_charset']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Включить импорт?</strong></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_enabled']}</td>
		</tr>
	   </table>
	 </fieldset>
  </td>
 </tr>
  <tr>
   <td class='tablerow1'>
      <fieldset>
       <legend><strong>.htaccess аутентификация RSS Импорта</strong></legend>
 		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
 		<tr>
   		  <td width='40%' class='tablerow1'><strong>Этот поток требует .htaccess аутентификацию?</strong><div class='desctext'>Большинство потоков не требует это</div></td>
   		  <td width='60%' class='tablerow2'>{$form['rss_import_auth']}</td>
 		</tr>
		<tr>
		  <td colspan='2' width='100%' id='rss_import_auth_userinfo' {$form['rss_div_show']}>
		   <table cellpadding='0' cellspacing='0' border='0' width='100%'>
		    <tr>
		  		<td width='40%' class='tablerow1'><strong>Имя пользователя</strong></td>
		 		<td width='60%' class='tablerow2'>{$form['rss_import_auth_user']}</td>
		 	</tr>
		 	<tr>
		  		<td width='40%' class='tablerow1'><strong>Пароль</strong></td>
		  		<td width='60%' class='tablerow2'>{$form['rss_import_auth_pass']}</td>
			</tr>
		  </table>
		 </td>
		</tr>
	   </table>
	 </fieldset>
  </td>
 </tr>
 <tr>
  <td class='tablerow1'>
  	<div id='ipb-get-members' style='border:1px solid #000; background:#FFF; padding:2px;position:absolute;width:170px;display:none;z-index:100'></div>
      <fieldset>
       <legend><strong>Контент RSS Импорта</strong></legend>
 		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
 		<tr>
		  <td width='40%' class='tablerow1'><strong>Импортировать в  форумы</strong><div class='desctext'>Выберите форум, в который импортировать каждую RSS запись, как новую тему.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_forum_id']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Сохранить HTML?</strong><div class='desctext'>Если да, HTML будет сохранен &mdash; вы обязаны включить HTML на форуме, чтобы была возможность редактировать импортируемые записи. Если нет, HTML будет конвертироваться по возможности в BB-код.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_allow_html']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Автор записей</strong><div class='desctext'>От этого имени будет публиковаться тема с RSS записью (отображаeмое имя)</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_mid']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Увеличивать счетчик сообщений при RSS Импорте</strong><div class='desctext'>При публикации новой темы от пользователя из RSS Импорта, у него будет увеличен счетчик сообщений в форумах, где этот счетчик включен.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_inc_pcount']}</td>
		</tr>
		 <tr>
		  <td width='40%' class='tablerow1'><strong>Добавлять ссылку источника</strong><div class='desctext'>BB-код разрешен: {url} = URL к записи<br />Если это поле заполнено, то к сообщению будет добавлена ссылка на источник, откуда взята RSS запись</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_showlink']} <div class='desctext'>*Оставьте поле пустым, чтобы не включать ссылку в сообщение</div></td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Открытость тем</strong><div class='desctext'>Если «Да», то тема будет создана открытая. «Нет» — тема будет закрыта</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_topic_open']}</td>
		</tr>
	    <tr>
		  <td width='40%' class='tablerow1'><strong>Невидимость тем</strong><div class='desctext'>Если «Да», то будет создана невидимая тема. «Нет» — тема видимая</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_topic_hide']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Префикс тем</strong><div class='desctext'>Префикс, который будет добавлен перед заголовком RSS записи</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_topic_pre']}</td>
		</tr>
	   </table>
	 </fieldset>
	</td>
  </tr>
  <tr>
    <td class='tablerow1'>
      <fieldset>
       <legend><strong>Настройки RSS импорта</strong></legend>
 		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
 		<tr>
		  <td width='40%' class='tablerow1'><strong>Количество импортируемых записей за обновление</strong><div class='desctext'>Импортировать <em>n</em> записей за обновление. Импортирование довольно тяжелый процесс.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_pergo']}</td>
		</tr>
		<tr>
		  <td width='40%' class='tablerow1'><strong>Частота обновления</strong><div class='desctext'>Проверять новые RSS записи каждые <em>n</em> минут. Минимум 30 минут.</div></td>
		  <td width='60%' class='tablerow2'>{$form['rss_import_time']}</td>
		</tr>
	   </table>
	 </fieldset>
	</td>
  </tr>
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='$button' /> &nbsp;&nbsp;&nbsp;
 										 <input type='button' class='realbutton' value='Проверить поток' onclick='do_validate();' /></div>
</div>
</form>
<script type="text/javascript">
	// INIT find names
	init_js( 'rssimport_form', 'rss_import_mid');
	// Run main loop
	var tmp = setTimeout( 'main_loop()', 10 );
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// RSS
//===========================================================================
function rss_import_overview($content, $page_links) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Быстрая проверка потока</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablerow1'><b>Введите ссылку:</b></td>
  <td class='tablerow2'><form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_validate' method='post'><input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' /><input type='text' size='50' name='rss_url' value='http://' /> <input type='submit' class='realbutton' value='Проверить' /></form></td>
 </tr>
 </table>
</div>
<br />
<div class='tableborder'>
 <div class='tableheaderalt'>Потоки RSS импортирования</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='90%'>Название потока</td>
  <td class='tablesubheader' width='5%' align='center'>Статус</td>
  <td class='tablesubheader' width='5%'><img id="menumainone" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
 </tr>
EOF;

if( $page_links != "" )
{
$IPBHTML .= <<<EOF
 <tr>
  <td class='tablesubheader' colspan='3' align='right'>
  	{$page_links}
  </td>
 </tr>
EOF;
}

$IPBHTML .= <<<EOF
 {$content}
EOF;

if( $page_links != "" )
{
$IPBHTML .= <<<EOF
 <tr>
  <td class='tablesubheader' colspan='3' align='right'>
  	{$page_links}
  </td>
 </tr>
EOF;
}

$IPBHTML .= <<<EOF
 </table>
 <div align='center' class='tablefooter'>&nbsp;</div>
</div>
<script type="text/javascript">
  menu_build_menu(
  "menumainone",
  new Array( img_add   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_add'>Создать новый поток</a>",
  			 img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_recache&amp;rss_import_id=all'>Обновить все кеши RSS импортирования</a>"
           ) );
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// RSS
//===========================================================================
function rss_validate_msg( $info ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
  <span class='{$info['class']}'>{$info['msg']}</span>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// RSS
//===========================================================================
function rss_import_overview_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
   <a target='_blank' href='{$data['rss_import_url']}'><img src='{$this->ipsclass->skin_acp_url}/images/rss.png' border='0' alt='RSS' style='vertical-align:top' /></a>
   <strong>{$data['rss_import_title']}</strong>
 </td>
 <td class='tablerow2' align='center'><img src='{$this->ipsclass->skin_acp_url}/images/{$data['_enabled_img']}' border='0' alt='YN' class='ipd' /></td>
 <td class='tablerow1'><img id="menu{$data['rss_import_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$data['rss_import_id']}",
  new Array(
			img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_edit&amp;rss_import_id={$data['rss_import_id']}'>Изменить</a>",
  			img_delete   + " <a href='#' onclick='maincheckdelete(\"{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_delete&amp;rss_import_id={$data['rss_import_id']}\");'>Удалить поток</a>",
  			img_delete   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_remove&amp;rss_import_id={$data['rss_import_id']}'>Удалить все записи</a>",
  			img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_recache&amp;rss_import_id={$data['rss_import_id']}'>Обновить</a>",
  			img_item   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;code=rssimport_validate&amp;rss_id={$data['rss_import_id']}'>Проверить</a>"
  		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}



}


?>