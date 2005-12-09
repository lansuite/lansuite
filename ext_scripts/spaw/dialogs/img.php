<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Image properties dialog
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
$l->setBlock('image_prop');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
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
  function Init() {
    var iProps = window.dialogArguments;
    if (iProps)
    {
      // set attribute values
      if (iProps.width) {
        document.getElementById('cwidth').value = iProps.width;
      }
      if (iProps.height) {
        document.getElementById('cheight').value = iProps.height;
      }
      
      setAlign(iProps.align);
      
      if (iProps.src) {
        document.getElementById('csrc').value = iProps.src;
      }
      if (iProps.alt) {
        document.getElementById('calt').value = iProps.alt;
      }
      if (iProps.border) {
        document.getElementById('cborder').value = iProps.border;
      }
      if (iProps.hspace) {
        document.getElementById('chspace').value = iProps.hspace;
      }
      if (iProps.vspace) {
        document.getElementById('cvspace').value = iProps.vspace;
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
    if (isNaN(parseInt(document.getElementById('cborder').value)) && document.getElementById('cborder').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_border_nan')?>');
      document.getElementById('cborder').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('chspace').value)) && document.getElementById('chspace').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_hspace_nan')?>');
      document.getElementById('chspace').focus();
      return false;
    }
    if (isNaN(parseInt(document.getElementById('cvspace').value)) && document.getElementById('cvspace').value != '')
    {
      alert('<?php echo $l->m('error').': '.$l->m('error_vspace_nan')?>');
      document.getElementById('cvspace').focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var iProps = {};
      iProps.align = (document.getElementById('calign').value)?(document.getElementById('calign').value):'';
      iProps.width = (document.getElementById('cwidth').value)?(document.getElementById('cwidth').value):'';
      iProps.height = (document.getElementById('cheight').value)?(document.getElementById('cheight').value):'';
      iProps.border = (document.getElementById('cborder').value)?(document.getElementById('cborder').value):'';
      iProps.src = (document.getElementById('csrc').value)?(document.getElementById('csrc').value):'';
      iProps.alt = (document.getElementById('calt').value)?(document.getElementById('calt').value):'';
      iProps.hspace = (document.getElementById('chspace').value)?(document.getElementById('chspace').value):'';
      iProps.vspace = (document.getElementById('cvspace').value)?(document.getElementById('cvspace').value):'';

      window.returnValue = iProps;
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
  
  
  function setAlign(alignment)
  {
    for (i=0; i<document.getElementById('calign').options.length; i++)  
    {
      al = document.getElementById('calign').options.item(i);
      if (al.value == alignment.toLowerCase()) {
        document.getElementById('calign').selectedIndex = al.index;
      }
    }
  }

  //-->
  </script>
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">
<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="img_prop" id="img_prop">
<tr>
  <td><?php echo $l->m('source')?>:</td>
  <td colspan="3"><input type="text" name="csrc" id="csrc" class="input" size="32"></td>
</tr>
<tr>
  <td><?php echo $l->m('alt')?>:</td>
  <td colspan="3"><input type="text" name="calt" id="calt" class="input" size="32"></td>
</tr>
<tr>
  <td><?php echo $l->m('align')?>:</td>
  <td align="left">
  <select name="calign" id="calign" size="1" class="input">
    <option value=""></option>
    <option value="left"><?php echo $l->m('left')?></option>
    <option value="right"><?php echo $l->m('right')?></option>
    <option value="top"><?php echo $l->m('top')?></option>
    <option value="middle"><?php echo $l->m('middle')?></option>
    <option value="bottom"><?php echo $l->m('bottom')?></option>
    <option value="absmiddle"><?php echo $l->m('absmiddle')?></option>
    <option value="texttop"><?php echo $l->m('texttop')?></option>
    <option value="baseline"><?php echo $l->m('baseline')?></option>
  </select>
  </td>
  <td><?php echo $l->m('border')?>:</td>
  <td align="left"><input type="text" name="cborder" id="cborder" class="input_small"></td>
</tr>
<tr>
  <td><?php echo $l->m('width')?>:</td>
  <td nowrap>
    <input type="text" name="cwidth" id="cwidth" size="3" maxlength="3" class="input_small">
  </td>
  <td><?php echo $l->m('height')?>:</td>
  <td nowrap>
    <input type="text" name="cheight" id="cheight" size="3" maxlength="3" class="input_small">
  </td>
</tr>
<tr>
  <td><?php echo $l->m('hspace')?>:</td>
  <td nowrap>
    <input type="text" name="chspace" id="chspace" size="3" maxlength="3" class="input_small">
  </td>
  <td><?php echo $l->m('vspace')?>:</td>
  <td nowrap>
    <input type="text" name="cvspace" id="cvspace" size="3" maxlength="3" class="input_small">
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
