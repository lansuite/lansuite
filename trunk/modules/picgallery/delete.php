<?php
if (!$_GET["file"]) $_GET["file"] = "/";
$akt_dir = substr($_GET["file"], 0, strrpos($_GET["file"], '/') + 1);
$db_dir = substr($_GET["file"], 1, strlen($_GET["file"]));
$akt_file = substr($_GET["file"], strrpos($_GET["file"], '/') + 1, strlen($_GET["file"]));
$root_dir = "ext_inc/picgallery". $akt_dir;
$root_file = "ext_inc/picgallery". $_GET["file"];


$pic = $db->query_first("SELECT caption FROM {$config[tables][picgallery]} WHERE name = '$db_dir'");
if (!$pic['caption']) $pic['caption'] = "<i>{$lang['picgallery']['del_unknown']}</i>";

switch ($_GET["step"]) {
	default:
		$func->question(str_replace("%NAME%", $_GET["file"], str_replace("%CAPTION%", $pic['caption'], $lang['picgallery']['del_pic_quest'])), "index.php?mod=picgallery&action=delete&step=2&file={$_GET["file"]}", "index.php?mod=picgallery&file=$akt_dir");
	break;
	
	case 2:
		$delete_db = $db->query("DELETE FROM {$config[tables][picgallery]} WHERE name = '$db_dir'");

		unlink($root_file);
		unlink($root_dir ."lsthumb_". $akt_file);

		$func->confirmation(str_replace("%NAME%", $_GET["file"], str_replace("%CAPTION%", $pic['caption'], $lang['picgallery']['del_pic_success'])), "index.php?mod=picgallery&file=$akt_dir");
	break;
}
?>
