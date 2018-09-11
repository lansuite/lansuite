<?php

if ($_GET['mod'] == 'logout') {
    $smarty->assign('action', 'index.php');
} else {
    $smarty->assign('action', 'index.php?mod=auth&action=login');
}
$smarty->assign('buttons_add', $dsp->FetchIcon('add_user', 'index.php?mod=signon', t('Registrieren')));
$smarty->assign('buttons_pw', $dsp->FetchIcon('pw_forgot', 'index.php?mod=usrmgr&amp;action=pwrecover', t('Passwort vergessen')));
$smarty->assign('buttons_login', '<input type="submit" class="Button" name="login" value="Einloggen" />');

// TODO Remove static IP address, because lansuite.orgapage.de is not the mainsite anymore
// 62.67.200.4 = Proxy IP of https://sslsites.de/lansuite.orgapage.de
if ($cfg['sys_partyurl_ssl'] && ($_SERVER['HTTPS'] != 'on' && getenv('REMOTE_ADDR') != "62.67.200.4")) {
    $smarty->assign('ssl_link', $cfg['sys_partyurl_ssl']);
}

$box->AddTemplate($smarty->fetch('modules/boxes/templates/box_login_content.htm'));
