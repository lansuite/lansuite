<?php

class masterdelete {

  var $References = array();
  var $SubReferences = array();
  var $DeleteIfEmpty = array();
  var $LogID = 0;

  // Internal function, used to delete
  function DoDelete($table, $idname, $id) {
  global $func, $db, $config;

    // Get key to master table
    foreach ($this->DeleteIfEmpty as $key => $val) {
      $row = $db->query_first("SELECT $val FROM {$config['tables'][$table]} WHERE $idname = '$id'");
      $MasterKey[$key] = $row[$val];
    }

    // Delete main table
    $res = $db->qry("DELETE FROM %prefix%%plain% WHERE $idname = %string%", $table, $id);
		if ($res) {

      // Delete master tables, if content is now missing
      foreach ($this->DeleteIfEmpty as $key => $val) {
        if ($val == '') $val = $idname;
        $row = $db->query_first("SELECT 1 AS found FROM {$config['tables'][$table]} WHERE $val = '{$MasterKey[$key]}'");
        if (!$row['found']) $db->qry("DELETE FROM %prefix%%plain% WHERE $val = %string%", $key, $MasterKey[$key]);
      }

      // Delete all attached tables
      foreach ($this->References as $key => $val) {
        if ($val == '') $val = $idname;

        // If a table is attached, to the attached table, fetch all keys from the first and delete them in the second
        if ($this->SubReferences) foreach ($this->SubReferences as $key2 => $val2) if ($val2) {
          $res2 = $db->query("SELECT $val2 FROM {$config['tables'][$key]} WHERE $val = '$id'");
          while ($row2 = $db->fetch_array($res2)) $db->query("DELETE FROM {$config['tables'][$key2]} WHERE $val2 = '{$row2[$val2]}'");
          $db->free_result($res2);
        }

        $db->query("DELETE FROM {$config['tables'][$key]} WHERE $val = '$id'");
      }

      if ($table != 'log') $func->log_event(t('Eintrag #%1 aus Tabelle "%2" gelöscht', array($id, $config['tables'][$table])), 1, '', $this->LogID);
		} elseif ($table != 'log') $func->log_event(t('Fehler beim Löschen von #%1 aus Tabelle "%2"', array($id, $config['tables'][$table])), 3, '', $this->LogID);

    return $res;
  }

  function Delete($table, $idname, $id) {
  global $framework, $func;
  
    $CurentURLBase = $framework->get_clean_url_query('base');
	$CurentURLBase = str_replace('&md_step=2', '', $CurentURLBase);
    $CurentURLBase = preg_replace('#&'. $idname .'=[0-9]*#si', '', $CurentURLBase);

    switch ($_GET['md_step']) {
      // Question
      default:

        $_SESSION['md_referrer'] = $func->internal_referer;
        $func->question(t('Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?'), $CurentURLBase. '&md_step=2&'. $idname .'='. $id, $_SESSION['md_referrer']);
        
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
    if ($_POST['action']) {
      foreach ($_POST['action'] as $key => $val) {
        $res = $this->DoDelete($table, $idname, $key);
        if (!$res) $failed .= HTML_NEWLINE . '#'. $key;
      }

      if ($failed != '') $func->information(t('Die folgenden Einträge konnte nicht gelöscht werden').':'.$failed, $func->internal_referer);
      else $func->confirmation(t('Die Einträge wurde erfolgreich gelöscht'), $func->internal_referer);
    } else $func->information(t('Es wurden keine Einträge selektiert'));
    
    return !$failed;
  }
}
?>
