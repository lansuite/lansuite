<?php

if ($_GET['act'] == "him" && $auth['type'] < 3) {
    $func->information("ACCESS_DENIED");
} elseif ($_GET['act'] == "him" && $auth['type'] = 3) {
    switch ($_GET['step']) {
        case 2:
            $userid = $_GET['userid'];
            break;

        default:
            $current_url = 'index.php?mod=cashmgr&action=myaccounting&act=him';
            $target_url = 'index.php?mod=cashmgr&action=myaccounting&act=him&step=2&userid=';
            include_once('modules/usrmgr/search_basic_userselect.inc.php');
            break;
    }
}

if (!$_GET['act'] || ($_GET['act'] && $_GET['step'] == 2)) {
    if ($userid == null) {
        $userid = $auth['userid'];
    }
    
    $dsp->NewContent(t('Buchhaltung'), t('Übersicht aller meiner Ein- und Ausgaben'));

    $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('accounting');

    $ms2->query['from'] = "%prefix%cashmgr_accounting AS a
                            LEFT JOIN %prefix%user AS fu ON a.fromUserid = fu.userid
                            LEFT JOIN %prefix%user AS tu ON a.toUserid = tu.userid";
    $ms2->query['default_order_by'] = 'actiontime DESC';
    $ms2->query['where'] = "a.toUserid = {$userid} OR a.fromUserid = {$userid}";
    $ms2->config['EntriesPerPage'] = 20;
    
    $party_list = array('' => 'Alle');
    $row = $db->qry("SELECT party_id, name FROM %prefix%partys");
    while ($res = $db->fetch_array($row)) {
        $party_list[$res['party_id']] = $res['name'];
    }
    $db->free_result($row);

    $ms2->AddTextSearchDropDown('Party', 'a.partyid', $party_list, $party->party_id);
    $ms2->AddTextSearchDropDown('Zahlungsart', 'a.fix', array('' => 'Alle', '0' => 'Nur Online','1' => 'Nur Bar'));

    $ms2->AddResultField(t('Datum'), 'a.actiontime');
    $ms2->AddResultField(t('Modul'), 'a.modul');
    $ms2->AddResultField(t('Kommentar'), 'a.comment');
    $ms2->AddSelect('a.fromUserid');
    $ms2->AddSelect("IF(a.fromUserid = {$userid},'1','0') AS my");
    $ms2->AddResultField(t('Bearbeiter'), 'fu.username', 'UserNameAndIcon');
    $ms2->AddResultField(t('Betrag'), 'a.movement', 'GetColor');

    $ms2->PrintSearch('index.php?mod=cashmgr&action=myaccounting', 'a.id');
}
