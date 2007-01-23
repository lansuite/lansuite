<?php

$dsp->NewContent('Ungelesene E-Mails');
$dsp->AddSingleRow('Sie haben ungelesene E-Mails in Ihrem Posteingang');
$dsp->AddSingleRow('<a href="javascript:opener.location.href=\'index.php?mod=mail\'; this.close()">Zum Posteingang</a>');
$dsp->AddContent();
#$index .= $templ['index']['info']['content'];
#$func->templ_output($index);
echo $func->FetchMasterTmpl("design/templates/base_index.htm", $templ);
?>