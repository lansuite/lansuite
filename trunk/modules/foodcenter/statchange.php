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
		if($_GET['status'] == 4){
			$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET['status']}, lastchange = '$time', supplytime = '$time'  WHERE id = {$_GET['id']}");
		}elseif ($_GET['status'] == 5){
			$prodrow = $db->query_first("SELECT * FROM {$config['tables']['food_ordering']} WHERE id = {$_GET['id']}");				
			
			if($prodrow['pice'] > 1 && !isset($_POST['delcount'])){
				$count_array[] = "<option selected value=\"{$prodrow['pice']}\">{$lang['foodcenter']['ordered_delete_all']}</option>";
				
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
				$account->change($price,$lang['foodcenter']['theke_repayment'] . " (" . $auth['username'] . ")");
				
				if(!isset($_POST['delcount']) || $_POST['delcount'] == $prodrow['pice']){
					$db->query_first("DELETE FROM {$config['tables']['food_ordering']} WHERE id = " . $_GET['id']);
				}else{
					$pice = $prodrow['pice'] - $_POST['delcount'];
					$db->query_first("UPDATE {$config['tables']['food_ordering']} SET pice = {$pice} WHERE id = " . $_GET['id']);
				}
			}
		}else{
			$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET['status']}, lastchange = '$time' WHERE id = {$_GET['id']}");
			if($_GET['status'] == 3){
				$user_id = $db->query_first("SELECT userid FROM {$config['tables']['food_ordering']} WHERE id = {$_GET['id']}");
				$func->setainfo($lang['foodcenter']['statchange_fetch'],$user_id['userid'],2,"foodcenter",$_GET['id']);
			}
		}
		break;
}


switch ($_GET['step']){
	
	default:

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('news');

    $ms2->query['from'] = "{$config['tables']['food_ordering']} AS a
		  LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id
		  LEFT JOIN {$config['tables']['food_supp']} AS s ON p.supp_id = s.supp_id
		  LEFT JOIN {$config['tables']['user']} AS u ON u.userid = a.userid
      ";

    $ms2->AddTextSearchField('Titel', array('p.caption' => 'like'));
    $ms2->AddTextSearchDropDown('Status', 'a.status', array('1' => 'Wird bestellt', '2' => 'Lieferung erwartet', '3' => 'Abholbereit', '4' => 'abgeholt'), 3);

  	$supp = $db->query("SELECT * FROM {$config['tables']['food_supp']}");
  	$supp_array[''] = $lang['ms']['select_all'];
  	while ($supprows = $db->fetch_array($supp)) {
  		$supp_array[$supprows['supp_id']] = $supprows['name'];
  	}
    $ms2->AddTextSearchDropDown('Lieferant', 's.supp_id', $supp_array);

  	$userquery = $db->query("SELECT * FROM {$config['tables']['food_ordering']} AS a LEFT JOIN {$config['tables']['user']} AS u ON a.userid=u.userid");
  	$user_array[''] = $lang['ms']['select_all'];
  	while ($userrows = $db->fetch_array($userquery)) {
  		$user_array[$userrows['userid']] = $userrows['username'];
  	}
    $ms2->AddTextSearchDropDown('Besteller', 'a.userid', $user_array);

    $ms2->AddSelect('u.userid');
    $ms2->AddResultField('Titel', 'p.caption');
    $ms2->AddResultField('Beschreibung', 'a.opts');
    $ms2->AddResultField('Besteller', 'u.username', 'UserNameAndIcon');
    $ms2->AddResultField('Bestellt', 'a.ordertime', 'MS2GetDate');
    $ms2->AddResultField('Lieferant', 's.name');
    $ms2->AddResultField('Geliefert', 'a.supplytime', 'MS2GetDate');
    $ms2->AddResultField('Anzahl', 'a.pice');

    $ms2->AddIconField('details', 'index.php?mod=foodcenter&action=statchange&step=2&id=', $lang['ms2']['details']);
	
	  $ms2->AddMultiSelectAction($lang['foodcenter']['ordered_status_quest'][0], 'index.php?mod=foodcenter&action=statchange&step=2&status=4', 1);
	  $ms2->AddMultiSelectAction($lang['foodcenter']['ordered_status_quest'][1], 'index.php?mod=foodcenter&action=statchange&step=2&status=3', 1);
	  $ms2->AddMultiSelectAction($lang['foodcenter']['ordered_status_quest'][2], 'index.php?mod=foodcenter&action=statchange&step=2&status=2', 1);
	  $ms2->AddMultiSelectAction($lang['foodcenter']['ordered_status_quest'][3], 'index.php?mod=foodcenter&action=statchange&step=2&status=1', 1);
	  $ms2->AddMultiSelectAction($lang['foodcenter']['ordered_status_quest'][4], 'index.php?mod=foodcenter&action=statchange&step=2&status=5', 1);

    switch ($_POST['search_dd_input'][0]){
    	case 1:
        $dsp->NewContent($lang['foodcenter']['list_order'], '');
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_stop'];
    	break;

    	case 2:
        $dsp->NewContent($lang['foodcenter']['list_ordered'], '');
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_supplied'];
    	break;

    	case 3:
        $dsp->NewContent($lang['foodcenter']['list_fetch'], '');
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_wait'];
    	break;

    	case 4:
        $dsp->NewContent($lang['foodcenter']['list_fetched'], '');
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_supply'];
    	break;
    }

    $ms2->PrintSearch('index.php?mod=foodcenter&action=statchange', 'p.id');


		$handle = opendir("ext_inc/foodcenter_templates");
		while ($file = readdir ($handle)) if (($file != ".") and ($file != "..") and ($file != "CVS") and (!is_dir($file))) {
			if((substr($file, -3, 3) == "htm") && (substr($file, -7, 7) != "row.htm") || (substr($file, -4, 4) == "html") && (substr($file, -8, 8) != "row.html")){
				$file_array[] .= "<option value=\"$file\">$file</option>";
			}
		}
		$dsp->SetForm("index.php?mod=foodcenter&action=print&design=base\" target=\"_blank\"","print");
		$dsp->AddDropDownFieldRow("file",$lang['foodcenter']['template'],$file_array,"");
		
		$templ['index']['info']['content'] .= "<input type=\"hidden\" name=\"search_keywords\" value=\"{$vars['search_keywords']}\">";
		$templ['index']['info']['content'] .= "<input type=\"hidden\" name=\"search_select1\" value=\"{$vars['search_select1']}\">";
		$templ['index']['info']['content'] .= "<input type=\"hidden\" name=\"search_select2\" value=\"{$vars['search_select2']}\">";
		$templ['index']['info']['content'] .= "<input type=\"hidden\" name=\"search_select3\" value=\"{$vars['search_select3']}\">";
		$dsp->AddFormSubmitRow("print");
		$dsp->AddContent();
		break;
	case 2:
	
	if($_POST['action']){
		$time = time();
		$totprice = 0;
		foreach($_POST["action"] AS $item => $val) {
			if($_GET["status"] == 4){
				$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET["status"]}, lastchange = '$time', supplytime = '$time'  WHERE id = {$item}");
			}elseif ($_GET["status"] == 5){
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
				$account->change($totprice,$lang['foodcenter']['theke_repayment'] . " (" . $auth['username'] . ")");
				$db->query_first("DELETE FROM {$config['tables']['food_ordering']} WHERE id = " . $item);
			}else{
				$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET["status"]}, lastchange = '$time'  WHERE id = {$item}");
				if($_GET["status"] == 3){
					$user_id = $db->query_first("SELECT userid FROM {$config['tables']['food_ordering']} WHERE id = {$item}");
					$func->setainfo($lang['foodcenter']['statchange_fetch'],$user_id['userid'],2,"foodcenter",$item);
				}
			}
	
		}
		$func->confirmation($lang['foodcenter']['ordered_status_ask'][$_GET["status"]],"index.php?mod=foodcenter&action=statchange");
	}else{
	
		$link_array[0] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=4";
		$link_array[1] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=3";
		$link_array[2] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=2";
		$link_array[3] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=1";
		$link_array[4] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=5";
		$link_array[5] = "index.php?mod=foodcenter&action=statchange";
		$func->multiquestion($lang['foodcenter']['ordered_status_quest'],$link_array,$lang['foodcenter']['ordered_status_question']);
	}
	break;
	
	case 10:
		$dsp->NewContent($lang['foodcenter']['ordered_delete_capt'],$lang['foodcenter']['ordered_delete_subcapt']);
		$dsp->SetForm("index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=4");
		$dsp->AddDropDownFieldRow("delcount",$lang['foodcenter']['ordered_delete_count'],$count_array,"");
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	break;
}
?>

