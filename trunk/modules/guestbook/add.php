<?php

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

if ($_POST['poster'] == '') $_POST['poster'] = $auth['username'];

$mf->AddField($lang['guestbook']['author'], 'poster', '', '');
if (!$auth['login']) $mf->AddField('', '', IS_CAPTCHA);
$mf->AddField($lang['guestbook']['entry'], 'text', '', LSCODE_ALLOWED);
$mf->AddGroup($lang['guestbook']['entry']);

$mf->AddFix('date', time());
$mf->AddFix('userid', $auth['userid']);

$mf->SendForm('index.php?mod=guestbook&action=add', 'guestbook', 'guestbookid', $_GET['guestbookid']);

?>