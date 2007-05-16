<?php
	if (isset($_GET['autorefresh'])) {
		$autorefresh = $_GET['autorefresh'];
	} else {
		$autorefresh = 0;
	}
	if ($autorefresh == 1) {
		echo("		<meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "?mod=teamspeak2&autorefresh=1\">\n");
	}

	// The code between the 2 lines below turns on PHPs error handlers.
	// Uncomment it for debugging purposes, but leave commented in live
	// environments. Having your script running in a live environment with the
	// error handlers turned on, decreases your sites security as a warning may
	// reveal information used to exploit security holes in your site.
	//================== BEGIN OF ERROR REPORTING CODE ====================
	//echo("<span style=\"color: #dd0000; font-weight: bold\">Error reporting ");
	//echo("is currently on. Turn it off in live environments !</span><br><br>\n");
	//error_reporting(E_ALL);
	//ini_set("display_errors", "1");
	//ini_set("display_startup_errors", "1");
	//ini_set("ignore_repeated_errors", "0");
	//ini_set("ignore_repeated_source", "0");
	//ini_set("report_memleaks", "1");
	//ini_set("track_errors", "1");
	//ini_set("html_errors", "1");
	//ini_set("warn_plus_overloading", "1");
	//================== END OF ERROR REPORTING CODE ======================
	
	// Load the Teamspeak Display:
	require("teamspeakdisplay.php");
	
	// Get the default settings
	$settings = $teamspeakDisplay->getDefaultSettings();
	
	//================== BEGIN OF CONFIGURATION CODE ======================
	
	// Set the teamspeak server IP or Hostname below (DO NOT INCLUDE THE
	// PORT NUMBER):
	// $settings["serveraddress"] = "localhost";
	$settings["serveraddress"] = $cfg['serveraddress'];
	
	// If your you use another port than 8767 to connect to your teamspeak
	// server using a teamspeak client, then uncomment the line below and
	// set the correct teamspeak port:
	// $settings["serverudpport"] = "8767";
	$settings["serverudpport"] = $cfg['serverudpport'];
	
	// If your teamspeak server uses another query port than 51234, then
	// uncomment the line below and set the teamspeak query port of your
	// server (look in the server.ini of your teamspeak server for this
	// portnumber):
	// $settings["serverqueryport"] = "51234";
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
$dsp->AddSingleRow("&nbsp");
?>
<table border="0" width="<? echo $cfg['tabellenbreite'] ?>" bgcolor = "<? echo $cfg['hintergrund'] ?>">
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
<?php } ?>
