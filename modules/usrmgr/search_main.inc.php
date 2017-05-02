<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('usrmgr');

$ms2->query['from'] = "%prefix%user AS u
    LEFT JOIN %prefix%clan AS c ON u.clanid = c.clanid
    LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
    LEFT JOIN %prefix%party_prices AS i ON i.party_id = p.party_id AND i.price_id = p.price_id";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddTextSearchField(t('Benutzername'), array('u.username' => '1337'));
$ms2->AddTextSearchField(t('Userid'), array('u.userid' => 'exact'));
$ms2->AddTextSearchField(t('Name'), array('u.name' => 'multiword', 'u.firstname' => 'multiword'));
$ms2->AddTextSearchField(t('E-Mail'), array('u.email' => 'like'));

$ms2->AddResultField(t('Benutzername'), 'u.username');
$ms2->AddResultField(t('Vorname'), 'u.firstname');
$ms2->AddResultField(t('Nachname'), 'u.name');
