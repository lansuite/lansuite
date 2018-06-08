<?php

$mail = new \LanSuite\Module\Mail\Mail();
$usrmgr = new \LanSuite\Module\UsrMgr\UserManager($mail);

$seat2 = new \LanSuite\Module\Seating\Seat2();

if ($party->count == 0) {
    $func->information(t('Aktuell sind keine Partys geplant.'), 'index.php?mod='. $_GET['mod']);
} else {
    if ($_GET['user_id'] == $auth['userid'] or $auth['type'] >= 2) {
        // Show Upcomming
        $MFID = 1;

        $res = $db->qry("
          SELECT
            *,
            UNIX_TIMESTAMP(enddate) AS enddate,
            UNIX_TIMESTAMP(sstartdate) AS sstartdate,
            UNIX_TIMESTAMP(senddate) AS senddate,
            UNIX_TIMESTAMP(startdate) AS startdate
          FROM %prefix%partys
          WHERE
            UNIX_TIMESTAMP(enddate) >= UNIX_TIMESTAMP(NOW())
          ORDER BY startdate");
        while ($row = $db->fetch_array($res)) {
            $mf = new \LanSuite\MasterForm($MFID);
            if ($_GET['mf_step'] != 2 || $row['party_id'] == $_GET['party_id']) {
                $dsp->AddFieldsetStart($row['name'] .' ('. $func->unixstamp2date($row['startdate'], 'datetime') .' - '. $func->unixstamp2date($row['enddate'], 'datetime') .')');
                $mf->AdditionalKey = 'party_id = '. $row['party_id'];

                // Signon
                $mf->AddInsertControllField = t('Angemeldet').'|'.t('Wenn dieses Häckchen gesetzt ist, bist du zu dieser Party angemeldet');
                $mf->AddChangeCondition = 'ChangeAllowed';

                // Paid
                if ($auth['type'] >= 2) {
                    $selections = array();
                    $selections['0'] = t('Nicht bezahlt');
                    $selections['1'] = t('Bezahlt');
                    $mf->AddField(t('Bezahltstatus'), 'paid', \LanSuite\MasterForm::IS_SELECTION, $selections);
                } elseif ($cfg['signon_autopaid']) {
                    $mf->AddFix('paid', '1');
                }

                if ($cfg['signon_autopaid'] or $_POST['paid']) {
                    $mf->AddFix('paiddate', 'NOW()');
                }

                // Prices
                $qrytmp = "SELECT * FROM %prefix%party_prices WHERE party_id = %int% AND requirement <= %string%";
                // Show all prices for administrators and only the one not ended for normal users
                if ($auth['type'] <= 1) {
                    $qrytmp.=" AND enddate > now()";
                }
                $res2 = $db->qry($qrytmp, $row['party_id'], $auth['type']);
                $selections = [];
                while ($row2 = $db->fetch_array($res2)) {
                    $selections[$row2['price_id']] = $row2['price_text'] .' ['. $row2['price'] .' '. $cfg['sys_currency'] .']&nbsp;&nbsp;'.t('Gültig bis : ').date_format(date_create($row2['enddate']), 'd.m.Y');
                }
                if ($selections) {
                    $mf->AddField(t('Eintrittspreis'), 'price_id', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
                } else {
                    $mf->AddField(t('Eintrittspreis'), 'price_id', \LanSuite\MasterForm::IS_TEXT_MESSAGE, t('Für diese Party wurden keine Preise definiert'));
                }
                $db->free_result($res2);

                if ($cfg['signon_autocheckin']) {
                    $mf->AddFix('checkin', 'NOW()');
                }

                $mf->AddFix('signondate', 'NOW()');

                if ($auth['type'] >= 2) {
                    $mf->AddField(t('Mail versenden?') .'|'. t('Den Benutzer per Mail über die Änderung informieren'), 'sendmail', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
                }
                $mf->SendButtonText = 'An-/Abmelden';

                $mf->AdditionalDBUpdateFunction = 'PartyMail';
                $mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&party_id='. $row['party_id'], 'party_user', 'user_id', $_GET['user_id']);
                $dsp->AddFieldsetEnd();
            } else {
                $mf->IncrementNumber();
            }
            $MFID++;
        }
        $db->free_result($res);

        // ShowHistory
        $dsp->AddFieldsetStart(t('Vergangene Partys'));
        $res = $db->qry("
          SELECT
            p.*,
            pu.user_id,
            pu.paid,
            UNIX_TIMESTAMP(pu.checkin) AS checkin,
            UNIX_TIMESTAMP(pu.checkout) AS checkout,
            UNIX_TIMESTAMP(p.enddate) AS enddate,
            UNIX_TIMESTAMP(p.sstartdate) AS sstartdate,
            UNIX_TIMESTAMP(p.senddate) AS senddate,
            UNIX_TIMESTAMP(p.startdate) AS startdate
          FROM %prefix%partys AS p
          LEFT JOIN %prefix%party_user AS pu ON p.party_id = pu.party_id
          WHERE
            UNIX_TIMESTAMP(p.enddate) < UNIX_TIMESTAMP(NOW())
            AND (
              pu.user_id = %int%
              OR pu.user_id IS NULL
            )
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
}
