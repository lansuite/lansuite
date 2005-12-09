<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			whatsup.php
*	Module: 			misc
*	Main editor: 		denny@one-network.org
*	Last change:
*	Description: 		View important changes in last minutes..
*	Remarks: 		---
*
**************************************************************************/
/*
* Neue überlegung für whatsup:
*
* oben gibt es nur ein paar statistischen zahlenausgaben für alle module (wenn aktiviert)
* unten gibt es dann eine liste der änderungen, sortiert nach zeit und nicht jeweils nach modul
*
* für unten denkmodel:
*
* - jeweilige tabelle lesen und alle rows zu einem array hinzufügen
* - array sortieren nach zeit
* - ausgeben
*
*/


// Zeitgrenze bestimmen

$backtime = 1500;
$sqltimestamp = date("YmdHis", time() - $backtime);
$timestamp = time()-$backtime;
$minuten = $backtime/60;


//********* STATS START *****************
	$row = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["user"]} AS u LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid=p.user_id WHERE party_id={$party->party_id} AND (p.paid=1 or p.checkin>0) AND u.type = 1");
	$row2 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["user"]} AS u LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid=p.user_id WHERE party_id={$party->party_id} AND p.checkin>1 AND p.checkout=0 AND u.type=1");
	$row3 = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["user"]} AS u LEFT JOIN {$config["tables"]["party_user"]} AS p ON u.userid=p.user_id WHERE party_id={$party->party_id} AND p.checkout>1 AND u.type=1");
	$row4  = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["stats_auth"]} WHERE logtime>=$timestamp");

		$templ['misc']['show']['row']['text']['info']['text'] =	 "on: ".$row4["n"]." &nbsp; in: ".$row2["n"]." &nbsp; out: ".$row3["n"]." von ".$row["n"];

			$templ['misc']['show']['case']['control']['stats'] .= $dsp->FetchModTpl("misc","whatsup_show_row_text");
//********* STATS END *****************

$link = "<a href=>{$templ['misc']['show']['row']['info']['linktext']}</a>";


$z = 0;

// USER START
	$query = $db->query("SELECT userid, username, UNIX_TIMESTAMP(changedate) AS changedate FROM {$config["tables"]["user"]} WHERE changedate>'$sqltimestamp'");
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {
			AddToList("User: ".$row["username"],"index.php?mod=usrmgr&action=details&userid=".$row['userid'],$row["changedate"]);
		}
	}
// USER END


// TROUBLETICKET START
/*
	$query = $db->query("SELECT ttid, caption, status, UNIX_TIMESTAMP(changedate) AS changedate FROM {$config["tables"]["troubleticket"]}"); //  WHERE changedate>'$sqltimestamp'
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {
			AddToList("Troubleticket: ".$row["caption"],"index.php?mod=usrmgr&action=details&userid=".$row['ttid'],$row["changedate"]);
		}
	}

*/
// TROUBLETICKET END


// RENT USER START
	$query = $db->query("SELECT rentid, userid, UNIX_TIMESTAMP(changedate) AS changedate FROM {$config["tables"]["rentuser"]}"); //  WHERE changedate>'$sqltimestamp'
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {
			AddToList("Verleih, User: ".$row["userid"],"index.php?mod=rent&action=details&userid=".$row['rentid'],$row["changedate"]);
		}
	}
// RENT USER END

// RENT EQUIP START
	$query = $db->query("SELECT stuffid, UNIX_TIMESTAMP(changedate) AS changedate FROM {$config["tables"]["rentstuff"]}"); //  WHERE changedate>'$sqltimestamp'
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_array($query)) {
			AddToList("Verleih, Equipment: ".$row["stuffid"],"index.php?mod=rent&action=details&userid=".$row['rentid'],$row["changedate"]);
		}
	}
// RENT EQUIP END



function AddToList($caption,$link,$timestamp){
 global $z, $test;
	$test[$z][0] = $caption;
	$test[$z][1] = $link;
	$test[$z][2] = $timestamp;
	$z++;
}

// OUT

	if(sizeof($test)>0) {
		foreach($test as $key){
			$temparray[] = $key[2];
		}

		array_multisort($temparray, SORT_DESC, $test);

		foreach($test as $key){
			 $templ['misc']['show']['row']['info']['text1'] = strftime("%a. %d.%b, %H:%M:%S - ",$key[2]).$key[0];
			 $templ['misc']['show']['row']['control']['link'] = $key[1];
			 $templ['misc']['show']['case']['control']['history'] .= $dsp->FetchModTpl("misc","whatsup_show_row");
		}
	} //if
	else
	{
     $templ['misc']['show']['row']['text']['info']['text']  = "<strong>".$lang['misc']['wu_no_changes']."</strong>";
     $templ['misc']['show']['case']['control']['history'] .= $dsp->FetchModTpl("misc","whatsup_show_row_text");
	}




// Load Whatsup Template
$templ['index']['info']['content'] .= $dsp->FetchModTpl("misc","whatsup_show_case");


?>
