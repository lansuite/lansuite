<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		ipgen.php
*	Module: 		seat
*	Main editor: 		raphael@lansuite.de
*	Last change: 		15.02.2003 16:04
*	Description: 		generate new IPs for all seatblocks
*	Remarks:
*
**************************************************************************/

$step 		= $_GET['step'];

switch($step) {

	default:

		$_POST["ipgen_a"] = "10";
		$_POST["ipgen_b"] = "10";
		$_POST["ipgen_c"] = "0";
		$_POST["ip_offset"] = "0";

		$templ['misc']['ipgen']['details']['control']['form_action']	= "index.php?mod=misc&action=ipgen&step=10";
		$templ['misc']['ipgen']['details']['info']['page_title'] 	= $lang['misc']['ip_gen'];

		$templ['misc']['ipgen']['control']['case'] .= $dsp->FetchModTpl("misc","misc_ipgen_details");
		$templ['index']['info']['content'] .= $dsp->FetchModTpl("misc","misc_ipgen");


	break;


	case 10:

		$ip_a  = $_POST["ipgen_a"];
		$ip_b  = $_POST["ipgen_b"];
		$ip_c  = $_POST["ipgen_c"];
		// Marco Müller
		$ip_offset  = $_POST['ipgen_offset'];

		$sort  = $_POST["ipgen_sort"];
		$br    = $_POST["ipgen_break"];
		$s_col = $_POST["ipgen_startcol"];
		$s_row = $_POST["ipgen_startrow"];

		$mastersearch = new MasterSearch($vars, 'index.php?mod=misc&action=ipgen&step=10', "index.php?mod=misc&action=ipgen&step=11&ipa=$ip_a&ipb=$ip_b&ipc=$ip_c&ipoffset=$ip_offset&sort=$sort&br=$br&scol=$s_col&srow=$s_row&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 11:

		$ip_a  = $_GET["ipa"];
		$ip_b  = $_GET["ipb"];
		$ip_c  = $_GET["ipc"];
		// Marco Müller
		$ip_offset  = $_GET["ipoffset"];

		$block_id = $_GET["blockid"];
		$sort  = $_GET["sort"];
		$br    = $_GET["br"];
		$s_col = $_GET["scol"];
		$s_row = $_GET["srow"];

/*
echo $sort;
echo $s_col;
echo $s_row;
*/

		// Lösche alle IP einträge (sicher ist sicher)
		$del_ips = $db->query("UPDATE {$config["tables"]["seat_seats"]} SET ip = '' WHERE blockid='$block_id'");

		// ermittle wieviele reihen und spalten der sitzplan hat
		$get_size = $db->query_first("SELECT rows,cols FROM {$config["tables"]["seat_block"]} WHERE blockid='$block_id'");
		$max_row = $get_size["rows"];
		$max_col = $get_size["cols"];

    if ($br != 'rowcol' and $br != 'rowcol2') $check_status = 'status < 10 AND status > 0 AND';
    else $check_status = '';

		// Hole alle seatids nach reihen sortiert in ein array
		$count = 0;
		if($sort=="col") { $order = "col $s_col,row $s_row"; } else { $order = "row $s_row,col $s_col"; }
		$query_sub = $db->query("SELECT seatid, row, col FROM {$config["tables"]["seat_seats"]} WHERE $check_status blockid='$block_id' ORDER BY $order");
		while($row_seat_ids = $db->fetch_array($query_sub)) {
			$seat_ids[$count] = $row_seat_ids["seatid"];
			$seat_row[$count] = $row_seat_ids["row"];
			$seat_col[$count] = $row_seat_ids["col"];
			$count++;
		} // while


		// Marco Müller
		// $ip_d = 0;
		$ip_d = ($ip_offset - 1);

		$durchg = 0;

		// zähle das array und update db
		for ($i=1;$i<=$count;$i++) {

			$durchg++;

			// aktuelle anzahl der Spalten falls Sitzplaetze deaktiviert
			$colakt = $seat_col[$i-1];
			$get_size = $db->query("SELECT row FROM {$config["tables"]["seat_seats"]} WHERE blockid='$block_id' and col='$colakt'");
			$max_row_durchg = $db->num_rows($get_size);

			// aktuelle anzahl der Zeilen falls Sitzplaetze deaktiviert
			$rowakt = $seat_row[$i-1];
		    $get_size2 = $db->query("SELECT col FROM {$config["tables"]["seat_seats"]} WHERE blockid='$block_id' and row='$rowakt'");
			$max_col_durchg = $db->num_rows($get_size2);

			//ip hochzählen
			$ip_d++;

			// überlauf der 4. stelle abfangen und 3. hochzählen
			if($ip_d>254) {
				$ip_c++;
				$ip_d = 1;
				// echo "<font color=red>normaler Überlauf</font><br><br>";
			}


			// seat_id aus array ermitteln
			$seat_id = $seat_ids[$i-1];


			// ip adresse aufbauen
			$ip = $ip_a.".".$ip_b.".".$ip_c.".".$ip_d;


			// db updaten
			$db->query("UPDATE {$config["tables"]["seat_seats"]} SET ip='$ip' WHERE seatid='$seat_id'");


			/*
			echo $i." zähler<br>";
			echo $ip." ip<br>";
			echo $seat_id." seatid<br>";
			echo $seat_row[$i-1]." seat row<br>";
			echo $seat_col[$i-1]." seat col<br>";
			echo $max_row_durchg . " max Rows Durchg.<br>";
			echo $max_col_durchg . " max Cols Durchg.<br>";
			*/


		switch($br) {

			case "rowcol": 	// wenn gewünscht nach jeder Reihe oder Spalte die 3. stelle hochzählen

				if($sort=="row") {
					if($durchg==$max_col_durchg) {
						$ip_c++;
						$ip_d = 0;
						$durchg = 0;
						// echo "<font color=red>Max COL = SEAT COL (Überlauf)</font><br><br>";
					}
				}

				if($sort=="col") {
					if($durchg==$max_row_durchg) {
						$ip_c++;
						$ip_d = 0;
						$durchg = 0;
						// echo "<font color=red>Max ROW = SEAT ROW (Überlauf)</font><br><br>";
					}
				}

			break;


			case "rowcol2": 	// wenn gewünscht nach jeder ZWEITEN Reihe oder Spalte

				if($sort=="row") {
					if($durchg==$max_col_durchg) {
						if($sp2 == false) {
						 $sp2 = true;
						 $durchg = 0;
						} else {
							$ip_c++;
							$ip_d = 0;
							$durchg = 0;
							$sp2 = false;
						}
						// echo "<font color=red>Max COL = SEAT COL (Überlauf)</font><br><br>";
					}
				}

				if($sort=="col") {
					if($durchg==$max_row_durchg) {
						if($sp2 == false) {
						 $sp2 = true;
						 $durchg = 0;
						} else {
							$ip_c++;
							$ip_d = 0;
							$durchg = 0;
							$sp2 = false;
						}
						// echo "<font color=red>Max ROW = SEAT ROW (Überlauf)</font><br><br>";
					}
				}
			break;
		} // switch



		} //for loop

		$func->confirmation($lang['misc']['cf_add_ip'], "index.php?mod=misc&action=ipgen");

	break;


} // switch


?>
