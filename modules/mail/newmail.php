<?php

$user_id = $_GET["userID"];

// Error-Step
switch($_GET["step"]) {
	case 3:
			
		$body_i = strlen($_POST["msgbody_text"]);
		$sub_i =  strlen($_POST["subject_text"]);

		if($body_i > 5000) {
			$msgbody_error = $lang["mail"]["new_err_max5000"];
			$_GET["step"] = 2;
		}

		if($body_i == '') {
		 $msgbody_error = $lang["mail"]["new_err_min1"];
		 $_GET["step"] = 2;
		}

		if($sub_i == '') {
			$subject_error = $lang["mail"]["new_err_subject"];
			$_GET["step"] = 2;
		}
		if($sub_i > 100) {
			$subject_error = $lang["mail"]["new_err_maxsubj"];
			$_GET["step"] = 2;
		}
	break;
}


// set pointer direkt to mastersearch selection
if ($_GET["step"] == "") $_GET["step"]="userbyms";


switch($_GET["step"]) {
	default:
		$questarr[0] = $lang["mail"]["new_select1"];
		$questarr[1] = $lang["mail"]["new_select2"];
		$questarr[2] = $lang["mail"]["new_select3"];

		$linkarr[0]	 = "index.php?mod=mail&action=newmail&step=userbyms";
		$linkarr[1]	 = "index.php?mod=mail&action=newmail&step=userbygroup";
		$linkarr[2]	 = "index.php?mod=mail&action=newmail&step=userbyclan";				

		$func->multiquestion($questarr, $linkarr, $lang["mail"]["new_toselect"]);
	break;

	// Übersicht ihrer Mails im Posteingang
	case "userbyms":
    $additional_where = 'u.type > 0';
    $current_url = 'index.php?mod=mail&action=newmail&step=userbyms';
    $target_url = 'index.php?mod=mail&action=newmail&step=2&userID=';
    include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;

	case 2:
		if ($user_id=='') $func->error($lang["mail"]["new_no_userid"], "index.php?mod=mail");

		$get_username = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userID = '$user_id'");

		if($vars["replyto"]) {
			$mail->get_mail($vars["replyto"]);
			$_POST["subject_text"] = "Re: ".$mail->mail_subject["text"];
		}

        $dsp->NewContent($lang["mail"]["new_caption"], str_replace("%USERNAME%", $get_username["username"], $lang["mail"]["new_subcaption"]));
        $dsp->SetForm("index.php?mod=mail&action=newmail&step=3&userID=$user_id&to_userid=$user_id&replyto=".$_GET["replyto"], "newmail");
        $dsp->AddTextFieldRow("subject_text", $lang["mail"]["new_subject"], $_POST["subject_text"], $subject_error,51);
        $dsp->AddTextAreaPlusRow("msgbody_text", $lang["mail"]["new_text"], $_POST["msgbody_text"], $msgbody_error);
        $dsp->AddFormSubmitRow("next");
        $dsp->AddContent();
	break;

	// set staus "reply"
	case 3:
		if($mail->create_mail($auth["userid"], $_GET["to_userid"], $_POST["subject_text"], $_POST["msgbody_text"])) {
			if ($_GET["replyto"]) $mail->set_status_reply($_GET["replyto"]);
			$func->confirmation($lang["mail"]["new_success"], "index.php?mod=mail");
		} 
		else $func->error($lang["mail"]["new_send_error"] ." ". $mail->error, "index.php?mod=mail");
	break;
}
?>
