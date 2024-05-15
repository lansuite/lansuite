<?php

$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    // Mastersearch
    default:
        $additional_where = 'u.userid != '. (int)$auth["userid"];
        $current_url = 'index.php?mod=msgsys&action=addbuddy';
        $target_url = 'index.php?mod=msgsys&action=addbuddy&step=2&userid=';
        include_once('modules/usrmgr/search_basic_userselect.inc.php');
        break;

    // Add
    case 2:
        if ($_GET['userid'] == '') {
            $func->error(t('Du hast keinen Benutzer ausgew&auml;hlt'), "index.php?mod=msgsys&action=addbuddy");
        } else {
              $user[] = $_GET['userid'];

            //init loop variables
            $err = [];
            $names1 = "";
            $sux = [];
            foreach ($user as $buddyid) {
                // User already in list ?
                $userAlreadyAdded= $database->queryWithOnlyFirstRow(
                    "SELECT id
                    FROM %prefix%buddys
                    WHERE userid = ?
                    AND buddyid = ?",
                    [$auth['userid'], $buddyid]
                );

                // Does the user exist ?
                $userExists = $database->queryWithOnlyFirstRow(
                "SELECT userid
                  FROM %prefix%user
                  WHERE userid = ?",
                  [$buddyid]
                  );

                // Too many users in the list ?
                $user_num = $database->queryWithFullResult(
                 "SELECT id
                  FROM %prefix%buddys
                  WHERE userid = ?", [$auth['userid']]);

                $too_many_users = count($user_num) >= 20;

                // Is it the User himself ?
                $i_am_the_user = $buddyid == $auth['userid'];

                // Get name
                $name = $database->queryWithOnlyFirstRow(
                    "SELECT
                    username,
                    firstname,
                    name
                  FROM %prefix%user
                  WHERE userid = ?", [$buddyid]);

                // If the user isn't in the list
                if (!$userAlreadyAdded && $userExists && !$too_many_users && !$i_am_the_user) {
                    $insert = $database->query("
                      INSERT INTO %prefix%buddys
                      SET
                        userid = ?,
                        buddyid = ?", [$auth['userid'], $buddyid]);

                    if ($insert == true) {
                        if ($cfg['sys_internet'] == 0) {
                            $sux[] = $name["username"] . " (" . $name["firstname"] . " " . $name["name"] . ")";
                        } else {
                              $sux[] = $name["username"];
                        }
                    }

                // If the user is already in the list
                } else {
                    if ($cfg['sys_internet'] == 0) {
                        $err[] = $name["username"] . " (" . $name["firstname"] . " " . $name["name"] . ")";
                    } else {
                        $err[] = $name["username"] ?? t('Unbekannte Nutzerid');
                    }
                }
            }

            // Confirmations / Errors
            // Successful
            if ((is_countable($sux) ? count($sux) : 0) > "0" && (is_countable($err) ? count($err) : 0) == "0") {
                foreach ($sux as $item) {
                    if ($names1 != "") {
                        $names1 .= ", ";
                    }
                    $names1 .= "$item";
                }
                $func->confirmation(str_replace('%NAMES1%', $names1, t('Die folgenden Benutzer wurden in deiner Buddy-Liste hinzugef&uuml;gt:
                                           <b>%NAMES1%</b> ' . HTML_NEWLINE . ' Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf sichtbar.')));

            // Partly Successful
            } elseif ((is_countable($sux) ? count($sux) : 0) > "0" && (is_countable($err) ? count($err) : 0) > "0") {
                foreach ($sux as $item) {
                    if ($names1 != "") {
                        $names1 .= ", ";
                    }
                    $names1 .= "$item";
                }
                foreach ($err as $item) {
                    if ($names2 != "") {
                        $names2 .= ", ";
                    }
                    $names2 .= "$item";
                }

                $func->confirmation(str_replace('%NAMES2%', $names2, str_replace('%NAMES1%', $names1, t('Die folgenden Benutzer wurden in deiner Buddy-Liste hinzugef&uuml;gt:
                                           <b>%NAMES1%</b> ' . HTML_NEWLINE . '
                                           Folgende Benutzer konnten nicht in deiner Buddy-Liste hinzugef&uuml;gt werden:
                                           <b>%NAMES2%</b> ' . HTML_NEWLINE . '
                                           Dies kann folgende Ursachen haben: ' . HTML_NEWLINE . '
                                           - Der Benutzer ist bereits in deiner Buddy-Liste ' . HTML_NEWLINE . '
                                           - Der Benutzer existiert nicht ' . HTML_NEWLINE . '
                                           - Es sind bereits zuviele Benutzer in deiner Buddy-Liste ' . HTML_NEWLINE . '
                                           - Du versuchst dich selbst in die Buddy-Liste hinzuzuf&uuml;gen ' . HTML_NEWLINE . '
                                           Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf sichtbar.'))), "");
                // Not successful
            } elseif ((is_countable($sux) ? count($sux) : 0) == "0" && (is_countable($err) ? count($err) : 0) > "0") {
                $func->error(t('Es konnten keine Benutzer in die Buddy-Liste hinzugef&uuml;gt werden. ' . HTML_NEWLINE . '
                      Dies kann folgende Ursachen haben: ' . HTML_NEWLINE . '
                      - Der Benutzer ist bereits in deiner Buddy-Liste ' . HTML_NEWLINE . '
                      - Der Benutzer existiert nicht ' . HTML_NEWLINE . '
                      - Es sind bereits zuviele Benutzer in deiner Buddy-Liste ' . HTML_NEWLINE . '
                      - Du versuchst dich selbst in die Buddy-Liste hinzuzuf&uuml;gen ' . HTML_NEWLINE));
            }
        }
        break;
}
