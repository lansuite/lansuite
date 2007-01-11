<?php
class tss2info {


// **** SETTINGS - to be edited before first use ****
var $sitetitle = "LanSuite - TeamSpeak"; // SeitenTitle und Scriptversion
var $serverAddress = "localhost"; // Hier die TeamSpeak IP Adresse eintragen !!wichtig!! (Beispiel: 192.168.7.1)
var $serverQueryPort = "51234"; // TeamSpeak QueryPort.. Schau in die server.ini von TeamSpeak (Standard 51234)
var $serverUDPPort = "8767"; // UDP Port für Teamspeak der auch hinter der IP Adresse genutzt wird (Standard 8767)
var $tabellenbreite = "150"; // Mindestbreite der Teamspeaktabelle (die einbindung mit einem IFRAME sollte 20px mehr betragen)
var $refreshtime = "10"; // Zeit in Sekunden nach der die Anzeige aktualisiert wird, wenn "auto on" aktiv ist
var $serverPasswort = ""; // Serverpasswort das bei Serversettings eingestellt wird (wenn kein Passwort erteilt, dann leer lassen)
// (Passwort wird meistens bei Clanservern gebraucht)
// **** end of SETTINGS ****

/* Ab hier darf >>> KEIN <<< Text mehr geändert werden */

//internal
var $socket;

// external
var $serverStatus = "offline";
var $playerList = array();
var $channelList = array();

// Constructor
function tss2info() {
	global $cfg;

	if ($cfg["teamspeak_sitetitle"]) $this->sitetitle = $cfg["teamspeak_sitetitle"];
	if ($cfg["teamspeak_serverAddress"]) $this->serverAddress = $cfg["teamspeak_serverAddress"];
	if ($cfg["teamspeak_serverQueryPort"]) $this->serverQueryPort = $cfg["teamspeak_serverQueryPort"];
	if ($cfg["teamspeak_serverUDPPort"]) $this->serverUDPPort = $cfg["teamspeak_serverUDPPort"];
	if ($cfg["teamspeak_tabellenbreite"]) $this->tabellenbreite = $cfg["teamspeak_tabellenbreite"];
	if ($cfg["teamspeak_refreshtime"]) $this->refreshtime = $cfg["teamspeak_refreshtime"];
	if ($cfg["teamspeak_serverPasswort"]) $this->serverPasswort = $cfg["teamspeak_serverPasswort"];
}


// opens a connection to the teamspeak server
function getSocket($host, $port, $errno, $errstr, $timeout) {
  unset($socket);
  $attempts = 1;
  while($attempts <= "1" and !$this->socket) {
        $attempts++;
    @$socket = fsockopen($host, $port, $errno, $errstr, $timeout);
    $this->errno = $errno;
    $this->errstr = $errstr;
    if($socket and fread($socket, 4) == "[TS]") {
      fgets($socket, 128);
      return $socket;
        }
  }// end while
  return false;
}// end function getSocket(...)

// sends a query to the teamspeak server
function sendQuery($socket, $query) {
  fputs($socket, $query."\n");
}// end function sendQuery(...)

// answer OK?
function getOK($socket) {
  $result = fread($socket, 2);
  fgets($socket, 128);
  return($result == "OK");
}// end function getOK(...)

// closes the connection to the teamspeak server
function closeSocket($socket) {
  fputs($socket, "quit");
  fclose($socket);
}// end function closeSocket(...)

// retrieves the next argument in a tabulator-separated string (PHP scanf function bug workaround)
function getNext($evalString) {
  $pos = strpos($evalString, "\t");
  if(is_integer($pos)) {
    return substr($evalString, 0, $pos);
  } else {
    return $evalString;
  }// end if
}// end function getNext($evalString);

// removes the first argument in a tabulator-separated string (PHP scanf function bug workaround)
function chopNext($evalString) {
  $pos = strpos($evalString, "\t");
  if(is_integer($pos)) {
    return substr($evalString, $pos + 1);
  } else {
    return "";
  }// end if
}// end function chopNext($evalString)

// strips the quotes around a string
function stripQuotes($evalString) {
  if(strpos($evalString, '"') == 0) $evalString = substr($evalString, 1, strlen($evalString) - 1);
  if(strrpos($evalString, '"') == strlen($evalString) - 1) $evalString = substr($evalString, 0, strlen($evalString) - 1);

  return $evalString;
}// end function stripQuotes($evalString)

// returns the codec name
function getVerboseCodec($codec) {
  if($codec == 0) {
    $codec = "CELP 5.1 Kbit";
  } elseif($codec == 1) {
    $codec = "CELP 6.3 Kbit";
  } elseif($codec == 2) {
    $codec = "GSM 14.8 Kbit";
  } elseif($codec == 3) {
    $codec = "GSM 16.4 Kbit";
  } elseif($codec == 4) {
    $codec = "CELP Windows 5.2 Kbit";
  } elseif($codec == 5) {
    $codec = "Speex 3.4 Kbit";
  } elseif($codec == 6) {
    $codec = "Speex 5.2 Kbit";
  } elseif($codec == 7) {
    $codec = "Speex 7.2 Kbit";
  } elseif($codec == 8) {
    $codec = "Speex 9.3 Kbit";
  } elseif($codec == 9) {
    $codec = "Speex 12.3 Kbit";
  } elseif($codec == 10) {
    $codec = "Speex 16.3 Kbit";
  } elseif($codec == 11) {
    $codec = "Speex 19.5 Kbit";
  } elseif($codec == 12) {
    $codec = "Speex 25.9 Kbit";
  } else {
    $codec = "unknown (".$codec.")";
  }// end if
  return $codec;
}// end function getVerboseCodec($codec);

function getInfo() {
// ---=== main program ===---

// establish connection to teamspeak server
$this->socket = $this->getSocket($this->serverAddress, $this->serverQueryPort, "", "", 0.3);
if($this->socket == false) {
  return;
  echo ("No Server");
} else {
  $this->serverStatus = "online";

  // select the one and only running server on port 8767
  $this->sendQuery($this->socket, "sel ".$this->serverUDPPort);

  // retrieve answer "OK"
  if(!$this->getOK($this->socket)) {
    echo "Server didn't answer \"OK\" after last command. Aborting.";
    return;
  }// end if

  // retrieve player list
  $this->sendQuery($this->socket,"pl");

  // read player info
  $this->playerList = array();
  do {
    $playerinfo = fscanf($this->socket, "%s %d %d %d %d %d %d %d %d %d %d %d %d %s %s");
    list($playerid, $channelid, $receivedpackets, $receivedbytes, $sentpackets, $sentbytes, $paketlost, $pingtime, $totaltime, $idletime, $privileg, $userstatus, $attribute, $s, $playername) = $playerinfo;
    if($playerid != "OK") {
      $this->playerList[$playerid] = array("playerid" => $playerid,
      "channelid" => $channelid,
      "receivedpackets" => $receivedpackets,
      "receivedbytes" => $receivedbytes,
      "sentpackets" => $sentpackets,
      "sentbytes" => $sentbytes,
      "paketlost" => $paketlost / 100,
      "pingtime" => $pingtime,
      "totaltime" => $totaltime,
      "idletime" => $idletime,
      "privileg" => $privileg,
      "userstatus" => $userstatus,
      "attribute" => $attribute,
      "s" => $s,
      "playername" => $this->stripQuotes($playername));
    }// end if
  } while($playerid != "OK");

  // retrieve channel list
  $this->sendQuery($this->socket,"cl");

  // read channel info
  $this->channelList = array();
  do {
    $channelinfo = "";
    do {
      $input = fread($this->socket, 1);
      if($input != "\n" && $input != "\r") $channelinfo .= $input;
    } while($input != "\n");

    $channelid = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $codec = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $parent = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $d = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $maxplayers = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $channelname = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $d = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $d = $this->getNext($channelinfo);
    $channelinfo = $this->chopNext($channelinfo);
    $topic = $this->getNext($channelinfo);

    if($channelid != "OK") {
      //if($isdefault == "Default") $isdefault = "1"; else $isdefault = "0";
      
          // determine number of players in channel
      $playercount = 0;
      foreach($this->playerList as $playerInfo) {
        if($playerInfo['channelid'] == $channelid) $playercount++;
      }// end foreach

      $this->channelList[$channelid] = array("channelid" => $channelid,
      "codec" => $codec,
      "parent" => $parent,
      "maxplayers" => $maxplayers,
      "channelname" => $this->stripQuotes($channelname),
      "isdefault" => $isdefault,
      "topic" => $this->stripQuotes($topic),
      "currentplayers" => $playercount);
    }// end if
  } while($channelid != "OK");

  // close connection to teamspeak server
  $this->closeSocket($this->socket);

  }// end getInfo()
}// class tss2info
}
$tss2info = new tss2info;
?>
