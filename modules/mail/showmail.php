<?php

$mail_id = $_GET["mailID"];

if ($mail_id) {

	// get the mail, load mail-data to class-variables
	$mail->get_mail($mail_id);

	if ($auth["userid"] == $mail->mail_header["from_userid"] OR $auth["userid"] == $mail->mail_header["to_userid"]){

        $dsp->NewContent($lang["mail"]["showmail_title"], $lang["mail"]["showmail_mail_from"] ." <b>". $mail->mail_header["from_username"] ."</b> ". $lang["mail"]["showmail_mail_send"].": <b>". $mail->mail_header["sendtime_text"] ."</b>");

		$dsp->AddDoubleRow($lang["mail"]["subject"], $mail->mail_subject["text"]);
		$dsp->AddSingleRow($func->text2html($mail->mail_body["text"]));

		// parse REFERER-Action and set buttons
		$buttons = "";
		switch($_GET["ref"]) {
			default:
				$back_link = "index.php?mod=mail&action=inbox";
			break;

			case "in":
				$back_link = "index.php?mod=mail&action=inbox";
				$buttons .= $dsp->FetchButton("index.php?mod=mail&action=inbox&step=99&mailid=$mail_id", "delete");
				$buttons .= $dsp->FetchButton("index.php?mod=mail&action=newmail&step=2&userID={$mail->mail_header["from_userid"]}&replyto=$mail_id", "new_post");
			break;

			case "out":
				$back_link = "index.php?mod=mail&action=outbox";
			break;	

			case "trash":
				$back_link = "index.php?mod=mail&action=trash";
				$buttons .= $dsp->FetchButton("index.php?mod=mail&action=newmail&step=2&userID={$mail->mail_header["from_userid"]}&replyto=$mail_id", "new_post");
			break;	
		}

		if ($buttons) $dsp->AddDoubleRow("", $buttons);
		$dsp->AddBackButton($back_link, "showmail");
        $dsp->AddContent();

		// set Mail to "READ"
		if ($mail->mail_header['to_userid'] == $auth['userid']	
				AND	($mail->mail_header['readtime_stamp']=='' 
					OR $mail->mail_header['src_status']=='send' 
					OR $mail->mail_header['des_status']=='new'
				)
			) $mail->set_status_read($mail_id);
		
	} else $func->information($lang["mail"]["showmail_error"], "");
} else $func->error($lang["mail"]["showmail_nomailid"], "");
?>
