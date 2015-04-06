<?php

class cp_skin_taskmanager {

var $ipsclass;


//===========================================================================
// TASK MANAGER: Overview
//===========================================================================
function task_manager_logsshow_wrapper( $last5 ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>:Журнал выполненных задач</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader'>Задача</td>
  <td class='tablesubheader'>Время запуска</td>
  <td class='tablesubheader'>Информация</td>
 </tr>
 $last5
 </table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// TASK MANAGER: Overview
//===========================================================================
function task_manager_logs_wrapper( $last5, $form ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>5 последних запущенных задач</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader'>Название задачи</td>
  <td class='tablesubheader'>Дата запуска</td>
  <td class='tablesubheader'>Информация</td>
 </tr>
 $last5
 </table>
</div>

<br />

<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_log_show' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>Просмотр журнала задач</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablerow1'><strong>Просомтр записей для</strong></td>
  <td class='tablerow2'>{$form['task_title']}</td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>Показать <em>n</em> записей</strong></td>
  <td class='tablerow2'>{$form['task_count']}</td>
 </tr>
 <tr>
  <td colspan='2' class='tablefooter' align='center'><input class='realbutton' type='submit' value='Показать' /></td>
 </tr>
 </table>
</div>
</form>

<br />

<form action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_log_delete' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<div class='tableborder'>
 <div class='tableheaderalt'>Удаление записей журнала</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablerow1'><strong>Удаление записей для</strong></td>
  <td class='tablerow2'>{$form['task_title_delete']}</td>
 </tr>
 <tr>
  <td class='tablerow1'><strong>Удалить записи старше, чем <em>n</em> дней</strong></td>
  <td class='tablerow2'>{$form['task_prune']}</td>
 </tr>
 <tr>
  <td colspan='2' class='tablefooter' align='center'><input class='realbutton' type='submit' value='Удалить' /></td>
 </tr>
 </table>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// TRAFFIC: POPULAR row
//===========================================================================
function task_manager_last5_row( $data ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td width='25%' class='tablerow1'><strong>{$data['log_title']}</strong></td>
 <td width='15%' class='tablerow2'>{$data['log_date']}</td>
 <td width='45%' class='tablerow2'>{$data['log_desc']}</td>
</tr>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Task manager form
//===========================================================================
function task_manager_form( $form, $button, $formbit, $type, $title, $task ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<script type='text/javascript' language='javascript'>
function updatepreview()
{
	var formobj  = document.adminform;
	var dd_wday  = new Array();
	
	dd_wday[0]   = 'Воскресенье';
	dd_wday[1]   = 'Понедельник';
	dd_wday[2]   = 'Вторник';
	dd_wday[3]   = 'Среда';
	dd_wday[4]   = 'Четверг';
	dd_wday[5]   = 'Пятница';
	dd_wday[6]   = 'Суббота';
	
	var output       = '';
	
	chosen_min   = formobj.task_minute.options[formobj.task_minute.selectedIndex].value;
	chosen_hour  = formobj.task_hour.options[formobj.task_hour.selectedIndex].value;
	chosen_wday  = formobj.task_week_day.options[formobj.task_week_day.selectedIndex].value;
	chosen_mday  = formobj.task_month_day.options[formobj.task_month_day.selectedIndex].value;
	
	var output_min   = '';
	var output_hour  = '';
	var output_day   = '';
	var timeset      = 0;
	
	if ( chosen_mday == -1 && chosen_wday == -1 )
	{
		output_day = '';
	}
	
	if ( chosen_mday != -1 )
	{
		output_day = +chosen_mday+' числа.';
	}
	
	if ( chosen_mday == -1 && chosen_wday != -1 )
	{
		output_day = 'В ' + dd_wday[ chosen_wday ]+'.';
	}
	
	if ( chosen_hour != -1 && chosen_min != -1 )
	{
		output_hour = 'В '+chosen_hour+':'+formatnumber(chosen_min)+'.';
	}
	else
	{
		if ( chosen_hour == -1 )
		{
			if ( chosen_min == 0 )
			{
				output_hour = 'Каждый час';
			}
			else
			{
				if ( output_day == '' )
				{
					if ( chosen_min == -1 )
					{
						output_min = 'Каждую минуту';
					}
					else
					{
						output_min = 'Каждые '+chosen_min+' минут.';
					}
				}
				else
				{
					output_min = 'В '+formatnumber(chosen_min)+' минут каждого часа';
				}
			}
		}
		else
		{
			if ( output_day != '' )
			{
				output_hour = 'В ' + chosen_hour + ':00';
			}
			else
			{
				output_hour = 'Каждые ' + chosen_hour + ' часов';
			}
		}
	}
	
	output = output_day + ' ' + output_hour + ' ' + output_min;
	
	formobj.showtask.value = output;
}
							
function formatnumber(num)
{
	if ( num == -1 )
	{
		return '00';
	}
	if ( num < 10 )
	{
		return '0'+num;
	}
	else
	{
		return num;
	}
}
</script>
<form name='adminform' action='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=$formbit&amp;task_id={$task['task_id']}&amp;type=$type' method='post'>
<input type='hidden' name='_admin_auth_key' value='{$this->ipsclass->_admin_auth_key}' />
<input type='hidden' name='task_cronkey' value='{$task['task_cronkey']}' />
<div class='tableborder'>
 <div class='tableheaderalt'>
  <div style='float:left'>$title</div>
  <div align='right' style='padding-right:5px'><input type='text' name='showtask' class='realbutton' size='50' style='font-size:10px;width:auto;font-weight:normal;'/></div>
 </div>
 <table cellpadding='0' cellspacing='0' border='0' width='100%'>
 <tr>
   <td width='40%' class='tablerow1'><strong>Название задачи</strong></td>
   <td width='60%' class='tablerow2'>{$form['task_title']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Краткое описание задачи</strong></td>
   <td width='60%' class='tablerow2'>{$form['task_description']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>PHP файл задачи для запуска</strong><div class='desctext'>Это PHP файл, который будет запускаться при запуске задачи.</div></td>
   <td width='60%' class='tablerow2'>./sources/tasks/ {$form['task_file']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1' colspan='2'>
    <fieldset>
    <legend><strong>Опции времени</strong></legend>
    <table cellpadding='0' cellspacing='0' border='0' width='100%'>
    <tr>
   		<td width='40%' class='tablerow1'><strong>Время задачи: минуты</strong><div class='desctext'>Выберите 'Каждую минуту' для запуска каждую минуту или точное значение минуты в часу</div></td>
   		<td width='60%' class='tablerow2'>{$form['task_minute']}</td>
 	</tr>
 	<tr>
   		<td width='40%' class='tablerow1'><strong>Время задачи: часы</strong><div class='desctext'>Выберите 'Каждый час' для запуска каждый час или точное значение часа в день</div></td>
   		<td width='60%' class='tablerow2'>{$form['task_hour']}</td>
 	</tr>
 	<tr>
   		<td width='40%' class='tablerow1'><strong>Время задачи: день недели</strong><div class='desctext'>Выберите 'Каждый день недели' для запуска каждый день недели или просто укажите день недели</div></td>
   		<td width='60%' class='tablerow2'>{$form['task_week_day']}</td>
 	</tr>
 	<tr>
   		<td width='40%' class='tablerow1'><strong>Время задачи: день месяца</strong><div class='desctext'>Выберите 'Каждый день месяца' для запуска каждый день или точное число</div></td>
   		<td width='60%' class='tablerow2'>{$form['task_month_day']}</td>
 	</tr>
    </table>
   </fieldset>
  </td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Включить запись журнала действий</strong><div class='desctext'>При включенной опции все выполнения задач будут записаны в базу данных, не рекомендуется для основных задач, выполняемых каждые 5-10 минут.</div></td>
   <td width='60%' class='tablerow2'>{$form['task_log']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Включить задачу?</strong><div class='desctext'>Если вы используете CRON, вы должны отключить вызов этой задачи здесь.</div></td>
   <td width='60%' class='tablerow2'>{$form['task_enabled']}</td>
 </tr>
EOF;
//startif
if ( $form['task_key'] != "" )
{		
$IPBHTML .= <<<EOF
 <tr>
   <td width='40%' class='tablerow1'><strong>Ключ задачи</strong><div class='desctext'>Эта опция используется для вызова задачи только при указании ID</div></td>
   <td width='60%' class='tablerow2'>{$form['task_key']}</td>
 </tr>
 <tr>
   <td width='40%' class='tablerow1'><strong>Безопасный режим</strong><div class='desctext'>Если выбрана данная опция, эта задача не сможет быть изменена администраторами</div></td>
   <td width='60%' class='tablerow2'>{$form['task_safemode']}</td>
 </tr>
EOF;
}//endif
$IPBHTML .= <<<EOF
 </table>
 <div align='center' class='tablefooter'><input type='submit' class='realbutton' value='$button' /></div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// TASK MANAGER: Overview
//===========================================================================
function task_manager_wrapper($content, $date) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='tableborder'>
 <div class='tableheaderalt'>Список задач</div>
 <table cellpadding='0' cellspacing='0' width='100%'>
 <tr>
  <td class='tablesubheader' width='40%'>Название</td>
  <td class='tablesubheader' width='25%'>Следующий запуск</td>
  <td class='tablesubheader' width='5%'>Минута</td>
  <td class='tablesubheader' width='5%'>Час</td>
  <td class='tablesubheader' width='5%'>День месяца</td>
  <td class='tablesubheader' width='5%'>День недели</td>
  <td class='tablesubheader' width='1%'>Действия</td>
 </tr>
 $content
 </table>
 <div align='center' class='tablefooter'><div class='fauxbutton-wrapper'><span class='fauxbutton'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_add'>Добавить задачу</a></span></div></div>
</div>
<br />
<div align='center' class='desctext'><a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_rebuild_xml'>Обновить задачи из файла «tasks.xml»</a></em></div>
<br />
<div align='center' class='desctext'><em>Всё время в GMT. Сейчас: $date</em></div>
EOF;
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// TRAFFIC: POPULAR row
//===========================================================================
function task_manager_row( $row ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<tr>
 <td class='tablerow1'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
   <td width='99%' style='font-size:10px'>
	 <strong{$row['_class']}>
EOF;
//startif
if ( $row['task_locked'] > 0 )
{		
$IPBHTML .= <<<EOF
 <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_unlock&amp;task_id={$row['task_id']}'><img src='{$this->ipsclass->skin_acp_url}/images/lock_close.gif' border='0' alt='Разблокировать' class='ipd' /></a>
EOF;
}//endif
$IPBHTML .= <<<EOF
	 {$row['task_title']}{$row['_title']}</strong>
	 <div style='color:gray'><em>{$row['task_description']}</em></div>
	   <div align='center' style='position:absolute;width:auto;display:none;text-align:center;background:#EEE;border:2px outset #555;padding:4px' id='pop{$row['task_id']}'>
		curl -s -o /dev/null {$this->ipsclass->vars['board_url']}/index.{$this->ipsclass->vars['php_ext']}?{$this->ipsclass->form_code}&amp;ck={$row['task_cronkey']}
	   </div>
   </td>
   <td width='1%' nowrap='nowrap'>
	<a href='#' onclick="toggleview('pop{$row['task_id']}');return false;" title='Показать вызов команды CURL из CRON'><img src='{$this->ipsclass->skin_acp_url}/images/task_cron.gif' border='0' alt='Cron' /></a>
	<a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_run_now&amp;task_id={$row['task_id']}' title='Выполнить задачу сейчас (ID: {$row['task_id']})'><img src='{$this->ipsclass->skin_acp_url}/images/{$row['_image']}'  border='0' alt='Запустить' /></a>
   </td>
  </tr>
 </table>
 </td>
 <td class='tablerow2'>{$row['_next_run']}</td>
 <td class='tablerow2'>{$row['task_minute']}</td>
 <td class='tablerow2'>{$row['task_hour']}</td>
 <td class='tablerow2'>{$row['task_month_day']}</td>
 <td class='tablerow2'>{$row['task_week_day']}</td>
 <td class='tablerow1'><img id="menu{$row['task_id']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Опции' class='ipd' /></td>
</tr>
<script type="text/javascript">
  menu_build_menu(
  "menu{$row['task_id']}",
  new Array( img_edit   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_edit&amp;task_id={$row['task_id']}'>Изменить</a>",
  			 img_password   + " <a href='{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_unlock&amp;task_id={$row['task_id']}'>Разблокировать</a>",
  			 img_delete   + " <a href='#' onclick='confirm_action(\"{$this->ipsclass->base_url}&amp;{$this->ipsclass->form_code}&amp;do=task_delete&amp;task_id={$row['task_id']}\"); return false;'>Удалить</a>"
		    ) );
 </script>
EOF;

//--endhtml--//
return $IPBHTML;
}


}


?>