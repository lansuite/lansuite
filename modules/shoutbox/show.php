<?php
/*
 * Created on 12.02.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$dsp->NewContent(t('Shoutbox'), t('Shoutbox History'));

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('task');

$ms2->query['from'] = '%prefix%shoutbox';
$ms2->query['default_order_by'] ="id DESC";

$ms2->config['EntriesPerPage'] = 20;

$ms2->AddSelect('id');
$ms2->AddResultField('Name', 'name');
$ms2->AddResultField('Shout Message', 'message');
$ms2->AddResultField('Datum', 'created');
$ms2->PrintSearch('index.php?mod=shoutbox&action=show', 'id');
