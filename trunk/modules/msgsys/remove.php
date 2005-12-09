<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		remove.php
*	Module: 		Msgsys
*	Main editor: 		johannes@one-network.org
*	Last change: 		04.01.2003 17:32
*	Description:
*	Remarks:
*
**************************************************************************/

//
// Check queryid
//
if($_GET[queryid])
{

	//
	// Step
	//
	switch($_GET[step])
	{

		//
		// Case question
		//
		default:

			$rowcheck = $db->query("
			SELECT	id
			FROM	{$config[tables][buddys]}
			WHERE	userid = '{$_SESSION[auth][userid]}'
			AND	buddyid = '$_GET[queryid]'
			");

			//
			// User in buddylist ?
			//
			if($db->num_rows() != '0')
			{
				//
				// Get name
				//
				$row = $db->query_first("
				SELECT	username, name, firstname
				FROM	{$config[tables][user]}
				WHERE	userid = '$_GET[queryid]'
				");

				//
				// Question
				//
				if($cfg['sys_internet'] == 0){
					$func->question(str_replace('%NAME%',$row[name],str_replace('%FIRSTNAME%',$row[firstname],str_replace('%USERNAME%',$row[username],$lang['msgsys']['confirm_delete']))),"index.php?mod=msgsys&action=removebuddy&queryid=$_GET[queryid]&step=2","index.php?mod=news");
				}else {
					$func->question(str_replace('%USERNAME%',$row[username],$lang['msgsys']['confirm_delete2']),"index.php?mod=msgsys&action=removebuddy&queryid=$_GET[queryid]&step=2","index.php?mod=news");
				}
			} // if
			else
			{
				//
				// Error
				//
				$func->error($lang['msgsys']['err_not_a_buddy'],"");

			} // else

		break;

		//
		// Case remove
		//
		case 2:

			//
			// Get name
			//
			$row1 = $db->query_first("
			SELECT	username, name, firstname
			FROM	{$config[tables][user]}
			WHERE	userid = '$_GET[queryid]'
			");

			//
			// Remove
			//
			$row2 = $db->query("
			DELETE
			FROM	{$config[tables][buddys]}
			WHERE	buddyid = '$_GET[queryid]'
			AND	userid = '{$_SESSION[auth][userid]}'
			");

			//
			// Confirmation
			//
			if($row2 == TRUE){
				if($cfg['sys_internet'] == 1){
					$func->confirmation(str_replace('%USERNAME%',$row1[username],$lang['msgsys']['del_confirm']),"");
				}else{
					$func->confirmation(str_replace('%NAME%',$row1[name],str_replace('%FIRSTNAME%',$row1[firstname],str_replace('%USERNAME%',$row1[username],$lang['msgsys']['del_confirm2']))),"");
				}
			}



		break;

	} // switch
} // if queryid
else
{
	//
	// Error
	//
	$func->error($lang['msgsys']['err_no_user_choosen'],"");
} // else queryid
?>