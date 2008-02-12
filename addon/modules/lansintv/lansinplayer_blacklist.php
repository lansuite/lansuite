<?
if ($_GET["file_id"] != ""){
	$db->query("DELETE FROM {$config["tables"]["lansintv_blacklist_files"]} WHERE id='$file_id'");
	$db->query("UPDATE {$config["tables"]["lansintv"]} SET banned = '0' WHERE id ='$file_id'");

	$func->confirmation("Die Datei wurde erfolgreich freigegeben", "?mod=lansintv&action=blacklist");

} elseif ($_GET["user_id"] != ""){
	$db->query("DELETE FROM {$config["tables"]["lansintv_blacklist_user"]} WHERE id='$user_id'");
	$db->query("UPDATE {$config["tables"]["lansintv_user"]} SET banned = '0' WHERE userid = '$user_id'");
	$db->query("UPDATE {$config["tables"]["lansintv"]} SET banned = '0' WHERE uid='$user_id'");

	$func->confirmation("Der Benutzer wurde erfolgreich freigegeben", "?mod=lansintv&action=blacklist");


		

} else {
	$dsp->NewContent("Lansin-TV (tm) - Blacklists", "");

if ($_GET["unban_file"]) $db->query("DELETE FROM {$config["tables"]["lansintv_blacklist_files"]} WHERE id='{$_GET["unban_file"]}'");
if ($_GET["unban_user"]) $db->query("DELETE FROM {$config["tables"]["lansintv_blacklist_user"]} WHERE userid='{$_GET["unban_user"]}'");
if ($_GET["unban_user"]) $db->query("DELETE FROM {$config["tables"]["lansintv_user"]} WHERE userid='{$_GET["unban_user"]}'");


	$get_data = $db->query("SELECT id, userid FROM {$config["tables"]["lansintv_blacklist_files"]}");
	$banned_files = "";
	while($row = $db->fetch_array($get_data)) {
		$banned_path = $db->query_first("SELECT pfad FROM {$config["tables"]["lansintv"]} WHERE id='{$row["id"]}'");
		$banned_user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid='{$row["userid"]}'");
		$banned_files .= "<a href=\"?mod=lansintv&action=blacklist&file_id={$row["id"]}\">{$row["id"]}. $banned_path[0] uploaded by $banned_user[0]</a> - <a href=?mod=lansintv&amp;action=blacklist&unban_file={$row["id"]}><img src=design/standard/images/delete.gif border=0></a><br>";
	}
	
	echo "$bannes_files";

	$db->free_result($get_data);
	if ($banned_files == "") $banned_files = "<i>-keine-</i>";
	$dsp->AddDoubleRow("Gesperrte Dateien<br>(Zum Freigeben anklicken)", $banned_files);

	$dsp->AddHRuleRow();
	$get_data = $db->query("SELECT userid, id FROM {$config["tables"]["lansintv_blacklist_user"]}");
	$banned_users = "";
	while($row = $db->fetch_array($get_data)) {
		
		$banned_path = $db->query_first("SELECT pfad FROM {$config["tables"]["lansintv"]} WHERE id='{$row["id"]}'");
		$banned_user = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid='{$row["userid"]}'");
		$banned_users .= "<a href=\"?mod=lansintv&action=blacklist&user_id={$row["id"]}\">{$row["id"]}. $banned_user[0] banned for clip $banned_path[0]</a> - <a href=?mod=lansintv&amp;action=blacklist&unban_user={$row["userid"]}><img src=design/standard/images/delete.gif border=0></a><br>";
	}
	$db->free_result($get_data);
	if ($banned_users == "") $banned_users = "<i>-keine-</i>";
	$dsp->AddDoubleRow("Gesperrte Benutzer<br>(Zum Freigeben anklicken)", $banned_users);

	$dsp->AddBackButton("?mod=lansintv", "lansintv/blacklist");
	$dsp->AddContent();
}
?>
