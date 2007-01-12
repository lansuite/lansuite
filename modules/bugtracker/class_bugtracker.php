<?
class Bugtracker {

  var $stati = array();

  // Constructor
  function Bugtracker() {
    $this->stati[0] = t('Neu');
    $this->stati[1] = t('Bestätigt');
    $this->stati[2] = t('In Bearbeitung');
    $this->stati[3] = t('Feedback benötigt');
    $this->stati[4] = t('Behoben');
    $this->stati[5] = t('Aufgeschoben');
    $this->stati[6] = t('Geschlossen');
  }

  function SetBugStateInternal($bugid, $state) {
    global $db, $config, $func, $auth, $mail;

    if ($auth['type'] <= 1) {
      $row = $db->query_first("SELECT caption, state FROM {$config['tables']['bugtracker']} WHERE bugid = ". (int)$bugid);
      if ($state > 2) {
        $func->information(t('Der Status für des Eintrags "%1" konnte nicht geändert werden, da sie nur auf die Stati "Neu" und "Bestätigt wechseln dürfen"', array($row['caption'])));
        return;
      } elseif ($row['state'] == 6) {
        $func->information(t('Der Status für des Eintrags "%1" konnte nicht geändert werden, da er bereits auf geschlossen steht"', array($row['caption'])));
        return;
      }
    }

    $row = $db->query_first("SELECT 1 AS found FROM {$config['tables']['bugtracker']} WHERE state = ". (int)$state ." AND bugid = ". (int)$bugid);
    if (!$row['found']) {
      $db->query("UPDATE {$config['tables']['bugtracker']} SET state = ". (int)$state .' WHERE bugid = '. (int)$bugid);
      $func->log_event(t('Problem auf Status "%1" geändert', array($this->stati[$state])), 1, '', $bugid);
      if ($state == 3) {
        $row = $db->query_first("SELECT reporter, caption FROM {$config['tables']['bugtracker']} WHERE bugid = ". (int)$bugid);
        $mail->create_sys_mail($row['reporter'], t('Feedback zu deinem Bugreport benötigt'), t('Der Status deines Bugreports %1 wurd auf Feedback benötigt gesetzt. Bitte schau dir den Report noch einmal an und helfe deine Angaben zu vervollständigen', array($row['caption'])));
        $func->log_event(t('Benachrichtigungsmail an Reporter versandt'), 1, '', $bugid);
      }
    }
  }

  function AssignBugToUserInternal($bugid, $userid) {
    global $db, $config, $func;

    $row = $db->query_first("SELECT 1 AS found FROM {$config['tables']['bugtracker']} WHERE agent = ". (int)$userid ." AND bugid = ". (int)$bugid);
    if (!$row['found']) {
      $db->query("UPDATE {$config['tables']['bugtracker']} SET agent = ". (int)$userid .' WHERE bugid = '. (int)$bugid);

      if ($userid == 0) $func->log_event(t('Benutzerzuordnung gelöscht'), 1, '', $bugid);
      else {
        $row = $db->query_first("SELECT username FROM {$config['tables']['user']} WHERE userid = ". (int)$userid);
        $func->log_event(t('Problem Benutzer "%1" zugeordnet', array($row['username'])), 1, '', $bugid);
      }
    }
  }

  function AssignBugToUser($bugid, $userid) {

    $this->AssignBugToUserInternal($bugid, $userid);

    if ($userid == 0) $this->SetBugStateInternal($bugid, 0);
    else $this->SetBugStateInternal($bugid, 2);
  }

  function SetBugState($bugid, $state) {
    global $auth;

    $this->SetBugStateInternal($bugid, $state);
    if ($state == 2 or $state == 3 or $state == 4) $this->AssignBugToUserInternal($bugid, $auth['userid']);
  }
}
?>