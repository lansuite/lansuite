<?php

/**
 * @param int $id
 */
function ChangeAllowed($id): bool|string
{
    global $db, $database, $row, $func, $auth, $seat2;

    // Do not allow changes, if party is over
    if ($row['enddate'] < time()) {
        return t('Du kannst dich nicht mehr zu dieser Party an-, oder abmelden, da sie bereits vorüber ist');
    }

    // Signon started?
    if ($row['sstartdate'] > time()) {
        return t('Die Anmeldung öffnet am'). HTML_NEWLINE .'<strong>'. $func->unixstamp2date($row['sstartdate'], 'daydatetime'). '</strong>';
    }

    // Signon ended?
    if ($row['senddate'] < time() and $auth['type'] < \LS_AUTH_TYPE_ADMIN) {
        return t('Die Anmeldung ist beendet seit'). HTML_NEWLINE .'<strong>'. $func->unixstamp2date($row['senddate'], 'daydatetime'). '</strong>';
    }

    // Do not allow changes, if user has paid
    if ($auth['type'] <= \LS_AUTH_TYPE_USER) {
        $row2 = $database->queryWithOnlyFirstRow("SELECT paid FROM %prefix%party_user WHERE party_id = ? AND user_id = ?", [$_GET['party_id'], $id]);
        if (is_array($row2) && $row2['paid'] != 0) {
            return t('Du bist für diese Party bereits auf bezahlt gesetzt. Bitte einen Admin dich auf "nicht bezahlt" zu setzen, bevor du dich abmeldest');
        }
    }

    // Check age
    if (isset($_POST['InsertControll1']) && $_POST['InsertControll1']) {
        $res = $db->qry("
                  SELECT
                    %prefix%partys.minage
                  FROM %prefix%user, %prefix%partys
                  WHERE
                    %prefix%partys.party_id = %int%
                    AND %prefix%user.userid = %int%
                    AND DATEDIFF(DATE_SUB(%prefix%partys.startdate, INTERVAL %prefix%partys.minage YEAR), %prefix%user.birthday) < 0
                    AND %prefix%partys.minage > 0", $_GET['party_id'], $id);

        $minage = $db->fetch_array($res);
        $db->free_result($res);
        if (isset($minage['minage'])) {
            return t('Du must mindestens %1 Jahre alt sein um an dieser Party teilnehmen zu d&uuml;rfen!', $minage['minage']);
        }
    }

    $row2 = $database->queryWithOnlyFirstRow("SELECT paid FROM %prefix%party_user WHERE party_id = ? AND user_id = ?", [$_GET['party_id'], $id]);

    // Free seats if the user hasn't paid already
    if ($row2 && $row2['paid'] == 0) {
        $seat2->FreeSeatAllMarkedByUser($id);
    }

    return false;
}
