<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Confirmation dialog
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// $Revision: 1.1 $, $Date: 2003/04/24 19:27:56 $
// ================================================

// include wysiwyg config
include '../config/spaw_control.config.php';
include $spaw_root.'class/lang.class.php';

$theme = empty($HTTP_GET_VARS['theme'])?$spaw_default_theme:$HTTP_GET_VARS['theme'];
$theme_path = $spaw_dir.'lib/themes/'.$theme.'/';

$block = $HTTP_GET_VARS['block'];
$message = $HTTP_GET_VARS['message'];

$l = new SPAW_Lang($HTTP_GET_VARS['lang']);
$l->setBlock($block);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
  <title><?php echo $l->m('title')?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l->getCharset()?>">
  <link rel="stylesheet" type="text/css" href="<?php echo $theme_path.'css/'?>dialog.css">
  <script language="javascript" src="utils.js"></script>
  
  <script language="javascript">
  <!--  
  function Init() {
    cur_color = window.dialogArguments;
    resizeDialogToContent();
  }
  
  function okClick() {
    window.returnValue = true;
    window.close();
  }

  function cancelClick() {
    window.returnValue = false;
    window.close();
  }
  //-->
  </script>
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">

<p align="center">
<br>
<?php echo $l->m($message)?>
<br><br>
<form name="colorpicker">
<input type="button" value="<?php echo $l->m('ok')?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo $l->m('cancel')?>" onClick="cancelClick()" class="bt"><br><br>
</form>
</p>

</body>
</html>
