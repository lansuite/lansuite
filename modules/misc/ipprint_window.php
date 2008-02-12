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
*	Filename: 		ipprint_window.php
*	Module: 		seat
*	Main editor: 		raphael@one-network.org
*	Last change: 		01.04.2003 17:54
*	Description: 		display ip tickets
*	Remarks:
*
**************************************************************************/
$blockid	= $_GET['blockid'];
$seatid 	= $_GET['seatid'];
$userid 	= $_GET['userid'];

$mode 		= $_GET['mode'];

$seat = new seat;

switch($mode) {

	default:	$body = $lang['misc']['ipprint_nofunc']; break;

	case "b":
		$query = $db->query("SELECT
			   A.userid,
			   B.name as blockname,
			   orientation,
			   row,
			   col,
			   C.name,
			   firstname,
			   username,
			   clan,
			   A.status,
			   A.ip
			        FROM {$config["tables"]["seat_seats"]}  AS A,
			   	     {$config["tables"]["seat_block"]}  AS B LEFT OUTER JOIN
				     {$config["tables"]["user"]} 	AS C ON A.userid = C.userid
											      WHERE A.blockid='$blockid' AND
											      	    B.blockid=A.blockid");
	break;

	case "s":
		$query = $db->query("SELECT
			   A.userid,
			   B.name as blockname,
			   orientation,
			   row,
			   col,
			   C.name,
			   firstname,
			   username,
			   clan,
			   A.status,
			   A.ip
			   	FROM {$config["tables"]["seat_seats"]}  AS A,
			   	     {$config["tables"]["seat_block"]}  AS B LEFT OUTER JOIN
				     {$config["tables"]["user"]} 	AS C ON A.userid = C.userid
											      WHERE A.seatid='$seatid' AND
											      	    B.blockid=A.blockid");

	break;

	case "u":
		$query = $db->query("SELECT
			   A.userid,
			   B.name as blockname,
			   orientation,
			   row,
			   col,
			   C.name,
			   firstname,
			   username,
			   clan,
			   A.status,
			   A.ip
			     	FROM {$config["tables"]["seat_seats"]}  AS A,
			   	     {$config["tables"]["seat_block"]}  AS B LEFT OUTER JOIN
				     {$config["tables"]["user"]} 	AS C ON A.userid = C.userid
											      WHERE A.userid='$userid' AND
											      	    B.blockid=A.blockid");
	break;


} // mode


if($db->num_rows($query) == 0) $body = $lang['misc']['ipprint_noentr'];

else	while($row = $db->fetch_array($query)) {

		$status =($row["status"] == 2) ? $lang['misc']['occupied'] : $lang['misc']['free'];

		$seatnumber = $seat->display_seat_index($row["orientation"], $row["col"], $row["row"]);

		$string = str_replace("\"","\\\"",@implode("",@file("ext_inc/ip_paper/ipprint_row.htm")));
		$string = str_replace("{seat}", 	$seatnumber, 		$string);
		$string = str_replace("{block}", 	$row["blockname"], 	$string);
		$string = str_replace("{name}", 	$row["name"], 		$string);
		$string = str_replace("{firstname}", 	$row["firstname"], 	$string);
		$string = str_replace("{username}", 	$row["username"], 	$string);
		$string = str_replace("{clan}", 	$row["clan"], 		$string);
		$string = str_replace("{ip}", 		$row["ip"], 		$string);
		$string = str_replace("{status}", 	$status, 		$string);

		eval("\$body .= \"".$string."\";");
	} // while



eval("\$templ['index']['info']['content'] .= \"".str_replace("\"","\\\"",@implode("",@file("ext_inc/ip_paper/ipprint.htm")))."\";");

echo $templ['index']['info']['content'];

?>
