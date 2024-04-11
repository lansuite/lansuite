<?php

use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;

use PlanetTeamSpeak\TeamSpeak3Framework\Viewer;

/**
 * Fetches the current server state and both stores it in a cache file and returns it.
 *
 * @param string $cache_file
 * @return string
 */
function TS3GenerateCacheFile($cache_file)
{
    global $cfg;

    $TS3 = TeamSpeak3::factory("serverquery://" . $cfg['serverqueryuser'].':'.$cfg['serverquerypassword'].'@'. $cfg['ts3_serveraddress']. ':' . $cfg['ts3_serverqueryport']/* . '/?server_port=' . $settings["serverudpport"]*/);
    $TS3->serverSelectById(1);
    $content = $TS3->getViewer(new TeamSpeak3_Viewer_Html("vendor/planetteamspeak/ts3-php-framework/images/viewer", "vendor/planetteamspeak/ts3-php-framework/images/flags", "data:image"));

    // Write back content to file
    $cache_file_handle = fopen($cache_file, 'w');
    fwrite($cache_file_handle, $content);
    fclose($cache_file_handle);

    return $content;
}
