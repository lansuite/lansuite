<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		changeclanpw.php
*	Module: 		Usermanager
*	Main editor: 		marco@chuchi.tv
*	Last change: 		21.01.2006
*	Description: 		Changes Clan-passwords
*	Remarks: 		Ready for release
*
**************************************************************************/

$clanid = $_GET['clanid'];

// Check Clan
if($_GET['clanid'] == ""){
	$func->error($lang['usrmgr']['clanpw_noclan'],"index.php?mod=home");
// Check Permission
}elseif ($_GET['clanid'] != $auth['clanid'] && $auth['type'] < "2"){
	$func->error($lang['usrmgr']['clanpw_noperm'],"index.php?mod=home");
// Change Clanpassword
}else{

	switch ($_GET['step']){
		case 2:
				if($_POST['newclanpw'] != $_POST['newclanpw2']){
					$error['clanpwdiff'] = $lang['usrmgr']['clanpw_diffpw'];
					$_GET['step'] = 1;
				}

				$oldpw = $db->query_first("SELECT password FROM {$config['tables']['clan']} WHERE clanid='$clanid'");
				if(md5($_POST['clanoldpw']) !=  $oldpw['password'] && $oldpw['password'] != ""){
					$error['clanoldpw'] = $lang['usrmgr']['clanpw_oldpw'];
					$_GET['step'] = 1;
				}


	}


	switch ($_GET['step']){
		default: $dsp->NewContent($lang['usrmgr']['clanpw_caption'],$lang['usrmgr']['clanpw_subcaption']);
				 $dsp->SetForm("index.php?mod=usrmgr&action=changeclanpw&step=2&clanid={$_GET['clanid']}");
				 $dsp->AddPasswordRow('clanoldpw',$lang["usrmgr"]["chpwd_oldpassword"],"",$error['clanoldpw']);
				 $dsp->AddPasswordRow('newclanpw',$lang["usrmgr"]["chpwd_password"],"",$error['clanpwdiff']);
				 $dsp->AddPasswordRow('newclanpw2',$lang["usrmgr"]["chpwd_password2"],"","");
				 $dsp->AddFormSubmitRow("change");
				 $dsp->AddBackButton("index.php?mod=home");
				 $dsp->AddContent();
				 break;


		case 2:
				$newclanpw = md5($_POST['newclanpw']);
				$db->query_first("UPDATE {$config['tables']['clan']} SET password='$newclanpw' WHERE clanid='$clanid'");

				$clanuser = $db->query("SELECT userid, username, email FROM {$config['tables']['user']} WHERE clanid='$clanid'");

				while ($data = $db->fetch_array($clanuser)) {
					$mail->create_mail($auth['userid'],$data['userid'],$lang['usrmgr']['clanpw_haschange_sub'],str_replace("%%USER%%",$auth['username'],$lang['usrmgr']['clanpw_haschange']));
					$mail->create_inet_mail($data['username'],$data['email'],$lang['usrmgr']['clanpw_haschange_sub'],str_replace("%%USER%%",$auth['username'],$lang['usrmgr']['clanpw_haschange']),$cfg["sys_party_mail"]);
				}

				$func->log_event(str_replace("%%USER%%",$auth['username'],$lang['usrmgr']['clanpw_haschange']),1);
				$func->confirmation($lang['usrmgr']['clanpw_haschange_sub'],"index.php?mod=home");
				break;
	}
}

?>