<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board 2.2.2
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2006 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|        http://www.ibresource.ru/products/invisionpowerboard/
|   Time: Tuesday 27th of March 2007 07:00:16 AM
|   Release: 936d62a249c0dc8fd81438cdbc911b98
|   Licence Info: http://www.invisionboard.com/?license
|                  http://www.ibresource.ru/license
+---------------------------------------------------------------------------
|   INVISION POWER BOARD �� �������� ���������� ����������� ������������!
|   ����� �� �� ����������� Invision Power Services
|   ����� �� ������� IBResource (http://www.ibresource.ru)
+---------------------------------------------------------------------------
|   > $Date: 2006-09-22 05:28:54 -0500 (Fri, 22 Sep 2006) $
|   > $Revision: 567 $
|   > $Author: matt $
+---------------------------------------------------------------------------
|
|   > Admin Template functions library
|   > Script written by Matt Mecham
|   > Date started: 19th November 2003
|
|   > DBA Checked: Tue 25th May 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_ACP' ) )
{
    print "<h1>�������� ����</h1> � ��� ��� ������� � ��������� ����� �����. ���� �� ��������� ����������, ���������, ��� �� ������ �������� 'admin.php'.";
	exit();
}

class admin_template_functions
{

	var $menu_fontchange  = "";
	var $menu_sizechange  = "";
	var $menu_backchange  = "";
	var $menu_fontcolor   = "";
	var $menu_widthchange = "";
	var $menu_highchange  = "";
	var $default_css      = "";
	var $type			  = "";

	//-----------------------------------------
	// Constructor
	//-----------------------------------------

	function admin_template_functions()
	{
		$this->default_css = "font-family:Verdana;font-size:11pt;color:black;background-color:white;border:3px outset #555";
	}

	//-----------------------------------------
	// Build generic editor
	//-----------------------------------------

	function build_generic_editor_area( $data )
	{
		$return = "";

		$return .= $this->html_build_editor_top();

		$return .= "
					<script language='javascript'>
					<!--
      				var template_bit_ids = 'txt{$data['textareaname']}';
					//-->
					</script>
					<div class='tableborder'>
		            <div class='tablesubheader'>
				    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
				    <tr>
				     <td width='1%' align='left' valign='middle'>
				         <input type='button' value='��������� ����'  class='realdarkbutton' title='������� �������������� ����� ������� � ������� ����' onclick=\"pop_win('section={$data['section']}&act={$data['act']}&code=floateditor&id={$data['textareaname']}', 'Float', 800, 400)\">&nbsp;
				     </td>
				     <td width='95%' align='left' valign='middle'>&nbsp;<b>{$data['title']}</b></td>
				     <td width='5% align='right'  valign='middle' nowrap='nowrap' ><!--TOP.RIGHT--></td>
				   </tr>
				   <!--TR.ROW-->
				   </table>
				   </div>";

		$return .= "<div align='center' style='padding:2px;'>".$this->ipsclass->adskin->form_textarea("txt{$data['textareaname']}", $data['textareainput'], '', '', 'none', "txt{$data['textareaname']}", $this->default_css )."</div>\n";

		$return .= $this->html_build_editor_bottom() . "</div>";

		return $return;

	}

	//-----------------------------------------
	// Build editor text area table
	//-----------------------------------------

	function build_editor_area( $html, $template=array(), $img="" )
	{
		$return = "";

		$edit_prefs = $this->type == 'single' ? $this->editor_prefs_dropdown : '';
		$close_box  = $this->type == 'single' ? "<input type='button' class='realbutton' style='color:red' onclick='parent.template_edit_close(\"{$template['suid']}\")' value=' X ' />" : '';

		$template['func_data'] = str_replace( "'", '&#039;', $template['func_data'] );

		$spiffy_diffy = "<div align='center' style='position:absolute;width:99%;display:none;text-align:center' id='dv_{$template['suid']}'>
						 <table cellspacing='0' width='500' align='center' cellpadding='6' style='background:#EEE;border:2px outset #555;'>
						 <tr>
						  <td align='center' valign='top'>
						   <b>����������� �����: ���������� �������</b><br />�������� ��� ����, ���� �� �� ������, ��� ��� ��������.<br />����������� ������� ��� ���������� ���������� ����������.
						   <br /><input class='textinput' name='funcdata_{$template['suid']}' value='{$template['func_data']}' type='text' size='50' />
						   <br /><br />
						   <input type='button' class='realdarkbutton' value='��������� � �������' onclick=\"togglediv('dv_{$template['suid']}');\" />
						  </td>
						 </tr>
						 </table>
						</div>";

		$return .= <<<EOF
					<div class='tableborder'>
		            <div class='tablesubheader'>
				    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
				    <tr>
				     <td width='1%' align='left' valign='middle'>{$close_box}</td>
				     <td width='19%' align='left' valign='middle' nowrap='nowrap'>
				         {$edit_prefs}
				     </td>
				     <td width='75%' align='left' valign='middle'>&nbsp;{$img}<b>{$template['easy_name']}</b></td>
				     <td width='5% align='right' nowrap='nowrap' valign='middle'>
				      <input type='hidden' id='edited-{$template['suid']}' name='edited-{$template['suid']}' value='0' />
				      <input type='submit' name='submit-{$template['suid']}' value='��������� ������' id='sb-t{$template['suid']}' class='realdarkbutton' />
				      <input type='button' value='��������� ����'  class='realbutton' title='������� �������������� ����� ������� � ��������� ����' onclick="pop_win('section=lookandfeel&act=templ&code=floateditor&id={$template['suid']}', '��������� ���� {$template['suid']}', 800, 400)">
					  <img id="tmpl-{$template['suid']}" src='{$this->ipsclass->skin_acp_url}/images/filebrowser_action.gif' border='0' alt='Options' class='ipd' />
					  &nbsp;
					</td>
				   </tr>
				   </table>
				   $spiffy_diffy
				   </div>
				   <script type="text/javascript">
EOF;
		if ( $this->type == 'single' OR $this->type == 'multiple' )
		{
			$return .= "\ntry { parent.template_bit_onload(); } catch(e) { }\n";
		}

		$return .= <<<EOF
					menu_build_menu(
					"tmpl-{$template['suid']}",
					new Array(
							   img_item   + " <a href='#' onclick=\"menu_action_close(); toggleview('dv_{$template['suid']}')\"; return false;'>�������� ����������</a>",
							   img_item   + " <a href='#' onclick='menu_action_close(); pop_win(\"act=rtempl&code=macro_one&suid={$template['suid']}\", \"MacroWindow\", 400, 200); return false;'>�������� ��������</a>",
							   img_item   + " <a href='#' onclick='menu_action_close(); pop_win(\"act=rtempl&code=compare&suid={$template['suid']}\", \"CompareWindow\", 500,400); return false;'>�������� ������</a>",
							   img_item   + " <a href='#' onclick='menu_action_close(); parent.template_bit_restore(\"{$template['suid']}\"); return false;'>�������� ��� ���������</a>",
							   img_item   + " <a href='#' onclick='menu_action_close(); pop_win(\"act=rtempl&code=preview&suid={$template['suid']}&type=html\", \"OriginalPreview\", 400,400); return false;'>�������� ���������</a>"
							 ) );
				  </script>
EOF;

		$return .= "<div align='center' style='padding:2px;'>".$this->ipsclass->adskin->form_textarea("txt{$template['suid']}", $html, 60, 5, 'none', "t{$template['suid']}", $this->default_css, 'onkeypress="parent.template_bit_changed(\'t'.$template['suid'].'\')"' )."</div>\n";

		$return .= "</div>";

		return $return;

	}

	//-----------------------------------------
	// Build JS for floated window
	//-----------------------------------------

	function build_editor_area_floated($no_buttons=0)
	{
		$return = "<form name='theform'>";

		$return .= $this->html_build_editor_top();

		$return .= "<div class='tableborder'>
		            <div class='tablesubheader' align='right'>";

		if ( $no_buttons == 0 )
		{
			$return .= "
				      <input type='button' value='�����'  class='realbutton' title='����� �� �������' onClick='pop_win(\"act=rtempl&code=search&set_id={$this->ipsclass->input['id']}&type=html\", \"Search\", 900,600)'>
					  <input type='button' value='�������'  class='realbutton' title='�������� �������� �� ��������� ������' onClick='pop_win(\"act=rtempl&code=macro_one&suid={$this->ipsclass->input['id']}\", \"MacroWindow\", 400, 200)'>
					  <input type='button' value='��������'  class='realbutton' title='�������� ���������� ���� ������� ������� � �������������� ����������' onClick='pop_win(\"act=rtempl&code=compare&suid={$this->ipsclass->input['id']}&pop=1\", \"CompareWindow\", 500,400)'>
					  <input type='button' value='��������'  class='realbutton' title='�������� ��� ��������� �������, �� ���� �������� ��� � ��������������� ������������ ����' onClick='template_bit_restore(); return false;'>
					  <input type='button' value='��������' class='realbutton' title='�������� ������������� ��������������� �������� �������' onClick='pop_win(\"act=rtempl&code=preview&suid={$this->ipsclass->input['id']}&type=html\", \"OriginalPreview\", 400,400)'>
				   ";
		}

		$return .= "</div>";

		$return .= "<div align='center' style='padding:2px;'>".$this->ipsclass->adskin->form_textarea("templatebit", $html, $this->ipsclass->vars['tx'], $this->ipsclass->vars['ty'], 'none', "templatebit", $this->default_css )."</div>\n";

		$return .= "</div>";

		$return .= "<script type='text/javascript'>
				   		var template_id = '{$this->ipsclass->input['id']}';
				   		var template_bit  = eval(\"opener.document.theform.txt\"+template_id+\".value\");
				   		document.theform.templatebit.value = template_bit;
				   		var template_bit_ids = 'templatebit';

				   		function saveandclose()
				   		{
				   			eval(\"opener.document.theform.txt\"+template_id+\".value = document.theform.templatebit.value\");
				   			window.close();
				   		}

						function template_bit_restore( )
						{
							var edit_box_obj    = document.theform.templatebit;

							if ( template_bit )
							{
								if ( confirm(\"Are you sure you want to restore the template?\\nALL UNSAVED CHANGES WILL BE LOST!\") )
								{
									edit_box_obj.value     = template_bit;
								}
							}
						}
				   </script>
				   ";

		$return .= $this->html_build_editor_bottom();

		$return .= "<br /><div class='tableborder'><div class='catrow2' align='center' style='padding:4px;'><input type='button' onclick='saveandclose()' value='����������� � ����������� ���� � ������� ����' class='realdarkbutton' /></div></div></form>";

		$this->ipsclass->html = $return;

		$this->ipsclass->admin->print_popup();

	}

	//-----------------------------------------
	// Build editor preferences menus
	//-----------------------------------------

	function build_editor_pref_menus()
	{
		$this->menu_fontchange =  "<select name='fontchange' class='smalldropdown'>".
								   "<option value='monaco'>Monaco</option>".
								   "<option value='courier'>Courier</option>".
								   "<option value='verdana'>Verdana</option>".
								   "<option value='arial'>Arial</option>".
								   "</select>";

		$this->menu_sizechange =  "<select name='sizechange' class='smalldropdown'>".
								   "<option value='8pt'>8 �������</option>".
								   "<option value='9pt'>9 �������</option>".
								   "<option value='10pt'>10 �������</option>".
								   "<option value='11pt'>11 �������</option>".
								   "<option value='12pt'>12 �������</option>".
								   "</select>";

		$this->menu_backchange =  "<select name='backchange' class='smalldropdown'>".
								   "<option value='black'>������</option>".
								   "<option value='white'>�����</option>".
								   "<option value='#EEEEEE'>�����������</option>".
								   "<option value='gray'>�����</option>".
								   "</select>";

		$this->menu_fontcolor  =  "<select name='fontcolor' class='smalldropdown'>".
								   "<option value='black'>������</option>".
								   "<option value='white'>�����</option>".
								   "<option value='blue'>�����</option>".
								   "<option value='lightgreen'>�������������</option>".
								   "<option value='green'>�������</option>".
								   "<option value='darkgreen'>������������</option>".
								   "<option value='gray'>�����</option>".
								   "</select>";

		$this->menu_widthchange = "<select name='widthchange' class='smalldropdown'>".
								   "<option value='100%'>100%</option>".
								   "<option value='90%'>90%</option>".
								   "<option value='80%'>80%</option>".
								   "<option value='70%'>70%</option>".
								   "<option value='60%'>60%</option>".
								   "<option value='50%'>50%</option>".
								   "</select>";

		//-----------------------------------------
		// Not single?
		//-----------------------------------------

		if ( $this->type != 'single' AND $this->type != 'multiple' )
		{
			$this->menu_highchange  = "<select name='highchange' class='smalldropdown'>".
									   "<option value='50px'>50 ��������</option>".
									   "<option value='100px'>100 ��������</option>".
									   "<option value='200px'>200 ��������</option>".
									   "<option value='300px'>300 ��������</option>".
									   "<option value='400px'>400 ��������</option>".
									   "<option value='500px'>500 ��������</option>".
									   "<option value='600px'>600 ��������</option>".
									   "<option value='700px'>700 ��������</option>".
									   "<option value='800px'>800 ��������</option>".
									   "<option value='900px'>900 ��������</option>".
									   "<option value='1000px'>1000 ��������</option>".
									   "</select>";
		}
		else
		{
			$this->menu_highchange = "<select name='highchange' class='smalldropdown' style='color:gray'><option value='-'>����������</option></span>";
		}

		if ( $cookie = $this->ipsclass->my_getcookie( 'acpeditorprefs' ) )
		{
			list( $font, $size, $bg, $fc, $width, $height ) = explode( "," ,$cookie );
		}
		else
		{
			//-----------------------------------------
			// Decent defaults
			//-----------------------------------------

			$font   = 'monaco';
			$size   = '8pt';
			$bg     = 'white';
			$fc     = 'black';
			$width  = '100%';
			$height = '300px';
		}

		//-----------------------------------------
		// Force decent height if single
		//-----------------------------------------

		if ( $this->type == 'single' OR $this->type == 'multiple' )
		{
			$height = '300px';
		}

		//-----------------------------------------
		// Show..
		//-----------------------------------------

		$this->default_css  = "font-family:$font;font-size:$size;color:$fc;background-color:$bg;width:$width;height:$height;border:3px outset #555";

		$this->menu_fontchange   = preg_replace( "/(option value='".preg_quote($font)."')/"   , "\\1 selected='selected'", $this->menu_fontchange  );
		$this->menu_sizechange   = preg_replace( "/(option value='".preg_quote($size)."')/"   , "\\1 selected='selected'", $this->menu_sizechange  );
		$this->menu_backchange   = preg_replace( "/(option value='".preg_quote($bg)."')/"     , "\\1 selected='selected'", $this->menu_backchange  );
		$this->menu_fontcolor    = preg_replace( "/(option value='".preg_quote($fc)."')/"     , "\\1 selected='selected'", $this->menu_fontcolor   );
		$this->menu_widthchange  = preg_replace( "/(option value='".preg_quote($width)."')/"  , "\\1 selected='selected'", $this->menu_widthchange );
		$this->menu_highchange   = preg_replace( "/(option value='".preg_quote($height)."')/" , "\\1 selected='selected'", $this->menu_highchange  );


		$this->editor_prefs_dropdown = "<div class='fauxbutton' style='padding:4px;color:black;white-space:nowrap;font-weight:bold;float:left' id='tmpl-editor-prefs'>��������� ��������� <img src='{$this->ipsclass->skin_acp_url}/images/icon_open.gif' border='0' style='vertical-align:top' /></div>";

	}


	function html_build_editor_top()
	{
		$this->build_editor_pref_menus();

		if ( $this->type == 'single' )
		{
			return "<input type='hidden' name='changed_bits' id='changed_bits' /><div class='tableborder'><div>";
		}

		$close_box  = $this->type == 'multiple' ? "<input type='button' class='realbutton' style='color:red' onclick='parent.template_edit_close(\"{$template['suid']}\")' value=' X ' />" : '';

		return <<<EOF
				<div class='tableborder'>
				<div class='tableheaderalt' align='left' style='height:30px'>

				  <div style='float:right'>{$close_box} <!--TOPRIGHT--></div>
				  <div>{$this->editor_prefs_dropdown}</div>
				</div>
				<div class='tablepad'><!--BEFORETEXTAREA-->
EOF;
	}

	function html_build_editor_bottom()
	{
		return <<<EOF
				<script type="text/javascript">
				  menu_build_menu(
				  'tmpl-editor-prefs',
				  new Array( "<table cellpadding='4' cellspacing='0' width='200' border='0'>"+
							  "<tr>"+
							   "<td nowrap='nowrap'>�����</td>"+
							   "<td width='100%'>{$this->menu_fontchange}</td>"+
							  "</tr>"+
							  "<tr>"+
							   "<td nowrap='nowrap'>������ ������</td>"+
							   "<td width='100%'>{$this->menu_sizechange}</td>"+
							  "</tr>"+
							  "<tr>"+
							   "<td nowrap='nowrap'>���� ������</td>"+
							   "<td width='100%'>{$this->menu_fontcolor}</td>"+
							  "</tr>"+
							  "<tr>"+
							   "<td nowrap='nowrap'>���</td>"+
							   "<td width='100%'>{$this->menu_backchange}</td>"+
							  "</tr>"+
							  "<tr>"+
							   "<td nowrap='nowrap'>������</td>"+
							   "<td width='100%'>{$this->menu_widthchange}</td>"+
							  "</tr>"+
							  "<tr>"+
							   "<td nowrap='nowrap'>������</td>"+
							   "<td width='100%'>{$this->menu_highchange}</td>"+
							  "</tr>"+
							  "<tr>"+
							   "<td colspan='2' align='center'>"+
							   "<input type='button' value='��������' class='realbutton' onclick=\"menu_action_close(); changefont();\" />"+
							   "</td>"+
							  "</tr>"+
							  "</table>"
							) );
				 </script>
				 <!--IPB.EDITORBOTTOM--></div>\n</div>
EOF;
	}


}





?>