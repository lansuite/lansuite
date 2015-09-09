<?php
/**
 * Created on 17.08.2015
 * @package ts3viewer
 * @author MaLuZ
 */
     if (isset($_GET['autorefresh'])) {
        $autorefresh = $_GET['autorefresh'];
    } else {
        $autorefresh = 0;
    }
    if ($autorefresh != 0) {
        echo("<meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "index.php?mod=teamspeak3&autorefresh=" . $cfg['autorefresh'] . "\">\n");
    }

   
/**
 * Checks if a cache file is existing and if it is current
 * @return boolean Indicates if the cache file is recent or too old. true if recent false if old or nonexistent
 */
function TS3CacheIsFresh($file, $time){
    if (file_exists($file)){
        if (filemtime($file) + $time < time()) {
            //File too old
            return false;
        }
        else {
            //File is current
            return true;
        };  
    } else {
        //File is not existing...yet
        return false;
    };
}

/**
 * Outputs either the cached content if it is quite current or fetches a new page via the TS3 framework and stores it in the cahce file
 */
function TS3PrintContent(){
    $TS3_CACHE_FILE = 'ext_inc/teamspeak3/ts3cache.html';  //file where the output is buffered
    $TS3_CACHE_TIME = 180; //how long should we use that file?
    //check the cache....
    if (TS3CacheIsFresh($TS3_CACHE_FILE, $TS3_CACHE_TIME)) {
        readfile($TS3_CACHE_FILE); //read it from file
    } else {
        echo TS3GenerateCacheFile($TS3_CACHE_FILE); //Generate new and printout
    }    
}

/**
 * Fetches the current server state and both stores it in a cache file and returns it
 * @global array $cfg Global configuration array
 * @param string $cache_file Path to cache file
 * @return string The generated TS server view
 */
function TS3GenerateCacheFile($cache_file){
    global $cfg;
    // Load the Teamspeak3 PHP Framework:
    include_once("ext_inc/teamspeak3/libraries/TeamSpeak3/TeamSpeak3.php");
    //create object
    $TS3 = TeamSpeak3::factory("serverquery://" . /*$settings['serverqueryuser'].':'.$settings['serverquerypassword'].'@'.*/ $cfg['ts3_serveraddress']. ':' . $cfg['ts3_serverqueryport']/* . '/?server_port=' . $settings["serverudpport"]*/);
    $TS3->serverSelectById(1); //select VirtualServer
    $content = $TS3->getViewer(new TeamSpeak3_Viewer_Html("ext_inc/teamspeak3/images/viewer/", "ext_inc/teamspeak3/images/countryflags/", "data:image")); //generate output
    //write back content to file
    $cache_file_handle = fopen($cache_file, 'w');
    fwrite($cache_file_handle, $content);
    fclose($cache_file_handle);
    return $content;
}
/**
 * Print overview page
 * @global type $dsp Display output class
 */
function TS3GenerateOverview(){
    global $dsp; 
    $dsp->NewContent(t('Teamspeak3'), t('Auflistung aller Nutzer auf Virtualserver 1'));
    ob_start();
    TS3PrintContent();
    $dsp->AddContent();
    $dsp->AddSingleRow(ob_get_contents());
    ob_end_clean(); 
    
    
    //   $TS3_server= $TS3->serverGetSelected();
    //    $TS3_tournament_channel = $TS3_server->channelGetByName($settings["tournamentchannel"]);
    //    echo $TS3_tournament_channel->getUniqueId();
}    

TS3GenerateOverview();

?>