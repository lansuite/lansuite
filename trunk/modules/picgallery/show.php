<?php

/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		query.php
*	Module: 		Picgallery
*	Main editor: 		johannes@one-network.org
*	Last change: 		06.01.2003 17:43
*	Description: 		 
*	Remarks: 		
*
***************************************************************************/

// Returns, wheather the supplied extension is supported, or not.
function IsSupportedType($ext) {
	$ext = strtolower($ext);
	if ((($ext == "jpeg" or $ext == "jpg") and (ImageTypes() & IMG_JPG))
	or ($ext == "png" and (ImageTypes() & IMG_PNG))
	or ($ext == "gif" and (ImageTypes() & IMG_GIF))
	or ($ext == "wbmp" and (ImageTypes() & IMG_WBMP))
	or ($ext == "bmp")
#	or ($ext == "ico")		// Problem: "Die" in target-function + most Browsers can not display this type
#	or ($ext == "cur")		// Problem: "Die" in target-function + most Browsers can not display this type
#	or ($ext == "ani")		// Problem: "Die" in target-function + most Browsers can not display this type
	) return true;
	else return false;
}

// If a new gallery should be created
if ($_POST['gallery_name']) {
	if ($cfg["picgallery_allow_user_upload"] or $auth["type"] > 1) {
	  $func->CreateDir('ext_inc/picgallery/'. $_GET['file'] . $_POST['gallery_name']);
	} else $func->error($lang['picgallery']['err_add_gallery_denied'], "index.php?mod=picgallery&file={$_GET["file"]}");
}

if (!$_GET["file"]) $_GET["file"] = "/";
$akt_dir = substr($_GET["file"], 0, strrpos($_GET["file"], '/') + 1);
$db_dir = substr($_GET["file"], 1, strlen($_GET["file"]));
$akt_file = substr($_GET["file"], strrpos($_GET["file"], '/') + 1, strlen($_GET["file"]));
$root_dir = "ext_inc/picgallery". $akt_dir;
$root_file = "ext_inc/picgallery". $_GET["file"];

$gallery_id = $_GET["galleryid"];
if (!$_GET["page"]) $_GET["page"] = 0;

// Upload posted File
if  (($cfg["picgallery_allow_user_upload"] or $auth["type"] > 1) and $_FILES["file_upload"]) {
	if (IsSupportedType(substr($_FILES['file_upload']['name'], strrpos($_FILES['file_upload']['name'], ".") + 1, 4))) {
		$upload = $func->FileUpload("file_upload", $root_dir);
		$db->query("REPLACE INTO {$config["tables"]["picgallery"]} SET userid = '{$auth["userid"]}', name = '$db_dir{$_FILES["file_upload"]["name"]}'");
	} else $func->error("Bitte nur Grafik-Dateien hochladen (Format: Jpg, Png, Gif, Bmp)", "index.php?mod=picgallery");
}

// Set Changed Name
if ($_POST["file_name"]) $db->query("UPDATE {$config["tables"]["picgallery"]} SET caption = '{$_POST["file_name"]}' WHERE name = '$db_dir'");

// GD-Check
if (!$gd->available) $func->error($lang['picgallery']['no_gd'], "");

// Wenn keine Datei ausgewählt ist: Übersicht
elseif (!$akt_file) {
	session_unregister("klick_reload");
	unset($klick_reload);

	$dsp->NewContent($lang['picgallery']['pic_show_caption'] . ": ". $get_gname["caption"], $overall_entries . " " . $lang['picgallery']['pic_show_subcaption'], "picgallery/help");

	if (!$cfg["picgallery_items_per_row"]) $cfg["picgallery_items_per_row"] = 3;
	if (!$cfg["picgallery_rows"]) $cfg["picgallery_rows"] = 4;
	if (!$cfg["picgallery_max_width"]) $cfg["picgallery_max_width"] = 150;
	if (!$cfg["picgallery_max_height"]) $cfg["picgallery_max_height"] = 120;

	// Scan Directory
	$dir_list = array();
	$file_list = array();
	$dir_size = 0;
	$last_modified = 0;
	if (is_dir($root_dir)) $handle = opendir($root_dir);
	while ($file = readdir ($handle)) if (($file != ".") and ($file != "..") and ($file != "CVS")) {
		if (is_dir($root_dir . $file)) array_push($dir_list, $file);
		elseif (substr($file, 0, 8) != "lsthumb_") {
			$extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
			if (IsSupportedType($extension)) {
				$dir_size += filesize($root_dir . $file);
				$file_modified = filemtime($root_dir . $file);
				if ($file_modified > $last_modified) $last_modified = $file_modified;
				array_push($file_list, $file);
			}
		}
	}
	closedir($handle);
	$num_files = count($file_list);

	// Show Directory Navigation
	$directory_selection = "";
	if ($_GET["file"] != "" and $_GET["file"] != "/" and $_GET["file"] != ".") {
		$dir_up = substr($_GET["file"], 0, strrpos($_GET["file"], '/'));
		$dir_up = substr($dir_up, 0, strrpos($dir_up, '/') + 1);
		$directory_selection .= "[<a href=\"index.php?mod=picgallery&file=$dir_up\">..</a>] ";
	}
	if ($dir_list) foreach($dir_list as $dir) $directory_selection .= "[<a href=\"index.php?mod=picgallery&file=$akt_dir$dir/\">$dir</a>] ";
	if ($directory_selection) $dsp->AddDoubleRow($lang['picgallery']['show_dir'], $directory_selection);

	// Show Page-Selection
	$page_selection = "";
	$num_pages = ceil($num_files / ($cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"]));

	for ($z = 0; $z < $num_pages; $z++) {
		if ($z == $_GET["page"]) $page_selection .= "[<b>$z</b>] ";
		else $page_selection .= "[<a href=\"index.php?mod=picgallery&file={$_GET["file"]}&page=$z\">$z</a>] ";
	}
	if ($num_pages > 1) $dsp->AddDoubleRow($lang['picgallery']['show_page'], $page_selection);

	// Show Picture-List
	if (!$file_list) $dsp->AddSingleRow("<i>{$lang['picgallery']['show_no_pic_dir']}</i>");
	else {
		$z = 0;

		$templ['ls']['row']['gallery']['zeile'] = "";
		$templ['ls']['row']['gallery']['spalte'] = "";

		$templ['ls']['row']['gallery']['name'] = $key;
		if($optional) $templ['ls']['row']['gallery']['optional'] = "_optional";

		foreach($file_list as $file) {
			$z++;
			if ($z > $cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"] * $_GET["page"]
			and $z <= $cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"] * ($_GET["page"] + 1)) {

				$thumb_path = $root_dir ."lsthumb_". $file;

				// Wenn Thumb noch nicht generiert wurde, generieren versuchen
				if (!file_exists($thumb_path)) $gd->CreateThumb($root_dir . $file, $thumb_path, $cfg["picgallery_max_width"], $cfg["picgallery_max_height"]);

				// Size HTML
				if (file_exists($thumb_path)) $pic_dimensions = GetImageSize($thumb_path);
				if (!$pic_dimensions) {
					$pic_dimensions[0] = $cfg["picgallery_max_width"];
					$pic_dimensions[1] = $cfg["picgallery_max_height"];
				}

				$templ['ls']['row']['gallery']['pic_width'] = $pic_dimensions[0];
				$templ['ls']['row']['gallery']['pic_height'] = $pic_dimensions[1];

				$templ['ls']['row']['gallery']['pic_src'] = $thumb_path;
				$templ['ls']['row']['gallery']['file'] = $akt_dir . $file;
				if (strlen($file) > 22) $templ['ls']['row']['gallery']['file_name'] = substr(strtolower($file), 0, 16) ."..". substr(strtolower($file), strrpos($file, "."), 5);
				else $templ['ls']['row']['gallery']['file_name'] = strtolower($file);

				$pic = $db->query_first("SELECT picid, caption, clicks FROM {$config["tables"]["picgallery"]} WHERE name = '$db_dir$file'");
				($pic['caption']) ? $templ['ls']['row']['gallery']['caption'] = $pic['caption']
					: $templ['ls']['row']['gallery']['caption'] = "<i>Unbenannt</i>";
				$templ['ls']['row']['gallery']['clicks'] = $pic['clicks'];

				$templ['ls']['row']['gallery']['galleryid'] = $gallery_id;

				$templ['ls']['row']['gallery']['buttons'] = $dsp->FetchButton("index.php?mod=picgallery&file=$akt_dir$file&page={$_GET["page"]}", "open", $lang['picgallery']['show_show_pic']);
				if ($auth["type"] > 1) {
					$templ['ls']['row']['gallery']['buttons'] .= " ". $dsp->FetchButton("index.php?mod=picgallery&action=delete&file=$akt_dir$file&page={$_GET["page"]}", "delete", $lang['picgallery']['show_del_pic']);
				}

				$templ['ls']['row']['gallery']['spalte'] .= $dsp->FetchModTpl("picgallery", "ls_row_gallery_spalte");

				if ($z % $cfg["picgallery_items_per_row"] == 0) {
					$templ['ls']['row']['gallery']['zeile'] .= $dsp->FetchModTpl("picgallery", "ls_row_gallery_zeile");
					$templ['ls']['row']['gallery']['spalte'] = "";
				}
			}
		}

		if ($z % $cfg["picgallery_items_per_row"] != 0) {
			$templ['ls']['row']['gallery']['zeile'] .= $dsp->FetchModTpl("picgallery", "ls_row_gallery_zeile");
			$templ['ls']['row']['gallery']['spalte'] = "";
		}

		$dsp->AddModTpl("picgallery", "ls_row_gallery");
	}

	// Stats
	$dsp->AddDoubleRow($lang['picgallery']['show_stats'], "$num_files {$lang['picgallery']['show_files']} (". (round(($dir_size / 1024), 1)) ."kB); {$lang['picgallery']['show_last_change']}: ". $func->unixstamp2date($last_modified, "datetime"));

	// Upload-Formular
	if ($cfg["picgallery_allow_user_upload"] or $auth["type"] > 1) {
		$dsp->SetForm("index.php?mod=picgallery&file={$_GET["file"]}", "", "", "multipart/form-data");
		$dsp->AddFileSelectRow("file_upload", $lang['picgallery']['show_upload_file'], "");
		$dsp->AddFormSubmitRow("add");

		// Add Gallery
		$dsp->SetForm("index.php?mod=picgallery&file={$_GET["file"]}", "Form2", "", "");
		$dsp->AddTextFieldRow("gallery_name", $lang['picgallery']['add_gallery'], "", "");
		$dsp->AddFormSubmitRow("add");
	}

	$dsp->AddContent();



// Details
} else {
	if (!is_file($root_file)) $func->error($lang['picgallery']['is_no_pic'], "index.php?mod=picgallery");
	else {

		if ($_GET['mcact'] == "show" or $_GET['mcact'] == ""){
			// Get pic data
			$picinfo = GetImageSize($root_file);
			$picinfo['5'] = filesize($root_file) / 1024;

			// Check width
			($picinfo['0'] > "450") ? $pic_width = "450" : $pic_width = $picinfo['0'];

			$js_full_link = "javascript:var w=window.open('$root_file','_blank','width=". ($picinfo['0'] + 10) .",height=". ($picinfo['1'] + 10) .",resizable=yes,scrollbars=yes')";

			// Select pic data
			$pic = $db->query_first("SELECT	p.picid, p.userid, p.caption, p.clicks, u.username
				FROM {$config["tables"]["picgallery"]} AS p
				LEFT JOIN {$config["tables"]["user"]} AS u ON p.userid = u.userid
				WHERE p.name = '$db_dir'
				");

			if (!$_SESSION["click_reload"][$db_dir]) {
				$db->query("UPDATE {$config["tables"]["picgallery"]} SET clicks = clicks + 1 WHERE name = '$db_dir'");
				$_SESSION["click_reload"][$db_dir] = 1;
			}

			if ($pic['caption']) $dsp->AddDoubleRow($lang['picgallery']['pic_name'], $pic['caption']);

			//					JPG						PNG						GIF						BMP
			if ($picinfo['2'] == "1" or $picinfo['2'] == "2" or $picinfo['2'] == "3" or $picinfo['2'] == "6")
				$dsp->AddDoubleRow("", "<a href=\"$js_full_link\"><img border=\"1\" src=\"$root_file\" width=\"$pic_width\" class=\"img\"></a>");

			// Define Buttons
			$dl_button = $dsp->FetchButton($js_full_link, "fullscreen", $lang['picgallery']['show_fullscreen']);
			$full_button = $dsp->FetchButton("base.php?mod=pic_download&picurl={$_GET["file"]}", "download", $lang['picgallery']['show_download_pic']);
			($auth[type] > "1") ? $del_button = $dsp->FetchButton("index.php?mod=picgallery&action=delete&file={$_GET["file"]}", "delete", $lang['picgallery']['show_del_pic']) : $del_button = "";

			// Scan Directory
			$file_list = array();
			if (is_dir($root_dir)) $handle = opendir($root_dir);
			while ($file = readdir ($handle))
				if (($file != ".") and ($file != "..") and ($file != "CVS") and (!is_dir($root_dir . $file) and substr($file, 0, 8) != "lsthumb_")) {
					$extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
					if (IsSupportedType($extension)) array_push($file_list, $file);
				}
			closedir($handle);
			$num_files = count($file_list);
			$akt_file = array_keys($file_list, $akt_file);

			if ($file_list[$akt_file[0] - 1]) $prev_button = $dsp->FetchButton("index.php?mod=picgallery&file=$akt_dir". $file_list[$akt_file[0] - 1], "back", $lang['picgallery']['show_pic_back']);
			else $prev_button = "";
			if ($file_list[$akt_file[0] + 1]) $next_button = $dsp->FetchButton("index.php?mod=picgallery&file=$akt_dir". $file_list[$akt_file[0] + 1], "next", $lang['picgallery']['show_pic_next']);
			else $next_button = "";
			$dsp->AddDoubleRow("", "$prev_button $next_button $full_button $dl_button $del_button");

			// Change Pic-Name
			$dsp->SetForm("index.php?mod=picgallery&file={$_GET["file"]}");
			$dsp->AddTextFieldRow("file_name", $lang['picgallery']['pic_name'], $pic['caption'], "");
			$dsp->AddFormSubmitRow("edit");


			// Show Picname
			$dsp->AddDoubleRow($lang['picgallery']['show_file_name'], $db_dir);

			// Calculate Size-Format
			$size_format = "";
			if ($picinfo[0] == 0) $verh = 0;
			else $verh = $picinfo[1] / $picinfo[0];
			for ($z = 1; $z <= 16; $z++){
				if (($verh * $z) == round(($verh * $z), 0)) {
					$size_format = $z .":". $verh * $z;
					break;
				}
			}
			if ($size_format == "") $size_format = "100:". round($verh * 100, 1);
			$dsp->AddDoubleRow($lang['picgallery']['show_pic_size'], "{$picinfo['0']} x {$picinfo['1']} Pixel ($size_format); ". round($picinfo['5'], 1) ." kB");

			// File-Times
			$dsp->AddDoubleRow($lang['picgallery']['show_created'], $func->unixstamp2date(filectime($root_file), "datetime"));
			$dsp->AddDoubleRow($lang['picgallery']['show_last_change'], $func->unixstamp2date(filemtime($root_file), "datetime"));

			// Show DB-Data to Pic
			if ($pic['username']) $dsp->AddDoubleRow($lang['picgallery']['owner'], "{$pic['username']} <a href=\"index.php?mod=usrmgr&action=details&userid={$pic['userid']}\"><img src=\"design/{$auth["design"]}/images/arrows_user.gif\" width=\"12\" height=\"13\" hspace=\"1\" vspace=\"0\" border=\"0\"></a>");
			if ($pic['clicks']) $dsp->AddDoubleRow($lang['picgallery']['clicks'], $pic['clicks']);

			$dsp->AddBackButton("index.php?mod=picgallery&file=$akt_dir&page={$_GET["page"]}", "picgallery");
			$dsp->AddContent();
		}

		// Mastercomment
		if ($_GET['picid']) $pic['picid'] = $_GET['picid'];
		include("modules/mastercomment/class_mastercomment.php");
		$comment = new Mastercomment($vars, "index.php?mod=picgallery&file={$_GET["file"]}&page={$_GET["page"]}&picid=" . $pic['picid'], "Picgallery", $pic['picid'], $pic['caption']);				
		$comment->action();
	}
}
?>
