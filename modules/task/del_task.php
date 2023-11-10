<?php

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
	case 2:
		$tid = $_GET["taskid"];
		$db->qry("DELETE FROM %prefix%tasks WHERE taskid = %int%", $tid); 
		$db->qry("DELETE FROM %prefix%taskuser WHERE id_task =  %int%", $tid);	
	
		$func->confirmation(t("Die Aufgabe wurde erfolgreich gelöscht"), "?mod=task&action=del_task"); 
	break;

	default:
		$dsp->NewContent(t("Aufgabe löschen"), t("Hier können Sie die ausgewählte Aufgabe löschen"));
		$dsp->AddSingleRow(t("Achtung: Nach Klicken ist diese Aufgabe für immer verworfen!!!"));
		$display = "
		<table class=tbl_0>
			<tr>
				<td width=30><center>Nr.:</center></td>
				<td width=40><center>Prio:</center></td>
				<td width=250>Aufgabe:</td>
				<td width=280>Detail:</td>
				<td width=100>User:</td>
				<td width=40>Status:</td>
			</tr>
		</table>";
	
		$dsp->AddSingleRow($display);
		$party_id = $party->party_id;
		$res = $db->qry("SELECT * FROM %prefix%tasks WHERE party_id =%int% ORDER BY 'prio'", $party_id);
		$i = 0;
		while ($tasks = $db->fetch_array($res)){
			$i++;
			if ($tasks["prio"] == 1) { 
				$prio="<img src=ext_inc/task_icons/hoch.gif>";
			}
			if ($tasks["prio"] == 2) {
				$prio="<img src=ext_inc/task_icons/mittel.gif>";
			}
			if ($tasks["prio"] == 3) {
				$prio="<img src=ext_inc/task_icons/niedrig.gif>";
			}
	
			if (strlen($tasks["shortinfo"]) > 20) { 
        		$info = substr($tasks["shortinfo"], 0, 20) . "...";
        	} else {
        		$info = $tasks["shortinfo"];
        	}
			$tid = $tasks["taskid"];
			$status = $tasks["status"];
			if ($status=="0") {
				$status="<font color=red><b>offen</b></font>";}else{$status="geschlossen";
			}
	
			$logged = $db->qry_first("SELECT COUNT(*) AS anz FROM %prefix%taskuser WHERE ($tid=id_task) GROUP BY id_task");

			$userCount = $logged["anz"] ?? 0;
			if ($userCount < 1) {
				$userCount = "<font color=red><b>0</b></font>";
			}
	
			$display = '
			<table class=tbl_0>
				<tr>
					<td width=30><center>' . $i . '.</center></td>
					<td width=40><center>' . $prio . '</center></td>
					<td width=250>
						<a href=?mod=task&action=del_task&step=2&taskid=' . $tid . '>' . $tasks['task'] . '</a>
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