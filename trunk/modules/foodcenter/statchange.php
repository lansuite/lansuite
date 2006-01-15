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
		if($_GET['status'] == 5){
			$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_GET['status']}, lastchange = '$time', supplytime = '$time'  WHERE id = {$_GET['id']}");
		}elseif ($_GET['status'] == 4){
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
		}
		break;
}


switch ($_GET['step']){
	
	default:
		if(!isset($vars['search_select1'])) $vars['search_select1'] = 3;
		
		$mastersearch = new MasterSearch($vars, "index.php?mod=foodcenter&action=statchange", "index.php?mod=foodcenter&action=statchange&step=2&id=", "");

		switch ($vars['search_select1']){
			case 1:
				$mastersearch->config['title']	= $lang['foodcenter']['list_order'];
				$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_stop'];
			break;
			
			case 2:
				$mastersearch->config['title']	= $lang['foodcenter']['list_ordered'];
				$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_supplied'];
			break;
			
			case 3:
				$mastersearch->config['title']	= $lang['foodcenter']['list_fetch'];
				$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_wait'];
			break;
			
			case 4:
				$mastersearch->config['title']	= $lang['foodcenter']['list_fetched'];
				$mastersearch->config['no_items_caption'] = $lang['foodcenter']['ordered_no_supply'];
			break;
		}

		$mastersearch->LoadConfig("food_statchange", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$mastersearch->PrintForm();		
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();

		break;
	case 2:
	
	if(isset($_POST['checkbox'])){
		$time = time();
		$totprice = 0;
		foreach($_POST["checkbox"] AS $item) {
			if($_POST["action_select"] == 5){
				$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_POST["action_select"]}, lastchange = '$time', supplytime = '$time'  WHERE id = {$item}");
			}elseif ($_POST["action_select"] == 4){
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
				$db->query_first("DELETE FROM {$config['tables']['food_ordering']} WHERE id = " . $item);
			}else{
				$db->query("UPDATE {$config['tables']['food_ordering']} SET status = {$_POST["action_select"]}, lastchange = '$time'  WHERE id = {$item}");
			}
	
		}
		$account->change($totprice,$lang['foodcenter']['theke_repayment'] . " (" . $auth['username'] . ")");
		$func->confirmation($lang['foodcenter']['ordered_status_ask'][$_POST["action_select"]],"index.php?mod=foodcenter&action=statchange");
	}else{
	
		$link_array[0] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=5";
		$link_array[1] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=3";
		$link_array[2] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=2";
		$link_array[3] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=1";
		$link_array[4] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=4";
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

