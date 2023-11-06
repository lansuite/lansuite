<?php

// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Mainpage-entrys
// (fielsets) for Userdetails

$user_party = $db->qry_first('
  SELECT
    *,
    UNIX_TIMESTAMP(checkin) AS checkin,
    UNIX_TIMESTAMP(checkout) AS checkout
  FROM %prefix%party_user
  WHERE
    user_id = %int%
    AND party_id = %int%', $_GET['userid'], $party->party_id);

// $user_party can be null, thats why we pre-setting the values here
$userPartyPriceID = $user_party['price_id'] ?? 0;
$userPartyUserID = $user_party['user_id'] ?? 0;
$userPartyPaid = $user_party['paid'] ?? false;
$userPartyCheckin = $user_party['checkin'] ?? null;
$userPartyCheckout = $user_party['checkout'] ?? null;

$party_seatcontrol = $db->qry_first('SELECT * FROM %prefix%party_prices WHERE price_id = %int%', $userPartyPriceID);

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
        $party_row .= ' ['. $user_party['price_text'] .']';
    }

    // Platzpfand
    if ($partySeatControlDepotPrice > 0) {
        $party_row .= ', '. $party_seatcontrol['depot_desc'];
        $party_row .= ($user_party['seatcontrol']) ? t(' gezahlt') : t(' NICHT gezahlt');
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
