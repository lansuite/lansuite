<?php

$stepParameter = $_GET['step'] ?? 0;
switch($stepParameter) {
	case 2:
		$taskParameter = $_POST['task'] ?? '';
		if (strlen($taskParameter) == 0) {
			$dsp->AddSingleRow(t("Error, Sie haben keine Aufgabe eingetragen!!!"));
			$dsp->AddBackButton("?mod=task&action=add_task", ""); 

		} else {
			$shortinfoParameter = $_POST["shortinfo"] ?? '';
			if (strlen($shortinfoParameter) == 0) {
				$shortinfoParameter = t("Keine Details!");
			}

			$prio = $_POST["prio"] ?? '';
			$add_it = $db->qry("
				INSERT INTO %prefix%tasks (task, shortinfo, prio, party_id)
				VALUES (%string%, %string%, %string%, %int%)",
				$taskParameter, $shortinfoParameter, $prio, $party->party_id
			);

			if ($add_it == 1) {
				$func->confirmation(t("Die Aufgabe wurde erfolgreich eingetragen."), "?mod=task&action=show_task");
			} else {
				$func->error("NO_REFRESH","");
			}
		}
	break;	

	default:
		$dsp->NewContent(t('Aufgaben'), t('Hier können Sie Aufgaben erstellen'));
		$dsp->SetForm("index.php?mod=task&action=add_task&step=2");
		$dsp->AddTextFieldRow("task", t('Aufgabe hinzuf&uuml;gen'), "", "");
		$dsp->AddTextAreaRow("shortinfo", t('Kurze Beschreibung'), "", "", "","",1);
	
		$option1_array = [
			"1" => "Hoch",
			"2" => "Mittel",
			"3" => "Niedrig"
		];
		$t_array = [];
		foreach ($option1_array as $key => $value) {
			$t_array[] = '<option value="' . $key . '">' . $value . '</option>';
		}
		$dsp->AddDropDownFieldRow("prio", t('Priorität der Aufgabe'), $t_array, '');
		$dsp->AddFormSubmitRow("Hinzuf&uuml;gen");
		$dsp->AddBackButton("?mod=task&action=show_task", ""); 
	break;
}
