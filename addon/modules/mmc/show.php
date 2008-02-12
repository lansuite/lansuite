<?php

	$dsp->NewContent($lang['mmc']['headline'], $lang['mmc']['subhline']);
	$templ['mmc']['show']['stateicon'] = 'playing';
	$templ['mmc']['show']['nextfile'] = 'next: Nächste_Datei.mpg';
	$templ['mmc']['show']['currfile'] = 'Wird_zurzeit_abgespielt.mpeg';
    $dsp->AddModTpl('mmc', 'mmc_show_infobox');
    $dsp->AddModTpl('mmc', 'mmc_show_control');

?>
