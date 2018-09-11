<?php

$dsp->NewContent(t('Shoutbox'), t('Shoutbox History'));

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('task');

$ms2->query['from'] = '%prefix%shoutbox';
$ms2->query['default_order_by'] ="id DESC";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddSelect('id');
$ms2->AddResultField('Name', 'name');
$ms2->AddResultField('Shout Message', 'message');
$ms2->AddResultField('Datum', 'created');
$ms2->PrintSearch('index.php?mod=shoutbox&action=show', 'id');
