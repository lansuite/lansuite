<?php
$db->qry("UPDATE %prefix%partys SET checked = NOW() WHERE party_id = %int%", $party->party_id);
$func->confirmation(t("Datum und Uhrzeit wurden erfolgreich eingetragen!"), '?mod=guestlist&action=guestlist');
$dsp->AddContent();
?>