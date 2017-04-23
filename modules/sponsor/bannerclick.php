<?php
$get_banner = $db->qry_first("SELECT url FROM %prefix%sponsor WHERE sponsorid = %int%", $_GET['sponsorid']);

if ($get_banner) {
    if ($_GET['type'] == 'banner') {
        $db->qry("UPDATE %prefix%sponsor SET hits_banner = hits_banner + 1 WHERE sponsorid = %int%", $_GET['sponsorid']);
    } elseif ($_GET['type'] == 'box') {
        $db->qry("UPDATE %prefix%sponsor SET hits_box = hits_box + 1 WHERE sponsorid = %int%", $_GET['sponsorid']);
    } else {
        $db->qry("UPDATE %prefix%sponsor SET hits = hits + 1 WHERE sponsorid = %int%", $_GET['sponsorid']);
    }
    Header("Location: ". $get_banner["url"]);
} else {
    die("<strong>".t('Diese Banner-ID existiert nicht! Manipulationsversuch oder Datenbankfehler. Bitte ZURÜCK anklicken.')."</strong>");
}
