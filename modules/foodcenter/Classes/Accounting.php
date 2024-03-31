<?php

namespace LanSuite\Module\Foodcenter;

class Accounting
{
    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $balance;

    public function __construct($user_id)
    {
        global $database;
        
        $this->user_id = $user_id;
        
        if (isset($_SESSION['foodcenter']['account_block']) && $_SESSION['foodcenter']['account_block'] != $_SERVER['QUERY_STRING']) {
            unset($_SESSION['foodcenter']['account_block']);
        }
        $result = $database->queryWithOnlyFirstRow("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = ?", [$this->user_id]);
        
        if ($result['total'] == "") {
            $this->balance = 0;
        } else {
            $this->balance = $result['total'];
        }
    }

    /**
     * @param string $price
     * @param string $comment
     * @param int $userid
     * @return void
     */
    public function change($price, $comment, $userid)
    {
        global $database;

        if (!isset($_SESSION['foodcenter']['account_block'])) {
            $database->query("INSERT INTO %prefix%food_accounting SET userID = ?, comment = ?, movement = ?, actiontime = NOW()", [$userid, $comment, $price]);
            $_SESSION['foodcenter']['account_block'] = $_SERVER['QUERY_STRING'];
        }
        
        $result = $database->queryWithOnlyFirstRow("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userID = ?", [$userid]);
        
        if ($result['total'] == "") {
            $this->balance = 0;
        } else {
            $this->balance = $result['total'];
        }
    }

    /**
     * @return void
     */
    public function list_balance()
    {
        global $db, $database, $dsp, $cfg;

        $result = $database->queryWithFullResult("SELECT *, DATE_FORMAT(actiontime,\"%d.%m.%y %H:%i\") AS time FROM %prefix%food_accounting WHERE userid = ? ORDER BY actiontime DESC", [$this->user_id]);
        $deposit = $database->queryWithOnlyFirstRow("SELECT movement, SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = ? AND movement > 0", [$this->user_id]);
        $disbursement = $database->queryWithOnlyFirstRow("SELECT movement, SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = ? AND movement < 0", [$this->user_id]);

        $depositTotal = $deposit['total'] ?? 0;
        $disbursementTotal = $disbursement['total'] ?? 0;

        $dsp->NewContent(t('Kontoauszug'), t('Alle bisher getätigten Zahlungen'));

        if ($this->balance > 0) {
            $dsp->AddDoubleRow("<strong>" . t('Total') . "</strong>", "<table width=\"100%\">
								<tr><td align=\"right\" width=\"33%\"><strong><font color='green'>" . round($depositTotal, 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"33%\"><strong><font color='red'>" . round($disbursementTotal, 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($this->balance, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
        } else {
            $dsp->AddDoubleRow("<strong>" . t('Total') . "</strong>", "<table width=\"100%\">
								<tr><td align=\"right\" width=\"33%\"><strong><font color='green'>" . round($deposit['movement'], 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"33%\"><strong><font color='red'>" . round($disbursement['movement'], 2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($this->balance, 2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");
        }
        
        if (count($result) > 0) {
            $total = $this->balance;
            foreach ($result as $row) {
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
    }
}
