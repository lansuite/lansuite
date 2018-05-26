<?php

use LanSuite\Module\Seating\Seat2;

include_once('modules/usrmgr/search_main.inc.php');

$seat2 = new Seat2();

$ms2->query['where'] = $additional_where;

$ms2->AddTextSearchField('NGL/WWCL/LGZ-ID', array('u.nglid' => 'exact', 'u.nglclanid' => 'exact', 'u.wwclid' => 'exact', 'u.wwclclanid' => 'exact', 'u.lgzid' => 'exact', 'u.lgzclanid' => 'exact',));

$ms2->AddTextSearchDropDown(t('Benutzertyp'), 'u.type', array('' => t('Alle'), '1' => t('Gast'), '!1' => 'Nicht Gast', '<0' => t('Gelöschte User'), '2' => t('Administrator'), '3' => t('Superadmin'), '2,3' => t('Orgas')));
    
$ms2->AddTextSearchDropDown(t('Bezahltstatus'), 'p.paid', array('' => t('Alle'), '0' => t('Nicht bezahlt'), '>1' => t('Bezahlt')));
$ms2->AddTextSearchDropDown(t('Geschlecht'), 'u.sex', array('' => t('Alle'), '0' => t('Geschlecht unbekannt'), '1' => t('ist männlich'), '2' => t('ist weiblich')));

$ms2->AddSelect('c.url AS clanurl');
$ms2->AddResultField(t('Clan'), 'c.name AS clan', 'ClanURLLinkUsrMgrSearch');
$ms2->AddResultField('Bez.', 'p.paid', 'PaidIcon');
$ms2->AddSelect('i.price');
$ms2->AddResultField(t('Preis'), 'i.price_text', 'p_priceUsrMgrUserSelect');

$ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLinkUsrMgr');

$ms2->AddIconField('assign', $target_url, t('Zuweisen'));

$ms2->PrintSearch($current_url, 'u.userid');
