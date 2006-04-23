<?php
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
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');

    $ms2->query['from'] = "{$config["tables"]["rentuser"]} AS u
      LEFT JOIN {$config["tables"]["rentstuff"]} AS s ON u.stuffid = s.stuffid
      LEFT JOIN {$config["tables"]["user"]} AS r ON u.userid = r.userid";
    $ms2->query['where'] = "u.back_orgaid = ''";

    $ms2->AddTextSearchField('Mieter', array('r.username' => '1337', 'r.name' => 'like', 'r.firstname' => 'like'));

    $ms2->AddSelect('r.userid');
    $ms2->AddResultField('Mieter', 'r.username', 'UserNameAndIcon');
    $ms2->AddResultField('Gemietet', 'COUNT(*) AS RentCount');

    $ms2->AddIconField('details', 'index.php?mod=rent&action=show_out&step=2&userid=', $lang['ms2']['details']);

    $ms2->PrintSearch('index.php?mod=rent&action=show_out', 'u.userid');
  break;

	
	case 2:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');

    $ms2->query['from'] = "{$config["tables"]["rentstuff"]} AS s
      LEFT JOIN {$config["tables"]["rentuser"]} AS u ON u.stuffid = s.stuffid
      LEFT JOIN {$config["tables"]["user"]} AS v ON u.out_orgaid = v.userid";
    $ms2->query['where'] = "u.back_orgaid = '' AND u.userid = {$_GET['userid']}";

    $ms2->AddTextSearchField('Vermieter', array('v.username' => '1337', 'v.name' => 'like', 'v.firstname' => 'like'));

    $ms2->AddSelect('v.userid');
    $ms2->AddResultField('Equipment', 's.caption');
    $ms2->AddResultField('Vermieter', 'v.username', 'UserNameAndIcon');

    $ms2->AddIconField('delete', 'index.php?mod=rent&action=show_out&step=20&userid=22&itemid=3&stuffid=', $lang['ms2']['delete']);

    $ms2->PrintSearch('index.php?mod=rent&action=show_out', 's.stuffid');

/*
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
*/
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
