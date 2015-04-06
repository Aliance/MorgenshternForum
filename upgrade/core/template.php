<?php
/**
 * Invision Power Board
 * Template Controller for installer framework
 */

class install_template
{
	var $page_title   = '';
	var $page_content = '';
	var $page_current = '';
	var $message	  = '';
	var $hide_next    = 0;	
	var $in_error	  = 0;

	var $install_pages = array();
	
	var $ipsclass;
	
	/**
	 * install_template::install_template
	 * 
	 * CONSTRUCTOR
	 *
	 */	
	function install_template( &$ipsclass )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$_pages         =  array();
		$this->ipsclass =& $ipsclass;
		
		//-----------------------------------------
		// Grab XML file and check
		//-----------------------------------------
		
		if ( file_exists( INS_ROOT_PATH . 'installfiles/sequence.xml' ) )
		{
			$config = implode( '', file( INS_ROOT_PATH . 'installfiles/sequence.xml' ) );
			$xml = new class_xml();
	
			$config = $xml->xml_parse_document( $config );
			
			//-----------------------------------------
			// Loop through and sort out settings...
			//-----------------------------------------

			foreach( $xml->xml_array['installdata']['action'] as $id => $entry )
			{
				$_pages[ $entry['position']['VALUE'] ] = array( 'file' => $entry['file']['VALUE'],
															    'menu' => $entry['menu']['VALUE'] );
			}
			
			ksort( $_pages );
			
			foreach( $_pages as $position => $data )
			{
				$this->install_pages[ $data['file'] ] = $this->ipsclass->txt_convert_charsets( $data['menu'], 'UTF-8' );
			}
		}
		
		$this->install_pages['done'] = 'Завершение';
	   
		/* Set Current Page */
		$this->page_current = ( $this->ipsclass->input['p'] ) ? $this->ipsclass->input['p'] : 'login';
		
		if( ! $this->install_pages[$this->page_current] )
		{
			$this->page_current = 'login';	
		}
	}
	
	/**
	 * install_template::set_title
	 * 
	 * Sets the title for the current page
	 *
	 * @var string $title
	 */
	function set_title( $title )
	{
		$this->page_title = $title;	
	}

	/**
	 * install_template::append
	 * 
	 * Adds to the main body output
	 *
	 * @var string $add
	 */
	
	function append( $add )
	{
		$this->page_content .= $add;	
	}
	

	/**
	 * install_template::output
	 * 
	 * Builds page and sends to browser
	 *
	 */	
	function output()
	{
		/* Build Side Bar */
		$curr_reached   = 0;
		$this->progress = array();
		
		foreach( $this->install_pages as $key => $page )
		{
			if( $key == $this->page_current )
			{
				$this->progress[] = array( 'step_doing', $page );
				$curr_reached = 1;
			}
			else if( $curr_reached )
			{
				$this->progress[] = array( 'step_notdone', $page );
			}
			else 
			{
				$this->progress[] = array( 'step_done', $page );
			}
			
		}
		
		$this->page_template();
	}
	
	/***************************************************************
	 *
	 * HTML TEMPLATE FUNCTIONS
	 *
	 **************************************************************/
	
	// ------------------------------------------------------------
	// Main Template
	// ------------------------------------------------------------
	function page_template()
	{
echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
		<title>Мастер обновления продуктов Invision Power Services </title>
		<style type='text/css' media='all'>
			@import url('install.css');
		</style>
		<script type='text/javascript'>
			//<![CDATA[
		  		if (top.location != self.location) { top.location = self.location }
				var use_enhanced_js = 1;
			//]]>
		</script>
		<script type="text/javascript" src='ips_xmlhttprequest.js'></script>	
	</head>
	<body>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<form id='install-form' action='index.php{$this->next_action}' method='post'>
		<input type='hidden' name='saved_data' value='{$this->saved_data}'>
		
		<div id='ipswrapper'>
		    <div class='main_shell'>

		 	    <h1><img src='images/package_icon.gif' align='absmiddle' /> Инструмент обновления Invision Power Services</h1>
		 	    <div class='content_shell'>
		 	        <div class='package'>
		 	            <div>
		 	                <div class='install_info'>
		 	                    <h3>{$this->install_pages[$this->page_current]}</h3>
		 	                    		 	                    
    		 	                <ul id='progress'>

EOF;

foreach( $this->progress as $p )
{
echo "<li class='{$p[0]}'>{$p[1]}</li>";
}

echo <<<EOF
    		 	                </ul>
    		 	            </div>
		 	            
    		 	            <div class='content_wrap'>
    		 	                <div style='border-bottom: 1px solid #939393; padding-bottom: 4px;'>
    		 	                    <div class='float_img'>
    		 	                        <img src='images/box.gif' />
    		 	                    </div>

    		 	                    <div style='vertical-align: middle'>
    		 	                        <h2>Обновление {$this->product_name}</h2>
    		 	                        <!--<strong>{$this->product_version}</strong>-->
    		 	                    </div>
    		 	                </div>
    		 	                <div style='clear:both'></div>

        		 	            {$this->page_content}        		 	          
            		 	        <br />        		 	            
    		 	            </div>
		 	            </div>
		 	            <br clear='all' />
    
		 	            <div class='hr'></div>
		 	            <div style='padding-top: 17px; padding-right: 15px; padding-left: 15px'>
		 	                <div style='float: left'>
		 	                    <input type='button' class='nav_button' value='Прекратить обновление' onclick="window.location='index.php';return false;">
		 	                </div>

		 	                <div style='float: right'>
EOF;

if( ! $this->hide_next )
{
if( $this->next_action == 'disabled' )
{
echo <<<EOF
		 	                    <input type='submit' class='nav_button' value='Обновление не завершено...' disabled='disabled'>
EOF;
}
else if( $this->in_error == 1 )
{
echo <<<EOF
		 	                    <input type='submit' class='nav_button' value='Все равно продолжить'>
EOF;
}
else 
{
echo <<<EOF
		 	                    <input type='submit' class='nav_button' value='Дальше >'>
EOF;
}
}

echo <<<EOF
						</div>
		 	            </div>
		 	            <div style='clear: both;'></div>
		 	            <div class='copyright'>
		 	                &copy; 
EOF;
echo date("Y");
echo <<<EOF
 Invision Power Services, Inc. & IBResource, LTD
		 	            </div>
		 	        </div>

		 	    </div>
    		</div>
    	</div>
    	
		</form>
	
	</body>
</html>
EOF;
	}
	
	// ------------------------------------------------------------
	// Login Page Template
	// ------------------------------------------------------------	
	function login_page( $msg='' )
	{
		$output = "";
		if ( $msg )
		{
			$extra = "<div class='warning'>
		        		<div style='float: left; margin-right: 7px; margin-left: 5px'><img src='images/warning.gif' /></div>
						<p>{$msg}</p>
					  </div><br />";
		}


$output .= <<<EOF
        		 	            <br />
        		 	            <div>
        		 	                <div style='float: left; margin-right: 7px; margin-left: 5px;'>
        		 	                    <img src='images/wizard.gif' align='absmiddle' />
        		 	                </div>
        		 	                <div>
        		 	                    Вас приветствует мастер обновления {$this->product_name}. Он поможет вам обновить установленный продукт до последней версии.
        		 	                </div>
        		 	            </div>
    <br/>{$extra}
    <h3>Обязательная проверка &mdash; авторизация</h3>
    Для работы с мастером вам необходимо войти в систему под учетной записью с правами администратора.<br />
    <br />
	<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'>
		<tr>
			<td width='40%'  valign='middle'>Ваш 
EOF;

if( $this->ipsclass->login_type == 'username' )
{
$output .= <<<EOF
логин
EOF;
}
else
{
$output .= <<<EOF
e-mail
EOF;
}
$output .= <<<EOF
:</td>
			<td width='60%'  valign='middle'><input type='text' style='width:100%' name='username' value='' class='sql_form'></td>
		</tr>
		<tr>
			<td width='40%'  valign='middle'>Ваш пароль:</td>
			<td width='60%'  valign='middle'><input type='password' style='width:100%' name='password' value='' class='sql_form'></td>
		</tr>
	</table>
EOF;

	return $output;
	}
	
	// ------------------------------------------------------------
	// Overview Page Template
	// ------------------------------------------------------------	
	function overview_page( $current_version, $summary )
	{
return <<<EOF
        		 	            <br />
        		 	            <div>
        		 	                <div style='float: left; margin-right: 7px; margin-left: 5px;'>
        		 	                    <img src='images/wizard.gif' align='absmiddle' />
        		 	                </div>
        		 	                <div>
        		 	                    Вас приветствует мастер обновления {$this->product_name}. Он поможет вам обновить установленный продукт до последней версии.
        		 	                </div>
        		 	            </div>
    <br/>
    <h3>Информация</h3>
    Текущая версия: $current_version.<br />
    Мастер осуществит $summary<br />
    <br />

EOF;
	}

	// ------------------------------------------------------------
	// EULA Page Template
	// ------------------------------------------------------------
	function eula_page( $eula )
	{
return <<<EOF

<script language='javascript'>

check_eula = function()
{
	if( document.getElementById( 'eula' ).checked == true )
	{
		return true;
	}
	else
	{
		alert( 'Вы должны принять пользовательское соглашение, прежде чем продолжить обновление' );
		return false;
	}
}

document.getElementById( 'install-form' ).onsubmit = check_eula;

</script>

Пожалуйста прочитайте и примите пользовательское соглашение.<br /><br />

        		 	            
        		 	            <div class='eula'>
									$eula        		 	                
                                </div>
                                <input type='checkbox' name='eula' id='eula'><strong> Я принимаю</strong>


EOF;
	}
	
	
	// ------------------------------------------------------------
	// Install Page Splash Template
	// ------------------------------------------------------------		
	function install_page( $show_manual=0 )
	{
		$output = "";
		
$output = <<<EOF
<br />
Мастер собрал все данные и готов начать обновление {$this->product_name}.  Нажмите <strong>Запустить обновление</strong> для запуска автоматического процесса обновления продукта!<br /><br />
    <ul id='links'>
        <li><img src='images/link.gif' align='absmiddle' /> <input type='checkbox' name='helpfile' id='helpfile' value='1' checked='checked' /> Обновить разделы помощи, если будут найдены различия.</li>
EOF;

if( $show_manual == 1 )
{
$output .= <<<EOF
        <li><img src='images/link.gif' align='absmiddle' /> <input type='checkbox' name='man' id='man' value='1' /> Показывать SQL запросы для ручного обновления (для больших форумов). <b>Внимание:</b> вам будет необходимо выполнить все SQL запросы, выводимые мастером, в mysql shell (коммандной строке). Если вы плохо представляете себе что это, пожалуйста обратитесь в <a href="http://www.ibresource.ru/clientarea/" target="blank">нашу службу поддержки</a>.</li>
EOF;
}

$output .= <<<EOF
    </ul>

<br /><br />
        		 	            
        		 	            <div style='float: right'>
        		 	                <input type='submit' class='nav_button' value='Запустить обновление...'>
        		 	            </div>
EOF;

		return $output;
	}
	
	// ------------------------------------------------------------
	// Install Page Refresh Template
	// ------------------------------------------------------------		
	function install_page_refresh( $output=array() )
	{
$HTML = <<<EOF
<script type='text/javascript'>
//<![CDATA[
setTimeout("form_redirect()",2000);

function form_redirect()
{
	document.getElementById( 'install-form' ).submit();
}
//]]>
</script>
    		 	                <ul id='auto_progress'>
EOF;

foreach( $output as $l )
{
$HTML .= <<<EOF
    		 	                    <li><img src='images/check.gif' align='absmiddle' /> $l</li>
EOF;
}

$HTML .= <<<EOF
    		 	                </ul>
								<br />
								<div style='float: right'>
									<input type='submit' class='nav_button' value='Нажмите сюда, если вас не переместило автоматически' />
								</div>
EOF;

		return $HTML;
	}
	
	// ------------------------------------------------------------
	// Install Progress Screen
	// ------------------------------------------------------------		
	function install_progress( $line )
	{
$HTML = <<<EOF
    		 	                <ul id='auto_progress'>
EOF;

foreach( $line as $l )
{
$HTML .= <<<EOF
    		 	                    <li><img src='images/check.gif' align='absmiddle' /> $l</li>
EOF;
}

$HTML .= <<<EOF
    		 	                </ul>
EOF;

		return $HTML;
	}
	
	
	// ------------------------------------------------------------
	// Install Skin Revert
	// ------------------------------------------------------------		
	function install_template_skinrevert( $skin_name="" )
	{
$HTML = <<<EOF
		<br /><h3><b>Удаление изменений в стилях</b></h3><br />
		Во время обновления часто возникает необходимость вносить изменения в шаблоны стиля чтобы исправить ошибки и добавить новые возможности.<br /><br />
		Если вы не захотите удалять изменения в шаблонах стиля, мастер обновления не сможет внести изменения. Однако если вы удалите изменения в стилях 
		вы <i><b>потеряете</b></i> всю проделанную работу по изменению стандартного стиля.<br /><br />
		По-этому мы рекоммендуем вам удалять изменения в стиле только в том случае, если изменений было мало. Если же у вас собственный стиль или большое количество 
		изменений в шаблонах от модификаций мы рекоммендуем отключить опцию удаления изменений и воспользоваться инструментом сравнения стилей для добавления изменений
		в ручную.<br /><br />
		
		<h3>Вы желаете удалить изменения в шаблонах стиля &laquo;<b>{$skin_name}</b>&raquo;?</h3>
            <ul id='links'>
                <li><img src='images/link.gif' align='absmiddle' /> <input type='radio' name='do' value='all' /> Удалить изменения в шаблонах всех стилей</li>
                <li><img src='images/link.gif' align='absmiddle' /> <input type='radio' name='do' value='1' /> Удалить изменения в шаблонах стиля &laquo;{$skin_name}&raquo;</li>
                <li><img src='images/link.gif' align='absmiddle' /> <input type='radio' name='do' value='none' /> Не удалять изменения в шаблонах всех стилей</li>
                <li><img src='images/link.gif' align='absmiddle' /> <input type='radio' name='do' value='0' /> Не удалять изменения в шаблонах стиля &laquo;{$skin_name}&raquo;</li>
            </ul>
EOF;

		return $HTML;
	}	
	
	// ------------------------------------------------------------
	// Install Done Screen
	// ------------------------------------------------------------		
	function install_done( $url )
	{
$HTML .= <<<EOF
        		 	            <br />
        		 	            <img src='images/install_done.gif' align='absmiddle' />&nbsp;&nbsp;<span class='done_text'>Обновление завершено!</span><br /><br />
        		 	            Поздравляем, обновление <a href='$url'>{$this->product_name}</a> завершено!<br /><br />
        		 	            Вы сейчас должны зайти в админцентр и запустить утилиты &laquo;Обновление содержимого сообщений&raquo; и &laquo;Обновление информации о прикрепленных файлах&raquo; в меню НАСТРОЙКИ -> Пересчет и обновление.
        		 	            По желанию вы также можете запустить утилиты между 2.1 и 2.2 в разделе &laquo;Инструменты очистки&raquo;.
        		 	            <br /><br />Ниже приведены несколько полезных ссылок, которые помогут в вашей работе.<br /><br /><br />
        		 	            <h3>Полезные ссылки</h3>
        		 	            <ul id='links'>
        		 	                <li><img src='images/link.gif' align='absmiddle' /> <a href='http://external.iblink.ru/clientarea'>Клиент-центр</a></li>
        		 	                <li><img src='images/link.gif' align='absmiddle' /> <a href='http://external.iblink.ru/docs-ipb'>Документация</a></li>
        		 	                <li><img src='images/link.gif' align='absmiddle' /> <a href='http://www.ibresource.ru/forums/'>Форумы компании</a></li>
        		 	                <li><img src='images/link.gif' align='absmiddle' /> <a href='http://external.iblink.ru/wiki'>Invison Power Book</a></li>
587                                 <li><img src='images/link.gif' align='absmiddle' /> <a href='http://external.ipslink.com/ipboard22/landing/?p=forums'>Форумы компании IPS</a></li>
        		 	            </ul>
EOF;
		return $HTML;
	}
	
	// ------------------------------------------------------------
	// Warning Message Template
	// ------------------------------------------------------------	
	function warning( $messages )
	{
$HTML = <<<EOF
<br />
    <div class='warning'>
        <div style='float: left; margin-right: 7px; margin-left: 5px'><img src='images/warning.gif' /></div>
EOF;

foreach( $messages as $msg )
{
	$HTML .= "<p>$msg</p>";	
}

$HTML .= <<<EOF
    </div><br />
   
EOF;

		$this->append( $HTML );
	}
}

?>