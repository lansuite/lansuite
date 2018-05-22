<?php
$db->connect();

$dsp->NewContent(t('Adminaccount anlegen'), t('Lege hier einen Adminaccount an, über welchen du Zugriff auf diese Admin-Seite erh�lst. Wenn du bereits Benutzer-Daten importiert hast musst du hier keinen weiteren Account anlegen.'));

$find = $db->qry("SELECT * FROM %prefix%user");
if ($db->num_rows($find) == 0) {
    $dsp->AddSingleRow("<font color=\"red\">".t('ACHTUNG: Es wurde noch kein Adminaccount angelegt. Bitte lege diesen unbedingt unterhalb an. Sobald dieser angelegt worden ist, ist diese Seite nur noch mit diesem Account erreichbar.')."</font>");
}
$db->free_result($find);

switch ($_GET["step"]) {
    case 2:
        if ($_POST["email"] == "") {
            $func->error(t('Bitte gib eine E-Mail-Adresse ein!'), "index.php?mod=install&action=adminaccount");
        } elseif ($_POST["password"] == "") {
            $func->error(t('Bitte gib ein Kennwort ein!'), "index.php?mod=install&action=adminaccount");
        } elseif ($_POST["password"] != $_POST["password2"]) {
            $func->error(t('Das Passwort und seine Verifizierung stimmen nicht überein!'), "index.php?mod=install&action=adminaccount");
        } else {
            $db->qry("
              INSERT INTO %prefix%user
              SET
                username = 'ADMIN',
                email=%string%,
                password = %string%,
                type = '3'", $_POST["email"], md5($_POST["password"]));
            $userid = $db->insert_id();

            // Add administrator to party
            $party->add_user_to_party($userid, 1, "1", "1");

            $func->confirmation(t('Der Adminaccount wurde erfolgreich angelegt.'), "index.php?mod=install&action=adminaccount");
        }
        break;
    
    default:
        $dsp->SetForm("index.php?mod=install&action=adminaccount&step=2");
        $dsp->AddTextFieldRow("email", t('E-Mail'), $user, "");
        $dsp->AddPasswordRow("password", t('Kennwort'), $pass, "");
        $dsp->AddPasswordRow("password2", t('Kennwort wiederholen'), $pass, "");
        $dsp->AddFormSubmitRow(t('Weiter'));

        $dsp->AddBackButton("index.php?mod=install", "install/admin");
        break;
}
