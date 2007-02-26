<?php

$templ['box']['rows'] = '';

$templ['login']['action'] = $CurentURLBase;
$templ['login']['buttons_add'] = $dsp->FetchIcon('index.php?mod=signon', 'add_user');
$templ['login']['buttons_pw'] = $dsp->FetchIcon('index.php?mod=usrmgr&action=pwrecover', 'pw_forgot');

$gd->CreateButton('login');
$templ['login']['buttons_login'] = '<input type="image" name="login" src="ext_inc/auto_images/'. $auth['design'] .'/'. $language .'/button_login.png" border="0" alt="Login" title="Login" />';

$box->AddTemplate("box_login_content");

?>