<?php

$stepParameter = $_GET['step'] ?? 0;
switch($stepParameter) {
	case 2:
		$dsp->NewContent(t('Detailansicht'), t('Hier kannst du die Details der Aufgabe sehen'));
		$taskid = $_GET["taskid"];
		$userid = $auth['userid'];
		$task = $db->qry_first("SELECT * FROM %prefix%tasks WHERE taskid = '$taskid' ");
		$dsp->AddSingleRow($task["task"]);
		$dsp->AddSingleRow($task["shortinfo"]);
		$dsp->AddBackButton("?mod=task&action=my_task", ""); 
	break;
	
	case 3:
		$tid = $_GET["taskid"];
		$userid = $auth['userid'];
		$db->qry("DELETE FROM %prefix%taskuser WHERE id_task='$tid' and id_user='$userid'");
		$func->confirmation(t("Die Aufgabe wurde erfolgreich gelöscht"), "?mod=task&action=my_task"); 
	break;

	default:
		$dsp->NewContent(t('deine Aufgaben'), t('Hier kannst du deine Aufgaben sehen'));
	
		$display = "
		<table class=tbl_0>
			<tr>
				<td width=30><center>Nr.:</center></td>
				<td width=40><center>Prio:</center></td>
				<td width=250>Aufgabe:</td>
				<td width=300>Detail:</td>
			</tr>
		</table>";
	
		$dsp->AddSingleRow($display);
		$party_id = $party->party_id;
		$userid = $auth['userid'];
		$res = $db->qry("SELECT * FROM %prefix%tasks,%prefix%taskuser WHERE taskid=id_task AND id_user=$userid and party_id=$party_id ORDER BY 'prio'");
		$i = 0;
		while ($tasks = $db->fetch_array($res)){
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
	
			if (strlen($tasks["shortinfo"]) > 60) { 
        		$info = substr($tasks["shortinfo"], 0, 60) . "...";
        	} else {
        		$info = $tasks["shortinfo"];
        	}
			$tid = $tasks["taskid"];
			$display = '
			<table class=tbl_0>
				<tr>
					<td width=30><center>' . $i . '.' . '</center></td>
					<td width=40><center>' . $prio . '</center></td>
					<td width=250>
						<a href=?mod=task&action=my_task&step=2&taskid=' . $tid . '>' . $tasks['task'] . '</a>
					</td>
					<td width=300>' . $info . '</td>
					<td width=50>
						<a href=?mod=task&action=my_task&step=3&taskid=' . $tid . '>abmelden</a>
					</td>
				</tr>
			</table>';
			$dsp->AddSingleRow($display);
		}
		$dsp->AddBackButton("?mod=task&action=show_task", "");
	break;	
}