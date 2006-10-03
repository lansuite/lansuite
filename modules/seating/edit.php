<?php

if ($_GET['action'] == 'add' and $_GET['step'] < 2) $_GET['step'] = 2;

// Error-Switch
$error = array();
switch($_GET['step']) {
	case 3:
		// Error Columns
		if ($_POST['cols'] == "") $error['cols'] = $lang['seating']['e_hori_lenght'];
		elseif ($_POST['cols'] <= 0) $error['cols'] = $lang['seating']['e_gt_zero'];
		elseif ($_POST['cols'] >= 60) $error['cols'] = $lang['seating']['e_lt_sixty'];
		else {
			$row = $db->query_first("SELECT count(*) AS number FROM {$config['tables']['seat_seats']}
				WHERE blockid = '{$_GET['blockid']}' AND status = 2 AND col >= '{$_POST['cols']}'");
			if ($row["number"] != 0) $error['cols'] = $lang['seating']['e_gt_seats'];
		}

		// Error Rows
		if ($_POST['rows'] == "") $error['rows'] = $lang['seating']['e_vert_lenght'];
		elseif ($_POST['rows'] <= 0) $error['rows'] = $lang['seating']['e_gt_zero'];
		elseif ($_POST['rows'] >= 100) $error['rows'] = $lang['seating']['e_lt_hundred'];
		else {
			$row = $db->query_first("SELECT count(*) AS number FROM {$config['tables']['seat_seats']}
				WHERE blockid = '{$_GET['blockid']}' AND status = 2 AND row >= '{$_POST['rows']}'");
    		if ($row["number"] != 0) $error['rows'] = $lang['seating']['e_gt_seats'];
    	}

		// Remark
		if (strlen($_POST['remark']) > 1500) $error['remark'] = $lang['seating']['e_max_chars'];

		foreach ($error as $key => $val) if ($val) {
			$_GET['step']--;
			break;
		}
	break;

	// Update Seperators
	case 4:
		if ($_GET['change_sep_row'] > 0) {
			$seperator = $db->query_first("SELECT value FROM {$config["tables"]["seat_sep"]}
				WHERE blockid = '{$_GET['blockid']}' AND orientation = '1' AND value = '{$_GET['change_sep_row']}'");
			if ($seperator['value']) $db->query("DELETE FROM {$config["tables"]["seat_sep"]}
				WHERE blockid = '{$_GET['blockid']}' AND orientation = '1' AND value = '{$_GET['change_sep_row']}'");
			else $db->query("INSERT INTO {$config["tables"]["seat_sep"]} SET blockid = '{$_GET['blockid']}', orientation = '1', value = '{$_GET['change_sep_row']}'");
		}
		if ($_GET['change_sep_col'] > 0) {
			$seperator = $db->query_first("SELECT value FROM {$config["tables"]["seat_sep"]}
				WHERE blockid = '{$_GET['blockid']}' AND orientation = '0' AND value = '{$_GET['change_sep_col']}'");
			if ($seperator['value']) $db->query("DELETE FROM {$config["tables"]["seat_sep"]}
				WHERE blockid = '{$_GET['blockid']}' AND orientation = '0' AND value = '{$_GET['change_sep_col']}'");
			else $db->query("INSERT INTO {$config["tables"]["seat_sep"]} SET blockid = '{$_GET['blockid']}', orientation = '0', value = '{$_GET['change_sep_col']}'");
		}
	break;

	case 6:
		 // $icon_nr = (int) substr($_POST['icon'], 0, 3);
		if ($_POST['cell']) foreach($_POST['cell'] as $cur_cell => $value) {
			$col = floor($cur_cell / 100);
			$row = $cur_cell % 100;

			$seats_qry = $db->query_first("SELECT seatid FROM {$config["tables"]["seat_seats"]}
			WHERE blockid = '{$_GET['blockid']}' AND row = '$row' AND col = '$col'");

			if (!$seats_qry['seatid'])
				$db->query("INSERT INTO {$config["tables"]["seat_seats"]} SET
					blockid = '{$_GET['blockid']}',
					row = '$row',
					col = '$col',
					status = '{$value}'
					");
			else
				$db->query("UPDATE {$config["tables"]["seat_seats"]} SET
					status = '$value'
					WHERE seatid = {$seats_qry['seatid']}
					");
		}
	break;
}


// Form-Switch
switch($_GET['step']) {
	default:
    include_once('modules/seating/search.inc.php');
	break;

	case 2:
		// Get data from DB
		if ($_GET['action'] == 'edit') {
			$block = $db->query_first("SELECT * FROM {$config["tables"]["seat_block"]} WHERE blockid = '{$_GET['blockid']}'");
			if ($_POST['name'] == "") $_POST['name'] = $block['name'];
			if ($_POST['cols'] == "") $_POST['cols'] = $block['cols'] + 1;
			if ($_POST['rows'] == "") $_POST['rows'] = $block['rows'] + 1;
			if ($_POST['orientation'] == "") $_POST['orientation'] = $block['orientation'];
			if ($_POST['u18'] == "") $_POST['u18'] = $block['u18'];
			if ($_POST['remark'] == "") $_POST['remark'] = $block['remark'];
			if ($_POST['text_tl'] == "") $_POST['text_tl'] = $block['text_tl'];
			if ($_POST['text_tc'] == "") $_POST['text_tc'] = $block['text_tc'];
			if ($_POST['text_tr'] == "") $_POST['text_tr'] = $block['text_tr'];
			if ($_POST['text_lt'] == "") $_POST['text_lt'] = $block['text_lt'];
			if ($_POST['text_lc'] == "") $_POST['text_lc'] = $block['text_lc'];
			if ($_POST['text_lb'] == "") $_POST['text_lb'] = $block['text_lb'];
			if ($_POST['text_rt'] == "") $_POST['text_rt'] = $block['text_rt'];
			if ($_POST['text_rc'] == "") $_POST['text_rc'] = $block['text_rc'];
			if ($_POST['text_rb'] == "") $_POST['text_rb'] = $block['text_rb'];
			if ($_POST['text_bl'] == "") $_POST['text_bl'] = $block['text_bl'];
			if ($_POST['text_bc'] == "") $_POST['text_bc'] = $block['text_bc'];
			if ($_POST['text_br'] == "") $_POST['text_br'] = $block['text_br'];
		}

		$dsp->NewContent($lang['seating']['create_block'], $lang['seating']['cr_block_sub']);
		$dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=3&blockid={$_GET['blockid']}");

		$dsp->AddTextFieldRow('name', $lang['seating']['block_name'],  $_POST['name'], $error['name']);
		$dsp->AddTextFieldRow('cols', $lang['seating']['lenght_hori'], $_POST['cols'], $error['cols']);
		$dsp->AddTextFieldRow('rows', $lang['seating']['lenght_vert'], $_POST['rows'], $error['rows']);

		// Orientation
		$selections = array();
		($_POST['orientation'] == 0) ? $selected = 'selected' : $selected = '';
		array_push ($selections, "<option $selected value=\"0\">".$lang['seating']['vertical'].'</option>');
		($_POST['orientation'] == 1) ? $selected = 'selected' : $selected = '';
		array_push ($selections, "<option $selected value=\"1\">".$lang['seating']['horizontal'].'</option>');
		$dsp->AddDropDownFieldRow('orientation', $lang['seating']['orientation'], $selections, '');

		$dsp->AddCheckBoxRow('u18', $lang['seating']['u18_block'], '', '', 0, $_POST['u18']);

		$dsp->AddTextAreaPlusRow('remark', $lang['seating']['remark'], $_POST['remark'], $error['remark'], '', 4, 1);

		$dsp->AddDoubleRow($lang['seating']['block_caption'], $dsp->FetchModTpl('seating', 'plan_labels'));

		$dsp->AddFormSubmitRow('next');
		$dsp->AddBackButton('index.php?mod=seating', 'seating/add');
		$dsp->AddContent();
	break;

	case 3:
		// Save block settings
		if ($_GET['action'] == 'add') {
			$db->query("INSERT INTO {$config['tables']['seat_block']} SET
				party_id = '{$party->party_id}',
				name = '{$_POST['name']}',
				rows = '". ($_POST['rows'] - 1) ."',
				cols = '". ($_POST['cols'] - 1) ."',
				orientation = '{$_POST['orientation']}',
				u18 = '{$_POST['u18']}',
				remark = '{$_POST['remark']}',
				text_tl = '{$_POST['text_tl']}',
				text_tc = '{$_POST['text_tc']}',
				text_tr = '{$_POST['text_tr']}',
				text_lt = '{$_POST['text_lt']}',
				text_lc = '{$_POST['text_lc']}',
				text_lb = '{$_POST['text_lb']}',
				text_rt = '{$_POST['text_rt']}',
				text_rc = '{$_POST['text_rc']}',
				text_rb = '{$_POST['text_rb']}',
				text_bl = '{$_POST['text_bl']}',
				text_bc = '{$_POST['text_bc']}',
				text_br = '{$_POST['text_br']}'
				");
			$_GET['blockid'] = $db->insert_id();
		} else {
			$db->query("UPDATE {$config["tables"]["seat_block"]} SET
				party_id = '{$party->party_id}',
				name = '{$_POST['name']}',
				rows = '". ($_POST['rows'] - 1) ."',
				cols = '". ($_POST['cols'] - 1) ."',
				orientation = '{$_POST['orientation']}',
				u18 = '{$_POST['u18']}',
				remark = '{$_POST['remark']}',
				text_tl = '{$_POST['text_tl']}',
				text_tc = '{$_POST['text_tc']}',
				text_tr = '{$_POST['text_tr']}',
				text_lt = '{$_POST['text_lt']}',
				text_lc = '{$_POST['text_lc']}',
				text_lb = '{$_POST['text_lb']}',
				text_rt = '{$_POST['text_rt']}',
				text_rc = '{$_POST['text_rc']}',
				text_rb = '{$_POST['text_rb']}',
				text_bl = '{$_POST['text_bl']}',
				text_bc = '{$_POST['text_bc']}',
				text_br = '{$_POST['text_br']}'
				WHERE blockid = '{$_GET['blockid']}'
				");
		}
	// No Break!
	case 4:
		// Continue with seperator definition
		$dsp->NewContent($lang['seating']['block_spacing'], $lang['seating']['spacing_sub']);

		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 1));

		$dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=5&blockid={$_GET['blockid']}");
		$dsp->AddFormSubmitRow('next');
		$dsp->AddBackButton("index.php?mod=seating&action={$_GET['action']}&step=2&blockid={$_GET['blockid']}", 'seating/add');
		$dsp->AddContent();
	break;

	// Seat-Selection
	case 5:
	case 6:
		$dsp->NewContent($lang['seating']['define_seat'], $lang['seating']['def_seat_sub']);
		$dsp->SetForm("index.php?mod=seating&action={$_GET['action']}&step=6&blockid={$_GET['blockid']}", "block");

		$dsp->AddPictureSelectRow('icon', 'ext_inc/seating_symbols', 20, 15, 0, $_POST["icon"], 14, 14, true);
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 2));

		$dsp->AddFormSubmitRow('save');
		$dsp->AddDoubleRow('', $dsp->FetchButton("index.php?mod=seating&action={$_GET['action']}&step=7&blockid={$_GET['blockid']}", 'next'));
		$dsp->AddBackButton("index.php?mod=seating&action={$_GET['action']}&step=4&blockid={$_GET['blockid']}", 'seating/add');
		$dsp->AddContent();
	break;

	// Finished
	case 7:
		$func->confirmation($lang['seating']['c_plan_edit'], 'index.php?mod=seating');
	break;
}
?>
