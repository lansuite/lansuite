<?php
$dsp->NewContent(t('Geburtstage'), t('Hier kannst du die Geburtstage deiner Mitspieler einsehen'));

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

$ms2->query['from'] = "%prefix%user AS u
    LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id";
		
if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
	$ms2->query['where'] = '1 = 1';
} else {
	$ms2->query['where'] = 'party_id = ' . intval($party->party_id);
	$ms2->query['where'] .= ' AND u.show_birthday = 1';
}

$ms2->query['default_order_by'] = 'birthday DESC';

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField(t('Benutzername'), array('u.username' => '1337'));
if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
	$ms2->AddTextSearchField(t('Userid'), array('u.userid' => 'exact'));
	$ms2->AddTextSearchField(t('Name'), array('u.name' => 'like', 'u.firstname' => 'like'));
}
$ms2->AddTextSearchField(t('Geburtstag'), array('birthday' => 'like'));

$ms2->AddResultField(t('Benutzername'), 'u.username');
if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
	$ms2->AddResultField(t('Vorname'), 'u.firstname');
	$ms2->AddResultField(t('Nachname'), 'u.name');
	$ms2->AddResultField(t('Geburtstag anzeigen'), 'u.show_birthday', 'ShowActiveState');
}

$ms2->AddResultField('Geburtstag', 'DATE_FORMAT(birthday, "%d.%m.%Y") as birthd');
$ms2->AddResultField(t('Alter'), 'DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),birthday)), "%Y") + 0 AS age');
 
$ms2->AddIconField('details', 'index.php?mod=guestlist&action=details&userid=', t('Details'));

$ms2->PrintSearch('index.php?mod=birthday', 'u.userid');
