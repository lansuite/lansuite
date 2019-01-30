<?php

namespace LanSuite;

/**
 * Class Display
 *
 * When handling own templates, the prefered way is to use $smarty->fetch().
 * However, at some point, you have to append these fetched content to $MainContent
 * But: Never write to $MainContent within your module!
 * Instead: Use either $dsp->AddSmartyTpl(), or $dsp->AddContentLine()
 * to attach your content to the LS-output.
 */
class Display
{
    /**
     * @var int
     */
    public $form_open = 0;

    /**
     * @var int
     */
    private $formcount = 1;

    /**
     * @var string
     */
    public $errortext_prefix = '';

    /**
     * @var string
     */
    public $errortext_suffix = '';

    /**
     * @var int
     */
    private $FirstLine = 1;

    /**
     * @var int
     */
    private $CurrentTab = 0;

    /**
     * @var string
     */
    private $TabsMainContentTmp = '';

    /**
     * @var array
     */
    private $TabNames = [];

    /**
     * @var string
     */
    private $form_name = '';

    public function __construct()
    {
        $this->errortext_prefix = HTML_NEWLINE . HTML_FONT_ERROR;
        $this->errortext_suffix = HTML_FONT_END;
    }

    /**
     * Adds a smarty template.
     * Attention: This does not add the LS-line-container, so you have to take care of it yourself!
     *
     * @param string $name
     * @param string $mod
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function AddSmartyTpl($name, $mod = '')
    {
        global $smarty, $MainContent;

        if ($mod == '') {
            $MainContent .= $smarty->fetch('design/templates/'. $name .'.htm');
        } else {
            $MainContent .= $smarty->fetch('modules/'. $mod .'/templates/'. $name .'.htm');
        }
    }

    /**
     * Adds the provided content in a new LS-line
     *
     * @param $content
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function AddContentLine($content)
    {
        global $smarty, $MainContent;

        if ($this->FirstLine) {
            $smarty->assign('content', $content);
            $MainContent .= $smarty->fetch('design/templates/ls_row_firstline.htm');
            $this->FirstLine = 0;
        } else {
            $smarty->assign('content', $content);
            $MainContent .= $smarty->fetch('design/templates/ls_row_line.htm');
        }
    }

    /**
     * Writes the headline of a page
     *
     * @param string $caption
     * @param string $text
     * @param string $helplet_id
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
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

    /**
     * @return void
     */
    public function StartTabs()
    {
        global $MainContent;

        $this->TabsMainContentTmp = $MainContent;
        $MainContent = '';
    }

    /**
     * @param string $name
     * @param string $icon
     * @return void
     */
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

    /**
     * @return void
     */
    public function EndTab()
    {
        global $MainContent;
        $MainContent .= '</div>';
    }

    /**
     * @return void
     */
    public function EndTabs()
    {
        global $MainContent, $framework;

        $this->AddSingleRow('');
        $out = $this->TabsMainContentTmp;

        $items = '';
        foreach ($this->TabNames as $key => $name) {
            $items .= '<li><a href="#tabs-'. $key .'">'. $name .'</a></li>';
        }
        $out .= '<div id="tabs"><ul>'. $items .'</ul>';

        $sel = '';
        if ($_GET['tab']) {
            $sel = '{ selected: '. (int)$_GET['tab'] .' }';
        }
        $framework->add_js_code('$(function() { $("#tabs").tabs('. $sel .'); });');

        $out .= $MainContent .'</div>';
        $MainContent = $out;
    }

    /**
     * @param array     $names
     * @param string    $link
     * @param string    $active
     * @return void
     */
    public function AddHeaderMenu($names, $link, $active = null)
    {
        global $MainContent;

        $items = '';
        foreach ($names as $key => $name) {
            if ($key == $active && $active != null) {
                $items .= '<span class="HeaderMenuItemActive">'. $name .'</span>';
            } else {
                $items .= '<span class="HeaderMenuItem"><a href="'. $link .'&headermenuitem='. $key .'">'. $name .'</a></span>';
            }
        }

        $MainContent .= $items;
    }

    /**
     * @param array     $names
     * @param string    $link
     * @param string    $active
     * @return void
     */
    public function AddHeaderMenu2($names, $link, $active = null)
    {
        global $MainContent;

        $items = '';
        foreach ($names as $key => $name) {
            if ($key == $active && $active != '') {
                $am = '';
            } else {
                $am = 'class="menu"';
            }
            $items .= '<a href="'. $link . $key .'"'. $am .'><b>'. $name .'</b></a> - ';
        }
        $items = substr($items, 0, -3);

        $MainContent .=  $items;
    }

    /**
     * @param string    $name
     * @param bool      $vissible
     * @return void
     */
    public function StartHiddenBox($name, $vissible = false)
    {
        global $MainContent;

        if ($vissible) {
            $vissible = '';
        } else {
            $vissible = 'none';
        }
        $MainContent .=  '<div id="'. $name .'" style="display:'. $vissible .'">';
    }

    /**
     * @return void
     */
    public function StopHiddenBox()
    {
        global $MainContent;

        $MainContent .=  '</div>';
    }

    /**
     * @param string $text
     * @param string $parm
     * @param string $class
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function AddSingleRow($text, $parm = null, $class = '')
    {
        global $smarty;

        $smarty->assign('text', $text);
        if ($parm != '') {
            $smarty->assign('align', $parm);
        }

        if ($class != '') {
            $smarty->assign('class', 'class="' . $class . '"');
        }

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_single.htm'));
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $id
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function AddDoubleRow($key, $value, $id = null)
    {
        global $smarty;

        if ($key == '') {
            $key = "&nbsp;";
        }

        if ($value == '') {
            $value = "&nbsp;";
        }

        if ($id == '') {
            $id = "DoubleRowVal";
        }

        $smarty->assign('key', $key);
        $smarty->assign('value', $value);
        $smarty->assign('id', $id);

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_double.htm'));
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $text
     * @param string $errortext
     * @param boolean $optional
     * @param boolean $checked
     * @param boolean $disabled
     * @param string  $val
     * @param string $additionalHTML
     * @return void
     */
    public function AddCheckBoxRow($name, $key, $text, $errortext, $optional = null, $checked = null, $disabled = null, $val = null, $additionalHTML = null)
    {
        if ($checked) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

        if ($disabled) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }


        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        // TODO Remove variable $optional, it is not used at all (or implement it)
        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        if ($val == '') {
            $val = '1';
        }

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<input id="'. $name .'" name="'. $name .'" type="checkbox" class="checkbox" value="'. $val .'" '. $checked .' '. $disabled .' '. $additionalHTML .' />';
        $value .= '<label for="'. $name .'">'. $text .'</label>'. $errortext;
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $val
     * @param string  $errortext
     * @param boolean $optional
     * @param boolean $checked
     * @param boolean $disabled
     */
    public function AddRadioRow($name, $key, $val, $errortext = null, $optional = null, $checked = null, $disabled = null)
    {

        if ($checked) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        if ($disabled) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }

        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        $value = '<input name="'. $name .'" type="radio" class="form'. $optional .'" value="'. $val .'" '. $checked .' '. $disabled .' />'. $errortext;
        $key = '<label for="'. $name .'">'. $key .'</label>';
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $errortext
     * @param string $size
     * @param boolean $optional
     * @param boolean $not_changeable
     * @param int $maxlength
     * @return void
     */
    public function AddTextFieldRow($name, $key, $value, $errortext, $size = null, $optional = null, $not_changeable = null, $maxlength = null)
    {
        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        if ($not_changeable) {
            $not_changeable = ' readonly="readonly"';
        } else {
            $not_changeable = '';
        }

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

    /**
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $errortext
     * @param string $size
     * @param boolean $optional
     * @param string $additional
     * @return void
     */
    public function AddPasswordRow($name, $key, $value, $errortext, $size = null, $optional = null, $additional = null)
    {
        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        if ($size == '') {
            $size = '30';
        }

        $value = '<input type="password" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" size="'. $size .'" value="'. $value .'" '. $additional .' />'. $errortext;
        $key = '<label for="'. $name .'">'. $key .'</label>';
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param array $table
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
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

    /**
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $errortext
     * @param string $cols
     * @param string $rows
     * @param boolean $optional
     * @param string $maxchar
     * @return void
     */
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

        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        $key = '<label for="'. $name .'">'. $key .'</label>
      <br />
      <br />
      <br />
      <a href="index.php?mod=popups&action=ls_row_textareamail_popup&design=popup&form='. $this->form_name .'&textarea='. $name .'" onclick="OpenWindow(this.href, \'TextFormatSelect\'); return false">Variablen einfügen</a>';
        $value = '<textarea name="'. $name .'" id="'. $name .'" class="form'. $name .'" cols="'. $cols .'" rows="'. $rows .'" onKeyUp="TextAreaPlusCharsLeft(this, document.'. $this->form_name .'.'. $name .'_chr, '. $maxchar .'); AddaptTextAreaHeight(this)">'. $value .'</textarea>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $errortext
     * @param string $cols
     * @param string $rows
     * @param boolean $optional
     * @return void
     */
    public function AddTextAreaRow($name, $key, $value, $errortext, $cols = null, $rows = null, $optional = null)
    {
        if ($cols == "") {
            $cols = "50";
        }
        if ($rows == "") {
            $rows = "7";
        }

        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        // TODO implement $optional, right now it is not in use
        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<textarea name="'. $name .'" id="'. $name .'" class="form'. $name .'" cols="'. $cols .'" rows="'. $rows .'" onKeyUp="AddaptTextAreaHeight(this)">'. $value .'</textarea>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    /**
     * TODO remove or implement $cols, it is not in use right now
     *
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $errortext
     * @param string $cols
     * @param string $rows
     * @param boolean $optional
     * @param string $maxchar
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
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
        $buttons .= " ". $this->FetchIcon('bold', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[b]', '[/b]')", t('Fett'));
        $buttons .= " ". $this->FetchIcon('italic', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[i]', '[/i]')", t('Kursiv'));
        $buttons .= " ". $this->FetchIcon('underline', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[u]', '[/u]')", t('Unterstrichen'));
        $buttons .= " ". $this->FetchIcon('strike', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[s]', '[/s]')", t('Durchstreichen'));
        $buttons .= " ". $this->FetchIcon('sub', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[sub]', '[/sub]')", t('Tiefstellen'));
        $buttons .= " ". $this->FetchIcon('sup', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[sup]', '[/sup]')", t('Hochstellen'));
        $buttons .= " ". $this->FetchIcon('quote', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[c]', '[/c]')", t('Code'));
        $buttons .= " ". $this->FetchIcon('img', "javascript:InsertCode(document.{$this->form_name}.{$name}, '[img]', '[/img]')", t('Bild'));
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

    /**
     * @param string $name
     * @param string $key
     * @param array $option_array
     * @param string $errortext
     * @param boolean $optional
     * @param null $additionalHTML
     * @return void
     */
    public function AddDropDownFieldRow($name, $key, $option_array, $errortext, $optional = null, $additionalHTML = null)
    {
        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        if ($option_array) {
            $options = implode('', $option_array);
        } else {
            $options = '';
        }

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<select name="'. $name .'" class="form'. $optional .'" '. $additionalHTML .'>';
        $value .= $options;
        $value .= '</select>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $name
     * @return void
     */
    public function AddFieldsetStart($name)
    {
        global $MainContent;

        $MainContent .=  '<br /><fieldset width="100%" style="clear:left; width:100%"><legend><b>'. $name .'</b></legend>';
        $this->FirstLine = 1;
    }

    /**
     * @return void
     */
    public function AddFieldsetEnd()
    {
        global $MainContent;

        $MainContent .=  '</fieldset>';
        $this->FirstLine = 1;
    }

    /**
     * @param string $name
     * @param string $key
     * @param array $option_array
     * @param string $errortext
     * @param boolean $optional
     * @param int $size
     * @return void
     */
    public function AddSelectFieldRow($name, $key, $option_array, $errortext, $optional = null, $size = null)
    {
        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        if ($option_array) {
            $options = implode('', $option_array);
        } else {
            $options = '';
        }

        if (!$size) {
            $size = 4;
        }

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<select name="'. $name .'[]" class="form'. $optional .'" size="'. $size .'" multiple>';
        $value .= $options;
        $value .= '</select>';
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $text
     * @param bool $close
     * @param string $name
     * @return void
     */
    public function AddFormSubmitRow($text, $close = true, $name = "imageField")
    {
        $this->AddDoubleRow('&nbsp;', '<input type="submit" class="Button" name="'. $name .'" value="'. $text .'" />');
        if ($this->form_open and $close) {
            $this->CloseForm();
        }
    }

    /**
     * TODO Remove $helplet_id, this is not used
     *
     * @param string $back_link
     * @param null $helplet_id
     * @return void
     */
    public function AddBackButton($back_link = null, $helplet_id = null)
    {
        global $func;

        if (!$back_link) {
            $back_link = $func->internal_referer;
        }
        $this->AddDoubleRow('', $this->FetchSpanButton(t('Zurück'), $back_link));
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $action
     * @param string $method
     * @param string $errortext
     * @param string $size
     * @param boolean $optional
     * @return void
     */
    public function AddBarcodeForm($key, $value, $action, $method = "post", $errortext = null, $size = null, $optional = null)
    {
        if ($size == '') {
            $size = '30';
        }
        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

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

    /**
     * @param string $name
     * @param string $key
     * @param int $time
     * @param string $errortext
     * @param array $values
     * @param array $disableds
     * @param int $start_year
     * @param int $end_year
     * @param int $hidetime 0 =  All visible / 1 = Hide Time / 2 = Hide Date
     * @param boolean $optional
     * @param string $additional
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function AddDateTimeRow($name, $key, $time, $errortext, $values = null, $disableds = null, $start_year = null, $end_year = null, $hidetime = null, $optional = null, $additional = null)
    {
        global $smarty;

        $smarty->assign('name', $name);
        $smarty->assign('key', $key);
        $smarty->assign('additional', $additional);
        if ($optional) {
            $smarty->assign('optional', '_optional');
        }

        if ($time > 0) {
            $day = date("j", $time);
            $month = date("n", $time);
            $year = date("Y", $time);
            $hour = date("H", $time);
            $min = date("i", $time);
        } elseif ($values['day'] != "" and $values['month'] != "" and $values['year'] != "") {
            $day = ltrim($values['day'],'0');
            $month = ltrim($values['month'],'0');
            $year = $values['year'];
            $hour = $values['hour'];
            $min = $values['min'];
        } else {
            $day = date("j");
            $month = date("n");
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
        $arr = [];
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

        if ($hidetime != 1) {
            $smarty->assign('showtime', '1');
        }

        if ($hidetime != 2) {
            $smarty->assign('showdate', '1');
        }

        $this->AddContentLine($smarty->fetch('design/templates/ls_row_datetime.htm'));
    }

    /**
     * @return void
     */
    public function AddHRuleRow()
    {
        global $MainContent;

        $MainContent .=  '<div class="hrule"></div>';
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $path
     * @param string $errortext
     * @param boolean $optional
     * @param boolean $selected
     * @return void
     */
    public function AddPictureDropDownRow($name, $key, $path, $errortext, $optional = null, $selected = null)
    {
        global $func;

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

        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }

        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }
        if ($selected && $selected != "none") {
            $picpreview_init = $path."/".$selected;
        } else {
            $picpreview_init = 'design/images/transparent.png';
        }
        $options = implode("", $file_out);

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<select name="'. $name .'" id="'. $name .'" class="form'. $optional .'" onChange="javascript:changepic(\''. $path .'/\'+ this.value, window.document.'. $name .'_picpreview)" > '. $options .'';
        $value .= '</select>';
        $value .= $errortext;
        $value .= '<br /><img src="'. $picpreview_init .'" name="'. $name .'_picpreview" alt="pic" />';
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $errortext
     * @param string $size
     * @param int $maxlength
     * @param boolean $optional
     * @return void
     */
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
        if ($errortext) {
            $errortext = $this->errortext_prefix . $errortext . $this->errortext_suffix;
        } else {
            $errortext = '';
        }
        if ($optional) {
            $optional = "_optional";
        } else {
            $optional = '';
        }

        $key = '<label for="'. $name .'">'. $key .'</label>';
        $value = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $maxfilesize .'" />';
        $value .= '<input type="file" id="'. $name .'" name="'. $name .'" class="form'. $optional .'" value="" size="'. $size .'" enctype="multipart/form-data" maxlength="'. $maxlength .'" /> '. $maxfilesize_formated;
        $value .= $errortext;
        $this->AddDoubleRow($key, $value);
    }

    /**
     * @param string $name
     * @return void
     */
    public function AddJumpToMark($name)
    {
        global $MainContent;
        $MainContent .= "<a name=\"$name\"></a>";
    }

    /**
     * Should be called AddForm
     *
     * @param string $f_url
     * @param string $f_name
     * @param string $f_method
     * @param string $f_enctype
     * @return void
     */
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

    /**
     * Should be called AddCloseForm
     *
     * @return void
     */
    public function CloseForm()
    {
        $this->form_open = false;
        $this->AddSmartyTpl('ls_row_formend');
    }

    /**
     * @param string $file
     * @return string
     */
    public function FetchAttachmentRow($file)
    {
        $gd = new GD();

        $FileEnding = strtolower(substr($file, strrpos($file, '.'), 5));

        if ($FileEnding == '.png' or $FileEnding == '.gif' or $FileEnding == '.jpg' or $FileEnding == '.jpeg') {
            $FileNamePath = strtolower(substr($file, 0, strrpos($file, '.')));
            $FileThumb = $FileNamePath. '_thumb' .$FileEnding;

            $gd->CreateThumb($file, $FileThumb, '300', '300');
            return HTML_NEWLINE . HTML_NEWLINE. '<a href="'. $file .'" target="_blank"><img src="'. $FileThumb .'" border="0" /></a>';
        }

        return HTML_NEWLINE . HTML_NEWLINE. $this->FetchIcon('download', $file) .' ('. t('Angehängte Datei herunterladen').')';
    }

    /**
     * @param string $title
     * @param string $link
     * @param string $hint
     * @param string $target
     * @return string
     */
    public function FetchCssButton($title, $link, $hint = null, $target = null)
    {
        if ($hint) {
            $hint = '<span class="infobox">'. t($hint) .'</span>';
        } else {
            $hint = '';
        }

        if ($target) {
            $target = ' target="_blank"';
        } else {
            $target = '';
        }

        return '<div class="Button"><a href="'. $link .'"'. $target .'>'. $title . $hint .'</a></div>';
    }

    /**
     * @param string $title
     * @param string $link
     * @param string $hint
     * @param string $target
     * @return string
     */
    public function FetchSpanButton($title, $link, $hint = null, $target = null)
    {
        if ($hint) {
            $hint = '<span class="infobox">'. t($hint) .'</span>';
        } else {
            $hint = '';
        }

        if ($target) {
            $target = ' target="_blank"';
        } else {
            $target = '';
        }

        return '<div class="Buttons" style="display:inline"><a href="'. $link .'"'. $target .'>'. $title . $hint .'</a></div>';
    }

    /**
     * @param string $picname
     * @param string $link
     * @param string $hint
     * @param string $target
     * @param string $align
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    public function FetchIcon($picname, $link = '', $hint = null, $target = null, $align = 'left')
    {
        global $smarty;

        switch ($picname) {
            case 'next':
                $picname = 'forward';
                break;
            case 'preview':
                $picname = 'search';
                break;
        }
        $smarty->assign('name', $picname);

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

    /**
     * @param int $userid
     * @param string $username
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    public function FetchUserIcon($userid, $username = '')
    {
        global $smarty, $authentication;

        if ($userid == 0) {
            $username = '<i>System</i>';
        }

        $smarty->assign('userid', $userid);
        $smarty->assign('username', $username);
        $smarty->assign('hint', t('Benutzerdetails aufrufen'));

        if (in_array($userid, $authentication->online_users)) {
            $state ='online';
        } else {
            $state ='offline';
        }

        if (in_array($userid, $authentication->away_users)) {
            $state ='idle';
        }

        $smarty->assign('state', $state);

        return $smarty->fetch('design/templates/ls_usericon.htm');
    }

    /**
     * @param string $text
     * @param string $link
     * @param string $class
     * @param string $target
     * @return string
     */
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

    /**
     * @param string $text
     * @param string $help
     * @return string
     */
    public function HelpText($text, $help)
    {
        return '<div class="infolink" style="display:inline">'. t($text) .'<span class="infobox">'. t($help) .'</span></div>';
    }
}
