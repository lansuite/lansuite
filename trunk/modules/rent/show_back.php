<?php

$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];

switch($step) {
	default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');

    $ms2->query['from'] = "{$config["tables"]["rentuser"]} AS u
      LEFT JOIN {$config["tables"]["rentstuff"]} AS s ON u.stuffid = s.stuffid
      LEFT JOIN {$config["tables"]["user"]} AS um ON u.userid = um.userid
      LEFT JOIN {$config["tables"]["user"]} AS uv ON u.out_orgaid = uv.userid
      LEFT JOIN {$config["tables"]["user"]} AS uz ON u.back_orgaid = uz.userid";
    $ms2->query['where'] = "u.back_orgaid != ''";

    $ms2->AddTextSearchField('Mieter', array('um.username' => '1337', 'um.name' => 'like', 'um.firstname' => 'like'));

    $ms2->AddSelect('u.userid');
    $ms2->AddResultField('Equipment', 's.caption');
    $ms2->AddResultField('Mieter', 'um.username AS Mieter', 'UserNameAndIcon');
    $ms2->AddResultField('Vermieter', 'uv.username AS Vermieter');
    $ms2->AddResultField('Zurücknehmer', 'uz.username AS Zuruecknehmer');

    $ms2->PrintSearch('index.php?mod=rent&action=show_back', 'u.userid, s.stuffid');
	break;
}
?>