<?php
/*
 * Created on 29.03.2009
 * 
 * 
 * 
 * @package package_name
 * @author Maztah
 * 
 */
 
function Check()
{
    global $func, $auth;
    
    $ret = true;
    
    if (($_POST['fromUserid'] != $auth['userid']) and $auth['type'] < 2) {
        $func->error(t("Deine Identität konnte nicht verifiziert werden. Die Transaktion wird sicherheitshalber abgebrochen."));
        $ret = false;
    } elseif ($_POST['toUserid'] == -1) {
        $func->information(t("Bitte wähle einen Empfänger für deine Transaktion aus"));
        $ret = false;
    } elseif ($_POST['toUserid'] == $auth['userid']) {
        $func->information(t("Du kannst dir nicht selbst Geld überweisen"));
        $ret = false;
    } elseif ((float)$_POST['movement'] <= 0) {
        $func->information(t("Du kannst keine negativen oder neutralen  Beträge überweisen"));
        $ret = false;
    } elseif ($_POST['comment'] == "") {
        $func->information(t("Bitte gib einen Kommentar zu ihrer Überweisung an."));
        $ret = false;
    } else {//check if the user has enough money for the transaction
                include_once("modules/cashmgr/class_accounting.php");
        $accounting = new accounting(0, $auth['userid']); //party not needed for calculation...or is it?
                $userbalance = $accounting->GetUserBalance();
        if ($userbalance<(float)$_POST['movement']) {
            $func->information(t("Du hast nicht genug Guthaben! Kontostand:"). $userbalance);
            $ret = false;
        }
    }
    return $ret;
}

$mf = new masterform();

$dsp->NewContent(t('Geld überweisen'), t('Hier kannst du anderen Benutzern Geld überweisen'));

$AdminFound = 0;
$UserFound = 0;
$res = $db->qry("SELECT type, userid, username, firstname, name FROM %prefix%user WHERE type >= %string% ORDER BY type DESC, username", $WhereMinType);
if (!$_POST['toUserID']) {
    $selections[-1] = "- Bitte wählen -";
}
while ($row = $db->fetch_array($res)) {
    if (!$AdminFound and $row['type'] > 1) {
        $selections['-OptGroup-1'] = t('Admins');
        $AdminFound = 1;
    }
    if (!$UserFound and $row['type'] <= 1) {
        $selections['-OptGroup-2'] = t('Benutzer');
        $UserFound = 1;
    }

    if ($auth['type'] >= 2 or !$cfg['sys_internet'] or $cfg['guestlist_shownames']) {
        $selections[$row['userid']] = $row['username'] .' ('. $row['firstname'] .' '. $row['name'] .')';
    } else {
        $selections[$row['userid']] = $row['username'];
    }
}
$db->free_result($res);

$mf->AddField(t('Empfänger'), 'toUserid', IS_SELECTION, $selections, FIELD_OPTIONAL);

$mf->AddField('Betreff', 'comment');
$mf->AddField('Betrag', 'movement');
    
$mf->AddFix('fromUserid', $auth['userid']);
$mf->AddFix('modul', 'cashmgr');

$mf->CheckBeforeInserFunction = 'Check';

$mf->SendForm('index.php?mod=cashmgr&action=sendmoney', 'cashmgr_accounting', 'ID', $_GET['cashid']);
