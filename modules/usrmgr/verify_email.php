<?php

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        $dsp->NewContent(t('Email-Adresse verifizieren'), '');

        $user_data = $database->queryWithOnlyFirstRow('SELECT 1 AS found FROM %prefix%user WHERE fcode = ?', [$_GET['verification_code']]);
        if ($user_data['found'] and $_GET['verification_code'] != '') {
            $database->query('UPDATE %prefix%user SET email_verified = 1, fcode = \'\' WHERE fcode = ?', [$_GET['verification_code']]);
  
            $func->confirmation(t('Deine Email-Adresse wurde erfolgreich verifiziert. Vielen Dank!'), 'index.php');
        } else {
            $func->error(t('Der von dir Übermittelte Verifizierungscode ist inkorrekt! Bitte prüfe, ob du die URL komplett aus der Benachrichtigungs-Mail kopiert hast.'));
        }
        break;
  
    case 2:
        $mail = new \LanSuite\Module\Mail\Mail();
        $usrmgr = new \LanSuite\Module\UsrMgr\UserManager($mail);
        if ($usrmgr->SendVerificationEmail($_GET['userid'])) {
            $func->confirmation(t('Die Verifikations-Email ist versandt worden.'));
        }
        break;
}
