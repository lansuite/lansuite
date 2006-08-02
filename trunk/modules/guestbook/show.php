<?php

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

$ms2->PrintSearch('index.php?mod=guestbook', 'g.guestbookid');

$dsp->AddSingleRow($dsp->FetchButton('index.php?mod=guestbook&action=add', 'add'));

$dsp->AddContent();

?>