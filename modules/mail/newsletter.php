<?php

$mail = new \LanSuite\Module\Mail\Mail();

switch ($_GET["step"]) {
    case 2:
        if ($_POST["subject"] == "") {
            $subject_error = t('Bitte gib einen Betreff an');
            $_GET["step"] = 1;
        }
        if ($_POST["text"] == "") {
            $text_error = t('Bitte gib einen Text an');
            $_GET["step"] = 1;
        }
        if (($_POST["toinet"] == "") && ($_POST["tosys"] == "")) {
            $inet_error = t('Bitte wähle mindestens ein Ziel aus');
            $_GET["step"] = 1;
        }
        break;
}

switch ($_GET["step"]) {
    default:
        $dsp->NewContent(t('Rundmail versenden'), t('Hier kannst du eine Rundmail an alle Benutzer senden.'));
        $dsp->SetForm("index.php?mod=mail&action=newsletter&step=2");

        if ($_POST["onlynewsletter"] == "") {
            $_POST["onlynewsletter"] = 1;
        }
        if ($_POST["toinet"] == "") {
            $_POST["toinet"] = 1;
        }

        $dsp->AddFieldSetStart('Zielgruppen-Einschränkung');
        $dsp->AddCheckBoxRow("onlynewsletter", t('Nur Newsletter'), t('Nur an Benutzer, die den Newsletter bei der Anmeldung bestellt haben'), "", 1, $_POST["onlynewsletter"]);

        $t_array = array();
        array_push($t_array, '<option $selected value="0">'. t('An alle Benutzer') .'</option>');
        array_push($t_array, '<option $selected value="-1">'. t('Zu keiner Party angemeldet') .'</option>');
        $row = $db->qry("SELECT party_id, name FROM %prefix%partys");
        while ($res = $db->fetch_array($row)) {
            array_push($t_array, '<option $selected value="'. $res['party_id'] .'">'. $res['name'] .'</option>');
        }
        $db->free_result($row);
        $dsp->AddDropDownFieldRow("onlysignon", t('Nur Angemeldete an folgender Party'), $t_array, '');

        $t_array = array();
        array_push($t_array, "<option $selected value=\"0\">".t('An alle Benutzer')."</option>");
        array_push($t_array, "<option $selected value=\"1\">".t('Nur an Benutzer, die bezahlt haben')."</option>");
        array_push($t_array, "<option $selected value=\"2\">".t('Nur an Benutzer, die NICHT bezahlt haben')."</option>");
        $dsp->AddDropDownFieldRow("onlypaid", t('Nur Benutzer die zu oben ausgewählter Party bezahlt haben'), $t_array, '');

        $t_array = array();
        array_push($t_array, "<option $selected value=\"0\">". t('An alle Benutzer') ."</option>");
        array_push($t_array, "<option $selected value=\"1\">". t('Nur an Gäste') ."</option>");
        array_push($t_array, "<option $selected value=\"2\">". t('Nur an Admins und Superadminen') ."</option>");
        array_push($t_array, "<option $selected value=\"3\">". t('Nur an Superadminen') ."</option>");
        $dsp->AddDropDownFieldRow("type", t('Nur an folgende Benutzertypen'), $t_array, '');

        $t_array = array();
        array_push($t_array, '<option $selected value="0">'. t('An alle Gruppen') .'</option>');
        array_push($t_array, '<option $selected value="-1">'. t('Nur an Benutzer ohne Gruppe') .'</option>');
        $row = $db->qry("SELECT group_id, group_name FROM %prefix%party_usergroups");
        while ($res = $db->fetch_array($row)) {
            array_push($t_array, '<option $selected value="'. $res['group_id'] .'">'. $res['group_name'] .'</option>');
        }
        $db->free_result($row);
        $dsp->AddDropDownFieldRow("group_id", t('Nur an folgende Gruppen'), $t_array, '');
        
        // Clanfilter
        $t_array = array();
        array_push($t_array, '<option $selected value="0">'. t('An alle Clans') .'</option>');
        array_push($t_array, '<option $selected value="-1">'. t('Nur an Benutzer ohne Clan') .'</option>');
        $row = $db->qry("SELECT clanid, name FROM %prefix%clan");
        while ($res = $db->fetch_array($row)) {
            array_push($t_array, '<option $selected value="'. $res['clanid'] .'">'. $res['name'] .'</option>');
        }
        $db->free_result($row);
        $dsp->AddDropDownFieldRow("clan_id", t('Nur an folgenden Clan'), $t_array, '');
        $dsp->AddFieldSetEnd();
        
        $dsp->AddFieldSetStart(t('Zielsysteme'));
        $dsp->AddCheckBoxRow("toinet", t('E-Mail-Adresse'), t('An die bei der Anmeldung angegebene E-Mail-Adresse'), $inet_error, 1, $_POST["toinet"]);
        $dsp->AddCheckBoxRow("tosys", t('System-Mailbox'), t('An die System-Mailbox des Benutzers'), "", 1, $_POST["tosys"]);
        $dsp->AddFieldSetEnd();

        $dsp->AddFieldSetStart('Nachricht');
        $dsp->AddTextFieldRow("subject", t('Betreff'), $_POST["subject"], $subject_error);
        $dsp->AddTextAreaMailRow("text", t('Text'), $_POST["text"], $text_error);
        $dsp->AddFieldSetEnd();

        $dsp->AddFormSubmitRow(t('Senden'));
        break;

    case 2:
        $where = "u.username != 'LS_SYSTEM'";
        if ($_POST["onlynewsletter"]) {
            $where .= ' AND u.newsletter = 1 ';
        }

        if ($_POST['onlysignon'] == -1) {
            $where .= ' AND p.party_id IS NULL';
        } elseif ($_POST['onlysignon']) {
            $where .= " AND p.party_id=". (int)$_POST['onlysignon'];
        }

        if ($_POST["onlypaid"] == 1) {
            $where .= " AND p.paid > 0";
        } elseif ($_POST["onlypaid"] == 2) {
            $where .= " AND p.paid = 0";
        }

        if ($_POST["type"] == 1) {
            $where .= " AND u.type = 1";
        } elseif ($_POST["type"] == 2) {
            $where .= " AND (u.type = 2 OR u.type = 3)";
        } elseif ($_POST["type"] == 3) {
            $where .= " AND u.type = 3";
        } else {
            $where .= " AND u.type > 0";
        }

        if ($_POST['group_id'] == -1) {
            $where .= ' AND u.group_id = 0';
        } elseif ($_POST['group_id']) {
            $where .= " AND u.group_id=". (int)$_POST['group_id'];
        }
        
        // Clanfilter
        if ($_POST['clan_id'] == -1) {
            $where .= ' AND u.clanid = 0';
        } elseif ($_POST['clan_id']) {
            $where .= " AND u.clanid=". (int)$_POST['clan_id'];
        }

        $success = "";
        $fail = "";
        $users = $db->qry("
          SELECT
            s.ip,
            u.*,
            p.*,
            c.name AS clan,
            c.url AS clanurl
          FROM %prefix%user AS u
            LEFT JOIN %prefix%party_user AS p ON p.user_id=u.userid
            LEFT JOIN %prefix%clan AS c ON c.clanid=u.clanid
            LEFT JOIN %prefix%seat_seats AS s ON s.userid=u.userid
          WHERE %plain%
          GROUP BY u.email", $where);

        while ($user = $db->fetch_array($users)) {
            $text = $__POST["text"];

            // Replace variables
            $text = str_replace("%USERNAME%", $user["username"], $text);
            $text = str_replace("%VORNAME%", $user["firstname"], $text);
            $text = str_replace("%NACHNAME%", $user["name"], $text);
            $text = str_replace("%EMAIL%", $user["email"], $text);
            $text = str_replace("%CLAN%", $user["clan"], $text);
            $text = str_replace("%CLANURL%", $user["clanurl"], $text);
            
            $text = str_replace("%PARTYNAME%", $party_data["name"], $text);
            $text = str_replace('%PARTYURL%', (!empty($cfg['sys_partyurl_ssl'])) ? $cfg["sys_partyurl_ssl"] : $cfg["sys_partyurl"], $text);
            $text = str_replace("%MAXGUESTS%", $party_data['max_guest'], $text);
            
            $text = str_replace("%WWCLID%", $user["wwclid"], $text);
            $text = str_replace("%WWCLCLANID%", $user["wwclclanid"], $text);
            $text = str_replace("%NGLID%", $user["nglid"], $text);
            $text = str_replace("%NGLCLANID%", $user["nglclanid"], $text);
            $text = str_replace("%IP%", $user["ip"], $text);

            ($user["paid"]) ? $text = str_replace("%BEZAHLT%", t('Ja'), $text)
                : $text = str_replace("%BEZAHLT%", t('Nein'), $text);

            ($user["checkin"]) ? $text = str_replace("%EINGECHECKT%", t('Ja'), $text)
                : $text = str_replace("%EINGECHECKT%", t('Nein'), $text);

            ($user["party_id"]) ? $text = str_replace("%ANGEMELDET%", t('Ja'), $text)
                : $text = str_replace("%ANGEMELDET%", t('Nein'), $text);

            // Send mail
            if ($_POST["toinet"]) {
                if ($mail->create_inet_mail($user["firstname"] ." ". $user["name"], $user["email"], $_POST["subject"], $text, $cfg["sys_party_mail"])) {
                    $success .= $user["firstname"] ." ". $user["name"] ."[". $user["email"] ."]" . HTML_NEWLINE;
                } else {
                    $fail .= $user["firstname"] ." ". $user["name"] ."[". $user["email"] ."]" . HTML_NEWLINE;
                }
            }
            if ($_POST["tosys"]) {
                $mail->create_sys_mail($user["userid"], $__POST["subject"], $text);
            }
        }
        $db->free_result($users);

        if ($_POST["toinet"]) {
            $inet_success = t('Die Mail wurde erfolgreich an folgende Benutzer gesendet:') .HTML_NEWLINE. $success .HTML_NEWLINE . HTML_NEWLINE . t('An folgende Benutzer konnte die Mail leider nicht gesendet werden:') .HTML_NEWLINE. $fail . HTML_NEWLINE . HTML_NEWLINE;
        }
        if ($_POST["tosys"]) {
            $sys_success = t('Die Nachrichten an die System-Mailbox wurden erfolgreich versandt');
        }

        $func->confirmation($inet_success . $sys_success, "index.php?mod=mail&action=newsletter&step=1");
        break;
}
