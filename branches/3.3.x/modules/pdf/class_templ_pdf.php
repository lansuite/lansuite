<?php


class pdf_tmpl{
	
	var $action;
	var $tmpl_id;
	
	// Konstruktor
	function pdf_tmpl($action,$tmpl_id){
		$this->action = $action;
		$this->tmpl_id = $tmpl_id;
	}
	
	// Alle Vorlagen zu bestimmtem Thema auslesen
	function read_List(){
		global $config,$db,$dsp,$lang,$templ;	
		
		$data = $db->query("SELECT * FROM " .  $config['tables']['pdf_list'] . " WHERE template_type = '" . $this->action . "'");
		
		$dsp->NewContent($lang["pdf"]["guestcards_caption"], $lang["pdf"]["guestcards_subcaption"]);
		// Liste mit möglichen Vorlagen ausgeben
		$out = "";
		if ($db->num_rows($data) > 0){
			while($data_array = $db->fetch_array($data)){
				
				
				$templ['pdf']['liste'] = "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=start&id=" . $data_array['template_id'] . "\">" . $data_array['name'] . "</a>";
				$templ['pdf']['change'] = "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=change&id=" . $data_array['template_id'] . "\">" . $lang["pdf"]["change_templ"] . "</a>";
				$templ['pdf']['delete'] = "<a href=\"index.php?mod=pdf&action=" . $this->action . "&delete=1&id=" . $data_array['template_id'] . "\">" . $lang["pdf"]["delete_templ"] . "</a>";				
				$out .= $dsp->FetchModTpl("pdf","liste");
			}
			$dsp->AddSingleRow($out);
		}else {
			$dsp->AddSingleRow($lang['pdf']['template_error']);
		}
		$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=" . $_GET['action'] . "&act=new\">{$lang["pdf"]["new_subcaption"]}</a>");
		$dsp->AddBackButton("index.php?mod=pdf","pdf/template");
		$dsp->AddContent();

		
		
	}
	
	// Daten einfügen
	function add_templ(){
		global $config,$db,$dsp,$lang,$templ;	
		// In Liste einfügen
		$db->query("INSERT INTO " . $config['tables']['pdf_list'] . " ( `template_id` , `template_type` , `name` ) VALUES ('','" . $this->action . "','" . $_POST['template_name'] . "')");
		$this->tmpl_id = $db->insert_id();
		
		// Config anlegen
		$db->query("INSERT INTO " . $config['tables']['pdf_data'] . "
		( `pdfid` , `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` ) VALUES 
			('','" . $this->tmpl_id . "','" . $_POST['landscape'] . "','config','" . $_POST['rand_x'] . "','" . $_POST['rand_y'] . "','0','0','0','','0','0','0','" . $_POST['pagesize'] . "','')");
	
	
	}
	
	// Daten auslesen
	function display_data(){
		global $config,$db,$dsp,$lang,$templ,$gd;
				  
		// Name ausgeben
		$template = $db->query_first("SELECT * FROM " . $config['tables']['pdf_list'] . " WHERE template_id='" . $this->tmpl_id . "'");
		$dsp->NewContent($lang["pdf"]["change_caption"],$lang["pdf"]["change_subcaption"]);
		$dsp->AddDoubleRow($lang["pdf"]["change_vorlage"],$template['name']);
		
		// Konfiguration ausgeben
		$template_config = $db->query_first("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->tmpl_id . "' AND type='config'");
		
		$dsp->AddDoubleRow($lang["pdf"]["rand_x"],$template_config['pos_x']);
		$dsp->AddDoubleRow($lang["pdf"]["rand_y"],$template_config['pos_y']);
		$dsp->AddDoubleRow($lang["pdf"]["pagesize"],$template_config['text']);

		// Daten ausgeben
		$data = $db->query("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->tmpl_id . "' AND type != 'config' ORDER BY sort ASC");
		
		$templ['pdf']['action'] = $this->action;
		
		$out = "";
		while ($data_array = $db->fetch_array($data)){
		
			$templ['pdf']['name'] = $lang['pdf'][$data_array['type']];
			$templ['pdf']['itemid'] = $data_array['pdfid'];
			$templ['pdf']['id'] = $this->tmpl_id;
			if($data_array['type'] == "rect"){
				$templ['pdf']['description'] = $lang['pdf']['pos_x'] . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= $lang['pdf']['pos_y'] . " : " . $data_array['pos_y']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['width'] . " : " . $data_array['end_x']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['high'] . " : " . $data_array['end_y']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['visible'] . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= $lang['pdf']['color'] . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
			}elseif ($data_array['type'] == "line"){
				$templ['pdf']['description'] = $lang['pdf']['pos_x'] . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= $lang['pdf']['pos_y'] . " : " . $data_array['pos_y']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['end_x'] . " : " . $data_array['end_x']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['end_y'] . " : " . $data_array['end_y']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['visible'] . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= $lang['pdf']['color'] . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
			}elseif ($data_array['type'] == "text" || $data_array['type'] == "data"){
				$templ['pdf']['description'] = $lang['pdf']['text'] . " : " . $data_array['text']. HTML_NEWLINE; 
				$templ['pdf']['description'] .= $lang['pdf']['pos_x'] . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= $lang['pdf']['pos_y'] . " : " . $data_array['pos_y']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['orient'] . " : " . $data_array['end_x']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['font'] . " : " . $data_array['font']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['fontsize'] . " : " . $data_array['fontsize']. " , ";
				$templ['pdf']['description'] .= $lang['pdf']['visible'] . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= $lang['pdf']['color'] . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
			}elseif ($data_array['type'] == "image"){
				$templ['pdf']['description'] = $lang['pdf']['pos_x'] . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= $lang['pdf']['pos_y'] . " : " . $data_array['pos_y']. " , "; ;
				$templ['pdf']['description'] .= $lang['pdf']['width'] . " : " . $data_array['end_x']. " , "; 
				$templ['pdf']['description'] .= $lang['pdf']['visible'] . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= $lang['pdf']['high'] . " : " . $data_array['end_y'];
			}elseif ($data_array['type'] == "barcode"){
				$templ['pdf']['description'] = $lang['pdf']['pos_x'] . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= $lang['pdf']['pos_y'] . " : " . $data_array['pos_y']. " , "; ;
				$templ['pdf']['description'] .= $lang['pdf']['visible'] . " : " . $data_array['visible'] . " , ";
			}
      $gd->CreateButton('edit');
      $gd->CreateButton('delete');
			$out .= $dsp->FetchModTpl("pdf","edit_liste");
		}
		$dsp->AddSingleRow($out);
		
		// Array erzeugen für mögliche Einträge
		$type = array("<option selected value=\"rect\">" . $lang['pdf']['rect'] . "</option>",
					  "<option value=\"text\">" . $lang['pdf']['text'] . "</option>",
					  "<option value=\"line\">" . $lang['pdf']['line'] . "</option>",
					  "<option value=\"image\">" . $lang['pdf']['image'] . "</option>",
					  "<option value=\"data\">" . $lang['pdf']['data'] . "</option>",
					  "<option value=\"barcode\">" . $lang['pdf']['barcode'] . "</option>");

		// Formular für hinzufügen von Einträgen
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=insert_mask&id=" . $this->tmpl_id);
		$dsp->AddDropDownFieldRow('type',$lang['pdf']['choise'] ,$type,"");
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action,"pdf/change_template");
		$dsp->AddContent();	
	}
	
	
	// Eintrag erstellungs Maske anzeigen
	// Es müss das Objekt das erstellt werden soll übergreben werden
	function insert_mask($object){
		global $config,$db,$dsp,$lang,$templ;
		
		$pdf_export = new pdf($this->tmpl_id);
							  
		// Benutzertypen erzeugen
		$user_type = array("<option selected value=\"0\">" . $lang['pdf']['all'] . "</option>",
					  "<option value=\"1\">" . $lang['pdf']['guest'] . "</option>",
					  "<option value=\"2\">" . $lang['pdf']['admin'] . "</option>",
					  "<option value=\"3\">" . $lang['pdf']['opera'] . "</option>");
					  
		// Maske ausgeben für entsprechenden eintrag
		$dsp->NewContent($lang["pdf"]["object_caption"],$lang["pdf"]["object_new_subcaption"]);	
		$dsp->AddSingleRow($lang['pdf']['new_item'] . $lang['pdf'][$object]);		  
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action ."&act=insert_item&object=$object&id=$this->tmpl_id");
			if($object == "rect"){
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],'','');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],'','');
				$dsp->AddTextFieldRow("end_x",$lang["pdf"]["width"],'','');		
				$dsp->AddTextFieldRow("end_y",$lang["pdf"]["high"],'','');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],'0','');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],'0','');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],'0','');
				$dsp->AddCheckBoxRow("fontsize",$lang["pdf"]["fill"],'','');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL','1');
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],'','');
				$help = "pdf/item_rect";
			}elseif ($object == "line"){
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],'','');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],'','');
				$dsp->AddTextFieldRow("end_x",$lang["pdf"]["end_x"],'','');		
				$dsp->AddTextFieldRow("end_y",$lang["pdf"]["end_y"],'','');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],'0','');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],'0','');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],'0','');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL','1');
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],'','');
				$help = "pdf/item_line";
			}elseif ($object == "text"){				
				$dsp->AddTextFieldRow("text",$lang["pdf"]["text"],'','');
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],'','');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],'','');
				$dsp->AddCheckBoxRow("end_x",$lang["pdf"]["orient"],'','');		
				$dsp->AddTextFieldRow("font",$lang["pdf"]["font"],'Arial','');		
				$dsp->AddTextFieldRow("fontsize",$lang["pdf"]["fontsize"],'12','');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],'0','');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],'0','');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],'0','');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL','1');
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],'','');
				$help = "pdf/item_text";
			}elseif ($object == "data"){	
				$dsp->AddDropDownFieldRow('text',$lang['pdf']['data'] ,$pdf_export->get_data_array($this->action),"");
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],'','');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],'','');
				$dsp->AddCheckBoxRow("end_x",$lang["pdf"]["orient"],'','');		
				$dsp->AddTextFieldRow("font",$lang["pdf"]["font"],'Arial','');		
				$dsp->AddTextFieldRow("fontsize",$lang["pdf"]["fontsize"],'12','');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],'0','');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],'0','');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],'0','');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL','1');
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],'','');				
				$help = "pdf/item_data";
			}elseif ($object == "image"){				
				$dsp->AddTextFieldRow("text",$lang["pdf"]["file"],'','');
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],'','');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],'','');
				$dsp->AddTextFieldRow("end_x",$lang["pdf"]["width"],'','');		
				$dsp->AddTextFieldRow("end_y",$lang["pdf"]["high"],'','');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL','1');
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],'','');
				$help = "pdf/item_img";
			}elseif ($object == "barcode"){				
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],'','');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],'','');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL','1');
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],'','');
				$help = "pdf/item_img";
			}
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action, $help);
		$dsp->AddContent();	
	}
	
	// Maske um Einträge ändern anzeigen
	function change_mask($item_id){
		global $config,$db,$dsp,$lang,$templ;
		$pdf_export = new pdf($this->tmpl_id);
		
		$data = $db->query_first("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE pdfid='" . $item_id . "'");
								  
		$user_type_list = array( "0" =>  $lang['pdf']['all'] ,"1" =>  $lang['pdf']['guest'] ,"2" =>  $lang['pdf']['admin'] ,"3" =>  $lang['pdf']['opera'] ); 
		
		// Liste für Datenfeld erzeugen
		foreach ($user_type_list as $key => $value){
			if($key == $data['user_type']){
				$user_type[$key] = "<option selected value=\"$key\">$value</option>";
			}else {
				$user_type[$key] = "<option value=\"$key\">$value</option>";
			}
		}
		
		// Liste für Benutzer
		foreach ($user_type_list as $key => $value){
			if($key == $data['user_type']){
				$user_type[$key] = "<option selected value=\"$key\">$value</option>";
			}else {
				$user_type[$key] = "<option value=\"$key\">$value</option>";
			}
		}
					 
		$object = $data['type']; 
		$dsp->NewContent($lang["pdf"]["object_caption"],$lang["pdf"]["object_change_subcaption"]);	
		$dsp->AddSingleRow($lang['pdf']['change_item'] . " " . $lang['pdf'][$object]);		  
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action ."&act=change_item&object=$object&id=$this->tmpl_id&itemid=$item_id");
			if($object == "rect"){
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],$data['pos_y'],'');
				$dsp->AddTextFieldRow("end_x",$lang["pdf"]["width"],$data['end_x'],'');		
				$dsp->AddTextFieldRow("end_y",$lang["pdf"]["high"],$data['end_y'],'');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],$data['red'],'');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],$data['green'],'');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],$data['blue'],'');
				$dsp->AddCheckBoxRow("fontsize",$lang["pdf"]["fill"],'','','NULL',$data['fontsize']);
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],$data['sort'],'');
				$help = "pdf/item_rect";
			}elseif ($object == "line"){
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],$data['pos_y'],'');
				$dsp->AddTextFieldRow("end_x",$lang["pdf"]["end_x"],$data['end_x'],'');		
				$dsp->AddTextFieldRow("end_y",$lang["pdf"]["end_y"],$data['end_y'],'');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],$data['red'],'');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],$data['green'],'');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],$data['blue'],'');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],$data['sort'],'');
				$help = "pdf/item_line";
			}elseif ($object == "text"){				
				$dsp->AddTextFieldRow("text",$lang["pdf"]["text"],$data['text'],'');
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],$data['pos_y'],'');
				$dsp->AddCheckBoxRow("end_x",$lang["pdf"]["orient"],'','','NULL',$data['end_x']);		
				$dsp->AddTextFieldRow("font",$lang["pdf"]["font"],$data['font'],'');		
				$dsp->AddTextFieldRow("fontsize",$lang["pdf"]["fontsize"],$data['fontsize'],'');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],$data['red'],'');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],$data['green'],'');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],$data['blue'],'');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],$data['sort'],'');
				$help = "pdf/item_text";
			}elseif ($object == "data"){	
				$dsp->AddDropDownFieldRow('text',$lang['pdf']['data'] ,$pdf_export->get_data_array($this->action,$data['text']),"");
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],$data['pos_y'],'');
				$dsp->AddCheckBoxRow("end_x",$lang["pdf"]["orient"],'','','NULL',$data['end_x']);
				$dsp->AddTextFieldRow("font",$lang["pdf"]["font"],$data['font'],'');		
				$dsp->AddTextFieldRow("fontsize",$lang["pdf"]["fontsize"],$data['fontsize'],'');
				$dsp->AddTextFieldRow("red",$lang["pdf"]["red"],$data['red'],'');
				$dsp->AddTextFieldRow("green",$lang["pdf"]["green"],$data['green'],'');		
				$dsp->AddTextFieldRow("blue",$lang["pdf"]["blue"],$data['blue'],'');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],$data['sort'],'');			
				$help = "pdf/item_data";
			}elseif ($object == "image"){				
				$dsp->AddTextFieldRow("text",$lang["pdf"]["file"],$data['text'],'');
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],$data['pos_y'],'');
				$dsp->AddTextFieldRow("end_x",$lang["pdf"]["width"],$data['end_x'],'');		
				$dsp->AddTextFieldRow("end_y",$lang["pdf"]["high"],$data['end_y'],'');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],$data['sort'],'');
				$help = "pdf/item_img";
			}elseif ($object == "barcode"){				
				$dsp->AddTextFieldRow("pos_x",$lang["pdf"]["pos_x"],$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",$lang["pdf"]["pos_y"],$data['pos_y'],'');
				$dsp->AddDropDownFieldRow('user_type',$lang['pdf']['user_type'] ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",$lang["pdf"]["visible"],'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",$lang["pdf"]["sort"],$data['sort'],'');
				$help = "pdf/item_img";
			}
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id,$help);
		$dsp->AddContent();	
	}

	
	// ein Objekt einfügen
	function insert_item($object){
		global $config,$db,$dsp,$lang,$templ,$func;
		
		
		if($_POST['visible'] == "checked"){
			$visible = 1;
		}else{
			$visible = 0;
		}
		
		if($db->query("INSERT INTO " . $config['tables']['pdf_data'] . "  ( `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` , `sort` ) 
		VALUES ('$this->tmpl_id' , '" . $_POST['visible'] . "' , '$object', '" . $_POST['pos_x'] . "', '" . $_POST['pos_y'] . "', '" . $_POST['end_x'] . "', '" . $_POST['end_y'] . "', '" . $_POST['fontsize'] . "', '" . $_POST['font'] . "', '" . $_POST['red'] . "', '" . $_POST['green'] . "', '" . $_POST['blue'] . "', '" . $_POST['text'] . "', '" . $_POST['user_type'] . "', '" . $_POST['sort'] . "')" )){
			$func->confirmation($lang["pdf"]["input_ok"],"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}else{
			$func->error($lang["pdf"]["input_error"],"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}
		
	}
	
	// Objekt ändern
	function change_item($item_id){
		global $config,$db,$dsp,$lang,$templ,$func;
		
		
		if($_POST['visible'] == "checked"){
			$visible = 1;
		}else{
			$visible = 0;
		}

	
		if($db->query("UPDATE " . $config['tables']['pdf_data'] . "  
			SET `visible`='" . $_POST['visible'] . 
		       "', `pos_x`='" . $_POST['pos_x'] .
		       "', `pos_y`='" . $_POST['pos_y'] .
		       "', `end_x`='" . $_POST['end_x'] . 
		       "', `end_y`='" . $_POST['end_y'] .
		       "', `fontsize`='" . $_POST['fontsize'] .
		       "', `font`='" . $_POST['font'] .
		       "', `red`='" . $_POST['red'] . 
		       "', `green`='" . $_POST['green'] . 
		       "', `blue`='" . $_POST['blue'] .
		       "', `text`='" . $_POST['text'] .
		       "', `user_type`='" . $_POST['user_type'] .
		       "', `sort`='" . $_POST['sort'] .
		       "' WHERE `template_id`='" . $this->tmpl_id . "' AND `pdfid`='" . $item_id . "'")){
    
			$func->confirmation($lang["pdf"]["input_ok"],"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}else{
			$func->error($lang["pdf"]["input_error"],"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}
		
	}
		
		// Sortierung ändern
	function sortorder($direction,$item_id){
		global $config,$db,$dsp,$lang,$templ,$func;	
		
		if($direction == "minus"){
			$sort = "-1";
		}else {
			$sort = "+1";
		}
		$db->query("UPDATE " .  $config['tables']['pdf_data'] . " SET sort=sort$sort WHERE pdfid = '" . $item_id . "'");
	
		
	}
	// Daten löschen
	function delete_templ(){
		global $config,$db,$dsp,$lang,$templ;	
		
		$db->query("DELETE FROM " .  $config['tables']['pdf_list'] . " WHERE template_id = '" . $this->tmpl_id . "'");
		$db->query("DELETE FROM " .  $config['tables']['pdf_data'] . " WHERE template_id = '" . $this->tmpl_id . "'");
	
		
	}
	
	function delete_item($itemid){
		global $config,$db,$dsp,$lang,$templ;	
		
		$db->query("DELETE FROM " .  $config['tables']['pdf_data'] . " WHERE pdfid = '" . $itemid . "'");
	
		
	}
	
	function new_templ_mask(){
		global $dsp,$lang;
		// Array für Seitengrössen		
		$page_size = array("<option selected value=\"A4\">A4</option>","<option value=\"A3\">A3</option>","<option value=\"A5\">A5</option>");
		
		// Formular für neues Template
		$dsp->NewContent($lang["pdf"]["new_caption"],$lang["pdf"]["new_subcaption"]);
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=add");
		$dsp->AddTextFieldRow("template_name", $lang["pdf"]["template_name"],'','');
		$dsp->AddDropDownFieldRow("pagesize", $lang["pdf"]["pagesize"],$page_size,'');
		$dsp->AddTextFieldRow("rand_x", $lang["pdf"]["rand_x"],'','');
		$dsp->AddTextFieldRow("rand_y", $lang["pdf"]["rand_y"],'','');
		$dsp->AddCheckBoxRow("landscape",$lang["pdf"]["landscape"],'','');
		$dsp->AddFormSubmitRow("add","pdf/new_template");
		$dsp->AddContent();
	}
	
}
