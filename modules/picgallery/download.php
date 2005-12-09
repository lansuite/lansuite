<?php

/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		download.php
*	Module: 		Picgallery
*	Main editor: 		johannes@one-network.org
*	Last change: 		07.01.2003 17:01
*	Description: 		 
*	Remarks: 		
*
**************************************************************************/

//
// Get pic data
//
$picinfo = @GetImageSize("ext_inc/picgallery/$_GET[picurl]");
if($picinfo['2'] == "1" OR $picinfo['2'] == "2" OR $picinfo['2'] == "3")
{
header("content-type: application/octet-stream");
header("content-disposition: attachment; filename=$_GET[picurl]");

readfile("ext_inc/picgallery/$_GET[picurl]");
}
?>
