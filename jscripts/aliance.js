/*-------------------------------------------------------------------------*/
// Player tag
/*-------------------------------------------------------------------------*/

function tag_player(num)
{
	var FoundErrors = '';
	
	var enterNICK = prompt("Введите ник чара", "ник в БК");
	var enterLEVEL = prompt("Введите уровень чара", "уровень в БК");

	if ( ! enterNICK )
	{
		FoundErrors += "Вы не ввели ник!<br />";
	}
	if ( ! enterLEVEL )
	{
		FoundErrors += "Вы не указали уровень персонажа!<br />";
	}
	
	if ( FoundErrors )
	{
		alert( "Внимание! Были обнаружены следующие ошибки:<br />" + FoundErrors );
	}

	var str = "[b]"+enterNICK+"[/b] ["+enterLEVEL+"][url=http://capitalcity.combats.com/inf.pl?login="+enterNICK.replace(/\s/gi, '%20')+"][img]http://img.combats.com/i/inf"+num+".gif[/img][/url]";
	wrapIt( str );
}

/*-------------------------------------------------------------------------*/
// Clan tag
/*-------------------------------------------------------------------------*/

function tag_clan()
{
	var clan = prompt("Введите название клана", "Morgenshtern");

	if ( ! clan )
	{
		alert( "Внимание! Были обнаружены следующие ошибки:<br />Вы не ввели название клана!" );
	}

	wrapIt( "[url=http://capitalcity.combats.ru/encicl/klan/"+clan.replace(/\s/gi, '%20')+".html][img]http://img.combats.ru/i/klan/"+clan.replace(/\s/gi, '%20')+".gif[/img][/url][b]"+clan+"[/b]" );
}

/*-------------------------------------------------------------------------*/
// City gerb
/*-------------------------------------------------------------------------*/

function city_gerb(gerb)
{
	wrapIt( "[url=http://"+gerb+"city.combats.com][img]http://images.morgenshtern.com/gerb/"+gerb+"_small.gif[/img][/url]" );
}

/*-------------------------------------------------------------------------*/
// Dungeon Gerb
/*-------------------------------------------------------------------------*/

function dungeon_gerb(number, round)
{
	wrapIt( "[img]http://img.combats.ru/i/misc/zn"+number+"_"+round+".gif[/img]" );
}

/*-------------------------------------------------------------------------*/
// Align tag
/*-------------------------------------------------------------------------*/

function tag_align(align)
{
	wrapIt( "[url=http://capitalcity.combats.ru/encicl/alignment.html][img]http://img.combats.ru/i/"+align+".gif[/img][/url]" );
}

/*-------------------------------------------------------------------------*/
// Insert update
/*-------------------------------------------------------------------------*/

function insert_update()
{
	wrapIt( "[color=#FF0000][b]Update: [/b][/color]" );
}

/*-------------------------------------------------------------------------*/
// Insert tag
/*-------------------------------------------------------------------------*/

function insert_tag(tag)
{
	wrapIt( "[" + tag + "][/" + tag + "]" );
}

/*-------------------------------------------------------------------------*/
// ShowHide Tag
/*-------------------------------------------------------------------------*/

function ShowHide_tag()
{
	wrapIt( "[expand][/expand]" );
}

/*-------------------------------------------------------------------------*/
// Insert  copyright
/*-------------------------------------------------------------------------*/

function insert_copyright()
{
	var copyright = prompt("Введите копирайт", "Сайт Событий Бойцовского Клуба");
	var copyright_url = prompt("Введите адрес url", "http://events.combats.ru/");
	if ( ! copyright || ! copyright_url )
	{
		alert( "Внимание! Были обнаружены следующие ошибки:<br />Вы не ввели текст!" )
	}
	wrapIt( "&#169; [url=" + copyright_url.replace(/\s/gi, '%20') + "]" + copyright + "[/url]" );
}

/*-------------------------------------------------------------------------*/
// Insert  private nick
/*-------------------------------------------------------------------------*/

function addPrivate( nick )
{
	var field = document.getElementById( 'fast-reply_textarea' );
	field.value += "[b]" + nick + "[/b]";
	field.focus();
}

/*-------------------------------------------------------------------------*/
// Update char info
/*-------------------------------------------------------------------------*/
function updateChar( nick )
{
	if ( ! nick || typeof nick != 'string' ) return;
	$.post( 'http://www.morgenshtern.com/ajax', { nick: nick } );
}

// Copyright @ Aliance spb
/*
function show_hide_text(divObj) {
	var div = divObj.parentNode.getElementsByTagName('div')[1];
	if (div.style.display == 'none') {
		div.style.display = 'block';
		divObj.innerHTML = "скрыть";
	} else {
		div.style.display = 'none';
		divObj.innerHTML = "показать";
	}
}
*/
function show_hide_text( divObj )
{
	var $this = $( divObj );
	var $text = $this.text();
	if ( $text == 'Показать скрытый текст' )
	{
		$this.text( 'Cкрыть текст' );
	}
	else if ( $text == 'Cкрыть текст' )
	{
		$this.text( 'Показать скрытый текст' );
	}
	$this.next().slideToggle( 'slow' );
}

function wrapIt( text )
{
	document.getElementById( 'ed-0_textarea' ).value += "\n" + text;
}

/*-------------------------------------------------------------------------*/
// Плавная прокрутка в начало страницы
/*-------------------------------------------------------------------------*/
function scrollToTop()
{
	var scrollTop = ( document.documentElement && document.documentElement.scrollTop ) || ( document.body && document.body.scrollTop );
	if ( parseInt( scrollTop ) > 0 )
	{
		scrollTop -= 50;
		scroll( 0, scrollTop );
		setTimeout( "scrollToTop()", 20 );
	}
}

/*-------------------------------------------------------------------------*/
// Убираем лишнюю строку с информацией
/*-------------------------------------------------------------------------*/
function switchRow( pos, id )
{
	switch ( pos )
	{
		case 'left':
			var $switcher = $( '#switcher-left-' + id );
			if ( $switcher.css( 'backgroundPosition' ) === '0% 0%' )
			{
				$( '#topic-left-row-' + id ).hide();
				$switcher.css( 'backgroundPosition', '100% 0%' );
				$( '#nickname-row-' + id ).attr( 'colspan', '1' );
				$( '#up_button-row-' + id ).attr( 'colspan', '1' );
			}
			else
			{
				$( '#topic-left-row-' + id ).show();
				$switcher.css( 'backgroundPosition', '0% 0%' );
				$( '#nickname-row-' + id ).attr( 'colspan', '2' );
				$( '#up_button-row-' + id ).attr( 'colspan', '2' );
			}
		break;
		case 'right':
			var $switcher = $( '#switcher-right-' + id );
			if ( $switcher.css( 'backgroundPosition' ) === '100% 0%' )
			{
				$( '#topic-right-row-' + id ).hide();
				$switcher.css( 'backgroundPosition', '0% 0%' );
				$( '#post-count-' + id ).attr( 'colspan', '1' );
				$( '#buttons-row-' + id ).attr( 'colspan', '2' );
			}
			else
			{
				$( '#topic-right-row-' + id ).show();
				$switcher.css( 'backgroundPosition', '100% 0%' );
				$( '#post-count-' + id ).attr( 'colspan', '2' );
				$( '#buttons-row-' + id ).attr( 'colspan', '3' );
			}
		break;
		default:
		break;
	}
}