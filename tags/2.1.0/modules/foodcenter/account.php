<?php

include_once("modules/foodcenter/class_accounting.php");

$account = new accounting($auth['userid']);

if($auth['type'] > 1 && !isset($_GET['act'])){
	$_GET['act'] = "menu";
}elseif ($auth['type'] < 2){
	$_GET['act'] = "";
}

$step = $_GET['step'];

if($action == "payment" && $step == 3){
	if(!is_numeric($_POST['amount'])){
			$error['amount'] = $lang['foodcenter']['account_err_amount'];
			$step = 2;
	}
	
	if(strlen($_POST['comment'] . " (" . $auth['username'] . ")") > 255){
		$error['comment'] = $lang['foodcenter']['account_err_comm'];
		$step = 2;
	}
}


switch($_GET['act']){
	default: 
	case "list":
		$account->list_balance();
	break;


	case "menu":
		$dsp->NewContent($lang['foodcenter']['account_management'],$lang['foodcenter']['account_management_cap']);
		$dia_quest[] .= $lang['foodcenter']['account_payment']	;
		$dia_quest[] .= $lang['foodcenter']['account_him_balance'];
		$dia_quest[] .= $lang['foodcenter']['account_self_balance'];
		$dia_link[]	 .= "index.php?mod=foodcenter&action=account&act=payment";		
		$dia_link[]	 .= "index.php?mod=foodcenter&action=account&act=himbalance";		
		$dia_link[]	 .= "index.php?mod=foodcenter&action=account&act=list";		
		$func->multiquestion($dia_quest,$dia_link,"");
	break;
	
	case "payment":
		switch ($step){
			default:
				$mastersearch = new MasterSearch($vars, "index.php?mod=foodcenter&action=account&act=payment", "index.php?mod=foodcenter&action=account&act=payment&step=2&userid=","");
				$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
				$mastersearch->PrintForm();
				$mastersearch->Search();
				$mastersearch->PrintResult();

				$templ['index']['info']['content'] .= $mastersearch->GetReturn();
			
			break;
			
			
			case "2":
				$dsp->NewContent($lang['foodcenter']['account_payment']);
				$dsp->SetForm("index.php?mod=foodcenter&action=account&act=payment&step=3&userid=".$_GET['userid']);
				$dsp->AddTextFieldRow("amount",$lang['foodcenter']['account_amount'],$_POST['amount'],$error['amount']);
				$dsp->AddTextFieldRow("comment",$lang['foodcenter']['account_comment'],$_POST['comment'],$error['comment']);
				$dsp->AddFormSubmitRow("send");
				$dsp->AddContent();
				$account = new accounting($_GET['userid']);
				$account->list_balance();

			break;	
			
			
			case "3":
				$account = new accounting($_GET['userid']);
				$account->change($_POST['amount'],$_POST['comment'] . " (" . $auth['username'] . ")");
				$account->list_balance();
			break;
			
			
		}
	break;
		
	case "himbalance":
		switch ($step){
			default:
				$mastersearch = new MasterSearch($vars, "index.php?mod=foodcenter&action=account&act=payment", "index.php?mod=foodcenter&action=account&act=payment&step=2&userid=","");
				$mastersearch->LoadConfig("users", $lang['usrmgr']['ms_search'], $lang['usrmgr']['ms_result']);
				$mastersearch->PrintForm();
				$mastersearch->Search();
				$mastersearch->PrintResult();

				$templ['index']['info']['content'] .= $mastersearch->GetReturn();
			
			break;
			
			
			case "2":
				$account = new accounting($_GET['userid']);
				$account->list_balance();
			break;	
		}
	break;
}


?>