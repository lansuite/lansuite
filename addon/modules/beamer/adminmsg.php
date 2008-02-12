<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

if($_GET['mode']=="send") {

if($_POST["usermsg"]=="") {
$error_usermsg = "Es muss eine Nachricht angegeben werden!";
$_GET['mode'] = "";

} else {

  $sql = "SELECT bID, usertxt FROM {$config["tables"]["beamer_beamer"]} WHERE bID='{$_POST['beamer']}'";
  $query_id = $db->query($sql);
  $beamer = $db->fetch_array($query_id);


    $db->query("INSERT INTO {$config["tables"]["beamer_msg"]} (uID, bID, zeit, text, msgtyp) VALUES ('{$_SESSION['auth']['userid']}', '{$beamer['bID']}', UNIX_TIMESTAMP(), '{$_POST['usermsg']}', '{$_POST['msgtyp']}')");
    $func->confirmation($lang['beamer']['conf']['usermsg'], "?mod=beamer&action=adminmsg");
}

}

if($_GET['mode']=="") {

$res = $db->query("SELECT bID, bezeichnung FROM {$config["tables"]["beamer_beamer"]} ORDER BY bezeichnung");
while($beamer = $db->fetch_array($res)) {
$inhalt_b[] = '<option value="'.$beamer['bID'].'">'.$beamer['bezeichnung'].'</option>';
}
$inhalt_t[] = '<option value="loop">Endlosschleife</option>';
$inhalt_t[] = '<option value="once">Einmalig (Wird automatisch gelöscht)</option>';

$count = $db->num_rows($res);

if($count>0) {
$dsp->NewContent($lang['beamer']['usertxt'], $lang['beamer']['sub']['usertxt']);
$dsp->SetForm("index.php?mod=beamer&action=adminmsg&mode=send");
$dsp->AddDropDownFieldRow("beamer", $lang['beamer']['beamer'], $inhalt_b, '', 0);
$dsp->AddTextFieldRow("usermsg", $lang['beamer']['usermsg'], "", $error_usermsg);
$dsp->AddDropDownFieldRow("msgtyp", $lang['beamer']['msgtyp'], $inhalt_t, '', 0);
$dsp->AddFormSubmitRow('send');
$dsp->AddContent();
} else {
$func->error($lang['beamer']['error']['n_a'], "");
}
}

} else $func->error("ACCESS_DENIED", "");
?>