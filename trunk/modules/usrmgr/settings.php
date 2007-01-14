<?php

function CheckAndResizeUploadPic ($AvatarName) {
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
  while ($dir = readdir($ResDesign)) if (is_dir("design/$dir") and file_exists("design/$dir/design.xml")) {
    $file = "design/$dir/design.xml";
    $ResFile = fopen ($file, "rb");
    $XMLFile = fread ($ResFile, filesize ($file));
    fclose ($ResFile);
    $DesignName = $xml->get_tag_content('name', $XMLFile);
    $selections[$DesignName] = $DesignName;
  }
  closedir($ResDesign);

  $mf->AddField(t('Design'), 'design', IS_SELECTION, $selections, FIELD_OPTIONAL);
}

if ($cfg['user_avatarupload']) $mf->AddField(t('Avatar'), 'avatar_path', IS_FILE_UPLOAD, 'ext_inc/avatare/', FIELD_OPTIONAL, CheckAndResizeUploadPic);
$mf->AddField(t('Signatur'), 'signature', '', LSCODE_BIG, FIELD_OPTIONAL);

$mf->SendForm('', 'usersettings', 'userid', $auth['userid']);
?>