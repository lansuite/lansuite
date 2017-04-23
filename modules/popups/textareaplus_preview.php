<?php
$dsp->NewContent('Text-Vorschau');
$dsp->AddSingleRow($func->text2html($func->NoHTML(str_replace('--NEWLINE--', "\n", $__POST[$_GET['textareaname']]))));
