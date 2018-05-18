<?php

if ($_GET['step'] == 2 and $_POST['text'] != '') {
    $time = time();

    $insert = $db->qry("
      INSERT INTO %prefix%messages
      SET
        text=%string%,
        timestamp=%string%,
        new='1',
        senderid=%int%,
        receiverid=%int%", $_POST['text'], $time, $auth['userid'], $_GET['queryid']);
}

$buttons = " ". $dsp->FetchIcon('bold', "javascript:InsertCode(document.form.text, '[b]', '[/b]')", t('Fett'));
$buttons .= " ". $dsp->FetchIcon('italic', "javascript:InsertCode(document.form.text, '[i]', '[/i]')", t('Kursiv'));
$buttons .= " ". $dsp->FetchIcon('underline', "javascript:InsertCode(document.form.text, '[u]', '[/u]')", t('Unterstrichen'));
$buttons .= " ". $dsp->FetchIcon('quote', "javascript:InsertCode(document.form.text, '[c]', '[/c]')", t('Code'));
$buttons .= " ". $dsp->FetchIcon('img', "javascript:InsertCode(document.form.text, '[img]', '[/img]')", t('Bild'));
$smarty->assign('buttons', $buttons);
$smarty->assign('queryid', $_GET['queryid']);
$index .= $smarty->fetch('design/templates/messenger_query_console.htm');

echo $index;
