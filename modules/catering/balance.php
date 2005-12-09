<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		balance.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 22:58
*	Description: 		Balance actions for users
*	Remarks: 		None
*			
******************************************************************************/


	$templ['cateringbalance']['show']['info']['rows']="<table width=\"100%\" border=\"0\">";
	$res = $db->query("SELECT actiontime,movement,comment FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$_SESSION['auth']['userid']."' ORDER BY actiontime");
	$availmon = 0;
	$i=0;
	while ($row = $db->fetch_array($res)) {
		if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
		else $bg="";
		$templ['cateringbalance']['show']['info']['rows'].="<tr $bg><td width=\"30%\" class=\"tbl_1\">".$row["actiontime"]."</td>";
		$templ['cateringbalance']['show']['info']['rows'].="<td width=\"50%\" class=\"tbl_1\">".$row["comment"]."</td>";
		$prmvm = number_format($row["movement"],2,",","."); 
		if ($row["movement"]<0) {
			$templ['cateringbalance']['show']['info']['rows'].="<td width=\"10%\" class=\"tbl_1\"><font color=\"#AA0000\">$prmvm</font></td>";
			$templ['cateringbalance']['show']['info']['rows'].="<td width=\"10%\" class=\"tbl_1\">&nbsp;</td>";
		} else {
			$templ['cateringbalance']['show']['info']['rows'].="<td width=\"10%\" class=\"tbl_1\">&nbsp;</td>";
			$templ['cateringbalance']['show']['info']['rows'].="<td width=\"10%\" class=\"tbl_1\"><font color=\"#000000\">$prmvm</font></td></tr>";			
		}
		$availmon += $row["movement"];
		$i++;
	}
	$templ['cateringbalance']['show']['info']['rows'].="<tr><td colspan=\"4\" align=\"right\" class=\"tbl_1\"><b>Verf&uuml;gbares Guthaben: ".sprintf("%01.2f",$availmon)." {$cfg["sys_currency"]}</b></td></tr></table>";
	
	eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("cateringbalance_show")."\";");


?>
