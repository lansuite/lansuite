<?php
/*****************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	----------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		0.1
*	Filename: 		notpayedlist.php
*	Module: 		Catering
*	Main editor: 		Stephan Dahm
*	Last change: 		06.03.2005 14:58
*	Description: 		Admin notpayed list
*	Remarks: 		displayes only the not payed Elements
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

$templ['cateringnotpayedlist']['show']['info']['rows']="<table width=\"100%\" border=\"0\">";
switch ($selaction) {
	default: 
		$templ['cateringnotpayedlist']['show']['info']['title'] = "Bestellisten - Vom Gast noch nicht Bezahlt";
		$res = $db->query("SELECT O.ID, O.foodID, F.title, O.topay, U.username, D.actiontime, F.supplID, O.wizzard, O.addIDs
				FROM 	{$config["tables"]["catering_orders"]} AS O,
					{$config["tables"]["catering_foods"]} AS F,
					{$config["tables"]["catering_deliverylog"]} AS D,
					{$config["tables"]["user"]} AS U
				WHERE 	O.userID=U.userid 
					AND O.foodID=F.ID
					AND D.orderID=O.ID
					AND D.orderstatus='3'
				ORDER BY F.supplID
				");		//orderstatus changed from 0 to 3 so that the notpayed is selected
		$i=0;
		if ($db->num_rows($res)==0) {			//diese var ist die komplette liste mit allen zeilen (in diesem fall leer also hinweis)
			$templ['cateringnotpayedlist']['show']['info']['rows'].="	
				<tr><td colspan=\"5\" class=\"tbl_1\">Keine Bestellungen vorhanden, die noch vom Gast bezahlt werden müssen.</td></tr>";
			break;
		}
				//hier nicht leer wird gefüllt (append)
		$templ['cateringnotpayedlist']['show']['info']['rows'].="
			<form method=\"POST\" action=\"index.php?mod=catering&action=triggerorders\">
			<tr>
				<td colspan=\"5\" class=\"tbl_1\"><b>Nicht bezahlte Bestellungen</b></td>
			</tr>
			<tr>
				<td colspan=\"5\" class=\"tbl_1\">&nbsp;</td>
			</tr>";	
				//ka was hier das soll einfach übernommen von orderslist.php case block default
		$res2 = $db->query("SELECT * FROM {$config["tables"]["catering_supplier"]}");
		while($row3=$db->fetch_array($res2)) 
			$supplier["{$row3["ID"]}"]=$row3["contact"];
		$supplier["0"] = "keiner";
			
		while ($row = $db->fetch_array($res)) {
			if ($i % 2 == 1) $bg = "bgcolor=\"#EEEEEE\"";
			else $bg="";

			//wizard geht doch eh nicht ????????
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

			//und weiter gehts.....
			$templ['cateringnotpayedlist']['show']['info']['rows'].="
				<tr $bg>
				<td width=\"20%\" class=\"tbl_1\">".$row["actiontime"]."</td>
				<td width=\"20%\" class=\"tbl_1\">".$row["username"]."</td>
				<td width=\"35%\" class=\"tbl_1\">(".stripString($supplier["{$row["supplID"]}"],20).") ".$title."</td>
				<td width=\"15%\" class=\"tbl_1\">".sprintf("%01.2f",$row["topay"])."</td>
				<td width=\"15%\" class=\"tbl_1\"><a href=\"?mod=catering&action=accforuser&userid=".$row["userid"]."\">	
					<img src=\"design/".$GLOBALS[auth][design]."/images/buttons_add.gif\" border=\"0\">
					</a>
				</td>
				</tr>
			";
			$i++;
			$row["contact"]="keiner";
		}		//end tag....
		$templ['cateringnotpayedlist']['show']['info']['rows'].="</form>";
		break;
}	
	//table end tag....
$templ['cateringnotpayedlist']['show']['info']['rows'].="</table>";// diese template wird aufgerufen
eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("cateringnotpayedlist_show")."\";");


?>