<?php

function ShowRole ($role) {
  global $auth, $line;

  if ($role) $ret = t('Clan-Admin');
  else $ret = t('Clan-Mitglied');
  
  if (($_GET['clanid'] == $auth['clanid'] and $auth['clanadmin']) or $auth['type'] > 1) $ret = '<a href="index.php?mod=clanmgr&action=clanmgr&step=50&clanid='. $_GET['clanid'] .'&userid='. $line['userid'] .'">'. $ret .'</a>';
  
  return $ret;
}

function CheckClanPW ($clanpw) {
  global $db, $config, $auth;

  $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_GET['clanid']);
  if ($clan['password'] and $clan['password'] == md5($clanpw)) return true;
  return false;
}

function CheckExistingClan() {
	global $auth, $db, $func;
	$clanuser = $db->qry_first("SELECT clanid FROM %prefix%user WHERE userid=%int%", $auth['userid']);
	if($clanuser["clanid"] == NULL | $clanuser["clanid"] == 0)
		return true;	
	else
	{
		$func->error(t('Bevor du einen neuen Clan anlegen kannst, musst du aus deinem aktuellen Clan austreten.'), "index.php?mod=clanmgr");
		return false;
	}
}

function CountAdmins() {
	global $auth, $db, $func;
	
	 $query_admins = $db->qry("SELECT * FROM %prefix%user WHERE clanid = %int% AND clanadmin = 1",$_GET['clanid']);     
     return $db->num_rows($query_admins); 
}

function Update($id) {
	global $auth, $db, $func;
	
	if(!$_GET['clanid'])
	$func->log_event(t('Clan %1 erstellt', $_POST['name']), 1, t('clanmgr'));

		
	if($db->qry("UPDATE %prefix%user SET clanid = %int%, clanadmin = 1 WHERE userid =%int%", $id, $auth["userid"]))
			$func->confirmation(t('Der Clan wurde erfolgreich angelegt. Als Ersteller haben Sie die Rolle Admin in diesem Clan.'), "index.php?mod=clanmgr");
}
	


switch ($_GET['step']) {
  default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('clanmgr');
    
    $ms2->query['from'] = "{$config["tables"]["clan"]} AS c
        LEFT JOIN {$config["tables"]["user"]} AS u ON c.clanid = u.clanid";
    
    $ms2->config['EntriesPerPage'] = 20;
    #$ms2->AddBGColor('c.clanid', array($auth['clanid'] => 'ff0000'));
    
    $ms2->AddTextSearchField(t('Clanname'), array('c.name' => '1337', 'c.url' => 'like'));
    $ms2->AddTextSearchDropDown(t('Mitglieder'), 'COUNT(u.clanid)', array('' => t('Alle'), '0' => t('Ohne Mitglieder'), '>1' => t('Mit Mitglieder')));
    
    $ms2->AddResultField(t('Clanname'), 'c.name');
    $ms2->AddResultField(t('Webseite'), 'c.url');
    $ms2->AddResultField(t('Mitglieder'), 'COUNT(u.clanid) AS members');

    $ms2->AddIconField('details', 'index.php?mod=clanmgr&step=2&clanid=', t('Clan-Details'));
    if ($auth['type'] >= 2) $ms2->AddIconField('change_pw', 'index.php?mod=clanmgr&step=10&clanid=', t('Passwort ändern'));
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=clanmgr&step=30&clanid=', t('Editieren'));
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=clanmgr&step=20&clanid=', t('Löschen'));

    if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=clanmgr&step=20', 1);

    $ms2->PrintSearch('index.php?mod=clanmgr', 'c.clanid');
   if ($auth['type'] >= 1)$dsp->AddSingleRow($dsp->FetchButton('index.php?mod=clanmgr&step=30', 'add'));
    
  break;

  // Details
  case 2:
    $row = $db->qry_first('SELECT name, url, clanlogo_path FROM %prefix%clan WHERE clanid = %int%', $_GET['clanid']);
    
    if (func::chk_img_path($row['clanlogo_path'])) $dsp->AddDoubleRow(t(''), '<img src="'. $row['clanlogo_path'] .'" alt="'.$row['name'].'">');
    $dsp->AddDoubleRow(t('Clan'), $row['name']);
    $dsp->AddDoubleRow(t('Webseite'), '<a href="'. $row['url'] .'" target="_blank">'. $row['url'] .'</a>');
    
    $buttons = '';
    if ($auth['type'] >= 1 and $auth['clanid'] != $_GET['clanid']) $buttons .= $dsp->FetchSpanButton(t('Clan beitreten'), 'index.php?mod='. $_GET['mod'] .'&step=60&clanid='. $_GET['clanid']).' ';
    if ($auth['type'] >= 1 and $auth['clanid'] == $_GET['clanid']) $buttons .= $dsp->FetchSpanButton(t('Clan verlassen'), 'index.php?mod='. $_GET['mod'] .'&step=40&clanid='. $_GET['clanid'].'&userid='.$auth['userid']).' ';
    if (($auth['type'] >= 1 and $auth['clanid'] == $_GET['clanid'] and $auth['clanadmin'] == 1) or $auth['type'] >= 2) $buttons .= $dsp->FetchSpanButton(t('Clan editieren'), 'index.php?mod='. $_GET['mod'] .'&step=30&clanid='. $_GET['clanid']).' ';
    if (($auth['type'] >= 1 and $auth['clanid'] == $_GET['clanid'] and $auth['clanadmin'] == 1) or $auth['type'] >= 2) $buttons .= $dsp->FetchSpanButton(t('Passwort ändern'), 'index.php?mod='. $_GET['mod'] .'&step=10&clanid='. $_GET['clanid']).' ';
    $dsp->AddDoubleRow('',$buttons);
    

    $dsp->AddFieldSetStart(t('Mitglieder'));
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms3 = new mastersearch2('clanmgr');
    
    $ms3->query['from'] = "{$config["tables"]["user"]} AS u";
    $ms3->query['where'] = "u.clanid = ". (int)$_GET['clanid'];
    
    $ms3->config['EntriesPerPage'] = 100;
    $ms3->AddSelect('u.firstname');
	  $ms3->AddSelect('u.name');
    $ms3->AddResultField(t('Benutzername'), 'u.username', 'UserNameAndIcon');
    if (!$cfg['sys_internet'] or $auth['type'] > 1 or $auth['clanid'] == $_GET['clanid']) {
      $ms3->AddResultField(t('Vorname'), 'u.firstname', '');
      $ms3->AddResultField(t('Nachname'), 'u.name', '');
    }
    $ms3->AddResultField(t('Rolle'), 'u.clanadmin', 'ShowRole');
    
    $ms3->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Clan-Details'));
    if ($auth['type'] >= 3 | ($auth['clanid'] == $_GET['clanid'] & $auth['clanadmin'] == 1)) $ms3->AddIconField('delete', 'index.php?mod=clanmgr&action=clanmgr&step=40&clanid='. $_GET['clanid'] .'&userid=', t('Löschen'));

    $ms3->PrintSearch('index.php?mod=clanmgr&action=clanmgr&step=2', 'u.userid');
    $dsp->AddFieldSetEnd();

    $dsp->AddBackButton('index.php?mod=clanmgr&action=clanmgr');
    
	$AddSelect_List = array();
	array_push($AddSelect_List, 'u.firstname');
	array_push($AddSelect_List, 'u.name');
	array_push($AddSelect_List, 'u.clanadmin');
	
    include('inc/classes/class_mastercomment.php');
    new Mastercomment('Clan', $_GET['clanid'], '', $AddSelect_List);
  break;

  // Change clan password
  case 10:
    if($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
    elseif ($_GET['clanid'] != $auth['clanid'] and $auth['type'] < 2) $func->information(t('Sie sind nicht berechtigt das Passwort dieses Clans zu ändern'), "index.php?mod=home");
    else {
      include_once('inc/classes/class_masterform.php');
      $mf = new masterform();

      if ($auth['type'] < 2) $mf->AddField(t('Dezeitiges Passwort'), 'old_password', IS_PASSWORD, '', FIELD_OPTIONAL, 'CheckClanPW');
      $mf->AddField(t('Neues Passwort'), 'password', IS_NEW_PASSWORD);

      if ($mf->SendForm('index.php?mod=clanmgr&action=clanmgr&step=10', 'clan', 'clanid', $_GET['clanid'])) {

        // Send information mail to all clan members
      	$clanuser = $db->qry("SELECT userid, username, email FROM %prefix%user WHERE clanid=%int%", $_GET['clanid']);
      	while ($data = $db->fetch_array($clanuser)) {
      		$mail->create_mail($auth['userid'], $data['userid'], t('Clanpasswort geändert'), t('Das Clanpasswort wurde durch den Benutzer %1 in "%2" geändert', array($auth['username'], $_POST['password_original'])));
      		$mail->create_inet_mail($data['username'], $data['email'], t('Clanpasswort geändert'), t('Das Clanpasswort wurde durch den Benutzer %1 in "%2" geändert', array($auth['username'], $_POST['password_original'])), $cfg["sys_party_mail"]);
      	}
      	$func->log_event(t('Das Clanpasswort wurde durch den Benutzer %1 geändert', $auth['username']), 1, t('clanmgr'));
      }
    }
  break;

  // Delete
  case 20:
    if ($auth['type'] >= 3) {
      if ($_GET['clanid']) $_POST['action'][$_GET['clanid']] = 1;
      if ($_POST['action']) foreach ($_POST['action'] as $key => $val) {
        $db->qry("DELETE FROM %prefix%clan WHERE clanid = %string%", $key);
        $db->qry("UPDATE %prefix%user SET clanid = 0 WHERE clanid = %string%", $key);
      }
      $func->confirmation(t('Löschen erfolgreich'), 'index.php?mod=clanmgr&action=clanmgr');      
    }
  break;
  
  // Add - Edit
  case 30:
   // if ($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
    if ($_GET['clanid'] != '' and !($_GET['clanid'] == $auth['clanid'] and $auth['clanadmin'] == 1) and $auth['type'] < 2) $func->information(t('Sie sind nicht berechtigt diesen Clan zu ändern'), "index.php?mod=home");
    else {
      include_once('inc/classes/class_masterform.php');
      $mf = new masterform();

      $dsp->AddFieldsetStart(t('Clan-Daten'));
      $mf->AddField(t('Clanname'), 'name');
      if(!$_GET['clanid']) $mf->AddField(t('Beitritts Passwort'), 'password', IS_NEW_PASSWORD);
      $mf->AddField(t('Webseite'), 'url', '', '', FIELD_OPTIONAL);
      $mf->AddField(t('Clanlogo'), 'clanlogo_path', IS_FILE_UPLOAD, 'ext_inc/clan/'. $auth['userid'] .'_', FIELD_OPTIONAL);
      
      
      
      if (!$_GET['clanid']) $mf->CheckBeforeInserFunction = 'CheckExistingClan';
      $mf->AdditionalDBUpdateFunction = 'Update';
      $mf->SendForm('index.php?mod=clanmgr&step='. $_GET['step'], 'clan', 'clanid', $_GET['clanid']);
      	

      $dsp->AddFieldsetEnd();
		
	  if ($_GET['clanid'] != '')
		{
      $dsp->AddFieldsetStart(t('Mitglieder'));
      include_once('modules/mastersearch2/class_mastersearch2.php');
      $ms2 = new mastersearch2('clanmgr');

      $ms2->query['from'] = "{$config["tables"]["user"]} AS u";
      $ms2->query['where'] = 'u.clanid = '. (int)$_GET['clanid'];
      $ms2->config['EntriesPerPage'] = 20;

      $ms2->AddResultField(t('Vorname'), 'u.firstname');
      $ms2->AddResultField(t('Nachname'), 'u.name');
      $ms2->AddResultField(t('Benutzername'), 'u.username');
      $ms2->AddResultField(t('Rolle'), 'u.clanadmin', 'ShowRole');

      $ms2->AddIconField('delete', 'index.php?mod=clanmgr&action=clanmgr&step=40&clanid='. $_GET['clanid'] .'&userid=', t('Löschen'));
      $ms2->PrintSearch('index.php?mod=clanmgr&action=clanmgr&step=30&clanid='. $_GET['clanid'] .'&userid=', 'u.userid');
      $dsp->AddFieldsetEnd();
		}
      $dsp->AddBackButton('index.php?mod=clanmgr&action=clanmgr');
		
    }
  break;
  
  // Delete Member
  case 40:
    if ($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
    elseif(CountAdmins() == 1 and $auth['clanadmin'] == 1) {$func->information(t('Löschen nicht möglich. Sie sind der einzige Clan-Admin in diesem Clan. Bennen Sie bitte vorher einen weiteren Admin.'), 'index.php?mod=clanmgr&action=clanmgr&step=2&clanid='. $_GET['clanid']);}
    elseif (($_GET['clanid'] == $auth['clanid'] and $auth['clanadmin'] == 1) or ($_GET['clanid'] == $auth['clanid'] and $_GET['userid'] = $auth['userid']) or $auth['type'] > 2) {
      $db->qry("UPDATE %prefix%user SET clanid = 0 WHERE userid = %int%", $_GET['userid']);
      $func->confirmation(t('Löschen erfolgreich'), 'index.php?mod=clanmgr&action=clanmgr&step=2&clanid='. $_GET['clanid']);
    } else $func->information(t('Sie sind nicht berechtigt Mitglieder aus diesem Clan zu entfernen'), "index.php?mod=home");
  break;

  // Change role
  case 50:
    if ($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
    elseif (($_GET['clanid'] == $auth['clanid'] and $auth['clanadmin']) or $auth['type'] > 1) {  	
      $cur_role = $db->qry_first("SELECT username, clanadmin FROM %prefix%user WHERE clanid = %int% AND userid = %int%", $_GET['clanid'], $_GET['userid']);
      if ($cur_role['clanadmin'] and CountAdmins() == 1) $func->information(t('Sie können %1 nicht die Admin Rolle entziehen, da %1 z.z das einzige Mitglied mit der Rolle Clan-Admin ist. Benennen Sie bitte vorher einen anderen Admin.', $cur_role['username']), "index.php?mod=clanmgr&step=2&clanid=".$_GET["clanid"]);
      elseif ($cur_role['clanadmin']) {
        $db->qry("UPDATE %prefix%user SET clanadmin = 0 WHERE userid = %int%", $_GET['userid']);
        $func->confirmation(t('Benutzer %1 ist nun kein Clan-Admin mehr',$cur_role['username']), 'index.php?mod=clanmgr&action=clanmgr&step=2&clanid='. $_GET['clanid']);
      } else {
        $db->qry("UPDATE %prefix%user SET clanadmin = 1 WHERE userid = %int%", $_GET['userid']);
        $func->confirmation(t('Benutzer %1 ist nun Clan-Admin',$cur_role['username']), 'index.php?mod=clanmgr&action=clanmgr&step=2&clanid='. $_GET['clanid']);
      }
    } else $func->information(t('Sie sind nicht berechtigt die Berehtigung dieses Nutzers zu verändern'), "index.php?mod=home");
  break;
  
  //Clan beitreten
  case 60:     
  if ($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
  elseif ($auth["type"] < 1) $func->error(t('Keine Berechtigung diese Funktion auszuführen'), "index.php?mod=home");
  elseif(!$_POST['clan_pass'])
  {
  	$dsp->SetForm("index.php?mod=clanmgr&action=clanmgr&step=60&clanid=".$_GET['clanid']);
    $dsp->AddSingleRow(t('Um den Clan beizutreten, müssen Sie das Clanpasswort eingeben. Sollten Sie dies nicht kennen, wenden Sie sich bitte an Ihren Clan-Admin.'));
    $dsp->AddPasswordRow("clan_pass", t('Clan Passwort'), $_POST['clan_pass'], $mail_error);
    $dsp->AddFormSubmitRow("send");
    $dsp->AddBackButton("index.php?mod=clanmgr&action=clanmgr&step=2&clanid=".$_GET['clanid'], "usrmgr/pwremind");
  }
  else
  {
  	if(CheckClanPW($_POST['clan_pass']))
  	{
  		  $db->qry("UPDATE %prefix%user SET clanid = %int%, clanadmin = 0 WHERE userid =%int%", $_GET['clanid'], $auth["userid"]);
  		  $tmpclanname =  $db->qry_first("SELECT name FROM %prefix%clan WHERE clanid = %int%", $_GET['clanid']);
  		  $func->confirmation(t('Sie sind erfolgreich dem Clan beigetreten.'), "index.php?mod=clanmgr&action=clanmgr&step=2&clanid=".$_GET['clanid']);
  		  $func->log_event(t('%1 ist dem Clan %2 beigetreten', $auth['username'], $tmpclanname['name']), 1, t('clanmgr'));
  	}
  	else
  		 $func->error(t('Das eingegebene Clanpasswort ist falsch.'), "index.php?mod=clanmgr&action=clanmgr&step=60&clanid=".$_GET['clanid']);
  }
	
}

?>