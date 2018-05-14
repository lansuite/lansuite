<?php

$link_target_yes = "index.php?mod=guestlist&action=checked";
$link_target_no = "index.php?mod=guestlist&action=guestlist";

$func->question(t("Soll das Datum <b>\"Letzter Kontocheck\"</b> auf das aktuelle Datum gesetzt werden?"), $link_target_yes, $link_target_no);
