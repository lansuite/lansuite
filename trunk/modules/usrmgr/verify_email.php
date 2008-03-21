<?php

$dsp->NewContent(t('Email-Adresse verifizieren'), '');

$user_data = $db->qry_first('SELECT fcode FROM %prefix%user WHERE fcode = %string%', $_GET['verification_code']);
if ($user_data['fcode'] and $_GET['verification_code'] != '') {
  $db->qry('UPDATE %prefix%user SET email_verified = 1, fcode = '' WHERE fcode = %string%');
  
	$func->confirmation(t('Ihre Email-Adresse wurde erfolgreich verifiziert. Vielen Dank!'), 'index.php');
} else $func->error(t('Der von Ihnen Übermittelte Verifizierungscode ist inkorrekt! Bitte prüfen Sie, ob Sie die URL komplett aus der Benachrichtigungs-Mail kopiert haben.'));
?>
