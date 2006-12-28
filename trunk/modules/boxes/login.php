<?php

$templ['box']['rows'] = '';

$dsp->form_open = 1;
$templ['login']['buttons_login'] = $dsp->FetchIcon('', 'save');
$dsp->form_open = 0;

$templ['login']['buttons_manage'] = $dsp->FetchIcon('index.php?mod=signon', 'add_user') . $dsp->FetchIcon('index.php?mod=usrmgr&action=pwrecover', 'change_pw');
$box->AddTemplate("box_login_content");

$boxes['login'] .= $box->CreateBox("login",$lang['boxes']['userdata_login']);
?>
