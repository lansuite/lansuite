<?
if (get_cfg_var("safe_mode") == 1) {
	$dsp->NewContent("Lansin-TV (tm) - Fernbedienung", "");
	$func->error("Sie m&uuml;sen erst in der php.ini safe_mode=Off setzten bevor sie das Modul verweden k&ouml;nnen.","?mod=lansintv");
}


$ssh_data = $db->query_first("SELECT player_bin, ssh_user, ssh_host FROM {$config["tables"]["lansintv_admin"]}");
$control = "";

switch($_GET["control"]){
	case "prev":
	break;

	case "play":
		$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && cd lansintv_client && screen -dmS suxx sh lansinplayer.sh\"");

	break;

	case "pause":
		shell_exec("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && killall -STOP {$ssh_data["player_bin"]}\"");
	break;

	case "resume":
		shell_exec("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && killall -CONT {$ssh_data["player_bin"]}\"");
	break;

	case "stop":
		$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && fuser -9 -k ~/lansintv_client/lansinplayer.sh\"");
		$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && killall -9 {$ssh_data["player_bin"]}\"");
	break;

	case "next":
		//$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \" fuser -k /usr/local/bin/rve\"");
		//$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && fuser -9 -k {$ssh_data["lansinplayer_client"]}/lansinplayer.sh\"");
		$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && fuser -k -9 {$ssh_data["player_bin"]}\"");
		//$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"export DISPLAY=:0 && if fuser /usr/local/bin/mplayer; then killall -CONT mplayer; else cd {$ssh_data["lansinplayer_client"]} && screen -dmS suxx sh lansinplayer.sh;fi\"");
	break;

	case "up":
		shell_exec("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"aumix -v +5\"");
	break;

	case "down":
		shell_exec("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"aumix -v -5\"");
	break;

	case "mute":
		echo "MUTE!";
		shell_exec("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"aumix -v -100\"");
	break;

	case "form":
		if ($_POST['newsticker'] != "") {
			$text = $_POST['newsticker'];
			$loops = $_POST['loops'];

			$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \" fuser -k /usr/local/bin/rve\"");
			$control .= exec ("screen -dmS suxx ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \"/usr/local/bin/rve -c CCFF33 -F BATTLE3.TTF 320 240 0 hscroll_text -r $loops '$text' > myfifo.fifo\"");
		} else {
			$control .= exec ("ssh {$ssh_data["ssh_user"]}@{$ssh_data["ssh_host"]} \" fuser -k /usr/local/bin/rve\"");
		}
	break;
}

$dsp->NewContent("Lansin-TV (tm) - Fernbedienung", "");

$dsp->AddDoubleRow("Track-control", "<a href=\"?mod=lansintv&action=remote&control=play\">Play[>]</a>&nbsp;&nbsp;&nbsp;
			<a href=\"?mod=lansintv&action=remote&control=pause\">Pause[\"]</a>&nbsp;&nbsp;&nbsp;
			<a href=\"?mod=lansintv&action=remote&control=resume\">Resume[\"]</a>&nbsp;&nbsp;&nbsp;
			<a href=\"?mod=lansintv&action=remote&control=stop\">Stop[o]</a>&nbsp;&nbsp;&nbsp;
			<a href=\"?mod=lansintv&action=remote&control=next\">Next[>>]</a>
			");
$dsp->AddDoubleRow("Sound-control", "<a href=\"?mod=lansintv&action=remote&control=up\">Turn Up[+]</a>&nbsp;&nbsp;&nbsp;
			<a href=\"?mod=lansintv&action=remote&control=down\">Turn down[-]</a>&nbsp;&nbsp;&nbsp;
			<a href=\"?mod=lansintv&action=remote&control=mute\">Turn Off[x]</a>
			");
$dsp->AddHRuleRow();
$dsp->AddTextFieldRow("newsticker", "Newsticker", $_POST["newsticker"], "");

$loop_arr = array("5",
				"10",
				"15",
				"20",
				"35",
				"60");
$t_array = array();
reset ($loop_arr);
while (list ($key, $val) = each ($loop_arr)) array_push ($t_array, "<option>$val</option>");
$dsp->AddDropDownFieldRow("loops", "Loops", $t_array, $loop_error[$z]);

$dsp->SetForm("?mod=lansintv&action=remote&control=form", "", "", "multipart/form-data");

$dsp->AddFormSubmitRow("send");

$dsp->AddBackButton("?mod=lansintv", "lansintv/upload");
$dsp->AddContent();
?>
