<?php
$db->connect();

$dsp->NewContent(t('Adminaccount anlegen'), t('Hier legen Sie einen Adminaccount an, über welchen Sie Zugriff auf diese Admin-Seite erhalten. Wenn Sie bereits Benutzer-Daten importiert haben müssen Sie hier keinen weiteren Account anlegen.'));

$find = $db->query("SELECT * FROM {$config["tables"]["user"]}");
if ($db->num_rows($find) == 0) {
	$dsp->AddSingleRow("<font color=\"red\">".t('ACHTUNG: Es wurde noch kein Adminaccount angelegt. Bitte legen Sie diesen unbedingt unterhalb an. Sobald dieser angelegt worden ist, ist diese Seite nur noch mit diesem Account erreichbar.')."</font>"); 
}
$db->free_result($find);

switch($_GET["step"]) {
	case 2:
		if ($_POST["email"] == "") $func->error(t('Bitte geben Sie eine E-Mail-Adresse ein!'), "index.php?mod=install&action=adminaccount");
		elseif ($_POST["password"] == "") $func->error(t('Bitte geben Sie ein Kennwort ein!'), "index.php?mod=install&action=adminaccount");
		elseif ($_POST["password"] != $_POST["password2"]) $func->error(t('Das Passwort und seine Verifizierung stimmen nicht überein!'), "index.php?mod=install&action=adminaccount");
		else {
			$db->query("INSERT INTO {$config["tables"]["user"]} SET
					username = 'ADMIN',
					email='{$_POST["email"]}',
					password = '". md5($_POST["password"]) ."',
					type = '3'
					");
			$userid = $db->insert_id();
			// Admin zur Party hinzuf�gen
			$party->add_user_to_party($userid, 1, "1", "1");
			$db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid = '$userid'");

			$func->confirmation(t('Der Adminaccount wurde erfolgreich angelegt.'), "index.php?mod=install&action=adminaccount");
		}
	break;
	
	default:
		$dsp->SetForm("index.php?mod=install&action=adminaccount&step=2");
		$dsp->AddTextFieldRow("email", t('E-Mail'), $user, "");
		$dsp->AddPasswordRow("password", t('Kennwort'), $pass, "");
		$dsp->AddPasswordRow("password2", t('Kennwort wiederholen'), $pass, "");
		$dsp->AddFormSubmitRow("next");

		$dsp->AddBackButton("index.php?mod=install", "install/admin");
		$dsp->AddContent();
	break;
}
?>
