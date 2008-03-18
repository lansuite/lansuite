<?php

$module = $db->query_first("SELECT * FROM {$config["tables"]["modules"]} WHERE name = 'pdf'");
$pdf = $db->query_first("SELECT * FROM {$config["tables"]["pdf_list"]} WHERE name = '{$cfg['user_ticket_template']}' AND template_type='guestcards'");
if ($module["active"] && $pdf['template_id']){
	$dsp->NewContent(t('Eintrittskarte drucken'), t('Diese Seite ermöglicht es Ihnen Ihre Eintrittskarte zur Veranstaltung auszudrucken'));
	$dsp->AddSingleRow("<a class=\"menu\" href=\"index.php?mod=pdf&action=ticket&design=base&act=print&id={$pdf['template_id']}&userid={$auth['userid']}\">".t('Eintrittskarte anzeigen (Bitte speichern sie die Datei vor dem &ouml;ffnen ab.)')."</a>");
	$dsp->AddBackButton("index.php", "usrmgr/myticket"); 
	$dsp->AddContent();
}else{
	$func->error(t('Für die Eintrittskarte muss das PDF-Modul aktiv sein und es muss ein Template eingestellt sein.'),"index.php?mod=home");
}
	
?>