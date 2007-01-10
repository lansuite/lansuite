<?php

function GetPollStatus($endtime) {
	if ($endtime == 0 or $endtime > time()) return "offen";
	else return "geschlossen";
}

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('news');

$ms2->query['from'] = "{$config["tables"]["polls"]} AS p
  LEFT JOIN {$config["tables"]["pollvotes"]} AS v ON p.pollid = v.pollid";

$ms2->AddTextSearchField(t('Titel'), array('p.caption' => 'like'));

$ms2->AddResultField(t('Titel'), 'p.caption');
$ms2->AddResultField(t('Status'), 'p.endtime', 'GetPollStatus');
$ms2->AddResultField(t('Stimmen'), 'COUNT(v.pollid) AS Votes');

$ms2->AddIconField('details', 'index.php?mod=poll&action=show&step=2&pollid=', $lang['ms2']['details']);
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=poll&action=change&step=2&pollid=', $lang['ms2']['edit']);
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=poll&action=delete&step=2&pollid=', $lang['ms2']['delete']);

$ms2->PrintSearch('index.php?mod=poll', 'p.pollid');
?>