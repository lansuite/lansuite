<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		deletefood.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 03:20
*	Description: 		deletes an entry in food menu
*	Remarks: 		None
*			
******************************************************************************/
  

$step = $_GET["step"];

switch($step) {
	
	default:
		$foodres = $db->query("	SELECT 	D.orderstatus 
					FROM 	{$config["tables"]["catering_orders"]} AS O,
						{$config["tables"]["catering_deliverylog"]} AS D
 					WHERE 	O.foodID=\"".$_GET["foodid"]."\" 
						AND O.ID=D.orderID
					");
		$openorder=0; $lockedorder=0; $orderedorder=0;
		while($row = $db->fetch_array($foodres)) {
			if ($row["orderstatus"]==0) $openorder++;
			if ($row["orderstatus"]==1) $orderedorder++;
			if ($row["orderstatus"]==3) $lockedorder++;
		}
		
		// checken, ob's ein parent für den Artikel gibt
		$fres2 = $db->query("SELECT ID FROM {$config["tables"]["catering_foods"]} WHERE parentID=\"".$_GET["foodid"]."\"");
		while ($row2 = $db->fetch_array($fres2)) {
			$foodres = $db->query("	SELECT 	D.orderstatus 
						FROM 	{$config["tables"]["catering_orders"]} AS O,
							{$config["tables"]["catering_deliverylog"]} AS D
	 					WHERE 	O.foodID=\"".$row2["ID"]."\" 
							AND O.ID=D.orderID
						");
			while($row = $db->fetch_array($foodres)) {
				if ($row["orderstatus"]==0) $openorder++;
				if ($row["orderstatus"]==1) $orderedorder++;
				if ($row["orderstatus"]==3) $lockedorder++;
			}

		}
		
		if ($openorder>0 || $orderedorder>0) {
			$func->error("Dieser Eintrag kann zum jetzigen Zeitpunkt nicht gel&ouml;scht werden!" . HTML_NEWLINE ." Dieser Artikel ist:" . HTML_NEWLINE ." <b>".$openorder."</b>mal zur Bestellung vorgesehen" . HTML_NEWLINE ." <b>".$orderedorder."</b>mal in der Lieferung begriffen.","");
			break;
		}
		if ($lockedorder>0) 
			$anno = "Achtung! Dieser Artikel wurde von Nutzern bestellt, aber noch nicht bezahlt. " . HTML_NEWLINE ."Das L&ouml;schen dieses Artikels l&ouml;scht auch diese Bestellungen.";
		else 
			$anno = "";
		
		$foodres = $db->query("SELECT * FROM {$config["tables"]["catering_foods"]} WHERE ID=\"".$_GET["foodid"]."\"");
		$row = $db->fetch_array($foodres);
		$func->question("Eintrag <b>".$row["title"]."</b> wirklich l&ouml;schen?" . HTML_NEWLINE . HTML_NEWLINE .$anno."","index.php?mod=catering&action=deletefood&foodid=".$_GET["foodid"]."&step=2","index.php?mod=catering");			
		break;
	
	case 2:
		$db->query("LOCK TABLES {$config["tables"]["catering_supplier"]} WRITE, {$config["tables"]["catering_foodgroups"]} WRITE, {$config["tables"]["catering_foods"]} WRITE, {$config["tables"]["catering_orders"]} WRITE, {$config["tables"]["catering_deliverylog"]} WRITE"); 
		// do database query
		$foodres = $db->query("SELECT picfile, grpID FROM {$config["tables"]["catering_foods"]} WHERE ID='".$_GET["foodid"]."'");
		$row = $db->fetch_array($foodres);
		// delete picture, if present
		if ($row["picfile"]!="") 
			@unlink($_SERVER["DOCUMENT_ROOT"]."/ext_inc/catering/".$row["picfile"]);
			
		// check for group change, delete old groups if neccessary
		$res2 = $db->query("SELECT COUNT(ID) as cntID FROM {$config["tables"]["catering_foods"]} WHERE grpID=\"".$row["grpID"]."\"");
		$row2 = $db->fetch_array($res2);
		if ($row2["cntID"]==1) {
			$db->query("DELETE FROM {$config["tables"]["catering_foodgroups"]} WHERE ID=\"".$row["grpID"]."\"");
		}

		// check for supplier, delete old supplier if neccessary
		$res3 = $db->query("SELECT COUNT(ID) as cntID FROM {$config["tables"]["catering_foods"]} WHERE supplID=\"".$row["supplID"]."\"");
		$row3 = $db->fetch_array($res3);
		if ($row3["cntID"]==1) {
			$db->query("DELETE FROM {$config["tables"]["catering_supplier"]} WHERE ID=\"".$row["supplID"]."\"");
		}		
		
		$add_it = $db->query("UPDATE {$config["tables"]["catering_foods"]} SET deleted='y' WHERE ID='".$_GET["foodid"]."' OR parentID='".$_GET["foodid"]."'");
		$add_it = $db->query("DELETE {$config["tables"]["catering_deliverylog"]},{$config["tables"]["catering_orders"]} 
					FROM {$config["tables"]["catering_deliverylog"]},{$config["tables"]["catering_orders"]} 
				     WHERE   {$config["tables"]["catering_orders"]}.foodID='".$_GET["foodid"]."' 
					AND {$config["tables"]["catering_deliverylog"]}.orderID={$config["tables"]["catering_orders"]}.ID"); 

		$db->query("UNLOCK TABLES");
		
		if($add_it == 1) 
			$func->confirmation("Die Speisekarte wurde ge&auml;ndert.","");
		break; 
		
}
?>
