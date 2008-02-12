<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

if($_GET["inhalt"]=="") {
$mastersearch = new MasterSearch($vars, "index.php?mod=beamer", "index.php?mod=beamer&action=inhalt&inhalt=", "");
$mastersearch->LoadConfig("beamer_content", "Beamerinhalt: Suche", "Beamerinhalt: Ergebnis");
$mastersearch->PrintForm();
$mastersearch->Search();
$mastersearch->PrintResult();
$templ['index']['info']['content'] .= $mastersearch->GetReturn();
} else {

if($_GET["mode"]=="save_edit") {
// Speichern
if($_GET["inhalt"]!="new") {
$db->query("UPDATE {$config["tables"]["beamer_content"]} SET caption='{$_POST["c_caption"]}', content_type='{$_POST["content_type"]}', pfad='{$_POST["c_pfad"]}', anzeigedauer='{$_POST["c_anzeigedauer"]}', wiederholungen_soll='{$_POST["c_wiederholungen_soll"]}', active='{$_POST["c_active"]}' WHERE cID='{$_GET['inhalt']}'");
$func->confirmation("Die Änderungen wurden übernommen", "index.php?mod=beamer&action=inhalt&inhalt=".$_GET['inhalt']);
} else {
$res = $db->query("SELECT sortkey FROM {$config["tables"]["beamer_content"]} WHERE beamerID='{$_POST["bID"]}' ORDER BY sortkey DESC LIMIT 1");
$last_entry = $db->fetch_array($res);

$sortkey = $last_entry['sortkey'] + 1;
$db->query("INSERT INTO {$config["tables"]["beamer_content"]} (caption, content_type, pfad, anzeigedauer, wiederholungen_soll, active, beamerID, uID, sortkey) VALUES ('{$_POST["c_caption"]}', '{$_POST["content_type"]}', '{$_POST["c_pfad"]}', '{$_POST["c_anzeigedauer"]}', '{$_POST["c_wiederholungen_soll"]}', '{$_POST["c_active"]}', '{$_POST["bID"]}', '{$_SESSION['auth']['userid']}', '$sortkey')");
$func->confirmation("Der neue Inhalt wurde angelegt", "index.php?mod=beamer&action=inhalt&inhalt=".$db->insert_id());
}

} else {
// Editieren
$res = $db->query("SELECT * FROM {$config["tables"]["beamer_content"]} WHERE cID='{$_GET['inhalt']}'");
$cnt = $db->fetch_array($res);
$dsp->NewContent($lang["beamer"]["beamer"]." / ".$lang['beamer']['sub']['inhalt'], $lang['beamer']['descr']['inhalt']);
$dsp->SetForm("index.php?mod=beamer&action=inhalt&mode=save_edit&inhalt=".$_GET['inhalt']);

if($_GET["inhalt"]=="new") {

$res = $db->query("SELECT bID, bezeichnung FROM {$config["tables"]["beamer_beamer"]}");
while($beamer = $db->fetch_array($res)) {
$inhalt_b[] = '<option value="'.$beamer['bID'].'">'.$beamer['bezeichnung'].'</option>';
}

$dsp->AddDropDownFieldRow("bID", "Beamer:", $inhalt_b, '', 0);

}

$dsp->AddTextFieldRow("c_caption", "Bezeichnung:", $cnt['caption'],NULL,NULL,0);

$sel[$cnt['content_type']] = "selected";

$inhalt[] = '<option value="html" '.$sel['html'].'>HTML-Seite</option>';
$inhalt[] = '<option value="video" '.$sel['video'].'>Video</option>';
$inhalt[] = '<option value="audio" '.$sel['audio'].'>Audio</option>';
$inhalt[] = '<option value="image" '.$sel['image'].'>Bilder</option>';
$inhalt[] = '<option value="t2_paarung" '.$sel['t2_paarung'].'>Nächste Turnierpaarungen</option>';
$dsp->AddDropDownFieldRow("content_type", "Inhaltstyp:", $inhalt, '', 0);

$dsp->AddTextFieldRow("c_pfad", "Dateipfad:", $cnt['pfad'],NULL,NULL,0);
$dsp->AddTextFieldRow("c_anzeigedauer", "Anzeigedauer:", $cnt['anzeigedauer'],NULL,NULL,0);
$dsp->AddTextFieldRow("c_wiederholungen_soll", "Wiederholungen Soll", $cnt['wiederholungen_soll'],NULL,NULL,0);
$dsp->AddDoubleRow("Wiederholungen Ist", $cnt['wiederholungen_ist']);

$dsp->AddCheckBoxRow("c_active", "Aktiviert", "Ja", NULL, 1,$cnt['active']);
$dsp->AddFormSubmitRow('save');
$dsp->AddBackButton("index.php?mod=beamer&action=inhalt", NULL);

// This will finaly output all the $dsp-Rows
$dsp->AddContent();
}
}
} else $func->error("ACCESS_DENIED", "");
?>