<?
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Mainpage-entrys 
// (fielsets) for Userdetails

// ADD HERE MODULSPECIFIC INCLUDES

// ADD HERE MODULPUGINCODE
$user_party = $db->qry_first('SELECT *, UNIX_TIMESTAMP(checkin) AS checkin, UNIX_TIMESTAMP(checkout) AS checkout FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%', $_GET['userid'], $party->party_id);
$party_seatcontrol = $db->qry_first('SELECT * FROM %prefix%party_prices WHERE price_id = %int%', $user_party['price_id']);
// Party Checkin, paid, ...
if ($party->count > 0) {
    $clan = '<table width="100%"><tr><td>';
    $party_row = '';
    $link = '';
    ($user_party['user_id'])? $party_row .= $lang['usrmgr']['details_signon'] :  $party_row .= $lang['usrmgr']['details_not_signon'];
    if (IsAuthorizedAdmin()) ($user_party['paid'])? $link = 'index.php?mod=guestlist&step=11&userid='. $_GET['userid']
      : $link = 'index.php?mod=guestlist&step=10&userid='. $_GET['userid'];
    // Paid
    ($user_party['paid'])? $party_row .= ', '. $dsp->AddIcon('paid', $link, $lang['usrmgr']['paid_yes']) : $party_row .= ', '. $dsp->AddIcon('not_paid', $link, $lang['usrmgr']['paid_no']);
    if ($user_party['paid'] == 1) $party_row .= ' ['. $lang['usrmgr']['details_paid_vvk'] .']';
    elseif ($user_party['paid'] == 2) $party_row .= ' ['. $lang['usrmgr']['details_paid_ak'] .']';
    // Platzpfand
    if ($party_seatcontrol['depot_price'] > 0){
        $party_row .= ', '. $party_seatcontrol['depot_desc'];
        $party_row .= ($user_party['seatcontrol']) ? $lang['usrmgr']['details_seat_paid'] : $lang['usrmgr']['details_seat_not_paid'];
    }
    // CheckIn CheckOut
    $link = '';
    if (IsAuthorizedAdmin() and !$user_party['checkin']) $link = 'index.php?mod=guestlist&step=20&userid='. $_GET['userid'];
    if ($user_party['checkin']) $party_row .= ' '. $dsp->AddIcon('in', $link, $lang['usrmgr']['checkin']) .'['. $func->unixstamp2date($user_party['checkin'], 'datetime') .']';
    else $party_row .= ' '.$dsp->AddIcon('not_in', $link, $lang['usrmgr']['checkin_no']);
    
    $link = '';
    if (IsAuthorizedAdmin() and !$user_party['checkout'] and $user_party['checkin']) $link = 'index.php?mod=guestlist&step=21&userid='. $_GET['userid'];
    if ($user_party['checkout']) $party_row .= ' '. $dsp->AddIcon('out', $link, $lang['usrmgr']['checkout']) .'['. $func->unixstamp2date($user_party['checkout'], 'datetime') .']';
    else $party_row .= ' '.$dsp->AddIcon('not_out', $link, $lang['usrmgr']['checkout_no']);
    
    if (IsAuthorizedAdmin() and $user_party['checkin'] > 0 and $user_party['checkout'] > 0) $party_row .= $dsp->AddIcon('delete', 'index.php?mod=guestlist&step=22&userid='. $_GET['userid'], 'Reset Checkin');
    
    $dsp->AddDoubleRow("Party '<i>". $_SESSION['party_info']['name'] ."</i>'", $party_row);
} 
?>