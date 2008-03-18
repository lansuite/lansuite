<?php
include_once("modules/foodcenter/class_product.php");
include_once("modules/foodcenter/class_accounting.php");
$product_list = new product_list();

if($auth['type'] < 2){
	unset($_GET['step']);	
}

switch ($_GET['step']) {
	case 3:
		$time = time();
		if($_GET['status'] == 6 | $_GET['status'] == 7){
			$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET['status']}, lastchange = '$time', supplytime = '$time'  WHERE id = {$_GET['id']}");
		}elseif ($_GET['status'] == 8){
			$prodrow = $db->query_first("SELECT * FROM {$config['tables']['food_ordering']} WHERE id = {$_GET['id']}");				
			
			if($prodrow['pice'] > 1 && !isset($_POST['delcount'])){
				$count_array[] = "<option selected value=\"{$prodrow['pice']}\">".t('Alle')/* TRANS */."</option>";
				
				for($i = $prodrow['pice'];$i > 0;$i--){
					$count_array[] .= "<option value=\"{$i}\">{$i}</option>";
				}
				$_GET['step'] = 10;
			}else{
				$price = 0;
				$account = new accounting($prodrow['userid']);
				if(stristr($prodrow['opts'],"/")){
					$values = split("/",$prodrow['opts']);

					foreach ($values as $number){
						if(is_numeric($number)){
							$optrow = $db->query_first("SELECT price FROM {$config['tables']['food_option']} WHERE id = " . $number);
							$price += $optrow['price'];
						}

					}
				}else{
					$optrow = $db->query_first("SELECT price FROM {$config['tables']['food_option']} WHERE id = " . $prodrow['opts']);
					$price += $optrow['price'];
				}

				if(isset($_POST['delcount'])){
					$price = $price * $_POST['delcount'];
				}else{
					$price = $price * $prodrow['pice'];
				}
				$account->change($price,t('Rückzahlung bei abbestellten Produkten')/* TRANS */ . " (" . $auth['username'] . ")");
				
				if(!isset($_POST['delcount']) || $_POST['delcount'] == $prodrow['pice']){
					$db->query_first("DELETE FROM {$config['tables']['food_ordering']} WHERE id = " . $_GET['id']);
				}else{
					$pice = $prodrow['pice'] - $_POST['delcount'];
					$db->query_first("UPDATE {$config['tables']['food_ordering']} SET pice = ". (int)$pice ." WHERE id = " . $_GET['id']);
				}
			}
		}else{
			$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET['status']}, lastchange = '$time' WHERE id = {$_GET['id']}");
			if($_GET['status'] == 3){
				$user_id = $db->query_first("SELECT userid FROM {$config['tables']['food_ordering']} WHERE id = {$_GET['id']}");
				$func->setainfo(t('Deine bestellten Produkte sind abholbereit')/* TRANS */,$user_id['userid'],2,"foodcenter",$_GET['id']);
			}
		}
		break;
}


switch ($_GET['step']){
	
	default:

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');

    $ms2->query['from'] = "{$config['tables']['food_ordering']} AS a
    	  LEFT JOIN {$config['tables']['food_option']} AS o ON a.opts = o.id
		  LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id
		  LEFT JOIN {$config['tables']['food_supp']} AS s ON p.supp_id = s.supp_id
		  LEFT JOIN {$config['tables']['user']} AS u ON u.userid = a.userid";

	// Array Abfragen f�r DropDowns
	$status_list = array('' => 'Alle');
	$row = $db->query("SELECT * FROM {$config['tables']['food_status']}");
	while($res = $db->fetch_array($row)) $status_list[$res['id']] = $res['statusname'];
	$db->free_result($row); 
	
	$supp_list = array('' => 'Alle');
	$row = $db->query("SELECT * FROM {$config['tables']['food_supp']}");
	while($res = $db->fetch_array($row)) $supp_list[$res['supp_id']] = $res['name'];
	$db->free_result($row); 
  	
    $party_list = array('' => 'Alle');
	$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
	while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
	$db->free_result($row);
	
	$ms2->AddTextSearchDropDown('Status', 'a.status', $status_list, '1');
    $ms2->AddTextSearchDropDown('Lieferant', 's.supp_id', $supp_list);
	$ms2->AddTextSearchDropDown('Party', 'a.partyid', $party_list, $party->party_id);
/*
  	$userquery = $db->query("SELECT * FROM {$config['tables']['food_ordering']} AS a LEFT JOIN {$config['tables']['user']} AS u ON a.userid=u.userid");
  	$user_array[''] = t('')/* TRANS */;
  	while ($userrows = $db->fetch_array($userquery)) {
  		$user_array[$userrows['userid']] = $userrows['username'];
  	}
    $ms2->AddTextSearchDropDown('Besteller', 'a.userid', $user_array);
*/
    $ms2->AddSelect('u.userid');
    $ms2->AddResultField('Titel', 'p.caption');
    //$ms2->AddResultField('Option', 'o.caption');
    $ms2->AddResultField('Einheit', 'o.unit');
    $ms2->AddResultField('Anzahl', 'a.pice');
    $ms2->AddResultField('Lieferant', 's.name');
    $ms2->AddResultField('Besteller', 'u.username', 'UserNameAndIcon');
    $ms2->AddResultField('Bestellt', 'a.ordertime', 'MS2GetDate');
    $ms2->AddResultField('Geliefert', 'a.supplytime', 'MS2GetDate');

    $ms2->AddIconField('details', 'index.php?mod=foodcenter&action=statchange&step=2&id=', t('Details')/* TRANS */);
	
	  $ms2->AddMultiSelectAction(t('Array')/* TRANS */[0], 'index.php?mod=foodcenter&action=statchange&step=2&status=6', 1);
	  $ms2->AddMultiSelectAction(t('Array')/* TRANS */[1], 'index.php?mod=foodcenter&action=statchange&step=2&status=5', 1);
	  $ms2->AddMultiSelectAction(t('Array')/* TRANS */[2], 'index.php?mod=foodcenter&action=statchange&step=2&status=3', 1);
	  $ms2->AddMultiSelectAction(t('Array')/* TRANS */[3], 'index.php?mod=foodcenter&action=statchange&step=2&status=7', 1);
	  $ms2->AddMultiSelectAction(t('Array')/* TRANS */[4], 'index.php?mod=foodcenter&action=statchange&step=2&status=8', 1);

    switch ($_POST['search_dd_input'][0]){
    	case 1:
        $dsp->NewContent(t('Bestellte Produkte')/* TRANS */, '');
    		$ms2->NoItemsText = t('Keine aktuellen Bestellungen vorhanden.')/* TRANS */;
    	break;

    	case 2:
        $dsp->NewContent(t('Produkte die bestellt werden')/* TRANS */, '');
    		$ms2->NoItemsText = t('Es müssen keine Produkte bestellt werden.')/* TRANS */;
    	break;

    	case 3:
        $dsp->NewContent(t('Diese Produkte wurden bestellt. Auf die Lieferung wird gewartet.')/* TRANS */, '');
    		$ms2->NoItemsText = t('Es wird auf keine Lieferung gewartet.')/* TRANS */;
    	break;

    	case 4:
        $dsp->NewContent(t('Fertiggestellte Küchengerichte zur Abholung/Lieferung')/* TRANS */, '');
    		$ms2->NoItemsText = t('Derzeit gibt es keine fertiggestellten Gerichte aus der Küche.')/* TRANS */;
    	break;
    	
     	case 5:
        $dsp->NewContent(t('Abgeholt')/* TRANS */, '');
    		$ms2->NoItemsText = t('Sie haben alle Produkte abgeholt.')/* TRANS */;
    	break;  
       }

    $ms2->PrintSearch('index.php?mod=foodcenter&action=statchange', 'a.id');


		$handle = opendir("ext_inc/foodcenter_templates");
		while ($file = readdir ($handle)) if (($file != ".") and ($file != "..") and ($file != ".svn") and (!is_dir($file))) {
			if((substr($file, -3, 3) == "htm") && (substr($file, -7, 7) != "row.htm") || (substr($file, -4, 4) == "html") && (substr($file, -8, 8) != "row.html")){
				$file_array[] .= "<option value=\"$file\">$file</option>";
			}
		}
		$dsp->SetForm("index.php?mod=foodcenter&action=print&design=base\" target=\"_blank\"","print");
		$dsp->AddDropDownFieldRow("file",t('Bitte Template auswählen:')/* TRANS */,$file_array,"");
		
		

		echo "<input type=\"hidden\" name=\"search_input[0]\" value=\"{$_POST['search_input'][0]}\">";
		echo "<input type=\"hidden\" name=\"search_dd_input[0]\" value=\"{$_POST['search_dd_input'][0]}\">";
		echo "<input type=\"hidden\" name=\"search_dd_input[1]\" value=\"{$_POST['search_dd_input'][1]}\">";
		echo "<input type=\"hidden\" name=\"search_dd_input[2]\" value=\"{$_POST['search_dd_input'][2]}\">";
		
		$dsp->AddFormSubmitRow("print");
		$dsp->AddContent();
		break;
	case 2:
	
	if($_POST['action']){
		$time = time();
		$totprice = 0;
		foreach($_POST["action"] AS $item => $val) {
			if($_GET["status"] == 6 | $_GET["status"] == 7){
				$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET["status"]}, lastchange = '$time', supplytime = '$time'  WHERE id = {$item}");
			}elseif ($_GET["status"] == 8){
				$prodrow = $db->query_first("SELECT * FROM {$config['tables']['food_ordering']} WHERE id = {$item}");				
				
				unset($account);
				$account = new accounting($prodrow['userid']);
				$price = 0;
				if(stristr($prodrow['opts'],"/")){
					$values = split("/",$prodrow['opts']);

					foreach ($values as $number){
						if(is_numeric($number)){
							$optrow = $db->query_first("SELECT price FROM {$config['tables']['food_option']} WHERE id = " . $number);
							$price += $optrow['price'];
						}

					}
				}else{
					$optrow = $db->query_first("SELECT price FROM {$config['tables']['food_option']} WHERE id = " . $prodrow['opts']);
					$price += $optrow['price'];
				}
				$totprice += $price * $prodrow['pice'];
				$account->change($totprice,t('Rückzahlung bei abbestellten Produkten')/* TRANS */ . " (" . $auth['username'] . ")");
				$db->query_first("DELETE FROM {$config['tables']['food_ordering']} WHERE id = " . $item);
			}else{
				$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET["status"]}, lastchange = '$time'  WHERE id = {$item}");
				if($_GET["status"] == 3){
					$user_id = $db->query_first("SELECT userid FROM {$config['tables']['food_ordering']} WHERE id = {$item}");
					$func->setainfo(t('Deine bestellten Produkte sind abholbereit')/* TRANS */,$user_id['userid'],2,"foodcenter",$item);
				}
			}
	
		}
		$func->confirmation(t('Array')/* TRANS */[$_GET["status"]],"index.php?mod=foodcenter&action=statchange");
	}else{
	
		$link_array[0] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=6";
		$link_array[1] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=5";
		$link_array[2] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=3";
		$link_array[3] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=7";
		$link_array[4] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=8";
		$link_array[5] = "index.php?mod=foodcenter&action=statchange";
		$func->multiquestion(t('Array')/* TRANS */,$link_array,t('Status setzen')/* TRANS */);
	}
	break;
	
	case 10:
		$dsp->NewContent(t('Produkt abbestellen')/* TRANS */,t('Bitte wählen sie die Produktanzahl die abbestellt werden soll.')/* TRANS */);
		$dsp->SetForm("index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=4");
		$dsp->AddDropDownFieldRow("delcount",t('Anzahl')/* TRANS */,$count_array,"");
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;
}
?>

