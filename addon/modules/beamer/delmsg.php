<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

if($_GET['msg']=="") {
$mastersearch = new MasterSearch($vars, "index.php?mod=beamer&action=delmsg", "index.php?mod=beamer&action=delmsg&msg=", "");
$mastersearch->LoadConfig("beamer_usermsg", "User-MSG: Suche", "User-MSG: Ergebnis");
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();
$templ['index']['info']['content'] .= $mastersearch->GetReturn();
} else {
$db->query("DELETE FROM {$config["tables"]["beamer_msg"]} WHERE mID='{$_GET['msg']}'");
$func->confirmation($lang['beamer']['conf']['usermsg_del'], "?mod=beamer&action=delmsg");
}

} else $func->error("ACCESS_DENIED", "");
?>