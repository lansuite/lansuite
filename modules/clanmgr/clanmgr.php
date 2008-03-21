<?php

function CheckClanPW ($clanpw) {
  global $db, $config, $auth;

  $clan = $db->query_first("SELECT password FROM {$config['tables']['clan']} WHERE clanid = '{$_GET['clanid']}'");
  if ($clan['password'] and $clan['password'] != md5($clanpw)) return t('Passwort falsch!');
  return false;
}

switch ($_GET['step']) {
  default:
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('clanmgr');
    
    $ms2->query['from'] = "{$config["tables"]["clan"]} AS c
        LEFT JOIN {$config["tables"]["user"]} AS u ON c.clanid = u.clanid";
    
    $ms2->config['EntriesPerPage'] = 20;
    
    $ms2->AddTextSearchField(t('Clanname'), array('c.name' => '1337', 'c.url' => 'like'));
    $ms2->AddTextSearchDropDown(t('Mitglieder'), 'COUNT(u.clanid)', array('' => t('Alle'), '0' => t('Ohne Mitglieder'), '>1' => t('Mit Mitglieder')));
    
    $ms2->AddResultField(t('Clanname'), 'c.name');
    $ms2->AddResultField(t('Webseite'), 'c.url');
    $ms2->AddResultField(t('Mitglieder'), 'COUNT(u.clanid) AS members');

    $ms2->AddIconField('details', 'index.php?mod=clanmgr&action=clanmgr&step=2&clanid=', t('Clan-Details'));
    if ($auth['type'] >= 2) $ms2->AddIconField('change_pw', 'index.php?mod=clanmgr&action=clanmgr&step=10&clanid=', t('Passwort ändern'));
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=clanmgr&action=clanmgr&step=30&clanid=', t('Editieren'));
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=clanmgr&action=clanmgr&step=20&clanid=', t('Löschen'));

    if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=clanmgr&action=clanmgr&step=20', 1);

    $ms2->PrintSearch('index.php?mod=clanmgr&action=clanmgr', 'c.clanid');
  break;

  // Details
  case 2:
    $row = $db->qry_first('SELECT name, url FROM %prefix%clan WHERE clanid = %int%', $_GET['clanid']);
    $dsp->AddDoubleRow(t('Clan'), $row['name']);
    $dsp->AddDoubleRow(t('Webseite'), '<a href="'. $row['url'] .'" target="_blank">'. $row['url'] .'</a>');

    $dsp->AddFieldSetStart(t('Mitglieder'));
    $res2 = $db->qry('SELECT userid, firstname, name, username FROM %prefix%user WHERE clanid = %int%', $_GET['clanid']);
    while ($row2 = $db->fetch_array($res2)) {
      if (!$cfg['sys_internet'] or $auth['type'] > 1 or $auth['userid'] == $_GET['userid']) $realname = $row2['firstname'] .' '. $row2['name'];
      else $realname = ''; 
      $dsp->AddDoubleRow($row2['username'] .' '. $dsp->FetchUserIcon($row2['userid']), $realname);
    }
    $db->free_result($res2);
    $dsp->AddFieldSetEnd();

    $dsp->AddBackButton('index.php?mod=clanmgr&action=clanmgr');
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
      	$clanuser = $db->query("SELECT userid, username, email FROM {$config['tables']['user']} WHERE clanid='{$_GET['clanid']}'");
      	while ($data = $db->fetch_array($clanuser)) {
      		$mail->create_mail($auth['userid'], $data['userid'], t('Clanpasswort geändert'), t('Das Clanpasswort wurde durch den Benutzer %1 in "%2" geändert', array($auth['username'], $_POST['password_original'])));
      		$mail->create_inet_mail($data['username'], $data['email'], t('Clanpasswort geändert'), t('Das Clanpasswort wurde durch den Benutzer %1 in "%2" geändert', array($auth['username'], $_POST['password_original'])), $cfg["sys_party_mail"]);
      	}
      	$func->log_event(t('Das Clanpasswort wurde durch den Benutzer %1 geändert', $auth['username']), 1, t('Clanmanager'));
      }
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
      $func->confirmation(t('Löschen erfolgreich'), 'index.php?mod=clanmgr&action=clanmgr');      
    }
  break;
  
  // Edit
  case 30:
    if ($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
    elseif ($_GET['clanid'] != $auth['clanid'] and $auth['type'] < 2) $func->information(t('Sie sind nicht berechtigt das Passwort dieses Clans zu ändern'), "index.php?mod=home");
    else {
      include_once('inc/classes/class_masterform.php');
      $mf = new masterform();

      $dsp->AddFieldsetStart(t('Clan-Daten'));
      $mf->AddField(t('Clanname'), 'name');
      $mf->AddField(t('Webseite'), 'url', '', '', FIELD_OPTIONAL);
      $mf->SendForm('index.php?mod=clanmgr&action=clanmgr&step='. $_GET['step'], 'clan', 'clanid', $_GET['clanid']);
      $dsp->AddFieldsetEnd();

      $dsp->AddFieldsetStart(t('Mitglieder'));
      include_once('modules/mastersearch2/class_mastersearch2.php');
      $ms2 = new mastersearch2('clanmgr');

      $ms2->query['from'] = "{$config["tables"]["user"]} AS u";
      $ms2->query['where'] = 'u.clanid = '. (int)$_GET['clanid'];
      $ms2->config['EntriesPerPage'] = 20;

      $ms2->AddResultField(t('Vorname'), 'u.firstname');
      $ms2->AddResultField(t('Nachname'), 'u.name');
      $ms2->AddResultField(t('Benutzername'), 'u.username');

      $ms2->AddIconField('delete', 'index.php?mod=clanmgr&action=clanmgr&step=40&clanid='. $_GET['clanid'] .'&userid=', t('Löschen'));
      $ms2->PrintSearch('index.php?mod=clanmgr&action=clanmgr&step=30&clanid='. $_GET['clanid'], 'u.userid');
      $dsp->AddFieldsetEnd();

      $dsp->AddBackButton('index.php?mod=clanmgr&action=clanmgr');
    }
  break;
  
  // Delete Member
  case 40:
    if ($_GET['clanid'] == '') $func->error(t('Keine Clan-ID angegeben!'), "index.php?mod=home");
    elseif ($_GET['clanid'] != $auth['clanid'] and $auth['type'] < 2) $func->information(t('Sie sind nicht berechtigt das Passwort dieses Clans zu ändern'), "index.php?mod=home");
    else {
      $db->query("UPDATE {$config["tables"]["user"]} SET clanid = 0 WHERE userid = ". (int)$_GET['userid']);
      $func->confirmation(t('Löschen erfolgreich'), 'index.php?mod=clanmgr&action=clanmgr&step=30&clanid='. $_GET['clanid']);
    }
  break;
}

?>
