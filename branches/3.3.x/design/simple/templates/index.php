<?php
$dsp->SetVar('title', $_SESSION['party_info']['name'] .' - lansuite '. $config['lansuite']['version']);
$dsp->SetVar('body_atr', $templ['index']['body']['js']);
$dsp->SetVar('js', $templ['index']['control']['js']);
$dsp->SetVar('DateLogout', $templ['index']['info']['current_date'] .' '. $templ['index']['info']['logout_link']);

if ($_SESSION['lansuite']['fullscreen'] or $_GET['design'] == 'popup') $dsp->SetVar('ContentStyle', 'ContentFullscreen');
else $dsp->SetVar('ContentStyle', 'Content');
if (!$_SESSION['lansuite']['fullscreen']) {
  if ($_GET['design'] != 'popup') {
    $dsp->SetVar('BoxesLeft', $templ['index']['control']['boxes_letfside']);
    $dsp->SetVar('BoxesRight', $templ['index']['control']['boxes_rightside']);
    $dsp->SetVar('Logo', '<img src="design/simple/images/logo.gif" alt="Logo" title="Lansuite" />');
    $dsp->SetVar('Debug', $func->ShowDebug());
  }
} else $dsp->SetVar('Logo', '<a href="index.php?'. $URLQuery .'&amp;fullscreen=no" class="menu" onmouseover="return overlib(\''. t('Vollbildmodus schließen') .'\');" onmouseout="return nd();"><img src="design/'. $auth['design'] .'/images/arrows_delete.gif" border="0" alt="" /></a> Lansuite - Vollbildmodus');


?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <title><?php $dsp->EchoVar('title')?></title>
    <?php $dsp->EchoTpl('design/templates/html_header.htm');
      if( $_GET['sitereload'] ) {
        echo '<meta http-equiv="refresh" content="'.$_GET['sitereload'].'; URL='.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'">';
      }
    ?>
  <link rel="stylesheet" type="text/css" href="ext_scripts/niftycube/niftyCorners.css" />
  <script type="text/javascript" src="ext_scripts/niftycube/niftycube.js"></script>
</head>


<body onload="BodyOnload(1)" <?php $dsp->EchoVar('body_atr')?>>
<?php $dsp->EchoVar('js')?>
<a name="top"></a>
<span id="LSloading" class="loading"></span>

<div id="Logo"><?php $dsp->EchoVar('Logo')?></div>
<div id="DateLogout"><?php $dsp->EchoVar('DateLogout')?></div>
<div id="Banner"><?php include_once('modules/sponsor/banner.php')?></div>

<div id="BoxesLeft"><?php $dsp->EchoVar('BoxesLeft')?></div>
<div id="<?php $dsp->EchoVar('ContentStyle')?>">
  <?php include_once('index_module.inc.php')?>
  <div id="Footer"><?php if ($_GET['design'] != 'popup') include_once('design/templates/footer.php')?></div>
  <?php $dsp->EchoVar('Debug')?>
</div>
<div id="BoxesRight"><?php $dsp->EchoVar('BoxesRight')?></div>

</body>
</html>