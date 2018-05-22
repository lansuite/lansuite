<?php

$xml = new \LanSuite\XML();

switch ($_GET['step']) {
    case 10:
        $row = $db->qry_first("SELECT ls_url FROM %prefix%partylist WHERE partyid = %int%", $_GET['partyid']);
        if (substr($row['ls_url'], strlen($row['ls_url']) - 1, 1) != '/') {
            $row['ls_url'] .= '/';
        }
        if (substr($row['ls_url'], 0, 7) != 'http://') {
            $row['ls_url'] = 'http://'. $row['ls_url'];
        }
        header('Location: '. $row['ls_url'] . 'index.php?mod=signon');
        exit;
    break;
}

if (!$_GET['partyid']) {
    if ($_GET['action'] == 'history') {
        $where = 'p.end < NOW()';
    } else {
        $where = 'p.end >= NOW()';
    }

    $dsp->NewContent(t('Party-Liste'), t('Partys, die Lansuite verwenden'));

    $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('');

    $ms2->query['from'] = "%prefix%partylist AS p";
    $ms2->query['where'] = $where;
    $ms2->query['default_order_by'] = 'p.start ASC';
    $ms2->config['EntriesPerPage'] = 20;

    $ms2->AddSelect('p.motto');
    $ms2->AddSelect('p.userid');
    $ms2->AddResultField(t('Partyname'), 'p.name', 'NameAndMotto');
    $ms2->AddResultField(t('Begin'), 'UNIX_TIMESTAMP(p.start) as start', 'MS2GetDate');
    $ms2->AddResultField(t('Ende'), 'UNIX_TIMESTAMP(p.end) as end', 'MS2GetDate');
    $ms2->AddResultField(t('Anmelde-Status'), 'ls_url', 'AddSignonStatus');

    $ms2->AddIconField('details', 'index.php?mod=partylist&action='. $_GET['action'] .'&partyid=', t('Details'));
    if ($_GET['action'] != 'history') {
        $ms2->AddIconField('signon', 'nofollow.php?mod=partylist&step=10&design=base&partyid=', t('Anmelden'));
    }
    $ms2->AddIconField('edit', 'index.php?mod=partylist&action=add&partyid=', t('Editieren'), 'EditAllowed');
    if ($auth['type'] >= 3) {
        $ms2->AddIconField('delete', 'index.php?mod=partylist&action=delete&partyid=', t('Löschen'));
    }

    $ms2->PrintSearch('index.php?mod=partylist&action='. $_GET['action'], 'p.partyid');
} else {
    $row = $db->qry_first("
      SELECT
        u.username,
        p.*,
        UNIX_TIMESTAMP(p.start) AS start,
        UNIX_TIMESTAMP(p.end) AS end
      FROM %prefix%partylist AS p
      LEFT JOIN %prefix%user AS u on p.userid = u.userid
      WHERE
        p.partyid = %int%", $_GET['partyid']);
    $framework->AddToPageTitle($row["name"]);

    if (substr($row['url'], 0, 7) != 'http://') {
        $row['url'] = 'http://'. $row['url'];
    }

    $dsp->NewContent($row['name'], $row['motto']);
    $dsp->AddDoubleRow(t('Datum'), $func->unixstamp2date($row['start'], 'datetime') .' bis '. $func->unixstamp2date($row['end'], 'datetime'));
    $dsp->AddDoubleRow(t('Adresse'), $row['street'] .' '. $row['hnr'] .', '. $row['plz'] .' '. $row['city']);
    $dsp->AddDoubleRow(t('Webseite'), '<a href="'. $row['url'] .'" target="_blank">'. $row['url'] .'</a> ' . $dsp->FetchIcon('signon', 'nofollow.php?mod=partylist&step=10&design=base&partyid=' . $_GET['partyid']));
    $dsp->AddDoubleRow(t('Anmeldestatus'), AddSignonStatus($row['ls_url']));
    $dsp->AddDoubleRow(t('Zusätzliche Infos'), $func->text2html($row['text']));
    $dsp->AddDoubleRow(t('Eingetragen durch'), $dsp->FetchUserIcon($row['userid'], $row['username']));

    $dsp->AddFieldsetStart('Vergangene Veranstaltungen');
    $history = AddSignonStatus($row['ls_url'], 1);
    $dsp->AddFieldsetEnd();

    $dsp->AddBackButton('index.php?mod=partylist&action='. $_GET['action']);
}
