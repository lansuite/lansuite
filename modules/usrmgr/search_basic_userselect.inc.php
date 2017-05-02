<?php
include_once('modules/usrmgr/search_main.inc.php');

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

function SeatNameLink($userid)
{
    global $seat2;

    return $seat2->SeatNameLink($userid);
}

function PaidIcon($paid)
{
    global $dsp;

    if ($paid) {
        return $dsp->FetchIcon('', 'paid', t('Bezahlt'));
    } else {
        return $dsp->FetchIcon('', 'not_paid', t('Nicht bezahlt'));
    }
}

function ClanURLLink($clan_name)
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

function p_price($price_text)
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
$ms2->AddResultField(t('Clan'), 'c.name AS clan', 'ClanURLLink');
$ms2->AddResultField('Bez.', 'p.paid', 'PaidIcon');
$ms2->AddSelect('i.price');
$ms2->AddResultField(t('Preis'), 'i.price_text', 'p_price');

$ms2->AddResultField('Sitz', 'u.userid', 'SeatNameLink');

$ms2->AddIconField('assign', $target_url, t('Zuweisen'));

$ms2->PrintSearch($current_url, 'u.userid');
