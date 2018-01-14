<?php

class stats
{
    public $stat_data = array();
    
    // Constructor
    public function __construct()
    {
        global $db, $auth, $cfg;

    // Try not to count search engine bots
    // Bad Examples:
    //   Baiduspider+(+http://www.baidu.jp/spider/)
    //   msnbot/2.0b (+http://search.msn.com/msnbot.htm)
    //   Mozilla/5.0 (compatible; Exabot/3.0; +http://www.e...
    //   Mozilla/5.0 (compatible; Googlebot/2.1; +http://ww...
    // see also http://www.user-agents.org/
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot') === false
        and strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'spider') === false
        and strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'crawl') === false
        and strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'search') === false
        and strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'google') === false
        and strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'find') === false) {
            if ($cfg['log_browser_stats']) {
                $db->qry(
                    'INSERT INTO %prefix%stats_browser SET useragent = %string%, referrer = %string%, accept_language = %string%',
                    $_SERVER['HTTP_USER_AGENT'],
                    $_SERVER['HTTP_REFERER'],
                    $_SERVER['HTTP_ACCEPT_LANGUAGE']
                );
            }

            // Update usage stats
            // Is the user known, or is it a new visit? - After 30min idle this counts as a new visit
              // Existing session -> Only hit
            if ($_SESSION['last_hit'] > (time() - 60 * 30)) {
                $db->qry("INSERT INTO %prefix%stats_usage
          SET visits = 0, hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')
          ON DUPLICATE KEY UPDATE hits = hits + 1;");

          // New session -> Hit and visit
            } else {
                $db->qry("INSERT INTO %prefix%stats_usage
          SET visits = 1, hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')
          ON DUPLICATE KEY UPDATE visits = visits + 1, hits = hits + 1;");
            }
                $_SESSION['last_hit'] = time();
              #setcookie('last_hit', time(), time() + (30 * 60));

              // Beispiel: Suche bei Google nach lansuite.orgapage.de fuehrt zu folgendem Referrer:
              #$_SERVER['HTTP_REFERER'] = "http://www.google.de/search?hl=de&q=lansuite.orgapage.de&btnG=Google-Suche&meta=";

                // Update search engine data
                $search_engine = '';
            if (strpos($_SERVER['HTTP_REFERER'], 'ttp://www.google.') > 0) {
                $search_engine = 'google';
            } elseif (strpos($_SERVER['HTTP_REFERER'], '.yahoo.com/search') > 0) {
                $search_engine = 'yahoo';
            } elseif (strpos($_SERVER['HTTP_REFERER'], '.altavista.com') > 0) {
                $search_engine = 'altavista';
            } elseif (strpos($_SERVER['HTTP_REFERER'], 'ttp://search.msn.') > 0) {
                $search_engine = 'msn';
            } elseif (strpos($_SERVER['HTTP_REFERER'], '.aol.de/suche') > 0) {
                $search_engine = 'aol_de';
            } elseif (strpos($_SERVER['HTTP_REFERER'], 'search.aol.com/') > 0) {
                $search_engine = 'aol_com';
            } elseif (strpos($_SERVER['HTTP_REFERER'], '.web.de/') > 0) {
                $search_engine = 'web_de';
            }

            if ($search_engine != '') {
                $query_var = array(
                "google" => 'q',
                "yahoo" => "p",
                "altavista" => "q",
                "msn" => "q",
                "aol_de" => "q",
                "aol_com" => "query",
                "web_de" => "su"
                    );

                // Read URL parameters into an array
                $url_paras = explode("?", $_SERVER["HTTP_REFERER"]); // URL part behind ? -> $url_paras[1]
                $url_paras = explode("&", $url_paras[1]);

                foreach ($url_paras as $akt_para) {
                        list($para_var, $para_val) = explode("=", $akt_para);

                        // Search for parameter containing the search term
                    if ($para_var == $query_var[$search_engine]) {
                        $row = $db->qry_first_rows("SELECT term FROM %prefix%stats_se WHERE term = %string% AND se = %string%", $para_val, $search_engine);
                        if ($row["number"] > 0) {
                            $db->qry("UPDATE %prefix%stats_se SET hits = hits + 1 WHERE term = %string% AND se = %string%", $para_val, $search_engine);
                        } else {
                            $db->qry("INSERT INTO %prefix%stats_se SET hits = 1, term = %string%, se = %string%, first = NOW()", $para_val, $search_engine);
                        }
                    }
                }
            }
        }
    }
}
