<?php
$dsp->NewContent(t('News verwalten'), t('Mit Hilfe des folgenden Formulars können Sie Neuigkeiten auf Ihrer Seite ergänzen und bearbeiten'));

include_once('inc/classes/class_masterform.php');
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
  $mf->AddFix('date', time());
  $mf->AddFix('poster', $auth['userid']);
}

if ($mf->SendForm('index.php?mod=news&action='. $_GET['action'], 'news', 'newsid', $_GET['newsid'])) {
  include_once('modules/news/class_news.php');
  $news->GenerateNewsfeed();
}
?>
