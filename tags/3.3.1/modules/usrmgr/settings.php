<?php

function CheckAndResizeUploadPic($AvatarName) {
  global $gd;

  if ($AvatarName == '') return false;
  $FileEnding = strtolower(substr($AvatarName, strrpos($AvatarName, '.'), 5));
  if ($FileEnding != '.png' and $FileEnding != '.gif' and $FileEnding != '.jpg' and $FileEnding != '.jpeg') return t('Bitte eine Grafikdatei auswählen');

  $gd->CreateThumb($AvatarName, $AvatarName, 100, 100);
  return false;
}


include_once('inc/classes/class_masterform.php');
$mf = new masterform();

// Designs
if ($cfg['user_design_change']) {
  $selections = array();
  #$selections[''] = t('Standard Design');

  $ResDesign = opendir('design/');
  while ($dir = readdir($ResDesign)) if (is_dir("design/$dir") and file_exists("design/$dir/design.xml") and ($dir != 'beamer')) {
    $file = "design/$dir/design.xml";
    $ResFile = fopen ($file, "rb");
    $XMLFile = fread ($ResFile, filesize ($file));
    fclose ($ResFile);
    $DesignName = $xml->get_tag_content('name', $XMLFile);
    $selections[$dir] = $DesignName;
  }
  closedir($ResDesign);

  $mf->AddField(t('Design'), 'design', IS_SELECTION, $selections, FIELD_OPTIONAL);
}

$mf->AddField(t('Mich auf der Karte zeigen') .'|'. t('Meine Adresse in der Besucherkarte anzeigen?'), 'show_me_in_map', '', '', FIELD_OPTIONAL);
$mf->AddField(t('LS-Mail Alert') .'|'. t('Mir eine E-Mail senden, wenn eine neue LS-Mail eingegangen ist'), 'lsmail_alert', '', '', FIELD_OPTIONAL);

if ($cfg['user_avatarupload']) $mf->AddField(t('Avatar'), 'avatar_path', IS_FILE_UPLOAD, 'ext_inc/avatare/'. $auth['userid'] .'_', FIELD_OPTIONAL, 'CheckAndResizeUploadPic');
$mf->AddField(t('Signatur'), 'signature', '', LSCODE_ALLOWED, FIELD_OPTIONAL);

$mf->SendForm('', 'usersettings', 'userid', $auth['userid']);
?>
