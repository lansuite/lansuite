<?php


/**
 * boxes
 *
 * @package lansuite_core
 * @author bytekilla, knox
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class boxes
{
    public $box_rows = '';

  /**
   * Add a Menueitem to Navibar
   *
   * @param mixed Linktext
   * @param string Link
   * @param string Hinttext for Popup
   * @param integer Navigationslevel (0=Main, 1=Sub, etc)
   * @param integer Accesslevel (depend on auth['type'])
   * @param integer Highligt active Menueitem (0=off, 1=on)
   * @return void
   */
    public function add_menuitem($caption, $link = '', $hint = '', $level = 0, $requirement = 0, $highlighted = 0, $id = '')
    {
        global $func;

      // Set Item-Class
        switch ($requirement) {
            default:
                $link_class = 'menu';
                break;
            case 2:
            case 3:
                $link_class = 'admin';
                break;
        }
        switch ($level) {
            case 0:
                $class = "box_entry";
                break;
            case 1:
                $class = "box_entry_lvl_1";
                break;
        }

        if ($highlighted) {
            $class .= "_active";
        }
        if ($link != "") {
            if ($hint) {
                $box_row_hint = '<span class="infobox">'. $func->AllowHTML($hint) .'</span>';
            }
            $tmp_link = '<a href="'.$func->AllowHTML($link).'" class="'.$link_class.'">'.$caption.$box_row_hint.'</a>';
        }
        if (strip_tags($caption) == $caption) {
            $caption = wordwrap($caption, 18, "<br />\n", 1);
        }
        if ($id) {
            $id = ' id="'. $id .'"';
        }
        $this->box_rows .= '<li'. $id .' class="'. $class .'">'. $tmp_link ."</li>\n";
    }

  /**
   * boxes::LinkItem()
   *
   * @param mixed $link
   * @param mixed $caption
   * @param string $class
   * @param string $hint
   * @return
   */
    public function LinkItem($link, $caption, $class = "", $hint = '')
    {
        global $func;
        if ($link != "") {
            if ($hint) {
                $box_row_hint = '<span class="infobox">'. $func->AllowHTML($hint) .'</span>';
            }
            $out = '<a href="'.$link.'" class="'.$class.'">'.$caption.$box_row_hint.'</a>';
            return $out;
        } else {
            return $caption;
        }
    }

  /**
   * boxes::ItemRow()
   *
   * @param mixed $item
   * @param mixed $caption
   * @param string $link
   * @param string $hint
   * @param string $class
   * @return
   */
    public function ItemRow($item, $caption, $link = "", $hint = "", $class = "")
    {
        if (strip_tags($caption) == $caption) {
            $caption = wordwrap($caption, 18, "<br />\n", 1);
        }
        $this->box_rows .= "<li class=\"box_entry".$item."\">".$this->LinkItem($link, $caption, $class, $hint)."</li>\n";
    }

    public function StartHidden($id, $hide = 0, $class = '')
    {
        if ($hide) {
            $hide = ' style="display:none"';
        }
        if ($class) {
            $class = ' class="'. $class .'"';
        }
        $this->box_rows .= "<ul id=\"$id\"$class$hide>";
    }

    public function StopHidden()
    {
        $this->box_rows .= "</ul>";
    }
    
    public function AddJQueryTabsStart($id, $dynamic = null)
    {
        global $framework;
    
        if ($dynamic) {
            $framework->add_js_code("
				$(document).ready(function(){
					$('#".$id."').tabs();
				});");
        }
        $this->box_rows .= "<div class='ui-tabs ui-widget ui-widget-content ui-corner-all' id='".$id."'>\n";
    }
    
    public function AddJQueryTabNavStart()
    {
        $this->box_rows .= "  <ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>\n";
    }
    
    public function AddJQueryTab($content, $link, $title = null, $selected = null)
    {
    
#		if($selected) $additional = " ui-tabs-selected ui-state-active";
#		$this->box_rows .= "    <li class='ui-state-default ui-corner-top".$additional."'><a href='".$link."' title='".$title."'><em>".$content."</em></a></li>\n";
    }
    
    public function AddJQueryTabNavStop()
    {
        $this->box_rows .= "  </ul>\n";
    }
    
    public function AddJQueryTabContentStart()
    {
        $this->box_rows .= "  <div class='ui-content'>\n";
    }
    
    public function AddJQueryTabContent($id, $content)
    {
        $this->box_rows .= "    <div id='".$id."'>\n";
        $this->box_rows .= "      ".$content."\n";
        $this->box_rows .= "    </div>\n";
    }
    
    public function AddJQueryTabContentStop()
    {
        $this->box_rows .= "  </div>\n";
    }
    
    public function AddJQueryTabsStop()
    {
        $this->box_rows .= "</div>\n";
    }
  /**
   * boxes::Row()
   *
   * @param mixed $row
   * @return
   */
    public function Row($row, $name = '')
    {
        if ($name) {
            $name = ' name="'. $name .'"';
        }
        $this->box_rows .= "<li$name>".$row."</li>\n";
    }
    
    public function RowClean($row)
    {
        $this->box_rows .= $row."\n";
    }

  /**
   * boxes::HRuleRow()
   *
   * @return
   */
    public function HRuleRow()
    {
        $this->box_rows .= "<hr class=\"hrule\" width=\"100%\" />\n";
    }

  /**
   * boxes::HRuleEngagedRow()
   *
   * @return
   */
    public function HRuleEngagedRow()
    {
        $this->box_rows .= "<hr class=\"hrule\" width=\"90%\" align=\"right\" />";
    }

  /**
   * boxes::DotRow()
   *
   * @param mixed $caption
   * @param string $link
   * @param string $hint
   * @param string $class
   * @param string $highlighted
   * @return
   */
    public function DotRow($caption, $link = "", $hint = "", $class = "", $highlighted = "")
    {
        if ($highlighted) {
            $item = "_active";
        }
        $this->ItemRow($item, $caption, $link, $hint, $class);
    }

  /**
   * boxes::EmptyRow()
   *
   * @return
   */
    public function EmptyRow()
    {
        $this->box_rows .= '<br />';
    }

  /**
   * boxes::EngangedRow()
   *
   * @param mixed $caption
   * @param string $link
   * @param string $hint
   * @param string $class
   * @return
   */
    public function EngangedRow($caption, $link = "", $hint = "", $class = "")
    {
        if (strip_tags($caption) == $caption) {
            $caption = wordwrap($caption, 18, "<br />\n", 1);
        }
        $this->box_rows .= "<li class=\"engaged\" title=\"".$hint."\">".$this->LinkItem($link, $caption, $class)."</li>\n";
    }

  /**
   * boxes::AddTemplate()
   *
   * @param mixed $template
   * @return
   */
    public function AddTemplate($template)
    {
        global $dsp;
        $this->box_rows .= $this->Row($template);
    }

  /**
   * boxes::CreateBox()
   *
   * @param mixed $boxid
   * @param string $caption
   * @return
   */
    public function CreateBox($boxid, $caption = '', $title = '', $module = '')
    {
        global $smarty, $auth;
        if ($this->box_rows != '') {
            $smarty->assign('content', $this->box_rows);
        }
        if (!$title) {
            switch ((int)$boxid) {
                case 1:
                    $title = 'menu';
                    break;
                case 2:
                    $title = 'search';
                    break;
                case 3:
                    $title = 'sponsor';
                    break;
                case 4:
                    $title = 'info';
                    break;
                case 5:
                    $title = 'last_user';
                    break;
                case 6:
                    $title = 'user';
                    break;
                case 7:
                    $title = 'login';
                    break;
                case 8:
                    $title = 'stats';
                    break;
                case 9:
                    $title = 'signon_state';
                    break;
                case 10:
                    $title = 'messenger';
                    break;
                case 11:
                    $title = 'wwcl';
                    break;
            }
        }
        $smarty->assign('title', $title);
        $smarty->assign('caption', $caption);
        $smarty->assign('module', $module);
        $smarty->assign('link_open_close', "index.php?box_action=change&amp;boxid=$boxid");
        // Open or closed Box
        if (!$_SESSION['box_'. $boxid .'_active']) {
            $file = 'design/'. $auth['design'] .'/templates/box_case.htm';
        } else {
            $file = 'design/'. $auth['design'] .'/templates/box_case_closed.htm';
        }
        if ($title) {
            $out = $smarty->fetch($file, 'box'.$title.$auth['type']);
        } else {
            $smarty->fetch($file);
        }
        return $out;
    }
}
