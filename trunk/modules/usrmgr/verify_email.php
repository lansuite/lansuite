<?php

switch($_GET['step']) {
  default:
    $dsp->NewContent(t('Email-Adresse verifizieren'), '');

    $user_data = $db->qry_first('SELECT 1 AS found FROM %prefix%user WHERE fcode = %string%', $_GET['verification_code']);
    if ($user_data['found'] and $_GET['verification_code'] != '') {
      $db->qry('UPDATE %prefix%user SET email_verified = 1, fcode = \'\' WHERE fcode = %string%', $_GET['verification_code']);
  
	   $func->confirmation(t('Ihre Email-Adresse wurde erfolgreich verifiziert. Vielen Dank!'), 'index.php');
    } else $func->error(t('Der von Ihnen Übermittelte Verifizierungscode ist inkorrekt! Bitte prüfen Sie, ob Sie die URL komplett aus der Benachrichtigungs-Mail kopiert haben.'));
  break;
  
  case 2:
    include_once("modules/usrmgr/class_usrmgr.php");
    $usrmgr = new UsrMgr();   
    if ($usrmgr->SendVerificationEmail($_GET['userid'])) $func->confirmation(t('Die Verifikations-Email ist versandt worden.'));            
  break;
}
?>