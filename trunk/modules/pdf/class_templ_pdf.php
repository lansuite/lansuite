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
		
		$dsp->NewContent(t('Besucherausweise erstellen'), t('Bitte eine Formatierungsform ausw&auml;hlen oder eine Neue erstellen'));
		// Liste mit m�glichen Vorlagen ausgeben
		$out = "";
		if ($db->num_rows($data) > 0){
			while($data_array = $db->fetch_array($data)){
				
				
				$templ['pdf']['liste'] = "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=start&id=" . $data_array['template_id'] . "\">" . $data_array['name'] . "</a>";
				$templ['pdf']['change'] = "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=change&id=" . $data_array['template_id'] . "\">" . t('Vorlage &auml;ndern') . "</a>";
				$templ['pdf']['delete'] = "<a href=\"index.php?mod=pdf&action=" . $this->action . "&delete=1&id=" . $data_array['template_id'] . "\">" . t('Vorlage l&ouml;schen') . "</a>";				
				$out .= $dsp->FetchModTpl("pdf","liste");
			}
			$dsp->AddSingleRow($out);
		}else {
			$dsp->AddSingleRow(t('Keine Vorlagen gefunden'));
		}
		$dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=" . $_GET['action'] . "&act=new\">".t('Neue Vorlage erstellen')."</a>");
		$dsp->AddBackButton("index.php?mod=pdf","pdf/template");
		$dsp->AddContent();

		
		
	}
	
	// Daten einf�gen
	function add_templ(){
		global $config,$db,$dsp,$lang,$templ;	
		// In Liste einf�gen
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
		$dsp->NewContent(t('Vorlagen'),t('Vorlage &auml;ndern'));
		$dsp->AddDoubleRow(t('Vorlagenname'),$template['name']);
		
		// Konfiguration ausgeben
		$template_config = $db->query_first("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->tmpl_id . "' AND type='config'");
		
		$dsp->AddDoubleRow(t('Rand in x-Richtung'),$template_config['pos_x']);
		$dsp->AddDoubleRow(t('Rand in y-Richtung'),$template_config['pos_y']);
		$dsp->AddDoubleRow(t('Seitengr&ouml;sse'),$template_config['text']);

		// Daten ausgeben
		$data = $db->query("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE template_id='" . $this->tmpl_id . "' AND type != 'config' ORDER BY sort ASC");
		
		$templ['pdf']['action'] = $this->action;
		
		$out = "";
		while ($data_array = $db->fetch_array($data)){
		
			$templ['pdf']['name'] = $lang['pdf'][$data_array['type']];
			$templ['pdf']['itemid'] = $data_array['pdfid'];
			$templ['pdf']['id'] = $this->tmpl_id;
			if($data_array['type'] == "rect"){
				$templ['pdf']['description'] = t('Xo') . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= t('Yo') . " : " . $data_array['pos_y']. " , ";
				$templ['pdf']['description'] .= t('Breite') . " : " . $data_array['end_x']. " , ";
				$templ['pdf']['description'] .= t('H&ouml;he') . " : " . $data_array['end_y']. " , ";
				$templ['pdf']['description'] .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
			}elseif ($data_array['type'] == "line"){
				$templ['pdf']['description'] = t('Xo') . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= t('Yo') . " : " . $data_array['pos_y']. " , ";
				$templ['pdf']['description'] .= t('X') . " : " . $data_array['end_x']. " , ";
				$templ['pdf']['description'] .= t('Y') . " : " . $data_array['end_y']. " , ";
				$templ['pdf']['description'] .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
			}elseif ($data_array['type'] == "text" || $data_array['type'] == "data"){
				$templ['pdf']['description'] = t('Text') . " : " . $data_array['text']. HTML_NEWLINE; 
				$templ['pdf']['description'] .= t('Xo') . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= t('Yo') . " : " . $data_array['pos_y']. " , ";
				$templ['pdf']['description'] .= t('Rechtsb&uuml;ndig') . " : " . $data_array['end_x']. " , ";
				$templ['pdf']['description'] .= t('Schriftart') . " : " . $data_array['font']. " , ";
				$templ['pdf']['description'] .= t('Schriftgr&ouml;sse') . " : " . $data_array['fontsize']. " , ";
				$templ['pdf']['description'] .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
			}elseif ($data_array['type'] == "image"){
				$templ['pdf']['description'] = t('Xo') . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= t('Yo') . " : " . $data_array['pos_y']. " , "; ;
				$templ['pdf']['description'] .= t('Breite') . " : " . $data_array['end_x']. " , "; 
				$templ['pdf']['description'] .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
				$templ['pdf']['description'] .= t('H&ouml;he') . " : " . $data_array['end_y'];
			}elseif ($data_array['type'] == "barcode"){
				$templ['pdf']['description'] = t('Xo') . " : " . $data_array['pos_x']. " , "; 
				$templ['pdf']['description'] .= t('Yo') . " : " . $data_array['pos_y']. " , "; ;
				$templ['pdf']['description'] .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
			}
      $gd->CreateButton('edit');
      $gd->CreateButton('delete');
			$out .= $dsp->FetchModTpl("pdf","edit_liste");
		}
		$dsp->AddSingleRow($out);
		
		// Array erzeugen f�r m�gliche Eintr�ge
		$type = array("<option selected value=\"rect\">" . t('Rechteck') . "</option>",
					  "<option value=\"text\">" . t('Text') . "</option>",
					  "<option value=\"line\">" . t('Linie') . "</option>",
					  "<option value=\"image\">" . t('Bild') . "</option>",
					  "<option value=\"data\">" . t('Daten') . "</option>",
					  "<option value=\"barcode\">" . t('Strichcode') . "</option>");

		// Formular f�r hinzuf�gen von Eintr�gen
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=insert_mask&id=" . $this->tmpl_id);
		$dsp->AddDropDownFieldRow('type',t('Wahl des Feldes') ,$type,"");
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action,"pdf/change_template");
		$dsp->AddContent();	
	}
	
	
	// Eintrag erstellungs Maske anzeigen
	// Es m�ss das Objekt das erstellt werden soll �bergreben werden
	function insert_mask($object){
		global $config,$db,$dsp,$lang,$templ;
		
		$pdf_export = new pdf($this->tmpl_id);
							  
		// Benutzertypen erzeugen
		$user_type = array("<option selected value=\"0\">" . t('Alle') . "</option>",
					  "<option value=\"1\">" . t('Besucher ist normaler Gast') . "</option>",
					  "<option value=\"2\">" . t('Administrator') . "</option>",
					  "<option value=\"3\">" . t('Superadmin') . "</option>");
					  
		// Maske ausgeben f�r entsprechenden eintrag
		$dsp->NewContent(t('Objekt'),t('Neues Objekt erstellen'));	
		$dsp->AddSingleRow(t('Erstelle ') . $lang['pdf'][$object]);		  
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action ."&act=insert_item&object=$object&id=$this->tmpl_id");
			if($object == "rect"){
				$dsp->AddTextFieldRow("pos_x",t('Xo'),'','');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),'','');
				$dsp->AddTextFieldRow("end_x",t('Breite'),'','');		
				$dsp->AddTextFieldRow("end_y",t('H&ouml;he'),'','');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),'0','');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),'0','');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),'0','');
				$dsp->AddCheckBoxRow("fontsize",t('Gef&uuml;llt'),'','');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL','1');
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),'','');
				$help = "pdf/item_rect";
			}elseif ($object == "line"){
				$dsp->AddTextFieldRow("pos_x",t('Xo'),'','');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),'','');
				$dsp->AddTextFieldRow("end_x",t('X'),'','');		
				$dsp->AddTextFieldRow("end_y",t('Y'),'','');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),'0','');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),'0','');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),'0','');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL','1');
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),'','');
				$help = "pdf/item_line";
			}elseif ($object == "text"){				
				$dsp->AddTextFieldRow("text",t('Text'),'','');
				$dsp->AddTextFieldRow("pos_x",t('Xo'),'','');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),'','');
				$dsp->AddCheckBoxRow("end_x",t('Rechtsb&uuml;ndig'),'','');		
				$dsp->AddTextFieldRow("font",t('Schriftart'),'Arial','');		
				$dsp->AddTextFieldRow("fontsize",t('Schriftgr&ouml;sse'),'12','');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),'0','');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),'0','');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),'0','');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL','1');
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),'','');
				$help = "pdf/item_text";
			}elseif ($object == "data"){	
				$dsp->AddDropDownFieldRow('text',t('Daten') ,$pdf_export->get_data_array($this->action),"");
				$dsp->AddTextFieldRow("pos_x",t('Xo'),'','');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),'','');
				$dsp->AddCheckBoxRow("end_x",t('Rechtsb&uuml;ndig'),'','');		
				$dsp->AddTextFieldRow("font",t('Schriftart'),'Arial','');		
				$dsp->AddTextFieldRow("fontsize",t('Schriftgr&ouml;sse'),'12','');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),'0','');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),'0','');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),'0','');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL','1');
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),'','');				
				$help = "pdf/item_data";
			}elseif ($object == "image"){				
				$dsp->AddTextFieldRow("text",t('Datei (relativ zu ext_inc/pdf_templates/'),'','');
				$dsp->AddTextFieldRow("pos_x",t('Xo'),'','');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),'','');
				$dsp->AddTextFieldRow("end_x",t('Breite'),'','');		
				$dsp->AddTextFieldRow("end_y",t('H&ouml;he'),'','');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL','1');
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),'','');
				$help = "pdf/item_img";
			}elseif ($object == "barcode"){				
				$dsp->AddTextFieldRow("pos_x",t('Xo'),'','');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),'','');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL','1');
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),'','');
				$help = "pdf/item_img";
			}
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action, $help);
		$dsp->AddContent();	
	}
	
	// Maske um Eintr�ge �ndern anzeigen
	function change_mask($item_id){
		global $config,$db,$dsp,$lang,$templ;
		$pdf_export = new pdf($this->tmpl_id);
		
		$data = $db->query_first("SELECT * FROM " . $config['tables']['pdf_data'] . " WHERE pdfid='" . $item_id . "'");
								  
		$user_type_list = array( "0" =>  t('Alle') ,"1" =>  t('Besucher ist normaler Gast') ,"2" =>  t('Administrator') ,"3" =>  t('Superadmin') ); 
		
		// Liste f�r Datenfeld erzeugen
		foreach ($user_type_list as $key => $value){
			if($key == $data['user_type']){
				$user_type[$key] = "<option selected value=\"$key\">$value</option>";
			}else {
				$user_type[$key] = "<option value=\"$key\">$value</option>";
			}
		}
		
		// Liste f�r Benutzer
		foreach ($user_type_list as $key => $value){
			if($key == $data['user_type']){
				$user_type[$key] = "<option selected value=\"$key\">$value</option>";
			}else {
				$user_type[$key] = "<option value=\"$key\">$value</option>";
			}
		}
					 
		$object = $data['type']; 
		$dsp->NewContent(t('Objekt'),t('Objekt &auml;ndern'));	
		$dsp->AddSingleRow(t('Ändere ') . " " . $lang['pdf'][$object]);		  
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action ."&act=change_item&object=$object&id=$this->tmpl_id&itemid=$item_id");
			if($object == "rect"){
				$dsp->AddTextFieldRow("pos_x",t('Xo'),$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),$data['pos_y'],'');
				$dsp->AddTextFieldRow("end_x",t('Breite'),$data['end_x'],'');		
				$dsp->AddTextFieldRow("end_y",t('H&ouml;he'),$data['end_y'],'');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),$data['red'],'');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),$data['green'],'');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),$data['blue'],'');
				$dsp->AddCheckBoxRow("fontsize",t('Gef&uuml;llt'),'','','NULL',$data['fontsize']);
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),$data['sort'],'');
				$help = "pdf/item_rect";
			}elseif ($object == "line"){
				$dsp->AddTextFieldRow("pos_x",t('Xo'),$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),$data['pos_y'],'');
				$dsp->AddTextFieldRow("end_x",t('X'),$data['end_x'],'');		
				$dsp->AddTextFieldRow("end_y",t('Y'),$data['end_y'],'');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),$data['red'],'');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),$data['green'],'');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),$data['blue'],'');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),$data['sort'],'');
				$help = "pdf/item_line";
			}elseif ($object == "text"){				
				$dsp->AddTextFieldRow("text",t('Text'),$data['text'],'');
				$dsp->AddTextFieldRow("pos_x",t('Xo'),$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),$data['pos_y'],'');
				$dsp->AddCheckBoxRow("end_x",t('Rechtsb&uuml;ndig'),'','','NULL',$data['end_x']);		
				$dsp->AddTextFieldRow("font",t('Schriftart'),$data['font'],'');		
				$dsp->AddTextFieldRow("fontsize",t('Schriftgr&ouml;sse'),$data['fontsize'],'');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),$data['red'],'');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),$data['green'],'');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),$data['blue'],'');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),$data['sort'],'');
				$help = "pdf/item_text";
			}elseif ($object == "data"){	
				$dsp->AddDropDownFieldRow('text',t('Daten') ,$pdf_export->get_data_array($this->action,$data['text']),"");
				$dsp->AddTextFieldRow("pos_x",t('Xo'),$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),$data['pos_y'],'');
				$dsp->AddCheckBoxRow("end_x",t('Rechtsb&uuml;ndig'),'','','NULL',$data['end_x']);
				$dsp->AddTextFieldRow("font",t('Schriftart'),$data['font'],'');		
				$dsp->AddTextFieldRow("fontsize",t('Schriftgr&ouml;sse'),$data['fontsize'],'');
				$dsp->AddTextFieldRow("red",t('Rot Anteil'),$data['red'],'');
				$dsp->AddTextFieldRow("green",t('Gr&uuml;n Anteil'),$data['green'],'');		
				$dsp->AddTextFieldRow("blue",t('Blau Anteil'),$data['blue'],'');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),$data['sort'],'');			
				$help = "pdf/item_data";
			}elseif ($object == "image"){				
				$dsp->AddTextFieldRow("text",t('Datei (relativ zu ext_inc/pdf_templates/'),$data['text'],'');
				$dsp->AddTextFieldRow("pos_x",t('Xo'),$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),$data['pos_y'],'');
				$dsp->AddTextFieldRow("end_x",t('Breite'),$data['end_x'],'');		
				$dsp->AddTextFieldRow("end_y",t('H&ouml;he'),$data['end_y'],'');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),$data['sort'],'');
				$help = "pdf/item_img";
			}elseif ($object == "barcode"){				
				$dsp->AddTextFieldRow("pos_x",t('Xo'),$data['pos_x'],'');		
				$dsp->AddTextFieldRow("pos_y",t('Yo'),$data['pos_y'],'');
				$dsp->AddDropDownFieldRow('user_type',t('Angezeigt bei:') ,$user_type,"");
				$dsp->AddCheckBoxRow("visible",t('Sichtbar'),'','','NULL',$data['visible']);
				$dsp->AddTextFieldRow("sort",t('Reihenfolge'),$data['sort'],'');
				$help = "pdf/item_img";
			}
		$dsp->AddFormSubmitRow("add");
		$dsp->AddBackButton("index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id,$help);
		$dsp->AddContent();	
	}

	
	// ein Objekt einf�gen
	function insert_item($object){
		global $config,$db,$dsp,$lang,$templ,$func;
		
		
		if($_POST['visible'] == "checked"){
			$visible = 1;
		}else{
			$visible = 0;
		}
		
		if($db->query("INSERT INTO " . $config['tables']['pdf_data'] . "  ( `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` , `sort` ) 
		VALUES ('$this->tmpl_id' , '" . $_POST['visible'] . "' , '$object', '" . $_POST['pos_x'] . "', '" . $_POST['pos_y'] . "', '" . $_POST['end_x'] . "', '" . $_POST['end_y'] . "', '" . $_POST['fontsize'] . "', '" . $_POST['font'] . "', '" . $_POST['red'] . "', '" . $_POST['green'] . "', '" . $_POST['blue'] . "', '" . $_POST['text'] . "', '" . $_POST['user_type'] . "', '" . $_POST['sort'] . "')" )){
			$func->confirmation(t('Die Daten wurden hinzugef&uuml;gt'),"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}else{
			$func->error(t('Die Daten konnten nicht hinzugef&uuml;gt werden'),"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}
		
	}
	
	// Objekt �ndern
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
    
			$func->confirmation(t('Die Daten wurden hinzugef&uuml;gt'),"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}else{
			$func->error(t('Die Daten konnten nicht hinzugef&uuml;gt werden'),"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
		}
		
	}
		
		// Sortierung �ndern
	function sortorder($direction,$item_id){
		global $config,$db,$dsp,$lang,$templ,$func;	
		
		if($direction == "minus"){
			$sort = "-1";
		}else {
			$sort = "+1";
		}
		$db->query("UPDATE " .  $config['tables']['pdf_data'] . " SET sort=sort$sort WHERE pdfid = '" . $item_id . "'");
	
		
	}
	// Daten l�schen
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
		// Array f�r Seitengr�ssen		
		$page_size = array("<option selected value=\"A4\">A4</option>","<option value=\"A3\">A3</option>","<option value=\"A5\">A5</option>");
		
		// Formular f�r neues Template
		$dsp->NewContent(t('Vorlagen'),t('Neue Vorlage erstellen'));
		$dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=add");
		$dsp->AddTextFieldRow("template_name", t('Vorlagenname'),'','');
		$dsp->AddDropDownFieldRow("pagesize", t('Seitengr&ouml;sse'),$page_size,'');
		$dsp->AddTextFieldRow("rand_x", t('Rand in x-Richtung'),'','');
		$dsp->AddTextFieldRow("rand_y", t('Rand in y-Richtung'),'','');
		$dsp->AddCheckBoxRow("landscape",t('Querformat'),'','');
		$dsp->AddFormSubmitRow("add","pdf/new_template");
		$dsp->AddContent();
	}
	
}