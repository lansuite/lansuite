<?php

$dsp->NewContent(t('PDF aus Daten erstellen'), t('Bitte geben sie an was sie ben&ouml;tigen'));
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=guestcards\">".t('Ausweise erstellen')."</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=seatcards\">".t('Sitzplatzkarten erstellen')."</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=userlist\">".t('Besucherliste erstellen')."</a>");
$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=certificate\">".t('Urkunden erstellen')."</a>");
$party->get_party_dropdown_form(1, "index.php?mod=pdf");
