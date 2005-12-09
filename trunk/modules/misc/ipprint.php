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
*	Filename: 		ipprint.php
*	Module: 		seat
*	Main editor: 		raphael@lansuite.de
*	Last change: 		26.03.2003 15:18
*	Description: 		prints ip tickets
*	Remarks:
*
**************************************************************************/


$step = $_GET['step'];
$mode = $_GET['mode'];

$blockid	= $_GET['blockid'];
$seatid 	= $_GET['seatid'];
$userid 	= $_GET['userid'];


switch($step) {

	default:

		$questionarr[1] = $lang['misc']['qest_block'];
		$questionarr[2] = $lang['misc']['qest_seat'];
		$questionarr[3] = $lang['misc']['qest_user'];


		$linkarr[1]	= "index.php?mod=misc&action=ipprint&step=2&mode=block";
		$linkarr[2]	= "index.php?mod=misc&action=ipprint&step=2&mode=seat";
		$linkarr[3]	= "index.php?mod=misc&action=ipprint&step=2&mode=user";


		$func->multiquestion($questionarr,$linkarr,$lang['misc']['print_paper']);


		break;
	case 2:
		$func->error("Under construction", '');
	/*
		$mastersearch = new MasterSearch($vars, 'index.php?mod=misc&action=ip', "index.php?mod=misc&action=ip&step=2&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
*/
/*
		switch($mode) {

			case "block":
				$mastersearch["working_link"] 		= "index.php?mod=misc&action=ipprint&step=2&mode=block";
				$mastersearch["target_link"] 		= "index.php?mod=misc&action=ipprint&step=4&mode=block";
				$mastersearch["search_item"]		= "seat";
				$mastersearch["hide_form"] 		= TRUE;
				$mastersearch["extended_description"] 	= "<strong>".$lang['misc']['ch_print_bl']."</strong>";
				break;

			case "seat":
				$mastersearch["working_link"] 		= "index.php?mod=misc&action=ipprint&step=2&mode=seat";
				$mastersearch["target_link"] 		= "index.php?mod=misc&action=ipprint&step=3&mode=seat";
				$mastersearch["search_item"]		= "seat";
				$mastersearch["hide_form"] 		= TRUE;
				$mastersearch["extended_description"] 	= "<strong>".$lang['misc']['ch_print_se']."</strong>";
				break;

			case "user":
				$mastersearch["working_link"] 		= "index.php?mod=misc&action=ipprint&step=2&mode=user";
				$mastersearch["target_link"] 		= "index.php?mod=misc&action=ipprint&step=4&mode=user";
				$mastersearch["search_item"]		= "user";
				$mastersearch["only_with_seat"]		= TRUE;
				$mastersearch["extended_description"] 	= "<strong>".$lang['misc']['ch_print_usr']."</strong>";
				break;
		} // switch - mode

		$mastersearch["task"] 	= "ipprint";

		include("inc/database/mastersearch.php");

*/
		break;

	case 3:

		$seat = new seat;


			if($blockid == "") {
				$func->error($lang['misc']['err_no_block'], "index.php?mod=misc&action=ipprint");
			} else {



	        		$seat->seat_picstr[21] = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_highlighted.gif';
				$seat->seat_picstr[31] = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_highlighted.gif';
				$seat->seat_picstr[41] = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_highlighted.gif';
				$seat->seat_picstr[0]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_transparency.gif ';
				$seat->seat_picstr[1]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_free.gif';
				$seat->seat_picstr[2]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_reserved.gif';
				$seat->seat_picstr[3]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_reserved_you.gif';
				$seat->seat_picstr[4]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_reserved_clanmate.gif';

				$linkstr = '?mod=misc&action=ipprint&step=4&mode=seat&seatid=%s';

				$seat->seat_linkstr[1] = $linkstr;
				$seat->seat_linkstr[2] = $linkstr;
				$seat->seat_linkstr[3] = $linkstr;
				$seat->seat_linkstr[4] = $linkstr;

				$templ['index']['info']['content'] .= $seat->view_block($blockid);

			}



		break;


	case 4:

			switch($mode) {

				case "block":
						$text = $lang['misc']['print_block'];
						$link = "javascript:var w=window.open('base.php?mod=ipprint&mode=b&blockid=$blockid','_blank','width=320,height=400,resizable=yes,menubar=yes');";
					break;
				case "seat":
						$text = $lang['misc']['print_seat'];
						$link = "javascript:var w=window.open('base.php?mod=ipprint&mode=s&seatid=$seatid','_blank','width=320,height=400,resizable=yes,menubar=yes');";
					break;
				case "user":
						$text = $lang['misc']['print_user'];
						$link = "javascript:var w=window.open('base.php?mod=ipprint&mode=u&userid=$userid','_blank','width=320,height=400,resizable=yes,menubar=yes');";
					break;

			} // switch - mode

			$func->confirmation(
			$text. HTML_NEWLINE .
			"<a href=\"$link\">".
			$lang['misc']['print_popup']
			."</a>
			", "index.php?mod=misc&action=ipprint");

		break;

} // switch
?>
