var ie_ptags_to_newlines=true;
var IPS_editor=new Array();
var buttons_update=new Array("bold","italic","underline","justifyleft","justifycenter","justifyright","insertorderedlist","insertunorderedlist");
var ips_primary_colors=new Array('#000000','#A0522D','#556B2F','#006400','#483D8B','#000080','#4B0082','#2F4F4F','#8B0000','#FF8C00','#808000','#008000','#008080','#0000FF','#708090','#696969','#FF0000','#F4A460','#9ACD32','#2E8B57','#48D1CC','#4169E1','#800080','#808080','#FF00FF','#FFA500','#FFFF00','#00FF00','#00FFFF','#00BFFF','#9932CC','#C0C0C0','#FFC0CB','#F5DEB3','#FFFACD','#98FB98','#AFEEEE','#ADD8E6','#DDA0DD','#FFFFFF');
var ips_primary_fonts=new Array("Arial","Arial Black","Arial Narrow","Book Antiqua","Century Gothic","Comic Sans MS","Courier New","Franklin Gothic Medium","Garamond","Georgia","Impact","Lucida Console","Lucida Sans Unicode","Microsoft Sans Serif","Palatino Linotype","Tahoma","Times New Roman","Trebuchet MS","Verdana");
var ips_primary_sizes=new Array(1,2,3,4,5,6,7);
var ips_format_items={'cmd_subscript':'Sub-script','cmd_superscript':'Super-script','cmd_strikethrough':'Strikethrough'};
var ips_format_item_images={'cmd_subscript':'rte-subscript.gif','cmd_superscript':'rte-superscript.gif','cmd_strikethrough':'rte-strike.gif'};
ips_language_array={};
function ips_text_editor(editor_id,mode,use_bbcode,file_path,initial_text)
{
this.editor_id=editor_id;
this.is_rte=mode;
this.initialized=false;
this.buttons=new Array();
this.fonts=new Array();
this.state=new Array();
this.text_obj=document.getElementById(this.editor_id+'_textarea');
this.control_obj=document.getElementById(this.editor_id+'_controls');
this.font_obj=document.getElementById(this.editor_id+'_out_fontname');
this.size_obj=document.getElementById(this.editor_id+'_out_fontsize');
this.special_obj=document.getElementById(this.editor_id+'_out_special');
this.format_obj=document.getElementById(this.editor_id+'_out_format');
this.mainbar=document.getElementById(this.editor_id+'_main-bar');
this.use_menus=(typeof(ipsmenu)=='undefined'?false:true);
this.is_ie=is_ie;
this.is_moz=is_moz;
this.is_opera=is_opera;
this.is_safari=is_safari;
this.file_path=file_path?file_path:global_rte_includes_url;
this.font_state=null;
this.size_state=null;
this.use_bbcode=use_bbcode;
this.open_brace=this.use_bbcode?'[':'<';this.close_brace=this.use_bbcode?']':'>';
this.allow_advanced=this.use_bbcode?0:1;
this.ips_frame_html='';this.popups=new Array();
this.char_set=global_rte_char_set?global_rte_char_set:'UTF-8';
this.forum_fix_ie_newlines=0;
this.emoticon_window_id='';
this.is_loading=0;
this.hidden_objects=new Array();
this.history_pointer=-1;
this.history_recordings=new Array();
this._showing_html=0;
this._ie_cache=null;
this.current_bar_object=null;
this.ips_special_items={};
ips_format_items['cmd_subscript']=ips_language_array['js_rte_sub']?ips_language_array['js_rte_sub']:'Sub-script';
ips_format_items['cmd_superscript']=ips_language_array['js_rte_sup']?ips_language_array['js_rte_sup']:'Super-script';
ips_format_items['cmd_strikethrough']=ips_language_array['js_rte_strike']?ips_language_array['js_rte_strike']:'Strikethrough';
ips_language_array['emos_show_all']=ips_language_array['emos_show_all']?ips_language_array['emos_show_all']:'Show All';
this.init=function()
{
if(this.initialized)
{
return;
}
this.control_obj.style.display='';
try
{
document.getElementById(this.editor_id+'_wysiwyg_used').value=parseInt(this.is_rte);
}
catch(err)
{
}
this.ips_frame_html=this.get_frame_html();
this.editor_set_content(initial_text);
this.editor_set_functions();
this.editor_set_controls();
this.initialized=true;};
this.get_frame_html=function(){var ips_frame_html="";
ips_frame_html+="<html id=\""+this.editor_id+"_html\">\n";
ips_frame_html+="<head>\n";ips_frame_html+="<meta http-equiv=\"content-type\" content=\"text/html; charset="+this.char_set+"\" />";
ips_frame_html+="<style type='text/css' media='all'>\n";
ips_frame_html+="body {\n";
ips_frame_html+="	background: #FFFFFF;\n";
ips_frame_html+="	margin: 0px;\n";
ips_frame_html+="	padding: 4px;\n";
ips_frame_html+="	font-family: Verdana, arial, sans-serif;\n";
ips_frame_html+="	font-size: 10pt;\n";ips_frame_html+="}\n";ips_frame_html+="</style>\n";ips_frame_html+="</head>\n";ips_frame_html+="<body>\n";ips_frame_html+="{:content:}\n";ips_frame_html+="</body>\n";ips_frame_html+="</html>";return ips_frame_html;};
this.editor_check_focus=function()
{
	if(!this.editor_window.has_focus)
	{
		if(this.is_opera)
		{
			this.editor_window.focus();
		}
		this.editor_window.focus();
	}
};
this.editor_set_controls=function(){var controls=new Array();var _c=0;if(!this.control_obj){return;}var items=this.control_obj.getElementsByTagName("DIV");for(var i=0;i<items.length;i++){if((items[i].className=='rte-normal'||items[i].className=='rte-menu-button'||items[i].className=='rte-normal-menubutton')&&items[i].id){controls[controls.length]=items[i].id;}}for(var i=0;i<controls.length;i++){var control=document.getElementById(controls[i]);if(control.className=='rte-normal'){this.init_editor_button(control);}else if(control.className=='rte-menu-button'||control.className=='rte-normal-menubutton'){this.init_editor_popup_button(control);}}ipsclass.set_unselectable(this.control_obj);};this.init_editor_popup_button=function(obj){if(!this.use_menus){return;}ipsmenu.register(obj.id);obj.cmd=obj.id.replace(new RegExp('^'+this.editor_id+'_popup_(.+?)$'),'$1');obj.editor_id=this.editor_id;obj.state=false;this.buttons[obj.cmd]=obj;if(obj.cmd=='fontname'){this.fontout=this.font_obj;this.fontout.innerHTML=obj.title;this.fontoptions={'':this.fontout};for(var option in ips_primary_fonts){var div=document.createElement('div');div.id=this.editor_id+'_fontoption_'+ips_primary_fonts[option];div.style.width=this.fontout.style.width;div.style.display='none';div.innerHTML=ips_primary_fonts[option];this.fontoptions[ips_primary_fonts[option]]=this.fontout.parentNode.appendChild(div);}}else if(obj.cmd=='fontsize'){this.sizeout=this.size_obj;this.sizeout.innerHTML=obj.title;this.sizeoptions={'':this.sizeout};for(var option in ips_primary_sizes){var div=document.createElement('div');div.id=this.editor_id+'_sizeoption_'+ips_primary_sizes[option];div.style.width=this.sizeout.style.width;div.style.display='none';div.innerHTML=ips_primary_sizes[option];this.sizeoptions[ips_primary_sizes[option]]=this.sizeout.parentNode.appendChild(div);}}obj._onmouseover=obj.onmouseover;obj._onclick=obj.onclick;obj.onmouseover=obj.onmouseout=obj.onclick=ips_editor_events.prototype.popup_button_onmouseevent;ipsmenu.menu_registered[obj.id]._open=ipsmenu.menu_registered[obj.id].open;ipsmenu.menu_registered[obj.id].open=ips_editor_events.prototype.popup_button_show;};this.init_editor_menu=function(obj){var menu=document.createElement('div');menu.id=this.editor_id+'_popup_'+obj.cmd+'_menu';menu.className='rte-popupmenu';menu.style.display='none';menu.style.cursor='default';menu.style.padding='3px';menu.style.width='auto';menu.style.height='auto';menu.style.overflow='hidden';switch(obj.cmd){case 'fontsize':for(var i in ips_primary_sizes){var option=document.createElement('div');option.style.paddingTop=ips_primary_sizes[i]*2+'px';option.style.paddingBottom=ips_primary_sizes[i]*2+'px';option.innerHTML='<font size="'+ips_primary_sizes[i]+'">'+ips_primary_sizes[i]+'</font>';option.className='rte-menu-size';option.title=ips_primary_sizes[i];option.cmd=obj.cmd;option.editor_id=this.editor_id;option.onmouseover=option.onmouseout=option.onmouseup=option.onmousedown=ips_editor_events.prototype.menu_option_onmouseevent;option.onclick=ips_editor_events.prototype.font_format_option_onclick;menu.style.width=this.size_obj.style.width;menu.appendChild(option);}break;case 'fontname':for(var i in ips_primary_fonts){var option=document.createElement('div');option.innerHTML='<font face="'+ips_primary_fonts[i]+'">'+ips_primary_fonts[i]+'</font>';option.className='rte-menu-face';option.title=ips_primary_fonts[i];option.cmd=obj.cmd;option.editor_id=this.editor_id;option.onmouseover=option.onmouseout=option.onmouseup=option.onmousedown=ips_editor_events.prototype.menu_option_onmouseevent;option.onclick=ips_editor_events.prototype.font_format_option_onclick;menu.style.width=this.font_obj.style.width;menu.appendChild(option);}break;case 'special':for(var i in this.ips_special_items){var option=document.createElement('div');var img=(typeof this.ips_special_items[i][1]!='undefined')?'<img src="'+global_rte_images_url+'/'+this.ips_special_items[i][1]+'" style="vertical-align:middle" border="" /> ':'';option.innerHTML=img+this.ips_special_items[i][0];option.className='rte-menu-face';option.cmd='module_load',option.loader_key=i.replace('cmd_loader_','');option.editor_id=this.editor_id;option.onmouseover=option.onclick=option.onmouseout=option.onmouseup=option.onmousedown=ips_editor_events.prototype.special_onmouse_event;menu.style.width=this.special_obj.style.width;menu.appendChild(option);}break;case 'format':for(var i in ips_format_items){var option=document.createElement('div');var img=(typeof ips_format_item_images[i]!='undefined')?'<img src="'+global_rte_images_url+'/'+ips_format_item_images[i]+'" style="vertical-align:middle" border="" /> ':'';option.innerHTML=img+ips_format_items[i];option.className='rte-menu-face';option.cmd=i.replace('cmd_','');option.editor_id=this.editor_id;option.onmouseover=option.onclick=option.onmouseout=option.onmouseup=option.onmousedown=ips_editor_events.prototype.special_onmouse_event;menu.style.width='130px';menu.appendChild(option);}break;case 'emoticons':var table=document.createElement('table');table.cellPadding=0;table.cellSpacing=0;table.border=0;if(this.is_ie){table.style.paddingRight='15px';}var i=0;var perrow=3;var tr=table.insertRow(-1);var td=tr.insertCell(-1);td.colSpan=perrow;td.align='center';td.cellPadding=0;td.innerHTML='<div class="rte-menu-emo-header"><a href="#" style="text-decoration:none" onclick="return show_all_emoticons(\''+this.editor_id+'\')">'+ips_language_array['emos_show_all']+'</a></div>';for(var emo in ips_smilie_items){if(i%perrow==0){var tr=table.insertRow(-1);}i++;var div=document.createElement('div');var _tmp=ips_smilie_items[emo].split(",");var img='<img src="'+global_rte_emoticons_url+'/'+_tmp[1]+'" style="vertical-align:middle" border="0" id="smid_'+_tmp[0]+'" /> ';div.innerHTML=img;var option=tr.insertCell(-1);option.className='rte-menu-emo';option.appendChild(div);option.cmd=obj.cmd;option.editor_id=this.editor_id;option.id=this.editor_id+'_emoticon_'+_tmp[0];option.emo_id=_tmp[0];option.emo_image=_tmp[1];option.emo_code=emo;option.onmouseover=option.onmouseout=option.onmouseup=option.onmousedown=ips_editor_events.prototype.menu_option_onmouseevent;option.onclick=ips_editor_events.prototype.emoticon_onclick;}if(i>0){menu.style.width='auto';if(this.is_ie){menu.style.paddingRight='15px';}menu.style.height='200px';menu.style.overflow='auto';menu.style.overflowX='hidden';menu.appendChild(table);break;}else{menu.style.width='auto';menu.style.height='40px';menu.style.overflow='auto';menu.appendChild(table);break;}case 'forecolor':case 'backcolor':var table=document.createElement('table');table.cellPadding=0;table.cellSpacing=0;table.border=0;var i=0;for(var hex in ips_primary_colors){if(i%8==0){var tr=table.insertRow(-1);}i++;var div=document.createElement('div');div.style.backgroundColor=ips_primary_colors[hex];div.innerHTML='&nbsp;';var option=tr.insertCell(-1);option.className='rte-menu-color';option.appendChild(div);option.cmd=obj.cmd;option.editor_id=this.editor_id;option.colorname=ips_primary_colors[hex];option.id=this.editor_id+'_color_'+ips_primary_colors[hex];option.onmouseover=option.onmouseout=option.onmouseup=option.onmousedown=ips_editor_events.prototype.menu_option_onmouseevent;option.onclick=ips_editor_events.prototype.color_cell_onclick;}menu.style.overflow='visible';menu.appendChild(table);break;}this.popups[obj.cmd]=this.control_obj.appendChild(menu);ipsclass.set_unselectable(menu);};this.init_editor_button=function(obj){obj.cmd=obj.id.replace(new RegExp('^'+this.editor_id+'_cmd_(.+?)$'),'$1');obj.editor_id=this.editor_id;this.buttons[obj.cmd]=obj;obj.state=false;obj.mode='normal';obj.real_type='button';obj.onclick=ips_editor_events.prototype.button_onmouse_event;obj.onmousedown=ips_editor_events.prototype.button_onmouse_event;obj.onmouseover=ips_editor_events.prototype.button_onmouse_event;obj.onmouseout=ips_editor_events.prototype.button_onmouse_event;};this.set_menu_context=function(obj,state){if(this._showing_html){return false;}switch(obj.state){case true:{this.editor_set_ctl_style(obj,'menubutton','down');break;}default:{switch(state){case 'mouseout':{this.editor_set_ctl_style(obj,'menubutton','normal');break;}case 'mousedown':{this.editor_set_ctl_style(obj,'menubutton','down');break;}case 'mouseup':case 'mouseover':{this.editor_set_ctl_style(obj,'menubutton','hover');break;}}}}};this.set_button_context=function(obj,state,type){if(this._showing_html){return false;}if(typeof type=='undefined'){type='button';}if(state=='mousedown'&&(obj.cmd=='undo'||obj.cmd=='redo')){return false;}switch(obj.state){case true:{switch(state){case 'mouseout':{this.editor_set_ctl_style(obj,'button','selected');break;}case 'mouseover':case 'mousedown':case 'mouseup':{this.editor_set_ctl_style(obj,type,'down');break;}}break;}default:{switch(state){case 'mouseout':{this.editor_set_ctl_style(obj,type,'normal');break;}case 'mousedown':{this.editor_set_ctl_style(obj,type,'down');break;}case 'mouseover':case 'mouseup':{this.editor_set_ctl_style(obj,type,'hover');break;}}break;}}};this.editor_set_ctl_style=function(obj,type,mode){if(obj.mode!=mode){var extra='';if(type=='menu'){extra='-menu';}else if(type=='menubutton'){extra='-menubutton';}extra+=obj.colorname?'-color':'';extra+=obj.emo_id?'-emo':'';obj.mode=mode;try{switch(mode){case "normal":{obj.className='rte-normal'+extra;}break;case "hover":{obj.className='rte-hover'+extra;}break;case "selected":case "down":{obj.className='rte-selected'+extra;}break;}}catch(e){}}};this.format_text=function(e,command,arg){e=ipsclass.cancel_bubble(e,true);if(command.match( /resize_/)){this.resize_editorbox(command.replace( /resize_(up|down)/,"$1"));}if(command.match( /switcheditor/i)){switch_editor_mode(this.editor_id);}if(!this.is_rte){if(command!='redo'){this.history_record_state(this.editor_get_contents());}}this.editor_check_focus();if(this[command]){var return_val=this[command](e);}else{try{var return_val=this.apply_formatting(command,false,(typeof arg=='undefined'?true:arg));}catch(e){var return_val=false;}}if(!this.is_rte){if(command!='undo'){this.history_record_state(this.editor_get_contents());}}this.set_context(command);this.editor_check_focus();return return_val;};this.spellcheck=function(){if(this.is_moz||this.is_opera){return false;}try{if(this.rte_mode){var tmpis=new ActiveXObject("ieSpell.ieSpellExtension").CheckDocumentNode(this.editor_document);}else{var tmpis=new ActiveXObject("ieSpell.ieSpellExtension").CheckAllLinkedDocuments(this.editor_document);}}catch(exception){if(exception.number==-2146827859){if(confirm(ips_language_array['js_rte_erroriespell']?ips_language_array['js_rte_erroriespell']:"ieSpell not detected.  Click Ok to go to download page.")){window.open("http://www.iespell.com/download.php","DownLoad");}}else{alert(ips_language_array['js_rte_errorliespell']?ips_language_array['js_rte_errorliespell']:"Error Loading ieSpell: Exception "+exception.number);}}};this.module_remove_item=function(key){var tmp=this.ips_special_items;this.ips_special_items=new Array();for(var i in tmp){if(i!='cmd_loader_'+key){this.ips_special_items[i]=tmp[i];}}};this.module_add_item=function(key,text,image,evalcode){this.ips_special_items['cmd_loader_'+key]=new Array(text,image,evalcode);};this.module_load=function(obj,e,loader_key){if(!loader_key){return false;}e=ipsclass.cancel_bubble(e,true);this.editor_check_focus();this.preserve_ie_range();menu_action_close();var _m=loader_key.match( /\{(.+?)\}$/);var _args='';try{if(_m[1]){_args=_m[1];}}catch(internetexplorer){}if(typeof this.ips_special_items['cmd_loader_'+loader_key][2]!="undefined"){eval(this.ips_special_items['cmd_loader_'+loader_key][2]);return false}else{this.module_show_control_bar(loader_key,_args);}};this.module_show_control_bar=function(type,_args){if(!this.control_obj){return;}type=type.replace( /(\{.+?\})$/,'');_args=(typeof(_args)!='undefined')?_args:'';if(this.current_bar_object){this.module_remove_control_bar();}var newdiv=document.createElement('div');newdiv.id=this.editor_id+'_htmlblock_'+type+'_menu';newdiv.style.display='';newdiv.className='rte-buttonbar';newdiv.style.zIndex=parseInt(this.control_obj.style.zIndex)+1;newdiv.style.position='absolute';newdiv.style.width='220px';newdiv.style.height='400px';newdiv.style.top=ipsclass.get_obj_toppos(this.mainbar)+'px';var _left=ipsclass.get_obj_leftpos(this.mainbar)-(parseInt(newdiv.style.width)+10);if(_left<1){_left=ipsclass.get_obj_leftpos(this.mainbar);}newdiv.style.left=ipsclass.get_obj_leftpos(this.mainbar)-(parseInt(newdiv.style.width)+10)+'px';newdiv.style.left=_left+'px';var tmpheight=parseInt(newdiv.style.height)-16;newdiv.innerHTML=this.module_wrap_html_panel("<iframe id='"+this.editor_id+'_iframeblock_'+type+'_menu'+"' src='"+global_rte_includes_url+"module_"+type+".php?editorid="+this.editor_id+"&"+_args+"' frameborder='0' style='text-align:left;background:transparent;border:0px;overflow:auto;width:98%;height:"+tmpheight+"px'></iframe>");this.mainbar.appendChild(newdiv);if(is_ie){document.getElementById(this.editor_id+'_iframeblock_'+type+'_menu').style.backgroundColor='transparent';document.getElementById(this.editor_id+'_iframeblock_'+type+'_menu').allowTransparency=true;}ipsclass.set_unselectable(newdiv);Drag.init(document.getElementById(this.editor_id+'_pallete-handle'),newdiv);this.current_bar_object=newdiv;};this.module_remove_control_bar=function(){if(!this.current_bar_object){return;}this.mainbar.removeChild(this.current_bar_object);this.current_bar_object=null;};this.module_wrap_html_panel=function(html){var newhtml="";newhtml+=" <div id='"+this.editor_id+"_pallete-wrap'>";newhtml+="   <div id='"+this.editor_id+"_pallete-main'>";newhtml+="    <div class='rte-cb-bg' id='"+this.editor_id+"_pallete-handle'>";newhtml+="			<div align='left'><img id='"+this.editor_id+"_cb-close-window' src='"+global_rte_images_url+"rte-cb-close.gif' alt='' class='ipd' border='0' /></div>";newhtml+="	   </div>";newhtml+="    <div>"+html+"</div>";newhtml+="  </div>";newhtml+=" </div>";return newhtml;};this.resize_editorbox=function(direction){var inc_value=100;var current_height=parseInt(this.editor_box.style.height);var new_height=0;current_height=current_height?current_height:300;if(current_height>=50){if(direction=='up'){new_height=current_height-inc_value;}else{new_height=current_height+inc_value;}if(new_height>249){this.editor_box.style.height=new_height+'px';ipsclass.my_setcookie('ips_rte_height',new_height,1);}}};this.htmlspecialchars=function(html){html=html.replace(/&/g,"&amp;");html=html.replace(/"/g,"&quot;");html=html.replace(/</g,"&lt;");html=html.replace(/>/g,"&gt;");return html;};this.unhtmlspecialchars=function(html){html=html.replace( /&quot;/g,'"');html=html.replace( /&lt;/g,'<');html=html.replace( /&gt;/g,'>');html=html.replace( /&amp;/g,'&');return html;};this.strip_html=function(html){html=html.replace( /<\/?([^>]+?)>/ig,"");return html;};this.strip_empty_html=function(html){html=html.replace('<([^>]+?)></([^>]+?)>',"");return html;};this.clean_html=function(t){if(t==""||typeof t=='undefined'){return t;}t=t.replace( /<br>/ig,"<br />");t=t.replace( /<p>(\s+?)?<\/p>/ig,"");t=t.replace( /<p><hr \/><\/p>/ig,"<hr />");t=t.replace( /<p>&nbsp;<\/p><hr \/><p>&nbsp;<\/p>/ig,"<hr />");t=t.replace( /<(p|div)([^&]*)>/ig,"\n<$1$2>\n");t=t.replace( /<\/(p|div)([^&]*)>/ig,"\n</$1$2>\n");t=t.replace( /<br \/>(?!<\/td)/ig,"<br />\n");t=t.replace( /<\/(td|tr|tbody|table)>/ig,"</$1>\n");t=t.replace( /<(tr|tbody|table(.+?)?)>/ig,"<$1>\n");t=t.replace( /<(td(.+?)?)>/ig,"\t<$1>");t=t.replace( /<p>&nbsp;<\/p>/ig,"<br />");t=t.replace( /<br \/>/ig,"<br />\n");t=t.replace( /<br>/ig,"<br />\n");t=t.replace( /<td><br \/>\n<\/td>/ig,"<td><br /></td>");return t;};this.preserve_ie_range=function(){if(this.is_ie){this._ie_cache=this.is_rte?this.editor_document.selection.createRange():document.selection.createRange();}};
this.wrap_tags_lite=function(start_text,close_text){selected_text=this.get_selection();selected_text=(selected_text===false)?'':new String(selected_text);this.insert_text(start_text+selected_text+close_text);return false;};this.destruct=function(){for(var i in this.buttons){var _type=(this.buttons[i].real_type=='button')?'button':'menubutton';this.editor_set_ctl_style(this.buttons[i],_type,'normal');}if(this.fontoptions){for(var i in this.fontoptions){if(i!=''){this.fontoptions[i].parentNode.removeChild(this.fontoptions[i]);}}this.fontoptions[''].style.display='';}if(this.sizeoptions){for(var i in this.sizeoptions){if(i!=''){this.sizeoptions[i].parentNode.removeChild(this.sizeoptions[i]);}}this.sizeoptions[''].style.display='';}for(var i in this.hidden_objects){try{document.getElementById(i).style.display='';}catch(me){}}};
	this.wrap_tags=function(tag_name,has_option,selected_text)
	{
		var tag_close=tag_name;
		if(!this.use_bbcode)
		{
			switch(tag_name)
			{
				case 'url':
					tag_name='a href';
					tag_close='a';
				break;
				case'email':
					tag_name='a href';
					tag_close='a';
					has_option='mailto:'+has_option;
				break;
				case 'img':
					tag_name='img src';
					tag_close='';
				break;
				case 'font':
					tag_name='font face';
					tag_close='font';
				break;
				case 'size':
					tag_name='font size';
					tag_close='font';
				break;
				case 'color':
					tag_name='font color';
					tag_close='font';
				break;
				case 'background':
					tag_name='font bgcolor';
					tag_close='font';
				break;
				case 'indent':
					tag_name=tag_close='blockquote';
				break;
				case 'left':case 'right':case 'center':
					has_option=tag_name;
					tag_name='div align';tag_close='div';
				break;
			}
		}
		if(typeof selected_text=='undefined')
		{
			selected_text=this.get_selection();
			selected_text=(selected_text===false)?'':new String(selected_text);
		}
		if(has_option===true)
		{
			var option=prompt(ips_language_arrayp['js_rte_optionals']?ips_language_arrayp['js_rte_optionals']:"Enter the optional arguments for this tag",'');
			if(option)
			{
				var opentag=this.open_brace+tag_name+'="'+option+'"'+this.close_brace;
			}
			else
			{
				return false;
			}
		}
		else if(has_option!==false)
		{
			var opentag=this.open_brace+tag_name+'="'+has_option+'"'+this.close_brace;
		}
		else
		{
			var opentag=this.open_brace+tag_name+this.close_brace;
		}
		var closetag=this.open_brace+'/'+tag_close+this.close_brace;
		var text=opentag+selected_text+closetag;
		this.insert_text(text);
		return false;
	};
	this.history_record_state=function(content){if(this.history_recordings[this.history_pointer]!=content){this.history_pointer++;this.history_recordings[this.history_pointer]=content;if(typeof this.history_recordings[this.history_pointer+1]!='undefined'){this.history_recordings[this.history_pointer+1]=null;}}};this.history_time_shift=function(inc){var i=this.history_pointer+inc;if(i>=0&&this.history_recordings[i]!=null&&typeof this.history_recordings[i]!='undefined'){this.history_pointer+=inc;}};this.history_fetch_recording=function(){if(typeof this.history_recordings[this.history_pointer]!='undefined'&&this.history_recordings[this.history_pointer]!=null){return this.history_recordings[this.history_pointer];}else{return false;}};if(this.is_rte){this.editor_write_contents=function(text,do_init){if(text==''&&this.is_moz){text='<br />';}if(this.editor_document&&this.editor_document.initialized){this.editor_document.body.innerHTML=text;}else{if(do_init){this.editor_document.designMode='on';}this.editor_document=this.editor_window.document;this.editor_document.open('text/html','replace');this.editor_document.write(this.ips_frame_html.replace('{:content:}',text));this.editor_document.close();if(do_init){this.editor_document.body.contentEditable=true;this.editor_document.initialized=true;}}};this.editor_set_content=function(init_text){var iframe_obj=null;try{iframe_obj=document.getElementById(this.editor_id+'_iframe');}catch(error){}if(iframe_obj){this.editor_box=iframe_obj;}else{var iframe=document.createElement('iframe');if(this.is_ie&&window.location.protocol=='https:'){iframe.src=this.file_path+'/index.html';}this.editor_box=this.text_obj.parentNode.appendChild(iframe);this.editor_box.id=this.editor_id+'_iframe';this.editor_box.tabIndex=3;}if(!this.is_ie){this.editor_box.style.border='2px inset';}var test_height=parseInt(ipsclass.my_getcookie('ips_rte_height'));if(!isNaN(test_height)&&test_height>50){this.text_obj.style.height=test_height+'px';}this.editor_box.style.width=this.text_obj.style.width;this.editor_box.style.height=this.text_obj.style.height;this.editor_box.className=this.text_obj.className;this.text_obj.style.display='none';this.editor_window=this.editor_box.contentWindow;this.editor_document=this.editor_window.document;this.editor_write_contents((typeof init_text=='undefined'||!init_text?this.text_obj.value:init_text),true);this.editor_document.editor_id=this.editor_id;this.editor_window.editor_id=this.editor_id;this.editor_window.has_focus=false;if(this.use_bbcode){document.getElementById(this.editor_id+'_cmd_justifyfull').style.display='none';}};this.editor_set_functions=function(){this.editor_document.onmouseup=ips_editor_events.prototype.editor_document_onmouseup;this.editor_document.onkeyup=ips_editor_events.prototype.editor_document_onkeyup;this.editor_document.onkeydown=function(){if(IPS_editor[this.editor_id].forum_fix_ie_newlines&&IPS_editor[this.editor_id].is_ie&&IPS_editor[this.editor_id].editor_window.event.keyCode==13){var _test=new Array('Indent','Outdent','JustifyLeft','JustifyCenter','JustifyRight','InsertOrderedList','InsertUnorderedList');for(var i in _test){if(IPS_editor[this.editor_id].editor_window.document.queryCommandState(_test[i])){return true;}}var sel=IPS_editor[this.editor_id].editor_document.selection;var ts=IPS_editor[this.editor_id].editor_document.selection.createRange();var t=ts.htmlText.replace(/<p([^>]*)>(.*)<\/p>/i,'$2');if((sel.type=="Text"||sel.type=="None")){ts.pasteHTML("<br />"+t+"\n");}else{IPS_editor[this.editor_id].editor_document.innerHTML+="<br />\n";}IPS_editor[this.editor_id].editor_window.event.returnValue=false;ts.select();IPS_editor[this.editor_id].editor_check_focus();}};this.editor_window.onblur=ips_editor_events.prototype.editor_window_onblur;this.editor_window.onfocus=ips_editor_events.prototype.editor_window_onfocus;};this.set_context=function(cmd){if(this._showing_html){return false;}for(var i in buttons_update){var obj=document.getElementById(this.editor_id+'_cmd_'+buttons_update[i]);if(obj!=null){try{var state=this.editor_document.queryCommandState(buttons_update[i]);if(obj.state!=state){obj.state=state;this.set_button_context(obj,(obj.cmd==cmd?'mouseover':'mouseout'));}}catch(error){}}}this.button_set_font_context();this.button_set_size_context();};this.button_set_font_context=function(font_state){if(this._showing_html){return false;}if(this.buttons['fontname']){if(typeof font_state=='undefined'){font_state=this.editor_document.queryCommandValue('fontname');}switch(font_state){case '':{if(!this.is_ie&&window.getComputedStyle){font_state=this.editor_document.body.style.fontFamily;}}break;case null:{font_state='';}break;}if(font_state!=this.font_state){this.font_state=font_state;var fontword=font_state;var commapos=fontword.indexOf(",");if(commapos!=-1){fontword=fontword.substr(0,commapos);}fontword=fontword.toLowerCase();for(var i in this.fontoptions){this.fontoptions[i].style.display=(i.toLowerCase()==fontword?'':'none');}}}};this.button_set_size_context=function(size_state){if(this.buttons['fontsize']){if(typeof size_state=='undefined'){size_state=this.editor_document.queryCommandValue('fontsize');}switch(size_state){case null:case '':{if(this.is_moz){size_state=this.moz_convert_fontsize(this.editor_document.body.style.fontSize);if(!size_state){size_state='2';}}}break;}if(size_state!=this.size_state){this.size_state=size_state;for(var i in this.sizeoptions){this.sizeoptions[i].style.display=(i==this.size_state?'':'none');}}}};this.apply_formatting=function(cmd,dialog,argument){dialog=(typeof dialog=='undefined'?false:dialog);argument=(typeof argument=='undefined'?true:argument);if(this.is_ie&&this.forum_fix_ie_newlines){if(cmd=='justifyleft'||cmd=='justifycenter'||cmd=='justifyright'){var _a=cmd.replace("justify","");this.wrap_tags_lite("["+_a+"]","[/"+_a+"]");return true;}else if(cmd=='outdent'||cmd=='indent'||cmd=='insertorderedlist'||cmd=='insertunorderedlist'){this.editor_check_focus();var sel=this.editor_document.selection;var ts=this.editor_document.selection.createRange();var t=ts.htmlText.replace(/<p([^>]*)>(.*)<\/p>/i,'$2');if((sel.type=="Text"||sel.type=="None")){ts.pasteHTML(t+"<p />\n");}else{this.editor_document.body.innerHTML+="<p />";}}}this.editor_document.execCommand(cmd,dialog,argument);return false;};this.removeformat=function(e){this.apply_formatting('unlink',false,false);this.apply_formatting('removeformat',false,false);};this.editor_get_contents=function(){return this.editor_document.body.innerHTML;};this.get_selection=function(){var rng=this._ie_cache?this._ie_cache:this.editor_document.selection.createRange();if(rng.htmlText){return rng.htmlText;}else{var rtn='';for(var i=0;i<rng.length;i++){rtn+=rng.item(i).outerHTML;}}return rtn;};
	this.insert_text=function(text)
	{
		this.editor_check_focus();
		if(typeof(this.editor_document.selection)!='undefined'&&this.editor_document.selection.type!='Text'&&this.editor_document.selection.type!='None')
		{
			this.editor_document.selection.clear();
		}
		var sel=this._ie_cache?this._ie_cache:this.editor_document.selection.createRange();
		sel.pasteHTML(text);
		sel.select();
		this._ie_cache=null;
	};
		this.insert_emoticon=function(emo_id,emo_image,emo_code,event)
		{
		try{var _emo_url=global_rte_emoticons_url+"/"+emo_image;var _emo_html=' <img src="'+_emo_url+'" border="0" alt="" style="vertical-align:middle" emoid="'+this.unhtmlspecialchars(emo_code)+'" />';this.wrap_tags_lite(""+_emo_html,"");}catch(error){}if(IPS_editor[this.editor_id].emoticon_window_id!=''&&typeof(IPS_editor[this.editor_id].emoticon_window_id)!='undefined'){IPS_editor[this.editor_id].emoticon_window_id.focus();}};this.togglesource_cancel=function(){this.togglesource(true);};this.togglesource=function(no_replace){if(this._showing_html){var ta=document.getElementById(this.editor_id+'_htmlsource');var ba=document.getElementById(this.editor_id+'_html_control_bar');if(no_replace!==true){this.editor_document.body.innerHTML=ta.value;}this.editor_box.style.display='';this.control_obj.style.display='';ba.parentNode.removeChild(ba);ta.parentNode.removeChild(ta);this.togglesource_post_show_html();this._showing_html=0;}else{this._showing_html=1;this.togglesource_pre_show_html();var textarea=document.createElement('textarea');var new_ta=this.text_obj.parentNode.appendChild(textarea);new_ta.id=this.editor_id+'_htmlsource';new_ta.className=this.text_obj.className;new_ta.tabIndex=3;new_ta.style.width=this.text_obj.style.width;new_ta.style.height=this.text_obj.style.height;new_ta.value=this.clean_html(this.editor_get_contents());new_ta.focus();var new_div=document.createElement('DIV');new_div.id=this.editor_id+'_html_control_bar';new_div.className=this.control_obj.className;new_div.style.width=this.control_obj.style.width;new_div.style.height=this.control_obj.style.height;new_div.style.paddingBottom='8px';var savebutton=document.createElement('input');savebutton.className='rte-menu-button';savebutton.type='button';savebutton.value=' T�������� HTML ';savebutton.cmd='togglesource';savebutton.editor_id=this.editor_id;savebutton.onclick=ips_editor_events.prototype.button_onmouse_event;var cancelbutton=document.createElement('input');cancelbutton.className='rte-menu-button';cancelbutton.type='button';cancelbutton.value='+����� ';cancelbutton.cmd='togglesource_cancel';cancelbutton.editor_id=this.editor_id;cancelbutton.onclick=ips_editor_events.prototype.button_onmouse_event;new_div.appendChild(savebutton);new_div.appendChild(cancelbutton);this.control_obj.parentNode.appendChild(new_div);this.control_obj.style.display='none';this.editor_box.style.display='none';this.buttons['togglesource'].state=false;this.buttons['togglesource'].className='rte-normal';
		this.editor_check_focus();this.set_context();}};this.togglesource_pre_show_html=function(){};this.togglesource_post_show_html=function(){};this.update_for_form_submit=function(){this.text_obj.value=this.editor_get_contents();return true;};this.___OPERA_FUNCTIONS=function(){};if(this.is_opera){this._ORIGINAL_editor_set_content=this.editor_set_content;this.editor_set_content=function(initial_text){this._ORIGINAL_editor_set_content(initial_text);this.editor_document.body.style.height='95%';this.editor_document.addEventListener('keypress',ips_editor_events.prototype.editor_document_onkeypress,true);document.getElementById(this.editor_id+'_cmd_spellcheck').style.display='none';this.hidden_objects[this.editor_id+'_cmd_spellcheck']=1;if(this.use_bbcode){document.getElementById(this.editor_id+'_cmd_justifyfull').style.display='none';this.hidden_objects[this.editor_id+'_cmd_justifyfull']=1;}try{var _y=parseInt(window.pageYOffset);this.editor_document.execCommand("inserthtml",false,"-");this.editor_document.execCommand("undo",false,null);scroll(0,_y);}catch(error){}};this.insert_text=function(str){this.editor_document.execCommand('insertHTML',false,str);};this.get_selection=function(){var selection=this.editor_window.getSelection();this.editor_check_focus();var range=selection?selection.getRangeAt(0):this.editor_document.createRange();var lsserializer=document.implementation.createLSSerializer();return lsserializer.writeToString(range.cloneContents());};this.insert_emoticon=function(emo_id,emo_image,emo_code,event){this.editor_check_focus();try{var _emo_url=global_rte_emoticons_url+"/"+emo_image;this.editor_document.execCommand('InsertImage',false,_emo_url);var images=this.editor_document.getElementsByTagName('img');if(images.length>0){for(var i=0;i<=images.length;i++){if(images[i].src.match(new RegExp(_emo_url+"$"))){if(!images[i].getAttribute('emoid')){images[i].setAttribute('emoid',this.unhtmlspecialchars(emo_code));images[i].setAttribute('border','0');images[i].style.verticalAlign='middle';}}}}}catch(error){}};this.editor_set_functions=function(){this.editor_document.addEventListener('mouseup',ips_editor_events.prototype.editor_document_onmouseup,true);this.editor_document.addEventListener('keyup',ips_editor_events.prototype.editor_document_onkeyup,true);this.editor_window.addEventListener('focus',ips_editor_events.prototype.editor_window_onfocus,true);this.editor_window.addEventListener('blur',ips_editor_events.prototype.editor_window_onblur,true);};}this.___MOZ_FUNCTIONS=function(){};if(this.is_moz){this.togglesource_pre_show_html=function(){this.editor_document.designMode='off';};this.togglesource_post_show_html=function(){this.editor_document.designMode='on';};this._ORIGINAL_editor_set_content=this.editor_set_content;this.editor_set_content=function(initial_text){this._ORIGINAL_editor_set_content(initial_text);this.editor_document.addEventListener('keypress',ips_editor_events.prototype.editor_document_onkeypress,true);document.getElementById(this.editor_id+'_cmd_spellcheck').style.display='none';this.hidden_objects[this.editor_id+'_cmd_spellcheck']=1;if(this.use_bbcode){document.getElementById(this.editor_id+'_cmd_justifyfull').style.display='none';this.hidden_objects[this.editor_id+'_cmd_justifyfull']=1;}try{var _y=parseInt(window.pageYOffset);this.editor_document.execCommand("inserthtml",false,"-");this.editor_document.execCommand("undo",false,null);scroll(0,_y);}catch(error){}};this.moz_convert_fontsize=function(in_size){switch(in_size){case '7.5pt':case '10px':return 1;case '10pt':return 2;case '12pt':return 3;case '14pt':return 4;case '18pt':return 5;case '24pt':return 6;case '36pt':return 7;default:return '';}};this._ORIGINAL_apply_formatting=this.apply_formatting;this.apply_formatting=function(cmd,dialog,arg){if(cmd!='redo'){this.editor_document.execCommand("inserthtml",false,"-");this.editor_document.execCommand("undo",false,null);}this.editor_document.execCommand('useCSS',false,true);return this._ORIGINAL_apply_formatting(cmd,dialog,arg);};this.get_selection=function(){var selection=this.editor_window.getSelection();this.editor_check_focus();var range=selection?selection.getRangeAt(0):this.editor_document.createRange();return this.moz_read_nodes(range.cloneContents(),false);};this.insert_text=function(str,len){fragment=this.editor_document.createDocumentFragment();holder=this.editor_document.createElement('span');holder.innerHTML=str;while(holder.firstChild){fragment.appendChild(holder.firstChild);}var my_length=parseInt(len)>0?len:0;this.moz_insert_node_at_selection(fragment,my_length);};this.insert_emoticon=function(emo_id,emo_image,emo_code,event){this.editor_check_focus();try{var _emo_url=global_rte_emoticons_url+"/"+emo_image;this.editor_document.execCommand('InsertImage',false,_emo_url);var images=this.editor_document.getElementsByTagName('img');if(images.length>0){for(var i=0;i<=images.length;i++){if(images[i].src.match(new RegExp(_emo_url+"$"))){if(!images[i].getAttribute('emoid')){images[i].setAttribute('emoid',this.unhtmlspecialchars(emo_code));images[i].setAttribute('border','0');images[i].style.verticalAlign='middle';}}}}}catch(error){}};this.editor_set_functions=function(){this.editor_document.addEventListener('mouseup',ips_editor_events.prototype.editor_document_onmouseup,true);this.editor_document.addEventListener('keyup',ips_editor_events.prototype.editor_document_onkeyup,true);this.editor_window.addEventListener('focus',ips_editor_events.prototype.editor_window_onfocus,true);this.editor_window.addEventListener('blur',ips_editor_events.prototype.editor_window_onblur,true);this.editor_document.addEventListener('keydown',ips_editor_events.prototype.editor_document_onkeydown,true);};this.moz_add_range=function(node,text_length){this.editor_check_focus();var sel=this.editor_window.getSelection();var range=this.editor_document.createRange();range.selectNodeContents(node);if(text_length){range.setEnd(node,text_length);range.setStart(node,text_length);}sel.removeAllRanges();sel.addRange(range);};this.moz_read_nodes=function(root,toptag){var html="";var moz_check= /_moz/i;switch(root.nodeType){case Node.ELEMENT_NODE:case Node.DOCUMENT_FRAGMENT_NODE:{var closed;if(toptag){closed=!root.hasChildNodes();html='<'+root.tagName.toLowerCase();var attr=root.attributes;for(var i=0;i<attr.length;++i){var a=attr.item(i);if(!a.specified||a.name.match(moz_check)||a.value.match(moz_check)){continue;}html+=" "+a.name.toLowerCase()+'="'+a.value+'"';}html+=closed?" />":">";}for(var i=root.firstChild;i;i=i.nextSibling){html+=this.moz_read_nodes(i,true);}if(toptag&&!closed){html+="</"+root.tagName.toLowerCase()+">";}}break;case Node.TEXT_NODE:{html=this.htmlspecialchars(root.data);}break;}return html;};this.moz_goto_parent_then_body=function(n){var o=n;while(n.parentNode!=null&&n.parentNode.nodeName=='HTML'){n=n.parentNode;}if(n){for(var c=0;c<n.childNodes.length;c++){if(n.childNodes[c].nodeName=='BODY'){return n.childNodes[c];}}}return o;};this.moz_insert_node_at_selection=function(text,text_length){this.editor_check_focus();var sel=this.editor_window.getSelection();var range=sel?sel.getRangeAt(0):this.editor_document.createRange();sel.removeAllRanges();range.deleteContents();var node=range.startContainer;var pos=range.startOffset;text_length=text_length?text_length:0;if(node.nodeName=='HTML'){node=this.moz_goto_parent_then_body(node);}switch(node.nodeType){case Node.ELEMENT_NODE:{if(text.nodeType==Node.DOCUMENT_FRAGMENT_NODE){selNode=text.firstChild;}else{selNode=text;}node.insertBefore(text,node.childNodes[pos]);this.moz_add_range(selNode,text_length);}break;case Node.TEXT_NODE:{if(text.nodeType==Node.TEXT_NODE){var text_length=pos+text.length;node.insertData(pos,text.data);range=this.editor_document.createRange();range.setEnd(node,text_length);range.setStart(node,text_length);sel.addRange(range);}else{node=node.splitText(pos);var selNode;if(text.nodeType==Node.DOCUMENT_FRAGMENT_NODE){selNode=text.firstChild;}else{selNode=text;}node.parentNode.insertBefore(text,node);this.moz_add_range(selNode,text_length);}}break;}};}}else{this.___STD_FUNCTIONS=function(){};this.editor_write_contents=function(text){this.text_obj.value=text;};this.editor_set_content=function(init_text){var iframe=this.text_obj.parentNode.getElementsByTagName('iframe')[0];if(iframe){this.text_obj.style.display='';this.text_obj.style.width=iframe.style.width;this.text_obj.style.height=iframe.style.height;iframe.style.width='0px';iframe.style.height='0px';iframe.style.border='none';}this.editor_window=this.text_obj;this.editor_document=this.text_obj;this.editor_box=this.text_obj;if(typeof init_text!='undefined'){this.editor_write_contents(init_text);}this.editor_document.editor_id=this.editor_id;this.editor_window.editor_id=this.editor_id;if(!this.is_ie){document.getElementById(this.editor_id+'_cmd_spellcheck').style.display='none';this.hidden_objects[this.editor_id+'_cmd_spellcheck']=1;}document.getElementById(this.editor_id+'_cmd_togglesource').style.display='none';document.getElementById(this.editor_id+'_cmd_outdent').style.display='none';document.getElementById(this.editor_id+'_cmd_justifyfull').style.display='none';this.hidden_objects[this.editor_id+'_cmd_togglesource']=1;this.hidden_objects[this.editor_id+'_cmd_outdent']=1;this.hidden_objects[this.editor_id+'_cmd_justifyfull']=1;};this.editor_set_functions=function(){if(this.editor_document.addEventListener){this.editor_document.addEventListener('keypress',ips_editor_events.prototype.editor_document_onkeypress,false);}this.editor_window.onfocus=ips_editor_events.prototype.editor_window_onfocus;this.editor_window.onblur=ips_editor_events.prototype.editor_window_onblur;};this.set_context=function(){};this.removeformat=function(){var text=this.get_selection();if(text){text=this.strip_html(text);this.insert_text(text);}};this.apply_formatting=function(cmd,dialog,argument){switch(cmd){case 'bold':case 'italic':case 'underline':{this.wrap_tags(cmd.substr(0,1),false);return;}case 'justifyleft':case 'justifycenter':case 'justifyright':{this.wrap_tags(cmd.substr(7),false);return;}case 'indent':{this.wrap_tags(cmd,false);return;}case 'createlink':{var sel=this.get_selection();if(sel){this.wrap_tags('url',argument);}else{this.wrap_tags('url',argument,argument);}return;}case 'fontname':{this.wrap_tags('font',argument);return;}case 'fontsize':{this.wrap_tags('size',argument);return;}case 'forecolor':{this.wrap_tags('color',argument);return;}case 'backcolor':{this.wrap_tags('background',argument);return;}case 'insertimage':{this.wrap_tags('img',false,argument);return;}case 'strikethrough':{this.wrap_tags('s',false);return;}case 'superscript':{this.wrap_tags('sup',false);return;}case 'subscript':{this.wrap_tags('sub',false);return;}case 'removeformat':return;}};this.editor_get_contents=function(){return this.editor_document.value;};this.get_selection=function(){if(typeof(this.editor_document.selectionStart)!='undefined'){return this.editor_document.value.substr(this.editor_document.selectionStart,this.editor_document.selectionEnd-this.editor_document.selectionStart);}else if((document.selection&&document.selection.createRange)||this._ie_cache){return this._ie_cache?this._ie_cache.text:document.selection.createRange().text;}else if(window.getSelection){return window.getSelection()+'';}else{return false;}};this.insert_text=function(text){this.editor_check_focus();if(typeof(this.editor_document.selectionStart)!='undefined'){var open=this.editor_document.selectionStart+0;var st=this.editor_document.scrollTop;this.editor_document.value=this.editor_document.value.substr(0,this.editor_document.selectionStart)+text+this.editor_document.value.substr(this.editor_document.selectionEnd);if(!text.match(new RegExp("\\" + this.open_brace + "(\\S+?)" + "\\" + this.close_brace + "\\" + this.open_brace + "/(\\S+?)" + "\\" + this.close_brace ) ) )
{
this.editor_document.selectionStart = open;
this.editor_document.selectionEnd   = open + text.length;
this.editor_document.scrollTop      = st;
}
}
else if ( ( document.selection && document.selection.createRange ) || this._ie_cache )
{
var sel  = this._ie_cache ? this._ie_cache : document.selection.createRange();
sel.text = text.replace(/\r?\n/g, '\r\n');
sel.select();
}
else
{
this.editor_document.value += text;
}
this._ie_cache = null;
};
this.insert_emoticon = function( emo_id, emo_image, emo_code, event )
{
emo_code = this.unhtmlspecialchars( emo_code );
this.wrap_tags_lite( " " + emo_code, " ");
if ( this.is_ie )
{
if ( IPS_editor[ this.editor_id ].emoticon_window_id != '' && typeof( IPS_editor[ this.editor_id ].emoticon_window_id ) != 'undefined' )
{
IPS_editor[ this.editor_id ].emoticon_window_id.focus();
}
}
};
this.insertorderedlist = function(e)
{
this.insertlist( 'ol');
};
this.insertunorderedlist = function(e)
{
this.insertlist( 'ul');
};
this.insertlist = function( list_type )
{
var open_tag;
var close_tag;
var item_open_tag  = '<li>';
var item_close_tag = '</li>';
var regex          = '';
var all_add        = '';
if ( this.use_bbcode )
{
regex          = new RegExp('([\r\n]+|^[\r\n]*)(?!\\[\\*\\]|\\[\\/?list)(?=[^\r\n])', 'gi');
open_tag       = list_type == 'ol' ? '[list=1]\n' : '[list]\n';
close_tag      = '[/list]';
item_open_tag  = '[*]';
item_close_tag = '';
}
else
{
regex     = new RegExp('([\r\n]+|^[\r\n]*)(?!<li>|<\\/?ol|ul)(?=[^\r\n])', 'gi');
open_tag  = list_type == 'ol'  ? '<ol>\n'  : '<ul>\n';
close_tag = list_type == 'ol'  ? '</ol>\n' : '</ul>\n';
}
if ( text = this.get_selection() )
{
text = open_tag + text.replace( regex, "\n" + item_open_tag + '$1' + item_close_tag ) + '\n' + close_tag;
if ( this.use_bbcode )
{
text = text.replace( new RegExp( '\\[\\*\\][\r\n]+', 'gi' ), item_open_tag );
}
this.insert_text( text );
}
else
{
if ( this.is_moz )
{
this.insert_text( open_tag + close_tag );
while ( val = prompt( ipb_global_lang['editor_enter_list'], '') )
{
this.insert_text( open_tag + all_add + item_open_tag + val + item_close_tag + '\n' + close_tag );
all_add += item_open_tag + val + item_close_tag + '\n';
}
}
else
{
this.insert_text( open_tag );
while ( val = prompt( ipb_global_lang['editor_enter_list'], '') )
{
this.insert_text( item_open_tag + val + item_close_tag + '\n' );
}
this.insert_text( close_tag );
}
}
};
this.unlink = function()
{
var text       = this.get_selection();
var link_regex = '';
var link_text  = '';
if ( text !== false )
{
if ( text.match( link_regex ) )
{ 
text = ( this.use_bbcode ) ? text.replace( /\[url=([^\]]+?)\]([^\[]+?)\[\/url\]/ig, "$2" )
: text.replace( /<a href=['\"]([^\"']+?)['\"]([^>]+?)?>(.+?)<\/a>/ig,"$3");}this.insert_text(text);}};this.undo=function(){this.history_record_state(this.editor_get_contents());this.history_time_shift(-1);if((text=this.history_fetch_recording())!==false){this.editor_document.value=text;}};this.redo=function(){this.history_time_shift(1);if((text=this.history_fetch_recording())!==false){this.editor_document.value=text;}};this.update_for_form_submit=function(subjecttext,minchars){return true;};}this.___SAFARI_FUNCTIONS=function(){};if(this.is_safari){try{document.getElementById(this.editor_id+'_cmd_switcheditor').style.display='none';}catch(error){}}this.___IPB_FUNCTIONS=function(){};this.createlink=function(e){var _text=this.get_selection();_text=_text.replace( /\n|\r|<br \/>/g,'');if(_text.match( /(<a href|\[url)/ig)){this.format_text(e,"unlink",false);}else{var _url=prompt(ipb_global_lang['editor_enter_url'],'http://');if(!_url||_url==null||_url=='http://'){return false;}_text=_text?_text:prompt(ipb_global_lang['editor_enter_title'],ipb_global_lang['visit_my_website']);if(!_text||_text==null){return false;}this.wrap_tags('url',_url,_text);}};this.insertemail=function(e){var _text=this.get_selection();_text=_text.replace( /\n|\r|<br \/>/g,'');if(_text.match( /(<a href|\[email)/ig)){this.format_text(e,"unlink",false);}else{var _url=prompt(ipb_global_lang['editor_enter_email'],'');if(!_url||_url==null){return false;}_text=_text?_text:prompt(ipb_global_lang['editor_enter_title']);if(!_text||_text==null){return false;}this.wrap_tags('email',_url,_text);}};this.insertimage=function(){var _text=this.get_selection();_text=_text.replace( /\n|\r|<br \/>/g,'');if(this.is_rte){if(_text.match( /<img(.+?)src=['"](.+?)["'](.*?)>/g)){_text=_text.replace( /<img(.+?)src=['"](.+?)["'](.*?)>/g,'$2');}}var _url=prompt(ipb_global_lang['editor_enter_image'],_text?_text:"http://");if(!_url||_url==null||_url=='http://'){return false;}if(!this.is_rte){this.wrap_tags('img',false,_url);}else{this.wrap_tags('img',_url,'');}};this.ipb_quote=function(){this.wrap_tags_lite('[quote]','[/quote]',0)};this.ipb_code=function(){this.wrap_tags_lite('[code]','[/code]',0)};this.init();}function ips_editor_events(){}ips_editor_events.prototype.button_onmouse_event=function(e){if(is_ie){e=ipsclass.cancel_bubble(e,true);}if(e.type=='click'){IPS_editor[this.editor_id].format_text(e,this.cmd,false,true);}IPS_editor[this.editor_id].set_button_context(this,e.type);};ips_editor_events.prototype.special_onmouse_event=function(e){e=ipsclass.cancel_bubble(e,true);if(e.type=='click'){if(!this.loader_key){IPS_editor[this.editor_id].format_text(e,this.cmd,false,true);ipsmenu.close();}else{IPS_editor[this.editor_id].module_load(this,e,this.loader_key);}}IPS_editor[this.editor_id].set_button_context(this,e.type,'menu');};ips_editor_events.prototype.editor_window_onfocus=function(e){this.has_focus=true;};ips_editor_events.prototype.editor_window_onblur=function(e){this.has_focus=false;};ips_editor_events.prototype.editor_document_onmouseup=function(e){try{if(typeof(this.editor_id=='undefined')&&is_moz){this.editor_id=e.view.editor_id;}}catch(me){}IPS_editor[this.editor_id].set_context();menu_action_close();};ips_editor_events.prototype.editor_document_onkeyup=function(e){IPS_editor[this.editor_id].set_context();};ips_editor_events.prototype.editor_document_onkeypress=function(e){if(e.ctrlKey){switch(String.fromCharCode(e.charCode).toLowerCase()){case 'b':cmd='bold';break;case 'i':cmd='italic';break;case 'u':cmd='underline';break;default:return;}e.preventDefault();IPS_editor[this.editor_id].apply_formatting(cmd,false,null);return false;}};ips_editor_events.prototype.popup_button_onmouseevent=function(e){e=ipsclass.cancel_bubble(e,true);if(e.type=='click'){this._onclick(e);IPS_editor[this.editor_id].set_menu_context(this,'mouseover');}else{IPS_editor[this.editor_id].set_menu_context(this,e.type);}};ips_editor_events.prototype.popup_button_show=function(obj){if(typeof IPS_editor[obj.editor_id].popups[obj.cmd]=='undefined'||IPS_editor[obj.editor_id].popups[obj.cmd]==null){IPS_editor[obj.editor_id].init_editor_menu(obj);}this._open(obj);};ips_editor_events.prototype.menu_option_onmouseevent=function(e){e=ipsclass.cancel_bubble(e,true);IPS_editor[this.editor_id].set_button_context(this,e.type,'menu');};ips_editor_events.prototype.font_format_option_onclick=function(e){IPS_editor[this.editor_id].format_text(e,this.cmd,this.firstChild.innerHTML);ipsmenu.close();};ips_editor_events.prototype.emoticon_onclick=function(e){e=ipsclass.cancel_bubble(e,true);IPS_editor[this.editor_id].insert_emoticon(this.emo_id,this.emo_image,this.emo_code,e);ipsmenu.close();};ips_editor_events.prototype.color_cell_onclick=function(e){IPS_editor[this.editor_id].format_text(e,this.cmd,this.colorname);ipsmenu.close();};ips_editor_events.prototype.editor_document_onkeydown=function(e){};
