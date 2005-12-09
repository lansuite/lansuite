<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		orders.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 22:58
*	Description: 		Order overview for users
*	Remarks: 		None
*			
******************************************************************************/

if ($_GET["action"]!="") $selaction = $_GET["action"];
else $selaction = $_POST["action"];

$templ['cateringorders']['show']['info']['rows']="<table width=\"100%\" border=\"0\">";
switch ($selaction) {

	default:

			$templ['cateringorders']['show']['info']['title'] = "Meine Bestellungen";
//			$res = $db->query("SELECT F.title,O.topay,D.orderstatus,D.actiontime,O.ID
//						FROM	{$config["tables"]["catering_orders"]} AS O,
//							{$config["tables"]["catering_deliverylog"]} AS D,
//							{$config["tables"]["catering_foods"]} AS F 
//						WHERE	O.userID='".$_SESSION['auth']['userid']."'
//							AND O.foodID=F.ID  
//							AND D.orderID=O.ID 
//						ORDER BY D.orderstatus");

			$i=0;
			$notpaid = 0;

			$o_res = $db->query("SELECT O.ID, O.foodID, O.addIDs, O.wizzard, O.size, O.topay, D.orderstatus, D.actiontime 
								FROM {$config["tables"]["catering_orders"]} as O, 
								{$config["tables"]["catering_deliverylog"]} AS D 
								WHERE O.userID='".$_SESSION['auth']['userid']."' 
								AND D.orderID=O.ID 
								ORDER BY D.orderstatus");
			
			if ($db->num_rows($o_res)==0) {
				$templ['cateringorders']['show']['info']['rows'].="<tr><td class=\"tbl_1\"><center>Keine Bestellungen vorhanden!</center></td></tr>";
				break;
			}
			while ($o_row = $db->fetch_array($o_res)) {
				if($o_row['wizzard']=="y"){
					$f_res = $db->query("SELECT title FROM {$config["tables"]["catering_wizzard"]} 
										WHERE foodID='".$o_row["foodID"]."'
										AND master='y'");
					$f_row = $db->fetch_array($f_res);
					$build_title = "";
					if($o_row["size"]=="s") {$groesse="klein";}
					if($o_row["size"]=="m") {$groesse="mittel";}
					if($o_row["size"]=="l") {$groesse="groß";}
					if($o_row["size"]=="p") {$groesse="Pfannenpizza";}
					$build_title .= $f_row["title"] . " [". $groesse ."] (";

					$first_add = true;
					$new_title="";
					$addids = split("/",$o_row["addIDs"]);
					foreach($addids as $addid) {
						if($addid!=""){
							if($first_add==false)
								$build_title .= $new_title . ", ";
	
							$res = $db->query("SELECT title FROM {$config["tables"]["catering_wizzard"]} 
										WHERE ID='".$addid."'
										AND master='n'");
							$row = $db->fetch_array($res);
							$new_title = $row["title"];
							$first_add=false;
						}
					}
					$build_title .=  $new_title . ")";
					$title = $build_title;

					if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
					else $bg="";
					$templ['cateringorders']['show']['info']['rows'].="<tr $bg><td width=\"20%\">".$o_row["actiontime"]."</td>";
					$templ['cateringorders']['show']['info']['rows'].="<td width=\"10%\">";
					if ($o_row["orderstatus"]==3 || $o_row["orderstatus"]==0) {
						$templ['cateringorders']['show']['info']['rows'].="
						<a href=\"index.php?mod=catering&action=deleteopenorder&oid=".$o_row["ID"]."\">	
							<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_delete.gif\" align=\"middle\" border=\"0\">
						</a>";
					}		
					$templ['cateringorders']['show']['info']['rows'].="</td><td width=\"40%\" class=\"tbl_1\">".$title."</td>";
					if ($o_row["orderstatus"]==0) $ost = "angenommen";
					if ($o_row["orderstatus"]==1) $ost = "bestellt";
					if ($o_row["orderstatus"]==2) $ost = "ausgeliefert";
					if ($o_row["orderstatus"]==3) { $ost = "nicht bezahlt"; $notpaid += $o_row["topay"]; }
					$templ['cateringorders']['show']['info']['rows'].="<td width=\"15%\" class=\"tbl_1\">".$ost;
					$templ['cateringorders']['show']['info']['rows'].="</td>";
					$templ['cateringorders']['show']['info']['rows'].="<td width=\"25%\" class=\"tbl_1\">{$cfg["sys_currency"]} ".sprintf("%01.2f",$o_row["topay"])."</td></tr>";
					$i++;

				}
				else {
					$f_res = $db->query("SELECT title FROM {$config["tables"]["catering_foods"]} 
										WHERE ID='".$o_row["foodID"]."'");
					$f_row = $db->fetch_array($f_res);
					
					if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
					else $bg="";
					$templ['cateringorders']['show']['info']['rows'].="<tr $bg><td width=\"20%\">".$o_row["actiontime"]."</td>";
					$templ['cateringorders']['show']['info']['rows'].="<td width=\"10%\">";
					if ($o_row["orderstatus"]==3 || $o_row["orderstatus"]==0) {
						$templ['cateringorders']['show']['info']['rows'].="
						<a href=\"index.php?mod=catering&action=deleteopenorder&oid=".$o_row["ID"]."\">	
							<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_delete.gif\" align=\"middle\" border=\"0\">
						</a>";
					}		
					$templ['cateringorders']['show']['info']['rows'].="</td><td width=\"40%\" class=\"tbl_1\">".$f_row["title"]."</td>";
					if ($o_row["orderstatus"]==0) $ost = "angenommen";
					if ($o_row["orderstatus"]==1) $ost = "bestellt";
					if ($o_row["orderstatus"]==2) $ost = "ausgeliefert";
					if ($o_row["orderstatus"]==3) { $ost = "nicht bezahlt"; $notpaid += $o_row["topay"]; }
					$templ['cateringorders']['show']['info']['rows'].="<td width=\"15%\" class=\"tbl_1\">".$ost;
					$templ['cateringorders']['show']['info']['rows'].="</td>";
					$templ['cateringorders']['show']['info']['rows'].="<td width=\"25%\" class=\"tbl_1\">{$cfg["sys_currency"]} ".sprintf("%01.2f",$o_row["topay"])."</td></tr>";
					$i++;
				}
			}
			$templ['cateringorders']['show']['info']['rows'].="<tr><td colspan=\"4\" align=\"right\" class=\"tbl_1\">nicht bezahlt:&nbsp;</td><td class=\"tbl_1\"><b>{$cfg["sys_currency"]} ".sprintf("%01.2f",$notpaid)."</b></td></tr>";

		break;
		
	case "deleteopenorder":
		if ($_GET["step"]=="2") {
			$res = $db->query("SELECT O.topay,D.orderstatus FROM {$config["tables"]["catering_orders"]} AS O, {$config["tables"]["catering_deliverylog"]} AS D WHERE O.ID=\"".$_GET["oid"]."\" AND O.userID=\"".$_SESSION["auth"]["userid"]."\" AND D.orderID=O.ID");
			$ost = -1;
			if ($db->num_rows($res) == 0) break; // User versucht, eine nicht ihm gehoerende Order zu loeschen
			else {
				$row = $db->fetch_array($res);
				$topay = $row["topay"];
				$ost = $row["orderstatus"];
			}
	
			$db->query("DELETE FROM {$config["tables"]["catering_orders"]} WHERE ID=\"".$_GET["oid"]."\" AND userID=\"".$_SESSION["auth"]["userid"]."\"");
			$db->query("DELETE FROM {$config["tables"]["catering_deliverylog"]} WHERE orderID=\"".$_GET["oid"]."\"");
			
			// rueckzahlung, wenn verbucht
			if ($ost == "0") {
				$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET movement=\"".$topay."\", comment=\"Storno\", actiontime=NOW(), userID=\"".$_SESSION["auth"]["userid"]."\"");	
			}
			
			header("Location: ?mod=catering&action=showmyorders");
			break;
		} else {
			$func->question("Order wirklich löschen?","index.php?mod=catering&action=deleteopenorder&oid=".$_GET["oid"]."&step=2","index.php?mod=catering&action=showmyorders");
			break;
		}
		break;
			
}

$templ['cateringorders']['show']['info']['rows'].="</table>";
eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("cateringorders_show")."\";");


?>
