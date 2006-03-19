<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 			vote.php
*	Module: 			Poll
*	Main editor: 		johannes@one-network.org
*	Last change: 		15.03.03 12:00 
*	Description: 		 
*	Remarks: 		
*
**************************************************************************/

//
// Define standard vars
//
$HANDLE[POLLID]	= $_GET[pollid];
$HANDLE[STEP]	= $_GET[step];
$HANDLE[OPTION]	= $_POST[option];

//
// Error - switch
//
switch($HANDLE[STEP]) {
	case 2:
		//
		// Check if property multi is set
		//

		$POLL = $db->query_first("
		SELECT	multi
		FROM	{$config[tables][polls]}
		WHERE	pollid = $HANDLE[POLLID]
		");

		if($POLL[multi] == TRUE) {
			if($HANDLE[OPTION] == "") {
				$func->information($lang["poll"]["vote_err_nooption"], "");
				$HANDLE[STEP] = "1";
			}
		} else {
			if($HANDLE[OPTION] == "") {
				$func->information($lang["poll"]["vote_err_nooption"], "");
				$HANDLE[STEP] = "1";	
			}	
		}
	break;	
}	

//
// Switch 
//
switch($HANDLE[STEP]) {
	default:
		//
		// Get poll
		//
		$POLL = $db->query_first("
		SELECT	pollid, caption, endtime, multi
		FROM	{$config[tables][polls]}
		WHERE	pollid = $HANDLE[POLLID]
		");

		//
		// Check if the user has already voted
		//
		$VOTE = $db->query_first("
		SELECT	pollvoteid
		FROM	{$config[tables][pollvotes]}
		WHERE	pollid = $HANDLE[POLLID]
		AND		userid = {$_SESSION[auth][userid]}
		");

		//
		// Checks
		//
		if(
		// Check pollid
		(isset($POLL[pollid])) AND 
		// Check if the poll is open
		($poll[endtime] > time() OR ($poll[endtime] == "0" OR $poll[endtime] == "")) AND
		// Check if the user has already voted
		($VOTE[pollvoteid] != TRUE)) {
			//
			// Get options
			//
			$OPTIONS = $db->query("
			SELECT	polloptionid, caption
			FROM	{$config[tables][polloptions]}
			WHERE	pollid = $HANDLE[POLLID]
			");

			$dsp->NewContent(str_replace("%NAME%", $POLL["caption"], $lang["poll"]["vote_caption"]), $lang["poll"]["vote_subcaption"]);
			$dsp->SetForm("index.php?mod=poll&action=vote&step=2&pollid=". $HANDLE[POLLID]);

			while($OPTIONS = $db->fetch_array()) {
				if($POLL[multi] == TRUE) {
					$dsp->AddCheckBoxRow("option[]", $OPTIONS[caption], "", "", "", "", "", $OPTIONS[polloptionid]);
				} else {
					$dsp->AddRadioRow("option", $OPTIONS["caption"], $OPTIONS["polloptionid"]);
				}
			}

			$dsp->AddFormSubmitRow("vote");
			$dsp->AddBackButton("index.php?mod=poll", "poll/vote");
			$dsp->AddContent();

		} else  $func->information($lang["poll"]["vote_err_common"], "index.php?mod=poll&action=show");
	break;

	case 2:
		//
		// Get poll
		//
		$POLL = $db->query_first("
		SELECT	multi
		FROM	{$config[tables][polls]}
		WHERE	pollid = $HANDLE[POLLID]
		");

		//
		// Check if the user has already voted
		//
		$VOTE = $db->query_first("
		SELECT	pollvoteid
		FROM	{$config[tables][pollvotes]}
		WHERE	pollid = $HANDLE[POLLID]
		AND		userid = {$_SESSION[auth][userid]}
		");

		if($VOTE[pollvoteid] != TRUE) {
			if($POLL[multi] == TRUE) {
				foreach($HANDLE[OPTION] as $option) {
					$db->query("
					INSERT INTO {$config[tables][pollvotes]}
					SET	pollid = $HANDLE[POLLID], userid = {$_SESSION[auth][userid]}, polloptionid = $option
					");
				}
			} else {
				$db->query("
				INSERT INTO {$config[tables][pollvotes]}
				SET	pollid = $HANDLE[POLLID], userid = {$_SESSION[auth][userid]}, polloptionid = $HANDLE[OPTION]
				");
			} // else
			$func->confirmation($lang["poll"]["vote_success"], "index.php?mod=poll&action=show&step=2&pollid=$HANDLE[POLLID]");	
		} else $func->information($lang["poll"]["vote_err_allready"], "");
	break;
} // switch
?>
