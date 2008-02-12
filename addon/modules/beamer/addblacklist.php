<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

if($_GET['userid']=="") {
$mastersearch = new MasterSearch($vars, "index.php?mod=beamer&action=addblacklist", "index.php?mod=beamer&action=addblacklist&userid=", "");
$mastersearch->LoadConfig("users", "Benutzersuche: Suche", "Benutzersuche: Ergebnis");
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();
$templ['index']['info']['content'] .= $mastersearch->GetReturn();
} else {


$res = $db->query("SELECT * FROM {$config["tables"]["beamer_blacklist"]} WHERE uID='{$_GET['userid']}'");
$bl = $db->fetch_array($res);

if($bl['bID']=="") {
$db->query("INSERT INTO {$config["tables"]["beamer_blacklist"]} (uID) VALUES ('{$_GET['userid']}')");
$func->confirmation($lang['beamer']['conf']['blacklist_add'], "?mod=beamer&action=addblacklist");
} else {
$func->error($lang['beamer']['conf']['blacklist_already'], "?mod=beamer&action=addblacklist");
}
}
} else $func->error("ACCESS_DENIED", "");
?>