<?php
switch ($_GET['step']) {
    default:
        $dsp->NewContent(t('Bugtracker Export'), t('Nutze diese Funktion um einen Export der Bugtracker-Einträge zu erstellen, den sie auf lansuite.de importieren können'));
        $dsp->AddDoubleRow('', t('Bitte versuche Fehler zunächst selbst zu beheben und filter vor dem Export Probleme aus, die nicht von generellem Interesse für Lansuite sind. Es werden nur Probleme mit Status offen, oder bestätigt exportiert. Ergänze unvollständige Angaben, so gut du kannst. Danke, für die Hilfe!'));
        $dsp->SetForm('index.php?mod=bugtracker&action=export&step=2');
        $dsp->AddTextFieldRow('version', t('Version'), LANSUITE_VERSION, '');
        $dsp->AddTextFieldRow('url', t('URL'), $_SERVER['SERVER_NAME'], '');
        $dsp->AddFormSubmitRow('next');
        break;

    case 2:
        $xmlExport = new \LanSuite\XML();
        $export = new \LanSuite\Module\Install\Export($xmlExport);
        $export->LSTableHead('bugs.xml');

        $entrys = '';
        $res = $db->qry("SELECT * FROM %prefix%bugtracker WHERE state = 0 OR state = 1");
        while ($row = $db->fetch_array($res)) {
            $entry = '';
            $data = '';
            $data .= $xml->write_tag('caption', $row['caption'], 4);
            $data .= $xml->write_tag('text', $row['text'], 4);
            $data .= $xml->write_tag('version', $_POST['version'], 4);
            $data .= $xml->write_tag('url', $_POST['url'], 4);
            $data .= $xml->write_tag('priority', $row['priority'], 4);
            $data .= $xml->write_tag('date', $row['date'], 4);
            $data .= $xml->write_tag('type', $row['type'], 4);
            $data .= $xml->write_tag('module', $row['module'], 4);
            $data .= $xml->write_tag('state', $row['state'], 4);
            $entry .= $xml->write_master_tag('main', $data, 3);

            $comments = '';
            $res2 = $db->qry("SELECT date, text FROM %prefix%comments WHERE relatedto_item = 'BugEintrag' AND relatedto_id = %int%", $row['bugid']);
            while ($row2 = $db->fetch_array($res2)) {
                $comment = '';
                $comment .= $xml->write_tag('text', $row2['text'], 5);
                $comment .= $xml->write_tag('date', $row2['date'], 5);
                $comments .= $xml->write_master_tag('comment', $comment, 4);
            }
            $db->free_result($res2);
            $entry .= $xml->write_master_tag('comments', $comments, 3);
            $entrys .= $xml->write_master_tag('entry', $entry, 2);
        }
        $db->free_result($res);

        $export->lansuite .= $xml->write_master_tag('entrys', $entrys, 1);
        $export->LSTableFoot();
        break;
}
