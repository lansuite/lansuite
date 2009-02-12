<?php

include_once('modules/board2/class_board2install.php');

if ($config['board2']['configured'])
{
	$dsp->NewContent(t('Board phpBB'), t('Updaten von phpBB oder lansuite'));
	$dsp->AddSingleRow('');
	$dsp->AddSingleRow(t('Wenn sie phpBB oder lansuite Updaten m&uuml;ssen bitte befolgen sie folgende Schritte:'));
	$dsp->AddSingleRow(t('1. <b>Deintetgrieren</b> sie phpBB von lansuite.'));
	$dsp->AddSingleRow(t('2. Updaten sie phpbb und/oder lansuite.'));
	$dsp->AddSingleRow(t('3. <b>Integrieren</b> sie phpBB wieder in lansuite.'));

	$dsp->SetForm('index.php');
	$dsp->AddFormSubmitRow('next');
			
	$dsp->AddContent();
}
else
{
	$func->information(t('Das Board wurde noch nicht integriert, daher m&uuml;ssen f&uuml;r ein Update keine besonderen Schritte durchgef&uuml;hrt werden.'), 'index.php?mod=board2&action=index');
}

?>