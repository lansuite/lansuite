<?php

switch($_GET['step']) {
	default:
    include_once('modules/seating/search.inc.php');
	break;

	// Show seatplan
	case 2:
		$dsp->NewContent(t('Sitzplatz - Informationen'), t('Fahren Sie mit der Maus über einen Sitzplatz, um weitere Informationen zu erhalten.'));
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0));

		$dsp->AddBackButton('index.php?mod=seating', 'seating/show');
		$dsp->AddContent();
	break;


	// Reserve free seat
	case 10:
		$user_data = $db->query_first("SELECT paid FROM {$config['tables']['party_user']}
      WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");
		$seat_user = $db->query_first("SELECT status FROM {$config["tables"]["seat_seats"]}
      WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}'");

		// Check paid
		if (!$user_data['paid'] and $cfg['seating_paid_only'] and !$cfg['seating_not_paid_mark']) $func->information(t('Sie müssen zuerst für diese Party bezahlen, bevor Sie sich einen Sitzplatz reservieren dürfen.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check seat availability
	  elseif ($seat_user['status'] == 2) $func->error(t('Dieser Sitzplatz ist bereits vergeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
	    
		// Check seat availability
	  elseif ($seat_user['status'] == 0 or $seat_user['status'] > 9) $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
		
		// No errors
		else {
			// Get number of marked seats of this user
			$marked_seats = $db->query_first("SELECT count(*) AS anz FROM {$config['tables']['seat_seats']} AS s
				LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b. blockid
				WHERE s.userid = {$auth['userid']} AND s.status = 3 AND b.party_id = {$party->party_id}");

			// Check if not paid user has allready marked one seat
      if (!$user_data['paid'] and $marked_seats['anz'] >= 1) $func->information(t('Solange Sie nicht für diese Party bezahlt haben, dürfen Sie nur einen Sitz vormerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
      else {
				$questionarray = array();
				$linkarray = array();

				$row = $db->query_first("SELECT seatid FROM {$config["tables"]["seat_seats"]} WHERE blockid = '{$_GET['blockid']}' AND status = 2 AND userid = '{$auth['userid']}'");
				if ($user_data['paid']) {
					// Reserve seat for myselfe
					if ($row['seatid']) {
						array_push($questionarray, t('Sie haben bereits einen Sitzplatz reserviert. Möchten Sie Ihren Sitzplatz wieder frei geben und statt dessen diesen Platz reservieren?'));
						array_push($linkarray, "index.php?mod=seating&action=show&step=11&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					// Change my seat, if I allready have one
					} else {
						array_push($questionarray, t('Diesen Sitzplatz für mich reservieren'));
						array_push($linkarray, "index.php?mod=seating&action=show&step=11&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					}
					// If not reached the maximum of marks
					if ($marked_seats['anz'] < $cfg['seating_max_marks']) {
						array_push($questionarray, t('Diesen Sitzplatz für einen Freund vormerkenHTML_NEWLINE(Eine Vormekung kann von jedem überschrieben werden. Erst nach dem Bezahlen ist eine feste Reservierung möglich)'));
						array_push($linkarray, "index.php?mod=seating&action=show&step=12&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					}
					// Delete mark, if Admin
					if ($auth['type'] > 1) {
						array_push($questionarray, t('Möchten Sie als Admin diese Vormerkung entfernen?'));
						array_push($linkarray, "index.php?mod=seating&action=show&step=31&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
					}
				// Mark seat for myselfe (if not paid)
				} else {
					array_push($questionarray, t('Diesen Sitzplatz für mich vormerkenHTML_NEWLINE(Eine Vormekung kann von jedem überschrieben werden. Erst nach dem Bezahlen ist eine feste Reservierung möglich)'));
					array_push($linkarray, "index.php?mod=seating&action=show&step=12&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
				}
				array_push($questionarray, t('Aktion abbrechen. Zurück zum Sitzplan'));
				array_push($linkarray, "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

				$func->multiquestion($questionarray, $linkarray, t('Dieser Sitzplatz ist momentan noch frei'));
			}
		}
	break;

	// Reserve seat for me
	case 11:
		$user_data = $db->query_first("SELECT paid, price_id FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");

		$block_data = $db->query_first("SELECT group_id, price_id FROM {$config['tables']['seat_block']} WHERE blockid = {$_GET['blockid']}");

		$seat_user = $db->query_first("SELECT status FROM {$config["tables"]["seat_seats"]}
            WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}'");

		// Check paid
		if (!$user_data['paid'] and $cfg['seating_paid_only']) $func->information(t('Sie müssen zuerst für diese Party bezahlen, bevor Sie sich einen Sitzplatz reservieren dürfen.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check Group ID
		elseif ($block_data['group_id'] and $auth['group_id'] != $block_data['group_id']) $func->information(t('Sie gehören nicht der richtigen Gruppe an, um in diesem Block einen Sitz zu reservieren'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check Price ID
                elseif ($block_data['price_id'] and $user_data['price_id'] != $block_data['price_id']) $func->information(t('Sie sind nicht dem richtigen Eintrittspreis zugeordnet, um in diesem Block einen Sitz zu reservieren'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
		
		// Check seat availability
    elseif ($seat_user['status'] == 2)  $func->error(t('Dieser Sitzplatz ist bereits vergeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

    // Check is a seat 
    elseif ($seat_user['status'] == 0 or $seat_user['status'] > 9)  $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
	    
		// No errors
		else {
			$seat2->AssignSeat($auth['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
			$func->confirmation(t('Der Sitzplatz wurde erfolgreich reserviert'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
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

    $user_party = $db->query_first("SELECT user_id FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");

		// Check Signed on
		if (!$user_party['user_id']) $func->information(t('Nur zur Party angemeldete Benutzer dürfen Sitzplätze vormerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check paid
		elseif (!$user_data['paid'] and $cfg['seating_paid_only'] and !$cfg['seating_not_paid_mark']) $func->information(t('Sie müssen zuerst für diese Party bezahlen, bevor Sie sich einen Sitzplatz reservieren dürfen.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check seat availability
		elseif ($seat_user['userid']) $func->error(t('Dieser Sitzplatz ist bereits vergeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check if not paid user has allready marked one seat
    elseif (!$user_data['paid'] and $marked_seats['anz'] >= 1) $func->information(t('Solange Sie nicht für diese Party bezahlt haben, dürfen Sie nur einen Sitz vormerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// Check number of marked seats of this user
		elseif ($user_data['paid'] and $marked_seats['anz'] >= $cfg['seating_max_marks']) $func->information(t('Sie haben bereits das Maximum an Sitzen reserviert'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

    // Check is a seat 
    elseif ($seat_user['status'] > 9) $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		// No errors
		else {
			$seat2->MarkSeat($auth['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
			$func->confirmation(t('Der Sitzplatz wurde erfolgreich reserviert'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
		}
	break;


	// Release my seat
	case 20:
		$questionarray = array();
		$linkarray = array();

		array_push($questionarray, t('Meinen Sitzplatz wieder freigeben'));
		array_push($linkarray, "index.php?mod=seating&action=show&step=21&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

		array_push($questionarray, t('Aktion abbrechen. Zurück zum Sitzplan'));
		array_push($linkarray, "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

		$func->multiquestion($questionarray, $linkarray, t('Dieser Sitzplatz ist momentan für Sie reserviert'));
	break;

	// Release seat
	case 21:
		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = 0, status = 1
			WHERE blockid = '{$_GET['blockid']}' AND row = '{$_GET['row']}' AND col = '{$_GET['col']}' AND userid = '{$auth['userid']}'");

		$func->confirmation(t('Der Sitzplatz wurde erfolgreich freigegeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
	break;

	// Change reserved to mark
	case 22:
	break;


	// Free seat as admin (question)
	case 30:
    if ($auth['type'] > 1) {
      $func->question(t('Sind Sie sicher, dass Sie diesen Sitzplatz wieder freigeben möchten?'), "index.php?mod=seating&action=show&step=31&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}", "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
    }
	break;
	
	// Free seat as admin
	case 31:
    if ($auth['type'] > 1) {
			$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = 0, status = 1
			WHERE blockid = {$_GET['blockid']} AND row = {$_GET['row']} AND col = {$_GET['col']}");

		$func->confirmation(t('Der Sitzplatz wurde erfolgreich freigegeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
    }
	break;
}
?>