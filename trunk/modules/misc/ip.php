<?php
$blockid = $_GET['blockid'];
$seating_ip = $_POST['seating_ip'];

$seat = new seat;

switch($_GET['step']) {
	case 3:
		$seating_ip_exists = array();
		$seating_ip = array();

		if ($_POST['cell']) foreach($_POST['cell'] as $cur_cell => $value) if ($value) {
			$col = floor($cur_cell / 100);
			$row = $cur_cell % 100;

			// Check IP format
			if (!$func->checkIP($value)) {
		    $func->error($lang['misc']['err_ip_format'], '');
				$_GET['step'] = 2;
				break;
			}

			// Check for allready assigned IPs
			/*
			$current_ip = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["seat_seats"]} WHERE ip = '$value'");
			if ($current_ip['found']) {
				$func->error($lang['misc']['err_double_ip'], '');
				$_GET['step'] = 2;
				break;
			}*/
		}
	break;
}

switch($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, 'index.php?mod=misc&action=ip', "index.php?mod=misc&action=ip&step=2&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$dsp->NewContent($lang['seating']['seat_info'], $lang['seating']['seat_info_sub']);

		$dsp->SetForm("index.php?mod=misc&action=ip&step=3&blockid={$_GET['blockid']}");
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 3));
		$dsp->AddFormSubmitRow('next');

		$dsp->AddBackButton('index.php?mod=misc&action=ip', 'seating/show');
		$dsp->AddContent();
	break;

	case 3:
		if ($_POST['cell']) foreach($_POST['cell'] as $cur_cell => $value) {
			$col = floor($cur_cell / 100);
			$row = $cur_cell % 100;

			$db->query_first("UPDATE {$config["tables"]["seat_seats"]} SET ip='$value'
				WHERE blockid = '{$_GET['blockid']}' AND row = '$row' AND col = '$col'");
		}
		$func->confirmation($lang['misc']['cf_add_ips'], 'index.php?mod=misc&action=ip');
	break;
}
?>