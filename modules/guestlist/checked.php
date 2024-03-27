<?php

$database->query("UPDATE %prefix%partys SET checked = NOW() WHERE party_id = ?", [$party->party_id]);
$func->confirmation(t("Datum und Uhrzeit wurden erfolgreich eingetragen!"), 'index.php?mod=guestlist&action=guestlist');
