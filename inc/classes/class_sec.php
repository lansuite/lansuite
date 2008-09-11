<?php
class sec {
	function check_blacklist () {
		global $db, $config, $cfg;

		// Global-Black-List
		$found = $db->qry_first("SELECT ip FROM %prefix%ip_blacklist
   WHERE ip = %string% AND (module = '' OR module = %string%)
   LIMIT 1
   ", $_SERVER['REMOTE_ADDR'], $_GET["mod"]);
		if ($found) die ("Deine IP wird von LanSuite geblockt. Melde dich bitte bei den Organisatoren");

    if ($cfg["reload_limit"]) {
  		// Reload-Black-List
  		if (!$cfg["reload_time"]) $cfg["reload_time"] = 600;
  		$db->qry("DELETE FROM %prefix%ip_hits WHERE (date + %string%) < %string%", $cfg["reload_time"], time());

  		$db->qry("INSERT INTO %prefix%ip_hits SET ip = %string%, module = %string%, action = %string%, step = %string%, date = %int%", $_SERVER['REMOTE_ADDR'], $_GET["mod"], $_GET["action"], $_GET["step"], time());

  		$ip_hits = $db->qry_first("SELECT COUNT(*) AS hits FROM %prefix%ip_hits
     WHERE ip = %string%
     GROUP BY ip
     LIMIT 1
     ", $_SERVER['REMOTE_ADDR']);

  		if (!$cfg["reload_hits"]) $cfg["reload_hits"] = 120;
  		if ($ip_hits["hits"] > $cfg["reload_hits"]) die ("Deine IP wird von LanSuite wegen zu häufigen Seitenaufrufen geblockt. Bitte warte ein wenig und versuche es dann erneut.");
    }
	}


	function lock ($module = NULL) {
		global $db, $config;

		$_SESSION["lock_$module"] = true;
		$db->qry("REPLACE INTO %prefix%ip_locklist SET ip = %string%, module = %string%", $_SERVER['REMOTE_ADDR'], $module);
	}

	function unlock ($module = NULL) {
		global $db, $config;

		$_SESSION["lock_$module"] = false;
		$db->qry("DELETE FROM %prefix%ip_locklist WHERE ip = %string% AND module = %string%", $_SERVER['REMOTE_ADDR'], $module);
	}

	function locked ($module = NULL, $referrer = '') {
		global $db, $config, $func;

		if ($_SESSION["lock_$module"]) $locked = true;
		else {
			$found = $db->qry_first("SELECT ip FROM %prefix%ip_locklist WHERE ip = %string% AND module = %string% LIMIT 1", $_SERVER['REMOTE_ADDR'], $module);
			if ($found) $locked = true;
			else $locked = false;
		}

		if ($locked) $func->error("NO_REFRESH", $referrer);
		return $locked;
	}
}
?>