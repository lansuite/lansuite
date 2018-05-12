<?php

/**
 * Check is used as a masterform callback function
 *
 * @return bool
 */
function Check()
{
    global $func, $auth;

    $ret = true;

    if (($_POST['fromUserid'] != $auth['userid']) && $auth['type'] < 2) {
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

        // Check if the user has enough money for the transaction
    } else {
        // Party not needed for calculation...or is it?
        $accounting = new \LanSuite\Module\CashMgr\Accounting(0, $auth['userid']);
        $userbalance = $accounting->GetUserBalance();

        if ($userbalance<(float)$_POST['movement']) {
            $func->information(t("Du hast nicht genug Guthaben! Kontostand:"). $userbalance);
            $ret = false;
        }
    }

    return $ret;
}
