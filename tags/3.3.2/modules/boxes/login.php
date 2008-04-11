<?php

$templ['box']['rows'] = '';

if ($_GET['mod'] == 'logout') $templ['login']['action'] = 'index.php';
else $templ['login']['action'] = $CurentURLBase;
$templ['login']['buttons_add'] = $dsp->FetchIcon('index.php?mod=signon', 'add_user');
$templ['login']['buttons_pw'] = $dsp->FetchIcon('index.php?mod=usrmgr&amp;action=pwrecover', 'pw_forgot');

$gd->CreateButton('login');
#$templ['login']['buttons_login'] = '<input type="image" name="login" src="ext_inc/auto_images/'. $auth['design'] .'/'. $language .'/button_login.png" border="0" alt="Login" title="Login" />';
$templ['login']['buttons_login'] = '<input type="submit" class="Button" name="login" value="Einloggen" />';
$box->AddTemplate("box_login_content");

?>
