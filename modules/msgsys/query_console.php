<?php

if ($_GET['step'] == 2 and $_POST['text'] != '') {
    $time = time();

    $insert = $db->qry("INSERT INTO %prefix%messages
    SET text=%string%, timestamp=%string%, new='1', senderid=%int%, receiverid=%int%
    ", $_POST['text'], $time, $auth['userid'], $_GET['queryid']);
}

$buttons = " ". $dsp->FetchIcon("javascript:InsertCode(document.form.text, '[b]', '[/b]')", 'bold', t('Fett'));
$buttons .= " ". $dsp->FetchIcon("javascript:InsertCode(document.form.text, '[i]', '[/i]')", 'italic', t('Kursiv'));
$buttons .= " ". $dsp->FetchIcon("javascript:InsertCode(document.form.text, '[u]', '[/u]')", 'underline', t('Unterstrichen'));
$buttons .= " ". $dsp->FetchIcon("javascript:InsertCode(document.form.text, '[c]', '[/c]')", 'quote', t('Code'));
$buttons .= " ". $dsp->FetchIcon("javascript:InsertCode(document.form.text, '[img]', '[/img]')", 'img', t('Bild'));
$smarty->assign('buttons', $buttons);
$smarty->assign('queryid', $_GET['queryid']);
$index .= $smarty->fetch('design/templates/messenger_query_console.htm');

echo $index;
