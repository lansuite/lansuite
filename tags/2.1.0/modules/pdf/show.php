<?php

$dsp->NewContent($lang["pdf"]["pdf_caption"], $lang["pdf"]["pdf_subcaption"]);
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=guestcards\">{$lang["pdf"]["pdf_guestcard"]}</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=seatcards\">{$lang["pdf"]["pdf_seatcard"]}</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=userlist\">{$lang["pdf"]["pdf_liste"]}</a>");
$party->get_party_dropdown_form(1,"index.php?mod=pdf");
$dsp->AddContent();

?>
