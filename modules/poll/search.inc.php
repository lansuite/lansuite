<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('news');

$ms2->query['from'] = "%prefix%polls AS p
  LEFT JOIN %prefix%polloptions AS o ON p.pollid = o.pollid
  LEFT JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid";
$ms2->query['default_order_by'] = 'p.changedate ASC';
$ms2->query['where'] = (int)$auth['type'] .' >= 2 OR !p.group_id OR p.group_id = '. (int)$auth['group_id'];

$ms2->AddTextSearchField(t('Titel'), array('p.caption' => 'like'));

$ms2->AddResultField(t('Titel'), 'p.caption');
$ms2->AddResultField(t('Status'), 'UNIX_TIMESTAMP(p.endtime) AS endtime', 'GetPollStatus');
$ms2->AddResultField(t('Stimmen'), 'COUNT(v.polloptionid) AS Votes');

$ms2->AddIconField('details', 'index.php?mod=poll&action=show&step=2&pollid=', t('Details'));
if ($auth['type'] >= 2) {
    $ms2->AddIconField('signon', 'index.php?mod=poll&action=result&pollid=', t('Ergebnis'));
}
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=poll&action=change&step=2&pollid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=poll&action=delete&step=2&pollid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=poll', 'p.pollid');
