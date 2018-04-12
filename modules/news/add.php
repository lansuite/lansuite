<?php
$dsp->NewContent(t('News verwalten'), t('Mit Hilfe des folgenden Formulars kannst du Neuigkeiten auf deiner Seite ergänzen und bearbeiten'));

$mf = new masterform();

// Name
$mf->AddField(t('Überschrift (Knappe Zusammenfassung für die Startseite)'), 'caption');
$mf->AddField(t('Kategorie / Icon'), 'icon', masterform::IS_PICTURE_SELECT, 'ext_inc/news_icons', masterform::FIELD_OPTIONAL);
$mf->AddField(t('Text'), 'text', '', $cfg['news_html']);
$selections = array();
$selections['0'] = t('Normal');
$selections['1'] = t('Wichtig');
$mf->AddField(t('Priorität'), 'priority', masterform::IS_SELECTION, $selections, masterform::FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Nein');
$selections['1'] = t('Ja');
$mf->AddField(t('Top-Meldung'), 'top', masterform::IS_SELECTION, $selections, masterform::FIELD_OPTIONAL);

if (!$_GET['newsid']) {
    $mf->AddFix('date', 'NOW()');
    $mf->AddFix('poster', $auth['userid']);
}

$mf->AddField(t('Link 1'), 'link_1', '', '', masterform::FIELD_OPTIONAL);
$mf->AddField(t('Link 2'), 'link_2', '', '', masterform::FIELD_OPTIONAL);
$mf->AddField(t('Link 3'), 'link_3', '', '', masterform::FIELD_OPTIONAL);

if ($mf->SendForm('index.php?mod=news&action='. $_GET['action'], 'news', 'newsid', $_GET['newsid'])) {
    include_once('modules/news/class_news.php');
    $news->GenerateNewsfeed();
}
