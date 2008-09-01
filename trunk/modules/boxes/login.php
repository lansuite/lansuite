<?php
/**
 * Generate Loginbox
 *
 * @package lansuite_core
 * @author knox
 * @version $Id$
 */
 
if ($_GET['mod'] == 'logout') $templ['login']['action'] = 'index.php';
else $templ['login']['action'] = "index.php?mod=auth&action=login";
$templ['login']['buttons_add'] = $dsp->FetchIcon('index.php?mod=signon', 'add_user');
$templ['login']['buttons_pw'] = $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=pwrecover', 'pw_forgot');

$templ['login']['buttons_login'] = '<input type="submit" class="Button" name="login" value="Einloggen" />';
$box->AddTemplate("box_login_content");

?>
