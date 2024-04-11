<?php

if (!$_GET['party_id']) {
    $_GET['party_id'] = $party->party_id;
}
$dsp->AddDoubleRow('Party', $party->data['name']);

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    case 11:
        $database->query('UPDATE %prefix%partys SET evening_price_id = ? WHERE party_id = ?', [$_GET['evening_price_id'], $_GET['party_id']]);
        $func->confirmation(t('Der neue Abendkasse-Preis wurde gesetzt'));
        break;
}

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('party');

$ms2->query['from'] = "%prefix%partys AS party LEFT JOIN %prefix%party_prices AS p ON p.party_id = party.party_id";
$ms2->query['default_order_by'] = 'p.price_text DESC';
$ms2->query['where'] = "party.party_id = ". (int) $_GET['party_id'];

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddResultField(t('Text für Eintrittspreis'), 'p.price_text');
$ms2->AddResultField(t('Preis'), 'p.price');
$ms2->AddResultField(t('Abendkasse-Preis?'), 'party.evening_price_id', 'EveningPriceIdLink');
$ms2->AddResultField('Party', 'party.name');

if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN) {
    $ms2->AddIconField('edit', 'index.php?mod=party&action=price_edit&party_id='. $_GET['party_id'] .'&price_id=', t('Editieren'));
}

if ($auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
    $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=party&action=price_del&party_id='. $_GET['party_id'], 1);
}

$ms2->PrintSearch('index.php?mod=party&action=price&party_id='. $_GET['party_id'], 'p.price_id');

$dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=party&action=price_edit'));

$dsp->AddBackButton('index.php?mod=party');
