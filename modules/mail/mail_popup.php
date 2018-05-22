<?php

$dsp->NewContent('Ungelesene E-Mails');
$func->information('Du hast ungelesene E-Mails in deinem Posteingang<br /><br />'. $dsp->FetchCssButton('Zum Posteingang', 'javascript:opener.location.href=\'index.php?mod=mail\'; this.close()'), NO_LINK);
