<?php

$guestlist["working_link"] 	= "index.php?mod=guestlist&action=guestlist";
$guestlist["target_link"] 	= "index.php?mod=usrmgr&action=details";
$guestlist["task"] 			= "guestlist";
$guestlist["search_item"] 	= "user";

	// Loading the Template

		$templ['guestlist']['search']['form']['user']['control']['form_action'] = ("index.php?mod=guestlist&action=guestlist");

		$templ['guestlist']['search']['form']['user']['info']['title'] = "<b>{$lang["guestlist"]["list_search"]}</b>";
		$templ['guestlist']['search']['case']['user']['info']['title'] = "<b>{$lang["guestlist"]["list_for_party"]} {$_SESSION['party_info']['name']}</b>";

	// This allows you to say that you dont want to have the results imeditaly shown

	// Ordering

		if ($_GET["orderby"] == "") $orderby = "paid,desc"; else $orderby = $_GET["orderby"];
		$order = explode(",",$orderby);
		$order_column = $order["0"];
		$order_type = $order["1"];
		$order_for_sql_query = $order_column . " " . $order_type . ",username asc";

		$templ["guestlist"]["search"]["case"]["user"]["control"]["order"][$order_column][$order_type] = "_active";



	// Adding formvalues and searchstep to the URI

		$templ['guestlist']['search']['case']['user']['control']['working_link']= ($guestlist["working_link"]
					. "&search_keyword=" . $_POST["search_keyword"]
					. "&page=" . $_GET["page"]);


	// Query kitchen cooks the MySQL-query (should get nobel price for this piece of code ;) )

		if ($cfg["signon_showorga"] == 0) { $usertype = "= '1'"; }
		else { $usertype = ">= '1'"; }
		
			$data_query =("SELECT A.userid, A.firstname, A.name, A.username, party.paid, A.clan
						FROM {$config["tables"]["user"]} as A
						LEFT JOIN {$config["tables"]["party_user"]} AS party ON A.userid = party.user_id
						WHERE
						A.type $usertype AND
						A.type != '-4' AND
						party_id = {$party->party_id} AND
						");
			
			$count_query = ("SELECT COUNT(*) AS number FROM
						{$config["tables"]["user"]} AS A
						LEFT JOIN {$config["tables"]["party_user"]} AS party ON A.userid = party.user_id
						WHERE
						A.type $usertype AND
						A.type != '-4' AND
						party_id = {$party->party_id} AND
						");

			// If the form is empty show all entries
			if ($_POST["search_keyword"] == "")
			     $final_where = "1";
			else {
				$final_where = "username like '%" .  $_POST["search_keyword"] . "%' AND A.type >= 1 AND A.type != '-4' OR
								firstname like '%" .  $_POST["search_keyword"] . "%' AND A.type >= 1 AND A.type != '-4' OR
								name like '%" .  $_POST["search_keyword"] . "%' AND A.type >= 1 AND A.type != '-4' OR
								clan like '%" .  $_POST["search_keyword"] . "%' AND A.type >= 1 AND A.type != '-4'
								";
			}//else

			// Adding search inputs to the queries

			$count_query .= $final_where;
			$data_query .= $final_where;

			// Counting all entires

			$count_query = $db->query_first($count_query);
			$overall_entries = $count_query["number"];

			// Multipage support

			$templ['guestlist']['search']['case']['user']['info']['overall_user']  = $overall_entries;
			$templ['guestlist']['search']['case']['user']['info']['extended_description'] = HTML_NEWLINE . $guestlist["extended_description"];

			// Putting in pages function
			$pages = $func->page_split($_GET["page"],$config["size"]["guestlist"],$overall_entries,$guestlist["working_link"] . "&orderby=$orderby","page");

			// Setting template var
			$templ['guestlist']['search']['case']['user']['control']['pages'] = $pages["html"];

			// Setting query
			$data_query .= (" ORDER BY $order_for_sql_query " .$pages["sql"]);

			//Counting stats
			$count_bezahlt = $db->query_first("SELECT COUNT(*) AS number 
												FROM {$config["tables"]["user"]} AS user
												LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id 
												WHERE type $usertype AND type != '-4' AND party.party_id = {$party->party_id} AND (party.paid > 0)");

			$max_teilnehmer = $_SESSION['party_info']['max_guest'];
			$teilnehmer_bezahlt = $count_bezahlt[number];
			$teilnehmer_frei = $max_teilnehmer - $teilnehmer_bezahlt;

			$templ['guestlist']['search']['case']['user']['info']['max_teilnehmer'] = $max_teilnehmer;
			$templ['guestlist']['search']['case']['user']['info']['bezahlt'] = $teilnehmer_bezahlt;
			$templ['guestlist']['search']['case']['user']['info']['frei'] = $teilnehmer_frei;


	//Running Query
		$get_data = $db->query($data_query);

		// Output: Search results

				while($row=$db->fetch_array($get_data)) {

					$templ['guestlist']['search']['row']['user']['info']['tbl'] = "";
					$userid = $row["userid"];

			    $get_seat = $db->query_first("SELECT s.col, s.row, s.blockid, b.name, b.orientation
            FROM {$config['tables']['seat_seats']} AS s
			      LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b.blockid
            WHERE s.userid = {$row['userid']} AND s.status = 2"); 

          if ($get_seat['col'] and $get_seat['row']) $templ['guestlist']['search']['row']['user']['info']['seat'] = "<a href=\"index.php?mod=seating&action=show&step=2&blockid={$get_seat['blockid']}&col={$get_seat['col']}&row={$get_seat['row']}\">". $get_seat['name'] ." - ". $seat2->CoordinateToName($get_seat['col'], $get_seat['row'], $get_seat['orientation']) ."</a>";
					elseif ($row["paid"]) $templ['guestlist']['search']['row']['user']['info']['seat'] = $lang["guestlist"]["list_paid"];
					else {
						$templ['guestlist']['search']['row']['user']['info']['seat'] = $lang["guestlist"]["list_not_paid"];
						$templ['guestlist']['search']['row']['user']['info']['tbl'] = "_off";
					}


					$templ['guestlist']['search']['case']['user']['info']['user_on_page']++;
					$templ['guestlist']['search']['row']['user']['info']['username'] = $row["username"];

					if ($auth["type"] >= 2 OR $cfg["sys_internet"] == 0)
					{
					    $templ['guestlist']['search']['row']['user']['info']['name'] = $row["name"];
					    $templ['guestlist']['search']['row']['user']['info']['firstname'] = $row["firstname"];
					
					}
					else
					{
					    $templ['guestlist']['search']['row']['user']['info']['name'] = "nicht angezeigt";
					    $templ['guestlist']['search']['row']['user']['info']['firstname'] = "nicht angezeigt";
					}

					if($row["clan"] == ''){
						$templ['guestlist']['search']['row']['user']['info']['clan'] = '&nbsp;';
					}else{
						$templ['guestlist']['search']['row']['user']['info']['clan'] = $row["clan"];
					}
					$templ['guestlist']['search']['row']['user']['control']['target_link'] = ($guestlist["target_link"]  . "&userid=" . $row["userid"]);

				// Output: Template for each row
				$templ['guestlist']['search']['case']['user']['control']['rows'] .= $dsp->FetchModTpl("guestlist", "guestlist_row");

				} // while

	// Output: Searchassitant's case
	if ($overall_entries == 0) echo $func->no_items("{$lang["guestlist"]["list_user"]} $active_action","","search");
	else $dsp->AddSingleRow($dsp->FetchModTpl("guestlist", "guestlist"));
	
	$dsp->AddContent();
?>
