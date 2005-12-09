<?php

$templ['box']['rows'] = "";

$gd->CreateButton("save");
$gd->CreateButton("login");

$box->AddTemplate("box_login_content");
$boxes['login'] .= $box->CreateBox("login",$lang['boxes']['userdata_login']);
?>