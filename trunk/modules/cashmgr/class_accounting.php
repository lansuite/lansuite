<?php

    function getMoneyColor($money)
    {
        if($money > 0)
            return "<font color='green'>+".number_format($money, 2, ',', '.') . " EUR</font>";
        
        if($money < 0)
            return "<font color='red'>".number_format($money, 2, ',', '.') . " EUR</font>";
    }


class accounting
    {
    var $editorid;      //Bearbeiter
    var $partyid;       //Party
    var $fix = 0;           //Fixkosten / Fixeinnahmen?
    var $modul;

    /**
    * Konstruktor
    */
    function accounting($party_id = 0, $userid = null)
    { 
    	global $party, $auth;
    	
        $this->modul = $_GET['mod'];
        
        if($userid) $this->editorid = $userid;
        else $this->editorid = $auth['userid'];
        
        if($party_id = 0) $this->partyid = $party->party_id;
        else $this->partyid = $party_id;
    }
    
    /**
    * Buchung
    * @param $movement
    * @param String $comment
    * @param int $toUserID
    * @param boolean $silentMode
    */
    function booking($movement, $comment, $toUserid = 0, $silentMode = false)
    {
        global $func, $db;
                   
        global $db,$config;
            $db->qry("INSERT INTO %prefix%cashmgr_accounting SET 
                toUserid  =%int%,
                fromUserid=%int%,
                partyid =%int%,
                modul   =%string%,
                movement=%string%,
                fix     =%string%,
                comment =%string%",
                $toUserid, $this->editorid, $this->partyid, $this->modul, $movement, $this->fix, $comment);
                
        if(!$silentMode) $func->confirmation("Betrag von " . getMoneyColor($movement) . " erfolgreich von Modul " . $this->modul ." gebucht.", "");
    }
    

    function getCashTotalBudget()
    {
        global $db, $config;
        $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE userid = %int% AND cash = '1'", $this->userid);
        return getMoneyColor($result['total']);
    }

    function getOnlineTotalBudget()
    {
        global $db, $config;
        $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE userid = %int% AND cash = '0'", $this->userid);
        return getMoneyColor($result['total']);
    }

    
    function getEnergyUsage($paid)
    {
        global $cfg, $db, $config;
        
        $partydate = $db->qry_first("SELECT UNIX_TIMESTAMP(startdate) AS startdate, UNIX_TIMESTAMP(enddate) AS enddate FROM %prefix%partys WHERE party_id = %int%", $this->partyid);
        $partytime = ($partydate['enddate'] - $partydate['startdate']) /3600;
        
        $query = $db->qry("SELECT user_id FROM %prefix%party_user WHERE party_id = %int% AND paid != %int%", $this->partyid, $paid);
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
            $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE partyid = %int% AND fix = %string% AND movement < 0", $this->partyid, $fix);
            break;
        case 1:
            $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE partyid = %int% AND fix = %string% AND movement > 0", $this->partyid, $fix);
            break;
        case 3:
            $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE partyid = %int% AND fix = %string", $this->partyid, $fix);
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
            $row = $db->qry("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM %prefix%cashmgr_accounting AS a LEFT JOIN %prefix%cashmgr_group AS g ON a.groupid = g.id WHERE partyid = %int% AND fix = %string% AND movement < 0 GROUP BY modul, caption", $this->partyid, $fix);
            break;
        case 1:
            $row = $db->qry("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM %prefix%cashmgr_accounting AS a LEFT JOIN %prefix%cashmgr_group AS g ON a.groupid = g.id  WHERE partyid = %int% AND fix = %string% AND movement > 0 GROUP BY modul, caption", $this->partyid, $fix);
            break;
        case 3:
            $row = $db->qry("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM %prefix%cashmgr_accounting AS a LEFT JOIN %prefix%cashmgr_group AS g ON a.groupid = g.id  WHERE partyid = %int% AND fix = %string% GROUP BY modul, caption", $this->partyid, $fix);
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
        global $dsp, $cfg, $smarty;
        
        $dsp->AddFieldsetStart(t('Stromkosten '));
            $dsp->AddDoubleRow("Kosten laut Voranmeldung", $this->getEnergyUsage(1));
            $dsp->AddDoubleRow("Kosten laut Bezahlung", $this->getEnergyUsage(0));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Positive Fixbetraege '));
            foreach($this->getGroup(1,1) AS $row)
                $dsp->AddDoubleRow($row[0], $row[1]);
            
            $smarty->assign('bgcolor', 'CCFFCC');
            $smarty->assign('totalcaption', t('Summe'));
            $smarty->assign('totalsum', $this->getSum(1,1));
            $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Negative Fixbetraege '));
            foreach($this->getGroup(1,0) AS $row)
                $dsp->AddDoubleRow($row[0], $row[1]);
            
            $smarty->assign('bgcolor', 'CCFFCC');
            $smarty->assign('totalcaption', t('Summe'));
            $smarty->assign('totalsum', $this->getSum(1,0));
            $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Einnahmen'));
            foreach($this->getGroup(0,1) AS $row1)
                $dsp->AddDoubleRow($row1[0], $row1[1]);
            
            $smarty->assign('bgcolor', 'CCFFCC');
            $smarty->assign('totalcaption', t('Summe'));
            $smarty->assign('totalsum', $this->getSum(0,1));
            $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
       $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Ausgaben '));
            foreach($this->getGroup(0,0) AS $row)
                $dsp->AddDoubleRow($row[0], $row[1]);
            
            $smarty->assign('bgcolor', 'FFCCCC');
            $smarty->assign('totalcaption', t('Summe'));
            $smarty->assign('totalsum', $this->getSum(0,0));
            $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
            $dsp->AddFieldsetEnd();
   }
}
?>

