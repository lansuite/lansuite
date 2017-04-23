<?php
// if logged out
if (!$auth['userid']) {
    $dsp->NewContent(t('Posteingang'));
    $func->information(t('Um deinen Posteingang sehen zu können, musst du dich zuerst einloggen. Nutzen kannst du das <a href="index.php?mod=mail&action=newmail">Kontaktformular</a> um Mails zu versenden. Dies ist auch im ausgeloggten Zustand möglich.'));
}

// If logged in
if ($auth['userid']) {
    switch ($_GET['step']) {
    // Lable
        case 10:  // None
        case 11:
        case 12:
        case 13:
        case 14:
        case 15:
            foreach ($_POST['action'] as $key => $val) {
                $db->qry('UPDATE %prefix%mail_messages SET label = %int% WHERE mailID = %int%', ($_GET['step'] - 10), $key);
            }
            break;

    // Move to trashcan
        case 20:
            if (!$_POST['action'] and $_GET['mailid']) {
                $_POST['action'][$_GET['mailid']] = 1;
            }
            if ($_POST['action']) {
                foreach ($_POST['action'] as $key => $val) {
                    $db->qry("UPDATE %prefix%mail_messages SET mail_status = 'delete' WHERE mailID = %int%", $key);
                }
            }
            break;
    }

    $colors = array();
    $colors[0] = '';
    $colors[1] = 'red';
    $colors[2] = 'blue';
    $colors[3] = 'green';
    $colors[4] = 'yellow';
    $colors[5] = 'purple';

  
    function MailStatus($status)
    {
        global $lang;
        if ($status == "new") {
            return t('Ungelesen');
        }
        if ($status == "read") {
            return t('Gelesen');
        }
        if ($status == "reply") {
            return t('Beantwortet');
        }
    }

    $mail_new_total = $db->qry_first("SELECT count(*) as n FROM %prefix%mail_messages WHERE ToUserID = %int% AND mail_status = 'active' AND des_status = 'new'", $auth['userid']);
    $mail_total = $db->qry_first("SELECT count(*) as n FROM %prefix%mail_messages WHERE ToUserID = %int% AND mail_status = 'active'", $auth['userid']);
    $dsp->NewContent(t('Posteingang'), t('Du hast <b>%1</b> Mail(s) empfangen. Davon sind <b>%2</b> ungelesen.', array($mail_total['n'], $mail_new_total['n'])));
    
  
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2();

    $ms2->query['from'] = "%prefix%mail_messages AS m LEFT JOIN %prefix%user AS u ON m.FromUserID = u.userid";
    $ms2->query['where'] = "m.toUserID = '{$auth['userid']}' AND m.mail_status = 'active'";
    $ms2->query['default_order_by'] = 'm.tx_date';
    $ms2->query['default_order_dir'] = 'DESC';

    $ms2->AddBGColor('label', $colors);

    $ms2->config['EntriesPerPage'] = 20;

    $ms2->AddTextSearchField('Mail', array('m.subject' => 'fulltext', 'm.msgbody' => 'fulltext'));
    $ms2->AddTextSearchField(t('Nachricht von'), array('u.userid' => 'exact', 'u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

    $ms2->AddSelect('u.userid');

    $ms2->AddResultField(t('Betreff'), 'm.subject', '', 160);
    $ms2->AddResultField(t('Nachricht von'), 'u.username', 'UserNameAndIcon', '', 100);
    $ms2->AddResultField('Status', 'm.des_status', 'MailStatus', '', 80);
    $ms2->AddResultField(t('Gesendet'), 'UNIX_TIMESTAMP(m.tx_date) AS tx_date', 'MS2GetDate', '', 70);
    $ms2->AddResultField(t('Gelesen'), 'UNIX_TIMESTAMP(m.rx_date) AS rx_date', 'MS2GetDate', '', 20);
    
    $ms2->AddIconField('details', 'index.php?mod=mail&action=showmail&ref=in&mailID=', t('Details'), '', 10);
    $ms2->AddIconField('delete', 'index.php?mod=mail&action=inbox&step=20&mailid=', t('Löschen'), '', 10);

    $ms2->AddMultiSelectAction(t('Markierung entfernen'), 'index.php?mod=mail&step=10', 0);
    $ms2->AddMultiSelectAction(t('Markieren: Rot'), 'index.php?mod=mail&step=11', 0);
    $ms2->AddMultiSelectAction(t('Markieren: Blau'), 'index.php?mod=mail&step=12', 0);
    $ms2->AddMultiSelectAction(t('Markieren: Grün'), 'index.php?mod=mail&step=13', 0);
    $ms2->AddMultiSelectAction(t('Markieren: Gelb'), 'index.php?mod=mail&step=14', 0);
    $ms2->AddMultiSelectAction(t('Markieren: Lila'), 'index.php?mod=mail&step=15', 0);
    $ms2->AddMultiSelectAction(t('In den Papierkorb'), 'index.php?mod=mail&step=20', 1, 'delete');

    $ms2->PrintSearch('index.php?mod=mail', 'm.mailid');
}
