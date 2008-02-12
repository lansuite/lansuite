<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		theke.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 22:58
*	Description: 		theken bestellungen
*	Remarks: 		None
*			
******************************************************************************/

if ($_GET["action"]!="") $selaction = $_GET["action"];
else $selaction = $_POST["action"];

switch ($selaction) {
		
	case "thekenbestellung": 
		// get catering discount for current user
		$discres = $db->query("SELECT catering_discount FROM {$config["tables"]["user"]} WHERE userID='".$_POST["teilnehmer"]."'");
		$disctemp = $db->fetch_array($discres);
		$disc = $disctemp["catering_discount"];
		if ($disc=="") $disc = 0;
	
		// Lock Table, sicherstellen, dass der verfuegbare Betrag valid ist
		$db->query("LOCK TABLES {$config["tables"]["catering_accounting"]} WRITE, {$config["tables"]["catering_foods"]} READ, {$config["tables"]["catering_orders"]} WRITE, {$config["tables"]["catering_deliverylog"]} WRITE");
		
		// Eventuelle Einzahlung verbuchen
		if ($_POST["bargegeben"]!="" && $_POST["bargegeben"]>0) {
			$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_POST["teilnehmer"]."', actiontime=NOW(), movement='".$_POST["bargegeben"]."', comment='Bareinzahlung Thekenbestellung'");
		}
		
		$monres = $db->query("SELECT SUM(movement) as availmon FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$_POST["teilnehmer"]."'");
		$row = $db->fetch_array($monres);
		$availmon = sprintf("%01.2f", $row["availmon"]);

		// 1st get item price
		$r1 = $db->query("SELECT ek,price,title FROM {$config["tables"]["catering_foods"]} WHERE ID='".$_POST["produkt"]."' AND deleted='n'");
		$row = $db->fetch_array($r1);
		// manipulation verhindern
		if ($row["price"]=="") {
			$db->query("UNLOCK TABLES");
			header ("Location: ?mod=catering&action=theke");
		}
		if ($disc > 0) {
			$price = $row["price"] - ((($row["price"] - $row["ek"])/100)*$disc);
			$price = number_format($price, 2, '.', '');
		} else
			$price = $row["price"];
			
		$pname = $row["title"];
		if (strlen($pname)>40) $pname = substr($pname,0,40)." ...";
			
		$ordered = 0;
		$deferred = 0;
		$delivered = 0;
		for ($mc=0;$mc<$_POST["menge"];$mc++) {
			// check if user has enough money, if not lock order
			if ($price > $availmon) { 
				$deferred++; 
			} else { 
				// checken ob sofortige auslieferung stattfindet
				if ($_POST["ausgabe"]=="1") { $status=2; $delivered++; $ordered++;}
				else { $status = 0; $ordered++; }
				
				$r1 = $db->query("INSERT INTO {$config["tables"]["catering_orders"]} SET userID='".$_POST["teilnehmer"]."', foodID='".$_POST["produkt"]."', topay='".$price."'");
				$lid = $db->insert_id();
				$db->query("INSERT INTO {$config["tables"]["catering_deliverylog"]} SET orderID='".$lid."', actiontime=NOW(), orderstatus='".$status."'");
				if ($status==0 || $status==2) { // decrease available money
					$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_POST["teilnehmer"]."', actiontime=NOW(), movement='-".$price."', comment='Order: ".$pname."'");
				}
				$availmon-=$price;
			}
		}
		if ($_POST["auszahlen"]=="1") {
			$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_POST["teilnehmer"]."', actiontime=NOW(), movement='-".$availmon."', comment='Auszahlung Restbetrag Thekenbestellung'");
		} else $availmon=0;
		$db->query("UNLOCK TABLES");
		
		
		$templ['cateringtheke']['show']['info']['status'] = $ordered."x $pname bestellt (davon $delivered sofort ausgegeben)<br>".$deferred."x NICHT bestellt<br>".$availmon." EUR als Geldr&uuml;ckgabe verbucht!";
		
		
		
	
	default:
		$templ['cateringtheke']['show']['javascript'] = "var konto = new Array(); rabatt = new Array(); prodvk=new Array(); prodek=new Array();\n";
		$templ['cateringtheke']['show']['info']['title'] = "Thekenmodus";
		$res = $db->query("SELECT userid,LOWER(username) AS luser, catering_discount 
				FROM 	{$config["tables"]["user"]} 
				ORDER BY username");
		while ($arr = $db->fetch_array($res)) {
			$res2 = $db->query("SELECT SUM(movement) AS stand FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$arr["userid"]."'");
			$arr2 = $db->fetch_array($res2);
			$db->free_result($res2);
			$templ['cateringtheke']['show']['javascript'] .= "konto[".$arr["userid"]."]=".number_format($arr2["stand"], 2, '.', '')."; rabatt[".$arr["userid"]."]=".$arr["catering_discount"]."\n";
			$templ['cateringtheke']['show']['teilnehmer'] .="<option value=\"".$arr["userid"]."\">".$arr["luser"]."</option>\n";
		}

		$res = $db->query("SELECT F.title,F.ID,FG.name,F.price,F.ek,F.locked
				FROM 	{$config["tables"]["catering_foods"]} AS F,
						{$config["tables"]["catering_foodgroups"]} AS FG
				WHERE 	F.grpID=FG.ID AND F.deleted='n' AND wizzard='n'
				ORDER BY FG.name,F.title");
		while ($arr = $db->fetch_array($res)) {
			if (strlen($arr["title"])>40) $arr["title"]=substr($arr["title"],0,40)." ...";
			if ($arr["locked"]=="y") $lock=" GESPERRT "; else $lock="";
			$templ['cateringtheke']['show']['produkte'].="<option value=\"".$arr["ID"]."\">".$arr["name"]." - ".$arr["title"].$lock."</option>\n";
			$templ['cateringtheke']['show']['javascript'] .= "prodvk[".$arr["ID"]."]=".number_format($arr["price"], 2, '.', '')."; prodek[".$arr["ID"]."]=".$arr["ek"]."\n";
		}
		
		
		break;		
				
	
}	
	
eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("cateringtheke_show")."\";");


?>
