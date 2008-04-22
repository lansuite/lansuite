<?php

class display {

  var $form_line = '';
  var $content_need_form = 0;
  var $form_ok = 0;
  var $form_open = 0;
  var $formcount = 1;
  var $TplCache = array();
  var $errortext_prefix = '';
  var $errortext_suffix = '';
  var $FirstLine = 1;
  var $TplVars = array();

  // Constructor
  function display() {
    $this->errortext_prefix = HTML_NEWLINE . HTML_FONT_ERROR;
    $this->errortext_suffix = HTML_FONT_END;
  }

  function EchoTpl($file) {
    global $auth, $language;

    $handle = fopen ($file, 'rb');
    $tpl_str = fread($handle, filesize($file));
    fclose ($handle);

    $tpl_str = str_replace('{language}', $language, $tpl_str );
    $tpl_str = str_replace('{default_design}', $auth['design'], $tpl_str);

    echo $tpl_str;
  }

  function SetVar($name, $value){
    $this->TplVars[$name] = $value;
  }

  function EchoVar($name){
    echo $this->TplVars[$name];
  }

  // Returns the template $file
  function FetchTpl($file, $templx = ''){
    global $auth, $language, $cfg, $TplCache, $templ;
        
    #echo "Loading $file<br>";
    if ($this->TplCache[$file] != '') $tpl_str = $this->TplCache[$file];
    else {
      $handle = fopen ($file, 'rb');
      $tpl_str = fread ($handle, filesize ($file));
      fclose ($handle);
      $this->TplCache[$file] = $tpl_str;
    }

    $tpl_str = str_replace("\"","\\\"", $tpl_str );
    $tpl_str = str_replace("{language}", $language, $tpl_str );
    $tpl_str = str_replace("{default_design}", $auth["design"], $tpl_str);

    $tpl = "";
    if ($cfg['sys_showdebug']) $tpl .= "\r\n<!-- Start of template '$file' -->\r\n";
    eval("\$tpl .= \"" .$tpl_str. "\";");
    if ($cfg['sys_showdebug']) $tpl .= "\r\n<!-- End of template '$file' -->\r\n";

    return $tpl;
  }

  // Output the template $file
  function AddTpl($file, $OpenTable = 1){
    global $templ;

  echo $this->FetchTpl($file, $templ);
  }

  // Output the template $file
  function AddLineTpl($file, $OpenTable = 1){
    global $templ;

    if ($_GET['design'] != 'base') {
      if ($this->FirstLine) {
        echo '<ul class="LineFirst">'. $this->FetchTpl($file) .'</ul>';
        $this->FirstLine = 0;
      } else echo '<ul class="Line">'. $this->FetchTpl($file) .'</ul>';
    }
  }

  // Output the template $file
  function AddLineTplSmarty($file, $OpenTable = 1){
    global $smarty;

    if ($_GET['design'] != 'base') {
      if ($this->FirstLine) {
        $smarty->assign('content', $file);
        $smarty->display('design/templates/ls_row_firstline.htm');
        $this->FirstLine = 0;
      } else {
        $smarty->assign('content', $file);
        $smarty->display('design/templates/ls_row_line.htm');
      }
    }
  }

  // Writes the headline of a page
  function NewContent($caption, $text = NULL, $helplet_id = 'help') {
    global $smarty;

    $smarty->assign('helplet_id', $helplet_id);
    $smarty->assign('mod', $_GET['mod']);
    $smarty->assign('caption', $caption);
    $smarty->assign('text', $text);

    unset($this->content_need_form);
    $this->form_ok = false;

    $this->AddLineTplSmarty($smarty->fetch('design/templates/ls_row_headline.htm'));
  }

  function AddHeaderButtons() {
  }

  /*TODO*/
  function AddHeaderMenu($names, $link, $active = NULL) {
    global $templ;

    foreach ($names as $key => $name) {
      if ($key == $active and $active != NULL) $am = '';
      else $am = 'class="menu"';
      $templ['AddHeaderMenu']['items'] .= "<a href=\"".$link."&headermenuitem=$key\"".$am."><b>".$name."</b></a> - ";
    }
    // Letztes Minus rausschneiden
    $templ['AddHeaderMenu']['items'] = substr($templ['AddHeaderMenu']['items'], 0, -3);

    $this->AddLineTpl("design/templates/ls_row_headermenu.htm");
  }
    
  /*TODO*/
  function AddHeaderMenu2($names, $link, $active = NULL) {
    global $templ;

    foreach($names as $key => $name) {
      ($key == $active and $active != '')? $am = '' : $am = 'class="menu"';
      $templ['AddHeaderMenu']['items'] .= '<a href="'. $link . $key .'"'. $am .'><b>'. $name .'</b></a> - ';
    }
    $templ['AddHeaderMenu']['items'] = substr($templ['AddHeaderMenu']['items'], 0, -3);
    
    $this->AddLineTpl("design/templates/ls_row_headermenu.htm");
  }

    function StartHiddenBox($name, $vissible = false) {
        global $templ;

    ($vissible)? $vissible = '' : $vissible = 'none';
    echo '<div id="'. $name .'" style="display:'. $vissible .'">';
    }

    function StopHiddenBox() {
        global $templ;
        
    echo '</div>';
    }

    function AddSingleRow($text, $parm = NULL) {
      global $smarty;

      $smarty->assign('text', $text);
      if ($parm != "") $smarty->assign('align', $parm);
      $this->AddLineTplSmarty($smarty->fetch('design/templates/ls_row_single.htm'));
    }

    function AddDoubleRow($key, $value, $id = NULL) {
      global $smarty;

        if ($key == "") $key = "&nbsp;";
        if ($value == "") $value = "&nbsp;";
        if ($id == "") $id = "DoubleRowVal";

        $smarty->assign('key', $key);
        $smarty->assign('value', $value);
        $smarty->assign('id', $id);

        $this->AddLineTplSmarty($smarty->fetch('design/templates/ls_row_double.htm'));
    }

    function AddCheckBoxRow($name, $key, $text, $errortext, $optional = NULL, $checked = NULL, $disabled = NULL, $val = NULL, $additionalHTML = NULL) {
        global $templ;

        ($checked)? $checked = 'checked' : $checked = '';
        ($disabled)? $disabled = 'disabled' : $disabled = '';
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        if ($val == '') $val = '1';

        $value = '<input id="'. $name .'" name="'. $name .'" type="checkbox" class="checkbox" value="'. $val .'" '. $checked .' '. $disabled .' '. $additionalHTML .' />';
    $value .= '<label for="'. $name .'">'. $text .'</label>'. $errortext;
    $key = '<label for="'. $name .'">'. $key .'</label>';
    $this->AddDoubleRow($key, $value);
    }

    function AddRadioRow($name, $key, $val, $errortext = NULL, $optional = NULL, $checked = NULL, $disabled = NULL) {
        global $templ;

        ($checked)? $checked = 'checked="checked"' : $checked = '';
        ($disabled)? $disabled = 'disabled' : $disabled = '';
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';

        $value = '<input name="'. $name .'" type="radio" class="form'. $optional .'" value="'. $val .'" '. $checked .' '. $disabled .' />'. $errortext;
    $key = '<label for="'. $name .'">'. $key .'</label>';
    $this->AddDoubleRow($key, $value);
    }

    function AddTextFieldRow($name, $key, $value, $errortext, $size = NULL, $optional = NULL, $not_changeable = NULL) {
        global $templ;

        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        ($not_changeable)? $not_changeable = ' readonly="readonly"' : $not_changeable = '';
        if ($size == '') $size = '30';

        $value = '<input type="text" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" size="'. $size .'"'. $not_changeable .' value="'. $value .'" />'. $errortext;
    $key = '<label for="'. $name .'">'. $key .'</label>';
    $this->AddDoubleRow($key, $value);
    }


    function AddPasswordRow($name, $key, $value, $errortext, $size = NULL, $optional = NULL, $additional = NULL) {
        global $templ;

        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        if ($size == '') $size = '30';

        $value = '<input type="password" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" size="'. $size .'" value="'. $value .'" '. $additional .' />'. $errortext;
    $key = '<label for="'. $name .'">'. $key .'</label>';
    $this->AddDoubleRow($key, $value);
    }

  function AddTextAreaMailRow($name, $key, $value, $errortext, $cols = NULL, $rows = NULL, $optional = NULL, $maxchar = NULL) {
    global $templ;

    if ($cols == "") $cols = "50";
    if ($rows == "") $rows = "7";
    if ($maxchar == "") $maxchar = "5000";

    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';

    $key = '<label for="'. $name .'">'. $key .'</label>
      <br />
      <br />
      <br />
      <a href="index.php?mod=popups&action=ls_row_textareamail_popup&design=popup&form='. $this->form_name .'&textarea='. $name .'" onclick="OpenWindow(this.href, \'TextFormatSelect\'); return false">Variablen einfügen</a>';
    $value = '<textarea name="'. $name .'" id="'. $name .'" class="form'. $name .'" cols="'. $cols .'" rows="'. $rows .'" onKeyUp="TextAreaPlusCharsLeft(this, document.'. $this->form_name .'.'. $name .'_chr, '. $maxchar .')">'. $value .'</textarea>';
    $value .= $errortext;
    $this->AddDoubleRow($key, $value);
  }

    function AddTextAreaRow($name, $key, $value, $errortext, $cols = NULL, $rows = NULL, $optional = NULL) {
        global $templ;

    if ($cols == "") $cols = "50";
    if ($rows == "") $rows = "7";
    if ($maxchar == "") $maxchar = "5000";

    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';

    $key = '<label for="'. $name .'">'. $key .'</label>';
    $value = '<textarea name="'. $name .'" id="'. $name .'" class="form'. $name .'" cols="'. $cols .'" rows="'. $rows .'" onKeyUp="TextAreaPlusCharsLeft(this, document.'. $this->form_name .'.'. $name .'_chr, '. $maxchar .')">'. $value .'</textarea>';
    $value .= $errortext;
    $this->AddDoubleRow($key, $value);
    }

  function AddTextAreaPlusRow($name, $key, $value, $errortext, $cols = NULL, $rows = NULL, $optional = NULL, $maxchar = NULL) {
    global $templ, $db;

    #if ($cols == "") $cols = "50"; // Now dynamicaly
    if ($rows == "") $rows = "7";
    if ($maxchar == "") $maxchar = "5000";

    $templ['TextAreaPlusRow']['name'] = $name;
    $templ['TextAreaPlusRow']['formname'] = $this->form_name;
    $templ['TextAreaPlusRow']['key'] = $key;
    $templ['TextAreaPlusRow']['value'] = $value;
    #$templ['TextAreaPlusRow']['cols'] = $cols;
    $templ['TextAreaPlusRow']['rows'] = $rows;
    $templ['TextAreaPlusRow']['maxchar'] = $maxchar;

    ($errortext)? $templ['TextAreaPlusRow']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix : $templ['TextAreaPlusRow']['errortext'] = '';
    ($optional)? $templ['TextAreaPlusRow']['optional'] = "_optional" : $templ['TextAreaPlusRow']['optional'] = '';

    $this->form_open = false;
    $templ['TextAreaPlusRow']['buttons'] = $this->FetchButton('index.php?mod=popups&action=textareaplus_preview&design=popup&textareaname='. $name .'" onclick="javascript:OpenPreviewWindow(this.href, document.'. $this->form_name .'); return false;', 'preview', t('Vorschau'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[b]', '[/b]')", 'bold', t('Fett'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[i]', '[/i]')", 'italic', t('Kursiv'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[u]', '[/u]')", 'underline', t('Unterstrichen'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[s]', '[/s]')", 'strike', t('Durchstreichen'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[sub]', '[/sub]')", 'sub', t('Tiefstellen'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[sup]', '[/sup]')", 'sup', t('Hochstellen'));
    $templ['TextAreaPlusRow']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[c]', '[/c]')", 'quote', t('Code'));
    $templ['TextAreaPlusRowa']['buttons'] .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[img]', '[/img]')", 'img', t('Bild'));
    $this->form_open = true;

    $this->AddLineTpl("design/templates/ls_row_textareaplus.htm");
  }

    function AddDropDownFieldRow($name, $key, $option_array, $errortext, $optional = NULL, $additionalHTML = NULL) {
        global $templ;

    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';
        ($option_array)? $options = implode('', $option_array) : $options = '';
        
        // TODO: If no <option> in $options generate from array

    $key = '<label for="'. $name .'">'. $key .'</label>';
    $value = '<select name="'. $name .'" class="form'. $optional .'" '. $additionalHTML .'>';
    $value .= $options;
    $value .= '</select>';
    $value .= $errortext;
    $this->AddDoubleRow($key, $value);
    }

    function AddFieldsetStart($name) {
        global $templ;

    $templ['FieldsetStart']['name'] = $name;
        $this->AddTpl("design/templates/ls_row_fieldset_start.htm", 0);
        $this->FirstLine = 1;
    }

    function AddFieldsetEnd() {
        $this->AddTpl("design/templates/ls_row_fieldset_end.htm", 0);
        $this->FirstLine = 1;
    }

    function AddSelectFieldRow($name, $key, $option_array, $errortext, $optional = NULL, $size = NULL) {
        global $templ;

    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';
        ($option_array)? $options = implode('', $option_array) : $options = '';
        if (!$size) $size = 4;

        // TODO: If no <option> in $options generate from array

    $key = '<label for="'. $name .'">'. $key .'</label>';
    $value = '<select name="'. $name .'[]" class="form'. $optional .'" size="'. $size .'" multiple>';
    $value .= $options;
    $value .= '</select>';
    $value .= $errortext;
    $this->AddDoubleRow($key, $value);
    }

    function AddFormSubmitRow($button, $helplet_id = NULL, $var = false, $close = true) {
        global $templ, $gd, $auth, $language, $lang;

        ($var)? $ButtonName = $var : $ButtonName = 'imageField';
    $hint = $button;

    $key = '&nbsp;';
    // For old compatibility
    if ($lang['button'][$button]) $value = '<input type="submit" class="Button" name="'. $ButtonName .'" value="'. $lang['button'][$button] .'" />';
    else $value = '<input type="submit" class="Button" name="'. $ButtonName .'" value="'. t($button) .'" />';
    $this->AddDoubleRow($key, $value);

    if ($this->form_open && $close) $this->CloseForm();
    }

  function AddBackButton($back_link = NULL, $helplet_id = NULL) {
    global $templ, $gd, $auth, $func;

    if ( !$back_link ) $back_link = $func->internal_referer;
    $gd->CreateButton($button);
    $this->AddDoubleRow('', $this->FetchButton($back_link, 'back', t('Zurück')));
  }

  function AddBarcodeForm($key, $value, $action, $methode = "post", $errortext = NULL,  $size = NULL, $optional = NULL){
    global $templ;

    if ($size == '') $size = '30';
    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';

    $key = '<label for="barcode">'. $key .'</label>';
    $val= '<form name="barcode" method="'. $method .'" action="'. $action .'">';
    $val .= '<input onkeyup="checkfield(this)" type="text" name="barcodefield" class="form'. $optional .'" size="'. $size .'" value="'. $value .'" />';
    $val .= $errortext;
    $val .= '</form>';
    $val .= '<script type="text/javascript">';
    $val .= 'function selectfield(){';
    $val .= 'document.forms["barcode"].elements["barcodefield"].focus();';
    $val .= '}';
    $val .= 'function checkfield(id){';
    $val .= 'if(id.value.length == 12){';
    $val .= 'document.barcode.submit();';
    $val .= '}';
    $val .= '}';
    $val .= 'selectfield();';
    $val .= '</script>';
    $this->AddDoubleRow($key, $val);
  }

  function AddDateTimeRow($name, $key, $time, $errortext, $values = NULL, $disableds = NULL, $start_year = NULL, $end_year = NULL, $hidetime = NULL, $optional = NULL, $additional = NULL) {
    global $templ;

    $templ['ls']['row']['datetime']['name'] = $name;
    $templ['ls']['row']['datetime']['key'] = $key;

    // IF timestamp
    if($time > 0) {
      $day = date("d", $time);
      $month = date("m", $time);
      $year = date("Y", $time);
      $hour = date("H", $time);
      $min = date("i", $time);
    // IF values
    } else if (($values['day'] != "") && ($values['month'] != "") && ($values['year'] != "")){
      $day = $values['day'];
      $month = $values['month'];
      $year = $values['year'];
      $hour = $values['hour'];
      $min = $values['min'];
    // ELSE current date
    } else {
      $day = date("d");
      $month = date("m");
      $year = date("Y");
      $hour = date("H");
      $min = round(date("i") / 5) * 5;
    }

    if ($start_year == "") $start_year = -1;
    if ($end_year == "") $end_year = 5;
    $start_year = date("Y") + $start_year;
    $end_year = date("Y") + $end_year;

    $templ['ls']['row']['datetime']['value']['day'] = "";
    $templ['ls']['row']['datetime']['value']['month'] = "";
    $templ['ls']['row']['datetime']['value']['year'] = "";
    $templ['ls']['row']['datetime']['value']['hour'] = "";
    $templ['ls']['row']['datetime']['value']['min'] = "";
    $templ['ls']['row']['datetime']['additional'] = $additional;
    if ($optional) $templ['ls']['row']['datetime']['optional'] = "_optional";

    $templ['ls']['row']['datetime']['value']['day'] .= "<option value=\"00\" $selected>-</option>";
    for ($x = 1; $x <= 31; $x++) {
      ($x < 10) ? $y = "0".$x : $y = $x;
      ($day == $x)? $selected = "selected" : $selected = "";
      $templ['ls']['row']['datetime']['value']['day'] .= "<option value=\"$x\" $selected>$y</option>";
    }

    $templ['ls']['row']['datetime']['value']['month'] .= "<option value=\"00\" $selected>-</option>";
    for ($x = 1; $x <= 12; $x++) {
      ($x < 10) ? $y = "0".$x : $y = $x;
      ($month == $x)? $selected = "selected" : $selected = "";
      $templ['ls']['row']['datetime']['value']['month'] .= "<option value=\"$x\" $selected>$y</option>";
    }

    $templ['ls']['row']['datetime']['value']['year'] .= "<option value=\"0000\" $selected>-</option>";
    for ($x = $start_year; $x <= $end_year; $x++) {
      ($year == $x)? $selected = "selected" : $selected = "";
      $templ['ls']['row']['datetime']['value']['year'] .= "<option value=\"$x\" $selected>$x</option>";
    }

    for ($x = 0; $x <= 23; $x++) {
      ($x < 10) ? $y = "0".$x : $y = $x;
      ($hour == $x)? $selected = "selected" : $selected = "";
      $templ['ls']['row']['datetime']['value']['hour'] .= "<option value=\"$x\" $selected>$y</option>";
    }

    for ($x = 0; $x <= 55; $x +=5) {
      ($x < 10) ? $y = "0".$x : $y = $x;
      ($min == $x)? $selected = "selected" :$selected = "";
      $templ['ls']['row']['datetime']['value']['min'] .= "<option value=\"$x\" $selected>$y</option>";
    }

    if($disableds['day'])  { $templ['ls']['row']['datetime']['disabled']['day'] = "disabled"; }
    else                   { $templ['ls']['row']['datetime']['disabled']['day'] = ""; }
    if($disableds['month']){ $templ['ls']['row']['datetime']['disabled']['month'] = "disabled"; }
    else                   { $templ['ls']['row']['datetime']['disabled']['month'] = ""; }
    if($disableds['year']) { $templ['ls']['row']['datetime']['disabled']['year'] = "disabled"; }
    else                   { $templ['ls']['row']['datetime']['disabled']['year'] = ""; }
    if($disableds['hour']) { $templ['ls']['row']['datetime']['disabled']['hour'] = "disabled"; }
    else                   { $templ['ls']['row']['datetime']['disabled']['hour'] = ""; }
    if($disableds['min'])  { $templ['ls']['row']['datetime']['disabled']['min'] = "disabled"; }
    else                   { $templ['ls']['row']['datetime']['disabled']['min'] = ""; }

    if ($errortext) $templ['ls']['row']['datetime']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['datetime']['errortext'] = '';

    // 0 =  All visible / 1 = Hide Time / 2 = Hide Date
    ($hidetime == 2)? $templ['ls']['row']['datetime']['show_date'] = ""
    : $templ['ls']['row']['datetime']['show_date'] = $this->FetchModTpl("", "ls_row_datetime_date");
    ($hidetime == 1)? $templ['ls']['row']['datetime']['show_time'] = ""
    : $templ['ls']['row']['datetime']['show_time'] = $this->FetchModTpl("", "ls_row_datetime_time");

    $this->AddLineTpl("design/templates/ls_row_datetime.htm");
  }

  function AddHRuleRow() {
    $this->AddTpl("design/templates/ls_row_hrule.htm");
  }

  function AddPictureDropDownRow($name, $key, $path, $errortext, $optional = NULL, $selected = NULL) {
    global $templ, $func;

    $dir = $func->GetDirList($path);
    $file_out = array();
    $file_out[] = "<option value=\"none\">None</option>";
    if ($dir) foreach($dir as $file) {
      $extension = substr($file, strpos($file, '.') + 1, 4);
      if ($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "gif") {
        ($file == $selected)? $file_out[] = "<option value=\"".$file."\" selected>".$file."</option>"
          : $file_out[] = "<option value=\"".$file."\">".$file."</option>";
      }
    }

    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';
    ($selected and $selected != "none")? $picpreview_init = $path."/".$selected :$picpreview_init = 'design/standard/images/index_transparency.gif';
    $options = implode("", $file_out);

    $key = '<label for="'. $name .'">'. $key .'</label>';
    $value .= '<select name="'. $name .'" id="'. $name .'" class="form'. $optional .'" onChange="javascript:changepic(\''. $path .'/\'+ this.value, window.document.'. $name .'_picpreview)" > '. $options .'';
    $value .= '</select>';
    $value .= $errortext;
    $value .= '<br /><img src="'. $picpreview_init .'" name="'. $name .'_picpreview" alt="pic" />';
    $this->AddDoubleRow($key, $value);
  }

  // TODO: Review!
  function AddPictureSelectRow($key, $path, $pics_per_row = NULL, $max_rows = NULL, $optional = NULL, $checked = NULL, $max_width = NULL, $max_height = NULL, $JS = false) {
    global $templ, $gd;

    if ($max_width == "") $max_width = 150;
    if ($max_height == "") $max_height = 120;
    if ($max_rows == "") $max_rows = 100;
    if ($pics_per_row == "") $pics_per_row = 3;

    $templ['ls']['row']['pictureselect']['zeile'] = "";
    $templ['ls']['row']['pictureselect']['spalte'] = "";

  if ($optional) $optional = '_optional';

    $handle = @opendir($path);
    $z = 0;
    // Filter and sort files in directory
    $file_list = array();
    while (($z < $max_rows * $pics_per_row) && ($file = @readdir($handle))) {
        if (($file != ".") && ($file != "..") && (!is_dir($file)) && (substr($file, 0, 8) != "lsthumb_")) {
      array_push($file_list, $file);
        $z++;
      }
    }
    @closedir($handle);
    sort($file_list, SORT_NUMERIC);
    
    // For each file in directory
    $z = 0;
    foreach ($file_list as $file) {
        $extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
        if (($extension == "jpeg") or ($extension == "jpg") or ($extension == "png") or ($extension == "gif")){

            $file_out = "$path/lsthumb_$file";

            // Wenn Thumb noch nicht generiert wurde, generieren versuchen
            if (!file_exists($file_out)) $gd->CreateThumb("$path/$file", $file_out, $max_width, $max_height);

            $pic_dimensions = GetImageSize($file_out);
            if (!$pic_dimensions[0] or $pic_dimensions[0] > $max_width) $pic_dimensions[0] = $max_width;
            if (!$pic_dimensions[1] or $pic_dimensions[1] > $max_height) $pic_dimensions[1] = $max_height;
            $templ['ls']['row']['pictureselect']['pic_width'] = $pic_dimensions[0];
            $templ['ls']['row']['pictureselect']['pic_height'] = $pic_dimensions[1];

            $templ['ls']['row']['pictureselect']['pic_src'] = $file_out;
            $caption = strtolower(substr($file, 0, strrpos($file, ".")));
            if (($z == $checked) || ($file == $checked)) $check = 'checked';
            else $check = '';

      if ($JS) {
        $templ['pictureselect']['IconClick'] = " onClick=\"javascript:UpdateCurrentPicture('$file_out');\"";
        $templ['pictureselect']['InputForm'] = '<input type="hidden" name="'. $key .'" value="'. $file .'" />';
      }
      else $templ['pictureselect']['InputForm'] = '<input type="radio" name="'. $key .'" class="form'. $optional .'" value="'. $file .'" '. $check .' />'. $caption;
            $templ['ls']['row']['pictureselect']['spalte'] .= $this->FetchModTpl("", "ls_row_pictureselect_spalte");
            $z++;

            if ($z % $pics_per_row == 0) {
                $templ['ls']['row']['pictureselect']['zeile'] .= $this->FetchModTpl("", "ls_row_pictureselect_zeile");
                $templ['ls']['row']['pictureselect']['spalte'] = "";
            }
        }
    }

    if ($z % $pics_per_row != 0) {
        $templ['ls']['row']['pictureselect']['zeile'] .= $this->FetchModTpl("", "ls_row_pictureselect_zeile");
        $templ['ls']['row']['pictureselect']['spalte'] = "";
    }

    $this->AddTpl("design/templates/ls_row_pictureselect.htm");
  }

    function AddFileSelectRow($name, $key, $errortext, $size = NULL, $maxlength = NULL, $optional = NULL) {
        global $templ, $func;

    $maxfilesize = ini_get('upload_max_filesize');
    if (strpos($maxfilesize, 'M') > 0) $maxfilesize = (int)$maxfilesize * 1024 * 1024;
    elseif (strpos($maxfilesize, 'K') > 0) $maxfilesize = (int)$maxfilesize * 1024;
    else $maxfilesize = (int)$maxfilesize;

    // If value is too low (most likely because of errors in above statement), set it to 100M
    if ($maxfilesize < 1000) $maxfilesize = 1024 * 1024 * 100;
    $maxfilesize_formated = '(Max: '. $func->FormatFileSize($maxfilesize) .')';

        if ($size == '') $size = '30';
    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';
    ($selected and $selected != "none")? $picpreview_init = $path."/".$selected :$picpreview_init = 'design/standard/images/index_transparency.gif';

    $key = '<label for="'. $name .'">'. $key .'</label>';
    $value = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $maxfilesize .'" />';
    $value .= '<input type="file" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" value="" size="'. $size .'" enctype="multipart/form-data" maxlength="'. $maxlength .'" /> '. $maxfilesize_formated;
    $value .= $errortext;
    $this->AddDoubleRow($key, $value);
    }

  function AddJumpToMark($name) {
    global $templ;

        $templ['jumpto']['name'] = $name;
        $this->AddTpl("design/templates/ls_row_jump_to.htm");
  }

  // Still used?
  function AddIFrame($url, $width=795, $height=600) {
    global $lang, $templ, $func;

    $templ["class_display"]["IFrame"]["noIFrame"] .= t('Wenn ihr Broswer keine IFrames unterstützt, ');
    $templ["class_display"]["IFrame"]["clickhere"] .= t('bitte hier klicken!');
    $templ["class_display"]["IFrame"]["url"] = 'http://' . $url;
    $templ["class_display"]["IFrame"]["width"] = $width;
    $templ["class_display"]["IFrame"]["height"] = $height;

    $this->AddSingleRow($this->FetchModTpl("", "ls_row_IFrame"));
  }

  // Still used?
  function ShowNewWindow($url) {
    global $lang, $templ;

    $templ["class_display"]["NewWindow"]["popupBlocked"] .= t('Wenn das PopUp geblockt wurde, ');
    $templ["class_display"]["NewWindow"]["clickhere"] .= t('bitte hier klicken!');
    $templ["class_display"]["NewWindow"]["url"] = 'http://' . $url;
    $this->AddSingleRow($this->FetchModTpl("", "ls_row_newWindow"));
  }

    // ################################################################################################################# //

    function AddModTpl($mod, $name) {
        global $templ, $debug;
        
        if ($mod == "") $return = $this->AddTpl("design/templates/".$name.".htm");
        else $return = $this->AddTpl("modules/".$mod."/templates/".$name.".htm");
    }

  function FetchAttachmentRow($file) {
    global $gd;
    
    $FileEnding = strtolower(substr($file, strrpos($file, '.'), 5));

    if ($FileEnding == '.png' or $FileEnding == '.gif' or $FileEnding == '.jpg' or $FileEnding == '.jpeg') {
      $FileNamePath = strtolower(substr($file, 0, strrpos($file, '.') - 1));
      $FileThumb = $FileNamePath. '_thumb' .$FileEnding;

      $gd->CreateThumb($file, $FileThumb, '300', '300');
      return HTML_NEWLINE . HTML_NEWLINE. '<a href="'. $file .'" target="_blank"><img src="'. $FileThumb .'" border="0" /></a>';

    } else return HTML_NEWLINE . HTML_NEWLINE. $this->FetchIcon($file, 'download') .' ('. t('Angehängte Datei herunterladen').')';
  }

    function FetchButton($link, $picname, $hint = NULL, $target = NULL) {
    global $lang;

    return $this->FetchSpanButton($lang['button'][$picname], $link, $hint, $target);
/*
        global $templ, $gd;

        if (!$hint) $hint = 'Pic: '. $picname;

        $templ['ls']['linkbutton']['link'] = $link;
        $templ['ls']['linkbutton']['picname'] = $picname;
        $templ['ls']['linkbutton']['hint'] = $hint;
        if ($target) $templ['ls']['linkbutton']['target'] = "target=\"$target\"";
        else $templ['ls']['linkbutton']['target'] = "";

        $gd->CreateButton($picname);

        return $this->FetchModTpl("", "ls_linkbutton");
*/  }

    function FetchCssButton($title, $link, $hint = NULL, $target = NULL) {
    ($hint)? $hint = ' onmouseover="return overlib(\''. t($hint) .'\');" onmouseout="return nd();"' : $hint = '';
    ($target)? $target = ' target="_blank"' : $target = '';
    return '<div class="Button"><a href="'. $link .'"'. $hint .''. $target .'>'. $title .'</a></div>';
    }

    function FetchSpanButton($title, $link, $hint = NULL, $target = NULL) {
    ($hint)? $hint = ' onmouseover="return overlib(\''. t($hint) .'\');" onmouseout="return nd();"' : $hint = '';
    ($target)? $target = ' target="_blank"' : $target = '';
#    return '<a href="'. $link .'"'. $hint .''. $target .'><span class="Button">'. $title .'</span></a> ';
    return '<div class="Buttons" style="display:inline"><a href="'. $link .'"'. $hint .''. $target .'>'. $title .'</a></div>';
    }
  
    function FetchIcon($link, $picname, $hint = NULL, $target = NULL, $align = 'left') {
        global $templ, $gd;

    // Picname-Mappings
    switch ($picname) {
      case 'next': $picname = 'forward'; break;
      case 'preview': $picname = 'search'; break;
    }
    $templ['icon']['name'] = $picname;

    // Hint
    $templ['icon']['title'] = '';
    if ($hint == '') switch ($picname) {
      default: $hint = ''; break;
      case 'add': $hint = t('Hinzufügen'); break;
      case 'change': $hint = t('Ändern'); break;
      case 'edit': $hint = t('Editieren'); break;
      case 'delete': $hint = t('Löschen'); break;
      case 'send': $hint = t('Senden'); break;
    }
    if ($hint) $templ['icon']['title'] = ' onmouseover="return overlib(\''. $hint .'\');" onmouseout="return nd();"';

    $templ['icon']['additionalhtml'] = '';
    if ($align == 'right') $templ['icon']['additionalhtml'] = 'align="right" valign="bottom" vspace="2" ';

    if ($this->form_open) $ret = $this->FetchModTpl('', 'ls_fetch_icon_submit');
    else $ret = $this->FetchModTpl('', 'ls_fetch_icon');
    
        if ($target) $target = " target=\"$target\"";
    if ($link) $ret = '<a href="'.$link.'"'.$target.'>'.$ret.'</a>';
    return $ret;  
    }

    function FetchUserIcon($userid) {
        global $templ, $db;

        $templ['usericon']['userid'] = $userid;
        $templ['usericon']['hint'] = t('Benutzerdetails aufrufen');

        $user_online = $db->qry_first('SELECT 1 AS found FROM %prefix%stats_auth WHERE userid = %int% AND login = "1" AND lasthit > %int%', $userid, time() - 60*10);
    		($user_online['found'])? $templ['usericon']['state'] ='online' : $templ['usericon']['state'] ='offline';

        return $this->FetchModTpl("", "ls_usericon");
    }

    function FetchModTpl($mod, $name) {
        global $templ, $debug;

        if ($mod == "") $return = $this->FetchTpl("design/templates/".$name.".htm", $templ);
        else $return = $this->FetchTpl("modules/".$mod."/templates/".$name.".htm", $templ);

        return $return;
    }

    function SetForm($f_url, $f_name = NULL, $f_method = NULL, $f_enctype = NULL) {
        global $templ;

        if ($f_name == NULL) $f_name = "dsp_form" . $this->formcount++;
        if ($f_method == NULL) $f_method = "POST";

        if ($f_enctype == NULL) $f_enctype = "";
        else $f_enctype = "enctype=\"$f_enctype\"";

        if ($this->form_open) $this->CloseForm();
        $this->form_open = true;

        $this->form_name = $f_name;
        $templ['ls']['row']['formbegin']['name']   = $f_name;
        $templ['ls']['row']['formbegin']['method'] = strtolower($f_method);
        $templ['ls']['row']['formbegin']['action'] = $f_url;
        $templ['ls']['row']['formbegin']['enctype'] = $f_enctype;

        $this->AddTpl("design/templates/ls_row_formbegin.htm");
    }

    function CloseForm() {
        global $templ;

        $this->form_open = false;
        $this->AddTpl("design/templates/ls_row_formend.htm");
    }

    function AddContent($target = NULL) {
    }

    function HelpText($text, $help) {
    return '<span onmouseover="return overlib(\''. t($help) .'\');" onmouseout="return nd();">'. t($text) .'</span>';
    }

  function AddIcon($name, $link = '', $title = '') {
    global $templ;
    
      $templ['ms2']['icon_name'] = $name;
    $templ['ms2']['icon_title'] = $title;
    $templ['ms2']['link_item'] = $this->FetchModTpl('mastersearch2', 'result_icon');
    if ($link) {
      $templ['ms2']['link'] = $link;
      return $this->FetchModTpl('mastersearch2', 'result_link');
    } else return $templ['ms2']['link_item'];
  }
}
?>
