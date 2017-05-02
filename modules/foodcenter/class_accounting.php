<?php


class accounting
{
    public $user_id;
    public $balance;
    
    
    public function accounting($user_id)
    {
        global $db;
        
        $this->user_id = $user_id;
        
        if (isset($_SESSION['foodcenter']['account_block']) && $_SESSION['foodcenter']['account_block'] != $_SERVER['QUERY_STRING']) {
            unset($_SESSION['foodcenter']['account_block']);
        }
        $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = %int%", $this->user_id);
        
        if ($result['total'] == "") {
            $this->balance = 0;
        } else {
            $this->balance = $result['total'];
        }
    }
    
    
    public function get_balance()
    {
        return $this->balance;
    }
    
    public function change($price, $comment, $userid)
    {
        global $db;

   // echo("<script language='JavaScript'>alert('TEST');</script>");
        
        if (!isset($_SESSION['foodcenter']['account_block'])) {
            $db->qry("INSERT INTO %prefix%food_accounting SET userID=%int%, comment=%string%, movement=%string%,actiontime=NOW()", $userid, $comment, $price);
            $_SESSION['foodcenter']['account_block'] = $_SERVER['QUERY_STRING'];
        }
        
        $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userID = %int%", $userid);
        
        if ($result['total'] == "") {
            $this->balance = 0;
        } else {
            $this->balance = $result['total'];
        }
    }
    
    public function list_balance()
    {
        global $db,$dsp,$lang,$cfg;
        
        
        $result = $db->qry("SELECT *, DATE_FORMAT(actiontime,\"%d.%m.%y %H:%i\") AS time FROM %prefix%food_accounting WHERE userid = %int% ORDER BY actiontime DESC", $this->user_id);
        
        $deposit = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = %int% AND movement > 0", $this->user_id);
        
        $disbursement = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = %int% AND movement < 0", $this->user_id);
        
        
        $dsp->NewContent(t('Kontoauszug'), t('Alle bisher getätigten Zahlungen'));

        
        
        if ($this->balance > 0) {
            $dsp->AddDoubleRow("<strong>" . t('Total') . "</strong>", "<table width=\"100%\">
								<tr><td align=\"right\" width=\"33%\"><strong><font color='green'>" . round($deposit['total'], 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"33%\"><strong><font color='red'>" . round($disbursement['total'], 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($this->balance, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
        } else {
            $dsp->AddDoubleRow("<strong>" . t('Total') . "</strong>", "<table width=\"100%\">
								<tr><td align=\"right\" width=\"33%\"><strong><font color='green'>" . round($deposit['movement'], 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"33%\"><strong><font color='red'>" . round($disbursement['movement'], 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($this->balance, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
        }
        
        if ($db->num_rows($result) > 0) {
            $total = $this->balance;
            while ($row = $db->fetch_array($result)) {
                if ($row['movement'] > 0) {
                    if ($total > 0) {
                        $dsp->AddDoubleRow($row['time'] . "  " . $row['comment'], "<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>" . round($row['movement'], 2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>&nbsp;</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($total, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
                    } else {
                        $dsp->AddDoubleRow($row['time'] . "  " . $row['comment'], "<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>" . round($row['movement'], 2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>&nbsp;</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($total, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
                    }
                } else {
                    if ($total > 0) {
                        $dsp->AddDoubleRow($row['time'] . "  " . $row['comment'], "<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>&nbsp;</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>" . round($row['movement'], 2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($total, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
                    } else {
                        $dsp->AddDoubleRow($row['time'] . "  " . $row['comment'], "<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>&nbsp;</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>" . round($row['movement'], 2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($total, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
                    }
                }
                $total = $total - $row['movement'];
            }
        } else {
            $dsp->AddSingleRow("<strong>" . t('Keine Kontobewegungen') . "</strong>");
        }

        $dsp->AddContent();
    }
}
