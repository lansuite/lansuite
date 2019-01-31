<?php

if ($auth['login']) {
    $smarty->assign('caption', t('Neue Mails'));
    $content = "";

    $query = $db->qry('
      SELECT
        m.des_Status,
        m.mailID,
        m.subject,
        u.username
      FROM
        %prefix%mail_messages AS m
        LEFT JOIN %prefix%user AS u ON m.FromUserID = u.userid
      WHERE
        m.toUserID = %int%
        AND m.mail_status = \'active\'
      ORDER BY m.tx_date DESC
      LIMIT 0, %int%', $auth['userid'], $cfg['home_item_cnt_mail']);

    if ($db->num_rows($query) > 0) {
        while ($row = $db->fetch_array($query)) {
            $smarty->assign('link', 'index.php?mod=mail&action=showmail&ref=in&mailID='. $row['mailID']);
            $smarty->assign('text', $func->CutString($row['subject'], 40));
            $smarty->assign('text2', ' ['.($row['username']=='' ? 'System' : $row['username']).']');
            if ($row['des_Status'] == 'new') {
                $content .= $smarty->fetch('modules/home/templates/show_row_new.htm');
            } else {
                $content .= $smarty->fetch('modules/home/templates/show_row.htm');
            }
        }
    } else {
        $content = "<i>". t('Keine Mails bisher vorhanden') ."</i>";
    }
}
