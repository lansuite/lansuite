<?php
include_once('modules/boxes/class_boxes.php');

/**
 * PartyAvailible()
 *
 * @return
 */
    function PartyAvailible() {
      global $party;
      if ($party->count > 0) return 1;
      else return 0;
    }
    
/**
 * MsgInIntMode()
 *
 * @return
 */
    function MsgInIntMode() {
      global $cfg;
      if (!$cfg['sys_internet'] or $cfg['msgsys_alwayson']) return 1;
      else return 0;
    }
    
/**
 * IsWWCLT()
 *
 * @return
 */
    function IsWWCLT() {
      global $db, $config, $party;
      if ($_GET['mod'] != 'tournament2') return 0;
      else {
        $row = $db->qry_first("SELECT 1 AS found FROM %prefix%tournament_tournaments WHERE wwcl_gameid > 0 AND party_id = %int%", $party->party_id);
        if ($row['found']) return 1;
        else return 0;
      }
    }

### In LogOff state all boxes are visible (no ability to minimize them)

    if ($auth['login'] == "1") {
        // Change state, when Item is clicked
        if ($_GET['box_action'] == 'change' and $_GET['boxid'] != "") {
            if ($_SESSION['box_'. $_GET['boxid'] .'_active']) unset($_SESSION['box_'. $_GET['boxid'] .'_active']);
            else $_SESSION['box_'. $_GET['boxid'] .'_active'] = 1;
        }
    }

### Generate Boxes

    // Fetch Boxes
    $BoxRes = $db->qry("SELECT boxid, name, place, source, module, callback, login, internet FROM %prefix%boxes
                        WHERE active = 1
                            AND (internet = 0 OR internet = %int% + 1)
                            AND (login = 0 OR (login = 1 AND %int% = 0) OR (login = 2 AND %int% = 1) OR (login > 2 AND login <= %int% + 1))
                        ORDER BY pos
                        ", $cfg['sys_internet'], $auth['login'], $auth['login'], $auth['type']);
    $MenuActive = 0;
    
    // Boxloop
    while ($BoxRow = $db->fetch_array($BoxRes)) if (($BoxRow['module'] == '' or in_array($BoxRow['module'], $ActiveModules)) and ($BoxRow['callback'] == '' or call_user_func($BoxRow['callback'], ''))) {
      if ($BoxRow['source'] == 'menu') {
        // Generate Menuboxes (Links, etc)
        include_once('modules/boxes/class_menu.php');
        $menu = new menu($BoxRow['boxid'], $BoxRow['name'], $BoxRow['source']);
        if ($BoxRow['place'] == 0) $templ['index']['control']['boxes_letfside'] .= $menu->get_menu_items();
        elseif ($BoxRow['place'] == 1) $templ['index']['control']['boxes_rightside'] .= $menu->get_menu_items();
        if ($menu->box->box_rows) $MenuActive = 1;            
      } else {
        $box = new boxes();
        // Generate Funktionboxes
        // Load file
        if (!$_SESSION['box_'. $BoxRow['boxid'] .'_active']) include_once('modules/boxes/'. $BoxRow['source'] .'.php');
        // Write content to template var
        if ($BoxRow['place'] == 0) $templ['index']['control']['boxes_letfside'] .= $box->CreateBox($BoxRow['boxid'], t($BoxRow['name']),t($BoxRow['name']));
        elseif ($BoxRow['place'] == 1) $templ['index']['control']['boxes_rightside'] .= $box->CreateBox($BoxRow['boxid'], t($BoxRow['name']),t($BoxRow['name']));
      }
    }
    $db->free_result($BoxRes);
    
    // Add Link to boxmanager, if menu is missing and loginbox, if not logged in
    if (!$MenuActive) {
      if ($auth['type'] >= 2) {
        $box = new boxes();
        $box->Row(t('Keine Navigation gefunden. Bitte korrekte zuweisung Box / Navigation prüfen (BoxID). Temporäre Links aktiviert.'));
        $box->EmptyRow();
        $box->DotRow('Boxmanager', 'index.php?mod=boxes');
        $box->DotRow('Admin-Seite', 'index.php?mod=install');
        $templ['index']['control']['boxes_letfside'] .= $box->CreateBox(0, t('Temporär'));
      }
    }

?>