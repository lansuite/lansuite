<?php
/**
 * Generate Loginbox
 *
 * @package lansuite_core
 * @author knox
 * @version $Id: login.php 1798 2009-01-18 17:46:03Z maztah $
 */

if ($_GET['mod'] == 'logout') $smarty->assign('action', 'index.php');
else $smarty->assign('action', 'index.php?mod=auth&action=login');
$smarty->assign('buttons_add', $dsp->FetchIcon('index.php?mod=signon', 'add_user', t('Registrieren')));
$smarty->assign('buttons_pw', $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=pwrecover', 'pw_forgot',t('Passwort vergessen')));
$smarty->assign('buttons_login', '<input type="submit" class="Button" name="login" value="Einloggen" />');

// 62.67.200.4 = Proxy IP of https://sslsites.de/lansuite.orgapage.de
if ($cfg['sys_partyurl_ssl'] and ($_SERVER['HTTPS'] != 'on' and getenv(REMOTE_ADDR) != "62.67.200.4"))
  $smarty->assign('ssl_link', $cfg['sys_partyurl_ssl']);

$box->AddTemplate($smarty->fetch('modules/boxes/templates/box_login_content.htm'));
?>