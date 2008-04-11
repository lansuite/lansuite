<?php

$dsp->AddSingleRow($dsp->FetchButton('index.php?mod=guestbook&action=add', 'add') .HTML_NEWLINE);

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config["tables"]["guestbook"]} AS g";
$ms2->query['default_order_by'] = 'g.date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 50;

$ms2->AddSelect('g.userid');
$ms2->AddResultField($lang['guestbook']['author'], 'g.poster', 'UserNameAndIcon');
$ms2->AddResultField($lang['guestbook']['entry'], 'g.text', 'Text2LSCode');
$ms2->AddResultField($lang['guestbook']['date'], 'g.date', 'MS2GetDate');

if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=guestbook&action=add&guestbookid=', $lang['ms2']['edit']);
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['ms2']['delete'], 'index.php?mod=guestbook&action=delete&guestbookid=', 1);
$ms2->PrintSearch('index.php?mod=guestbook', 'g.guestbookid');

$dsp->AddSingleRow($dsp->FetchButton('index.php?mod=guestbook&action=add', 'add'));

$dsp->AddContent();

?>
