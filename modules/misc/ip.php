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
*	Filename: 		ip.php
*	Module: 		seat
*	Main editor: 		raphael@lansuite.de
*	Last change: 		04.01.2003 14:51
*	Description: 		To change ips =)
*	Remarks:
*
**************************************************************************/


$blockid = $_GET['blockid'];
$step 	 = $_GET['step'];
$seating_ip = $_POST['seating_ip'];


$seat = new seat;


switch($step) {

	case 3:


	$query = $db->query("SELECT ip FROM {$config["tables"]["seat_seats"]} WHERE blockid!='$blockid'");

	while($row =$db->fetch_array($query)) {
		$seating_ip_exists[] = $row["ip"];
	} // while

	if(!is_array($seating_ip_exists)) $seating_ip_exists = array();
	if(!is_array($seating_ip)) $seating_ip = array();

	foreach($seating_ip as $seatid => $ip) {

		$ip = chop(ltrim($ip));

		$seat->seat_ip_new[$seatid] = $ip;

		if($func->checkIP($ip) == FALSE) {
			eval($func->generate_error_template("general_general_form", $seatid, $lang['misc']['err_invalid']));
			$step = 2;


		}
		/*
		 elseif(in_array($ip, $seating_ip_exists)) {
			eval($func->generate_error_template("general_general_form", $seatid, "existiert"));
			$step = 2;

		}
		*/

		if(isset($checked_ips[$ip]))
		{
		$double_ips[$seatid] = $ip;
		$double_ips[$checked_ips[$ip]] =  $seating_ip[$checked_ips[$ip]];
		}
		else
		{
		$checked_ips[$ip] = $seatid;
		}

	} // foreach



	if(is_array($double_ips)) {
	foreach($double_ips as $seatid => $ip) {
		eval($func->generate_error_template("general_general_form", $seatid, $lang['misc']['err_dbl']));
		$step = 2;

	} // foreach
	} // if

	break;


} // switch - step - error



switch($step) {


	default:
		$func->error("Under construction", '');
	/*
		$mastersearch = new MasterSearch($vars, 'index.php?mod=misc&action=ip', "index.php?mod=misc&action=ip&step=2&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
*/
	break;


	case 2:
		$templ['seating']['ip']['form']['control']['backlink'] = "index.php?mod=misc&action=ip";
		$templ['seating']['ip']['form']['control']['action']   = "index.php?mod=misc&action=ip&step=3&blockid=$blockid";



		$templ['misc']['general']['case']['control']['plan'] .=  $seat->view_block_with_input($blockid);

		$templ['misc']['ip']['case']['info']['js_popup'] = $seat->convert_js_string("ip", $blockid, "", $lang['misc']['this_plan'], 0);

		$templ['index']['info']['content'] .= $dsp->FetchModTpl("misc","misc_ip_form_step2");

	break;


	case 3:

	foreach($seating_ip as $seatid => $ip) {

		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET ip='$ip' WHERE seatid='$seatid'");


		}

	$func->confirmation($lang['misc']['cf_add_ips'], "index.php?mod=seating&action=ip");


	break;


} // switch - step



?>
