<?php
$serverid = $_GET["serverid"];

$server = $db->query_first("SELECT a.*, b.userid, b.username
		FROM {$config["tables"]["server"]} AS a
		LEFT JOIN {$config["tables"]["user"]} AS b ON a.owner = b.userid
		WHERE serverid = '$serverid'
		");
     
if($server == "") $func->error(t('Der von Ihnen aufgerufene Server existiert nicht'), "index.php?mod=server&action=show");
else {

	//Just show details if the user is not adding, deleting or chaning his comment
	if($_GET["mcact"] == "" || $_GET["mcact"] == "show") {

		$dsp->NewContent(t('Serverdetails'), t('Auf dieser Seite sehen Sie alle Details zum Server <b>%1</b>. Durch eine Klick auf den Zur&uuml;ck-Button gelangen Sie zur Übersicht zur&uuml;ck', $server["caption"]));

		$dsp->AddDoubleRow(t('Name'), $server["caption"]);
		$dsp->AddDoubleRow(t('Besitzer'), $server["username"] .' '. $dsp->FetchUserIcon($server['userid']));

		$type_descriptor["gameserver"] = t('Gameserver');	
		$type_descriptor["ftp"] = t('FTP-Server');
		$type_descriptor["irc"] = t('IRC-Server');
		$type_descriptor["web"] = t('Webserver');
		$type_descriptor["proxy"] = t('Proxy / Gateway');
		$type_descriptor["misc"] = t('Sonstiges');
		$dsp->AddDoubleRow(t('Servertyp'), $type_descriptor[$server["type"]]);

		// Wenn Intranetversion, Servererreichbarkeit testen
		if ($cfg["sys_internet"] == 0 and (!get_cfg_var("safe_mode"))) {
			include_once("modules/server/ping_server.inc.php");	   
			ping_server($server["ip"],$server["port"]);

			// Gescannte Daten neu auslesen
			$server_scan = $db->qry_first('SELECT special_info, available, success, scans, UNIX_TIMESTAMP(lastscan) AS lastscan from %prefix%server WHERE serverid = %int%', $serverid);

			($server_scan["available"] == 1) ?
				$serverstatus = "<div class=\"tbl_green\">".t('Dienst erreichbar')."</div>" : $serverstatus = "<div class=\"tbl_red\">".t('Dienst nicht ereichbar')."</div>";

			($server_scan["scans"] >= 1) ?
				$accessibleness = round((($server_scan["success"])/($server_scan["scans"])*100), 1)."%"
				: $accessibleness = t('Noch nicht getestet');

			$dsp->AddDoubleRow(t('Status'), $serverstatus);
			$dsp->AddDoubleRow(t('Erreichbarkeit'), $accessibleness);
			$dsp->AddDoubleRow(t('Gescannte Infos'), $server_scan["special_info"]);
			$dsp->AddDoubleRow(t('Letzter Scan'), $func->unixstamp2date($server_scan["lastscan"], "datetime"));

		} else {
			// Im Internet Server nicht testen
			$dsp->AddDoubleRow(t('Status'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
			$dsp->AddDoubleRow(t('Erreichbarkeit'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
			$dsp->AddDoubleRow(t('Gescannte Infos'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
			$dsp->AddDoubleRow(t('Letzter Scan'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
		}

		$dsp->AddDoubleRow(t('IP-Adresse / Domain'), $server["ip"]);
		$dsp->AddDoubleRow(t('MAC Adresse'), $server["mac"]);
		$dsp->AddDoubleRow(t('Port'), $server["port"]);

		if ($server["os"] == "") $server["os"] = "<i>". t('Keine Angabe') ."</i>";
		$dsp->AddDoubleRow(t('Betriebssystem'), $server["os"]);

		($server["cpu"] == "0") ? $server["cpu"] = "<i>". t('Keine Angabe') ."</i>" : $server["cpu"] = $server["cpu"]." Megaherz";
		($server["ram"] == "0") ? $server["ram"] = "<i>". t('Keine Angabe') ."</i>" : $server["ram"] = $server["ram"]." Megabyte";
		($server["hdd"] == "0") ? $server["hdd"] = "<i>". t('Keine Angabe') ."</i>" : $server["hdd"] = $server["hdd"]." Gigabyte";
		$dsp->AddDoubleRow("CPU", $server["cpu"]);
		$dsp->AddDoubleRow("RAM", $server["ram"]);
		$dsp->AddDoubleRow("HDD", $server["hdd"]);

		($server["pw"] == 1) ? $password = t('Ja') : $password = t('Nein');
		$dsp->AddDoubleRow(t('Passwort gesch&uuml;tzt'), $password);

		$dsp->AddDoubleRow(t('Beschreibung'), $func->text2html($server["text"]));

		$buttons = "";
		if ($_SESSION["auth"]["type"] > 1 OR $_SESSION["auth"]["userid"] == $server["owner"]) {
			$buttons .= $dsp->FetchButton("index.php?mod=server&action=change&step=2&serverid=$serverid", "edit", t('editieren')) ." ";
			$buttons .= $dsp->FetchButton("index.php?mod=server&action=delete&step=2&serverid=$serverid", "delete", t('l&ouml;schen')) ." ";
		}
		if($server["type"] == "web") {
			$buttons .= $dsp->FetchButton("http://{$server['ip']}:{$server['port']}", "open", t('Webseite &ouml;ffnen'), "_blank") ." ";
		}
		if ($buttons) $dsp->AddDoubleRow("", $buttons);

		$dsp->AddBackButton("index.php?mod=server&action=show", "server/show"); 
		$dsp->AddContent();
	}

	// Including comment-engine     
	if($_SESSION["auth"]["login"] == 1) {
  	include('inc/classes/class_mastercomment.php');
  	new Mastercomment('server', $_GET['serverid']);
	}
	//End comment-engine	
}
?>
