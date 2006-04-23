<?
class UsrMgr {
  function LockAccount($userid) {
    global $db, $config;

    $db->query("UPDATE {$config["tables"]["user"]} SET locked = 1 WHERE userid=". (int)$userid);
  }

  function UnlockAccount($userid) {
    global $db, $config;

    $db->query("UPDATE {$config["tables"]["user"]} SET locked = 0 WHERE userid=". (int)$userid);
  }
}
?>