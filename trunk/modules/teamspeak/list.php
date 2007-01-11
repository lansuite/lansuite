<?
require("TS_config.php");
$tss2info->getInfo();
$tss2info->userName="Guest";

$dsp->NewContent("TeamSpeak", "");

$out = "";
// display channel list
$out .= "<table border=\"0\" width=\"".$tss2info->tabellenbreite."\" cellpadding=\"0\" cellspacing=\"0\">\n";
$out .= "<tr>\n";
$out .= "<td>\n";
//-------------------------------------------------------------------------------------------------
//---> Refresh <---\\ Anfang
$out .= "<table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
$out .= "<tr>\n";
$out .= "<td width=\"16\"><img src=\"ext_inc/teamspeak/refresh.gif\" width=\"16\"height=\"16\" border=\"0\" alt=\"\"></td><td class=\"refresh\"><a class=\"refreshlink\" href=\"index.php?mod=teamspeak\" target=\"_self\">refresh</a></td></tr>\n";
$out .= "</tr>\n";
$out .= "</table>\n";
//---> Refresh <---\\ Ende
//-------------------------------------------------------------------------------------------------
$out .= "</td>\n";
$out .= "</tr>\n";
$out .= "<tr>\n";
$out .= "<td>\n";
//-------------------------------------------------------------------------------------------------
//---> TeamSpeak <---\\ Anfang
$out .= "<table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
$out .= "<tr>\n";
$out .= "<td width=\"16\"><img src=\"ext_inc/teamspeak/teamspeak.gif\" width=\"16\"height=\"16\" border=\"0\" alt=\"\"></td><td class=\"teamspeak\">&nbsp;TeamSpeak</td>\n";
$out .= "</tr>\n";
$out .= "</table>\n";
//---> TeamSpeak <---\\ Ende
//-------------------------------------------------------------------------------------------------
$out .= "</td>\n";
$out .= "</tr>\n";
$counter = 0;
foreach($tss2info->channelList as $channelInfo) {
  $channelname = $channelInfo['channelname'];
  // determine codec (verbose)
  $codec = $tss2info->getVerboseCodec($channelInfo['codec']);
  // default?
  //if($channelInfo['isdefault'] == "1")  $isDefault = "yes"; else $isDefault = "no";
  if ($channelInfo['channelid'] != "id") {
    $out .= ("<tr>\n");
    $out .= ("<td>\n");
//-------------------------------------------------------------------------------------------------
    //---> Channel <---\\ Anfang
    $out .= (" <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n");
    $out .= ("   <tr>\n");
    $out .= ("    <td width=\"32\"><img width=\"16\"height=\"16\" src=\"ext_inc/teamspeak/gitter2.gif\" border=\"0\" alt=\"\"><img src=\"ext_inc/teamspeak/channel.gif\" width=\"16\"height=\"16\" border=\"0\" alt=\"\"></td><td class=\"channel\">&nbsp;<a class=\"channellink\" href=\"teamspeak://".$tss2info->serverAddress.":".$tss2info->serverUDPPort."/?channel=".$channelname."?password=".$tss2info->serverPasswort."\" title=\"".$channelInfo['topic']."\">".utf8_encode($channelname)."</a> (".utf8_encode($channelInfo['currentplayers'])."/".$channelInfo['maxplayers'].")</td>\n");
    $out .= ("  </tr>\n");
    $out .= (" </table>\n");
    //---> Channel <---\\ Ende
//-------------------------------------------------------------------------------------------------
    $out .= ("</td>\n");
    $out .= ("</tr>\n");
    $counter_player = 0;
    foreach($tss2info->playerList as $playerInfo) {
          if ($playerInfo['channelid'] == $channelInfo['channelid']) {
//-------------------------------------------------------------------------------------------------
//--- UserStatusBild --\\
if ($playerInfo['attribute'] == "0") $playergif = "player.gif";
if (($playerInfo['attribute'] == "8") or
    ($playerInfo['attribute'] == "9") or
    ($playerInfo['attribute'] == "12") or
    ($playerInfo['attribute'] == "13") or
    ($playerInfo['attribute'] == "24") or
    ($playerInfo['attribute'] == "25") or
    ($playerInfo['attribute'] == "28") or
    ($playerInfo['attribute'] == "29") or
    ($playerInfo['attribute'] == "40") or
    ($playerInfo['attribute'] == "41") or
    ($playerInfo['attribute'] == "44") or
    ($playerInfo['attribute'] == "45") or
    ($playerInfo['attribute'] == "56") or
    ($playerInfo['attribute'] == "57")) $playergif = "away.gif";
if (($playerInfo['attribute'] == "16") or
    ($playerInfo['attribute'] == "17") or
    ($playerInfo['attribute'] == "20") or
    ($playerInfo['attribute'] == "21")) $playergif = "mutemicro.gif";
if (($playerInfo['attribute'] == "32") or
    ($playerInfo['attribute'] == "33") or
    ($playerInfo['attribute'] == "36") or
    ($playerInfo['attribute'] == "37") or
    ($playerInfo['attribute'] == "48") or
    ($playerInfo['attribute'] == "49") or
    ($playerInfo['attribute'] == "52") or
    ($playerInfo['attribute'] == "53")) $playergif = "mutespeakers.gif";
if ($playerInfo['attribute'] == "4") $playergif = "player.gif";
if (($playerInfo['attribute'] == "1") or
    ($playerInfo['attribute'] == "5")) $playergif = "channelcommander.gif";
//--- UserStatusBild --\\
//-------------------------------------------------------------------------------------------------
//--- UserRechte ---\\
if ($playerInfo['userstatus'] < "4") { $playerstatus = "U"; } // Unregistriert

if ($playerInfo['userstatus'] == "4") { $playerstatus = "R"; } // Registriert

if ($playerInfo['userstatus'] == "5") { $playerstatus = "R SA"; } // Serveradmin
//--- UserRechte ---\\
//-------------------------------------------------------------------------------------------------
//--- Privilegien ---\\
if ($playerInfo['privileg'] == "0") { $privileg = ""; } // nix
if ($playerInfo['privileg'] == "1") { $privileg = " CA"; } // Channeladmin
//--- Privilegien ---\\
//-------------------------------------------------------------------------------------------------
//--- Online Seit ---\\
if ($playerInfo['totaltime'] < 60 ) {
 $playertotaltime = strftime("%S Sekunden", $playerInfo['totaltime']);
} else {
 if ($playerInfo['totaltime'] >= 3600 ) {
  $playertotaltime = strftime("%H:%M:%S Stunden", $playerInfo['totaltime'] - 3600);
 } else {
   $playertotaltime = strftime("%M:%S Minuten", $playerInfo['totaltime']);
 }
}
//--- Online Seit ---\\
//-------------------------------------------------------------------------------------------------
//--- Idle Seit ---\\
if ($playerInfo['idletime'] < 60 ) {
 $playeridletime = strftime("%S Sekunden", $playerInfo['idletime']);
} else {
 if ($playerInfo['idletime'] >= 3600 ) {
  $playeridletime = strftime("%H:%M:%S Stunden", $playerInfo['idletime'] - 3600);
 } else {
   $playeridletime = strftime("%M:%S Minuten", $playerInfo['idletime']);
 }
}
//--- Idle Seit ---\\
//-------------------------------------------------------------------------------------------------
    //---> Player <---\\ Anfang
            $out .= ("<tr>\n");
            $out .= ("<td>\n");
            $out .= (" <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n");
            $out .= ("   <tr><td width=\"48\"><img src=\"ext_inc/teamspeak/gitter.gif\" width=\"16\"height=\"16\" border=\"0\" alt=\"\"><img src=\"ext_inc/teamspeak/gitter2.gif\" width=\"16\"height=\"16\" border=\"0\" alt=\"\"><img src=\"ext_inc/teamspeak/".$playergif."\" width=\"16\"height=\"16\" border=\"0\" alt=\"Time [online:".$playertotaltime." | idle:".$playeridletime."] Ping:".$playerInfo['pingtime']."ms\"></td><td class=\"player\" title=\"Time [online:".$playertotaltime." | idle:".$playeridletime."] Ping:".$playerInfo['pingtime']."ms\">&nbsp;".$playerInfo['playername']." (".$playerstatus."".$privileg.")</td></tr>\n");
            $out .= (" </table>\n");
            $out .= ("</td>\n");
            $out .= ("</tr>\n");
    //---> Player <---\\ Ende
//-------------------------------------------------------------------------------------------------
                $counter_player++;
          }
    }
  }
  $counter++;
}// end foreach
//-------------------------------------------------------------------------------------------------
//---> OFFLINE <---\\
if ($counter == 0) {
 $out .= ("<tr>\n");
 $out .= ("<td>\n");
 $out .= (" <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n");
 $out .= ("   <tr><td class=\"offline\" width=\"110\"align=\"center\" colspan=\"2\"><b>Offline</b></td></tr>\n");
 $out .= (" </table>\n");
 $out .= ("</td>\n");
 $out .= ("</tr>\n");
}
//---> OFFLINE <---\\
//-------------------------------------------------------------------------------------------------
$out .= ("<tr>\n");
$out .= ("<td class=\"player\">\n<br>");
#$out .= ($tss2info->sitetitle."<br>\n");
$out .= ("</td>\n");
$out .= ("</tr>\n");
$out .= ("</table>\n");

$dsp->AddSingleRow($out);

$dsp->AddContent();
?>