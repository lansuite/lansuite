<?php

$dsp->NewContent(t('Suchmaschinen'), t('Hier siehst du, &uuml;ber welche Suchbegriffe Besucher &uuml;ber Suchmaschinenen auf deiner Seite gelandet sind'));

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "%prefix%stats_se";
$ms2->query['default_order_by'] = 'hits DESC';

$ms2->config['EntriesPerPage'] = 50;

$ms2->AddTextSearchField(t('Suchbegriff'), array('term' => 'like'));

$list = array('' => 'Alle');
$res = $db->qry('SELECT se FROM %prefix%stats_se GROUP BY se ORDER BY se');
while($row = $db->fetch_array($res)) $list[$row['se']] = $row['se'];
$db->free_result($res);
$ms2->AddTextSearchDropDown('Suchmaschiene', 'se', $list);

$ms2->AddResultField(t('Suchbegriff'), 'term', '', 80);
$ms2->AddResultField(t('Anzahl'), 'hits');
$ms2->AddResultField(t('Erstmalig'), 'UNIX_TIMESTAMP(first) AS first', 'MS2GetDate');
$ms2->AddResultField(t('Zuletzt'), 'UNIX_TIMESTAMP(last) AS last', 'MS2GetDate');

#$ms2->AddIconField('details', 'index.php?mod=news&action=comment&newsid=', t('Details'));
#if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=news&action=change&step=2&newsid=', t('Editieren'));
#if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=news&action=delete&step=2&newsid=', t('L�schen'));

$ms2->PrintSearch('index.php?mod=stats&action=search_engines', '1');

$dsp->AddBackButton("index.php?mod=stats", "stats/se");
$dsp->AddContent();
?>