<?php

// STATS

	$templ['home']['show']['item']['info']['caption'] = $lang["home"]["stats_caption"] . " " . $_SESSION['party_info']['name'];
	$templ['home']['show']['item']['control']['row'] = "";

	// mit oder ohne orgas
    if($cfg["signon_showorga"] == 0) { $querytype = "type = 1"; } else { $querytype = "type >= 1"; }

	$stat_infos = $stats->get_stat();
    $row6 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["troubleticket"]} WHERE target_userid = '0'");
	$row7 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["troubleticket"]}");
	$row8 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["rentuser"]} WHERE back_orgaid = '' AND out_orgaid != ''");
	$row9 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["rentuser"]} WHERE back_orgaid > '0' AND out_orgaid > '0'");	
	
		$templ['home']['show']['row']['text']['info']['text'] =	 $lang["home"]["stats_guests"] .": ".$stat_infos['user_paid'];

		$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'troubleticket'");
		if ($module["active"]) {
			if($auth["type"]>=2) $templ['home']['show']['row']['text']['info']['text2'] = $lang["home"]["stats_tts"] .": ".$row6["n"]."/".$row7["n"];
		}
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
		unset( $templ['home']['show']['row']['text']['info']['text2'] );
					
		$templ['home']['show']['row']['text']['info']['text'] = $lang["home"]["stats_guests_checked"] .": ".$stat_infos['user_checkin'];
		$module = $db->query_first("SELECT active FROM {$config["tables"]["modules"]} WHERE name = 'rent'");
		if ($module["active"]) {		
			if($auth["type"]>=2) $templ['home']['show']['row']['text']['info']['text2'] = $lang["home"]["stats_rental"] .": ".$row8["n"]."/".$row9["n"];
		}
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
		unset( $templ['home']['show']['row']['text']['info']['text2'] );
		
		$templ['home']['show']['row']['text']['info']['text'] = $lang["home"]["stats_guests_checked_out"] .": ".$stat_infos['user_checkout'];
		$templ['home']['show']['row']['text']['info']['text2'] = "";
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
		
		$templ['home']['show']['row']['text']['info']['text'] = $lang["home"]["stats_user_visits"] .": ". $stat_infos['user_visits'];
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");
			
		$templ['home']['show']['row']['text']['info']['text'] = $lang["home"]["stats_user_online"] .": ". $stat_infos['user_online'];
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_text");

		
?>
