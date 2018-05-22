<?php

use LanSuite\Module\Seating\Seat2;

include_once('modules/usrmgr/search_main.inc.php');

$seat2 = new Seat2();

/**
 * @param int $userid
 * @return string
 */
function SeatNameLinkUsrMgr($userid)
{
    global $seat2;

    return $seat2->SeatNameLink($userid);
}

/**
 * @param boolean $paid
 * @return string
 */
function PaidIcon($paid)
{
    global $dsp;

    if ($paid) {
        return $dsp->FetchIcon('paid', '', t('Bezahlt'));
    } else {
        return $dsp->FetchIcon('not_paid', '', t('Nicht bezahlt'));
    }
}

/**
 * @param string $clan_name
 * @return string
 */
function ClanURLLinkUsrMgrSearch($clan_name)
{
    global $line;

    if ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
        if (substr($line['clanurl'], 0, 7) != 'http://') {
            $line['clanurl'] = 'http://'. $line['clanurl'];
        }
        return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
    } else {
        return $clan_name;
    }
}

/**
 * @param string $price_text
 * @return string
 */
function p_priceUsrMgrUserSelect($price_text)
{
    global $line, $cfg;
  
    if ($line['price']) {
        return $price_text .' ('. $line['price'] .' '. $cfg['sys_currency'] .')';
    } else {
        return $price_text;
    }
}

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
