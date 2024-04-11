<?php

$get_cur = $db->qry_first('
    SELECT COUNT(userid) as n
    FROM
        %prefix%user AS user
        LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id
    WHERE
        (%plain%)
        AND party.paid > 0
        AND party_id = %int%', $querytype, $party->party_id);
$cur = $get_cur["n"];

$dsp->NewContent(t('PDF aus Daten erstellen'), t('W&auml;hle aus, was du ben&ouml;tigst'));
$dsp->AddSingleRow(t('<br>Du kannst für '.$cur.' Teilnehmer:<br><br>'));
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=guestcards\">".t('Ausweise erstellen')."</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=seatcards\">".t('Sitzplatzkarten erstellen')."</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=userlist\">".t('Besucherliste erstellen')."</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=certificate\">".t('Urkunden erstellen')."</a>");
