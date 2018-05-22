<?php

namespace LanSuite\Module\CashMgr;

class Accounting
{
    /**
     * Editor ID
     *
     * @var int
     */
    private $editorid;

    /**
     * Party ID
     *
     * @var int
     */
    private $partyid;

    /**
     * Fixed costs / Fixed income?
     *
     * TODO Is this really in use?
     *
     * @var int
     */
    public $fix = 0;

    /**
     * @var string
     */
    private $modul;

    public function __construct($party_id = 0, $userid = null)
    {
        global $party, $auth;
        
        $this->modul = $_GET['mod'];
        
        if ($userid) {
            $this->editorid = $userid;
        } else {
            $this->editorid = $auth['userid'];
        }
        
        if ($party_id = 0) {
            $this->partyid = $party->party_id;
        } else {
            $this->partyid = $party_id;
        }
    }

    /**
     * @param float $money
     * @return string
     */
    private function getMoneyColor($money)
    {
        if ($money > 0) {
            return "<font color='green'>+".number_format($money, 2, ',', '.') . " EUR</font>";
        }

        if ($money < 0) {
            return "<font color='red'>".number_format($money, 2, ',', '.') . " EUR</font>";
        }

        return '';
    }

    /**
     * @param string    $movement
     * @param string    $comment
     * @param int       $toUserid
     * @param bool      $silentMode
     * @return void
     */
    public function booking($movement, $comment, $toUserid = 0, $silentMode = false)
    {
        global $func, $db;

        $db->qry(
            "INSERT INTO %prefix%cashmgr_accounting SET
                toUserid  =%int%,
                fromUserid=%int%,
                partyid =%int%,
                modul   =%string%,
                movement=%string%,
                fix     =%string%,
                comment =%string%",
            $toUserid,
            $this->editorid,
            $this->partyid,
            $this->modul,
            $movement,
            $this->fix,
            $comment
        );
                
        if (!$silentMode) {
            $func->confirmation("Betrag von " . $this->getMoneyColor($movement) . " erfolgreich von Modul " . $this->modul ." gebucht.", "");
        }
    }

    /**
     * @param int $userid
     * @return int
     */
    public function GetUserBalance($userid = 0)
    {
        global $db;

        if ($userid == 0) {
            $userid = $this->editorid;
        }

        $result = $db->qry_first("SELECT 
            (SELECT SUM(movement) FROM %prefix%cashmgr_accounting WHERE toUserid = %int%) AS received,
            (SELECT SUM(movement) FROM %prefix%cashmgr_accounting WHERE fromUserid = %int%) AS sent;", $userid, $userid);

        return $result['received'] - $result['sent'];
    }

    /**
     * @param int $paid
     * @return string
     */
    private function getEnergyUsage($paid)
    {
        global $cfg, $db;
        
        $partydate = $db->qry_first("SELECT UNIX_TIMESTAMP(startdate) AS startdate, UNIX_TIMESTAMP(enddate) AS enddate FROM %prefix%partys WHERE party_id = %int%", $this->partyid);
        $partytime = ($partydate['enddate'] - $partydate['startdate']) /3600;
        
        $query = $db->qry("SELECT user_id FROM %prefix%party_user WHERE party_id = %int% AND paid != %int%", $this->partyid, $paid);
        $result = $db->num_rows($query);

        return $this->getMoneyColor($result * $cfg['cashmgr_kwhaverage_usage'] * $cfg['cashmgr_kwh'] * $partytime * (-1));
    }

    /**
     * @param string    $fix
     * @param int       $posneg
     * @return string
     */
    private function getSum($fix, $posneg)
    {
        global $db;

        $result = [];
        switch ($posneg) {
            // All negative
            case 0:
                $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE partyid = %int% AND fix = %string% AND movement < 0", $this->partyid, $fix);
                break;

            // All positive
            case 1:
                $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE partyid = %int% AND fix = %string% AND movement > 0", $this->partyid, $fix);
                break;

            // All
            case 3:
                $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%cashmgr_accounting WHERE partyid = %int% AND fix = %string", $this->partyid, $fix);
                break;
        }

        return $this->getMoneyColor($result['total']);
    }

    /**
     * @param string    $fix
     * @param int       $posneg
     * @return array
     */
    private function getGroup($fix, $posneg)
    {
        global $db;
        
        $result_list = [];
    
        switch ($posneg) {
            // All negative
            case 0:
                $row = $db->qry("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM %prefix%cashmgr_accounting AS a LEFT JOIN %prefix%cashmgr_group AS g ON a.groupid = g.id WHERE partyid = %int% AND fix = %string% AND movement < 0 GROUP BY modul, caption", $this->partyid, $fix);
                break;

            // All positive
            case 1:
                $row = $db->qry("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM %prefix%cashmgr_accounting AS a LEFT JOIN %prefix%cashmgr_group AS g ON a.groupid = g.id  WHERE partyid = %int% AND fix = %string% AND movement > 0 GROUP BY modul, caption", $this->partyid, $fix);
                break;

            // All
            case 3:
                $row = $db->qry("SELECT SUM(movement) AS movement, modul AS subjekt_m, caption AS subjekt FROM %prefix%cashmgr_accounting AS a LEFT JOIN %prefix%cashmgr_group AS g ON a.groupid = g.id  WHERE partyid = %int% AND fix = %string% GROUP BY modul, caption", $this->partyid, $fix);
                break;
        }

        while ($res = $db->fetch_array($row)) {
            if (isset($res['subjekt'])) {
                $arrobjekt = array($res['subjekt'], $this->getMoneyColor($res['movement']));
            } else {
                $arrobjekt = array($res['subjekt_m'], $this->getMoneyColor($res['movement']));
            }
            $result_list[] = $arrobjekt;
        }
        $db->free_result($row);

        return $result_list;
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function showCalculation()
    {
        global $dsp, $smarty;
        
        $dsp->AddFieldsetStart(t('Stromkosten '));
        $dsp->AddDoubleRow("Kosten laut Voranmeldung", $this->getEnergyUsage(1));
        $dsp->AddDoubleRow("Kosten laut Bezahlung", $this->getEnergyUsage(0));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Positive Fixbetraege '));
        foreach ($this->getGroup(1, 1) as $row) {
            $dsp->AddDoubleRow($row[0], $row[1]);
        }
            
        $smarty->assign('bgcolor', 'CCFFCC');
        $smarty->assign('totalcaption', t('Summe'));
        $smarty->assign('totalsum', $this->getSum(1, 1));
        $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Negative Fixbetraege '));
        foreach ($this->getGroup(1, 0) as $row) {
            $dsp->AddDoubleRow($row[0], $row[1]);
        }
            
        $smarty->assign('bgcolor', 'CCFFCC');
        $smarty->assign('totalcaption', t('Summe'));
        $smarty->assign('totalsum', $this->getSum(1, 0));
        $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Einnahmen'));
        foreach ($this->getGroup(0, 1) as $row1) {
            $dsp->AddDoubleRow($row1[0], $row1[1]);
        }
            
        $smarty->assign('bgcolor', 'CCFFCC');
        $smarty->assign('totalcaption', t('Summe'));
        $smarty->assign('totalsum', $this->getSum(0, 1));
        $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
        $dsp->AddFieldsetEnd();
        
        $dsp->AddFieldsetStart(t('Gruppenanzeige Ausgaben '));
        foreach ($this->getGroup(0, 0) as $row) {
            $dsp->AddDoubleRow($row[0], $row[1]);
        }
            
        $smarty->assign('bgcolor', 'FFCCCC');
        $smarty->assign('totalcaption', t('Summe'));
        $smarty->assign('totalsum', $this->getSum(0, 0));
        $dsp->AddContentLine($smarty->fetch('modules/cashmgr/templates/sum.htm'));
        $dsp->AddFieldsetEnd();
    }
}
