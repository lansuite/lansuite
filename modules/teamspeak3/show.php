<?php
use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;
use PlanetTeamSpeak\TeamSpeak3Framework\Viewer\Html;
/*
if (isset($_GET['autorefresh'])) {
    $autorefresh = $_GET['autorefresh'];
} else {
    $autorefresh = 0;
}

if ($autorefresh != 0) {
    echo("<meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "index.php?mod=teamspeak3&autorefresh=" . $cfg['autorefresh'] . "\">\n");
}
*/


$ts3Server = New \LanSuite\Module\TeamSpeak3\TS3Server();

$queryurl =  $ts3Server->getQueryURL();
// connect to local server, authenticate and spawn an object for the virtual server on port 9987
$TS3PHPFramework = new TeamSpeak3();

$ts3_VirtualServer = $TS3PHPFramework->factory($queryurl);
// build and display HTML treeview using custom image paths (remote icons will be embedded using data URI sheme)
  ob_start();
echo $ts3_VirtualServer->getViewer(new Html("images/viewericons/", "images/countryflags/", "data:image"));
    $dsp->AddContent();
    $dsp->AddSingleRow(ob_get_contents());
    ob_end_clean();