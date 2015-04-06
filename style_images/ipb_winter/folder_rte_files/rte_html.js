
function write_rte_html()
{
	//-------------------------------
	// Set up DDs
	//-------------------------------
	
	format_select =  '<!--<select class="rteselectbox" id="formatblock" onchange="do_select(this.id);">';
	format_select += '	<option value="">[Style]</option>';
	format_select += '	<option value="<p>">Paragraph</option>';
	format_select += '	<option value="<h1>">Heading 1 <h1></option>';
	format_select += '	<option value="<h2>">Heading 2 <h2></option>';
	format_select += '	<option value="<h3>">Heading 3 <h3></option>';
	format_select += '	<option value="<h4>">Heading 4 <h4></option>';
	format_select += '	<option value="<h5>">Heading 5 <h5></option>';
	format_select += '	<option value="<h6>">Heading 6 <h6></option>';
	format_select += '	<option value="<address>">Address <ADDR></option>';
	format_select += '	<option value="<pre>">Formatted <pre></option>';
	format_select += '</select>-->';
	
	font_select  = '<select class="rteselectbox" id="fontname" onchange="do_select(this.id)">';
	font_select += write_fontface_box();
	font_select += '</select>';
		
	size_select  = '<select class="rteselectbox" unselectable="on" id="fontsize" onchange="do_select(this.id);">';
	size_select += write_fontsize_box();
	size_select += '</select>';
	
	//-------------------------------
	// Set up buttons
	//-------------------------------
	
	button_removeform  = '<td><div id="do_removeformat"><img class="rteimage"  src="' + g_imagesPath + 'removeformat.gif" width="25" height="24" alt="'+js_tt_noformat+'" title="'+js_tt_noformat+'" onClick="format_text(\'removeformat\', \'\')"></div></td>';
	
	button_SEP         = '<td><img class="rteVertSep" src="' + g_imagesPath + 'blackdot.gif" width="1" height="20" border="0" alt=""></td>';
	button_DOTS        = '<td><img class="rteVertSep" src="' + g_imagesPath + 'rte_dots.gif" width="3" height="15" border="0" alt=""></td>';
	
	button_bold        = '<td><div id="do_bold"><img class="rteimage"  src="' + g_imagesPath + 'bold.gif" width="25" height="24" alt="'+js_tt_bold+'" title="'+js_tt_bold+'" onClick="format_text(\'bold\', \'\')"></div></td>';
	button_italic      = '<td><div id="do_italic"><img class="rteimage" src="' + g_imagesPath + 'italic.gif" width="25" height="24" alt="'+js_tt_italic+'" title="'+js_tt_italic+'" onClick="format_text(\'italic\', \'\')"></div></td>';
	button_underline   = '<td><div id="do_underline"><img class="rteimage" src="' + g_imagesPath + 'underline.gif" width="25" height="24" alt="'+js_tt_underline+'" title="'+js_tt_underline+'" onClick="format_text(\'underline\', \'\')"></div></td>';
	button_strike      = '<td><div id="do_strikethrough"><img class="rteimage" src="' + g_imagesPath + 'strike.gif" width="25" height="24" alt="'+js_tt_strike+'" title="'+js_tt_strike+'" onClick="format_text(\'strikethrough\', \'\')"></div></td>';
	
	/* Not required in IPB */
	//button_table       = '<td><div><img class="rteimage" src="' + g_imagesPath + 'rte_table.gif"  alt="Insert Table" title="Insert Table" onClick="launch_table();"></div></td>';
	//button_div         = '<td><div><img class="rteimage" src="' + g_imagesPath + 'rte_div.gif"    alt="Insert DIV" title="Insert DIV" onClick="launch_div();"></div></td>';
	//button_subscript   = '<td><div id="do_subscript"><img class="rteimage" src="' + g_imagesPath + 'subscript.gif" width="25" height="24" alt="Subscript" title="Subscript" onClick="format_text(\'subscript\', \'\')"></div></td>';
	//button_supscript   = '<td><div id="do_superscript"><img class="rteimage" src="' + g_imagesPath + 'superscript.gif" width="25" height="24" alt="Superscript" title="Superscript" onClick="format_text(\'superscript\', \'\')"></div></td>';
	//button_hr          = '<td><div><img class="rteimage" src="' + g_imagesPath + 'hr.gif" width="25" height="24" alt="Horizontal Rule" title="Horizontal Rule" onClick="format_text(\'inserthorizontalrule\', \'\')"></div></td>';
	//button_just_full   = '<td><div id="do_justifyfull"><img class="rteimage" src="' + g_imagesPath + 'justifyfull.gif" width="25" height="24" alt="Justify Full" title="Justify Full" onclick="format_text(\'justifyfull\', \'\')"></div></td>';

    //button_smilies       = '<td><div id="popsmilies"><img class="rteimage" src="' + g_imagesPath + 'hr.gif" width="25" height="24" alt="Horizontal Rule" title="Horizontal Rule" onClick="show_smilies();"></div></td>';
	
    
	button_just_left   = '<td><div id="do_justifyleft"><img class="rteimage" src="' + g_imagesPath + 'left_just.gif" width="25" height="24" alt="'+js_tt_left+'" title="'+js_tt_left+'" onClick="format_text(\'justifyleft\', \'\')"></div></td>';
	button_just_center = '<td><div id="do_justifycenter"><img class="rteimage" src="' + g_imagesPath + 'centre.gif" width="25" height="24" alt="'+js_tt_center+'" title="'+js_tt_center+'" onClick="format_text(\'justifycenter\', \'\')"></div></td>';
	button_just_right  = '<td><div id="do_justifyright"><img class="rteimage" src="' + g_imagesPath + 'right_just.gif" width="25" height="24" alt="'+js_tt_right+'" title="'+js_tt_right+'" onClick="format_text(\'justifyright\', \'\')"></div></td>';
	
	button_list_num    = '<td><div><img class="rteimage" src="' + g_imagesPath + 'numbered_list.gif" width="25" height="24" alt="'+js_tt_list+'" title="'+js_tt_list+'" onClick="format_text(\'insertorderedlist\', \'\')"></div></td>';
	button_list_bullet = '<td><div><img class="rteimage" src="' + g_imagesPath + 'list.gif" width="25" height="24" alt="'+js_tt_list+'" title="'+js_tt_list+'" onClick="format_text(\'insertunorderedlist\', \'\')"></div></td>';
	
	button_outdent     = '<td><div id="do_outdent"><img class="rteimage" src="' + g_imagesPath + 'outdent.gif" width="25" height="24" alt="'+js_tt_outdent+'" title="'+js_tt_outdent+'" onClick="format_text(\'outdent\', \'\')"></div></td>';
	button_indent      = '<td><div id="do_indent"><img class="rteimage" src="' + g_imagesPath + 'indent.gif" width="25" height="24" alt="'+js_tt_indent+'" title="'+js_tt_indent+'" onClick="format_text(\'indent\', \'\')"></div></td>';
	
	button_col_for     = '<td><div id="forecolor"><div><img class="rteimage" src="' + g_imagesPath + 'textcolor.gif" width="25" height="24" alt="'+js_tt_font_col+'" title="'+js_tt_font_col+'" onClick="format_text(\'forecolor\', \'\')"></div></div></td>';
	button_col_back    = '<!--<td><div id="hilitecolor"><div><img class="rteimage" src="' + g_imagesPath + 'bgcolor.gif" width="25" height="24" alt="'+js_tt_back_col+'" title="'+js_tt_back_col+'" onClick="format_text(\'hilitecolor\', \'\')"></div></div></td>-->';
	
	button_link        = '<td><div><img class="rteimage" src="' + g_imagesPath + 'hyperlink.gif" width="25" height="24" alt="'+js_tt_link+'" title="'+js_tt_link+'" onClick="format_text(\'createlink\')"></div></td>';
	button_image       = '<td><div><img class="rteimage" src="' + g_imagesPath + 'image.gif" width="25" height="24" alt="'+js_tt_image+'" title="'+js_tt_image+'" onClick="add_image()"></div></td>';
	button_email       = '<td><div><img class="rteimage" src="' + g_imagesPath + 'email.gif" width="25" height="24" alt="'+js_tt_email+'" title="'+js_tt_email+'" onClick="add_email()"></div></td>';
	
	button_quote       = '<td><div><img class="rteimage" src="' + g_imagesPath + 'quote.gif" width="25" height="24" alt="'+js_tt_quote+'" title="'+js_tt_quote+'" onClick="wrap_tags(\'[quote]\',\'[/quote]\')"></div></td>';
	
	button_cut         = '<td><div><img class="rteimage" src="' + g_imagesPath + 'cut.gif" width="25" height="24" alt="'+js_tt_cut+'" title="'+js_tt_cut+'" onClick="format_text(\'cut\')"></div></td>';
	button_copy        = '<td><div><img class="rteimage" src="' + g_imagesPath + 'copy.gif" width="25" height="24" alt="'+js_tt_copy+'" title="'+js_tt_copy+'" onClick="format_text(\'copy\')"></div></td>';
	button_paste       = '<td><div><img class="rteimage" src="' + g_imagesPath + 'paste.gif" width="25" height="24" alt="'+js_tt_paste+'" title="'+js_tt_paste+'" onClick="format_text(\'paste\')"></div></td>';
	button_undo        = '<td><div><img class="rteimage" src="' + g_imagesPath + 'undo.gif" width="25" height="24" alt="'+js_tt_undo+'" title="'+js_tt_undo+'" onClick="format_text(\'undo\')"></div></td>';
	button_redo        = '<td><div><img class="rteimage" src="' + g_imagesPath + 'redo.gif" width="25" height="24" alt="'+js_tt_redo+'" title="'+js_tt_redo+'" onClick="format_text(\'redo\')"></div></td>';
	
	if (isIE)
	{
		button_spell = '<td><div><img class="rteimage" src="' + g_imagesPath + 'spellcheck.gif" width="25" height="24" alt="Spell Check" title="Spell Check" onClick="checkspell()"></td>' + button_SEP;
	}
	else
	{
		button_spell = "";
	}
	
	//-------------------------------
	// Set up Button bar 1
	//-------------------------------
	
	bar_buttons_one  = '<table style="width:'+g_tablewidth+'px" class="rtebuttonbar1" cellpadding="2" cellspacing="0" id="Buttons1" width="' + g_tablewidth + '">';
	bar_buttons_one += "<tr>\n";
	bar_buttons_one	+= " <td width='1%' align='left' nowrap='nowrap'>\n";
	bar_buttons_one += "  <table cellpadding='0' cellspacing='0' width='100%'>\n";
	bar_buttons_one += "   <tr>\n";
	bar_buttons_one += "    " + button_DOTS + button_removeform + "\n"  + button_SEP  + "\n";
	bar_buttons_one += "    <td>&nbsp;" + font_select   + "</td>\n";
	bar_buttons_one += "    <td>" + size_select   + "</td>\n";
	bar_buttons_one += "\n"  + button_SEP  + button_outdent + "\n" + button_indent;
	bar_buttons_one	+= "   </tr>\n";
	bar_buttons_one	+= "  </table>\n";
	bar_buttons_one += " </td>\n";
	bar_buttons_one += " <td width='98%'>&nbsp;</td>\n";
	bar_buttons_one += " <td width='1%' nowrap='nowrap' align='right'>\n";
	bar_buttons_one += "  <table cellpadding='0' cellspacing='0' width='100%'>\n";
	bar_buttons_one += "   <tr>\n";
	bar_buttons_one += "    " + button_spell + "\n" + button_cut + "\n" + button_copy + "\n" + button_paste + "\n" + button_SEP
				    +  "\n"   + button_undo + "\n" + button_redo;
	bar_buttons_one	+= "   </tr>\n";
	bar_buttons_one	+= "  </table>\n";
	bar_buttons_one += " </td>\n";
	bar_buttons_one	+= "</tr>\n";
	bar_buttons_one	+= "</table>\n";
	
	//-------------------------------
	// Set up Button bar 2
	//-------------------------------
	
	bar_buttons_two  = '<table style="width:'+g_tablewidth+'px" class="rtebuttonbar2" cellpadding="2" cellspacing="0" id="Buttons2" width="' + g_tablewidth + '">';
	bar_buttons_two += "<tr>\n";
	bar_buttons_two	+= " <td width='1%' align='left' nowrap='nowrap'>\n";
	bar_buttons_two += "  <table cellpadding='0' cellspacing='0' width='100%'>\n";
	bar_buttons_two += "   <tr>\n";
	bar_buttons_two += "    " + button_DOTS + button_bold + "\n" + button_italic + "\n" + button_underline + "\n" + button_strike + "\n" + button_SEP
				    +  "\n"   + button_col_for + "\n" + button_col_back + "\n" + button_SEP
				    +  "\n"   + button_link + "\n" + button_image + "\n" + button_email + "\n"  + button_SEP  + button_quote; // + button_SEP + button_smilies + "\n";
	bar_buttons_two	+= "   </tr>\n";
	bar_buttons_two	+= "  </table>\n";
	bar_buttons_two += " </td>\n";
	bar_buttons_two += " <td width='98%'>&nbsp;</td>\n";
	bar_buttons_two += " <td width='1%' nowrap='nowrap' align='right'>\n";
	bar_buttons_two += "  <table cellpadding='0' cellspacing='0' width='100%'>\n";
	bar_buttons_two += "   <tr>\n";
	bar_buttons_two += "    " + button_just_left + "\n" + button_just_center + "\n" + button_just_right + "\n" + button_SEP
					+  "\n"   + button_list_num + "\n" + button_list_bullet;
	bar_buttons_two	+= "   </tr>\n";
	bar_buttons_two	+= "  </table>\n";
	bar_buttons_two += " </td>\n";
	bar_buttons_two	+= "</tr>\n";
	bar_buttons_two	+= "</table>\n";
	
	//-------------------------------
	// Start output
	//-------------------------------
	
	document.writeln('<div class="rteDiv">');
	
	if ( g_buttons == true )
	{
		document.writeln( bar_buttons_one );
		document.writeln( bar_buttons_two );
	}
		
	document.writeln('<iframe class="rteiframe" frameborder="0" style=" style="margin:0px;width:' + g_width + 'px;height:' + g_height + 'px" id="' + g_rte + '" name="' + g_rte + '" width="' + g_width + 'px" height="' + g_height + 'px" src="' + g_includesPath + 'blank.html"></iframe>');
	
	//-------------------------------
	// Bottom tabs
	//-------------------------------

	document.writeln('<table cellpadding="0" cellspacing="0" style="padding:0px;margin:0px;height:30px;width:'+ g_tablewidth+'px" width="' + g_tablewidth + '">');
	document.writeln('<tr><td style="padding:0px;margin:0px;" align="left" width="1%" valign="top" nowrap="nowrap">');
	document.writeln( '<input type="button" value=" + " onclick=\'rte_window_resize( "' + g_rte + '", 100 );\' id="rtesizeplus"  class="rtebottombutton" />');
	document.writeln( '<input type="button" value=" - " onclick=\'rte_window_resize( "' + g_rte + '",-100 );\' id="rtesizeminus" class="rtebottombutton" />');
	document.writeln('</td><td style="padding:0px;margin:0px;" align="right" width="99%">');
	document.writeln('</td></tr></table>');
	
	//document.writeln('<div style="visibility:hidden;display:none;position:absolute;" id="smiliestable">boo</div>');
	
	document.writeln('<input type="hidden" id="hdn_rte_content" name="' + g_rte + '" value="">');
	document.writeln('<iframe width="154" height="104" id="cp" src="' + g_includesPath + 'palette.html" marginwidth="0" marginheight="0" scrolling="no" style="visibility:hidden; display: none; position: absolute;"></iframe>');
	
	//-------------------------------
	// DEBUG?
	//-------------------------------
	
	if ( g_DEBUG )
	{
		document.writeln("<textarea id='debugmsg' style='width:100%;height:100px' name='debugmsg'></textarea>");
	}
	
	document.writeln('</div>');

}