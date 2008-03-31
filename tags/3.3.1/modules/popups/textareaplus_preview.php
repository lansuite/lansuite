<?
$dsp->NewContent('Text-Vorschau');
$dsp->AddSingleRow($func->text2html(str_replace('--NEWLINE--', "\n", $__POST[$_GET['textareaname']])));
?>