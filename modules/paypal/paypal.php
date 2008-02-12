<?php
	// GET FORM TO SEE WHAT YOU MUS PAY
	
if($auth['userid'] == 0 && $cfg['paypal_donation'] == 0){
	$func->error($lang['paypal']['error'],"index.php?mod=home");
}else{
	$dsp->NewContent($lang["paypal"]["caption"], $lang["paypal"]["subcaption"]);
	$dsp->AddModTpl("paypal","javascript");
	$dsp->SetForm("base.php?mod=paypal\" target=\"PopWnd\" onsubmit=\"submitpaypal(); return false;","paypal");

	// LIST ALL PARTYS
	if($auth['userid'] != 0){
		$pay_partys = $db->query("SELECT * FROM {$config["tables"]["party_user"]} AS pu LEFT JOIN {$config["tables"]["partys"]} AS p USING(party_id) LEFT JOIN {$config["tables"]["party_prices"]} AS price ON price.price_id=pu.price_id WHERE user_id={$auth['userid']} AND paid = '0'");

		if($db->num_rows($pay_partys) > 0){
			while($pay = $db->fetch_array($pay_partys)){
				$dsp->AddCheckBoxRow("price[]",$pay['name'],$pay['price_text'] . " " . $pay['price'] . " " . $cfg['paypal_currency_code'],"",NULL,NULL,NULL,$pay['price_id']);
				if($cfg['paypal_depot'] && $pay['depot_price'] > 0){
					$dsp->AddCheckBoxRow("depot[]",$pay['name'],$pay['depot_text'] . " " . $pay['depot_price'] . " " . $cfg['paypal_currency_code'],"",NULL,NULL,NULL,$pay['price_id']);
				}
			}
		}else{
			$dsp->AddSingleRow($lang['paypal']['no_not_paid_party']);
		}
		
			if($cfg['paypal_catering']){
				$dsp->AddTextFieldRow("catering",$lang["paypal"]["catering"],0,"");
			}
	}
	
	if($cfg['paypal_donation']){
		$dsp->AddTextFieldRow("donation",$lang["paypal"]["donation"],0,"");
	}
	
	$dsp->AddFormSubmitRow("next");
	$db->free_result($pay_partys);
	$dsp->AddContent();
}
	
?>
