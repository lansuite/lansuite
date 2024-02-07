<?php

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    case 11:
        $db->qry('UPDATE %prefix%partys SET evening_price_id = %int% WHERE party_id = %int%', $_GET['evening_price_id'], $_GET['party_id']);
        $func->confirmation(t('Der neue Abendkasse-Preis wurde gesetzt'));
        break;
}

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('party');

$ms2->query['from'] = "%prefix%party_location AS l";
$ms2->query['default_order_by'] = 'l.location_id DESC';

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddResultField(t('Name'), 'l.location_name');
$ms2->AddResultField(t('PLZ'), 'l.postcode');

if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
    $ms2->AddIconField('add', 'index.php?mod=party&action=edit&step=2&party_id=', t('Veranstaltung an diesem Ort hinzufügen'));
}

if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
    $ms2->AddIconField('edit', 'index.php?mod=party&action=location_edit&party_id='. $_GET['party_id'] .'&location_id=', t('Editieren'));
}

if ($auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
    $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=party&action=location_del&party_id='. $_GET['party_id'], 1);
}

$ms2->PrintSearch('index.php?mod=party&action=location&location_id='. $_GET['party_id'], 'l.location_id');
if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
    $dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=party&action=location_edit'));
}
$dsp->AddBackButton('index.php?mod=party');
