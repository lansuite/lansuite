<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

if ($func->isModActive('seating')) {
    $seat2 = new \LanSuite\Module\Seating\Seat2();
}

$ms2->query['from'] = "%prefix%user AS u
    LEFT JOIN %prefix%clan AS c ON u.clanid = c.clanid
    LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
    LEFT JOIN %prefix%party_prices AS i ON i.party_id = p.party_id AND i.price_id = p.price_id";

if ($party->party_id) {
    $ms2->query['where'] = 'p.party_id = '. (int)$party->party_id;
} else {
    $ms2->query['where'] = '1 = 1';
}
($cfg['guestlist_showorga'])? $ms2->query['where'] .= ' AND u.type >= 1' : $ms2->query['where'] .= ' AND u.type = 1';

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField(t('Benutzername'), array('u.username' => '1337'));
$ms2->AddTextSearchField(t('Userid'), array('u.userid' => 'exact'));
$ms2->AddTextSearchField(t('Name'), array('u.name' => 'like', 'u.firstname' => 'like'));

if ($party->party_id) {
    $ms2->AddTextSearchDropDown(t('Bezahlt'), 'p.paid', array('' => 'Alle', '0' => 'Nicht bezahlt', '>1' => 'Bezahlt'));
    if (!$cfg['sys_internet']) {
        $ms2->AddTextSearchDropDown(t('Eingecheckt'), 'p.checkin', array('' => t('Alle'), '0' => t('Nicht Eingecheckt'), '>1' => t('Eingecheckt')));
        $ms2->AddTextSearchDropDown(t('Ausgecheckt'), 'p.checkout', array('' => t('Alle'), '0' => t('Nicht Ausgecheckt'), '>1' => t('Ausgecheckt')));
    }
}

$ms2->AddTextSearchField(t('Clan'), array('c.name' => 'like'));

$ms2->AddResultField(t('Benutzername'), 'u.username');
if ($auth['type'] >= 2 or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
    $ms2->AddResultField(t('Vorname'), 'u.firstname');
    $ms2->AddResultField(t('Nachname'), 'u.name');
}
$ms2->AddSelect('c.url AS clanurl');
$ms2->AddSelect('c.clanid AS clanid');
$ms2->AddResultField('Clan', 'c.name AS clan', 'ClanURLLink');

if ($party->party_id) {
    $ms2->AddResultField(t('Bez.'), 'p.paid', 'PaidIconLinkGuestlist');
    $ms2->AddSelect('i.price');
    $ms2->AddResultField(t('Preis'), 'i.price_text', 'p_price');
    if ($func->isModActive('seating')) {
        $ms2->AddResultField(t('Sitz'), 'u.userid', 'SeatNameLink');
    }

    if (!$cfg['sys_internet']) {
        $ms2->AddResultField(t('In'), 'UNIX_TIMESTAMP(p.checkin) AS checkin', 'MS2GetDate');
        $ms2->AddResultField(t('Out'), 'UNIX_TIMESTAMP(p.checkout) AS checkout', 'MS2GetDate');
    }
}
$ms2->AddIconField('details', 'index.php?mod=guestlist&action=details&userid=', t('Details'));
$ms2->AddIconField('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID=', t('Mail senden'));

if ($auth['type'] >= 2) {
    $ms2->AddMultiSelectAction(t('Auf "Bezahlt" setzen'), "index.php?mod=guestlist&step=10", 1, 'paid');
    $ms2->AddMultiSelectAction(t('Auf "Nicht Bezahlt" setzen'), "index.php?mod=guestlist&step=11", 1, 'not_paid');
    $ms2->AddMultiSelectAction(t('Einchecken'), "index.php?mod=guestlist&step=20", 1, 'in');
    $ms2->AddMultiSelectAction(t('Auschecken'), "index.php?mod=guestlist&step=21", 1, 'out');
    $ms2->AddMultiSelectAction(t('Ein- und Auschecken rückgängig'), "index.php?mod=guestlist&step=22", 1, 'not_out');
}
$ms2->PrintSearch('index.php?mod=guestlist', 'u.userid');
