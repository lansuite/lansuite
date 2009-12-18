<?php

include_once("inc/classes/class_gd.php");
$gd = new gd;

if ($_GET['step'] == 2 and $_POST['text'] != '') {
	$time = time();

	$insert = $db->qry("INSERT INTO %prefix%messages
    SET text=%string%, timestamp=%string%, new='1', senderid=%int%, receiverid=%int%
    ", $_POST['text'], $time, $auth['userid'], $_GET['queryid']);
}

$gd->CreateButton('bold');
$gd->CreateButton('kursiv');
$gd->CreateButton('underline');
$gd->CreateButton('code');
$gd->CreateButton('picture');
$gd->CreateButton('send');

$smarty->assign('queryid', $_GET['queryid']);
$index .= $smarty->fetch('design/templates/messenger_query_console.htm');

echo $index;
	
?>