<?php
include("modules/beamer/language/beamer_lang_de.php");
include("modules/beamer/show_function.php");
if(isset($_GET["set_beamer"])) {
$_SESSION['beamer']['id'] = $_GET["set_beamer"];
}

// ---- INITALISIERUNG ----

if($_GET["unset_beamer"]=="1") {
$db->query("UPDATE {$config["tables"]["beamer_beamer"]} SET current_view='-1' WHERE bID='{$_SESSION['beamer']['id']}'");
$_SESSION['beamer']['id'] = 0;
$_GET["frame"] = "closewindow";
}

if(!isset($_SESSION['beamer']['id'])) {
$_GET["frame"] = "";
die("Fehler - Kein Beamer gewählt!");
}

// ---- FRAMES ----

if($_GET["frame"]=="") {
// Frameset initalisieren

$sql = "SELECT show_msg FROM {$config["tables"]["beamer_beamer"]} WHERE bID='{$_SESSION['beamer']['id']}'";
$query_id = $db->query($sql);
$beamer = $db->fetch_array($query_id);
?>
<html><head><title>Beamer</title>
<style type="text/css">
  body { margin-top:0px; margin-left:0px; }
</style>
<?php
if($beamer['show_msg']) {
?>
<frameset rows="*,50" border="0">
<frame src="index.php?mod=beamer&action=show&design=base&frame=main" scrolling="no">
<frame src="index.php?mod=beamer&action=show&design=base&frame=usertxt" scrolling="no">
</frameset>
<?php
} else {
?>
<frameset rows="*" border="0">
<frame src="index.php?mod=beamer&action=show&design=base&frame=main" scrolling="no">
</frameset>
<?php
}
?>
</head><body onUnload="ende();"></body></html>
<?php
}

if($_GET["frame"]=="usertxt") {

$sql = "SELECT usertxt FROM {$config["tables"]["beamer_beamer"]} WHERE bID='{$_SESSION['beamer']['id']}'";
$query_id = $db->query($sql);
$beamer = $db->fetch_array($query_id);

$query_id = $db->query("SELECT * FROM {$config["tables"]["beamer_msg"]} WHERE bID='{$_SESSION['beamer']['id']}' AND msgtyp='once' LIMIT 1");
$text = $db->fetch_array($query_id);

if($text['mID']=="") {
// Kein Text vorhanden hole einen Endlostext aus der DB
$query_id = $db->query("SELECT * FROM {$config["tables"]["beamer_msg"]} WHERE bID='{$_SESSION['beamer']['id']}' AND msgtyp='loop' ORDER BY RAND() LIMIT 1");
$text = $db->fetch_array($query_id);

	if($text['mID']=="") {

	 if($beamer['usertxt']==1) {
	  $text['text'] = $lang['beamer']['user_msg'];
	 }
	
	}
} else {
$db->query("INSERT INTO {$config["tables"]["beamer_history"]} (uID, bID, zeit, text) VALUES ('{$text['uID']}', '{$text['bID']}', UNIX_TIMESTAMP(), '{$text['text']}')");
$db->query("DELETE FROM {$config["tables"]["beamer_msg"]} WHERE mID='{$text['mID']}'");
}

$handle 	= fopen ("modules/beamer/templates/msg.htm", "rb");
$temp_main	= fread ($handle, filesize ("modules/beamer/templates/msg.htm"));
fclose ($handle);
$temp_main = str_replace("\"","\\\"", $temp_main);

// Badwordfilter
$sterne = "****************************************************************************************************";

$sql = "SELECT badword FROM {$config["tables"]["beamer_badword"]}";
$query_id = $db->query($sql);
while($bw = $db->fetch_array($query_id)) {
$text['text'] = str_ireplace($bw['badword'], substr($sterne, 0, strlen($bw['badword'])), $text['text']);

}

$temp['text'] = $text['text'];

eval("\$output .= \"" .$temp_main. "\";");		
		
echo $output;
}

if($_GET["frame"]=="main") {
$sql = "SELECT current_view FROM {$config["tables"]["beamer_beamer"]} WHERE bID='{$_SESSION['beamer']['id']}'";
$query_id = $db->query($sql);
$beamer = $db->fetch_array($query_id);

// Aktuelle Anzeige laden
$query_id = $db->query("SELECT sortkey FROM {$config["tables"]["beamer_content"]} WHERE cID='{$beamer['current_view']}'");
$current_view = $db->fetch_array($query_id);

// Nächste Anzeige laden
$sql = "SELECT * FROM {$config["tables"]["beamer_content"]} WHERE sortkey>{$current_view['sortkey']} AND active='1' AND (wiederholungen_soll>wiederholungen_ist OR wiederholungen_soll=-1) ORDER BY sortkey LIMIT 1";
$query_id = $db->query($sql);
$next_view = $db->fetch_array($query_id);

if($next_view['cID']=="") {
// Wenn es kein nächsten Eintrag gibt, dann den ersten auswählen
$sql = "SELECT * FROM {$config["tables"]["beamer_content"]} WHERE active='1' AND (wiederholungen_soll>wiederholungen_ist OR wiederholungen_soll=-1) ORDER BY sortkey LIMIT 1";
$query_id = $db->query($sql);
$next_view = $db->fetch_array($query_id);
}

if($next_view['cID']=="") {
// Wenn es immer noch keinen Eintrag gibt, dann das Bild schwarz schalten und die Standardanzeigedauer setzen
$bgcolor = 'black';
$db->query("UPDATE {$config["tables"]["beamer_beamer"]} SET current_view='0' WHERE bID='{$_SESSION['beamer']['id']}'");
} else {
$db->query("UPDATE {$config["tables"]["beamer_beamer"]} SET current_view='{$next_view['cID']}' WHERE bID='{$_SESSION['beamer']['id']}'");
$db->query("UPDATE {$config["tables"]["beamer_content"]} SET wiederholungen_ist=wiederholungen_ist+1 WHERE cID='{$next_view['cID']}'");
}

// Keine Anzeigedauer angegeben
if($next_view['anzeigedauer']=="") {
$next_view['anzeigedauer'] = "15";
}
?>
<html>
<head>
<title>Beamer</title>
<style type="text/css">
  body { margin-top:0px; margin-left:0px; }
</style>
<meta http-equiv="Page-Enter" content="BlendTrans(Duration=3)">
<meta http-equiv="Page-Exit" content="BlendTrans(Duration=3)">
<meta http-equiv="refresh" content="<?=$next_view['anzeigedauer']?>; url=index.php?mod=beamer&action=show&design=base&frame=main">
</head>
<body bgcolor="<?=$cfg['beamer_bgcolor']?>">
<?php
switch($next_view['content_type']) {
	case "html":
	$handle 	= fopen ("modules/beamer/templates/html.htm", "rb");
	$temp_main	= fread ($handle, filesize ("modules/beamer/templates/html.htm"));
	fclose ($handle);
	$temp_main = str_replace("\"","\\\"", $temp_main);

	$temp['pfad'] = $next_view['pfad'];

	eval("\$output .= \"" .$temp_main. "\";");		
		
	echo $output;
	break;

	case "image":
	$handle 	= fopen ("modules/beamer/templates/image.htm", "rb");
	$temp_main	= fread ($handle, filesize ("modules/beamer/templates/image.htm"));
	fclose ($handle);
	$temp_main = str_replace("\"","\\\"", $temp_main);

	$temp['pfad'] = $next_view['pfad'];

	eval("\$output .= \"" .$temp_main. "\";");		
		
	echo $output;
	break;

	case "audio":
	$handle 	= fopen ("modules/beamer/templates/audio.htm", "rb");
	$temp_main	= fread ($handle, filesize ("modules/beamer/templates/audio.htm"));
	fclose ($handle);
	$temp_main = str_replace("\"","\\\"", $temp_main);

	$temp['pfad'] = $next_view['pfad'];

	eval("\$output .= \"" .$temp_main. "\";");		
		
	echo $output;
	break;

	case "video":
	$handle 	= fopen ("modules/beamer/templates/video.htm", "rb");
	$temp_main	= fread ($handle, filesize ("modules/beamer/templates/video.htm"));
	fclose ($handle);
	$temp_main = str_replace("\"","\\\"", $temp_main);

	$temp['pfad'] = $next_view['pfad'];

	eval("\$output .= \"" .$temp_main. "\";");		
		
	echo $output;
	break;
	
	case "t2_paarung":
	ShowSpielpaarungen();
	break;
}
?>
</body></html>
<?php
}

if($_GET["frame"]=="closewindow") {
?>
<html>
<head>
<title>Beamer</title>
</head><body onLoad="self.close();"></body></html>
<?php
}
?>