<?php

include_once('class_board2install.php');

if ($config['board2']['configured'])
{
	if (!isset($_GET['todo'])) {
		$func->question(t('Wollen sie phpBB und Lansuite wirklich wieder trennen, wenn ja <b>Bitte Datenbank sichern!</br>'), 'index.php?mod=board2&action=disintegrate&todo=disintegrate', 'index.php?mod=board2&action=index');
	} else {
		if ($_GET['todo'] == 'disintegrate') {
			Board2install::deIntegrate();
			$dsp->NewContent(t('Board phpBB'), t('Deintegration'));
			$dsp->AddSingleRow(t('PhpBB und lansuite wurden erfolgreich getrennt.'));
			$dsp->AddContent();
		}
	}
}
else
{
	$func->error(t('Das Board wurde noch nicht integriert, daher kann es nicht getrennt werden.'), 'index.php?mod=board2&action=index');
}

//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 14. 1. 2009 : Created the file.
 */
?>