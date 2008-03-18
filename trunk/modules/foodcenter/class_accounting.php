<?php


class accounting{
	
	var $user_id;
	var $balance;
	
	
	function accounting($user_id){
		global $db,$config;
		
		$this->user_id = $user_id;
		
		if(isset($_SESSION['foodcenter']['account_block']) && $_SESSION['foodcenter']['account_block'] != $_SERVER['QUERY_STRING']){
			unset($_SESSION['foodcenter']['account_block']);
		}
		$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['food_accounting']} WHERE userid = {$this->user_id}");
		
		if($result['total'] == ""){
			$this->balance = 0;
		}else {
			$this->balance = $result['total'];
		}
	}
	
	
	function get_balance(){
		return $this->balance;
	}
	
	function change($price,$comment){
		global $db,$config;
		if(!isset($_SESSION['foodcenter']['account_block'])){
			$db->query("INSERT INTO {$config['tables']['food_accounting']} SET userid='{$this->user_id}', comment='{$comment}', movement='{$price}',actiontime=NOW()");
			$_SESSION['foodcenter']['account_block'] = $_SERVER['QUERY_STRING'];
		}
		
		$result = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['food_accounting']} WHERE userid = '{$this->user_id}'");
		
		if($result['total'] == ""){
			$this->balance = 0;
		}else {
			$this->balance = $result['total'];
		}
	}
	
	function list_balance(){
		global $db,$config,$dsp,$lang,$cfg;
		
		$result = $db->query("SELECT *, DATE_FORMAT(actiontime,\"%d.%m.%y %H:%i\") AS time FROM {$config['tables']['food_accounting']} WHERE userid = '{$this->user_id}' ORDER BY actiontime DESC");
		
		$deposit = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['food_accounting']} WHERE userid = '{$this->user_id}' AND movement > 0");
		
		$disbursement = $db->query_first("SELECT SUM(movement) AS total FROM {$config['tables']['food_accounting']} WHERE userid = '{$this->user_id}' AND movement < 0");
		
		
		$dsp->NewContent(t('Kontoauszug')	,t('Alle bisher getätigten Zahlungen')	);

		
		
		if($this->balance > 0){
			$dsp->AddDoubleRow("<strong>" . t('Total') . "</strong>","<table width=\"100%\">
								<tr><td align=\"right\" width=\"33%\"><strong><font color='green'>" . round($deposit['total'],2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"33%\"><strong><font color='red'>" . round($disbursement['total'],2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($this->balance,2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");	
		}else{
			$dsp->AddDoubleRow("<strong>" . t('Total') . "</strong>","<table width=\"100%\">
								<tr><td align=\"right\" width=\"33%\"><strong><font color='green'>" . round($deposit['movement'],2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"33%\"><strong><font color='red'>" . round($disbursement['movement'],2) . " " . $cfg['sys_currency'] ."</font></strong></td>
								<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($this->balance,2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");	
		}
		
		if($db->num_rows($result) > 0){
			$total = $this->balance;
			while ($row = $db->fetch_array($result)){
				
				if($row['movement'] > 0){
					if($total > 0){
						$dsp->AddDoubleRow($row['time'] . "  " . $row['comment'],"<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>" . round($row['movement'],2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>&nbsp;</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($total,2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");	
					}else{
						$dsp->AddDoubleRow($row['time'] . "  " . $row['comment'],"<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>" . round($row['movement'],2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>&nbsp;</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($total,2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");	
					}
				}else{
					if($total > 0){
						$dsp->AddDoubleRow($row['time'] . "  " . $row['comment'],"<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>&nbsp;</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>" . round($row['movement'],2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='green'>" . round($total,2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");	
					}else{
						$dsp->AddDoubleRow($row['time'] . "  " . $row['comment'],"<table width=\"100%\">
							<tr><td align=\"right\" width=\"33%\"><font color='green'>&nbsp;</font></td>
							<td align=\"right\" width=\"33%\"><font color='red'>" . round($row['movement'],2) . " " . $cfg['sys_currency'] ."</font></td>
							<td align=\"right\" width=\"34%\"><strong><font color='red'>" . round($total,2) . " " . $cfg['sys_currency'] ."</font></strong></td></tr></table>");	
					}

				}
				$total = $total - $row['movement'];
			}
		}else{
			$dsp->AddSingleRow("<strong>" . t('Keine Kontobewegungen') . "</strong>");
		}

		$dsp->AddContent();
	}
}






?>