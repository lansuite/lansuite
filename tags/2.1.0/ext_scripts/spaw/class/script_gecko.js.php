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
    oForm = document.getElementById(thefield).form;
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
    var ed = document.getElementById(editor+'_rEdit');
    if (!SPAW_editor_registered(editor))
    {
      // register the editor 
      spaw_editors[spaw_editors.length] = editor;
    
      // add on submit handler
      SPAW_addOnSubmitHandler(editor);
   
      ed.contentDocument.designMode = 'on';
      var s_sheet = ed.contentDocument.createElement("link");
      s_sheet.setAttribute("rel","stylesheet");
      s_sheet.setAttribute("type","text/css");
      s_sheet.setAttribute("href",css_stylesheet);

      var head = ed.contentDocument.getElementsByTagName("head");
      head[0].appendChild(s_sheet);

      // set initial value
      var ta_field = document.getElementById(editor);
      var html = ta_field.value;
      if (html != null && html != "\n")
        ed.contentDocument.body.innerHTML = html;
        
     // hookup active toolbar related events
     ed.contentDocument.addEventListener('keyup', new Function("e","SPAW_onkeyup('"+editor+"',e);"), false);
     ed.contentDocument.addEventListener('mouseup', new Function("SPAW_update_toolbar('"+editor+"', true);"), false);
     
     // initialize toolbar
     spaw_context_html = "";
     SPAW_update_toolbar(editor, true);

     // workaround to missing cursor on first load        
     ed.contentDocument.designMode = 'on';
    }
  } 
   
  
  
  function SPAW_showColorPicker(editor,curcolor,callback) 
  {
    var wnd = window.open('<?php echo $spaw_dir?>dialogs/colorpicker.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value 
      + '&editor=' + editor + '&callback=' + callback, "color_picker", 
      'status=no,modal=yes,width=350,height=250'); 
    wnd.dialogArguments = curcolor;
    return wnd;
  }

  function SPAW_bold_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    ed.contentDocument.execCommand('bold', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_italic_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('italic', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_underline_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('underline', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_left_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('justifyleft', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_center_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('justifycenter', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_right_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('justifyright', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_justify_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('justifyfull', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_ordered_list_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('insertorderedlist', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_bulleted_list_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('insertunorderedlist', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_fore_color_click(editor, sender)
  {
    var wnd = SPAW_showColorPicker(editor,null,'SPAW_fore_color_click_callback'); 
  }
  
  function SPAW_fore_color_click_callback(editor, sender)
  {
    var fCol = sender.returnValue;
    if (fCol != null)
    {
      var ed = document.getElementById(editor+'_rEdit');
     	ed.contentDocument.execCommand('forecolor', false, fCol);
    }
    ed.contentWindow.focus();
  }

  function SPAW_bg_color_click(editor, sender)
  {
    var wnd = SPAW_showColorPicker(editor,null,'SPAW_bg_color_click_callback'); 
  }

  function SPAW_bg_color_click_callback(editor, sender)
  {
    var fCol = sender.returnValue;
    if (fCol != null)
    {
      var ed = document.getElementById(editor+'_rEdit');
     	ed.contentDocument.execCommand('hilitecolor', false, fCol);
    }
    ed.contentWindow.focus();
  }

  function SPAW_getA(editor)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var selection = ed.contentWindow.getSelection();
    var selectedRange;
    if (selection.rangeCount > 0) {
      selectedRange = selection.getRangeAt(0);
    }
    var aControl = selectedRange.startContainer;
    while ((aControl.tagName != 'A') && (aControl.tagName != 'BODY'))
    {
      aControl = aControl.parentNode;
    }
    if (aControl.tagName == 'A')
      return(aControl);
    else
      return(null);
  }

  function SPAW_hyperlink_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var a = SPAW_getA(editor);

    var aProps = {};
    // get anchors on the page
    aProps.anchors = new Array();
    var links = ed.contentDocument.getElementsByTagName('A');
    var aln = 0;
    if (links != null) aln = links.length;
    for (var i=0;i<aln;i++)
    {
      if (links[i].name != null && links[i].name != '')
        aProps.anchors[aProps.anchors.length] = links[i].name;
    }

    if (a)
    {
      aProps.href = a.attributes["href"]?a.attributes["href"].nodeValue:'';
      aProps.name = a.name;
      aProps.target = a.target;
      aProps.title = a.title;
    }
    var wnd = window.open('<?php echo $spaw_dir?>dialogs/a.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value
      + '&editor=' + editor + '&callback=SPAW_hyperlink_click_callback', "link_window", 
      'status=no,modal=yes,width=350,height=250'); 
    wnd.dialogArguments = aProps;
  }
  
 /***********************/
 function insertNodeAtSelection(win, insertNode)
  {
      // get current selection
      var sel = win.getSelection();

      // get the first range of the selection
      // (there's almost always only one range)
      var range = sel.getRangeAt(0);

      // deselect everything
      sel.removeAllRanges();

      // remove content of current selection from document
      range.deleteContents();

      // get location of current selection
      var container = range.startContainer;
      var pos = range.startOffset;

      // make a new range for the new selection
      range=document.createRange();

      if (container.nodeType==3 && insertNode.nodeType==3) {

        // if we insert text in a textnode, do optimized insertion
        container.insertData(pos, insertNode.nodeValue);

        // put cursor after inserted text
        range.setEnd(container, pos+insertNode.length);
        range.setStart(container, pos+insertNode.length);

      } else {


        var afterNode;
        if (container.nodeType==3) {

          // when inserting into a textnode
          // we create 2 new textnodes
          // and put the insertNode in between

          var textNode = container;
          container = textNode.parentNode;
          var text = textNode.nodeValue;

          // text before the split
          var textBefore = text.substr(0,pos);
          // text after the split
          var textAfter = text.substr(pos);

          var beforeNode = document.createTextNode(textBefore);
          var afterNode = document.createTextNode(textAfter);

          // insert the 3 new nodes before the old one
          container.insertBefore(afterNode, textNode);
          container.insertBefore(insertNode, afterNode);
          container.insertBefore(beforeNode, insertNode);

          // remove the old node
          container.removeChild(textNode);

        } else {

          // else simply insert the node
          afterNode = container.childNodes[pos];
          container.insertBefore(insertNode, afterNode);
        }

        range.setEnd(afterNode, 0);
        range.setStart(afterNode, 0);
      }

      sel.addRange(range);
      
      // remove all ranges
      win.getSelection().removeAllRanges();
  };
 /***********************/
  
  function SPAW_hyperlink_click_callback(editor, sender)
  {
    var naProps = sender.returnValue;

    var ed = document.getElementById(editor+'_rEdit');
    var a = SPAW_getA(editor);
    
    if (a)
    {
      // edit link
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
    else
    {
      // new link
      var a;
      a = document.createElement('A');
      if (naProps.name)
      {
        a.name = naProps.name;
      }
      else
      if (naProps.href)
        a.href = naProps.href;
      if (naProps.target && naProps.target!='_self')
        a.target = naProps.target;
      if (naProps.title)
        a.title = naProps.title;
      
      if (ed.contentWindow.getSelection().rangeCount>0 
      && ed.contentWindow.getSelection().getRangeAt(0).startOffset != ed.contentWindow.getSelection().getRangeAt(0).endOffset)
      {
        a.appendChild(ed.contentWindow.getSelection().getRangeAt(0).cloneContents());
      }
      else
      {
        a.innerHTML = (a.href && a.attributes["href"].nodeValue!='')?a.attributes["href"].nodeValue:a.name;
      }
      
      insertNodeAtSelection(ed.contentWindow, a);        
        
    }
  }

  function SPAW_internal_link_click(editor, sender)
  {
  }
  
  function SPAW_image_insert_click(editor, sender)
  {
    var wnd = window.open('<?php echo $spaw_dir?>dialogs/img_library.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value
      + '&editor=' + editor + '&callback=SPAW_image_insert_click_callback', "img_library", 
      'status=no,modal=yes,width=420,height=420'); 
  }
  
  function SPAW_image_insert_click_callback(editor, sender)
  {
    var imgSrc = sender.returnValue;
    if (imgSrc != null)
    {
      var ed = document.getElementById(editor+'_rEdit');
     	ed.contentDocument.execCommand('insertimage', false, imgSrc);
    }
    ed.contentWindow.focus();
  }
  
  function SPAW_image_prop_click(editor, sender)
  {
    var im = SPAW_getImg(editor); // current img
    
    if (im)
    {
      var iProps = {};
      if (im.attributes["src"])
        iProps.src = im.attributes["src"].nodeValue;
      iProps.alt = im.alt;
      iProps.width = (im.style.width)?im.style.width:im.width;
      iProps.height = (im.style.height)?im.style.height:im.height;
      iProps.border = im.border;
      iProps.align = im.align;
      if (im.hspace>-1) // (-1 when not set under gecko for some reason)
        iProps.hspace = im.attributes["hspace"].nodeValue;
      if (im.vspace>-1)
        iProps.vspace = im.attributes["vspace"].nodeValue;

      var wnd = window.open('<?php echo $spaw_dir?>dialogs/img.php?lang=' 
        + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
        + document.getElementById('SPAW_'+editor+'_theme').value
        + '&editor=' + editor + '&callback=SPAW_image_prop_click_callback', "img_prop", 
        'status=no,modal=yes,width=420,height=420'); 
      wnd.dialogArguments = iProps;
    }
  }

  function SPAW_image_prop_click_callback(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var niProps = sender.returnValue;
    var im = SPAW_getImg(editor); // current img
    
    if (im && niProps)
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
  
    ed.contentWindow.focus();
  }


  function SPAW_image_popup_click(editor, sender)
  {
    var wnd = window.open('<?php echo $spaw_dir?>dialogs/img_library.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value
      + '&editor=' + editor + '&callback=SPAW_image_popup_click_callback', "img_library", 
      'status=no,modal=yes,width=420,height=420'); 
  }
  
  function SPAW_image_popup_click_callback(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	var a = SPAW_getA(editor);
   	var imgSrc = sender.returnValue;

    if(imgSrc != null)    
    {
      if (a)
      {
        // edit hyperlink
        a.href="#";
        a.setAttribute("onclick","window.open('<?php echo $spaw_img_popup_url?>?img_url="+imgSrc+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;");
      }
      else
      {
        var a;
        a = document.createElement('A');
        a.href="#";
        a.setAttribute("onclick","window.open('<?php echo $spaw_img_popup_url?>?img_url="+imgSrc+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;");

        if (ed.contentWindow.getSelection().rangeCount>0 
        && ed.contentWindow.getSelection().getRangeAt(0).startOffset != ed.contentWindow.getSelection().getRangeAt(0).endOffset)
        {
          a.appendChild(ed.contentWindow.getSelection().getRangeAt(0).cloneContents());
        }
        else
        {
          a.innerHTML = (a.href && a.attributes["href"].nodeValue!='')?a.attributes["href"].nodeValue:a.name;
        }
        
        insertNodeAtSelection(ed.contentWindow, a);  
      }      
		}	
    ed.contentWindow.focus();
  }
  
  function SPAW_hr_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('inserthorizontalrule', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_copy_click(editor, sender)
  {
    // not implemented in gecko
  }

  function SPAW_paste_click(editor, sender)
  {
    // not implemented in gecko
  }
  
  function SPAW_cut_click(editor, sender)
  {
    // not implemented in gecko
  }

  function SPAW_delete_click(editor, sender)
  {
    // not implemented in gecko
  }

  function SPAW_indent_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('indent', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_unindent_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('outdent', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_undo_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('undo','',null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_redo_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
   	ed.contentDocument.execCommand('redo', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }
  
  
  function SPAW_getParentTag(editor)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var selection = ed.contentWindow.getSelection();
    var selectedRange;
    var aControl;
    if (selection && selection.rangeCount > 0) {
      selectedRange = selection.getRangeAt(0);
      aControl = selectedRange.startContainer;
      if (aControl.nodeType != 1)
        aControl = aControl.parentNode;
    }
    return aControl;
 
  }

  // trim functions  
  function SPAW_ltrim(txt)
  {
  }
  function SPAW_rtrim(txt)
  {
  }
  function SPAW_trim(txt)
  {
  }

  
  // is selected text a full tags inner html?
  function SPAW_isFoolTag(editor, el)
  {
  }
  
  function SPAW_style_change(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    
    var classname = sender.options[sender.selectedIndex].value;
    
    if (ed.contentWindow.getSelection().rangeCount>0)
    {
      var currentRange = ed.contentWindow.getSelection().getRangeAt(0);

      var parent = currentRange.commonAncestorContainer;
      if (parent.nodeType != 1)
        parent = currentRange.commonAncestorContainer.parentNode;
      
      if (parent && parent.tagName.toLowerCase() != "body" && parent.tagName.toLowerCase() != "html")
      {
        // set class on parent
        parent.className = classname;
      }
      else
      {
        // create new container
        var newSpan = ed.contentDocument.createElement("SPAN");
        newSpan.className = classname;
        newSpan.appendChild(currentRange.cloneContents());
        insertNodeAtSelection(ed.contentWindow, newSpan);
      }
    }

    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_font_change(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var fontname = sender.options[sender.selectedIndex].value;
    
    if (fontname == null || fontname == '')
    {
      ed.contentDocument.execCommand('RemoveFormat', false, null);
    }
    else   
    {
      ed.contentDocument.execCommand('fontname', false, fontname);
    }

    sender.selectedIndex = 0;

    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  
  }

  function SPAW_fontsize_change(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var fontsize = sender.options[sender.selectedIndex].value;

    ed.contentDocument.execCommand('fontsize', false, fontsize);

    sender.selectedIndex = 0;
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_paragraph_change(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var format = sender.options[sender.selectedIndex].value;

    ed.contentDocument.execCommand('formatBlock', false, format);

    sender.selectedIndex = 0;
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }
    
  function SPAW_table_create_click(editor, sender)
  {
      var wnd = window.open('<?php echo $spaw_dir?>dialogs/table.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value
      + '&editor=' + editor + '&callback=SPAW_table_create_click_callback', "table_prop", 
      'status=no,modal=yes,width=420,height=420'); 
  }
  
  function SPAW_table_create_click_callback(editor, sender)
  {
    var nt = sender.returnValue;

    var ed = document.getElementById(editor+'_rEdit');

      if (nt)
      {
        var newtable = document.createElement('TABLE');
        try 
        {
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
            newtable.style.backgroundImage = "url("+nt.background+");";
          if (nt.className)
            newtable.className = nt.className;
          
          // create rows
          for (var i=0;i<parseInt(nt.rows);i++)
          {
            var newrow = document.createElement('TR');
            for (var j=0; j<parseInt(nt.cols); j++)
            {
              var newcell = document.createElement('TD');
              newcell.innerHTML = "&nbsp;"; // otherwise it doesn't show cell borders
              newrow.appendChild(newcell);
            }
            newtable.appendChild(newrow);
          }
          
          insertNodeAtSelection(ed.contentWindow, newtable);
          
          SPAW_toggle_borders(editor, ed.contentDocument.body, null);
          SPAW_update_toolbar(editor, true);    
        }
        catch (excp)
        {
          alert('error');
        }
      }
  }
  
  function SPAW_table_prop_click(editor, sender)
  {
    var tTable = SPAW_getTable(editor);
    
    if (tTable)
    {
      var tProps = {};
      tProps.width = (tTable.style.width)?tTable.style.width:tTable.width;
      tProps.height = (tTable.style.height)?tTable.style.height:tTable.height;
      tProps.border = tTable.border;
      tProps.cellPadding = tTable.cellPadding;
      tProps.cellSpacing = tTable.cellSpacing;
      tProps.bgColor = tTable.bgColor;
      tProps.className = tTable.className;
      if (tTable.style.backgroundImage != undefined)
        tProps.background = tTable.style.backgroundImage.substr(4,tTable.style.backgroundImage.length-5);
      
      var wnd = window.open('<?php echo $spaw_dir?>dialogs/table.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value
      + '&editor=' + editor + '&callback=SPAW_table_prop_click_callback', "table_prop", 
      'status=no,modal=yes,width=420,height=420'); 
      wnd.dialogArguments = tProps;
    }
    
  }

  function SPAW_table_prop_click_callback(editor, sender)
  {
    var ntProps = sender.returnValue;

    var ed = document.getElementById(editor+'_rEdit');
    
    var tTable = SPAW_getTable(editor)

    if (tTable && ntProps)
    {
      // set new settings
      if (ntProps.width)
        tTable.width = ntProps.width;
      else
        tTable.removeAttribute('width',0);
      if (ntProps.height)
        tTable.height = ntProps.height
      else
        tTable.removeAttribute('height',0);
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
	  	  tTable.style.backgroundImage = "url("+ntProps.background+")";
      else
	  	  tTable.style.backgroundImage = "";
      if (ntProps.className)
        tTable.className = ntProps.className;
      else
        tTable.removeAttribute('className',0);

      SPAW_toggle_borders(editor, tTable, null);
    }

    SPAW_update_toolbar(editor, true);    
  }
  
  // edits table cell properties
  function SPAW_table_cell_prop_click(editor, sender)
  {
    var cd = SPAW_getTD(editor);
    if (cd)
    {
      var cProps = {};
      cProps.width = (cd.style.width)?cd.style.width:cd.width;
      cProps.height = (cd.style.height)?cd.style.height:cd.height;
      cProps.bgColor = cd.bgColor;
      if (cd.style.backgroundImage != undefined)
        cProps.background = cd.style.backgroundImage.substr(4,cd.style.backgroundImage.length-5);

      cProps.align = cd.align;
      cProps.vAlign = cd.vAlign;
      cProps.className = cd.className;
      cProps.noWrap = cd.noWrap;
      cProps.styleOptions = new Array();
      if (document.getElementById('SPAW_'+editor+'_tb_style') != null)
      {
        cProps.styleOptions = document.getElementById('SPAW_'+editor+'_tb_style').options;
      }
      
      var wnd = window.open('<?php echo $spaw_dir?>dialogs/td.php?lang=' 
      + document.getElementById('SPAW_'+editor+'_lang').value + '&theme=' 
      + document.getElementById('SPAW_'+editor+'_theme').value
      + '&editor=' + editor + '&callback=SPAW_table_cell_prop_click_callback', "table_prop", 
      'status=no,modal=yes,width=420,height=420'); 
      wnd.dialogArguments = cProps;
    }    
  }

  function SPAW_table_cell_prop_click_callback(editor, sender)
  {
    var ncProps = sender.returnValue;

    var ed = document.getElementById(editor+'_rEdit');
    
    var cd = SPAW_getTD(editor)

    if (cd && ncProps)  
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
      if (ncProps.height)
        cd.height = ncProps.height;
      else
        cd.removeAttribute('height',0);
      if (ncProps.bgColor)
        cd.bgColor = ncProps.bgColor;
      else
        cd.removeAttribute('bgcolor',0);
      if (ncProps.background)
        cd.style.backgroundImage = "url(" + ncProps.background + ")";
      else
        cd.style.backgroundImage = "";
      if (ncProps.className)
        cd.className = ncProps.className;
      else
        cd.removeAttribute('className',0);
      if (ncProps.noWrap)
        cd.noWrap = ncProps.noWrap;
      else
        cd.removeAttribute('nowrap',0);
    }
    SPAW_update_toolbar(editor, true);    
  }


  // returns current table cell  
  function SPAW_getTD(editor)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var selection = ed.contentWindow.getSelection();
    var selectedRange;
    var aControl;
    if (selection.rangeCount > 0) {
      selectedRange = selection.getRangeAt(0);
      aControl = selectedRange.startContainer;
      if (aControl.nodeType != 1)
        aControl = aControl.parentNode;
      while ((aControl.tagName.toLowerCase() != 'td')
        && (aControl.tagName.toLowerCase() != 'th') 
        && (aControl.tagName.toLowerCase() != 'table') 
        && (aControl.tagName.toLowerCase() != 'body'))
      {
        aControl = aControl.parentNode;
      }
    }
    if (aControl.tagName.toLowerCase() == 'td' || aControl.tagName.toLowerCase() == 'th')
      return(aControl);
    else
      return(null);
  }

  // returns current table row  
  function SPAW_getTR(editor)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var selection = ed.contentWindow.getSelection();
    var selectedRange;
    var aControl;
    if (selection.rangeCount > 0) {
      selectedRange = selection.getRangeAt(0);
      aControl = selectedRange.startContainer;
      if (aControl.nodeType != 1)
        aControl = aControl.parentNode;
      while ((aControl.tagName.toLowerCase() != 'tr')
        && (aControl.tagName.toLowerCase() != 'table') 
        && (aControl.tagName.toLowerCase() != 'body'))
      {
        aControl = aControl.parentNode;
      }
    }
    if (aControl.tagName.toLowerCase() == 'tr')
      return(aControl);
    else
      return(null);
  }
  
  // returns current table  
  function SPAW_getTable(editor)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var selection = ed.contentWindow.getSelection();
    var selectedRange;
    var aControl = null;
    if (selection && selection.rangeCount > 0) {
      selectedRange = selection.getRangeAt(0);
      aControl = selectedRange.startContainer;
      if (aControl.nodeType != 1)
        aControl = aControl.parentNode;
      while ((aControl.tagName.toLowerCase() != 'table') && (aControl.tagName.toLowerCase() != 'body'))
      {
        aControl = aControl.parentNode;
      }
      if (aControl.tagName.toLowerCase() != 'table')
        aControl = null;
    }
    return(aControl);
  }
  
  // returns selected image
  function SPAW_getImg(editor) 
  {
    var result = null;
    var ed = document.getElementById(editor+'_rEdit');
    var selection = ed.contentWindow.getSelection();
    var selectedRange;
    if (selection && selection.rangeCount > 0) {
      selectedRange = selection.getRangeAt(0);
      if (selectedRange.startContainer.nodeType == 1) // element node
      {
        var aControl = selectedRange.startContainer.childNodes[selectedRange.startOffset];
        if (aControl && aControl.tagName && aControl.tagName.toLowerCase() == 'img')
          result = aControl
      }
    }
    return result;
  }

  function SPAW_table_row_insert_click(editor, sender)
  {
  } // insertRow
  
  function SPAW_formCellMatrix(ct)
  {
  }
  
  function SPAW_table_column_insert_click(editor, sender)
  {
  } // insertColumn
  
  function SPAW_table_cell_merge_right_click(editor, sender)
  {
  } // mergeRight


  function SPAW_table_cell_merge_down_click(editor, sender)
  {
  } // mergeDown
  
  function SPAW_table_row_delete_click(editor, sender)
  {
  } // deleteRow
  
  function SPAW_table_column_delete_click(editor, sender)
  {
  } // deleteColumn
  
  // split cell horizontally
  function SPAW_table_cell_split_horizontal_click(editor, sender)
  {
  } // splitH
  
  function SPAW_table_cell_split_vertical_click(editor, sender)
  {
  } // splitV
  

  // switch to wysiwyg mode
  function SPAW_design_tab_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    iText = document.getElementById(editor).value;
    // mozilla bug? workaround
    ed.contentDocument.designMode = "off";
    
    ed.contentDocument.body.innerHTML = iText;
    
    document.getElementById('SPAW_'+editor+'_editor_mode').value = 'design';

    // turn off html mode toolbars
    document.getElementById('SPAW_'+editor+'_toolbar_top_html').style.display = 'none';
    document.getElementById('SPAW_'+editor+'_toolbar_left_html').style.display = 'none';
    document.getElementById('SPAW_'+editor+'_toolbar_right_html').style.display = 'none';
    document.getElementById('SPAW_'+editor+'_toolbar_bottom_html').style.display = 'none';

    // turn on design mode toolbars
    document.getElementById('SPAW_'+editor+'_toolbar_top_design').style.display = '';
    document.getElementById('SPAW_'+editor+'_toolbar_left_design').style.display = '';
    document.getElementById('SPAW_'+editor+'_toolbar_right_design').style.display = '';
    document.getElementById('SPAW_'+editor+'_toolbar_bottom_design').style.display = '';

    // switch editors    
    document.getElementById(editor).style.display = "none";
    ed.style.display = "";
    // workaround mozilla bug with losing design mode
    ed.contentDocument.designMode = "on";
    //document.getElementById(editor+"_rEdit").contentDocument.body.focus();
    
    // turn on invisible borders if needed
    //SPAW_toggle_borders(editor,ed.contentDocument.body, null);
    
    SPAW_update_toolbar(editor, true);    
  }
  
  // switch to html mode
  function SPAW_html_tab_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    var iHTML = SPAW_getHtmlValue(editor, null);

    document.getElementById(editor).value = iHTML;
    
    document.getElementById('SPAW_'+editor+'_editor_mode').value = 'html';

    // turn off design mode toolbars
    document.getElementById('SPAW_'+editor+'_toolbar_top_design').style.display = 'none';
    document.getElementById('SPAW_'+editor+'_toolbar_left_design').style.display = 'none';
    document.getElementById('SPAW_'+editor+'_toolbar_right_design').style.display = 'none';
    document.getElementById('SPAW_'+editor+'_toolbar_bottom_design').style.display = 'none';

    // turn on html mode toolbars
    document.getElementById('SPAW_'+editor+'_toolbar_top_html').style.display = '';
    document.getElementById('SPAW_'+editor+'_toolbar_left_html').style.display = '';
    document.getElementById('SPAW_'+editor+'_toolbar_right_html').style.display = '';
    document.getElementById('SPAW_'+editor+'_toolbar_bottom_html').style.display = '';

    // switch editors    
    document.getElementById(editor+"_rEdit").style.display = "none";
    document.getElementById(editor).style.display = "";
    //document.getElementById(editor).focus();

    //SPAW_update_toolbar(editor, true);    
  }
  
  function SPAW_getFieldByEditor(editor, field)
  {
    var thefield;
    // get field by editor id
    if (field == null || field == "")
    {
      thefield = document.getElementById(editor).id;
    }
    else
    {
      thefield=field;
    }
    return thefield;
  }
  
  function SPAW_stripAbsoluteUrl(url)
  {
  }

  function SPAW_stripAbsoluteUrlFromImg(url)
  {
  }
  
  function SPAW_getHtmlValue(editor, thefield)
  {
    // temporary simplified
    return document.getElementById(editor+"_rEdit").contentDocument.body.innerHTML;
  }
  
  function SPAW_updateField(editor, field)
  {  
    var thefield = SPAW_getFieldByEditor(editor, field);
    
    var htmlvalue = SPAW_getHtmlValue(editor, thefield);

    if (document.getElementById(thefield).value != htmlvalue)
    {
      // something changed
      document.getElementById(thefield).value = htmlvalue;
    }
  }

  function SPAW_confirm(editor,block,message) 
  {
  }
  
  // cleanup html
  function SPAW_cleanup_click(editor, sender)
  {
  } // SPAW_cleanup_click
  
  // toggle borders worker function
  function SPAW_toggle_borders(editor, root, toggle)
  {
  } // SPAW_toggle_borders
  
  // toggle borders click event 
  function SPAW_toggle_borders_click(editor, sender)
  {
  } // SPAW_toggle_borders_click
  
  // returns base toolbar image name
  function SPAW_base_image_name(ctrl)
  {
    var imgname = ctrl.src.substring(0,ctrl.src.lastIndexOf("/"))+"/tb_"+ctrl.id.substr(ctrl.id.lastIndexOf("_tb_")+4, ctrl.id.length);
    return imgname;
  }

  // update toolbar if cursor moved or some event happened
  function SPAW_onkeyup(editor, e)
  {
    if (e.ctrlKey || (e.keyCode >= 33 && e.keyCode<=40))
    {
      SPAW_update_toolbar(editor, false);
    }
  }
  
  var spaw_context_html = null;
  
    // update active toolbar state
  function SPAW_update_toolbar(editor, force)
  {
    document.getElementById(editor+'_rEdit').contentWindow.focus();
    var pt = SPAW_getParentTag(editor);
    if (pt)
    {
      if (pt.outerHTML == pt && !force)
      {
        return;
      }
      else
      {
        spaw_context_html = pt;
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
    var eobj = document.getElementById(editor+'_rEdit'); // editor iframe
    var edoc = eobj.contentDocument; // editor docutment
    
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
      try
      {
        SPAW_toggle_tbi_state(editor, togglable_items[i][1], edoc.queryCommandState(togglable_items[i][0]));
      }
      catch (excp) {}
    }
    for (var i=0; i<standard_cmd_items.length; i++)
    {
      try
      {
        SPAW_toggle_tbi(editor, standard_cmd_items[i][1], edoc.queryCommandEnabled(standard_cmd_items[i][0]));
      }
      catch (excp) {}
    }
    
    // set state of toggle borders button
    if (document.getElementById("SPAW_"+editor+"_borders").value == "on")
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
      try
      {
        SPAW_toggle_tbi_dropdown(editor, standard_dropdowns[i][1], edoc.queryCommandValue(standard_dropdowns[i][0]));
      }
      catch (excp) {}
    }
    // style dropdown
    var pt = SPAW_getParentTag(editor);
    if (pt)
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
    if (document.getElementById("SPAW_"+editor+"_tb_"+item))
    {
      var ctrl = document.getElementById("SPAW_"+editor+"_tb_"+item);
      if (enable)
      {
        if (ctrl)
        {
          ctrl.disabled = false;
          eval("SPAW_"+document.getElementById("SPAW_"+editor+"_theme").value+"_bt_out(ctrl);");
        }
      }
      else
      {
        if (ctrl)
        {
          ctrl.disabled = true;
          eval("SPAW_"+document.getElementById("SPAW_"+editor+"_theme").value+"_bt_off(ctrl);");
        }
      }
    }
  }
  
  // set state of the toolbar item
  function SPAW_toggle_tbi_state(editor, item, state)
  {
    if (document.getElementById("SPAW_"+editor+"_tb_"+item))
    {
      var ctrl = document.getElementById("SPAW_"+editor+"_tb_"+item);
      ctrl.setAttribute("spaw_state",state)
      eval("SPAW_"+document.getElementById("SPAW_"+editor+"_theme").value+"_bt_out(ctrl);");
    }
  }
  
  // set dropdown value
  function SPAW_toggle_tbi_dropdown(editor, item, value)
  {
    if (document.getElementById("SPAW_"+editor+"_tb_"+item))
    {
      var ctrl = document.getElementById("SPAW_"+editor+"_tb_"+item);
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
    var ed = document.getElementById(editor+'_rEdit');
    ed.contentDocument.execCommand('superscript', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }

  function SPAW_subscript_click(editor, sender)
  {
    var ed = document.getElementById(editor+'_rEdit');
    ed.contentDocument.execCommand('subscript', false, null);
    ed.contentWindow.focus();
    SPAW_update_toolbar(editor, true);    
  }
  
