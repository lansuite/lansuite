<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			delete.php
*	Module: 			Poll
*	Main editor: 		johannes@one-network.org
*	Last change: 		15.07.2005 22:19
*	Description:
*	Remarks:
*
**************************************************************************/

//
// Define standard vars
//
$HANDLE["POLLID"]	= $_GET["pollid"];
$HANDLE["STEP"]		= $_GET["step"];

switch($HANDLE["STEP"])
{
	//
	// Overview page (Related to Mastersearch)
	//
	default:

		//
		// Include Mastersearch
		//
		$mastersearch = new MasterSearch( $vars, "index.php?mod=poll&action=delete", "index.php?mod=poll&action=delete&step=2&pollid=", "");
		$mastersearch->LoadConfig("polls", $lang["poll"]["ms_search"], $lang["poll"]["ms_result"]);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();


	break;

	//
	// Question
	//
	case 2:

			$POLL = $db->query_first("
			SELECT	caption
			FROM	{$config[tables][polls]}
			WHERE	pollid = '$HANDLE[POLLID]'
			");

			if(isset($POLL['caption']))
			{
				$func->question(str_replace("%CAPTION%", $POLL['caption'], $lang["poll"]["del_confirm"]),"index.php?mod=poll&action=delete&step=3&pollid=" . $HANDLE["POLLID"], "index.php?mod=poll&action=delete");
			}
			else
			{
				$func->error($lang["poll"]["add_err_noexist"], "index.php?mod=poll&action=delete");
			}

	break;

	//
	// Delete poll and output a confirmation
	//
	case 3:
		$POLL = $db->query_first("SELECT caption FROM {$config[tables][polls]} WHERE pollid = '$HANDLE[POLLID]'");

		if (isset($POLL['caption'])) {
			$DELETE = $db->query("DELETE FROM {$config[tables][polls]} WHERE pollid = '$HANDLE[POLLID]'");
			if ($DELETE) $func->confirmation(str_replace("%CAPTION%", $POLL['caption'], $lang["poll"]["del_deleted"]),"index.php?mod=poll&action=delete");
		} else $func->error($lang["poll"]["add_err_noexist"], "index.php?mod=poll&action=delete");
	break;

} // switch

?>
