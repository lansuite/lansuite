<?php

$module = $db->query_first("SELECT * FROM {$config["tables"]["modules"]} WHERE name = 'pdf'");
$pdf = $db->query_first("SELECT * FROM {$config["tables"]["pdf_list"]} WHERE name = '{$cfg['user_ticket_template']}' AND template_type='guestcards'");
if ($module["active"] && $pdf['template_id']){
	$dsp->NewContent($lang["usrmgr"]["myticket_caption"], $lang["usrmgr"]["myticket_subcaption"]);
	$dsp->AddSingleRow("<a class=\"menu\" href=\"index.php?mod=pdf&action=ticket&design=base&act=print&id={$pdf['template_id']}&userid={$auth['userid']}\">{$lang["usrmgr"]["myticket_show"]}</a>");
	$dsp->AddBackButton("index.php", "usrmgr/myticket"); 
	$dsp->AddContent();
}else{
	$func->error($lang["usrmgr"]["myticket_error"],"index.php?mod=home");
}
	
?>
