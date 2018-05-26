<?php

/**
 * @param int $id
 * @return bool
 */
function UpdateUsrMgr($id)
{
    global $mf, $db, $usrmgr, $func, $cfg;

    // Clan-Management
    $clan = new \LanSuite\Module\ClanMgr\Clan();
    if (ShowField('clan')) {
        if ($_POST['new_clan_select']) {
            $clan->Add($_POST['clan_new'], $id, $_POST["clanurl"], $_POST["newclanpw"]);
        } elseif ($_POST['clan']) {
            $clan->AddMember($_POST['clan'], $id);
        } elseif (isset($_POST['clan'])) {
            $clan->RemoveMember($id);
        }
    }

    // Update User-Perissions
    if ($id) {
        $db->qry("DELETE FROM %prefix%user_permissions WHERE userid = %int%", $id);
        if ($_POST["permissions"]) {
            foreach ($_POST["permissions"] as $perm) {
                $db->qry("INSERT INTO %prefix%user_permissions SET module = %string%, userid = %int%", $perm, $id);
            }
        }
    }

    // If new user has been added
    if (!$mf->isChange) {
        $usrmgr->WriteXMLStatFile();

        // If auto generated PW, use PW stored in session, else use PW send by POST field
        if ($_POST['password_original']) {
            $_SESSION['tmp_pass'] = $_POST['password_original'];
        }

        if ($cfg["signon_password_mail"]) {
            if ($usrmgr->SendSignonMail(0)) {
                $func->confirmation(t('Dein Passwort und weitere Informationen wurden an deine angegebene E-Mail-Adresse gesendet.'), NO_LINK);
            } elseif ($cfg['sys_internet']) {
                $func->error(t('Es ist ein Fehler beim Versand der Informations-Email aufgetreten.') .'<br />'. t('Dein Passwort lautet: <b>%1</b>', array($_SESSION['tmp_pass'])), NO_LINK);
            }
        }

        // Send email-verification link
        if ($cfg['sys_login_verified_mail_only']) {
            $usrmgr->SendVerificationEmail($id);
        }

        // Show passwort, if wanted, or has mail failed
        if ($cfg['signon_password_view']) {
            $func->information(t('Dein Passwort lautet: <b>%1</b>', array($_SESSION['tmp_pass'])), NO_LINK);
        }
        $_SESSION['tmp_pass'] = '';
    }

    return true;
}
