<?php
switch($_GET["step"]) {
	default:
		$sec->unlock("ip_block");

		$dsp->NewContent("Blacklist-Verwaltung", "Hier können Sie IPs die Verwendung von LanSuite verweigern");
		$dsp->SetForm("index.php?mod=misc&action={$_GET["action"]}&step=2");

		$blacklist = "";
		$res = $db->query("SELECT ip FROM {$config["tables"]["ip_blacklist"]}");
		while ($ip = $db->fetch_array($res)) $blacklist .= $ip["ip"] ."\n";
		$db->free_result($res);
		$dsp->AddTextAreaRow("blacklist", "<b>Blacklist</b>" . HTML_NEWLINE ."<i>Einträge durch Zeilenumbruch trennen</i>", $blacklist, $error["blacklist"]);

		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=misc", "misc/ip_block"); 
		$dsp->AddContent();
	break;
	
	case 2:
		if (!$sec->locked("ip_block")) {
			$db->query("TRUNCATE TABLE {$config["tables"]["ip_blacklist"]}");
			$blacklist = split("\n", $_POST["blacklist"]);
			foreach ($blacklist as $entry) $db->query("INSERT INTO {$config["tables"]["ip_blacklist"]} SET ip = '$entry'");

			$func->confirmation("Blacklist wurde aktuallisiert", "index.php?mod=misc&action=ip_block");

			$sec->lock("ip_block");
		}
	break;
		
}
?>
