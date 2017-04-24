<?php
$dsp->NewContent('Variable anklicken, um es ins Textfeld einzufügen');

$variable = $db->qry("SELECT shortcut, title FROM %prefix%variables");
$out = '';
$z = 0;
while ($variables = $db->fetch_array($variable)) {
    $out .= '<a href="#" onclick="javascript:InsertCode(opener.document.'. $_GET['form'] .'.'. $_GET['textarea'] .', \''. $variables['shortcut'] .'\'); return false">'.$variables['title'].'</a>';
    $z++;
    if ($z % 1 == 0) {
        $out .= '<br />';
    }
}
$dsp->AddSingleRow($out);
$dsp->AddSingleRow("ACHTUNG:" . HTML_NEWLINE . "Angemeldet?, Bezahlt?, Eingecheckt? damit diese Variablen korrekt funtkionieren muss *Nur Angemeldete* ausgewählt sein");
