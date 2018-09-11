<?php

namespace LanSuite\Module\Boxes;

class Boxes
{
    /**
     * @var string
     */
    public $box_rows = '';

    /**
     * Add a menu item to navigation bar
     *
     * @param string $caption
     * @param string $link
     * @param string $hint
     * @param int $level
     * @param int $requirement
     * @param int $highlighted
     * @param string $id
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

        $class = '';
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

        if (strip_tags($caption) == $caption) {
            $caption = wordwrap($caption, 18, "<br />\n", 1);
        }

        $tmp_link = '';
        if ($link != "") {
            $box_row_hint = '';
            if ($hint) {
                $box_row_hint = '<span class="infobox">'. $func->AllowHTML($hint) .'</span>';
            }
            $tmp_link = '<a href="'.$func->AllowHTML($link).'" class="'.$link_class.'">'.$caption.$box_row_hint.'</a>';
        }

        if ($id) {
            $id = ' id="'. $id .'"';
        }
        $this->box_rows .= '<li'. $id .' class="'. $class .'">'. $tmp_link ."</li>\n";
    }

    /**
     * @param string $link
     * @param string $caption
     * @param string $class
     * @param string $hint
     * @return string
     */
    public function LinkItem($link, $caption, $class = "", $hint = '')
    {
        global $func;
        if ($link != "") {
            $box_row_hint = '';
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
     * @param string $item
     * @param string $caption
     * @param string $link
     * @param string $hint
     * @param string $class
     * @return void
     */
    public function ItemRow($item, $caption, $link = "", $hint = "", $class = "")
    {
        if (strip_tags($caption) == $caption) {
            $caption = wordwrap($caption, 18, "<br />\n", 1);
        }
        $this->box_rows .= "<li class=\"box_entry".$item."\">".$this->LinkItem($link, $caption, $class, $hint)."</li>\n";
    }

    /**
     * @param string $row
     * @param string $name
     * @return void
     */
    public function Row($row, $name = '')
    {
        if ($name) {
            $name = ' name="'. $name .'"';
        }
        $this->box_rows .= "<li$name>".$row."</li>\n";
    }

    /**
     * @return void
     */
    public function HRuleRow()
    {
        $this->box_rows .= "<hr class=\"hrule\" width=\"100%\" />\n";
    }

    /**
     * @return void
     */
    public function HRuleEngagedRow()
    {
        $this->box_rows .= "<hr class=\"hrule\" width=\"90%\" align=\"right\" />";
    }

    /**
     * @param string $caption
     * @param string $link
     * @param string $hint
     * @param string $class
     * @param string $highlighted
     */
    public function DotRow($caption, $link = "", $hint = "", $class = "", $highlighted = "")
    {
        $item = '';
        if ($highlighted) {
            $item = "_active";
        }
        $this->ItemRow($item, $caption, $link, $hint, $class);
    }

    /**
     * @return void
     */
    public function EmptyRow()
    {
        $this->box_rows .= '<br />';
    }

    /**
     * @param string $caption
     * @param string $link
     * @param string $hint
     * @param string $class
     * @return void
     */
    public function EngangedRow($caption, $link = "", $hint = "", $class = "")
    {
        if (strip_tags($caption) == $caption) {
            $caption = wordwrap($caption, 18, "<br />\n", 1);
        }
        $this->box_rows .= "<li class=\"engaged\" title=\"".$hint."\">".$this->LinkItem($link, $caption, $class)."</li>\n";
    }

    /**
     * @param string $template
     * @return void
     */
    public function AddTemplate($template)
    {
        $this->Row($template);
    }

    /**
     * @param int $boxid
     * @param string $caption
     * @param string $title
     * @param string $module
     * @return string
     * @throws \Exception
     * @throws \SmartyException
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
            $out = $smarty->fetch($file);
        }
        return $out;
    }
}
