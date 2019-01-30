<?php

use LanSuite\Module\Seating\Seat2;

$seat2 = new Seat2();

// Errors
if ($_GET['step'] > 1 and (!$_GET['userid'])) {
    $func->error(t('Es wurde kein Benutzer ausgewählt'), "index.php?mod=seating&action=seatadmin");
}
if ($_GET['step'] > 2 and (!$_GET['blockid'])) {
    $func->error(t('Es wurde kein Sitzblock ausgewählt'), "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}");
}

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
    if ($_GET['quest'] == 1) {
        $back_link = 'index.php?mod=seating&action=seatadmin';
    }
    $func->confirmation(t('Der Sitzplatz wurde erfolgreich für %1 reserviert', $new_user['username']), $back_link);
}

// Select seat and user infos
if ($_GET['blockid'] and isset($_GET['row']) and isset($_GET['col'])) {
    $seat = $db->qry_first("
      SELECT
        s.userid,
        s.status,
        u.username,
        u.firstname,
        u.name
      FROM %prefix%seat_seats AS s
      LEFT JOIN %prefix%user AS u ON s.userid = u.userid
      WHERE
        blockid = %int%
        AND row = %string%
        AND col = %string%", $_GET['blockid'], $_GET['row'], $_GET['col']);
}

if ($_GET['userid']) {
    $new_user = $db->qry_first("
      SELECT
        u.userid,
        u.username,
        u.firstname,
        u.name,
        pu.paid
      FROM %prefix%user AS u
      LEFT JOIN %prefix%party_user AS pu ON pu.user_id = u.userid
      WHERE
        userid = %int%
        AND pu.party_id = %int%", $_GET['userid'], $party->party_id);
}

switch ($_GET['step']) {
    default:
        $additional_where = "p.party_id = {$party->party_id} and u.type > 0";
        $current_url = 'index.php?mod=seating&action=seatadmin';
        $target_url = 'index.php?mod=seating&action=seatadmin&step=2&userid=';
        include_once('modules/usrmgr/search_basic_userselect.inc.php');
        break;

    case 2:
        $current_url = "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}";
        $target_url = "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid=";
        include_once('modules/seating/search_basic_blockselect.inc.php');
        break;

    case 3:
        $dsp->NewContent(t('Sitzplatz - Informationen'), t('Fahre mit der Maus über einen Sitzplatz, um weitere Informationen zu erhalten.'));
        $dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0, "index.php?mod=seating&action=seatadmin&step=10&userid={$_GET['userid']}&blockid={$_GET['blockid']}"));
        $dsp->AddBackButton("index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}", 'seating/seatadmin');
        break;

    // Reserve seat - questions
    case 10:
        switch ($seat['status']) {
            // Seat unavailable
            case 0:
            case '':
                $func->error(t('Dieser Sitzplatz existiert nicht'), "index.php?mod=seating&action=seatadmin&step=2&userid={$_GET['userid']}");
                break;

            // Seat free, or just marked -> ask if reserve, or mark
            case 1:
            case 3:
                if (!$_GET['quest']) {
                    $questionarray = array();
                    $linkarray = array();
                    if ($new_user['paid'] == 0) {
                        $markinfo = HTML_NEWLINE . "(Alle markierten Sitzplätze von %1 werden gelöscht, da %1 noch nicht bezahlt hat)";
                    }

                    array_push($questionarray, t('Sitzplatz für %1 reservieren' . HTML_NEWLINE . '(Ein evtl. zuvor für diesen Benutzer reservierter Platz wird freigegeben)', $new_user['username']));
                    array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=11&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

                    array_push($questionarray, t('Sitzplatz für %1 markieren'.$markinfo, $new_user['username']));
                    array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=12&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}");

                    array_push($questionarray, t('Aktion abbrechen. Zurück zum Sitzplan'));
                    array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid={$_GET['blockid']}");
            
                    $func->multiquestion($questionarray, $linkarray, t('Dieser Sitzplatz ist noch frei (bzw. nur markiert)' . HTML_NEWLINE . 'Soll er fest reserviert oder nur markiert werden?'));
                }
                break;

            // Seat occupied -> show action selection
            case 2:
                if (!$_GET['quest']) {
                    // Selected users own seat
                    if ($seat['userid'] == $_GET['userid']) {
                        $func->question(t('Dies ist der Sitzplatz des aktuell ausgewählten Benutzers. Soll der Platz freigegeben werden?'), 'index.php?mod=seating&action=seatadmin&step=20&userid='. $_GET['userid'] .'&blockid='. $_GET['blockid'] .'&row='. $_GET['row'] .'&col='. $_GET['col'], 'index.php?mod=seating&action=seatadmin&step=3&userid='. $_GET['userid'] .'&blockid='. $_GET['blockid']);
                    } else {
                        $questionarray = array();
                        $linkarray = array();

                        array_push($questionarray, t('Dennoch reservieren. %1 hat dadurch anschließend keinen Sitzplatz mehr', $seat['username']));
                        array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=10&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}&quest=1");

                        array_push($questionarray, t('Dennoch reservieren und %1 anschließend einen neuen Sitzplatz zuweisen', $seat['username']));
                        array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=10&userid={$_GET['userid']}&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}&quest=2&next_userid={$seat['userid']}");

                        array_push($questionarray, t('Aktion abbrechen. Zurück zum Sitzplan'));
                        array_push($linkarray, "index.php?mod=seating&action=seatadmin&step=3&userid={$_GET['userid']}&blockid={$_GET['blockid']}");

                        $func->multiquestion($questionarray, $linkarray, t('Dieser Sitzplatz ist aktuell belegt durch %1 (%2 %3)', $seat['username'], $seat['firstname'], $seat['name']));
                    }
                }
                break;
        }
        break;

    // Reserve seat
    case 11:
        $seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
        $func->confirmation(t('Der Sitzplatz wurde erfolgreich für %1 reserviert', $new_user['username']), "index.php?mod=seating&action=seatadmin");
        break;

    // Mark seat
    case 12:
        if ($new_user['paid'] == 0) {
            $seat2->FreeSeatAllMarkedByUser($_GET['userid']);
        }
        $seat2->MarkSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
        $func->confirmation(t('Der Sitzplatz wurde erfolgreich für %1 vorgemerkt', $new_user['username']), "index.php?mod=seating&action=seatadmin");
        break;

    // Free seat
    case 20:
        $seat2->FreeSeat($_GET['blockid'], $_GET['row'], $_GET['col']);
        $func->confirmation(t('Der Sitzplatz wurde wieder freigegeben'), 'index.php?mod=seating&action=seatadmin');
        break;
}
