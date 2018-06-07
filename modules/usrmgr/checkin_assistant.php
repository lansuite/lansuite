<?php

use LanSuite\Module\Seating\Seat2;

$seat2 = new Seat2();

$timestamp    = time();

if (!$party->party_id) {
    $func->information(t('Es gibt keine aktive Party. Bitte setze im Partymanager eine Party aktiv'));
} else {
    // Main-Switch
    switch ($_GET["step"]) {
        // Auswahl: Angemeldet? ja/Nein
        case '':
        case 1:
            unset($_SESSION['quick_signon']);

            $dsp->AddFieldsetStart('Direkt zu folgendem Benutzer springen');
            if ($cfg['sys_barcode_on']) {
                $dsp->AddBarcodeForm("<strong>" . t('Strichcode') . "</strong>", "", "index.php?mod=usrmgr&action=entrance&step=3&userid=");
            }
            $dsp->SetForm('index.php?mod=usrmgr&action=entrance&step=3', 'CheckinAssistantUseridForm');
            $dsp->AddTextFieldRow('userid', t('UserID'), '', '');
            $dsp->AddFormSubmitRow(t('Weiter'));
            $dsp->AddFieldsetEnd();

            $questionarr[1] = t('Bereits <b>angemeldeten Gast einchecken</b>');
            $questionarr[2] = t('Bereits <b>zu einer vergangenen Party angemeldeten Gast einchecken</b>');
            $questionarr[3] = t('Neuer Gast. Einen <b>Account erstellen</b><br /><i>Es wird nur eine E-Mail-Adresse angelegt und ein Passwort automatisch generiert. Alle weiteren Daten gibt der Benutzer beim ersten Einloggen selbst ein.</i>');
            $questionarr[4] = t('Neuer Gast. Einen <b>erweiterten Account erstellen</b><br /><i>Hier liegst du direkt am Einlass alle Benutzerdaten fest.</i>');
            $linkarr[1] = "index.php?mod=usrmgr&action=entrance&step=2&signon=1";
            $linkarr[2] = "index.php?mod=usrmgr&action=entrance&step=2&signon=0";
            $linkarr[3] = "index.php?mod=usrmgr&action=entrance&step=3&quick_signon=1";
            $linkarr[4] = "index.php?mod=usrmgr&action=entrance&step=3&quick_signon=0";
            $func->multiquestion($questionarr, $linkarr, "");
            break;

        // Wenn Angemeldet: Benutzerauswahl
        case 2:
            if ($_GET['signon']) {
                $additional_where = "(!p.checkin OR p.checkout) AND u.type > 0 AND p.party_id = {$party->party_id}";
            } else {
                $additional_where = "u.type > 0 AND (p.party_id != {$party->party_id} OR p.party_id IS NULL)";
            }
            $current_url = 'index.php?mod=usrmgr&action=entrance&step=2&signon='. $_GET['signon'];
            $target_url = 'index.php?mod=usrmgr&action=entrance&step=3&userid=';
            include_once('modules/usrmgr/search_basic_userselect.inc.php');
            break;

        // Benutzerdaten eingeben / ändern
        case 3:
            $cfg['signon_autopw'] = 1;
            $cfg['signon_captcha'] = 0;

            if ($_POST['barcodefield']) {
                $row = $db->qry_first('SELECT userid FROM %prefix%user WHERE barcode = %string%', $_POST['barcodefield']);
                $_GET['userid']=$row['userid'];
            }
                          
            if (!$_GET['userid']) {
                $_GET['userid'] = $_POST['userid'];
            }
            if (!$_POST['paid']) {
                $_POST['paid'] = 2;
            }

            if ($_GET['quick_signon']) {
                $_SESSION['quick_signon'] = $_GET['quick_signon'];
            }
            if ($_SESSION['quick_signon']) {
                $quick_signon = $_SESSION['quick_signon'];
            }

            $dsp->NewContent(t('Benutzer hinzufügen'), t('Um einen Benutzer hinzuzufügen, fülle bitte das folgende Formular vollständig aus.'));

            $row = $db->qry_first('
              SELECT
                pp.price,
                pp.price_text
              FROM %prefix%partys AS p
              LEFT JOIN %prefix%party_prices AS pp ON p.evening_price_id = pp.price_id
              WHERE
                p.party_id = %int%', $party->party_id);
            
            $row2 = $db->qry_first('
              SELECT
                paid,
                price,
                price_text
              FROM %prefix%party_user AS u
              LEFT JOIN %prefix%party_prices AS p ON u.price_id = p.price_id
              WHERE
                u.party_id = %int%
                AND u.user_id = %int%', $party->party_id, $_GET['userid']);
            if ($_GET['mf_step'] != 2 and !$row2['paid']) {
                ($row2['price_text'])? $his_wish = '<br>'. t('Der von ihm gewünschte Preis war: '). $row2['price_text'] .' ('. $row2['price'] .' '. $cfg['sys_currency'] .')' : $his_wish = '';
                $func->information(t('Achtung: Der Benutzer wird mit der nächsten Seite auf Bezahlt gesetzt.') .'<br>'. t('Preis: ') . $row['price_text'] .' ('. $row['price'] .' '. $cfg['sys_currency'] .')'. $his_wish, NO_LINK);
            }

            include_once("modules/usrmgr/add.php");
            if ($AddUserSuccess) {
                if (!$_GET['userid']) {
                    $_GET['userid'] = $mf->insert_id;
                }
                $_GET['step']++;

                $row = $db->qry_first('SELECT evening_price_id FROM %prefix%partys WHERE party_id = %int%', $party->party_id);
        
                if ($row2['paid']) {
                    $db->qry('
                      UPDATE %prefix%party_user
                      SET
                        checkin = NOW()
                      WHERE
                        user_id = %int%
                        AND party_id = %int%', $_GET['userid'], $party->party_id);
                } else {
                    $db->qry('DELETE FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%', $_GET['userid'], $party->party_id);
                    $db->qry('
                      INSERT INTO %prefix%party_user
                      SET
                        user_id = %int%,
                        party_id = %int%,
                        price_id = %int%,
                        checkin = NOW(),
                        paid = 2,
                        paiddate = NOW(),
                        seatcontrol = 0,
                        signondate = NOW()', $_GET['userid'], $party->party_id, $row['evening_price_id']);
                }
            }
            break;
    }

    switch ($_GET["step"]) {
        // Platzpfand prüfen
        case 4:
        // Passwort ausgeben
        case 5:
        // Neuen Sitzplatz auswählen?
        case 6:
            $func->question(t('Willst du diesem Benutzer einen Sitzplatz zuweisen?<br />Er sitzt aktuell auf:<br />%1', $seat2->SeatNameLink($_GET["userid"])), "index.php?mod=usrmgr&action=entrance&step=7&umode=". $_GET["umode"] ."&userid=". $_GET["userid"], "index.php?mod=usrmgr&action=entrance&step=11&umode=". $_GET["umode"] ."&userid=". $_GET["userid"]);
            break;

        // Sitzblock auswählen
        case 7:
            if ($_GET['next_userid']) {
                $seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);
                $func->confirmation("Der Sitzplatz wurde erfolgreich reserviert. Fahre nun mit dem alten Besitzer dieses Sitzplatzes fort", '');
                $_GET['userid'] = $_GET['next_userid'];
            }

            $current_url = "index.php?mod=usrmgr&action=entrance&step=7&umode={$_GET["umode"]}&userid={$_GET["userid"]}";
            $target_url = "index.php?mod=usrmgr&action=entrance&step=8&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid=";
            include_once('modules/seating/search_basic_blockselect.inc.php');
            break;

        // Sitzplatz auswählen
        case 8:
            $dsp->NewContent('Sitzplatz - Informationen', 'Fahre mit der Maus über einen Sitzplatz um weitere Informationen zu erhalten.');

            $dsp->AddDoubleRow('Sitzplatz', '', 'seating');
            $dsp->AddDoubleRow('Benutzer', '', 'name');
            $dsp->AddDoubleRow('Clan', '', 'clan');
            $dsp->AddDoubleRow('IP', '', 'ip');
            $dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0, "index.php?mod=usrmgr&action=entrance&step=9&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}"));

            $dsp->AddBackButton('index.php?mod=seating', 'seating/show');
            break;

        // Belegten Sitzplatz tauschen / löschen?
        case 9:
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

            if ($seat['status'] == 1 or $seat['status'] == 3) {
                $_GET['step'] = 10;
            // Status pre reserverd or free
            } elseif ($seat['status'] == 2) {
                $questionarray = array();
                $linkarray = array();

                array_push($questionarray, "Dennoch reservieren. {$seat['username']} hat dadurch anschließend keinen Sitzplatz mehr");
                array_push($linkarray, "index.php?mod=usrmgr&action=entrance&step=10&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}&row={$_GET['row']}&col={$_GET['col']}");

                array_push($questionarray, "Dennoch reservieren und {$seat['username']} anschließend einen neuen Sitzplatz aussuchen");
                array_push($linkarray, "index.php?mod=usrmgr&action=entrance&step=7&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}&next_userid={$seat['userid']}&row={$_GET['row']}&col={$_GET['col']}");

                array_push($questionarray, 'Aktion abbrechen. Zurück zum Sitzplan');
                array_push($linkarray, "index.php?mod=usrmgr&action=entrance&step=7&umode={$_GET["umode"]}&userid={$_GET["userid"]}&blockid={$_GET["blockid"]}");

                $func->multiquestion($questionarray, $linkarray, "Dieser Sitzplatz ist aktuell belegt durch {$seat['username']} ({$seat['firstname']} {$seat['name']})");
            }
            break;

        case 10:
        case 11:
            break;
    }

    switch ($_GET['step']) {
        case 10:
            $seat2->AssignSeat($_GET['userid'], $_GET['blockid'], $_GET['row'], $_GET['col']);

        // Erfolgsmeldung zeigen
        case 11:
            $func->confirmation(t('Der Benutzer <b>%1</b> wurde erfolgreich eingecheckt', $user_data["username"]), "index.php?mod=usrmgr&action=entrance");
            break;
    }
}
