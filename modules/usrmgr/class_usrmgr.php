<?
class UsrMgr {
  function LockAccount($userid) {
    global $db, $config;

    $db->query("UPDATE {$config["tables"]["user"]} SET locked = 1 WHERE userid=". (int)$userid);
  }


  function UnlockAccount($userid) {
    global $db, $config;

    $db->query("UPDATE {$config["tables"]["user"]} SET locked = 0 WHERE userid=". (int)$userid);
  }


	function GeneratePassword() {
		return rand(10000, 99999);
	}


	function SendSignonMail(){
		global $cfg, $func, $templ, $dsp, $mail;

		$anmelde_schluss = "";
		if ($_SESSION['party_info']['s_enddate'] > 0) $anmelde_schluss = "Anmeldeschluß: ". $func->unixstamp2date($_SESSION['party_info']['s_enddate'], date) .HTML_NEWLINE;

		if ($_GET["signon"]) $message = $cfg["signon_signonemail_text"];
		else $message = $cfg["signon_signonemail_text_register"];

		$message = str_replace('%USERNAME%', $_POST['username'], $message);
		$message = str_replace('%EMAIL%', $_POST['email'], $message);
		$message = str_replace('%PASSWORD%', $_SESSION['tmp_pass'], $message);
		$message = str_replace('%CLAN%', $_POST['clan'], $message);
		$message = str_replace('%PARTYNAME%', $_SESSION['party_info']['name'], $message);
		$message = str_replace('%PARTYURL%', $cfg['sys_partyurl'], $message);
		$message = str_replace('%MAXGUESTS%', $_SESSION['party_info']['max_guest'], $message);

		if ($mail->create_inet_mail($_POST["firstname"]." ".$_POST["lastname"], $_POST["email"], $cfg["signon_signonemail_subject"], $message, $cfg["sys_party_mail"])) return true;
		else return false;
	}


	function WriteXMLStatFile() {
		global $cfg, $db, $config,$party;

		include_once ("inc/classes/class_xml.php");
		$xml = new xml;
		$output = '<?xml version="1.0" encoding="UTF-8"?'.'>'."\r\n";

		$part_infos = $xml->write_tag("name", $cfg["feed_partyname"], 2);
		$part_infos .= $xml->write_tag("link", $cfg["sys_partyurl"], 2);
		$part_infos .= $xml->write_tag("language", "de-de", 2);
		$lansuite = $xml->write_master_tag("part_infos", $part_infos, 1);

		$registered = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["party_user"]} WHERE party_id = {$party->party_id}");
		$paid = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["party_user"]} WHERE (paid = 1) AND party_id = {$party->party_id}");

		$stats = $xml->write_tag("guests", ($registered["anz"] - 1), 2);
		$stats .= $xml->write_tag("paid_guests", $paid["anz"], 2);
		$stats .= $xml->write_tag("max_guests", $_SESSION['party_info']['max_guest'] , 2);
		$stats .= $xml->write_tag("signon_start", $_SESSION['party_info']['s_startdate'], 2);
		$stats .= $xml->write_tag("signon_end", $_SESSION['party_info']['s_enddate'], 2);
		$lansuite .= $xml->write_master_tag("stats", $stats, 1);

		$output .= $xml->write_master_tag("lansuite version=\"1.0\"", $lansuite, 0);

		if (is_writable("ext_inc/party_infos/")) {
			if ($fp = @fopen("ext_inc/party_infos/infos.xml", "w")) {
				if (!@fwrite($fp, $output)) return false;
			@fclose($fp);
			} else return false;
		} else return false;
		return true;
	}

}
?>
