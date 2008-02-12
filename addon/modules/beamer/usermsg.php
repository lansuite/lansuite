<?php
if($_GET['mode']=="send") {

if($_POST["usermsg"]=="") {
$error_usermsg = "Es muss eine Nachricht angegeben werden!";
$_GET['mode'] = "";

} else {

  $sql = "SELECT uID FROM {$config["tables"]["beamer_blacklist"]} WHERE uID='{$_SESSION['auth']['userid']}'";
  $query_id = $db->query($sql);
  $blacklist = $db->fetch_array($query_id);
  if($blacklist['uID']=="") {
  $sql = "SELECT bID, usertxt FROM {$config["tables"]["beamer_beamer"]} WHERE bID='{$_POST['beamer']}'";
  $query_id = $db->query($sql);
  $beamer = $db->fetch_array($query_id);

  if($beamer['usertxt']) {
    $db->query("INSERT INTO {$config["tables"]["beamer_msg"]} (uID, bID, zeit, text, msgtyp) VALUES ('{$_SESSION['auth']['userid']}', '{$beamer['bID']}', UNIX_TIMESTAMP(), '{$_POST['usermsg']}', 'once')");
    $func->confirmation($lang['beamer']['conf']['usermsg'], "?mod=beamer&action=usermsg");
  } else {
    $func->error($lang['beamer']['error']['not_active'], "?mod=beamer&action=usermsg");
  }

  } else {
  // Blacklist
    $func->error($lang['beamer']['error']['blacklist'], "?mod=beamer&action=usermsg");
  }


}

}

if($_GET['mode']=="") {

$res = $db->query("SELECT bID, bezeichnung FROM {$config["tables"]["beamer_beamer"]} WHERE usertxt='1' ORDER BY bezeichnung");
while($beamer = $db->fetch_array($res)) {
$inhalt_b[] = '<option value="'.$beamer['bID'].'">'.$beamer['bezeichnung'].'</option>';
}

$count = $db->num_rows($res);

if($count>0) {
$dsp->NewContent($lang['beamer']['usertxt'], $lang['beamer']['sub']['usertxt']);
$dsp->SetForm("index.php?mod=beamer&action=usermsg&mode=send");
$dsp->AddDropDownFieldRow("beamer", $lang['beamer']['beamer'], $inhalt_b, '', 0);
$dsp->AddTextFieldRow("usermsg", $lang['beamer']['usermsg'], "", $error_usermsg);
$dsp->AddFormSubmitRow('send');
$dsp->AddContent();
} else {
$func->error($lang['beamer']['error']['n_a'], "");
}
}


?>