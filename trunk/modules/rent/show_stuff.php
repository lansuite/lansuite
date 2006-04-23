<?php

$step 	 = $vars["step"];
$item_id = $vars["itemid"];
$user_id = $vars["userid"];

switch($step) {
	default:
    include_once('modules/rent/search.inc.php');
	break;

	case 2:		// abfrage ob eintrag verliehen werden soll
		$checkempty = $db->query_first("SELECT quantity FROM {$config["tables"]["rentstuff"]} WHERE stuffid = '$item_id'");
		$quantity = $checkempty["quantity"];
		if ($quantity > 0) $func->question($lang['rent']['show_stuff_question'],"index.php?mod=rent&action=show_stuff&step=3&itemid=$item_id","index.php?mod=rent&action=show_stuff");
		else $func->error($lang['rent']['show_stuff_not_rent'],"index.php?mod=rent&action=show_stuff");
	break;

	case 3:		// user auswählen
    $additional_where = 'u.type >= 1';
    $current_url = 'index.php?mod=rent&action=show_stuff&step=3';
    $target_url = "index.php?mod=rent&action=show_stuff&step=4&itemid=$item_id&userid=";
    include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;

	case 4:		// set database
		$db->query("UPDATE {$config["tables"]["rentstuff"]} SET quantity = quantity-1, rented = rented+1 WHERE stuffid = '$item_id'");

		$add_it = $db->query("INSERT INTO {$config["tables"]["rentuser"]} SET
								stuffid = '{$item_id}',
								userid = '{$user_id}',
								out_orgaid = '{$_SESSION["auth"]["userid"]}',
								back_orgaid = '0'
								");

		if ($add_it == 1) $func->confirmation($lang['rent']['show_stuff_rent_ok'],"index.php?mod=rent&action=show_stuff");
		else $func->error("NO_REFRESH","");
	break;
}
?>