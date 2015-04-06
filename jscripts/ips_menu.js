ipsmenu=new ips_menu();function ips_menu(){this.menu_registered=new Array();this.menu_openfuncs=new Array();this.menu_over_css=new Array();this.menu_out_css=new Array();this.menu_open_event=new Array();this.dynamic_register=new Array();this.menu_cur_open=null;this.dynamic_html=null;}ips_menu.prototype.register=function(cid,callback,menu_over_css,menu_out_css,event_type){if(event_type){this.menu_open_event[cid]=(event_type=='onmouseover')?'onmouseover':'onclick';}this.menu_registered[cid]=new ips_menu_class(cid);if(callback){this.menu_openfuncs[cid]=callback;}if(menu_over_css&&menu_out_css){this.menu_over_css[cid]=menu_over_css;this.menu_out_css[cid]=menu_out_css;}return this.menu_registered[cid];};ips_menu.prototype.close=function(){if(this.menu_cur_open){this.menu_registered[this.menu_cur_open].close();}};function ips_menu_class(cid){this.cid=cid;this.initialized=false;this.init_control_object();this.init_menu();};ips_menu_class.prototype.init_control_object=function(){this.cid_obj=document.getElementById(this.cid);try{this.cid_obj.style.cursor="pointer";}catch(e){this.cid_obj.style.cursor="hand";}this.cid_obj.unselectable=true;if(ipsmenu.menu_open_event[this.cid]=='onmouseover'){this.cid_obj.onmouseover=ips_menu_events.prototype.event_onclick;}else{this.cid_obj.onclick=ips_menu_events.prototype.event_onclick;this.cid_obj.onmouseover=ips_menu_events.prototype.event_onmouseover;}this.cid_obj.onmouseout=ips_menu_events.prototype.event_onmouseout;};ips_menu_class.prototype.init_menu=function(){this.cid_menu_obj=document.getElementById(this.cid+'_menu');if(this.cid_menu_obj){if(this.initialized){return;}this.cid_menu_obj.style.display="none";this.cid_menu_obj.style.position="absolute";this.cid_menu_obj.style.left="0px";this.cid_menu_obj.style.top="0px";this.cid_menu_obj.onclick=ipsclass.cancel_bubble_low;this.cid_menu_obj.zIndex=50;this.initialized=true;}};ips_menu_class.prototype.open=function(obj){if(!this.cid_menu_obj){this.initialized=false;this.init_menu();}if(ipsmenu.menu_cur_open!=null){ipsmenu.menu_registered[ipsmenu.menu_cur_open].close();}if(ipsmenu.menu_cur_open==obj.id){return false;}ipsmenu.menu_cur_open=obj.id;var left_px=ipsclass.get_obj_leftpos(obj);var top_px=ipsclass.get_obj_toppos(obj)+obj.offsetHeight;var ifid=obj.id;try{if(is_safari&&!ipb_is_acp){top_px+=20;}}catch(error){if(is_safari){top_px+=20;}}this.cid_menu_obj.style.zIndex=-1;this.cid_menu_obj.style.display="";var width=parseInt(this.cid_menu_obj.style.width)?parseInt(this.cid_menu_obj.style.width):this.cid_menu_obj.offsetWidth;if((left_px+width)>=document.body.clientWidth){left_px=left_px+obj.offsetWidth-width;}if(is_moz){top_px-=1;}this.cid_menu_obj.style.left=left_px+"px";this.cid_menu_obj.style.top=top_px+"px";this.cid_menu_obj.style.zIndex=100;if(ipsmenu.menu_openfuncs[obj.id]){eval(ipsmenu.menu_openfuncs[obj.id]);}if(is_ie){try{if(!document.getElementById('if_'+obj.id)){var iframeobj=document.createElement('iframe');iframeobj.src='javascript:;';iframeobj.id='if_'+obj.id;document.getElementsByTagName('body').appendChild(iframeobj);}else{var iframeobj=document.getElementById('if_'+obj.id);}iframeobj.scrolling='no';iframeobj.frameborder='no';iframeobj.className='iframeshim';iframeobj.style.position='absolute';iframeobj.style.width=parseInt(this.cid_menu_obj.offsetWidth)+'px';iframeobj.style.height=parseInt(this.cid_menu_obj.offsetHeight)+'px';iframeobj.style.top=this.cid_menu_obj.style.top;iframeobj.style.left=this.cid_menu_obj.style.left;iframeobj.style.zIndex=99;iframeobj.style.display="block";}catch(error){}}if(is_safari){try{mlinks=this.cid_menu_obj.getElementsByTagName('a');for(var i=0;i<=mlinks.length;i++){if(mlinks[i]!=null&&mlinks[i].href!=null&&mlinks[i].href&&(mlinks[i].href.indexOf('#',0)!=(mlinks[i].href.length-1))){mlinks[i].onmousedown=ips_menu_events.prototype.event_safari_onclick_handler;mlinks[i].id='saf-link-'+this.cid+i;}}}catch(error){}}if(this.cid_obj.editor_id){this.cid_obj.state=true;IPS_editor[this.cid_obj.editor_id].set_menu_context(this.cid_obj,'mousedown');}return false;};ips_menu_class.prototype.close=function(){if(this.cid_menu_obj!=null){this.cid_menu_obj.style.display="none";}else if(ipsmenu.menu_cur_open!=null){ipsmenu.menu_registered[ipsmenu.menu_cur_open].cid_menu_obj.style.display='none';}ipsmenu.menu_cur_open=null;if(this.cid_obj){if(ipsmenu.menu_out_css[this.cid_obj.id]){this.cid_obj.className=ipsmenu.menu_out_css[this.cid_obj.id];}}if(is_ie){try{document.getElementById('if_'+this.cid).style.display="none";}catch(error){}}if(this.cid_obj.editor_id){this.cid_obj.state=false;IPS_editor[this.cid_obj.editor_id].set_menu_context(this.cid_obj,'mouseout');}};ips_menu_class.prototype.hover=function(e){if(ipsmenu.menu_cur_open!=null){if(ipsmenu.menu_registered[ipsmenu.menu_cur_open].cid!=this.id){this.open(e);}}};function ips_menu_events(){};ips_menu_events.prototype.event_safari_onclick_handler=function(){if(this.id){window.location=document.getElementById(this.id).href;}};ips_menu_events.prototype.event_onmouseover=function(e){e=ipsclass.cancel_bubble(e,true);ipsmenu.menu_registered[this.id].hover(this);if(ipsmenu.menu_over_css[this.id]){this.className=ipsmenu.menu_over_css[this.id];}};ips_menu_events.prototype.event_onmouseout=function(e){e=ipsclass.cancel_bubble(e,true);if(ipsmenu.menu_out_css[this.id]&&ipsmenu.menu_cur_open!=this.id){this.className=ipsmenu.menu_out_css[this.id];}};ips_menu_events.prototype.event_onclick=function(e){e=ipsclass.cancel_bubble(e,true);if(ipsmenu.menu_cur_open==null){if(ipsmenu.menu_over_css[this.id]){this.className=ipsmenu.menu_over_css[this.id];}ipsmenu.menu_registered[this.id].open(this);}else{if(ipsmenu.menu_cur_open==this.id){ipsmenu.menu_registered[this.id].close();if(ipsmenu.menu_out_css[this.id]){this.className=ipsmenu.menu_out_css[this.id];}}else{if(ipsmenu.menu_over_css[this.id]){this.className=ipsmenu.menu_over_css[this.id];}ipsmenu.menu_registered[this.id].open(this);};}};function menu_do_global_init(){document.onclick=menu_action_close;if(ipsmenu.dynamic_html!=null&&ipsmenu.dynamic_html!=''){}if(ipsmenu.dynamic_register.length){for(var i in ipsmenu.dynamic_register){if(ipsmenu.dynamic_register[i]){ipsmenu.register(ipsmenu.dynamic_register[i]);}}}};function menu_action_close(e){try{if(e.button==2||e.button==3){return;}}catch(acold){};ipsmenu.close(e);};
