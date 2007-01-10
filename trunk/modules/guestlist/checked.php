<?
$year = date("Y" );
$month = date("m" );
$day = date("d" );
$hour = date("H" );
$minute = date("i" );
$unixtimes = $func->date2unixstamp($year, $month, $day, $hour, $minute, 0);
$link_target = "?mod=guestlist&action=guestlist";

$db->query("UPDATE lansuite_partys SET checked = $unixtimes" );
$func->confirmation(t("Datum und Uhrzeit wurden erfolgreich eingetragen!"), $link_target) ;
$dsp->AddContent();
?>