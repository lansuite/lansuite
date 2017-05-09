<?php

if (!$_GET['mailID']) {
    $func->error(t('Keine Mail ausgewählt'));
} else {
    $row = $db->qry_first("SELECT mm.*, UNIX_TIMESTAMP(mm.tx_date) AS tx_date, UNIX_TIMESTAMP(mm.rx_date) AS rx_date, u1.username AS fromUsername, u2.username AS ToUsername
  FROM %prefix%mail_messages AS mm
  LEFT JOIN %prefix%user AS u1 ON mm.FromUserID = u1.userid
  LEFT JOIN %prefix%user AS u2 ON mm.ToUserID = u2.userid
  WHERE mailID = %int%", $_GET['mailID']);

    if (!($auth['userid'] == $row['fromUserID'] or $auth['userid'] == $row['toUserID'])) {
        $func->information(t('Zugriff verweigert'));
    } else {
        $dsp->NewContent(t('Nachricht'), '');
        ($row['fromUserID'])? $dsp->AddDoubleRow(t('Von'), $dsp->FetchUserIcon($row['fromUserID'], $row['fromUsername']))
        : $dsp->AddDoubleRow(t('Von'), '<i>'. t('System') .'</i>');
        ($row['toUserID'])? $dsp->AddDoubleRow(t('An'), $dsp->FetchUserIcon($row['toUserID'], $row['ToUsername']))
        : $dsp->AddDoubleRow(t('An'), '<i>'. t('System') .'</i>');
        $dsp->AddDoubleRow(t('Gesendet'), $func->unixstamp2date($row['tx_date'], 'daydatetime'));
        $dsp->AddDoubleRow(t('Betreff'), $row['Subject']);
        $dsp->AddDoubleRow(t('Nachricht'), $func->text2html($row['msgbody']));

        $buttons = '';
        switch ($_GET['ref']) {
            default:
                $back_link = 'index.php?mod=mail&action=inbox';
                break;

            case 'in':
                $back_link = 'index.php?mod=mail&action=inbox';
                $buttons .= $dsp->FetchSpanButton(t('Löschen'), "index.php?mod=mail&action=inbox&step=20&mailid=". (int)$_GET['mailID']);
                if ($row['fromUserID']) {
                    $buttons .= $dsp->FetchSpanButton(t('Antworten'), "index.php?mod=mail&action=newmail&step=2&userID=". $row['fromUserID'] ."&replyto=". (int)$_GET['mailID']);
                }
                $buttons .= $dsp->FetchSpanButton(t("Weiterleiten"), "index.php?mod=mail&action=newmail&step=2&replyto=". (int)$_GET['mailID']);
                break;

            case 'out':
                $back_link = 'index.php?mod=mail&action=outbox';
                break;

            case 'trash':
                $back_link = 'index.php?mod=mail&action=trash';
                if ($row['fromUserID']) {
                    $buttons .= $dsp->FetchSpanButton(t('Antworten'), "index.php?mod=mail&action=newmail&step=2&userID=". $row['fromUserID'] ."&replyto=". (int)$_GET['mailID']);
                }
                break;
        }

        if ($buttons) {
            $dsp->AddDoubleRow('', $buttons);
        }
        $dsp->AddBackButton($back_link);

        if ($auth['userid'] == $row['toUserID'] and !$row['rx_date']) {
            $db->qry("UPDATE %prefix%mail_messages SET des_status = 'read', rx_date = NOW() WHERE mailID = %int%", $_GET['mailID']);
        }
    }
}
