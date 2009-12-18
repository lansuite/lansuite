<?php
$blockid = $_GET['blockid'];
$seating_ip = $_POST['seating_ip'];

switch($_GET['step']) {
	case 3:
		$seating_ip_exists = array();
		$seating_ip = array();

		if ($_POST['cell']) foreach($_POST['cell'] as $cur_cell => $value) if ($value) {
			$col = floor($cur_cell / 100);
			$row = $cur_cell % 100;

			// Check IP format
			if (!$func->checkIP($value)) {
		    $func->error(t('Das Format mindestens einer IP ist ungültig. Format: 192.168.123.12'), '');
				$_GET['step'] = 2;
				break;
			}

			// Check for allready assigned IPs
			/*
			$current_ip = $db->qry_first("SELECT 1 AS found FROM %prefix%seat_seats WHERE ip = %string%", $value);
			if ($current_ip['found']) {
				$func->error(t('Mindestens eine IP wurde bereits vergeben'), '');
				$_GET['step'] = 2;
				break;
			}*/
		}
	break;
}

switch($_GET['step']) {
	default:
    $current_url = 'index.php?mod=seating&action=ip';
    $target_url = 'index.php?mod=seating&action=ip&step=2&blockid=';
    $target_icon = 'generate';
    include_once('modules/seating/search_basic_blockselect.inc.php');
	break;

	case 2:
    include_once("modules/seating/class_seat.php");
    $seat2 = new seat2();

		$dsp->NewContent(t('Sitzplatz - IP-Verteilung'), t('Hier sehen Sie die einzelnen Sitzpl&auml;tze und die jeweils zugewiesene IP-Nummer. Diese k&ouml;nnen einzeln von Hand neu eingetragen oder ge&auml;ndert werden.'));

		$dsp->SetForm("index.php?mod=seating&action=ip&step=3&blockid={$_GET['blockid']}");
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 3));
		$dsp->AddFormSubmitRow('next');

		$dsp->AddBackButton('index.php?mod=seating', 'seating/show');
		$dsp->AddContent();
	break;

	case 3:
		if ($_POST['cell']) foreach($_POST['cell'] as $cur_cell => $value) {
			$col = floor($cur_cell / 100);
			$row = $cur_cell % 100;

			$db->qry_first("UPDATE %prefix%seat_seats SET ip=%string%
    WHERE blockid = %int% AND row = %string% AND col = %string%", $value, $_GET['blockid'], $row, $col);
		}
		$func->confirmation(t('Die IPs wurden erfolgreich eingetragen'), 'index.php?mod=seating');
	break;
}
?>