﻿<?php
/*
 * Created on 12.02.2009
 * 
 * TeamSpeak2
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
        echo("      <meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "?mod=teamspeak2&autorefresh=1\">\n");
    }

     // Load the Teamspeak Display:
    include_once("teamspeakdisplay.php");

	// Create an instance of the Teamspeak Display Class
	$teamspeakDisplay = new teamspeakDisplayClass;
    
    // Get the default settings
    $settings = $teamspeakDisplay->getDefaultSettings();
    
    //================== BEGIN OF CONFIGURATION CODE ======================
    $settings["serveraddress"] = $cfg['serveraddress'];
    $settings["serverudpport"] = $cfg['serverudpport'];
    $settings["serverqueryport"] = $cfg['serverqueryport'];
    
    // If you want to limit the display to only one channel including it's
    // players and subchannels, uncomment the following line and set the
    // exact name of the channel. This feature is case-sensitive!
    //$settings["limitchannel"] = "";
    
    // If your teamspeak server uses another set of forbidden nickname
    // characters than "()[]{}" (look in your server.ini for this setting),
    // then uncomment the following line and set the correct set of
    // forbidden nickname characters:
    //$settings["forbiddennicknamechars"] = "()[]{}";
    
    //================== END OF CONFIGURATION CODE ========================
    // Is the script improperly configured?
  if ($settings["serveraddress"] == "*.*.*.*") { $func->information(t('Kein Teamspeak Server konfiguriert.')); }
else {
$dsp->NewContent("TeamSpeak2", t('Übersicht der Channels und User auf dem TeamSpeak Server. Um auf den Server zu connecten, einfach auf einen Channel klicken und den Anweisungen folgen.'));
ob_start();
?>
<table border="0" width="<?php echo $cfg['tabellenbreite'] ?>" bgcolor = "<?php echo $cfg['hintergrund'] ?>">
 <tr>
  <td>
<?php
    // Display the Teamspeak server
    $teamspeakDisplay->displayTeamspeakEx($settings);
            echo("<br>");
    // Display autorefresh status and control link:
    if ($autorefresh == 0) {
    echo("<img src=\"ext_inc/teamspeak2/refresh_off.gif\"> <b>Autorefresh:</b> <font color=red><b>". t('AUS') ."</b></font> (<a href=\"" . $_SERVER["PHP_SELF"] . "?mod=teamspeak2&autorefresh=1\">". t('Aktivieren') ."</a>)<br>\n");
    } else if ($autorefresh == 1) {
        echo("<img src=\"ext_inc/teamspeak2/refresh_on.gif\"> <b>Autorefresh:</b> <font color=green><b>". t('AN') ."</b></font> (<a href=\"" . $_SERVER["PHP_SELF"] . "?mod=teamspeak2&autorefresh=0\">". t('Deaktivieren') ."</a>)<br>\n");
    }
?>
  </td>
 </tr>
</table>
<?php
  $dsp->AddSingleRow(ob_get_contents());
  ob_end_clean();
}
?>
?>
