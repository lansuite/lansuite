<?php
class MasterSearch {
	var $config;
	var $vars;
	var $data;
	var $class;

	// Konstruktor
	function MasterSearch($vars, $working_link, $target_link, $sql_additional="") {
		$this->config['working_link']   = $working_link;
		$this->config['target_link']    = $target_link;
		$this->config['sql_additional'] = $sql_additional;
		$this->vars                     = $vars;
	}

	// Ab hier sind die Funktionen für die MasterSearch
	function LoadConfig($task, $form_title = "", $case_title = "") {
		global $config, $auth, $party, $cfg, $db, $lang;

		$this->config['task']                 = $task;
		$this->config['load']                 = 1;
		$this->config['inputs'][0]['title']   = "Suche";
		$this->config['inputs'][0]['name']    = "search_keywords";

		$file = "modules/mastersearch/$task.inc.php";

		if (file_exists($file)) include($file);
		else {
			$this->config['load']          = 0;
			$this->config['search_fields'] = array();
			$this->config['result_fields'] = array();
			$this->config['sql_statment']  = "";
			$this->config['title']         = $title;
		}

		// Hier wird der Standardtitel ausgew?hlt falls keine Titel ?bergeben wurde
		if ($form_title == "") $this->config['form_title'] = $this->config['title'];
		else $this->config['form_title'] = $form_title;

		if ($case_title == "") $this->config['case_title'] = $this->config['title'];
		else $this->config['case_title'] = $case_title;
	}


	function PrintForm($title = "") {
		global $func, $auth, $dsp, $templ, $lang, $gd;

		// If Config is loaded
		if ($this->config['load'] == 1 ) {		//if($this->data['count']!=0) {
			$vars    = $this->vars;
			$templ["ms"]["title"] = $this->config['form_title'];

			// Generate Input-Template
			$templ['ms']['inputs'] = "";
			foreach ($this->config['inputs'] as $input) {
				$templ['ms']['input'] = "";
				$templ['ms']['title'] = $input['title'];

				// Select Input
				if ($input['type'] == "select") {
					$templ['ms']['input'] .= "<select name=\"{$input['name']}\" class=\"form\">\n";
					foreach ($input['options'] as $key => $value ) {
						(trim($vars[$input['name']]) == trim($key)) ? $selected = " selected" : $selected = "";
						$templ['ms']['input'] .= "<option value=\"$key\"$selected>$value</option>\n";
					}
					$templ['ms']['input'] .= "</select>\n";
				
				// Other Input
				} else
					$templ['ms']['input'] .= "<input type=\"text\" name=\"{$input['name']}\" size=\"30\" class=\"form\" value=\"{$vars[$input['name']]}\" />\n";

				$templ['ms']['inputs'] .= $dsp->FetchModTpl("mastersearch", "mastersearch_search_inputs");
			}

			// Order By Input
			if ($this->config["orderby_dropdown"]) {
				if ($_POST["order"] == "" and $this->vars['orderby'] != "") $_POST["order"] = substr($this->vars['orderby'], 0, strpos($this->vars['orderby'], ","));
				if ($_POST["direction"] == "" and $this->vars['orderby'] != "") $_POST["direction"] = substr($this->vars['orderby'], strpos($this->vars['orderby'], ",")+1, 4);

				$templ['ms']['title'] = "<i>Sortieren nach</i>";
				$templ['ms']['input'] = "<select name=\"order\" class=\"form\">\n";
				foreach ($this->config["orderby_dropdown"] as $key => $value) {
					((string)$_POST["order"] == (string)$key) ? $selected = " selected" : $selected = "";
					$templ['ms']['input'] .= "<option value=\"$key\"$selected>$value</option>\n";
				}
				$templ['ms']['input'] .= "</select> \n";

				$templ['ms']['input'] .= "<select name=\"direction\" class=\"form\">\n";
				((string)$_POST["direction"] == "ASC") ? $selected = " selected" : $selected = "";
				$templ['ms']['input'] .= "<option value=\"ASC\"$selected>Aufsteigend</option>\n";
				((string)$_POST["direction"] == "DESC") ? $selected = " selected" : $selected = "";
				$templ['ms']['input'] .= "<option value=\"DESC\"$selected>Absteigend</option>\n";
				$templ['ms']['input'] .= "</select>\n";

				$templ['ms']['inputs'] .= $dsp->FetchModTpl("mastersearch", "mastersearch_search_inputs");
			}

			// If data available -> return
			$gd->CreateButton("search");
			if ($this->data['count'] > 0 or $this->config['hidden_searchform'] == false) {
				$templ['ms']['action'] = $this->config['working_link'];
				$templ['ms']['search_hint'] = $lang['ms']['search_hint'];
				$this->return .= $dsp->FetchModTpl("mastersearch", "mastersearch_search");
			}
		} else $func->error($lang['ms']['error'], $lang['ms']['error_no_loadcfg']);
	}


	function Search() {
		global $db, $func;

		$this->data['load'] = 1;

		// Ordering
		if ($_POST["order"]) $this->vars['orderby'] = $_POST["order"] .",". $_POST["direction"];
		if  ($this->vars['orderby'] == "") $this->data['orderby']  = $this->config['orderby'];
		else $this->data['orderby'] = $this->vars['orderby'];

		$order                      = explode(",", $this->data['orderby']);
		$order[1]                   = strtoupper($order[1]);
		$this->data['order_column'] = $order[0];
		$this->data['order_type']   = $order[1];
		$sqlorderby                 = $order[0]." ".$order[1];

		$search_keywords	= trim($this->vars["search_keywords"]);
		$search_keywords	= ereg_replace("  ", " ", $search_keywords);
		$search_keywords	= ereg_replace(", ", ",", $search_keywords);
		$search_keywords	= ereg_replace(" ,", ",", $search_keywords);
		$search_keywords	= ereg_replace(" ", ",", $search_keywords);
		$search_keywords	= explode(",", $search_keywords);

		// cut GROUP BY Statement 
		$sql_group_by = "";
		if(stristr($this->config['sql_additional'], "GROUP BY")){
			$sql_group_by = stristr($this->config['sql_additional'], "GROUP BY");
			$this->config['sql_additional'] = substr($this->config['sql_additional'], 0, strlen($this->config['sql_additional']) - strlen($sql_group_by));
		}

		$search = "";

		// Generate SQL-Where for Dropdown-Inputs
		$i = 1;
		while ($this->config['inputs'][$i]){
			if ($this->vars["search_select$i"] != "" and $this->vars["search_select$i"] != "all") {
				$search .= "(";
				$s = 1;
				while ($this->config['inputs'][$i]['sql'][$s] != "") {

					$search_select = explode(';', $this->vars["search_select$i"]);
					foreach ($search_select as $select_item) {

						if (substr($select_item, 0, 1) == "!") {
							$select_item = substr($select_item, 1, strlen($select_item));
							$search .= "(". $this->config['inputs'][$i]['sql'][$s]." != '$select_item') OR ";
						} else {
							$search .= "(". $this->config['inputs'][$i]['sql'][$s]." = '$select_item') OR ";
						}
					}
					$s++;
				}
				$search = substr($search, 0, strlen($search) - 4);
				($serach == "(") ? $serach = "" : $search .= ") AND ";
			}
			$i++;
		}


		// Generate SQL-Where for Text-Inputs
		if (is_array($search_keywords)) {
			foreach ($search_keywords as $key) if ($key) {

				$search .= "(";

				$key_1337 = $key;
				$key_1337 = str_replace ("o", "(o|0)", $key_1337);
				$key_1337 = str_replace ("O", "(O|0)", $key_1337);
				$key_1337 = str_replace ("l", "(l|1|\\\\||!)", $key_1337);
				$key_1337 = str_replace ("L", "(L|1|\\\\||!)", $key_1337);
				$key_1337 = str_replace ("e", "(e|3|€)", $key_1337);
				$key_1337 = str_replace ("E", "(E|3|€)", $key_1337);
				$key_1337 = str_replace ("t", "(t|7)", $key_1337);
				$key_1337 = str_replace ("T", "(T|7)", $key_1337);
				$key_1337 = str_replace ("a", "(a|@)", $key_1337);
				$key_1337 = str_replace ("A", "(A|@)", $key_1337);
				$key_1337 = str_replace ("s", "(s|5|$)", $key_1337);
				$key_1337 = str_replace ("S", "(S|5|$)", $key_1337);
				$key_1337 = str_replace ("z", "(z|2)", $key_1337);
				$key_1337 = str_replace ("Z", "(Z|2)", $key_1337);

				$d = 0;
				foreach ($this->config['search_fields'] as $col) {
					switch ($this->config['search_type'][$d]) {
						case "exact": $search .= "($col = '$key') OR "; break;
						case "1337": $search .= "($col REGEXP '$key_1337') OR "; break;
						default: $search .= "($col LIKE '%$key%') OR "; break;
					}
					$d ++;
				}
				$search = substr($search, 0, strlen($search) - 4);

				$search .= ") AND ";
			}
		}

		// Cut last AND
		if (substr($search, strlen($search) - 5, 5) == " AND ") $search = substr($search, 0, strlen($search) - 5);
		if ($search == "") $search = "1";

		if ($this->config['where']) $this->config['sql_additional'] .= ' AND '. $this->config['where'];
		$query = $this->config['sql_statment']." WHERE $search {$this->config['sql_additional']} $sql_group_by ORDER BY $sqlorderby";

		// Send SQL
		$this->data['result'] = $db->query($query);
		$this->data['count'] = $db->num_rows($this->data['result']);
	}


	function PrintResult( ) {
		global $func, $db, $optional_header, $auth, $templ, $dsp, $lang;

		$templ["ms"]["ms_optional_header_code"] = $optional_header;

		if ($this->data['count'] == 0) $func->no_items($this->config['no_items_caption'], $this->config['no_items_link'], "free");
		elseif ($this->config['load'] == 1) {
			$design = $auth["design"];
			$vars = $this->vars;
			$templ["ms"]["title"] = $this->config['case_title'];
			$workingp = $this->config['working_link'];

			// Seitenlinks generieren
			foreach ($this->config['inputs'] as $input) $workingp .= "&{$input['name']}={$this->vars[$input['name']]}";
			$working = $workingp."&page={$this->vars['page']}";

			$templ["ms"]["target"] = $this->config['target_link'];
			if ($this->config['entrys_page'] < $this->data['count']) $entrys_page = $this->config['entrys_page'];
			else $entrys_page = $this->data['count'];

			if ($this->config['entrys_page'] != 0) {
				$page_data = $func->page_split($this->vars['page'], $this->config['entrys_page'], $this->data['count'], "{$workingp}&orderby={$this->data['orderby']}", "page");
				$templ["ms"]["pages"] = $page_data["html"];
			} else {
				$templ["ms"]["pages"] = "";
				$page_data['a'] = "0";
				$page_data['b'] = $this->data['count'];
			}

			// Tabellenkopf generieren
			$templ["ms"]["table_head"] = "";

			foreach ($this->config['result_fields'] as $result_field) {
				$order = "";
				if ($result_field['sqlrow']) {
					// Aufsteigend-Pfeil generieren
					if ($this->data['order_column'] == $result_field['sqlrow'] && $this->data['order_type'] == "ASC") {
						$order .= "<a href=\"{$working}&orderby={$result_field['sqlrow']},ASC\"><img src=\"design/{$design}/images/arrows_orderby_asc_active.gif\" border=\"0\" /></a>";
					} else {
						$order .= "<a href=\"{$working}&orderby={$result_field['sqlrow']},ASC\"><img src=\"design/{$design}/images/arrows_orderby_asc.gif\" border=\"0\" /></a>";
					}
					$order .= " ";

					// Absteigend-Pfeil generieren
					if( $this->data['order_column'] == $result_field['sqlrow'] && $this->data['order_type'] == "DESC" ) {
						$order .= "<a href=\"{$working}&orderby={$result_field['sqlrow']},DESC\"><img src=\"design/{$design}/images/arrows_orderby_desc_active.gif\" border=\"0\" /></a>";
					} else {
						$order .= "<a href=\"{$working}&orderby={$result_field['sqlrow']},DESC\"><img src=\"design/{$design}/images/arrows_orderby_desc.gif\" border=\"0\" /></a>";
					}
				}
				if ($result_field['align'] != "") $align = "align=\"{$result_field['align']}\" ";
				else $align = "";

				$templ["ms"]["table_head"] .= "<td {$align}width=\"{$result_field['width']}\" class=\"row_key\" nowrap>{$result_field['name']} $order</td>\n";
			}

			// Tabellen Einträge generieren
			$templ["ms"]["table_entrys"] = "";
			$y = 0;
			$z = 0;
			while ($row = $db->fetch_array($this->data['result'])) {

				$templ["ms"]["table_entry"] = "";
				$this->row = $row;
				if ($page_data['a'] <= $y && $z < $page_data['b']) {

					$z++;
					foreach ($this->config['result_fields'] as $result_field) {
						$this->current_result_field = $result_field;

						if (!is_array($result_field['row'])) $org_text = $func->db2text($row[$result_field['row']]);
						$text = "";

						// Display Icons in front of Item
						$icon = "";
						if ($result_field['iconname'] != "") {
							$icon = "<img src=\"design/$design/images/{$result_field['iconname']}\" border=\"0\" /> ";
							if ($result_field['iconlink'] != "") {
								$icon = "<a href=\"{$result_field['iconlink']}{$row[$this->config['linkcol']]}\">$icon</a> ";
							}
						}
						$text .= $icon;

						// Convert Item using callback
						if ($result_field['callback'] != "") {
							if (is_array($result_field['row'])) {
								unset($param);
								foreach ($result_field['row'] as $row_field) $param[] = $func->db2text($row[$row_field]);
								$text .= $this->$result_field['callback']($param);
							} else $text .= $this->$result_field['callback']($org_text);
						} else {
							// Cut Text?
							if (intval($result_field['maxchar']) > 0 and strlen($org_text) > intval($result_field['maxchar'])) {
								$org_text = substr($org_text, 0, intval($result_field['maxchar']) - 2) ."...";
							}
							$text .= $org_text;
						}

						// Link Item?
						if (!$this->config['list_only'] and !$result_field['list_only']) {
							$target = $this->config['target_link'] . $row[$this->config['linkcol']];
							if($result_field['ext_link']) $target .= "&" . $result_field['ext_link'];
							$text = "<a class=\"menu\" href=\"$target\">{$text}</a>";
						}

						// Display Checkbox?
						if ($result_field['checkbox'] != "") {
							$text = "<input type=\"checkbox\" name=\"{$result_field['checkbox']}[]\" value=\"{$row[$this->config['linkcol']]}\" /> $text";
							$this->config['checkbox'] = 1;
						}

						// Display Profil-Icon?
						if ($result_field['profil'])
							$text .= " <a href=\"index.php?mod=usrmgr&action=details&userid={$row[$this->config['userid']]}\"><img src=\"design/$design/images/arrows_user.gif\" border=\"0\" /></a>";
						// Display complete profil
						if ($result_field['fullprofil'])
							$text .= HTML_NEWLINE . $this->GetUsername($row[$this->config['userid']]) ."<a href=\"index.php?mod=usrmgr&action=details&userid={$row[$this->config['userid']]}\"> <img src=\"design/$design/images/arrows_user.gif\" border=\"0\" /></a>";
							
						// Generate Text
						$text = "<div title=\"{$org_text}\">$text</div>";

						if ($result_field['align']) $align = "align=\"{$result_field['align']}\" ";
						else $align = "";

						$templ["ms"]["table_entry"] .= "<td {$align}nowrap width=\"{$result_field['width']}\" class=\"row_value\" height=\"30\">$text</td>\n";
					}

					$templ["ms"]["table_entrys"] .= $dsp->FetchModTpl("mastersearch", "mastersearch_entry_case");
				}
				$y++;
			}

			$templ["ms"]["form"] = "";
			if ($this->config['checkbox'] == 1) {
				if ($this->config['action_select']) {
					$templ["ms"]["form"] .= "<select name=\"action_select\" onChange=\"javascript:change_selection();\">";
					$templ["ms"]["form"] .= "<option value=\"\">{$lang['ms']['select_action']}</option>";
					foreach ($this->config['action_select'] as $key => $val){
						$templ["ms"]["form"] .= "<option value=\"$key\">$val</option>";
						if ($this->config['action_secure'][$key]) $templ['ms']['js_secure'] .= "if (document.ms_case.action_select.value == \"$key\"){return confirm(\"Sind Sie sicher, dass Sie die gewählte Aktion '$val' ausführen möchten?\");}";
					}
					$templ["ms"]["form"] .= "</select>";
				}
				$templ["ms"]["form"] .= " <input type=\"image\" value=\"send\" name=\"ms_send\" src=\"ext_inc/auto_images/{default_design}/{language}/button_search.png\" />";
			}
			$templ['ms']['sort_hint'] = $lang['ms']['sort_hint'];
			$this->return .= $dsp->FetchModTpl("mastersearch", "mastersearch_case");

		} else $func->error($lang['ms']['error'], $lang['ms']['error_no_search']);
	}
	
	// Gibt den Return wieder
	function GetReturn() {
		return $this->return;
	}
	
	// ?bergangsl?sung wird die Tagen ge?ndert
	function page_split($current_page,$max_entries_per_page,$overall_entries,$working_link,$var_page_name) {
	if( $max_entries_per_page > 0 && $overall_entries >= 0 && $working_link != "" && $var_page_name != "") {
		if($current_page == "") {
			$page_sql = "LIMIT 0," . $max_entries_per_page;
			$page_a = 0;
			$page_b = $max_entries_per_page;
		}
		if($current_page == "all") {
			$page_sql = "";
			$page_a = 0;
			$page_b = $overall_entries;
		} else	{
			$page_sql = ("LIMIT " . ($current_page * $max_entries_per_page) . ", " . ($max_entries_per_page));
			$page_a = ($current_page * $max_entries_per_page);
			$page_b = ($max_entries_per_page);
		}
		if($overall_entries > $max_entries_per_page) {
			$page_output = ("Seiten: ");
			if( $current_page != "all" && ($current_page + 1) > 1 ) {
				$page_output .= ("&nbsp; " . "<a class=\"menue\" href=\"" . $working_link . "&" . $var_page_name . "=" . ($current_page - 1) . "&orderby=" . $orderby . "\">" ."<b>" . "<" . "</b>" . "</a>");
			}
			$i = 0;					
			while($i < ($overall_entries / $max_entries_per_page)) {
				if($current_page == $i && $current_page != "all") {
					$page_output .= (" " . ($i + 1));
				} else {
					$page_output .= ("&nbsp; " . "<a class=\"menue\" href=\"" . $working_link . "&" . $var_page_name . "=" . $i . "\">" ."<b>" . ($i + 1) . "</b>" . "</a>");
				}
				$i++;
			}
			if($current_page != "all" && ($current_page + 1) < ($overall_entries/$max_entries_per_page)) {
				$page_output .= ("&nbsp; " . "<a class=\"menue\" href=\"" . $working_link ."&" . $var_page_name . "=" . ($current_page + 1) . "\">" ."<b>" . ">" . "</b>" . "</a>");
			}
			if($current_page != "all") {
				$page_output .= ("&nbsp; " . "<a class=\"menue\" href=\"" . $working_link ."&" . $var_page_name . "=all" . "\">" ."<b>" . "Alle" . "</b>" . "</a>");									
			}
			if ($current_page == "all") {
				$page_output .= " Alle";
			}
		
		}

		$output["html"] = $page_output;
		$output["sql"] = $page_sql;
		$output["a"] = $page_a;
		$output["b"] = $page_b;

		return($output);
	
		// ?!?! unset($output); unset($working_link); unset($page_sql); unset($page_output);

	} else {
		echo ("Error: Function page_split needs defined: current_page, max_entries_per_page,working_link, page_varname For more information please visit the lansuite programmers docu");
	}
	}
	
	// Ab hier bitte nur Callbackfunctionen f?r die Suchergebnisse

	// Gibt zu einer Userid den Username aus
	function GetUsername( $userid )	{
		global $db, $config;
		$get_username = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$userid'");
		return $get_username["username"];
	}
	
	// Hiermit wir der Platz ermittelt
	function GetSeat($userid) {
		global $db, $config, $seat2, $party;

    return $seat2->SeatOfUser($userid, 14);
	}
	
	// Get number of seats in block
	function SeatsAvailable($blockid) {
		global $db, $config;

		$row = $db->query_first("SELECT COUNT(*) AS SeatsAvailable FROM {$config['tables']['seat_seats']} WHERE blockid='$blockid' AND status > 0 AND status < 7");
		return $row['SeatsAvailable'];
	}

	// Get number of seats in block
	function SeatsOccupied($blockid) {
		global $db, $config;

		$row = $db->query_first("SELECT COUNT(*) AS SeatsOccupied FROM {$config['tables']['seat_seats']} WHERE blockid='$blockid' AND status = 2");
		return $row['SeatsOccupied'];
	}

	// Get number of seats in block
	function SeatLoad($blockid) {
		global $dsp, $templ;
		
		$seats = $this->SeatsAvailable($blockid);
		if($seats != 0){
			$SeatLoad = $this->SeatsOccupied($blockid) / $seats * 100;
		}else{
			$SeatLoad = 0;
		}
		$templ['bar']['width'] = round($SeatLoad, 0) * 2;
		$templ['bar']['text'] = round($SeatLoad, 1) .'%';
		return $dsp->FetchModTpl('seating', 'bar');
	}

	function GetGroup( $id ){
		global $db,$config,$lang;
		if($id == 0){
			return $lang['class_party']['drowpdown_no_group'];
		}else{
			$row = $db->query_first("SELECT * FROM {$config['tables']['party_usergroups']} WHERE group_id='$id'");
			return $row['group_name'];
		}
	}
	
	// Zeigt Usernamen nur im Intranetmodus, oder Admins
	function CheckName( $userid ) {
		global $cfg, $auth, $lang;

		if ($auth["type"] >= 2 OR $cfg["sys_internet"] == 0) return $userid;
		else return $lang['ms']['cb_not_shown'];
	}
	
	
	function GetTournamentStatus( $status ) {
		global $lang;
		$status_descriptor["open"] 	= $lang['ms']['cb_ts_open'];
		$status_descriptor["process"] 	= $lang['ms']['cb_ts_progress'];
		$status_descriptor["closed"] 	= $lang['ms']['cb_ts_closed'];
		
		return $status_descriptor[$status];
	}

	
	function GetTournamentName($arr) {
		global $auth, $lang;

		//$arr 	0 => name
		//	1 => over18
		//	2 => id
		//	3 => icon
		//	4 => wwcl
		//	5 => ngl

		$return = "";

		// Game Icon
		if (($arr[3]) && ($arr[3] != "none")) $return .= "<img src=\"ext_inc/tournament_icons/{$arr[3]}\" title=\"Icon\" border=\"0\" /> ";

		// Name + Link
		$return .= sprintf('</a><a class="menu" href="'. $this->config['target_link'] . $arr[2] .'">%s', $arr[0]);

		// WWCL Icon
		if ($arr[4]) $return .= " <img src=\"ext_inc/tournament_icons/leagues/wwcl.png\" title=\"WWCL Game\" border=\"0\" />";

		// NGL Icon
		if ($arr[5]) $return .= " <img src=\"ext_inc/tournament_icons/leagues/ngl.png\" title=\"NGL Game\" border=\"0\" />";

		// Over 18 Icon
		if ($arr[1]) $return .= " <img src='design/".$auth["design"]."/images/fsk_18.gif' title='{$lang['ms']['cb_t_over18']}' border=\"0\" />";

		return $return;
	}

	function GetTournamentTeamAnz($arr) {
		return "</a><a href=\"index.php?mod=tournament2&action=details&tournamentid={$arr[2]}&headermenuitem=2\">". $arr[0] ."/". $arr[1];
	}

	function GetDate( $time ) {
		global $func;

		if ($this->config['datetime_format']=='') return $func->unixstamp2date( $time , "datetime"); 
		else return $func->unixstamp2date( $time , $this->config['datetime_format']); 
	}

	function GetPostDate($pid) {
		global $db, $config, $auth;

		if ($pid){
			$last_post = $db->query_first("SELECT date, b.userid, username FROM {$config["tables"]["board_posts"]} AS b LEFT JOIN {$config["tables"]["user"]} AS u ON u.userid=b.userid WHERE pid = $pid");

			return $this->GetDate($last_post["date"]) . HTML_NEWLINE . "</a>{$last_post['username']} <a href=\"index.php?mod=usrmgr&action=details&userid={$last_post["userid"]}\"><img src=\"design/{$auth["design"]}/images/arrows_user.gif\" border=\"0\" />";
		} else return "---";
	}

	function NewPosts($last_read) {
		global $db, $config, $auth;

		// Delete old entries
		$db->query("DELETE FROM {$config["tables"]["board_read_state"]} WHERE last_read < ". (time() - 60 * 60 * 24 * 7));

		// Older, than one week
		if ($last_read[2] < (time() - 60 * 60 * 24 * 7)) return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$last_read[3]}\">Alt</a>";

		// No entry -> Thread completely new
		elseif (!$last_read[0]) return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$last_read[3]}\">Neu</a>";

		// Entry exists
		else {
			$last_post = $db->query_first("SELECT date, pid FROM {$config["tables"]["board_posts"]} WHERE pid = {$last_read[1]} ORDER BY date ASC");

			// The posts date is newer than the mark -> New
			if ($last_read[0] < $last_post["date"]) return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$last_read[3]}#pid{$last_post["pid"]}\">Neu</a>";

			// The posts date is older than the mark -> Old
			else return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&fid={$_GET["fid"]}&tid={$last_read[3]}\">Alt</a>";
		}
	}

	function GetPicTotal( $galleryid ) {
		global $db, $config;

		$get_total = $db->query("SELECT * FROM {$config["tables"]["picgallery"]} WHERE galleryid = '$galleryid'");
		return $db->num_rows($get_total);
	}
	
			
	function GetVotesTotal( $pollid ) {
		global $db, $config;

		$get_total = $db->query("SELECT * FROM {$config["tables"]["pollvotes"]} WHERE pollid = '$pollid'");
		return $db->num_rows($get_total);
	}
	
	function GetPostsTotal( $fid ) {
		global $db, $config;
		
		$get_total = $db->query("SELECT * FROM {$config["tables"]["board_threads"]} WHERE fid = '$fid'");
		$total = $db->num_rows($get_total);
		
		$get_total = $db->query("SELECT * FROM {$config["tables"]["board_posts"]} WHERE fid = '$fid'");
		$total = $total + $db->num_rows($get_total);
		
		return $total;
		
	}
	
	function GetPollStatus( $pollid ) {
		global $db, $config;

		$get_time = $db->query_first("SELECT endtime FROM {$config["tables"]["polls"]} WHERE pollid = '$pollid'");
								
		if ($get_time["endtime"] == 0 || $get_time["endtime"] > time()) return "offen";
		else return "geschlossen";
	}

	function GetGalleryStatus( $galleryid ) {
		global $db, $config, $lang;

		$get_status = $db->query_first("SELECT status FROM {$config["tables"]["gallery"]} WHERE galleryid = '$galleryid'");
		switch($get_status["status"]){
			default: return $lang['ms']['cb_g_unknown'];
			case 1: return $lang['ms']['cb_g_no_upload'];
			case 2: return $lang['ms']['cb_g_upload'];
			case 3: return $lang['ms']['cb_g_upload_orga'];
			case 4: return $lang['ms']['cb_g_orga'];
		}
	}

	function GetRentTotal( $ru_userid ) {
		global $db, $config;

		$get_data = $db->query("SELECT userid FROM {$config["tables"]["rentuser"]} WHERE userid = '$ru_userid' AND back_orgaid = ''");
		$how_many = $db->num_rows($get_data);

		return $how_many;
	} 


	function ParseInboxMailStatus( $status ) {
		global $auth;

		switch($status){
			default: return "undefine";
			case "new": return "<img src=\"design/".$auth["design"]."/images/arrows_message_blink.gif\" border=\"0\" alt=\"NEU\" />";
			case "read": return "<img src=\"design/".$auth["design"]."/images/arrows_message.gif\" border=\"0\" alt=\"Gelesen\" />";
			case "reply": return "<img src=\"design/".$auth["design"]."/images/arrows_message.gif\" border=\"0\" alt=\"Beantwortet\" /><img src=\"design/".$auth["design"]."/images/arrows_info.gif\" border=\"0\" alt=\"Beantwortet\" />";
		}
	}
	
	function GetSenderName($username) {
		if ($username == "") return "SYSTEM";
		else return $username;
	}
	
	function ParseReadTime( $time ) {
		global $func;

		if ($time==0) { return "---"; }
		else return $func->unixstamp2date( $time , "shortdaytime");
	}
	
	function tt_status ($status) {
		global $lang;

		switch ($status) {
			default: return $lang['ms']['cb_tt_unassigned']; break;
			case 1: return $lang['ms']['cb_tt_new']; break;
			case 2: return $lang['ms']['cb_tt_accepted']; break;
			case 3: return $lang['ms']['cb_tt_in_work']; break;
			case 4: return $lang['ms']['cb_tt_closed']; break;
			case 5: return $lang['ms']['cb_tt_rejected']; break;
		}
	}

	function server_type ($type) {
		global $lang;

		switch ($type) {
			default: return "???"; break;
			case "gameserver": return "Game"; break;
			case "ftp": return "FTP"; break;
			case "irc": return "IRC"; break;
			case "web": return "Web"; break;
			case "proxy": return "Proxy"; break;
			case "misc": return $lang['ms']['cb_s_misc']; break;
		}
	}

	function server_pwicon ($pw) {
		global $auth;

		if ($pw) return "<img src=\"design/{$auth["design"]}/images/server_pw.gif\" border=\"0\" />";
		else return "<img src=\"design/{$auth["design"]}/images/server_nopw.gif\" border=\"0\" />";
	}

	function server_status () {
		global $cfg;

		// Wenn Intranetversion, erreichbarkeit testen
		if ($cfg["sys_internet"] == 0 and (!get_cfg_var("safe_mode"))) {
			include_once("modules/server/ping_server.inc.php");	   
			ping_server($this->row['ip'], $this->row['port']);

			if ($this->row['available'] == 1) return "<div class=\"tbl_green\">Online</div>";
			elseif ($this->row['available'] == 2) return "<div class=\"tbl_red\">Port Offline</div>";
			else return "<div class=\"tbl_red\">IP Offline</div>";
		} else  return "-";

		if ($pw) return "<img src=\"design/{$auth["design"]}/images/server_pw.gif\" border=\"0\" />";
		else return "<img src=\"design/{$auth["design"]}/images/server_nopw.gif\" border=\"0\" />";
	}

	function ParseSettingsValue($cfg_value) {
		global $func, $cfg, $db, $config, $lang;


		$get_cfg_selection = $db->query_first("SELECT cfg_display
			FROM {$config["tables"]["config_selections"]}
			WHERE cfg_key = ('". $this->row["cfg_type"] ."') AND (cfg_value = '". $func->text2db($cfg_value) ."')
			");

		if ($get_cfg_selection["cfg_display"]) return $get_cfg_selection["cfg_display"];
		else switch($this->row["cfg_type"]) {
			default:
				return "?: ".$cfg_value;													break;

			case "string":
				if ($cfg_value=="") return "<i>- {$lang['ms']['cb_set_no_entry']} -</i>";
				else {
					$value = strip_tags($cfg_value);

					if( strlen( $value ) > 30 ) $text = substr( $value, 0, 30) ."...";
					else $text = $value;
					return "\"".$text."\""; 
				}
			break;

			case "integer":
				return $cfg_value;			break;

			case "datetime":
				return $func->unixstamp2date( $cfg_value , "daydatetime");
			break;

			case "time":
				return $func->unixstamp2date( $cfg_value , "time");
			break;

			case "date":
				return $func->unixstamp2date( $cfg_value , "date");
			break;

			case "password":
				if ($cfg["sys_show_password_direct"]=="1") return "\"".$cfg_value."\"";
				else {
					while (strlen($starpwd) < strlen($cfg_value) ) $starpwd = $starpwd."*";
					return "Passwort: ".$starpwd;
				}
			break;
		} // END SWITCH
	} // END FUNCTION
	
	function GetFoodoption($value){
		global $func, $cfg, $db, $config, $lang;
		
		
		if(stristr($value,"/")){
			$values = split("/",$value);

			foreach ($values as $number){
				if(is_numeric($number)){
					$data = $db->query_first("SELECT caption, unit FROM {$config['tables']['food_option']} WHERE id = " . $number);
					if($data['caption'] == ""){
						$out .= $data['unit'] . HTML_NEWLINE;
					}else{
						$out .= $data['caption'] . HTML_NEWLINE;
					}
				}
		
			}
		}else {
			$data = $db->query_first("SELECT caption,unit FROM {$config['tables']['food_option']} WHERE id = " . $value);
			if($data['caption'] == ""){
				$out .= $data['unit'] . HTML_NEWLINE;
			}else{
				$out .= $data['caption'] . HTML_NEWLINE;
			}			
		}
		return $out;	
		
	}
	

} // Class Mastersearch
?>
