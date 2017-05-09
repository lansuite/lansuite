<?php

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

switch ($_GET['step']) {
    default:
    #include_once('modules/seating/search.inc.php');
    
        $row = $db->qry_first('SELECT blockid FROM %prefix%seat_block
      WHERE party_id = %int%', $party->party_id);
        $_GET['blockid'] = $row['blockid'];

    // Show seatplan
    case 2:
        $dsp->NewContent(t('Sitzplatz - Informationen'), t('Fahre mit der Maus über einen Sitzplatz, um weitere Informationen zu erhalten.'));

        $current_url = 'index.php?mod=seating';
        include_once('modules/seating/search_basic_blockselect.inc.php');

        $dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0));
        $dsp->AddBackButton('index.php?mod=seating', 'seating/show');
        $dsp->AddContent();
        break;


    // Reserve free seat
    case 10:
        $user_data = $db->qry_first("SELECT paid FROM %prefix%party_user
      WHERE user_id = %int% AND party_id = %int%", $auth['userid'], $party->party_id);
        $seat_user = $db->qry_first("SELECT status FROM %prefix%seat_seats
      WHERE blockid = %int% AND row = %string% AND col = %string%", $_GET['blockid'], $_GET['row'], $_GET['col']);

        // Check paid
        if (!$user_data['paid'] and $cfg['seating_paid_only'] and !$cfg['seating_not_paid_mark']) {
            $func->information(t('Du musst zuerst für diese Party bezahlen, bevor du dir einen Sitzplatz reservieren darfst.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check seat availability
        elseif ($seat_user['status'] == 2) {
            $func->error(t('Dieser Sitzplatz ist bereits vergeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check seat availability
        elseif ($seat_user['status'] == 0 or $seat_user['status'] > 9) {
            $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // No errors
        else {
            // Get number of marked seats of this user
            $marked_seats = $db->qry_first("SELECT count(*) AS anz FROM %prefix%seat_seats AS s
    LEFT JOIN %prefix%seat_block AS b ON s.blockid = b. blockid
    WHERE s.userid = %int% AND s.status = 3 AND b.party_id = %int%", $auth['userid'], $party->party_id);

            // Check if not paid user has allready marked one seat
            if (!$user_data['paid'] and $marked_seats['anz'] >= 1) {
                $func->information(t('Solange du nicht für diese Party bezahlt hast, darfst du nur einen Sitz vormerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
            } else {
                $questionarray = array();
                $linkarray = array();

                $row = $db->qry_first("SELECT seatid FROM %prefix%seat_seats WHERE blockid = %int% AND status = 2 AND userid = %int%", $_GET['blockid'], $auth['userid']);
                if ($user_data['paid']) {
                    // Reserve seat for myselfe
                    if ($row['seatid']) {
                        array_push($questionarray, t('Du hast bereits einen Sitzplatz reserviert. Möchtest du deinen Sitzplatz wieder frei geben und statt dessen diesen Platz reservieren?'));
                        array_push($linkarray, "index.php?mod=seating&action=show&step=11&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
                    // Change my seat, if I allready have one
                    } else {
                        array_push($questionarray, t('Diesen Sitzplatz für mich reservieren'));
                        array_push($linkarray, "index.php?mod=seating&action=show&step=11&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
                    }
                          // If not reached the maximum of marks
                    if ($marked_seats['anz'] < $cfg['seating_max_marks']) {
                          array_push($questionarray, t('Diesen Sitzplatz für einen Freund vormerken<br />(Eine Vormekung kann von jedem überschrieben werden. Erst nach dem Bezahlen ist eine feste Reservierung möglich)'));
                          array_push($linkarray, "index.php?mod=seating&action=show&step=12&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
                    }
                          // Clanadmins can reserve seats for paid clan-members
                    if ($auth['clanadmin']) {
                          $res = $db->qry("SELECT u.userid, u.username FROM %prefix%user AS u LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
              WHERE u.clanid = %int% AND u.userid != %int% AND p.paid AND p.party_id = %int%", $auth['clanid'], $auth['userid'], $party->party_id);
                        while ($row = $db->fetch_array($res)) {
                                    array_push($questionarray, t('Diesen Sitzplatz für mein bezahltes Clan-Mitglied %1 reservieren', $row['username']));
                                    array_push($linkarray, "index.php?mod=seating&action=show&step=13&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}&userid={$row['userid']}");
                        }
                          $db->free_result($res);
                    }
                          // Delete mark, if Admin
                    if ($auth['type'] > 1 and $seat_user['status'] == 3) {
                          array_push($questionarray, t('Möchtest du als Admin diese Vormerkung entfernen?'));
                          array_push($linkarray, "index.php?mod=seating&action=show&step=31&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
                    }
                      // Mark seat for myselfe (if not paid)
                } else {
                    array_push($questionarray, t('Diesen Sitzplatz für mich vormerken<br />(Eine Vormekung kann von jedem überschrieben werden. Erst nach dem Bezahlen ist eine feste Reservierung möglich)'));
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
        $_GET['userid'] = $auth['userid'];
  // no break!
  
    // Reserve seat for clan-member
    case 13:
        $user = $db->qry_first("SELECT group_id FROM %prefix%user WHERE userid = %int%", $_GET['userid']);
        $user_data = $db->qry_first("SELECT paid, price_id FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%", $_GET['userid'], $party->party_id);
        $block_data = $db->qry_first("SELECT group_id, price_id FROM %prefix%seat_block WHERE blockid = %int%", $_GET['blockid']);
        $seat_user = $db->qry_first("SELECT status FROM %prefix%seat_seats
            WHERE blockid = %int% AND row = %string% AND col = %string%", $_GET['blockid'], $_GET['row'], $_GET['col']);

        // Check paid
        if (!$user_data['paid'] and $cfg['seating_paid_only']) {
            $func->information(t('Du musst zuerst für diese Party bezahlen, bevor du dir einen Sitzplatz reservieren darfst.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check Group ID
        elseif ($block_data['group_id'] and $user['group_id'] != $block_data['group_id']) {
            $func->information(t('Du gehörst nicht der richtigen Gruppe an, um in diesem Block einen Sitz zu reservieren'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check Price ID
        elseif ($block_data['price_id'] and $user_data['price_id'] != $block_data['price_id']) {
            $func->information(t('Du bist nicht dem richtigen Eintrittspreis zugeordnet, um in diesem Block einen Sitz zu reservieren'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check seat availability
        elseif ($seat_user['status'] == 2) {
            $func->error(t('Dieser Sitzplatz ist bereits vergeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check is a seat
        elseif ($seat_user['status'] == 0 or $seat_user['status'] > 9) {
            $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // No errors
        else {
            $seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
            $func->confirmation(t('Der Sitzplatz wurde erfolgreich reserviert'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        }
        break;

    // Mark seat for friend (or myselfe, if not paid)
    case 12:
        $marked_seats = $db->qry_first("SELECT count(*) AS anz FROM %prefix%seat_seats AS s
   LEFT JOIN %prefix%seat_block AS b ON s.blockid = b. blockid
   WHERE s.userid = %int% AND s.status = 3 AND b.party_id = %int%", $auth['userid'], $party->party_id);

        $user_data = $db->qry_first("SELECT paid FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%", $auth['userid'], $party->party_id);

        $seat_user = $db->qry_first("SELECT userid FROM %prefix%seat_seats
   WHERE blockid = %int% AND row = %string% AND col = %string%", $_GET['blockid'], $_GET['row'], $_GET['col']);
        
        $block_data = $db->qry_first("SELECT group_id, price_id FROM %prefix%seat_block WHERE blockid = %int%", $_GET['blockid']);
        $user_party = $db->qry_first("SELECT user_id FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%", $auth['userid'], $party->party_id);

        // Check Signed on
        if (!$user_party['user_id']) {
            $func->information(t('Nur zur Party angemeldete Benutzer dürfen Sitzplätze vormerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check paid
        elseif (!$user_data['paid'] and $cfg['seating_paid_only'] and !$cfg['seating_not_paid_mark']) {
            $func->information(t('Du musst zuerst für diese Party bezahlen, bevor du dich einen Sitzplatz reservieren darfst.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check Group ID
        elseif ($block_data['price_id'] and $user_data['price_id'] != $block_data['price_id']) {
            $func->information(t('Du bist nicht dem richtigen Eintrittspreis zugeordnet, um in diesem Block einen Sitz vorzumerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check seat availability
        elseif ($seat_user['userid']) {
            $func->error(t('Dieser Sitzplatz ist bereits vergeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check if not paid user has allready marked one seat
        elseif (!$user_data['paid'] and $marked_seats['anz'] >= 1) {
            $questionarray = array();
            $linkarray = array();

            array_push($questionarray, t('Diesen Platz für mich vorreservieren, meinen alten Platz freigeben.'));
            array_push($linkarray, "index.php?mod=seating&action=show&step=22&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");
        
            array_push($questionarray, t('Aktion abbrechen. Zurück zum Sitzplan'));
            array_push($linkarray, "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        
        
            $func->multiquestion($questionarray, $linkarray, t('Solange du nicht für diese Party bezahlt hast, darfst du nur einen Sitz vormerken'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check number of marked seats of this user
        elseif ($user_data['paid'] and $marked_seats['anz'] >= $cfg['seating_max_marks']) {
            $func->information(t('Du hast bereits das Maximum an Sitzen reserviert'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // Check is a seat
        elseif ($seat_user['status'] > 9) {
            $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        } // No errors
        else {
            $seat2->MarkSeat($auth['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
            $func->confirmation(t('Der Sitzplatz wurde erfolgreich vorgemerkt'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
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

        $func->multiquestion($questionarray, $linkarray, t('Dieser Sitzplatz ist momentan für dich reserviert'));
        break;

    // Release seat
    case 21:
        $seat2->FreeSeat($_GET['blockid'], $_GET['row'], $_GET['col']);
        $func->confirmation(t('Der Sitzplatz wurde erfolgreich freigegeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        break;

    // Release seat and reserve new
    case 22:
        $seat2->FreeSeatAllMarkedByUser($auth['userid']);
        $seat2->MarkSeat($auth['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
        $func->confirmation(t('Der alte Sitzplatz wurde freigegeben und der neue erfolgreich vorgemerkt.'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        break;
    
    // Free seat as admin (question)
    case 30:
        if ($auth['type'] > 1) {
            $seatingUser = $db->qry_first("SELECT s.userid, u.username FROM %prefix%seat_seats AS s
    			 INNER JOIN %prefix%user AS u ON u.userid = s.userid
  				 WHERE blockid = %int% AND row = %string% AND col = %string%", $_GET['blockid'], $_GET['row'], $_GET['col']);
            
                $questionarray = array();
                $linkarray = array();

                array_push($questionarray, t('Diesen Sitzplatz wieder freigeben'));
                array_push($linkarray, "index.php?mod=seating&action=show&step=31&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

                array_push($questionarray, t('Für %1 einen anderen Sitzplatz bestimmen (umsetzen)', $seatingUser['username']));
                array_push($linkarray, "index.php?mod=seating&action=show&step=32&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}&userid={$seatingUser['userid']}");

                array_push($questionarray, t('Aktion abbrechen. Zurück zum Sitzplan'));
                array_push($linkarray, "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");

                $func->multiquestion($questionarray, $linkarray, t('Dieser Sitzplatz ist momentan für %1 reserviert. Du kannst:', $seatingUser['username']));
        }
        break;
    
    // Free seat as admin
    case 31:
        if ($auth['type'] > 1) {
            $seat2->FreeSeat($_GET['blockid'], $_GET['row'], $_GET['col']);
            $func->confirmation(t('Der Sitzplatz wurde erfolgreich freigegeben'), "index.php?mod=seating&action=show&step=2&blockid={$_GET['blockid']}");
        }
        break;
    
    //Umsetzen als Admin
    case 32:
        if ($auth['type'] > 1) {
            $current_url = "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}";
            $target_url = "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid=";
            include_once('modules/seating/search_basic_blockselect.inc.php');
        }
}
