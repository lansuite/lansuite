<?php

$HANDLE["POLLID"]	= $_GET["pollid"];
$HANDLE["STEP"]		= $_GET["step"];

switch($HANDLE["STEP"]) {
	default:
	  include_once('modules/poll/search.inc.php');
	break;

	case 2:
			$POLL = $db->query_first("
			SELECT	caption
			FROM	{$config[tables][polls]}
			WHERE	pollid = '$HANDLE[POLLID]'
			");

			if(isset($POLL['caption'])) $func->question(str_replace("%CAPTION%", $POLL['caption'], $lang["poll"]["del_confirm"]),"index.php?mod=poll&action=delete&step=3&pollid=" . $HANDLE["POLLID"], "index.php?mod=poll&action=delete");
			else $func->error($lang["poll"]["add_err_noexist"], "index.php?mod=poll&action=delete");
	break;

	case 3:
		$POLL = $db->query_first("SELECT caption FROM {$config[tables][polls]} WHERE pollid = '$HANDLE[POLLID]'");

		if (isset($POLL['caption'])) {
			$DELETE = $db->query("DELETE FROM {$config[tables][polls]} WHERE pollid = '$HANDLE[POLLID]'");
			if ($DELETE) $func->confirmation(str_replace("%CAPTION%", $POLL['caption'], $lang["poll"]["del_deleted"]),"index.php?mod=poll&action=delete");
		} else $func->error($lang["poll"]["add_err_noexist"], "index.php?mod=poll&action=delete");
	break;

}
?>