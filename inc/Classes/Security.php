<?php

namespace LanSuite;

class Security
{
    /**
     * @return string
     */
    public function check_blacklist()
    {
        global $db, $database, $cfg;

        // Global blacklist
        if (str_contains($cfg['ip_blacklist'], (string) $_SERVER['REMOTE_ADDR'])) {
            return 'Deine IP wird von LanSuite geblockt. Melde dich bitte bei den Administratoren';
        }

        if ($cfg['reload_limit']) {
            // Reload blacklist
            if (!$cfg['reload_time']) {
                $cfg['reload_time'] = 600;
            }

            $database->query('DELETE FROM %prefix%ip_hits WHERE (date + ?) < NOW()', [$cfg["reload_time"]]);
            $db->qry(
                'INSERT INTO %prefix%ip_hits SET ip = INET6_ATON(%string%)',
                $_SERVER['REMOTE_ADDR'],
                $_GET["mod"],
                $_GET["action"],
                $_GET["step"]
            );

            $ip_hits = $database->queryWithOnlyFirstRow('
              SELECT COUNT(*) AS hits 
              FROM %prefix%ip_hits
              WHERE ip = INET6_ATON(?)
              GROUP BY ip
              LIMIT 1', [$_SERVER['REMOTE_ADDR']]);

            if (!$cfg['reload_hits']) {
                $cfg['reload_hits'] = 120;
            }
            if ($ip_hits['hits'] > $cfg['reload_hits']) {
                return 'Deine IP wird von LanSuite wegen zu häufigen Seitenaufrufen geblockt. Bitte warte ein wenig und versuche es dann erneut.';
            }
        }

        return '';
    }

    /**
     * Locks a single IP address for the given module.
     *
     * @param string $module
     * @return void
     */
    public function lock($module = null)
    {
        global $database;

        $_SESSION["lock_$module"] = true;
        $database->query('REPLACE INTO %prefix%ip_locklist SET ip = INET6_ATON(?), module = ?', [$_SERVER['REMOTE_ADDR'], $module]);
    }

    /**
     * Unlocks a single IP address for the given module.
     *
     * @param string $module
     * @return void
     */
    public function unlock($module = null)
    {
        global $database;

        $_SESSION["lock_$module"] = false;
        $database->query('DELETE FROM %prefix%ip_locklist WHERE ip = INET6_ATON(?) AND module = ?', [$_SERVER['REMOTE_ADDR'], $module]);
    }

    /**
     * Checks if the current requester is locked for given module.
     *
     * @param string    $module
     * @param string    $referrer
     * @return bool
     */
    public function locked($module = null, $referrer = '')
    {
        global $database, $func;

        if ($_SESSION["lock_$module"]) {
            $locked = true;
        } else {
            $row = $database->queryWithOnlyFirstRow('
              SELECT 1 AS found 
              FROM %prefix%ip_locklist 
              WHERE 
                ip = INET6_ATON(?) 
                AND module = ? LIMIT 1', [$_SERVER['REMOTE_ADDR'], $module]);

            if ($row) {
                $locked = true;
            } else {
                $locked = false;
            }
        }

        if ($locked) {
            $func->error('NO_REFRESH', $referrer);
        }

        return $locked;
    }
}
