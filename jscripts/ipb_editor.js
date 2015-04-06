//------------------------------------------
// Invision Power Board 2.1.7
// Text Editor Global Functions
// (c) 2005 Invision Power Services, Inc.
//
// http://www.invisionboard.com
//------------------------------------------

/*-------------------------------------------------------------------------*/
// INIT
/*-------------------------------------------------------------------------*/

var dontpassgo   = 0;

//-------------------------------
// Keep an array of open tags
//-------------------------------

var bbtags = new Array();

//-------------------------------
// Define font faces
//-------------------------------

var ips_primary_fonts = new Array(
	"arial",
	"arial black",
	"arial narrow",
	"book antiqua",
	"century gothic",
	"comic sans ms",
	"courier new",
	"fixedsys",
	"franklin gothic medium",
	"garamond",
	"georgia",
	"impact",
	"lucida console",
	"lucida sans unicode",
	"microsoft sans serif",
	"palatino linotype",
	"system",
	"tahoma",
	"times new roman",
	"trebuchet ms",
	"verdana"
);

//-------------------------------
// Remap font sizes
//-------------------------------

var ips_fontsizes =
{
	'8px' : 1,
	'10pt' : 2,
	'12pt' : 3,
	'14pt' : 4,
	'18pt' : 5,
	'24pt' : 6,
	'36pt' : 7
}

//-------------------------------
// Define sizes
//-------------------------------

var ips_font_sizes = new Array( 1, 2, 3, 4, 5, 6, 7 );

//-------------------------------
// Reverse them so we can look up
// by ID
//-------------------------------

var ips_reverse_primary_fonts = new Array();
var ips_reverse_font_sizes    = new Array();
var ips_reverse_fontsizes     = new Array();

for( var i in ips_primary_fonts )
{
	ips_reverse_primary_fonts[ ips_primary_fonts[i] ] = i;
}

for( var i in ips_font_sizes )
{
	ips_reverse_font_sizes[ ips_font_sizes[i] ] = i;
}

for( var i in ips_fontsizes )
{
	ips_reverse_fontsizes[ ips_fontsizes[i] ] = i;
}

/*-------------------------------------------------------------------------*/
// Close all tags
/*-------------------------------------------------------------------------*/

function closeall()
{
	if ( bbtags[0] )
	{
		while ( bbtags[0] )
		{
			tagRemove = popstack( bbtags )
			postformobj.Post.value += "[/" + tagRemove + "]";
			
			if ( i_am == 'std' )
			{
				toggle_button( tagRemove );
			}
			else
			{
				rte_button_update( tagRemove );
			}
		}
	}
	
	//--------------------------------------------
	// Ensure we got them all
	//--------------------------------------------

	bbtags = new Array();
	postformobj.Post.focus();
}

/*-------------------------------------------------------------------------*/
// Close all nested tags
/*-------------------------------------------------------------------------*/

function smart_close_tags( thetag )
{
	//--------------------------------------------
	// Find the last occurance of the opened tag
	//--------------------------------------------
	
	lastindex = 0;
	
	for (i = 0 ; i < bbtags.length; i++ )
	{
		if ( bbtags[i] == thetag )
		{
			lastindex = i;
		}
	}
	
	//--------------------------------------------
	// Close all tags opened up to that tag was opened
	//--------------------------------------------
	
	while ( bbtags[lastindex] )
	{
		tagRemove = popstack(bbtags);
		
		wrap_tags("[/" + tagRemove + "]", "")
		
		//--------------------------------------------
		// Change the button status
		//--------------------------------------------
		
		if ( i_am == 'std' )
		{
			toggle_button( tagRemove );
		}
		else
		{
			rte_button_update( tagRemove );
		}
	}
}

/*-------------------------------------------------------------------------*/
// Write font face box
/*-------------------------------------------------------------------------*/

function write_fontface_box()
{
	html = "";
	
	for( var i in ips_primary_fonts )
	{
		option  = ips_primary_fonts[i];
		display = ips_primary_fonts[i];
		
		//-------------------------------
		// Tidy up show name
		// Make "courier new" "Courier New"
		//-------------------------------
		
		var tmp = display.split( " " );
		var rtn = new Array;
		
		for ( var id in tmp )
		{
			rtn[id] = tmp[id].substr(0, 1).toUpperCase() + tmp[id].substr(1);
		}
		
		display = rtn.join( " " );
		
		html += "\n<option style='font-family:" + display + "' value='" + display + "'>" + display + "</option>";
	}
	
	return html;
}

/*-------------------------------------------------------------------------*/
// Write font size box
/*-------------------------------------------------------------------------*/

function write_fontsize_box()
{
	html = "";
	
	for( var i in ips_font_sizes )
	{
		option  = ips_font_sizes[i];
		display = ips_font_sizes[i];
		
		html += "\n<option value='" + display + "'>" + display + "</option>";
	}
	
	return html;
}

/*-------------------------------------------------------------------------*/
// INIT Editor
/*-------------------------------------------------------------------------*/

function init_editor()
{ 
	if ( ! postformid )
	{
		postformid = 'postingform';
	}
	
	if ( ! postfieldid )
	{
		postfieldid = 'postcontent';
	}
	
	postformobj  = document.getElementById( postformid );
	postfieldobj = document.getElementById( postfieldid );
}

/*-------------------------------------------------------------------------*/
// Remove an attachment
/*-------------------------------------------------------------------------*/

function removeattach(id)
{
	if ( id != "" )
	{
		Override = 1;
		postformobj.removeattachid.value = id;
	}
}

/*-------------------------------------------------------------------------*/
// Insert attachment tag
/*-------------------------------------------------------------------------*/

function insert_attach_to_textarea(aid)
{
	rtewindow.focus();
	
	wrap_tags( "[attachmentid="+aid+"]", "" );
	
	rtewindow.focus();
}

/*-------------------------------------------------------------------------*/
// Open emoticon window
/*-------------------------------------------------------------------------*/

var emowindow = '';

function emo_pop()
{
	emowindow = window.open( ipb_var_base_url + "act=legends&CODE=emoticons","Legends","width=250,height=500,resizable=yes,scrollbars=yes"); 
}

/*-------------------------------------------------------------------------*/
// BBCode window
/*-------------------------------------------------------------------------*/

function bbc_pop()
{
	window.open( ipb_var_base_url + "act=legends&CODE=bbcode","Legends","width=700,height=500,resizable=yes,scrollbars=yes"); 
}

/*--------------------------------------------*/
// Fix the amount of digging parents up.
/*--------------------------------------------*/

function getOffsetTop(elm)
{
	var mOffsetTop    = elm.offsetTop;
	var mOffsetParent = elm.offsetParent;
	var parents_up    = 2;
	
	while(parents_up > 0)
	{
		mOffsetTop   += mOffsetParent.offsetTop;
		mOffsetParent = mOffsetParent.offsetParent;
		parents_up--;
	}
	
	return mOffsetTop;
}

/*--------------------------------------------*/
// Fix the amount of digging parents up.
/*--------------------------------------------*/

function getOffsetLeft(elm)
{
	var mOffsetLeft   = elm.offsetLeft;
	var mOffsetParent = elm.offsetParent;
	var parents_up    = 2;
	
	while(parents_up > 0)
	{
		mOffsetLeft  += mOffsetParent.offsetLeft;
		mOffsetParent = mOffsetParent.offsetParent;
		parents_up--;
	}
	
	return mOffsetLeft;
}
