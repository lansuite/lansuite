<?php
// Use /ext_inc/downloads
if (!$cfg['download_use_ftp']) {
    $BaseDir = 'ext_inc/downloads/';

  // Don't allow directories above base!
    $_GET['dir'] = str_replace('..', '', $_GET['dir']);

  // Download dialoge, if file is selected
    if (is_file($BaseDir.$_GET['dir'])) {
        $row = $db->qry_first("SELECT 1 AS found FROM %prefix%download_stats WHERE file = %string% AND DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", $_GET['dir']);
        if ($row['found']) {
            $db->qry("UPDATE %prefix%download_stats SET hits = hits + 1 WHERE file = %string% AND DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", $_GET['dir']);
        } else {
            $db->qry("INSERT INTO %prefix%download_stats SET file = %string%, hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", $_GET['dir']);
        }

    #    header('Content-type: application/octetstream'); # Others: application/octet-stream # application/force-download
    #    header('Content-Disposition: attachment; filename="'. substr($_GET['dir'], strrpos($_GET['dir'], '/') + 1, strlen($_GET['dir'])) .'"');
    #    header("Content-Length: " .(string)(filesize($BaseDir.$_GET['dir'])));
    #    readfile($BaseDir.$_GET['dir']);
        header('Location: http://'. $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']) . $BaseDir . $_GET['dir']);
          exit;

      // Display directory
    } else {
        // Generate up-links
        $Dirs = explode('/', $_GET['dir']);
        $LinkUp = '<a href="index.php?mod=downloads" class="menu">Downloads</a>';
        $LinkUpDir = '';
        $FileName = '';
        foreach ($Dirs as $val) {
            if ($val != '') {
                $LinkUpDir .= $val;
                $LinkUp .= ' - <a href="index.php?mod=downloads&dir='. $LinkUpDir .'" class="menu">'. $val .'</a>';
                $LinkUpDir .= '/';
                $FileName = $val;
            }
        }
        $dsp->NewContent(t('Downloads'), $LinkUp);

        // Display Dir-Info-Text from DB
        $row = $db->qry_first("SELECT dirid, text, allow_upload FROM %prefix%download_dirs WHERE name = %string%", $_GET['dir']);
        if (!$row['dirid'] and is_dir($BaseDir.$_GET['dir'])) {
            $db->qry("INSERT INTO %prefix%download_dirs SET name = %string%", $_GET['dir']);
            $row['dirid'] = $db->insert_id();
        }
        if ($row['text']) {
            $dsp->AddFieldSetStart(t('Ordner-Information'));
            $dsp->AddSingleRow($func->text2html($row['text']));
            $dsp->AddFieldSetEnd();
        }

        // Upload submittet file
        if ($_GET['step'] == 20 and $auth['type'] >= 2 or ($auth['login'] and $row['allow_upload'])) {
            $func->FileUpload('upload', $BaseDir.$_GET['dir']);
        }

        $dsp->AddFieldSetStart(t('Navigation: ') . $LinkUp);
        $FileList = array();
        if (file_exists($BaseDir.$_GET['dir'])) {
            $DLDesign = opendir($BaseDir.$_GET['dir']);
            if ($DLDesign) {
                while ($CurFile = readdir($DLDesign)) {
                    if ($CurFile != '.' and $CurFile != '..') {
                        $FileList[] = $CurFile;
                    }
                }
                closedir($DLDesign);
                sort($FileList);
            }
        }
          
        if ($FileList) {
            foreach ($FileList as $CurFile) {
                $CreateTime = filectime($BaseDir.'/'.$CurFilePath);

                if ($_GET['dir']) {
                    $CurFilePath = $_GET['dir'] .'/'. $CurFile;
                } else {
                    $CurFilePath = $CurFile;
                }

                if ($CurFilePath != 'info.txt' and $CurFilePath != '.svn') {
          // Dir
                    if (is_dir($BaseDir.'/'.$CurFilePath)) {
                        $dsp->AddSingleRow('<a href="index.php?mod=downloads&dir='. $CurFilePath .'" class="menu"><img src="design/'. $auth['design'] .'/images/downloads_folder.gif" border="0" /> '. $CurFile .'</a>');

                              // File
                    } else {
                        $Size = filesize($BaseDir.'/'.$CurFilePath);
                        $dsp->AddSingleRow('<a href="index.php?mod=downloads&design=base&dir='. $CurFilePath .'" class="menu"><img src="design/'. $auth['design'] .'/images/downloads_file.gif" border="0" /> '. $CurFile .' ['. $func->FormatFileSize($Size) .']'.'</a>');
                    }
                }
            }
        } else {
            $func->information('No files found in "'. ($BaseDir.$_GET['dir']) .'"');
        }

        // Links
        $res2 = $db->qry('SELECT link FROM %prefix%download_urls WHERE dir = %string%', $_GET['dir']);
        while ($row2 = $db->fetch_array($res2)) {
            $LinkName = substr($row2['link'], strrpos($row2['link'], '/') + 1, strlen($row2['link']));
            $dsp->AddSingleRow('<a href="'. $row2['link'] .'" class="menu"><img src="design/'. $auth['design'] .'/images/downloads_file.gif" border="0" /> '. $LinkName .'</a>');
        }
          $db->free_result($res2);

          $dsp->AddFieldSetEnd();

        if ($auth['type'] >= 2 or ($auth['login'] and $row['allow_upload'])) {
            // File Upload Box
            $dsp->AddFieldSetStart(t('Datei hochladen'));
            $dsp->SetForm('index.php?mod=downloads&step=20&dir='. $_GET['dir'], '', '', 'multipart/form-data');
            $dsp->AddFileSelectRow('upload', t('Datei'), '', '', '', 1);
            $dsp->AddFormSubmitRow('add');
            $dsp->AddFieldSetEnd();

          // URL Upload Box
            $dsp->AddFieldSetStart(t('URL verlinken'));
            $mf = new masterform();
            $mf->AddField(t('URL'), 'link');
            $mf->AddFix('dir', $_GET['dir']);
            $mf->SendForm('index.php?mod=downloads&dir='. $_GET['dir'], 'download_urls', 'urlid', $row['urlid']);
            $dsp->AddFieldSetEnd();
        }

        // Comments
        if ($_GET['mf_step'] != 2 or $_GET['step'] != 10) {
              new Mastercomment('downloads', $row['dirid']);
        }

        // Admin functions for dir
        if ($auth['type'] >= 2 and ($_GET['mf_step'] != 2 or $_GET['step'] == 10)) {
            $dsp->AddFieldSetStart(t('Ordner Text und Einstellungen editieren'));
            $mf = new masterform();

            $mf->AddField(t('Text'), 'text', '', LSCODE_BIG, FIELD_OPTIONAL);
            $mf->AddField(t('Benutzer-Upload erlauben?'), 'allow_upload', '', '', FIELD_OPTIONAL);
            if (!$_GET['dirid']) {
                $mf->AddFix('name', $_GET['dir']);
                $mf->AddFix('userid', $auth['userid']);
            }

            $mf->SendForm('index.php?mod=downloads&step=10&dir='. $_GET['dir'], 'download_dirs', 'dirid', $row['dirid']);
            $dsp->AddFieldSetEnd();
        }
    }
    $dsp->AddContent();


// Try to connect to FTP-Server
} elseif (!extension_loaded(ftp)) {
    $func->error(t('Die PHP-Erweiterung <b>FTP</b> konnte nicht geladen werden. &Uuml;berpr&uuml;fe, ob diese in PHP einkompiliert bzw. aktiviert ist'));
} else {
    session_register("downloads_dir");

    $server     = $cfg['download_server'];
    $port       = $cfg['download_port'];
    $loginuser    = $cfg['download_username'];
    $loginpassword  = $cfg['download_password'];
    $subdir     = $cfg['download_subdir'];

    $connect = @ftp_connect($server, $port, "2");
    $login   = @ftp_login($connect, $loginuser, $loginpassword);

    if ($cfg['sys_showdebug_ftp'] == "1") {
        $debugFTP[] = "FTP> Connect to " . $server . " on Port:" . $port;
        $debugFTP[] = "FTP> " . $connect;
        $debugFTP[] = "FTP> Login with " . $loginuser . ", " . $loginpassword;
        $debugFTP[] = "FTP> Login (true=1): " . $login;
    }

    if ($connect != false and $login != false) {
        if ($_GET[go_dir] == "up") {
            array_pop($_SESSION['downloads_dir']);
        } elseif ($_GET[go_dir]) {
            if (count($_SESSION['downloads_dir']) > "0") {
                foreach ($_SESSION['downloads_dir'] as $dir_entry) {
                    $set_dir .= "/" . $dir_entry;
                }
            }
      
            $join_dir = @ftp_chdir($connect, $subdir . $set_dir . "/" . $_GET[go_dir]);
            if ($join_dir == true and $_GET[go_dir] != "." and $_GET[go_dir] != "..") {
                $_SESSION['downloads_dir'][] = $_GET[go_dir];
            }

            unset($set_dir);
            unset($join_dir);
        }

        if (count($_SESSION['downloads_dir']) > "0") {
            foreach ($_SESSION['downloads_dir'] as $dir_entry) {
                $set_dir .= "/" . $dir_entry;
            }
        }
        if ($subdir != "" or $set_dir != "") {
            $join_dir = @ftp_chdir($connect, $subdir . $set_dir);
        }

        $dir   = ftp_pwd($connect);
        $content = ftp_rawlist($connect, $dir);

        if ($cfg['sys_showdebug_ftp'] == "1") {
            $debugFTP[] = "FTP> Set timeout ". $set_timeout;
            ;
            $debugFTP[] = "FTP> Join dir";
            $debugFTP[] = "FTP> " . $join_dir;
            $debugFTP[] = "FTP> Read dir";
            $debugFTP[] = "FTP> " . $dir;
            $debugFTP[] = "FTP> Read content";
        }

        if ($content) {
            $z = 0;
            $table = array();
            foreach ($content as $cur_line) {
                if ($cfg['sys_showdebug_ftp'] == "1") {
                    $debugFTP[] = "FTP> " . $cur_line . HTML_NEWLINE;
                }

                if (ereg("([-d])([rwxst-]{9}).* ([0-9]*) ([a-zA-Z]+[0-9: ]* [0-9]{2}:?[0-9]{2}) (.+)", $cur_line, $regs)) {
                    if ($regs[1] == "d") {
                        $lineinfo['folder'] = true;
                    } else {
                        $lineinfo['folder'] = false;
                    }
                    $lineinfo['size']   = $regs[3];
                    $lineinfo['name']   = $regs[5];
                    if ($lineinfo['size'] < "1024") {
                        $lineinfo['rounded_size'] = "< 1 KB";
                    } elseif ($lineinfo['size'] >= "1024000") {
                        $lineinfo['rounded_size'] = round($lineinfo['size'] / "1024" / "1000", "2") . " MB";
                    } else {
                        $lineinfo['rounded_size'] = round($lineinfo['size'] / "1024", "2") . " KB";
                    }

                    $lineinfo['type']   = explode(".", $lineinfo['name']);
                    if (count($lineinfo['type']) > "1") {
                        $lineinfo['datatype'] = $lineinfo['type'][count($lineinfo['type'])-1] . "-Datei";
                    }
                }
    
                if ($lineinfo['size'] > "0" and $lineinfo['folder'] != true) {
                    $loginpassword = str_replace("@", "(at)", $loginpassword);

                    if ($loginuser == "" and $loginpassword == "") {
                        $account = "";
                    } else {
                        $account = $loginuser . ":" . $loginpassword;
                    }

                    $table[$z][0]['text'] = '<img src="design/'. $auth['design'] .'/images/downloads_file.gif" border="0">';
                    $table[$z][0]['link'] = "ftp://" . $account . "@" . $server . ":" . $port . "/" .  $subdir . $set_dir . "/" . $lineinfo['name'];
                    $table[$z][0]['link_target'] = '_blank';
                    $table[$z][1]['text'] = $lineinfo['name'];
                    $table[$z][1]['link'] = "ftp://" . $account . "@" . $server . ":" . $port . "/" .  $subdir . $set_dir . "/" . $lineinfo['name'];
                    $table[$z][1]['link_target'] = '_blank';
                    $table[$z][2]['text'] = $lineinfo['datatype'];
                    $table[$z][3]['text'] = $lineinfo['rounded_size'];
                } elseif ($lineinfo['folder'] == true and $lineinfo['name'] != "." and $lineinfo['name'] != "..") {
                    $linkurl = rawurlencode($lineinfo['name']);
                    $table[$z][0]['text'] = '<img src="design/'. $auth['design'] .'/images/downloads_folder.gif" border="0">';
                    $table[$z][0]['link'] = 'index.php?mod=downloads&action=show&go_dir='. $linkurl;
                    $table[$z][1]['text'] = "<a href=\"index.php?mod=downloads&action=show&go_dir=" . $linkurl . "\" class=\"menu\" >" . $lineinfo['name'] . "</a>";
                    $table[$z][1]['link'] = 'index.php?mod=downloads&action=show&go_dir='. $linkurl;
                    $table[$z][2]['text'] = "Ordner";
                    $table[$z][3]['text'] = "&nbsp;";
                }
                unset($lineinfo);
                $z++;
            }
        } else {
            $func->information(t('Kein Inhalt vorhanden'));
        }

        $dsp->NewContent(t('Downloads'), t('Hier kannst du zum Download bereitgestellte Dateien downloaden. Ordner sind durch ein Ordner-Symbol gekennzeichnet und können per Klick auf dieses oder den Namen ge&ouml;ffnet werden. Bei &ouml;ffnen eines Unterverzeichnisses wird das aktuelle Verzeichnis am oberen Rand angezeigt. Ebenfalls angezeigt wird ein Symbol mit dem du zum nächst höhergelegenen Verzeichnis gelangst'));
        if (count($_SESSION['downloads_dir']) > "0") {
            $dsp->AddSingleRow('<a href="index.php?mod=downloads&action=show&go_dir=up"><img src="design/'. $auth['design'] .'/images/downloads_goup.gif" border="0"></a> '. $dir .'/');
        }
        $dsp->AddTableRow($table);
        $dsp->AddContent();

        $quit = ftp_quit($connect);

        $debugFTP[] = "FTP> Quit connection " . $connect. HTML_NEWLINE;
    } else {
        $func->error(t('Konnte Verbindung zum Downloadserver "%1" auf Port %2 nicht herstellen', $server, $port));
    }

    if (isset($debug) and $cfg['sys_showdebug_ftp'] == "1") {
        $debug->addvar('FTP', $debugFTP);
    }
}
