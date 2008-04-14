<?php

include_once("modules/foodcenter/class_accounting.php");


class basket{
	
	var $count = 0;
	var $price = 0;
	var $product;
	var $account;
	
	function basket($backlink = null){
		// Load Basket
		if(!isset($_SESSION['basket_item']['product'])){
			$_SESSION['basket_item'] = array();
			$_SESSION['basket_count'] = 0;
			$this->count = 0;
			$this->product = new product_list();
		}else {
			$this->product = unserialize($_SESSION['basket_item']['product']);
			$this->count = $_SESSION['basket_count'];
		}
		if($backlink != null) $_SESSION['basket_item']['backlink'] = $backlink;
	}	
	
	
	function add_to_basket_from_global(){
		// Add new Products
		if(isset($_GET['add']) && $_GET['add'] > 0){
			if($_GET['opt'] != 0){
				$this->product->add_product($_GET['add'],$_GET['opt']);
				$this->count ++;
			}else{
				if(!is_array($_POST['option'])) $_POST['option'] = array();
				$this->product->add_product($_GET['add'],$_POST['option']);
				$this->count ++;
			}
		}
		
		$_SESSION['basket_item']['product'] = serialize($this->product);
		$_SESSION['basket_count'] = $this->count;
	}
	
	
	
	function show_basket(){
			global $dsp,$config,$db,$lang,$cfg;
			
			$dsp->NewContent(t('Warenkorb'),t('Um einen Artikel zu löschen, setzen Sie ihn auf 0 und klicken anschließend auf /\'/Neu berechnen/\'/.'));
			if($this->product->count_products() > 0){
				$dsp->SetForm("?mod=foodcenter&action={$_GET['action']}&mode=change");
				$dsp->AddDoubleRow("<b>" . t('Artikel / Preis') . "</b> ","<b>" . t('Anzahl') . "</b> ");

				$this->product->get_basket_form();			
				$dsp->AddDoubleRow("<b>" . t('Gesamtpreis: ') . "</b> " . $this->product->count_products_price() . " " . $cfg['sys_currency'],"<b>" . t('Anzahl Artikel: ') . "</b> " . $this->product->count_products());
				$dsp->AddFormSubmitRow("new_calculate",null,"calculate",false);
				if($_GET['action'] == "theke"){
					foreach (t('Array') as $key => $value){
						($key == 2) ? $selected = "selected" : $selected = "";
						$delivered_array[] .= "<option $selected value=\"$key\">$value</option>";
					}
					$dsp->AddDropDownFieldRow("delivered","",$delivered_array,"");
						  
				}
		
				$dsp->AddFormSubmitRow("order");
			}else{
				$dsp->AddDoubleRow("",t('Keine Artikel im Warenkorb'));
			}
			if(isset($_SESSION['basket_item']['backlink'])) $dsp->AddBackButton($_SESSION['basket_item']['backlink']);
			$dsp->AddContent();
			
	}
	
	
	function change_basket($userid){
		global $func,$lang, $cfg;
		$ok = true;
		$this->count = 0;
		foreach ($_POST as $key => $value){
				if(stristr($key,"option")){
					$tmp = split("_",$key);
					if(!$this->product->chanche_ordered($tmp[1],$tmp[2],$value)){
						$ok = false;
					}
					$this->count += $value;
				}
		}
		$this->product->check_list();
		$_SESSION['basket_item']['product'] = serialize($this->product);
		$_SESSION['basket_count'] = $this->count;
		
		$this->account = new accounting($userid);
		
		// Wird nur ausgeführt wenn Credit-System an
		if( $cfg['foodcenter_credit'] == 0)
		{
			if($this->product->count_products_price() <= $this->account->balance){
				return $ok;
			}else{
				$func->error(t('Nicht genügend Geld auf dem Konto.'),"index.php?mod=foodcenter&action={$_GET['action']}");
				return false;
			}
		}else{
		return $ok;
		}
		
	}
	
	function order_basket($userid, $delivered = 0){
		global $db,$config,$func,$lang,$auth;
		$this->account->change(- $this->product->order_product($userid,$delivered),t('Bestellung Foodcenter') . "  (" . $auth['username'] . ")");
		unset($this->product);
		$this->product = new product_list();
		unset($_SESSION['basket_item']['product']);
		$_SESSION['basket_item']['product'] = serialize($this->product);
		$_SESSION['basket_count'] = 0;

	}
	
}
?>