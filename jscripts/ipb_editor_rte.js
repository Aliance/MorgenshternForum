//------------------------------------------------------------------------------
// IPS Cross-Browser Rich Text Editor
//------------------------------------------------------------------------------
// Supports Mozilla 1.3+ (Firefox, etc) and IE 5.5+
// (c) 2005 Invision Power Services, Inc.
// http://www.invisionpower.com
// http://www.ibresource.ru
//------------------------------------------------------------------------------


/*--------------------------------------------*/
// INIT variables
/*--------------------------------------------*/

var i_am = 'rte';

//document.getElementById( 'debugmsg' ).value
var DEBUG      = 0;

var isRichText = false;
var css_off    = false;
var rng;
var currentRTE;
var allRTEs = "";
var rte     = "";

// Defined in enable_design_mode
var rtewindow;
var rtedocument;
var rteready;


var isIE;
var isGecko;
var isSafari;
var isKonqueror;

var imagesPath;
var includesPath;

//-------------------------------
// Set global scope
//-------------------------------

var g_buttons;
var g_rte;
var g_tablewidth;
var g_width_unit;
var g_imagesPath;
var g_width;
var g_height;
var g_readOnly;
var g_includesPath
var g_imagesPath;
var g_html;
var g_DEBUG;

//-------------------------------
// Font & size
//-------------------------------

var currentfont;
var currentsize;
var button_status = new Array();

//-------------------------------
// Viewing HTML?
//-------------------------------

var viewingHTML = 0;

//-------------------------------
// Viewing Full screen?
//-------------------------------

var viewingFS = 0;

//-------------------------------
// Define which buttons change
// when clicked
//-------------------------------

var buttons_update = new Array(
	"bold",
	"italic",
	"underline",
	"strikethrough",
	"hide",
	//"superscript",
	//"subscript",
	"justifyleft",
	"justifycenter",
	"justifyright",
	"insertorderedlist",
	"insertunorderedlist"
);

var opentags =
{
	'bold'          : 0,
	'italic'        : 0,
	'strikethrough' : 0,
	'underline'     : 0,
	'hide'	    	: 0,
	'forecolor'     : 0,
	'fontname'      : 0,
	'fontsize'      : 0
};

/*--------------------------------------------*/
// INIT RTE
/*--------------------------------------------*/

function init_rte( imgPath, incPath )
{
	//-------------------------------
	// Set browser vars
	//-------------------------------
	
	var ua      = navigator.userAgent.toLowerCase();
	isIE        = ((ua.indexOf("msie")     != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1)); 
	isGecko     = (ua.indexOf("gecko")     != -1);
	isSafari    = (ua.indexOf("safari")    != -1);
	isKonqueror = (ua.indexOf("konqueror") != -1);

	//-------------------------------
	// Check to see if designMode mode
	// is available. Should be for:
	// IE5.5+ Mozilla 1.3+
	// Safari 2+
	//-------------------------------
	
	if ( document.getElementById && document.designMode && ! isSafari &&!isKonqueror )
	{
		isRichText = true;
	}
	
	if ( isIE )
	{
		document.onmouseover = ie_raise_button;
		document.onmouseout  = ie_normal_button;
		document.onmousedown = ie_lower_button;
		document.onmouseup   = ie_raise_button;
	}
	
	//-------------------------------
	// Set paths vars
	//-------------------------------
	
	imagesPath   = imgPath;
	includesPath = incPath;
	
	//-------------------------------
	//for testing standard textarea, uncomment the following line
	//isRichText = false;
	//-------------------------------
}

/*--------------------------------------------*/
// Write Rich text to screen
/*--------------------------------------------*/

function write_rte_editor(fieldname, html, width, height, buttons, readOnly)
{
	rte  = fieldname;
	
	if ( isRichText )
	{
		if (allRTEs.length > 0)
		{
			allRTEs += ";";
		}
		
		allRTEs += rte;
		
		write_rte(html, width, height, buttons, readOnly);
	}
	else
	{
		write_default(html, width, height, buttons, readOnly);
	}
}

/*--------------------------------------------*/
// Write the default info
/*--------------------------------------------*/

function write_default(html, width, height, buttons, readOnly)
{
	if ( ! readOnly )
	{
		document.writeln('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;">' + html + '</textarea>');
	}
	else
	{
		document.writeln('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;" readonly>' + html + '</textarea>');
	}
}

/*--------------------------------------------*/
// Display RTE
/*--------------------------------------------*/

function write_rte(html, width, height, buttons, readOnly)
{
	if (readOnly) buttons = false;
	
	var o_width  = width;
	var o_height = height;
	
	width  = parseInt( width );
	height = parseInt( height );
	
	//-------------------------------
	// Adjust minimum table widths
	//-------------------------------
	
	if ( isIE )
	{
		if ( ! o_width.match( /%/ ) )
		{
			if ( buttons && (width < 400) )
			{
				width = 400;
			}
			
			g_width_unit   = 'px';
			var tablewidth = width + 4;
			
		}
		else
		{
			g_width_unit   = '%';
			var tablewidth = width ;
			width          = width;
		}
		
	}
	else
	{
		if ( ! o_width.match( /%/ ) )
		{
			if ( buttons && (width < 400) )
			{
				width = 400;
			}
			
			g_width_unit   = 'px';
			var tablewidth = width + 2;
		}
		else
		{
			g_width_unit   = '%';
			var tablewidth = width;
			width          = width;
		}
	}
	
	//-------------------------------
	// Globalize
	//-------------------------------
	
	g_buttons      = buttons;
	g_rte          = rte;
	g_tablewidth   = tablewidth;
	g_imagesPath   = imagesPath;
	g_width        = width;
	g_height       = height;
	g_readOnly     = readOnly;
	g_includesPath = includesPath;
	g_imagesPath   = imagesPath;
	g_html         = html;
	g_DEBUG        = DEBUG;
	
	boo_to_int     = isRichText == true ? 1 : 0;
	
	//-------------------------------
	// Use HTML from rte_html.js
	//-------------------------------
	
	document.writeln( "<input type='hidden' id='wysiwyg_used' name='wysiwyg_used' value='" + boo_to_int + "' />" );
	
	write_rte_html();
	
	document.getElementById('hdn_rte_content').value = html;
	enable_design_mode( make_newlines_safe(html), readOnly );
	
	//-------------------------------
	// Make buttons unselectable
	//-------------------------------
	
	poss_images = document.getElementsByTagName('img');
	
	if ( poss_images.length )
	{
		for( var i in poss_images )
		{
			if ( poss_images[i].className == 'rteimage' )
			{
				poss_images[i].unselectable = true;
			}
		}
	}

	//-----------------------------------------------------------
	// This setting makes hitting the enter key in IE turn into a
	// newline (br tag) instead of the default action of using a
	// paragraph tag.  This causes the indent and list options not
	// to work properly in IE, so do not enable this unless you
	// don't care about those options.  This functionality will be
	// expanded upon in a future release, however this is not due
	// to an issue with IPB, but rather due to how IE handles the
	// javascript exec_command functions, and is beyond our control
	// without a lot of javascript rewriting of those functions,
	// specific to IE behaviors.
	//-----------------------------------------------------------

	var ie_ptags_to_newlines = false;

	if( isIE && ie_ptags_to_newlines )
	{
		rtewindow = frames[rte];

      	rtewindow.document.onkeydown = function () {
          		if (rtewindow.event.keyCode == 13)
			{  // ENTER
				//wrap_tags( "<br />\n", "\n" );

				var sel = rtewindow.document.selection;
				var ts  = rtewindow.document.selection.createRange();

				var t   = ts.htmlText.replace(/<p([^>]*)>(.*)<\/p>/i, '$2');
		
				if ( (sel.type == "Text" || sel.type == "None") )
				{
					ts.pasteHTML( "<br />" + t + "\n" );
				}
				else
				{
					rtewindow.document.body.innerHTML += "<br />\n";
				}

            		rtewindow.event.returnValue = false;
				ts.select();
				rtewindow.focus();

          		}
  		}
	}
}

/*--------------------------------------------*/
// Enable Design Mode
/*--------------------------------------------*/

function enable_design_mode(html, readOnly)
{
	//-------------------------------
	// Got char set?
	//-------------------------------
	
	var use_charset = '';
	
	try
	{
		use_charset = g_CHARSET;
	}
	catch(e)
	{
	}
	
	use_charset = use_charset ? use_charset : 'windows-1251';

	//-------------------------------
	// Print...
	//-------------------------------
	
	var frameHtml = "<html id=\"" + rte + "\">\n";
	frameHtml += "<head>\n";
	frameHtml += '<meta http-equiv="content-type" content="text/html; charset=' + use_charset  + '" />' + "\n";
	frameHtml += "<style type='text/css' media='all'>\n";
	frameHtml += "body {\n";
	frameHtml += "	background: #FFFFFF;\n";
	frameHtml += "	margin: 0px;\n";
	frameHtml += "	padding: 4px;\n";
	frameHtml += "	font-family: arial, verdana, sans-serif;\n";
	frameHtml += "	font-size: 10pt;\n";
	frameHtml += "}\n";
	frameHtml += "p {\n";
	frameHtml += "	margin:0px;\n";
	frameHtml += "	padding:0px;\n";
	frameHtml += "}\n";

	
	frameHtml += "</style>\n";
	frameHtml += "</head>\n";
	frameHtml += "<body>\n";
	frameHtml += html + "\n";
	frameHtml += "</body>\n";
	frameHtml += "</html>";
	
	//alert( frameHtml );
	
	//-------------------------------
	// IE
	//-------------------------------
	
	if ( isIE )
	{
		try
		{
			rtewindow = frames[rte];
			rtewindow.document.open();
			rtewindow.document.write(frameHtml);
			rtewindow.document.close();
		}
		catch(e)
		{
			alert( e );
		}

		if ( ! readOnly )
		{
			rtewindow.document.designMode = "On";
		}
		
		rtewindow.document.onmouseup = rte_button_update;
		rtewindow.document.onkeyup   = rte_button_update;
	}
	else
	{
		try
		{
			rtewindow   = document.getElementById(rte).contentWindow;
			rtedocument = document.getElementById(rte).contentDocument;
			
			if ( ! readOnly )
			{
				rtedocument.designMode = "on";
			}
			
			try
			{
				rtewindow.document.open();
				rtewindow.document.write(frameHtml);
				rtewindow.document.close();
				
				rtewindow.document.body.style.fontSize = '10pt';
				
				rtewindow.document.addEventListener("mouseup", rte_button_update, true);
				rtewindow.document.addEventListener("keyup"  , rte_button_update, true);
		
				if ( isGecko && ! readOnly )
				{
					//attach a keyboard handler for gecko browsers to make keyboard shortcuts work
					rtewindow.document.addEventListener("keypress", kb_handler, true);
				}
			}
			catch (e) 
			{
				alert(jsfile_errorc_lang + e);
			}
		}
		catch (e)
		{
			//-------------------------------
			// Gecko may take some time to enable
			// design mode.
			// Keep looping until able to set.
			//-------------------------------
			
			if ( isGecko )
			{
				setTimeout("enable_design_mode('" + rte + "', '" + make_newlines_safe(html) + "', " + readOnly + ");", 10);
			}
			else
			{
				return false;
			}
		}
	}
	
	//-------------------------------
	// Set up buttons
	//-------------------------------
	
	for ( var i in buttons_update )
	{
		button_status[ buttons_update[i] ] = false;
	}
	
	rteready = 1;
}

/*--------------------------------------------*/
// Update single RTE
/*--------------------------------------------*/

function update_rte()
{
	if (!isRichText) return;
	
	//-------------------------------
	// Set message value
	//-------------------------------
	
	var oHdnMessage = document.getElementById('hdn_rte_content');
	var readOnly    = false;
	
	//-------------------------------
	// Check for readOnly mode
	//-------------------------------
	
	if ( isIE )
	{
		if ( rtewindow.document.designMode != "On" )
		{
			readOnly = true;
		}
	}
	else
	{
		if ( rtedocument.designMode != "on" )
		{
			readOnly = true;
		}
	}
	
	if ( isRichText && ! readOnly )
	{
		//-------------------------------
		// If viewing source, switch back
		// to design view
		//-------------------------------
		
		if ( viewingHTML ) 
		{
			toggleHTMLSrc();
		}
		
		if (oHdnMessage.value == null)
		{
			oHdnMessage.value = "";
		}
		
		oHdnMessage.value = rtewindow.document.body.innerHTML;

		//-------------------------------
		// If there is no content (other
		// than formatting) set value to nothing
		//-------------------------------
		
		if (strip_html(oHdnMessage.value.replace("&nbsp;", " ")) == "" 
			&& oHdnMessage.value.toLowerCase().search("<hr") == -1
			&& oHdnMessage.value.toLowerCase().search("<img") == -1) oHdnMessage.value = "";
			
		//-------------------------------
		// Fix for gecko
		//-------------------------------
		
		if (escape(oHdnMessage.value) == "%3Cbr%3E%0D%0A%0D%0A%0D%0A")
		{
			oHdnMessage.value = "";
		}
	}
}

/*--------------------------------------------*/
// Toggle HTML src
/*--------------------------------------------*/

function toggleHTMLSrc()
{
	if ( viewingHTML == 0)
	{
		document.getElementById("Buttons1").style.visibility = "hidden";
		document.getElementById("Buttons2").style.visibility = "hidden";
		
		viewingHTML = 1;
		
		//-------------------------------
		// Change tab colour
		//-------------------------------
		
		try
		{
			document.getElementById("chkSrc").className = 'rtebottombuttonon';
		}
		catch ( e )
		{
			// Ssshhh...
		}
		
		if ( isIE )
		{
			rtewindow.document.body.innerText = rtewindow.document.body.innerHTML;
		}
		else
		{
			if ( isGecko )
			{
				rtewindow.document.designMode = 'off';
			}
			
			var htmlSrc = rtewindow.document.createTextNode( rtewindow.document.body.innerHTML );
			
			rtewindow.document.body.innerHTML = "";
			rtewindow.document.body.appendChild(htmlSrc);
			
			rtewindow.document.body.innerHTML = clean_up_html_to_show( rtewindow.document.body.innerHTML );
		}
	}
	else
	{
		document.getElementById("Buttons1").style.visibility = "visible";
		document.getElementById("Buttons2").style.visibility = "visible";
		
		viewingHTML = 0;
		
		//-------------------------------
		// Change tab colour
		//-------------------------------
		
		try
		{
			document.getElementById("chkSrc").className = 'rtebottombutton';
		}
		catch ( e )
		{
			// Ssshhh...
		}
		
		if ( isIE )
		{
			//-------------------------------
			// Fix for IE
			//-------------------------------
			
			var output = escape(rtewindow.document.body.innerText);
			output     = output.replace("%3CP%3E%0D%0A%3CHR%3E", "%3CHR%3E");
			output     = output.replace("%3CHR%3E%0D%0A%3C/P%3E", "%3CHR%3E");
			
			rtewindow.document.body.innerHTML = unescape(output);
		}
		else
		{
			if ( isGecko )
			{
				rtewindow.document.designMode = 'on';
			}
			
			var htmlSrc = rtewindow.document.body.ownerDocument.createRange();
			
			htmlSrc.selectNodeContents(rtewindow.document.body);
			
			rtewindow.document.body.innerHTML = htmlSrc.toString();
		}
	}
}

/*--------------------------------------------*/
// Function to clean up HTML to show
/*--------------------------------------------*/

function clean_up_html_to_show( t )
{
	if ( t == "" || t == 'undefined' )
	{
		return t;
	}
	
	//-------------------------------
	// Sort out BR tags
	//-------------------------------
	
	t = t.replace( /&lt;br&gt;/ig, "&lt;br /&gt;");
	
	//-------------------------------
	// Remove empty &lt;p&gt; tags
	//-------------------------------
	
	t = t.replace( /&lt;p&gt;(\s+?)?&lt;\/p&gt;/ig, "");
	
	//-------------------------------
	// HR issues
	//-------------------------------
	
	t = t.replace( /&lt;p&gt;&lt;hr \/&gt;&lt;\/p&gt;/ig                   , "&lt;hr /&gt;"); 
	t = t.replace( /&lt;p&gt;&nbsp;&lt;\/p&gt;&lt;hr \/&gt;&lt;p&gt;&nbsp;&lt;\/p&gt;/ig, "&lt;hr /&gt;");
	
	//-------------------------------
	// Attempt to fix some formatting
	// issues....
	//-------------------------------
	
	t = t.replace( /&lt;(p|div)([^&]*)&gt;/ig     , "<br />&lt;$1$2&gt;<br />" );
	t = t.replace( /&lt;\/(p|div)([^&]*)&gt;/ig   , "<br />&lt;/$1$2&gt;<br />");
	t = t.replace( /&lt;br \/&gt;(?!&lt;\/td)/ig  , "&lt;br /&gt;<br />"   );
	
	//-------------------------------
	// And some table issues...
	//-------------------------------
	
	t = t.replace( /&lt;\/(td|tr|tbody|table)&gt;/ig  , "&lt;/$1&gt;<br />");
	t = t.replace( /&lt;(tr|tbody|table(.+?)?)&gt;/ig , "&lt;$1&gt;<br />" );
	t = t.replace( /&lt;(td(.+?)?)&gt;/ig             , "    &lt;$1&gt;" );
	
	//-------------------------------
	// Newlines
	//-------------------------------
	
	t = t.replace( /&lt;p&gt;&nbsp;&lt;\/p&gt;/ig     , "&lt;br /&gt;");
	
	return t;
}

/*--------------------------------------------*/
// Event handler: Update buttons when text is
// changed or selected
/*--------------------------------------------*/

function rte_button_update( command )
{
	if ( viewingHTML )
	{
		return false;
	}

	//-------------------------------
	// Make sure RTE is ready
	//-------------------------------
	
	if ( ! rteready )
	{
		return false;
	}
	
	//-------------------------------
	// Loop through buttons...
	//-------------------------------
	
	try {
		var cmd_state;
		
		for ( var i in buttons_update )
		{
			cmd_state = rtewindow.document.queryCommandState( buttons_update[i] );
			
			if ( typeof( button_status[ buttons_update[i] ] ) != "undefined" && button_status[ buttons_update[i] ] != cmd_state )
			{
				prev_state = button_status[ buttons_update[i] ];
				button_status[ buttons_update[i] ] = cmd_state;
				rte_button_context( my_getbyid('do_' + buttons_update[i]), prev_state == true ? "mouseout" : "mouseover" );
			}
		}
	}catch(e) { }
	
	//-------------------------------
	// Do font family
	//-------------------------------
	
	font_context = rtewindow.document.queryCommandValue( "fontname" );
	
	if ( font_context == "" )
	{
		if ( isIE )
		{
			font_context = document.body.style.fontFamily;
		}
	}
	else if ( font_context == null )
	{
		font_context = "";
	}
	
	if ( font_context != false && font_context != currentfont )
	{
		//-------------------------------
		// strip off rest of defs
		//-------------------------------
		
		fontword = font_context;
		commapos = fontword.indexOf(",");
		
		if (commapos != -1)
		{
			fontword = fontword.substr(0, commapos);
		}
		
		fontword = fontword.toLowerCase();
		
		document.getElementById( "fontname" ).selectedIndex = ips_reverse_primary_fonts[ fontword ];
		
		currentfont = font_context;
	}
	
	//-------------------------------
	// Font size
	//-------------------------------
	
	size_context = rtewindow.document.queryCommandValue( "fontsize" );
	
	if ( size_context == "" )
	{
		if ( ! isIE )
		{
			size_context = ips_fontsizes[ rtewindow.document.body.style.fontSize ];
		}
	}
	else if ( size_context == null )
	{
		size_context = "";
	}
	
	if ( size_context != currentsize )
	{
		document.getElementById( "fontsize" ).selectedIndex = ips_reverse_font_sizes[ size_context ];
		
		currentsize = size_context;
	}
	
	rtewindow.focus();
}

/*--------------------------------------------*/
// Make current RTE window (n)px bigger
/*--------------------------------------------*/

function rte_window_resize( elid, pix )
{
	var box        = my_getbyid( elid );
	var cur_height = parseInt( box.style.height );
	
	cur_height = cur_height ? cur_height : g_height;
	
	var new_height = cur_height + pix;
	
	if ( new_height > 0 )
	{
		box.style.height = new_height + "px";
	}
	
	return false;
}

/*--------------------------------------------*/
// Set context of buttons
/*--------------------------------------------*/

function rte_button_context( element, state )
{
	if (element == null)
	{
		return;
	}
	
	command = element.id.toString();
	command = command.replace( /do_/, "" );
	
	if (typeof(button_status[ command ] ) == "undefined")
	{
		button_status[ command ] = null;
	}
	
	try
	{
		switch (state)
		{
			case "click":
			case "mouseout":
			{
				// Normal
				element.className = 'rteimage';
			}
			break;

			case "mouseup":
			case "mouseover":
			{
				// Hover
				element.className = 'rteImageRaised';
			}
			break;

			case "mousedown":
			{
				// On
				element.className = 'rteImageRaised';
			}
			break;
		}
	}
	catch (e)
	{
		
	}
}


/*--------------------------------------------*/
// Function to format text in the text box
/*--------------------------------------------*/

function format_text(command, option)
{	
	if ( isIE )
	{
		//-------------------------------
		// Get current selected range
		//-------------------------------
		
		var selection = rtewindow.document.selection; 
		
		if (selection != null)
		{
			rng = selection.createRange();
		}

	}
	else if ( is_safari )
	{
		var selection = rtewindow.getSelection();
	}
	else
	{
		//-------------------------------
		// Get currently selected range
		//-------------------------------
		
		var selection = rtewindow.getSelection();
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}
	
	try
	{
		if ( (command == "forecolor") || (command == "hilitecolor") )
		{
			//-------------------------------
			// Save current values
			//-------------------------------
			
			parent.command = command;
			
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
		else if ( command == 'cut' || command == 'copy' || command == 'paste' )
		{
			try
			{
				rtewindow.document.execCommand( command, false, null );
			}
			catch (e)
			{
				if ( isGecko )
				{
					alert( jsfile_alert1 + jsfile_alert2 + jsfile_alert3 + "\n" + jsfile_alert4 );
				}
			}
		}
		else if (command == "createlink")
		{
			if ( isIE )
			{
				var check_text = rng.htmlText;
			}
			else
			{
				var check_text = _gecko_get_html();
			}
			
			if ( ! check_text )
			{
				alert( jsfile_highlight_lang );
				return false;
			}
			else
			{
				var szURL = prompt( text_enter_url, "http://");
			}
			
			try
			{
				var pass_link = 0;

				if(szURL.substring(0,7) == 'http://')
				{
					pass_link = 1;
				}

				if(szURL.substring(0,8) == 'https://')
				{
					pass_link = 1;
				}

				if(szURL.substring(0,6) == 'ftp://')
				{
					pass_link = 1;
				}

				if( pass_link == 0 )
				{
					szURL = null;
				}

				//-------------------------------
				// Ignore error for blank urls
				//-------------------------------
				
				rtewindow.document.execCommand("Unlink"    , false, null );
				rtewindow.document.execCommand("CreateLink", false, szURL);
			}
			catch (e)
			{
				//do nothing
			}
		}
		else
		{
			rtewindow.focus();
			_gecko_kill_css();
		  	rtewindow.document.execCommand(command, false, null);
		  	rte_button_update( command );
			rtewindow.focus();
		}
	}
	catch (e)
	{
		alert(e);
	}
}

/*--------------------------------------------*/
// Show smilies pop-up
/*--------------------------------------------*/

function show_smilies()
{
	//-------------------------------
	// Position and show color palette
	//-------------------------------
	
	buttonElement = document.getElementById('popsmilies');
	
	var iLeftPos  = getOffsetLeft(buttonElement);
	var iTopPos   = getOffsetTop(buttonElement) + (buttonElement.offsetHeight + 30);
	
	document.getElementById('smiliestable').style.left = (iLeftPos) + "px";
	document.getElementById('smiliestable').style.top  = (iTopPos)  + "px";
	
	if (document.getElementById('smiliestable').style.visibility == "hidden")
	{
		document.getElementById('smiliestable').style.visibility = "visible";
		document.getElementById('smiliestable').style.display    = "inline";
	}
	else
	{
		document.getElementById('smiliestable').style.visibility = "hidden";
		document.getElementById('smiliestable').style.display    = "none";
	}
}

/*--------------------------------------------*/
// Function to set color
/*--------------------------------------------*/

function setColor(color)
{
	var parentCommand = parent.command;
	
	if ( isIE )
	{
		//-------------------------------
		//retrieve selected range
		//-------------------------------
		
		var sel = rtewindow.document.selection; 
		
		if (parentCommand == "hilitecolor") parentCommand = "backcolor";
		
		if (sel != null)
		{
			var newRng = sel.createRange();
			newRng = rng;
			newRng.select();
		}
	}
	
	rtewindow.focus();
	_gecko_kill_css();
	rtewindow.document.execCommand(parentCommand, false, color);
	rtewindow.focus();
	
	document.getElementById('cp').style.visibility = "hidden";
	document.getElementById('cp').style.display    = "none";
}

/*--------------------------------------------*/
// Function to add email address
/*--------------------------------------------*/

function add_email()
{
	if ( isIE )
	{
		//-------------------------------
		// Get current selected range
		//-------------------------------
		
		var selection = rtewindow.document.selection; 
		
		if (selection != null)
		{
			rng = selection.createRange();
		}
	}
	else
	{
		//-------------------------------
		// Get currently selected range
		//-------------------------------
		
		var selection = rtewindow.getSelection();
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}
	
	emailaddress = prompt( text_enter_email, '');	
	
	if ((emailaddress != null) && (emailaddress != ""))
	{
		rtewindow.focus();
		_gecko_kill_css();
		rtewindow.document.execCommand('createlink', false, 'mailto:' + emailaddress);
		rtewindow.focus();
	}
}

/*--------------------------------------------*/
// Function to add image
/*--------------------------------------------*/

function add_image()
{
	if ( isIE )
	{
		//-------------------------------
		// Get current selected range
		//-------------------------------
		
		var selection = rtewindow.document.selection; 
		
		if (selection != null)
		{
			rng = selection.createRange();
		}
	}
	else
	{
		//-------------------------------
		// Get currently selected range
		//-------------------------------
		
		var selection = rtewindow.getSelection();
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}
	
	imagePath = prompt( text_enter_image, 'http://');

	var pass_image = 0;

	if(imagePath.substring(0,7) == 'http://')
	{
		pass_image = 1;
	}

	if(imagePath.substring(0,8) == 'https://')
	{
		pass_image = 1;
	}

	if(imagePath.substring(0,6) == 'ftp://')
	{
		pass_image = 1;
	}

	if( pass_image == 0 )
	{
		imagePath = null;
	}
	
	if ((imagePath != null) && (imagePath != ""))
	{
		rtewindow.focus();
		_gecko_kill_css();
		rtewindow.document.execCommand('InsertImage', false, imagePath);
		rtewindow.focus();
	}
}

/*--------------------------------------------*/
//Function to perform spell check
/*--------------------------------------------*/

function checkspell()
{
	try
	{
		var tmpis = new ActiveXObject("ieSpell.ieSpellExtension");
		tmpis.CheckAllLinkedDocuments(document);
	}
	
	catch(exception)
	{
		if(exception.number==-2146827859)
		{
			if (confirm( jsfile_erroriespell ))
				window.open("http://www.iespell.com/download.php","DownLoad");
		}
		else
		{
			alert( jsfile_errorliespell + exception.number);
		}
	}
}

/*--------------------------------------------*/
// Add emoticon
/*--------------------------------------------*/

function emoticon( scode, smid, surl )
{
	rtewindow.focus();
	
	try
	{
		if ( ! surl )
		{
			surl = document.getElementById(smid).src;
		}
		
		if ( ! isIE )
		{
			rtewindow.focus();
			_gecko_kill_css();
			rtewindow.document.execCommand('InsertImage', false, surl);
			rtewindow.focus();
			
			var images = rtewindow.document.getElementsByTagName('img');
		
			//----------------------------------
			// Sort through and fix emo
			//----------------------------------
			
			for ( var i = 0 ; i <= images.length ; i++ )
			{  
				if ( images[i].src == surl )
				{
					if ( ! images[i].getAttribute('emoid') )
					{
						images[i].setAttribute( 'emoid', scode );
						images[i].setAttribute( 'border', '0'  );
						images[i].style.verticalAlign = 'middle';
					}
				}
			}
		}
		else
		{
			smilieHTML = '<img src="' + surl + '" border="0" alt="" emoid="' + scode + '" />';
			wrap_tags( "" + smilieHTML, "");
		}
	}
	catch(e)
	{
		//Ssshhhhhh
	}
	
	rtewindow.focus();

	if ( emowindow != '' && emowindow != 'undefined' )
	{
		emowindow.focus();
	}
}


/*--------------------------------------------*/
// Add simple HTML tag
/*--------------------------------------------*/

function wrap_tags( opentag, closetag )
{
	//-------------------------------
	// Has closed flag
	//-------------------------------
	
	var has_closed = false;
	
    if ( ! isRichText )
    {
    	return;
    }
    
	if ( isIE )
	{
		var sel = rtewindow.document.selection;
		var ts  = rtewindow.document.selection.createRange();
		var t   = ts.htmlText.replace(/<p([^>]*)>(.*)<\/p>/i, '$2');
		
		if ( (sel.type == "Text" || sel.type == "None") )
		{
			has_closed = true;
			ts.pasteHTML( opentag + t + closetag );
		}
		else
		{
			has_closed = true;
			rtewindow.document.body.innerHTML += opentag + closetag;
		}
	}
	else
	{
		//-------------------------------
		// FRAGment, not a game of UT
		//-------------------------------
		
		var frag = rtewindow.document.createDocumentFragment();
		var span = rtewindow.document.createElement("span");
		
		//-------------------------------
		// Apply tags...
		//-------------------------------
		
		sel_html = _gecko_get_html();
		
		//-------------------------------
		// Remove empty span tags
		//-------------------------------
		
		if ( sel_html )
		{
			has_closed     = true;
			span.innerHTML = opentag + sel_html + closetag;
		}
		else
		{
			span.innerHTML = opentag + closetag;
		}
		
		while ( span.firstChild )
		{
			frag.appendChild(span.firstChild);
		}

		_gecko_insert_node_at_selection( frag );
	}
	
	//-------------------------------
	// Add tag to stack
	//-------------------------------
	
    rtewindow.focus();
    
    return has_closed;
}

/*--------------------------------------------*/
// Do fontsize, etc
/*--------------------------------------------*/

function do_select(selectname)
{
	if ( isIE )
	{
		//-------------------------------
		// Get current selected range
		//-------------------------------
		
		var selection = rtewindow.document.selection; 
		
		if (selection != null)
		{
			rng = selection.createRange();
		}
	}
	else
	{
		//-------------------------------
		// Get currently selected range
		//-------------------------------
		
		var selection = rtewindow.getSelection();
		
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}
	
	var idx = document.getElementById(selectname).selectedIndex;
	
	var selected = document.getElementById(selectname).options[idx].value;
	
	rtewindow.focus();
	_gecko_kill_css();
	rtewindow.document.execCommand(selectname, false, selected);
	rte_button_update( selectname );
	rtewindow.focus();
}

/*--------------------------------------------*/
// Launch fullscreen...
/*--------------------------------------------*/

function launch_rte_fs()
{
	if ( viewingFS )
	{
		//return false;
	}
	
	//-------------------------------
	// If viewing source, switch back
	// to design view
	//-------------------------------
	
	if ( viewingHTML ) 
	{
		toggleHTMLSrc();
	}
	
	viewingFS = 1;
	
	window.open( ipb_var_base_url + '&act=rtefs', 'RTEFS', 'width=640,height=480,resizable=yes,scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no');
}

/*--------------------------------------------*/
// Launch div...
/*--------------------------------------------*/

function launch_div()
{
	// Could do with sorting this mess out.
	
	window.open( includesPath + 'insert_div.html', 'DIV', 'width=550,height=340,resizable=yes,scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no');
}

/*--------------------------------------------*/
// Manage tables...
/*--------------------------------------------*/

function write_div( param, pwindow )
{
	try
	{
		if ( pwindow )
		{
			pwindow.close();
		}
	}
	catch (e)
	{
	}
	
	if ( ! param ) 
	{
		return false;
	}
	
	//-------------------------------
	// Build remap array
	//-------------------------------
	
	var remap_elements = {
		"f_fontfamily"       : "font-family"      ,
		"f_fontsize"         : "font-size"        ,
		"f_color"            : "color"            ,
		"f_backgroundimage"  : "background-image" ,
		"f_backgroundrepeat" : "background-repeat",
		"f_backgroundcolor"  : "background-color" ,
		"f_border"           : "border"           , 
		"f_padding"          : "padding"          ,
		"f_margin"           : "margin"           
	};
	
	//-------------------------------
	// Get tag type
	//-------------------------------
	
	var tagtype = param["f_type"];
	var style   = "";
	
	if ( ! param["f_classes"] || param["f_classes"] == "__none" )
	{
		//-------------------------------
		// Start building style
		//-------------------------------
		
		style += "style='";
		
		for (var field in param)
		{
			var value = param[field];
			
			if ( ! value || value == 'undefined' || field == 'undefined' || ! remap_elements[ field ] )
			{
				continue;
			}
			
			if ( remap_elements[ field ] && value )
			{
				style += remap_elements[ field ] + ":" + value + ";";
			}
		}
		
		//-------------------------------
		// Finish style
		//-------------------------------
		
		style += param["f_other"]+"'";
	}
	else
	{
		style += "class='" + param["f_classes"] + "'";
	}
	
	//-------------------------------
	// Pass to handler
	//-------------------------------
	
	if ( style )
	{
		wrap_tags( "<"+tagtype+" " + style + ">", "</"+tagtype+">" );
	}
	else
	{
		wrap_tags( "<"+tagtype+">", "</"+tagtype+">" )
	}
	
	return true;
}

/*--------------------------------------------*/
// Launch tables...
/*--------------------------------------------*/

function launch_table()
{
	// Could do with sorting this mess out.
	
	window.open( includesPath + 'insert_table.html', 'TABLE', 'width=450,height=220,resizable=yes,scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no');
}

/*--------------------------------------------*/
// Manage tables...
/*--------------------------------------------*/

function write_tables( param, pwindow )
{
	try
	{
		if ( pwindow )
		{
			pwindow.close();
		}
	}
	catch (e)
	{
	}
	
	var sel   = _my_getselection();
	var range = _my_createrange(sel);
	
	if ( ! param ) 
	{
		return false;
	}
	
	var doc = rtewindow.document;
	
	//-------------------------------
	// create the table element
	//-------------------------------
	
	var table = doc.createElement("table");
	
	//-------------------------------
	// assign the given arguments
	//-------------------------------
	
	for (var field in param)
	{
		var value = param[field];
		
		if ( ! value )
		{
			continue;
		}
		
		switch (field)
		{
			case "f_width"   : table.style.width = value + param["f_unit"]; break;
			case "f_align"   : table.align	     = value; break;
			case "f_border"  : table.border	     = parseInt(value); break;
			case "f_spacing" : table.cellspacing = parseInt(value); break;
			case "f_padding" : table.cellpadding = parseInt(value); break;
		}
	}
	
	var tbody = doc.createElement("tbody");
	
	table.appendChild(tbody);
	
	for (var i = 0; i < param["f_rows"]; ++i)
	{
		var tr = doc.createElement("tr");
		
		tbody.appendChild(tr);
		
		for (var j = 0; j < param["f_cols"]; ++j)
		{
			var td = doc.createElement("td");
			
			tr.appendChild(td);
			
			//-------------------------------
			// Mozilla likes to see something
			// inside the cell.
			//-------------------------------
			
			( ! isIE )
			{
				td.appendChild(doc.createElement("br"));
			}
		}
	}
	
	if ( isIE )
	{
		range.pasteHTML(table.outerHTML);
	}
	else
	{
		//-------------------------------
		// insert the table
		//-------------------------------
		
		_gecko_insert_node_at_selection(table);
	}
	
	return true;
}



/*--------------------------------------------*/
// KeyBoard Handler
/*--------------------------------------------*/

function kb_handler(evt)
{
	var rte = evt.target.id;

	if (evt.ctrlKey)
	{
		var key = String.fromCharCode(evt.charCode).toLowerCase();
		var cmd = '';
		
		switch (key)
		{
			case 'b': cmd = "bold";      break;
			case 'i': cmd = "italic";    break;
			case 'u': cmd = "underline"; break;
		};

		if (cmd)
		{
			format_text(cmd, true);
			
			//-------------------------------
			// evt.target.ownerDocument.execCommand(cmd, false, true);
			// stop the event bubble
			//-------------------------------
			
			evt.preventDefault();
			evt.stopPropagation();
		}
 	}
}

function docChanged (evt)
{
	alert('changed');
}

/*--------------------------------------------*/
// Get selection (MY)
/*--------------------------------------------*/

function _my_getselection()
{
	if ( isIE )
	{
		return rtewindow.document.selection; 
	}
	else
	{
		return rtewindow.getSelection();
	}
}

/*--------------------------------------------*/
// Create Range (my)
/*--------------------------------------------*/

function _my_createrange( sel )
{
	if ( isIE )
	{
		return sel.createRange(); 
	}
	else
	{
		rtewindow.focus();
		return sel ? sel.getRangeAt(0) : rtewindow.document.createRange();
	}
}

/*--------------------------------------------*/
// Gecko work around to get selection HTML
/*--------------------------------------------*/

function _gecko_get_html()
{
	var sel   = _my_getselection();
	var range = _my_createrange(sel);
	var root  = range.cloneContents();
	
	return _gecko_read_nodes(root, false);
}

/*--------------------------------------------*/
// Gecko read nodes (based on midas)
/*--------------------------------------------*/

function _gecko_read_nodes(root, toptag)
{
	var html      = "";
	var moz_check = /_moz/i;

	switch (root.nodeType)
	{
		case Node.ELEMENT_NODE:
		case Node.DOCUMENT_FRAGMENT_NODE:
		{
			var closed;
			
			if (toptag)
			{
				closed = !root.hasChildNodes();
				html   = '<' + root.tagName.toLowerCase();
				var attr = root.attributes;
				for (i = 0; i < attr.length; ++i)
				{
					var a = attr.item(i);
					
					if (!a.specified || a.name.match(moz_check) || a.value.match(moz_check))
					{
						continue;
					}
	
					html += " " + a.name.toLowerCase() + '="' + a.value + '"';
				}
				html += closed ? " />" : ">";
			}
			for (var i = root.firstChild; i; i = i.nextSibling)
			{
				html += _gecko_read_nodes(i, true);
			}
			if (toptag && !closed)
			{
				html += "</" + root.tagName.toLowerCase() + ">";
			}
		}
		break;

		case Node.TEXT_NODE:
		{
			html = htmlspecialchars(root.data);
		}
		break;
	}
	return html;
}


/*--------------------------------------------*/
// Gecko insert html mode at selection (midas)
/*--------------------------------------------*/

function _gecko_insert_node_at_selection(text)
{
	var sel   = _my_getselection();
	var range = _my_createrange(sel);
	
	sel.removeAllRanges();
	range.deleteContents();

	var node = range.startContainer;
	var pos  = range.startOffset;

	switch (node.nodeType)
	{
		case Node.ELEMENT_NODE:
		{
			if (text.nodeType == Node.DOCUMENT_FRAGMENT_NODE)
			{
				selNode = text.firstChild;
			}
			else
			{
				selNode = text;
			}
			node.insertBefore(text, node.childNodes[pos]);
			_gecko_add_range(selNode);
		}
		break;

		case Node.TEXT_NODE:
		{
			if ( text.nodeType == Node.TEXT_NODE )
			{
				var text_length = pos + text.length;
				node.insertData(pos, text.data);
				range = rtewindow.document.createRange();
				range.setEnd(  node, text_length);
				range.setStart(node, text_length);
				sel.addRange(range);
			}
			else
			{
				node = node.splitText(pos);
				var selNode;
				if (text.nodeType == Node.DOCUMENT_FRAGMENT_NODE)
				{
					selNode = text.firstChild;
				}
				else
				{
					selNode = text;
				}
				node.parentNode.insertBefore(text, node);
				_gecko_add_range(selNode);
			}
		}
		break;
	}
}

/*--------------------------------------------*/
// Turns off CSS mode for gecko
/*--------------------------------------------*/

function _gecko_kill_css()
{
	//-------------------------------
	// Moz likes to use <span> styles
	// Not really sure if this needs
	// to be fired before each command
	// midas shows not, experience shows
	// that it does.
	//-------------------------------
	
	if ( ! isIE )
	{
		try
		{
			rtewindow.document.execCommand("useCSS", false, true);
			css_off = true;
		}
		catch (e)
		{
			css_off = false;
		}
	}
}


/*--------------------------------------------*/
// Add range of inserted text
/*--------------------------------------------*/

function _gecko_add_range(node)
{
	rtewindow.focus();
	
	var sel   = rtewindow.getSelection();
	var range = null;
	
	range     = rtewindow.document.createRange();
	range.selectNodeContents(node);
	
	sel.removeAllRanges();
	sel.addRange(range);
}


/*--------------------------------------------*/
// IE ONLY: Button raised
/*--------------------------------------------*/

function ie_raise_button(e)
{
	var el = window.event.srcElement;
	
	className = el.className;
	
	if (className == 'rteimage' || className == 'rteImageLowered')
	{
		el.className = 'rteImageRaised';
	}
}

/*--------------------------------------------*/
// IE ONLY: Normal Button
/*--------------------------------------------*/

function ie_normal_button(e)
{
	var el = window.event.srcElement;
	
	className = el.className;
	var command;
	
	try
	{
		if ( el.id )
		{
			command = el.id.toString();
			command = command.replace( /do_/, "" );
		}
	}
	catch (e)
	{
		// Who cares
	}
	
	if (className == 'rteImageRaised' || className == 'rteImageLowered')
	{
		if ( ! command || button_status[ command ] != true )
		{
			el.className = 'rteimage';
		}
		else
		{
			el.className = 'rteImageRaised';
		}
	}
}

/*--------------------------------------------*/
// IE ONLY: Lowered Button
/*--------------------------------------------*/

function ie_lower_button(e)
{
	var el = window.event.srcElement;
	
	className = el.className;
	
	if (className == 'rteimage' || className == 'rteImageRaised')
	{
		el.className = 'rteImageLowered';
	}
}

/*--------------------------------------------*/
// Strip HTML
/*--------------------------------------------*/

function strip_html(oldString)
{
	var newString = oldString.replace(/(<([^>]+)>)/ig,"");
	
	//-------------------------------
	//replace carriage returns and line feeds
	//-------------------------------
	
	newString = newString.replace(/\r\n/g," ");
	newString = newString.replace(/\n/g," ");
	newString = newString.replace(/\r/g," ");
	
	//-------------------------------
	//trim string
	//-------------------------------
	
	newString = trim(newString);
	
	return newString;
}

/*--------------------------------------------*/
// Newlines safe
/*--------------------------------------------*/

function make_newlines_safe(t)
{
	//t = t.replace(/\n/g,'\\n');
	
	//-------------------------------
	//replace carriage returns and line feeds
	//-------------------------------
	
	//t = t.replace(/\r/g,'\\r');
	
	//-------------------------------
	//trim string
	//-------------------------------
	
	//t = trim(t);
	
	return t;
}

/*--------------------------------------------*/
// TRIM
/*--------------------------------------------*/

function trim(inputString)
{
	//-------------------------------
	// Removes leading and trailing spaces from the passed string. Also removes
	// consecutive spaces and replaces it with one space. If something besides
	// a string is passed in (null, custom object, etc.) then return the input.
	//-------------------------------
	
	if (typeof inputString != "string") return inputString;
	var retValue = inputString;
	var ch = retValue.substring(0, 1);
	
	while (ch == " ")
	{
		//-------------------------------
		// Check for spaces at the beginning of the string
		//-------------------------------
		
		retValue = retValue.substring(1, retValue.length);
		ch       = retValue.substring(0, 1);
	}
	
	ch = retValue.substring(retValue.length - 1, retValue.length);
	
	while (ch == " ")
	{
		//-------------------------------
		// Check for spaces at the end of the string
		//-------------------------------
		
		retValue = retValue.substring(0, retValue.length - 1);
		ch       = retValue.substring(retValue.length - 1, retValue.length);
	}
	
	//-------------------------------
	// Note that there are two spaces
	// in the string - look for multiple
	// spaces within the string
	//-------------------------------
	
	while (retValue.indexOf("  ") != -1)
	{
		//-------------------------------
		// Again, there are two spaces in each of the strings
		//-------------------------------
		retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ") + 1, retValue.length);
	}
	
	return retValue;
}

//*-------------------------------------------------------------------------*/
// Validate form (must always have this method defined!)
/*-------------------------------------------------------------------------*/

function ValidateForm( isMsg )
{
	//-----------------------------------------
	// Sync RTE
	//-----------------------------------------
	
	update_rte();
	
	MessageLength  = 3; //postfieldobj.value.length;
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

	if ( MessageMax != 0 )
	{
		if (MessageLength > MessageMax)
		{
			errors = js_max_length + " " + MessageMax + " " + js_characters + ". " + js_current + ": " + MessageLength;
		}
	}
	
	if ( errors != "" && Override == "" )
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

/*--------------------------------------------*/
// HTML SPECIAL CHARACTERS (PHP emulation)
/*--------------------------------------------*/

function htmlspecialchars(html)
{
	html = html.replace(/"/, "&quot;");
	html = html.replace(/</, "&lt;");
	html = html.replace(/>/, "&gt;");
	return html;
}
