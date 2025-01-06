<?php

use LanSuite\PasswordHash;

$user_data = $database->queryWithOnlyFirstRow("SELECT name, firstname, username, type FROM %prefix%user WHERE userid = ?", [$_GET['userid']]);

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        include_once('modules/usrmgr/search.inc.php');
        break;

    case 2:
        $func->question(t('Bist du sicher, dass du dem Benutzer <b>%1 %2 (%3)</b> ein neues Passwort zuweisen willst?', $user_data["firstname"], $user_data["name"], $user_data["username"]), "index.php?mod=usrmgr&action=newpwd&step=3&userid=". $_GET['userid']);
        break;

    case 3:
        $password = random_int(100000, 999999);
        $hash = PasswordHash::hash($password);

        if ($_SESSION["auth"]["type"] < $userdata["type"]) {
            $func->information(t('Du verfügst über ein geringeres Benutzerlevel, als derjenige, auf den du diese Funktion anwenden möchten. Es wurde kein neues Passwort generiert'));
        } else {
            $database->query("UPDATE %prefix%user SET password = ? WHERE userid = ?", [$hash, $_GET['userid']]);

            $func->confirmation(t('Das Passwort von <b>%2 %3 (%4)</b> wurde erfolgreich geändert.<br>Das neue Passwort lautet <b>%1</b>.', $password, $user_data["firstname"], $user_data["name"], $user_data["username"]), "index.php?mod=usrmgr&action=details&userid=". $_GET['userid']);
        }
        break;
}
