<?php

	/*************************************************************************
	* 
	*	Lansuite - Webbased LAN-Party Management System
	*	-----------------------------------------------
	*	Lansuite Version:	2.0
	*	File Version:		2.0
	*	Filename: 		modindex_messenger.php
	*	Module: 		Msgsys
	*	Main editor: 		johannes@one-network.org
	*	Last change: 		16.12.2002 17:06
	*	Description: 		 
	*	Remarks: 		
	*
	**************************************************************************/

	switch( $vars["action"] ) {
		case removebuddy: 
			if($_SESSION['auth']['login'] == 1) {
				include("modules/msgsys/remove.php");
			} else {
				$func->error("NO_LOGIN", "");
			}
		break;
		case addbuddy: 
			if($_SESSION["auth"]["login"] == 1) {
				include("modules/msgsys/add.php");
			} else {
				$func->error("NO_LOGIN", "");
			}
		break;
	}
	
?>
