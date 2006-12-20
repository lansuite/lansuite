<?php

if (!$_GET['party_id']) $_GET['party_id'] = $party->party_id;

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AdditionalKey = 'party_id = '. (int)$_GET['party_id'];

$dsp->AddDoubleRow('Party', (int)$_GET['party_id']);

$mf->AddField($lang['signon']['price_text'], 'price_text');
$mf->AddField($lang['signon']['price'], 'price');
$mf->AddField($lang['signon']['depot_desc'], 'depot_desc');
$mf->AddField($lang['signon']['depot_price'], 'depot_price');

$mf->SendForm('index.php?mod=party&action=price_edit', 'party_prices', 'price_id', $_GET['price_id']);
$dsp->AddBackButton('index.php?mod=party&action=price');
$dsp->AddContent();

?>
