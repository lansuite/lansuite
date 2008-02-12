<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

if($_GET['userid']=="") {
$mastersearch = new MasterSearch($vars, "index.php?mod=beamer&action=delblacklist", "index.php?mod=beamer&action=delblacklist&userid=", "");
$mastersearch->LoadConfig("beamer_blacklist", "Benutzersuche: Suche", "Benutzersuche: Ergebnis");
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();
$templ['index']['info']['content'] .= $mastersearch->GetReturn();
} else {


$res = $db->query("SELECT * FROM {$config["tables"]["beamer_blacklist"]} WHERE uID='{$_GET['userid']}'");
$bl = $db->fetch_array($res);

if($bl['bID']=="") {

$func->error($lang['beamer']['conf']['blacklist_no'], "?mod=beamer&action=addblacklist");



} else {

$db->query("DELETE FROM {$config["tables"]["beamer_blacklist"]} WHERE uID='{$_GET['userid']}'");
$func->confirmation($lang['beamer']['conf']['blacklist_del'], "?mod=beamer&action=delblacklist");

}
}
} else $func->error("ACCESS_DENIED", "");
?>