<?php

/**
 * Print overview page
 *
 * @return void
 */
function TS3ShowOverview()
{
    global $dsp;

    $dsp->NewContent(t('Teamspeak3'), t('Auflistung aller Nutzer auf Virtualserver 1'));
    $dsp->AddSingleRow($dsp->FetchSpanButton(t("Hier klicken zum Verbinden"), TS3BuildServerLink()));

    ob_start();
    TS3PrintContent();
    $dsp->AddContent();
    $dsp->AddSingleRow(ob_get_contents());
    ob_end_clean();
}
