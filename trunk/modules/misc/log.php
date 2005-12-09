<?php

	// Odering
	if($vars["orderby"] == "") {
		$orderby = "l.date,desc";
	} else {
		$orderby = $vars["orderby"];
	}

	$order = explode(",",$orderby);
	$order_column = $order["0"];
	$order_type = $order["1"];

	$templ['log']['show']['case']['control']['order'][$order_column][$order_type]= "_active";

	$found_entries = $db->query("SELECT date FROM {$config["tables"]["log"]}");
	$found_entries = $db->num_rows($found_entries);

	$pages = $func->page_split( $vars["log_page"], $config["size"]["log"], $found_entries, "index.php?mod=misc&action=log&orderby=$orderby", "log_page");

	// Define main temp vars
	$templ['log']['show']['case']['control']['working_link'] = "index.php?mod=misc&action=log&log_page={$_GET["log_page"]}";
	$templ['log']['show']['case']['control']['pages'] = $pages["html"];

	// Running Query
	$get_log_entries = $db->query("SELECT l.description, l.date, l.type, u.username FROM {$config["tables"]["log"]} l LEFT JOIN {$config["tables"]["user"]} u ON l.userid = u.userid ORDER BY $order_column $order_type {$pages["sql"]}");

	// Setting colours
	$colours[1] = "row_value";
	$colours[2] = "row_key";
	$colours[3] = "row_value_important";

	while( $log_entry = $db->fetch_array($get_log_entries) ) {

		if(strlen($log_entry["description"]) > 55) {
			$description = "<div title=\"{$log_entry["description"]}\">".substr($log_entry["description"],0,55) . "...</div>";
		} else {
			$description = "<div title=\"{$log_entry["description"]}\">".$log_entry["description"]."</div>";
		}

		if(strlen($log_entry["username"]) > 15) {
			$username = "<div title=\"{$log_entry["username"]}\">".substr($log_entry["username"],0,15) . "...</div>";
		} else {
			$username = "<div title=\"{$log_entry["username"]}\">".$log_entry["username"]."</div>";
		}

		if( $log_entry["type"] == 3 ) {
			$templ['log']['show']['row']['control']['colour'] = $colours[3];
		} else {
			if( $bg == 1 ) {
				$bg = 0;
				$templ['log']['show']['row']['control']['colour'] = $colours[1];
			} else {
				$bg = 1;
				$templ['log']['show']['row']['control']['colour'] = $colours[2];
			}
		}

		$templ['log']['show']['row']['info']['description'] = $description;
		$templ['log']['show']['row']['info']['date'] = $func->unixstamp2date($log_entry["date"],"datetime");

		// If the user at whom the error occured is know write his id, otherwise not
		if($log_entry["username"] != "") {
			$templ['log']['show']['row']['info']['userid'] = $username;
		} else {
			$templ['log']['show']['row']['info']['userid'] = "<i> ".$lang['misc']['log_unknown']." </i>";
		}

		// output rows
		$templ['log']['show']['case']['control']['rows'] .= $dsp->FetchModTpl("misc","log_show_row");

		unset($templ['log']['show']['row']['info']);

	}//while

 // Output case
 $templ['index']['info']['content'] .= $dsp->FetchModTpl("misc","log_show_case");

?>
