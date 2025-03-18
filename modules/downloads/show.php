<?php
// Use /ext_inc/downloads
if (!array_key_exists('download_use_ftp', $cfg) || !$cfg['download_use_ftp'] ) {
    $fileCollection = new \LanSuite\FileCollection();
    $BaseDir = 'ext_inc/downloads/';
    $fileCollection->setRelativePath($BaseDir);

    $dirParameter = $_GET['dir'] ?? '';

    // Download dialog, if file is selected
    if ($fileCollection->exists($dirParameter) && is_file($fileCollection->getFullPath($dirParameter))) {
        $row = $database->queryWithOnlyFirstRow("SELECT 1 AS found FROM %prefix%download_stats WHERE file = ? AND DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", [$dirParameter]);
        if ($row['found']) {
            $database->query("UPDATE %prefix%download_stats SET hits = hits + 1 WHERE file = ? AND DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", [$dirParameter]);
        } else {
            $database->query("INSERT INTO %prefix%download_stats SET file = ?, hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", [$dirParameter]);
        }

        header('Location: http://'. $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']) . $BaseDir . $dirParameter);
        exit;

      // Display directory
    } else {
        // Generate up-links
        $Dirs = explode('/', $dirParameter);
        $LinkUp = '<a href="index.php?mod=downloads" class="menu">Downloads</a>';
        $LinkUpDir = '';
        $FileName = '';
        foreach ($Dirs as $val) {
            if ($val != '') {
                $LinkUpDir .= $val;
                $LinkUp .= ' >> <a href="index.php?mod=downloads&dir='. $LinkUpDir .'" class="menu">'. $val .'</a>';
                $LinkUpDir .= '/';
                $FileName = $val;
            }
        }
        $dsp->NewContent(t('Downloads'), $LinkUp);

        // Display Dir-Info-Text from DB
        $row = $database->queryWithOnlyFirstRow("SELECT dirid, text, allow_upload FROM %prefix%download_dirs WHERE name = ?", [$dirParameter]);
        if (!$row['dirid'] and is_dir($BaseDir.$dirParameter)) {
            $db->qry("INSERT INTO %prefix%download_dirs SET name = %string%", $dirParameter);
            $row['dirid'] = $db->insert_id();
        }
        if ($row['text']) {
            $dsp->AddFieldSetStart(t('Ordner-Information'));
            $dsp->AddSingleRow($func->text2html($row['text']));
            $dsp->AddFieldSetEnd();
        }

        // Upload submitted file
        $stepParameter = $_GET['step'] ?? 0;
        if ($stepParameter == 20 && $auth['type'] >= \LS_AUTH_TYPE_ADMIN || ($auth['login'] && $row['allow_upload'])) {
            $func->FileUpload('upload', $BaseDir.$dirParameter);
        }

        $dsp->AddFieldSetStart(t('Navigation: ') . $LinkUp);
        $FileList = array();
        if (file_exists($BaseDir.$dirParameter)) {
            $DLDesign = opendir($BaseDir.$dirParameter);
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
                if ($dirParameter) {
                    $CurFilePath = $dirParameter .'/'. $CurFile;
                } else {
                    $CurFilePath = $CurFile;
                }

                if ($CurFilePath != 'info.txt' and $CurFilePath != '.svn' and $CurFilePath !='.gitkeep' and $CurFilePath !='.htaccess') {
                    // Directory
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
            $func->information('No files found in "'. ($BaseDir.$dirParameter) .'"');
        }

        // Links
        $res2 = $db->qry('SELECT link FROM %prefix%download_urls WHERE dir = %string%', $dirParameter);
        while ($row2 = $db->fetch_array($res2)) {
            $dsp->AddSingleRow('<a href="'. $row2['link'] .'" class="menu"><img src="design/'. $auth['design'] .'/images/downloads_file.gif" border="0" /> '. $row2['link'] .'</a>');
        }
        $db->free_result($res2);

        $dsp->AddFieldSetEnd();

        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN or ($auth['login'] and $row['allow_upload'])) {
            // File Upload Box
            $dsp->AddFieldSetStart(t('Datei hochladen'));
            $dsp->SetForm('index.php?mod=downloads&step=20&dir='. $dirParameter, '', '', 'multipart/form-data');
            $dsp->AddFileSelectRow('upload', t('Datei'), '', '', '', 1);
            $dsp->AddFormSubmitRow('add');
            $dsp->AddFieldSetEnd();

            // URL Upload Box
            $dsp->AddFieldSetStart(t('URL verlinken'));
            $mf = new \LanSuite\MasterForm();
            $mf->AddField(t('URL'), 'link');
            $mf->AddFix('dir', $dirParameter);
            $mf->SendForm('index.php?mod=downloads&dir=' . $dirParameter, 'download_urls', 'urlid', 0);
            $dsp->AddFieldSetEnd();
        }

        // Comments
        $stepParameter = $_GET['step'] ?? 0;
        $masterFormStepParameter = $_GET['mf_step'] ?? 0;
        if ($masterFormStepParameter != 2 || $stepParameter != 10) {
              new \LanSuite\MasterComment('downloads', $row['dirid']);
        }

        // Admin functions for dir
        if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN && ($masterFormStepParameter != 2 || $stepParameter == 10)) {
            $dsp->AddFieldSetStart(t('Ordner Text und Einstellungen editieren'));
            $mf = new \LanSuite\MasterForm();

            $mf->AddField(t('Text'), 'text', '', \LanSuite\MasterForm::LSCODE_BIG, \LanSuite\MasterForm::FIELD_OPTIONAL);
            $mf->AddField(t('Benutzer-Upload erlauben?'), 'allow_upload', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

            $dirIDParameter = $_GET['dirid'] ?? null;
            if (!$dirIDParameter) {
                $mf->AddFix('name', $dirParameter);
                $mf->AddFix('userid', $auth['userid']);
            }

            $mf->SendForm('index.php?mod=downloads&step=10&dir='. $dirParameter, 'download_dirs', 'dirid', $row['dirid']);
            $dsp->AddFieldSetEnd();
        }
    }

// Try to connect to FTP-Server
} elseif (!extension_loaded('ftp')) {
    $func->error(t('Die PHP-Erweiterung <b>FTP</b> konnte nicht geladen werden. &Uuml;berpr&uuml;fe, ob diese in PHP einkompiliert bzw. aktiviert ist'));
} else {
    $server         = $cfg['download_server'];
    $port           = $cfg['download_port'];
    $loginuser      = $cfg['download_username'];
    $loginpassword  = $cfg['download_password'];
    $subdir         = $cfg['download_subdir'];

    $connect = @ftp_connect($server, $port, "2");
    $login   = @ftp_login($connect, $loginuser, $loginpassword);

    if ($cfg['sys_showdebug_ftp'] == "1") {
        $debugFTP[] = "FTP> Connect to " . $server . " on Port:" . $port;
        $debugFTP[] = "FTP> " . $connect;
        $debugFTP[] = "FTP> Login with " . $loginuser . ", " . $loginpassword;
        $debugFTP[] = "FTP> Login (true=1): " . $login;
    }

    if ($connect != false and $login != false) {
        if ($_GET['go_dir'] == "up") {
            array_pop($_SESSION['downloads_dir']);
        } elseif ($_GET['go_dir']) {
            if ((is_countable($_SESSION['downloads_dir']) ? count($_SESSION['downloads_dir']) : 0) > "0") {
                foreach ($_SESSION['downloads_dir'] as $dir_entry) {
                    $set_dir .= "/" . $dir_entry;
                }
            }

            $join_dir = @ftp_chdir($connect, $subdir . $set_dir . "/" . $_GET['go_dir']);
            if ($join_dir == true and $_GET['go_dir'] != "." and $_GET['go_dir'] != "..") {
                $_SESSION['downloads_dir'][] = $_GET['go_dir'];
            }

            unset($set_dir);
            unset($join_dir);
        }

        if ((is_countable($_SESSION['downloads_dir']) ? count($_SESSION['downloads_dir']) : 0) > "0") {
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

                if (preg_match("([-d])([rwxst-]{9}).* ([0-9]*) ([a-zA-Z]+[0-9: ]* [0-9]{2}:?[0-9]{2}) (.+)", $cur_line, $regs)) {
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
        if ((is_countable($_SESSION['downloads_dir']) ? count($_SESSION['downloads_dir']) : 0) > "0") {
            $dsp->AddSingleRow('<a href="index.php?mod=downloads&action=show&go_dir=up"><img src="design/'. $auth['design'] .'/images/downloads_goup.gif" border="0"></a> '. $dir .'/');
        }
        $dsp->AddTableRow($table);

        ftp_close($connect);

        $debugFTP[] = "FTP> Quit connection " . $connect. HTML_NEWLINE;
    } else {
        $func->error(t('Konnte Verbindung zum Downloadserver "%1" auf Port %2 nicht herstellen', $server, $port));
    }

    if (isset($debug) and $cfg['sys_showdebug_ftp'] == "1") {
        $debug->addvar('FTP', $debugFTP);
    }
}
