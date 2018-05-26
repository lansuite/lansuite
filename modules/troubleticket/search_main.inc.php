<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();
$ms2->query['from'] = "%prefix%troubleticket AS t
  LEFT JOIN %prefix%user AS u ON t.target_userid = u.userid";

$ms2->query['where'] = "status > '0'";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField('Ticket', array('t.caption' => 'like'));

$ms2->AddResultField('Ticket', 't.caption');
$ms2->AddResultField('Zuständig', 'u.username');
$ms2->AddResultField('Status', 't.status', 'TTStatus');

$ms2->AddIconField('details', 'index.php?mod=troubleticket&action=show&step=2&ttid=', 'Details');
if ($auth['type'] >= 2) {
    $ms2->AddIconField('assign', 'index.php?mod=troubleticket&action=assign&step=2&ttid=', 'Assign');
}
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=troubleticket&action=change&step=2&ttid=', 'Edit');
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=troubleticket&action=delete&step=2&ttid=', 'Delete');
}
