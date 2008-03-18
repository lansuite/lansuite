<?php
	// GET FORM TO SEE WHAT YOU MUS PAY
	
if($auth['userid'] == 0 && $cfg['paypal_donation'] == 0){
	$func->error(t('Sie k&ouml;nnen nichts einzahlen wenn Sie nicht eingeloggt sind.'),"index.php?mod=home");
}else{
	$dsp->NewContent(t('Einzahlen'), t('Hier sehen sie was f&uuml;r Betr&auml;ge noch ausstehend sind. W&auml;hlen sie was sie bezahlen m&ouml;chten.'));
	$dsp->AddModTpl("paypal","javascript");
	$dsp->SetForm("index.php?mod=paypal&action=paying&design=base\" target=\"PopWnd\" onsubmit=\"submitpaypal(); return false;","paypal");

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
			$dsp->AddSingleRow(t('Alle Eintrittspreise bezahlt.'));
		}
		
			if($cfg['paypal_catering']){
				$dsp->AddTextFieldRow("catering",t('Einzahlung f&uuml;r Catering'),0,"");
			}
	}
	
	if($cfg['paypal_donation']){
		$dsp->AddTextFieldRow("donation",t('Spende f&uuml;r die Organisatoren'),0,"");
	}
	
	$dsp->AddFormSubmitRow("next");
	$db->free_result($pay_partys);
	$dsp->AddContent();
}
	
?>