<?php
class sec {
	function check_blacklist () {
		global $db, $cfg;

		// Global-Black-List
		if (strpos($cfg['ip_blacklist'], $_SERVER['REMOTE_ADDR']) !== false) die ("Deine IP wird von LanSuite geblockt. Melde dich bitte bei den Administratoren");

    if ($cfg["reload_limit"]) {
  		// Reload-Black-List
  		if (!$cfg["reload_time"]) $cfg["reload_time"] = 600;
  		$db->qry("DELETE FROM %prefix%ip_hits WHERE (date + %int%) < NOW()", $cfg["reload_time"]);
  		$db->qry("INSERT INTO %prefix%ip_hits SET ip = INET_ATON(%string%)",
              $_SERVER['REMOTE_ADDR'], $_GET["mod"], $_GET["action"], $_GET["step"]);

  		$ip_hits = $db->qry_first("SELECT COUNT(*) AS hits FROM %prefix%ip_hits
            WHERE ip = INET_ATON(%string%)
            GROUP BY ip
            LIMIT 1
            ", $_SERVER['REMOTE_ADDR']);

  		if (!$cfg["reload_hits"]) $cfg["reload_hits"] = 120;
  		if ($ip_hits["hits"] > $cfg["reload_hits"]) die ("Deine IP wird von LanSuite wegen zu häufigen Seitenaufrufen geblockt. Bitte warte ein wenig und versuche es dann erneut.");
    }
	}


	function lock ($module = NULL) {
		global $db;

		$_SESSION["lock_$module"] = true;
        if ($_SERVER['REMOTE_ADDR'] == '::1') return true; // for INET_ATON(IPv6-Localhost) returns sql error
		$db->qry("REPLACE INTO %prefix%ip_locklist SET ip = INET_ATON(%string%), module = %string%", $_SERVER['REMOTE_ADDR'], $module);
	}

	function unlock ($module = NULL) {
		global $db;

		$_SESSION["lock_$module"] = false;
        if ($_SERVER['REMOTE_ADDR'] == '::1') return true; // for INET_ATON(IPv6-Localhost) returns sql error
		$db->qry("DELETE FROM %prefix%ip_locklist WHERE ip = INET_ATON(%string%) AND module = %string%", $_SERVER['REMOTE_ADDR'], $module);
	}

	function locked ($module = NULL, $referrer = '') {
		global $db, $func;

		if ($_SESSION["lock_$module"]) $locked = true;
		else {
			$row = $db->qry_first("SELECT 1 AS found FROM %prefix%ip_locklist WHERE ip = INET_ATON(%string%) AND module = %string% LIMIT 1", $_SERVER['REMOTE_ADDR'], $module);
			if ($row['found']) $locked = true;
			else $locked = false;
		}

		if ($locked) $func->error("NO_REFRESH", $referrer);
		return $locked;
	}
}
?>
