<?php

switch($_GET["step"]){
	default:
		if($cfg['sys_barcode_on']){
			$dsp->AddBarcodeForm("<strong>" . $lang['barcode']['barcode'] . "</strong>","","index.php?mod=usrmgr&action=details&userid=");
		}

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2();
    
    $ms2->query['from'] = "{$config['tables']['user']} AS u LEFT JOIN {$config['tables']['party_user']} AS p ON u.userid = p.user_id";
  
    $ms2->config['EntriesPerPage'] = 20;

    $ms2->AddTextSearchField($lang['usrmgr']['userid'], array('u.userid' => 'exact'));
    $ms2->AddTextSearchField($lang['usrmgr']['add_username'], array('u.username' => '1337'));
    $ms2->AddTextSearchField($lang['usrmgr']['name'], array('u.name' => 'like', 'u.firstname' => 'like'));
  
    $ms2->AddTextSearchDropDown($lang['usrmgr']['add_type'], 'u.type', array('' => $lang['usrmgr']['all'], '1' => $lang['usrmgr']['details_guest'], '!1' => 'Nicht Gast', '2' => $lang['usrmgr']['add_type_admin'], '3' => $lang['usrmgr']['add_type_operator'], '2,3' => 'Admin und Op'));
    	
    $party_list = array('' => 'Alle');
    $row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
    while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
    $db->free_result($row);
    $ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list, $party->party_id);
  
    $ms2->AddTextSearchDropDown($lang['usrmgr']['add_paid'], 'p.paid', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['add_paid_no'], '>1' => $lang['usrmgr']['details_paid']));
    $ms2->AddTextSearchDropDown($lang['usrmgr']['checkin'], 'p.checkin', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['checkin_no'], '>1' => $lang['usrmgr']['checkin']));
    $ms2->AddTextSearchDropDown($lang['usrmgr']['checkout'], 'p.checkout', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['checkout_no'], '>1' => $lang['usrmgr']['checkout']));
  
    
    $ms2->AddResultField($lang['usrmgr']['add_username'], 'u.username');
    $ms2->AddResultField($lang['usrmgr']['add_firstname'], 'u.firstname');
    $ms2->AddResultField($lang['usrmgr']['add_lastname'], 'u.name');
    $ms2->AddResultField($lang['usrmgr']['details_clan'], 'u.clan', 'http://', 'u.clanurl');
    // If Party selected
    if ($_POST["search_dd_input"][1] != '' or $_GET["search_dd_input"][1] != '') {
      $ms2->AddResultField('Bez.', 'p.paid');
      $ms2->AddResultField('In', 'p.checkin', '', '', 'GetDate');
      $ms2->AddResultField('Out', 'p.checkout', '', '', 'GetDate');
    }
  
    $ms2->AddIconField('details', 'u.userid', 'index.php?mod=usrmgr&action=details&userid=');
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'u.userid', 'index.php?mod=usrmgr&action=change&step=1&userid=');
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'u.userid', 'index.php?mod=usrmgr&action=delete&step=2&userid=');
  
    $ms2->PrintSearch('index.php?mod=usrmgr&action=search', 'u.userid');
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