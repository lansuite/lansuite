<?php
$dsp->SetVar('title', $_SESSION['party_info']['name'] .' - lansuite '. $config['lansuite']['version']);
$dsp->SetVar('body_atr', $templ['index']['body']['js']);
$dsp->SetVar('js', $templ['index']['control']['js']);
$dsp->SetVar('DateLogout', $templ['index']['info']['current_date'] .' '. $templ['index']['info']['logout_link']);
if ($_SESSION['lansuite']['fullscreen'] or $_GET['design'] == 'popup') $dsp->SetVar('ContentStyle', 'ContentFullscreen');
else $dsp->SetVar('ContentStyle', 'Content');
if (!$_SESSION['lansuite']['fullscreen']) {
  if ($_GET['design'] != 'popupb') {
    $dsp->SetVar('BoxesLeft', $templ['index']['control']['boxes_letfside']);
    $dsp->SetVar('BoxesRight', $templ['index']['control']['boxes_rightside']);
    $dsp->SetVar('Logo', '<img src="design/sunset/images/index_top_lansuite.gif" alt="Logo" title="Lansuite" border="0" />');
    $dsp->SetVar('Debug', $func->ShowDebug());
  }
} else $dsp->SetVar('Logo', '<a href="index.php?'. $URLQuery .'&fullscreen=no" class="menu" onmouseover="return overlib(\''. t('Vollbildmodus schließen') .'\');" onmouseout="return nd();"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" border="0" alt="" /></a> Lansuite - Vollbildmodus');


?>



<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?$dsp->EchoVar('title')?></title>
	<?$dsp->EchoTpl('design/templates/html_header.htm')?>
  <link rel="stylesheet" type="text/css" href="ext_scripts/niftycube/niftyCorners.css" />
  <!-- <script type="text/javascript" src="ext_scripts/niftycube/niftycube.js"></script> -->
</head>

<body onload="BodyOnload()" <?$dsp->EchoVar('body_atr')?>>
<?$dsp->EchoVar('js')?>
<a name="top"></a>
<span id="LSloading" class="loading"></span>
<? if ($_GET['design'] != 'popup') { ?>
<table width="100%" cellspacing="0" cellpadding="0" style="leftmargin: 4px; topmargin: 4px; marginwidth: 4px;marginheight: 4px; background:url(./design/sunset/images/index_top_background.gif)">
<tr>
	<td colspan="5">
	<div align="right" class="small"><?$dsp->EchoVar('DateLogout')?></div>

	<table width="100%" cellspacing="0" cellpadding="0" style="leftmargin: 4px; topmargin: 4px; marginwidth: 4px;marginheight: 4px; background:url(./design/{default_design}/images/index_top_background.gif)">
	<tr>
		<td><a href="index.php"><?$dsp->EchoVar('Logo')?></a></td>
		<td align="right" ><img src="design/{default_design}/images/index_top_advertisement.gif" width="7" height="60" alt="" /><?include_once('modules/sponsor/banner.php')?></td>
		<td align="right" valign="bottom">&nbsp;</td>
	</tr>
	</table>

	</td>
</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" style="leftmargin: 4px; topmargin: 4px; marginwidth: 4px;marginheight: 4px;">

<!-- 20er Space  //-->
<tr>
	<td colspan="5">&nbsp;</td>
</tr>

<tr>
	<!-- Block: Boxes left -->
	<td width="144" class="box_frame" valign="top"><?$dsp->EchoVar('BoxesLeft')?></td>

	<!-- 10er Space //-->
	<td><img src="design/{default_design}/images/px.gif" width="10" height="1" alt="" /></td>

	<!-- MiddleBlock //-->
	<td valign="top" width="75%">
		<table width="100%" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td style="background:url(design/{default_design}/images/menu_topic_bg.gif)" height="15" valign="top" width="1%"><img src="design/{default_design}/images/arrows_content.gif" width="15" height="12" hspace="1" vspace="0" alt="" /></td>
			<td style="background:url(design/{default_design}/images/menu_topic_bg.gif)" height="15"><img src="design/{default_design}/images/index_topic_lansuite.gif" width="78" height="7" alt="" /></td>
		</tr>
		<tr class="content">
			<td colspan="2" id="LScontent">
<? } ?>
        <div id="<?$dsp->EchoVar('ContentStyle')?>">
          <?include_once('index_module.inc.php')?>
        </div>
<? if ($_GET['design'] != 'popup') { ?>
      </td>
		</tr>
		<tr class="content">
			<td colspan="2" align="right" valign="bottom"><a href="#top"><img src="design/{default_design}/images/index_bottom_2top.gif" width="78" height="7" border="0" alt="" /></a></td>
		</tr>
		<tr>
			<td colspan="2" class="hrule"></td>
		</tr>
		</table>

		<table width="100%" cellspacing="0" cellpadding="0" align="center">
		<tr><td>
		<?$dsp->EchoVar('Debug')?>
		</td></tr>
		</table>
	</td>

	<!-- 10er Space //-->
	<td valign="top"><img src="design/{default_design}/images/px.gif" width="10" height="1" alt=""/></td>

	<!-- RightBlock //-->
	<td td width="144" class="box_frame" valign="top"><?$dsp->EchoVar('BoxesRight')?></td>
</tr>
<tr>
	<td valign="bottom" height="1"></td>
	<td colspan="3"><div align="center" class="copyright" id="LSfooter"><?if ($_GET['design'] != 'base') include_once('design/templates/footer.php')?></div></td>
	<td valign="bottom" height="1"></td>
</tr>
</table>
<br />
<? } ?>

</body>
</html>