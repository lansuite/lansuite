<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0.3
*	File Version:		1.0
*	Filename: 			show_party.php
*	Module: 			signon
*	Main editor: 		Genesis marco@chuchi.tv
*	Last change: 		25.02.05
*	Description: 		Party übersicht und Anmeldung an mehrere Partys
*	Remarks:
*
**************************************************************************/

switch ($_GET['step']){
	default:
		
		$time = time();
		$row = $db->query("SELECT * FROM {$config['tables']['partys']} ORDER BY startdate ASC");
		
		// Auf vorhandene Partys Prüfen
		$currenttime = time();
		if ($db->num_rows($row) == 0) $func->error($lang['signon']['no_partys'], "index.php?mod=home");
		else {
			$row = $db->query("SELECT * FROM {$config['tables']['partys']} WHERE enddate >= $time ORDER BY enddate ASC");
			$dsp->NewContent($lang['signon']['party_list_caption'],$lang['signon']['party_list_subcaption']);
			// Neue Partys auflisten
			while ($res = $db->fetch_array($row)){
				// Daten umwandeln
				$signon_start = $func->unixstamp2date($res["sstartdate"], "datetime");
				$signon_end = $func->unixstamp2date($res["senddate"], "datetime");
				$party_start = $func->unixstamp2date($res["startdate"], "datetime");
				$party_end = $func->unixstamp2date($res["enddate"], "datetime");
				
				// Prüfen ob der User eingeloggt ist
				if ($auth['userid'] > 0){
				// Prüfen ob der user schon angemeldet ist
					$row_user = $db->query("SELECT * FROM {$config['tables']['party_user']} WHERE user_id={$auth['userid']} AND party_id={$res['party_id']}");
				
					if($db->num_rows($row_user) > 0){
						$user_signon = true;
					}else{
						$user_signon = false;
					}
				}else{
					$user_signon = false;
				}
				// Ausgabe mit Infos über Anmeldung	
				if($res['sstartdate'] >= $currenttime){
					$dsp->AddDoubleRow($res['name'], $lang['signon']['signon_start'] . ": " .  $signon_start . " (Party: $party_start - $party_end)");
					
				}elseif ($res['senddate'] <= $currenttime){
					
					if($user_signon){
						$dsp->AddDoubleRow("<a href='index.php?mod=signon&action=show_party&step=1&party_id={$res['party_id']}'>" . $res['name'] . "</a>",$party_start . " - " .$party_end . HTML_NEWLINE . "<strong>" . $lang['signon']['is_signon'] . "</strong>");				
					}else{
						$dsp->AddDoubleRow("<a href='index.php?mod=signon&action=show_party&step=1&party_id={$res['party_id']}'>" . $res['name'] . "</a>",$party_start . " - " .$party_end . HTML_NEWLINE . "<strong>" . $lang['signon']['signon_end'] . $signon_start . "</strong>");			
					}
					
				}else{
					if($user_signon){
						$dsp->AddDoubleRow("<a href='index.php?mod=signon&action=show_party&step=1&party_id={$res['party_id']}'>" . $res['name'] . "</a>",$party_start . " - " .$party_end . HTML_NEWLINE . "<strong>" . $lang['signon']['is_signon'] . "</strong>");				
					}else{
						$dsp->AddDoubleRow("<a href='index.php?mod=signon&action=show_party&step=1&party_id={$res['party_id']}'>" . $res['name'] . "</a>",$party_start . " - " .$party_end . HTML_NEWLINE . $dsp->FetchButton("index.php?mod=signon&action=add&signon=1&party_id={$res['party_id']}",'join'));				
					}
				}

							
				
			}
			
			// Alte Party Auflisten
			$row = $db->query("SELECT * FROM {$config['tables']['partys']} WHERE enddate < $time ORDER BY enddate ASC");
			
			if($db->num_rows($row) > 0){
				$dsp->AddHRuleRow();
				$dsp->AddSingleRow("<strong>{$lang['signon']['signon_history']}</strong>");
				while ($res = $db->fetch_array($row)){
					// Daten umwandeln
					$signon_start = $func->unixstamp2date($res["sstartdate"], "datetime");
					$signon_end = $func->unixstamp2date($res["senddate"], "datetime");
					$party_start = $func->unixstamp2date($res["startdate"], "datetime");
					$party_end = $func->unixstamp2date($res["enddate"], "datetime");

					// Prüfen ob der User eingeloggt ist
					if ($auth['userid'] > 0){
						// Prüfen ob der user schon angemeldet ist
						$row_user = $db->query("SELECT * FROM {$config['tables']['party_user']} WHERE user_id={$auth['userid']} AND party_id={$res['party_id']}");

						if($db->num_rows($row_user) > 0){
							$user_signon = "<strong>" . $lang['signon']['signon_true'] . "</strong>";
						}else{
							$user_signon = $lang['signon']['signon_false'];
						}
					}else{
						$user_signon = "";
					}

					$dsp->AddDoubleRow("<a href='index.php?mod=signon&action=show_party&step=1&party_id={$res['party_id']}'>" . $res['name'] . "</a>",$party_start . " - " .$party_end . HTML_NEWLINE . $user_signon);

				}
			}
			if($_SESSION["auth"]["type"] == 0){
				$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
			}
			$dsp->AddContent();
		}
		
	break;
		
	case 1:
		$currenttime = time();
		$row = $db->query_first("SELECT * FROM {$config['tables']['partys']} WHERE party_id={$party->party_id}");
		if($auth["userid"] > 0){
			$user = $db->query("SELECT * FROM {$config['tables']['party_user']} WHERE party_id={$party->party_id} AND user_id= {$auth['userid']}");
		}else{
			$user = $db->query("SELECT * FROM {$config['tables']['party_user']} WHERE party_id={$party->party_id}");
		}
		if($db->num_rows($user) > 0){
			$is_signon = true;
		}else{
			$is_signon = false;	
		}
		$dsp->NewContent($lang['signon']['show_party_caption'],$lang['signon']['show_party_subcaption']);
		$dsp->AddDoubleRow($lang['signon']['partyname'],$row['name']);
		$dsp->AddDoubleRow($lang['signon']['max_guest'],$row['max_guest']);
		$dsp->AddDoubleRow($lang['signon']['plz'],$row['plz']);
		$dsp->AddDoubleRow($lang['signon']['ort'],$row['ort']);
		$dsp->AddDoubleRow($lang['signon']['stime'],$func->unixstamp2date($row['startdate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['etime'],$func->unixstamp2date($row['enddate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['sstime'],$func->unixstamp2date($row['sstartdate'],"datetime"));
		$dsp->AddDoubleRow($lang['signon']['setime'],$func->unixstamp2date($row['senddate'],"datetime"));
		if(($row['sstartdate'] <= $currenttime) && ($row['senddate'] >= $currenttime) && !$is_signon){
			$dsp->AddDoubleRow('',$dsp->FetchButton("index.php?mod=signon&action=add&signon=1&party_id={$row['party_id']}",'join'));
		}
		$dsp->AddBackButton("index.php?mod=signon&action=show_party","signon/show_party");
		$dsp->AddContent();

	break;
}

?>
