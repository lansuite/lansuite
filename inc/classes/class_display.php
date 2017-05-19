<?php

class display
{
    public $form_open = 0;
    public $formcount = 1;
    public $errortext_prefix = '';
    public $errortext_suffix = '';
    public $FirstLine = 1;
    public $CurrentTab = 0;
    public $TabsMainContentTmp = '';
    public $tabNames = array();

  // Constructor
    public function __construct()
    {
        $this->errortext_prefix = HTML_NEWLINE . HTML_FONT_ERROR;
        $this->errortext_suffix = HTML_FONT_END;
    }

  #### Main Functions ####

  /* When handling own templates, the prefered way is to use $smarty->fetch()
  / However, at some point, you have to append these fetched content to $MainContent
  / But: Never write to $MainContent within your module!
  / Instead: Use either $dsp->AddSmartyTpl(), or $dsp->AddContentLine()
  / to attach your content to the LS-output.
  */

  // Adds a smarty template.
  // Attention: This does not add the LS-line-container, so you have to take care of it yourselfe!
    public function AddSmartyTpl($name, $mod = '')
    {
        global $smarty, $MainContent;

        if ($mod == '') {
            $MainContent .= $smarty->fetch('design/templates/'. $name .'.htm');
        } else {
            $MainContent .= $smarty->fetch('modules/'. $mod .'/templates/'. $name .'.htm');
        }
    }

  // Adds the provided content in a new LS-line
    public function AddContentLine($content)
    {
        global $smarty, $MainContent;

    #    if ($_GET['design'] != 'base') {
        if ($this->FirstLine) {
            $smarty->assign('content', $content);
            $MainContent .= $smarty->fetch('design/templates/ls_row_firstline.htm');
            $this->FirstLine = 0;
        } else {
            $smarty->assign('content', $content);
            $MainContent .= $smarty->fetch('design/templates/ls_row_line.htm');
        }
    #    }
    }

  #### Add content ####
  # The following functions all printing their content directly, to the LS-content-container

  // Writes the headline of a page
    public function NewContent($caption, $text = null, $helplet_id = 'help')
    {
        global $smarty, $language;

        if (file_exists('modules/'. $_GET['mod'] .'/docu/'. $language .'_'. $helplet_id .'.php')) {
            $smarty->assign('helplet_id', $helplet_id);
        }
        $smarty->assign('mod', $_GET['mod']);
        $smarty->assign('newcontent_caption', $caption);
        $smarty->assign('newcontent_text', $text);

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_headline.htm'));
    }

    public function StartTabs()
    {
        global $MainContent;
    
        $this->TabsMainContentTmp = $MainContent;
        $MainContent = '';
    }

    public function StartTab($name, $icon = '')
    {
        global $MainContent;

        if ($icon) {
            $name = '<img src="design/images/icon_'. $icon .'.png" height="14" alt="'. $icon .'" border=\"0\" /> '. $name;
        }
        $this->TabNames[] = $name;
        $MainContent .= '<div id="tabs-'. (int)$this->CurrentTab .'">';
        $this->CurrentTab++;
    }

    public function EndTab()
    {
        global $MainContent;
        $MainContent .= '</div>';
    }

    public function EndTabs()
    {
        global $MainContent, $framework;

        $this->AddSingleRow('');
        $out = $this->TabsMainContentTmp;

        foreach ($this->TabNames as $key => $name) {
            $items .= '<li><a href="#tabs-'. $key .'">'. $name .'</a></li>';
        }
        $out .= '<div id="tabs"><ul>'. $items .'</ul>';

        ($_GET['tab'])? $sel = '{ selected: '. (int)$_GET['tab'] .' }' : $sel = '';
        $framework->add_js_code('$(function() {
	   $("#tabs").tabs('. $sel .');
    });');

        $out .= $MainContent .'</div>';
        $MainContent = $out;
    }

    public function AddHeaderMenu($names, $link, $active = null)
    {
        global $templ, $MainContent;

        foreach ($names as $key => $name) {
            if ($key == $active and $active != null) {
                $items .= '<span class="HeaderMenuItemActive">'. $name .'</span>';
            } else {
                $items .= '<span class="HeaderMenuItem"><a href="'. $link .'&headermenuitem='. $key .'">'. $name .'</a></span>';
            }
        }

        $MainContent .= $items;
    }
    
    public function AddHeaderMenu2($names, $link, $active = null)
    {
        global $templ, $MainContent;

        foreach ($names as $key => $name) {
            ($key == $active and $active != '')? $am = '' : $am = 'class="menu"';
            $items .= '<a href="'. $link . $key .'"'. $am .'><b>'. $name .'</b></a> - ';
        }
        $items = substr($items, 0, -3);
    
        $MainContent .=  $items;
    }
  
  // dynamic = 1 --> Jquery-Javascript-Tabs
  // dynamic = 0 --> static tabs without Jquery & Javascript
    public function AddJQueryTabsStart($id, $dynamic = null)
    {
        global $MainContent, $framework;
    
        if ($dynamic) {
            $framework->add_js_code("
				$(document).ready(function(){
					$('#".$id."').tabs({
    					click: function(tab) {
        					location.href = $.data(tab, 'href');
        					return false;
    					}
					});
				});");
        }
    
        $MainContent .= "<div class='ui-tabs ui-widget ui-widget-content ui-corner-all' id='".$id."'>\n";
        $this->FirstLine = 1;
    }
  
    public function AddJQueryTabNavStart()
    {
        global $MainContent;
        
        $MainContent .= "  <ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>\n";
        $this->FirstLine = 1;
    }
  
  // In case of dynamic Jquery-Javascript-Tabs selected is set automaticly
  // In case of static-tabs, selected has to be set 0 or 1
    public function AddJQueryTab($content, $link, $title = null, $selected = null)
    {
        global $MainContent;
        
    #       if($selected) $additional = " ui-tabs-selected ui-state-active";
    #       $MainContent .= "    <li class='ui-state-default ui-corner-top".$additional."'><a href='".$link."' title='".$title."'><em>".$content."</em></a></li>\n";
        $this->FirstLine = 1;
    }
  
    public function AddJQueryTabNavStop()
    {
        global $MainContent;
    
        $MainContent .= "  </ul>\n";
        $this->FirstLine = 1;
    }
  
    public function AddJQueryTabContentStart()
    {
        global $MainContent;
    
        $MainContent .= "  <div class='ui-content'>\n";
    }
  
    public function AddJQueryTabContent($id, $content)
    {
        global $MainContent;
    
        $MainContent .= "    <div id='".$id."'>\n";
        $MainContent .= "      ".$content."\n";
        $MainContent .= "    </div>\n";
    }
    
    public function AddJQueryTabContentStop()
    {
        global $MainContent;
    
        $MainContent .= "  </div>\n";
    }
  
    public function AddJQueryTabsStop()
    {
        global $MainContent;
    
        $MainContent .= "</div>\n";
    }
  
    public function StartHiddenBox($name, $vissible = false)
    {
        global $templ, $MainContent;

        ($vissible)? $vissible = '' : $vissible = 'none';
        $MainContent .=  '<div id="'. $name .'" style="display:'. $vissible .'">';
    }

    public function StopHiddenBox()
    {
        global $templ, $MainContent;
        
        $MainContent .=  '</div>';
    }
/* Please note that there is no escaping of $value in the following functions.
    Data taken from the DB, $_GET and $_POST is already sanitized by $func-NoHTML().
    !!!IF YOUR INPUT COMES FROM A DIFFERENT SOURCE, MAKE SURE TO ESCAPE THE DATA YOURSELF!!!
*/
    public function AddSingleRow($text, $parm = null, $class = '')
    {
        global $smarty;

        $smarty->assign('text', $text);
        if ($parm != "") {
            $smarty->assign('align', $parm);
        }
        if ($class != "") {
            $smarty->assign('class', 'class="'. $class .'"');
        }
        $this->AddContentLine($smarty->fetch('design/templates/ls_row_single.htm'));
    }

    public function AddDoubleRow($key, $value, $id = null)
    {
        global $smarty;

        if ($key == "") {
            $key = "&nbsp;";
        }
        if ($value == "") {
            $value = "&nbsp;";
        }
        if ($id == "") {
            $id = "DoubleRowVal";
        }

        $smarty->assign('key', $key);
        $smarty->assign('value', $value);
        $smarty->assign('id', $id);

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_double.htm'));
    }

    public function AddTripleRow($key, $value, $id = null, $ext_txt)
    {
        global $smarty;

        if ($key == "") {
            $key = "&nbsp;";
        }
        if ($value == "") {
            $value = "&nbsp;";
        }
        if ($ext_txt == "") {
            $value = "&nbsp;";
        }
        if ($id == "") {
            $id = "DoubleRowVal";
        }

        $smarty->assign('key', $key);
        $smarty->assign('value', $value);
        $smarty->assign('id', $id);
        $smarty->assign('ls_triplerow_ext', $ext_txt);

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_triple.htm'));
    }

    public function AddCheckBoxRow($name, $key, $text, $errortext, $optional = null, $checked = null, $disabled = null, $val = null, $additionalHTML = null)
    {
        ($checked)? $checked = 'checked' : $checked = '';
        ($disabled)? $disabled = 'disabled' : $disabled = '';
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        if ($val == '') {
            $val = '1';
        }

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<input id="'. $name .'" name="'. $name .'" type="checkbox" class="checkbox" value="'. $val .'" '. $checked .' '. $disabled .' '. $additionalHTML .' />';
        $value .= '<label for="'. $name .'">'. $text .'</label>'. $errortext;
        $this->AddDoubleRow($key, $value);
    }

    public function AddRadioRow($name, $key, $val, $errortext = null, $optional = null, $checked = null, $disabled = null)
    {
        ($checked)? $checked = 'checked="checked"' : $checked = '';
        ($disabled)? $disabled = 'disabled' : $disabled = '';
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';

        $value = '<input name="'. $name .'" type="radio" class="form'. $optional .'" value="'. $val .'" '. $checked .' '. $disabled .' />'. $errortext;
        $key = '<label for="'. $name .'">'. $key .'</label>';
        $this->AddDoubleRow($key, $value);
    }

    public function AddTextFieldRow($name, $key, $value, $errortext, $size = null, $optional = null, $not_changeable = null, $maxlength = null)
    {
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        ($not_changeable)? $not_changeable = ' readonly="readonly"' : $not_changeable = '';
        if ($maxlength) {
            $maxlength = ' maxlength="'. $maxlength .'"';
        }
        if ($size == '') {
            $size = '30';
        }

        $value = '<input type="text" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" size="'. $size .'"'. $not_changeable .' value="'. $value .'"'. $maxlength .' />'. $errortext;
        $key = '<label for="'. $name .'">'. $key .'</label>';
        $this->AddDoubleRow($key, $value);
    }

    public function AddPasswordRow($name, $key, $value, $errortext, $size = null, $optional = null, $additional = null)
    {
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        if ($size == '') {
            $size = '30';
        }

        $value = '<input type="password" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" size="'. $size .'" value="'. $value .'" '. $additional .' />'. $errortext;
        $key = '<label for="'. $name .'">'. $key .'</label>';
        $this->AddDoubleRow($key, $value);
    }

    public function AddTableRow($table)
    {
        global $func, $smarty;

        $rows = '';
        if (!is_array($table)) {
            $func->error(t('AddTableRow: First argument needs to be array'));
        } else {
            foreach ($table as $y => $row) {
                $cells = '';
                if (!is_array($row)) {
                    $func->error(t('AddTableRow: First argument needs to be 2-dimension-array'));
                } else {
                    foreach ($row as $x => $cell) {
                        if ($cell['link']) {
                            $cell['text'] = $this->FetchLink($cell['text'], $cell['link'], '', $cell['link_target']);
                        }
                        $smarty->assign('content', $cell['text']);
                        $cells .= $smarty->fetch('design/templates/ls_row_table_cells.htm');
                    }
                }
                $smarty->assign('cells', $cells);
                $rows .= $smarty->fetch('design/templates/ls_row_table_rows.htm');
            }
        }

        $smarty->assign('rows', $rows);
        $this->AddSingleRow($smarty->fetch('design/templates/ls_row_table.htm'));
    }

    public function AddTextAreaMailRow($name, $key, $value, $errortext, $cols = null, $rows = null, $optional = null, $maxchar = null)
    {
        if ($cols == "") {
            $cols = "50";
        }
        if ($rows == "") {
            $rows = "7";
        }
        if ($maxchar == "") {
            $maxchar = "5000";
        }

        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';

        $key = '<label for="'. $name .'">'. $key .'</label>
      <br />
      <br />
      <br />
      <a href="index.php?mod=popups&action=ls_row_textareamail_popup&design=popup&form='. $this->form_name .'&textarea='. $name .'" onclick="OpenWindow(this.href, \'TextFormatSelect\'); return false">Variablen einfügen</a>';
        $value = '<textarea name="'. $name .'" id="'. $name .'" class="form'. $name .'" cols="'. $cols .'" rows="'. $rows .'" onKeyUp="TextAreaPlusCharsLeft(this, document.'. $this->form_name .'.'. $name .'_chr, '. $maxchar .'); AddaptTextAreaHeight(this)">'. $value .'</textarea>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    public function AddTextAreaRow($name, $key, $value, $errortext, $cols = null, $rows = null, $optional = null)
    {
        if ($cols == "") {
            $cols = "50";
        }
        if ($rows == "") {
            $rows = "7";
        }
        if ($maxchar == "") {
            $maxchar = "5000";
        }

        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<textarea name="'. $name .'" id="'. $name .'" class="form'. $name .'" cols="'. $cols .'" rows="'. $rows .'" onKeyUp="AddaptTextAreaHeight(this)">'. $value .'</textarea>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    public function AddTextAreaPlusRow($name, $key, $value, $errortext, $cols = null, $rows = null, $optional = null, $maxchar = null)
    {
        global $smarty;

        if ($rows == "") {
            $rows = "7";
        }
        if ($maxchar == "") {
            $maxchar = "5000";
        }

        $this->form_open = false;
        $buttons = $this->FetchSpanButton(t('Vorschau'), 'index.php?mod=popups&action=textareaplus_preview&design=popup&textareaname='. $name .'" onclick="javascript:OpenPreviewWindow(this.href, document.'. $this->form_name .'); return false;');
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[b]', '[/b]')", 'bold', t('Fett'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[i]', '[/i]')", 'italic', t('Kursiv'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[u]', '[/u]')", 'underline', t('Unterstrichen'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[s]', '[/s]')", 'strike', t('Durchstreichen'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[sub]', '[/sub]')", 'sub', t('Tiefstellen'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[sup]', '[/sup]')", 'sup', t('Hochstellen'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[c]', '[/c]')", 'quote', t('Code'));
        $buttons .= " ". $this->FetchIcon("javascript:InsertCode(document.{$this->form_name}.{$name}, '[img]', '[/img]')", 'img', t('Bild'));
        $this->form_open = true;
        $smarty->assign('buttons', $buttons);

        $smarty->assign('name', $name);
        $smarty->assign('key', $key);
        $smarty->assign('maxchar', $maxchar);
        $smarty->assign('formname', $this->form_name);
        $smarty->assign('value', $value);
        $smarty->assign('rows', $rows);

        if ($errortext) {
            $smarty->assign('errortext', $this->errortext_prefix . $errortext . $this->errortext_suffix);
        }
        if ($optional) {
            $smarty->assign('optional', '_optional');
        }

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_textareaplus.htm'));
    }

    public function AddDropDownFieldRow($name, $key, $option_array, $errortext, $optional = null, $additionalHTML = null)
    {
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

    public function AddFieldsetStart($name)
    {
        global $MainContent;
    
        $MainContent .=  '<br /><fieldset width="100%" style="clear:left; width:100%"><legend><b>'. $name .'</b></legend>';
        $this->FirstLine = 1;
    }

    public function AddFieldsetEnd()
    {
        global $MainContent;
    
        $MainContent .=  '</fieldset>';
        $this->FirstLine = 1;
    }

    public function AddSelectFieldRow($name, $key, $option_array, $errortext, $optional = null, $size = null)
    {
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        ($option_array)? $options = implode('', $option_array) : $options = '';
        if (!$size) {
            $size = 4;
        }

    // TODO: If no <option> in $options generate from array

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<select name="'. $name .'[]" class="form'. $optional .'" size="'. $size .'" multiple>';
        $value .= $options;
        $value .= '</select>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    public function AddFormSubmitRow($text, $close = true, $name = "imageField")
    {
        $this->AddDoubleRow('&nbsp;', '<input type="submit" class="Button" name="'. $name .'" value="'. $text .'" />');
        if ($this->form_open and $close) {
            $this->CloseForm();
        }
    }

    public function AddBackButton($back_link = null, $helplet_id = null)
    {
        global $func;

        if (!$back_link) {
            $back_link = $func->internal_referer;
        }
        $this->AddDoubleRow('', $this->FetchSpanButton(t('Zurück'), $back_link));
    }

    public function AddBarcodeForm($key, $value, $action, $method = "post", $errortext = null, $size = null, $optional = null)
    {
        if ($size == '') {
            $size = '30';
        }
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

    public function AddDateTimeRow($name, $key, $time, $errortext, $values = null, $disableds = null, $start_year = null, $end_year = null, $hidetime = null, $optional = null, $additional = null)
    {
        global $smarty, $framework;

        $smarty->assign('name', $name);
        $smarty->assign('key', $key);
        $smarty->assign('additional', $additional);
        if ($optional) {
            $smarty->assign('optional', '_optional');
        }

    // IF timestamp
        if ($time > 0) {
            $day = date("d", $time);
            $month = date("m", $time);
            $year = date("Y", $time);
            $hour = date("H", $time);
            $min = date("i", $time);
            // IF values
        } elseif ($values['day'] != "" and $values['month'] != "" and $values['year'] != "") {
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
        $smarty->assign('day', $day);
        $smarty->assign('month', $month);
        $smarty->assign('year', $year);
        $smarty->assign('hour', $hour);
        $smarty->assign('min', $min);

        $arr = array();
        for ($x = 0; $x <= 55; $x+=5) {
            $arr[$x] = $x;
        }
        $smarty->assign('mins', $arr);

        $arr = array();
        for ($x = 0; $x <= 23; $x++) {
            $arr[$x] = $x;
        }
        $smarty->assign('hours', $arr);

        $arr = array();
        for ($x = 1; $x <= 31; $x++) {
            $arr[$x] = $x;
        }
        $smarty->assign('days', $arr);

        $arr = array();
        for ($x = 1; $x <= 12; $x++) {
            $arr[$x] = $x;
        }
        $smarty->assign('months', $arr);

        if ($start_year == "") {
            $start_year = -1;
        }
        if ($end_year == "") {
            $end_year = 5;
        }
        $start_year = date("Y") + $start_year;
        $end_year = date("Y") + $end_year;
        $arr = array();
        for ($x = $start_year; $x <= $end_year; $x++) {
            $arr[$x] = $x;
        }
        $smarty->assign('years', $arr);

        if (isset($disableds['min']) and $disableds['min']) {
            $smarty->assign('dis_min', 'disabled=disabled');
        }
        if (isset($disableds['hour']) and $disableds['hour']) {
            $smarty->assign('dis_hour', 'disabled=disabled');
        }
        if (isset($disableds['day']) and $disableds['day']) {
            $smarty->assign('dis_day', 'disabled=disabled');
        }
        if (isset($disableds['month']) and $disableds['month']) {
            $smarty->assign('dis_month', 'disabled=disabled');
        }
        if (isset($disableds['year']) and $disableds['year']) {
            $smarty->assign('dis_year', 'disabled=disabled');
        }

        if ($errortext) {
            $smarty->assign('errortext', $this->errortext_prefix . $errortext . $this->errortext_suffix);
        }

    // 0 =  All visible / 1 = Hide Time / 2 = Hide Date
        if ($hidetime != 1) {
            $smarty->assign('showtime', '1');
        }
        if ($hidetime != 2) {
            $smarty->assign('showdate', '1');
        }

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_datetime.htm'));

/*
    // Experiment mit JQueryUI Datepicker

    ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
    ($optional)? $optional = "_optional" : $optional = '';
    ($not_changeable)? $not_changeable = ' readonly="readonly"' : $not_changeable = '';
    if ($maxlength) $maxlength = ' maxlength="'. $maxlength .'"';
    if ($size == '') $size = '30';

    $framework->add_js_code('$(function() {
        $("#datepicker").datepicker();
    });');

    $value = '<input type="text" id="datepicker" name="'. $name .'" class="form'. $optional .'" size="'. $size .'"'. $not_changeable .' value="'. $value .'"'. $maxlength .' />'. $errortext;
    $key = '<label for="'. $name .'">'. $key .'</label>';
    $this->AddDoubleRow($key, $value);
*/
    }

    public function AddHRuleRow()
    {
        global $MainContent;
    
        $MainContent .=  '<div class="hrule"></div>';
    }

    public function AddPictureDropDownRow($name, $key, $path, $errortext, $optional = null, $selected = null)
    {
        global $templ, $func;

        $dir = $func->GetDirList($path);
        $file_out = array();
        $file_out[] = "<option value=\"none\">None</option>";
        if ($dir) {
            foreach ($dir as $file) {
                $extension = substr($file, strpos($file, '.') + 1, 4);
                if ($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "gif") {
                    ($file == $selected)? $file_out[] = "<option value=\"".$file."\" selected>".$file."</option>"
                    : $file_out[] = "<option value=\"".$file."\">".$file."</option>";
                }
            }
        }

        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        ($selected and $selected != "none")? $picpreview_init = $path."/".$selected :$picpreview_init = 'design/images/transparent.png';
        $options = implode("", $file_out);

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value .= '<select name="'. $name .'" id="'. $name .'" class="form'. $optional .'" onChange="javascript:changepic(\''. $path .'/\'+ this.value, window.document.'. $name .'_picpreview)" > '. $options .'';
        $value .= '</select>';
        $value .= $errortext;
        $value .= '<br /><img src="'. $picpreview_init .'" name="'. $name .'_picpreview" alt="pic" />';
        $this->AddDoubleRow($key, $value);
    }

  // TODO: Review!
    public function AddPictureSelectRow($key, $path, $pics_per_row = null, $max_rows = null, $optional = null, $checked = null, $max_width = null, $max_height = null, $JS = false)
    {
        global $smarty;

        include_once("inc/classes/class_gd.php");
        $gd = new gd;

        if ($max_width == "") {
            $max_width = 150;
        }
        if ($max_height == "") {
            $max_height = 120;
        }
        if ($max_rows == "") {
            $max_rows = 100;
        }
        if ($pics_per_row == "") {
            $pics_per_row = 3;
        }

        $zeile = "";
        $templ['ls']['row']['pictureselect']['spalte'] = "";

        if ($optional) {
            $optional = '_optional';
        }

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
        $pics = array();
        $x = 0;
        $y = 0;
        $z = 0;
        foreach ($file_list as $file) {
            $arr = array();
            $extension =  strtolower(substr($file, strrpos($file, ".") + 1, 4));
            if (($extension == "jpeg") or ($extension == "jpg") or ($extension == "png") or ($extension == "gif")) {
                $file_out = "$path/lsthumb_$file";

          // Wenn Thumb noch nicht generiert wurde, generieren versuchen
                if (!file_exists($file_out)) {
                    $gd->CreateThumb("$path/$file", $file_out, $max_width, $max_height);
                }

                $pic_dimensions = GetImageSize($file_out);
                if (!$pic_dimensions[0] or $pic_dimensions[0] > $max_width) {
                    $pic_dimensions[0] = $max_width;
                }
                if (!$pic_dimensions[1] or $pic_dimensions[1] > $max_height) {
                    $pic_dimensions[1] = $max_height;
                }
                $arr['width'] = $pic_dimensions[0];
                $arr['height'] = $pic_dimensions[1];

                $arr['src'] = $file_out;
                $caption = strtolower(substr($file, 0, strrpos($file, ".")));
                if (($z == $checked) || ($file == $checked)) {
                    $check = 'checked';
                } else {
                    $check = '';
                }

                if ($JS) {
                    $arr['IconClick'] = " onClick=\"javascript:UpdateCurrentPicture('$file_out');\"";
                    $arr['InputForm'] = '<input type="hidden" name="'. $key .'" value="'. $file .'" />';
                } else {
                    $arr['InputForm'] = '<input type="radio" name="'. $key .'" class="form'. $optional .'" value="'. $file .'" '. $check .' />'. $caption;
                }

                $pics[$x][$y] = $arr;
                $z++;
                $y++;

                if ($z % $pics_per_row == 0) {
                    $x++;
                    $y = 0;
                }
            }
        }

        $smarty->assign('pics', $pics);
        $this->AddContentLine($smarty->fetch('design/templates/ls_row_pictureselect.htm'));
    }

    public function AddFileSelectRow($name, $key, $errortext, $size = null, $maxlength = null, $optional = null)
    {
        global $func;

        $maxfilesize = ini_get('upload_max_filesize');
        if (strpos($maxfilesize, 'M') > 0) {
            $maxfilesize = (int)$maxfilesize * 1024 * 1024;
        } elseif (strpos($maxfilesize, 'K') > 0) {
            $maxfilesize = (int)$maxfilesize * 1024;
        } else {
            $maxfilesize = (int)$maxfilesize;
        }

    // If value is too low (most likely because of errors in above statement), set it to 100M
        if ($maxfilesize < 1000) {
            $maxfilesize = 1024 * 1024 * 100;
        }
        $maxfilesize_formated = '(Max: '. $func->FormatFileSize($maxfilesize) .')';

        if ($size == '') {
            $size = '30';
        }
        ($errortext)? $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix : $errortext = '';
        ($optional)? $optional = "_optional" : $optional = '';
        ($selected and $selected != "none")? $picpreview_init = $path."/".$selected :$picpreview_init = 'design/images/transparent.png';

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $maxfilesize .'" />';
        $value .= '<input type="file" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" value="" size="'. $size .'" enctype="multipart/form-data" maxlength="'. $maxlength .'" /> '. $maxfilesize_formated;
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    public function AddJumpToMark($name)
    {
        global $MainContent;
        $MainContent .= "<a name=\"$name\"></a>";
    }

    public function AddIFrame($url, $width = 795, $height = 600)
    {
        global $smarty;

        $smarty->assign('noIFrame', t('Wenn ihr Broswer keine IFrames unterstützt, '));
        $smarty->assign('clickhere', t('bitte hier klicken!'));
        $smarty->assign('url', 'http://' . $url);
        $smarty->assign('width', $width);
        $smarty->assign('height', $height);

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_IFrame.htm'));
    }

    public function AddContent($target = null)
    {
    }

  // Should be called AddForm
    public function SetForm($f_url, $f_name = null, $f_method = null, $f_enctype = null)
    {
        global $smarty;

        if ($f_name == null) {
            $f_name = "dsp_form" . $this->formcount++;
        }
        if ($f_method == null) {
            $f_method = "POST";
        }

        if ($f_enctype == null) {
            $f_enctype = "";
        } else {
            $f_enctype = "enctype=\"$f_enctype\"";
        }

        if ($this->form_open) {
            $this->CloseForm();
        }
          $this->form_open = true;

          $this->form_name = $f_name;

          $smarty->assign('name', $f_name);
          $smarty->assign('method', strtolower($f_method));
          $smarty->assign('action', $f_url);
          $smarty->assign('enctype', $f_enctype);

          $this->AddSmartyTpl('ls_row_formbegin');
    }

  // Should be called AddCloseForm
    public function CloseForm()
    {
        $this->form_open = false;
        $this->AddSmartyTpl('ls_row_formend');
    }


  #### Fetch Content ####
  # The following functions all return their content, to the module, instead of printing them directly

    public function FetchAttachmentRow($file)
    {
        include_once("inc/classes/class_gd.php");
        $gd = new gd;

        $FileEnding = strtolower(substr($file, strrpos($file, '.'), 5));

        if ($FileEnding == '.png' or $FileEnding == '.gif' or $FileEnding == '.jpg' or $FileEnding == '.jpeg') {
            $FileNamePath = strtolower(substr($file, 0, strrpos($file, '.')));
            $FileThumb = $FileNamePath. '_thumb' .$FileEnding;

            $gd->CreateThumb($file, $FileThumb, '300', '300');
            return HTML_NEWLINE . HTML_NEWLINE. '<a href="'. $file .'" target="_blank"><img src="'. $FileThumb .'" border="0" /></a>';
        } else {
            return HTML_NEWLINE . HTML_NEWLINE. $this->FetchIcon($file, 'download') .' ('. t('Angehängte Datei herunterladen').')';
        }
    }

    public function FetchCssButton($title, $link, $hint = null, $target = null)
    {
        ($hint)? $hint = '<span class="infobox">'. t($hint) .'</span>' : $hint = '';
        ($target)? $target = ' target="_blank"' : $target = '';
        return '<div class="Button"><a href="'. $link .'"'. $target .'>'. $title . $hint .'</a></div>';
    }

    public function FetchSpanButton($title, $link, $hint = null, $target = null)
    {
        ($hint)? $hint = '<span class="infobox">'. t($hint) .'</span>' : $hint = '';
        ($target)? $target = ' target="_blank"' : $target = '';
        return '<div class="Buttons" style="display:inline"><a href="'. $link .'"'. $target .'>'. $title . $hint .'</a></div>';
    }
  
    public function FetchIcon($link, $picname, $hint = null, $target = null, $align = 'left')
    {
        global $smarty;

    // Picname-Mappings
        switch ($picname) {
            case 'next':
                $picname = 'forward';
                break;
            case 'preview':
                $picname = 'search';
                break;
        }
        $smarty->assign('name', $picname);

    // Hint
        if ($hint == '') {
            switch ($picname) {
                default:
                    $hint = '';
                    break;
                case 'add':
                    $hint = t('Hinzufügen');
                    break;
                case 'change':
                    $hint = t('Ändern');
                    break;
                case 'edit':
                    $hint = t('Editieren');
                    break;
                case 'delete':
                    $hint = t('Löschen');
                    break;
                case 'send':
                    $hint = t('Senden');
                    break;
                case 'quote':
                    $hint = t('Zitieren');
                    break;
            }
        }
        $smarty->assign('hint', $hint);
        if ($align == 'right') {
            $smarty->assign('additionalhtml', 'align="right" valign="bottom" vspace="2" ');
        } else {
            $smarty->assign('additionalhtml', '');
        }

        if ($this->form_open) {
            $ret = $smarty->fetch('design/templates/ls_fetch_icon_submit.htm');
        } else {
            $ret = $smarty->fetch('design/templates/ls_fetch_icon.htm');
        }
    
        if ($target) {
            $target = " target=\"$target\"";
        }
        if ($link) {
            $ret = '<a href="'.$link.'"'.$target.'>'. $ret .'</a>';
        }
        return $ret;
    }

    public function FetchUserIcon($userid, $username = '')
    {
        global $smarty, $authentication;

        if ($userid == 0) {
            $username = '<i>System</i>';
        }
        $smarty->assign('userid', $userid);
        $smarty->assign('username', $username);
        $smarty->assign('hint', t('Benutzerdetails aufrufen'));

        (in_array($userid, $authentication->online_users))? $state ='online' : $state ='offline';
        if (in_array($userid, $authentication->away_users)) {
            $state ='idle';
        }
    
        $smarty->assign('state', $state);

        return $smarty->fetch('design/templates/ls_usericon.htm');
    }

    public function FetchLink($text, $link, $class = '', $target = '')
    {
        if ($class) {
            $class = ' class="'. $class .'"';
        }
        if ($target) {
            $target = ' target="'. $target .'"';
        }
        return '<a href="'.$link.'"'. $class . $target.'>'. $text .'</a>';
    }

  // Old: Use FetchIcon instead
    public function AddIcon($name, $link = '', $title = '')
    {
        return $this->FetchIcon($link, $name, $title);
    }

  // Should be called FetchHelpText
    public function HelpText($text, $help)
    {
        return '<div class="infolink" style="display:inline">'. t($text) .'<span class="infobox">'. t($help) .'</span></div>';
    }
}
