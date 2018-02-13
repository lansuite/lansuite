<?php
include_once('modules/usrmgr/search_main.inc.php');

function ClanURLLink($clan_name)
{
    global $line, $func;

    if ($clan_name == '') {
        return '';
    } elseif ($func->isModActive('clanmgr')) {
        return '<a href="index.php?mod=clanmgr&action=clanmgr&step=2&clanid='. $line['clanid'] .'">'. $clan_name .'</a>';
    } elseif ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
        if (substr($line['clanurl'], 0, 7) != 'http://') {
            $line['clanurl'] = 'http://'. $line['clanurl'];
        }
        return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
    } else {
        return $clan_name;
    }
}

function IfLowerUserlevel($userid)
{
    global $line, $auth;
  
    if ($line['type'] < $auth['type']) {
        return true;
    } else {
        return false;
    }
}

function IfLowerOrEqualUserlevel($userid)
{
    global $line, $auth;

    if ($line['type'] <= $auth['type']) {
        return true;
    } else {
        return false;
    }
}

function IfLocked($userid)
{
    global $line;

    if ($line['locked']) {
        return true;
    } else {
        return false;
    }
}

function IfUnlocked($userid)
{
    global $line;

    if (!$line['locked']) {
        return true;
    } else {
        return false;
    }
}


$ms2->AddTextSearchField(t('Clan'), array('c.name' => 'like'));

$ms2->AddTextSearchDropDown(t('Benutzertyp'), 'u.type', array('' => t('Alle'), '1' => t('Gast'), '!1' => t('Nicht Gast'), '<0' => t('Deaktiviert'), '2' => t('Admin'), '3' => t('Superadmin'), '2,3' => t('Admin, oder Superadmin')));
    
$party_list = array('' => 'Alle', 'NULL' => 'Zu keiner Party angemeldet');
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while ($res = $db->fetch_array($row)) {
    $party_list[$res['party_id']] = $res['name'];
}
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list);#, $party->party_id

$ms2->AddTextSearchDropDown(t('Zahlstatus'), 'p.paiddate', array('' => t('Alle'), '<1' => t('Nicht bezahlt'), '>1' => t('Bezahlt')));
$ms2->AddTextSearchDropDown(t('Eingecheckt'), 'p.checkin', array('' => t('Alle'), '0' => t('Nicht eingecheckt'), '>1' => t('Eingecheckt')));
$ms2->AddTextSearchDropDown(t('Ausgecheckt'), 'p.checkout', array('' => t('Alle'), '0' => t('Nicht ausgecheckt'), '>1' => t('Ausgecheckt')));
$ms2->AddTextSearchDropDown(t('Geschlecht'), 'u.sex', array('' => t('Alle'), '0' => t('Unbekannt'), '1' => t('Männlich'), '2' => t('Weblich')));
$ms2->AddTextSearchDropDown(t('Accounts'), 'u.locked', array('' => t('Alle'), '0' => t('Nur freigegebene'), '1' => t('Nur gesperrte')));

$ms2->AddSelect('u.type');
$ms2->AddSelect('u.locked');
if ($cfg['signon_show_clan']) {
    $ms2->AddSelect('c.url AS clanurl');
    $ms2->AddSelect('c.clanid AS clanid');
    $ms2->AddResultField(t('Clan'), 'c.name AS clan', 'ClanURLLink');
}
$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Details'));
$ms2->AddIconField('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID=', t('Mail senden'));
$ms2->AddIconField('change_pw', 'index.php?mod=usrmgr&action=newpwd&step=2&userid=', t('Passwort ändern'), 'IfLowerOrEqualUserlevel');
if ($auth['type'] >= 2) {
    $ms2->AddIconField('assign', 'index.php?mod=auth&action=switch_to&userid=', t('Benutzer wechseln'), 'IfLowerUserlevel');
}
if ($auth['type'] >= 3 and $func->isModActive('foodcenter')) {
    $ms2->AddIconField('paid', 'index.php?mod=foodcenter&action=account&act=payment&step=2&userid=', t('Geld auf Konto buchen'));
#  $ms2->AddIconField('paid', 'index.php?mod=foodcenter&action=account&act=himbalance&step=2&userid=', t('Kontostand zeigen'));
}
$ms2->AddIconField('locked', 'index.php?mod=usrmgr&step=11&userid=', t('Account freigeben'), 'IfLocked');
$ms2->AddIconField('unlocked', 'index.php?mod=usrmgr&step=10&userid=', t('Account sperren'), 'IfUnlocked');

// Add icons depending on other modules
$plugin = new plugin('usrmgr_search');
while (list($caption, $inc) = $plugin->fetch()) {
    include_once($inc);
}

if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=change&step=1&userid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid=', t('Löschen'));
}


if ($auth['type'] >= 2) {
    $res = $db->qry("SELECT * FROM %prefix%party_usergroups");
    $ms2->AddMultiSelectAction("Gruppenzuordung aufheben", "index.php?mod=usrmgr&action=group&step=30&group_id=0", 0, 'delete_group');
    while ($row = $db->fetch_array($res)) {
        $ms2->AddMultiSelectAction("Der Gruppe '{$row['group_name']}' zuordnen", "index.php?mod=usrmgr&action=group&step=30&group_id={$row['group_id']}", 0, 'assign_group');
    }
    $db->free_result($res);
}

if ($auth['type'] >= 2) {
    $ms2->AddMultiSelectAction(t('Freigeben'), "index.php?mod=usrmgr&step=11", 1, 'unlocked');
}
if ($auth['type'] >= 2) {
    $ms2->AddMultiSelectAction(t('Sperren'), "index.php?mod=usrmgr&step=10", 1, 'locked');
}
if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction(t('Löschen'), "index.php?mod=usrmgr&action=delete&step=10", 1, 'delete');
}

$ms2->PrintSearch('index.php?mod=usrmgr&action=search', 'u.userid');
