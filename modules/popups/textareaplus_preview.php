<?
$dsp->NewContent('Text-Vorschau');

$dsp->AddSingleRow($func->text2html(str_replace('--NEWLINE--', "\n", $_GET['text'])));
$dsp->AddContent();

echo $func->FetchMasterTmpl("design/templates/base_index.htm", $templ);
?>