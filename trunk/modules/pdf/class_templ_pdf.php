<?php

include_once("inc/classes/class_gd.php");
$gd = new gd;

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
        global $config,$db,$dsp,$lang,$templ, $smarty;   
        
        $data = $db->qry("SELECT * FROM %prefix%pdf_list WHERE template_type = %string%", $this->action);
        
        $dsp->NewContent(t('Besucherausweise erstellen'), t('Bitte eine Formatierungsform ausw&auml;hlen oder eine Neue erstellen'));
        // Liste mit möglichen Vorlagen ausgeben
        $out = "";
        if ($db->num_rows($data) > 0){
            while($data_array = $db->fetch_array($data)){
              $smarty->assign('liste', "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=start&id=" . $data_array['template_id'] . "\">" . $data_array['name'] . "</a>");
              $smarty->assign('change', "<a href=\"index.php?mod=pdf&action=" . $this->action . "&act=change&id=" . $data_array['template_id'] . "\">" . t('Vorlage &auml;ndern') . "</a>");
              $smarty->assign('delete', "<a href=\"index.php?mod=pdf&action=" . $this->action . "&delete=1&id=" . $data_array['template_id'] . "\">" . t('Vorlage l&ouml;schen') . "</a>");                
              $out .= $smarty->fetch('modules/pdf/templates/liste.htm');
            }
            $dsp->AddSingleRow($out);
        }else {
            $dsp->AddSingleRow(t('Keine Vorlagen gefunden'));
        }
        $dsp->AddSingleRow("<a href=\"index.php?mod=pdf&action=" . $_GET['action'] . "&act=new\">".t('Neue Vorlage erstellen')."</a>");
        $dsp->AddBackButton("index.php?mod=pdf","pdf/template");
        $dsp->AddContent();

        
        
    }
    
    // Daten einfügen
    function add_templ(){
        global $config,$db,$dsp,$lang,$templ;   
        // In Liste einfügen
        $db->qry("INSERT INTO %prefix%pdf_list ( `template_id` , `template_type` , `name` ) VALUES ('', %string%, %string%)", $this->action, $_POST['template_name']);
        $this->tmpl_id = $db->insert_id();
        
        // Config anlegen
        $db->qry("INSERT INTO %prefix%pdf_data ( `pdfid` , `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` ) VALUES
  ('', %int%, %string%, 'config', %string%, %string%,'0','0','0','','0','0','0', %string%, '')",
  $this->tmpl_id, $_POST['landscape'], $_POST['rand_x'], $_POST['rand_y'], $_POST['pagesize']);
    
    
    }
    
    // Daten auslesen
    function display_data(){
        global $config,$db,$dsp,$lang,$templ,$gd, $smarty;
                  
        // Name ausgeben
        $template = $db->qry_first("SELECT * FROM %prefix%pdf_list WHERE template_id= %int%", $this->tmpl_id);
        $dsp->NewContent(t('Vorlagen'),t('Vorlage &auml;ndern'));
        $dsp->AddDoubleRow(t('Vorlagenname'),$template['name']);
        
        // Konfiguration ausgeben
        $template_config = $db->qry_first("SELECT * FROM %prefix%pdf_data WHERE template_id= %int% AND type='config'", $this->tmpl_id);
        
        $dsp->AddDoubleRow(t('Rand in x-Richtung'),$template_config['pos_x']);
        $dsp->AddDoubleRow(t('Rand in y-Richtung'),$template_config['pos_y']);
        $dsp->AddDoubleRow(t('Seitengr&ouml;sse'),$template_config['text']);

        // Daten ausgeben
        $data = $db->qry("SELECT * FROM %prefix%pdf_data WHERE template_id= %int% AND type != 'config' ORDER BY sort ASC", $this->tmpl_id);
        
        $templ['pdf']['action'] = $this->action;
        
        $out = "";
        while ($data_array = $db->fetch_array($data)){
        	$smarty->assign('action', $_GET['action']);
            $smarty->assign('name', $lang['pdf'][$data_array['type']]);
            $smarty->assign('itemid', $data_array['pdfid']);
            $smarty->assign('id', $this->tmpl_id);
            if($data_array['type'] == "rect"){
                $description = t('Xo') . " : " . $data_array['pos_x']. " , "; 
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Breite') . " : " . $data_array['end_x']. " , ";
                $description .= t('H&ouml;he') . " : " . $data_array['end_y']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            }elseif ($data_array['type'] == "line"){
                $description = t('Xo') . " : " . $data_array['pos_x']. " , "; 
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('X') . " : " . $data_array['end_x']. " , ";
                $description .= t('Y') . " : " . $data_array['end_y']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            }elseif ($data_array['type'] == "text" || $data_array['type'] == "data"){
                $description = t('Text') . " : " . $data_array['text']. HTML_NEWLINE; 
                $description .= t('Xo') . " : " . $data_array['pos_x']. " , "; 
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , ";
                $description .= t('Rechtsb&uuml;ndig') . " : " . $data_array['end_x']. " , ";
                $description .= t('Schriftart') . " : " . $data_array['font']. " , ";
                $description .= t('Schriftgr&ouml;sse') . " : " . $data_array['fontsize']. " , ";
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('Farbe (r/g/b)') . " : " . $data_array['red'] . "/". $data_array['green'] . "/". $data_array['blue'];
            }elseif ($data_array['type'] == "image"){
                $description = t('Xo') . " : " . $data_array['pos_x']. " , "; 
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , "; ;
                $description .= t('Breite') . " : " . $data_array['end_x']. " , "; 
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
                $description .= t('H&ouml;he') . " : " . $data_array['end_y'];
            }elseif ($data_array['type'] == "barcode"){
                $description = t('Xo') . " : " . $data_array['pos_x']. " , "; 
                $description .= t('Yo') . " : " . $data_array['pos_y']. " , "; ;
                $description .= t('Sichtbar') . " : " . $data_array['visible'] . " , ";
            }
            $smarty->assign('description', $description);

      $gd->CreateButton('edit');
      $gd->CreateButton('delete');
      $out .= $smarty->fetch('modules/pdf/templates/edit_liste.htm');
        }
        $dsp->AddSingleRow($out);
        
        // Array erzeugen für mögliche Einträge
        $type = array("<option selected value=\"rect\">" . t('Rechteck') . "</option>",
                      "<option value=\"text\">" . t('Text') . "</option>",
                      "<option value=\"line\">" . t('Linie') . "</option>",
                      "<option value=\"image\">" . t('Bild') . "</option>",
                      "<option value=\"data\">" . t('Daten') . "</option>",
                      "<option value=\"barcode\">" . t('Strichcode') . "</option>");

        // Formular für hinzufügen von Einträgen
        $dsp->SetForm("index.php?mod=pdf&action=" . $this->action . "&act=insert_mask&id=" . $this->tmpl_id);
        $dsp->AddDropDownFieldRow('type',t('Wahl des Feldes') ,$type,"");
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
        $user_type = array("<option selected value=\"0\">" . t('Alle') . "</option>",
                      "<option value=\"1\">" . t('Besucher ist normaler Gast') . "</option>",
                      "<option value=\"2\">" . t('Administrator') . "</option>",
                      "<option value=\"3\">" . t('Superadmin') . "</option>");
                      
        // Maske ausgeben für entsprechenden eintrag
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
    
    // Maske um Einträge ändern anzeigen
    function change_mask($item_id){
        global $config,$db,$dsp,$lang,$templ;
        $pdf_export = new pdf($this->tmpl_id);
        
        $data = $db->qry_first("SELECT * FROM %prefix%pdf_data WHERE pdfid= %int%", $item_id);
                                  
        $user_type_list = array( "0" =>  t('Alle') ,"1" =>  t('Besucher ist normaler Gast') ,"2" =>  t('Administrator') ,"3" =>  t('Superadmin') ); 
        
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

    
    // ein Objekt einfügen
    function insert_item($object){
        global $config,$db,$dsp,$lang,$templ,$func;
        
        
        if($_POST['visible'] == "checked"){
            $visible = 1;
        }else{
            $visible = 0;
        }
        
        if ($db->qry("INSERT INTO %prefix%pdf_data ( `template_id` , `visible` , `type` , `pos_x` , `pos_y` , `end_x` , `end_y` , `fontsize` , `font` , `red` , `green` , `blue` , `text` , `user_type` , `sort` ) 
          VALUES %plain%", "('$this->tmpl_id' , '" . $_POST['visible'] . "' , '$object', '" . $_POST['pos_x'] . "', '" . $_POST['pos_y'] . "', '" . $_POST['end_x'] . "', '" . $_POST['end_y'] . "', '" . $_POST['fontsize'] . "', '" . $_POST['font'] . "', '" . $_POST['red'] . "', '" . $_POST['green'] . "', '" . $_POST['blue'] . "', '" . $_POST['text'] . "', '" . $_POST['user_type'] . "', '" . $_POST['sort'] . "')")) {
            $func->confirmation(t('Die Daten wurden hinzugef&uuml;gt'),"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
        }else{
            $func->error(t('Die Daten konnten nicht hinzugef&uuml;gt werden'),"index.php?mod=pdf&action=" . $this->action ."&act=change&id=" . $this->tmpl_id);
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

    
        if($db->qry("UPDATE %prefix%pdf_data SET %plain%", "  
             `visible`='" . $_POST['visible'] . 
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
        
        // Sortierung ändern
    function sortorder($direction,$item_id){
        global $config,$db,$dsp,$lang,$templ,$func; 
        
        if($direction == "minus"){
            $sort = "-1";
        }else {
            $sort = "+1";
        }
        $db->qry("UPDATE %prefix%pdf_data SET sort=sort%plain% WHERE pdfid = %int%", $sort, $item_id);
    
        
    }
    // Daten löschen
    function delete_templ(){
        global $config,$db,$dsp,$lang,$templ;   
        
        $db->qry("DELETE FROM %prefix%pdf_list WHERE template_id = %int%", $this->tmpl_id);
        $db->qry("DELETE FROM %prefix%pdf_data WHERE template_id = %int%", $this->tmpl_id);
    
        
    }
    
    function delete_item($itemid){
        global $config,$db,$dsp,$lang,$templ;   
        
        $db->qry("DELETE FROM %prefix%pdf_data WHERE pdfid = %int%", $itemid);
    
        
    }
    
    function new_templ_mask(){
        global $dsp,$lang;
        // Array für Seitengrössen        
        $page_size = array("<option selected value=\"A4\">A4</option>","<option value=\"A3\">A3</option>","<option value=\"A5\">A5</option>");
        
        // Formular für neues Template
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
?>