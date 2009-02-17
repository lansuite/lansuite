<?php
/*
 * Created on 12.02.2009
 * 
 * Gibt zuf�llig ausgew�hlte Benutzer aus. N�tzlich f�r Gewinnspiele etc.
 * 
 * @package lansuite_core
 * @author Maztah
 * 
 */
 
 $dsp->NewContent(t('Zufallsuser'));

		include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new mastersearch2('random_user');
		
		//Anzeige User
		$ms2->query['from'] = "{$config['tables']['user']} AS u
   		LEFT JOIN {$config['tables']['clan']} AS c ON u.clanid = c.clanid
    	LEFT JOIN {$config['tables']['party_user']} AS p ON u.userid = p.user_id
   		LEFT JOIN {$config["tables"]["party_prices"]} AS i ON i.party_id = p.party_id AND i.price_id = p.price_id";
		
		$ms2->query['default_order_by'] ="RAND()"; 
		$ms2->config['EntriesPerPage'] = 20;

		$party_list = array('' => 'Alle', 'NULL' => 'Zu keiner Party angemeldet');
		$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
		while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
		$db->free_result($row);

		$ms2->AddTextSearchDropDown(t('Party'), 'p.party_id', $party_list,$party->party_id);
		$ms2->AddTextSearchDropDown(t('Eingecheckt'), 'p.checkin', array('' => t('Alle'), '0' => t('Nicht eingecheckt'), '>1' => t('Eingecheckt')));

		$ms2->AddResultField(t('LS ID'), 'u.userid');
		$ms2->AddResultField(t('Benutzername'), 'u.username');
		$ms2->AddResultField(t('Vorname'), 'u.firstname');
		$ms2->AddResultField(t('Nachname'), 'u.name');
		$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Details'));

		$ms2->PrintSearch('index.php?mod=misc&action=random_user', 'u.userid');
?>
