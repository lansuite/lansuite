<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Hyperlink properties dialog
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2004-01-20
// ================================================

// include wysiwyg config
include '../config/spaw_control.config.php';
include $spaw_root.'class/util.class.php';
include $spaw_root.'class/lang.class.php';

$theme = empty($HTTP_GET_VARS['theme'])?$spaw_default_theme:$HTTP_GET_VARS['theme'];
$theme_path = $spaw_dir.'lib/themes/'.$theme.'/';

$l = new SPAW_Lang($HTTP_GET_VARS['lang']);
$l->setBlock('hyperlink');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
  <title><?php echo $l->m('title')?></title>
  <meta http-equiv="Pragma" content="no-cache">
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
    var aProps = window.dialogArguments;
    if (aProps && (aProps.href || aProps.name))
    {
      // set attribute values
      if (aProps.href) {
        document.getElementById("chref").value = aProps.href;
      }
      if (aProps.name) {
        document.getElementById("cname").value = aProps.name;
      }

      setTarget(aProps.target);
      
      if (aProps.title) {
        document.getElementById("ctitle").value = aProps.title;
      }
    }
    var found = setAnchors(aProps.anchors, aProps.href);
	
    var atype = "link";
    if (aProps.name)
    {
      atype = "anchor";	
    }
    else if (found)
    {
      atype = "link2anchor";
    }
    if (document.getElementById("canchor").options.length<=1)
    {
      // no anchors found, disable link to anchor feature
      document.getElementById("catype").remove(2);
    }
    changeType(atype);    
  }
  
  function validateParams()
  {
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var aProps = {};
      if (document.getElementById("catype").options[document.getElementById("catype").selectedIndex].value == "link2anchor")
        aProps.href = (document.getElementById("canchor").options[document.getElementById("canchor").selectedIndex].value)?(document.getElementById("canchor").options[document.getElementById("canchor").selectedIndex].value):'';
      else
        aProps.href = (document.getElementById("chref").value)?(document.getElementById("chref").value):'';
      aProps.name = (document.getElementById("cname").value)?(document.getElementById("cname").value):'';
      aProps.target = (document.getElementById("ctarget").value)?(document.getElementById("ctarget").value):'';
      aProps.title = (document.getElementById("ctitle").value)?(document.getElementById("ctitle").value):'';

      window.returnValue = aProps;
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
  
  
  function setTarget(target)
  {
    for (i=0; i<document.getElementById("ctarget").options.length; i++)  
    {
      tg = document.getElementById("ctarget").options.item(i);
      if (tg.value == target.toLowerCase()) {
        document.getElementById("ctarget").selectedIndex = tg.index;
      }
    }
  }

  function setAnchors(anchors, anchor)
  {
  	var found = false;
  	for(var i=0; i<anchors.length; i++)
    {
      var opt = document.createElement("OPTION");
      document.getElementById("canchor").options.add(opt);
      opt.innerText = anchors[i];
      opt.value = '#'+anchors[i];
      if (opt.value == anchor)
      {
        opt.selected = true;
        found = true;
      }
    }
    return found;
  }

  function changeType(new_type)
  {
    document.getElementById("catype").selectedIndex = 0;
    if (new_type == "anchor")
    {
      document.getElementById("catype").selectedIndex = 1;
    }
    else if (new_type == "link2anchor")
    {
      document.getElementById("catype").selectedIndex = 2;
    }

    <?php if (SPAW_Util::getBrowser() != 'Gecko') { ?>
    // doesn't work right in mozilla
    document.getElementById("url_row").style.display = new_type=="link"?"inline":"none";
  	document.getElementById("name_row").style.display = new_type=="anchor"?"inline":"none";
  	document.getElementById("anchor_row").style.display = new_type=="link2anchor"?"inline":"none";
  	document.getElementById("target_row").style.display = (new_type=="link"||new_type=="link2anchor")?"inline":"none";
  	<?php } ?>
  	
    resizeDialogToContent();
  }
  //-->
  </script>
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">
<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="a_prop">
<tr>
  <td width="50%"><?php echo $l->m('a_type')?>:</td>
  <td width="50%">
  <select name="catype" id="catype" size="1" class="input" onchange="changeType(this.options[this.selectedIndex].value);">
  	<option value="link"><?php echo $l->m('type_link')?></option>
  	<option value="anchor"><?php echo $l->m('type_anchor')?></option>
  	<option value="link2anchor"><?php echo $l->m('type_link2anchor')?></option>
  </select>
  </td>
</tr>
<tr id="url_row">
  <td width="50%"><?php echo $l->m('url')?>:</td>
  <td width="50%"><input type="text" name="chref" id="chref" class="input" size="32"></td>
</tr>
<tr id="name_row">
  <td width="50%"><?php echo $l->m('name')?>:</td>
  <td width="50%"><input type="text" name="cname" id="cname" class="input" size="32"></td>
</tr>
<tr id="anchor_row">
  <td width="50%"><?php echo $l->m('anchors')?>:</td>
  <td width="50%">
  <select name="canchor" id="canchor" size="1" class="input">
  	<option></option>
  </select>
  </td>
</tr>
<tr id="target_row">
  <td width="50%"><?php echo $l->m('target')?>:</td>
  <td width="50%" align="left">
  <select name="ctarget" id="ctarget" size="1" class="input">
    <?php
		foreach($spaw_a_targets as $key=>$value)
		{
			if ($l->m($key,'hyperlink_targets')!='') 
				$value = $l->m($key,'hyperlink_targets');
			echo '<option value="'.$key.'">'.$value."</option>";
		}
	?>
  </select>
  </td>
</tr>
<tr id="title_row">
  <td width="50%"><?php echo $l->m('title_attr')?>:</td>
  <td width="50%" align="left">
    <input type="text" name="ctitle" id="ctitle" size="32" class="input">
  </td>
</tr>
<tr>
<td colspan="2" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="2" align="right" valign="bottom" nowrap>
<input type="button" value="<?php echo $l->m('ok')?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo $l->m('cancel')?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>

</body>
</html>
