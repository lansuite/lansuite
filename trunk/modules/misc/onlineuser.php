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
//$user_online = $db->qry_first('SELECT 1 AS found FROM %prefix%stats_auth WHERE userid = %int% AND login = \'1\' AND lasthit > %int%', $_GET['userid'], time() - 60*10);

 		include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new mastersearch2('games');

		//Anzeige der Aufgaben
		$ms2->query['from'] = "{$config['tables']['stats_auth']} AS s LEFT JOIN {$config['tables']['user']} AS u on s.userid = u.userid";
		$ms2->query['where'] ="login = '1' AND lasthit > UNIX_TIMESTAMP(NOW())- 60*10 AND u.userid > 0 AND u.type > 0"; 
		$ms2->config['EntriesPerPage'] = 50;
        $ms2->query['order_by'] = "s.lasthit";

		$ms2->AddResultField(t('Name'), 'u.username', 'UserNameAndIcon');
		$ms2->AddResultField(t('URL'), 's.lasthiturl');
		$ms2->PrintSearch('index.php?mod=misc&action=onlineuser', 'u.userid');
?>
