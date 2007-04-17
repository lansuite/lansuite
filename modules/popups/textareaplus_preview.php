<?
$dsp->NewContent('Text-Vorschau');
#$dsp->AddSingleRow($func->text2html(str_replace('--NEWLINE--', "\n", $_GET['text'])));
$dsp->AddSingleRow($func->text2html(str_replace('--NEWLINE--', "\n", $__COOKIE['Preview'])));
?>