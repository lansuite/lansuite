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
					$func->question(t('Wollen Sie den Benutzer <b>%1 (%2 %3)</b> wirklich aus Ihrer Buddy-Liste entfernen?',$row[name],$row[firstname],$row[username]),"index.php?mod=msgsys&action=removebuddy&queryid=$_GET[queryid]&step=2","index.php");
				}else {
					$func->question(t('Wollen Sie den Benutzer <b>%1</b> wirklich aus Ihrer Buddy-Liste entfernen?', $row[username]),"index.php?mod=msgsys&action=removebuddy&queryid=$_GET[queryid]&step=2","index.php");
				}
			} // if
			else
			{
				//
				// Error
				//
				$func->error(t('Dieser Benutzer befindet sich nicht in ihrer Buddy-Liste'),"");

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
					$func->confirmation(t('Der Benutzer <b>%1</b> wurde aus Ihrer Buddy-Liste entfernt. Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf wirksam.', $row1[username]),"");
				}else{
					$func->confirmation(t('Der Benutzer <b>%1 (%2 %3)</b> wurde aus Ihrer Buddy-Liste entfernt. Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf wirksam.', $row1[name],$row1[firstname],$row1[username]),"");
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
	$func->error(t('Sie haben keinen Benutzer ausgew&auml;hlt'),"");
} // else queryid
?>