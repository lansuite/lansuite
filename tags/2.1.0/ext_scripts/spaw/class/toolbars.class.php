<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Toolbars class
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-22
// ================================================

// toolbar item type constants
define("SPAW_TBI_IMAGE", "image");
define("SPAW_TBI_BUTTON", "button");
define("SPAW_TBI_DROPDOWN", "dropdown");

// toolbar item
class SPAW_TB_Item
{
  // name
  var $name;
  // language object
  var $lang;
  // editor name
  var $editor;
  // additional item data
  var $data;
  // toolbar theme
  var $theme;
  
  // get items html
  function get()
  {
    return $this->lang->m('title',$this->name);
  }
  
  // show item
  function show()
  {
    echo $this->get();
  }
  
  // constructor
  function SPAW_TB_Item($name, &$lang, $editor, $theme, $attributes='', $data='')
  {
    $this->name = $name;
    $this->lang = $lang;
    $this->editor = $editor;
    $this->theme = $theme;
    if (!is_array($data))
    {
      $this->data = array();
    }
    else
    {
      $this->data = $data;
    }
  }
} // SPAW_TB_Item

// toolbar image
class SPAW_TB_Image extends SPAW_TB_Item
{
  // override get
  function get()
  {
    global $spaw_dir;
    
    if (!empty($this->name))
    {
      $buf = '<img id="SPAW_'.$this->editor.'_tb_'.$this->name.'" alt="'.$this->lang->m('title',$this->name).'" src="'.$spaw_dir.'lib/themes/'.$this->theme.'/img/tb_'.$this->name.'.gif" '.(isset($this->attributes)?$this->attributes:'').' unselectable="on">';
      return $buf;
    }
  }
} // SPAW_TB_Image

// toolbar button
class SPAW_TB_Button extends SPAW_TB_Item
{
  // override get
  function get()
  {
    global $spaw_dir;
    
    if (!empty($this->name))
    {
      $buf = '<img id="SPAW_'.$this->editor.'_tb_'.$this->name.'" alt="'.$this->lang->m('title',$this->name).'" src="'.$spaw_dir.'lib/themes/'.$this->theme.'/img/tb_'.$this->name.'.gif" onClick="SPAW_'.$this->name.'_click(\''.$this->editor.'\',this)" class="SPAW_'.$this->theme.'_tb_out" onMouseOver="SPAW_'.$this->theme.'_bt_over(this)" onMouseOut="SPAW_'.$this->theme.'_bt_out(this)" onMouseDown="SPAW_'.$this->theme.'_bt_down(this)" onMouseUp="SPAW_'.$this->theme.'_bt_up(this)"  '.(isset($this->attributes)?$this->attributes:'').' unselectable="on">';
      return $buf;
    }
  }
} // SPAW_TB_Button

// toolbar dropdown
class SPAW_TB_Dropdown extends SPAW_TB_Item
{
  // override get
  function get()
  {
    global $spaw_dir;
    global $spaw_theme;
    
    if (!empty($this->name))
    {
      $buf = '<select size="1" id="SPAW_'.$this->editor.'_tb_'.$this->name.'" name="SPAW_'.$this->editor.'_tb_'.$this->name.'" align="absmiddle" class="SPAW_'.$this->theme.'_tb_input" onchange="SPAW_'.$this->name.'_change(\''.$this->editor.'\',this)" '.(isset($this->attributes)?$this->attributes:'').'>';
      $buf.='<option>'.$this->lang->m('title',$this->name).'</option>';
      while(list($value,$text) = each($this->data))
      {
        $buf.='<option value="'.$value.'">'.$text.'</option>';
      }
      $buf.= '</select>';
      return $buf;
    }
  }
} // SPAW_TB_Button

// toolbars
class SPAW_Toolbars
{
  // array of toolbar data
  var $toolbars;

  // toolbar mode (scheme)
  var $mode;
  
  // dropdown data
  var $dropdown_data;
  
  // accessors
  function setMode($value)
  {
    global $spaw_dir;
    global $spaw_root;
    global $spaw_default_toolbars;
    
    if ($value == '')
    {
      $this->mode = $spaw_default_toolbars;
    }
    else
    {
      $this->mode = $value;
    }
    
    // try loading specific tollbar for this mode and browser type
    if (!@include($spaw_root.'lib/toolbars/'.$this->mode.'/'.$this->mode.'_toolbar_data.'.strtolower(SPAW_Util::getBrowser()).'.inc.php'))
    {
      if (!@include($spaw_root.'lib/toolbars/'.$this->mode.'/'.$this->mode.'_toolbar_data.inc.php'))
      {
        // load default toolbar data
        @include($spaw_root.'lib/toolbars/'.$spaw_default_toolbars.'/'.$spaw_default_toolbars.'_toolbar_data.inc.php');
      }
    }
    $this->toolbars = $spaw_toolbar_data;
  }
  
  // language object
  var $lang;
  
  // editor name
  var $editor;
  
  // toolbar theme
  var $theme;
  
  // constructor
  function SPAW_Toolbars(&$lang, $editor, $mode='', $theme='', $dropdown_data='')
  {
    global $spaw_dropdown_data;
    
    $this->lang = $lang;
    $this->editor = $editor;
    $this->setMode($mode);
    $this->theme = $theme;
    if ($dropdown_data != '')
    {
      $this->dropdown_data = $dropdown_data;
    }
    else
    {
      $this->dropdown_data = $spaw_dropdown_data;
    }
  }
  
  // get toolbar html for the specified position (top, left, right, bottom)
  function get($pos, $mode='design')
  {
  	$buf = '';
    if (!empty($this->toolbars[$pos.'_'.$mode]))
    {
      if ($pos == 'top' || $pos == 'bottom')
      {
        // horizontal toolbar
        $tb_pos_start = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
        $tb_pos_end = '</table>';
        $tb_item_sep = '';
      }
      else
      {
        // vertical toolbar
        $tb_pos_start = '<table border="0" cellpadding="0" cellspacing="0"><tr>';
        $tb_pos_end = '</tr></table>';
        $tb_item_sep = '<br>';
      }
      $buf = $tb_pos_start;
      while (list(,$tb) = each($this->toolbars[$pos.'_'.$mode]))
      {
        if ($pos == 'top' || $pos == 'bottom')
        {
          // horizontal toolbar
          $tb_start = '<tr><td align="'.$tb['settings']['align'].'" valign="'.$tb['settings']['valign'].'" class="SPAW_'.$this->theme.'_toolbar_'.$pos.'" nowrap="yes">';
          $tb_end = '</td></tr>';
        }
        else
        {
          // vertical toolbar
          $tb_start = '<td align="'.$tb['settings']['align'].'" valign="'.$tb['settings']['valign'].'" class="SPAW_'.$this->theme.'_toolbar_'.$pos.'">';
          $tb_end = '</td>';
        }
      
        $buf .= $tb_start;
        while (list(,$tbitem) = each($tb['data']))
        {
          $buf .= $this->getTbItem($tbitem['name'],$tbitem['type'],isset($tbitem['attributes'])?$tbitem['attributes']:'', isset($tbitem['data'])?$tbitem['data']:'') . $tb_item_sep;
        }
        $buf .= $tb_end;
      }
      $buf .= $tb_pos_end;
    }
    return $buf;
  } // get
  
  // returns toolbar item html based on name and type
  function getTbItem($name, $type, $attributes, $data)
  {
    switch($type)
    {
      case SPAW_TBI_IMAGE:
        $tbi = new SPAW_TB_Image($name, $this->lang, $this->editor, $this->theme, $attributes);
        $buf = $tbi->get();
        break;
      case SPAW_TBI_BUTTON:
        $tbi = new SPAW_TB_Button($name, $this->lang, $this->editor, $this->theme, $attributes);
        $buf = $tbi->get();
        break;
      case SPAW_TBI_DROPDOWN:
        if (!empty($this->dropdown_data[$name]))
        {
          $d_data = $this->dropdown_data[$name];
        }
        else
        {
          $d_data = $data;
        }
        $tbi = new SPAW_TB_Dropdown($name, $this->lang, $this->editor, $this->theme, $attributes, $d_data);
        $buf = $tbi->get();
        break;
      default:
        $tbi = new SPAW_TB_Item($name, $this->lang, $this->editor, $this->theme, $attributes);
        $buf = $tbi->get();
        break;
    }
    return $buf;
  } // getTbItem
  
  // output toolbar html for the specified position (top, left, right, bottom)
  function show($pos)
  {
    echo $this->get($pos);
  } // show
} // class SPAW_Toolbars
?>
