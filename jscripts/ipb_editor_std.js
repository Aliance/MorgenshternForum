//------------------------------------------
// Invision Power Board 2.1.7
// STD Editor Functions
// (c) 2005 Invision Power Services, Inc.
//
// http://www.invisionboard.com
// SHOULD BE CALLED AFTER ipb_editor.js
//------------------------------------------

/*-------------------------------------------------------------------------*/
// INIT
/*-------------------------------------------------------------------------*/

var i_am = 'std';
var rtewindow;
var buttonbar;

var opentags =
{
	'b'          : 0,
	'i'          : 0,
	's'          : 0,
	'u'          : 0,
	'hide'       : 0,
	'img'        : 0,
	'quote'      : 0,
	'left'       : 0,
	'center'     : 0,
	'right'      : 0,
	'color'      : 0,
	'background' : 0,
	'indent'     : 0,
	'code'       : 0
};

var tags_to_div =
{
	'b'          : 'do_bold',
	'i'          : 'do_italic',
	's'          : 'do_strikethrough',
	'u'          : 'do_underline',
	'hide'       : 'do_hide',
	'quote'      : 'do_quote',
	'left'       : 'do_justifyleft',
	'center'     : 'do_justifycenter',
	'right'      : 'do_justifyright',
	'color'      : 'forecolor',
	'background' : 'hilitecolor',
	'indent'     : 'do_indent',
	'code'       : 'do_code'
};

var rte_to_std =
{
	'forecolor'   : 'color',
	'hilitecolor' : 'background'
};

var easymode = 0;

// IE bug fix
var ie_range_cache = '';

/*-------------------------------------------------------------------------*/
// INIT
/*-------------------------------------------------------------------------*/

function init_std_editor()
{
	//--------------------------------------------
	// Pull in width of textarea
	//--------------------------------------------
	
	try
	{
		rtewindow = document.getElementById( 'postcontent' );
		oldwidth  = parseInt(document.getElementById( 'postcontent' ).style.width);
		buttonbar = document.getElementById( 'std-table-buttons' );
		widthunit = document.getElementById( 'postcontent' ).style.width.match( /%/ ) ? '%' : 'px';
		
		if ( widthunit == 'px' )
		{
			if ( is_opera )
			{
				document.getElementById( 'postcontent' ).style.width = oldwidth - 4 + widthunit;
				buttonbar.style.width = parseInt( buttonbar.style.width ) - 4 + widthunit;
			}
			else if ( is_safari )
			{
			
			}
			else
			{
				document.getElementById( 'postcontent' ).style.width = oldwidth - 6 + widthunit;
			}
		}
		else
		{
			if ( is_ie || is_moz )
			{
				document.getElementById( 'postcontent' ).style.width = oldwidth - 1 + widthunit;
			}
		}
	}
	catch(e)
	{}
	
	cvalue = my_getcookie( "bbmode" );
	
	if ( cvalue == 1 )
	{
		document.getElementById('togglebbmode').value = js_bbeasy_on;
		easymode = 1;
	}
	else
	{
		document.getElementById('togglebbmode').value = js_bbeasy_off;
		easymode = 0;
	}
}

/*-------------------------------------------------------------------------*/
// Toggle button highlighting (used when clicked on)
/*-------------------------------------------------------------------------*/

function toggle_button( tag )
{
	//--------------------------------------------
	// Change the button status
	// Ensure we're not looking for FONT, SIZE or COLOR as these
	// buttons don't exist, they are select lists instead.
	//--------------------------------------------
			
	if ( (tag == 'font') || (tag == 'size') )
	{
		return;
	}
	
	if ( opentags[ tag ] )
	{
		document.getElementById( tags_to_div[ tag ] ).className = 'rteimage';
		
		// Turn off
		opentags[ tag ] = 0;
	}
	else
	{
		document.getElementById( tags_to_div[ tag ] ).className = 'rteImageRaised';
		
		// Turn on
		opentags[ tag ] = 1;
	}
}

/*-------------------------------------------------------------------------*/
// Set BBCode mode
/*-------------------------------------------------------------------------*/

function toggle_bbmode(mVal)
{
	if ( easymode )
	{
		document.getElementById('togglebbmode').value = js_bbeasy_off;
		easymode = 0;
	}
	else
	{
		document.getElementById('togglebbmode').value = js_bbeasy_on;
		easymode = 1;
	}
	
	my_setcookie( 'bbmode', easymode, 1 );
}

/*-------------------------------------------------------------------------*/
// Get easy mode state
/*-------------------------------------------------------------------------*/

function get_easy_mode_state()
{
	//--------------------------------------------
	// Returns true if we've chosen easy mode
	//--------------------------------------------
	
	if ( easymode )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/*-------------------------------------------------------------------------*/
// Pop open and set up the color palette
/*-------------------------------------------------------------------------*/

function popcolor( command )
{
	//-------------------------------
	// Already open?
	//-------------------------------
	
	if ( opentags[ rte_to_std[ command ] ] )
	{
		//--------------------------------------------
		// Smart close tags
		//--------------------------------------------
	   
		smart_close_tags( rte_to_std[ command ] );
		
		return false;
	}
	else
	{
		//-------------------------------
		// Save current values
		//-------------------------------
		
		parent.command = command;
		
		if ( is_ie )
		{
			postfieldobj.focus();
			ie_range_cache = document.selection.createRange();
		}
		
		//-------------------------------
		// Position and show color palette
		//-------------------------------
		
		buttonElement = document.getElementById(command);
		
		var iLeftPos  = getOffsetLeft(buttonElement);
		var iTopPos   = getOffsetTop(buttonElement) + (buttonElement.offsetHeight + 30);
		
		document.getElementById('cp').style.left = (iLeftPos) + "px";
		document.getElementById('cp').style.top  = (iTopPos)  + "px";
		
		if (document.getElementById('cp').style.visibility == "hidden")
		{
			document.getElementById('cp').style.visibility = "visible";
			document.getElementById('cp').style.display    = "inline";
		}
		else
		{
			document.getElementById('cp').style.visibility = "hidden";
			document.getElementById('cp').style.display    = "none";
		}
	}
}

/*-------------------------------------------------------------------------*/
// Write the color
/*-------------------------------------------------------------------------*/

function setColor(color)
{
	var parentCommand = parent.command;
	
	if ( parentCommand == "hilitecolor" )
	{
		if ( wrap_tags("[background=" +color+ "]", "[/background]", true ) )
		{
			toggle_button( "background" );
			pushstack(bbtags, "background");
		}
	}
	else
	{
		if ( wrap_tags("[color=" +color+ "]", "[/color]", true ) )
		{
			toggle_button( "color" );
			pushstack(bbtags, "color");
		}
	}

	document.getElementById('cp').style.visibility = "hidden";
	document.getElementById('cp').style.display    = "none";
}

/*-------------------------------------------------------------------------*/
// EMOTICONS
/*-------------------------------------------------------------------------*/

function emoticon(theSmilie)
{
	doInsert(" " + theSmilie + " ", "", false);

	if ( (ua_vers >= 4) && is_ie && is_win && emowindow != 'undefined' )
	{
		emowindow.focus();
	}
}

/*-------------------------------------------------------------------------*/
// ALTER FONT
/*-------------------------------------------------------------------------*/

function alterfont(theval, thetag)
{
    if (theval == 0)
    {
    	return;
	}
	
	if ( wrap_tags("[" + thetag + "=" + theval + "]", "[/" + thetag + "]", true ) )
	{
		pushstack(bbtags, thetag);
	}
	
    postformobj.ffont.selectedIndex  = 0;
    postformobj.fsize.selectedIndex  = 0;
}

/*-------------------------------------------------------------------------*/
// SIMPLE TAGS (such as B, I S U, etc)
/*-------------------------------------------------------------------------*/

function simpletag(thetag)
{
	var tagOpen = opentags[ thetag ];
	
	if ( get_easy_mode_state() )
	{
		inserttext = prompt(prompt_start + "\n[" + thetag + "]xxx[/" + thetag + "]");
		
		if ( (inserttext != null) && (inserttext != "") )
		{
			wrap_tags("[" + thetag + "]" + inserttext + "[/" + thetag + "] ", "", false);
		}
	}
	else
	{
		if ( tagOpen == 0 )
		{
			if ( wrap_tags("[" + thetag + "]", "[/" + thetag + "]", true ) )
			{
				//--------------------------------------------
				// Toggle
				//--------------------------------------------
				
				toggle_button( thetag );
		
				pushstack(bbtags, thetag);
			}
		}
		else
		{
			//--------------------------------------------
			// Smart close tags
			//--------------------------------------------
			
			smart_close_tags( thetag );
		}
	}
}

/*-------------------------------------------------------------------------*/
// List tag
/*-------------------------------------------------------------------------*/

function tag_list( type )
{
	var listvalue = "init";
	var thelist   = "";
	
	opentag = ( type == 'ordered' ) ? '[list=1]' : '[list]';
	
	while ( (listvalue != "") && (listvalue != null) )
	{
		listvalue = prompt(list_prompt, "");
		
		if ( (listvalue != "") && (listvalue != null) )
		{
			thelist = thelist+"[*]"+listvalue+"\n";
		}
	}
	
	if ( thelist != "" )
	{
		wrap_tags( opentag + "\n" + thelist + "[/list]\n", "");
	}
}

/*-------------------------------------------------------------------------*/
// URL tag
/*-------------------------------------------------------------------------*/

function tag_url()
{
	var FoundErrors = '';
	
	//----------------------------------------
	// Do we have ranged text?
	//----------------------------------------
	
	if ( check_range() )
	{
		//----------------------------------------
		// Yes, just ask for the URL and wrap
		//----------------------------------------
		
		var enterURL = prompt(text_enter_url, "http://");
		
		if ( ! enterURL)
		{
			alert(error_no_url);
			return;
		}
    
		wrap_tags( "[url="+enterURL+"]", "[/url]", "" );
	}
	else
	{
		var enterURL    = prompt(text_enter_url, "http://");
		var enterTITLE  = prompt(text_enter_url_name, jsfile_myweb_lang );
	
		if ( ! enterURL)
		{
			FoundErrors += " " + error_no_url;
		}
		if ( ! enterTITLE)
		{
			FoundErrors += " " + error_no_title;
		}
	
		if ( FoundErrors )
		{
			alert( jsfile_error_lang +FoundErrors);
			return;
		}
	
		wrap_tags("[url="+enterURL+"]"+enterTITLE+"[/url]", "");
	}
}

/*-------------------------------------------------------------------------*/
// Image tag
/*-------------------------------------------------------------------------*/

function tag_image()
{
	var FoundErrors = '';
	
	//----------------------------------------
	// Do we have ranged text?
	//----------------------------------------
	
	if ( check_range() )
	{
		//----------------------------------------
		// Yes, just wrap text
		//----------------------------------------
		
		wrap_tags( "[img]", "[/img]", "" );
	}
	else
	{
		var enterURL = prompt(text_enter_image, "http://");
	
		if ( ! enterURL )
		{ 
			alert(error_no_url); 
			return; 
		}
	
		wrap_tags("[img]"+enterURL+"[/img]", "");
	}
}

/*-------------------------------------------------------------------------*/
// Email tag
/*-------------------------------------------------------------------------*/

function tag_email()
{
	//----------------------------------------
	// Do we have ranged text?
	//----------------------------------------
	
	if ( check_range() )
	{
		//----------------------------------------
		// Yes, just wrap text
		//----------------------------------------
		
		wrap_tags( "[email]", "[/email]", "" );
	}
	else
	{
		var emailAddress = prompt(text_enter_email, "");
	
		if ( ! emailAddress )
		{ 
			alert(error_no_email); 
			return; 
		}
	
		wrap_tags( "[email]"+emailAddress+"[/email]", "" );
	}
}

/*-------------------------------------------------------------------------*/
// Do we have ranged text?
/*-------------------------------------------------------------------------*/

function check_range()
{
	var has_range = false;
	
	//----------------------------------------
	// It's IE!
	//----------------------------------------
	
	if ( (ua_vers >= 4) && is_ie && is_win )
	{
		var sel = document.selection;
		var rng = sel.createRange();
		rng.colapse;
		
		if ( (sel.type == "Text" || sel.type == "None") && rng != null )
		{
			if ( rng.text.length > 0)
			{
				has_range = true;
			}
		}
	}
	
	//----------------------------------------
	// It's MOZZY!
	//----------------------------------------
	
	else if ( postfieldobj.selectionEnd )
	{ 
		var ss = postfieldobj.selectionStart;
		var st = postfieldobj.scrollTop;
		var es = postfieldobj.selectionEnd;
		
		if (es <= 2)
		{
			es = postfieldobj.textLength;
		}
		
		var start  = (postfieldobj.value).substring(0, ss);
		var middle = (postfieldobj.value).substring(ss, es);
		var end    = (postfieldobj.value).substring(es, postfieldobj.textLength);
		
		//-----------------------------------
		// text range?
		//-----------------------------------
		
		if (postfieldobj.selectionEnd - postfieldobj.selectionStart > 0)
		{
			has_range = true;
		}
	}
	
	return has_range;
}

/*-------------------------------------------------------------------------*/
// Check message length
/*-------------------------------------------------------------------------*/

function check_length()
{
	MessageLength  = postfieldobj.value.length;
	message        = "";
	
	if (MessageMax > 0)
	{
		message = js_post + ": " + js_max_length + "  " + MessageMax + " " + js_characters +".";
	}
	else
	{
		message = "";
	}
			
	alert(message + "      " + js_used + " " + MessageLength + " " + js_characters + ".");
}

/*-------------------------------------------------------------------------*/
// GENERAL INSERT FUNCTION
// opentext : opening tag
// closetext: closing tag, used if we have selected text
/*-------------------------------------------------------------------------*/

function wrap_tags(opentext, closetext, issingle)
{
	var has_closed = false;
	
	if ( ! issingle )
	{
		issingle = false;
	}
	
	//----------------------------------------
	// It's IE!
	//----------------------------------------
	
	if ( (ua_vers >= 4) && is_ie && is_win )
	{
		if ( postfieldobj.isTextEdit )
		{
			postfieldobj.focus();

			var sel = document.selection;
			
			var rng = ie_range_cache ? ie_range_cache : sel.createRange();

			var stored_range = rng.duplicate()
			stored_range.moveToElementText( postfieldobj );
			stored_range.setEndPoint( 'EndToEnd', rng );

			// Stores start position of text selection
			postfieldobj.selectionStart = stored_range.text.length - rng.text.length;
			// Stores end position of text selection
			postfieldobj.selectionEnd = postfieldobj.selectionStart + rng.text.length;

			rng.colapse;
			
			if ( (sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if (closetext != "" && rng.text && rng.text.length > 0)
				{ 
					opentext += rng.text + closetext;
				}
				else if ( issingle )
				{
					has_closed = true;
				}

				rng.text = rng.text.charAt(rng.text.length - 1) == ' ' ? opentext + ' ' : opentext;
			}
			else
			{
				postfieldobj.value += opentext;
				has_closed = true;
			}
		}
		else
		{
			postfieldobj.value += opentext;
			has_closed = true;
		}

		ie_range_cache = null;

		rng.select();
	}
	
	//----------------------------------------
	// It's MOZZY!
	//----------------------------------------
	
	else if ( postfieldobj.selectionEnd )
	{
		var ss = postfieldobj.selectionStart;
		var st = postfieldobj.scrollTop;
		var es = postfieldobj.selectionEnd;
		
		if (es <= 0)
		{
			es = postfieldobj.textLength;
		}
		
		var start  = (postfieldobj.value).substring(0, ss);
		var middle = (postfieldobj.value).substring(ss, es);
		var end    = (postfieldobj.value).substring(es, postfieldobj.textLength);
		
		//-----------------------------------
		// text range?
		//-----------------------------------
		
		if ( postfieldobj.selectionEnd - postfieldobj.selectionStart > 0 )
		{
			middle = opentext + middle + closetext;
		}
		else
		{
			middle = opentext + middle;
			
			if ( issingle )
			{
				has_closed = true;
			}
		}
		
		postfieldobj.value = start + middle + end;
		
		var cpos = ss + (middle.length);
		
		postfieldobj.selectionStart = cpos;
		postfieldobj.selectionEnd   = cpos;
		postfieldobj.scrollTop      = st;
	}
	
	//----------------------------------------
	// It's CRAPPY!
	//----------------------------------------
	
	else
	{ 
		if ( issingle )
		{
			has_closed = false;
		}
			
		postfieldobj.value += opentext + ' ' + closetext;
	}
	
	postfieldobj.focus();

	return has_closed;
}	

/*--------------------------------------------*/
// Make current STD window (n)px bigger
/*--------------------------------------------*/

function std_window_resize( pix )
{
	var box        = postfieldobj;
	var cur_height = parseInt( box.style.height ) ? parseInt( box.style.height ) : 300;
	var new_height = cur_height + pix;
	
	if ( new_height > 0 )
	{
		box.style.height = new_height + "px";
	}
	
	return false;
}

/*-------------------------------------------------------------------------*/
// Validate form (must always have this method defined!)
/*-------------------------------------------------------------------------*/

function ValidateForm( isMsg )
{
	MessageLength  = postfieldobj.value.length;
	errors         = "";
	
	//-----------------------------------------
	// Check for remove attachments
	//-----------------------------------------
	
	try
	{
		if ( postformobj.removeattachid.value > 0 )
		{
			okdelete = confirm( js_remove_attach );
	
			if ( okdelete == true )
			{
				return true;
			}
			else
			{
				postformobj.removeattachid.value = 0;
				return false;
			}
		}
	}
	catch(error)
	{
		//
	}

	if ( isMsg == 1)
	{
		if ( postformobj.msg_title.value.length < 2 )
		{
			errors = js_msg_no_title;
		}
	}
	
	if ( MessageLength < 2 )
	{
		errors = js_no_message;
	}

	if ( MessageMax !=0 )
	{
		if (MessageLength > MessageMax)
		{
			errors = js_max_length + " " + MessageMax + " " + js_characters + ". " + js_current + ": " + MessageLength;
		}
	}
	
	if ( errors != "" && ! Override )
	{
		alert(errors);
		return false;
	}
	
	else
	{
		try
		{
			postformobj.submit.disabled = true;
		}
		catch(e)
		{
			try
			{
				postformobj.dosubmit.disabled = true;
			}
			catch(e) { }
		}

		return true;
	}
}


function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = fombj.Post;
	
	//----------------------------------------
	// It's IE!
	//----------------------------------------
	if ( (ua_vers >= 4) && is_ie && is_win)
	{
		if (obj_ta.isTextEdit)
		{
			obj_ta.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if(ibClsTag != "" && rng.text.length > 0)
					ibTag += rng.text + ibClsTag;
				else if(isSingle)
					isClose = true;
	
				rng.text = ibTag;
			}
		}
		else
		{
			if(isSingle)
			{
				isClose = true;
			}
			
			obj_ta.value += ibTag;
		}
	}
	//----------------------------------------
	// It's MOZZY!
	//----------------------------------------
	
	else if ( obj_ta.selectionEnd )
	{ 
		var ss = obj_ta.selectionStart;
		var st = obj_ta.scrollTop;
		var es = obj_ta.selectionEnd;
		
		if (es <= 2)
		{
			es = obj_ta.textLength;
		}
		
		var start  = (obj_ta.value).substring(0, ss);
		var middle = (obj_ta.value).substring(ss, es);
		var end    = (obj_ta.value).substring(es, obj_ta.textLength);
		
		//-----------------------------------
		// text range?
		//-----------------------------------
		
		if (obj_ta.selectionEnd - obj_ta.selectionStart > 0)
		{
			middle = ibTag + middle + ibClsTag;
		}
		else
		{
			middle = ibTag + middle;
			
			if (isSingle)
			{
				isClose = true;
			}
		}
		
		obj_ta.value = start + middle + end;
		
		var cpos = ss + (middle.length);
		
		obj_ta.selectionStart = cpos;
		obj_ta.selectionEnd   = cpos;
		obj_ta.scrollTop      = st;


	}
	//----------------------------------------
	// It's CRAPPY!
	//----------------------------------------
	else
	{
		if (isSingle)
		{
			isClose = true;
		}
		
		obj_ta.value += ibTag;
	}
	
	obj_ta.focus();

	return isClose;
}	
var bbtags   = new Array();

var fombj    = document.REPLIER;