<?php
$dsp->addsinglerow(print_r($auth, true));
	// GET FORM TO SEE WHAT YOU MUS PAY
	
if($auth['userid'] == 0 && $cfg['paypal_donation'] == 0){
	$func->error(t('Du kannst nichts bezahlen wenn du nicht eingeloggt bist.'),"index.php?mod=home");
}else{
	$dsp->NewContent(t('Einzahlen'), t('Hier sehen sie was f&uuml;r Betr&auml;ge noch ausstehend sind. W&auml;hlen sie was sie bezahlen m&ouml;chten.'));
	$dsp->AddSmartyTpl('javascript', 'paypal');
	$dsp->SetForm("index.php?mod=paypal&action=CreatePayment","paypal");

	// list all current parties with unpaid entrance fees
	if($auth['userid'] != 0){
		$pay_partys = $db->qry("SELECT * FROM %prefix%party_user AS pu LEFT JOIN %prefix%partys AS p USING(party_id) LEFT JOIN %prefix%party_prices AS price ON price.price_id=pu.price_id WHERE user_id=%int% AND paid = '0'", $auth['userid']);

		if($db->num_rows($pay_partys) > 0){
			while($pay = $db->fetch_array($pay_partys)){
				$dsp->AddCheckBoxRow("price[]",$pay['name'],$pay['price_text'] . " " . $pay['price'] . " " . $cfg['paypal_currency_code'],"",NULL,true,NULL,$pay['price_id']);
				if($cfg['paypal_depot'] && $pay['depot_price'] > 0){
					$dsp->AddCheckBoxRow("depot[]",$pay['name'],$pay['depot_text'] . " " . $pay['depot_price'] . " " . $cfg['paypal_currency_code'],"",NULL,NULL,NULL,$pay['price_id']);
				}
			
                                
                                
                                
                                
                                }
		}else{
			$dsp->AddSingleRow(t('Alle Eintrittspreise bezahlt.'));
		}
                //Show also payment options for clanmates, if the user is a clan admin
                $pay_clanmates = $db->qry('select pu.party_id, u.userid, u.clanid, pu.price_id, u.username from party_user as pu left join user as u on pu.user_id=u.userid where u.clanid=%int% and paid=0', $auth['clanid']);
                if ($db->num_rows($pay_clanmates > 0)) {
                    while ($pay = $db->fetch_array($pay_clanmates)){
                        $dsp->AddCheckBoxRow("clanmates[]",$pay['username'],"",NULL,true,NULL,$pay['userid'].'-'.$pay['price_id']);   
                    }
                    
                } else  {
                    $dsp->AddSingleRow(t('Keine offenen Zahlungen von Clanmitgliedern.'));
                    
                }
			if($cfg['paypal_catering']){
				$dsp->AddTextFieldRow("catering",t('Einzahlung f&uuml;r Catering'),0,"");
			}
	}
	
	if($cfg['paypal_donation']){
		$dsp->AddTextFieldRow("donation",t('Spende f&uuml;r die Organisatoren'),0,"");
	}
	
	$dsp->AddFormSubmitRow(t('Weiter'));
	$db->free_result($pay_partys);
	$dsp->AddContent();
}
	
?>
