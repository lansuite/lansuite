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
  if ($_GET['design'] != 'base') $dsp->SetVar('Logo', '<img src="design/simple/images/logo.gif" alt="Logo" title="Lansuite" />');
} else $dsp->SetVar('Logo', '<a href="index.php?'. $URLQuery .'&fullscreen=no" class="menu"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" border="0" alt="" /></a> Lansuite - Vollbildmodus');


?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<title><?$dsp->EchoVar('title')?></title>
	<?$dsp->EchoTpl('html_header')?>
  <link rel="stylesheet" type="text/css" href="ext_scripts/niftycube/niftyCorners.css" />
  <script type="text/javascript" src="ext_scripts/niftycube/niftycube.js"></script>
</head>


<body onload="BodyOnload()" <?$dsp->EchoVar('body_atr')?>>
<?$dsp->EchoVar('js')?>
<a name="top"></a>
<span id="LSloading" class="loading"></span>

<div id="Logo"><?$dsp->EchoVar('Logo')?></div>
<div id="DateLogout"><?$dsp->EchoVar('DateLogout')?></div>
<div id="Banner"><?include_once('modules/sponsor/banner.php')?></div>

<div id="BoxesLeft"><?$dsp->EchoVar('BoxesLeft')?></div>
<div id="<?$dsp->EchoVar('ContentStyle')?>">
  <?include_once('index_module.inc.php')?>
  <div id="Footer"><?if ($_GET['design'] != 'base') include_once('design/templates/footer.php')?></div>
</div>
<div id="BoxesRight"><?$dsp->EchoVar('BoxesRight')?></div>

</body>
</html>