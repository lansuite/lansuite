<?php

$gd = new gd();

# Forbid changedir to upper directories
$_GET['file'] = str_replace('/..', '', $_GET['file']);
$_GET['file'] = str_replace('\\..', '', $_GET['file']);

$icon_dir = "ext_inc/picgallery_icon/";

function IsSupportedVideo($ext)
{
    if (($ext == "mp4") or ($ext == "mpg") or ($ext == "mpeg") or ($ext == "ogv")) {
        return true;
    } else {
        return false;
    }
}

// Returns, wheather the supplied extension is supported, or not.
function IsSupportedType($ext)
{
    $ext = strtolower($ext);
    if ((($ext == "jpeg" or $ext == "jpg") and (ImageTypes() & IMG_JPG))
    or ($ext == "png" and (ImageTypes() & IMG_PNG))
    or ($ext == "gif" and (ImageTypes() & IMG_GIF))
    or ($ext == "wbmp" and (ImageTypes() & IMG_WBMP))
    or ($ext == "bmp")
    or (IsSupportedVideo($ext))
#	or ($ext == "ico")		// Problem: "Die" in target-function + most Browsers can not display this type
#	or ($ext == "cur")		// Problem: "Die" in target-function + most Browsers can not display this type
#	or ($ext == "ani")		// Problem: "Die" in target-function + most Browsers can not display this type
    ) {
        return true;
    } else {
        return false;
    }
}

function IsPackage($ext)
{
    $ext = strtolower($ext);
    if (($ext == "zip")
    or ($ext == "tar")
    or ($ext == "rar")
    or ($ext == "ace")
    or ($ext == "gz")
    or ($ext == "bz")
    ) {
        return true;
    } else {
        return false;
    }
}

// If a new gallery should be created
if ($_POST['gallery_name']) {
    if ($cfg["picgallery_allow_user_upload"] or $auth["type"] > 1) {
        $func->CreateDir('ext_inc/picgallery/'. $_GET['file'] . $_POST['gallery_name']);
    } else {
        $func->error(t('Du bist nicht berechtigt neue Galerien anzulegen'), "index.php?mod=picgallery&file={$_GET["file"]}");
    }
}

if (!$_GET["file"]) {
    $_GET["file"] = "/";
}
$akt_dir = substr($_GET["file"], 0, strrpos($_GET["file"], '/') + 1);
$db_dir = substr($_GET["file"], 1, strlen($_GET["file"]));
$akt_file = substr($_GET["file"], strrpos($_GET["file"], '/') + 1, strlen($_GET["file"]));
$root_dir = "ext_inc/picgallery". $akt_dir;
$root_file = "ext_inc/picgallery". $_GET["file"];

$gallery_id = $_GET["galleryid"];
if (!$_GET["page"]) {
    $_GET["page"] = 0;
}

// Insert non existing entries
$row = $db->qry_first("SELECT 1 AS found FROM %prefix%picgallery WHERE name = %string%", $db_dir);
if (!$row['found']) {
    $db->qry("INSERT INTO %prefix%picgallery SET userid = '', name = %string%", $db_dir);
}

// Upload posted File
if (($cfg["picgallery_allow_user_upload"] or $auth["type"] > 1) and $_FILES["file_upload"]) {
    $extension = substr($_FILES['file_upload']['name'], strrpos($_FILES['file_upload']['name'], ".") + 1, 4);
    if (IsSupportedType($extension) || IsPackage($extension)) {
        $upload = $func->FileUpload("file_upload", $root_dir);
        $db->qry("REPLACE INTO %prefix%picgallery SET userid = %int%, name = %string%", $auth["userid"], $db_dir.$_FILES["file_upload"]["name"]);
    } else {
        $func->error("Bitte nur Grafik-Dateien hochladen (Format: Jpg, Png, Gif, Bmp)<br> oder Archive (Format: zip,ace,rar,tar,gz,bz)", "index.php?mod=picgallery");
    }
}

// Set Changed Name
if ($_POST["file_name"] and ($auth['type'] >= 2 or $cfg['picgallery_allow_user_naming'])) {
    $db->qry("UPDATE %prefix%picgallery SET caption = %string% WHERE name = %string%", $_POST["file_name"], $db_dir);
}

// GD-Check
if (!$gd->available) {
    $func->error(t('Kein GD installiert'));
} // Wenn keine Datei ausgewählt ist: Übersicht
elseif (!$akt_file) {
    unset($_SESSION['klick_reload']);
    unset($klick_reload);

    $dsp->NewContent(t('Bildergalerie') . ": ". $get_gname["caption"], $overall_entries . " " . t('Klicke auf ein Bild um das Bild in voller Größe anzuzeigen.'));

    if (!$cfg["picgallery_items_per_row"]) {
        $cfg["picgallery_items_per_row"] = 3;
    }
    if (!$cfg["picgallery_rows"]) {
        $cfg["picgallery_rows"] = 4;
    }
    if (!$cfg["picgallery_max_width"]) {
        $cfg["picgallery_max_width"] = 150;
    }
    if (!$cfg["picgallery_max_height"]) {
        $cfg["picgallery_max_height"] = 120;
    }

    // Scan Directory
    $dir_list = array();
    $file_list = array();
    $package_list = array();
    $dir_size = 0;
    $last_modified = 0;
    if (is_dir($root_dir)) {
        $handle = opendir($root_dir);
    }
    if ($handle) {
        while ($file = readdir($handle)) {
            if ($file != "." and $file != ".." and $file != ".svn" and substr($file, 0, 1) != '.') {
                if (is_dir($root_dir . $file)) {
                    array_push($dir_list, $file);
                } elseif (substr($file, 0, 8) != "lsthumb_") {
                    $extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
                    if (IsSupportedType($extension)) {
                        $dir_size += filesize($root_dir . $file);
                        $file_modified = filemtime($root_dir . $file);
                        if ($file_modified > $last_modified) {
                            $last_modified = $file_modified;
                        }
                        array_push($file_list, $file);
                    } elseif (IsPackage($extension)) {
                        $dir_size += filesize($root_dir . $file);
                        $file_modified = filemtime($root_dir . $file);
                        if ($file_modified > $last_modified) {
                            $last_modified = $file_modified;
                        }
                        array_push($package_list, $file);
                    }
                }
            }
        }
        closedir($handle);

    // Sort by Name
        sort($dir_list);
        sort($file_list);
    }
    $num_files = count($file_list);
    $num_files += count($package_list);

    // Show Directory Navigation
    $directory_selection = "";
    if ($_GET["file"] != "" and $_GET["file"] != "/" and $_GET["file"] != ".") {
        $dir_up = substr($_GET["file"], 0, strrpos($_GET["file"], '/'));
        $dir_up = substr($dir_up, 0, strrpos($dir_up, '/') + 1);
        $directory_selection .= "[<a href=\"index.php?mod=picgallery&file=$dir_up\">..</a>] ";
    }
    if ($dir_list) {
        foreach ($dir_list as $dir) {
            $DelDirLink = '';
            if ($auth['type'] > 2) {
                $DelDirLink = ' <a href="index.php?mod=picgallery&action=delete&step=10&file='.$akt_dir.$dir.'"><img src="design/'.$auth['design'].'/images/arrows_delete.gif" border="0" /></a>';
            }
            $directory_selection .= "[<a href=\"index.php?mod=picgallery&file=$akt_dir$dir/\">$dir$DelDirLink</a>] <br />";
        }
    }
    if ($directory_selection) {
        $dsp->AddDoubleRow(t('Ordner'), $directory_selection);
    }

    // Show Page-Selection
    $page_selection = "";
    $num_pages = ceil($num_files / ($cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"]));

    for ($z = 0; $z < $num_pages; $z++) {
        if ($z == $_GET["page"]) {
            $page_selection .= "[<b>$z</b>] ";
        } else {
            $page_selection .= "[<a href=\"index.php?mod=picgallery&file={$_GET["file"]}&page=$z\">$z</a>] ";
        }
    }
    if ($num_pages > 1) {
        $dsp->AddDoubleRow(t('Seite'), $page_selection);
    }

    // Show Picture-List
    if (!$file_list && !$package_list) {
        $dsp->AddSingleRow("<i>".t('Keine Bilder in diesem Ordner vorhanden')."</i>");
    } else {
        $z = 0;

        $rows = "";
        $cols = "";

        $smarty->assign('page', $_GET['page']);

        $smarty->assign('name', $key);
        if ($optional) {
            $smarty->assign('optional', '_optional');
        }

        if ($file_list) {
            foreach ($file_list as $file) {
                $z++;
                if ($z > $cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"] * $_GET["page"]
                and $z <= $cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"] * ($_GET["page"] + 1)) {
                    $thumb_path = $root_dir."lsthumb_". $file;

                    $extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));

                    $smarty->assign('file', $akt_dir . $file);

                    if (strlen($file) > 22) {
                        $file_name = substr(strtolower($file), 0, 16) ."..". substr(strtolower($file), strrpos($file, "."), 5);
                    } else {
                        $file_name = strtolower($file);
                    }
                    $smarty->assign('file_name', $file_name);

                    $pic = $db->qry_first("SELECT p.picid, p.caption, p.clicks, COUNT(*) AS comments FROM %prefix%picgallery AS p
       LEFT JOIN %prefix%comments AS c ON p.picid = c.relatedto_id
            WHERE p.name = %string% AND c.relatedto_item = 'Picgallery'
       GROUP BY p.picid
            ", $db_dir . $file);
                    ($pic['caption']) ? $caption = $pic['caption'] : $caption = "<i>Unbenannt</i>";
                    $smarty->assign('caption', $caption);

                    $smarty->assign('clicks', $dsp->HelpText($pic['clicks'], 'Angesehen') .'/'. $dsp->HelpText($pic['comments'], 'Kommentare'));
                    $smarty->assign('galleryid', $gallery_id);

                    $buttons = $dsp->FetchIcon("index.php?mod=picgallery&file=$akt_dir$file&page={$_GET["page"]}", "next", t('Bild anzeigen'));
                    if ($auth["type"] > 1) {
                        $buttons .= " ". $dsp->FetchIcon("index.php?mod=picgallery&action=delete&file=$akt_dir$file&page={$_GET["page"]}", "delete", t('Bild löschen'));
                    }
                    $smarty->assign('buttons', $buttons);

          // Videos
                    if (IsSupportedVideo($extension)) {
                        $smarty->assign('pic_width', $cfg["picgallery_max_width"]);
                        $smarty->assign('pic_height', $cfg["picgallery_max_height"]);
                        $smarty->assign('pic_src', $root_dir . $file);

                        $cols .= $smarty->fetch('modules/picgallery/templates/ls_row_gallery_spalte_vid.htm');
                              // Pics
                    } else {
                        // Wenn Thumb noch nicht generiert wurde, generieren versuchen
                        if (!file_exists($thumb_path)) {
                            $gd->CreateThumb($root_dir . $file, $thumb_path, $cfg["picgallery_max_width"], $cfg["picgallery_max_height"]);
                        }

                              // Size HTML
                        if (file_exists($thumb_path)) {
                            $pic_dimensions = GetImageSize($thumb_path);
                        }
                        if (!$pic_dimensions) {
                            $pic_dimensions[0] = $cfg["picgallery_max_width"];
                            $pic_dimensions[1] = $cfg["picgallery_max_height"];
                        }

                        $smarty->assign('pic_width', $pic_dimensions[0]);
                        $smarty->assign('pic_height', $pic_dimensions[1]);
                        $smarty->assign('pic_src', $thumb_path);

                        $cols .= $smarty->fetch('modules/picgallery/templates/ls_row_gallery_spalte.htm');
                    }

                    if ($z % $cfg["picgallery_items_per_row"] == 0) {
                        $smarty->assign('cols', $cols);
                        $rows .= $smarty->fetch('modules/picgallery/templates/ls_row_gallery_zeile.htm');
                        $cols = "";
                    }
                }
            }
        }

        // Gepackte Daten anzeigen
        if ($package_list) {
            foreach ($package_list as $package) {
                $z++;
                if ($z > $cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"] * $_GET["page"]
                and $z <= $cfg["picgallery_rows"] * $cfg["picgallery_items_per_row"] * ($_GET["page"] + 1)) {
                    $extension =  strtolower(substr($package, strrpos($package, ".") + 1, 4));

                    switch ($extension) {
                        case "ace":
                            $icon = "ace.jpg";
                            break;
                        case "rar":
                            $icon = "rar.jpg";
                            break;
                        case "zip":
                            $icon = "zip.jpg";
                            break;
                        default:
                            $icon = "zip.jpg";
                            break;
                    }

                    $thumb_path = $icon_dir . $icon;
                    if (file_exists($thumb_path)) {
                        $pic_dimensions = GetImageSize($thumb_path);
                    }
                    if (!$pic_dimensions) {
                        $pic_dimensions[0] = "100px";
                        $pic_dimensions[1] = "100px";
                    }

                    $smarty->assign('pic_width', $pic_dimensions[0]);
                    $smarty->assign('pic_height', $pic_dimensions[1]);
                    $smarty->assign('pic_src', $thumb_path);
                    $smarty->assign('file', $akt_dir . $file);

                    if (strlen($file) > 22) {
                        $file_name = substr(strtolower($package), 0, 16) ."..". substr(strtolower($package), strrpos($package, "."), 5);
                    } else {
                        $file_name = strtolower($package);
                    }
                    $smarty->assign('file_name', $file_name);

                    $pic = $db->qry_first("SELECT picid, caption, clicks FROM %prefix%picgallery WHERE name = %string%", $db_dir . $package);
                    ($pic['caption']) ? $caption = $pic['caption'] : $caption = "<i>Unbenannt</i>";
                    $smarty->assign('caption', $caption);

                    $smarty->assign('clicks', $pic['clicks']);
                    $smarty->assign('galleryid', $gallery_id);

                    $buttons = $dsp->FetchIcon("index.php?mod=picgallery&action=download&design=base&picurl=$akt_dir$package", "download", t('Bild herrunterladen'));
                    if ($auth["type"] > 1) {
                        $buttons .= " ". $dsp->FetchIcon("index.php?mod=picgallery&action=delete&file=$akt_dir$package&page={$_GET["page"]}", "delete", t('Bild l&ouml;schen'));
                    }
                    $smarty->assign('buttons', $buttons);

                    $cols .= $smarty->fetch('modules/picgallery/templates/ls_row_gallery_spalte.htm');

                    if ($z % $cfg["picgallery_items_per_row"] == 0) {
                        $smarty->assign('cols', $cols);
                        $rows .= $smarty->fetch('modules/picgallery/templates/ls_row_gallery_zeile.htm');
                        $cols = "";
                    }
                }
            }
        }

        if ($z % $cfg["picgallery_items_per_row"] != 0) {
            $smarty->assign('cols', $cols);
            $rows .= $smarty->fetch('modules/picgallery/templates/ls_row_gallery_zeile.htm');
            $cols = "";
        }

        $smarty->assign('rows', $rows);
        $dsp->AddSmartyTpl('ls_row_gallery', 'picgallery');
    }

    // Stats
    $dsp->AddDoubleRow(t('Statistiken'), "$num_files ".t('Dateien')." (". (round(($dir_size / 1024), 1)) ."kB); ".t('Letzte Änderung').": ". $func->unixstamp2date($last_modified, "datetime"));

    // Upload-Formular
    if ($cfg["picgallery_allow_user_upload"] or $auth["type"] > 1) {
        $dsp->SetForm("index.php?mod=picgallery&file={$_GET["file"]}", "", "", "multipart/form-data");
        $dsp->AddFileSelectRow("file_upload", t('Datei hochladen'), "");
        $dsp->AddFormSubmitRow(t('Hinzufügen'));

        // Add Gallery
        $dsp->SetForm("index.php?mod=picgallery&file={$_GET["file"]}", "Form2", "", "");
        $dsp->AddTextFieldRow("gallery_name", t('Neue Galerie anlegen'), "", "");
        $dsp->AddFormSubmitRow(t('Hinzufügen'));
    }

    $dsp->AddContent();



// Details
} else {
    if (!is_file($root_file)) {
        $db->qry("DELETE FROM %prefix%picgallery WHERE name =  %string% AND clicks = 0", $db_dir);
        $func->error(t('Dieses Bild ist nicht vorhanden'), "index.php?mod=picgallery");
    } else {
        if ($_GET['mcact'] == "show" or $_GET['mcact'] == "") {
            $extension =  strtolower(substr($root_file, strrpos($root_file, ".") + 1, 4));

            // Select pic data
            $pic = $db->qry_first("SELECT p.picid, p.userid, p.caption, p.clicks, u.userid, u.username
    FROM %prefix%picgallery AS p
    LEFT JOIN %prefix%user AS u ON p.userid = u.userid
    WHERE p.name = %string%
    ", $db_dir);

            if (!$_SESSION["click_reload"][$db_dir]) {
                $db->qry("UPDATE %prefix%picgallery SET clicks = clicks + 1 WHERE name = %string%", $db_dir);
                $_SESSION["click_reload"][$db_dir] = 1;
            }

            if ($pic['caption']) {
                $dsp->AddDoubleRow(t('Bildname'), $pic['caption']);
            }

      // Videos
            if (IsSupportedVideo($extension)) {
                $dsp->AddDoubleRow("", '<video width="450" height="350" src="'. $root_file .'" autobuffer autoplay controls>
          <div class="video-fallback"><br>Du benötigst einen Browser, der HTML5 unterstützt.</div>
        </video>');

                  // Pics
            } else {
                // Get pic data
                  $picinfo = GetImageSize($root_file);
                $picinfo['5'] = filesize($root_file) / 1024;

                  // Check width
                  ($picinfo['0'] > "450") ? $pic_width = "450" : $pic_width = $picinfo['0'];

                $js_full_link = "javascript:var w=window.open('$root_file','_blank','width=". ($picinfo['0'] + 10) .",height=". ($picinfo['1'] + 10) .",resizable=yes,scrollbars=yes')";

                  //					JPG						PNG						GIF						BMP
                if ($picinfo['2'] == "1" or $picinfo['2'] == "2" or $picinfo['2'] == "3" or $picinfo['2'] == "6") {
                    $dsp->AddDoubleRow("", "<a href=\"$js_full_link\"><img border=\"1\" src=\"$root_file\" width=\"$pic_width\" class=\"img\"></a>");
                }
            }

            // Define Buttons
            if (!IsPackage($extension)) {
                $dl_button = $dsp->FetchIcon($js_full_link, "fullscreen", t('Vollbild'));
            }
            $full_button = $dsp->FetchIcon("index.php?mod=picgallery&action=download&design=base&picurl={$_GET["file"]}", "download", t('Bild herrunterladen'));
            ($auth[type] > "1") ? $del_button = $dsp->FetchIcon("index.php?mod=picgallery&action=delete&file={$_GET["file"]}", "delete", t('Bild l&ouml;schen')) : $del_button = "";
            $note_button = $dsp->FetchIcon("index.php?mod=picgallery&action=download&design=base&picurl={$_GET["file"]}", "add", t('Verlinkung hinzufügen'));


            // Scan Directory
            $file_list = array();
            if (is_dir($root_dir)) {
                $handle = opendir($root_dir);
            }
            while ($file = readdir($handle)) {
                if (($file != ".") and ($file != "..") and ($file != ".svn") and (!is_dir($root_dir . $file) and substr($file, 0, 8) != "lsthumb_")) {
                    $extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
                    if (IsSupportedType($extension)) {
                        array_push($file_list, $file);
                    }
                }
            }
            closedir($handle);
            $num_files = count($file_list);
            $akt_file = array_keys($file_list, $akt_file);

            if ($file_list[$akt_file[0] - 1]) {
                $prev_button = $dsp->FetchIcon("index.php?mod=picgallery&file=$akt_dir". $file_list[$akt_file[0] - 1], "back", t('Bild zur&uuml;ck'));
            } else {
                $prev_button = "";
            }
            if ($file_list[$akt_file[0] + 1]) {
                $next_button = $dsp->FetchIcon("index.php?mod=picgallery&file=$akt_dir". $file_list[$akt_file[0] + 1], "next", t('Bild weiter'));
            } else {
                $next_button = "";
            }
            $dsp->AddDoubleRow("", "$prev_button $next_button $full_button $dl_button $del_button $note_button");

            // Change Pic-Name
            if ($auth['type'] >= 2 or $cfg['picgallery_allow_user_naming']) {
                $dsp->SetForm("index.php?mod=picgallery&file={$_GET["file"]}");
                $dsp->AddTextFieldRow("file_name", t('Bildname'), $pic['caption'], "");
                $dsp->AddFormSubmitRow(t('Editieren'));
            }

            // Show Picname
            $dsp->AddDoubleRow(t('Dateiname'), $db_dir);

            // Calculate Size-Format
            $size_format = "";
            if ($picinfo[0] == 0) {
                $verh = 0;
            } else {
                $verh = $picinfo[1] / $picinfo[0];
            }
            for ($z = 1; $z <= 16; $z++) {
                if (($verh * $z) == round(($verh * $z), 0)) {
                    $size_format = $z .":". $verh * $z;
                    break;
                }
            }
            if ($size_format == "") {
                $size_format = "100:". round($verh * 100, 1);
            }
            if (!IsPackage($extension)) {
                $dsp->AddDoubleRow(t('Bildgr&ouml;&szlig;e'), "{$picinfo['0']} x {$picinfo['1']} Pixel ($size_format); ". round($picinfo['5'], 1) ." kB");
            } else {
                $dsp->AddDoubleRow(t('Bildgr&ouml;&szlig;e'), round($picinfo['5'], 1) ." kB");
            }

            // File-Times
            $dsp->AddDoubleRow(t('Erstellt'), $func->unixstamp2date(filectime($root_file), "datetime"));
            $dsp->AddDoubleRow(t('Letzte Änderung'), $func->unixstamp2date(filemtime($root_file), "datetime"));

            // Show DB-Data to Pic
            if ($pic['username']) {
                $dsp->AddDoubleRow(t('Ersteller'), $dsp->FetchUserIcon($pic['userid'], $pic['username']));
            }
            if ($pic['clicks']) {
                $dsp->AddDoubleRow(t('Aufrufe'), $pic['clicks']);
            }

            $dsp->AddBackButton("index.php?mod=picgallery&file=$akt_dir&page={$_GET["page"]}", "picgallery");
            $dsp->AddContent();
        }

        // Mastercomment
        if ($_GET['picid']) {
            $pic['picid'] = $_GET['picid'];
        }
        new Mastercomment('Picgallery', $pic['picid']);
    }
}
