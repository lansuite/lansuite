<?php
$mail_total = $db->qry_first("
  SELECT
    COUNT(*) as n
  FROM %prefix%mail_messages
  WHERE
    ToUserID = %int%
    AND mail_status = 'delete'", $auth['userid']);

$mail_unread_total = $db->qry_first("
  SELECT
    COUNT(*) as n
  FROM %prefix%mail_messages
  WHERE
    ToUserID = %int%
    AND mail_status = 'delete'
    AND des_status = 'new'", $auth['userid']);

$dsp->NewContent(t('Papierkorb'), t('Du hast <b>%1</b> Mail(s) in ihrem Papierkorb. Davon wurde(n) <b>%2</b> nicht von dir gelesen.', $mail_total["n"], $mail_unread_total["n"]));

if ($auth['userid']) {
    switch ($_GET['step']) {
        // check if it can delete from Database and delete
        case 20:
            if (!$_POST['action'] and $_GET['mailid']) {
                $_POST['action'][$_GET['mailid']] = 1;
            }
            foreach ($_POST['action'] as $key => $val) {
                  $tx_status = $db->qry_first("SELECT fromUserID, tx_deleted FROM %prefix%mail_messages WHERE mailID = %int%", $key);
          
                  // Ist eMail vom Sender gelöscht? JA: Lösche aus DB, NEIN: Setze rx flag
                if ($tx_status['tx_deleted'] == 1 or $tx_status['fromUserID'] == 0) {
                    $db->qry("DELETE FROM %prefix%mail_messages WHERE mailID = %int%", $key);
                } else {
                    $db->qry("UPDATE %prefix%mail_messages SET rx_deleted = 1 WHERE mailID = %int%", $key);
                }
            }
            break;
    }
}

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();

$ms2->query['from'] = "%prefix%mail_messages AS m LEFT JOIN %prefix%user AS u ON m.FromUserID = u.userid";
$ms2->query['where'] = "m.toUserID = '{$auth['userid']}' AND m.mail_status = 'delete' AND rx_deleted = 0";
$ms2->query['default_order_by'] = 'm.tx_date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
$ms2->AddTextSearchField(t('Nachricht von'), array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddSelect('u.userid');

$ms2->AddResultField(t('Betreff'), 'm.subject', '', 160);
$ms2->AddResultField(t('Nachricht von'), 'u.username', 'UserNameAndIcon', '', 100);
$ms2->AddResultField('Status', 'm.des_status', 'MailStatus', '', 80);
$ms2->AddResultField(t('Gesendet'), 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate', '', 70);
$ms2->AddResultField(t('Gelesen'), 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate', '', 60);

$ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=trash&mailID=', t('Details'));
$ms2->AddIconField('delete', 'index.php?mod=mail&action=trashcan&step=20&mailid=', t('Entgültig löschen'), '', 10);

$ms2->AddMultiSelectAction(t('Entgültig löschen'), 'index.php?mod=mail&action=trashcan&step=20', 1, 'delete');

$ms2->PrintSearch('index.php?mod=mail&action=trash', 'm.mailid');
