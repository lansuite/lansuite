<?php
$LSCurFile = __FILE__;

switch($vars["step"]) {
	default:
    include_once('modules/news/search.inc.php');
	break;

	case 2:
		// CHECK IF NEWSID IS VALID
		$get_data = $db->query_first("SELECT caption FROM {$config["tables"]["news"]} WHERE newsid = '{$vars["newsid"]}'");
		$caption = $get_data["caption"];
		$newsid = $vars["newsid"];
	
		if ($caption != "") {
			$func->question(t('Sind Sie sicher, dass Sie die Newsmeldung <b>%1</b> wirklich l&ouml;schen wollen?', array($caption)), "index.php?mod=news&action=delete&step=3&newsid=$newsid", "index.php?mod=news");
		} else $func->error(t('Diese Newsmeldung existiert nicht'), "index.php?mod=news&action=delete");
	break;

	case 3:
		// CHECK IF NEWSID IS VALID
		$get_data = $db->query_first("SELECT caption FROM {$config["tables"]["news"]} WHERE newsid = '{$vars["newsid"]}'");
		$caption = $get_data["caption"];
		$newsid = $vars["newsid"];

		if($caption != "") {
			$del_it = $db->query("DELETE from {$config["tables"]["news"]} WHERE newsid = '$newsid'");
			if ($del_it) {
				$func->confirmation(t('Die Newsmeldung wurde erfolgreich gelöscht'), "index.php?mod=news&action=show");
				$func->log_event(t('Die News "%1" wurde gelöscht', array($get_data['caption'])), 1, "News");
			}
		} else $func->error(t('Diese Newsmeldung existiert nicht'), "index.php?mod=news&action=delete");
	break;
}
?>
