<?php

$usrmgr_selection[0] = t('Keine zuweisung');
$usrmgr_selection[1] = t('Alter');
$usrmgr_selection[2] = t('Weiblich');
$usrmgr_selection[3] = t('Männlich');
$usrmgr_selection[4] = t('Ortschaft');

switch ($_GET['step']) {
    case 3:
        if (isset($_GET['group_id'])) {
            $_POST['group_id'] = $_GET['group_id'];
        }
        if ($_POST['group_name'] == "") {
            $error_usrmgr['group'] = t('Gib einen Gruppennamen ein');
            $_GET['step'] = 2;
        }
        
        if ($_POST['selection'] == 1) {
            if (!(preg_match("/^[0-9]+-[0-9]+$/i", trim($_POST['select_opts'])) || preg_match("/^-[0-9]+$/i", trim($_POST['select_opts'])) || preg_match("/^[0-9]+\+$/i", trim($_POST['select_opts'])))) {
                $error_usrmgr['select_opts'] = t('Alter wurde falsch angegeben bitte in der Form 14+ , -18 oder 15-17 angeben.');
                $_GET['step'] = 2;
            }
        }
        if ($_POST['selection'] == 4 && trim($_POST['select_opts']) == "") {
            $error_usrmgr['select_opts'] = t('Bitte eine Stadt angeben');
            $_GET['step'] = 2;
        }
        break;
    
    // Move Up
    case 16:
        $db->qry("UPDATE %prefix%party_usergroups SET pos = 0 WHERE pos = %int%", ($_GET["pos"] - 1));
        $db->qry("UPDATE %prefix%party_usergroups SET pos = pos - 1 WHERE pos = %int%", $_GET["pos"]);
        $db->qry("UPDATE %prefix%party_usergroups SET pos = %string% WHERE pos = 0", $_GET["pos"]);
        $_GET['step'] = 15;
        break;

    // Move Down
    case 17:
        $db->qry("UPDATE %prefix%party_usergroups SET pos = 0 WHERE pos = %int%", ($_GET["pos"] + 1));
        $db->qry("UPDATE %prefix%party_usergroups SET pos = pos + 1 WHERE pos = %int%", $_GET["pos"]);
        $db->qry("UPDATE %prefix%party_usergroups SET pos = %string% WHERE pos = 0", $_GET["pos"]);
        $_GET['step'] = 15;
        break;
    
    case 22:
        if ($_GET['group_id'] == $_POST['group_id']) {
            $_GET['step'] = 21;
        }
        break;
}

switch ($_GET['step']) {
    default:
        $dsp->NewContent(t('Gruppenverwaltung'), t('Erstelle Benutzergruppen um unterschiedliche Preise zu verlangen.'));
        $dsp->AddSingleRow("<a href='index.php?mod=usrmgr&action=group&step=9'>".t('Benutzer einer Gruppe zuweisen')."</a>");

        if ($_GET['var'] == "update") {
            $dsp->AddDoubleRow('', $dsp->FetchSpanButton(t('Hinzufügen'), "index.php?mod=usrmgr&action=group&step=2&var=new"));
            $dsp->SetForm("index.php?mod=usrmgr&action=group&step=3&var=update&group_id={$_POST['group_id']}");
            if (!isset($_POST['group_name'])) {
                $row = $db->qry_first("SELECT * FROM %prefix%party_usergroups WHERE group_id=%int%", $_POST['group_id']);
                $_POST = array_merge_recursive($_POST, $row);
            }
        } else {
            $dsp->SetForm("index.php?mod=usrmgr&action=group&step=3&var=new");
        }
                    
        $dsp->AddTextFieldRow("group_name", t('Gruppenname'), $_POST['group_name'], $error_usrmgr['group']);
        $dsp->AddTextFieldRow("description", t('Benutzergruppenbeschreibung'), $_POST['description'], $error_usrmgr['group_desc']);

        $dsp->AddFormSubmitRow(t('Hinzufügen'));
        
        if ($_GET['var'] != "update") {
            $count = $db->qry_first("SELECT count(group_id) as n FROM %prefix%party_usergroups WHERE selection != 0");
            if ($count['n'] > 1) {
                $dsp->AddHRuleRow();
                $dsp->AddDoubleRow("", "<a href='index.php?mod=usrmgr&action=group&step=15'>".t('Automatisch Zuordnung sortieren')."</a>");
            }
            $dsp->AddHRuleRow();
            $dsp->SetForm("index.php?mod=usrmgr&action=group&step=2&var=update");
            if ($party->get_user_group_dropdown()) {
                $dsp->AddFormSubmitRow(t('Editieren'));
            }
            if ($dsp->form_open) {
                $dsp->CloseForm();
            }
            $dsp->AddHRuleRow();
            $dsp->SetForm("index.php?mod=usrmgr&action=group&step=20");
            if ($party->get_user_group_dropdown()) {
                $dsp->AddFormSubmitRow(t('Löschen'));
            }
            if ($dsp->form_open) {
                $dsp->CloseForm();
            }
        }
        break;
    
    case 3:
        if ($_GET['var'] == "new") {
            $party->add_user_group($_POST['group_name'], $_POST['description'], $_POST['selection'], $_POST['select_opts']);
            $func->confirmation(t('Benutzergruppe wurde hinzugefügt'), 'index.php?mod=usrmgr&action=group&step=2');
        } elseif ($_GET['var'] == "update") {
            $party->update_user_group($_GET['group_id'], $_POST['group_name'], $_POST['description'], $_POST['selection'], $_POST['select_opts']);
            $func->confirmation(t('Benutzergruppe wurde erfolgreich editiert.'), 'index.php?mod=usrmgr&action=group&step=2');
        } else {
            $func->error(t('Die Benutzergruppe konnte nicht angelegt werden.'), 'index.php?mod=usrmgr&action=group&step=2');
        }
        
        break;
    
    case 9:
        $dsp->NewContent(t('Gruppe auswählen'), t('Gruppe auswählen'));
        $dsp->SetForm("index.php?mod=usrmgr&action=group&step=10");
        $party->get_user_group_dropdown();
        $dsp->AddFormSubmitRow(t('Weiter'));
        break;
    
    case 10:
        if (isset($_POST['group_id'])) {
            $_GET['group_id'] = $_POST['group_id'];
        }
        $current_url = "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}";
        $target_url = "index.php?mod=usrmgr&action=group&step=11&group_id={$_GET['group_id']}&userid=";
        include_once('modules/usrmgr/search_basic_userselect.inc.php');
        break;

    case 11:
        if ($_POST['checkbox']) {
            $text = "";
            $userids = "";
            foreach ($_POST['checkbox'] as $userid) {
                $user_data = $db->qry_first("SELECT user.username, g.group_name FROM %prefix%user AS user LEFT JOIN %prefix%party_usergroups AS g ON user.group_id = g.group_id WHERE userid = %int%", $userid);
                if ($user_data["group_name"] != "") {
                    $text .=  "<b>{$user_data["username"]}</b> " . t('ist in der Gruppe') . " <b>" . $user_data["group_name"] . "</b>" . HTML_NEWLINE;
                } else {
                    $text .=  "<b>{$user_data["username"]}</b> " . t('ist in keiner Gruppe') . HTML_NEWLINE;
                }
                $userids .= "$userid,";
            }
            $row = $db->qry_first("SELECT group_name FROM %prefix%party_usergroups WHERE group_id=%int%", $_GET['group_id']);
            $text .= HTML_NEWLINE . t('Willst du diese Benutzer der Gruppe %1 zuweisen?', "\"<b>" .$row['group_name'] . "</b>\"");
            $userids = substr($userids, 0, strlen($userids) - 1);
            $func->question($text, "index.php?mod=usrmgr&action=group&step=12&userids=$userids&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}");
        } elseif ($_GET["userid"]) {
            $user_data = $db->qry_first("SELECT user.username, g.group_name FROM %prefix%user AS user LEFT JOIN %prefix%party_usergroups AS g ON user.group_id = g.group_id WHERE userid = %int%", $_GET["userid"]);
                    
            if ($user_data["username"]) {
                $func->question(t('Willst du den Benutzer %1 der Gruppe %2 zuweisen?', $user_data["username"], $user_data["group_name"]), "index.php?mod=usrmgr&action=group&step=12&userid={$_GET["userid"]}&group_id={$_GET['group_id']}", "index.php?mod=usrmgr&action=group&step=10&group_id={$_GET['group_id']}");
            } else {
                $func->error(t('Dieser Benutzer existiert nicht'), "index.php?mod=usrmgr&action=group&step=10");
            }
        } else {
            $func->error(t('Dieser Benutzer existiert nicht'), "index.php?mod=usrmgr&action=group&step=10");
        }
    
        break;
    
    case 12:
        if ($_GET["userids"]) {
            $userids = explode(",", $_GET["userids"]);
            foreach ($userids as $userid) {
                $db->qry("UPDATE %prefix%user SET group_id = %int% WHERE userid = %int% LIMIT 1", $_GET['group_id'], $_GET["userid"]);
            }
        } else {
            $db->qry("UPDATE %prefix%user SET group_id = %int% WHERE userid = %int% LIMIT 1", $_GET['group_id'], $_GET["userid"]);
        }

        $func->confirmation(t('Die Gruppenzuweisung wurde erfolgreich durchgeführt'), "index.php?mod=usrmgr&action=group&group_id={$_GET['group_id']}");
        break;
    
    // Sort Groups
    case 15:
        $dsp->NewContent(t('Gruppen sortieren'), t('Hier kannst du die Gruppen sortieren in welcher Reihenfolge sie Angewendet werden sollen. Die oberste hat die höchste Priorität'));

        $groups = $db->qry("SELECT * FROM %prefix%party_usergroups WHERE selection != 0 ORDER BY pos");
        $z = 0;
        
        while ($group = $db->fetch_array($groups)) {
            $z++;
            $db->qry("UPDATE %prefix%party_usergroups SET pos = %int% WHERE group_id = %int%", $z, $group["group_id"]);

            $link = "";
            if ($z > 1) {
                $link .= "[<a href=\"index.php?mod=usrmgr&action=group&step=16&pos=$z\">^</a>] ";
            }
            if ($z < $db->num_rows($groups)) {
                $link .= "[<a href=\"index.php?mod=usrmgr&action=group&step=17&pos=$z\">v</a>]";
            }
            $link .= " " . $usrmgr_selection[$group['selection']] . " " . $group['select_opts'];
            
            $dsp->AddDoubleRow("$z) ". $group["group_name"], $link);
        }
        $db->free_result($groups);

        $dsp->AddBackButton("index.php?mod=usrmgr&action=group");
        break;
    
    // Delete Group
    case 20:
        $row = $db->qry_first("SELECT * FROM %prefix%party_usergroups WHERE group_id=%int%", $_POST['group_id']);
        $func->question(t('Wollen sie die Gruppe %1 wirklich löschen?', $row['group_name']), "index.php?mod=usrmgr&action=group&step=21&group_id={$_POST['group_id']}", "index.php?mod=usrmgr&action=group");
        break;
    
    case 21:
        $dsp->NewContent(t('Gruppe zuweisen'), t('Welche Gruppe möchtest du den Benutzern die in der gelöschten Gruppe sind zuweisen?'));
        $dsp->SetForm("index.php?mod=usrmgr&action=group&step=22&group_id={$_GET['group_id']}");
        $party->get_user_group_dropdown("NULL", 1);
        $dsp->AddFormSubmitRow(t('Weiter'));
        break;
    
    case 22:
        $party->delete_usergroups($_GET['group_id'], $_POST['group_id']);
        $func->confirmation(t('Gruppe erfolgreich gelöscht.'), "index.php?mod=usrmgr&action=group");
        break;
    
    // Multi-User-Assign
    case 30:
        foreach ($_POST['action'] as $key => $val) {
            $db->qry("UPDATE %prefix%user SET group_id = %int% WHERE userid = %int%", $_GET['group_id'], $key);
        }
        $func->confirmation(t('Die Gruppenzuweisung wurde erfolgreich durchgeführt'), "index.php?mod=usrmgr");
        break;
}
