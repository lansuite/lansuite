<?php

namespace LanSuite\Module\Boxes;

class Menu
{
    /**
     * @var int|mixed
     */
    private $boxid = 0;

    /**
     * @var mixed|string
     */
    private $caption = '';

    /**
     * @var Boxes
     */
    public $box;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @param $id
     * @param $caption
     * @param string $title
     */
    public function __construct($id, $caption, $title = '')
    {
        $this->caption = $caption;
        $this->boxid = $id;
        $this->title = $title;
        $this->box = new Boxes();
    }

    /**
     * @param array $item
     * @return void
     */
    private function FetchItem($item)
    {
        global $cfg;

        $item['caption'] = t($item['caption']);
        $item['hint'] = t($item['hint']);

        // Horizontal Line IF Caption == '--hr--'
        if ($item['caption'] == '--hr--') {
            switch ($item['level']) {
                default:
                    $this->box->HRuleRow();
                    return;
                break;
                case 1:
                    $this->box->HRuleEngagedRow();
                    return;
                break;
            }
        } else {
            $info2_id = '';

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
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    public function get_menu_items()
    {
        global $auth, $db;

        if (!$_GET['menu_group']) {
            $_GET['menu_group'] = 0;
        }

        // Get Main-Items
        $res = $db->qry("
          SELECT menu.*
          FROM %prefix%menu AS menu
          LEFT JOIN %prefix%modules AS module ON menu.module = module.name
          WHERE
            (
              (module.active)
              OR (menu.caption = '--hr--')
            )
            AND (menu.boxid = %int%)
            AND (menu.caption != '')
            AND (menu.level = 0)
            AND (menu.group_nr = %int%)
            AND (
              (menu.requirement = '')
              OR (menu.requirement = 0)
              OR (menu.requirement = 1 AND %int% = 1)
              OR (menu.requirement = 2 AND %int% > 1)
              OR (menu.requirement = 3 AND %int% > 2)
              OR (menu.requirement = 4 AND %int% = 1)
              OR (menu.requirement = 5 AND %int% = 0)
            )
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
                    $res2 = $db->qry("
                      SELECT menu.*
                      FROM %prefix%menu AS menu
                      WHERE
                        (menu.caption != '')
                        AND (menu.level = 1)
                        AND (menu.module = %string%)
                        AND (
                          (menu.requirement = '')
                          OR (menu.requirement = 0)
                          OR (menu.requirement = 1 AND %int% = 1)
                          OR (menu.requirement = 2 AND %int% > 1)
                          OR (menu.requirement = 3 AND %int% > 2)
                          OR (menu.requirement = 4 AND %int% = 1)
                          OR (menu.requirement = 5 AND %int% = 0)
                        )
                      ORDER BY menu.requirement, menu.pos", $module, $auth['login'], $auth['type'], $auth['type'], $auth['type'], $auth['login']);
                    while ($sub_item = $db->fetch_array($res2)) {
                        if ($sub_item['needed_config'] == '' or call_user_func($sub_item['needed_config'], '')) {
                            $this->FetchItem($sub_item);
                        }
                    }
                    $db->free_result($res2);

                    // If Admin add general Management-Links
                    if ($auth['type'] > 2) {
                        $adminIcons = $this->box->LinkItem('index.php?mod=install&amp;action=mod_cfg&amp;module='. $module, t('Mod-Konfig'), 'admin', t('Dieses Modul verwalten'));
                        $this->box->Row('<span class="AdminIcons">'. $adminIcons .'</span>');
                    }
                }
            }
        }
        $db->free_result($res);
        if ($this->box->box_rows) {
            return $this->box->CreateBox($this->boxid, t($this->caption), $this->title);
        }

        return '';
    }
}
