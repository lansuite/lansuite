<?php

if ($_GET['step'] == 2 and $_POST['text'] != '') {
	$time = time();

	$insert = $db->query("INSERT INTO	{$config['tables']['messages']}
    SET text='{$_POST['text']}', timestamp='$time', new='1', senderid='{$auth['userid']}', receiverid='{$_GET['queryid']}'
    ");
}

/*
$dsp->SetForm("index.php?mod=msgsys&action=query_console&design=base&queryid={$_GET['queryid']}&step=2");
$dsp->AddSingleRow('
<script language="JavaScript">
function loadwindow()
{
	top.location.href="index.php?mod=msgsys&action=query&design=base&queryid='. $_GET['queryid'] .'";
}
</script>
');
$dsp->AddTextAreaPlusRow('text', 'Eingabe', '', '');
$dsp->AddFormSubmitRow('send', '', 'senden" onClick="loadwindow()');
$dsp->AddContent();
$index .= $templ['index']['info']['content'];
*/

$gd->CreateButton('bold');
$gd->CreateButton('kursiv');
$gd->CreateButton('underline');
$gd->CreateButton('code');
$gd->CreateButton('picture');
$gd->CreateButton('send');
$index .= $dsp->FetchTpl('design/templates/messenger_query_console.htm', $templ);

$func->templ_output($index);
	
?>