<?php

include_once("modules/usrmgr/class_usrmgr.php");
include_once("modules/signon/language/signon_lang_de.php");
$usrmgr = new UsrMgr();

function PartyMail() {
  global $cfg, $usrmgr, $func, $mail;

  $usrmgr->WriteXMLStatFile();

  if ($cfg["signon_password_mail"]) {
  	if ($usrmgr->SendSignonMail(1)) $func->confirmation(t('Eine Bestätigung der Anmeldung wurde an deine eMail-Adresse gesendet'), NO_LINK);
  	else {
  		$func->error(t('Es ist ein Fehler beim Versand der Informations-eMail aufgetreten. Fehlermeldung:'). $mail->error, NO_LINK);
  		$cfg['signon_password_view'] = 1;
  	}
  }
  
  return true;
}


if ($party->count == 0) $func->information(t('Aktuell sind keine Partys geplant'), 'index.php?mod='. $_GET['mod']);
else {

  if ($_GET['user_id'] == $auth['userid'] or $auth['type'] >= 2) {
  
    function ChangeAllowed($id) {
      global $db, $config, $row, $lang, $func, $auth;
    
      // Do not allow changes, if party is over
      if ($row['enddate'] < time()) return $lang['usrmgr']['err_party_over'];
      
      // Signon started?
      if ($row['sstartdate'] > time()) return $lang['signon']['signon_start']. HTML_NEWLINE .'<strong>'. $func->unixstamp2date($row['sstartdate'], 'daydatetime'). '</strong>';
    
      // Signon ended?
      if ($row['senddate'] < time()) return $lang['signon']['signon_closed']. HTML_NEWLINE .'<strong>'. $func->unixstamp2date($row['senddate'], 'daydatetime'). '</strong>';
    
      // Do not allow changes, if user has paid
      if ($auth['type'] <= 1) {
        $row2 = $db->query_first("SELECT paid FROM {$config['tables']['party_user']} WHERE party_id = ". (int)$_GET['party_id'] ." AND user_id = ". (int)$id);
        if ($row2['paid']) return $lang['usrmgr']['err_paid_no_change'];
      }
      
      return false;
    }
    

    // Show Upcomming
    include_once('inc/classes/class_masterform.php');
    $MFID = 0;

    $res = $db->query("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM {$config['tables']['partys']} WHERE UNIX_TIMESTAMP(enddate) >= UNIX_TIMESTAMP(NOW()) ORDER BY startdate");
    while ($row = $db->fetch_array($res)) {
      if ($_GET['mf_step'] != 2 or $row['party_id'] == $_GET['party_id']) {

        $dsp->AddFieldsetStart($row['name'] .' ('. $func->unixstamp2date($row['startdate'], 'datetime') .' - '. $func->unixstamp2date($row['enddate'], 'datetime') .')');
        $mf = new masterform($MFID);
        $mf->AdditionalKey = 'party_id = '. $row['party_id'];
    
        // Signon
        $mf->AddInsertControllField = $lang['usrmgr']['signon'];
        $mf->AddChangeCondition = 'ChangeAllowed';
    
        // Paid
        if ($auth['type'] >= 2) {
          $selections = array();
          $selections['0'] = $lang['usrmgr']['add_paid_no'];
          $selections['1'] = $lang['usrmgr']['add_paid_vvk'];
          $selections['2'] = $lang['usrmgr']['add_paid_ak'];
          $mf->AddField($lang['usrmgr']['add_paid'], 'paid', IS_SELECTION, $selections);
        }
    
        // Prices
        $selections = array();  
        $res2 = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$row['party_id']}");
        while ($row2 = $db->fetch_array($res2)) $selections[$row2['price_id']] = $row2['price_text'] .' ['. $row2['price'] .' '. $cfg['sys_currency'] .']';
        $mf->AddField($lang['usrmgr']['prince_id'], 'price_id', IS_SELECTION, $selections, FIELD_OPTIONAL);
        $db->free_result($res2);
    
        if ($auth['type'] >= 2) {
          //$mf->AddField('Seatcontrol', 'seatcontrol', '', '', FIELD_OPTIONAL);
          $mf->AddField($lang['usrmgr']['checkin'], 'checkin', '', '', FIELD_OPTIONAL);
          $mf->AddField($lang['usrmgr']['checkout'], 'checkout', '', '', FIELD_OPTIONAL);
          $mf->AddField($lang['usrmgr']['signondate'], 'signondate', '', '', FIELD_OPTIONAL);
        }

        $mf->AdditionalDBUpdateFunction = 'PartyMail';
        $mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&party_id='. $row['party_id'], 'party_user', 'user_id', $_GET['user_id']);
        $dsp->AddFieldsetEnd();
      }
      $MFID++;
    }
    $db->free_result($res);


    // ShowHistory
    $dsp->AddFieldsetStart(t('Vergangene Partys'));
    $res = $db->query("SELECT p.*, pu.user_id, pu.paid, pu.checkin, pu.checkout, UNIX_TIMESTAMP(p.enddate) AS enddate, UNIX_TIMESTAMP(p.sstartdate) AS sstartdate, UNIX_TIMESTAMP(p.senddate) AS senddate, UNIX_TIMESTAMP(p.startdate) AS startdate FROM {$config['tables']['partys']} AS p
      LEFT JOIN {$config['tables']['party_user']} AS pu ON p.party_id = pu.party_id
      WHERE UNIX_TIMESTAMP(p.enddate) < UNIX_TIMESTAMP(NOW()) AND (pu.user_id = ". (int)$_GET['user_id'] ." OR pu.user_id IS NULL)
      ORDER BY p.startdate");
    while ($row = $db->fetch_array($res)) {
      $text = '';
      if ($row['user_id']) {
        $text .= t('Du warst angemeldet');
        if ($row['paid'] == 0) $text .= t(', aber hattest nicht bezahlt');
        if ($row['paid'] == 1) $text .= t('und hattest per Vorkasse gezahlt');
        if ($row['paid'] == 2) $text .= t('und hattest per Abendkasse gezahlt');

        if ($row['price_id']) $text .= '('. $row['price_id'] .')';
        $text .= '.'. HTML_NEWLINE;

        if ($row['checkin']) $text .= t('Eingecheckt: '). $func->unixstamp2date($row['checkin'], 'datetime') .' ';
        if ($row['checkout']) $text .= t('Ausgecheckt: ').$func->unixstamp2date($row['checkout'], 'datetime');

      } else $text .= t('Du warst nicht angemeldet');
      $dsp->AddDoubleRow($row['name'] .' ('. $func->unixstamp2date($row['startdate'], 'datetime') .' - '. $func->unixstamp2date($row['enddate'], 'datetime') .')', $text);
    }
    $db->free_result($res);
    $dsp->AddFieldsetEnd();

  } else $func->error('ACCESS_DENIED', ''); 

  $dsp->AddBackButton('index.php?mod='. $_GET['mod']);
  $dsp->AddContent();
}
?>
