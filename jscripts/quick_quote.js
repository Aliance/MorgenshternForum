function QuickQuote(a, b, c){

	if ( document.getSelection ) {
		if ( typeof(window.getSelection) != "undefined" ) {
			QuickQuote.selection = window.getSelection().toString();
		} else {
			QuickQuote.selection = document.getSelection();
		}
	} else {
		QuickQuote.selection = document.selection.createRange().text;
	}

	if( QuickQuote.selection.toString().length > 0 ) {
		var d = document.getElementById( "post" + c ), e = "";
		var f = ( typeof(d.innerText) != "undefined" ) ? d.innerText : ( typeof(d.textContent) != "undefined" ) ? d.textContent : "";
		if( ! (document.up_post === true && document.down_pid == c) ){
			alert("Вы нажали на кнопку цитаты другого участника");
			return;
		}
//		post.target.value += ( ( ( post.target.value.length > 0 ) ? "\n" : "" ) + "[quote" + ( a ? ( "=" + a + ", " + b + ( c ? ( ", post" + c ) : "" ) ) : "" ) + " ] " + ( QuickQuote.selection.toString() ) + "[/quote]\n" );
		alert( ( ( post.target.value.length > 0 ) ? "\n" : "" ) + "[quote" + ( a ? ( "=" + a + ", " + b + ( c ? ( ", post" + c ) : "" ) ) : "" ) + " ] " + ( QuickQuote.selection.toString() ) + "[/quote]\n" );
	}
}