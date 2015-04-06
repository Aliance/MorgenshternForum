/*
* Gallery jscript functions
* By Adam Kinder
*
* $Id$
*/
var addquotebutton     = ipb_var_image_url + "/p_mq_add.gif";
var removequotebutton  = ipb_var_image_url + "/p_mq_remove.gif";
var unselectedbutton = ipb_var_image_url + "/topic_unselected.gif";
var selectedbutton   = ipb_var_image_url + "/topic_selected.gif";
var lang_gobutton    = "With selected";
var gallery_lang_prompt = "Link to Image:";

/**
* Resize thumb-div to ensure that all image rows are the
* same height
*
* @author	Matt
* @since	2.0.3
*/
function html_resize_thumb_divs( use_thumbs, thumbs_x, thumbs_x )
{
	if ( use_thumbs && thumbs_x && thumbs_y )
	{
		var thumb_divs = document.getElementsByTagName('DIV');
		
		//----------------------------------
		// Sort through and resize divs
		//----------------------------------
		
		for ( var i = 0 ; i <= thumb_divs.length ; i++ )
		{
			try
			{
				if ( ! thumb_divs[i].id && thumb_divs[i].className == 'thumb' )
				{
					// Resize to chosen thumb height + 30 px of "padding"
					thumb_divs[i].style.height = thumbs_x + 30 + 'px';
				}
			}
			catch(e)
			{
				continue;
			}
		}
	}
}

function gallery_link_to_post(pid,img) {
	temp = prompt( gallery_lang_prompt, ipb_var_base_url + "automodule=gallery&cmd=si&img=" + img + "#" + pid );
	return false;
}

function delete_img(theURL)  {
	if (confirm( ipb_lang_js_del_1 )) {
		window.location.href=theURL;
	}
	else {
		alert ( ipb_lang_js_del_2 );
	} 
}

function gallery_toggleview(id)  {
	if ( ! id ) return;
	
	if ( itm = my_getbyid(id) )	{
		if (itm.style.display == "none") {
			my_show_div(itm);
		}
		else {
			my_hide_div(itm);
		}
	}
}

function gallery_toggle_pid( id )
{
	saved = new Array();
	clean = new Array();
	add   = 1;
	
	//-----------------------------------
	// Get form info
	//-----------------------------------
	
	tmp = document.modform.selectedgcids.value;
	
	saved = tmp.split(",");
	
	//-----------------------------------
	// Remove bit if exists
	//-----------------------------------
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != "" )
		{
			if ( saved[i] == id)
			{
				 add = 0;
			}
			else
			{
				clean[clean.length] = saved[i];
			}
		}
	}
	
	//-----------------------------------
	// Add?
	//-----------------------------------
	
	if ( add )
	{
		clean[ clean.length ] = id;
		eval("document.pid"+id+".src=selectedbutton");
	}
	else
	{
		eval(" document.pid"+id+".src=unselectedbutton");
	}
	
	newvalue = clean.join(',');
	
	my_setcookie( 'modgcids', newvalue, 0 );
	
	document.modform.selectedgcids.value = newvalue;
	
	newcount = stacksize(clean);
	
	document.modform.gobutton.value = lang_gobutton + '(' + newcount + ')';
	
	return false;
}

/*--------------------------------------------*/
// Multi quote ( From ibf_topic.js )
/*--------------------------------------------*/

function multiquote_add(id)
{
	saved = new Array();
	clean = new Array();
	add   = 1;
	
	//-----------------------------------
	// Get any saved info
	//-----------------------------------
	
	if ( tmp = my_getcookie('gal_pids') )
	{
		saved = tmp.split(",");
	}
	
	//-----------------------------------
	// Remove bit if exists
	//-----------------------------------
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != "" )
		{
			if ( saved[i] == id )
			{
				 add = 0;
			}
			else
			{
				clean[clean.length] = saved[i];
			}
		}
	}
	
	//-----------------------------------
	// Add?
	//-----------------------------------
	
	if ( add )
	{
		clean[ clean.length ] = id;
		eval("document.mad_"+id+".src=removequotebutton");
	}
	else
	{
		eval(" document.mad_"+id+".src=addquotebutton");
	}
	
	my_setcookie( 'gal_pids', clean.join(','), 0 );
	
	return false;
}

function gallery_toggle_img( id )
{
	saved = new Array();
	clean = new Array();
	add   = 1;
	
	//-----------------------------------
	// Get form info
	//-----------------------------------
	
	tmp = document.modform.selectedimgids.value;
	
	saved = tmp.split(",");
	
	//-----------------------------------
	// Remove bit if exists
	//-----------------------------------
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != "" )
		{
			if ( saved[i] == id)
			{
				 add = 0;
			}
			else
			{
				clean[clean.length] = saved[i];
			}
		}
	}
	
	//-----------------------------------
	// Add?
	//-----------------------------------
	
	if ( add )
	{
		clean[ clean.length ] = id;
		eval("document.img"+id+".src=selectedbutton");
	}
	else
	{
		eval(" document.img"+id+".src=unselectedbutton");
	}
	
	newvalue = clean.join(',');
	
	my_setcookie( 'modimgids', newvalue, 0 );
	
	document.modform.selectedimgids.value = newvalue;
	
	newcount = stacksize(clean);
	
	document.modform.gobutton.value = lang_gobutton + '(' + newcount + ')';
	
	return false;
}