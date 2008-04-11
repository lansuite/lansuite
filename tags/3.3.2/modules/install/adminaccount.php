<?php
$db->connect();

$dsp->NewContent($lang["install"]["admin_caption"], $lang["install"]["admin_subcaption"]);

$find = $db->query("SELECT * FROM {$config["tables"]["user"]}");
if ($db->num_rows($find) == 0) {
	$dsp->AddSingleRow("<font color=\"red\">{$lang["install"]["admin_warning"]}</font>"); 
}
$db->free_result($find);

switch($_GET["step"]) {
	case 2:
		if ($_POST["email"] == "") $func->error($lang["install"]["admin_err_noemail"], "index.php?mod=install&action=adminaccount");
		elseif ($_POST["password"] == "") $func->error($lang["install"]["admin_err_nopw"], "index.php?mod=install&action=adminaccount");
		elseif ($_POST["password"] != $_POST["password2"]) $func->error($lang["install"]["admin_err_pwnotequal"], "index.php?mod=install&action=adminaccount");
		else {
			$db->query("INSERT INTO {$config["tables"]["user"]} SET
					username = 'ADMIN',
					email='{$_POST["email"]}',
					password = '". md5($_POST["password"]) ."',
					type = '3'
					");
			$userid = $db->insert_id();
			// Admin zur Party hinzufügen
			$party->add_user_to_party($userid, 1, "1", "1");
			$db->query("INSERT INTO {$config["tables"]["usersettings"]} SET userid = '$userid'");

			$func->confirmation($lang["install"]["admin_success"], "index.php?mod=install&action=adminaccount");
		}
	break;
	
	default:
		$dsp->SetForm("index.php?mod=install&action=adminaccount&step=2");
		$dsp->AddTextFieldRow("email", $lang["install"]["admin_email"], $user, "");
		$dsp->AddPasswordRow("password", $lang["install"]["conf_pass"], $pass, "");
		$dsp->AddPasswordRow("password2", $lang["install"]["admin_pass2"], $pass, "");
		$dsp->AddFormSubmitRow("next");

		$dsp->AddBackButton("index.php?mod=install", "install/admin");
		$dsp->AddContent();
	break;
}
?>
