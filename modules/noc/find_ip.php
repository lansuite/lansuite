<?php

switch ($_GET['step']){
	case "2":
			if($_POST['ip'] == ''){
				$error_noc['ip'] = $lang['noc']['device_ip_error'];
				$_GET['step'] = 1;
			}
}


switch ($_GET['step']){
	
	default:
			$dsp->NewContent($lang['noc']['find_caption'],$lang['noc']['find_subcaption']);
			$dsp->SetForm("index.php?mod=noc&action=find&step=2");
			$dsp->AddTextFieldRow("ip",$lang['noc']['device_ip'],$_POST['ip'],$error_noc['ip']);
			$dsp->AddFormSubmitRow("next");
			$dsp->AddBackButton("index.php?mod=noc");
			$dsp->AddContent();
	break;
	
	case "2":
			$dsp->NewContent($lang['noc']['find_caption'],$lang['noc']['find_subcaption']);
			$dsp->AddDoubleRow($lang['noc']['ip'],$_POST['ip']);
			$noc->IPtoMAC_arp($_POST['ip']);
			$dsp->AddSingleRow("<a href='index.php?mod=noc&action=find&step=3&ip={$_POST['ip']}'>Alle Ports Updaten</<a>");
			$dsp->AddBackButton("index.php?mod=noc&action=find&step=1");
			$dsp->AddContent();
	break;

	case "3":
			$func->question($lang['noc']['update_question'],"index.php?mod=noc&action=find&step=4&ip={$_GET['ip']}","index.php?mod=noc&action=find&step=1");
	break;
	case "4":
			$dsp->NewContent($lang['noc']['find_caption'],$lang['noc']['find_subcaption']);
			
			// Alle Device Updaten
			$row = $db->query_first("SELECT * FROM {$config["tables"]["noc_devices"]}");
			while ($db->fetch_array($row)){
				$noc->getMacAddress($row["ip"],$row["readcommunity"],$row["id"],$row["sysDescr"]);
			}
			$dsp->AddDoubleRow($lang['noc']['ip'],$_GET['ip']);
			$noc->IPtoMAC_arp($_GET['ip']);
			$dsp->AddBackButton("index.php?mod=noc&action=find&step=1");
			$dsp->AddContent();
	break;
}


?>
