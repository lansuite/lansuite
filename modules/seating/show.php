<?php

switch($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, 'index.php?mod=seating&action=show', 'index.php?mod=seating&action=show&step=2&blockid=', '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	// Show seatplan
	case 2:
		$dsp->NewContent($lang['seating']['seat_info'], $lang['seating']['seat_info_sub']);
/*
		$dsp->AddDoubleRow($lang['seating']['seating'] . HTML_SPACE, '', 'seating');
		$dsp->AddDoubleRow($lang['seating']['user'] . HTML_SPACE,    '', 'name');
		$dsp->AddDoubleRow($lang['seating']['clan'] . HTML_SPACE,    '', 'clan');
		$dsp->AddDoubleRow($lang['seating']['ip'] . HTML_SPACE,      '', 'ip');
*/		
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0));

		$dsp->AddBackButton('index.php?mod=seating', 'seating/show');
		$dsp->AddContent();
	break;


	// Reserve free seat
	case 10:
		// Check paid
		$user_data = $db->query_first("SELECT paid FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");
		if (!$user_data['paid'] and $cfg['seating_paid_only'] and !$cfg['seating_not_paid_mark']) $func->information($lang['seating']['i_not_paid2'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
		
		// No errors
		else {
			// Get number of marked seats of this user
			$marked_seats = $db->query_first("SELECT count(*) AS anz FROM {$config['tables']['seat_seats']} AS s
				LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b. blockid
				WHERE s.userid = {$auth['userid']} AND s.status = 3 AND b.party_id = {$party->party_id}");

			// Check if not paid user has allready marked one seat
      if (!$user_data['paid'] and $marked_seats['anz'] >= 1) $func->information($lang['seating']['e_max_marked_not_paid'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
      else {
				$questionarray = array();
				$linkarray = array();

				$row = $db->query_first("SELECT seatid FROM {$config["tables"]["seat_seats"]} WHERE blockid = '{$_GET['blockid']}' AND status = 2 AND userid = '{$auth['userid']}'");
				if ($user_data['paid']) {
					// Reserve seat for myselfe
					if ($row['seatid']) {
						array_push($questionarray, $lang['seating']['q_change_seat']);
						array_push($linkarray, "index.php?mod=seating&action=show&step=11&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					// Change my seat, if I allready have one
					} else {
						array_push($questionarray, $lang['seating']['reserve_seat']);
						array_push($linkarray, "index.php?mod=seating&action=show&step=11&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					}
					// If not reached the maximum of marks
					if ($marked_seats['anz'] < $cfg['seating_max_marks']) {
						array_push($questionarray, $lang['seating']['mark_seat']);
						array_push($linkarray, "index.php?mod=seating&action=show&step=12&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					}
				// Mark seat for myselfe (if not paid)
				} else {
					array_push($questionarray, $lang['seating']['mark_my_seat']);
					array_push($linkarray, "index.php?mod=seating&action=show&step=12&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
				}
				array_push($questionarray, $lang['seating']['q_cancel']);
				array_push($linkarray, "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

				$func->multiquestion($questionarray, $linkarray, $lang['seating']['unfilled_seat']);
			}
		}
	break;

	// Reserve seat for me
	case 11:
		$user_data = $db->query_first("SELECT paid FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");

		$seat_user = $db->query_first("SELECT status FROM {$config["tables"]["seat_seats"]}
            WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}'");

		// Check paid
		if (!$user_data['paid'] and $cfg['seating_paid_only']) $func->information($lang['seating']['i_not_paid2'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check seat availability
	    elseif ($seat_user['status'] == 2)  $func->error($lang['seating']['e_assigned'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// No errors
		else {
			$seat2->AssignSeat($auth['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
			$func->confirmation($lang['seating']['c_seat_res2'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
		}
	break;

	// Mark seat for friend (or myselfe, if not paid)
	case 12:
		$marked_seats = $db->query_first("SELECT count(*) AS anz FROM {$config['tables']['seat_seats']} AS s
			LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b. blockid
			WHERE s.userid = {$auth['userid']} AND s.status = 3 AND b.party_id = {$party->party_id}");

		$user_data = $db->query_first("SELECT paid FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");

		$seat_user = $db->query_first("SELECT userid FROM {$config["tables"]["seat_seats"]}
			WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}'");

		// Check paid
		if (!$user_data['paid'] and $cfg['seating_paid_only'] and !$cfg['seating_not_paid_mark']) $func->information($lang['seating']['i_not_paid2'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check seat availability
		elseif ($seat_user['userid']) $func->error($lang['seating']['e_assigned'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check if not paid user has allready marked one seat
    elseif (!$user_data['paid'] and $marked_seats['anz'] >= 1) $func->information($lang['seating']['e_max_marked_not_paid'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check number of marked seats of this user
		elseif ($user_data['paid'] and $marked_seats['anz'] >= $cfg['seating_max_marks']) $func->information($lang['seating']['e_max_marked'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// No errors
		else {
			$seat2->MarkSeat($auth['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
			$func->confirmation($lang['seating']['c_seat_res2'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
		}
	break;


	// Release my seat
	case 20:
		$questionarray = array();
		$linkarray = array();

		array_push($questionarray, $lang['seating']['release_seat']);
		array_push($linkarray, "index.php?mod=seating&action=show&step=21&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

		array_push($questionarray, $lang['seating']['q_cancel']);
		array_push($linkarray, "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		$func->multiquestion($questionarray, $linkarray, $lang['seating']['confim_reserv']);
	break;

	// Release seat
	case 21:
		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = 0, status = 1
			WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}' AND userid = '{$auth['userid']}'");

		$func->confirmation($lang['seating']['c_release'], "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
	break;

	// Change reserved to mark
	case 22:
	break;
}
?>