<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

function TTStatus($status) {
	global $lang;

	switch ($status) {
		default: return $lang['ms']['cb_tt_unassigned']; break;
		case 1: return $lang['ms']['cb_tt_new']; break;
		case 2: return $lang['ms']['cb_tt_accepted']; break;
		case 3: return $lang['ms']['cb_tt_in_work']; break;
		case 4: return $lang['ms']['cb_tt_closed']; break;
		case 5: return $lang['ms']['cb_tt_rejected']; break;
	}
}  

$ms2->query['from'] = "{$config["tables"]["troubleticket"]} AS t
  LEFT JOIN {$config["tables"]["user"]} AS u ON t.target_userid = u.userid";

$ms2->query['where'] = "status > '0'";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddResultField('Ticket', 't.caption');
$ms2->AddResultField('Zustšndig', 'u.username');
$ms2->AddResultField('Status', 't.status', 'TTStatus');

$ms2->AddIconField('details', 'index.php?mod=troubleticket&action=show&step=2&ttid=', 'Details');
if ($auth['type'] >= 2) $ms2->AddIconField('assign', 'index.php?mod=troubleticket&action=assign&step=2&ttid=', 'Assign');
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=troubleticket&action=change&step=2&ttid=', 'Edit');
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=troubleticket&action=delete&step=2&ttid=', 'Delete');
?>