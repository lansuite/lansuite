<?php

function recursiveRemoveDirectory($dir) {
  $dir = str_replace('\\', '/', $dir);
  $dir = str_replace('/..', '', $dir);

  if (is_dir($dir)) {
    $ResDir = opendir($dir);
    while ($file = readdir($ResDir)) if ($file != '.' and $file != '..') {
      if (is_dir("$dir/$file")) recursiveRemoveDirectory("$dir/$file");
      elseif (file_exists("$dir/$file")) unlink("$dir/$file");
    }
    closedir($ResDir);
  }
  rmdir($dir);
}


if (!$_GET["file"]) $_GET["file"] = "/";
$akt_dir = substr($_GET["file"], 0, strrpos($_GET["file"], '/') + 1);
$db_dir = substr($_GET["file"], 1, strlen($_GET["file"]));
$akt_file = substr($_GET["file"], strrpos($_GET["file"], '/') + 1, strlen($_GET["file"]));
$root_dir = "ext_inc/picgallery". $akt_dir;
$root_file = "ext_inc/picgallery". $_GET["file"];


$pic = $db->query_first("SELECT caption FROM {$config[tables][picgallery]} WHERE name = '$db_dir'");
if (!$pic['caption']) $pic['caption'] = "<i>".t('Unbekannt')."</i>";

switch ($_GET["step"]) {
	default:
		$func->question(t('Wollen sie das Bild <b>%1 (%2)</b> wirklich l&ouml;schen?', $pic['caption'], $_GET["file"]), "index.php?mod=picgallery&action=delete&step=2&file={$_GET["file"]}", "index.php?mod=picgallery&file=$akt_dir");
	break;
	
	case 2:
		$delete_db = $db->query("DELETE FROM {$config[tables][picgallery]} WHERE name = '$db_dir'");

		unlink($root_file);
		if(file_exists($root_dir ."lsthumb_". $akt_file)) unlink($root_dir ."lsthumb_". $akt_file);

		$func->confirmation(t('Das Bild <b>%1 (%2)</b> wurde gel&ouml;scht', $pic['caption'], $_GET["file"]), "index.php?mod=picgallery&file=$akt_dir");
	break;
	
  // Delete directory
	case 10:
		$func->question(t('Möchten Sie dieses Verzeichnis wirklich löschen? Dabei werden alle darin enthaltenen Bilder mit gelöscht!'),
      "index.php?mod=picgallery&action=delete&step=11&file={$_GET["file"]}",
      "index.php?mod=picgallery&file=$akt_dir"
      );
  break;

  case 11:
    recursiveRemoveDirectory("ext_inc/picgallery".$_GET["file"]);
    $func->confirmation(t('Das Verzeichnis wurde erfolgreich gelöscht'), 'index.php?mod=picgallery');
	break;
}
?>