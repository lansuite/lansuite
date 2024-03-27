<?php

$account = new LanSuite\Module\Foodcenter\Accounting($auth['userid']);

if ($auth['type'] > \LS_AUTH_TYPE_USER && !isset($_GET['act'])) {
    $_GET['act'] = "menu";
} elseif ($auth['type'] < \LS_AUTH_TYPE_ADMIN) {
    $_GET['act'] = "";
}

$step = $_GET['step'] ?? 0;
$action = $_GET['action'] ?? '';

if ($action == "payment" && $step == 3) {
    if (!is_numeric($_POST['amount'])) {
        $error['amount'] = t('Bitte einen Korrekten Betrag angeben');
        $step = 2;
    }
    
    if (strlen($_POST['comment'] . " (" . $auth['username'] . ")") > 255) {
        $error['comment'] = t('Kommentar zu lange bitte kürzen.');
        $step = 2;
    }
}

switch ($_GET['act']) {
    default:
    case "list":
        $account->list_balance();
        break;

    case "menu":
        $dsp->NewContent(t('Kontoverwaltung'), t('Hier kannst du Einzahlungen, Auszahlungen verwalten und Kontostände einsehen.'));
        $dia_quest[] = t('Zahlungen')    ;
        $dia_quest[] = t('Fremder Kontoauszug');
        $dia_quest[] = t('Eigener Kontoauszug');
        $dia_link[] = "index.php?mod=foodcenter&action=account&act=payment";
        $dia_link[] = "index.php?mod=foodcenter&action=account&act=himbalance";
        $dia_link[] = "index.php?mod=foodcenter&action=account&act=list";
        $func->multiquestion($dia_quest, $dia_link, "");
        break;
    
    case "payment":
        switch ($step) {
            case "2":
                $errorAmount = $error['amount'] ?? '';
                $valueAmount = $_POST['amount'] ?? 0;
                $errorComment = $error['comment'] ?? '';
                $valueComment = $_POST['comment'] ?? 0;

                $dsp->NewContent(t('Zahlungen'));
                $dsp->SetForm("index.php?mod=foodcenter&action=account&act=payment&step=3&userid=".$_GET['userid']);
                $dsp->AddTextFieldRow("amount", t('Betrag'), $valueAmount, $errorAmount );
                $dsp->AddTextFieldRow("comment", t('Kommentar (Dein Name wird in Klammer angefügt)'), $valueComment, $errorComment);
                $dsp->AddFormSubmitRow(t('Abschicken'));
                $account = new LanSuite\Module\Foodcenter\Accounting($_GET['userid']);
                $account->list_balance();
                break;

            case "3":
                $account = new LanSuite\Module\Foodcenter\Accounting($_GET['userid']);
                $account->change($_POST['amount'], $_POST['comment'] . " (" . $auth['username'] . ")", $_GET['userid']);
                $account->list_balance();
                break;

            default:
                $current_url = 'index.php?mod=foodcenter&action=account&act=payment';
                $target_url = 'index.php?mod=foodcenter&action=account&act=payment&step=2&userid=';
                include_once('modules/usrmgr/search_basic_userselect.inc.php');
                break;
        }
        break;
        
    case "himbalance":
        switch ($step) {
            case "2":
                $account = new LanSuite\Module\Foodcenter\Accounting($_GET['userid']);
                $account->list_balance();
                break;

            default:
                $current_url = 'index.php?mod=foodcenter&action=account&act=himbalance';
                $target_url = 'index.php?mod=foodcenter&action=account&act=himbalance&step=2&userid=';
                include_once('modules/usrmgr/search_basic_userselect.inc.php');
                break;
        }
        break;
}
