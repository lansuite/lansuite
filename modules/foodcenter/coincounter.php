<?php



	$dsp->NewContent(t('Münzzähler'),t('Hier kannst du die gezählten M&uuml;nzen eintragen um den Total zu erfahren.'));
	
	$templ['foodcenter']['coincounter']['cents'] = t('Cent') . " " .t('M&uuml;nzen');
	$templ['foodcenter']['coincounter']['coins']	 = $cfg['sys_currency'] . " " . t('M&uuml;nzen');
	$templ['foodcenter']['coincounter']['note']	 = $cfg['sys_currency'] . " " . t('Noten');
	$templ['foodcenter']['coincounter']['hardmoney']	= t('Hartgeld total:');
	$templ['foodcenter']['coincounter']['softmoney']	= t('Weichgeld total:');
	$templ['foodcenter']['coincounter']['money']		= t('Total');
	$templ['foodcenter']['coincounter']['currency']		= $cfg['sys_currency'];
	$dsp->AddModTpl("foodcenter","coincounter");
	$dsp->AddContent();
	

?>