var ips_personal_portal=new ips_personal_portal();function ips_personal_portal(){this.div_rating_wrapper='pp-rating-wrapper';this.text_rating_image='pp-rating-img-';this.css_classes={'pp-tabon':'pp-tabon','pp-taboff':'pp-taboff','pp-tabshaded':'pp-tabshaded','pp-contentbox-back':'pp-contentbox-back','pp-contentbox-entry-noheight':'pp-contentbox-entry-noheight','pp-contentbox-entry-noheight-mod':'pp-contentbox-entry-noheight-mod','pp-contentbox-entry-noheight-sel':'pp-contentbox-entry-noheight-sel'};this.contact_types={'aim':'aim','msn':'msn','icq':'icq','yahoo':'yahoo'};this.content_tabs={};this.stored_tabs={};this.stored_div_classes={};this.iframe_tabs={'comments':ipb_var_base_url+'act=profile&CODE=personal_iframe_comments','settings':ipb_var_base_url+'act=profile&CODE=personal_iframe_settings','friends':ipb_var_base_url+'act=profile&CODE=personal_iframe_friends'};this.stored_tabs_css={};this.div_names={'pp-entry-gender-wrap':'pp-entry-gender-wrap','pp-entry-gender-text':'pp-entry-gender-text','pp-entry-gender-img':'pp-entry-gender-img','pp-entry-gender-imgwrap':'pp-entry-gender-imgwrap','pp-entry-location-wrap':'pp-entry-location-wrap','pp-entry-location-text':'pp-entry-location-text','pp-entry-born-wrap':'pp-entry-born-wrap','pp-entry-born-text':'pp-entry-born-text','pp-entry-age-wrap':'pp-entry-age-wrap','pp-entry-age-text':'pp-entry-age-text','pp-entry-age-yearsold':'pp-entry-age-yearsold','pp-main-tab-content':'pp-main-tab-content','pp-content-tab':'pp-content-tab','pp-comment-textarea':'pp-comment-textarea','pp-comments-wrap':'pp-comments-wrap','pp-comment-entry':'pp-comment-entry','pp-comment-entry-main':'pp-comment-entry-main','pp-iframe-wrap':'pp-iframe-wrap','pp-entry-contact-wrap':'pp-entry-contact-wrap','pp-entry-contact-entry':'pp-entry-contact-entry','pp-main-photo':'pp-main-photo','pp-personal_statement':'pp-personal_statement','pp-checked':'pp-checked','pp-friend-img':'pp-friend-img','pp-friend-text':'pp-friend-text','pp-friend-wrap':'pp-friend-wrap'};this.settings={'allow_rating':0,'default_rating':3,'edit_allowed':0,'member_id':0,'viewer_id':0,'img_star_on':'star_filled.gif','img_star_selected':'star_selected.gif','img_star_off':'star_empty.gif','img_gender_male':'gender_male.png','img_gender_female':'gender_female.png','img_gender_mystery':'gender_mystery.png','img_base_url':'','img_menu_icon':'','default_tab':'','photo_def_width':'150','photo_def_height':'150','photo_def_src':ipb_var_image_url+'/folder_profile_portal/pp-blank-large.png','img_friend_remove':ipb_var_image_url+'/folder_profile_portal/friend_remove_small.png','img_friend_add':ipb_var_image_url+'/folder_profile_portal/friend_add_small.png'};this.languages={'rate_me':'Rate Me','img_alt_rate':'Rate this member, click on desired star to send rating','gender_male':'Male','gender_female':'Female','gender_mystery':'Gender Not Set','saving_comment':'Saving Comment...','loading_tab':ajax_load_msg,'deleting_comment':'Deleting Comment...','friend_add':'Add as Friend','friend_remove':'Remove Friend'};var loaded_tab=null;this.init=function(){var divs=document.getElementsByTagName('DIV');var divcount=0;for(var i=0;i<=divs.length;i++){try{if(!divs[i].id){continue;}}catch(error){continue;}var divid=divs[i].id;var divname=ipsclass.get_name_from_text(divs[i].id);var divnum=ipsclass.get_id_from_text(divs[i].id);if(divname==this.div_names['pp-content-tab']){var _highlight=0;this.stored_tabs[divnum]=divs[i];this.stored_tabs_css[divnum]=divs[i].className;divs[i].style.cursor='pointer';divs[i].onclick=this.tab_onclick;if(this.settings['default_tab']!=""){if(this.settings['default_tab']==divnum){_highlight=1;}}else if(divcount==0){_highlight=1;}if(_highlight==1){divs[i].className=this.css_classes['pp-tabon'];divs[i].style.display='block';}else{if(divs[i].className!=this.css_classes['pp-tabshaded']){divs[i].className=this.css_classes['pp-taboff'];}divs[i].style.display='block';}divcount++;}}this.init_rating_images();this.show_dropdown_boxes();};this.show_friend_add_or_remove=function(add_friend){try{var _div=document.getElementById(this.div_names['pp-friend-wrap']);var _html='';if(_div.id){if(add_friend){_html="<img src='"+this.settings['img_friend_add']+"' id='"+this.div_names['pp-friend-img']+"' alt='' border='0' /> ";_html+="<a href='#' onclick='friends_pop(\"&amp;do=add&amp;member_id="+this.settings['member_id']+"&amp;md5check="+ipb_md5_check+"\"); ips_personal_portal.show_friend_add_or_remove(0); return false;' id='"+this.div_names['pp-friend-text']+"'>"+this.languages['friend_add']+"</a>";}else{_html="<img src='"+this.settings['img_friend_remove']+"' id='"+this.div_names['pp-friend-img']+"' alt='' border='0' /> ";_html+="<a href='#' onclick='friends_pop(\"&amp;do=remove&amp;member_id="+this.settings['member_id']+"&amp;md5check="+ipb_md5_check+"\"); ips_personal_portal.show_friend_add_or_remove(1); return false;' id='"+this.div_names['pp-friend-text']+"'>"+this.languages['friend_remove']+"</a>";}_div.innerHTML=_html;}}catch(error){}};this.toggle_comment_box=function(comment_id,from_iframe){comment_id=parseInt(comment_id);from_iframe=parseInt(from_iframe);var _document=document;if(from_iframe){var iframe=document.getElementById('pp-main-tab-content-iframe');if(iframe.contentDocument){_document=iframe.contentDocument;}else if(iframe.contentWindow){_document=iframe.contentWindow.document;}else if(iframe.document){_document=iframe.document;}}if(comment_id){var _div=_document.getElementById(this.div_names['pp-comment-entry-main']+'-'+comment_id);var _box=_document.getElementById(this.div_names['pp-checked']+'-'+comment_id);var _class=_div.className;if(_class==this.css_classes['pp-contentbox-entry-noheight']||_class==this.css_classes['pp-contentbox-entry-noheight-mod']){this.stored_div_classes[this.div_names['pp-comment-entry']+'-'+comment_id]=_class;_div.className=this.css_classes['pp-contentbox-entry-noheight-sel'];}else if(this.stored_div_classes[this.div_names['pp-comment-entry']+'-'+comment_id]){_div.className=this.stored_div_classes[this.div_names['pp-comment-entry']+'-'+comment_id];}else{_div.className=this.css_classes['pp-contentbox-entry-noheight'];}}return true;};this.show_dropdown_boxes=function(){var html='';if((this.settings['member_id']==this.settings['viewer_id'])&&(this.settings['viewer_id'])&&(this.settings['edit_allowed']==1)){var _wrap=document.getElementById(this.div_names['pp-entry-gender-wrap']);_wrap.className='popmenubutton-new';ipsmenu.register("pp-entry-gender-wrap");var _wrap=document.getElementById(this.div_names['pp-entry-location-wrap']);_wrap.className='popmenubutton-new';ipsmenu.register("pp-entry-location-wrap");var _wrap=document.getElementById(this.div_names['pp-entry-born-wrap']);_wrap.className='popmenubutton-new';ipsmenu.register("pp-entry-born-wrap");for(var i in this.contact_types){try{var _wrap=document.getElementById(this.div_names['pp-entry-contact-wrap']+'-'+this.contact_types[i]);_wrap.className='popmenubutton-new';ipsmenu.register("pp-entry-contact-wrap"+'-'+this.contact_types[i]);}catch(error){}}}};this.init_rating_images=function(){var html='';if(!this.settings['allow_rating']){return false;}for(var i=1;i<=5;i++){var _onmouseover='';var _onmouseout='';var _onclick='';var _title='';if((this.settings['member_id']!=this.settings['viewer_id'])&&(this.settings['viewer_id'])){_onmouseover=' onmouseover="this.style.cursor=\'pointer\'; ips_personal_portal.show_rating_images('+i+', 0)"';_onmouseout=' onmouseout="ips_personal_portal.show_rating_images(-1, 1)"';_onclick=' onclick="ips_personal_portal.send_rating('+i+')"';_title=this.languages['img_alt_rate'];}html+="<img src='"+this.settings['img_base_url']+'/'+this.settings['img_star_off']+"' "+_onmouseover+_onmouseout+_onclick+"id='"+this.text_rating_image+i+"' alt='-' title='"+_title+"' />";}if((this.settings['member_id']!=this.settings['viewer_id'])&&(this.settings['viewer_id'])){document.getElementById(this.div_rating_wrapper).innerHTML=this.languages['rate_me']+' '+html;}else{document.getElementById(this.div_rating_wrapper).innerHTML=html;}this.show_rating_images(this.settings['default_rating'],1);};this.load_content_tab=function(tab){var html='';if(is_ie&&!is_ie7){var _div=document.getElementById(ips_personal_portal.div_names['pp-main-tab-content']);var _width=parseInt(_div.offsetWidth-12);}if(tab){hide_inline_messages_instant();this.loaded_tab=tab;if(this.iframe_tabs[tab]){var url=this.iframe_tabs[tab]+'&member_id='+this.settings['member_id']+'&md5check='+ipb_md5_check;document.getElementById(this.div_names['pp-main-tab-content']).innerHTML='';var iframeinclude=new iframe_include();iframeinclude.iframe_id='pp-main-tab-content-iframe';iframeinclude.iframe_add_to_div='pp-main-tab-content';iframeinclude.iframe_main_wrapper='pp-iframe-wrap';iframeinclude.init();iframeinclude.include(url);}else{var url=ipb_var_base_url+'act=profile&CODE=personal_ajax_load_tab&member_id='+this.settings['member_id']+'&tab='+tab+'&md5check='+ipb_md5_check;do_request_function=function(){if(!xmlobj.readystate_ready_and_ok()){xmlobj.show_loading(ips_personal_portal.languages['loading_tab']);return;}xmlobj.hide_loading();var html=xmlobj.xmlhandler.responseText;if(html!='error'){document.getElementById(ips_personal_portal.div_names['pp-main-tab-content']).innerHTML=html;try{fix_linked_image_sizes();xmlobj.execute_javascript(html);document.getElementById(ips_personal_portal.div_names['pp-main-tab-content']).style.height='auto';document.getElementById(ips_personal_portal.div_names['pp-main-tab-content']).style.padding='6px';document.getElementById(ips_personal_portal.div_names['pp-main-tab-content']).className=ips_personal_portal.css_classes['pp-contentbox-back'];if(is_ie){_div.style.width=(_width)?_width+"px":'auto';_div.style.overflowX='auto';}}catch(error){}}};xmlobj=new ajax_request();xmlobj.onreadystatechange(do_request_function);xmlobj.process(url);return false;}}};this.send_rating=function(rating){rating=rating?rating:0;if(rating){var url=ipb_var_base_url+'act=xmlout&do=member-rate&member_id='+this.settings['member_id']+'&rating='+rating;do_request_function=function(){if(!xmlobj.readystate_ready_and_ok()){xmlobj.show_loading('');return;}xmlobj.hide_loading();var html=xmlobj.xmlhandler.responseText;if(html=='no_permission'){alert(js_error_no_permission);}else if(html!='error'){var _result=html.split(',');var _new_value=_result[0];var _new_hits=_result[1];var _new_stars=_result[2];var _type=_result[3];ips_personal_portal.settings['default_rating']=parseInt(_new_stars);ips_personal_portal.show_rating_images(ips_personal_portal.settings['default_rating'],1);ipsclass.fade_in_element(ips_personal_portal.div_rating_wrapper);show_inline_messages_instant('rating_updated');}};xmlobj=new ajax_request();xmlobj.onreadystatechange(do_request_function);xmlobj.process(url);return false;}};this.show_rating_images=function(rating,restore_default){rating=restore_default?this.settings['default_rating']:parseInt(rating);var star=restore_default?this.settings['img_star_on']:this.settings['img_star_selected'];for(var i=1;i<=5;i++){var _img=document.getElementById(this.text_rating_image+i);_img.src=this.settings['img_base_url']+'/'+this.settings['img_star_off'];}for(var i=1;i<=rating;i++){var _img=document.getElementById(this.text_rating_image+i);_img.src=this.settings['img_base_url']+'/'+star;}};this.delete_comment=function(comment_id){var url=ipb_var_base_url+'act=profile&CODE=personal_ajax_delete_comment&member_id='+this.settings['member_id'];var final_fields=new Array();final_fields['md5check']=ipb_md5_check;final_fields['comment_id']=comment_id;do_request_function=function(){if(!xmlobj.readystate_ready_and_ok()){xmlobj.show_loading(ips_personal_portal.languages['deleting_comment']);return;}xmlobj.hide_loading();var html=xmlobj.xmlhandler.responseText;if(html=='nopermission'){alert(js_error_no_permission);}else if(html!='error'){document.getElementById(ips_personal_portal.div_names['pp-comments-wrap']).innerHTML=html;if(ips_personal_portal.loaded_tab=='comments'){ips_personal_portal.load_content_tab('comments');}}};xmlobj=new ajax_request();xmlobj.onreadystatechange(do_request_function);var xmlreturn=xmlobj.process(url,'POST',xmlobj.format_for_post(final_fields));return false;};this.reload_comments=function(){var url=ipb_var_base_url+'act=profile&CODE=personal_ajax_reload_comments&member_id='+this.settings['member_id'];var final_fields=new Array();final_fields['md5check']=ipb_md5_check;do_request_function=function(){if(!xmlobj_gender.readystate_ready_and_ok()){return;}var html=xmlobj_gender.xmlhandler.responseText;if(html!='error'&&html!='no_permission'){document.getElementById(ips_personal_portal.div_names['pp-comments-wrap']).innerHTML=html;}};xmlobj_gender=new ajax_request();xmlobj_gender.onreadystatechange(do_request_function);var xmlreturn=xmlobj_gender.process(url,'POST',xmlobj_gender.format_for_post(final_fields));return false;};this.save_comment=function(){var url=ipb_var_base_url+'act=profile&CODE=personal_ajax_add_comment&member_id='+this.settings['member_id'];var final_fields=new Array();menu_action_close();final_fields['md5check']=ipb_md5_check;final_fields['comment']=document.getElementById(this.div_names['pp-comment-textarea']).value;do_request_function=function(){if(!xmlobj.readystate_ready_and_ok()){xmlobj.show_loading(ips_personal_portal.languages['saving_comment']);return;}xmlobj.hide_loading();var html=xmlobj.xmlhandler.responseText;if(html=='nopermission'){alert(js_error_no_permission);return;}else if(html=='error-no-comment'){show_inline_messages_instant('pp_comment_error');return;}else if(html!='error'){document.getElementById(ips_personal_portal.div_names['pp-comments-wrap']).innerHTML=html;xmlobj.execute_javascript(html);if(ips_personal_portal.loaded_tab=='comments'){ips_personal_portal.load_content_tab('comments');}document.getElementById(ips_personal_portal.div_names['pp-comment-textarea']).value='';}};xmlobj=new ajax_request();xmlobj.onreadystatechange(do_request_function);var xmlreturn=xmlobj.process(url,'POST',xmlobj.format_for_post(final_fields));return false;};this.update_personal_photo=function(img_url,width,height){if(!img_url||!img_url.match("/photo-")){img_url=this.settings['photo_def_src'];width=this.settings['photo_def_width'];height=this.settings['photo_def_height'];}var _img=document.getElementById(this.div_names['pp-main-photo']);var mydate=new Date();_img.src=img_url+'?__time='+mydate.getTime();_img.width=width;_img.height=height;};this.update_personal_statement=function(content,website){var _statement=document.getElementById(this.div_names['pp-personal_statement']);content=content.replace( /\[b\](.+?)\[\/b\]/gi,"<b>$1</b>");content=content.replace( /\[i\](.+?)\[\/i\]/gi,"<i>$1</i>");content=content.replace( /\[u\](.+?)\[\/u\]/gi,"<u>$1</u>");content=content.replace( /(\S{19})(\S+?)/g,"$1 $2");_statement.innerHTML=content;if(website){if(website.length>30){_statement.innerHTML+="<br /><br /><a href='"+website+"' target='_blank'>"+ipb_global_lang['visit_my_website']+"</a>";}else{_statement.innerHTML+="<br /><br /><a href='"+website+"' target='_blank'>"+website+"</a>";}}};this.save_settings=function(func_name,field_data,get_data_from_this_id){var url=ipb_var_base_url+'act=xmlout&do=profile-save-settings&member_id='+this.settings['member_id'];var final_fields=new Array();menu_action_close();final_fields['md5check']=ipb_md5_check;final_fields['cmd']=func_name;try{for(var i in field_data){final_fields[i]=field_data[i];}}catch(error){}if(func_name=='birthdate'){final_fields['pp_b_day']=document.getElementById('pp_b_day').options[document.getElementById('pp_b_day').selectedIndex].value;final_fields['pp_b_month']=document.getElementById('pp_b_month').options[document.getElementById('pp_b_month').selectedIndex].value;final_fields['pp_b_year']=document.getElementById('pp_b_year').options[document.getElementById('pp_b_year').selectedIndex].value;}if(get_data_from_this_id!=''){final_fields['value']=document.getElementById(get_data_from_this_id).value;}do_request_function=function(){if(!xmlobj.readystate_ready_and_ok()){xmlobj.show_loading();return;}xmlobj.hide_loading();var html=xmlobj.xmlhandler.responseText;if(html=='nopermission'){alert(js_error_no_permission);}else if(html!='error'){switch(final_fields['cmd']){case 'gender':var _txt=document.getElementById(ips_personal_portal.div_names['pp-entry-gender-text']);var _imgwrap=document.getElementById(ips_personal_portal.div_names['pp-entry-gender-imgwrap']);var _date=new Date();var _src='';if(html=='male'){_src=ips_personal_portal.settings['img_base_url']+'/'+ips_personal_portal.settings['img_gender_male']+"?__="+_date.getTime();_txt.innerHTML=ips_personal_portal.languages['gender_male'];}else if(html=='female'){_src=ips_personal_portal.settings['img_base_url']+'/'+ips_personal_portal.settings['img_gender_female']+"?__="+_date.getTime();_txt.innerHTML=ips_personal_portal.languages['gender_female'];}else{_src=ips_personal_portal.settings['img_base_url']+'/'+ips_personal_portal.settings['img_gender_mystery']+"?__="+_date.getTime();_txt.innerHTML=ips_personal_portal.languages['gender_mystery'];}_imgwrap.innerHTML="<img src='"+_src+"' alt='' border='0' />";ips_personal_portal.reload_comments();ipsclass.fade_in_element(ips_personal_portal.div_names['pp-entry-gender-wrap']);break;case 'contact':if(html=='icqerror'){show_inline_messages_instant('pp_icq_error');}else{document.getElementById(ips_personal_portal.div_names['pp-entry-contact-entry']+'-'+field_data['contacttype']).innerHTML=html;ipsclass.fade_in_element(ips_personal_portal.div_names['pp-entry-contact-wrap']+'-'+field_data['contacttype']);}break;case 'location':document.getElementById(ips_personal_portal.div_names['pp-entry-location-text']).innerHTML=html;ipsclass.fade_in_element(ips_personal_portal.div_names['pp-entry-location-wrap']);break;case 'birthdate':if(html=='dateerror'){show_inline_messages_instant('pp_date_error');}else{document.getElementById(ips_personal_portal.div_names['pp-entry-born-text']).innerHTML=html;var _dates=html.split('/');if(_dates[2]){var today=new Date();var _years=today.getFullYear();var _days=today.getDate();var _mos=today.getMonth()+1;var _age=_years-_dates[2];if(_dates[0]>_mos){_age-=1;}else if(_dates[0]==_mos){if(_dates[1]>_days){_age-=1;}}document.getElementById(ips_personal_portal.div_names['pp-entry-age-text']).innerHTML=_age;document.getElementById(ips_personal_portal.div_names['pp-entry-age-yearsold']).style.display='';ipsclass.fade_in_element(ips_personal_portal.div_names['pp-entry-age-wrap']);}ipsclass.fade_in_element(ips_personal_portal.div_names['pp-entry-born-wrap']);}}}};xmlobj=new ajax_request();xmlobj.onreadystatechange(do_request_function);var xmlreturn=xmlobj.process(url,'POST',xmlobj.format_for_post(final_fields));return false;};this.tab_onclick=function(event){var tabid=ipsclass.get_id_from_text(this.id);ipsclass.cancel_bubble(event);ips_personal_portal.tab_load(tabid);return false;};this.tab_load=function(tabid){for(var i in ips_personal_portal.stored_tabs){if(i==tabid){ips_personal_portal.stored_tabs[i].style.display='block';ips_personal_portal.stored_tabs[i].className=ips_personal_portal.css_classes['pp-tabon'];}else{ips_personal_portal.stored_tabs[i].style.display='block';if(ips_personal_portal.stored_tabs_css[i]==ips_personal_portal.css_classes['pp-tabshaded']){ips_personal_portal.stored_tabs[i].className=ips_personal_portal.css_classes['pp-tabshaded'];}else{ips_personal_portal.stored_tabs[i].className=ips_personal_portal.css_classes['pp-taboff'];}}}ips_personal_portal.load_content_tab(tabid);return false;};}
