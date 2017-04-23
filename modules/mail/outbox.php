<?php
$mail_send_total = $db->qry_first("SELECT count(*) as n FROM %prefix%mail_messages WHERE FromUserID = %int% AND mail_status != 'disabled'", $auth['userid']);
$mail_read_total = $db->qry_first("SELECT count(*) as n FROM %prefix%mail_messages WHERE FromUserID = %int% AND mail_status != 'disabled' AND des_status = 'read'", $auth['userid']);

$dsp->NewContent(t('Postausgang'), t('Du hast <b>%1</b> Mail(s) versendet. Davon wurde(n) <b>%2</b> gelesen.', $mail_send_total["n"], $mail_read_total["n"]));
$dsp->AddContent();


if ($auth['userid']) {
    switch ($_GET['step']) {
    // check if it can delete from Database and delete
        case 20:
            if (!$_POST['action'] and $_GET['mailid']) {
                $_POST['action'][$_GET['mailid']] = 1;
            }
            foreach ($_POST['action'] as $key => $val) {
                  $rx_status = $db->qry_first("SELECT rx_deleted FROM %prefix%mail_messages WHERE mailID = %int%", $key);
          
                  // Ist eMail vom Sender gelöscht? JA: Lösche aus DB, NEIN: Setze rx flag
                if ($rx_status['rx_deleted']) {
                    $db->qry("DELETE FROM %prefix%mail_messages WHERE mailID = %int%", $key);
                } else {
                    $db->qry("UPDATE %prefix%mail_messages SET tx_deleted = 1 WHERE mailID = %int%", $key);
                }
            }
            break;
    }
}

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "%prefix%mail_messages AS m LEFT JOIN %prefix%user AS u ON m.ToUserID = u.userid";
$ms2->query['where'] = "m.FromUserID = '{$auth['userid']}' AND m.mail_status != 'disabled' AND tx_deleted = 0";
$ms2->query['default_order_by'] = 'm.tx_date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
$ms2->AddTextSearchField(t('Empfänger'), array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Betreff'), 'm.subject', '', 160);
$ms2->AddResultField(t('Empfänger'), 'u.username', 'UserNameAndIcon', '', 100);
$ms2->AddResultField(t('Gesendet'), 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate', '', 75);
$ms2->AddResultField(t('Gelesen'), 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate', '', 45);

$ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=out&mailID=', t('Details'));
$ms2->AddIconField('delete', 'index.php?mod=mail&action=outbox&step=20&mailid=', t('Entgültig löschen'));


$ms2->AddMultiSelectAction(t('Entgültig löschen'), 'index.php?mod=mail&action=outbox&step=20', 1, 'delete');


$ms2->PrintSearch('index.php?mod=mail&action=outbox', 'm.mailid');
