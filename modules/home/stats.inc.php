<?php
  $templ['home']['show']['item']['info']['caption'] = $lang["home"]["stats_caption"] . " " . $_SESSION['party_info']['name'];
  $templ['home']['show']['item']['control']['row'] = "";
  
if ($party->count > 0) {
  // With or without admins?
  if($cfg["guestlist_showorga"] == 0) { $querytype = "type = 1"; } else { $querytype = "type >= 1"; }
  
  $stat_infos = $stats->get_stat();
  $templ['home']['show']['item']['control']['row'] = '';
  
  // User paid / Checked In / Checked Out
  $templ['home']['show']['item']['control']['row'] .= $lang["home"]["stats_guests"] .': '. $stat_infos['user_paid'] .' / '. $stat_infos['user_checkin'] .' / '. $stat_infos['user_checkout'] . HTML_NEWLINE;
  
  // User overall / online
  $templ['home']['show']['item']['control']['row'] .= $lang["home"]["stats_user"] .": ". $stat_infos['user_visits'] .' / '. $stat_infos['user_online'] . HTML_NEWLINE;
}

// Additional Admin-Stats	
if ($auth["type"] >= 2) {
  // Troubletickets
  if (in_array('troubleticket', $ActiveModules)) {
    $row6 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["troubleticket"]} WHERE target_userid = '0'");
    $row7 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["troubleticket"]}");
    $templ['home']['show']['item']['control']['row'] .= $lang["home"]["stats_tts"] .": ".$row6["n"]." / ".$row7["n"] . HTML_NEWLINE;
  }
  
	// Rental
  if (in_array('rent', $ActiveModules)) {
    $row8 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["rentuser"]} WHERE back_orgaid = '' AND out_orgaid != ''");
    $row9 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["rentuser"]} WHERE back_orgaid > '0' AND out_orgaid > '0'");	
    $templ['home']['show']['item']['control']['row'] .= $lang["home"]["stats_rental"] .": ".$row8["n"]." / ".$row9["n"] . HTML_NEWLINE;
  }		
}			
?>
