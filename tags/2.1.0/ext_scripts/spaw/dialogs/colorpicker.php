<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Color picker dialog
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// $Revision: 1.8 $, $Date: 2004/12/18 14:28:50 $
// ================================================

// include wysiwyg config
include '../config/spaw_control.config.php';
include $spaw_root.'class/util.class.php';
include $spaw_root.'class/lang.class.php';

$theme = empty($HTTP_GET_VARS['theme'])?$spaw_default_theme:$HTTP_GET_VARS['theme'];
$theme_path = $spaw_dir.'lib/themes/'.$theme.'/';

$l = new SPAW_Lang($HTTP_GET_VARS['lang']);
$l->setBlock('colorpicker');
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
  var cur_color; // passed color
  function Init() {
    cur_color = window.dialogArguments;
    if (cur_color != null)
    {
      document.getElementById("color").value = cur_color;
      document.getElementById("sample").bgColor = cur_color;
    }
    resizeDialogToContent();
  }
  
  function okClick() {
    window.returnValue = document.getElementById("color").value;
    window.close();
    <?php
    if (!empty($_GET['callback']))
      echo "opener.".$_GET['callback']."('".$_GET['editor']."',this);\n";
    ?>
  }

  function cancelClick() {
    window.returnValue = cur_color;
    window.close();
  }
  
  function imgOn(imgid)
  {
    imgid.className = 'img_pick_over';
  }
  function imgOff(imgid)
  {
    imgid.className = 'img_pick';
  }
  function selColor(colorcode)
  {
    document.getElementById("sample").bgColor = '#'+colorcode;
    document.getElementById("color").value = '#'+colorcode;
  }
  function returnColor(colorcode)
  {
    window.returnValue = '#'+colorcode;
    window.close();
  }
  function setSample()
  {
    document.getElementById("sample").bgColor = document.getElementById("color").value;
  }
  //-->
  </script>
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">



<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td bgcolor="#000000"><img id="img000000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000000')" onDblClick="returnColor('000000')"></td>
    <td bgcolor="#060606"><img id="img060606" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('060606')" onDblClick="returnColor('060606')"></td>
    <td bgcolor="#0c0c0c"><img id="img0c0c0c" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0c0c0c')" onDblClick="returnColor('0c0c0c')"></td>
    <td bgcolor="#121212"><img id="img121212" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('121212')" onDblClick="returnColor('121212')"></td>
    <td bgcolor="#181818"><img id="img181818" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('181818')" onDblClick="returnColor('181818')"></td>
    <td bgcolor="#1e1e1e"><img id="img1e1e1e" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('1e1e1e')" onDblClick="returnColor('1e1e1e')"></td>
    <td bgcolor="#242424"><img id="img242424" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('242424')" onDblClick="returnColor('242424')"></td>
    <td bgcolor="#000000"><img id="img000000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000000')" onDblClick="returnColor('000000')"></td>
    <td bgcolor="#00002b"><img id="img00002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00002b')" onDblClick="returnColor('00002b')"></td>
    <td bgcolor="#000056"><img id="img000056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000056')" onDblClick="returnColor('000056')"></td>
    <td bgcolor="#000081"><img id="img000081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000081')" onDblClick="returnColor('000081')"></td>
    <td bgcolor="#0000ac"><img id="img0000ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0000ac')" onDblClick="returnColor('0000ac')"></td>
    <td bgcolor="#0000d7"><img id="img0000d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0000d7')" onDblClick="returnColor('0000d7')"></td>
    <td bgcolor="#0000ff"><img id="img0000ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0000ff')" onDblClick="returnColor('0000ff')"></td>
    <td bgcolor="#002b00"><img id="img002b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002b00')" onDblClick="returnColor('002b00')"></td>
    <td bgcolor="#002b2b"><img id="img002b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002b2b')" onDblClick="returnColor('002b2b')"></td>
    <td bgcolor="#002b56"><img id="img002b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002b56')" onDblClick="returnColor('002b56')"></td>
    <td bgcolor="#002b81"><img id="img002b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002b81')" onDblClick="returnColor('002b81')"></td>
    <td bgcolor="#002bac"><img id="img002bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002bac')" onDblClick="returnColor('002bac')"></td>
    <td bgcolor="#002bd7"><img id="img002bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002bd7')" onDblClick="returnColor('002bd7')"></td>
    <td bgcolor="#002bff"><img id="img002bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('002bff')" onDblClick="returnColor('002bff')"></td>
    <td bgcolor="#005600"><img id="img005600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('005600')" onDblClick="returnColor('005600')"></td>
    <td bgcolor="#00562b"><img id="img00562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00562b')" onDblClick="returnColor('00562b')"></td>
    <td bgcolor="#005656"><img id="img005656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('005656')" onDblClick="returnColor('005656')"></td>
    <td bgcolor="#005681"><img id="img005681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('005681')" onDblClick="returnColor('005681')"></td>
    <td bgcolor="#0056ac"><img id="img0056ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0056ac')" onDblClick="returnColor('0056ac')"></td>
    <td bgcolor="#0056d7"><img id="img0056d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0056d7')" onDblClick="returnColor('0056d7')"></td>
    <td bgcolor="#0056ff"><img id="img0056ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0056ff')" onDblClick="returnColor('0056ff')"></td>
</tr>
<tr>
    <td bgcolor="#252525"><img id="img252525" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('252525')" onDblClick="returnColor('252525')"></td>
    <td bgcolor="#2b2b2b"><img id="img2b2b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2b2b')" onDblClick="returnColor('2b2b2b')"></td>
    <td bgcolor="#313131"><img id="img313131" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('313131')" onDblClick="returnColor('313131')"></td>
    <td bgcolor="#373737"><img id="img373737" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('373737')" onDblClick="returnColor('373737')"></td>
    <td bgcolor="#3d3d3d"><img id="img3d3d3d" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3d3d3d')" onDblClick="returnColor('3d3d3d')"></td>
    <td bgcolor="#434343"><img id="img434343" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('434343')" onDblClick="returnColor('434343')"></td>
    <td bgcolor="#494949"><img id="img494949" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('494949')" onDblClick="returnColor('494949')"></td>
    <td bgcolor="#2b0000"><img id="img2b0000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b0000')" onDblClick="returnColor('2b0000')"></td>
    <td bgcolor="#2b002b"><img id="img2b002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b002b')" onDblClick="returnColor('2b002b')"></td>
    <td bgcolor="#2b0056"><img id="img2b0056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b0056')" onDblClick="returnColor('2b0056')"></td>
    <td bgcolor="#2b0081"><img id="img2b0081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b0081')" onDblClick="returnColor('2b0081')"></td>
    <td bgcolor="#2b00ac"><img id="img2b00ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b00ac')" onDblClick="returnColor('2b00ac')"></td>
    <td bgcolor="#2b00d7"><img id="img2b00d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b00d7')" onDblClick="returnColor('2b00d7')"></td>
    <td bgcolor="#2b00ff"><img id="img2b00ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b00ff')" onDblClick="returnColor('2b00ff')"></td>
    <td bgcolor="#2b2b00"><img id="img2b2b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2b00')" onDblClick="returnColor('2b2b00')"></td>
    <td bgcolor="#2b2b2b"><img id="img2b2b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2b2b')" onDblClick="returnColor('2b2b2b')"></td>
    <td bgcolor="#2b2b56"><img id="img2b2b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2b56')" onDblClick="returnColor('2b2b56')"></td>
    <td bgcolor="#2b2b81"><img id="img2b2b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2b81')" onDblClick="returnColor('2b2b81')"></td>
    <td bgcolor="#2b2bac"><img id="img2b2bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2bac')" onDblClick="returnColor('2b2bac')"></td>
    <td bgcolor="#2b2bd7"><img id="img2b2bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2bd7')" onDblClick="returnColor('2b2bd7')"></td>
    <td bgcolor="#2b2bff"><img id="img2b2bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b2bff')" onDblClick="returnColor('2b2bff')"></td>
    <td bgcolor="#2b5600"><img id="img2b5600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b5600')" onDblClick="returnColor('2b5600')"></td>
    <td bgcolor="#2b562b"><img id="img2b562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b562b')" onDblClick="returnColor('2b562b')"></td>
    <td bgcolor="#2b5656"><img id="img2b5656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b5656')" onDblClick="returnColor('2b5656')"></td>
    <td bgcolor="#2b5681"><img id="img2b5681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b5681')" onDblClick="returnColor('2b5681')"></td>
    <td bgcolor="#2b56ac"><img id="img2b56ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b56ac')" onDblClick="returnColor('2b56ac')"></td>
    <td bgcolor="#2b56d7"><img id="img2b56d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b56d7')" onDblClick="returnColor('2b56d7')"></td>
    <td bgcolor="#2b56ff"><img id="img2b56ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b56ff')" onDblClick="returnColor('2b56ff')"></td>
</tr>
<tr>
    <td bgcolor="#4a4a4a"><img id="img4a4a4a" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('4a4a4a')" onDblClick="returnColor('4a4a4a')"></td>
    <td bgcolor="#505050"><img id="img505050" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('505050')" onDblClick="returnColor('505050')"></td>
    <td bgcolor="#565656"><img id="img565656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('565656')" onDblClick="returnColor('565656')"></td>
    <td bgcolor="#5c5c5c"><img id="img5c5c5c" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5c5c5c')" onDblClick="returnColor('5c5c5c')"></td>
    <td bgcolor="#626262"><img id="img626262" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('626262')" onDblClick="returnColor('626262')"></td>
    <td bgcolor="#686868"><img id="img686868" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('686868')" onDblClick="returnColor('686868')"></td>
    <td bgcolor="#6e6e6e"><img id="img6e6e6e" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6e6e6e')" onDblClick="returnColor('6e6e6e')"></td>
    <td bgcolor="#560000"><img id="img560000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('560000')" onDblClick="returnColor('560000')"></td>
    <td bgcolor="#56002b"><img id="img56002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56002b')" onDblClick="returnColor('56002b')"></td>
    <td bgcolor="#560056"><img id="img560056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('560056')" onDblClick="returnColor('560056')"></td>
    <td bgcolor="#560081"><img id="img560081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('560081')" onDblClick="returnColor('560081')"></td>
    <td bgcolor="#5600ac"><img id="img5600ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5600ac')" onDblClick="returnColor('5600ac')"></td>
    <td bgcolor="#5600d7"><img id="img5600d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5600d7')" onDblClick="returnColor('5600d7')"></td>
    <td bgcolor="#5600ff"><img id="img5600ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5600ff')" onDblClick="returnColor('5600ff')"></td>
    <td bgcolor="#562b00"><img id="img562b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562b00')" onDblClick="returnColor('562b00')"></td>
    <td bgcolor="#562b2b"><img id="img562b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562b2b')" onDblClick="returnColor('562b2b')"></td>
    <td bgcolor="#562b56"><img id="img562b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562b56')" onDblClick="returnColor('562b56')"></td>
    <td bgcolor="#562b81"><img id="img562b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562b81')" onDblClick="returnColor('562b81')"></td>
    <td bgcolor="#562bac"><img id="img562bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562bac')" onDblClick="returnColor('562bac')"></td>
    <td bgcolor="#562bd7"><img id="img562bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562bd7')" onDblClick="returnColor('562bd7')"></td>
    <td bgcolor="#562bff"><img id="img562bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('562bff')" onDblClick="returnColor('562bff')"></td>
    <td bgcolor="#565600"><img id="img565600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('565600')" onDblClick="returnColor('565600')"></td>
    <td bgcolor="#56562b"><img id="img56562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56562b')" onDblClick="returnColor('56562b')"></td>
    <td bgcolor="#565656"><img id="img565656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('565656')" onDblClick="returnColor('565656')"></td>
    <td bgcolor="#565681"><img id="img565681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('565681')" onDblClick="returnColor('565681')"></td>
    <td bgcolor="#5656ac"><img id="img5656ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5656ac')" onDblClick="returnColor('5656ac')"></td>
    <td bgcolor="#5656d7"><img id="img5656d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5656d7')" onDblClick="returnColor('5656d7')"></td>
    <td bgcolor="#5656ff"><img id="img5656ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5656ff')" onDblClick="returnColor('5656ff')"></td>
</tr>
<tr>
    <td bgcolor="#6f6f6f"><img id="img6f6f6f" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6f6f6f')" onDblClick="returnColor('6f6f6f')"></td>
    <td bgcolor="#757575"><img id="img757575" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('757575')" onDblClick="returnColor('757575')"></td>
    <td bgcolor="#7b7b7b"><img id="img7b7b7b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('7b7b7b')" onDblClick="returnColor('7b7b7b')"></td>
    <td bgcolor="#818181"><img id="img818181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('818181')" onDblClick="returnColor('818181')"></td>
    <td bgcolor="#878787"><img id="img878787" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('878787')" onDblClick="returnColor('878787')"></td>
    <td bgcolor="#8d8d8d"><img id="img8d8d8d" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8d8d8d')" onDblClick="returnColor('8d8d8d')"></td>
    <td bgcolor="#939393"><img id="img939393" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('939393')" onDblClick="returnColor('939393')"></td>
    <td bgcolor="#810000"><img id="img810000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('810000')" onDblClick="returnColor('810000')"></td>
    <td bgcolor="#81002b"><img id="img81002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81002b')" onDblClick="returnColor('81002b')"></td>
    <td bgcolor="#810056"><img id="img810056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('810056')" onDblClick="returnColor('810056')"></td>
    <td bgcolor="#810081"><img id="img810081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('810081')" onDblClick="returnColor('810081')"></td>
    <td bgcolor="#8100ac"><img id="img8100ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8100ac')" onDblClick="returnColor('8100ac')"></td>
    <td bgcolor="#8100d7"><img id="img8100d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8100d7')" onDblClick="returnColor('8100d7')"></td>
    <td bgcolor="#8100ff"><img id="img8100ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8100ff')" onDblClick="returnColor('8100ff')"></td>
    <td bgcolor="#812b00"><img id="img812b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812b00')" onDblClick="returnColor('812b00')"></td>
    <td bgcolor="#812b2b"><img id="img812b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812b2b')" onDblClick="returnColor('812b2b')"></td>
    <td bgcolor="#812b56"><img id="img812b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812b56')" onDblClick="returnColor('812b56')"></td>
    <td bgcolor="#812b81"><img id="img812b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812b81')" onDblClick="returnColor('812b81')"></td>
    <td bgcolor="#812bac"><img id="img812bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812bac')" onDblClick="returnColor('812bac')"></td>
    <td bgcolor="#812bd7"><img id="img812bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812bd7')" onDblClick="returnColor('812bd7')"></td>
    <td bgcolor="#812bff"><img id="img812bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('812bff')" onDblClick="returnColor('812bff')"></td>
    <td bgcolor="#815600"><img id="img815600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('815600')" onDblClick="returnColor('815600')"></td>
    <td bgcolor="#81562b"><img id="img81562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81562b')" onDblClick="returnColor('81562b')"></td>
    <td bgcolor="#815656"><img id="img815656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('815656')" onDblClick="returnColor('815656')"></td>
    <td bgcolor="#815681"><img id="img815681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('815681')" onDblClick="returnColor('815681')"></td>
    <td bgcolor="#8156ac"><img id="img8156ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8156ac')" onDblClick="returnColor('8156ac')"></td>
    <td bgcolor="#8156d7"><img id="img8156d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8156d7')" onDblClick="returnColor('8156d7')"></td>
    <td bgcolor="#8156ff"><img id="img8156ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8156ff')" onDblClick="returnColor('8156ff')"></td>
</tr>
<tr>
    <td bgcolor="#949494"><img id="img949494" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('949494')" onDblClick="returnColor('949494')"></td>
    <td bgcolor="#9a9a9a"><img id="img9a9a9a" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9a9a9a')" onDblClick="returnColor('9a9a9a')"></td>
    <td bgcolor="#a0a0a0"><img id="imga0a0a0" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('a0a0a0')" onDblClick="returnColor('a0a0a0')"></td>
    <td bgcolor="#a6a6a6"><img id="imga6a6a6" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('a6a6a6')" onDblClick="returnColor('a6a6a6')"></td>
    <td bgcolor="#acacac"><img id="imgacacac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acacac')" onDblClick="returnColor('acacac')"></td>
    <td bgcolor="#b2b2b2"><img id="imgb2b2b2" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('b2b2b2')" onDblClick="returnColor('b2b2b2')"></td>
    <td bgcolor="#b8b8b8"><img id="imgb8b8b8" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('b8b8b8')" onDblClick="returnColor('b8b8b8')"></td>
    <td bgcolor="#ac0000"><img id="imgac0000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac0000')" onDblClick="returnColor('ac0000')"></td>
    <td bgcolor="#ac002b"><img id="imgac002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac002b')" onDblClick="returnColor('ac002b')"></td>
    <td bgcolor="#ac0056"><img id="imgac0056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac0056')" onDblClick="returnColor('ac0056')"></td>
    <td bgcolor="#ac0081"><img id="imgac0081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac0081')" onDblClick="returnColor('ac0081')"></td>
    <td bgcolor="#ac00ac"><img id="imgac00ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac00ac')" onDblClick="returnColor('ac00ac')"></td>
    <td bgcolor="#ac00d7"><img id="imgac00d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac00d7')" onDblClick="returnColor('ac00d7')"></td>
    <td bgcolor="#ac00ff"><img id="imgac00ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac00ff')" onDblClick="returnColor('ac00ff')"></td>
    <td bgcolor="#ac2b00"><img id="imgac2b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2b00')" onDblClick="returnColor('ac2b00')"></td>
    <td bgcolor="#ac2b2b"><img id="imgac2b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2b2b')" onDblClick="returnColor('ac2b2b')"></td>
    <td bgcolor="#ac2b56"><img id="imgac2b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2b56')" onDblClick="returnColor('ac2b56')"></td>
    <td bgcolor="#ac2b81"><img id="imgac2b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2b81')" onDblClick="returnColor('ac2b81')"></td>
    <td bgcolor="#ac2bac"><img id="imgac2bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2bac')" onDblClick="returnColor('ac2bac')"></td>
    <td bgcolor="#ac2bd7"><img id="imgac2bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2bd7')" onDblClick="returnColor('ac2bd7')"></td>
    <td bgcolor="#ac2bff"><img id="imgac2bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac2bff')" onDblClick="returnColor('ac2bff')"></td>
    <td bgcolor="#ac5600"><img id="imgac5600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac5600')" onDblClick="returnColor('ac5600')"></td>
    <td bgcolor="#ac562b"><img id="imgac562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac562b')" onDblClick="returnColor('ac562b')"></td>
    <td bgcolor="#ac5656"><img id="imgac5656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac5656')" onDblClick="returnColor('ac5656')"></td>
    <td bgcolor="#ac5681"><img id="imgac5681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac5681')" onDblClick="returnColor('ac5681')"></td>
    <td bgcolor="#ac56ac"><img id="imgac56ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac56ac')" onDblClick="returnColor('ac56ac')"></td>
    <td bgcolor="#ac56d7"><img id="imgac56d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac56d7')" onDblClick="returnColor('ac56d7')"></td>
    <td bgcolor="#ac56ff"><img id="imgac56ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac56ff')" onDblClick="returnColor('ac56ff')"></td>
</tr>
<tr>
    <td bgcolor="#b9b9b9"><img id="imgb9b9b9" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('b9b9b9')" onDblClick="returnColor('b9b9b9')"></td>
    <td bgcolor="#bfbfbf"><img id="imgbfbfbf" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('bfbfbf')" onDblClick="returnColor('bfbfbf')"></td>
    <td bgcolor="#c5c5c5"><img id="imgc5c5c5" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('c5c5c5')" onDblClick="returnColor('c5c5c5')"></td>
    <td bgcolor="#cbcbcb"><img id="imgcbcbcb" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('cbcbcb')" onDblClick="returnColor('cbcbcb')"></td>
    <td bgcolor="#d1d1d1"><img id="imgd1d1d1" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d1d1d1')" onDblClick="returnColor('d1d1d1')"></td>
    <td bgcolor="#d7d7d7"><img id="imgd7d7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d7d7')" onDblClick="returnColor('d7d7d7')"></td>
    <td bgcolor="#dddddd"><img id="imgdddddd" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('dddddd')" onDblClick="returnColor('dddddd')"></td>
    <td bgcolor="#d70000"><img id="imgd70000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d70000')" onDblClick="returnColor('d70000')"></td>
    <td bgcolor="#d7002b"><img id="imgd7002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7002b')" onDblClick="returnColor('d7002b')"></td>
    <td bgcolor="#d70056"><img id="imgd70056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d70056')" onDblClick="returnColor('d70056')"></td>
    <td bgcolor="#d70081"><img id="imgd70081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d70081')" onDblClick="returnColor('d70081')"></td>
    <td bgcolor="#d700ac"><img id="imgd700ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d700ac')" onDblClick="returnColor('d700ac')"></td>
    <td bgcolor="#d700d7"><img id="imgd700d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d700d7')" onDblClick="returnColor('d700d7')"></td>
    <td bgcolor="#d700ff"><img id="imgd700ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d700ff')" onDblClick="returnColor('d700ff')"></td>
    <td bgcolor="#d72b00"><img id="imgd72b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72b00')" onDblClick="returnColor('d72b00')"></td>
    <td bgcolor="#d72b2b"><img id="imgd72b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72b2b')" onDblClick="returnColor('d72b2b')"></td>
    <td bgcolor="#d72b56"><img id="imgd72b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72b56')" onDblClick="returnColor('d72b56')"></td>
    <td bgcolor="#d72b81"><img id="imgd72b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72b81')" onDblClick="returnColor('d72b81')"></td>
    <td bgcolor="#d72bac"><img id="imgd72bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72bac')" onDblClick="returnColor('d72bac')"></td>
    <td bgcolor="#d72bd7"><img id="imgd72bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72bd7')" onDblClick="returnColor('d72bd7')"></td>
    <td bgcolor="#d72bff"><img id="imgd72bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d72bff')" onDblClick="returnColor('d72bff')"></td>
    <td bgcolor="#d75600"><img id="imgd75600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d75600')" onDblClick="returnColor('d75600')"></td>
    <td bgcolor="#d7562b"><img id="imgd7562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7562b')" onDblClick="returnColor('d7562b')"></td>
    <td bgcolor="#d75656"><img id="imgd75656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d75656')" onDblClick="returnColor('d75656')"></td>
    <td bgcolor="#d75681"><img id="imgd75681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d75681')" onDblClick="returnColor('d75681')"></td>
    <td bgcolor="#d756ac"><img id="imgd756ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d756ac')" onDblClick="returnColor('d756ac')"></td>
    <td bgcolor="#d756d7"><img id="imgd756d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d756d7')" onDblClick="returnColor('d756d7')"></td>
    <td bgcolor="#d756ff"><img id="imgd756ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d756ff')" onDblClick="returnColor('d756ff')"></td>
</tr>
<tr>
    <td bgcolor="#dedede"><img id="imgdedede" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('dedede')" onDblClick="returnColor('dedede')"></td>
    <td bgcolor="#e4e4e4"><img id="imge4e4e4" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('e4e4e4')" onDblClick="returnColor('e4e4e4')"></td>
    <td bgcolor="#eaeaea"><img id="imgeaeaea" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('eaeaea')" onDblClick="returnColor('eaeaea')"></td>
    <td bgcolor="#f0f0f0"><img id="imgf0f0f0" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('f0f0f0')" onDblClick="returnColor('f0f0f0')"></td>
    <td bgcolor="#f6f6f6"><img id="imgf6f6f6" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('f6f6f6')" onDblClick="returnColor('f6f6f6')"></td>
    <td bgcolor="#fcfcfc"><img id="imgfcfcfc" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('fcfcfc')" onDblClick="returnColor('fcfcfc')"></td>
    <td bgcolor="#ffffff"><img id="imgffffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffffff')" onDblClick="returnColor('ffffff')"></td>
    <td bgcolor="#ff0000"><img id="imgff0000" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff0000')" onDblClick="returnColor('ff0000')"></td>
    <td bgcolor="#ff002b"><img id="imgff002b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff002b')" onDblClick="returnColor('ff002b')"></td>
    <td bgcolor="#ff0056"><img id="imgff0056" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff0056')" onDblClick="returnColor('ff0056')"></td>
    <td bgcolor="#ff0081"><img id="imgff0081" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff0081')" onDblClick="returnColor('ff0081')"></td>
    <td bgcolor="#ff00ac"><img id="imgff00ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff00ac')" onDblClick="returnColor('ff00ac')"></td>
    <td bgcolor="#ff00d7"><img id="imgff00d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff00d7')" onDblClick="returnColor('ff00d7')"></td>
    <td bgcolor="#ff00ff"><img id="imgff00ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff00ff')" onDblClick="returnColor('ff00ff')"></td>
    <td bgcolor="#ff2b00"><img id="imgff2b00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2b00')" onDblClick="returnColor('ff2b00')"></td>
    <td bgcolor="#ff2b2b"><img id="imgff2b2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2b2b')" onDblClick="returnColor('ff2b2b')"></td>
    <td bgcolor="#ff2b56"><img id="imgff2b56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2b56')" onDblClick="returnColor('ff2b56')"></td>
    <td bgcolor="#ff2b81"><img id="imgff2b81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2b81')" onDblClick="returnColor('ff2b81')"></td>
    <td bgcolor="#ff2bac"><img id="imgff2bac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2bac')" onDblClick="returnColor('ff2bac')"></td>
    <td bgcolor="#ff2bd7"><img id="imgff2bd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2bd7')" onDblClick="returnColor('ff2bd7')"></td>
    <td bgcolor="#ff2bff"><img id="imgff2bff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff2bff')" onDblClick="returnColor('ff2bff')"></td>
    <td bgcolor="#ff5600"><img id="imgff5600" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff5600')" onDblClick="returnColor('ff5600')"></td>
    <td bgcolor="#ff562b"><img id="imgff562b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff562b')" onDblClick="returnColor('ff562b')"></td>
    <td bgcolor="#ff5656"><img id="imgff5656" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff5656')" onDblClick="returnColor('ff5656')"></td>
    <td bgcolor="#ff5681"><img id="imgff5681" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff5681')" onDblClick="returnColor('ff5681')"></td>
    <td bgcolor="#ff56ac"><img id="imgff56ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff56ac')" onDblClick="returnColor('ff56ac')"></td>
    <td bgcolor="#ff56d7"><img id="imgff56d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff56d7')" onDblClick="returnColor('ff56d7')"></td>
    <td bgcolor="#ff56ff"><img id="imgff56ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff56ff')" onDblClick="returnColor('ff56ff')"></td>
</tr>

<tr>
    <td bgcolor="#008100"><img id="img008100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('008100')" onDblClick="returnColor('008100')"></td>
    <td bgcolor="#00812b"><img id="img00812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00812b')" onDblClick="returnColor('00812b')"></td>
    <td bgcolor="#008156"><img id="img008156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('008156')" onDblClick="returnColor('008156')"></td>
    <td bgcolor="#008181"><img id="img008181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('008181')" onDblClick="returnColor('008181')"></td>
    <td bgcolor="#0081ac"><img id="img0081ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0081ac')" onDblClick="returnColor('0081ac')"></td>
    <td bgcolor="#0081d7"><img id="img0081d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0081d7')" onDblClick="returnColor('0081d7')"></td>
    <td bgcolor="#0081ff"><img id="img0081ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0081ff')" onDblClick="returnColor('0081ff')"></td>
    <td bgcolor="#00ac00"><img id="img00ac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ac00')" onDblClick="returnColor('00ac00')"></td>
    <td bgcolor="#00ac2b"><img id="img00ac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ac2b')" onDblClick="returnColor('00ac2b')"></td>
    <td bgcolor="#00ac56"><img id="img00ac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ac56')" onDblClick="returnColor('00ac56')"></td>
    <td bgcolor="#00ac81"><img id="img00ac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ac81')" onDblClick="returnColor('00ac81')"></td>
    <td bgcolor="#00acac"><img id="img00acac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00acac')" onDblClick="returnColor('00acac')"></td>
    <td bgcolor="#00acd7"><img id="img00acd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00acd7')" onDblClick="returnColor('00acd7')"></td>
    <td bgcolor="#00acff"><img id="img00acff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00acff')" onDblClick="returnColor('00acff')"></td>
    <td bgcolor="#00d700"><img id="img00d700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d700')" onDblClick="returnColor('00d700')"></td>
    <td bgcolor="#00d72b"><img id="img00d72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d72b')" onDblClick="returnColor('00d72b')"></td>
    <td bgcolor="#00d756"><img id="img00d756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d756')" onDblClick="returnColor('00d756')"></td>
    <td bgcolor="#00d781"><img id="img00d781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d781')" onDblClick="returnColor('00d781')"></td>
    <td bgcolor="#00d7ac"><img id="img00d7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d7ac')" onDblClick="returnColor('00d7ac')"></td>
    <td bgcolor="#00d7d7"><img id="img00d7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d7d7')" onDblClick="returnColor('00d7d7')"></td>
    <td bgcolor="#00d7ff"><img id="img00d7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00d7ff')" onDblClick="returnColor('00d7ff')"></td>
    <td bgcolor="#00ff00"><img id="img00ff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ff00')" onDblClick="returnColor('00ff00')"></td>
    <td bgcolor="#00ff2b"><img id="img00ff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ff2b')" onDblClick="returnColor('00ff2b')"></td>
    <td bgcolor="#00ff56"><img id="img00ff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ff56')" onDblClick="returnColor('00ff56')"></td>
    <td bgcolor="#00ff81"><img id="img00ff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ff81')" onDblClick="returnColor('00ff81')"></td>
    <td bgcolor="#00ffac"><img id="img00ffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ffac')" onDblClick="returnColor('00ffac')"></td>
    <td bgcolor="#00ffd7"><img id="img00ffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ffd7')" onDblClick="returnColor('00ffd7')"></td>
    <td bgcolor="#00ffff"><img id="img00ffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00ffff')" onDblClick="returnColor('00ffff')"></td>
</tr>
<tr>
    <td bgcolor="#2b8100"><img id="img2b8100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b8100')" onDblClick="returnColor('2b8100')"></td>
    <td bgcolor="#2b812b"><img id="img2b812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b812b')" onDblClick="returnColor('2b812b')"></td>
    <td bgcolor="#2b8156"><img id="img2b8156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b8156')" onDblClick="returnColor('2b8156')"></td>
    <td bgcolor="#2b8181"><img id="img2b8181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b8181')" onDblClick="returnColor('2b8181')"></td>
    <td bgcolor="#2b81ac"><img id="img2b81ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b81ac')" onDblClick="returnColor('2b81ac')"></td>
    <td bgcolor="#2b81d7"><img id="img2b81d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b81d7')" onDblClick="returnColor('2b81d7')"></td>
    <td bgcolor="#2b81ff"><img id="img2b81ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2b81ff')" onDblClick="returnColor('2b81ff')"></td>
    <td bgcolor="#2bac00"><img id="img2bac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bac00')" onDblClick="returnColor('2bac00')"></td>
    <td bgcolor="#2bac2b"><img id="img2bac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bac2b')" onDblClick="returnColor('2bac2b')"></td>
    <td bgcolor="#2bac56"><img id="img2bac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bac56')" onDblClick="returnColor('2bac56')"></td>
    <td bgcolor="#2bac81"><img id="img2bac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bac81')" onDblClick="returnColor('2bac81')"></td>
    <td bgcolor="#2bacac"><img id="img2bacac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bacac')" onDblClick="returnColor('2bacac')"></td>
    <td bgcolor="#2bacd7"><img id="img2bacd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bacd7')" onDblClick="returnColor('2bacd7')"></td>
    <td bgcolor="#2bacff"><img id="img2bacff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bacff')" onDblClick="returnColor('2bacff')"></td>
    <td bgcolor="#2bd700"><img id="img2bd700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd700')" onDblClick="returnColor('2bd700')"></td>
    <td bgcolor="#2bd72b"><img id="img2bd72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd72b')" onDblClick="returnColor('2bd72b')"></td>
    <td bgcolor="#2bd756"><img id="img2bd756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd756')" onDblClick="returnColor('2bd756')"></td>
    <td bgcolor="#2bd781"><img id="img2bd781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd781')" onDblClick="returnColor('2bd781')"></td>
    <td bgcolor="#2bd7ac"><img id="img2bd7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd7ac')" onDblClick="returnColor('2bd7ac')"></td>
    <td bgcolor="#2bd7d7"><img id="img2bd7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd7d7')" onDblClick="returnColor('2bd7d7')"></td>
    <td bgcolor="#2bd7ff"><img id="img2bd7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bd7ff')" onDblClick="returnColor('2bd7ff')"></td>
    <td bgcolor="#2bff00"><img id="img2bff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bff00')" onDblClick="returnColor('2bff00')"></td>
    <td bgcolor="#2bff2b"><img id="img2bff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bff2b')" onDblClick="returnColor('2bff2b')"></td>
    <td bgcolor="#2bff56"><img id="img2bff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bff56')" onDblClick="returnColor('2bff56')"></td>
    <td bgcolor="#2bff81"><img id="img2bff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bff81')" onDblClick="returnColor('2bff81')"></td>
    <td bgcolor="#2bffac"><img id="img2bffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bffac')" onDblClick="returnColor('2bffac')"></td>
    <td bgcolor="#2bffd7"><img id="img2bffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bffd7')" onDblClick="returnColor('2bffd7')"></td>
    <td bgcolor="#2bffff"><img id="img2bffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('2bffff')" onDblClick="returnColor('2bffff')"></td>
</tr>
<tr>
    <td bgcolor="#568100"><img id="img568100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('568100')" onDblClick="returnColor('568100')"></td>
    <td bgcolor="#56812b"><img id="img56812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56812b')" onDblClick="returnColor('56812b')"></td>
    <td bgcolor="#568156"><img id="img568156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('568156')" onDblClick="returnColor('568156')"></td>
    <td bgcolor="#568181"><img id="img568181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('568181')" onDblClick="returnColor('568181')"></td>
    <td bgcolor="#5681ac"><img id="img5681ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5681ac')" onDblClick="returnColor('5681ac')"></td>
    <td bgcolor="#5681d7"><img id="img5681d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5681d7')" onDblClick="returnColor('5681d7')"></td>
    <td bgcolor="#5681ff"><img id="img5681ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('5681ff')" onDblClick="returnColor('5681ff')"></td>
    <td bgcolor="#56ac00"><img id="img56ac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ac00')" onDblClick="returnColor('56ac00')"></td>
    <td bgcolor="#56ac2b"><img id="img56ac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ac2b')" onDblClick="returnColor('56ac2b')"></td>
    <td bgcolor="#56ac56"><img id="img56ac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ac56')" onDblClick="returnColor('56ac56')"></td>
    <td bgcolor="#56ac81"><img id="img56ac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ac81')" onDblClick="returnColor('56ac81')"></td>
    <td bgcolor="#56acac"><img id="img56acac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56acac')" onDblClick="returnColor('56acac')"></td>
    <td bgcolor="#56acd7"><img id="img56acd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56acd7')" onDblClick="returnColor('56acd7')"></td>
    <td bgcolor="#56acff"><img id="img56acff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56acff')" onDblClick="returnColor('56acff')"></td>
    <td bgcolor="#56d700"><img id="img56d700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d700')" onDblClick="returnColor('56d700')"></td>
    <td bgcolor="#56d72b"><img id="img56d72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d72b')" onDblClick="returnColor('56d72b')"></td>
    <td bgcolor="#56d756"><img id="img56d756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d756')" onDblClick="returnColor('56d756')"></td>
    <td bgcolor="#56d781"><img id="img56d781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d781')" onDblClick="returnColor('56d781')"></td>
    <td bgcolor="#56d7ac"><img id="img56d7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d7ac')" onDblClick="returnColor('56d7ac')"></td>
    <td bgcolor="#56d7d7"><img id="img56d7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d7d7')" onDblClick="returnColor('56d7d7')"></td>
    <td bgcolor="#56d7ff"><img id="img56d7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56d7ff')" onDblClick="returnColor('56d7ff')"></td>
    <td bgcolor="#56ff00"><img id="img56ff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ff00')" onDblClick="returnColor('56ff00')"></td>
    <td bgcolor="#56ff2b"><img id="img56ff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ff2b')" onDblClick="returnColor('56ff2b')"></td>
    <td bgcolor="#56ff56"><img id="img56ff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ff56')" onDblClick="returnColor('56ff56')"></td>
    <td bgcolor="#56ff81"><img id="img56ff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ff81')" onDblClick="returnColor('56ff81')"></td>
    <td bgcolor="#56ffac"><img id="img56ffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ffac')" onDblClick="returnColor('56ffac')"></td>
    <td bgcolor="#56ffd7"><img id="img56ffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ffd7')" onDblClick="returnColor('56ffd7')"></td>
    <td bgcolor="#56ffff"><img id="img56ffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('56ffff')" onDblClick="returnColor('56ffff')"></td>
</tr>
<tr>
    <td bgcolor="#818100"><img id="img818100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('818100')" onDblClick="returnColor('818100')"></td>
    <td bgcolor="#81812b"><img id="img81812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81812b')" onDblClick="returnColor('81812b')"></td>
    <td bgcolor="#818156"><img id="img818156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('818156')" onDblClick="returnColor('818156')"></td>
    <td bgcolor="#818181"><img id="img818181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('818181')" onDblClick="returnColor('818181')"></td>
    <td bgcolor="#8181ac"><img id="img8181ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8181ac')" onDblClick="returnColor('8181ac')"></td>
    <td bgcolor="#8181d7"><img id="img8181d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8181d7')" onDblClick="returnColor('8181d7')"></td>
    <td bgcolor="#8181ff"><img id="img8181ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('8181ff')" onDblClick="returnColor('8181ff')"></td>
    <td bgcolor="#81ac00"><img id="img81ac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ac00')" onDblClick="returnColor('81ac00')"></td>
    <td bgcolor="#81ac2b"><img id="img81ac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ac2b')" onDblClick="returnColor('81ac2b')"></td>
    <td bgcolor="#81ac56"><img id="img81ac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ac56')" onDblClick="returnColor('81ac56')"></td>
    <td bgcolor="#81ac81"><img id="img81ac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ac81')" onDblClick="returnColor('81ac81')"></td>
    <td bgcolor="#81acac"><img id="img81acac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81acac')" onDblClick="returnColor('81acac')"></td>
    <td bgcolor="#81acd7"><img id="img81acd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81acd7')" onDblClick="returnColor('81acd7')"></td>
    <td bgcolor="#81acff"><img id="img81acff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81acff')" onDblClick="returnColor('81acff')"></td>
    <td bgcolor="#81d700"><img id="img81d700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d700')" onDblClick="returnColor('81d700')"></td>
    <td bgcolor="#81d72b"><img id="img81d72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d72b')" onDblClick="returnColor('81d72b')"></td>
    <td bgcolor="#81d756"><img id="img81d756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d756')" onDblClick="returnColor('81d756')"></td>
    <td bgcolor="#81d781"><img id="img81d781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d781')" onDblClick="returnColor('81d781')"></td>
    <td bgcolor="#81d7ac"><img id="img81d7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d7ac')" onDblClick="returnColor('81d7ac')"></td>
    <td bgcolor="#81d7d7"><img id="img81d7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d7d7')" onDblClick="returnColor('81d7d7')"></td>
    <td bgcolor="#81d7ff"><img id="img81d7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81d7ff')" onDblClick="returnColor('81d7ff')"></td>
    <td bgcolor="#81ff00"><img id="img81ff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ff00')" onDblClick="returnColor('81ff00')"></td>
    <td bgcolor="#81ff2b"><img id="img81ff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ff2b')" onDblClick="returnColor('81ff2b')"></td>
    <td bgcolor="#81ff56"><img id="img81ff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ff56')" onDblClick="returnColor('81ff56')"></td>
    <td bgcolor="#81ff81"><img id="img81ff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ff81')" onDblClick="returnColor('81ff81')"></td>
    <td bgcolor="#81ffac"><img id="img81ffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ffac')" onDblClick="returnColor('81ffac')"></td>
    <td bgcolor="#81ffd7"><img id="img81ffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ffd7')" onDblClick="returnColor('81ffd7')"></td>
    <td bgcolor="#81ffff"><img id="img81ffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('81ffff')" onDblClick="returnColor('81ffff')"></td>
</tr>
<tr>
    <td bgcolor="#ac8100"><img id="imgac8100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac8100')" onDblClick="returnColor('ac8100')"></td>
    <td bgcolor="#ac812b"><img id="imgac812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac812b')" onDblClick="returnColor('ac812b')"></td>
    <td bgcolor="#ac8156"><img id="imgac8156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac8156')" onDblClick="returnColor('ac8156')"></td>
    <td bgcolor="#ac8181"><img id="imgac8181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac8181')" onDblClick="returnColor('ac8181')"></td>
    <td bgcolor="#ac81ac"><img id="imgac81ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac81ac')" onDblClick="returnColor('ac81ac')"></td>
    <td bgcolor="#ac81d7"><img id="imgac81d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac81d7')" onDblClick="returnColor('ac81d7')"></td>
    <td bgcolor="#ac81ff"><img id="imgac81ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ac81ff')" onDblClick="returnColor('ac81ff')"></td>
    <td bgcolor="#acac00"><img id="imgacac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acac00')" onDblClick="returnColor('acac00')"></td>
    <td bgcolor="#acac2b"><img id="imgacac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acac2b')" onDblClick="returnColor('acac2b')"></td>
    <td bgcolor="#acac56"><img id="imgacac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acac56')" onDblClick="returnColor('acac56')"></td>
    <td bgcolor="#acac81"><img id="imgacac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acac81')" onDblClick="returnColor('acac81')"></td>
    <td bgcolor="#acacac"><img id="imgacacac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acacac')" onDblClick="returnColor('acacac')"></td>
    <td bgcolor="#acacd7"><img id="imgacacd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acacd7')" onDblClick="returnColor('acacd7')"></td>
    <td bgcolor="#acacff"><img id="imgacacff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acacff')" onDblClick="returnColor('acacff')"></td>
    <td bgcolor="#acd700"><img id="imgacd700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd700')" onDblClick="returnColor('acd700')"></td>
    <td bgcolor="#acd72b"><img id="imgacd72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd72b')" onDblClick="returnColor('acd72b')"></td>
    <td bgcolor="#acd756"><img id="imgacd756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd756')" onDblClick="returnColor('acd756')"></td>
    <td bgcolor="#acd781"><img id="imgacd781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd781')" onDblClick="returnColor('acd781')"></td>
    <td bgcolor="#acd7ac"><img id="imgacd7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd7ac')" onDblClick="returnColor('acd7ac')"></td>
    <td bgcolor="#acd7d7"><img id="imgacd7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd7d7')" onDblClick="returnColor('acd7d7')"></td>
    <td bgcolor="#acd7ff"><img id="imgacd7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acd7ff')" onDblClick="returnColor('acd7ff')"></td>
    <td bgcolor="#acff00"><img id="imgacff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acff00')" onDblClick="returnColor('acff00')"></td>
    <td bgcolor="#acff2b"><img id="imgacff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acff2b')" onDblClick="returnColor('acff2b')"></td>
    <td bgcolor="#acff56"><img id="imgacff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acff56')" onDblClick="returnColor('acff56')"></td>
    <td bgcolor="#acff81"><img id="imgacff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acff81')" onDblClick="returnColor('acff81')"></td>
    <td bgcolor="#acffac"><img id="imgacffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acffac')" onDblClick="returnColor('acffac')"></td>
    <td bgcolor="#acffd7"><img id="imgacffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acffd7')" onDblClick="returnColor('acffd7')"></td>
    <td bgcolor="#acffff"><img id="imgacffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('acffff')" onDblClick="returnColor('acffff')"></td>
</tr>
<tr>
    <td bgcolor="#d78100"><img id="imgd78100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d78100')" onDblClick="returnColor('d78100')"></td>
    <td bgcolor="#d7812b"><img id="imgd7812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7812b')" onDblClick="returnColor('d7812b')"></td>
    <td bgcolor="#d78156"><img id="imgd78156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d78156')" onDblClick="returnColor('d78156')"></td>
    <td bgcolor="#d78181"><img id="imgd78181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d78181')" onDblClick="returnColor('d78181')"></td>
    <td bgcolor="#d781ac"><img id="imgd781ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d781ac')" onDblClick="returnColor('d781ac')"></td>
    <td bgcolor="#d781d7"><img id="imgd781d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d781d7')" onDblClick="returnColor('d781d7')"></td>
    <td bgcolor="#d781ff"><img id="imgd781ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d781ff')" onDblClick="returnColor('d781ff')"></td>
    <td bgcolor="#d7ac00"><img id="imgd7ac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ac00')" onDblClick="returnColor('d7ac00')"></td>
    <td bgcolor="#d7ac2b"><img id="imgd7ac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ac2b')" onDblClick="returnColor('d7ac2b')"></td>
    <td bgcolor="#d7ac56"><img id="imgd7ac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ac56')" onDblClick="returnColor('d7ac56')"></td>
    <td bgcolor="#d7ac81"><img id="imgd7ac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ac81')" onDblClick="returnColor('d7ac81')"></td>
    <td bgcolor="#d7acac"><img id="imgd7acac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7acac')" onDblClick="returnColor('d7acac')"></td>
    <td bgcolor="#d7acd7"><img id="imgd7acd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7acd7')" onDblClick="returnColor('d7acd7')"></td>
    <td bgcolor="#d7acff"><img id="imgd7acff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7acff')" onDblClick="returnColor('d7acff')"></td>
    <td bgcolor="#d7d700"><img id="imgd7d700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d700')" onDblClick="returnColor('d7d700')"></td>
    <td bgcolor="#d7d72b"><img id="imgd7d72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d72b')" onDblClick="returnColor('d7d72b')"></td>
    <td bgcolor="#d7d756"><img id="imgd7d756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d756')" onDblClick="returnColor('d7d756')"></td>
    <td bgcolor="#d7d781"><img id="imgd7d781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d781')" onDblClick="returnColor('d7d781')"></td>
    <td bgcolor="#d7d7ac"><img id="imgd7d7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d7ac')" onDblClick="returnColor('d7d7ac')"></td>
    <td bgcolor="#d7d7d7"><img id="imgd7d7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d7d7')" onDblClick="returnColor('d7d7d7')"></td>
    <td bgcolor="#d7d7ff"><img id="imgd7d7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7d7ff')" onDblClick="returnColor('d7d7ff')"></td>
    <td bgcolor="#d7ff00"><img id="imgd7ff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ff00')" onDblClick="returnColor('d7ff00')"></td>
    <td bgcolor="#d7ff2b"><img id="imgd7ff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ff2b')" onDblClick="returnColor('d7ff2b')"></td>
    <td bgcolor="#d7ff56"><img id="imgd7ff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ff56')" onDblClick="returnColor('d7ff56')"></td>
    <td bgcolor="#d7ff81"><img id="imgd7ff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ff81')" onDblClick="returnColor('d7ff81')"></td>
    <td bgcolor="#d7ffac"><img id="imgd7ffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ffac')" onDblClick="returnColor('d7ffac')"></td>
    <td bgcolor="#d7ffd7"><img id="imgd7ffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ffd7')" onDblClick="returnColor('d7ffd7')"></td>
    <td bgcolor="#d7ffff"><img id="imgd7ffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('d7ffff')" onDblClick="returnColor('d7ffff')"></td>
</tr>
<tr>
    <td bgcolor="#ff8100"><img id="imgff8100" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff8100')" onDblClick="returnColor('ff8100')"></td>
    <td bgcolor="#ff812b"><img id="imgff812b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff812b')" onDblClick="returnColor('ff812b')"></td>
    <td bgcolor="#ff8156"><img id="imgff8156" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff8156')" onDblClick="returnColor('ff8156')"></td>
    <td bgcolor="#ff8181"><img id="imgff8181" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff8181')" onDblClick="returnColor('ff8181')"></td>
    <td bgcolor="#ff81ac"><img id="imgff81ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff81ac')" onDblClick="returnColor('ff81ac')"></td>
    <td bgcolor="#ff81d7"><img id="imgff81d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff81d7')" onDblClick="returnColor('ff81d7')"></td>
    <td bgcolor="#ff81ff"><img id="imgff81ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ff81ff')" onDblClick="returnColor('ff81ff')"></td>
    <td bgcolor="#ffac00"><img id="imgffac00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffac00')" onDblClick="returnColor('ffac00')"></td>
    <td bgcolor="#ffac2b"><img id="imgffac2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffac2b')" onDblClick="returnColor('ffac2b')"></td>
    <td bgcolor="#ffac56"><img id="imgffac56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffac56')" onDblClick="returnColor('ffac56')"></td>
    <td bgcolor="#ffac81"><img id="imgffac81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffac81')" onDblClick="returnColor('ffac81')"></td>
    <td bgcolor="#ffacac"><img id="imgffacac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffacac')" onDblClick="returnColor('ffacac')"></td>
    <td bgcolor="#ffacd7"><img id="imgffacd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffacd7')" onDblClick="returnColor('ffacd7')"></td>
    <td bgcolor="#ffacff"><img id="imgffacff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffacff')" onDblClick="returnColor('ffacff')"></td>
    <td bgcolor="#ffd700"><img id="imgffd700" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd700')" onDblClick="returnColor('ffd700')"></td>
    <td bgcolor="#ffd72b"><img id="imgffd72b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd72b')" onDblClick="returnColor('ffd72b')"></td>
    <td bgcolor="#ffd756"><img id="imgffd756" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd756')" onDblClick="returnColor('ffd756')"></td>
    <td bgcolor="#ffd781"><img id="imgffd781" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd781')" onDblClick="returnColor('ffd781')"></td>
    <td bgcolor="#ffd7ac"><img id="imgffd7ac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd7ac')" onDblClick="returnColor('ffd7ac')"></td>
    <td bgcolor="#ffd7d7"><img id="imgffd7d7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd7d7')" onDblClick="returnColor('ffd7d7')"></td>
    <td bgcolor="#ffd7ff"><img id="imgffd7ff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffd7ff')" onDblClick="returnColor('ffd7ff')"></td>
    <td bgcolor="#ffff00"><img id="imgffff00" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffff00')" onDblClick="returnColor('ffff00')"></td>
    <td bgcolor="#ffff2b"><img id="imgffff2b" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffff2b')" onDblClick="returnColor('ffff2b')"></td>
    <td bgcolor="#ffff56"><img id="imgffff56" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffff56')" onDblClick="returnColor('ffff56')"></td>
    <td bgcolor="#ffff81"><img id="imgffff81" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffff81')" onDblClick="returnColor('ffff81')"></td>
    <td bgcolor="#ffffac"><img id="imgffffac" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffffac')" onDblClick="returnColor('ffffac')"></td>
    <td bgcolor="#ffffd7"><img id="imgffffd7" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffffd7')" onDblClick="returnColor('ffffd7')"></td>
    <td bgcolor="#ffffff"><img id="imgffffff" src="spacer.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('ffffff')" onDblClick="returnColor('ffffff')"></td>
</tr>

</table>



<table border="0" cellspacing="0" cellpadding="0" width="336">
<form name="colorpicker" onsubmit="okClick(); return false;">
<tr>
<td id="sample" align="left" width="80"><img src="spacer.gif" border="1" width="80" height="30" hspace="0" vspace="0"></td>
</td>
<td align="right" valign="bottom" width="80%" nowrap>
<input type="text" id="color" name="color" size="7" maxlength="7" class="input_color" onKeyUp="setSample()">
<input type="submit" value="<?php echo $l->m('ok')?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo $l->m('cancel')?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>

</body>
</html>
