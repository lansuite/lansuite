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
*	Filename: 			overview
*	Module: 			Games
*	Main editor: 		denny@one-network.org
*	Description: 		Overview overall available Games
*	Remarks:
*
**************************************************************************/

$dsp->NewContent(t('Games-Übersicht'), t('Hier findest du ein paar kleine Webspiele, um sich die Zeit zu vertreiben'));
$dsp->AddSingleRow($smarty->fetch('modules/games/templates/overview.htm'));
$dsp->AddContent();
