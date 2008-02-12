<?
/*
* LansinTV - A interactive mediaplayer webinterface
* Copyright  (C) 2003-2004 by Mario Ohnewald
* 
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You  should have received a copy of the GNU General Public License along with
* this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

$lansintv_admin = $db->query_first("SELECT * FROM {$config["tables"]["lansintv_admin"]}");

// Clips Editieren, nochmal admin status checken
if ($auth["type"] >= 2) {
	if ($_POST["check_del_ban"]) foreach ($_POST["check_del_ban"] as $val){
		if ($val) switch ($_POST["do_action"]){
			case "del_file":
				$delete_file = $db->query_first("SELECT pfad FROM {$config["tables"]["lansintv"]} WHERE id='$val'");

				$db->query_first("DELETE FROM {$config["tables"]["lansintv"]} WHERE id='$val'");
				shell_exec("rm -f \"{$lansintv_admin["upload_directory"]}/{$delete_file["pfad"]}\"");
			break;

			case "ban_file":
				
				$db->query_first("UPDATE {$config["tables"]["lansintv"]} SET banned = '1 ' WHERE `id` = '$val';");
				
				// write into blackist_files table	
				$upload_user = $db->query_first("SELECT uid FROM {$config["tables"]["lansintv"]} WHERE `id` = '$val'");
				$db->query_first("INSERT into {$config["tables"]["lansintv_blacklist_files"]} SET `id` = '$val', userid='{$upload_user[0]}';");
			break;

			case "ban_user":
				// find out who uploaded it
				$ban_user=$db->query_first("SELECT uid FROM {$config["tables"]["lansintv"]} WHERE id='$val'");
				
				//Write Banned user into table
				$db->query_first("INSERT INTO {$config["tables"]["lansintv_user"]} SET banned = '1', userid = '$ban_user[0]'");
				$db->query_first("INSERT INTO {$config["tables"]["lansintv_blacklist_user"]} SET userid = '$ban_user[0]', id='$val'");
				$db->query_first("UPDATE {$config["tables"]["lansintv"]} SET banned = '1 ' WHERE `id` = '$val';");
			break;
		}
	}

/*
	// Datei Löschen
	if ($_POST["delete"] != ""){
		$delete_file = $db->query_first("SELECT pfad FROM {$config["tables"]["lansintv"]} WHERE id='{$_GET['edit_action']}'");

		$db->query_first("DELETE FROM {$config["tables"]["lansintv"]} WHERE id='" . $_GET['edit_action'] . "'");
		shell_exec("rm -f \"{$lansintv_admin["upload_directory"]}/{$delete_file["pfad"]}\"");
	}

	
	if ($_POST["file_ban"] != ""){
		$file_data = $db->query_first("SELECT pfad, md5sum FROM {$config["tables"]["lansintv"]} WHERE id = '{$_GET['edit_action']}'");

		$db->query("INSERT INTO {$config["tables"]["lansintv_blacklist_files"]} SET id = '{$_GET['edit_action']}', md5sum='{$file_data["md5sum"]}', name='{$file_data["pfad"]}'");
		
		$db->query("UPDATE {$config["tables"]["lansintv"]} SET banned = '1' WHERE id ='{$_GET['edit_action']}'");
	}


	if ($_POST["user_ban"] != ""){
		$user_data = $db->query_first("SELECT ltv.uid, u.username
				FROM {$config["tables"]["lansintv"]} AS ltv
				LEFT JOIN {$config["tables"]["user"]} AS u ON ltv.uid = u.userid
				WHERE id = '{$_GET['edit_action']}'");

		$db->query_first("INSERT INTO {$config["tables"]["lansintv_blacklist_user"]} SET id='{$user_data["uid"]}', name='{$user_data["username"]}'");

		$db->query("UPDATE {$config["tables"]["lansintv_user"]} SET banned = '1' WHERE userid = '{$user_data["uid"]}'");
		$db->query("UPDATE {$config["tables"]["lansintv"]} SET banned = '1' WHERE uid='{$user_data["uid"]}'");
	}
*/
	if ($_GET['best_of']) $db->query_first("UPDATE {$config["tables"]["lansintv"]} SET bestof = '1' WHERE id = '{$_GET['best_of']}'");

	if ($_GET['veto']) $db->query_first("UPDATE {$config["tables"]["lansintv"]} SET votes = votes + 10 WHERE id = '{$_GET['veto']}'");
} 



if ($_GET['vote']) {
	if ($auth["login"] <= 0) $templ['lansinplayer']['case']['info']['not_logged_in'] = "Du musst eingeloggt sein um Voten zu kicknen<img src=design/standard/images/suxxif border=0>";	
	else {
		$vote_ok = 1;
		if ($auth["type"] < 2) {
			$lansuite_user = $db->query_first("SELECT votes_left FROM {$config['tables']['user']} WHERE userid='{$auth["userid"]}'");

			if ($lansuite_user["votes_left"] <= 0) {
				$subcaption = "<font size=1 color=red>Du keines Votes mehr f&uuml;r diese Stunde &uuml;brig</font>";
				$vote_ok = 0;
			} else {
				$subcaption = "<font size=1>Du hast noch ". $lansuite_user["votes_left"] ."/ 10 Votes f&uuml;r diese Stunde &uuml;brig</font>";
			}

			$lansintv_user = $db->query_first("SELECT banned FROM {$config['tables']['lansintv_user']} WHERE userid='{$auth["userid"]}'");
			if ($lansintv_user["banned"]) {
				$subcaption = "<font size=1 color=red>You were banned</font>";
				$vote_ok = 0;
			}
		}
	}

	if ($vote_ok) {
		// check if userid exists
		$check_user=$db->query_first("SELECT votes FROM {$config['tables']['lansintv_user']} WHERE userid='{$auth["userid"]}'");
		if ($check_user) {
			$db->query_first("UPDATE {$config['tables']['lansintv_user']} SET votes =votes + 1 WHERE userid = '{$auth['userid']}'");
		}else{
			$db->query_first("INSERT into {$config['tables']['lansintv_user']} SET userid = '{$auth["userid"]}', votes ='1'");
		}
		
		$db->query_first("UPDATE {$config['tables']['lansintv']} SET votes =votes + 1 WHERE id = '{$_GET['vote']}'");
		if ($auth["type"] < 2) $db->query_first("UPDATE {$config['tables']['user']} SET votes_left = votes_left -1 WHERE userid = '{$auth["userid"]}'");
	}
}
########## Ende: lansinplayer_vote.php #############

// Rausnehmen ??
//if ($auth["type"] >= 2) $subcaption .= "Admin Mode ole!";

################## lansinplayer_table.php ################
switch ($_GET["action"]) {
case history:
        $dsp->NewContent("Lansin-TV (tm) - History", $subcaption);
	break;
case upload:
   	$dsp->NewContent("Lansin-TV (tm) - Upload", $subcaption);
	break;
case stats:
   	$dsp->NewContent("Lansin-TV (tm) - Stats", $subcaption);
	break;
case ssearch:
        $dsp->NewContent("Lansin-TV (tm) - Search", $subcaption);
        break;
case bestof:
        $dsp->NewContent("Lansin-TV (tm) - Best Of", $subcaption);
        break;
case setup:
        $dsp->NewContent("Lansin-TV (tm) - Setup", $subcaption);
        break;
case remote:
        $dsp->NewContent("Lansin-TV (tm) - Fernbedienung", $subcaption);
        break;
case blacklist:
        $dsp->NewContent("Lansin-TV (tm) - Blacklist", $subcaption);
        break;
default:
        $dsp->NewContent("Lansin-TV (tm)", $subcaption);
        break;
}



if ($_GET["action"] == "history") {
	$dsp->NewContent("Lansin-TV (tm) - History", $subcaption);
} else {
	$dsp->NewContent("Lansin-TV (tm)", $subcaption);
}



// reset now playing text
$now_playing_text = "";

//query for new and voted clips
$find_votes = $db->query_first("SELECT id FROM {$config['tables']['lansintv']} WHERE `banned` != '1'");
#$find_votes=$find_votes[0];


// wenn keine votes >= 1
if (!$find_votes) {

	// no clips
        $func->no_items("Clips/Uploads","","rlist");
        $now_playing_text = "";


} else {

	$find_votes = $db->query_first("SELECT id FROM {$config['tables']['lansintv']} WHERE votes >= 0 AND `banned` != '1'");
        if (!$find_votes) {
		if (!$_GET["action"]=="history") $now_playing_text = "<h2><font color=red>Random Modus</font></h2><h4>(Da fuer keine Clips gevotet wurde.)</h4>";
		
	}

	########### Draw Table start #########

	//$now_playing_text = "<h2><font color=red>Random Modus</font></h2><h4>(Da fuer keine Clips gevotet wurde.)</h4>";
	$now_playing = $db->query_first("SELECT pfad, uploader
		FROM {$config['tables']['lansintv']}
		WHERE id = '{$lansintv_admin["now_playing"]}' AND `banned` != '1'
		LIMIT 1;
		");
	
	$now_playing_name = $db->query_first("SELECT username FROM {$config['tables']['user']}
		WHERE userid = '{$now_playing["uploader"]}'
		LIMIT 1;
		");
		
		
		
	if ($now_playing["pfad"] and !$_GET["action"] or $_GET["action"]=="vote") $now_playing_text .= "<h3><b> Now playing:</h3><p><i> {$now_playing["pfad"]}  by  {$now_playing_name["username"]}</i><br>";
	
	$templ['lansinplayer']['case']['table']['now_playing'] = "$now_playing_text";






	if($auth["type"] >= 2) {

		// Admin needs check column
		$templ['lansinplayer']['case']['table']['admin_header_playing'] ="$now_playing_text";

	}

	$limit = $db->query_first("SELECT max_table_rows FROM {$config['tables']['lansintv_admin']}");

	// Check if max_table_rows is set at all, if not, set it to 30 
	if (!$limit["max_table_rows"]) $add_sql = "LIMIT 30";
	else $add_sql = "LIMIT " . $limit["max_table_rows"];


	// if show all, then leave limit empty
	if ($_GET['show'] == "all") $add_sql="";

	// find out if you need to display history, best of or default table.
	if ($_GET["action"] == "history") {
		$get_data = $db->query("SELECT items.id, items.pfad, user.username, items.votes, items.size, history.timestamp
			FROM {$config['tables']['lansintv']} AS items
			LEFT JOIN {$config['tables']['user']} AS user ON user.userid = items.uploader
			INNER JOIN {$config['tables']['lansintv_history']} AS history ON history.ID_History = items.id
			WHERE (!items.banned)
			ORDER by items.votes DESC $add_sql");

	} elseif ($_GET["action"] == "best_of"){
		$get_data = $db->query("SELECT items.id, items.pfad, user.username, items.votes, items.size
			FROM {$config['tables']['lansintv']} AS items
			LEFT JOIN {$config['tables']['user']} AS user ON user.userid = items.uploader
			WHERE ((!items.banned) AND (bestof))
			ORDER by items.votes DESC $add_sql");

	} else {
		$get_data = $db->query("SELECT items.id, items.pfad, user.username, user.userid, items.votes, items.size
			FROM {$config['tables']['lansintv']} AS items
			LEFT JOIN {$config['tables']['user']} AS user ON user.userid = items.uploader
			WHERE (!items.banned)
			ORDER by items.votes DESC $add_sql");

	}

	$tr = 0;
	$dsp->SetForm("?mod=lansintv");

	while ($row = $db->fetch_array($get_data)) {

	################## lansinplayer_table_extra.php ################

	// Admin-Spalte
	if($auth["type"] >= 2) {
	
		$templ['lansinplayer']['case']['table']['adm_delete'] = "<td bgcolor={$templ['lansinplayer']['case']['table']['tr_bgcolor']}><div align=center><input type=checkbox name=\"check_del_ban[]\" value=\"{$row["id"]}\"></div></td>";
	} else {
		$templ['lansinplayer']['case']['table']['adm_delete'] = "<td bgcolor={$templ['lansinplayer']['case']['table']['tr_bgcolor']}><div align=center>-</div></td>";
	}


	// ID-Spalte
	if ($auth["type"] >= 2) $templ['lansinplayer']['case']['table']['id'] = "{$row["id"]}";
	else $templ['lansinplayer']['case']['table']['id'] = $row["id"];

	// Pfad-Spalte
	if (strlen($row["pfad"]) >= 42) $row["pfad"] = substr($row["pfad"], 0, 40) . "...";
	// add download prefix
	if (($_GET["action"] == "history")) $templ['lansinplayer']['case']['table']['pfad'] = "<a href={$lansintv_admin["download_prefix"]}/{$row["pfad"]}>{$row["pfad"]}</a>";
	else $templ['lansinplayer']['case']['table']['pfad'] = $row["pfad"];
	
	$get_date = $db->query_first("SELECT Date FROM {$config['tables']['lansintv_history']} WHERE id_history='{$row["id"]}'");
	if ($_GET["action"] == "history") $templ['lansinplayer']['case']['table']['pfad'] .= " [". "$get_date[0]" ." Uhr]";
	

	// Namen-Spalte
	if (strlen($row["username"]) >= 17) $row["username"] = substr($row["username"], 0, 15) . "...";
        $templ['lansinplayer']['case']['table']['username'] = "<a href=index.php?mod=mail&action=newmail&step=2&userID={$row["userid"]}>{$row["username"]}</a>";
		

	// Votes-Spalte
	$templ['lansinplayer']['case']['table']['votes'] = $row["votes"];
	if ($row["votes"] > 0) $templ['lansinplayer']['case']['table']['votes_color'] = "green";
	elseif ($row["votes"] == 0) $templ['lansinplayer']['case']['table']['votes_color'] = "yellow";
	elseif ($row["votes"] < 0) {$templ['lansinplayer']['case']['table']['votes'] = "keine"; $templ['lansinplayer']['case']['table']['votes_color'] = "orange";}

	// Size
	$templ['lansinplayer']['case']['table']['size'] = $row["size"];

	// Vote
	$templ['lansinplayer']['case']['table']['vote'] = "<a href=index.php?mod=lansintv&action={$_GET["action"]}&vote={$row["id"]}><img src=\"design/standard/images/thumb.gif\" title=\"Vote\" alt=\"Vote\" border=\"0\"></a>";

	if($auth["type"] >= 2) $templ['lansinplayer']['case']['table']['adm_fkt'] = "
		<td bgcolor={$templ['lansinplayer']['case']['table']['tr_bgcolor']}><font size=2><center>
		<a href=index.php?mod=lansintv&action={$_GET["action"]}&veto={$row["id"]}><img src=design/standard/images/veto.jpeg title=Veto alt=Veto border=0></a>
		</font></center></td>
		<td bgcolor={$templ['lansinplayer']['case']['table']['tr_bgcolor']}><font size=2><center>
		<a href=index.php?mod=lansintv&action={$_GET["action"]}&best_of={$row["id"]}><img src=design/standard/images/best.gif title=BestOf alt=BestOf border=0 width=20 hight=25></a>
		</font></center></td>
		";

	################## Ende: lansinplayer_table_extra.php ################

	// my little table color hack
	if ($tr) {
		$tr = 0;
		$templ['lansinplayer']['case']['table']['tr_bgcolor'] = "silver";
	} else {
		$tr = 1;
		$templ['lansinplayer']['case']['table']['tr_bgcolor'] = "#cccccc";
	}

	$templ['lansinplayer']['case']['table']['rows'] .= $dsp->FetchModTpl("lansintv", "table_row");
	}

	$dsp->AddSingleRow($dsp->FetchModTpl("lansintv", "table_case"));

	if ($auth["type"] >= 2) {
		$t_array = array();
		array_push ($t_array, "<option value=\"del_file\">...Datei L&ouml;schen</option>");
		array_push ($t_array, "<option value=\"ban_file\">...Datei Bannen</option>");
		array_push ($t_array, "<option value=\"- - -\">- - -</option>");
		array_push ($t_array, "<option value=\"ban_user\">...User Bannen</option>");
		$dsp->AddDropDownFieldRow("do_action", "Markierte...", $t_array, "");
		$dsp->AddFormSubmitRow("next");
	}

}  ######## Draw Table end ########

$dsp->AddBackButton("?mod=lansintv&action={$_GET["action"]}", "lansintv/show");
$dsp->AddContent();

################## Ende: lansinplayer_table.php ################
?>
