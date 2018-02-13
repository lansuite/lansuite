<?php

switch ($_GET['step']) {
    case 10:
        $md = new masterdelete();
        $md->MultiDelete('comments', 'commentid');
        break;
}

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "%prefix%comments AS c
  LEFT JOIN %prefix%user AS u ON u.userid = c.creatorid";
$ms2->query['default_order_by'] = 'c.date DESC';
$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField(t('Kommentar'), array('c.text' => 'like'));

$list = array('' => t('Alle'), '0' => t('System'));
$res = $db->qry('SELECT u.userid, u.username FROM %prefix%comments AS c
  LEFT JOIN %prefix%user AS u ON u.userid = c.creatorid
  GROUP BY c.creatorid');
while ($row = $db->fetch_array($res)) {
    if ($row['userid']) {
        $list[$row['userid']] = $row['username'];
    }
}
$db->free_result($res);
$ms2->AddTextSearchDropDown(t('Auslöser'), 'c.creatorid', $list);

$list = array('' => t('Alle'));
$row = $db->qry('SELECT relatedto_item FROM %prefix%comments GROUP BY relatedto_item');
while ($res = $db->fetch_array($row)) {
    if ($res['relatedto_item']) {
        $list[$res['relatedto_item']] = $res['relatedto_item'];
    }
}
$db->free_result($row);
$ms2->AddTextSearchDropDown(t('Modul'), 'c.relatedto_item', $list);

$list = array('' => t('Alle'));
$row = $db->qry('SELECT relatedto_id FROM %prefix%comments GROUP BY relatedto_id');
while ($res = $db->fetch_array($row)) {
    if ($res['relatedto_id']) {
        $list[$res['relatedto_id']] = $res['relatedto_id'];
    }
}
$db->free_result($row);
$ms2->AddTextSearchDropDown(t('Beitrags ID'), 'c.relatedto_id', $list);

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Meldung'), 'c.text', '', 220);
$ms2->AddResultField(t('Modul'), 'c.relatedto_item');
$ms2->AddResultField(t('Beitrags ID'), 'c.relatedto_id');
$ms2->AddResultField(t('Datum'), 'UNIX_TIMESTAMP(c.date) AS date', 'MS2GetDate');
$ms2->AddResultField(t('Auslöser'), 'u.username', 'UserNameAndIcon');

if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=install&action=mc_search&step=10', 1);
}

$ms2->PrintSearch('index.php?mod=install&action=mc_search', 'c.commentid');
