<?php

include_once('modules/guestlist/class_guestlist.php');
$guestlist = new guestlist;

switch($_GET['step']) {
  // Paid
  case 10:
    if (!$_POST['action'] and $_GET['userid']) $_POST['action'][$_GET['userid']] = 1;

    if ($auth['type'] >= 2 and $_POST['action']) {
      $Messages = array('success' => '', 'error' => '');
      foreach ($_POST['action'] as $key => $val) {
        $Msg = $guestlist->SetPaid($key, $party->party_id);
        if ($Msg['success']) $Messages['success'] .= $Msg['success'] . HTML_NEWLINE;
        if ($Msg['error']) $Messages['error'] .= $Msg['error'] . HTML_NEWLINE;
      }
      $func->confirmation(t('Der Zahlstatus wurde erfolgreich gesetzt'));
      if ($Messages['success']) $func->confirmation(t('Die Mails wurden erfolgreich an die folgenden Benutzer versandt:') . HTML_NEWLINE . $Messages['success'], NO_LINK);
      if ($Messages['error']) $func->information(t('Fehler beim Versenden der Mails an folgende Benutzer:') . HTML_NEWLINE . $Messages['error'], NO_LINK);
    }
  break;

  // Not paid
  case 11:
    if (!$_POST['action'] and $_GET['userid']) $_POST['action'][$_GET['userid']] = 1;

    if ($auth['type'] >= 2 and $_POST['action']) {
      $Messages = array('success' => '', 'error' => '');
      foreach ($_POST['action'] as $key => $val) {
        $Msg = $guestlist->SetNotPaid($key, $party->party_id);
        if ($Msg['success']) $Messages['success'] .= $Msg['success'] . HTML_NEWLINE;
        if ($Msg['error']) $Messages['error'] .= $Msg['error'] . HTML_NEWLINE;
      }
      $func->confirmation(t('Der Zahlstatus wurde erfolgreich entfernt'));
      if ($Messages['success']) $func->confirmation(t('Die Mails wurden erfolgreich an die folgenden Benutzer versandt:') . HTML_NEWLINE . $Messages['success'], NO_LINK);
      if ($Messages['error']) $func->information(t('Fehler beim Versenden der Mails an folgende Benutzer:') . HTML_NEWLINE . $Messages['error'], NO_LINK);
    }
  break;

  // Check in
  case 20:
    if (!$_POST['action'] and $_GET['userid']) $_POST['action'][$_GET['userid']] = 1;

    if ($auth['type'] >= 2 and $_POST['action']) {
      foreach ($_POST['action'] as $key => $val) {
        if ($guestlist->CheckIn($key, $party->party_id))
          $func->information(t('Der Benutzer #%1 konnte nicht eingecheckt werden, da er nicht auf "bezahlt" steht', array($key)), NO_LINK);
      }
      $func->confirmation(t('Checkin wurde durchgeführt'));
    }
  break;

  // Check out
  case 21:
    if (!$_POST['action'] and $_GET['userid']) $_POST['action'][$_GET['userid']] = 1;

    if ($auth['type'] >= 2 and $_POST['action']) {
      foreach ($_POST['action'] as $key => $val) {
        if ($guestlist->CheckOut($key, $party->party_id))
          $func->information(t('Der Benutzer #%1 konnte nicht ausgecheckt werden, da er nicht eingecheckt ist', array($key)), NO_LINK);
      }
      $func->confirmation(t('Checkout wurde durchgeführt'));
    }
  break;

  // Delete check in + out
  case 22:
    if (!$_POST['action'] and $_GET['userid']) $_POST['action'][$_GET['userid']] = 1;

    if ($auth['type'] >= 2 and $_POST['action']) {
      foreach ($_POST['action'] as $key => $val) {
        $guestlist->UndoCheckInOut($key, $party->party_id);
      }
      $func->confirmation(t('Checkin- und Checkout- Marken wurden entfernt'));
    }
  break;
}

if (!$_GET['userid']) include_once('modules/guestlist/search.inc.php');
?>