<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('seating');

$ms2->query['from'] = '%prefix%seat_block AS b';
$ms2->query['where'] = 'b.party_id = '. $party->party_id;

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddResultField(t('Blockname'), 'b.name');
$ms2->AddResultField(t('Plätze'), 'b.blockid', 'SeatsAvailable');
$ms2->AddResultField(t('Belegt'), 'b.blockid', 'SeatsOccupied');
$ms2->AddResultField(t('Auslastung'), 'b.blockid', 'SeatLoad');

if (!$target_icon) {
    $target_icon = 'details';
}
if ($target_url) {
    $ms2->AddIconField($target_icon, $target_url, t($target_icon));
} else {
    $ms2->AddIconField('details', 'index.php?mod=seating&action=show&step=2&blockid=', t('Details'));

    if ($auth['type'] >= 3) {
        $ms2->AddIconField('ip_generate', 'index.php?mod=seating&action=ipgen&blockid=', t('IPs generieren'));
    }
    if ($auth['type'] >= 3) {
        $ms2->AddIconField('ip_edit', 'index.php?mod=seating&action=ip&step=2&blockid=', t('IPs editieren'));
    }
    if ($auth['type'] >= 3) {
        $ms2->AddIconField('ip_del', 'index.php?mod=seating&action=ipgen&step=20&blockid=', t('IPs löschen'));
    }

    if ($auth['type'] >= 2) {
        $ms2->AddIconField('edit', 'index.php?mod=seating&action=edit&step=2&blockid=', t('Editieren'));
    }
    if ($auth['type'] >= 3) {
        $ms2->AddIconField('delete', 'index.php?mod=seating&action=delete&step=2&blockid=', t('Löschen'));
    }
}
$ms2->PrintSearch($current_url, 'b.blockid');
