<?php

class masterdelete {

  function Delete($table, $idname, $id) {
  global $CurentURLBase, $func, $db, $config;
  
    switch ($_GET['md_step']) {
      // Question
      default:
        $_SESSION['md_referrer'] = $func->internal_referer;
        $func->question(t('Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?'), $CurentURLBase.'&md_step=2', $_SESSION['md_referrer']);
      break;

      // Action
      case 2:
        $res = $db->query("DELETE FROM {$config['tables'][$table]} WHERE $idname = '$id'");
  			if ($res) {
  				$func->confirmation(t('Der Eintrag wurde erfolgreich gelöscht'), $_SESSION['md_referrer']);
          $func->log_event(t('Eintrag #%1 aus Tabelle "%2" gelöscht', array($id, $config['tables'][$table])), 1, 'Masterdelete');
  			} else {
  				$func->error(t('Der Eintrag konnte nicht gelöscht werden'), $_SESSION['md_referrer']);
          $func->log_event(t('Fehler beim Löschen von #%1 aus Tabelle "%2"', array($id, $config['tables'][$table])), 3, 'Masterdelete');
        }
        unset($_SESSION['md_referrer']);
      break;
    }
  }

  function MultiDelete($table, $idname, $id) {
  global $CurentURLBase, $func, $db, $config;

    $failed = '';
    foreach ($_POST['action'] as $key => $val) {
      $res = $db->query("DELETE FROM {$config['tables'][$table]} WHERE $idname = '$key'");
			if ($res) $func->log_event(t('Eintrag #%1 aus Tabelle "%2" gelöscht', array($key, $config['tables'][$table])), 1, 'Masterdelete');
			else {
        $failed .= HTML_NEWLINE . '#'. $key;
        $func->log_event(t('Fehler beim Löschen von #%1 aus Tabelle "%2"', array($key, $config['tables'][$table])), 3, 'Masterdelete');
      }
    }

		if ($failed != '') $func->error(t('Die folgenden Einträge konnte nicht gelöscht werden').':'.$failed, $_SESSION['md_referrer']);
    else $func->confirmation(t('Die Einträge wurde erfolgreich gelöscht'), $func->internal_referer);
  }
}
?>