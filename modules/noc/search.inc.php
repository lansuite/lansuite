<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('news');

$ms2->query['from'] = "%prefix%noc_devices AS n";

$ms2->AddTextSearchField('Device', array('n.name' => 'like', 'n.ip' => 'like'));

$ms2->AddResultField('Autor', 'n.name');
$ms2->AddResultField('Titel', 'n.id');
$ms2->AddResultField('Datum', 'n.ip');

$ms2->AddIconField('details', 'index.php?mod=noc&action=details_device&deviceid=', t('Details'));
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=noc&action=change_device&step=2&deviceid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=noc&action=delete_device&step=2&deviceid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=noc&action=show_device', 'n.id');
