<?php
/*
 * Created on 03.03.2009
 * 
 * 
 * 
 * @package package_name
 * @author Maztah
 * 
 */
     if (isset($_GET['autorefresh'])) {
        $autorefresh = $_GET['autorefresh'];
    } else {
        $autorefresh = 0;
    }
    if ($autorefresh == 1) {
        echo("      <meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "index.php?mod=teamspeak3&autorefresh=1\">\n");
    }

    function LoadTS3Config (){
    global $cfg;
    $settings = array();
    $settings["serveraddress"] = $cfg['ts3_serveraddress'];
    $settings["serverqueryport"] = $cfg['ts3_serverqueryport'];
    $settings["serverqueryuser"] = $cfg['ts3_serverqueryuser'];
    $settings["serverquerypassword"] = $cfg['ts3_serverquerypassword'];
    $settings["serverudpport"] = $cfg['ts3_serverudpport'];
    $settings["serverpassword"] = $cfg['ts3_serverpassword'];
//    $settings["tournamentchannel"] = $cfg['ts3_tournamentchannel'];
return $settings;    
}
    
     // Load the Teamspeak3 PHP Framework:
    include_once("ext_inc/teamspeak3/libraries/Teamspeak3/TeamSpeak3.php");
    //Load configuration settings
    $settings = LoadTS3Config();
    // Create an instance of the Teamspeak Display Class
    $TS3 = TeamSpeak3::factory("serverquery://" . /*$settings['serverqueryuser'].':'.$settings['serverquerypassword'].'@'.*/ $settings["serveraddress"]. ':' . $settings["serverqueryport"]/* . '/?server_port=' . $settings["serverudpport"]*/);
    $TS3->serverSelectById(1);
    $dsp->NewContent(t('Teamspeak3'), t('Auflistung aller Nutzer auf Virtualserver 1'));
    ob_start();
    echo $TS3->getViewer(new TeamSpeak3_Viewer_Html("ext_inc/teamspeak3/images/viewer/", "ext_inc/teamspeak3/images/countryflags/", "data:image"));
    $TS3_server= $TS3->serverGetSelected();
//    $TS3_tournament_channel = $TS3_server->channelGetByName($settings["tournamentchannel"]);
//    echo $TS3_tournament_channel->getUniqueId();
    $dsp->AddContent();
    $dsp->AddSingleRow(ob_get_contents());
    ob_end_clean();
?>