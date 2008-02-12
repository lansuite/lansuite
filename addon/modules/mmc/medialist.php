<?php

	include_once('modules/mastersearch2/class_mastersearch2.php');
	$ms2 = new mastersearch2('media');

	$ms2->query['from'] = "{$config["tables"]["mmc_media"]} n LEFT JOIN {$config["tables"]["user"]} u ON n.ownerid=u.userid";

	$ms2->config['EntriesPerPage'] = 20;

	$ms2->AddTextSearchField('Name', array('n.name' => '1337'));
	$ms2->AddTextSearchField('Uploader', array('n.owner' => '1337'));

	$ms2->AddResultField('Name', 'n.name');
	$ms2->AddResultField('Ort', 'n.file');
	$ms2->AddSelect('u.userid');
	$ms2->AddResultField('Uploader', 'u.username', 'UserNameAndIcon');
	$ms2->AddResultField('Stimmen', 'n.votes');

	if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=mmc&action=medialist&step=3&mediaid=', 'Edit');
	if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=mmc&action=medialist&step=2&mediaid=', 'Delete');

	$ms2->PrintSearch('index.php?mod=mmc&action=medialist', 'n.mediaid');

?>
