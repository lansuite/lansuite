<?php
$dsp->NewContent(t('News verwalten'), t('Mit Hilfe des folgenden Formulars kannst du Neuigkeiten auf deiner Seite ergänzen und bearbeiten'));

$mf = new \LanSuite\MasterForm();

// Name
$mf->AddField(t('Überschrift (Knappe Zusammenfassung für die Startseite)'), 'caption');
$mf->AddField(t('Kategorie / Icon'), 'icon', \LanSuite\MasterForm::IS_PICTURE_SELECT, 'ext_inc/news_icons', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Text'), 'text', '', $cfg['news_html']);
$selections = array();
$selections['0'] = t('Normal');
$selections['1'] = t('Wichtig');
$mf->AddField(t('Priorität'), 'priority', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
$selections = array();
$selections['0'] = t('Nein');
$selections['1'] = t('Ja');
$mf->AddField(t('Top-Meldung'), 'top', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

if (!$_GET['newsid']) {
    $mf->AddFix('date', 'NOW()');
    $mf->AddFix('poster', $auth['userid']);
}

$mf->AddField(t('Link 1'), 'link_1', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Link 2'), 'link_2', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Link 3'), 'link_3', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

if ($mf->SendForm('index.php?mod=news&action='. $_GET['action'], 'news', 'newsid', $_GET['newsid'])) {
    $news = new \LanSuite\Module\News\News();
    $news->GenerateNewsfeed();
}
