<?php
$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];
$stuff_id = $vars["stuffid"];


switch($step) {

	case 20:		// set back 

		$db->query("UPDATE {$config["tables"]["rentuser"]} SET back_orgaid = '{$auth["userid"]}' WHERE userid = '$user_id' AND stuffid = '$stuff_id'");
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

    $ms2->AddIconField('delete', 'index.php?mod=rent&action=show_out&step=20&userid='. $_GET['userid'] .'&stuffid=', $lang['ms2']['delete']);

    $ms2->PrintSearch('index.php?mod=rent&action=show_out', 's.stuffid');
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
