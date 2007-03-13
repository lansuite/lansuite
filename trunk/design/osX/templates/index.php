<?php
$dsp->SetVar('title', $_SESSION['party_info']['name'] .' - lansuite '. $config['lansuite']['version']);
$dsp->SetVar('body_atr', $templ['index']['body']['js']);
$dsp->SetVar('js', $templ['index']['control']['js']);
$dsp->SetVar('DateLogout', $templ['index']['info']['current_date'] .' '. $templ['index']['info']['logout_link']);
if ($_SESSION['lansuite']['fullscreen'] or $_GET['design'] == 'base') $dsp->SetVar('ContentStyle', 'ContentFullscreen');
else $dsp->SetVar('ContentStyle', 'Content');
if (!$_SESSION['lansuite']['fullscreen']) {
  $dsp->SetVar('BoxesLeft', $templ['index']['control']['boxes_letfside']);
  $dsp->SetVar('BoxesRight', $templ['index']['control']['boxes_rightside']);
  if ($_GET['design'] != 'base') {
    $dsp->SetVar('Logo', '<img src="design/simple/images/logo.gif" alt="Logo" title="Lansuite" border="0" />');
    $dsp->SetVar('Debug', $func->ShowDebug());
  }
} else $dsp->SetVar('Logo', '<a href="index.php?'. $URLQuery .'&fullscreen=no" class="menu"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" border="0" alt="" /></a> Lansuite - Vollbildmodus');


?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<title><?$dsp->EchoVar('title')?></title>
	<?$dsp->EchoTpl('design/templates/html_header.htm')?>
  <link rel="stylesheet" type="text/css" href="ext_scripts/niftycube/niftyCorners.css" />
  <script type="text/javascript" src="ext_scripts/niftycube/niftycube.js"></script>
</head>


<body onload="BodyOnload()" <?$dsp->EchoVar('body_atr')?>>
<?$dsp->EchoVar('js')?>
<a name="top"></a>
<span id="LSloading" class="loading"></span>
<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
<tr>
	<td colspan="2" align="right">
		<span class="small"><?$dsp->EchoVar('DateLogout')?></span>
	</td>
</tr>
<tr>
	<td>
		<strong><a href="index.php"><?$dsp->EchoVar('Logo')?></a></strong>
	</td>
	<td align="right">
		<img src="design/osX/images/index_top_advertisement.gif" width="7" height="60" alt="" />
		<?include_once('modules/sponsor/banner.php')?>
	</td>
</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
<tr>
	<!-- LeftBlock //-->
	<td valign="top" width="155" style="padding-right:8px;text-align:left;"><?$dsp->EchoVar('BoxesLeft')?></td>

	<!-- MiddleBlock //-->
	<td valign="top" style="padding-right:8px;text-align:center;">
		<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
		<tr>
			<td valign="middle" width="8" height="18" style="background:url(design/osX/images/menu_content_tl.gif) no-repeat">&nbsp;</td>
			<td valign="middle" height="18" style="background:url(design/osX/images/menu_content_top.gif) repeat-x">&nbsp;</td>
			<td valign="middle" width="8" height="18" style="background:url(design/osX/images/menu_content_tr.gif) no-repeat">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" width="8" style="background:url(design/osX/images/menu_content_ml.gif) repeat-y"></td>
			<td valign="top" align="left" id="LScontent">
        <div id="<?$dsp->EchoVar('ContentStyle')?>">
          <?include_once('index_module.inc.php')?>
        </div>
      </td>
			<td valign="top" width="8" style="background:url(design/osX/images/menu_content_mr.gif) repeat-y"></td>
		</tr>
		<tr>
			<td valign="bottom" width="8" height="20" style="background:url(design/osX/images/menu_content_bl.gif) no-repeat">&nbsp;</td>
			<td valign="bottom" align="right" height="20" style="background:url(design/osX/images/menu_content_bottom_.gif) repeat-x">
				<a href="#top"><img src="design/osX/images/index_bottom_2top.gif" width="78" height="7" border="0" alt="" /></a>
			</td>
			<td valign="bottom" width="8" height="20"  style="background:url(design/osX/images/menu_content_br.gif) no-repeat"></td>
		</tr>
		<tr>
			<td></td>
			<td><?$dsp->EchoVar('Debug')?></td>
			<td></td>
		</tr>
		</table>
	</td>

	<!-- RightBlock -->
	<td valign="top" width="155" style="padding-left:8px;text-align:right"><?$dsp->EchoVar('BoxesRight')?></td>
</tr>
<tr>
	<td colspan="3" align="center" class="copyright" id="LSfooter"><?if ($_GET['design'] != 'base') include_once('design/templates/footer.php')?></td>
</tr>
</table>
</body>
</html>
