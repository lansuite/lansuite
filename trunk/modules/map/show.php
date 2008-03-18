<?php

	$dsp->NewContent(t('Anfahrt'), t('Hier könnt ihr euch von Map24.de eure Route zur LAN berechnen lassen.'));

	if ($auth['login'] == 1) {
		$res = $db->query("SELECT street, hnr, plz, city FROM {$config["tables"]["user"]} WHERE userid = {$auth['userid']}");
		$user = $db->fetch_array($res);

		$tmp = $user["street"];
		if($user["hnr"] != ""){
			$tmp = $user["street"] . " " . $user["hnr"];
		}
					
		$db->free_result($res);
  }
  
	if (!$cfg['sys_country']) $cfg['sys_country'] = 'DE';

  $dsp->AddDoubleRow('', '<iframe src="http://www.de.map24.com/source/link2map/v2.0.0/cnt_pick_code.php?linksection=linkdestroute&lid=0f73c3df&ol=de-de&dstreet='. $cfg['map_street'] .'&dzip='. $cfg['map_plz'] .'&dcity='. $cfg['map_city'] .'&dstate=&dcountry='. $cfg['sys_country'] .'&ddescription='. $cfg['map_street'] .' '. $cfg['map_hnr'] .'%3Cbr%3E'. $cfg['map_plz'] .' '. $cfg['map_city'] .'" width="200" height="241" scrolling="no" frameborder=0></iframe>');

	$dsp->AddContent();
?>