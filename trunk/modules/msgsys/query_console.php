<?php

if($_GET[action] == "add" && $_POST[text] != "") {
	$time = time();

	$insert = $db->query("INSERT INTO	{$config[tables][messages]} 
    SET text='$_POST[text]', timestamp='$time', new='1', senderid='{$_SESSION[auth][userid]}', receiverid='$_GET[queryid]'
    ");
}

$gd->CreateButton('bold');
$gd->CreateButton('kursiv');
$gd->CreateButton('underline');
$gd->CreateButton('code');
$gd->CreateButton('picture');
$gd->CreateButton('send');
$index .= $dsp->FetchTpl('design/templates/messenger_query_console.htm', $templ);
$func->templ_output($index);
	
?>