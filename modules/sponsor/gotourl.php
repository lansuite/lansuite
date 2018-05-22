<?php

$sponsoren = $db->qry("UPDATE %prefix%sponsor SET hits = hits + 1 WHERE url=%string%", $_GET["url"]);

$dsp->NewContent(t('Unsere Sponsoren'), t('Bei den folgenden Sponsoren möchten wir uns herzlich für ihren Beitrag zu unserer Veranstaltung bedanken.'));
$dsp->AddDoubleRow(t('Du wirst weitergeleitet...'), "<a href=\"{$_GET["url"]}\">{$_GET["url"]}</a>" . HTML_NEWLINE . "
<script language = \"JavaScript\">window.location.href = \"{$_GET["url"]}\";</script>");
$dsp->AddBackButton("index.php?mod=sponsor", "sponsor/show");
