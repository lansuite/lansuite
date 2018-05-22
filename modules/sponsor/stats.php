<?php

$dsp->NewContent(t('Statistiken'), t('Hier siehst du die Statistiken zu den einzelnen Sponsoren der Sponsorliste'));
$sponsoren = $db->qry("SELECT * FROM %prefix%sponsor");
while ($sponsor = $db->fetch_array($sponsoren)) {
    $dsp->AddFieldsetStart("<a href=\"{$sponsor["url"]}\" traget=\"_blank\">{$sponsor["name"]}</a>");
    if ($sponsor['views']) {
        $percentage = round($sponsor['hits'] / $sponsor['views'], 4) * 100 .'%';
    } else {
        $percentage = '---';
    }
    $dsp->AddDoubleRow(t('Auf Sponsorenseite'), $sponsor['views'] .'x '. t('Angezeigt').
    ', '.$sponsor['hits'] .'x '. t('Angeklickt').
    ' ['. t('Klickrate') .': '. $percentage .']');

    if ($sponsor['views_banner']) {
        $percentage = round($sponsor['hits_banner'] / $sponsor['views_banner'], 4) * 100 .'%';
    } else {
        $percentage = '---';
    }
    $dsp->AddDoubleRow(t('In Rotations-Banner'), $sponsor['views_banner'] .'x '. t('Angezeigt').
    ', '.$sponsor['hits_banner'] .'x '. t('Angeklickt').
    ' ['. t('Klickrate') .': '. $percentage .']');

    if ($sponsor['views_box']) {
        $percentage = round($sponsor['hits_box'] / $sponsor['views_box'], 4) * 100 .'%';
    } else {
        $percentage = '---';
    }
    $dsp->AddDoubleRow(t('In Sponsoren-Box'), $sponsor['views_box'] .'x '. t('Angezeigt').
    ', '.$sponsor['hits_box'] .'x '. t('Angeklickt').
    ' ['. t('Klickrate') .': '. $percentage .']');
    $dsp->AddFieldsetEnd();
}
$db->free_result($sponsoren);
$dsp->AddBackButton('index.php?mod=sponsor', 'sponsor/show');
