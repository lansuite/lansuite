<?php

// Errors
if ($_GET['step'] > 1 and (!$_GET['userid'])) $func->error($lang['seating']['e_choose_user'], "index.php?mod=seating&action=seatadmin");
if ($_GET['step'] > 2 and (!$_GET['blockid'])) $func->error($lang['seating']['e_choose_seat'], "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}");

// Exec step10-query
if ($_GET['step'] == 10 and $_GET['quest']) {
	// Assign seat
	$seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);

	// If old owner should get a new seat, jump to step 2 an procede with this user
	if ($_GET['quest'] == 2) {
		$_GET['step'] = 2;
		$_GET['userid'] = $_GET['next_userid'];
	}

	$back_link = '';
	if ($_GET['quest'] == 1) $back_link = 'index.php?mod=seating&action=seatadmin';
	$func->confirmation(str_replace("%USERNAME%", $new_user['username'], $lang['seating']['c_seat_res']) , $back_link);
}

// Select seat and user infos
if ($_GET['blockid'] and isset($_GET['row']) and isset($_GET['col']))
	$seat = $db->query_first("SELECT s.userid, s.status, u.username, u.firstname, u.name FROM {$config["tables"]["seat_seats"]} AS s
		LEFT JOIN {$config["tables"]["user"]} AS u ON s.userid = u.userid
		WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}'");

if ($_GET['userid'])
	$new_user = $db->query_first("SELECT userid, username, firstname, name FROM {$config["tables"]["user"]} WHERE userid = '{$_GET['userid']}'");


switch($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=seating&action=seatadmin", "index.php?mod=seating&action=seatadmin&step=2&userid=", "GROUP BY email");
		$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$mastersearch = new MasterSearch($vars, "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}", "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);   // seat-lang file ?
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 3:
		$dsp->NewContent($lang['seating']['seat_info'], $lang['seating']['seat_info_sub']);

		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0, "index.php?mod=seating&action=seatadmin&step=10&userid={$_GET['userid']}&blockid={$_GET['blockid']}"));

		$dsp->AddBackButton("index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}", 'seating/seatadmin');
		$dsp->AddContent();
	break;

	// Reserve seat - questions
	case 10:
		switch ($seat['status']) {
			case 0:	// Seat unavailable
			case '':
				$func->error($lang['seating']['e_no_seat'], "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}");
			break;

			case 1:	// Seat free, or just marked -> ask if reserve, or mark
			case 3:
				if (!$_GET['quest']) {
					$questionarray = array();
					$linkarray = array();

  				array_push($questionarray, $lang['seating']['q_answ_reserve']);
  				array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=11&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

  				array_push($questionarray, $lang['seating']['q_answ_mark']);
  				array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=12&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

  				array_push($questionarray, $lang['seating']['q_cancel']);
  				array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid={$_GET['blockid']}");
  			
  				$func->multiquestion($questionarray, $linkarray, $lang['seating']['q_reserve_mark']);
        }
			break;

			case 2:	// Seat occupied -> show action selection
				if (!$_GET['quest']) {
					$questionarray = array();
					$linkarray = array();

					array_push($questionarray, str_replace("%USERNAME%", $seat['username'], $lang['seating']['q_res_however']));
					array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=10&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}&quest=1");

					array_push($questionarray, str_replace("%USERNAME%", $seat['username'], $lang['seating']['q_res_howev_2']));
					array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=10&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}&quest=2&next_userid={$seat['userid']}");

					array_push($questionarray, $lang['seating']['q_cancel']);
					array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid={$_GET['blockid']}");

					$func->multiquestion($questionarray, $linkarray, str_replace("%USERNAME%", $seat['username'], str_replace("%FIRSTNAME%", $seat['firstname'], str_replace("%NAME%", $seat['name'], $lang['seating']['q_reserved_by']))));
				}
			break;
		}
	break;

	// Reserve seat
	case 11:
		$seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
		$func->confirmation(str_replace("%USERNAME%", $new_user['username'], $lang['seating']['c_seat_res']), "index.php?mod=seating&action=seatadmin");
	break;

	// Mark seat
	case 12:
		$seat2->MarkSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
		$func->confirmation(str_replace("%USERNAME%", $new_user['username'], $lang['seating']['c_seat_mark2']), "index.php?mod=seating&action=seatadmin");
	break;
}

?>