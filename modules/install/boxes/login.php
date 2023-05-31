<?php

if ($_GET['mod'] == 'logout') {
    $smarty->assign('action', 'index.php');
} else {
    $smarty->assign('action', 'index.php?mod=auth&action=login');
}
$smarty->assign('buttons_add', $dsp->FetchIcon('add_user', 'index.php?mod=signon', t('Registrieren')));
$smarty->assign('buttons_pw', $dsp->FetchIcon('pw_forgot', 'index.php?mod=usrmgr&amp;action=pwrecover', t('Passwort vergessen')));
$smarty->assign('buttons_login', '<input type="submit" class="Button" name="login" value="Einloggen" />');

if ($cfg['sys_partyurl_ssl'] && $_SERVER['HTTPS'] != 'on') {
    $smarty->assign('ssl_link', $cfg['sys_partyurl_ssl']);
} else {
    $smarty->assign('ssl_link', '');
}

$box->AddTemplate($smarty->fetch('modules/boxes/templates/box_login_content.htm'));
