<?php
// Switch
$step = $vars['step'];


switch($step) {

	// Mastersearch
	default:
    $additional_where = 'u.userid != '. (int)$auth["userid"];
    $current_url = 'index.php?mod=msgsys&action=addbuddy';
    $target_url = 'index.php?mod=msgsys&action=addbuddy&step=2&userid=';
    include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;

// Add
case 2:

  if ($_GET['userid'] == '') $func->error($lang['msgsys']['err_no_user_choosen'],"index.php?mod=msgsys&action=addbuddy");
  else {
/*
	// Check vars
	if( $vars['checkbox'] == "" ) {
		$func->error($lang['msgsys']['err_no_user_choosen'],"index.php?mod=msgsys&action=addbuddy");
	} else {
		// print_r($vars['checkbox']);
		foreach( $vars['checkbox'] AS $send_user ) {
			// echo "-".$send_user."-";
			$user[]	= $send_user;
		}
*/
    $user[] = $_GET['userid'];
		foreach( $user as $buddyid ) {

			// User already in list ?
			$existsinthelist = $db->query("
			SELECT 		id
			FROM		{$config[tables][buddys]}
			WHERE		userid ='{$_SESSION[auth][userid]}'
			AND		buddyid ='$buddyid'
			");

			if($db->num_rows() != "0") {
				$user_exist_in_the_list = 1;
			}

			// Does the user exist ?
			$exist = $db->query("
			SELECT 		userid
			FROM		{$config[tables][user]}
			WHERE		userid ='$buddyid'
			");
			if($db->num_rows() != "0") {
				$user_exist = 1;
			}

			// Too many users in the list ?
			$num = $db->query("
			SELECT 		id
			FROM		{$config[tables][buddys]}
			WHERE		userid ='{$_SESSION[auth][userid]}'
			");
			$user_num = $db->num_rows();
			if($user_num >= $config[size][buddies]) {
				$to_many_users = 1;
			}

			// Is it the User himself ?
			if($buddyid == $_SESSION[auth][userid])
			$i_am_the_user = 1;

			// Get name
     	$name = $db->query_first("
     	SELECT 		username, firstname, name
     	FROM		{$config[tables][user]}
     	WHERE		userid = '$buddyid'
     	");

			// If the user isn't in the list
			if($user_exist_in_the_list != 1 && $user_exist == 1 && $to_many_users != 1 && $i_am_the_user != 1)
			{

	          		$insert = $db->query("
	          		INSERT INTO	{$config[tables][buddys]}
	          		SET 		userid = '{$_SESSION[auth][userid]}', buddyid = '$buddyid'
	          		");

				if($insert == TRUE){
					if($cfg['sys_internet'] == 0){
						$sux[] = $name["username"] . " (" . $name["firstname"] . " " . $name["name"] . ")";
					}else{
						$sux[] = $name["username"];
					}
				}
			}
			//
			// If the user is already in the list
			//
			else
			{
				if($cfg['sys_internet'] == 0){
					$err[] = $name["username"] . " (" . $name["firstname"] . " " . $name["name"] . ")";
				}else{
					$err[] = $name["username"];
				}
			}

		} // foreach

			//
			// Confirmations / Errors
			//
			//
			// Successful
			//
			if(count($sux) > "0" && count($err) == "0")
			{
				foreach($sux as $item)
				{ if($names1 != "") { $names1 .= ", "; } $names1 .= "$item"; }
				$func->confirmation(str_replace('%NAMES1%',$names1,$lang['msgsys']['add_confirm']),$func->internal_referer);

			} // if

			//
			// Partly Successful
			//
			elseif(count($sux) > "0" && count($err) > "0")
			{
				foreach($sux as $item)
				{
					if($names1 != "")
					{
						$names1 .= ", ";
					}
					$names1 .= "$item";
				}
				foreach($err as $item)
				{
					if($names2 != "")
					{
						$names2 .= ", ";
					}
					$names2 .= "$item";
				}
				$func->confirmation(str_replace('%NAMES2%',$names2,str_replace('%NAMES1%',$names1,$lang['msgsys']['add_confirm2'])),"");
			} // elseif

			//
			// Not successful
			//
			elseif(count($sux) == "0" && count($err) > "0")
			{
				$func->error($lang['msgsys']['err_add'],"");
			} // elseif
		}
break;
} // switch
?>