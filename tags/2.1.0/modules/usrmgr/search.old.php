<?php

switch($_GET["step"]){
	default:
		if($cfg['sys_barcode_on']){
			$dsp->AddBarcodeForm("<strong>" . $lang['barcode']['barcode'] . "</strong>","","index.php?mod=usrmgr&action=details&userid=");
		}
		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=search", "index.php?mod=usrmgr&action=details&userid=", "GROUP BY email");
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();	
	break;

	case 2:
		$templ['usrmgr']['form']['action'] = "index.php?mod=usrmgr&action=extendsearch&step=3";
		$templ['index']['info']['content'] .= $dsp->FetchModTpl("usrmgr","usrmgr_extendsearch_form");
	break;

	case 3:
		$sql = "";
		$q = 0; 

		switch($_POST["user_type"]) {
			case "1": $sql = ($q==1) ? $sql."u.type = '1'" : "u.type = '1'"; $q = 1; $sqlinfo = "- {$lang['usrmgr']['search_guest']}" . HTML_NEWLINE; break;
			case "2": $sql = ($q==1) ? $sql."u.type > '1'" : "u.type > '1'"; $q = 1; $sqlinfo = "- {$lang['usrmgr']['search_orga']}" . HTML_NEWLINE; break;			
			case "3": $sql = ($q==1) ? $sql."u.type = '2'" : "u.type = '2'"; $q = 1; $sqlinfo = "- {$lang['usrmgr']['search_admin']}" . HTML_NEWLINE; break;			
			case "4": $sql = ($q==1) ? $sql."u.type = '3'" : "u.type = '3'"; $q = 1; $sqlinfo = "- {$lang['usrmgr']['search_op']}" . HTML_NEWLINE; break;			
			case "5": $sql = ($q==1) ? $sql."u.type < '1'" : "u.type < '1'"; $q = 1; $sqlinfo = "- {$lang['usrmgr']['search_deactivated']}" . HTML_NEWLINE; break;			
		}

		switch($_POST["user_paid"]) {
			case "1": $sql = ($q==1) ? $sql." AND p.paid = '0'" : $sql."p.paid = '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_not_paid']}" . HTML_NEWLINE; break;
			case "2": $sql = ($q==1) ? $sql." AND (p.paid = '1' OR p.paid = '2')" : $sql."(p.paid = '1' OR p.paid = '2')"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_paid']}" . HTML_NEWLINE; break;			
			case "3": $sql = ($q==1) ? $sql." AND p.paid = '2' " : $sql."p.paid = '2' "; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_paid_ak']}" . HTML_NEWLINE; break;
		}

		switch($_POST["user_seatcontrol"]) {
			case "1": $sql = ($q==1) ? $sql." AND p.seatcontrol = '1'" : "p.seatcontrol = '1'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_seat_paid']}" . HTML_NEWLINE; break;
			case "2": $sql = ($q==1) ? $sql." AND p.seatcontrol = '0' " : "p.seatcontrol = '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_seat_not_paid']}" . HTML_NEWLINE; break;
		}

		switch($_POST["user_checkinout"]) {
			case "1": $sql = ($q==1) ? $sql." AND p.checkin = '0'" : "p.checkin = '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_not_checked_in']}" . HTML_NEWLINE; break;	
			case "2": $sql = ($q==1) ? $sql." AND p.checkin > '0' AND p.checkout = '0'" : "p.checkin > '0' AND p.checkout = '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_checked_in']}" . HTML_NEWLINE; break;
			case "3": $sql = ($q==1) ? $sql." AND p.checkout > '0'" : "p.checkout > '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_checked_out']}" . HTML_NEWLINE; break;
		}

		switch($_POST["user_wwclnglid"]) {
			case "1": $sql = ($q==1) ? $sql." AND wwclid = '0' AND nglid = '0'" : "wwclid = '0' AND nglid = '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_no_wwcl_ngl']}" . HTML_NEWLINE; break;			
			case "2": $sql = ($q==1) ? $sql." AND wwclid > '0'" : "wwclid > '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_wwcl']}" . HTML_NEWLINE; break;
			case "3": $sql = ($q==1) ? $sql." AND nglid > '0'" : "nglid > '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_ngl']}" . HTML_NEWLINE; break;			
			case "4": $sql = ($q==1) ? $sql." AND wwclid > '0' AND nglid > '0'" : "wwclid > '0' AND nglid > '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_ngl_wwcl']}" . HTML_NEWLINE; break;	
		}

		switch($_POST["user_sex"]) {
			case "1": $sql = ($q==1) ? $sql." AND sex = '0'" : "sex = '0'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_unknown_sex']}" . HTML_NEWLINE; break;			
			case "2": $sql = ($q==1) ? $sql." AND sex = '1'" : "sex = '1'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_male']}" . HTML_NEWLINE; break;
			case "3": $sql = ($q==1) ? $sql." AND sex = '2'" : "sex = '2'"; $q = 1; $sqlinfo = $sqlinfo."- {$lang['usrmgr']['search_female']}" . HTML_NEWLINE; break;	
		}

		if ($_POST["user_paid"] != "0" || $_POST["user_checkinout"] != "0" || $_POST["user_seatcontrol"] != "0")
			$sql .= " AND  p.party_id={$party->party_id} ";
		else
			$sql = ($q==1) ? $sql." GROUP BY email" : "1 GROUP BY email";

		$mastersearch = new MasterSearch($vars, "index.php?mod=usrmgr&action=extendsearch", "index.php?mod=usrmgr&action=details&userid=", " AND $sql");
		$mastersearch->LoadConfig("extendusersearch", $lang['usrmgr']['ms_search'], str_replace("%SQL%", $sqlinfo, $lang['usrmgr']['ms_result_own']));
//		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();

		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;
}
?>
