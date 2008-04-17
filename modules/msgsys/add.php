<?php

switch($_GET['step']) {

	// Mastersearch
	default:
    $additional_where = 'u.userid != '. (int)$auth["userid"];
    $current_url = 'index.php?mod=msgsys&action=addbuddy';
    $target_url = 'index.php?mod=msgsys&action=addbuddy&step=2&userid=';
    include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;

// Add
case 2:

  if ($_GET['userid'] == '') $func->error(t('Sie haben keinen Benutzer ausgew&auml;hlt'),"index.php?mod=msgsys&action=addbuddy");
  else {

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
				$func->confirmation(str_replace('%NAMES1%',$names1,t('Die folgenden Benutzer wurden in Ihre Buddy-Liste hinzugef&uuml;gt:
                                           <b>%NAMES1%</b> HTML_NEWLINE Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf sichtbar.')),$func->internal_referer);

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
				$func->confirmation(str_replace('%NAMES2%',$names2,str_replace('%NAMES1%',$names1,t('Die folgenden Benutzer wurden in Ihre Buddy-Liste hinzugef&uuml;gt:
                                           <b>%NAMES1%</b> HTML_NEWLINE
                                           Folgende Benutzer konnten nicht in Ihre Buddy-Liste hinzugef&uuml;gt werden:
                                           <b>%NAMES2%</b> HTML_NEWLINE
                                           Dies kann folgende Ursachen haben: HTML_NEWLINE
                                           - Der Benutzer ist bereits in Ihrer Buddy-Liste HTML_NEWLINE
                                           - Der Benutzer existiert nicht HTML_NEWLINE
                                           - Es sind bereits zuviele Benutzer in Ihrer Buddy-Liste HTML_NEWLINE
                                           - Sie versuchen sich selbst in die Buddy-Liste hinzuzuf&uuml;gen HTML_NEWLINE
                                           Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf sichtbar.'))),"");
			} // elseif

			//
			// Not successful
			//
			elseif(count($sux) == "0" && count($err) > "0")
			{
				$func->error(t('Es konnten keine Benutzer in die Buddy-Liste hinzugef&uuml;gt werden. HTML_NEWLINE
					  Dies kann folgende Ursachen haben: HTML_NEWLINE
					  - Der Benutzer ist bereits in Ihrer Buddy-Liste HTML_NEWLINE
					  - Der Benutzer existiert nicht HTML_NEWLINE
					  - Es sind bereits zuviele Benutzer in Ihrer Buddy-Liste HTML_NEWLINE
					  - Sie versuchen sich selbst in die Buddy-Liste hinzuzuf&uuml;gen HTML_NEWLINE'),"");
			} // elseif
		}
break;
} // switch
?>