<?php

if (!$_GET['party_id']) {
    $_GET['party_id'] = $party->party_id;
}
$dsp->AddDoubleRow('Party', $party->data['name']);

switch ($_GET['step']) {
    case 11:
        $db->qry('UPDATE %prefix%partys SET evening_price_id = %int% WHERE party_id = %int%', $_GET['evening_price_id'], $_GET['party_id']);
        $func->confirmation(t('Der neue Abendkasse-Preis wurde gesetzt'));
        break;
}

function eveningPriceIdLink($evening_price_id)
{
    global $dsp, $templ, $lang, $line;
  
    if ($evening_price_id == $line['price_id']) {
        return $dsp->FetchIcon('', 'yes', t('Ja'));
    } else {
        return $dsp->FetchIcon('index.php?mod=party&action=price&step=11&party_id='. $_GET['party_id'] .'&evening_price_id='. $line['price_id'], 'no', t('Nein'));
    }
}

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('party');

$ms2->query['from'] = "%prefix%partys AS party LEFT JOIN %prefix%party_prices AS p ON p.party_id = party.party_id";
$ms2->query['default_order_by'] = 'p.price_text DESC';
$ms2->query['where'] = "party.party_id = ". (int)$_GET['party_id'];

$ms2->config['EntriesPerPage'] = 20;

/*
$party_list = array('' => 'Alle');
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list, $party->party_id);
*/

$ms2->AddResultField(t('Text für Eintrittspreis'), 'p.price_text');
$ms2->AddResultField(t('Preis'), 'p.price');
$ms2->AddResultField(t('Abendkasse-Preis?'), 'party.evening_price_id', 'eveningPriceIdLink');
$ms2->AddResultField('Party', 'party.name');

if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=party&action=price_edit&party_id='. $_GET['party_id'] .'&price_id=', t('Editieren'));
}

if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=party&action=price_del&party_id='. $_GET['party_id'], 1);
}

$ms2->PrintSearch('index.php?mod=party&action=price&party_id='. $_GET['party_id'], 'p.price_id');

$dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=party&action=price_edit'));

$dsp->AddBackButton('index.php?mod=party');
$dsp->AddContent();
