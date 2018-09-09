<?php

$seating = new \LanSuite\Module\Seating\Seat2();

$mail = new \LanSuite\Module\Mail\Mail();
$userManager = new \LanSuite\Module\UsrMgr\UserManager($mail);

$guestlist = new LanSuite\Module\GuestList\GuestList($seating, $userManager);

switch ($_GET['step']) {
    // Export CSV
    case 10:
        if (!$_POST['action'] and $_GET['userid']) {
            $_POST['action'][$_GET['userid']] = 1;
        }

        if ($auth['type'] >= 2 and $_POST['action']) {
            header('Expires: 0');
            header('Cache-control: private');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.ms-excel; charset: UTF-8');
            header('Content-disposition: attachment; filename=user.csv');
            echo pack("CCC", 0xef, 0xbb, 0xbf);
            
            echo "UserID;Username;Firstname;Lastname;Clan\n";
            foreach ($_POST['action'] as $key => $val) {
                echo $func->AllowHTML($guestlist->Export($key, $party->party_id)). "\n";
            }
            exit;
        }
    
        $func->information(t('Bitte markiere die User jetzt noch als exportiert.'));
        break;
    
    // Set Exported
    case 11:
        if (!$_POST['action'] and $_GET['userid']) {
            $_POST['action'][$_GET['userid']] = 1;
        }

        if ($auth['type'] >= 2 and $_POST['action']) {
            foreach ($_POST['action'] as $key => $val) {
                $guestlist->SetExported($key, $party->party_id);
            }
            $func->confirmation(t('Die User wurden für die aktuelle Party als exportiert markiert.'));
        }
        break;
}

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

if (!$party->party_id) {
    $func->information(t('Bitte setzte zuerst eine aktive Party.'));
} else {
    $ms2->query['from'] = "%prefix%party_user pu
    INNER JOIN %prefix%user u ON u.userid = pu.user_id
    LEFT JOIN %prefix%clan c ON c.clanid = u.clanid";

    $ms2->query['where'] = 'pu.party_id = '. (int)$party->party_id . ' AND (exported IS NULL OR exported = 0)';

    $ms2->config['EntriesPerPage'] = 100;

    $ms2->AddResultField(t('Benutzername'), 'u.username');
    if ($auth['type'] >= 2 or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
        $ms2->AddResultField(t('Vorname'), 'u.firstname');
        $ms2->AddResultField(t('Nachname'), 'u.name');
    }
    $ms2->AddSelect('c.url AS clanurl');
    $ms2->AddSelect('c.clanid AS clanid');
    $ms2->AddResultField('Clan', 'c.name AS clan', 'ClanURLLink');

    $ms2->AddIconField('details', 'index.php?mod=guestlist&action=details&userid=', t('Details'));

    if ($auth['type'] >= 2) {
        $ms2->AddMultiSelectAction(t('Exportieren'), "index.php?mod=guestlist&action=export&step=10", 1, 'export');
        $ms2->AddMultiSelectAction(t('Als exportiert markieren'), "index.php?mod=guestlist&action=export&step=11", 1, 'setexported');
    }
    $ms2->PrintSearch('index.php?mod=guestlist', 'u.userid');
}
