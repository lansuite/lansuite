<?php
class sec {
	function check_blacklist () {
		global $db, $config, $cfg;

		// Global-Black-List
		$found = $db->query_first("SELECT ip FROM {$config["tables"]["ip_blacklist"]}
			WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND (module = '' OR module = '{$_GET["mod"]}')
			LIMIT 1
			");
		if ($found) die ("Deine IP wird von LanSuite geblockt. Melde dich bitte bei den Organisatoren");

    if ($cfg["reload_limit"]) {
  		// Reload-Black-List
  		if (!$cfg["reload_time"]) $cfg["reload_time"] = 600;
  		$db->query("DELETE FROM {$config["tables"]["ip_hits"]} WHERE (date + {$cfg["reload_time"]}) < ". time());

  		$db->query("INSERT INTO {$config["tables"]["ip_hits"]} SET ip = '{$_SERVER['REMOTE_ADDR']}', module = '{$_GET["mod"]}', action = '{$_GET["action"]}', step = '{$_GET["step"]}', date = ". time());

  		$ip_hits = $db->query_first("SELECT COUNT(*) AS hits FROM {$config["tables"]["ip_hits"]}
  			WHERE ip = '{$_SERVER['REMOTE_ADDR']}'
  			GROUP BY ip
  			LIMIT 1
  			");

  		if (!$cfg["reload_hits"]) $cfg["reload_hits"] = 120;
  		if ($ip_hits["hits"] > $cfg["reload_hits"]) die ("Deine IP wird von LanSuite wegen zu häufigen Seitenaufrufen geblockt. Bitte warte ein wenig und versuche es dann erneut.");
    }
	}


	function lock ($module = NULL) {
		global $db, $config;

		$_SESSION["lock_$module"] = true;
		$db->query("REPLACE INTO {$config["tables"]["ip_locklist"]} SET ip = '{$_SERVER['REMOTE_ADDR']}', module = '$module'");
	}

	function unlock ($module = NULL) {
		global $db, $config;

		$_SESSION["lock_$module"] = false;
		$db->query("DELETE FROM {$config["tables"]["ip_locklist"]} WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND module = '$module'");
	}

	function locked ($module = NULL) {
		global $db, $config, $func;

		if ($_SESSION["lock_$module"]) $locked = true;
		else {
			$found = $db->query_first("SELECT ip FROM {$config["tables"]["ip_locklist"]} WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND module = '$module' LIMIT 1");
			if ($found) $locked = true;
			else $locked = false;
		}

		if ($locked) $func->error("NO_REFRESH", $func->internal_referer);
		return $locked;
	}
}
?>