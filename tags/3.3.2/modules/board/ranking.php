<?php

$dsp->NewContent(t('Ränge'));

$out = '';
$lines = split("\n", $cfg['board_rank']);
foreach ($lines as $line) {
  list($num, $name) = split('->', $line);
  $out .= t('Ab %1 Posts: %2', array($num, $name)) . HTML_NEWLINE;
}
$dsp->AddSingleRow($out);

$dsp->AddFieldSetStart(t('Aktuelle Rangliste'));
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config['tables']['board_posts']} AS p
    LEFT JOIN {$config['tables']['user']} AS u ON u.userid = p.userid";
$ms2->query['default_order_by'] = 'posts DESC';
$ms2->AddResultField(t('Benutzername'), 'u.username', 'UserNameAndIcon');
$ms2->AddResultField(t('Beiträge'), 'COUNT(*) as posts');
$ms2->PrintSearch('index.php?mod=board&action=ranking', 'p.userid');
$dsp->AddFieldsetEnd();

$dsp->AddBackButton($func->internal_referer);
?>