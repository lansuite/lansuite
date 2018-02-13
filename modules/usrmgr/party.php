<?php

include_once("modules/usrmgr/class_usrmgr.php");
$usrmgr = new UsrMgr();

include_once("modules/seating/class_seat.php");
$seat2 = new seat2();

function PartyMail()
{
    global $usrmgr, $func, $mail, $auth;

    $usrmgr->WriteXMLStatFile();

    if ($_POST['sendmail'] or $auth['type'] < 2) {
        if ($usrmgr->SendSignonMail(1)) {
            $func->confirmation(t('Eine Bestätigung der Anmeldung wurde an deine E-Mail-Adresse gesendet.'), NO_LINK);
        } else {
            $func->error(t('Es ist ein Fehler beim Versand der Informations-E-Mail aufgetreten.'). $mail->error, NO_LINK);
        }
    }

    return true;
}

if ($party->count == 0) {
    $func->information(t('Aktuell sind keine Partys geplant.'), 'index.php?mod='. $_GET['mod']);
} else {
    if ($_GET['user_id'] == $auth['userid'] or $auth['type'] >= 2) {
        function ChangeAllowed($id)
        {
            global $db, $row, $lang, $func, $auth, $seat2;

      // Do not allow changes, if party is over
            if ($row['enddate'] < time()) {
                return t('Du kannst dich nicht mehr zu dieser Party an-, oder abmelden, da sie bereits vorüber ist');
            }

      // Signon started?
            if ($row['sstartdate'] > time()) {
                return t('Die Anmeldung öffnet am'). HTML_NEWLINE .'<strong>'. $func->unixstamp2date($row['sstartdate'], 'daydatetime'). '</strong>';
            }

      // Signon ended?
            if ($row['senddate'] < time() and $auth['type'] < 2) {
                return t('Die Anmeldung ist beendet seit'). HTML_NEWLINE .'<strong>'. $func->unixstamp2date($row['senddate'], 'daydatetime'). '</strong>';
            }

      // Do not allow changes, if user has paid
            if ($auth['type'] <= 1) {
                $row2 = $db->qry_first("SELECT paid FROM %prefix%party_user WHERE party_id = %int% AND user_id = %int%", $_GET['party_id'], $id);
                if ($row2['paid']!= 0) {
                    return t('Du bist für diese Party bereits auf bezahlt gesetzt. Bitte einen Admin dich auf "nicht bezahlt" zu setzen, bevor du dich abmeldest');
                }
            }

      // Check age
            if (isset($_POST['InsertControll1']) && $_POST['InsertControll1']) {
                $res = $db->qry("SELECT %prefix%partys.minage FROM %prefix%user, %prefix%partys
                            WHERE %prefix%partys.party_id = %int%
                                AND %prefix%user.userid = %int%
                                AND DATEDIFF(DATE_SUB(%prefix%partys.startdate, INTERVAL %prefix%partys.minage YEAR), %prefix%user.birthday) < 0
                                AND %prefix%partys.minage > 0", $_GET['party_id'], $id);
                      $minage = $db->fetch_array($res);
                      $db->free_result($res);
                if (isset($minage['minage'])) {
                    return t('Du must mindestens %1 Jahre alt sein um an dieser Party teilnehmen zu d&uuml;rfen!', $minage['minage']);
                }
            }

            $row2 = $db->qry_first("SELECT paid FROM %prefix%party_user WHERE party_id = %int% AND user_id = %int%", $_GET['party_id'], $id);

            // Free seats if the user hasn't paid already
            if ($row2['paid'] == 0) {
                $seat2->FreeSeatAllMarkedByUser($id);
            }

            return false;
        }


    // Show Upcomming
        $MFID = 1;

        $res = $db->qry("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM %prefix%partys WHERE UNIX_TIMESTAMP(enddate) >= UNIX_TIMESTAMP(NOW()) ORDER BY startdate");
        while ($row = $db->fetch_array($res)) {
            if ($_GET['mf_step'] != 2 or $row['party_id'] == $_GET['party_id']) {
                $dsp->AddFieldsetStart($row['name'] .' ('. $func->unixstamp2date($row['startdate'], 'datetime') .' - '. $func->unixstamp2date($row['enddate'], 'datetime') .')');
                $mf = new masterform($MFID);
                $mf->AdditionalKey = 'party_id = '. $row['party_id'];

        // Signon
                $mf->AddInsertControllField = t('Angemeldet').'|'.t('Wenn dieses Häckchen gesetzt ist, bist du zu dieser Party angemeldet');
                $mf->AddChangeCondition = 'ChangeAllowed';

        // Paid
                if ($auth['type'] >= 2) {
                    $selections = array();
                    $selections['0'] = t('Nicht bezahlt');
                    $selections['1'] = t('Bezahlt');
                    $mf->AddField(t('Bezahltstatus'), 'paid', IS_SELECTION, $selections);
                } elseif ($cfg['signon_autopaid']) {
                    $mf->AddFix('paid', '1');
                }

                if ($cfg['signon_autopaid'] or $_POST['paid']) {
                    $mf->AddFix('paiddate', 'NOW()');
                }

        // Prices
                $selections = array();
                $res2 = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% AND requirement <= %string%", $row['party_id'], $auth['type']);
                while ($row2 = $db->fetch_array($res2)) {
                    $selections[$row2['price_id']] = $row2['price_text'] .' ['. $row2['price'] .' '. $cfg['sys_currency'] .']&nbsp;&nbsp;'.t('Gültig bis : ').date_format(date_create($row2['enddate']), 'd.m.Y');
                }
                if ($selections) {
                    $mf->AddField(t('Eintrittspreis'), 'price_id', IS_SELECTION, $selections, FIELD_OPTIONAL);
                } else {
                    $mf->AddField(t('Eintrittspreis'), 'price_id', IS_TEXT_MESSAGE, t('Für diese Party wurden keine Preise definiert'));
                }
                $db->free_result($res2);

                if ($cfg['signon_autocheckin']) {
                    $mf->AddFix('checkin', 'NOW()');
                }

        #if ($auth['type'] >= 2) {
          //$mf->AddField('Seatcontrol', 'seatcontrol', IS_TEXT_MESSAGE, '', FIELD_OPTIONAL);
          #$mf->AddField(t('Bezahltdatum'), 'paiddate', '', '', FIELD_OPTIONAL);
          #$mf->AddField(t('Eingecheckt'), 'checkin', '', '', FIELD_OPTIONAL);
          #$mf->AddField(t('Ausgecheckt'), 'checkout', '', '', FIELD_OPTIONAL);
          #$mf->AddField(t('Anmeldedatum'), 'signondate', '', '', FIELD_OPTIONAL);
        #}
        #else
                $mf->AddFix('signondate', 'NOW()');

                if ($auth['type'] >= 2) {
                    $mf->AddField(t('Mail versenden?') .'|'. t('Den Benutzer per Mail über die Änderung informieren'), 'sendmail', 'tinyint(1)', '', FIELD_OPTIONAL);
                }
                $mf->SendButtonText = 'An-/Abmelden';

                $mf->AdditionalDBUpdateFunction = 'PartyMail';
                $mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&party_id='. $row['party_id'], 'party_user', 'user_id', $_GET['user_id']);
                $dsp->AddFieldsetEnd();
            } else {
                // Fucking bad Bugfix. $mf_number is a Globalvar in Masterform
                $mf_number++;
            }
            $MFID++;
        }
        $db->free_result($res);


    // ShowHistory
        $dsp->AddFieldsetStart(t('Vergangene Partys'));
        $res = $db->qry("SELECT
          p.*
        , pu.user_id
        , pu.paid
        , UNIX_TIMESTAMP(pu.checkin) AS checkin
        , UNIX_TIMESTAMP(pu.checkout) AS checkout
        , UNIX_TIMESTAMP(p.enddate) AS enddate
        , UNIX_TIMESTAMP(p.sstartdate) AS sstartdate
        , UNIX_TIMESTAMP(p.senddate) AS senddate
        , UNIX_TIMESTAMP(p.startdate) AS startdate
      FROM %prefix%partys AS p
      LEFT JOIN %prefix%party_user AS pu ON p.party_id = pu.party_id
      WHERE UNIX_TIMESTAMP(p.enddate) < UNIX_TIMESTAMP(NOW()) AND (pu.user_id = %int% OR pu.user_id IS NULL)
      ORDER BY p.startdate", $_GET['user_id']);
        while ($row = $db->fetch_array($res)) {
            $text = '';
            if ($row['user_id']) {
                $text .= t('Du warst angemeldet');
                if ($row['paid'] == 0) {
                    $text .= t(', aber hattest nicht bezahlt');
                }
                if ($row['paid']) {
                    $text .= t(' und hattest bezahlt');
                }

                if ($row['price_id']) {
                    $text .= '('. $row['price_id'] .')';
                }
                $text .= '.'. HTML_NEWLINE;

                if ($row['checkin'] and $row['checkin'] != '0000-00-00 00:00:00') {
                    $text .= t('Eingecheckt: '). $func->unixstamp2date($row['checkin'], 'datetime') .' ';
                }
                if ($row['checkout'] and $row['checkout'] != '0000-00-00 00:00:00') {
                    $text .= t('Ausgecheckt: ').$func->unixstamp2date($row['checkout'], 'datetime');
                }
            } else {
                $text .= t('Du warst nicht angemeldet');
            }
            $dsp->AddDoubleRow($row['name'] .' ('. $func->unixstamp2date($row['startdate'], 'datetime') .' - '. $func->unixstamp2date($row['enddate'], 'datetime') .')', $text);
        }
        $db->free_result($res);
        $dsp->AddFieldsetEnd();
    } else {
        $func->information('ACCESS_DENIED', '');
    }

    $dsp->AddBackButton('index.php?mod='. $_GET['mod']);
    $dsp->AddContent();
}
