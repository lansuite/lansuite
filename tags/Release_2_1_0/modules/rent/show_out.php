<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.1
*	Filename: 			show_out.php
*	Module: 			Verleih/Rent
*	Main editor: 		denny@one-network.org
*	Description: 		show all stuff thats rented at time
*	Remarks: 		
*
**************************************************************************/

$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];
$stuff_id = $vars["stuffid"];


switch($step) {

	case 20:		// set back 

		$db->query("UPDATE {$config["tables"]["rentuser"]} SET back_orgaid = '{$auth["userid"]}' WHERE rentid = '$item_id'");
		$db->query("UPDATE {$config["tables"]["rentstuff"]} SET quantity = quantity+1, rented = rented-1 WHERE stuffid = '$stuff_id'");
		$step = 2;

	break;

}

switch($step) {

	default:

		$mastersearch = new MasterSearch( $vars, "index.php?mod=rent&action=show_out", "index.php?mod=rent&action=show_out&step=2&userid=", "AND ru.back_orgaid='' GROUP BY ru.userid");
		$mastersearch->LoadConfig( "rentout", $lang['rent']['show_out_print_form'], $lang['rent']['show_out_search_result'] );
//		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	
	
	
	break;

	
	case 2:

		$get_organame = $db->query_first("SELECT uu.username FROM {$config["tables"]["rentuser"]} AS ru LEFT JOIN {$config["tables"]["user"]} AS uu ON ru.userid=uu.userid WHERE ru.userid = '$user_id'");

		$get_stuff = $db->query("SELECT ru.rentid, rs.stuffid, rs.caption, uu.userid, uu.username FROM {$config["tables"]["rentuser"]} AS ru LEFT JOIN {$config["tables"]["rentstuff"]} AS rs ON ru.stuffid = rs.stuffid LEFT JOIN {$config["tables"]["user"]} AS uu ON uu.userid=ru.out_orgaid WHERE ru.userid='$user_id' AND ru.back_orgaid = '0'");
		if($db->num_rows($get_stuff) > 0) {
			$dsp->NewContent($lang['rent']['rent_on_user'] . " " .$get_organame["username"],$lang['rent']['rent_info']);
			while( $row = $db->fetch_array( $get_stuff ) ) {
				$templ["rent"]["stuffback"]["row"]["control"]["link"] = "index.php?mod=rent&action=show_out&step=20&userid=".$user_id."&itemid=".$row["rentid"]."&stuffid=".$row["stuffid"];
				$templ["rent"]["stuffback"]["row"]["info"]["caption"] = $row["caption"];
				$templ["rent"]["stuffback"]["row"]["info"]["out_organame"] = $row["username"];			
				$templ["rent"]["stuffback"]["row"]["info"]["orgaid"] = $row["userid"];			
				$templ['rent']['stuffback']['case']['control']['row'] .= $dsp->FetchModTpl("rent","rent_stuffback_row");
			}	
			
			$templ['rent']['stuffback']['case']['control']['eq'] = $lang['rent']['equipment'];
			$templ['rent']['stuffback']['case']['control']['rent_from'] = $lang['rent']['rent_from'];
			$dsp->AddSingleRow($dsp->FetchModTpl("rent","rent_stuffback_case"));
			$dsp->AddContent();
		}
		else
		{
			$dialog[0]="information";
			$dialog[1]="Information";
			$dialog[2]=str_replace("%NAME%",$get_organame["username"],$lang['rent']['user_no_rent']);
			$link[0]="index.php?mod=rent&action=show_out";
			$pic[0]="back";
			$func->dialog($dialog,$link,$pic);
		}
		
	break;
	
	case 3:		// abfrage ob eintrag zurückgenommen werden soll

		$checkempty = $db->query_first("SELECT rented FROM {$config["tables"]["rentstuff"]} WHERE stuffid = '$item_id'");
		$rented = $checkempty["rented"];
		if ($rented > 0) {
			$func->question($lang['rent']['show_out_get_rent'],"index.php?mod=rent&action=show_out&step=3&itemid=$item_id","index.php?mod=rent&action=show_stuff");
		}
		else
		{
			$func->error($lang['rent']['show_out_db_error'],"index.php?mod=rent&action=delete_stuff");
		}


	break;
	
}// switch

?>
