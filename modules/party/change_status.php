<?php

include_once("modules/usrmgr/class_usrmgr.php");
include_once("modules/signon/language/signon_lang_de.php");
include_once("modules/usrmgr/language/usrmgr_lang_de.php");

$usrmgr = new UsrMgr();

function PartyMail() {
    global $cfg, $usrmgr, $func, $mail;
    $usrmgr->WriteXMLStatFile();
        if ($cfg["signon_password_mail"]) {
        if ($usrmgr->SendSignonMail(1)) $func->confirmation(t('Eine Bestätigung der Anmeldung wurde an Ihre E-Mail-Adresse gesendet.'), NO_LINK);
        else {
            $func->error(t('Es ist ein Fehler beim Versand der Informations-E-Mail aufgetreten.'). $mail->error, NO_LINK);
            $cfg['signon_password_view'] = 1;
        }
    }
    return true;
}

if ($auth['type'] >= 2) {
$user_data = $db->qry_first("SELECT * FROM %prefix%user AS u WHERE u.userid = %int%",$_GET['user_id']);

    function ChangeAllowed($id) {
        global $db, $lang, $auth;
      // Do not allow changes, if user has paid
        $row = $db->qry_first("SELECT paid FROM %prefix%party_user WHERE party_id = %int% AND user_id = %int%", $_GET['party_id'],  $id);
        // if ($row['paid']) return $lang['usrmgr']['err_paid_no_change'];
      return false;
    }

    // Show Partydata
    include_once('inc/classes/class_masterform.php');
    $MFID = 0;
    $party_data = $db->qry_first("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate
                                  FROM %prefix%partys WHERE party_id=%int%",   $_GET['party_id']);
    $dsp->NewContent(t("Partystatus ändern"), t("Hier k&ouml;nnen sie die Anmeldedaten im Detail f&uuml;r jeden User / Party einstellen"));
        $dsp->AddDoubleRow("Username", '<b>'.$user_data['username'].'</b>');
    $dsp->AddDoubleRow("Party", '<b>'.$party_data['name'] .'</b> ('. $func->unixstamp2date($party_data['startdate'], 'datetime') .' - '. $func->unixstamp2date($party_data['enddate'], 'datetime') .')');
        
        $mf = new masterform($MFID);
    $mf->AdditionalKey = 'party_id = '. $party_data['party_id'];
    
        // Signon
        $mf->AddInsertControllField = t('Angemeldet').'|'.t('Wenn dieses Häckchen gesetzt ist, sind Sie zu dieser Party angemeldet');
        $mf->AddChangeCondition = 'ChangeAllowed';
    
        // Paid
          $selections = array();
          $selections['0'] = $lang['usrmgr']['add_paid_no'];
          $selections['1'] = $lang['usrmgr']['add_paid_vvk'];
          $selections['2'] = $lang['usrmgr']['add_paid_ak'];
          $mf->AddField($lang['usrmgr']['add_paid'], 'paid', IS_SELECTION, $selections);
    
        // Prices
        $selections = array();  
    $res2 = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$party_data['party_id']}");
        while ($row2 = $db->fetch_array($res2)) $selections[$row2['price_id']] = $row2['price_text'] .' ['. $row2['price'] .' '. $cfg['sys_currency'] .']';
        if ($selections) $mf->AddField(t('Eintrittspreis'), 'price_id', IS_SELECTION, $selections, FIELD_OPTIONAL);
        else $mf->AddField(t('Eintrittspreis'), 'price_id', IS_TEXT_MESSAGE, t('Für diese Party wurden keine Preise definiert'));
        $db->free_result($res2);

          $mf->AddField(t('Bezahltdatum'), 'paiddate', '', '', FIELD_OPTIONAL);
          $mf->AddField($lang['usrmgr']['checkin'], 'checkin', '', '', FIELD_OPTIONAL);
          $mf->AddField($lang['usrmgr']['checkout'], 'checkout', '', '', FIELD_OPTIONAL);
          $mf->AddField($lang['usrmgr']['signondate'], 'signondate', '', '', FIELD_OPTIONAL);

        $mf->SendButtonText = 'An-/Abmelden';

        $mf->AdditionalDBUpdateFunction = 'PartyMail';
    $mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&party_id='. $party_data['party_id'], 'party_user', 'user_id', $_GET['user_id']);
    $db->free_result($res);

  } else $func->error('ACCESS_DENIED', '');

  $dsp->AddContent();
?>