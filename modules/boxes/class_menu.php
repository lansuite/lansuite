<?php

/**
 * menu
 *
 * @package lansuite_core
 * @author
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class menu
{
    public $boxid = 0;
    public $caption = '';
    public $box;

  /**
   * menu::menu()
   *
   * @param mixed $id
   * @param mixed $caption
   * @return
   */
    public function __construct($id, $caption, $title = '')
    {
        $this->caption = $caption;
        $this->boxid = $id;
        $this->title = $title;
        $this->box = new boxes();
    }

  /**
   * menu::FetchItem()
   *
   * @param mixed $item
   * @return
   */
    public function FetchItem($item)
    {
        global $cfg, $func;

        $item['caption'] = t($item['caption']);
        $item['hint'] = t($item['hint']);

        // Horrizontal Line IF Caption == '--hr--'
        if ($item['caption'] == '--hr--') {
            switch ($item['level']) {
                default:
                    return $this->box->HRuleRow();
                break;
                case 1:
                    return $this->box->HRuleEngagedRow();
                break;
            }
        } else {
            // Scan for ID in info2 Link
            if ($_GET['mod'] == 'info2') {
                preg_match('/(id=)(\\d{1,4})/', $item['link'], $treffer);
                $info2_id = $treffer[2];
            }
            if (($item['module'] != 'info2' and $item['module'] == $_GET['mod'] and $item['level']==0)
                or ($item['module'] == 'info2' and $item['module'] == $_GET['mod'] and $item['level']==0 and $info2_id == $_GET['id'])
                or ($item['module'] == 'info2' and $_GET['mod'] == 'info2' and $info2_id == $_GET['id'])
                or ($item['module'] == 'info2' and $_GET['mod'] == 'info2' and $cfg['info2_use_submenus']==1 and $item['level']==0)
                or ($item['module'] != 'info2' and $item['module'] == $_GET['mod'] and ($item['action'] == $_GET['action']) and $item['level']==1)
               ) {
                $highlighted = 1;
            } else {
                $highlighted = 0;
            }
            $this->box->add_menuitem($item['caption'], $item['link'], $item['hint'], $item['level'], $item['requirement'], $highlighted);
        }
        return '';
    }

  /**
   * menu::get_menu_items()
   *
   * @return
   */
    public function get_menu_items()
    {
        global $cfg, $func, $auth, $db;

        if (!$_GET['menu_group']) {
            $_GET['menu_group'] = 0;
        }
        // Get Main-Items
        $res = $db->qry("SELECT menu.*
        FROM %prefix%menu AS menu
        LEFT JOIN %prefix%modules AS module ON menu.module = module.name
        WHERE ((module.active) OR (menu.caption = '--hr--'))
        AND (menu.boxid = %int%)
        AND (menu.caption != '') AND (menu.level = 0) AND (menu.group_nr = %int%)
        AND ((menu.requirement = '') OR (menu.requirement = 0)
        OR (menu.requirement = 1 AND %int% = 1)
        OR (menu.requirement = 2 AND %int% > 1)
        OR (menu.requirement = 3 AND %int% > 2)
        OR (menu.requirement = 4 AND %int% = 1)
        OR (menu.requirement = 5 AND %int% = 0))
        ORDER BY menu.pos", $this->boxid, $_GET['menu_group'], $auth['login'], $auth['type'], $auth['type'], $auth['type'], $auth['login']);

        while ($main_item = $db->fetch_array($res)) {
            if ($main_item['needed_config'] == '' or call_user_func($main_item['needed_config'], '')) {
                $this->FetchItem($main_item);

            // If selected Module: Get Sub-Items
                if (isset($_GET['module'])) {
                    $module = $_GET['module'];
                } else {
                    $module = $_GET['mod'];
                }
                if ($module and $main_item['module'] == $module and $main_item['action'] != 'show_info2') {
                    $res2 = $db->qry("SELECT menu.*
                    FROM %prefix%menu AS menu
                    WHERE (menu.caption != '') AND (menu.level = 1) AND (menu.module = %string%)
                    AND ((menu.requirement = '') OR (menu.requirement = 0)
                    OR (menu.requirement = 1 AND %int% = 1)
                    OR (menu.requirement = 2 AND %int% > 1)
                    OR (menu.requirement = 3 AND %int% > 2)
                    OR (menu.requirement = 4 AND %int% = 1)
                    OR (menu.requirement = 5 AND %int% = 0))
                    ORDER BY menu.requirement, menu.pos", $module, $auth['login'], $auth['type'], $auth['type'], $auth['type'], $auth['login']);
                    while ($sub_item = $db->fetch_array($res2)) {
                        if ($sub_item['needed_config'] == '' or call_user_func($sub_item['needed_config'], '')) {
                            $this->FetchItem($sub_item);
                        }
                    }
                    $db->free_result($res2);

                // If Admin add general Management-Links
                    if ($auth['type'] > 2) {
                        $AdminIcons .= $this->box->LinkItem('index.php?mod=install&amp;action=mod_cfg&amp;module='. $module, t('Mod-Konfig'), 'admin', t('Dieses Modul verwalten'));
                        $this->box->Row('<span class="AdminIcons">'. $AdminIcons .'</span>');
                    }
                }
            }
        }
        $db->free_result($res);
        if ($this->box->box_rows) {
            return $this->box->CreateBox($this->boxid, t($this->caption), $this->title);
        }
    }
}

$MenuCallbacks = array();
$MenuCallbacks[] = 'ShowSignon';
$MenuCallbacks[] = 'ShowGuestMap';
$MenuCallbacks[] = 'sys_internet';
$MenuCallbacks[] = 'snmp';
$MenuCallbacks[] = 'DokuWikiNotInstalled';

// Callbacks
/**
 * ShowSignon()
 *
 * @return
 */
function ShowSignon()
{
    global $cfg, $auth;

    if ($cfg['signon_partyid'] or !$auth['login']) {
        return true;
    } else {
        return false;
    }
}

/**
 * ShowGuestMap()
 *
 * @return
 */
function ShowGuestMap()
{
    global $cfg;

    if ($cfg['guestlist_guestmap']) {
        return true;
    } else {
        return false;
    }
}

/**
 * sys_internet()
 *
 * @return
 */
function sys_internet()
{
    global $cfg;

    if ($cfg['sys_internet']) {
        return true;
    } else {
        return false;
    }
}

/**
 * snmp()
 *
 * @return
 */
function snmp()
{
    if (extension_loaded('snmp')) {
        return true;
    } else {
        return false;
    }
}

/**
 * DokuWikiNotInstalled()
 *
 * @return
 */
function DokuWikiNotInstalled()
{
    if (!file_exists('ext_scripts/dokuwiki/conf/local.php')) {
        return true;
    } else {
        return false;
    }
}
