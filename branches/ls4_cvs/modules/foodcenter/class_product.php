<?php

/**
 * 	Produktliste
 *	Wird verwendet um zum einen die Liste der Speisekarte anzuzeigen
 *  zum anderen für den Warenkorb
 */
class product_list{
	/**
	 * Array mit den Produktnummern
	 *
	 * @var int
	 */
	var $product_list	= array();
	/**
	 * Array mit allen in der Liste enthaltenen Produkte 
	 *
	 * @var product
	 */
	var $product		= array();

	
	/**
	 * Lade alle Produkte einer Kategorie
	 *
	 * @param int $cat
	 */
	function load_cat($cat){
		global $db,$config;
		$products = $db->query("SELECT id FROM {$config['tables']['food_product']} WHERE cat_id={$cat}");
		
		$i = 0;
		while ($data = $db->fetch_array($products)){
			$this->product_list[$i] .= $data['id'];
			$this->product[$i] = new product($data['id']);
			$i++;
		}
	}
	
	/**
	 * Produktliste für anzeige ausgeben
	 *
	 * @param string $worklink
	 */
	function get_list($worklink){
		global $dsp,$lang;
		
		if(count($this->product) > 0){
			for($i = 0;$i < count($this->product);$i++){
				$this->product[$i]->order_form($worklink);
			}
		}else{
			$dsp->AddSingleRow($lang['foodcenter']['show_product_noproducts']);	
		}
	}
	
	/**
	 * Zeige Detailansicht eines eingefügten Produktes
	 *
	 * @param int $id
	 * @param string $worklink
	 */
	function get_info($id,$worklink){
		global $dsp,$lang,$cfg,$db,$config;
		
		$data_array = array_flip($this->product_list);
		$this->product[$data_array[$id]]->get_info($worklink);
		
	}
	
	/**
	 * Produkt zur Liste hinzufügen
	 * Gibt true zurück wenn das Produkt hinzugefügt wurde sonst false
	 * 
	 * @param int $id
	 * @param array or int $opt
	 * @return boolean
	 */
	function add_product($id,$opt){
		// Produkt schon vorhanden?
		if(in_array($id,$this->product_list)){
		
			// Wenn das Produkt ein 
			if(is_array($opt)){
				// Produkt für den Vergleich erzeugen
				$temp_prod = new product($id);
				$temp_prod->ordered++;
				
				foreach ($opt as $key => $value){
					$temp_prod->order_option($key);
				}
				// Liste nach gleichem Produkt durchsuchen
				foreach ($this->product_list as $key => $value){
					// Wenn das Produkt gefunden wird Vergleichen
					if($value == $id){
						// Vergleich Positiv Produkt aufaddieren und Funktion verlassen
						if($this->product[$key]->compare($temp_prod)){
							$this->product[$key]->ordered++;
							return true;
						}
					}
				}
				// Vergleich Fehlgeschlagen 
				// Letzten Key auslesen
				end($this->product);
				$key_array = each($this->product);
				(count($this->product) == 0) ? $key = 0 : $key = $key_array[0] + 1;
	
				// Produkt hinzufügen
				$this->product[$key] = new product($id);
				$this->product[$key]->ordered++;
				$this->product_list[] .= $id;
				foreach ($opt as $cle => $value){
					$this->product[$key]->order_option($cle);
				}
				return true;
			}else{
				// Produkt suchen und aufaddieren
				foreach ($this->product_list as $key => $value){
					if($value == $id){
						$this->product[$key]->order_option($opt);
						return true;
					}
				}
				return false;
			}
		}else{
			// Letzten Key auslesen
			end($this->product);
			$key_array = each($this->product);
			(count($this->product) == 0) ? $key = 0 : $key = $key_array[0] + 1;
	
			// Produkt hinzufügen
			$this->product[$key] = new product($id);
			$this->product[$key]->ordered++;
			$this->product_list[] .= $id;
			
			if(is_array($opt)){
				foreach ($opt as $cle => $value){
					$this->product[$key]->order_option($cle);
				}
			}else{
				$this->product[$key]->order_option($opt);				
			}
			return $key;
		}			
			
	}
	
	/**
	 * Warenkorb neu schreiben bei änderungen
	 *
	 * @param int $listid
	 * @param array or int $opt
	 * @param int $value
	 * @return true or false 
	 */
	function chanche_ordered($listid,$opt,$value){
		if(!is_null($opt)){
			//print_r($this->product[$listid]);
			return $this->product[$listid]->order_option($opt,$value);
		}else{
			return $this->product[$listid]->set_ordered($value);
			// $this->product[$listid]->ordered = $value;	
		}
	}
	
	/**
	 * Leere objekte aus der Liste entfernen
	 *
	 */
	function check_list(){
		foreach ($this->product_list as $key => $value){
			if($this->product[$key]->count_unit() == 0){
				unset($this->product[$key]);
				unset($this->product_list[$key]);
			}
		}
	}
	
	/**
	 * Erzeuge Formular für Warenkorb
	 *
	 */
	function get_basket_form(){
		foreach ($this->product_list as $key => $value){
			$this->product[$key]->get_basket($key);	
		}
	}
	
	/**
	 * Produkte zählen
	 *
	 * @return int
	 */
	function count_products(){
		foreach ($this->product_list as $key => $value){
			$count += $this->product[$key]->count_unit();	
		}
		return $count;
	}
	
	/**
	 * Produktepreis zusammenzählen
	 *
	 * @return int
	 */
	function count_products_price(){
		foreach ($this->product_list as $key => $value){
			$price += $this->product[$key]->count_price();	
		}
		return $price;
	}
	
	/**
	 * Produkt kaufen 
	 *
	 * @param int $userid
	 * @param array $delivered
	 * @return int
	 */
	function order_product($userid,$delivered){
		$price = 0;
		foreach ($this->product_list as $key => $value){
			$price += $this->product[$key]->order($userid,$delivered);
		}
		return $price;
	}
}


/**
 * Produkt Klasse
 * Ermöglicht alle Funktionen die für ein Produkt benötigt werden.
 *
 */
class product{
	/**
	 * Produktid
	 *
	 * @var int
	 */
	var $id			= null;
	/**
	 * Produktname
	 *
	 * @var string
	 */
	var $caption 	= "";
	/**
	 * Produktbeschreibung
	 *
	 * @var string
	 */
	var $desc 		= "";
	/**
	 * Kategorie 
	 *
	 * @var cat_object
	 */
	var $cat;
	/**
	 * Lieferant
	 *
	 * @var supp_object
	 */
	var $supp;
	/**
	 * Lieferanten Infos
	 *
	 * @var string
	 */
	var $supp_infos;
	/**
	 * Produktebild
	 *
	 * @var string
	 */
	var $pic		= "";
	/**
	 * Materialverwaltung
	 *
	 * @var boolean
	 */
	var $mat		= "";
	/**
	 * Produktetype
	 *
	 * @var int
	 */
	var $type		= null;
	/**
	 * Mehrfachauswahl
	 *
	 * @var int
	 */
	var $choise		= 0;
	/**
	 * Bestellartikel
	 *
	 * @var int
	 */
	var $wait		= 0;
	/**
	 * Anzahl bestellte Produkte
	 *
	 * @var int
	 */
	var $ordered	= 0;
	/**
	 * Produkteoptionen
	 *
	 * @var array
	 */
	var $option 	= array();
	/**
	 * Fehlerarray
	 *
	 * @var array
	 */
	var $error_food	= array();
	/**
	 * Fehlerstatus
	 *
	 * @var boolean
	 */
	var $noerror	= true;
			
	/**
	 * Konstruktor bestehendes Produkt wird geladen sonst ein neues erzeugt
	 *
	 * @param int $id
	 * @return product
	 */
	function product($id = null){
		if($id != null && $id > 0){
			$this->id = $id;
			$this->read();
		}
	}

	/*
	function get_product_by_option($id){
		global $db,$config;
		
		$option = $db->query_first("SELECT parentid FROM {$config['tables']['food_option']} WHERE id=$id");	
		
		$product = $db->query_first("SELECT * FROM {$config['tables']['food_product']} WHERE id={$option['parentid']}");	
	}*/
	
	/**
	 * Produktinformationen aus dem Formular auslesen
	 *
	 */
	function read_post(){
		$this->caption 	=	$_POST['p_caption'];
		$this->desc		=	$_POST['desc'];
		$this->cat		=	new cat($_POST['cat_id']);
		$this->supp		=	new supp($_POST['supp_id']);
		$this->supp_infos	=	$_POST['supp_infos'];
		$this->mat		=	$_POST['mat'];
		$this->type		=	$_POST['product_type'];
		$this->choise	=	$_POST['chois'];
		$this->wait 	= 	$_POST['wait'];
		$this->pic		= 	$_POST['pic'];
				
		$this->cat->read_post();
		$this->supp->read_post();
		
		if($this->type == 1){
			for($i=0;$i < 3;$i++){
				if($_POST['hidden'][$i] > 0){
					$this->option[$i]->read_post($this->id,$this->type,$i);
				}elseif($_POST['price'][$i] != ""){
					$x = count($this->option);
					$this->option[$x]	= new product_option();
					$this->option[$x]->read_post($this->id,$this->type,$i);
				}
			}
		}elseif ($this->type == 2){
			(isset($_POST['caption'][0])) ? $q = 0 : $q = 3;
			for($i=$q;$i < ($q + 8);$i++){
				if($_POST['hidden'][$i] > 0 ){
					$this->option[$i]->read_post($this->id,$this->type,$i);
				}elseif($_POST['caption'][$i] != "" || $i == $q){
					$x = count($this->option);
					$this->option[$x]	= new product_option();
					$this->option[$x]->read_post($this->id,$this->type,$i);
				}
			}
		}
	}
	
	/**
	 * Eingaben aus dem Formular prüfen
	 *
	 * @return boolean
	 */
	function check(){
		global $lang,$func;
		if($this->caption == ""){
			$this->error_food['caption'] = $lang['foodcenter']['add_product_err_caption'];
			$this->noerror = false;
		}
		
		if($_FILES['file']['error'] != 0 && $_FILES['file']['name'] != ""){
			$this->error_food['file']	= $lang['foodcenter']['add_product_err_file'];
			$this->noerror = false;
		}elseif($_FILES['file']['name'] != ""){
			$func->FileUpload("file","ext_inc/foodcenter/",$_FILES['file']['name']);
			$_POST['pic'] = $_FILES['file']['name'];
			$this->pic = $_FILES['file']['name'];
			
		}

		if($this->cat->check() == false) $this->noerror = false;
		if($this->supp->check() == false) $this->noerror = false;
		for($i=0;$i < count($this->option);$i++){
			if($this->option[$i]->check() == false) $this->noerror = false;
		}
	
		return $this->noerror;

	}
	
	/**
	 * Produktdaten aus der DB lesen
	 *
	 * @return boolean
	 */
	function read(){
		global $db,$config;
		if($this->id == null){
			return false;
		}else {
			$row = $db->query_first("SELECT * FROM {$config['tables']['food_product']} WHERE id={$this->id}");
			
			
			$this->caption 	=	$row['caption'];
			$this->desc		=	$row['p_desc'];
			$this->cat		=	new cat($row['cat_id']);
			$this->supp		=	new supp($row['supp_id']);
			$this->supp_infos	=	$row['supp_infos'];
			$this->mat		=	$row['mat'];
			$this->type		=	$row['p_type'];
			$this->choise	=	$row['chois'];
			$this->wait		= 	$row['wait'];
			$this->pic		= 	$row['p_file'];
			
			$opt = $db->query("SELECT id FROM {$config['tables']['food_option']} WHERE parentid={$this->id}");
			
			$int = 0;
			while ($option = $db->fetch_array($opt)){
				$this->option[$int] = new product_option($option['id'],$this->type);
				$int++;
			}
		}
		return true;
	}
	
	/**
	 * Produktdaten in die Datenbank schreiben
	 *
	 */
	function write(){
		global $db,$config;	

		if($this->supp->supp_id == null) $this->supp->write();
		if($this->cat->cat_id == null) $this->cat->write();
		
		if($this->id == null || $this->id < 1){
			$db->query("INSERT INTO {$config['tables']['food_product']} SET
						caption = '{$this->caption}',
						p_desc = '{$this->desc}',
						cat_id = '{$this->cat->cat_id}',
						supp_id = '{$this->supp->supp_id}',
						supp_infos = '{$this->supp_infos}',
						p_file = '{$this->pic}',
						mat = '{$this->mat}',
						p_type = '{$this->type}',
						wait = '{$this->wait}',
						chois = '{$this->choise}'");	
			$this->id = $db->insert_id();
		}else{
			$db->query("UPDATE {$config['tables']['food_product']} SET
						caption = '{$this->caption}',
						p_desc = '{$this->desc}',
						cat_id = '{$this->cat->cat_id}',
						supp_id = '{$this->supp->supp_id}',
						supp_infos = '{$this->supp_infos}',
						p_file = '{$this->pic}',
						mat = '{$this->mat}',
						p_type = '{$this->type}',
						chois = '{$this->choise}',
						wait = '{$this->wait}'
						WHERE id={$this->id}");		
		}
		// Save Productsoption
		foreach ($this->option as $opts){
			$opts->write($this->id);
		}
			
	}
	
	/**
	 * Preis zusammenzählen
	 *
	 * @return int
	 */
	function count_price(){
		if($this->type == 2){
			for($i=0;$i<count($this->option);$i++){
				if(is_object($this->option[$i])){
					$tot_price += $this->option[$i]->count_price();
				}
			}
			return  $this->ordered * $tot_price;
		}else{
			for($i=0;$i<count($this->option);$i++){
				if(is_object($this->option[$i])){
					$tot_price += $this->option[$i]->count_price();
				}
			}
			return $tot_price;
		}
		
	}
	
	/**
	 * Produktioption bestellen
	 *
	 * @param int $id
	 * @param int $value
	 * @return boolean
	 */
	function order_option($id,$value = null){
		global $lang;
		$ok = true;
		for($i = 0;$i < count($this->option);$i++){
			$count = $this->option[$i]->ordered;
			if($this->option[$i]->id == $id){
				if($value == null){
					$this->option[$i]->ordered++;
				}else{
					if($this->mat == 0 || $this->option[$i]->pice >= $value){
						$this->option[$i]->ordered = $value;
					}else{
						$this->option[$i]->ordered = $this->option[$i]->pice;
						$this->option[$i]->error['pice_error'] = $lang['foodcenter']['add_product_err_pice_count'];
						$ok = false;
					}
				}
			}	
		}
		return $ok;
	}
	
	/**
	 * Produkte zählen
	 *
	 * @return int
	 */
	function count_unit(){
		if($this->type == 2){
			return $this->ordered;
		}else{
			for($i=0;$i<count($this->option);$i++){
				if($this->option[$i]){
					$count += $this->option[$i]->ordered;
				}
			}
			return $count;
		}
	}
	
	/**
	 * Formular für das ändern und hinzufügen von Produkten ausgeben
	 *
	 * @param int $step
	 */
	function form_add_product($step){
		global $dsp,$gd,$lang,$templ;

		$nextstep = $step + 1;
		// Change or New ?
		if($this->id != null){
			$dsp->NewContent($lang['foodcenter']['add_product_add_cap'],$lang['foodcenter']['add_product_add_subcap']);
			$dsp->SetForm("?mod=foodcenter&action=addproduct&step=$nextstep&id={$this->id}","food_add", "", "multipart/form-data");	
		}else{
			$dsp->NewContent($lang['foodcenter']['add_product_edit_cap'],$lang['foodcenter']['add_product_edit_cap']);
			$dsp->SetForm("?mod=foodcenter&action=addproduct&step=$nextstep","food_add","", "multipart/form-data");
		}		
		
		// Add Javascript Code
		$dsp->AddModTpl("foodcenter","javascript");
		$dsp->AddTextFieldRow("p_caption",$lang['foodcenter']['add_product_prod_cap'],$this->caption,$this->error_food['caption']);
		$dsp->AddTextAreaRow("desc",$lang['foodcenter']['add_product_prod_desc'],$this->desc,$this->error_food['desc'],NULL,NULL,true);

		// Not functional now
		// Pic is only active with gd-Libary
		if ($gd->available){
			$dsp->AddFileSelectRow("file",$lang['foodcenter']['add_product_prod_pic'],$this->error_food['file'],NULL,NULL,true);
			$dsp->AddPictureDropDownRow("pic",$lang['foodcenter']['add_product_prod_pic'],"ext_inc/foodcenter",$this->error_food['file'],true,basename($this->pic));
		}

		// Select Cat
		if(!is_object($this->cat)) $this->cat = new cat();
		$this->cat->cat_form();

		// Select Supplier
		if(!is_object($this->supp)) $this->supp = new supp();
		$this->supp->supp_form();

			$dsp->AddTextFieldRow("supp_infos",$lang['foodcenter']['add_product_prod_supp_desc'],$this->supp_infos,"",null,true);


		// Picecontrol ?
		$dsp->AddCheckBoxRow("mat",$lang['foodcenter']['add_product_prod_mat_text'],$lang['foodcenter']['add_product_prod_mat_quest'],"",NULL,$this->mat,NULL,NULL);
		// Orderproduct ?
		$dsp->AddCheckBoxRow("wait",$lang['foodcenter']['add_product_prod_order'],$lang['foodcenter']['add_product_prod_order_text'],"",NULL,$this->wait,NULL,NULL);

		// Hiden not Selected Option an List Product Options
		foreach ($lang['foodcenter']['add_product_prod_opt'] as $key => $value){
			if($key == $this->type){
				$selected = "selected";
				$display[$key] = "";
			}else{
				$selected = "";
				$display[$key] = "none";
			}
			$opts[] .= "<option $selected value=\"$key\">$value</option>";

		}
		if($_POST['product_opts'] == ""){
			$display[1] = "";
		}

		if($this->type != null){
			$dsp->AddDropDownFieldRow("product_type\" disabled onchange=\"change_option(this.options[this.options.selectedIndex].value)\"","<input type=\"hidden\" name=\"product_type\" value=\"{$this->type}\" />" . $lang['foodcenter']['add_product_prod_opt_text'],$opts,$this->error_food['product_opts']);
		}else {
			$dsp->AddDropDownFieldRow("product_type\" onchange=\"change_option(this.options[this.options.selectedIndex].value)\"",$lang['foodcenter']['add_product_prod_opt_text'],$opts,$this->error_food['product_opts']);
		}


		if($this->type == null || $this->type == 1){
			// display HTML for option 1
			$templ['ls']['row']['hidden_row']['id'] = "food_1";
			$templ['ls']['row']['hidden_row']['display'] = $display[1];
			$dsp->AddModTpl("foodcenter","hiddenbox_start");

			for($i = 0;$i < 3;$i++){
				($i == 0) ? $optional = null : $optional = true;
				if(!is_object($this->option[$i])) $this->option[$i] = new product_option();
				$this->option[$i]->option_form($i,$optional);
			}
			$dsp->AddModTpl("foodcenter","hiddenbox_stop");
		}

		if($this->type == null || $this->type == 2){
			// display HTML for option 2
			$templ['ls']['row']['hidden_row']['id'] = "food_2";
			$templ['ls']['row']['hidden_row']['display'] = $display[2];
			$dsp->AddModTpl("foodcenter","hiddenbox_start");
			$dsp->AddCheckBoxRow("chois\" onclick=\"change_optionelem(this.checked)",$lang['foodcenter']['add_product_option_choise'],"","",null,$this->choise);
			($this->type == null) ? $q = 3 : $q = 0;
			for($i = $q;$i < ($q+8);$i++){
				($i == $q) ? $optional = null : $optional = true;
				if(!is_object($this->option[$i])) $this->option[$i] = new product_option();
				$this->option[$i]->option_form($i,$optional,true,$this->choise);
			}
			$dsp->AddModTpl("foodcenter","hiddenbox_stop");
		}
		if($this->id != null){
			$dsp->AddFormSubmitRow("edit");
		}else {
			$dsp->AddFormSubmitRow("add");
		}
		
		
		$dsp->AddContent();
	}
	
	
	/**
	 * Bestellforumlar anzeigen
	 *
	 * @param string $worklink
	 */
	function order_form($worklink){
		global $dsp,$cfg,$templ,$auth;
		
		switch ($this->type){
			case 1:
				unset($templ['foodcenter']['product']['pricerow']['name']);
				unset($templ['foodcenter']['product']['pricerow']["price_1"]);
				unset($templ['foodcenter']['product']['pricerow']["price_2"]);
				unset($templ['foodcenter']['product']['pricerow']["price_3"]);
				
				$templ['foodcenter']['product']['pricerow']['name'] = "<a href='$worklink&info={$this->id}'><b>" . $this->caption . "</b><br />" . $this->desc . "</a>";
				if(is_object($this->option[0])){
					$templ['foodcenter']['product']['pricerow']["price_3"] = "<b>" . $this->option[0]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'>" . $this->option[0]->price . " " . $cfg['sys_currency'] . "</a>";
					$templ['foodcenter']['product']['pricerow']["price_3"] .= "<a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" align=\"right\" /></a>";
				}
				if(is_object($this->option[1])){
					$templ['foodcenter']['product']['pricerow']["price_2"] = "<b>" . $this->option[1]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'>" . $this->option[1]->price . " " . $cfg['sys_currency'] . "</a>";
					$templ['foodcenter']['product']['pricerow']["price_2"] .= "<a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" align=\"right\" /></a>";
				}
				if(is_object($this->option[2])){
					$templ['foodcenter']['product']['pricerow']["price_1"] = "<b>" . $this->option[2]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[2]->price . " " . $cfg['sys_currency'] . "</a>";
					$templ['foodcenter']['product']['pricerow']["price_1"] .= "<a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" align=\"right\" /></a>";
				}
				$dsp->AddDoubleRow($templ['foodcenter']['product']['pricerow']['name'], $dsp->FetchModTpl('foodcenter', 'product_price_row'));
				break;
			case 2:
				if($this->choise == 1){
					$dsp->SetForm("$worklink&add={$this->id}&opt=0");
				}
				$i = 0;
				while (is_object($this->option[$i])){
					if($i==0){
						if($this->choise == 0){
							$dsp->AddHRuleRow();
							$dsp->AddDoubleRow("<a href='$worklink&info={$this->id}'>" . $this->caption . "</a>",$this->option[$i]->caption . " " . $this->option[$i]->unit . " <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");	
						}else{
							$dsp->AddHRuleRow();
							$dsp->AddCheckBoxRow("option[{$this->id}]","<a href='$worklink&info={$this->id}'>" . $this->caption . "</a>",$this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'],"",null,$this->option[$i]->fix,$this->option[$i]->fix);	
						}
					}else{
						if($this->choise == 0){
							$dsp->AddDoubleRow("",$this->option[$i]->caption . " " . $this->option[$i]->unit . "   <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");	
						}else{
							$dsp->AddCheckBoxRow("option[{$this->option[$i]->id}]","",$this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'],"",null,$this->option[$i]->fix,$this->option[$i]->fix);	
						}

						
					}
					$i++;
				}
				if($this->choise == 1){
					$dsp->AddFormSubmitRow("order");
				}
				break;
					
		}
	}
	
	/**
	 * Eintrag für den Warenkorb anzeigen
	 *
	 * @param int $listid
	 */
	function get_basket($listid){
		global $dsp;
		$show_caption = $this->caption;
		if($this->type == 1 || $this->choise == false){
			for($i = 0; $i < count($this->option);$i++){
				if($this->option[$i]->ordered > 0)	$this->option[$i]->get_basket($listid,$show_caption,false);
			}
		}else{
			$dsp->AddTextFieldRow("option_$listid",$this->caption,$this->ordered,$this->error_food['order_error']);
			$this->error_food['order_error'] = "";
			for($i = 0; $i < count($this->option);$i++){
				if($this->option[$i]->ordered > 0 || $this->option[$i]->fix > 0) $this->option[$i]->get_basket($listid,$show_caption,true);
			}
			
		}
			
	}

	/**
	 * Detailanzeige des Produktes
	 *
	 * @param string $worklink
	 */
	function get_info($worklink){
		global $dsp,$lang,$auth,$cfg;
				
			$dsp->NewContent($lang['foodcenter']['product_desc']);
			$dsp->AddDoubleRow($lang['foodcenter']['add_product_prod_cap'],"<b>" . $this->caption . "</b>");
			if($this->desc != "") $dsp->AddDoubleRow($lang['foodcenter']['add_product_prod_desc'],$this->desc);
			if($this->pic != "" && file_exists("ext_inc/foodcenter/" . $this->pic)) $dsp->AddDoubleRow("","<img src=\"ext_inc/foodcenter/{$this->pic}\" border=\"0\" alt=\"{$this->caption}\" />");
			$dsp->AddSingleRow($lang['foodcenter']['product_choise']);
			
			switch ($this->type){

				case 1:
				
				if(is_object($this->option[0])){
					$dsp->AddDoubleRow("","<b>" . $this->option[0]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'>" . $this->option[0]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" /></a>");
				}
				if(is_object($this->option[1])){
					$dsp->AddDoubleRow("","<b>" . $this->option[1]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'>" . $this->option[1]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" /></a>");
				}
				if(is_object($this->option[2])){
					$dsp->AddDoubleRow("","<b>" . $this->option[2]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[2]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" /></a>");
				}

				break;
				
				case 2:
				if($this->choise == 1){
					$dsp->SetForm("$worklink&add={$this->id}&opt=0");
				}
				$i = 0;
				while (is_object($this->option[$i])){
					if($i==0){
						if($this->choise == 0){
							$dsp->AddDoubleRow("<b>" . $this->caption . "</b>",$this->option[$i]->caption . " " . $this->option[$i]->unit . " <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");
						}else{
							$dsp->AddCheckBoxRow("option[{$this->id}]","<b>" . $this->caption . "</b>",$this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'],"",null,$this->option[$i]->fix,$this->option[$i]->fix);
						}
					}else{
						if($this->choise == 0){
							$dsp->AddDoubleRow("",$this->option[$i]->caption . " " . $this->option[$i]->unit . "   <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");
						}else{
							$dsp->AddCheckBoxRow("option[{$this->option[$i]->id}]","",$this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'],"",null,$this->option[$i]->fix,$this->option[$i]->fix);
						}


					}
					$i++;
				}
				if($this->choise == 1){
					$dsp->AddFormSubmitRow("order");
				}
				break;

			}
			if($auth['type'] > 1) $dsp->AddDoubleRow("",$dsp->FetchButton("?mod=foodcenter&amp;action=addproduct&amp;id=". $this->id,"edit"));
			$dsp->AddBackButton($worklink);	
	}

	
	/**
	 * Produkt mit anderem Produkt vergleichen 
	 *
	 * @param product_object $prod
	 * @return boolean
	 */
	function compare($prod){
		if($this->type == 2){
			for ($i = 0;$i < count($prod->option);$i++){
				if($this->option[$i]->ordered != $prod->option[$i]->ordered){
					return false;
				}	
			}
		}else{
			if($this->id != $prod->id){
				return false;
			}
		}
	
		return true;
	}
	
	
	/**
	 * Bestellte Produkte ändern 
	 *
	 * @param int $val
	 * @return boolean
	 */
	function set_ordered($val){
		global $lang;
		$error = -1;
		foreach ($this->option as $key => $value){
			if(($val * $this->option[$key]->ordered) <  $this->option[$key]->pice){
				if($error == -1 || $error > $this->option[$key]->pice){
					$error = $this->option[$key]->pice;
				}
			}
		}
		if($error = -1){
			$this->error_food['order_error'] = $lang['foodcenter']['add_product_err_pice_count'];
			$this->ordered = $error;	
			return false;
		}else{
			$this->ordered = $val;
		}
		return true;
	}
	
	/**
	 * Produkt bestellen gibt den Preis für das hinzugefügte Produkt zurück
	 *
	 * @param int $userid
	 * @param int $delivered
	 * @return int
	 */
	function order($userid,$delivered){
		global $db,$config, $party;
		$time = time();
		$price = 0;
		if($this->type == 2){
			foreach ($this->option as $key => $value){
				if($this->option[$key]->ordered > 0 || $this->option[$key]->fix == 1){
					$opt_array[] .= $this->option[$key]->id;
					$price += $this->option[$key]->price;
					if($this->mat == 1){
					$tmp_rest1 = $this->option[$key]->pice - $this->option[$key]->ordered;
						$db->query("UPDATE {$config['tables']['food_option']} SET pice = '$tmp_rest1' WHERE id = {$this->option[$key]->id}");
					}
				}
			}
			// Status setzen
			if($this->wait == 1)
				$status = 2 ;
			else 
				$status = 1;
				
			//if($delivered == 1 || $delivered == 2 && $this->wait == 1) $status = 4;
			$opt_string = implode("/",$opt_array);
			if($db->query("INSERT INTO {$config['tables']['food_ordering']} SET 
					userid = '$userid',
					productid = '{$this->id}',
					partyid = '{$party->party_id}',
					opts = '$opt_string',
					pice = '{$this->ordered}',
					status = '$status',
					ordertime = '$time',
					lastchange = '$time',
					supplytime = '0'")){
				return $price * $this->ordered;
			}else{
				return 0;
			}
		}else{
			foreach ($this->option as $key => $value){
				if($this->option[$key]->ordered > 0 || $this->option[$key]->fix == 1){
					if($this->wait == 1) 
						$status = 2;
					else 
						$status = 1;
					//if($delivered == 1 || $delivered == 2 && $this->wait == 1) $status = 4;
					if($db->query("INSERT INTO {$config['tables']['food_ordering']} SET 
									userid = '$userid',
									productid = '{$this->id}',
									partyid = '{$party->party_id}',
									opts = '{$this->option[$key]->id}',
									pice = '{$this->option[$key]->ordered}',
									status = '$status',
									ordertime = '$time',
									lastchange = '$time',
									supplytime = '0'")){
						$price += $this->option[$key]->price * $this->option[$key]->ordered;
					} 
					if($this->mat == 1){
					$tmp_rest2 = $this->option[$key]->pice - $this->option[$key]->ordered;
						$db->query("UPDATE {$config['tables']['food_option']} SET pice = '$tmp_rest2' WHERE id = {$this->option[$key]->id}");
					}
				}	
			}
			return $price;
		}
		
	}

}
	



/**
 * Produktoptionen 
 *
 */
class product_option{

	/**
	 * Produktoptionsid
	 * @var int
	 */
	var $id;
	/**
	 * Id des Elternproduktes
	 * @var int
	 */
	var $parentid;
	/**
	 * Typ des Elternproduktes
	 * @var int
	 */
	var $parenttyp;
	/**
	 * Barcode
	 * @barcode string
	 */
	var $barcode;
	/**
	 * Produktoptionsname
	 * @var string
	 */
	var $caption;
	/**
	 * Einheit
	 *
	 * @var String
	 */
	var $unit;
	/**
	 * Anzahl der am Lager vorhanden Produkte
	 *
	 * @var int
	 */
	var $pice;
	/**
	 * Verkaufspreis
	 *
	 * @var int
	 */
	var $price;
	/**
	 * Einkaufspreis
	 *
	 * @var int
	 */
	var $eprice;
	/**
	 * Muss mitbestellt werden
	 *
	 * @var int
	 */
	var $fix		= 0;
	/**
	 * Anzahl bestellte Produkte
	 *
	 * @var int
	 */
	var $ordered	= 0;
	/**
	 * Fehlermeldungsarray
	 *
	 * @var array
	 */
	var $error 		= array();
	
	/**
	 * Konstruktor
	 *
	 * @param int $id
	 * @param int $type
	 * @return product_option
	 */
	function product_option($id = null,$type = null){
		$this->parenttyp = $type;
		if($id != null && $id > 0){
			$this->id = $id;
			$this->read();
		}
		
		
	}
	
	/**
	 * Produktoptionsinformationen aus dem Formular lesen
	 *
	 * @param int $parentid
	 * @param int $type
	 * @param int $nr
	 */
	function read_post($parentid,$type,$nr){
		if($_POST['hidden'][$nr] > 0){
			$this->id = $_POST['hidden'][$nr];
		}else{
			$this->id = null;
		}
		$this->parentid	= $parentid;
		$this->parenttyp = $type;
		$this->barcode  = $_POST['barcode'][$nr];
		$this->caption	= $_POST['caption'][$nr];
		$this->unit		= $_POST['unit'][$nr];
		$this->price	= $_POST['price'][$nr];
		$this->eprice	= $_POST['eprice'][$nr];
		$this->pice		= $_POST['piece'][$nr];
		$this->fix		= isset($_POST['fix'][$nr]) ? 1 : 0;
	}
	
	/**
	 * Produktoption aus der DB lesen
	 */
	function read(){
		global $db,$config;
		
		$row = $db->query_first("SELECT * FROM {$config['tables']['food_option']} WHERE id={$this->id}");

		$this->parentid	= $row['parentid'];
		$this->caption	= $row['caption'];
		$this->barcode	= $row['barcode'];		
		$this->unit		= $row['unit'];
		$this->price	= $row['price'];
		$this->eprice	= $row['eprice'];
		$this->pice		= $row['pice'];
		$this->fix		= $row['fix'];
	
	}
	
	/**
	 * Produktoption hinzufügen
	 * @param int $id
	 */
	function write($id = 0){
		global $db,$config;
		if($this->parentid == null) $this->parentid = $id;
		if($this->id == null){
			
			$db->query("INSERT INTO {$config['tables']['food_option']}  SET 
									parentid 	= '{$this->parentid}',
									barcode 	= '{$this->barcode}',
									caption		= '{$this->caption}',
									unit		= '{$this->unit}',
									price		= '{$this->price}',
									eprice		= '{$this->eprice}',
									fix			= '{$this->fix}',
									pice		= '{$this->pice}'");
			$this->id = $db->insert_id();
		}else{
			$db->query("UPDATE {$config['tables']['food_option']}  SET 
									parentid 	= '{$this->parentid}',
									barcode 	= '{$this->barcode}',
									caption		= '{$this->caption}',
									unit		= '{$this->unit}',
									price		= '{$this->price}',
									eprice		= '{$this->eprice}',
									pice		= '{$this->pice}',
									fix			= '{$this->fix}'
									WHERE id = {$this->id}");
		}			

	}
	
	/**
	 * Eingabedaten prüfen
	 * @return boolean
	 */
	function check(){
		global $lang;
		if($this->caption == "" && $this->parenttyp == 2) $this->error['caption'] = $lang['foodcenter']['add_product_err_opt_cap'];
		
		if($this->unit == ""){
			$this->error['price'] .= $lang['foodcenter']['add_product_err_unit'];
		}
		if(!is_numeric($this->price) || $this->price == ""){
			if($this->error['price'] != "") $this->error['price'] .= HTML_NEWLINE;
						$this->error['price'] .= $lang['foodcenter']['add_product_err_price'];
		}
		if(count($this->error) > 0){
			return false;
		}else{
			return true;
		}
	
	}
	
	/**
	 * Produkte zählen
	 * @return unknown
	 */
	function count_unit(){
		return $this->ordered;	
	}
	
	/**
	 * Preiszusammenzählen
	 * @return int
	 */
	function count_price(){
		if($this->fix){
			return $this->fix * $this->price;
		}else {
			return $this->ordered * $this->price;			
		}	
	}
	
	/**
	 * Formular für Dateneingabe anzeigen
	 *
	 * @param int $nr
	 * @param int $optional
	 * @param boolean $big
	 * @param boolean $multiselect
	 */
	function option_form($nr,$optional = null, $big = false, $multiselect = false){
		global $dsp,$templ,$lang;
		($multiselect) ? $display = "" : $display = "none";
		if($big == true){
			// display HTML for option 3
			$templ['ls']['row']['hidden_row']['id'] = "opt_big_$nr";
			$templ['ls']['row']['hidden_row']['display'] = $display;
			$dsp->AddModTpl("foodcenter","hiddenbox_start");
			$dsp->AddCheckBoxRow("fix[$nr]",$lang['foodcenter']['add_product_prod_fix'],$lang['foodcenter']['add_product_prod_fix_quest'],"",$optional,$this->fix);
			$dsp->AddModTpl("foodcenter","hiddenbox_stop");
			$dsp->AddTextFieldRow("caption[$nr]",$lang['foodcenter']['add_product_prod_opt_capt'],$this->caption,$this->error['caption'],null,$optional);
		}
		$this->_Add_Option_Row($lang['foodcenter']['add_product_option_text'],$lang['foodcenter']['add_product_option_unit'],$lang['foodcenter']['add_product_option_pricetext'],$lang['foodcenter']['add_product_option_epricetext'],$lang['foodcenter']['add_product_option_piecetext'],$lang['foodcenter']['add_product_option_barcodetext'],"unit[$nr]","price[$nr]","eprice[$nr]","piece[$nr]","barcode[$nr]",$this->unit,$this->price,$this->eprice,$this->pice,$this->barcode,"hidden[$nr]",$this->id,$this->error['price'],$optional);
		$dsp->AddHRuleRow();
		
	}
	
	/**
	 * Warenkorbinhalt anzeigen
	 *
	 * @param int $listid
	 * @param string $caption
	 * @param boolean $checkbox
	 */
	function get_basket($listid,$caption,$checkbox = false){
		global $dsp,$cfg;
		if($this->caption == "" && $checkbox == false){
			$text = $caption . " / " . $this->unit . " / " . $this->price . " " . $cfg['sys_currency'];
		}elseif($caption == "" || $checkbox == true) {
			$text = $this->caption . " / " . $this->unit . " / " . $this->price . " " . $cfg['sys_currency'];
		}else{
			$text = $caption . " " .$this->caption . " / " . $this->unit . " / " . $this->price . " " . $cfg['sys_currency'];
		}
		if($checkbox == false){
			$dsp->AddTextFieldRow("option_{$listid}_{$this->id}",$text,$this->ordered,$this->error['pice_error']);	
			$this->error['pice_error'] = "";
		}else{
			$dsp->AddCheckBoxRow("product[{$this->parentid}][{$this->id}]","",$text,"",null,1,1);
		}
	}
	
	
	/**
	 * Produktoptionstemplate
	 *
	 * @param string $text
	 * @param string $text_product
	 * @param string $text_price
	 * @param string $text_eprice
	 * @param string $text_piece
	 * @param string $text_barcode
	 * @param string $name_product
	 * @param string $name_price
	 * @param string $name_eprice
	 * @param string $name_piece
	 * @param string $name_barcode
	 * @param int $value_product
	 * @param int $value_price
	 * @param int $value_eprice
	 * @param int $value_piece
	 * @param int $value_barcode
	 * @param string $hidden_name
	 * @param int $hidden_id
	 * @param string $errortext
	 * @param string $optional
	 * @return template
	 */
	function _Add_Option_Row($text,$text_product,$text_price,$text_eprice,$text_piece,$text_barcode,$name_product,$name_price,$name_eprice,$name_piece,$name_barcode,$value_product,$value_price,$value_eprice,$value_piece,$value_barcode,$hidden_name,$hidden_id,$errortext,$optional = false) {
		global $dsp,$templ;
				
		$templ['foodcenter']['productcontrol']['pricerow']['text_row'] = $text;
		$templ['foodcenter']['productcontrol']['pricerow']['text_product'] = $text_product;
		$templ['foodcenter']['productcontrol']['pricerow']['name_product'] = $name_product;
		$templ['foodcenter']['productcontrol']['pricerow']['value_name'] = $value_product;
		$templ['foodcenter']['productcontrol']['pricerow']['text_price'] = $text_price;
		$templ['foodcenter']['productcontrol']['pricerow']['name_price'] = $name_price;
		$templ['foodcenter']['productcontrol']['pricerow']['value_price'] = $value_price;
		$templ['foodcenter']['productcontrol']['pricerow']['text_eprice'] = $text_eprice;
		$templ['foodcenter']['productcontrol']['pricerow']['name_eprice'] = $name_eprice;
		$templ['foodcenter']['productcontrol']['pricerow']['value_eprice'] = $value_eprice;
		$templ['foodcenter']['productcontrol']['pricerow']['text_piece'] = $text_piece;
		$templ['foodcenter']['productcontrol']['pricerow']['name_piece'] = $name_piece;
		$templ['foodcenter']['productcontrol']['pricerow']['value_piece'] = $value_piece;
		$templ['foodcenter']['productcontrol']['pricerow']['text_barcode'] = $text_barcode;
		$templ['foodcenter']['productcontrol']['pricerow']['name_barcode'] = $name_barcode;
		$templ['foodcenter']['productcontrol']['pricerow']['value_barcode'] = $value_barcode;
		$templ['foodcenter']['productcontrol']['pricerow']['hidden_name'] = $hidden_name;
		$templ['foodcenter']['productcontrol']['pricerow']['hidden_id']	= $hidden_id;
		
        if($errortext) $templ['foodcenter']['productcontrol']['pricerow']['errortext'] = $errortext;
        if(!$errortext) $templ['foodcenter']['productcontrol']['pricerow']['errortext'] = "";
		if($optional) $templ['foodcenter']['productcontrol']['pricerow']['optional'] = "_optional";
		if(!$optional) $templ['foodcenter']['productcontrol']['pricerow']['optional'] = "";

		return $dsp->AddDoubleRow($templ['foodcenter']['productcontrol']['pricerow']['text_row'], $dsp->FetchModTpl('foodcenter', 'productcontrol_price_row'));
    #$dsp->AddModTpl("foodcenter","productcontrol_price_row");
	}
	
}


/**
 * Klasse für die Verwaltung der Lieferanten
 *
 */
class supp{
	/**
	 * ID des Lieferanten
	 *
	 * @var int
	 */
	var $supp_id			= null;
	/**
	 * Beschreibung des Lieferanten
	 *
	 * @var string
	 */
	var $supp_desc;
	/**
	 * Name des Lieferanten
	 *
	 * @var string
	 */
	var $supp_caption;
	/**
	 * Array mit erzeugten Fehlern
	 *
	 * @var array
	 */ 
	var $error = array();

	
	/**
	 * Konsturktor
	 * List falls vorhanden den Lieferanten gleich in die Klasse
	 *
	 * @param int $id
	 * @return supp
	 */
	function supp($id = NULL){
		if($id != null && $id > 0){
			$this->supp_id = $id;
			$this->read();	
		}
	}
	
	/**
	 * Gib ein Array mit den Lieferanten zurück
	 *
	 * @param int $select_id
	 * @param boolean $new
	 * @return array
	 */
	function get_supp_array($select_id, $new = null){
		global $db,$config,$lang;
		
		$row = $db->query("SELECT * FROM {$config['tables']['food_supp']}");		

		if($db->num_rows($row) > 0){
			$tmp = array();
		
			if($new != null){
				($select_id == 0) ? $selected = "selected" : $selected = "";
				array_push($tmp,"<option $selected value='0'>{$lang['foodcenter']['add_product_new_supp']}</option>");	
			}
			
			while ($data = $db->fetch_array($row)){
				($select_id == $data['supp_id']) ? $selected = "selected" : $selected = "";
				array_push($tmp,"<option $selected value='{$data['supp_id']}'>{$data['name']}</option>");	
			}
			return $tmp;
		}else return false;
	}

	
	/**
	 * Lese Daten von der Globalen Variable POST
	 *
	 */
	function read_post(){
		if(isset($_POST['supp_id']) && $_POST['supp_id'] > 0){
			$this->supp_id = $_POST['supp_id'];
		}else{
			$this->supp_id = null;
		}
		if($_POST['supp_id'] == 0){
			$this->supp_caption = $_POST['supp_name'];
			$this->supp_desc = $_POST["supp_desc"];
		}
		
	}
	
	/**
	 * Lese Lieferant aus der DB
	 *
	 * @return boolean
	 */
	function read(){
		global $db,$config;
		if($this->supp_id != null){	
			$row = $db->query_first("SELECT * FROM {$config['tables']['food_supp']} WHERE supp_id={$this->supp_id}");	
			if($db->num_rows($row) > 0){
				$this->supp_caption	= $row['name'];
				$this->supp_desc 	= $row['s_desc'];
				return true;
			}else {
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Schreibe Lieferant in die Datenbank
	 *
	 */
	function write(){
		global $db,$config;

		if($this->supp_id == NULL){
			$db->query("INSERT INTO {$config['tables']['food_supp']} SET 
							name = '{$this->supp_caption}',
							s_desc = '{$this->supp_desc}'");
			$this->supp_id = $db->insert_id();
		}else{
			$db->query("UPDADE {$config['tables']['food_supp']} SET 
							name = '{$this->supp_caption}',
							s_desc = '{$this->supp_desc}'
							WHERE supp_id = {$this->supp_id}");
		}
	}
	
	/**
	 * Klasse prüfen nach eingaben
	 *
	 * @return boolean
	 */
	function check(){
		global $lang;
		if($this->supp_caption == "" && $this->supp_id == null){
			$this->error['supp_name']	= $lang['foodcenter']['add_product_err_supp'];
			return false;
		}
		return true;
	}
	
	/**
	 * Erzeuge ein Formular für das anlegen von Lieferanten
	 *
	 */
	function supp_form(){
		global $dsp,$lang;
		// Get Supplier
		$supp_array = $this->get_supp_array($this->supp_id,1);
		if($supp_array){
			$dsp->AddDropDownFieldRow("supp_id",$lang['foodcenter']['add_product_prod_supp'],$supp_array,"");
		}
		$dsp->AddTextFieldRow("supp_name",$lang['foodcenter']['add_product_prod_supp_new'],$_POST['supp_name'],$this->error['supp_name']);	}
	
}











/**
 * Kategorien verwalten
 * Werden für Menu der Speisekarte verwendet.
 * Diese sind als Headermenu verfügbar
 */
class cat{
	/**
	 * ID der Kategorie
	 *
	 * @var int
	 */
	var $cat_id = null;
	/**
	 * Name der Kategorie
	 *
	 * @var string
	 */
	var $name = "";

	/**
	 * Error Array
	 * Fehler bei der Eingaben ausgeben
	 *
	 * @var array
	 */
	var $error = array();
	
	/**
	 * Constructor
	 *
	 * @param int $id
	 * @return cat
	 */
	function cat($id = NULL){
		global $db,$config,$lang;
		if($id != null && $id > 0){
			$this->cat_id = $id;
			$this->read();
		}
	}
	
	
	/**
	 * Lese daten der Kategorie aus der DB
	 *
	 * @return boolean
	 */
	function read(){
		global $db,$config;
		if($this->cat_id != null){	
			$row = $db->query_first("SELECT * FROM {$config['tables']['food_cat']} WHERE cat_id={$this->cat_id}");	
			if($db->num_rows($row) > 0){
				$this->name = $row['name'];
				return true;
			}else {
				return false;
			}
		}else{
			return false;
		}
	}
	
	
	/**
	 * Gibt ein Array mit allen Kategorieen zurück
	 *
	 * @param int $select_id
	 * @param boolean $new
	 * @return boolean
	 */
	function get_cat_array($select_id,$new = null){
		global $db,$config,$lang;
		
		$row = $db->query("SELECT * FROM {$config['tables']['food_cat']}");		

		if($db->num_rows($row) > 0){
			$tmp = array();
		
			if($new != null){
				($select_id == 0) ? $selected = "selected" : $selected = "";
				array_push($tmp,"<option $selected value='0'>{$lang['foodcenter']['add_product_new_cat']}</option>");	
			}
			
			while ($data = $db->fetch_array($row)){
				($select_id == $data['cat_id']) ? $selected = "selected" : $selected = "";
				array_push($tmp,"<option $selected value='{$data['cat_id']}'>{$data['name']}</option>");	
			}
			return $tmp;
		}else return false;
		
	}
	
	/**
	 * Liest die Daten von dem Globalen Register POST in die Klasse
	 *
	 */
	function read_post(){
		if(isset($_POST['cat_id']) && $_POST['cat_id'] > 0){
			$this->cat_id = $_POST['cat_id'];
		}else{
			$this->cat_id = null;
		}
		if($_POST['cat_id'] == 0){
			$this->name = $_POST['cat_name'];
		}
		
	}
	
	/**
	 * Schreibe die Klasse in die Datenbank
	 *
	 */
	function write(){
		global $db,$config;
		if($this->cat_id == NULL){
			$db->query("INSERT INTO {$config['tables']['food_cat']} SET name = '{$this->name}'");
			$this->cat_id = $db->insert_id();
		}else{
			$db->query("UPDATE {$config['tables']['food_cat']} SET name = '{$this->name}' WHERE cat_id='{$this->cat_id}");
		}

	}

	/**
	 * Eingaben prüfen
	 *
	 * @return boolean
	 */
	function check(){
		global $lang;
		if($this->name == "" && $this->cat_id == null){
			$this->error['cat_name'] = $lang['foodcenter']['add_product_err_cat'];
			return false;
		}else {
			return true;
		}
	}
	
	/**
	 * Erzeuge ein Textfeld für die Kategorie
	 *
	 */
	function cat_form(){
		global $dsp,$lang;
		// Check for existing categories
		$cat_array = $this->get_cat_array($this->cat_id,1);
		if($cat_array){
			$dsp->AddDropDownFieldRow("cat_id",$lang['foodcenter']['add_product_prod_cat'],$cat_array,"");
		}
		$dsp->AddTextFieldRow("cat_name",$lang['foodcenter']['add_product_prod_cat_new'],$_POST['cat_name'],$this->error['cat_name']);
	}
	
}
?>