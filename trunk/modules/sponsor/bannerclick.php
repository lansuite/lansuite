<?php
$get_banner = $db->query_first("SELECT url FROM {$config['tables']['sponsor']} WHERE sponsorid = '{$_GET['sponsorid']}'");

if ($get_banner) {
  if ($_GET['type'] == 'banner') $db->query("UPDATE {$config['tables']['sponsor']} SET hits_banner = hits_banner + 1 WHERE sponsorid = '{$_GET['sponsorid']}'");
  elseif ($_GET['type'] == 'box') $db->query("UPDATE {$config['tables']['sponsor']} SET hits_box = hits_box + 1 WHERE sponsorid = '{$_GET['sponsorid']}'");
  else $db->query("UPDATE {$config['tables']['sponsor']} SET hits = hits + 1 WHERE sponsorid = '{$_GET['sponsorid']}'");
	Header("Location: ". $get_banner["url"]);
} else die("<strong>".t('Diese Banner-ID existiert nicht! Manipulationsversuch oder Datenbankfehler. Bitte ZURÜCK anklicken.')."</strong>");
?>