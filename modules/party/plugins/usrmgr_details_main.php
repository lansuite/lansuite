<?php

// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Mainpage-entrys
// (fielsets) for Userdetails
$query = '
    SELECT
        `party_user`.`price_id`,
        `party_user`.`user_id`,
        `party_user`.`paid`,
        `party_user`.`seatcontrol`,
        UNIX_TIMESTAMP(`party_user`.`checkin`) AS checkin,
        UNIX_TIMESTAMP(`party_user`.`checkout`) AS checkout,
        `party_prices`.`price_text`
    FROM `%prefix%party_user` AS party_user
        LEFT JOIN `%prefix%party_prices` AS party_prices ON (
            `party_user`.`party_id` = `party_prices`.`party_id`
        )
    WHERE
        `party_user`.`user_id` = ?
        AND `party_user`.`party_id` = ?';
$user_party = $database->queryWithOnlyFirstRow($query, [$_GET['userid'], $party->party_id]);

// $user_party can be null, thats why we pre-setting the values here
$userPartyPriceID = $user_party['price_id'] ?? 0;
$userPartyUserID = $user_party['user_id'] ?? 0;
$userPartyPaid = $user_party['paid'] ?? false;
$userPartyCheckin = $user_party['checkin'] ?? null;
$userPartyCheckout = $user_party['checkout'] ?? null;
$userPartyPriceText = $user_party['price_text'] ?? '';
$userPartySeatControl = $user_party['seatcontrol'] ?? 0;

$party_seatcontrol = $database->queryWithOnlyFirstRow('SELECT * FROM %prefix%party_prices WHERE price_id = ?', [$userPartyPriceID]);

// $party_seatcontrol can be null, thats why we pre-setting the values here
$partySeatControlDepotPrice = $party_seatcontrol['depot_price'] ?? 0;

if ($party->count > 0) {
    $clan = '<table width="100%"><tr><td>';
    $party_row = '';
    $link = '';
    ($userPartyUserID)? $party_row .= t('Angemeldet') :  $party_row .= t('Nicht Angemeldet');

    if (IsAuthorizedAdmin()) {
        ($userPartyPaid)? $link = 'index.php?mod=guestlist&step=11&userid='. $_GET['userid']
        : $link = 'index.php?mod=guestlist&step=10&userid='. $_GET['userid'];
    }

    // Paid
    ($userPartyPaid)? $party_row .= ', '. $dsp->FetchIcon('paid', $link, t('Bezahlt')) : $party_row .= ', '. $dsp->FetchIcon('not_paid', $link, t('Nicht bezahlt'));
    if ($userPartyPaid > 0) {
        $party_row .= ' ['. $userPartyPriceText .']';
    }

    // Platzpfand
    if ($partySeatControlDepotPrice > 0) {
        $party_row .= ', '. $party_seatcontrol['depot_desc'];
        $party_row .= ($userPartySeatControl) ? t(' gezahlt') : t(' NICHT gezahlt');
    }

    // CheckIn CheckOut
    $link = '';
    if (IsAuthorizedAdmin() and !$userPartyCheckin) {
        $link = 'index.php?mod=guestlist&step=20&userid='. $_GET['userid'];
    }
    if ($userPartyCheckin) {
        $party_row .= ' '. $dsp->FetchIcon('in', $link, t('Eingecheckt')) .'['. $func->unixstamp2date($userPartyCheckin, 'datetime') .']';
    } else {
        $party_row .= ' '.$dsp->FetchIcon('not_in', $link, t('Nicht eingecheckt'));
    }
    
    $link = '';
    if (IsAuthorizedAdmin() && !$userPartyCheckout && $userPartyCheckin) {
        $link = 'index.php?mod=guestlist&step=21&userid='. $_GET['userid'];
    }
    if ($userPartyCheckout) {
        $party_row .= ' '. $dsp->FetchIcon('out', $link, t('Ausgecheckt')) .'['. $func->unixstamp2date($userPartyCheckout, 'datetime') .']';
    } else {
        $party_row .= ' '.$dsp->FetchIcon('not_out', $link, t('Nicht ausgecheckt'));
    }
    
    if (IsAuthorizedAdmin() && $userPartyCheckin > 0 && $userPartyCheckout > 0) {
        $party_row .= $dsp->FetchIcon('delete', 'index.php?mod=guestlist&step=22&userid=' . $_GET['userid'], 'Reset Checkin');
    }
    
    $dsp->AddDoubleRow("Party '<i>". $_SESSION['party_info']['name'] ."</i>'", $party_row);
}
