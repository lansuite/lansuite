<?php

$stepParameter = $_GET['step'] ?? 0;
switch($stepParameter) {
	case 2:
		$party_id = $party->party_id;
		$dsp->NewContent(t('Detailansicht'), t('Hier kannst du die Details der Aufgaben einsehen'));
		$taskid = $_GET["taskid"];
		$userid = $auth['userid'];
		$task = $db->qry_first("SELECT * FROM %prefix%tasks WHERE taskid = '$taskid' and party_id = %int%", $party_id);
		$dsp->AddSingleRow($task["task"]);
		$dsp->AddSingleRow($task["shortinfo"]);
	
		if (($auth['login'] == 1) && ($task["status"] == 0)) {
			$button = '<a href="%s"><img border="0" src="design/'.$_SESSION["auth"]["design"].'/images/%s.gif"></a> ';
			$buttons = sprintf($button, "?mod=task&action=show_task&taskid=$taskid&step=3", "buttons_join");
			$dsp->AddDoubleRow(t('Zu dieser Aufgabe anmelden?'), $buttons);
		}
		if ($task["status"] == 1) {
			$dsp->AddSingleRow("<font color=red>Status geschlossen: Anmeldung nicht mehr notwendig, es gibt genug Helfer !!!</font>");
		}
		if (($auth['login'] == 0) && ($task["status"] == 0)) {
			$dsp->AddSingleRow("<font color=red>Status offen: Bitte einloggen zum Anmelden!!!</font>");
		}
	
		$dsp->AddBackButton("?mod=task&action=show_task", ""); 
	break;
	
	case 3:
		$taskid = $_GET["taskid"];
		$userid = $auth['userid'];

		$task = $db->qry_first("SELECT * FROM %prefix%tasks,%prefix%taskuser WHERE taskid = %int% ", $taskid);
		$tasks = $db->qry_first("SELECT * FROM %prefix%taskuser WHERE id_task = %int% ", $taskid);

		if (is_array($tasks) && $tasks["id_task"] == $taskid && $tasks["id_user"] == $userid) {
			$dsp->AddSingleRow(t('Du hast dich bereits zu dieser Aufgabe angemeldet !!!'));
		} else {
			$add_it = $db->qry("
				INSERT INTO %prefix%taskuser (id_task, id_user) 
				VALUES (%int%, %int%)",
				$taskid, $userid
			);
			
			if ($add_it == 1) { 
				$func->confirmation(t('Du hast dich soeben f&uuml;r diese Aufgabe angemeldet!'),"?mod=task&action=show_task");
			} else {
				$func->error("NO_REFRESH","");
			}
		}
	break;
	default:
		$dsp->NewContent(t('Aufgaben'), t('Hier kannst du die angefallen Aufgaben einsehen'));
	
		$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=250>Aufgabe:</td><td width=280>Detail:</td><td width=100>Anzahl Helfer:</td><td width=40>Status:</td></tr></table>";
		$party_id=$party->party_id;
		$res = $db->qry("SELECT * FROM %prefix%tasks WHERE party_id = %int% ORDER BY 'prio'", $party_id);
	
		$dsp->AddSingleRow($display);
		$i = 0;
		while ($tasks = $db->fetch_array($res)) {
			$i++;
			if ($tasks["prio"] == 1) {
				$prio = "<img src=ext_inc/task_icons/hoch.gif>";
			}
			if ($tasks["prio"] == 2) {
				$prio = "<img src=ext_inc/task_icons/mittel.gif>";
			}
			if ($tasks["prio"] == 3) {
				$prio = "<img src=ext_inc/task_icons/niedrig.gif>";
			}
	
			if (strlen($tasks["shortinfo"]) > 20) { 
				$info = substr($tasks["shortinfo"] ,0 ,20) . "...";
			} else {
				$info = $tasks["shortinfo"];
			}
	
			$tid = $tasks["taskid"];
			$status = $tasks["status"];
			if ($status == "0") {
				$status="<font color=red><b>offen</b></font>";}else{$status="geschlossen";
			}
		
			$logged = $db->qry_first("SELECT COUNT(*) AS anz FROM %prefix%taskuser WHERE (%int% = id_task) GROUP BY id_task", $tid);
			$userCount = $logged["anz"] ?? 0;
			if ($userCount < 1) {
				$userCount = "<font color=red><b>0</b></font>";
			}
		
			$display = '
			<table class=tbl_0>
				<tr>
					<td width=30>
						<center>' . $i . '.</center>
					</td>
					<td width=40>
						<center>' . $prio . '</center>
					</td>
					<td width=250>
						<a href=?mod=task&action=show_task&step=2&taskid=' . $tid . '>' . $tasks['task'] . '</a>
					</td>
					<td width=280>' . $info . '</td>
					<td width=100>' . $userCount . '</td>
					<td width=40>' . $status . '</td>
				</tr>
			</table>';
			$dsp->AddSingleRow($display);
		}
	break;
}
