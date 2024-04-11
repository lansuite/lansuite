<?php
$stepParameter = $_GET["step"] ?? 0;
switch($stepParameter) {
	case 2:
		$dsp->NewContent(t("Aufgaben ändern"), t("Hier können Sie Aufgaben ändern"));
		$taskid = $_GET["taskid"];
		$dsp->SetForm("index.php?mod=task&action=change_task&step=3&taskid=$taskid");
	
		$tasks = $db->qry_first_rows("SELECT * FROM %prefix%tasks WHERE taskid = '$taskid' ");
	
		$dsp->AddTextFieldRow("task", t('Aufgabe &auml;ndern'), $tasks["task"], "");
		$dsp->AddTextAreaRow("shortinfo", t('Kurze Beschreibung'), $tasks["shortinfo"], "", "","",1);
	
		$option1_array = [
			"1" => "hoch",
			"2" => "mittel",
			"3" => "niedrig"
		];
		$t_array = [];
		foreach ($option1_array as $key => $value) {
			($tasks['prio']== $key) ? $selected = "selected" : $selected = "";
			$t_array[] = '<option ' . $selected . ' value="' . $key . '">' . $value . '</option>';
		}
		$status = $tasks["status"];
		$dsp->AddDropDownFieldRow("prio", t('Priorität der Aufgabe'), $t_array, "", $optional = NULL);
		$dsp->AddCheckBoxRow("status", "Status geschlossen?", "", "", "", $status);
		$dsp->AddFormSubmitRow("Ändern");$dsp->AddBackButton("?mod=task&action=change_task", ""); 
	break;
	
	case 3:
		if (strlen($_POST["task"]) == 0) {
			$dsp->AddSingleRow(t("Error, Sie haben keine Aufgabe eingetragen!!!"));
			if ($_POST["status"] == "") {
				$status=0;
			} else {
				$status=1;
			}
			$dsp->AddSingleRow($status);
			$dsp->AddBackButton("?mod=task&action=change_task", ""); 
		} else {

			$task = $_POST["task"];
			$shortinfo = $_POST["shortinfo"];
			if (strlen($_POST["shortinfo"]) == 0) {
				$shortinfo = t("Keine Details!");
			}
			$prio = $_POST["prio"];
			$statusParameter = $_POST["status"] ?? '';
			if ($statusParameter == "") {
				$status = 0;
			} else {
				$status = 1;
			}
		
			$taskid = $_GET["taskid"];
			// TODO Fix SQL injection
			$add_it = $db->qry("
				UPDATE %prefix%tasks 
				SET
					task = '{$task}',
					shortinfo = '{$shortinfo}',
					prio = '{$prio}',
					party_id = '$party->party_id',
					status= '{$status}'
				WHERE
					taskid='$taskid'");

			if ($add_it == 1) {
				$func->confirmation(t("Die Aufgabe wurde erfolgreich eingetragen."), "?mod=task&action=change_task");
			} else {
				$func->error("NO_REFRESH","");
			}
		} 
	break;
	default:
		$dsp->NewContent(t("Aufgaben ändern"), t("Hier können Sie Aufgaben ändern"));
		$display = "
		<table class=tbl_0>
			<tr>
				<td width=30><center>Nr.:</center></td>
				<td width=40><center>Prio:</center></td>
				<td width=250>Aufgabe:</td>
				<td width=280>Detail:</td>
				<td width=40>User:</td>
				<td width=40>Status:</td>
			</tr>
		</table>";
	
		$dsp->AddSingleRow($display);
		$party_id = $party->party_id;
		$res = $db->qry("SELECT * FROM %prefix%tasks WHERE party_id ={$party_id} ORDER BY 'prio'");
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
        		$info = substr($tasks["shortinfo"], 0, 20) . '...';
        	} else {
        		$info = $tasks["shortinfo"];
        	}
			$tid = $tasks["taskid"];
		
			$status = $tasks["status"];
			if ($status=="0") {
				$status="<font color=red><b>offen</b></font>";
			} else {
				$status="geschlossen";
			}
	
			$logged = $db->qry_first("SELECT COUNT(*) AS anz FROM %prefix%taskuser WHERE ($tid=id_task) GROUP BY id_task");
			
			$userCount = $logged["anz"] ?? 0;
			if ($userCount  < 1) {
				$userCount  = "<font color=red><b>0</b></font>";
			}
	
			$display = '
			<table class=tbl_0>
				<tr>
					<td width=30><center>' . $i . '.</center></td>
					<td width=40><center>' . $prio . '</center></td>
					<td width=250>
						<a href=?mod=task&action=change_task&step=2&taskid=' . $tid . '>' . $tasks['task'] . '</a>
					</td>
					<td width=280>' . $info . '</td>
					<td width=100>' . $userCount  . '</td>
					<td width=40>' . $status . '</td>
				</tr>
			</table>';
	
			$dsp->AddSingleRow($display);
		}
	break;
}