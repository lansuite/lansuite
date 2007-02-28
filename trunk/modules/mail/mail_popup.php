<?php
$dsp->NewContent('Ungelesene E-Mails');
$func->information('Sie haben ungelesene E-Mails in Ihrem Posteingang<br /><br />'. $dsp->FetchCssButton('Zum Posteingang', 'javascript:opener.location.href=\'index.php?mod=mail\'; this.close()'), NO_LINK);
echo $func->FetchMasterTmpl("design/templates/base_index.htm", $templ);
?>