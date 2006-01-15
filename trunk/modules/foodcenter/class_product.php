<?php

class product_list{
	var $product_list	= array();
	var $product		= array();

	
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
	
	function get_info($id,$worklink){
		global $dsp,$lang,$cfg,$db,$config;
		
		$data_array = array_flip($this->product_list);
		$this->product[$data_array[$id]]->get_info($worklink);
		
	}
	
	function add_product($id,$opt){
		// Produkt schon vorhanden?
		if(in_array($id,$this->product_list)){
		
			// Wenn das Produkt ein 
			if(is_array($opt)){
				// Produkt f�r den Vergleich erzeugen
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
	
				// Produkt hinzuf�gen
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
	
			// Produkt hinzuf�gen
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
	
	function chanche_ordered($listid,$opt,$value){
		if(!is_null($opt)){
			//print_r($this->product[$listid]);
			return $this->product[$listid]->order_option($opt,$value);
		}else{
			return $this->product[$listid]->set_ordered($value);
			// $this->product[$listid]->ordered = $value;	
		}
	}
	
	function check_list(){
		foreach ($this->product_list as $key => $value){
			if($this->product[$key]->count_unit() == 0){
				unset($this->product[$key]);
				unset($this->product_list[$key]);
			}
		}
	}
	
	function get_basket_form(){
		foreach ($this->product_list as $key => $value){
			$this->product[$key]->get_basket($key);	
		}
	}
	
	function count_products(){
		foreach ($this->product_list as $key => $value){
			$count += $this->product[$key]->count_unit();	
		}
		return $count;
	}
	
	function count_products_price(){
		foreach ($this->product_list as $key => $value){
			$price += $this->product[$key]->count_price();	
		}
		return $price;
	}
	
	function order_product($userid,$delivered){
		$price = 0;
		foreach ($this->product_list as $key => $value){
			$price += $this->product[$key]->order($userid,$delivered);
		}
		return $price;
	}
}


class product{
	var $id			= null;
	var $caption 	= "";
	var $desc 		= "";
	var $cat;
	var $supp;
	var $pic		= "";
	var $mat		= "";
	var $type		= null;
	var $choise		= 0;
	var $wait		= 0;
	var $ordered	= 0;
	var $option 	= array();
	var $error_food	= array();
	var $noerror	= true;
			
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
	
	function read_post(){
		$this->caption 	=	$_POST['p_caption'];
		$this->desc		=	$_POST['desc'];
		$this->cat		=	new cat($_POST['cat_id']);
		$this->supp		=	new supp($_POST['supp_id']);
		$this->mat		=	$_POST['mat'];
		$this->type		=	$_POST['product_type'];
		$this->choise	=	$_POST['chois'];
		(isset($_POST['wait'])) ? $this->wait = 2 : $this->wait = 1 ;
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
			$this->pic = $func->FileUpload("file","ext_inc/foodcenter/",$_FILES['file']['name']);
			$_POST['pic'] = $this->pic;
			
		}

		if($this->cat->check() == false) $this->noerror = false;
		if($this->supp->check() == false) $this->noerror = false;
		for($i=0;$i < count($this->option);$i++){
			if($this->option[$i]->check() == false) $this->noerror = false;
		}
	
		return $this->noerror;

	}
	
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
		
		// Picecontrol ?
		$dsp->AddCheckBoxRow("mat",$lang['foodcenter']['add_product_prod_mat_text'],$lang['foodcenter']['add_product_prod_mat_quest'],"",NULL,$_POST['mat']);
		// Orderproduct ?
		$dsp->AddCheckBoxRow("wait",$lang['foodcenter']['add_product_prod_order'],$lang['foodcenter']['add_product_prod_order_text'],"",NULL,$_POST['wait']);

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
	
	
	function order_form($worklink){
		global $dsp,$cfg,$templ,$auth;
		
		switch ($this->type){
			case 1:
				unset($templ['foodcenter']['product']['pricerow']['name']);
				unset($templ['foodcenter']['product']['pricerow']["price_1"]);
				unset($templ['foodcenter']['product']['pricerow']["price_2"]);
				unset($templ['foodcenter']['product']['pricerow']["price_3"]);
				
				$templ['foodcenter']['product']['pricerow']['name'] = "<a href='$worklink&info={$this->id}'>" . $this->caption . "</a>";
				if(is_object($this->option[0])){
					$templ['foodcenter']['product']['pricerow']["price_3"] = "<b>" . $this->option[0]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'>" . $this->option[0]->price . " " . $cfg['sys_currency'] . "</a>";
					$templ['foodcenter']['product']['pricerow']["price_3"] .= "<a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'><img src=\"design/{$auth["design"]}/images/basket.gif\" border=\"0\" alt=\"basket\" /></a>";
				}
				if(is_object($this->option[1])){
					$templ['foodcenter']['product']['pricerow']["price_2"] = "<b>" . $this->option[1]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'>" . $this->option[1]->price . " " . $cfg['sys_currency'] . "</a>";
					$templ['foodcenter']['product']['pricerow']["price_2"] .= "<a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'><img src=\"design/{$auth["design"]}/images/basket.gif\" border=\"0\" alt=\"basket\" /></a>";
				}
				if(is_object($this->option[2])){
					$templ['foodcenter']['product']['pricerow']["price_1"] = "<b>" . $this->option[2]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[2]->price . " " . $cfg['sys_currency'] . "</a>";
					$templ['foodcenter']['product']['pricerow']["price_1"] .= "<a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'><img src=\"design/{$auth["design"]}/images/basket.gif\" border=\"0\" alt=\"basket\" /></a>";
				}
				$dsp->AddModTpl("foodcenter","product_price_row");
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

	function get_info($worklink){
		global $dsp,$lang,$auth,$cfg;
				
			$dsp->NewContent($lang['foodcenter']['product_desc']);
			$dsp->AddDoubleRow($lang['foodcenter']['add_product_prod_cap'],"<b>" . $this->caption . "</b>");
			if($this->desc != "") $dsp->AddDoubleRow($lang['foodcenter']['add_product_prod_desc'],$this->desc);
			if($this->pic != "" && file_exists($this->pic)) $dsp->AddDoubleRow("","<img src=\"{$this->pic}\" border=\"0\" alt=\"{$this->caption}\" />");
			$dsp->AddSingleRow($lang['foodcenter']['product_choise']);
			
			switch ($this->type){

				case 1:
				
				if(is_object($this->option[0])){
					$dsp->AddDoubleRow("","<b>" . $this->option[0]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[0]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'><img src=\"design/{$auth["design"]}/images/basket.gif\" border=\"0\" alt=\"basket\" /></a>");
				}
				if(is_object($this->option[1])){
					$dsp->AddDoubleRow("","<b>" . $this->option[1]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[1]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'><img src=\"design/{$auth["design"]}/images/basket.gif\" border=\"0\" alt=\"basket\" /></a>");
				}
				if(is_object($this->option[2])){
					$dsp->AddDoubleRow("","<b>" . $this->option[2]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[2]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'><img src=\"design/{$auth["design"]}/images/basket.gif\" border=\"0\" alt=\"basket\" /></a>");
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
	
	
	function set_ordered($val){
		global $lang;
		$error = -1;
		foreach ($this->option as $key => $value){
			if(($val * $this->option[$key]->ordered) <  $this->option[$key]->piece){
				if($error == -1 || $error > $this->option[$key]->piece){
					$error = $this->option[$key]->piece;
				}
			}
		}
		if($error != -1){
			$this->error_food['order_error'] = $lang['foodcenter']['add_product_err_pice_count'];
			$this->ordered = $error;	
			return false;
		}else{
			$this->ordered = $val;
		}
		return true;
	}
	
	function order($userid,$delivered){
		global $db,$config;
		$time = time();
		$price = 0;
		if($this->type == 2){
			foreach ($this->option as $key => $value){
				if($this->option[$key]->ordered > 0 || $this->option[$key]->fix == 1){
					$opt_array[] .= $this->option[$key]->id;
					$price += $this->option[$key]->price;
					if($this->mat == 1){
						$db->query("UPDATE {$config['tables']['food_option']} SET pice = 'pice - {$this->option[$key]->ordered}' WHERE id = {$this->option[$key]->id}");
					}
				}
			}
			if($this->wait == 1) $status = 3 ;
			else $status = 1;
			if($delivered == 1 || $delivered == 2 && $this->wait == 1) $status = 4;
			$opt_string = implode("/",$opt_array);
			if($db->query("INSERT INTO {$config['tables']['food_ordering']} SET 
					userid = '$userid',
					productid = '{$this->id}',
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
					if($this->wait == 1) $status = 3 ;
					else $status = 1;
					if($delivered == 1 || $delivered == 2 && $this->wait == 1) $status = 4;
					if($db->query("INSERT INTO {$config['tables']['food_ordering']} SET 
									userid = '$userid',
									productid = '{$this->id}',
									opts = '{$this->option[$key]->id}',
									pice = '{$this->option[$key]->ordered}',
									status = '$status',
									ordertime = '$time',
									lastchange = '$time',
									supplytime = '0'")){
						$price += $this->option[$key]->price * $this->option[$key]->ordered;
					}
					if($this->mat == 1){
						$db->query("UPDATE {$config['tables']['food_option']} SET pice = 'pice - {$this->option[$key]->ordered}' WHERE id = {$this->option[$key]->id}");
					}
				}	
			}
			return $price;
		}
		
	}

}
	



class product_option{

	var $id;
	var $parentid;
	var $parenttyp;
	var $caption;
	var $unit;
	var $pice;
	var $price;
	var $eprice;
	var $fix		= 0;
	var $ordered	= 0;
	var $error 		= array();
	
	function product_option($id = null,$type = null){
		$this->parenttyp = $type;
		if($id != null && $id > 0){
			$this->id = $id;
			$this->read();
		}
		
		
	}
	
	function read_post($parentid,$type,$nr){
		if($_POST['hidden'][$nr] > 0){
			$this->id = $_POST['hidden'][$nr];
		}else{
			$this->id = null;
		}
		$this->parentid	= $parentid;
		$this->parenttyp = $type;
		$this->caption	= $_POST['caption'][$nr];
		$this->unit		= $_POST['unit'][$nr];
		$this->price	= $_POST['price'][$nr];
		$this->eprice	= $_POST['eprice'][$nr];
		$this->pice		= $_POST['pice'][$nr];
		$this->fix		= isset($_POST['fix'][$nr]) ? 1 : 0;
	}
	
	function read(){
		global $db,$config;
		
		$row = $db->query_first("SELECT * FROM {$config['tables']['food_option']} WHERE id={$this->id}");

		$this->parentid	= $row['parentid'];
		$this->caption	= $row['caption'];
		$this->unit		= $row['unit'];
		$this->price	= $row['price'];
		$this->eprice	= $row['eprice'];
		$this->pice		= $row['pice'];
		$this->fix		= $row['fix'];
	
	}
	
	function write($id = 0){
		global $db,$config;
		if($this->parentid == null) $this->parentid = $id;
		if($this->id == null){
			
			$db->query("INSERT INTO {$config['tables']['food_option']}  SET 
									parentid 	= '{$this->parentid}',
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
									caption		= '{$this->caption}',
									unit		= '{$this->unit}',
									price		= '{$this->price}',
									eprice		= '{$this->eprice}',
									pice		= '{$this->pice}',
									fix			= '{$this->fix}'
									WHERE id = {$this->id}");
		}			

	}
	
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
	
	function count_unit(){
		return $this->ordered;	
	}
	
	function count_price(){
		if($this->fix){
			return $this->fix * $this->price;
		}else {
			return $this->ordered * $this->price;			
		}	
	}
	
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
		$this->_Add_Option_Row($lang['foodcenter']['add_product_option_text'],$lang['foodcenter']['add_product_option_unit'],$lang['foodcenter']['add_product_option_pricetext'],$lang['foodcenter']['add_product_option_epricetext'],$lang['foodcenter']['add_product_option_piecetext'],"unit[$nr]","price[$nr]","eprice[$nr]","piece[$nr]",$this->unit,$this->price,$this->eprice,$this->pice,"hidden[$nr]",$this->id,$this->error['price'],$optional);
		$dsp->AddHRuleRow();
		
	}
	
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
	
	
	function _Add_Option_Row($text,$text_product,$text_price,$text_eprice,$text_piece,$name_product,$name_price,$name_eprice,$name_piece,$value_product,$value_price,$value_eprice,$value_piece,$hidden_name,$hidden_id,$errortext,$optional = false) {
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
		$templ['foodcenter']['productcontrol']['pricerow']['hidden_name'] = $hidden_name;
		$templ['foodcenter']['productcontrol']['pricerow']['hidden_id']	= $hidden_id;
		
        if($errortext) $templ['foodcenter']['productcontrol']['pricerow']['errortext'] = $errortext;
        if(!$errortext) $templ['foodcenter']['productcontrol']['pricerow']['errortext'] = "";
		if($optional) $templ['foodcenter']['productcontrol']['pricerow']['optional'] = "_optional";
		if(!$optional) $templ['foodcenter']['productcontrol']['pricerow']['optional'] = "";

		return $dsp->AddModTpl("foodcenter","productcontrol_price_row");
	}
	
}


/**
 * Klasse f�r die Verwaltung der Lieferanten
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
	 * Gib ein Array mit den Lieferanten zur�ck
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
	 * Klasse pr�fen nach eingaben
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
	 * Erzeuge ein Formular f�r das anlegen von Lieferanten
	 *
	 */
	function supp_form(){
		global $dsp,$lang;
		// Get Supplier
		$supp_array = $this->get_supp_array($this->supp_id,1);
		if($supp_array){
			$dsp->AddDropDownFieldRow("supp_id",$lang['foodcenter']['add_product_prod_supp'],$supp_array,"");
		}
		$dsp->AddTextFieldRow("supp_name",$lang['foodcenter']['add_product_prod_supp_new'],$_POST['supp_name'],$this->error['supp_name']);
		$dsp->AddTextFieldRow("supp_desc",$lang['foodcenter']['add_product_prod_supp_desc'],$_POST['supp_desc'],"",null,true);
	}
	
}











/**
 * Kategorien verwalten
 * Werden f�r Menu der Speisekarte verwendet.
 *
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
	 * Gibt ein Array mit allen Kategorieen zur�ck
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
	 * Eingaben pr�fen
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
	 * Erzeuge ein Textfeld f�r die Kategorie
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