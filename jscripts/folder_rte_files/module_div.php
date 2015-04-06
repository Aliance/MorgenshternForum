<html>
<head>
  <title>Вставка</title>
	<script type="text/javascript">
	<!--
	
	//-----------------------------------------
    // Attempt to get editor ID
    //-----------------------------------------

    var editor_id   = <?php print '"'.trim($_REQUEST['editorid']).'";'; ?>
	var this_module = 'div';
	var this_height = 400;
	
	/*-------------------------------------------------------------------------*/
	// INIT
	/*-------------------------------------------------------------------------*/
	
	function Init()
	{
		//-----------------------------------------
		// Set up main close button
		//-----------------------------------------
		
		parent.document.getElementById( editor_id + '_cb-close-window' ).onclick      = do_cancel;
		parent.document.getElementById( editor_id + '_cb-close-window' ).style.cursor = 'pointer';
		
		//-----------------------------------------
		// Resize window
		//-----------------------------------------
		
		parent.document.getElementById( editor_id +  '_htmlblock_'  + this_module + '_menu' ).style.height = this_height + 'px';
		parent.document.getElementById( editor_id + '_iframeblock_' + this_module + '_menu' ).style.height = ( this_height - 10 ) + 'px';
		
		//-----------------------------------------
		// Module specific stuff
		//-----------------------------------------
		
		document.getElementById("f_type").focus();
	}
	
	/*-------------------------------------------------------------------------*/
	// Do cancel
	/*-------------------------------------------------------------------------*/
	
	function do_cancel()
	{
		parent.IPS_editor[ editor_id ].module_remove_control_bar();
	 	return false;
	}
	
	/*-------------------------------------------------------------------------*/
	// Do submit (low tech)
	/*-------------------------------------------------------------------------*/
	
	function do_submit()
	{
		var fields = [ "f_type"   , "f_fontfamily"     , "f_fontsize", "f_color"  , "f_backgroundimage", "f_backgroundrepeat",
					   "f_backgroundcolor", "f_border"  , "f_padding", "f_margin"         , "f_other" ];
					  
		var param = new Object();
		
		//-----------------------------------------
		// Compile data
		//-----------------------------------------
		
		for ( var i in fields )
		{
			param[ fields[i] ] = document.getElementById( fields[i] ).value;
		}
		
		//-----------------------------------------
		// Now prep the table
		//-----------------------------------------
		
		var doc  = parent.IPS_editor[ editor_id ].editor_document;

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

		var tagtype  = param["f_type"];
		var style    = "";
		
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
		
		//-----------------------------------------
        // Return..
        //-----------------------------------------

		parent.IPS_editor[ editor_id].editor_check_focus();
		
		if ( style )
		{
			parent.IPS_editor[ editor_id ].wrap_tags_lite( '<' + tagtype + ' ' + style + '>', '</' + tagtype + '>' );
		}
		else
		{
			parent.IPS_editor[ editor_id ].wrap_tags_lite( '<' + tagtype + '>', '</' + tagtype + '>' );
		}
		
		//-----------------------------------------
		// Kill Window
		//-----------------------------------------
		
		do_cancel();
		
		return false;
	}
	//-->
	</script>

<style type='text/css' media="all">
@import url(rte_popup.css);
</style>
</head>

<body onload="Init()">

<div class="title">Выравнивание</div>

<form action="" method="get">
<table border="0" width='100%'  style="padding: 0px; margin: 0px">
  <tbody>
  <tr>
    <td width="1%" nowrap="nowrap">Тип:</td>
    <td width="99%"><select size="1" name="type" id="f_type" title="Div или Span?">
		<option value="div" selected="1"  >&lt;DIV&gt;</option>
		<option value="span"              >&lt;SPAN&gt;</option>
	  </select>
    </td>
  </tr>
  </tbody>
</table>

<br />

<fieldset style="margin-left: 5px;">
<legend>Текст</legend>
<table border="0" width='100%' style="padding: 0px; margin: 0px">
  <tbody>
  <tr>
    <td>Шрифт:</td>
    <td><input type="text" name="fontfamily" id="f_fontfamily" size="20" value="Verdana" title="Введите имя шрифта" /></td>
  </tr>
  <tr>
    <td>Размер:</td>
    <td><input type="text" name="fontsize" id="f_fontsize" size="7" value="10px" title="Введите размер" /></td>
  </tr>
  <tr>
    <td>Цвет:</td>
    <td><input type="text" name="color" id="f_color" size="7" value="#000" title="Введите цвет" /></td>
  </tr>
  </tbody>
</table>
</fieldset>

<br />

<fieldset style="margin-left: 5px;">
<legend>Фон</legend>
<table border="0" width='100%' style="padding: 0px; margin: 0px">
  <tbody>
  <tr>
    <td>Изображение:</td>
    <td><input type="text" name="backgroundimage" id="f_backgroundimage" size="15" value="" title="можно не заполнять" /></td>
  </tr>
  <tr>
    <td>Повтор:</td>
    <td><input type="text" name="backgroundrepeat" id="f_backgroundrepeat" size="15" value="" title="можно не заполнять" /></td>
  </tr>
  <tr>
    <td>Цвет:</td>
    <td><input type="text" name="backgroundcolor" id="f_backgroundcolor" size="7" value="" title="можно не заполнять" /></td>
  </tr>
  </tbody>
</table>
</fieldset>

<br />

<fieldset style="margin-left: 5px;">
<legend>Блок</legend>
<table border="0" width='100%' style="padding: 0px; margin: 0px">
  <tbody>
  <tr>
    <td>Границы:</td>
    <td><input type="text" name="border" id="f_border" size="20" value="" title="Введите размеры границ" /></td>
  </tr>
  <tr>
    <td>Размер полей:</td>
    <td><input type="text" name="padding" id="f_padding" size="20" value="4px" title="Введите размер" /></td>
  </tr>
  <tr>
    <td>Отступ:</td>
    <td><input type="text" name="margin" id="f_margin" size="20" value="" title="Введите размер" /></td>
  </tr>
  </tbody>
</table>
</fieldset>

<fieldset style="margin-left: 5px;">
<legend>Другие элементы</legend>
<table border="0" width='100%' style="padding: 0px; margin: 0px">
  <tbody>
  <tr>
    <td><textarea name="other" id="f_other" style='width:100%;height:70px' cols='23' rows='4'></textarea></td>
  </tr>
  </tbody>
</table>
</fieldset>


<div style="text-align: center;">
<hr />
<button type="button" class='tblbutton' name="ok" onclick="return do_submit();">ОК</button>
<button type="button" class='tblbutton' name="cancel" onclick="return do_cancel();">Отмена</button>
</div>

</form>

</body>
</html>
