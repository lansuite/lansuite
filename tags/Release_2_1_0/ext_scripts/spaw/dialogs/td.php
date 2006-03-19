<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Table cell properties dialog
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-04-01
// ================================================

// include wysiwyg config
include '../config/spaw_control.config.php';
include $spaw_root.'class/util.class.php';
include $spaw_root.'class/lang.class.php';

$theme = empty($HTTP_GET_VARS['theme'])?$spaw_default_theme:$HTTP_GET_VARS['theme'];
$theme_path = $spaw_dir.'lib/themes/'.$theme.'/';

$l = new SPAW_Lang($HTTP_GET_VARS['lang']);
$l->setBlock('table_cell_prop');

$request_uri = urldecode(empty($HTTP_POST_VARS['request_uri'])?(empty($HTTP_GET_VARS['request_uri'])?'':$HTTP_GET_VARS['request_uri']):$HTTP_POST_VARS['request_uri']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
  <title><?php echo $l->m('title')?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l->getCharset()?>">
  <link rel="stylesheet" type="text/css" href="<?php echo $theme_path.'css/'?>dialog.css">
  <?php if (SPAW_Util::getBrowser() == 'Gecko') { ?>
  <script language="javascript" src="utils.gecko.js"></script>
  <?php }else{ ?>
  <script language="javascript" src="utils.js"></script>
  <?php } ?>
  <script language="javascript">
  <!--  
  function showColorPicker(curcolor) {

  <?php if (SPAW_Util::getBrowser() == 'Gecko') { ?>

    var wnd = window.open('<?php echo $spaw_dir?>dialogs/colorpicker.php?lang=<?php echo $_GET["lang"]?>&theme=<?php echo $_GET["theme"]?>&editor=<?php echo $_GET["editor"]?>&callback=showColorPicker_callback', 
      "color_picker", 
      'status=no,modal=yes,width=350,height=250'); 
    wnd.dialogArguments = curcolor;

  <?php }else{ ?>

    var newcol = showModalDialog('colorpicker.php?theme=<?php echo $theme?>&lang=<?php echo $l->lang?>', curcolor, 
      'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
    try {
      td_prop.cbgcolor.value = newcol;
      td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
    }
    catch (excp) {}

  <?php } ?>
  }
  
  function showColorPicker_callback(editor, sender)
  {
    var bCol = sender.returnValue;
    try
    {
      document.getElementById('cbgcolor').value = bCol;
      document.getElementById('color_sample').style.backgroundColor = document.getElementById('cbgcolor').value;
    }
    catch (excp) {}
  }

  function showImgPicker()
  {
  <?php if (SPAW_Util::getBrowser() == 'Gecko') { ?>

    var wnd = window.open('<?php echo $spaw_dir?>dialogs/img_library.php?lang=<?php echo $_GET["lang"]?>&theme=<?php echo $_GET["theme"]?>&editor=<?php echo $_GET["editor"]?>&callback=showImgPicker_callback',
      "img_library", 
      'status=no,modal=yes,width=420,height=420'); 

  <?php }else{ ?>

    var imgSrc = showModalDialog('<?php echo $spaw_dir?>dialogs/img_library.php?theme=<?php echo $theme?>&lang=<?php echo $l->lang?>&request_uri=<?php echo $request_uri?>', '', 
      'dialogHeight:420px; dialogWidth:420px; resizable:no; status:no');
    
    if(imgSrc != null)
    {
      td_prop.cbackground.value = imgSrc;
    }

  <?php } ?>
  }
  
  function showImgPicker_callback(editor, sender)
  {
    var imgSrc = sender.returnValue;
    if(imgSrc != null)
    {
      document.getElementById('cbackground').value = imgSrc;
    }
  }

 
  function Init() {
    var cProps = window.dialogArguments;
    if (cProps)
    {
      // set attribute values
      document.getElementById('cbgcolor').value = cProps.bgColor;
      document.getElementById('color_sample').style.backgroundColor = document.getElementById('cbgcolor').value;
      document.getElementById('cbackground').value = cProps.background;
      if (cProps.width) {
        if (!isNaN(cProps.width) || (cProps.width.substr(cProps.width.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(cProps.width))
            document.getElementById('cwidth').value = cProps.width;
          else
            document.getElementById('cwidth').value = cProps.width.substr(0,cProps.width.length-2);
          document.getElementById('cwunits').options[0].selected = false;
          document.getElementById('cwunits').options[1].selected = true;
        }
        else
        {
          // percents
          document.getElementById('cwidth').value = cProps.width.substr(0,cProps.width.length-1);
          document.getElementById('cwunits').options[0].selected = true;
          document.getElementById('cwunits').options[1].selected = false;
        }
      }
      if (cProps.width) {
        if (!isNaN(cProps.height) || (cProps.height.substr(cProps.height.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(cProps.height))
            document.getElementById('cheight').value = cProps.height;
          else
            document.getElementById('cheight').value = cProps.height.substr(0,cProps.height.length-2);
          document.getElementById('chunits').options[0].selected = false;
          document.getElementById('chunits').options[1].selected = true;
        }
        else
        {
          // percents
          document.getElementById('cheight').value = cProps.height.substr(0,cProps.height.length-1);
          document.getElementById('chunits').options[0].selected = true;
          document.getElementById('chunits').options[1].selected = false;
        }
      }
      
      setHAlign(cProps.align);
      setVAlign(cProps.vAlign);
      
      if (cProps.noWrap)
        document.getElementById('cnowrap').checked = true;
      
      
	  /* spec styles for td will be used
      if (cProps.styleOptions) {
        for (i=1; i<cProps.styleOptions.length; i++)
        {
          var oOption = document.createElement("OPTION");
          td_prop.ccssclass.add(oOption);
          oOption.innerText = cProps.styleOptions[i].innerText;
          oOption.value = cProps.styleOptions[i].value;
  
          if (cProps.className) {
            td_prop.ccssclass.value = cProps.className;
          }
        }
      }
	  */

      if (cProps.className) {
        document.getElementById('ccssclass').value = cProps.className;
        css_class_changed();
      }
    }
    resizeDialogToContent();
  }
  
  function validateParams()
  {
    // check width and height
    if (isNaN(parseInt(document.getElementById('cwidth').value)) && document.getElementById('cwidth').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_width_nan')?>');
      document.getElementById('cwidth').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('cheight').value)) && document.getElementById('cheight').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_height_nan')?>');
      document.getElementById('cheight').focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var cprops = {};
      cprops.className = (document.getElementById('ccssclass').value != 'default')?document.getElementById('ccssclass').value:'';
      if (!document.getElementById('cwidth').disabled)
      {
        cprops.align = (document.getElementById('chalign').value)?(document.getElementById('chalign').value):'';
        cprops.vAlign = (document.getElementById('cvalign').value)?(document.getElementById('cvalign').value):'';
        cprops.width = (document.getElementById('cwidth').value)?(document.getElementById('cwidth').value + document.getElementById('cwunits').value):'';
        cprops.height = (document.getElementById('cheight').value)?(document.getElementById('cheight').value + document.getElementById('chunits').value):'';
        cprops.bgColor = document.getElementById('cbgcolor').value;
        cprops.noWrap = (document.getElementById('cnowrap').checked)?true:false;
        cprops.background = document.getElementById('cbackground').value;
      }
      window.returnValue = cprops;
      window.close();
      <?php
      if (!empty($_GET['callback']))
        echo "opener.".$_GET['callback']."('".$_GET['editor']."',this);\n";
      ?>
    }
  }

  function cancelClick() {
    window.close();
  }
  
  function setSample()
  {
    try {
      document.getElementById('color_sample').style.backgroundColor = document.getElementById('cbgcolor').value;
    }
    catch (excp) {}
  }
  
  function setHAlign(alignment)
  {
    switch (alignment) {
      case "left":
        document.getElementById('ha_left').className = "align_on";
        document.getElementById('ha_center').className = "align_off";
        document.getElementById('ha_right').className = "align_off";
        break;
      case "center":
        document.getElementById('ha_left').className = "align_off";
        document.getElementById('ha_center').className = "align_on";
        document.getElementById('ha_right').className = "align_off";
        break;
      case "right":
        document.getElementById('ha_left').className = "align_off";
        document.getElementById('ha_center').className = "align_off";
        document.getElementById('ha_right').className = "align_on";
        break;
    }
    document.getElementById('chalign').value = alignment;
  }

  function setVAlign(alignment)
  {
    switch (alignment) {
      case "middle":
        document.getElementById('ha_middle').className = "align_on";
        document.getElementById('ha_baseline').className = "align_off";
        document.getElementById('ha_bottom').className = "align_off";
        document.getElementById('ha_top').className = "align_off";
        break;
      case "baseline":
        document.getElementById('ha_middle').className = "align_off";
        document.getElementById('ha_baseline').className = "align_on";
        document.getElementById('ha_bottom').className = "align_off";
        document.getElementById('ha_top').className = "align_off";
        break;
      case "bottom":
        document.getElementById('ha_middle').className = "align_off";
        document.getElementById('ha_baseline').className = "align_off";
        document.getElementById('ha_bottom').className = "align_on";
        document.getElementById('ha_top').className = "align_off";
        break;
      case "top":
        document.getElementById('ha_middle').className = "align_off";
        document.getElementById('ha_baseline').className = "align_off";
        document.getElementById('ha_bottom').className = "align_off";
        document.getElementById('ha_top').className = "align_on";
        break;
    }
    document.getElementById('cvalign').value = alignment;
  }
  
  function css_class_changed()
  {
  	if (<?php echo (isset($spaw_disable_style_controls) && $spaw_disable_style_controls)?'true':'false'?>)
    {
	  	// disable/enable non-css class controls
      if (document.getElementById('ccssclass').value && document.getElementById('ccssclass').value!='default')
      {
        // disable all controls
        document.getElementById('cwidth').disabled = true;
        document.getElementById('cwunits').disabled = true;
        document.getElementById('cheight').disabled = true;
        document.getElementById('chunits').disabled = true;
        document.getElementById('cnowrap').disabled = true;
        document.getElementById('cbgcolor').disabled = true;
        document.getElementById('ha_left').src = '<?php echo $theme_path.'img/'?>tb_left_off.gif';
        document.getElementById('ha_left').disabled = true;
        document.getElementById('ha_center').src = '<?php echo $theme_path.'img/'?>tb_center_off.gif';
        document.getElementById('ha_center').disabled = true;
        document.getElementById('ha_right').src = '<?php echo $theme_path.'img/'?>tb_right_off.gif';
        document.getElementById('ha_right').disabled = true;
        document.getElementById('ha_top').src = '<?php echo $theme_path.'img/'?>tb_top_off.gif';
        document.getElementById('ha_top').disabled = true;
        document.getElementById('ha_middle').src = '<?php echo $theme_path.'img/'?>tb_middle_off.gif';
        document.getElementById('ha_middle').disabled = true;
        document.getElementById('ha_bottom').src = '<?php echo $theme_path.'img/'?>tb_bottom_off.gif';
        document.getElementById('ha_bottom').disabled = true;
        document.getElementById('ha_baseline').src = '<?php echo $theme_path.'img/'?>tb_baseline_off.gif';
        document.getElementById('ha_baseline').disabled = true;
        document.getElementById('ccolorpicker').src = '<?php echo $theme_path.'img/'?>tb_colorpicker_off.gif';
        document.getElementById('ccolorpicker').disabled = true;
        document.getElementById('cbackground').disabled = true;
        document.getElementById('cimg_picker').src = '<?php echo $theme_path.'img/'?>tb_image_insert_off.gif';
        document.getElementById('cimg_picker').disabled = true;
      }
      else
      {
        // enable all controls
        document.getElementById('cwidth').disabled = false;
        document.getElementById('cwunits').disabled = false;
        document.getElementById('cheight').disabled = false;
        document.getElementById('chunits').disabled = false;
        document.getElementById('cnowrap').disabled = false;
        document.getElementById('cbgcolor').disabled = false;
        document.getElementById('ha_left').src = '<?php echo $theme_path.'img/'?>tb_left.gif';
        document.getElementById('ha_left').disabled = false;
        document.getElementById('ha_center').src = '<?php echo $theme_path.'img/'?>tb_center.gif';
        document.getElementById('ha_center').disabled = false;
        document.getElementById('ha_right').src = '<?php echo $theme_path.'img/'?>tb_right.gif';
        document.getElementById('ha_right').disabled = false;
        document.getElementById('ha_top').src = '<?php echo $theme_path.'img/'?>tb_top.gif';
        document.getElementById('ha_top').disabled = false;
        document.getElementById('ha_middle').src = '<?php echo $theme_path.'img/'?>tb_middle.gif';
        document.getElementById('ha_middle').disabled = false;
        document.getElementById('ha_bottom').src = '<?php echo $theme_path.'img/'?>tb_bottom.gif';
        document.getElementById('ha_bottom').disabled = false;
        document.getElementById('ha_baseline').src = '<?php echo $theme_path.'img/'?>tb_baseline.gif';
        document.getElementById('ha_baseline').disabled = false;
        document.getElementById('ccolorpicker').src = '<?php echo $theme_path.'img/'?>tb_colorpicker.gif';
        document.getElementById('ccolorpicker').disabled = false;
        document.getElementById('cbackground').disabled = false;
        document.getElementById('cimg_picker').src = '<?php echo $theme_path.'img/'?>tb_image_insert.gif';
        document.getElementById('cimg_picker').disabled = false;
      }
    }
  }
  //-->
  </script>
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">
<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="td_prop">
<tr>
  <td nowrap><?php echo $l->m('css_class')?>:</td>
  <td nowrap colspan="3">
    <select id="ccssclass" name="ccssclass" id="ccssclass" size="1" class="input" onchange="css_class_changed();">
	<?php
	foreach($spaw_dropdown_data["td_style"] as $key => $text)
	{
		echo '<option value="'.$key.'">'.$text.'</option>'."\n";
	}
	?>
    </select>
  </td>
</tr>
<tr>
  <td colspan="2"><?php echo $l->m('horizontal_align')?>:</td>
  <td colspan="2" align="right"><input type="hidden" name="chalign" id="chalign">
  <img id="ha_left" src="<?php echo $theme_path.'img/'?>tb_left.gif" class="align_off" onClick="setHAlign('left');" alt="<?php echo $l->m('left')?>">
  <img id="ha_center" src="<?php echo $theme_path.'img/'?>tb_center.gif" class="align_off" onClick="setHAlign('center');" alt="<?php echo $l->m('center')?>">
  <img id="ha_right" src="<?php echo $theme_path.'img/'?>tb_right.gif" class="align_off" onClick="setHAlign('right');" alt="<?php echo $l->m('right')?>">
  </td>
</tr>
<tr>
  <td colspan="2"><?php echo $l->m('vertical_align')?>:</td>
  <td colspan="2" align="right"><input type="hidden" name="cvalign" id="cvalign">
  <img id="ha_top" src="<?php echo $theme_path.'img/'?>tb_top.gif" class="align_off" onClick="setVAlign('top');" alt="<?php echo $l->m('top')?>">
  <img id="ha_middle" src="<?php echo $theme_path.'img/'?>tb_middle.gif" class="align_off" onClick="setVAlign('middle');" alt="<?php echo $l->m('middle')?>">
  <img id="ha_bottom" src="<?php echo $theme_path.'img/'?>tb_bottom.gif" class="align_off" onClick="setVAlign('bottom');" alt="<?php echo $l->m('bottom')?>">
  <img id="ha_baseline" src="<?php echo $theme_path.'img/'?>tb_baseline.gif" class="align_off" onClick="setVAlign('baseline');" alt="<?php echo $l->m('baseline')?>">
  </td>
</tr>
<tr>
  <td><?php echo $l->m('width')?>:</td>
  <td nowrap>
    <input type="text" name="cwidth" id="cwidth" size="3" maxlength="3" class="input_small">
    <select size="1" name="cwunits" id="cwunits" class="input">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
  <td><?php echo $l->m('height')?>:</td>
  <td nowrap>
    <input type="text" name="cheight" id="cheight" size="3" maxlength="3" class="input_small">
    <select size="1" name="chunits" id="chunits" class="input">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
</tr>
<tr>
  <td nowrap><?php echo $l->m('no_wrap')?>:</td>
  <td nowrap>
    <input type="checkbox" name="cnowrap" id="cnowrap">
  </td>
  <td colspan="2">&nbsp;</td>
</tr>
<tr>
  <td colspan="4"><?php echo $l->m('bg_color')?>: <img src="spacer.gif" id="color_sample" border="1" width="30" height="18" align="absbottom">&nbsp;<input type="text" name="cbgcolor" id="cbgcolor" size="7" maxlength="7" class="input_color" onKeyUp="setSample()">&nbsp;
  <img id="ccolorpicker" src="<?php echo $theme_path.'img/'?>tb_colorpicker.gif" border="0" onClick="showColorPicker(cbgcolor.value)" align="absbottom">
  </td>
</tr>
<tr>
  <td colspan="4">
	<?php echo $l->m('background')?>: <input type="text" name="cbackground" id="cbackground" size="20" class="input" >&nbsp;<img id="cimg_picker" src="<?php echo $theme_path.'img/'?>tb_image_insert.gif" border="0" onClick="showImgPicker();" align="absbottom">	
  </td>
</tr>
<tr>
<td colspan="4" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap>
<input type="button" value="<?php echo $l->m('ok')?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo $l->m('cancel')?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>

</body>
</html>
