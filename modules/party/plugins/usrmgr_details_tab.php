<?php
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Headermenue
// for Userdetails

// ADD HERE MODULSPECIFIC INCLUDES

// ADD HERE MODULPUGINCODE
if ($auth['type'] >= 1) {
    function p_getactive($name)
    {
        global $line, $cfg;

        if ($cfg['signon_partyid'] == $line['party_id']) {
            return "<b>".$name." (aktiv)</b>";
        } else {
            return $name;
        }
    }
  
    function active($tid)
    {
        global $line, $party;
    
        if ($line['party_id'] == $party->party_id) {
            return true;
        } else {
            return false;
        }
    }

    function p_price($price_text)
    {
        global $line, $cfg;
    
        if ($line['price']) {
            return $price_text .'<br /> ('. $line['price'] .' '. $cfg['sys_currency'] .')';
        } else {
            return $price_text;
        }
    }

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('usrmgr');
  
    $ms2->query['from'] = "%prefix%partys p
    LEFT JOIN %prefix%party_user u ON p.party_id = u.party_id AND u.user_id = ". (int)$_GET['userid'] ."
    LEFT JOIN %prefix%party_prices i ON i.party_id = p.party_id AND i.price_id = u.price_id";
    $ms2->query['where'] = "u.user_id = ". (int)$_GET['userid'] . " OR u.user_id is NULL";
  
    $ms2->config['EntriesPerPage'] = 50;
  
    $ms2->AddSelect('p.party_id');
    $ms2->AddResultField(t('Party'), 'p.name', 'p_getactive');
    $ms2->AddResultField(t('Angemeldet'), 'u.user_id', 'TrueFalse');
    $ms2->AddSelect('i.price');
    $ms2->AddResultField(t('Preis'), 'i.price_text', 'p_price');
    $ms2->AddResultField(t('Bezahlt'), 'u.paid', 'TrueFalse');
    $ms2->AddResultField(t('Bezahltdatum'), 'UNIX_TIMESTAMP(u.paiddate) AS paiddate', 'MS2GetDate');
    $ms2->AddResultField(t('Eingecheckt'), 'UNIX_TIMESTAMP(u.checkin) AS checkin', 'MS2GetDate');
    $ms2->AddResultField(t('Ausgecheckt'), 'UNIX_TIMESTAMP(u.checkout) AS checkout', 'MS2GetDate');
    if ($auth['type'] >= 2) {
        $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=party&user_id='. $_GET['userid'] .'&party_id=', t('Editieren'), 'active');
    }
  
    $ms2->PrintSearch('index.php?mod=usrmgr&action=details&userid='. $_GET['userid'] .'&headermenuitem=6', 'p.party_id');
} else {
    $func->information(NO_LOGIN);
}
