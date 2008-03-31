<?php



	$dsp->NewContent($lang['foodcenter']['coincounter_caption'],$lang['foodcenter']['coincounter_subcaption']);
	
	$templ['foodcenter']['coincounter']['cents'] = $lang['foodcenter']['coincounter_cents'] . " " .$lang['foodcenter']['coincounter_coins'];
	$templ['foodcenter']['coincounter']['coins']	 = $cfg['sys_currency'] . " " . $lang['foodcenter']['coincounter_coins'];
	$templ['foodcenter']['coincounter']['note']	 = $cfg['sys_currency'] . " " . $lang['foodcenter']['coincounter_note'];
	$templ['foodcenter']['coincounter']['hardmoney']	= $lang['foodcenter']['coincounter_hardmoney'];
	$templ['foodcenter']['coincounter']['softmoney']	= $lang['foodcenter']['coincounter_softmoney'];
	$templ['foodcenter']['coincounter']['money']		= $lang['foodcenter']['coincounter_money'];
	$templ['foodcenter']['coincounter']['currency']		= $cfg['sys_currency'];
	$dsp->AddModTpl("foodcenter","coincounter");
	$dsp->AddContent();
	

?>