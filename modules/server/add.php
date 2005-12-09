<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	 2.0
*	File Version:		 2.0
*	Filename: 			add.php
*	Module: 			Servermangement
*	Main editor: 		bennjamin@one-network.org
*	Last change: 		01-01-2003
*	Description: 		Adds a server to the system
*	Remarks: 			
*
**************************************************************************/

if (($_GET["action"] == "add") && ($_GET["step"] == ""))$_GET["step"] = 2;


if($auth["type"] == 1) {
	$get_paid = $db->query_first("SELECT paid FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']}");
}

if((!$cfg["server_admin_only"] AND $get_paid["paid"] == 1) OR ($auth["type"] > 1)) {

	switch($_GET["step"]) {
	case 3:
		//  ERRORS
		if(strlen($_POST["server_text"]) > 5000) {
			$server_text_error = $lang["server"]["add_commenterror"];
			$_GET["step"] = 2;
		}

		if($_POST["server_caption"] == "") {		
			$caption_error = $lang["server"]["add_captionerror"];
			$_GET["step"] = 2;
		}

		if($_POST["server_ipaddress"] == "") {
			$ipaddress_error = $lang["server"]["add_iperror"];
			$_GET["step"] = 2;
		} elseif($cfg["sys_internet"] == 0){
			$ip_address = gethostbyname($_POST["server_ipaddress"]);
			$explode = explode(".",$ip_address);
			$count = count($explode);
			if($count == 4) {
				if($explode[0] > 255 || $explode[1] > 255 || $explode[2] > 255 || $explode[3] > 255) {
					$ipaddress_error = $lang["server"]["add_ipvaliderror"];
					$_GET["step"] = 2;
				}			
			} else {
				$ipaddress_error = $lang["server"]["add_ipvaliderror"];
				$_GET["step"] = 2;
			}
		}

		if($_POST["server_port"] == "") {
			$port_error = $lang["server"]["add_porterror"];
			$_GET["step"] = 2;
		} elseif($_POST["server_port"] < 1 || $_POST["server_port"] > 65535) {
			$port_error = $lang["server"]["add_portvaliderror"];
			$_GET["step"] = 2;
		}
/*
		if($_POST["server_mhz"] != "") if(!is_numeric($_POST["server_mhz"])) {
			$mhz_error = $lang["server"]["add_integererror"];
			$_GET["step"] = 2;
		}

		if($_POST["server_ram"] != "") if(!is_numeric($_POST["server_ram"])) {
			$ram_error = $lang["server"]["add_integererror"];
			$_GET["step"] = 2;
		}

		if($_POST["server_hdd"] != "") if(!is_numeric($_POST["server_hdd"])) {
			$ram_error = $lang["server"]["add_integererror"];
			$_GET["step"] = 2;
		}
*/
	break;
	} // close switch


	switch($_GET["step"]) {
		default:
			$mastersearch = new MasterSearch($vars, "index.php?mod=server&action=change", "index.php?mod=server&action=change&step=2&serverid=", "");
			$mastersearch->LoadConfig("server", $lang["server"]["ms_search"], $lang["server"]["ms_result"]);
			$mastersearch->PrintForm();
			$mastersearch->Search();
			$mastersearch->PrintResult();

			$templ['index']['info']['content'] .= $mastersearch->GetReturn();
		break;

		case 2:
			session_unregister("add_blocker_server");

			$server = $db->query_first("SELECT * from {$config["tables"]["server"]} WHERE serverid = '{$_GET["serverid"]}'");

			if($_POST["server_caption"] == "")		$_POST["server_caption"] = $server["caption"];
			if($_POST["server_type"] == "")			$_POST["server_type"] = $server["type"];
			if($_POST["server_text"] == "")			$_POST["server_text"] = $server["text"];
			if($_POST["server_ipaddress"] == "")	$_POST["server_ipaddress"] = $server["ip"];
			if($_POST["server_port"] == "") 		$_POST["server_port"] = $server["port"];
			if($_POST["server_os"] == "") 			$_POST["server_os"] = $server["os"];
			if($_POST["server_mhz"] == "") 			$_POST["server_mhz"] = $server["cpu"];
			if($_POST["server_ram"] == "") 			$_POST["server_ram"] = $server["ram"];
			if($_POST["server_hdd"] == "") 			$_POST["server_hdd"] = $server["hdd"];
			if($_POST["server_password"] == "")		$_POST["server_password"] = $server["pw"];

			$dsp->NewContent($lang["server"]["add_caption"], $lang["server"]["add_subcaption"]);
			$dsp->SetForm("index.php?mod=server&action={$_GET["action"]}&step=3&serverid={$_GET["serverid"]}");

			$dsp->AddTextFieldRow("server_caption", $lang["server"]["details_name"], $_POST["server_caption"], $caption_error);

			$server_typen = array("gameserver" => $lang["server"]["details_gameserver"],
					"ftp" => $lang["server"]["details_ftpserver"],
					"irc" => $lang["server"]["details_ircserver"],
					"web" => $lang["server"]["details_webserver"],
					"proxy" => $lang["server"]["details_proxyserver"],
					"misc" => $lang["server"]["details_miscserver"]
					);
			$dd_array = array();
			while (list($key, $val) = each($server_typen)) {
				if($_POST["server_type"] == $key) $selected = "selected";
				else $selected = "";
				array_push ($dd_array, "<option $selected value=\"$key\">$val</option>");
			}	
			$dsp->AddDropDownFieldRow("server_type", $lang["server"]["details_servertype"], $dd_array, $type_error);

			$dsp->AddTextFieldRow("server_ipaddress", $lang["server"]["details_ipaddr"], $_POST["server_ipaddress"], $ipaddress_error);
			$dsp->AddTextFieldRow("server_port", $lang["server"]["details_port"], $_POST["server_port"], $port_error);
			$dsp->AddTextFieldRow("server_os", $lang["server"]["details_os"], $_POST["server_os"], $os_error, "", 1);
			$dsp->AddTextFieldRow("server_mhz", "CPU (MHz)", $_POST["server_mhz"], $mhz_error, "", 1);
			$dsp->AddTextFieldRow("server_ram", "RAM (MB)", $_POST["server_ram"], $ram_error, "", 1);
			$dsp->AddTextFieldRow("server_hdd", "HDD (GB)", $_POST["server_hdd"], $hdd_error, "", 1);

			if ($_POST["server_password"]) $checked = 1;
			else $checked = 0;
			$dsp->AddCheckBoxRow("server_password", $lang["server"]["details_password"], "", "", 1, $checked);

			$dsp->AddTextAreaPlusRow("server_text", $lang["server"]["details_description"], $_POST["server_text"], $server_text_error, "", "", 1);

			$dsp->AddFormSubmitRow("add");
			$dsp->AddBackButton("index.php?mod=server", "server/form"); 
			$dsp->AddContent();
		break; // BREAK CASE 2
		
		case 3:
			if($_SESSION["add_blocker_server"] == TRUE) $func->error("NO_REFRESH", "index.php?mod=server&action={$_GET["action"]}");
			else {
				switch ($_GET["action"]) {
					case "add":
						$add_it = $db->query("INSERT INTO {$config["tables"]["server"]} SET
							caption = '{$_POST["server_caption"]}',
							owner = '{$_SESSION["auth"]["userid"]}',
							text = '{$_POST["server_text"]}',
							ip = '{$_POST["server_ipaddress"]}',
							port = '{$_POST["server_port"]}',
							os = '{$_POST["server_os"]}',
							cpu = '{$_POST["server_mhz"]}',
							ram = '{$_POST["server_ram"]}',
							hdd = '{$_POST["server_hdd"]}',
							type = '{$_POST["server_type"]}',
							pw = '{$_POST["server_password"]}'
							");

						$func->confirmation($lang["server"]["add_success"], "index.php?mod=server&action=show");
					break;

					case "change":
						$server = $db->query_first("SELECT caption, owner FROM {$config[tables][server]} WHERE serverid = '{$_GET["serverid"]}'");

						if (($server["owner"] == $_SESSION["auth"]["userid"]) || ($_SESSION["auth"]["type"] > 1)) {
							$change_it = $db->query("UPDATE {$config[tables][server]} SET
								caption = '{$_POST["server_caption"]}',
								owner = '{$_SESSION["auth"]["userid"]}',
								text = '{$_POST["server_text"]}',
								ip = '{$_POST["server_ipaddress"]}',
								port = '{$_POST["server_port"]}',
								os = '{$_POST["server_os"]}',
								cpu = '{$_POST["server_mhz"]}',
								ram = '{$_POST["server_ram"]}',
								hdd = '{$_POST["server_hdd"]}',
								type = '{$_POST["server_type"]}',
								pw = '{$_POST["server_password"]}'
								WHERE serverid = '{$_GET["serverid"]}'
								");

							$func->confirmation($lang["server"]["change_success"], "index.php?mod=server&action=show");
						} else $func->information($lang["server"]["change_norights"], "index.php?mod=server&action=change");
					break;
				}

				$_SESSION["add_blocker_server"] = TRUE;
			}
		break; // BREAK CASE 3
			
	} // close switch step

} else $func->information($lang["server"]["add_paiderror"], "index.php?mod=server");
?>
