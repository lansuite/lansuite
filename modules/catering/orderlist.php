<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		1.0
*	Filename: 		orderlist.php
*	Module: 		Catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		25.05.2003 22:58
*	Description: 		Admin order list
*	Remarks: 		None
*			
******************************************************************************/

function stripString($zk, $zkl) {
	if (strlen($zk) > $zkl) {
		$zk = substr ($zk, 0, ($zkl-3)); 
		$zk .= "...";
	}
	return $zk;
} 

if ($_GET["action"]!="") $selaction = $_GET["action"];
else $selaction = $_POST["action"];

$templ['cateringorderlist']['show']['info']['rows']="<table width=\"100%\" border=\"0\">";
switch ($selaction) {
	default:
		$templ['cateringorderlist']['show']['info']['title'] = "Bestellisten";
		$res = $db->query("SELECT OL.ID,OL.uqID, U.username 
				FROM 	{$config["tables"]["catering_orderlogs"]} AS OL,
					{$config["tables"]["catering_deliverylog"]} AS D,
					{$config["tables"]["user"]} AS U
				WHERE 	OL.userID=U.userid AND OL.orderID=D.orderID AND D.orderstatus=\"1\"
				GROUP BY OL.uqID ORDER BY OL.actiontime DESC");
		
		$i=0;
		$templ['cateringorderlist']['show']['info']['rows'].="
			<tr>
				<td colspan=\"3\" class=\"tbl_1\"><b>Aktuelle Liste</b></td>
			</tr>
			<tr>
				<td colspan=\"3\">
					<a href=\"index.php?mod=catering&action=openorders\">	
					<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_details.gif\" border=\"0\">
					</a>
				</td>
			</tr>
			<tr>
				<td colspan=\"3\">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=\"3\" class=\"tbl_1\"><b>Noch nicht gelieferte Bestellungen</b></td>
			</tr>";
		// Noch nicht gelieferte Bestellungen
		if ($db->num_rows($res)==0) {
			$templ['cateringorderlist']['show']['info']['rows'].="	
				<tr><td colspan=\"5\" class=\"tbl_1\">Keine nicht-gelieferten Bestellungen vorhanden.</td></tr>";
		} else {
			while ($row = $db->fetch_array($res)) {
				if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
				else $bg="";
				$templ['cateringorderlist']['show']['info']['rows'].="<tr $bg><td width=\"50%\" class=\"tbl_1\">".$row["uqID"]."</td>";
				$templ['cateringorderlist']['show']['info']['rows'].="<td width=\"20%\" class=\"tbl_1\">".$row["username"]."</td>";
				$templ['cateringorderlist']['show']['info']['rows'].="
					<td width=\"30%\" class=\"tbl_1\">
					<a href=\"index.php?mod=catering&action=orderlistdetails&olid=".$row["ID"]."&ost=1\">	
						<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_details.gif\" border=\"0\">
					</a>
					</td>
					</tr>";
				$i++;
			}
		}
		
		
		// Bereits gelieferte Bestellungen
		$res = $db->query("SELECT OL.ID,OL.uqID, U.username 
				FROM 	{$config["tables"]["catering_orderlogs"]} AS OL,
					{$config["tables"]["catering_deliverylog"]} AS D,
					{$config["tables"]["user"]} AS U
				WHERE 	OL.userID=U.userid AND OL.orderID=D.orderID AND D.orderstatus=\"2\"
				GROUP BY OL.uqID ORDER BY OL.actiontime DESC ");
		
		$i=0;
		$templ['cateringorderlist']['show']['info']['rows'].="
			<tr>
				<td colspan=\"3\">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=\"3\" class=\"tbl_1\"><b>Bereits gelieferte Bestellungen</b></td>
			</tr>";
		if ($db->num_rows($res)==0) {
			$templ['cateringorderlist']['show']['info']['rows'].="	
				<tr><td colspan=\"5\" class=\"tbl_1\">Keine bereits ausgelieferten Bestellungen vorhanden.</td></tr>";
			break;
		}
		while ($row = $db->fetch_array($res)) {
			if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
			else $bg="";
			$templ['cateringorderlist']['show']['info']['rows'].="<tr $bg><td width=\"50%\" class=\"tbl_1\">".$row["uqID"]."</td>";
			$templ['cateringorderlist']['show']['info']['rows'].="<td width=\"20%\" class=\"tbl_1\">".$row["username"]."</td>";
			$templ['cateringorderlist']['show']['info']['rows'].="
				<td width=\"30%\">
				<a href=\"index.php?mod=catering&action=orderlistdetails&olid=".$row["ID"]."&ost=2\">	
					<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_details.gif\" border=\"0\">
				</a>&nbsp;
				<a href=\"index.php?mod=catering&action=orderlistdelete&olid=".$row["ID"]."&ost=2\">	
					<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_delete.gif\" border=\"0\">
				</a>				
				</td>
				</tr>";
			$i++;
		}
		
		break;
		
		
		
		
		
		
	case "orderlistdetails":
		$templ['cateringorderlist']['show']['info']['title'] = "Bestellisten - Listendetails";
		$templ['cateringorderlist']['show']['info']['rows'].="
			<tr>
				<td colspan=\"3\">&nbsp;</td>
			</tr>";
		$res = $db->query("SELECT uqID FROM {$config["tables"]["catering_orderlogs"]} WHERE ID=\"".$_GET["olid"]."\"");
		$row = $db->fetch_array($res);
		$uqid = $row["uqID"];
		$res = $db->query("SELECT O.ID,O.topay,F.title,U.username,F.supplID,F.supplHint,F.ek,U2.username AS admUser, O.addIDs, O.wizzard, O.foodID, O.size
				FROM 	{$config["tables"]["catering_orders"]} AS O,
					{$config["tables"]["catering_orderlogs"]} AS OL,
					{$config["tables"]["catering_foods"]} AS F,
					{$config["tables"]["catering_deliverylog"]} AS D,
					{$config["tables"]["user"]} AS U,
					{$config["tables"]["user"]} AS U2
				WHERE 	OL.uqID=\"".$uqid."\"
					AND OL.orderID=O.ID
					AND OL.orderID=D.orderID
					AND OL.userID=U2.userID
					AND O.userID=U.userid
					AND O.foodID=F.ID
					AND D.orderstatus=\"".$_GET["ost"]."\"
				ORDER BY F.supplID,U.username
				");
		$i=0;
		
		$res2 = $db->query("SELECT * FROM {$config["tables"]["catering_supplier"]}");
		while($row3=$db->fetch_array($res2)) 
			$supplier["{$row3["ID"]}"]=$row3["contact"];
		$supplier["0"] = "keiner";
		
		$templ['cateringorderlist']['show']['info']['rows'].="
			<form method=\"POST\" action=\"index.php?mod=catering&action=delivery\">
			<tr>
				<td colspan=\"4\" class=\"tbl_1\"><b>Details der Liste $uqid</b></td>
			</tr>
			<tr>
				<td colspan=\"4\">&nbsp;</td>
			</tr>";	
		if ($_GET["print"]=="yes") {
			$printout = "	<html><head>
					<style type=\"text/css\">
					<!--
					body {
						font-family: Verdana, Arial, Helvetica, sans-serif;
						color: #000000;
					}
					-->
					</style>		
					</head>	
					<body><table width=\"100%\"><tr>
						<td colspan=\"3\"><b>Liste $uqid</b>&nbsp;&nbsp;<a href=\"javascript:window.print();\">Drucken</a><br>Preise sind EK!!!</td>
				 	</tr>
					<tr>
						<td colspan=\"3\">&nbsp;</td>
					</tr>";	

		}

		$cursuppl = "";
		$supplmon = 0;
		$allmon = 0;
		while ($row = $db->fetch_array($res)) {
			if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
			else $bg="";

			if($row["wizzard"]=="y"){
				$calc_ek=0;
				$w_m_res = $db->query("SELECT title, price_s, price_m, price_l, price_p, ek_s, ek_m, ek_l, ek_p FROM {$config["tables"]["catering_wizzard"]} WHERE master='y' AND foodID=".$row["foodID"]);
				$w_m_row=$db->fetch_array($w_m_res);
				$build_title = $w_m_row["title"] . " (";
				if($w_m_row["ek_".$row["size"]] == 0) {
					$calc_ek += $w_m_row["price_".$row["size"]];
				} else {
					$calc_ek += $w_m_row["ek_".$row["size"]];
				}
				$addids = split("/", $row["addIDs"]);
				$first_add=true;
				foreach($addids as $addid) {
					if($addid!=""){
						if($first_add==false)
							$build_title .= $new_title . ", ";
	
						$w_a_res = $db->query("SELECT title, price_s, price_m, price_l, price_p, ek_s, ek_m, ek_l, ek_p FROM {$config["tables"]["catering_wizzard"]} 
									WHERE ID='".$addid."'
									AND master='n'");
						$w_a_row = $db->fetch_array($w_a_res);
						$new_title = $w_a_row["title"];
						$first_add=false;
						if($w_a_row["ek_".$row["size"]] == 0) {
							$calc_ek += $w_a_row["price_".$row["size"]];
						} else {
							$calc_ek += $w_a_row["ek_".$row["size"]];
						}
					}
				}
				$build_title .=  $new_title . ")";
				$title = $build_title;
			} else {
				$title = $row["title"];
				$calc_ek = $row["ek"];
			}

			$templ['cateringorderlist']['show']['info']['rows'].="
				<tr $bg>
				<td width=\"10%\">";
			if ($_GET["ost"]==1) $templ['cateringorderlist']['show']['info']['rows'].="<input type=\"checkbox\" name=\"orderid[]\" value=\"".$row["ID"]."\" class=\"form\"></td>";
			else $templ['cateringorderlist']['show']['info']['rows'].="&nbsp;</td>";
			$templ['cateringorderlist']['show']['info']['rows'].="
				<td width=\"60%\" class=\"tbl_1\">(".stripString($supplier["{$row["supplID"]}"],20).") ".$row["supplHint"]." ".$title."</td>
				<td width=\"15%\" class=\"tbl_1\">".$row["username"]." (".$row["admUser"].")</td>
				<td width=\"15%\" class=\"tbl_1\">".sprintf("%01.2f",$row["topay"])." (".sprintf("%01.2f",$calc_ek).")</td>
				</tr>
			";
			
			if ($_GET["print"]=="yes") {			
				if ($cursuppl != $supplier["{$row["supplID"]}"]) {
					if ($cursuppl!="") {
						$printout .= "<tr><td colspan=\"3\" bgcolor=\"#CCCCCC\" align=\"right\"><b>{$cfg["sys_currency"]} ".sprintf("%01.2f",$supplmon)."</b></td></tr>";
						$supplmon=0;
					}
					$printout .= "	<tr><td colspan=\"3\">&nbsp;</td></tr>
							<tr><td colspan=\"3\" bgcolor=\"#CCCCCC\"><b>{$supplier["{$row["supplID"]}"]}</b></td></tr>
						     ";
					$cursuppl = $supplier["{$row["supplID"]}"];
				}
				$printout .= 	"<tr><td width=\"65%\">".$row["supplHint"]." ".$row["title"]."</td>
						<td width=\"20%\">".$row["username"]."</td>
						<td width=\"15%\">".sprintf("%01.2f",$calc_ek)."</td>
						</tr>
						";	
				$allmon += $calc_ek;
				$supplmon += $calc_ek;		
			}
			
			$i++;
		}
		if ($_GET["ost"]==1) {
			$templ['cateringorderlist']['show']['info']['rows'].="
				<tr>
				<td colspan=\"5\" class=\"tbl_1\">
					Bei selektierten Waren Liefereingang verbuchen?<br>
					<input type=\"image\" align=\"middle\" src=\"design/".$GLOBALS[auth][design]."/images/buttons_close.gif\" border=\"0\">
					<br><br><a href=\"#\" onClick=\"window.open('?mod=catering&action=orderlistdetails&olid={$_GET["olid"]}&ost=1&print=yes','win1','width=600,height=400,menubar=no,resizable=yes');\">Druckansicht</a>
				</td>
				</tr>";		
		}
		if ($_GET["print"]=="yes") {
			$printout .= "<tr><td colspan=\"3\" bgcolor=\"#CCCCCC\" align=\"right\" class=\"tbl_1\"><b>{$cfg["sys_currency"]} ".sprintf("%01.2f",$supplmon)."</b></td></tr>";
			$printout .= "<tr><td colspan=\"3\" class=\"tbl_1\">&nbsp;</td></tr>";
			$printout .= "<tr><td colspan=\"3\" bgcolor=\"#CCCCCC\" align=\"right\" class=\"tbl_1\"><b>{$cfg["sys_currency"]} ".sprintf("%01.2f",$allmon)."</b></td></tr>";
			$printout .= "</table></body></html>";
			print $printout;
			die;
		}
		
		$templ['cateringorderlist']['show']['info']['rows'].="</form>";
		break;

	
		
		
		
		
		
		
	
	case "openorders":
		$templ['cateringorderlist']['show']['info']['title'] = "Bestellisten - Zur Bestellung anstehend";
		$res = $db->query("SELECT O.ID, O.foodID, F.title, O.topay, U.username, D.actiontime, F.supplID, O.wizzard, O.addIDs
				FROM 	{$config["tables"]["catering_orders"]} AS O,
					{$config["tables"]["catering_foods"]} AS F,
					{$config["tables"]["catering_deliverylog"]} AS D,
					{$config["tables"]["user"]} AS U
				WHERE 	O.userID=U.userid 
					AND O.foodID=F.ID
					AND D.orderID=O.ID
					AND D.orderstatus='0'
				ORDER BY F.supplID
				");
		$i=0;
		if ($db->num_rows($res)==0) {
			$templ['cateringorderlist']['show']['info']['rows'].="	
				<tr><td colspan=\"5\" class=\"tbl_1\">Keine Bestellungen vorhanden, die noch nicht ausgelöst wurden.</td></tr>";
			break;
		}
		
		$templ['cateringorderlist']['show']['info']['rows'].="
			<form method=\"POST\" action=\"index.php?mod=catering&action=triggerorders\">
			<tr>
				<td colspan=\"5\" class=\"tbl_1\"><b>Nicht ausgelöste Bestellungen</b></td>
			</tr>
			<tr>
				<td colspan=\"5\" class=\"tbl_1\">&nbsp;</td>
			</tr>";	
		
		$res2 = $db->query("SELECT * FROM {$config["tables"]["catering_supplier"]}");
		while($row3=$db->fetch_array($res2)) 
			$supplier["{$row3["ID"]}"]=$row3["contact"];
		$supplier["0"] = "keiner";
			
		while ($row = $db->fetch_array($res)) {
			if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
			else $bg="";


			if($row["wizzard"]=="y"){
				$w_m_res = $db->query("SELECT * FROM {$config["tables"]["catering_wizzard"]} WHERE master='y' AND foodID=".$row["foodID"]);
				$w_m_row=$db->fetch_array($w_m_res);
				$build_title = $w_m_row["title"] . " (";
				$addids = split("/", $row["addIDs"]);
				$first_add=true;
				foreach($addids as $addid) {
					if($addid!=""){
							if($first_add==false)
								$build_title .= $new_title . ", ";
	
							$w_a_res = $db->query("SELECT title FROM {$config["tables"]["catering_wizzard"]} 
										WHERE ID='".$addid."'
										AND master='n'");
							$w_a_row = $db->fetch_array($w_a_res);
							$new_title = $w_a_row["title"];
							$first_add=false;
					}
				}
				$build_title .=  $new_title . ")";
				$title = $build_title;
			} else $title = $row["title"];


			$templ['cateringorderlist']['show']['info']['rows'].="
				<tr $bg>
				<td width=\"10%\" class=\"tbl_1\"><input type=\"checkbox\" name=\"orderid[]\" value=\"".$row["ID"]."\" checked class=\"form\"></td>
				<td width=\"20%\" class=\"tbl_1\">".$row["actiontime"]."</td>
				<td width=\"20%\" class=\"tbl_1\">".$row["username"]."</td>
				<td width=\"35%\" class=\"tbl_1\">(".stripString($supplier["{$row["supplID"]}"],20).") ".$title."</td>
				<td width=\"15%\" class=\"tbl_1\">".sprintf("%01.2f",$row["topay"])."</td>
				</tr>
			";
			$i++;
			$row["contact"]="keiner";
		}
		$templ['cateringorderlist']['show']['info']['rows'].="
			<tr>
			<td colspan=\"5\" class=\"tbl_1\">
				Kurzname der Liste: <input type=\"text\" name=\"kurzname\" size=\"10\" maxlength=\"20\" class=\"form\">&nbsp;
				<input type=\"image\" align=\"middle\" src=\"design/".$GLOBALS[auth][design]."/images/buttons_unblock.gif\" border=\"0\">
			</td>
			</tr>";
		$templ['cateringorderlist']['show']['info']['rows'].="</form>";
		break;
	
		
		
		
		
		
	case "triggerorders": 
		$listname = $_POST["kurzname"]."_".date("Y_m_d_H_i_s");
		for($i=0; $i<count($_POST["orderid"]);$i++) {
			$db->query("INSERT INTO {$config["tables"]["catering_orderlogs"]} SET
					uqID=\"".$listname."\",
					orderID=\"".$_POST["orderid"][$i]."\",
					userID=\"".$_SESSION["auth"]["userid"]."\",
					actiontime=NOW()
					");
			$db->query("UPDATE {$config["tables"]["catering_deliverylog"]} SET
					orderstatus=\"1\" 
				    WHERE orderID=\"".$_POST["orderid"][$i]."\"");
		}
		header("Location: ?mod=catering&action=ordersummary");
		break;
		

		
	case "delivery": 
		for($i=0; $i<count($_POST["orderid"]);$i++) {
			$db->query("UPDATE {$config["tables"]["catering_deliverylog"]} SET
					orderstatus=\"2\"
				    WHERE orderID=\"".$_POST["orderid"][$i]."\"");
			$db->query("UPDATE {$config["tables"]["catering_orderlogs"]} SET
					userID=\"".$_SESSION["auth"]["userid"]."\"
				    WHERE orderID=\"".$_POST["orderid"][$i]."\"");

		}
		header("Location: ?mod=catering&action=ordersummary");
		break;
		
		
	case "orderlistdelete": 
		if ($_GET["step"]=="2") {
			$res = $db->query("SELECT uqID FROM {$config["tables"]["catering_orderlogs"]} WHERE ID=\"".$_GET["olid"]."\"");
			$row = $db->fetch_array($res);
			$uqid = $row["uqID"];
	
			$db->query("DELETE FROM {$config["tables"]["catering_orderlogs"]} 
				    WHERE uqID=\"".$uqid."\"");
			
			header("Location: ?mod=catering&action=ordersummary");
			break;
		} else {
			$func->question("Liste wirklich löschen?","index.php?mod=catering&action=orderlistdelete&olid=".$_GET["olid"]."&step=2","index.php?mod=catering&action=ordersummary");
			break;
		}
		break;
				
	
}	
	
$templ['cateringorderlist']['show']['info']['rows'].="</table>";
eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("cateringorderlist_show")."\";");


?>
