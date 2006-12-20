<?php

if (!$_GET['party_id']) $_GET['party_id'] = $party->party_id;

include_once('inc/classes/class_masterform.php');
$mf = new masterform();

$mf->AdditionalKey = 'party_id = '. (int)$_GET['party_id'];

$dsp->AddDoubleRow('Party', (int)$_GET['party_id']);

$mf->AddField($lang['signon']['price_text'], 'price_text');
$mf->AddField($lang['signon']['price'], 'price');
$mf->AddField($lang['signon']['depot_desc'], 'depot_desc');
$mf->AddField($lang['signon']['depot_price'], 'depot_price');

$mf->SendForm('index.php?mod=party&action=price_edit', 'party_prices', 'price_id', $_GET['price_id']);
$dsp->AddBackButton('index.php?mod=party&action=price');
$dsp->AddContent();


/*
switch ($_GET['step']){
	case 3:
		if($_POST['price_text'] == ""){
			$error_signon['price_text'] = $lang['signon']['error_price_text'];
			$_GET['step'] = 2;
		}			
		
		
		if($_POST['price'] == ""){
			$error_signon['price'] = $lang['signon']['error_price'];
			$_GET['step'] = 2;
		}			
	
	break;
	
	case 12:
		if($_POST['price_id'] == $_GET['price_id']){
			$_GET['step'] = 11;
		}
	break;
	
}


switch ($_GET['step']){
	
	default:
		$party->get_party_dropdown_form(1,'?mod=signon&action=price&step=2');
		$dsp->AddContent();
#	break;
	
	case 2 :
    #$_POST['price_id'] = $_GET['party_id'];



		$dsp->NewContent($lang['signon']['edit_price_caption'],$lang['signon']['edit_price_subcaption']);
		if($_GET['var'] == "update"){
			$dsp->SetForm("index.php?mod=signon&action=price&step=3&var=update");
			if(!isset($_POST['price_text'])){
				$row = $db->query_first("SELECT * FROM {$config['tables']['party_prices']} WHERE price_id={$_POST['price_id']}");
				$_POST['price_text'] = $row['price_text'];
				$_POST['price'] = $row['price'];
				$_POST['depot_desc'] = $row['depot_desc'];
				$_POST['depot_price'] = $row['depot_price'];
				$_POST['group_id'] = $row['group_id'];
			}
			print_r($_POST['price_id']);
			$dsp->AddSingleRow("<input type='hidden' name='price_id' value='{$_POST['price_id']}'>");
		}else{
			$dsp->SetForm("index.php?mod=signon&action=price&step=3&var=new");
		}
		
		$dsp->AddTextFieldRow("price_text",$lang['signon']['price_text'],$_POST['price_text'],$error_signon['price_text']);
		$dsp->AddTextFieldRow("price",$lang['signon']['price'],$_POST['price'],$error_signon['price']);
		$dsp->AddTextFieldRow("depot_desc",$lang['signon']['depot_desc'],$_POST['depot_desc'],$error_signon['depot_desc']);
		$dsp->AddTextFieldRow("depot_price",$lang['signon']['depot_price'],$_POST['depot_price'],$error_signon['depot_price']);
		
		if($party->get_price_count(0) > 1){
			$party->get_user_group_dropdown('NULL',1,$_POST['group_id']);
		}else{
			$dsp->AddDoubleRow($lang['class_party']['drowpdown_user_group'],$lang['signon']['price_group_0']);
		}
		$dsp->AddFormSubmitRow("add");
		$dsp->AddHRuleRow();
		if($_GET['var'] != "update"){
			$dsp->AddHRuleRow();			
			$dsp->SetForm("index.php?mod=signon&action=price&step=2&var=update");
			$party->get_price_dropdown("NULL");
			$dsp->AddFormSubmitRow("edit");
			$dsp->AddHRuleRow();			
			$dsp->SetForm("index.php?mod=signon&action=price&step=10");
			$party->get_price_dropdown("NULL");
			$dsp->AddFormSubmitRow("delete");
			
		}
			
			
		$dsp->AddBackButton("index.php?mod=signon&action=price","signon/price");
		$dsp->AddContent();
	break;
	
	case 3:
		if($_GET['var'] == "new"){
			$party->add_price($_POST['price_text'],$_POST['price'],$_POST['depot_desc'],$_POST['depot_price'],$_POST['group_id']);
			$func->confirmation($lang['signon']['add_price_ok'],'?mod=signon&action=price&step=2');
		}elseif ($_GET['var'] == "update"){
			$party->update_price($_POST['price_id'],$_POST['price_text'],$_POST['price'],$_POST['depot_desc'],$_POST['depot_price'],$_POST['group_id']);
			$func->confirmation($lang['signon']['edit_price_ok'],'?mod=signon&action=price&step=2');
		}else{
			$func->error($lang['signon']['entry_error'],'?mod=signon&action=price&step=2');
		}
		
	break;
	
	
	case 10:
		$row = $db->query_first("SELECT * FROM {$config['tables']['party_prices']} WHERE price_id={$_POST['price_id']}");
		$func->question(str_replace("%PRICE%",$row['price_text'],$lang['signon']['delete_price']),"index.php?mod=signon&action=price&step=11&price_id={$_POST['price_id']}","index.php?mod=signon&action=price&step=2");
	break;
	
	case 11:
		$dsp->NewContent($lang['signon']['delete_price_capt'],$lang['signon']['delete_price_subcapt']);
		$dsp->SetForm("index.php?mod=signon&action=price&step=12&price_id={$_GET['price_id']}");
		$party->get_price_dropdown("NULL");
		$dsp->AddFormSubmitRow("add");
		$dsp->AddContent();
	break;
	
	case 12:
		$party->delete_price($_GET['price_id'],$_POST['price_id']);
		$func->confirmation($lang['signon']['delete_price_ok'],"index.php?mod=signon&action=price&step=2");
	break;
		
}
*/
?>
