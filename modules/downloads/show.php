<?php
// Use /ext_inc/downloads
$fileCollection = new \LanSuite\FileCollection();
$BaseDir = 'ext_inc/downloads/';
$fileCollection->setRelativePath($BaseDir);
$fileCollection->setBlacklist(['.', '..', 'info.txt', '.svn', '.gitkeep', '.htaccess']);

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