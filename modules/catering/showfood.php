<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		showfood.php
*	Module: 		catering
*	Main editor: 		fritzsche@emazynx.de
*	Last change: 		24.05.2003
*	Description: 		Show all available food
*	Remarks: 		none
*
**************************************************************************/

// check if catering module is open
$ts1 = strtotime($cfg["catering_openfrom1"]);
$ts2 = strtotime($cfg["catering_openfrom2"]);
$ts3 = strtotime($cfg["catering_openfrom3"]);
$te1 = strtotime($cfg["catering_opento1"]);
$te2 = strtotime($cfg["catering_opento2"]);
$te3 = strtotime($cfg["catering_opento3"]);
$tc = time();

// get catering discount for current user
$discres = $db->query("SELECT catering_discount FROM {$config["tables"]["user"]} WHERE userID='".$_SESSION["auth"]["userid"]."'");
$disctemp = $db->fetch_array($discres);
$disc = $disctemp["catering_discount"];
if ($disc=="") $disc = 0;

if (!(($tc>=$ts1 && $tc<=$te1) || ($tc>=$ts2 && $tc<=$te2) || ($tc>=$ts3 && $tc<=$te3))) {
	$out = "";
	for ($i=1;$i<=3;$i++) {
		if ($cfg["catering_openfrom$i"]!="")
			$out .= $i.") ".$cfg["catering_openfrom$i"]." bis ".$cfg["catering_opento$i"]."<br>";
	}
	$func->error("Das Catering Modul ist nur zu folgenden Zeiten benutzbar:<br>$out","");
	return;
}

$gotgrp = $_GET["grp"];
$gotsuppl = $_GET["suppl"];

if ($_GET["action"]=="prioup") {
	$db->query("UPDATE {$config["tables"]["catering_foods"]} SET prio=prio-1 WHERE ID='".$_GET["foodid"]."'");
}

if ($_GET["action"]=="priodown") {
	$db->query("UPDATE {$config["tables"]["catering_foods"]} SET prio=prio+1 WHERE ID='".$_GET["foodid"]."'");
}


// COUNT
$get_amount = $db->query("SELECT COUNT(ID) AS cntid FROM {$config["tables"]["catering_foods"]}"); 
$row = $db->fetch_array($get_amount);
$overall_foods = $row["cntid"];

// query available money
if ($auth['type']>0) {
	$monres = $db->query("SELECT SUM(movement) as availmon FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$_SESSION["auth"]["userid"]."'");
	$row = $db->fetch_array($monres);
	$availmon = sprintf("%01.2f", $row["availmon"]);
	$templ['catering']['show']['row']['info']['availmon'] = "<font color=\"#AA0000\">(Verfügbarer Betrag: ".$availmon." {$cfg["sys_currency"]})<br>Bestellungen, deren Bezahlung nachträglich erfolgt, müssen innerhalb einer Stunde beglichen werden.</font>";
}


// lock an item
if ($_GET["action"]=="lock") {
	$db->query("UPDATE {$config["tables"]["catering_foods"]} SET locked='y' WHERE ID=\"".$_GET["foodid"]."\"");
	header ("Location: ?mod=catering&action=showgrp&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$_GET["grp"]);
}

// unlock an item
if ($_GET["action"]=="unlock") {
	$db->query("UPDATE {$config["tables"]["catering_foods"]} SET locked='n' WHERE ID=\"".$_GET["foodid"]."\"");
	header ("Location: ?mod=catering&action=showgrp&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$_GET["grp"]);
}

// add an item to orders
if ($_GET["action"]=="addtocart") {
	// check if user come from ip which was assigned
	$ipres = $db->query("SELECT ip FROM {$config["tables"]["seat_seats"]} WHERE userid='".$_SESSION["auth"]["userid"]."'");
	if ($db->num_rows($ipres)==0 && $cfg["catering_iplock"]=="1") {
		header ("Location: ?mod=catering&action=show&grp=$gotgrp");
	} else {
		$row2 = $db->fetch_array($ipres);
		if ($row2["ip"]!=$_SERVER["REMOTE_ADDR"] && $cfg["catering_iplock"]=="1") {
			header ("Location: ?mod=catering&action=show&grp=$gotgrp");	
		} else {
			// Lock Table, sicherstellen, dass der verfuegbare Betrag valid ist
			$db->query("LOCK TABLES {$config["tables"]["catering_accounting"]} WRITE, {$config["tables"]["catering_foods"]} READ, {$config["tables"]["catering_wizzard"]} READ, {$config["tables"]["catering_orders"]} WRITE, {$config["tables"]["catering_deliverylog"]} WRITE");
			$monres = $db->query("SELECT SUM(movement) as availmon FROM {$config["tables"]["catering_accounting"]} WHERE userID='".$_SESSION["auth"]["userid"]."'");
			$row = $db->fetch_array($monres);
			$availmon = sprintf("%01.2f", $row["availmon"]);
	
			if($_GET['wizzard']==1){
				$size = $_GET["selsize"];
				// 1st get prices
				$r1 = $db->query("SELECT ek_".$size.", price_".$size.", title FROM {$config["tables"]["catering_wizzard"]} WHERE foodID='".$_GET["foodid"]."' AND master='y' AND del='n'");
				$row = $db->fetch_array($r1);

				// manipulation verhindern
				if ($row["price_".$size]=="") {
					$db->query("UNLOCK TABLES");
					header ("Location: ?mod=catering&action=show&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$_GET["grp"]);
				}

				$calc_price = 0;
				$calc_ek = 0;

				$calc_price +=$row["price_".$size];
				$calc_ek +=$row["ek_".$size];

				$addids = split("/",$_GET["addids"]);
				
				foreach($addids as $addid){

					$r2 = $db->query("SELECT ek_".$size.", price_".$size.", title FROM {$config["tables"]["catering_wizzard"]} WHERE ID='".$addid."' AND master='n' AND del='n'");
					$row = $db->fetch_array($r2);
	
					// manipulation verhindern
					if ($row["price_".$size]=="") {
						$db->query("UNLOCK TABLES");
						header ("Location: ?mod=catering&action=show&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$_GET["grp"]);
					}
					
					$calc_price +=$row["price_".$size];
					$calc_ek +=$row["ek_".$size];
				}

				$pname = $row["title"];
				if (strlen($pname)>40) $pname = substr($pname,0,40)." ...";			

				if ($disc > 0) {
					$price = $calc_price - ((($calc_price - $calc_ek)/100)*$disc);
					$price = number_format($price, 2, '.', '');
				} else {
					$price = $calc_price;
				}

				// check if user has enough money, if not lock order
				if ($price > $availmon) $status = 3;
				else $status = 0;
				$r1 = $db->query("INSERT INTO {$config["tables"]["catering_orders"]} SET userID='".$_SESSION["auth"]["userid"]."', foodID='".$_GET["foodid"]."', addIDs='".$_GET["addids"]."', size='".$size."', wizzard='y', topay='".$price."'");
				$lid = $db->insert_id();
				$db->query("INSERT INTO {$config["tables"]["catering_deliverylog"]} SET orderID='".$lid."', actiontime=NOW(), orderstatus='".$status."'");
				if ($status==0) { // decrease available money
					$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_SESSION["auth"]["userid"]."', actiontime=NOW(), movement='-".$price."', comment='Order: ".$pname."'");
				}

			}
			else {
				// 1st get item price
				$r1 = $db->query("SELECT ek,price,title FROM {$config["tables"]["catering_foods"]} WHERE ID='".$_GET["foodid"]."' AND deleted='n'");
				$row = $db->fetch_array($r1);
				// manipulation verhindern
				if ($row["price"]=="") {
					$db->query("UNLOCK TABLES");
					header ("Location: ?mod=catering&action=show&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$_GET["grp"]);
				}
				$pname = $row["title"];
				if (strlen($pname)>40) $pname = substr($pname,0,40)." ...";			
				
				if ($disc > 0) {
					$price = $row["price"] - ((($row["price"] - $row["ek"])/100)*$disc);
					$price = number_format($price, 2, '.', '');
				} else
					$price = $row["price"];
				// check if user has enough money, if not lock order
				if ($price > $availmon) $status = 3;
				else $status = 0;
				$r1 = $db->query("INSERT INTO {$config["tables"]["catering_orders"]} SET userID='".$_SESSION["auth"]["userid"]."', foodID='".$_GET["foodid"]."', topay='".$price."'");
				$lid = $db->insert_id();
				$db->query("INSERT INTO {$config["tables"]["catering_deliverylog"]} SET orderID='".$lid."', actiontime=NOW(), orderstatus='".$status."'");
				if ($status==0) { // decrease available money
					$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$_SESSION["auth"]["userid"]."', actiontime=NOW(), movement='-".$price."', comment='Order: ".$pname."'");
				}
			}
			$db->query("UNLOCK TABLES");
			
			header ("Location: ?mod=catering&action=showgrp&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$_GET["grp"]);
		} 
	}

}


// query ordered foods
// status: 
//   0 = order is open
//   1 = order was ordered
//   2 = order was delivered
//   3 = order was locked (user has to pay first) 
if ($auth['type']>0) {
	$ordres = $db->query("SELECT * FROM {$config["tables"]["catering_orders"]} AS O, {$config["tables"]["catering_deliverylog"]} AS L WHERE userID='".$_SESSION["auth"]["userid"]."' AND L.orderID=O.ID");
	unset($_SESSION["catering_user_order"]);
	while ($row = $db->fetch_array($ordres)) {
		$_SESSION["catering_user_order"]["{$row['foodID']}"]["{$row['orderstatus']}"]++;
	}
}

if($overall_foods == "0") {
	$func->no_items("Speisen oder Getränke","","rlist");
} else {
if($_GET["wizzard"] == "0") {

	$fgres = $db->query("SELECT * FROM {$config["tables"]["catering_foodgroups"]} WHERE supplID = ".$_GET["suppl"]." ORDER BY name");
	$templ['catering']['show']['row']['info']['foodgrps']="<a href=\"index.php?mod=catering&action=showgrp&wizzard=".$_GET["wizzard"]."&suppl=".$_GET["suppl"]."&grp=x\">Alle</a>";
	$i=0;
	while($row=$db->fetch_array($fgres)) {
		if ($i==0 && $gotgrp=="") { 
			$grpToShow = "grpID=\"".$row["ID"]."\" AND"; 
			$gotgrp = $row["ID"];
			$gotwiz = $row["wizzard"];
		} else $grpToShow = "";
		if ($row["ID"]==$gotgrp)
			$templ['catering']['show']['row']['info']['foodgrps'] .= " &nbsp;|&nbsp; <a href=\"index.php?mod=catering&action=showgrp&wizzard=".$row["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$row["ID"]."\"><b>".$row["name"]."</b></a>";
		else
			$templ['catering']['show']['row']['info']['foodgrps'] .= " &nbsp;|&nbsp; <a href=\"index.php?mod=catering&action=showgrp&wizzard=".$row["wizzard"]."&suppl=".$_GET["suppl"]."&grp=".$row["ID"]."\">".$row["name"]."</a>";
		$i++;
	}
	
	if ($gotgrp!="") {
		if ($gotgrp=="x") {
			$grpToShow="grpID > 0 ";
			$get_foods = $db->query("SELECT * FROM {$config["tables"]["catering_foods"]} 
			LEFT JOIN {$config["tables"]["catering_foodgroups"]} ON {$config["tables"]["catering_foodgroups"]}.ID = {$config["tables"]["catering_foods"]}.grpID
			WHERE {$config["tables"]["catering_foods"]}.$grpToShow 
				AND {$config["tables"]["catering_foods"]}.supplID='".$_GET["suppl"]."' 
				AND {$config["tables"]["catering_foods"]}.deleted='n' 
				AND {$config["tables"]["catering_foods"]}.parentID='0' 
				AND {$config["tables"]["catering_foodgroups"]}.wizzard='0'
			ORDER BY prio,title");
			$templ['catering']['show']['row']['info']['availmon'] = "<font color=\"#AA0000\">(Verfügbarer Betrag: ".$availmon." {$cfg["sys_currency"]})<br>Bestellungen, deren Bezahlung nachträglich erfolgt, müssen innerhalb einer Stunde beglichen werden.</font><br />In dieser Ansicht werden keine Daten aus den Wizzards angezeigt!";
		} 
		else {
			$grpToShow="grpID = '$gotgrp'";
			$get_foods = $db->query("SELECT * FROM {$config["tables"]["catering_foods"]} WHERE $grpToShow AND supplID='".$_GET["suppl"]."' AND deleted='n' AND parentID='0' ORDER BY prio,title");
		}
	}
	
	


	while($row=$db->fetch_array($get_foods)) {
		$templ['catering']['show']['row']['info']['title']  	= $func->text2html($row["title"]);
		$text = $row["description"];
		if ($disc > 0) {
			$price = $row["price"] - ((($row["price"] - $row["ek"])/100)*$disc);
			$price = number_format($price, 2, '.', '');
		} else {
			$price = $row["price"];
			$price = number_format($price, 2, '.', '');
		}
		$templ['catering']['show']['row']['info']['price']	= $price."&nbsp;".$cfg["sys_currency"];
		$foodid 						= $row["ID"];
		$templ['catering']['show']['row']['info']['picfile']	= $row["picfile"];	
		$templ['catering']['show']['row']['info']['text'] 	= chop($text)."<br>";

		// print notice that user has to be logged in
		if ($auth['type']==0) {
			$templ['catering']['show']['row']['info']['shop'] = "<font color=\"#AA0000\">(Zum Bestellen bitte zuerst einloggen!)</font>";
			$templ['catering']['show']['row']['info']['addtoshop'] = "<td width=\"0\">&nbsp;</td>";
		} else if ($auth['type']>0) {
			// check if user come from ip which was assigned
			$ipres = $db->query("SELECT ip FROM {$config["tables"]["seat_seats"]} WHERE userid='".$_SESSION["auth"]["userid"]."'");
			if ($db->num_rows($ipres)==0 && $cfg["catering_iplock"]=="1" ) {
				$templ['catering']['show']['row']['info']['shop'] = "<font color=\"#AA0000\">(Ihnen wurde keine IP Adresse zugewiesen! Bitte bei der Orga melden!)</font>";
				$templ['catering']['show']['row']['info']['addtoshop'] = "<td width=\"0\"></td>";
			} else {
				$row2 = $db->fetch_array($ipres);
				if ($row2["ip"]!=$_SERVER["REMOTE_ADDR"] && $cfg["catering_iplock"]=="1") {
					$templ['catering']['show']['row']['info']['shop'] = "<font color=\"#AA0000\">(Bestellungen können nur von der zugewiesenen IP aus vorgenommen werden!)</font>";
					$templ['catering']['show']['row']['info']['addtoshop'] = "<td width=\"0\"></td>";
				} else {
					if ($_SESSION["catering_user_order"]["$foodid"]["0"]=="") $_SESSION["catering_user_order"]["$foodid"]["0"]=0;
					if ($_SESSION["catering_user_order"]["$foodid"]["1"]=="") $_SESSION["catering_user_order"]["$foodid"]["1"]=0;
					if ($_SESSION["catering_user_order"]["$foodid"]["2"]=="") $_SESSION["catering_user_order"]["$foodid"]["2"]=0;
					if ($_SESSION["catering_user_order"]["$foodid"]["3"]=="") $_SESSION["catering_user_order"]["$foodid"]["3"]=0;
					$addarrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_basket.gif';
					$lockedarrow = 'design/'.$GLOBALS[auth][design].'/images/server_pw.gif';
// xxx					$templ['catering']['show']['row']['info']['shop'] = "<font color=\"#AA0000\">(offen:".$_SESSION["catering_user_order"]["{$foodid}"]["0"]." &nbsp;&nbsp; noch nicht bezahlt:".$_SESSION["catering_user_order"]["{$foodid}"]["3"]." &nbsp;&nbsp; bestellt:".$_SESSION["catering_user_order"]["{$foodid}"]["1"]." &nbsp;&nbsp; abgeschlossen:".$_SESSION["catering_user_order"]["{$foodid}"]["2"]."!)</font>";
					if ($row["locked"]=="n") 
						$templ['catering']['show']['row']['info']['addtoshop'] = "<td width=\"10\"><a href=\"index.php?mod=catering&action=addtocart&foodid=".$foodid."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\" title=\"(offen:".$_SESSION["catering_user_order"]["{$foodid}"]["0"]." &nbsp;&nbsp; noch nicht bezahlt:".$_SESSION["catering_user_order"]["{$foodid}"]["3"]." &nbsp;&nbsp; bestellt:".$_SESSION["catering_user_order"]["{$foodid}"]["1"]." &nbsp;&nbsp; abgeschlossen:".$_SESSION["catering_user_order"]["{$foodid}"]["2"]."!)\"><img border=\"0\" src=\"".$addarrow."\" vspace=\"2\" align=\"top\"></a></td>";
					else 
						$templ['catering']['show']['row']['info']['addtoshop'] = "<td width=\"10\"><img border=\"0\" src=\"".$lockedarrow."\" vspace=\"2\" align=\"top\"></td>";
				} 
			}
		}
		
		// SET ADMIN FUNCTION ONLY BUTTONS
		if($_SESSION["auth"]["type"] > 1) {
			$uparrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_orderby_desc.gif';
			$downarrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_orderby_asc.gif';
			$templ['catering']['show']['row']['control']['buttons']['updown'] = "<table width=\"20\"><tr><td><a href=\"index.php?mod=catering&action=prioup&foodid=".$foodid."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"><img border=\"0\" src=\"".$uparrow."\"></a></td><td>Prio:</td></tr><tr><td><a href=\"index.php?mod=catering&action=priodown&foodid=".$foodid."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"><img border=\"0\"src=\"".$downarrow."\"></a></td><td>".$row["prio"]."</td></tr></table>";
			if ($row["locked"]=="n") 
				$templ['catering']['show']['row']['control']['buttons']['edit'] = "<td width=\"150\"><a href=\"index.php?mod=catering&action=lock&foodid=" . $foodid ."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"  title=\"Artikel sperren!\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/arrows_info.gif\" vspace=\"1\"></a>&nbsp;";
			else 
				$templ['catering']['show']['row']['control']['buttons']['edit'] = "<td width=\"150\"><a href=\"index.php?mod=catering&action=unlock&foodid=" . $foodid ."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"  title=\"Artikel entsperren!\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/arrows_delete.gif\"  vspace=\"1\"></a>&nbsp;";
			$templ['catering']['show']['row']['control']['buttons']['edit'] .= "<a href=\"index.php?mod=catering&action=changefood&foodid=" . $foodid ."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_edit.gif\"></a>&nbsp;";
			$templ['catering']['show']['row']['control']['buttons']['delete'] = "<a href=\"index.php?mod=catering&action=deletefood&foodid=" . $foodid ."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_delete.gif\"></a></td>";
			$templ['catering']['show']['row']['control']['buttons']['newoption'] = "<a href=\"index.php?mod=catering&action=newfood&foodid=" . $foodid ."&parentid=".$foodid."&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."&grp=$gotgrp\"><img border=\"0\"src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_newoption.gif\"></a>";
		} else {
			$templ['catering']['show']['row']['control']['buttons']['edit'] = "<td width=\"0\"><!--admin-->";
			$templ['catering']['show']['row']['control']['buttons']['delete'] = "</td>";
		}

		
		$templ['catering']['show']['row']['info']['picfile'] = "<img src=\"/ext_inc/catering/showpic.php?w=100&f=".$row["picfile"]."\" border=\"0\">";

		// Checken, ob Optionen verfügbar sind
		$childres = $db->query("SELECT COUNT(ID) AS cntID,ID,title,description,price,ek,locked FROM  {$config["tables"]["catering_foods"]} WHERE parentID=\"{$row["ID"]}\" AND deleted='n' GROUP BY ID");
		$childrow = $db->fetch_array($childres);
		if ($childrow["cntID"]>0) {
			$addarrow = 'design/'.$GLOBALS[auth][design].'/images/arrows_basket.gif';
			$lockedarrow = 'design/'.$GLOBALS[auth][design].'/images/server_pw.gif';
			$templ['catering']['show']['row']['info']['text'] .= "<table width=\"100%\" align=\"left\"><tr><td colspan=\"4\"><b>Alternativen</b><br></td></tr>";
			do {
				if ($_SESSION["catering_user_order"]["{$childrow["ID"]}"]["0"]=="") $_SESSION["catering_user_order"]["{$childrow["ID"]}"]["0"]=0;
				if ($_SESSION["catering_user_order"]["{$childrow["ID"]}"]["1"]=="") $_SESSION["catering_user_order"]["{$childrow["ID"]}"]["1"]=0;
				if ($_SESSION["catering_user_order"]["{$childrow["ID"]}"]["2"]=="") $_SESSION["catering_user_order"]["{$childrow["ID"]}"]["2"]=0;
				if ($_SESSION["catering_user_order"]["{$childrow["ID"]}"]["3"]=="") $_SESSION["catering_user_order"]["{$childrow["ID"]}"]["3"]=0;

				if($_SESSION["auth"]["type"] > 0) {
					$templ['catering']['show']['row']['info']['text'] .= "<tr><td colspan=\"4\">";
// xxx					$templ['catering']['show']['row']['info']['text'] .= "<font color=\"#AA0000\">(offen:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["0"]." &nbsp;&nbsp; noch nicht bezahlt:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["3"]." &nbsp;&nbsp; bestellt:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["1"]." &nbsp;&nbsp; abgeschlossen:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["2"]."!)</font>";
					$templ['catering']['show']['row']['info']['text'] .= "</td></tr><tr><td width=\"10\">";
					if ($childrow["locked"]=="n") 
						$templ['catering']['show']['row']['info']['text'] .= "<a href=\"index.php?mod=catering&action=addtocart&foodid=".$childrow["ID"]."&grp=$gotgrp&suppl=$gotsuppl&wizzard=".$_GET["wizzard"]."\" title=\"(offen:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["0"]." &nbsp;&nbsp; noch nicht bezahlt:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["3"]." &nbsp;&nbsp; bestellt:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["1"]." &nbsp;&nbsp; abgeschlossen:".$_SESSION["catering_user_order"]["{$childrow["ID"]}"]["2"]."!)\"><img border=\"0\" src=\"".$addarrow."\" vspace=\"2\" align=\"top\"></a></td>";
					else
						$templ['catering']['show']['row']['info']['text'] .= "<img border=\"0\" src=\"".$lockedarrow."\" vspace=\"2\" align=\"top\"></td>";
				} else {
					$templ['catering']['show']['row']['info']['text'] .= "<td width=\"0\">&nbsp;</td>";
				}					
				// SET ADMIN FUNCTION ONLY BUTTONS
				if($_SESSION["auth"]["type"] > 1) {
					if ($childrow["locked"]=="n") 
						$templ['catering']['show']['row']['info']['text'] .= "<td width=\"150\"><a href=\"index.php?mod=catering&action=lock&foodid=".$childrow["ID"]."&parentid=".$foodid."&grp=$gotgrp&wizzard=".$_GET["wizzard"]."\"  title=\"Artikel sperren!\" ><img border=\"0\" src=\"design/" . $_SESSION["auth"]["design"] . "/images/arrows_info.gif\" vspace=\"1\"></a>&nbsp;";
					else 
						$templ['catering']['show']['row']['info']['text'] .= "<td width=\"150\"><a href=\"index.php?mod=catering&action=unlock&foodid=".$childrow["ID"]."&parentid=".$foodid."&grp=$gotgrp&wizzard=".$_GET["wizzard"]."\" title=\"Artikel entsperren!\"><img border=\"0\"src=\"design/" . $_SESSION["auth"]["design"] . "/images/arrows_delete.gif\" vspace=\"1\"></a>&nbsp;";
					$templ['catering']['show']['row']['info']['text'] .= "<a href=\"index.php?mod=catering&action=changefood&foodid=".$childrow["ID"]."&parentid=".$foodid."&grp=$gotgrp&wizzard=".$_GET["wizzard"]."\"><img border=\"0\"src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_edit.gif\"></a>&nbsp;";
					$templ['catering']['show']['row']['info']['text'] .= "<a href=\"index.php?mod=catering&action=deletefood&foodid=".$childrow["ID"]."&parentid=".$foodid."&grp=$gotgrp&wizzard=".$_GET["wizzard"]."\"><img border=\"0\"src=\"design/" . $_SESSION["auth"]["design"] . "/images/buttons_delete.gif\"></a></td>";
				} else {
					$templ['catering']['show']['row']['info']['text'] .= "<td width=\"0\"></td>";
				}
				if ($disc > 0) {
					$price = $childrow["price"] - ((($childrow["price"] - $childrow["ek"])/100)*$disc);
					$price = number_format($price, 2, '.', '');
				} else {
					$price = $childrow["price"];
					$price = number_format($price, 2, '.', '');
				}				
				$templ['catering']['show']['row']['info']['text'] .= "<td>".$childrow["title"]."&nbsp;</td><td width=\"80\" align=\"right\"><b>".$price."&nbsp;".$cfg["sys_currency"]."</b><bR>";
				$templ['catering']['show']['row']['info']['text'] .= "</td></tr>";
			} while ($childrow = $db->fetch_array($childres));
			$templ['catering']['show']['row']['info']['text'] .= "</table>";
		}
		
						
		eval("\$templ['catering']['show']['case']['control']['rows'] .= \"". $func->gettemplate("catering_show_row")."\";");

	} // CLOSE WHILE

	eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("catering_show_case")."\";");

}
else {
	include ("modules/catering/showwizzard.php");	
}//if
}//if

?>
