<?php

class Mail {

	var $mail_header;
	var $mail_subject;
	var $mail_body;
	var $mail_fromuserid;
	var $error;
	var $inet_from_mail;


	function set_status_delete ($mailID) {
		global $db, $config;

		if ($mailid != '0' OR $mailid != ''){
			$del_mail = $db->query("UPDATE {$config["tables"]["mail_messages"]} SET mail_status = 'delete' WHERE mailID = '$mailID'");
			if ($del_mail) return true;
			else return false;
		} else return false;
	}

	function set_status_read ($mailID) {
		global $db, $config;
		if ($mailid != '0' OR $mailid != '') {
			$zeit = time();
			$read_mail = $db->query("UPDATE {$config["tables"]["mail_messages"]} SET src_status = 'read', des_status = 'read', rx_date = '$zeit' WHERE mailID = '$mailID'");
			if ($read_mail) return true;
			else return false;
		} else return false;
	}

	function set_status_reply ($mailID) {
		global $db, $config;
		if ($mailid != '0' OR $mailid != '') {
			$zeit = time();
			$reply_mail	=	$db->query("UPDATE {$config["tables"]["mail_messages"]} SET des_status = 'reply' WHERE mailID = '$mailID'");
			if($reply_mail) return true;
			else return false;
		} else return false;
	}

	function get_mail($mailID) {
		global $db, $config, $func, $cfg;

		if ($mailID != ""){
			$g_mail = $db->query_first("SELECT mm.*, u1.username AS fromUsername, u2.username AS ToUsername
				FROM {$config["tables"]["mail_messages"]} AS mm 
				LEFT JOIN {$config["tables"]["user"]} AS u1 ON mm.FromUserID = u1.userid 
				LEFT JOIN {$config["tables"]["user"]} AS u2 ON mm.ToUserID = u2.userid 
				WHERE mailID = '$mailID'
				");
				
			$this->mail_header['from_userid'] = $g_mail['fromUserID'];
			
			$this->mail_fromuserid = $g_mail["fromUserID"];

			if ($this->mail_fromuserid == 0) $this->mail_header["from_username"] = "SYSTEM";
			else $this->mail_header["from_username"] = $g_mail["fromUsername"];

			$this->mail_header["to_userid"] 	= $g_mail["toUserID"];
			$this->mail_header["to_username"] 	= $g_mail["tousername"];

			$this->mail_header["mail_status"] 	= $g_mail["mail_status"];
			$this->mail_header["src_status"] 	= $g_mail["src_status"];
			$this->mail_header["des_status"] 	= $g_mail["des_status"];						

			$this->mail_header["priority"] 		= $g_mail["priority"];									

			$this->mail_subject["text"] 		= $g_mail["Subject"];	

			$this->mail_body["text"] 			= $g_mail["msgbody"];
//			$this->mail_body["length"] 			= strln($this->mail_body["text"]);

			$this->mail_header["sendtime_text"] = $func->unixstamp2date($g_mail["tx_date"],"daydatetime");
			$this->mail_header["sendtime_stamp"] = $g_mail["tx_date"];
						
			$this->mail_header["readtime_text"] = $func->unixstamp2date($g_mail["rx_date"],"daydatetime");
			$this->mail_header["readtime_stamp"] = $g_mail["rx_date"];

			if ($get_mail) return true;
			else return false;
		}
	} // END function


	function create_mail($from_userid, $to_userid, $subject_text, $msgbody_text) {
		global $db, $config, $func;

		if ($from_userid == "") {
			$this->error = "from_userid missing";
			return false;
		}
		if ($to_userid == "") {
			$this->error = "to_userid missing";
			return false;
		}

        $from_userid = (int) $from_userid;
        $to_userid = (int) $to_userid;

        $subject_text = $func->escape_sql($subject_text);
        $msgbody_text = $func->escape_sql($msgbody_text);

		$zeit = time();

		$c_mail = $db->query("INSERT INTO {$config["tables"]["mail_messages"]} SET
				mail_status = 'active',
				src_status 	= 'send',
				des_status 	= 'new',
				fromUserID 	= '$from_userid',
				toUserID 	= '$to_userid',
				Subject		= '$subject_text',
				msgbody		= '$msgbody_text',
				priority	= 'normal',
				tx_date		= '$zeit'
				");

		if ($c_mail) {
			$this->error = "OK";
			return true;
		} else {
			$this->error = "DB INSERT FAILED";
			return false;
		}
	} // END create_mail


	function create_sys_mail($to_userid, $subject_text, $msgbody_text ) {
		if ($this->create_mail("0", $to_userid, $subject_text, $msgbody_text)) return true;
		else return false;
	}

	
	function create_inet_mail($to_user_name, $to_user_email, $subject_text, $msgbody_text, $from) {
		global $cfg, $board_config;
		
    // Do not send, when in intranet mode
		if (!$cfg['sys_internet']) {
      $this->error = t('Um Internet-Mails zu versenden, muss sich Lansuite im Internet-Modus befinden');
      return false;
    }

		// Set default Sender-Mail, if non is set
		if ($this->inet_from_mail == "") $this->inet_from_mail = $cfg["sys_party_mail"];

    $this->inet_headers = "MIME-Version: 1.0\n";
    $this->inet_headers .= "Content-type: text/plain; charset=utf-8\n";
    $this->inet_headers .= "From: $from\n";
    $this->inet_headers .= "Reply-To: $from\n";

		// SNMP-Mail
		if ($cfg["mail_use_smtp"]) {
			$board_config["smtp_host"] = $cfg["mail_smtp_host"];
			$board_config["smtp_username"] = $cfg["mail_smtp_user"];
			$board_config["smtp_password"] = $cfg["mail_smtp_pass"];
			$board_config["board_email"] = $this->inet_from_mail;

      if (!$cfg['mail_utf8']) {
        $subject_text = utf8_decode($subject_text);
        $msgbody_text = utf8_decode($msgbody_text);
      }

			include_once("modules/mail/smtp.php");
			if (smtpmail("$to_user_name <$to_user_email>", $subject_text, $msgbody_text, $this->inet_headers)) return true;
			else return false;

		// PHP-Mail
		} else {
			if (mail("$to_user_name <$to_user_email>", $subject_text, $msgbody_text, $this->inet_headers)) return true;
			else return false;
		}
	}
	
	function delete_old_messages($to_user_id){
		global $db, $config, $func, $cfg;
		
		if ($to_user_id) {
  		$count = $db->query("SELECT * FROM {$config["tables"]["mail_messages"]} WHERE `toUserID` = $to_user_id AND `des_status` = 'read' ORDER BY `mail_status`  DESC, `rx_date` ASC");
  		$count_rows = $db->num_rows($count);

  		if(isset($cfg['mail_max_msg']) && $count_rows > $cfg['mail_max_msg']){
  			$db->query("DELETE FROM {$config["tables"]["mail_messages"]} WHERE `toUserID` = $to_user_id AND `des_status` = 'read' ORDER BY `mail_status`  DESC, `rx_date` ASC LIMIT " . ($count_rows - $cfg['mail_max_msg']));
  		}
    }
		
	}
} // END CLASS

?>
