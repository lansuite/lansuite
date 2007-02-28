<?php

$HANDLE["POLLID"]	= $_GET["pollid"];
$HANDLE["STEP"]		= $_GET["step"];
$HANDLE["ACTION"]	= $_GET["action"];

switch($HANDLE["ACTION"]) {

default:

	switch($HANDLE["STEP"]) {
		//
		// Overview page (Related to Mastersearch)
		//
		default:
		  include_once('modules/poll/search.inc.php');
		break;
		
		//
		// Show selected poll details
		//
		case 2:
			//
			// Check wheter the poll exists
			//
			$CHECK["poll"]	= $db->query("SELECT pollid FROM {$config["tables"]["polls"]} WHERE pollid='{$HANDLE["POLLID"]}';");
				
			//
			// Check whether the user voted already
			//	
			$CHECK["uservoted"] = $db->query("
			SELECT	pollvoteid
			FROM	{$config["tables"]["pollvotes"]}
			WHERE	pollid	= '{$HANDLE["POLLID"]}'
			AND	userid	= '{$_SESSION["auth"]["userid"]}'
			");
						
			if($db->num_rows($query_id = $CHECK["poll"]) == "1")
			{
				//
				// Read in poll data
				//			
				$POLL = $db->query_first("
				SELECT	caption, comment, anonym, multi, endtime, group_id
				FROM	{$config["tables"]["polls"]}
				WHERE	pollid	= '{$HANDLE["POLLID"]}'
				");
					
				//
				// Total votes
				//
				$CHECK["totalvotes"] = $db->query("
				SELECT	pollvoteid
				FROM	{$config["tables"]["pollvotes"]}
				WHERE	pollid = '{$HANDLE["POLLID"]}'
				");

				//
				// Poll options
				//
				$POLL_OPTION_QUERY = $db->query("
				SELECT	caption, polloptionid
				FROM	{$config["tables"]["polloptions"]}
				WHERE	pollid = '{$HANDLE["POLLID"]}'
				");
	
				$dsp->NewContent(str_replace("%NAME%", $POLL["caption"], $lang["poll"]["show_caption"]), "");

				$array_index = "0";
				while($POLL_OPTION = $db->fetch_array($query_id = $POLL_OPTION_QUERY)) {
					//
					// Select all votes for this option
					//
					$POLL_VOTES_QUERY = $db->query("
					SELECT	userid
					FROM	{$config["tables"]["pollvotes"]}
					WHERE	polloptionid = '{$POLL_OPTION["polloptionid"]}'
					");

					//
					// Javascript hover function
					//
					if($POLL["anonym"] == FALSE) {
						unset($first_entry);
						$templ['poll']['show']['details']['case']['control']['javascript'] .= "votes[$array_index] = '";

						while($POLL_VOTES = $db->fetch_array($query_id = $POLL_VOTES_QUERY)) {
							//
							// Title
							//
							if($first_entry == FALSE) {
								$templ['poll']['show']['details']['case']['control']['javascript'] .= $lang["poll"]["show_js_voted"] .": ";

								$USER = $db->query_first("
								SELECT	username, name, firstname
								FROM	{$config["tables"]["user"]}
								WHERE	userid = '{$POLL_VOTES["userid"]}'
								");

								$templ['poll']['show']['details']['case']['control']['javascript'] .= " " . addslashes($USER["username"]);	

								$first_entry = TRUE;
							} else {
								$USER = $db->query_first("
								SELECT	username, name, firstname
								FROM	{$config["tables"]["user"]}
								WHERE	userid = '{$POLL_VOTES["userid"]}'
								");

								$templ['poll']['show']['details']['case']['control']['javascript'] .= ", " . addslashes($USER["username"]);	
							}
						} // while users

						//
						// No votes for this option
						//
						if($first_entry == FALSE) {
							$templ['poll']['show']['details']['case']['control']['javascript'] .= $lang["poll"]["show_novote"];
						}
						$templ['poll']['show']['details']['case']['control']['javascript'] .= "';";
							
						//
						// No option hovered text
						//
						$templ['poll']['show']['details']['case']['control']['javascript_title']	= $lang["poll"]["show_js_default"];
							
					} // javascript
			
					//
					// Template vars
					//
					$totalvotes	= $db->num_rows($query_id = $CHECK["totalvotes"]);
					$optionvotes	= $db->num_rows($query_id = $POLL_VOTES_QUERY);
					if ($optionvotes == "") $optionvotes=0;

					if($optionvotes > 0) {
						$templ['poll']['show']['details']['row']['control']['percent']		= floor($optionvotes / $totalvotes * 100);
						$templ['poll']['show']['details']['row']['info']['percent']		= number_format($optionvotes / $totalvotes * 100, 0, ",", ".");
					} else {
						$templ['poll']['show']['details']['row']['info']['percent']		= "0";	
					}
					$templ['poll']['show']['details']['row']['info']['votes']  			= $optionvotes;

					if($POLL["anonym"] == FALSE) {
						$templ['poll']['show']['details']['row']['control']['link']		= " onMouseOver=\"showvotes($array_index)\" onMouseOut=\"remove()\" ";		
					}			

					//
					// Bar
					//
#					if ($templ['poll']['show']['details']['row']['control']['percent']=="") $templ['poll']['show']['details']['row']['control']['percent']=0;
#					else $templ['poll']['show']['details']['row']['control']['percent'].="%";
#					$templ['poll']['show']['details']['case']['control']['rows'] .= $dsp->FetchModTpl("poll", "show_details_row");

if ($templ['poll']['show']['details']['row']['control']['percent'] == '') $templ['poll']['show']['details']['row']['control']['percent'] = 0;
$bar = '<ul class="BarOccupied" style="width:'. $templ['poll']['show']['details']['row']['control']['percent'] .'%;" '. $templ['poll']['show']['details']['row']['control']['link'] .'>'. $templ['poll']['show']['details']['row']['info']['votes'] .' ('. $templ['poll']['show']['details']['row']['info']['percent'] .'%) </ul>';
$bar .= '<ul class="BarClear">&nbsp;</ul>';

/*
<!-- TEMPLATE - STANDARD - POLL SHOW DETAILS ROW -->
	<table width="{$templ['poll']['show']['details']['row']['control']['percent']}" cellpadding="0" cellspacing="0" border="0">
	<tr {$templ['poll']['show']['details']['row']['control']['link']}>
		<td width="5" background="design/{default_design}/images/bar_left.gif" height="13" align="top"><img src="design/{default_design}/images/px.gif" width="5"  height="13" align="top"></td>
		<td background="design/{default_design}/images/bar_center.gif"  height="13" align="top"><img src="design/{default_design}/images/px.gif" height="13" align="top"></td>
		<td width="5" background="design/{default_design}/images/bar_right.gif" height="13" align="top"><img src="design/{default_design}/images/px.gif" width="5"  height="13" align="top"></td>
	</tr>
	</table>
	{$templ['poll']['show']['details']['row']['info']['percent']}% ({$templ['poll']['show']['details']['row']['info']['votes']})
<!-- TEMPLATE - STANDARD - POLL SHOW DETAILS ROW -->
*/
					$dsp->AddDoubleRow($POLL_OPTION["caption"], $bar);
					unset($templ['poll']['show']['details']['row']['control']['percent']);

					$array_index++;

				} // while

				$dsp->AddDoubleRow($lang["poll"]["show_votecount"], $db->num_rows($query_id = $CHECK["totalvotes"]));
				if($POLL["anonym"] == FALSE) $dsp->AddDoubleRow("", $dsp->FetchModTpl("poll", "show_voters"));
				($POLL["endtime"] < "1" OR $POLL["endtime"] > time())? $endtime = $lang["poll"]["show_open"] : $endtime = $lang["poll"]["show_closed"];
				$dsp->AddDoubleRow($lang["poll"]["show_state"], $endtime);
				($POLL["anonym"] == "1")? $anonym = $lang["poll"]["show_yes"] : $anonym = $lang["poll"]["show_no"];
				$dsp->AddDoubleRow($lang["poll"]["show_anonym"], $anonym);
				($POLL["multi"] == "1")? $multi = $lang["poll"]["show_yes"] : $multi = $lang["poll"]["show_no"];
				$dsp->AddDoubleRow($lang["poll"]["show_multiple"], $multi);
				$dsp->AddDoubleRow($lang["poll"]["show_comment"], $POLL["comment"]);

				//
				// Buttons
				//
				if($_SESSION["auth"]["type"] > 1) {
					$buttons .= $dsp->FetchButton("index.php?mod=poll&action=change&step=2&pollid={$HANDLE['POLLID']}", "edit");
					$buttons .= $dsp->FetchButton("index.php?mod=poll&action=delete&step=2&pollid={$HANDLE['POLLID']}", "delete");
				}

				if((($POLL["endtime"] == 0) OR ($POLL["endtime"] > time())) AND ($db->num_rows($query_id = $CHECK["uservoted"]) < 1) AND ($_SESSION["auth"]["login"] == "1") AND ($POLL['group_id'] == 0 OR $POLL['group_id'] == $auth['group_id'])) {
					$buttons .= $dsp->FetchButton("index.php?mod=poll&action=vote&pollid={$HANDLE['POLLID']}", "vote");
				}
				$dsp->AddDoubleRow("", $buttons);
				$dsp->AddBackButton("index.php?mod=poll", "poll/show");
				$dsp->AddContent();

				// Including comment-engine       
				if($_SESSION["auth"]["login"] == 1) {
        	include('inc/classes/class_mastercomment.php');
        	new Mastercomment('Poll', $HANDLE[POLLID]);
				}
				//End comment-engine	

			} // Pollcheck
			else $func->error($lang["poll"]["add_err_noexist"],"index.php?mod=poll");
		break;
	} // switch step
break;

case 'search':
  include_once('modules/poll/search.inc.php');
break;

} // switch action
?>
