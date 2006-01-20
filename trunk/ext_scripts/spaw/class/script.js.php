  // control registration array
  var spaw_editors = new Array();
  
  // returns true if editor is already registered
  function SPAW_editor_registered(editor)
  {
    var found = false;
    for(var i=0;i<spaw_editors.length;i++)
    {
      if (spaw_editors[i] == editor)
      {
        found = true;
        break;
      }
    }
    return(found);
  }
  
  // onsubmit
  function SPAW_UpdateFields()
  {
    for (var i=0; i<spaw_editors.length; i++)
    {
      SPAW_updateField(spaw_editors[i], null);
    }
  }
  
  // adds event handler for the form to update hidden fields
  function SPAW_addOnSubmitHandler(editor)
  {
    thefield = SPAW_getFieldByEditor(editor, null);

    var sTemp = "";
    oForm = document.all[thefield].form;
    if(oForm.onsubmit != null) {
      sTemp = oForm.onsubmit.toString();
      iStart = sTemp.indexOf("{") + 2;
      sTemp = sTemp.substr(iStart,sTemp.length-iStart-2);
    }
    if (sTemp.indexOf("SPAW_UpdateFields();") == -1)
    {
      oForm.onsubmit = new Function("SPAW_UpdateFields();" + sTemp);
    }
  }

  // editor initialization
  function SPAW_editorInit(editor, css_stylesheet, direction)
  {
     
     if (this[editor+'_rEdit'].document.designMode != 'on')
     {
      this[editor+'_rEdit'].document.designMode = 'On';
     }
 
     // check if the editor completely loaded and schedule to try again if not
     if (this[editor+'_rEdit'].document.readyState != 'complete')
     {
       setTimeout(function(){SPAW_editorInit(editor, css_stylesheet, direction);},20);
       return;
     }

     // register the editor 
  // prevent from executing twice on the same editor
     if (!SPAW_editor_registered(editor))
     {
       spaw_editors[spaw_editors.length] = editor;
     }
  
     // add on submit handler
     SPAW_addOnSubmitHandler(editor);
 
     if (this[editor+'_rEdit'].document.readyState == 'complete')
     {
       this[editor+'_rEdit'].document.createStyleSheet(css_stylesheet);
       this[editor+'_rEdit'].document.body.dir = direction;
       this[editor+'_rEdit'].document.body.innerHTML = document.all[editor].value;
SPAW_toggle_borders(editor,this[editor+'_rEdit'].document.body,null);
 
       // hookup active toolbar related events
       this[editor+'_rEdit'].document.onkeyup = function() { SPAW_onkeyup(editor); }
       this[editor+'_rEdit'].document.onmouseup = function() { SPAW_update_toolbar(editor, true); }
       
       // initialize toolbar
       spaw_context_html = "";
       SPAW_update_toolbar(editor, true);
     }
  }  
  
  
  function SPAW_showColorPicker(editor,curcolor) {
    return showModalDialog('<?php echo $spaw_dir?>dialogs/colorpicker.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value, curcolor, 
      'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
  }

  function SPAW_bold_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('bold', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_italic_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
   	this[editor+'_rEdit'].document.execCommand('italic', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_underline_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('underline', false, null);
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_left_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('justifyleft', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_center_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('justifycenter', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_right_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('justifyright', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_justify_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('justifyfull', false, null);
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_ordered_list_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('insertorderedlist', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_bulleted_list_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('insertunorderedlist', false, null);
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_fore_color_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var fCol = SPAW_showColorPicker(editor,null);

    if(fCol != null)
      this[editor+'_rEdit'].document.execCommand('forecolor', false, fCol);

    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_bg_color_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var bCol = SPAW_showColorPicker(editor,null);
    
    if(bCol != null)
    	this[editor+'_rEdit'].document.execCommand('backcolor', false, bCol);

    SPAW_update_toolbar(editor, true);    
  }

  /*
  // using standard IE link dialog
  function SPAW_hyperlink_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	var l = this[editor+'_rEdit'].document.execCommand('createlink');
    SPAW_update_toolbar(editor, true);    
  }
  */
  
  function SPAW_getA(editor)
  {
  	var aControl = window.frames[editor+'_rEdit'].document.selection.createRange();
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
    	aControl = aControl.parentElement();
    }
    else
    {
    	aControl = aControl(0);
    }
    
	while ((aControl.tagName != 'A') && (aControl.tagName != 'BODY'))
    {
      aControl = aControl.parentElement;
    }
    if (aControl.tagName == 'A')
      return(aControl);
    else
      return(null);
  }

  function SPAW_hyperlink_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

  	var a = SPAW_getA(editor);
	
    var aProps = {};
	// get anchors on the page
	aProps.anchors = new Array();
	var links = this[editor+'_rEdit'].document.getElementsByTagName('A');
	var aln = 0;
	if (links != null) aln = links.length;
	for (var i=0;i<aln;i++)
	{
		if (links[i].name != null && links[i].name != '')
			aProps.anchors[aProps.anchors.length] = links[i].name;
	}

	if (a)
	{
	  // edit hyperlink
      aProps.href = SPAW_stripAbsoluteUrl(editor, a.href);
      aProps.name = a.name;
      aProps.target = a.target;
      aProps.title = a.title;
  
      var naProps = showModalDialog('<?php echo $spaw_dir?>dialogs/a.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value, aProps, 
        'dialogHeight:220px; dialogWidth:366px; resizable:no; status:no');  
      
      if (naProps)  
      {
	  	if (!naProps.href && !naProps.name)
		{
			// remove hyperlink
			a.outerHTML = a.innerHTML;
		}
		else
		{
			// set link properties
		  	if (naProps.href)
		        a.href = naProps.href;
			else
				a.removeAttribute('href',0);
			if (naProps.name)
		        a.name = naProps.name;
			else
				a.removeAttribute('name',0);
			if (naProps.target && naProps.target!='_self')
		        a.target = naProps.target;
			else
				a.removeAttribute('target',0);
			if (naProps.title)
		        a.title = naProps.title;
			else
				a.removeAttribute('title',0);
	
		  	a.removeAttribute('onclick',0);
		}
      }      
	}
	else
	{
	  // new hyperlink
      var naProps = showModalDialog('<?php echo $spaw_dir?>dialogs/a.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value, aProps, 
        'dialogHeight:220px; dialogWidth:366px; resizable:no; status:no');  
      
      if (naProps)  
      {
		var a;
		if (naProps.name)
		{
		  a = document.createElement('<A NAME="'+naProps.name+'"></A>');
		}
		else
		{
		  a = document.createElement('A');
		}
	  	if (naProps.href)
	        a.href = naProps.href;
		if (naProps.target && naProps.target!='_self')
	        a.target = naProps.target;
		if (naProps.title)
	        a.title = naProps.title;
		if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
		{
	        var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
			a.innerHTML = selection(0).outerHTML;
			selection(0).outerHTML = a.outerHTML;
		}
		else
		{
	        var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
		    if (selection.htmlText == '')
			  a.innerHTML = (a.href && a.href!='')?a.href:a.name;
		    else
			  a.innerHTML = selection.htmlText;
		
		    selection.pasteHTML(a.outerHTML);      
		}
      }      
	}

    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_internal_link_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

  	var a = SPAW_getA(editor);
	
	if (a)
	{
	  // edit hyperlink
      var aProps = {};
      aProps.href = SPAW_stripAbsoluteUrl(editor, a.href);
      aProps.name = a.name;
      aProps.target = a.target;
      aProps.title = a.title;
	  aProps.description = a.innerHTML;
  
      var naProps = showModalDialog('<?php echo $spaw_internal_link_script?>?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value, aProps, 
        'dialogHeight:220px; dialogWidth:366px; resizable:yes; status:no');  
      
      if (naProps)  
      {
	  	if (naProps.href)
	        a.href = naProps.href;
		else
			a.removeAttribute('href',0);
		if (naProps.name)
	        a.name = naProps.name;
		else
			a.removeAttribute('name',0);
		if (naProps.target && naProps.target!='_self')
	        a.target = naProps.target;
		else
			a.removeAttribute('target',0);
		if (naProps.title)
	        a.title = naProps.title;
		else
			a.removeAttribute('title',0);
		if (naProps.description)
			a.innerHTML = naProps.description;
      }      
	}
	else
	{
	  // new hyperlink
      var naProps = showModalDialog('<?php echo $spaw_internal_link_script?>?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value, null, 
        'dialogHeight:220px; dialogWidth:366px; resizable:no; status:no');  
      
      if (naProps)  
      {
		var a;
		if (naProps.name)
		{
		  a = document.createElement('<A NAME="'+naProps.name+'"></A>');
		}
		else
		{
		  a = document.createElement('A');
		}
	  	if (naProps.href)
	        a.href = naProps.href;
		if (naProps.target && naProps.target!='_self')
	        a.target = naProps.target;
		if (naProps.title)
	        a.title = naProps.title;

		if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
		{
	        var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
			a.innerHTML = selection(0).outerHTML;
			selection(0).outerHTML = a.outerHTML;
		}
		else
		{
	        var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
			if (naProps.description)
			{
				a.innerHTML = naProps.description;
			}
			else
			{
			    if (selection.htmlText == '')
					a.innerHTML = (a.href)?a.href:a.name;
			    else
				    a.innerHTML = selection.htmlText;
			}	
		    selection.pasteHTML(a.outerHTML);      
		}
      }      
	}

    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_image_insert_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var imgSrc = showModalDialog('<?php echo $spaw_dir?>dialogs/img_library.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value+'&request_uri='+escape(window.location.href), '', 
      'dialogHeight:420px; dialogWidth:420px; resizable:no; status:no');
    
    if(imgSrc != null)    
    	this[editor+'_rEdit'].document.execCommand('insertimage', false, imgSrc);

    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_image_prop_click(editor, sender)
  {
    var im = SPAW_getImg(editor); // current cell
    
    if (im)
    {
      var iProps = {};
      iProps.src = SPAW_stripAbsoluteUrlFromImg(editor, im.src);
      iProps.alt = im.alt;
      iProps.width = (im.style.width)?im.style.width:im.width;
      iProps.height = (im.style.height)?im.style.height:im.height;
      iProps.border = im.border;
      iProps.align = im.align;
      iProps.hspace = im.hspace;
      iProps.vspace = im.vspace;
  
      var niProps = showModalDialog('<?php echo $spaw_dir?>dialogs/img.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value, iProps, 
        'dialogHeight:200px; dialogWidth:366px; resizable:no; status:no');  
      
      if (niProps)  
      {
        im.src = (niProps.src)?niProps.src:'';
        if (niProps.alt) {
          im.alt = niProps.alt;
        }
        else
        {
          im.removeAttribute("alt",0);
        }
        im.align = (niProps.align)?niProps.align:'';
        im.width = (niProps.width)?niProps.width:'';
        //im.style.width = (niProps.width)?niProps.width:'';
        im.height = (niProps.height)?niProps.height:'';
        //im.style.height = (niProps.height)?niProps.height:'';
        if (niProps.border) {
          im.border = niProps.border;
        }
        else
        {
          im.removeAttribute("border",0);
        }
        if (niProps.hspace) {
          im.hspace = niProps.hspace;
        }
        else
        {
          im.removeAttribute("hspace",0);
        }
        if (niProps.vspace) {
          im.vspace = niProps.vspace;
        }
        else
        {
          im.removeAttribute("vspace",0);
        }
      }      
      //SPAW_updateField(editor,"");
    } // if im
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_image_popup_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
	
  	var a = SPAW_getA(editor);

    var imgSrc = showModalDialog('<?php echo $spaw_dir?>dialogs/img_library.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value+'&request_uri='+escape(window.location.href), '', 
      'dialogHeight:420px; dialogWidth:420px; resizable:no; status:no');
    
    if(imgSrc != null)    
	{
		if (a)
		{
			// edit hyperlink
			a.href="#";
			a.onclick="window.open('<?php echo $spaw_img_popup_url?>?img_url="+SPAW_stripAbsoluteUrlFromImg(editor, imgSrc)+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;";
		}
		else
		{
			var a;
			a = document.createElement('A');
			a.href="#";
			a.onclick="window.open('<?php echo $spaw_img_popup_url?>?img_url="+SPAW_stripAbsoluteUrlFromImg(editor, imgSrc)+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;";


			if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
			{
		        var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
				a.innerHTML = selection(0).outerHTML;
				selection(0).outerHTML = a.outerHTML;
			}
			else
			{
				var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
				if (selection.htmlText == '')
					a.innerHTML = (a.href)?a.href:a.name;
				else
					a.innerHTML = selection.htmlText;
		
				selection.pasteHTML(a.outerHTML);      
			}
		}	
	}	
	

    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_hr_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('inserthorizontalrule', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_copy_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('copy', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_paste_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('paste', false, null);
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_cut_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('cut', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_delete_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('delete', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_indent_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('indent', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_unindent_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('outdent', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_undo_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('undo','',null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_redo_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('redo', false, null);
    SPAW_update_toolbar(editor, true);    
  }
  
  
  function SPAW_getParentTag(editor)
  {
    var trange = this[editor+'_rEdit'].document.selection.createRange();
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      return (trange.parentElement());
    }
    else
    {
      return (trange(0));    
    }
  }

  // trim functions  
  function SPAW_ltrim(txt)
  {
    var spacers = " \t\r\n";
    while (spacers.indexOf(txt.charAt(0)) != -1)
    {
      txt = txt.substr(1);
    }
    return(txt);
  }
  function SPAW_rtrim(txt)
  {
    var spacers = " \t\r\n";
    while (spacers.indexOf(txt.charAt(txt.length-1)) != -1)
    {
      txt = txt.substr(0,txt.length-1);
    }
    return(txt);
  }
  function SPAW_trim(txt)
  {
    return(SPAW_ltrim(SPAW_rtrim(txt)));
  }


  
  // is selected text a full tags inner html?
  function SPAW_isFoolTag(editor, el)
  {
    var trange = this[editor+'_rEdit'].document.selection.createRange();
    var ttext;
    if (trange != null) ttext = SPAW_trim(trange.htmlText);
    if (ttext != SPAW_trim(el.innerHtml))
      return false;
    else
      return true;
  }
  
  function SPAW_style_change(editor, sender)
  {
    classname = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    var el = SPAW_getParentTag(editor);
    if (el != null && el.tagName.toLowerCase() != 'body')
    {
      if (classname != 'default')
        el.className = classname;
      else
        el.removeAttribute('className',0);
    }
    else if (el.tagName.toLowerCase() == 'body')
    {
      if (classname != 'default')
        this[editor+'_rEdit'].document.body.innerHTML = '<p class="'+classname+'">'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
      else
        this[editor+'_rEdit'].document.body.innerHTML = '<p>'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
    }
    sender.selectedIndex = 0;

    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_font_change(editor, sender)
  {
    fontname = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();
    
    if (fontname == null || fontname == '')
    {
      this[editor+'_rEdit'].document.execCommand('RemoveFormat', false, null);
    }
    else   
    {
      this[editor+'_rEdit'].document.execCommand('fontname', false, fontname);
    }

    sender.selectedIndex = 0;

    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_fontsize_change(editor, sender)
  {
    fontsize = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    this[editor+'_rEdit'].document.execCommand('fontsize', false, fontsize);

    sender.selectedIndex = 0;

    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_paragraph_change(editor, sender)
  {
    format = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    this[editor+'_rEdit'].document.execCommand('formatBlock', false, format);

    sender.selectedIndex = 0;

    SPAW_update_toolbar(editor, true);    
  }
    
  function SPAW_table_create_click(editor, sender)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      // selection is not a control => insert table 
      var nt = showModalDialog('<?php echo $spaw_dir?>dialogs/table.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value+'&request_uri='+escape(window.location.href), null, 
        'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
       
      if (nt)
      {
        window.frames[editor+'_rEdit'].focus();     
    
        var newtable = document.createElement('TABLE');
        try {
		  if (nt.width)
          	newtable.width = nt.width;
		  if (nt.height)
          	newtable.height = nt.height;
		  if (nt.border)
          	newtable.border = nt.border;
          if (nt.cellPadding) 
		  	newtable.cellPadding = nt.cellPadding;
          if (nt.cellSpacing) 
		  	newtable.cellSpacing = nt.cellSpacing;
		  if (nt.bgColor)
          	newtable.bgColor = nt.bgColor;
		  if (nt.background)
		  	newtable.background = nt.background;
		  if (nt.className)
		  	newtable.className = nt.className;
          
          // create rows
          for (var i=0;i<parseInt(nt.rows);i++)
          {
            var newrow = document.createElement('TR');
            for (var j=0; j<parseInt(nt.cols); j++)
            {
              var newcell = document.createElement('TD');
              newrow.appendChild(newcell);
            }
            newtable.appendChild(newrow);
          }
          var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
        	selection.pasteHTML(newtable.outerHTML);      
          SPAW_toggle_borders(editor, window.frames[editor+'_rEdit'].document.body, null);
          SPAW_update_toolbar(editor, true);    
        }
        catch (excp)
        {
          alert('error');
        }
      }
    }
  }
  
  function SPAW_table_prop_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var tTable
    // check if table selected
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
    { 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'TABLE')
      {
        tTable = tControl(0);
      }
    }
    else
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if (tControl.tagName == 'TABLE')
        tTable = tControl;
      else
        return false;
    }

    var tProps = {};
    tProps.width = (tTable.style.width)?tTable.style.width:tTable.width;
    tProps.height = (tTable.style.height)?tTable.style.height:tTable.height;
    tProps.border = tTable.border;
    tProps.cellPadding = tTable.cellPadding;
    tProps.cellSpacing = tTable.cellSpacing;
    tProps.bgColor = tTable.bgColor;
	tProps.className = tTable.className;
	tProps.background = tTable.background;

    var ntProps = showModalDialog('<?php echo $spaw_dir?>dialogs/table.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value+'&request_uri='+escape(window.location.href), tProps, 
      'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
    
    if (ntProps)
    {
      // set new settings
	  if (ntProps.width)
	    tTable.width = ntProps.width;
	  else
	  	tTable.removeAttribute('width',0);
   	  tTable.style.width = (ntProps.width)?ntProps.width:'';
      if (ntProps.height)
	  	tTable.height = ntProps.height
	  else
	  	tTable.removeAttribute('height',0);
      tTable.style.height = (ntProps.height)?ntProps.height:'';
	  if (ntProps.border)
	  	tTable.border = ntProps.border;
	  else
	  	tTable.removeAttribute('border',0);
      if (ntProps.cellPadding) 
	  	tTable.cellPadding = ntProps.cellPadding;
	  else
	  	tTable.removeAttribute('cellpadding',0);
      if (ntProps.cellSpacing) 
	  	tTable.cellSpacing = ntProps.cellSpacing;
	  else
	  	tTable.removeAttribute('cellspacing',0);
	  if (ntProps.bgColor)
      	tTable.bgColor = ntProps.bgColor;
	  else
	  	tTable.removeAttribute('bgcolor',0);
	  if (ntProps.background)
	  	tTable.background = ntProps.background;
	  else
	  	tTable.removeAttribute('background',0);
	  if (ntProps.className)
	  	tTable.className = ntProps.className;
	  else
	  	tTable.removeAttribute('className',0);

      SPAW_toggle_borders(editor, tTable, null);
    }

    SPAW_update_toolbar(editor, true);    
    //SPAW_updateField(editor,"");
  }
  
  // edits table cell properties
  function SPAW_table_cell_prop_click(editor, sender)
  {
    var cd = SPAW_getTD(editor); // current cell
    
    if (cd)
    {
      var cProps = {};
      cProps.width = (cd.style.width)?cd.style.width:cd.width;
      cProps.height = (cd.style.height)?cd.style.height:cd.height;
      cProps.bgColor = cd.bgColor;
	  cProps.background = cd.background;
      cProps.align = cd.align;
      cProps.vAlign = cd.vAlign;
      cProps.className = cd.className;
      cProps.noWrap = cd.noWrap;
      cProps.styleOptions = new Array();
      if (document.all['SPAW_'+editor+'_tb_style'] != null)
      {
        cProps.styleOptions = document.all['SPAW_'+editor+'_tb_style'].options;
      }
  
      var ncProps = showModalDialog('<?php echo $spaw_dir?>dialogs/td.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value+'&request_uri='+escape(window.location.href), cProps, 
        'dialogHeight:220px; dialogWidth:366px; resizable:no; status:no');  
      
      if (ncProps)  
      {
	  	if (ncProps.align)
			cd.align = ncProps.align;
		else
			cd.removeAttribute('align',0);
		if (ncProps.vAlign)
        	cd.vAlign = ncProps.vAlign;
		else
			cd.removeAttribute('valign',0);
		if (ncProps.width)
        	cd.width = ncProps.width;
		else
			cd.removeAttribute('width',0);
        cd.style.width = (ncProps.width)?ncProps.width:'';
		if (ncProps.height)
        	cd.height = ncProps.height;
		else
			cd.removeAttribute('height',0);
        cd.style.height = (ncProps.height)?ncProps.height:'';
		if (ncProps.bgColor)
        	cd.bgColor = ncProps.bgColor;
		else
			cd.removeAttribute('bgcolor',0);
	    if (ncProps.background)
		  	cd.background = ncProps.background;
		else
		  	cd.removeAttribute('background',0);
		if (ncProps.className)
        	cd.className = ncProps.className;
		else
			cd.removeAttribute('className',0);
		if (ncProps.noWrap)
        	cd.noWrap = ncProps.noWrap;
		else
			cd.removeAttribute('nowrap',0);
      }      
    }
    SPAW_update_toolbar(editor, true);    
    //SPAW_updateField(editor,"");
  }

  // returns current table cell  
  function SPAW_getTD(editor)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TD') && (tControl.tagName != 'TH') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if ((tControl.tagName == 'TD') || (tControl.tagName == 'TH'))
        return(tControl);
      else
        return(null);
    }
    else
    {
      return(null);
    }
  }

  // returns current table row  
  function SPAW_getTR(editor)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TR') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if (tControl.tagName == 'TR')
        return(tControl);
      else
        return(null);
    }
    else
    {
      return(null);
    }
  }
  
  // returns current table  
  function SPAW_getTable(editor)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
    { 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'TABLE')
        return(tControl(0));
      else
        return(null);
    }
    else
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if (tControl.tagName == 'TABLE')
        return(tControl);
      else
        return(null);
    }
  }
  
  // returns selected image
  function SPAW_getImg(editor) {
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
    { 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'IMG')
        return(tControl(0));
      else
        return(null);
    }
    else
    {
      return(null);
    }
  }

  function SPAW_table_row_insert_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row

    if (ct && cr)
    {
      var newr = ct.insertRow(cr.rowIndex+1);
      for (var i=0; i<cr.cells.length; i++)
      {
        if (cr.cells(i).rowSpan > 1)
        {
          // increase rowspan
          cr.cells(i).rowSpan++;
        }
        else
        {
          var newc = cr.cells(i).cloneNode();
          newr.appendChild(newc);
        }
      }
      // increase rowspan for cells that were spanning through current row
      for (var i=0; i<cr.rowIndex; i++)
      {
        var tempr = ct.rows(i);
        for (var j=0; j<tempr.cells.length; j++)
        {
          if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
            tempr.cells(j).rowSpan++;
        }
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // insertRow
  
  function SPAW_formCellMatrix(ct)
  {
    var tm = new Array();
    for (var i=0; i<ct.rows.length; i++)
      tm[i]=new Array();

    for (var i=0; i<ct.rows.length; i++)
    {
      jr=0;
      for (var j=0; j<ct.rows(i).cells.length;j++)
      {
        while (tm[i][jr] != undefined) 
          jr++;

        for (var jh=jr; jh<jr+(ct.rows(i).cells(j).colSpan?ct.rows(i).cells(j).colSpan:1);jh++)
        {
          for (var jv=i; jv<i+(ct.rows(i).cells(j).rowSpan?ct.rows(i).cells(j).rowSpan:1);jv++)
          {
            if (jv==i)
            {
              tm[jv][jh]=ct.rows(i).cells(j).cellIndex;
            }
            else
            {
              tm[jv][jh]=-1;
            }
          }
        }
      }
    }
    return(tm);
  }
  
  function SPAW_table_column_insert_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current row

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = SPAW_formCellMatrix(ct);
      
      for (var j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      // insert column based on real cell matrix
      for (var i=0; i<ct.rows.length; i++)
      {
        if (tm[i][realIndex] != -1)
        {
          if (ct.rows(i).cells(tm[i][realIndex]).colSpan > 1)
          {
            ct.rows(i).cells(tm[i][realIndex]).colSpan++;
          }
          else
          {
            var newc = ct.rows(i).insertCell(tm[i][realIndex]+1)
            var nc = ct.rows(i).cells(tm[i][realIndex]).cloneNode();
            newc.replaceNode(nc);
          }
        }
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // insertColumn
  
  function SPAW_table_cell_merge_right_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current row

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = SPAW_formCellMatrix(ct);
      
      for (var j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      if (cd.cellIndex+1<cr.cells.length)
      {
        ccrs = cd.rowSpan?cd.rowSpan:1;
        cccs = cd.colSpan?cd.colSpan:1;
        ncrs = cr.cells(cd.cellIndex+1).rowSpan?cr.cells(cd.cellIndex+1).rowSpan:1;
        nccs = cr.cells(cd.cellIndex+1).colSpan?cr.cells(cd.cellIndex+1).colSpan:1;
        // check if theres nothing between these 2 cells
        j=realIndex;
        while(tm[cr.rowIndex][j] == cd.cellIndex) j++;
        if (tm[cr.rowIndex][j] == cd.cellIndex+1)
        {
          // proceed only if current and next cell rowspans are equal
          if (ccrs == ncrs)
          {
            // increase colspan of current cell and append content of the next cell to current
            cd.colSpan = cccs+nccs;
            cd.innerHTML += cr.cells(cd.cellIndex+1).innerHTML;
            cr.deleteCell(cd.cellIndex+1);
          }
        }
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // mergeRight


  function SPAW_table_cell_merge_down_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current row

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = SPAW_formCellMatrix(ct);
      
      for (var j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          crealIndex=j;
          break;
        }
      }
      ccrs = cd.rowSpan?cd.rowSpan:1;
      cccs = cd.colSpan?cd.colSpan:1;
      
      if (cr.rowIndex+ccrs<ct.rows.length)
      {
        ncellIndex = tm[cr.rowIndex+ccrs][crealIndex];
        if (ncellIndex != -1 && (crealIndex==0 || (crealIndex>0 && (tm[cr.rowIndex+ccrs][crealIndex-1]!=tm[cr.rowIndex+ccrs][crealIndex]))))
        {
    
          ncrs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan:1;
          nccs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan:1;
          // proceed only if current and next cell colspans are equal
          if (cccs == nccs)
          {
            // increase rowspan of current cell and append content of the next cell to current
            cd.innerHTML += ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).innerHTML;
            ct.rows(cr.rowIndex+ccrs).deleteCell(ncellIndex);
            cd.rowSpan = ccrs+ncrs;
          }
        }
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // mergeDown
  
  function SPAW_table_row_delete_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // if there's only one row just remove the table
      if (ct.rows.length<=1)
      {
        ct.removeNode(true);
      }
      else
      {
        // get "real" cell position and form cell matrix
        var tm = SPAW_formCellMatrix(ct);
        
        
        // decrease rowspan for cells that were spanning through current row
        for (var i=0; i<cr.rowIndex; i++)
        {
          var tempr = ct.rows(i);
          for (var j=0; j<tempr.cells.length; j++)
          {
            if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
              tempr.cells(j).rowSpan--;
          }
        }
    
        
        curCI = -1;
        // check for current row cells spanning more than 1 row
        for (var i=0; i<tm[cr.rowIndex].length; i++)
        {
          prevCI = curCI;
          curCI = tm[cr.rowIndex][i];
          if (curCI != -1 && curCI != prevCI && cr.cells(curCI).rowSpan>1 && (cr.rowIndex+1)<ct.rows.length)
          {
            ni = i;
            nrCI = tm[cr.rowIndex+1][ni];
            while (nrCI == -1) 
            {
              ni++;
              if (ni<ct.rows(cr.rowIndex+1).cells.length)
                nrCI = tm[cr.rowIndex+1][ni];
              else
                nrCI = ct.rows(cr.rowIndex+1).cells.length;
            }
            
            var newc = ct.rows(cr.rowIndex+1).insertCell(nrCI);
            ct.rows(cr.rowIndex).cells(curCI).rowSpan--;
            var nc = ct.rows(cr.rowIndex).cells(curCI).cloneNode();
            newc.replaceNode(nc);
            // fix the matrix
            cs = (cr.cells(curCI).colSpan>1)?cr.cells(curCI).colSpan:1;
            for (var j=i; j<(i+cs);j++)
            {
              tm[cr.rowIndex+1][j] = nrCI;
              nj = j;
            }
            for (var j=nj; j<tm[cr.rowIndex+1].length; j++)
            {
              if (tm[cr.rowIndex+1][j] != -1)
                tm[cr.rowIndex+1][j]++;
            }
          }
        }
        // delete row
        ct.deleteRow(cr.rowIndex);
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // deleteRow
  
  function SPAW_table_column_delete_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = SPAW_formCellMatrix(ct);

      // if there's only one column delete the table
      if (tm[0].length<=1)  
      {
        ct.removeNode(true);
      }
      else
      {
        for (var j=0; j<tm[cr.rowIndex].length; j++)
        {
          if (tm[cr.rowIndex][j] == cd.cellIndex)
          {
            realIndex=j;
            break;
          }
        }
        
        for (var i=0; i<ct.rows.length; i++)
        {
          if (tm[i][realIndex] != -1)
          {
            if (ct.rows(i).cells(tm[i][realIndex]).colSpan>1)
              ct.rows(i).cells(tm[i][realIndex]).colSpan--;
            else
              ct.rows(i).deleteCell(tm[i][realIndex]);
          }
        }
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // deleteColumn
  
  // split cell horizontally
  function SPAW_table_cell_split_horizontal_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = SPAW_formCellMatrix(ct);
  
      for (var j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      if (cd.rowSpan>1) 
      {
        // split only current cell
        // find where to insert a cell in the next row
        i = realIndex;
        while (tm[cr.rowIndex+1][i] == -1) i++;
        if (i == tm[cr.rowIndex+1].length) 
          ni = ct.rows(cr.rowIndex+1).cells.length;
        else
          ni = tm[cr.rowIndex+1][i];
          
        var newc = ct.rows(cr.rowIndex+1).insertCell(ni);
        cd.rowSpan--;
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
  
        cd.rowSpan = 1;
      }
      else
      {
        // add new row and make all other cells to span one row more
        ct.insertRow(cr.rowIndex+1);
        for (var i=0; i<cr.cells.length; i++)
        {
          if (i != cd.cellIndex)
          {
            rs = cr.cells(i).rowSpan>1?cr.cells(i).rowSpan:1;
            cr.cells(i).rowSpan = rs+1;
          }
        }
  
        for (var i=0; i<cr.rowIndex; i++)
        {
          var tempr = ct.rows(i);
          for (var j=0; j<tempr.cells.length; j++)
          {
            if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
              tempr.cells(j).rowSpan++;
          }
        }
        
        // clone current cell to new row
        var newc = ct.rows(cr.rowIndex+1).insertCell(0);
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // splitH
  
  function SPAW_table_cell_split_vertical_click(editor, sender)
  {
    var ct = SPAW_getTable(editor); // current table
    var cr = SPAW_getTR(editor); // current row
    var cd = SPAW_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = SPAW_formCellMatrix(ct);
  
      for (var j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      if (cd.colSpan>1)    
      {
        // split only current cell
        var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
        cd.colSpan--;
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
        cd.colSpan = 1;
      }
      else
      {
        // clone current cell
        var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
        
        for (var i=0; i<tm.length; i++)
        {
          if (i!=cr.rowIndex && tm[i][realIndex] != -1)
          {
            cs = ct.rows(i).cells(tm[i][realIndex]).colSpan>1?ct.rows(i).cells(tm[i][realIndex]).colSpan:1;
            ct.rows(i).cells(tm[i][realIndex]).colSpan = cs+1;
          }
        }
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // splitV
  

  // switch to wysiwyg mode
  function SPAW_design_tab_click(editor, sender)
  {
    //iText = this[editor+'_rEdit'].document.body.innerText;
    iText = document.all[editor].value;
    this[editor+'_rEdit'].document.body.innerHTML = iText;
    
    document.all['SPAW_'+editor+'_editor_mode'].value = 'design';

    // turn off html mode toolbars
    document.all['SPAW_'+editor+'_toolbar_top_html'].style.display = 'none';
    document.all['SPAW_'+editor+'_toolbar_left_html'].style.display = 'none';
    document.all['SPAW_'+editor+'_toolbar_right_html'].style.display = 'none';
    document.all['SPAW_'+editor+'_toolbar_bottom_html'].style.display = 'none';

    // turn on design mode toolbars
    document.all['SPAW_'+editor+'_toolbar_top_design'].style.display = 'inline';
    document.all['SPAW_'+editor+'_toolbar_left_design'].style.display = 'inline';
    document.all['SPAW_'+editor+'_toolbar_right_design'].style.display = 'inline';
    document.all['SPAW_'+editor+'_toolbar_bottom_design'].style.display = 'inline';

    // switch editors    
    document.all[editor].style.display = "none";
    document.all[editor+"_rEdit"].style.display = "inline";
    document.all[editor+"_rEdit"].document.body.focus();
    
    // turn on invisible borders if needed
    SPAW_toggle_borders(editor,this[editor+'_rEdit'].document.body, null);
    
    this[editor+'_rEdit'].focus();
    SPAW_update_toolbar(editor, true);    
  }
  
  // switch to html mode
  function SPAW_html_tab_click(editor, sender)
  {
    var iHTML = SPAW_getHtmlValue(editor, null);

    //iHTML = this[editor+'_rEdit'].document.body.innerHTML;
    //this[editor+'_rEdit'].document.body.innerText = iHTML;
    document.all[editor].value = iHTML;
    
    document.all['SPAW_'+editor+'_editor_mode'].value = 'html';

    // turn off design mode toolbars
    document.all['SPAW_'+editor+'_toolbar_top_design'].style.display = 'none';
    document.all['SPAW_'+editor+'_toolbar_left_design'].style.display = 'none';
    document.all['SPAW_'+editor+'_toolbar_right_design'].style.display = 'none';
    document.all['SPAW_'+editor+'_toolbar_bottom_design'].style.display = 'none';

    // turn on html mode toolbars
    document.all['SPAW_'+editor+'_toolbar_top_html'].style.display = 'inline';
    document.all['SPAW_'+editor+'_toolbar_left_html'].style.display = 'inline';
    document.all['SPAW_'+editor+'_toolbar_right_html'].style.display = 'inline';
    document.all['SPAW_'+editor+'_toolbar_bottom_html'].style.display = 'inline';

    // switch editors    
    document.all[editor+"_rEdit"].style.display = "none";
    document.all[editor].style.display = "inline";
    document.all[editor].focus();

    this[editor+'_rEdit'].focus();
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_getFieldByEditor(editor, field)
  {
    // NOTE: this function doesn't make much sense in the current situation
    // but is left so nothing gets broken
    var thefield;
    // get by id
    //// OLD: get field by editor name if no field passed
    if (field == null || field == "")
    {
      // var flds = document.getElementsByName(editor);
      // thefield = flds[0].id;
      thefield = document.getElementById(editor).id;
    }
    else
    {
      thefield=field;
    }
    return thefield;
  }
  
  function SPAW_stripAbsoluteUrl(editor, url)
  {
	var curl = window.frames[editor+'_rEdit'].location.href;
	var di = curl.lastIndexOf('/', curl.lastIndexOf('?')!=-1?curl.lastIndexOf('?'):curl.length);
	var cdir = curl;
	if (di != -1)
		cdir = curl.substr(0,di+1);
	var chost = curl;
	var hi = curl.indexOf('/',curl.indexOf('://')!=-1?(curl.indexOf('://')+3):curl.length);
	if (hi != -1)
		chost = curl.substr(0,hi);
  	if (url.toLowerCase().indexOf(curl.toLowerCase())==0)
	{
		url = url.substr(curl.length);
	}
	else if (url.toLowerCase().indexOf(cdir.toLowerCase())==0)
	{
		url = url.substr(cdir.length);
	}
	else if (url.toLowerCase().indexOf(chost.toLowerCase())==0)
	{
		url = url.substr(chost.length);
	}
	return(url);
  }

  function SPAW_stripAbsoluteUrlFromImg(editor, url)
  {
	var curl = window.frames[editor+'_rEdit'].location.href;
	var chost = curl;
	var hi = curl.indexOf('/',curl.indexOf('://')!=-1?(curl.indexOf('://')+3):curl.length);
	if (hi != -1)
		chost = curl.substr(0,hi);
	if (url.toLowerCase().indexOf(chost.toLowerCase())==0)
	{
		url = url.substr(chost.length);
	}
	return(url);
  }
  
  function SPAW_getHtmlValue(editor, thefield)
  {
    var htmlvalue;

    if(document.all['SPAW_'+editor+'_editor_mode'].value == 'design')
    {
      // wysiwyg
	  // replace automatic absolute urls
	  var links = this[editor+'_rEdit'].document.getElementsByTagName('A');
	  var aln = 0;
	  if (links != null) aln = links.length;
	  for (var i=0;i<aln;i++)
	  {
	  	links[i].href = SPAW_stripAbsoluteUrl(editor, links[i].href);
		if (!links[i].href)
		{
			links[i].removeAttribute('href',0);
		}
	  }

	  var imgs = this[editor+'_rEdit'].document.getElementsByTagName('IMG');
	  var aln = 0;
	  if (imgs != null) aln = imgs.length;
	  for (var i=0;i<aln;i++)
	  {
	  	imgs[i].src = SPAW_stripAbsoluteUrlFromImg(editor, imgs[i].src);
	  }
	  
      htmlvalue = this[editor+'_rEdit'].document.body.innerHTML;
    }
    else
    {
      // code
      htmlvalue = document.all[thefield].value;
    }
    return htmlvalue;
  }
  
  function SPAW_updateField(editor, field)
  {  
    var thefield = SPAW_getFieldByEditor(editor, field);
    
    var htmlvalue = SPAW_getHtmlValue(editor, thefield);

    if (document.all[thefield].value != htmlvalue)
    {
      // something changed
      document.all[thefield].value = htmlvalue;
    }
  }

  function SPAW_confirm(editor,block,message) {
    return showModalDialog('<?php echo $spaw_dir?>dialogs/confirm.php?lang=' + document.all['SPAW_'+editor+'_lang'].value + '&theme=' + document.all['SPAW_'+editor+'_theme'].value + '&block=' + block + '&message=' + message, null, 'dialogHeight:100px; dialogWidth:300px; resizable:no; status:no');  
  }
  
  // cleanup html
  function SPAW_cleanup_click(editor, sender)
  {
    if (SPAW_confirm(editor,'cleanup','confirm'))
    {
      window.frames[editor+'_rEdit'].focus();     
  
      var found = true;
      while (found)
      {
        found = false;
        var els = window.frames[editor+'_rEdit'].document.body.all;
        for (var i=0; i<els.length; i++)
        {
          // remove tags with urns set
          if (els[i].tagUrn != null && els[i].tagUrn != '')
          {
            els[i].removeNode(false);
            found = true;
          } 
          
          // remove font and span tags
          if (els[i].tagName != null && (els[i].tagName == "FONT" || els[i].tagName == "SPAN" || els[i].tagName == "DIV"))
          {
            els[i].removeNode(false);
            found = true;
          }
        }      
      }
      
      // remove styles
      var els = window.frames[editor+'_rEdit'].document.body.all;
      for (var i=0; i<els.length; i++)
      {
        // remove style and class attributes from all tags
        els[i].removeAttribute("className",0);
        els[i].removeAttribute("style",0);
        
      }
    }
    SPAW_update_toolbar(editor, true);    
  } // SPAW_cleanup_click
  
  // toggle borders worker function
  function SPAW_toggle_borders(editor, root, toggle)
  {
    // get toggle mode (on/off)
    var toggle_mode = toggle;
    if (toggle == null)
    {
      var tgl_borders = document.getElementById("SPAW_"+editor+"_borders");
      if (tgl_borders != null)
      {
        toggle_mode = tgl_borders.value;
      }
      else
      {
        toggle_mode = "on"
      }
    }
    
    var tbls = new Array();
    if (root.tagName == "TABLE")
    {
      tbls[0] = root;
    }
    else
    {
      // get all tables starting from root
      tbls = root.getElementsByTagName("TABLE");
    }
    
    var tbln = 0;
    if (tbls != null) tbln = tbls.length;
    for (var ti = 0; ti<tbln; ti++)
    {
      if ((tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
          (tbls[ti].border == 0 || tbls[ti].border == "0px") &&
          (toggle_mode == "on"))
      {
        tbls[ti].runtimeStyle.borderWidth = "1px";
        tbls[ti].runtimeStyle.borderStyle = "dashed";
        tbls[ti].runtimeStyle.borderColor = "#aaaaaa";
      } // no border
      else 
      {
        tbls[ti].runtimeStyle.borderWidth = "";
        tbls[ti].runtimeStyle.borderStyle = "";
        tbls[ti].runtimeStyle.borderColor = "";
      }
        
      var cls = tbls[ti].cells;
      // loop through cells
      for (var ci = 0; ci<cls.length; ci++)
      {
        if ((tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
            (tbls[ti].border == 0 || tbls[ti].border == "0px") && 
            (cls[ci].style.borderWidth == 0 || cls[ci].style.borderWidth == "0px") && 
            (toggle_mode == "on"))
        {
          cls[ci].runtimeStyle.borderWidth = "1px";
          cls[ci].runtimeStyle.borderStyle = "dashed";
          cls[ci].runtimeStyle.borderColor = "#aaaaaa";
        }
        else 
        {
          cls[ci].runtimeStyle.borderWidth = "";
          cls[ci].runtimeStyle.borderStyle = "";
          cls[ci].runtimeStyle.borderColor = "";
        }
      } // cells loop
    } // tables loop
  } // SPAW_toggle_borders
  
  // toggle borders click event 
  function SPAW_toggle_borders_click(editor, sender)
  {
    // get current toggle mode (on/off)
    var toggle_mode;

    var tgl_borders = document.getElementById("SPAW_"+editor+"_borders");
    if (tgl_borders != null)
    {
      toggle_mode = tgl_borders;

      // switch mode    
      if (toggle_mode.value == "on")
      {
        toggle_mode.value = "off";
      }
      else
      {
        toggle_mode.value = "on";
      }

      // call worker function
      SPAW_toggle_borders(editor,this[editor+'_rEdit'].document.body, toggle_mode.value);
    }
    SPAW_update_toolbar(editor, true);    
  } // SPAW_toggle_borders_click
  
  // returns base toolbar image name
  function SPAW_base_image_name(ctrl)
  {
    var imgname = ctrl.src.substring(0,ctrl.src.lastIndexOf("/"))+"/tb_"+ctrl.id.substr(ctrl.id.lastIndexOf("_tb_")+4, ctrl.id.length);
    return imgname;
  }

  // update toolbar if cursor moved or some event happened
  function SPAW_onkeyup(editor)
  {
    var eobj = window.frames[editor+'_rEdit']; // editor iframe
    if (eobj.event.ctrlKey || (eobj.event.keyCode >= 33 && eobj.event.keyCode<=40))
    {
      SPAW_update_toolbar(editor, false);
    }
  }
  
  var spaw_context_html = "";
  
  // update active toolbar state
  function SPAW_update_toolbar(editor, force)
  {
    //window.frames[editor+'_rEdit'].focus();     
    var pt = SPAW_getParentTag(editor);
    if (pt)
    {
      if (pt.outerHTML == spaw_context_html && !force)
      {
        return;
      }
      else
      {
        spaw_context_html = pt.outerHTML;
      }
    }
     
    // button sets
    table_row_items     =  [
                            "table_row_insert", 
                            "table_row_delete"
                          ];
    table_cell_items    = [
                            "table_cell_prop", 
                            "table_column_insert",
                            "table_column_delete",
                            "table_cell_merge_right",
                            "table_cell_merge_down",
                            "table_cell_split_horizontal",
                            "table_cell_split_vertical"
                          ];
    table_obj_items     = [
                            "table_prop"
                          ];
    img_obj_items       = [
                            "image_prop"
                          ];
                          
    standard_cmd_items  = [ // command,             control id
                            ["cut",                 "cut"],
                            ["copy",                "copy"],
                            ["paste",               "paste"],
                            ["undo",                "undo"],
                            ["redo",                "redo"],
                            ["bold",                "bold"],
                            ["italic",              "italic"],
                            ["underline",           "underline"],
                            ["justifyleft",         "left"],
                            ["justifycenter",       "center"],
                            ["justifyright",        "right"],
                            ["justifyfull",         "justify"],
                            ["indent",              "indent"],
                            ["outdent",             "unindent"],
                            ["forecolor",           "fore_color"],
                            ["backcolor",           "bg_color"],
                            ["insertorderedlist",   "ordered_list"],
                            ["insertunorderedlist", "bulleted_list"],
                            ["createlink",          "hyperlink"],
                            ["createlink",          "internal_link"],
                            ["createlink",          "image_popup"],
                            ["inserthorizontalrule","hr"],
							["subscript",			"subscript"],
							["superscript",			"superscript"]
                          ];                          

    togglable_items     = [ // command,             control id
                            ["bold",                "bold"],
                            ["italic",              "italic"],
                            ["underline",           "underline"],
                            ["justifyleft",         "left"],
                            ["justifycenter",       "center"],
                            ["justifyright",        "right"],
                            ["justifyfull",         "justify"],
							["subscript",			"subscript"],
							["superscript",			"superscript"]
                          ];        
    standard_dropdowns  = [ // command,             control id
                            ["fontname",            "font"],
                            ["fontsize",            "fontsize"],
                            ["formatblock",         "paragraph"]
                          ];
  
    // proceed only if active toolbar is enabled
    if (!spaw_active_toolbar) return;
    
    //window.frames[editor+'_rEdit'].focus();     

    // get object references
    var eobj = window.frames[editor+'_rEdit']; // editor iframe
    var edoc = eobj.document; // editor docutment
    
    // enable image insert
    SPAW_toggle_tbi(editor,"image_insert", true);
    // enable table insert
    SPAW_toggle_tbi(editor,"table_create", true);

    // toggle table buttons
    // get table
    var ct = SPAW_getTable(editor);
    if (ct)
    {
      // table found
      // enable table properties
      SPAW_toggle_tb_items(editor,table_obj_items, true);
      
      // get table row
      var cr = SPAW_getTR(editor);
      if (cr)
      {
        // enable table row features
        SPAW_toggle_tb_items(editor,table_row_items, true);
        
        // get table cell
        var cd = SPAW_getTD(editor);
        if (cd)
        {
          // enable cell features
          SPAW_toggle_tb_items(editor,table_cell_items, true);
        }
        else
        {
          // disable cell features
          SPAW_toggle_tb_items(editor,table_cell_items, false);
          // disable image insert
          SPAW_toggle_tbi(editor,"image_insert", false);
        }
      }
      else
      {
        // disable table row and cell features
        SPAW_toggle_tb_items(editor,table_cell_items, false);
        SPAW_toggle_tb_items(editor,table_row_items, false);
        // disable image insert
        SPAW_toggle_tbi(editor,"image_insert", false);
      }
    }
    else
    {
      // disable all available table related buttons
      SPAW_toggle_tb_items(editor,table_obj_items, false);
      SPAW_toggle_tb_items(editor,table_row_items, false);
      SPAW_toggle_tb_items(editor,table_cell_items, false);
    }
    // end table buttons
    
    // image buttons
    // get image
    var im = SPAW_getImg(editor);    
    if (im)
    {
      // enable image buttons
      SPAW_toggle_tb_items(editor,img_obj_items, true);
      // disable table insert
      SPAW_toggle_tbi(editor,"table_create", false);
    }
    else
    {
      // disable image buttons
      SPAW_toggle_tb_items(editor,img_obj_items, false);
    }
    // end image buttons
    
    // set state and enable/disable standard command buttons
    for (var i=0; i<togglable_items.length; i++)
    {
      SPAW_toggle_tbi_state(editor, togglable_items[i][1], edoc.queryCommandState(togglable_items[i][0]));
    }
    for (var i=0; i<standard_cmd_items.length; i++)
    {
      SPAW_toggle_tbi(editor, standard_cmd_items[i][1], edoc.queryCommandEnabled(standard_cmd_items[i][0]));
    }
    
    // set state of toggle borders button
    if (document.all["SPAW_"+editor+"_borders"].value == "on")
    {
      SPAW_toggle_tbi_state(editor, "toggle_borders", true);
    }
    else
    {
      SPAW_toggle_tbi_state(editor, "toggle_borders", false);
    }
    
    // dropdowns
    for (var i=0; i<standard_dropdowns.length; i++)
    {
      SPAW_toggle_tbi_dropdown(editor, standard_dropdowns[i][1], edoc.queryCommandValue(standard_dropdowns[i][0]));
    }
    // style dropdown
    var pt = SPAW_getParentTag(editor);
    SPAW_toggle_tbi_dropdown(editor, "style", pt.className);
  }
  
  // enable/disable toolbar item
  function SPAW_toggle_tb_items(editor, items, enable)
  {
    for (var i=0; i<items.length; i++)
    {
      SPAW_toggle_tbi(editor, items[i], enable);
    }
  }
  
  // enable/disable toolbar item
  function SPAW_toggle_tbi(editor, item, enable)
  {
    if (document.all["SPAW_"+editor+"_tb_"+item])
    {
      var ctrl = document.all["SPAW_"+editor+"_tb_"+item];
      if (enable)
      {
        if (ctrl)
        {
          ctrl.disabled = false;
          eval("SPAW_"+document.all["SPAW_"+editor+"_theme"].value+"_bt_out(ctrl);");
        }
      }
      else
      {
        if (ctrl)
        {
          ctrl.disabled = true;
          eval("SPAW_"+document.all["SPAW_"+editor+"_theme"].value+"_bt_off(ctrl);");
        }
      }
    }
  }
  
  // set state of the toolbar item
  function SPAW_toggle_tbi_state(editor, item, state)
  {
    if (document.all["SPAW_"+editor+"_tb_"+item])
    {
      var ctrl = document.all["SPAW_"+editor+"_tb_"+item];
      ctrl.setAttribute("spaw_state",state)
      eval("SPAW_"+document.all["SPAW_"+editor+"_theme"].value+"_bt_out(ctrl);");
    }
  }
  
  // set dropdown value
  function SPAW_toggle_tbi_dropdown(editor, item, value)
  {
    if (document.all["SPAW_"+editor+"_tb_"+item])
    {
      var ctrl = document.all["SPAW_"+editor+"_tb_"+item];
      ctrl.options[0].selected = true;
      for (var ii=0; ii<ctrl.options.length; ii++)
      {
        if (ctrl.options[ii].value == value)
        {
          ctrl.options[ii].selected = true;
        }
        else
        {
          ctrl.options[ii].selected = false;
        }
      }
    }
  }
  
  function SPAW_superscript_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('superscript', false, null);
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_subscript_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('subscript', false, null);
    SPAW_update_toolbar(editor, true);    
  }
  
