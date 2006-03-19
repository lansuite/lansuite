<?php

$templ['box']['rows'] = "";

$gd->CreateButton("save");
$gd->CreateButton("login");

$box->AddTemplate("box_login_content");
$templ['box']['rows'] .= $dsp->FetchButton("index.php?mod=signon", "register");
$templ['box']['rows'] .= $dsp->FetchButton("index.php?mod=usrmgr&action=pwrecover", "lost_pw");
$boxes['login'] .= $box->CreateBox("login",$lang['boxes']['userdata_login']);
?>