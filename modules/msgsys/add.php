<?php

switch ($_GET['step']) {
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
            foreach ($user as $buddyid) {
                  // User already in list ?
                  $existsinthelist = $db->qry("
   SELECT   id
   FROM  %prefix%buddys
   WHERE  userid =%int%
   AND  buddyid =%int%
   ", $auth['userid'], $buddyid);

                if ($db->num_rows() != "0") {
                          $user_exist_in_the_list = 1;
                }

                  // Does the user exist ?
                  $exist = $db->qry("
   SELECT   userid
   FROM  %prefix%user
   WHERE  userid =%int%
   ", $buddyid);
                if ($db->num_rows() != "0") {
                          $user_exist = 1;
                }

                  // Too many users in the list ?
                  $num = $db->qry("
   SELECT   id
   FROM  %prefix%buddys
   WHERE  userid =%int%
   ", $auth['userid']);
                $user_num = $db->num_rows();
                if ($user_num >= 20) {
                          $to_many_users = 1;
                }

                  // Is it the User himself ?
                if ($buddyid == $auth['userid']) {
                      $i_am_the_user = 1;
                }

                    // Get name
                    $name = $db->qry_first("
      SELECT   username, firstname, name
      FROM  %prefix%user
      WHERE  userid = %int%
      ", $buddyid);

                  // If the user isn't in the list
                if ($user_exist_in_the_list != 1 && $user_exist == 1 && $to_many_users != 1 && $i_am_the_user != 1) {
                        $insert = $db->qry("
             INSERT INTO %prefix%buddys
             SET   userid = %int%, buddyid = %int%
             ", $auth['userid'], $buddyid);

                    if ($insert == true) {
                        if ($cfg['sys_internet'] == 0) {
                            $sux[] = $name["username"] . " (" . $name["firstname"] . " " . $name["name"] . ")";
                        } else {
                              $sux[] = $name["username"];
                        }
                    }
                } //
                    // If the user is already in the list
                    //
                else {
                    if ($cfg['sys_internet'] == 0) {
                        $err[] = $name["username"] . " (" . $name["firstname"] . " " . $name["name"] . ")";
                    } else {
                        $err[] = $name["username"];
                    }
                }
            } // foreach

                //
                // Confirmations / Errors
                //
                //
                // Successful
                //
            if (count($sux) > "0" && count($err) == "0") {
                foreach ($sux as $item) {
                    if ($names1 != "") {
                        $names1 .= ", ";
                    }
                    $names1 .= "$item";
                }
                $func->confirmation(str_replace('%NAMES1%', $names1, t('Die folgenden Benutzer wurden in deiner Buddy-Liste hinzugef&uuml;gt:
                                           <b>%NAMES1%</b> HTML_NEWLINE Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf sichtbar.')));
            } // if

                //
                // Partly Successful
                //
            elseif (count($sux) > "0" && count($err) > "0") {
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
                                           <b>%NAMES1%</b> HTML_NEWLINE
                                           Folgende Benutzer konnten nicht in deiner Buddy-Liste hinzugef&uuml;gt werden:
                                           <b>%NAMES2%</b> HTML_NEWLINE
                                           Dies kann folgende Ursachen haben: HTML_NEWLINE
                                           - Der Benutzer ist bereits in deiner Buddy-Liste HTML_NEWLINE
                                           - Der Benutzer existiert nicht HTML_NEWLINE
                                           - Es sind bereits zuviele Benutzer in deiner Buddy-Liste HTML_NEWLINE
                                           - Du versuchst dich selbst in die Buddy-Liste hinzuzuf&uuml;gen HTML_NEWLINE
                                           Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf sichtbar.'))), "");
            } // elseif

                //
                // Not successful
                //
            elseif (count($sux) == "0" && count($err) > "0") {
                $func->error(t('Es konnten keine Benutzer in die Buddy-Liste hinzugef&uuml;gt werden. HTML_NEWLINE
					  Dies kann folgende Ursachen haben: HTML_NEWLINE
					  - Der Benutzer ist bereits in deiner Buddy-Liste HTML_NEWLINE
					  - Der Benutzer existiert nicht HTML_NEWLINE
					  - Es sind bereits zuviele Benutzer in deiner Buddy-Liste HTML_NEWLINE
					  - Du versuchst dich selbst in die Buddy-Liste hinzuzuf&uuml;gen HTML_NEWLINE'));
            } // elseif
        }
        break;
} // switch
