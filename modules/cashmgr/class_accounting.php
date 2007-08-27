<?php

	function getMoneyColor($money)
	{
		if($money > 0)
			return "<font color='green'>+".number_format($money, 2, ',', '.') . " EUR</font>";
		
		if($money < 0)
			return "<font color='red'>".number_format($money, 2, ',', '.') . " EUR</font>";

		if($money = 0)
			return number_format($money, 2, ',', '.') . " EUR";
	}


class accounting
	{
	var $editorid;		//Bearbeiter
	var $partyid;		//Party
	var $fix;			//Fixkosten oder Fixeinnahmen?
	var $modul;

	/**
	* Konstruktor
	* Sobald keine UserID übergeben wird,
	* ist der einzutragene Wert automatisch Fix (z.B Miete oder Sponsoring)
	*/
	function accounting($party_id)
	{
		global $config;
		
		$this->modul = $_GET['mod'];
		$this->editorid = $auth['userid'];
		$this->partyid = $party_id;
	}
	
	/**
	* Buchung
	* nimmt positive und negative Werte an.
	* Wenn Wert negativ -> Ausgabe für die LAN
	*/
	function booking($movement, $comment, $user_id = NULL)
	{
		global $func;
		
		if($user_id == NULL)
			$this->fix = 1;
		else
			$this->fix = 0;
			
		global $db,$config;
			$db->query("INSERT INTO {$config['tables']['cashmgr_accounting']} SET 
				userid	='{$this->userid}',
				editorid='{$this->editorid}', 
				partyid	='{$this->partyid}', 
				modul	='{$this->modul}', 
				movement='{$movement}',
				fix		='{$this->fix}',
				comment	='{$comment}'");
				
		$func->confirmation("Betrag von " . getMoneyColor($movement) . " erfolgreich von Modul " . $this->modul ." gebucht.", "");
	}
	

	function getCashTotalBudget()
	{
		global $db, $config;
		$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['cashmgr_accounting']} WHERE userid = {$this->userid} AND cash = '1'");
		return getMoneyColor($result['total']);
	}

	function getOnlineTotalBudget()
	{
		global $db, $config;
		$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['cashmgr_accounting']} WHERE userid = {$this->userid} AND cash = '0'");
		return getMoneyColor($result['total']);
	}


	function getAccounting()
	{
		global $db, $config, $dsp, $party;

	$dsp->NewContent(t('Meine Buchhaltung'), t('Übersicht aller Ausgaben und Einnahmen'));

	include_once('modules/mastersearch2/class_mastersearch2.php');
	$ms2 = new mastersearch2('news');

	
	$ms2->query['from'] = "{$config["tables"]["cashmgr_accounting"]} AS a
							LEFT JOIN {$config['tables']['user']} AS u ON a.editorid = u.userid";
	$ms2->query['default_order_by'] = 'actiontime DESC';
	$ms2->query['where'] = "a.userid = {$this->userid}";
	$ms2->config['EntriesPerPage'] = 20;
	
	$party_list = array('' => 'Alle');
	$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
	while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
	$db->free_result($row);

	$ms2->AddTextSearchDropDown('Party', 'a.partyid', $party_list, $party->party_id);
	$ms2->AddTextSearchDropDown('Zahlungsart', 'a.cash', array('' => 'Alle', 0 => 'Nur Online','1' => 'Nur Bar'));

	$ms2->AddResultField(t('Datum'), 'a.actiontime', 'MS2GetDate');
	$ms2->AddResultField(t('Modul'), 'a.modul');
	$ms2->AddResultField(t('Kommentar'), 'a.comment');
	$ms2->AddSelect('a.editorid');
	$ms2->AddResultField(t('Bearbeiter'), 'u.username', 'UserNameAndIcon');
	$ms2->AddResultField(t('Betrag'), 'a.movement', 'getMoneyColor');

	$ms2->PrintSearch('index.php?mod=cashmgr&action=account', 'a.id');
  
	}
	
	function getEnergyUsage($paid)
	{
		global $cfg, $db, $config;
		
		$partydate = $db->query_first("SELECT UNIX_TIMESTAMP(startdate) AS startdate, UNIX_TIMESTAMP(enddate) AS enddate FROM {$config['tables']['partys']} WHERE party_id = {$this->partyid}");
		$partytime = ($partydate['enddate'] - $partydate['startdate']) /3600;
		
		$query = $db->query("SELECT user_id FROM {$config['tables']['party_user']} WHERE party_id = {$this->partyid} AND paid != $paid");
		$result = $db->num_rows($query);
		return getMoneyColor($result * $cfg['cashmgr_kwhaverage_usage'] * $cfg['cashmgr_kwh'] * $partytime * (-1));
	}
	
	function getSum($fix, $posneg)
	{
		//$posneg = 0 -> alle negativen, 1 -> alle positiven, 3 -> alle
		global $db, $config;
	
		switch($posneg)
		{
		case 0:
			$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['cashmgr_accounting']} WHERE partyid = {$this->partyid} AND fix = $fix AND movement < 0");
			break;
		case 1:
			$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['cashmgr_accounting']} WHERE partyid = {$this->partyid} AND fix = $fix AND movement > 0");
			break;
		case 3:
			$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['cashmgr_accounting']} WHERE partyid = {$this->partyid} AND fix = $fix");
			break;
		}
		return getMoneyColor($result['total']);
	}

	function getGroup($fix, $posneg)
	{
		//$posneg = 0 -> alle negativen, 1 -> alle positiven, 3 -> alle
		global $db, $config;
		
		$result_list = array();
	
		switch($posneg)
		{
		case 0:
			$row = $db->query("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM {$config['tables']['cashmgr_accounting']} AS a LEFT JOIN {$config['tables']['cashmgr_group']} AS g ON a.groupid = g.id WHERE partyid = {$this->partyid} AND fix = $fix AND movement < 0 GROUP BY modul, caption");
			break;
		case 1:
			$row = $db->query("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM {$config['tables']['cashmgr_accounting']} AS a LEFT JOIN {$config['tables']['cashmgr_group']} AS g ON a.groupid = g.id  WHERE partyid = {$this->partyid} AND fix = $fix AND movement > 0 GROUP BY modul, caption");
			break;
		case 3:
			$row = $db->query("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM {$config['tables']['cashmgr_accounting']} AS a LEFT JOIN {$config['tables']['cashmgr_group']} AS g ON a.groupid = g.id  WHERE partyid = {$this->partyid} AND fix = $fix GROUP BY modul, caption");
			break;
		}
		
		while($res = $db->fetch_array($row)) 
		{
			if(isset($res['subjekt']))
				$arrobjekt = array($res['subjekt'], getMoneyColor($res['movement']));
			else
				$arrobjekt = array($res['subjekt_m'], getMoneyColor($res['movement']));
			$result_list[] = $arrobjekt;
		}
		$db->free_result($row);

		return $result_list;
	}

	function showCalculation()
	{
		global $dsp, $cfg, $templ;
		
		$dsp->AddFieldsetStart(t('Stromkosten '));
			$dsp->AddDoubleRow("Kosten laut Voranmeldung", $this->getEnergyUsage(1));
			$dsp->AddDoubleRow("Kosten laut Bezahlung", $this->getEnergyUsage(0));
		$dsp->AddFieldsetEnd();
		
		$dsp->AddFieldsetStart(t('Gruppenanzeige Positive Fixbetraege '));
			foreach($this->getGroup(1,1) AS $row)
				$dsp->AddDoubleRow($row[0], $row[1]);
			
			$templ['cashmgr']['bgcolor'] = "CCFFCC";
			$templ['cashmgr']['totalcaption'] = "Summe";
			$templ['cashmgr']['totalsum'] = $this->getSum(1,1);	
			$dsp->AddModTpl("cashmgr", "sum");
		$dsp->AddFieldsetEnd();
		
		$dsp->AddFieldsetStart(t('Gruppenanzeige Negative Fixbetraege '));
			foreach($this->getGroup(1,0) AS $row)
				$dsp->AddDoubleRow($row[0], $row[1]);
			
			$templ['cashmgr']['bgcolor'] = "FFCCCC";
			$templ['cashmgr']['totalcaption'] = "Summe";
			$templ['cashmgr']['totalsum'] = $this->getSum(1,0);	
			$dsp->AddModTpl("cashmgr", "sum");
		$dsp->AddFieldsetEnd();
		
		$dsp->AddFieldsetStart(t('Gruppenanzeige Einnahmen'));
			foreach($this->getGroup(0,1) AS $row1)
				$dsp->AddDoubleRow($row1[0], $row1[1]);
			
			$templ['cashmgr']['bgcolor'] = "CCFFCC";
			$templ['cashmgr']['totalcaption'] = "Summe";
			$templ['cashmgr']['totalsum'] = $this->getSum(0,1);	
			$dsp->AddModTpl("cashmgr", "sum");
		$dsp->AddFieldsetEnd();
		
		$dsp->AddFieldsetStart(t('Gruppenanzeige Ausgaben '));
			foreach($this->getGroup(0,0) AS $row)
				$dsp->AddDoubleRow($row[0], $row[1]);
			
			$templ['cashmgr']['bgcolor'] = "FFCCCC";
			$templ['cashmgr']['totalcaption'] = "Summe";
			$templ['cashmgr']['totalsum'] = $this->getSum(0,0);	
			$dsp->AddModTpl("cashmgr", "sum");		
		$dsp->AddFieldsetEnd();
	}
}
  
?>