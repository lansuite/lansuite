<?php

class masterdelete {

  var $References = array();

  // Internal function, used to delete
  function DoDelete($table, $idname, $id) {
  global $func, $db, $config;

    $res = $db->query("DELETE FROM {$config['tables'][$table]} WHERE $idname = '$id'");
		if ($res) {
      foreach ($this->References as $key => $val) {
        if ($val == '') $val = $idname;
        $db->query("DELETE FROM {$config['tables'][$key]} WHERE $val = '$id'");
      }
      $func->log_event(t('Eintrag #%1 aus Tabelle "%2" gelöscht', array($id, $config['tables'][$table])), 1, 'Masterdelete');
		} else {
      $func->log_event(t('Fehler beim Löschen von #%1 aus Tabelle "%2"', array($id, $config['tables'][$table])), 3, 'Masterdelete');
    }
    
    return $res;
  }

  function Delete($table, $idname, $id) {
  global $CurentURLBase, $func;
  
    switch ($_GET['md_step']) {
      // Question
      default:

        $_SESSION['md_referrer'] = $func->internal_referer;
        $func->question(t('Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?'), $CurentURLBase.'&md_step=2', $_SESSION['md_referrer']);
        
        return false;
      break;

      // Action
      case 2:
        $res = $this->DoDelete($table, $idname, $id);
        if ($res) $func->confirmation(t('Der Eintrag wurde erfolgreich gelöscht'), $_SESSION['md_referrer']);
        else $func->error(t('Der Eintrag konnte nicht gelöscht werden'), $_SESSION['md_referrer']);

        unset($_SESSION['md_referrer']);
        return $res;
      break;
    }
  }

  function MultiDelete($table, $idname) {
  global $func;

    $failed = '';
    foreach ($_POST['action'] as $key => $val) {
      $res = $this->DoDelete($table, $idname, $key);
      if (!$res) $failed .= HTML_NEWLINE . '#'. $key;
    }

    if ($failed != '') $func->error(t('Die folgenden Einträge konnte nicht gelöscht werden').':'.$failed, $func->internal_referer);
    else $func->confirmation(t('Die Einträge wurde erfolgreich gelöscht'), $func->internal_referer);
    
    return !$failed;
  }
}
?>