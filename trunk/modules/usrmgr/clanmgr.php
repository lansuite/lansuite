<?php

switch ($_GET['step']) {
  default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('usrmgr');
    
    $ms2->query['from'] = "{$config["tables"]["clan"]} AS c
        LEFT JOIN {$config["tables"]["user"]} AS u ON c.clanid = u.clanid";
    
    $ms2->config['EntriesPerPage'] = 20;
    
    $ms2->AddTextSearchField($lang['usrmgr']['clan_name'], array('c.name' => '1337', 'c.url' => 'like'));
    $ms2->AddTextSearchDropDown($lang['usrmgr']['clan_members'], 'COUNT(u.clanid)', array('' => $lang['usrmgr']['all'], '0' => $lang['usrmgr']['clan_no_members'], '>1' => $lang['usrmgr']['clan_has_members']));
    
    $ms2->AddResultField($lang['usrmgr']['clan_name'], 'c.name');
    $ms2->AddResultField($lang['usrmgr']['clan_url'], 'c.url');
    $ms2->AddResultField($lang['usrmgr']['clan_members'], 'COUNT(u.clanid) AS members');

    if ($auth['type'] >= 2) $ms2->AddIconField('change_pw', 'index.php?mod=usrmgr&action=clanmgr&step=10&clanid=', $lang['ms2']['change_pw']);
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=clanmgr&step=30&clanid=', $lang['ms2']['edit']);
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=clanmgr&step=20&clanid=', $lang['ms2']['delete']);

    if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['ms2']['delete'], 'index.php?mod=usrmgr&action=clanmgr&step=20', 1);

    $ms2->PrintSearch('index.php?mod=usrmgr&action=clanmgr', 'c.clanid');
  break;
  
  // Change Password
  case 10:
    if ($auth['type'] >= 2) {
		 $dsp->SetForm("index.php?mod=usrmgr&action=clanmgr&step=11&clanid={$_GET['clanid']}");
		 $dsp->AddTextFieldRow('newclanpw', $lang["usrmgr"]["chpwd_password"], '', '');
		 $dsp->AddFormSubmitRow('change');
		 $dsp->AddBackButton('index.php?mod=usrmgr&action=clanmgr');
		 $dsp->AddContent();
    }
  break;
  
  case 11:
    if ($auth['type'] >= 2) {
  		$newclanpw = md5($_POST['newclanpw']);	
  		$db->query_first("UPDATE {$config['tables']['clan']} SET password='$newclanpw' WHERE clanid='{$_GET['clanid']}'");

			$clanuser = $db->query("SELECT userid, username, email FROM {$config['tables']['user']} WHERE clanid='{$_GET['clanid']}'");

			while ($data = $db->fetch_array($clanuser)) {
				$mail->create_mail($auth['userid'], $data['userid'], $lang['usrmgr']['clanpw_haschange_sub'], str_replace("%USER%", $auth['username'], str_replace("%PASSWORD%", $_POST['newclanpw'], $lang['usrmgr']['clanpw_haschange'])));
				$mail->create_inet_mail($data['username'], $data['email'], $lang['usrmgr']['clanpw_haschange_sub'], str_replace("%USER%", $auth['username'], str_replace("%PASSWORD%", $_POST['newclanpw'], $lang['usrmgr']['clanpw_haschange'])), $cfg["sys_party_mail"]);
			}

			$func->log_event(str_replace("%%USER%%", $auth['username'], $lang['usrmgr']['clanpw_haschange']),1);
			$func->confirmation($lang['usrmgr']['clanpw_haschange_sub'], 'index.php?mod=usrmgr&action=clanmgr');
    }
  break;
  
  // Delete
  case 20:
    if ($auth['type'] >= 3) {
      if ($_GET['clanid']) $_POST['action'][$_GET['clanid']] = 1;
      if ($_POST['action']) foreach ($_POST['action'] as $key => $val) {
        $db->query("DELETE FROM {$config["tables"]["clan"]} WHERE clanid = '$key'");
        $db->query("UPDATE {$config["tables"]["user"]} SET clanid = 0 WHERE clanid = '$key'");
      }
      $func->confirmation($lang['usrmgr']['clan_del_success'], 'index.php?mod=usrmgr&action=clanmgr');      
    }
  break;
  
  // Edit
  case 30:
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();

    $mf->AddField($lang['usrmgr']['clan'], 'name');
    $mf->AddField($lang['usrmgr']['add_clanurl'], 'url', '', '', FIELD_OPTIONAL);
    $mf->SendForm('index.php?mod=usrmgr&action=clanmgr&step='. $_GET['step'], 'clan', 'clanid', $_GET['clanid']);

    $dsp->AddFieldsetStart($lang['usrmgr']['clan_members']);
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('usrmgr');

    $ms2->query['from'] = "{$config["tables"]["user"]} AS u";
    $ms2->query['where'] = 'u.clanid = '. (int)$_GET['clanid'];
    $ms2->config['EntriesPerPage'] = 20;

    $ms2->AddResultField($lang['usrmgr']['add_firstname'], 'u.firstname');
    $ms2->AddResultField($lang['usrmgr']['add_lastname'], 'u.name');
    $ms2->AddResultField($lang['usrmgr']['add_username'], 'u.username');

    if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=clanmgr&step=40&clanid='. $_GET['clanid'] .'&userid=', $lang['ms2']['delete']);
    $ms2->PrintSearch('index.php?mod=usrmgr&action=clanmgr&step=30&clanid='. $_GET['clanid'], 'u.userid');
    $dsp->AddFieldsetEnd();

    $dsp->AddBackButton('index.php?mod=usrmgr&action=clanmgr');
    $dsp->AddContent();
  break;
  
  // Delete Member
  case 40:
    $db->query("UPDATE {$config["tables"]["user"]} SET clanid = 0 WHERE userid = ". (int)$_GET['userid']);
    $func->confirmation($lang['usrmgr']['del_success'], 'index.php?mod=usrmgr&action=clanmgr&step=30&clanid='. $_GET['clanid']);
  break;
}

?>