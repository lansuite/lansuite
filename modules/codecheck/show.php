<?php

$dsp->NewContent('Codecheck', '');

$menunames[1] = t('ShortOpenTags');
$menunames[2] = t('$db->query');
$menunames[3] = t('Tabs');
$menunames[4] = t('$ in $db->qry');
$dsp->AddHeaderMenu($menunames, 'index.php?mod=codecheck', $_GET['headermenuitem']);

switch ($_GET['headermenuitem']) {
    case 1:
        $dsp->AddSingleRow(t('Es sollte immer <?php statt <? verwendet werden. Anders funktioniert LS beispielsweise im IIS nicht'));
        break;
    case 2:
        $dsp->AddSingleRow(t('$db->query sollte durch das neue $qb->qry ausgetauscht werden. Dieses kann SQL-Injections sehr zuverlässig verhindern'));
        break;
    case 3:
        $dsp->AddSingleRow(t('Es sollten keine Tabs im Code verwendet werden. Statt dessen am Besten 2 Leerzeichen'));
        break;
    case 4:
        $dsp->AddSingleRow(t('Kein $ in erstem $db->qry Parameter. Statt dessen %string%, oder %int% verwenden. Zur Not %plain%'));
        break;
}
$modules = opendir('modules');
while ($mod = readdir($modules)) {
    if (is_dir("modules/$mod") and $mod != '.' and $mod != '..' and $mod != '.svn') {
        $out = '';

        $files = opendir("modules/$mod");
        while ($file = readdir($files)) {
            if (is_file("modules/$mod/$file") and substr($file, strlen($file) - 4, 4) == '.php') {
                $fh = fopen("modules/$mod/$file", 'r');
                $content = fread($fh, filesize("modules/$mod/$file"));
                fclose($fh);

                switch ($_GET['headermenuitem']) {
                    case 1:
                        if (preg_match("#<\\?[^a-z].#sUi", $content)) {
                                      $out .= "modules/$mod/$file<br />";
                        }
                        break;
                    case 2:
                        if (preg_match("#\\\$db->query#sUi", $content)) {
                                      $out .= "modules/$mod/$file<br />";
                        }
                        break;
                    case 3:
                        if (preg_match("#\t#sUi", $content)) {
                                      $out .= "modules/$mod/$file<br />";
                        }
                        break;
                    case 4:
                        if (preg_match("#\\\$db->qry(.)*\\\$(.)*(\",|',|\"\\)|'\\))#", $content)) {
                                      $out .= "modules/$mod/$file<br />";
                        }
                        break;
                }
            }
        }
        closedir($files);

        $dsp->AddDoubleRow($mod, $out);
    }
}
closedir($modules);
