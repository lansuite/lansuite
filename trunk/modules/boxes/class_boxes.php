<?php


/**
 * boxes
 *
 * @package ls_core
 * @author bytekilla, knox
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class boxes {

    var $box_rows = '';

  /**
   * Constructor
   *
   */
    function boxes() {
    }

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
    function add_menuitem($caption,$link='',$hint='',$level=0,$requirement=0,$highlighted=0){
        global $func;
        // Set Item-Class
        switch ($requirement){
            default: $link_class = 'menu'; break;
            case 2:
            case 3: $link_class = 'admin'; break;
        }
        switch ($level) {
            case 0: $class = "box_entry"; break;
            case 1: $class = "box_entry_lvl_1"; break;
        }  

        if ($highlighted) $class .= "_active";
        if ($link != "") {
            if ($hint) $box_row_hint = '<span class="infobox">'. $func->AllowHTML($hint) .'</span>';
            $tmp_link = '<a href="'.$func->AllowHTML($link).'" class="'.$link_class.'">'.$caption.$box_row_hint.'</a>';
        }
        if (strip_tags($caption) == $caption) $caption = wordwrap($caption, 18,"<br />\n",1);
        $this->box_rows .= "<li class=\"".$class."\">".$tmp_link."</li>\n";
            
    }

    function LinkItem($link, $caption, $class = "", $hint='') {
        global $func;
        if ($link != "") {
            if ($hint) $box_row_hint = '<span class="infobox">'. $func->AllowHTML($hint) .'</span>';
            $out = '<a href="'.$link.'" class="'.$class.'">'.$caption.$box_row_hint.'</a>';
            return $out;
        } else return $caption;
    }

    function ItemRow($item, $caption, $link = "", $hint = "", $class = "") {
        if (strip_tags($caption) == $caption) $caption = wordwrap($caption, 18,"<br />\n",1);
        $this->box_rows .= "<li class=\"box_entry".$item."\">".$this->LinkItem($link, $caption, $class, $hint)."</li>\n";
    }

    function Row($row) {
        $this->box_rows .= "<li>".$row."</li>\n";
    }

    function HRuleRow() {
        $this->box_rows .= "<hr class=\"hrule\" width=\"100%\" />\n";
    }

    function HRuleEngagedRow() {
        $this->box_rows .= "<hr class=\"hrule\" width=\"90%\" align=\"right\" />";
    }

    function DotRow($caption, $link = "", $hint = "", $class = "", $highlighted = "") {
        if ($highlighted) $item = "_active";
        $this->ItemRow($item, $caption, $link, $hint, $class);
    }

    function EmptyRow() {
        $this->box_rows .= '<br />';
    }

    function EngangedRow($caption, $link = "", $hint = "", $class = "") {
        if (strip_tags($caption) == $caption) $caption = wordwrap($caption, 18,"<br />\n",1);
        $this->box_rows .= "<li class=\"engaged\" title=\"".$hint."\">".$this->LinkItem($link, $caption, $class)."</li>\n";
    }

    function AddTemplate($template) {
        global $dsp;
        $this->box_rows .= $this->Row($dsp->FetchModTpl("boxes", $template));
    }

    function CreateBox($boxid, $caption = "") {
        global $smarty, $auth;
        if ($this->box_rows != '') $smarty->assign('content', $this->box_rows);
        switch((int)$boxid) {
            case 1: $title = 'menu'; break;
            case 2: $title = 'search'; break;
            case 3: $title = 'sponsor'; break;
            case 4: $title = 'info'; break;
            case 5: $title = 'last_user'; break;
            case 6: $title = 'user'; break;
            case 7: $title = 'login'; break;
            case 8: $title = 'stats'; break;
            case 9: $title = 'signon_state'; break;
            case 10: $title = 'messenger'; break;
            case 11: $title = 'wwcl'; break;
        }
        $smarty->assign('title', $title);
        $smarty->assign('default_design', $auth["design"]);
        $smarty->assign('caption', $caption);            
        $smarty->assign('link_open_close', "index.php?box_action=change&amp;boxid=$boxid");
        // Open or closed Box
        if (!$_SESSION['box_'. $boxid .'_active']) $file = 'design/'. $auth['design'] .'/templates/box_case.htm';
            else $file = 'design/'. $auth['design'] .'/templates/box_case_closed.htm';
        $out = $smarty->fetch($file);
        $this->box_rows = '';
        return $out;
        
    }
}

?>