<?php
$dsp->NewContent(t('News verwalten'), t('Mit Hilfe des folgenden Formulars kannst du Neuigkeiten auf deiner Seite ergänzen und bearbeiten'));

$mf = new masterform();

// Name
$mf->AddField(t('Überschrift (Knappe Zusammenfassung für die Startseite)'), 'caption');
$mf->AddField(t('Kategorie / Icon'), 'icon', IS_PICTURE_SELECT, 'ext_inc/news_icons', FIELD_OPTIONAL);
$mf->AddField(t('Text'), 'text', '', $cfg['news_html']); # 0 = HTML, 1 = LSCODE_ALLOWED, 2 = HTML_WYSIWYG
$selections = array();
$selections['0'] = t('Normal');
$selections['1'] = t('Wichtig');
$mf->AddField(t('Priorität'), 'priority', IS_SELECTION, $selections, FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Nein');
$selections['1'] = t('Ja');
$mf->AddField(t('Top-Meldung'), 'top', IS_SELECTION, $selections, FIELD_OPTIONAL);

if (!$_GET['newsid']) {
    $mf->AddFix('date', 'NOW()');
    $mf->AddFix('poster', $auth['userid']);
}

$mf->AddField(t('Link 1'), 'link_1', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Link 2'), 'link_2', '', '', FIELD_OPTIONAL);
$mf->AddField(t('Link 3'), 'link_3', '', '', FIELD_OPTIONAL);

if ($mf->SendForm('index.php?mod=news&action='. $_GET['action'], 'news', 'newsid', $_GET['newsid'])) {
    include_once('modules/news/class_news.php');
    $news->GenerateNewsfeed();
}
