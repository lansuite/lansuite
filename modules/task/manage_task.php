<?php

$stepParameter = $_GET["step"] ?? 0;
switch($stepParameter) {
	default:
		$dsp->NewContent(t('Aufgaben einsehen/zuteilen'), t('Hier kannst du die angefallen Aufgaben der Teilnehmer einsehen/zuordnen'));
		
		$display = "
			<table class=tbl_0>
				<tr>
					<td width=30><center>Nr.:</center></td>
					<td width=40><center>Prio:</center></td>
					<td width=250>Aufgabe:</td>
					<td width=280>Detail:</td>
					<td width=100>User:</td>
					<td width=40>Status:</td>
					<td width=40>Party:</td>
				</tr>
			</table>";
		$party_id = $party->party_id;
		$res = $db->qry("SELECT * FROM %prefix%tasks LEFT JOIN %prefix%taskuser ON id_task=taskid and party_id=%int% LEFT JOIN %prefix%user ON id_user=userid and party_id=%int% ORDER BY 'prio'", $party_id, $party_id);

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
				$info = substr($tasks["shortinfo"], 0, 20) . "...";
			} else {
				$info = $tasks["shortinfo"];
			}

			$tid = $tasks["taskid"];
			$status = $tasks["status"];
			if ($status == "0") {
				$status="<font color=red><b>offen</b></font>";
			} else {
				$status="geschlossen";
			}

			$display = '
				<table class=tbl_0>
					<tr>
						<td width=30><center>' . $i . '.</center></td>
						<td width=40><center>' . $prio . '</center></td>
						<td width=250>' . $tasks['task'] . '</td>
						<td width=280>' . $info . '</td>
						<td width=100>' . $username . '</td>
						<td width=40>' . $status .'</td>
						<td width=40>' . $tasks["party_id"] . '</td>
					</tr>
				</table>';
			$dsp->AddSingleRow($display);
		}
	break;
}