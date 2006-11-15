<?php
$serverid = $_GET["serverid"];

$server = $db->query_first("SELECT a.*, b.userid, b.username
		FROM {$config["tables"]["server"]} AS a
		LEFT JOIN {$config["tables"]["user"]} AS b ON a.owner = b.userid
		WHERE serverid = '$serverid'
		");
     
if($server == "") $func->error($lang["server"]["no_server_error"], "index.php?mod=server&action=show");
else {

	//Just show details if the user is not adding, deleting or chaning his comment
	if($_GET["mcact"] == "" || $_GET["mcact"] == "show") {

		$dsp->NewContent($lang["server"]["details_caption"], str_replace("%CAPTION%", $server["caption"], $lang["server"]["details_subcaption"]));

		$dsp->AddDoubleRow($lang["server"]["details_name"], $server["caption"]);
		$dsp->AddDoubleRow($lang["server"]["details_owner"], $server["username"] ." <a href=\"index.php?mod=usrmgr&action=details&userid={$server['userid']}\"><img src=\"design/". $_SESSION["auth"]["design"] ."/images/arrows_user.gif\" border=\"0\"></a>");

		$type_descriptor["gameserver"] = $lang["server"]["details_gameserver"];	
		$type_descriptor["ftp"] = $lang["server"]["details_ftpserver"];
		$type_descriptor["irc"] = $lang["server"]["details_ircserver"];
		$type_descriptor["web"] = $lang["server"]["details_webserver"];
		$type_descriptor["proxy"] = $lang["server"]["details_proxyserver"];
		$type_descriptor["misc"] = $lang["server"]["details_miscserver"];
		$dsp->AddDoubleRow($lang["server"]["details_servertype"], $type_descriptor[$server["type"]]);

		// Wenn Intranetversion, Servererreichbarkeit testen
		if ($cfg["sys_internet"] == 0 and (!get_cfg_var("safe_mode"))) {
			include_once("modules/server/ping_server.inc.php");	   
			ping_server($server["ip"],$server["port"]);

			// Gescannte Daten neu auslesen
			$server_scan = $db->query_first("SELECT special_info, available, success, scans, UNIX_TIMESTAMP(lastscan) AS lastscan from {$config["tables"]["server"]} WHERE serverid = '$serverid'");

			($server_scan["available"] == 1) ?
				$serverstatus = "<div class=\"tbl_green\">{$lang["server"]["details_service_available"]}</div>" : $serverstatus = "<div class=\"tbl_red\">{$lang["server"]["details_not_available"]}</div>";

			($server_scan["scans"] >= 1) ?
				$accessibleness = round((($server_scan["success"])/($server_scan["scans"])*100), 1)."%"
				: $accessibleness = $lang["server"]["details_not_tested"];

			$dsp->AddDoubleRow($lang["server"]["details_state"], $serverstatus);
			$dsp->AddDoubleRow($lang["server"]["details_accessibleness"], $accessibleness);
			$dsp->AddDoubleRow($lang["server"]["details_scannedinfos"], $server_scan["special_info"]);
			$dsp->AddDoubleRow($lang["server"]["details_lastscan"], $func->unixstamp2date($server_scan["lastscan"], "datetime"));

		} else {
			// Im Internet Server nicht testen
			$dsp->AddDoubleRow($lang["server"]["details_state"], $lang["server"]["details_on_party_only"]);
			$dsp->AddDoubleRow($lang["server"]["details_accessibleness"], $lang["server"]["details_on_party_only"]);
			$dsp->AddDoubleRow($lang["server"]["details_scannedinfos"], $lang["server"]["details_on_party_only"]);
			$dsp->AddDoubleRow($lang["server"]["details_lastscan"], $lang["server"]["details_on_party_only"]);
		}

		$dsp->AddDoubleRow($lang["server"]["details_ipaddr"], $server["ip"]);
		$dsp->AddDoubleRow($lang["server"]["details_port"], $server["port"]);

		if ($server["os"] == "") $server["os"] = "<i>". $lang["server"]["details_no_statement"] ."</i>";
		$dsp->AddDoubleRow($lang["server"]["details_os"], $server["os"]);

		($server["cpu"] == "0") ? $server["cpu"] = "<i>". $lang["server"]["details_no_statement"] ."</i>" : $server["cpu"] = $server["cpu"]." Megaherz";
		($server["ram"] == "0") ? $server["ram"] = "<i>". $lang["server"]["details_no_statement"] ."</i>" : $server["ram"] = $server["ram"]." Megabyte";
		($server["hdd"] == "0") ? $server["hdd"] = "<i>". $lang["server"]["details_no_statement"] ."</i>" : $server["hdd"] = $server["hdd"]." Gigabyte";
		$dsp->AddDoubleRow("CPU", $server["cpu"]);
		$dsp->AddDoubleRow("RAM", $server["ram"]);
		$dsp->AddDoubleRow("HDD", $server["hdd"]);

		($server["pw"] == 1) ? $password = $lang["server"]["details_yes"] : $password = $lang["server"]["details_no"];
		$dsp->AddDoubleRow($lang["server"]["details_password"], $password);

		$dsp->AddDoubleRow($lang["server"]["details_description"], $func->text2html($server["text"]));

		$buttons = "";
		if ($_SESSION["auth"]["type"] > 1 OR $_SESSION["auth"]["userid"] == $server["owner"]) {
			$buttons .= $dsp->FetchButton("index.php?mod=server&action=change&step=2&serverid=$serverid", "edit", $lang["server"]["details_edit"]) ." ";
			$buttons .= $dsp->FetchButton("index.php?mod=server&action=delete&step=2&serverid=$serverid", "delete", $lang["server"]["details_delete"]) ." ";
		}
		if($server["type"] == "web") {
			$buttons .= $dsp->FetchButton("http://{$server['ip']}:{$server['port']}", "open", $lang["server"]["details_openpage"], "_blank") ." ";
		}
		if ($buttons) $dsp->AddDoubleRow("", $buttons);

		$dsp->AddBackButton("index.php?mod=server&action=show", "server/show"); 
		$dsp->AddContent();
	}

	// Including comment-engine     
	if($_SESSION["auth"]["login"] == 1) {
		include("modules/mastercomment/class_mastercomment.php");
		$comment = new Mastercomment($vars,"index.php?mod=server&action=show_details&serverid=" . $_GET["serverid"],"server",$_GET["serverid"],$server["caption"]);
		$comment->action();
	}
	//End comment-engine	
}
?>
