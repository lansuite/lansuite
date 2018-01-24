<?php

$get_data = $db->qry_first("SELECT caption,text FROM %prefix%faq_item WHERE itemid = %int%", $_GET['itemid']);
$framework->AddToPageTitle($get_data["caption"]);

$dsp->NewContent(t('<b>F</b>requently <b>A</b>sked <b>Q</b>uestions'));
$buttons = $dsp->FetchSpanButton(t('Zurück'), "index.php?mod=faq");

if ($auth["type"] > 1) {
    $buttons .= $dsp->FetchSpanButton(t('Editieren'), "index.php?mod=faq&object=item&action=change_item&step=2&itemid=" . $_GET["itemid"]);
    $buttons .= $dsp->FetchSpanButton(t('Löschen'), "index.php?mod=faq&object=item&action=delete_item&step=2&itemid=" . $_GET["itemid"]);
}

if ($_GET['mcact'] == "show" or $_GET['mcact'] == "") {
    $dsp->AddFieldsetStart($func->text2html($get_data["caption"]));
    $dsp->AddSingleRow('<br>'. $func->text2html($get_data["text"]) .'<br>');

    new masterrate('faq', $_GET['itemid'], t('War dieser Eintrag hilfreich?'));

    $dsp->AddSingleRow($buttons);
    $dsp->AddFieldsetEnd();

    new Mastercomment('faq', $_GET['itemid']);
}
