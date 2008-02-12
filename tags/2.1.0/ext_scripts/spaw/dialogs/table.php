<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Table properties dialog
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
$l->setBlock('table_prop');

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
      table_prop.tbgcolor.value = newcol;
      table_prop.color_sample.style.backgroundColor = table_prop.tbgcolor.value;
    }
    catch (excp) {}

  <?php } ?>
  }
  
  function showColorPicker_callback(editor, sender)
  {
    var bCol = sender.returnValue;
    try
    {
      document.getElementById('tbgcolor').value = bCol;
      document.getElementById('color_sample').style.backgroundColor = document.getElementById('tbgcolor').value;
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
      table_prop.tbackground.value = imgSrc;
    }

  <?php } ?>
  }
  
  function showImgPicker_callback(editor, sender)
  {
    var imgSrc = sender.returnValue;
    if(imgSrc != null)
    {
      document.getElementById('tbackground').value = imgSrc;
    }
  }
  
  function Init() {
    var tProps = window.dialogArguments;
    if (tProps)
    {
      // set attribute values
      document.getElementById('trows').value = '3';
      document.getElementById('trows').disabled = true;
      document.getElementById('tcols').value = '3';
      document.getElementById('tcols').disabled = true;

      document.getElementById('tborder').value = tProps.border;
      document.getElementById('tcpad').value = tProps.cellPadding;
      document.getElementById('tcspc').value = tProps.cellSpacing;
      document.getElementById('tbgcolor').value = tProps.bgColor;
      document.getElementById('color_sample').style.backgroundColor = document.getElementById('tbgcolor').value;
      document.getElementById('tbackground').value = tProps.background;
      if (tProps.width) {
        if (!isNaN(tProps.width) || (tProps.width.substr(tProps.width.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(tProps.width))
            document.getElementById('twidth').value = tProps.width;
          else
            document.getElementById('twidth').value = tProps.width.substr(0,tProps.width.length-2);
          document.getElementById('twunits').options[0].selected = false;
          document.getElementById('twunits').options[1].selected = true;
        }
        else
        {
          // percents
          document.getElementById('twidth').value = tProps.width.substr(0,tProps.width.length-1);
          document.getElementById('twunits').options[0].selected = true;
          document.getElementById('twunits').options[1].selected = false;
        }
      }
      if (tProps.height) {
        if (!isNaN(tProps.height) || (tProps.height.substr(tProps.height.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(tProps.height))
            document.getElementById('theight').value = tProps.height;
          else
            document.getElementById('theight').value = tProps.height.substr(0,tProps.height.length-2);
          document.getElementById('thunits').options[0].selected = false;
          document.getElementById('thunits').options[1].selected = true;
        }
        else
        {
          // percents
          document.getElementById('theight').value = tProps.height.substr(0,tProps.height.length-1);
          document.getElementById('thunits').options[0].selected = true;
          document.getElementById('thunits').options[1].selected = false;
        }
      }
      if (tProps.className) {
        document.getElementById('tcssclass').value = tProps.className;
        css_class_changed();
      }
    }
    else
    {
      // set default values
      document.getElementById('trows').value = '3';
      document.getElementById('tcols').value = '3';
      document.getElementById('tborder').value = '1';
    }
    resizeDialogToContent();
  }
  
  function validateParams()
  {
    // check whether rows and cols are integers
    if (isNaN(parseInt(document.getElementById('trows').value)))
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_rows_nan')?>');
      document.getElementById('trows').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('tcols').value)))
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_columns_nan')?>');
      document.getElementById('tcols').focus();
      return false;
    }
    // check width and height
    if (isNaN(parseInt(document.getElementById('twidth').value)) && document.getElementById('twidth').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_width_nan')?>');
      document.getElementById('twidth').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('theight').value)) && document.getElementById('theight').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_height_nan')?>');
      document.getElementById('theight').focus();
      return false;
    }
    // check border, padding and spacing
    if (isNaN(parseInt(document.getElementById('tborder').value)) && document.getElementById('tborder').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_border_nan')?>');
      document.getElementById('tborder').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('tcpad').value)) && document.getElementById('tcpad').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_cellpadding_nan')?>');
      document.getElementById('tcpad').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('tcspc').value)) && document.getElementById('tcspc').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_cellspacing_nan')?>');
      document.getElementById('tcspc').focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var newtable = {};
      newtable.className = (document.getElementById('tcssclass').value != 'default')?document.getElementById('tcssclass').value:null;
      newtable.cols = document.getElementById('tcols').value;
      newtable.rows = document.getElementById('trows').value;
      if (!document.getElementById('twidth').disabled)
      {
          newtable.width = (document.getElementById('twidth').value)?(document.getElementById('twidth').value + document.getElementById('twunits').value):'';
          newtable.height = (document.getElementById('theight').value)?(document.getElementById('theight').value + document.getElementById('thunits').value):'';
          newtable.border = document.getElementById('tborder').value;
          newtable.cellPadding = document.getElementById('tcpad').value;
          newtable.cellSpacing = document.getElementById('tcspc').value;
          newtable.bgColor = document.getElementById('tbgcolor').value;
          newtable.background = document.getElementById('tbackground').value;
      }
      window.returnValue = newtable;
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
      document.getElementById('color_sample').style.backgroundColor = document.getElementById('tbgcolor').value;
    }
    catch (excp) {}
  }
  
  function css_class_changed()
  {
    if (<?php echo (isset($spaw_disable_style_controls) && $spaw_disable_style_controls)?'true':'false'?>)
    {
      // disable/enable non-css class controls
    	if (document.getElementById('tcssclass').value && document.getElementById('tcssclass').value!='default')
    	{
    		// disable all controls
    		document.getElementById('twidth').disabled = true;
    		document.getElementById('twunits').disabled = true;
    		document.getElementById('theight').disabled = true;
    		document.getElementById('thunits').disabled = true;
    		document.getElementById('tborder').disabled = true;
    		document.getElementById('tcpad').disabled = true;
    		document.getElementById('tcspc').disabled = true;
    		document.getElementById('tbgcolor').disabled = true;
    		document.getElementById('tcolorpicker').src = '<?php echo $theme_path.'img/'?>tb_colorpicker_off.gif';
    		document.getElementById('tcolorpicker').disabled = true;
    		document.getElementById('tbackground').disabled = true;
    		document.getElementById('timg_picker').src = '<?php echo $theme_path.'img/'?>tb_image_insert_off.gif';
    		document.getElementById('timg_picker').disabled = true;
    	}
    	else
    	{
    		// enable all controls
    		document.getElementById('twidth').disabled = false;
    		document.getElementById('twunits').disabled = false;
    		document.getElementById('theight').disabled = false;
    		document.getElementById('thunits').disabled = false;
    		document.getElementById('tborder').disabled = false;
    		document.getElementById('tcpad').disabled = false;
    		document.getElementById('tcspc').disabled = false;
    		document.getElementById('tbgcolor').disabled = false;
    		document.getElementById('tcolorpicker').src = '<?php echo $theme_path.'img/'?>tb_colorpicker.gif';
    		document.getElementById('tcolorpicker').disabled = false;
    		document.getElementById('tbackground').disabled = false;
    		document.getElementById('timg_picker').src = '<?php echo $theme_path.'img/'?>tb_image_insert.gif';
    		document.getElementById('timg_picker').disabled = false;
    	}
    }
  }
  
  //-->
  </script>
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">
<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="table_prop" id="table_prop">
<tr>
  <td><?php echo $l->m('rows')?>:</td>
  <td><input type="text" name="trows" id="trows" size="3" maxlength="3" class="input_small"></td>
  <td><?php echo $l->m('columns')?>:</td>
  <td><input type="text" name="tcols" id="tcols" size="3" maxlenght="3" class="input_small"></td>
</tr>
<tr>
  <td><?php echo $l->m('css_class')?>:</td>
  <td colspan="3">
    <select id="tcssclass" name="tcssclass" id="tcssclass" size="1" class="input" onchange="css_class_changed();">
	<?php
	foreach($spaw_dropdown_data["table_style"] as $key => $text)
	{
		echo '<option value="'.$key.'">'.$text.'</option>'."\n";
	}
	?>
    </select>
  </td>
</tr>
<tr>
  <td><?php echo $l->m('width')?>:</td>
  <td nowrap>
    <input type="text" name="twidth" id="twidth" size="3" maxlenght="3" class="input_small">
    <select size="1" name="twunits" id="twunits" class="input_small">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
  <td><?php echo $l->m('height')?>:</td>
  <td nowrap>
    <input type="text" name="theight" id="theight" size="3" maxlenght="3" class="input_small">
    <select size="1" name="thunits" id="thunits" class="input_small">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
</tr>
<tr>
  <td><?php echo $l->m('border')?>:</td>
  <td colspan="3"><input type="text" name="tborder" id="tborder" size="2" maxlenght="2" class="input_small"> <?php echo $l->m('pixels')?></td>
</tr>
<tr>
  <td><?php echo $l->m('cellpadding')?>:</td>
  <td><input type="text" name="tcpad" id="tcpad" size="3" maxlenght="3" class="input_small"></td>
  <td><?php echo $l->m('cellspacing')?>:</td>
  <td><input type="text" name="tcspc" id="tcspc" size="3" maxlenght="3" class="input_small"></td>
</tr>
<tr>
  <td colspan="4"><?php echo $l->m('bg_color')?>: <img src="spacer.gif" id="color_sample" border="1" width="30" height="18" align="absbottom">&nbsp;<input type="text" name="tbgcolor" id="tbgcolor" size="7" maxlenght="7" class="input_color" onKeyUp="setSample()">&nbsp;
  <img id="tcolorpicker" src="<?php echo $theme_path.'img/'?>tb_colorpicker.gif" border="0" onClick="showColorPicker(tbgcolor.value)" align="absbottom">
  </td>
</tr>
<tr>
  <td colspan="4">
	<?php echo $l->m('background')?>: <input type="text" name="tbackground" id="tbackground" size="20" class="input" >&nbsp;<img id="timg_picker" src="<?php echo $theme_path.'img/'?>tb_image_insert.gif" border="0" onClick="showImgPicker();" align="absbottom">	
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
