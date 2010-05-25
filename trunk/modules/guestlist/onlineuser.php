<?php
/*
 * Created on 08.03.2009
 * 
 * 
 * 
 * @package package_name
 * @author Maztah
 * 
 */

function getTimeDiff($last) {
	return date("i:s",time()-$last);
}

function getLastHitUrl($lasturl){
	return '<a href="'.$lasturl.'">'.substr(stristr($lasturl,'.php'),5).'</a>';
}

function getModul($url){
	$ret=array();
	parse_str(substr(stristr($url,'.php'),5),$ret);
	return $ret['mod'];
}

function getTimeDiffAsName($last){
	if((time()-$last) < 60*10)
		return t("Online");
	else
		return t("Untätig");
}

$dsp->NewContent(t("Online User"));

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('games');

		//Anzeige der Aufgaben
		$ms2->query['from'] = "%prefix%stats_auth AS s LEFT JOIN %prefix%user AS u on s.userid = u.userid";
		$ms2->query['where'] ="login = '1' AND (lasthit > UNIX_TIMESTAMP(NOW())- 60*10 OR lastajaxhit > UNIX_TIMESTAMP(NOW())- 60*3)  AND u.userid > 0 AND u.type > 0"; 
		$ms2->config['EntriesPerPage'] = 50;
        $ms2->query['order_by'] = "s.lasthit";

		$ms2->AddResultField(t('Name'), 'u.username', 'UserNameAndIcon');
		$ms2->AddResultField(t('Modul'), 's.lasthiturl', 'getModul');
		if($auth['type']>2) $ms2->AddResultField(t('URL'), 's.lasthiturl', 'getLastHitUrl');
		$ms2->AddResultField(t('Status'), 's.lasthit', 'getTimeDiffAsName');
		$ms2->AddResultField(t('Letzter Aufruf'), 's.lasthit', 'getTimeDiff');
		if($auth['type']>2) $ms2->AddResultField(t('Letzter Heartbeat'), 's.lastajaxhit', 'getTimeDiff');
		
		
		$ms2->PrintSearch('index.php?mod=misc&action=onlineuser', 'u.userid');		
?>
