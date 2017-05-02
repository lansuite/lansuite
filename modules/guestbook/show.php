<?php

$dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=guestbook&action=add') .HTML_NEWLINE);

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

$ms2->query['from'] = "%prefix%guestbook AS g";
$ms2->query['default_order_by'] = 'g.date';
$ms2->query['default_order_dir'] = 'DESC';

$ms2->config['EntriesPerPage'] = 50;

$ms2->AddSelect('g.userid');
$ms2->AddResultField(t('Autor'), 'g.poster', 'UserNameAndIcon');
$ms2->AddResultField(t('Eintrag'), 'g.text', 'Text2LSCode');
$ms2->AddResultField(t('Datum'), 'g.date', 'MS2GetDate');

if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=guestbook&action=add&guestbookid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=guestbook&action=delete&guestbookid=', 1);
}
$ms2->PrintSearch('index.php?mod=guestbook', 'g.guestbookid');

$dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=guestbook&action=add'));

$dsp->AddContent();
