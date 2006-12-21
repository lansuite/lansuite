<?php

class display {

	var $form_line = "";
	var $content_need_form = 0;
	var $form_ok = 0;
	var $form_open = 0;
	var $formcount = 1;
	var $TableOpen = false;
  var $TplCache = array();
  var $errortext_prefix = '';
  var $errortext_suffix = '';

  function display() {
    $this->errortext_prefix = HTML_NEWLINE . HTML_FONT_ERROR;
    $this->errortext_suffix = HTML_FONT_END;
  }
/* Class-Internal Functions*/

	// Returns the template $file
	function FetchTpl($file, $templx = ''){
		global $auth, $language, $cfg, $TplCache, $templ;
		
    #echo "Loading $file<br>";

    if ($this->TplCache[$file] != '') $tpl_str = $this->TplCache[$file];
    else {
  		$handle = fopen ($file, "rb");
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

		if (!$this->TableOpen and $OpenTable) {
			$templ['index']['info']['content'] .= '<table width="100%" cellspacing="0" cellpadding="0">';
			$this->TableOpen = true;
		}

    $templ['index']['info']['content'] .= $this->FetchTpl($file, $templ);
	}


/* External-Functions */

	// Writes the headline of a page
    function NewContent($caption, $header = NULL, $helplet_id = 'help') {
		global $templ, $language;

    if (file_exists("modules/{$_GET['mod']}/docu/{$language}_{$helplet_id}.php")) {
      $templ['ls']['row']['helpletbutton']['helplet_id'] = $helplet_id;
      $templ['ls']['row']['helpletbutton']['help'] = $this->FetchModTpl("", "ls_row_helpletbutton");
    }

		$templ['ls']['case']['caption'] = $caption;
		$templ['ls']['case']['header_text'] = $header;

		unset($this->content_need_form);
		$this->form_ok = false;


		$this->AddTpl("design/templates/ls_row_headline.htm");
	}


	function AddHeaderButtons() {

	}


  function AddHeaderMenu($names, $link, $active = NULL) {
   	global $templ;

    foreach ($names as $key => $name) {
			if ($key == $active and $active != NULL) $am = '';
      else $am = 'class="menu"';
			$templ['ls']['row']['headermenu']['items'] .= "<a href=\"".$link."&headermenuitem=$key\"".$am."><strong>".$name."</strong></a> - ";
    }

  	// Letztes Minus rausschneiden
  	$templ['ls']['row']['headermenu']['items'] = substr($templ['ls']['row']['headermenu']['items'], 0, -3);

		$this->AddTpl("design/templates/ls_row_headermenu.htm");
  }
    
  function AddHeaderMenu2($names, $link, $active = NULL) {
   	global $templ;

    foreach($names as $key => $name) {
      ($key == $active and $active != '')? $am = '' : $am = 'class="menu"';
      $templ['ls']['row']['headermenu']['items'] .= '<a href="'. $link . $key .'"'. $am .'><b>'. $name .'</b></a> - ';
    }
    $templ['ls']['row']['headermenu']['items'] = substr($templ['ls']['row']['headermenu']['items'], 0, -3);
    
    $this->AddTpl("design/templates/ls_row_headermenu.htm");
  }

	function StartHiddenBox($name, $vissible = false) {
		global $templ;

    $templ['ls']['row']['hidden_row']['id'] = $name;

    if ($vissible) $templ['ls']['row']['hidden_row']['display'] = '';
    else $templ['ls']['row']['hidden_row']['display'] = 'none';

		$this->AddTpl("design/templates/ls_row_hiddenbox_start.htm");
	}

	function StopHiddenBox() {
		global $templ;
		
		$this->AddTpl("design/templates/ls_row_hiddenbox_stop.htm");
	}


	function AddSingleRow($text, $parm = NULL) {
		global $templ;

		$templ['ls']['row']['single']['text'] = stripslashes($text);

		if ($parm != "") $templ['ls']['row']['single']['align'] = $parm;

		$this->AddTpl("design/templates/ls_row_single.htm");
	}

	function AddDoubleRow($key, $value, $id = NULL) {
		global $templ;

		if ($key == "") $key = "&nbsp;";
		if ($value == "") $value = "&nbsp;";
		if ($id == "") $id = "DoubleRowVal";
		$templ['ls']['row']['double']['key'] = stripslashes($key);
		$templ['ls']['row']['double']['value'] = stripslashes($value);
		$templ['ls']['row']['double']['id'] = $id;

		$this->AddTpl("design/templates/ls_row_double.htm");
	}


	function AddCheckBoxRow($name, $key, $text, $errortext, $optional = NULL, $checked = NULL, $disabled = NULL, $val = NULL, $additionalHTML = NULL) {
		global $templ;

		$templ['ls']['row']['checkbox']['name'] = $name;
		$templ['ls']['row']['checkbox']['key'] = $key;
		$templ['ls']['row']['checkbox']['text'] = $text;
		$templ['ls']['row']['additionalHTML'] = $additionalHTML;

		if($checked) $templ['ls']['row']['checkbox']['checked'] = "checked";
		else $templ['ls']['row']['checkbox']['checked'] = "";
		if($disabled) $templ['ls']['row']['checkbox']['disabled'] = "disabled";
		else $templ['ls']['row']['checkbox']['disabled'] = "";

		if($val == "") $templ['ls']['row']['checkbox']['val'] = "1";
		else $templ['ls']['row']['checkbox']['val'] = $val;

		if ($errortext) $templ['ls']['row']['checkbox']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
		else $templ['ls']['row']['checkbox']['errortext'] = '';
		if ($optional) $templ['ls']['row']['checkbox']['optional'] = "_optional";
		else $templ['ls']['row']['checkbox']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_checkbox.htm");
	}


	function AddRadioRow($name, $key, $val, $errortext = NULL, $optional = NULL, $checked = NULL, $disabled = NULL) {
		global $templ;

		$templ['ls']['row']['radio']['name'] = $name;
		$templ['ls']['row']['radio']['key'] = $key;
		$templ['ls']['row']['radio']['val'] = $val;

		if($checked) $templ['ls']['row']['radio']['checked'] = "selected";
		else $templ['ls']['row']['radio']['checked'] = "";
		if($disabled) $templ['ls']['row']['radio']['disabled'] = "disabled";
		else $templ['ls']['row']['radio']['disabled'] = "";

		if ($errortext) $templ['ls']['row']['radio']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
		else $templ['ls']['row']['radio']['errortext'] = '';
		if ($optional) $templ['ls']['row']['radio']['optional'] = "_optional";
		else $templ['ls']['row']['radio']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_radio.htm");
	}


	function AddTextFieldRow($name, $key, $value, $errortext, $size = NULL, $optional = NULL) {
		global $templ;

		if($size == "") $size = "30";

		$templ['ls']['row']['textfield']['errortext'] = '';

		$templ['ls']['row']['textfield']['name'] = $name;
		$templ['ls']['row']['textfield']['key'] = $key;
		$templ['ls']['row']['textfield']['value'] = stripslashes($value);
		$templ['ls']['row']['textfield']['size'] = $size;

    if ($errortext) $templ['ls']['row']['textfield']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['textfield']['errortext'] = ''; 
		if ($optional) $templ['ls']['row']['textfield']['optional'] = "_optional";
		else $templ['ls']['row']['textfield']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_textfield.htm");
	}


	function AddPasswordRow($name, $key, $value, $errortext, $size = NULL, $optional = NULL, $additional = NULL) {
		global $templ;

		if($size == "") $size = "30";

		$templ['ls']['row']['textfield']['name'] = $name;
		$templ['ls']['row']['textfield']['key'] = $key;
		$templ['ls']['row']['textfield']['value'] = stripslashes($value);
		$templ['ls']['row']['textfield']['size'] = $size;

    if ($errortext) $templ['ls']['row']['textfield']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['textfield']['errortext'] = '';
		if ($optional) $templ['ls']['row']['textfield']['optional'] = "_optional";
		else $templ['ls']['row']['textfield']['optional'] = '';
		
		$templ['ls']['row']['textfield']['additional'] = $additional;

		$this->AddTpl("design/templates/ls_row_password.htm");
	}


	function AddTextAreaRow($name, $key, $value, $errortext, $cols = NULL, $rows = NULL, $optional = NULL) {
		global $templ;

		if($cols == "") $cols = "50";
		if($rows == "") $rows = "7";

		$templ['ls']['row']['textarea']['name'] = $name;
		$templ['ls']['row']['textarea']['key'] = $key;
		$templ['ls']['row']['textarea']['value'] = stripslashes($value);
		$templ['ls']['row']['textarea']['cols'] = $cols;
		$templ['ls']['row']['textarea']['rows'] = $rows;

    if ($errortext) $templ['ls']['row']['textarea']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['textarea']['errortext'] = '';
		if ($optional) $templ['ls']['row']['textarea']['optional'] = "_optional";
		else $templ['ls']['row']['textarea']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_textarea.htm");
	}

    function AddTextAreaPlusRow($name, $key, $value, $errortext, $cols = NULL, $rows = NULL, $optional = NULL, $maxchar = NULL) {
	global $templ, $db;

		if ($cols == "") $cols = "50";
        if ($rows == "") $rows = "7";
        if ($maxchar == "") $maxchar = "5000";

		$templ['ls']['row']['textarea']['name'] = $name;
        $templ['ls']['row']['textarea']['formname'] = $this->form_name;
        $templ['ls']['row']['textarea']['key'] = $key;
        $templ['ls']['row']['textarea']['value'] = stripslashes($value);
        $templ['ls']['row']['textarea']['cols'] = $cols;
        $templ['ls']['row']['textarea']['rows'] = $rows;
		$templ['ls']['row']['textarea']['maxchar'] = $maxchar;

    if ($errortext) $templ['ls']['row']['textarea']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['textarea']['errortext'] = '';
    if ($optional) $templ['ls']['row']['textarea']['optional'] = "_optional";
    else $templ['ls']['row']['textarea']['optional'] = '';

		$templ['ls']['row']['textarea']['buttons'] = $this->FetchButton("javascript:InsertCode(document.{$this->form_name}.{$name}, '[b][/b]')", "bold");
		$templ['ls']['row']['textarea']['buttons'] .= " ". $this->FetchButton("javascript:InsertCode(document.{$this->form_name}.{$name}, '[i][/i]')", "kursiv");
		$templ['ls']['row']['textarea']['buttons'] .= " ". $this->FetchButton("javascript:InsertCode(document.{$this->form_name}.{$name}, '[u][/u]')", "underline");
		$templ['ls']['row']['textarea']['buttons'] .= " ". $this->FetchButton("javascript:InsertCode(document.{$this->form_name}.{$name}, '[c][/c]')", "code");
		$templ['ls']['row']['textarea']['buttons'] .= " ". $this->FetchButton("javascript:InsertCode(document.{$this->form_name}.{$name}, '[img][/img]')", "picture");

		$templ['ls']['row']['textarea']['smilies'] = "";
		$smilie = $db->query("SELECT shortcut, image FROM {$GLOBALS["config"]["tables"]["smilies"]}");

		$z = 0;
		while($smilies = $db->fetch_array($smilie)){
			if (file_exists("ext_inc/smilies/" . $smilies["image"])) {
				$templ['ls']['row']['textarea']['smilies'] .= "<a href=\"#\" onclick=\"javascript:code_".$templ['ls']['row']['textarea']['name']."('" . $smilies["shortcut"] . " ')\"><img src=\"ext_inc/smilies/" . $smilies["image"] . "\" border=\"0\" alt=\"". $smilies["image"] . "\" /></a>\n";
				$z++;
				if ($z % 12 == 0) $templ['ls']['row']['textarea']['smilies'] .= "<br />";
			}
		}

		$this->AddTpl("design/templates/ls_row_textareaplus.htm");
    }



	function AddDropDownFieldRow($name, $key, $option_array, $errortext, $optional = NULL, $additionalHTML = NULL) {
		global $templ;

		$templ['ls']['row']['dropdown']['name'] = $name;
		$templ['ls']['row']['dropdown']['key'] = $key;
		$templ['ls']['row']['dropdown']['options'] = implode('', $option_array);
		$templ['ls']['row']['additionalHTML'] = $additionalHTML;

    if ($errortext) $templ['ls']['row']['dropdown']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['dropdown']['errortext'] = '';
		if ($optional) $templ['ls']['row']['dropdown']['optional'] = "_optional";
		else $templ['ls']['row']['dropdown']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_dropdown.htm");
	}


	function AddFieldsetStart($name) {
		global $templ;

		$this->AddContent();
    $templ['ls']['row']['fieldset']['name'] = $name;
		$this->AddTpl("design/templates/ls_row_fieldset_start.htm", 0);
	}
	
	function AddFieldsetEnd() {
		global $templ;

		$this->AddContent();
		$this->AddTpl("design/templates/ls_row_fieldset_end.htm", 0);
	}



	function AddSelectFieldRow($name, $key, $option_array, $errortext, $optional = NULL, $size = NULL) {
		global $templ;

		$templ['ls']['row']['dropdown']['name'] = $name;
		$templ['ls']['row']['dropdown']['key'] = $key;
		$templ['ls']['row']['dropdown']['options'] = implode("", $option_array);

    if ($errortext) $templ['ls']['row']['dropdown']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['dropdown']['errortext'] = ''; 
		if ($optional) $templ['ls']['row']['dropdown']['optional'] = "_optional";
		else $templ['ls']['row']['dropdown']['optional'] = '';
		
		($size) ? $templ['ls']['row']['dropdown']['size'] = $size : $templ['ls']['row']['dropdown']['size'] = 4;

		$this->AddTpl("design/templates/ls_row_select.htm");
	}



	function AddFormSubmitRow($button, $helplet_id = NULL, $var = false, $close = true) {
		global $templ, $gd;

//		if ($helplet_id) {
//			$templ['ls']['row']['helpletbutton']['helplet_id'] = $helplet_id;
//			$templ['ls']['row']['helpletbutton']['help'] = $this->FetchModTpl("", "ls_row_helpletbutton");
//		} else {
			$templ['ls']['row']['helpletbutton']['helplet_id'] = "";
			$templ['ls']['row']['helpletbutton']['help'] = "&nbsp;";
//		}

		$templ['ls']['row']['formsubmit']['button'] = $button;
		
		if (!$var) $templ['ls']['row']['formsubmit']['buttonname'] = "imageField";
		else $templ['ls']['row']['formsubmit']['buttonname'] = $var;
		
		$gd->CreateButton("$button");

		$this->AddTpl("design/templates/ls_row_formsubmit.htm");

        if ($this->form_open && $close) $this->CloseForm();
	}
	

  function AddBackButton($back_link, $helplet_id = NULL) {
    global $templ, $gd, $auth;

    $gd->CreateButton("back");
    $templ['ls']['row']['backbutton']['back_link'] = $back_link;    
    $this->AddTpl("design/templates/ls_row_backbutton.htm");
  }

    
    function AddBarcodeForm($key, $value, $action, $methode = "post", $errortext = NULL,  $size = NULL, $optional = NULL){
		global $templ;

		if($size == "") $size = "30";

		$templ['ls']['row']['textfield']['key'] = $key;
		$templ['ls']['row']['textfield']['value'] = $value;
		$templ['ls']['row']['textfield']['size'] = $size;
		
		$templ['ls']['row']['formbegin']['action'] = $action;
		$templ['ls']['row']['formbegin']['method'] = $methode;

    if ($errortext) $templ['ls']['row']['textfield']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['textfield']['errortext'] = '';
		if ($optional) $templ['ls']['row']['textfield']['optional'] = "_optional";
		else $templ['ls']['row']['textfield']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_barcode.htm");
		$this->AddContent();
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

		for ($x = 1; $x <= 31; $x++) {
			($x < 10) ? $y = "0".$x : $y = $x;
			($day == $x)? $selected = "selected" : $selected = "";
			$templ['ls']['row']['datetime']['value']['day'] .= "<option value=\"$x\" $selected>$y</option>";
		}
		for ($x = 1; $x <= 12; $x++) {
			($x < 10) ? $y = "0".$x : $y = $x;
			($month == $x)? $selected = "selected" : $selected = "";
			$templ['ls']['row']['datetime']['value']['month'] .= "<option value=\"$x\" $selected>$y</option>";
		}
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

		$this->AddTpl("design/templates/ls_row_datetime.htm");
    }


    function AddHRuleRow() {
		$this->AddTpl("design/templates/ls_row_hrule.htm");
    }


    function AddPictureDropDownRow($name, $key, $path, $errortext, $optional = NULL, $selected = NULL) {
        global $templ;

        $file_out[] = "<option value=\"none\">None</option>";

        $handle = @opendir($path);
        while($file = @readdir ($handle))
        {

        	if( ($file != ".") AND ($file != ".."))
            {
             	$extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
                if (($extension == "jpeg") || ($extension == "jpg") || ($extension == "png") || ($extension == "gif"))
                {
                   	if($file == $selected) { $file_out[] = "<option value=\"".$file."\" selected>".$file."</option>"; }
					else { $file_out[] = "<option value=\"".$file."\">".$file."</option>"; }
                }
            }

		 }
         @closedir($handle);


        $templ['ls']['row']['picdropdown']['name'] = $name;
        $templ['ls']['row']['picdropdown']['key'] = $key;
        $templ['ls']['row']['picdropdown']['options'] = implode("", $file_out);
		$templ['ls']['row']['picdropdown']['path'] = $path."/";

        if ($errortext) $templ['ls']['row']['picdropdown']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        else $templ['ls']['row']['picdropdown']['errortext'] = '';
        if ($optional) $templ['ls']['row']['picdropdown']['optional'] = "_optional";
        else $templ['ls']['row']['picdropdown']['optional'] = '';
        if ($selected AND $selected != "none") $templ['ls']['row']['picdropdown']['picpreview_init'] = $path."/".$selected;
        else $templ['ls']['row']['picdropdown']['picpreview_init'] = "design/standard/images/index_transparency.gif";

		$this->AddTpl("design/templates/ls_row_picdropdown.htm");
    }






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
		global $templ;

		if($size == "") { $size = "30"; }

		$templ['ls']['row']['file']['name'] = $name;
		$templ['ls']['row']['file']['key'] = $key;
		$templ['ls']['row']['file']['size'] = $size;
		$templ['ls']['row']['file']['maxlength'] = $maxlength;

    if ($errortext) $templ['ls']['row']['file']['errortext'] = $this->errortext_prefix . $errortext . $this->errortext_suffix;
    else $templ['ls']['row']['file']['errortext'] = '';
		if ($optional) $templ['ls']['row']['file']['optional'] = "_optional";
		else $templ['ls']['row']['file']['optional'] = '';

		$this->AddTpl("design/templates/ls_row_fileselect.htm");
	}
	
	/*
	 * This function shows any URL in an Frame in the MoudleContent.
	 */
    function AddIFrame($url, $width=795, $height=600)
    {
	  global $lang, $templ, $func;
	  
	  $templ["class_display"]["IFrame"]["noIFrame"] .= $lang['class_display']['IFrame']['noIFrame'];
	  $templ["class_display"]["IFrame"]["clickhere"] .= $lang['class_display']['clickhere'];
	  $templ["class_display"]["IFrame"]["url"] = 'http://' . $url;
	  $templ["class_display"]["IFrame"]["width"] = $width;
	  $templ["class_display"]["IFrame"]["height"] = $height;
	  
      $this->AddSingleRow($this->FetchModTpl("", "ls_row_IFrame"));
    }
    
	/*
	 * This function shows an URL in an new PopUp Page.
	 */
    function ShowNewWindow($url)
    {
	  global $lang, $templ;
	  
	  $templ["class_display"]["NewWindow"]["popupBlocked"] .= $lang['class_display']['newWindow']['popupBlocked'];
	  $templ["class_display"]["NewWindow"]["clickhere"] .= $lang['class_display']['clickhere'];
	  $templ["class_display"]["NewWindow"]["url"] = 'http://' . $url;
      $this->AddSingleRow($this->FetchModTpl("", "ls_row_newWindow"));
    }


	// ################################################################################################################# //

	function AddModTpl($mod, $name) {
		global $templ, $debug;
		
		if ($mod == "") $return = $this->AddTpl("design/templates/".$name.".htm");
		else $return = $this->AddTpl("modules/".$mod."/templates/".$name.".htm");
	}


	function FetchButton($link, $picname, $hint = NULL, $target = NULL) {
		global $templ, $gd;

		if (!$hint) $hint = 'Pic: '. $picname;

		$templ['ls']['linkbutton']['link'] = $link;
		$templ['ls']['linkbutton']['picname'] = $picname;
		$templ['ls']['linkbutton']['hint'] = $hint;
		if ($target) $templ['ls']['linkbutton']['target'] = "target=\"$target\"";
		else $templ['ls']['linkbutton']['target'] = "";

		$gd->CreateButton($picname);

		return $this->FetchModTpl("", "ls_linkbutton");
	}

	function FetchUserIcon($userid) {
		global $templ;

		$templ['usericon']['userid'] = $userid;
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
		global $templ, $auth, $language;

		if ($this->TableOpen) {
      $templ['index']['info']['content'] .= '</table>';
  		$this->TableOpen = false;
  	}
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
