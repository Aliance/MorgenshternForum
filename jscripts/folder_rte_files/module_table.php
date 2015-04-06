<html>
<head>
  <meta HTTP-EQUIV="Pragma"  CONTENT="no-cache">
  <meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
  <meta HTTP-EQUIV="Expires" CONTENT="Mon, 06 May 1996 04:57:00 GMT">
  <title>Вставка</title>
	<script type="text/javascript">
	<!--
	
	//-----------------------------------------
    // Attempt to get editor ID
    //-----------------------------------------
    
    var editor_id   = <?php print '"'.trim($_REQUEST['editorid']).'";'; ?>
	var this_module = 'table';
	var this_height = 350;
	
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
		
		parent.document.getElementById( editor_id + '_htmlblock_' + this_module   + '_menu' ).style.height = this_height + 'px';
		parent.document.getElementById( editor_id + '_iframeblock_' + this_module + '_menu' ).style.height = ( this_height - 10 ) + 'px';
		
		//-----------------------------------------
		// Module specific stuff
		//-----------------------------------------
		
		document.getElementById("f_rows").focus();
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
        //-----------------------------------------
        // Fail safe
        //-----------------------------------------
        
        if ( ! editor_id )
        {
            alert("Невозможно найти ID редактора");
            return false;
        }
        
        //-----------------------------------------
        // Set up required fields
        //-----------------------------------------
        
		var required = {
						  "f_rows": "Вы должны ввести количество строк",
						  "f_cols": "Вы должны ввести количество столбцов"
					   };
					
		for (var i in required)
		{
			var el = document.getElementById(i);
		  
			if ( ! el.value )
			{
				alert(required[i]);
				el.focus();
				return false;
			}
		}
		
		var fields = ["f_rows" , "f_cols"  , "f_width"  , "f_unit",
					  "f_align", "f_border", "f_spacing", "f_padding"];
					  
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

    	//-----------------------------------------
    	// create the table element inside a wrapper
    	// div that we can use innerHTML on
    	//-----------------------------------------
        
        var wrap_div = document.createElement("DIV");
    	var table    = document.createElement("table");
        
        wrap_div.appendChild( table );
        
    	//-----------------------------------------
    	// assign the given arguments
    	//-----------------------------------------

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

    	var tbody = document.createElement("tbody");

    	table.appendChild(tbody);

    	for (var i = 0; i < param["f_rows"]; ++i)
    	{
    		var tr = document.createElement("tr");

    		tbody.appendChild(tr);

    		for (var j = 0; j < param["f_cols"]; ++j)
    		{
    			var td = document.createElement("td");

    			tr.appendChild(td);

    			//-------------------------------
    			// Mozilla likes to see something
    			// inside the cell.
    			//-------------------------------

    			( ! parent.IPS_editor[ editor_id ].is_ie )
    			{
    				td.appendChild( document.createElement("br") );
    			}
    		}
    	}

		//-----------------------------------------
		// Clean up
		//-----------------------------------------
		
		wrap_div.innerHTML =  parent.IPS_editor[ editor_id ].clean_html( wrap_div.innerHTML );
        
        //-----------------------------------------
        // Return..
        //-----------------------------------------

		parent.IPS_editor[ editor_id].editor_check_focus();
       
        parent.IPS_editor[ editor_id ].insert_text( wrap_div.innerHTML );
		
		//-----------------------------------------
		// Kill node
		//-----------------------------------------
		
		//doc.removeChild( wrap_div );
		
		//-----------------------------------------
		// Kill Window
		//-----------------------------------------
		
		do_cancel();
	}
	//-->
	</script>

<style type='text/css' media="all">
@import url(rte_popup.css);
</style>

</head>

<body onload="Init()">

<div class="title">Вставить таблицу</div>

<form action="" method="get">
<fieldset style="margin-left: 5px;">
<legend>Основы</legend>
<table border="0" width='100%' style="padding: 0px; margin: 0px">
  <tbody>
  <tr>
    <td>Строк:</td>
    <td><input type="text" name="rows" id="f_rows" size="5" title="Число строк" value="2" /></td>
  </tr>
  <tr>
    <td>Столбцов:</td>
    <td><input type="text" name="cols" id="f_cols" size="5" title="Число столбцов" value="4" /></td>
  </tr>
  <tr>
    <td>Ширина таблицы:</td>
    <td><input type="text" name="width" id="f_width" size="5" title="Ширина таблицы" value="100" />
        <select size="1" name="unit" id="f_unit" title="Величина">
            <option value="%" selected="1"  >процентов</option>
            <option value="px"              >пикселей</option>
            <option value="em"              >пунктов</option>
        </select>
    </td>
  </tr>
  </tbody>
</table>
</fieldset>

<br />

<fieldset style="margin-left: 5px;">
<legend>Размещение</legend>
<table border="0"  width='100%' >
  <tbody>
  <tr>
    <td>Выравнивание:</td>
    <td>
        <select size="1" name="align" id="f_align"
          title="Расположениe изображения">
          <option value="" selected="1"                >не делать</option>
          <option value="left"                         >слева</option>
          <option value="right"                        >справа</option>
          <option value="texttop"                      >над текстом</option>
          <option value="absmiddle"                    >абсолютно по середине</option>
          <option value="baseline"                     >на базовой линии</option>
          <option value="absbottom"                    >внизу по середине</option>
          <option value="bottom"                       >внизу</option>
          <option value="middle"                       >по середине</option>
          <option value="top"                          >наверху</option>
        </select>
    </td>
  </tr>
  </tbody>
</table>
</fieldset>

<br />

<fieldset style="margin-left: 5px;">
<legend>Разбивка</legend>
<table border="0"  width='100%' style="padding: 0px; margin: 0px">
  <tbody>
    <tr>
      <td>Расстояние между ячейками:</td>
      <td><input type="text" name="cols" id="f_spacing" size="5" title="расстояние между" value="0" /></td>
    </tr>
    <tr>
      <td>Отступ в ячейках:</td>
      <td><input type="text" name="cols" id="f_padding" size="5" title="отступ" value="0" /></td>
    </tr>
    <tr>
      <td>Толщина границ:</td>
      <td><input type="text" name="border" id="f_border" size="5" value="1" title="если без границы - оставьте пустым" />
      </td>
    </tr>
  </tbody>
</table>
</fieldset>

<hr />
<div align='center'>
	<button type="button" class='tblbutton' name="ok" onclick="return do_submit();">OK</button>
	<button type="button" class='tblbutton' name="cancel" onclick="return do_cancel();">Отмена</button>
</div>

</form>

</body>
</html>
