<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		changepw.php
*	Module: 		Usermanager
*	Main editor: 		michael@one-network.org, raphael@one-network.org, jochen@one-network.org
*	Last change: 		12.03.2003 16:32
*	Description: 		Changes user-passwords
*	Remarks: 		Ready for release
*
**************************************************************************/

$step 		= $_GET['step'];

switch($step) { 
	case 2:

		if ($_POST["password"] == "") {
			$password_error = $lang["usrmgr"]["chpwd_err_nopass"];
			$step = 1;
		}
		if ($_POST["password"] != $_POST["password2"]) {
			$password2_error = $lang["usrmgr"]["chpwd_err_wrong2ndpass"];
			$step = 1;
		}

		$get_dbpwd = $db->query_first("SELECT password FROM {$config["tables"]["user"]} WHERE userid = '{$_SESSION["auth"]["userid"]}'");
		if ($get_dbpwd["password"] <> md5($_POST["old_password"])) {
			$oldpassword_error = $lang["usrmgr"]["chpwd_err_wrongoldpass"];
			$step = 1;
		}
	break;
}

switch($step) {
	default:
		$dsp->NewContent($lang["usrmgr"]["chpwd_caption"], $lang["usrmgr"]["chpwd_subcaption"]);
		$dsp->SetForm("index.php?mod=usrmgr&action=changepw&step=2", "signon");

		$dsp->AddPasswordRow("old_password", $lang["usrmgr"]["chpwd_oldpassword"], $_POST["old_password"], $oldpassword_error, "", "", "onKeyUp=\"checkInput()\"");
		$dsp->AddPasswordRow("password", $lang["usrmgr"]["chpwd_password"], $_POST["password"], $password_error, "", "", "onKeyUp=\"checkInput()\"");
		$dsp->AddPasswordRow("password2", $lang["usrmgr"]["chpwd_password2"], $_POST["password2"], $password2_error);
		$dsp->AddDoubleRow($lang["usrmgr"]["chpwd_password_security"], str_replace("{default_design}", $_SESSION["auth"]["design"], $dsp->FetchModTPL("signon", "row_pw_security")));

		$dsp->AddFormSubmitRow("next");
		$dsp->AddBackButton("index.php", "usrmgr/change_pwd"); 
		$dsp->AddContent();
	break;

	case 2:
		$new_pwd = md5($_POST["password"]);

		$db->query("UPDATE {$config["tables"]["user"]} SET password = '$new_pwd' WHERE userid = '{$_SESSION["auth"]["userid"]}'");

		$_SESSION["auth"]["userpassword"] = $new_pwd;

		if(isset($_COOKIE["auth"]["userpassword"])) {
			setcookie("auth[userpassword]", "$auth[userpassword]", time()+(3600*24*365));
		}

		$func->confirmation($lang["usrmgr"]["chpwd_success"], "");
	break;

}
?>
