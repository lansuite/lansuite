<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		accounting.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 17:59
*	Description: 		Accounting actions for users
*	Remarks: 		None
*			
******************************************************************************/

if ($_GET["action"]=="") 
	$selact = $_POST["action"];
else 
	$selact = $_GET["action"];


// Error switch
switch ($selact) {
	case "savemovement":
		$_POST["cateringaccounting_amount"] = str_replace(",",".",$_POST["cateringaccounting_amount"]);
		if(is_numeric($_POST["cateringaccounting_amount"]) == FALSE) {
			$error_catering_text = $func->generate_error_template("cateringaccounting_form","amount", HTML_NEWLINE . "Der zu verbuchende Betrag muss numerisch sein!");
			eval($error_catering_text);
			$selact = "accforuser";
			$_GET["userid"]=$_POST["userid"];
		}
		
		if ($_POST["cateringaccounting_amount"] == 0) {
			$error_catering_text = $func->generate_error_template("cateringaccounting_form","amount", HTML_NEWLINE . "Der zu verbuchende Betrag muss ungleich NULL sein!");
			eval($error_catering_text);
			$selact = "accforuser";
			$_GET["userid"]=$_POST["userid"];
		}
}
 
	
	
switch ($selact) {
	default:
		if ($auth["type"] >= 2 AND $action!= "guestlist") $additional = " GROUP BY email";
		else $additional = " AND (type < 2) AND {p.party_id=$party->party_id}) GROUP BY email";

		$mastersearch = new MasterSearch($vars, "index.php?mod=catering&action=accounting", "index.php?mod=catering&action=accforuser&userid=", $additional);
		$mastersearch->LoadConfig("users", "Benutzer suchen", "Benutzerauswahl: Ergebnis");
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();	
	break;

	// Sehr kritisch, genauestens auf TABLE LOCKS achten
	case "savemovement":
		$db->query("LOCK TABLES	{$config["tables"]["catering_accounting"]} WRITE,
					{$config["tables"]["catering_deliverylog"]} WRITE, 
					{$config["tables"]["catering_orders"]} READ, 
					{$config["tables"]["user"]} READ, 
					{$config["tables"]["catering_foods"]} READ");
		
		$monres = $db->query("SELECT SUM(movement) as availmon FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$_POST["userid"]."'");
		$row = $db->fetch_array($monres);
		$availmon = sprintf("%01.2f", $row["availmon"]);

		if (($availmon + $_POST["cateringaccounting_amount"]) < 0) {
			$func->error("Der resultierende Betrag ist kleiner 0!!! ... Abbruch!","");
			$db->query("UNLOCK TABLES");
			break;
		}
		
		if ($_POST["cateringaccounting_amount"] < 0)  {
			$ures = $db->query("SELECT username FROM {$config["tables"]["user"]} WHERE userid=\"".$_SESSION["auth"]["userid"]."\"");
			$urow = $db->fetch_array($ures);
			$scomment = "Soll Buchung vorgenommen von: ".$urow["username"];
		} else {
			$ures = $db->query("SELECT username FROM {$config["tables"]["user"]} WHERE userid=\"".$_SESSION["auth"]["userid"]."\"");
			$urow = $db->fetch_array($ures);
			$scomment = "Haben Buchung vorgenommen von: ".$urow["username"];
		}
		
		$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_POST["userid"]."', actiontime=NOW(), comment=\"".$_POST["cateringaccounting_comment"]." - ".$scomment."\", movement='".$_POST["cateringaccounting_amount"]."'");

		$availmon = ($availmon + $_POST["cateringaccounting_amount"]);
		// check if there are locked orders ... unlock them if possible
		// availmon ist valid, weil kein anderer Prozess, die Kontoinformationen ändern kann
		$res = $db->query("SELECT 
					{$config["tables"]["catering_foods"]}.title, 
					{$config["tables"]["catering_deliverylog"]}.ID,
					{$config["tables"]["catering_deliverylog"]}.orderID,
					{$config["tables"]["catering_orders"]}.topay,
					{$config["tables"]["catering_orders"]}.foodID 
				   FROM {$config["tables"]["catering_deliverylog"]}, 
					{$config["tables"]["catering_orders"]},
					{$config["tables"]["catering_foods"]}
				   WHERE {$config["tables"]["catering_deliverylog"]}.orderID={$config["tables"]["catering_orders"]}.ID 
					AND {$config["tables"]["catering_deliverylog"]}.orderstatus=3 
					AND {$config["tables"]["catering_orders"]}.userID={$_POST["userid"]}
					AND {$config["tables"]["catering_orders"]}.foodID={$config["tables"]["catering_foods"]}.ID 
					AND DATE_ADD({$config["tables"]["catering_deliverylog"]}.actiontime, INTERVAL 1 HOUR) > NOW()");
			
		while ($row = $db->fetch_array($res)) {
			if ($availmon >= $row["topay"]) {
				if ($unlocked == "") $unlocked= HTML_NEWLINE . HTML_NEWLINE . "<b>Folgende Bestellungen wurden freigegeben:</b>";
				$unlocked .= HTML_NEWLINE . "Bestellung von ".$row["title"]." freigegeben.";
				$db->query("UPDATE {$config["tables"]["catering_deliverylog"]} SET orderstatus='0' WHERE ID='".$row["ID"]."'");
				$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_POST["userid"]."', actiontime=NOW(), comment=\"Order ID ".$row["orderID"]." unlocked.\", movement='-".$row["topay"]."'");
				$availmon-=$row["topay"];
			}
		}
		
		$db->query("UNLOCK TABLES");
		$func->confirmation("Die Zahlung wurde verbucht.".$unlocked,"");

		break;
		
		
	case "accforuser":
		$cares = $db->query("SELECT SUM(movement) AS curamount FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$_GET["userid"]."'");
		$row = $db->fetch_array($cares);
		if ($row["curamount"]=="") 
			$_POST["cateringaccounting_curamount"]=sprintf ("%01.2f", 0);
		else	
			$_POST["cateringaccounting_curamount"] = sprintf ("%01.2f", $row["curamount"]);
		$templ['cateringaccounting']['form']['info']['page_title']	= "Kontobewegung hinzuf&uuml;gen";
		$templ['cateringaccounting']['form']['info']['page_description']	= "Mit Hilfe des Dialoges k&ouml;nnen Sie Ein-/Auszahlungen auf Benutzerkonten verbuchen.";	
		$templ['cateringaccounting']['form']['control']['form_action'] 	= "index.php?mod=catering&action=savemovement";
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("cateringaccounting_form")."\";");
		break;

		
	

}	


?>
