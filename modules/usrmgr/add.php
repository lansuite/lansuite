<?php
include_once("modules/usrmgr/class_usrmgr.php");
$usrmgr = new UsrMgr();

$gd = new gd();

function Update($id)
{
    global $mf, $db, $auth, $authentication, $party, $usrmgr, $func, $cfg, $signon;

  // Clan-Management
    include_once("modules/clanmgr/class_clan.php");
    $clan = new Clan();
    if (ShowField('clan')) {
        if ($_POST['new_clan_select']) {
            $clan->Add($_POST['clan_new'], $id, $_POST["clanurl"], $_POST["newclanpw"]);
        } elseif ($_POST['clan']) {
            $clan->AddMember($_POST['clan'], $id);
        } elseif (isset($_POST['clan'])) {
            $clan->RemoveMember($id);
        }
    }

    // Update User-Perissions
    if ($id) {
        $db->qry("DELETE FROM %prefix%user_permissions WHERE userid = %int%", $id);
        if ($_POST["permissions"]) {
            foreach ($_POST["permissions"] as $perm) {
                $db->qry("INSERT INTO %prefix%user_permissions SET module = %string%, userid = %int%", $perm, $id);
            }
        }
    }

  // If new user has been added
    if (!$mf->isChange) {
        $usrmgr->WriteXMLStatFile();

        // If auto generated PW, use PW stored in session, else use PW send by POST field
        if ($_POST['password_original']) {
            $_SESSION['tmp_pass'] = $_POST['password_original'];
        }

        if ($cfg["signon_password_mail"]) {
            if ($usrmgr->SendSignonMail(0)) {
                $func->confirmation(t('Dein Passwort und weitere Informationen wurden an deine angegebene E-Mail-Adresse gesendet.'), NO_LINK);
            } elseif ($cfg['sys_internet']) {
                $func->error(t('Es ist ein Fehler beim Versand der Informations-Email aufgetreten.') .'<br />'. t('Dein Passwort lautet: <b>%1</b>', array($_SESSION['tmp_pass'])), NO_LINK);
            }
        }

        // Send email-verification link
        if ($cfg['sys_login_verified_mail_only']) {
            $usrmgr->SendVerificationEmail($id);
        }

        // Show passwort, if wanted, or has mail failed
        if ($cfg['signon_password_view']) {
            $func->information(t('Dein Passwort lautet: <b>%1</b>', array($_SESSION['tmp_pass'])), NO_LINK);
        }
        $_SESSION['tmp_pass'] = '';
    }

/*
    // Picture Upload
    if ($auth["type"] >= 2) if (file_exists($_FILES['picture']['tmp_name'])) {
        @unlink("ext_inc/user_pics/pic$id.jpg");
        @copy($_FILES["picture"]["tmp_name"], "ext_inc/user_pics/pic$id.jpg");
    }
*/

    return true;
}

function CheckAndResizeUploadPic($AvatarName)
{
    global $gd;

    if ($AvatarName == '') {
        return false;
    }
    $FileEnding = strtolower(substr($AvatarName, strrpos($AvatarName, '.'), 5));
    if ($FileEnding != '.png' and $FileEnding != '.gif' and $FileEnding != '.jpg' and $FileEnding != '.jpeg') {
        return t('Bitte eine Grafikdatei auswählen');
    }

    $gd->CreateThumb($AvatarName, $AvatarName, 100, 100);
    return false;
}

/**
 * Check for optional gender selection
 *
 * @param int Gender from Inputfield 0=None, 1=Male, 2=Female
 * @return mixed Returns Message on error else false
 */
function check_opt_gender($gender)
{
    global $cfg;
    if ($cfg["signon_show_gender"] == 2) {
        if ($gender == 0) {
            return t("Bitte wählen sie ein Geschlecht aus.");
        } else {
            return false;
        }
    }
}

/**
 * Check for optional birthday selection
 * If Date is (DateNow - 80 Jears) the Date is the presetet Value
 * from the display::AddDateTimeRow() function. Not the perfect way.
 *
 * @param string Birthday from Inputfield like 2000-01-02
 * @return mixed Returns Message on error else false
 */
function check_birthday($date)
{
    global $cfg;
    if ($cfg["signon_show_birthday"] == 2) {
        $ref_date = (date("Y")-80)."-".date("n")."-".date("d");
        if ($date == $ref_date or ($date=="0000-00-00")) {
            return t("Bitte das korrekte Geburtsdatum eingeben.");
        } else {
            return false;
        }
    }
}

function CheckClanPW($clanpw)
{
    global $db, $auth;

    if (!$_POST['new_clan_select'] and $auth['type'] <= 1 and $auth['clanid'] != $_POST['clan']) {
        $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $_POST['clan']);
        if ($clan['password'] and $clan['password'] != md5($clanpw)) {
            return t('Passwort falsch!');
        }
    }
    return false;
}

function CheckClanNotExists($ClanName)
{
    global $db, $auth;

    $clan = $db->qry_first("SELECT 1 AS found FROM %prefix%clan WHERE name = %string%", $ClanName);
    if ($clan['found']) {
        return t('Dieser Clan existiert bereits!') .HTML_NEWLINE. t(' Wenn du diesem beitreten möchten, wähle ihn oberhalb aus dem Dropdownmenü aus.');
    }

    if (preg_match("/([.^\"\'`´]+)/", $ClanName)) {
        return t('Du verwendest nicht zugelassene Sonderzeichen in deinem Clannamen.');
    }

    return false;
}

function PersoInput($field, $mode, $error = '')
{
    global $dsp, $templ, $auth, $usrmgr, $smarty;

    switch ($mode) {
        case OUTPUT_PROC:
              $_POST[$field .'_1'] = substr($_POST[$field], 0, 11);
              $_POST[$field .'_2'] = substr($_POST[$field], 13, 7);
              $_POST[$field .'_3'] = substr($_POST[$field], 21, 7);
              $_POST[$field .'_4'] = substr($_POST[$field], 35, 1);

            if ($_POST[$field .'_1'] == '') {
                $_POST[$field .'_1'] = "aaaaaaaaaaD";
            }
            if ($_POST[$field .'_2'] == '') {
                  $_POST[$field .'_2'] = "bbbbbbb";
            }
            if ($_POST[$field .'_3'] == '') {
                  $_POST[$field .'_3'] = "ccccccc";
            }
            if ($_POST[$field .'_4'] == '') {
                  $_POST[$field .'_4'] = "d";
            }

              $smarty->assign('name', $field);
              $smarty->assign('value1', $_POST[$field .'_1']);
              $smarty->assign('value2', $_POST[$field .'_2']);
              $smarty->assign('value3', $_POST[$field .'_3']);
              $smarty->assign('value4', $_POST[$field .'_4']);
            if ($error) {
                  $smarty->assign('errortext', $dsp->errortext_prefix . $error . $dsp->errortext_suffix);
            }
            if (Optional("perso")) {
                  $smarty->assign('optional', "_optional");
            }

            return $smarty->fetch('modules/usrmgr/templates/row_perso.htm');
        break;

        case CHECK_ERROR_PROC:
              $_POST[$field] = $_POST["perso_1"] . "<<" . $_POST["perso_2"] . "<". $_POST["perso_3"] . "<<<<<<<" . $_POST["perso_4"];
            if ($_POST[$field] == "aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d") {
                $_POST[$field] = "";
            }
            if ($_POST[$field] == "<<<<<<<<<<") {
                  $_POST[$field] = "";
            }
            if ($_POST[$field] != '') {
                  $perso_res = $usrmgr->CheckPerso($_POST[$field]);
                switch ($perso_res) {
                    case 2:
                        return str_replace("<", "&lt;", t('Das Format der Personalausweisnummer ist falsch. Bitte nach folgendem Muster eingeben: \'aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d\''));
                    break;
                    case 3:
                        return t('Prüfsummenfehler. Bitte überprüfen deine Angaben. Sehr wahrscheinlich hast du eine oder mehrere Zahlen falsch abgeschrieben.');
                    break;
                    case 4:
                        return t('Dieser Personalausweis ist leider bereits abgelaufen.');
                    break;
                }
            }
            return false; // -> Means no error
        break;
    }
}

function Addr1Input($field, $mode, $error = '')
{
    global $dsp, $templ, $auth, $func;

    switch ($mode) {
        case OUTPUT_PROC:
            if ($_POST['street|hnr'] == '' and $_POST['street'] and $_POST['hnr']) {
                $_POST['street|hnr'] = $_POST['street'] .' '. $_POST['hnr'];
            }
            $dsp->AddTextFieldRow('street|hnr', t('Straße und Hausnummer'), $_POST['street|hnr'], $error, '', Optional('street'));
            return false;
        break;

        case CHECK_ERROR_PROC:
            if ($_POST['street|hnr'] != '' or FieldNeeded('street')) {
                $pieces = explode(' ', $_POST['street|hnr']);
                $_POST['hnr'] = array_pop($pieces);
                $_POST['street'] = implode(' ', $pieces);

                if ($_POST['street'] == '' or $_POST['hnr'] == '') {
                    return t('Bitte gib Straße und Hausnummer in folgendem Format ein: "Straßenname 12".');
                }
            }
            return false; // -> Means no error
        break;
    }
}

function Addr2Input($field, $mode, $error = '')
{
    global $dsp, $templ, $auth, $func;

    switch ($mode) {
        case OUTPUT_PROC:
            if ($_POST['plz|city'] == '' and $_POST['plz'] and $_POST['city']) {
                $_POST['plz|city'] = $_POST['plz'] .' '. $_POST['city'];
            }
            $dsp->AddTextFieldRow('plz|city', t('PLZ und Ort'), $_POST['plz|city'], $error, '', Optional('city'));
            return false;
        break;

        case CHECK_ERROR_PROC:
            if (($_POST['plz|city'] != '') || (FieldNeeded('city'))) {
                $pieces = explode(' ', $_POST['plz|city']);
                $_POST['plz'] = array_shift($pieces);
                $_POST['city'] = implode(' ', $pieces);

                if ($_POST['plz'] == 0 or $_POST['city'] == '') {
                    return t('Bitte gib Postleitzahl und Ort in folgendem Format ein: "12345 Stadt".');
                } elseif (strlen($_POST['plz']) < 4) {
                    return t('Die Postleitzahl muss aus 5 Ziffern bestehen.');
                }
            }
            return false; // -> Means no error
        break;
    }
}


function Optional($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] <= 1) {
        return 1;
    } else {
        return 0;
    }
}

function FieldNeeded($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] == 2) {
        return 1;
    } else {
        return 0;
    }
}

function ShowField($key)
{
    global $cfg;

    if ($cfg["signon_show_".$key] > 0) {
        return 1;
    } else {
        return 0;
    }
}

if (!($_GET['mod'] == 'signon' and $auth['login'] and $_GET['party_id'])) {
    $party_user = $db->qry_first("SELECT * FROM %prefix%party_user WHERE user_id = %int% AND party_id= %int%", $_GET["userid"], $party->party_id);
    $mf = new masterform();
  
    if ($cfg['signon_def_locked'] and !$_GET['userid']) {
        $mf->AddFix('locked', 1);
    }

    if ($auth['type'] >= 2 or !$_GET['userid'] or ($auth['userid'] == $_GET['userid'] and ($cfg['user_self_details_change'] or $missing_fields))) {
        if (!$DoSignon) {
        // If Admin, Creating a new user, or Missing fields:
        //   Show Username Field
            ($quick_signon)? $optional = 1 : $optional = 0;
            if (($auth['type'] >= 2 or !$_GET['userid'] or $missing_fields)) {
                $mf->AddField(t('Benutzername'), 'username', '', '', $optional);
            } else {
                $mf->AddField(t('Benutzername'), '', IS_TEXT_MESSAGE, t('Als Benutzer kannst du deinen Benutzernamen, Bezahlt & Platz-Status, Ausweis / Sonstiges und Kommentar NICHT ändern. Wenden dich dazu bitte an einen Administrator.'));
            }
  
            if (!$quick_signon) {
                if (ShowField('firstname')) {
                    $mf->AddField(t('Vorname'), 'firstname', '', '', Optional('firstname'));
                }
                if (ShowField('lastname')) {
                    $mf->AddField(t('Nachname'), 'name', '', '', Optional('lastname'));
                }
                $mf->AddGroup(t('Namen'));
  
          // If Admin: Usertype, Group and Module-Permissions
                if ($auth['type'] >= 2) {
                    // Usertype
                        $selections = array();
                    $selections['1'] = t('Benutzer');
                    $selections['2'] = t('Administrator');
                    if ($auth['type'] >= 3) {
                        $selections['3'] = t('Superadmin');
                    }
                    $mf->AddField(t('Benutzertyp'), 'type', IS_SELECTION, $selections, '', '', 1, array('2', '3'));
  
                        // Module-Permissions
                        $selections = array();
                    $res = $db->qry("SELECT module.name, module.caption FROM %prefix%modules AS module
              LEFT JOIN %prefix%menu AS menu ON menu.module = module.name
              WHERE menu.file != ''
              GROUP BY menu.module");
                    while ($row = $db->fetch_array($res)) {
                          $selections[$row['name']] = $row['caption'];
                    }
                          $db->free_result($res);
  
                    if (!$_GET['mf_step'] and $_GET['userid']) {
                          $res = $db->qry("SELECT module FROM %prefix%user_permissions WHERE userid = %int%", $_GET['userid']);
                        while ($row = $db->fetch_array($res)) {
                            $_POST["permissions"][] = $row["module"];
                        }
                          $db->free_result($res);
                    }
  
                          $mf->AddField(
                              t('Zugriffsberechtigung').HTML_NEWLINE.HTML_NEWLINE.
                              '('.t('Der Benutzertyp muss zusätzlich Admin, oder Superadmin sein.') .')'.HTML_NEWLINE.HTML_NEWLINE.
                              '('.t('Solange kein Admim einem Modul zugeordnet ist, hat dort jeder Admin Berechtigungen.') .')',
                              'permissions',
                              IS_MULTI_SELECTION,
                              $selections,
                              FIELD_OPTIONAL
                          );
  
                          $mf->AddDropDownFromTable(t('Gruppe'), 'group_id', 'group_id', 'group_name', 'party_usergroups', t('Keine'));
                          $mf->AddGroup('Rechte');
                }
            }
        // If not admin and user is created (not changed)
        // or if quick sign on is enabled
            if ($quick_signon or ($auth['type'] < 2 and !$_GET['userid'])) {
                $mf->AddFix('type', 1);
            }
  
            $mf->AddField(t('E-Mail'), 'email', '', '', '', CheckValidEmail);
            $mf->AddField(t('E-Mail wiederholen'),'email2','','','');	
            if (($_GET['action'] != 'change' and $_GET['action'] != 'entrance') or ($_GET['action'] == 'entrance' and !$_GET['userid'])) {
                if ($cfg['signon_autopw']) {
                    $_SESSION['tmp_pass'] = $usrmgr->GeneratePassword();
                    $mf->AddFix('password', md5($_SESSION['tmp_pass']));
                } else {
                    $mf->AddField(t('Passwort'), 'password', IS_NEW_PASSWORD);
                }
  
                if ($cfg['signon_captcha'] and !$_GET['userid']) {
                    $mf->AddField('', 'captcha', IS_CAPTCHA);
                }
            }
            $mf->AddGroup(t('Zugangsdaten'));
        }
  
        if (!$DoSignon and !$quick_signon) {
            // Clan Options
            if (ShowField('clan')) {
                if (!isset($_POST['clan'])) {
                    $users_clan = $db->qry_first("SELECT clanid FROM %prefix%user WHERE userid = %int%", $_GET['userid']);
                    $_POST['clan'] = $users_clan['clanid'];
                }

                $selections = array();
                $selections[''] = '---';
                $PWClans = array();
                $clans_query = $db->qry("SELECT c.clanid, c.name, c.url, c.password, COUNT(u.clanid) AS members
                FROM %prefix%clan AS c
                LEFT JOIN %prefix%user AS u ON c.clanid = u.clanid
                WHERE u.clanid IS NULL or u.type >= 1
                GROUP BY c.clanid
                ORDER BY c.name
                ");
                while ($row = $db->fetch_array($clans_query)) {
                      $selections[$row['clanid']] = $row['name'] .' ('. $row['members'] .')';
                    if ($row['password']) {
                        $PWClans[] = $row['clanid'];
                    }
                }
                    $db->free_result($clans_query);

                    $mf->AddField(t('Vorhandener Clan'), 'clan', IS_SELECTION, $selections, Optional('clan'), '', 1, $PWClans);
                    $mf->AddField(t('Passwort'), 'clanpw', IS_PASSWORD, '', FIELD_OPTIONAL, 'CheckClanPW');
                    $mf->AddField(t('Neuer Clan'), 'new_clan_select', 'tinyint(1)', '', FIELD_OPTIONAL, '', 3);
                    $mf->AddField(t('Name'), 'clan_new', '', '', FIELD_OPTIONAL, 'CheckClanNotExists');
                if (ShowField('clanurl')) {
                        $mf->AddField(t('Webseite'), 'clanurl', '', '', FIELD_OPTIONAL);
                }
                    $mf->AddField(t('Passwort'), 'newclanpw', IS_NEW_PASSWORD, '', FIELD_OPTIONAL);
                    $mf->AddGroup(t('Clan'));
            }

        // Leagues
            if ($func->isModActive('tournament2')) {
                if (ShowField('wwcl_id')) {
                    $mf->AddField(t('WWCL ID'), 'wwclid', '', '', Optional('wwclid'));
                }
                if (ShowField('ngl_id')) {
                    $mf->AddField(t('NGL ID'), 'nglid', '', '', Optional('nglid'));
                }
                if (ShowField('lgz_id')) {
                    $mf->AddField(t('LGZ ID'), 'lgzid', '', '', Optional('lgzid'));
                }
                $mf->AddGroup(t('Ligen'));
            }

        // Address
            if (ShowField('street')) {
                $mf->AddField('', 'street|hnr', IS_CALLBACK, 'Addr1Input', Optional('street'));
            }
            if (ShowField('city')) {
                $mf->AddField('', 'plz|city', IS_CALLBACK, 'Addr2Input', Optional('city'));
            }

            $list = array();
            if (!isset($_POST['country'])) {
                $_POST['country'] = $cfg['sys_country'];
            }
            $res = $db->qry("SELECT cfg_display, cfg_value FROM %prefix%config_selections WHERE cfg_key = 'country' ORDER BY cfg_display");
            while ($row = $db->fetch_array($res)) {
                $list[$row['cfg_value']] = $row['cfg_display'];
            }
            $db->free_result($res);
            $mf->AddField(t('Land'), 'country', IS_SELECTION, $list, FIELD_OPTIONAL);
            $mf->AddGroup(t('Adresse'));

        // Contact
            if (ShowField('telefon')) {
                $mf->AddField(t('Telefon'), 'telefon', '', '', Optional('telefon'));
            }
            if (ShowField('handy')) {
                $mf->AddField(t('Handy'), 'handy', '', '', Optional('telefon'));
            }
            if (ShowField('icq')) {
                $mf->AddField('ICQ', 'icq', '', '', Optional('icq'));
            }
            if (ShowField('msn')) {
                $mf->AddField('MSN', 'msn', '', '', Optional('msn'));
            }
            if (ShowField('xmpp')) {
                $mf->AddField('XMPP', 'xmpp', '', '', Optional('xmpp'));
            }
            if (ShowField('skype')) {
                $mf->AddField('Skype', 'skype', '', '', Optional('skype'));
            }
            $mf->AddGroup(t('Kontakt'));

        // Misc (Perso + Birthday + Gender + Newsletter)
            if (($auth['type'] >= 2 or !$_GET['userid'] or $missing_fields)) {
                if (ShowField('perso')) {
                    $mf->AddField(t('Personalausweis'), 'perso', IS_CALLBACK, 'PersoInput', Optional('perso'));
                }
                if (ShowField('birthday')) {
                    $mf->AddField(t('Geburtstag'), 'birthday', '', '-80/-8', Optional('birthday'), 'check_birthday');
                }
            }
            if (ShowField('gender')) {
                $selections = array();
                $selections['0'] = t('Keine Angabe');
                $selections['1'] = t('Männlich');
                $selections['2'] = t('Weiblich');
                $mf->AddField(t('Geschlecht'), 'sex', IS_SELECTION, $selections, Optional('gender'), 'check_opt_gender');
            }
            if (ShowField('newsletter')) {
                $mf->AddField(t('Newsletter abonnieren'), 'newsletter', '', '', Optional('newsletter'));
            }

        // If Admin: Picture and Comment
            if (($auth['type'] >= 2)) {
                $mf->AddField(t('Benutzerbild hochladen'), 'picture', IS_FILE_UPLOAD, 'ext_inc/user_pics/', Optional('picture'));
                $mf->AddField(t('Kommentar'), 'comment', '', HTML_ALLOWED, FIELD_OPTIONAL);
            }

        // AGB and Vollmacht, if new user
            if ((!$_GET['userid'] or $DoSignon) and $auth['type'] <= 1) {
                if (ShowField('voll')) {
                    $mf->AddField(t('U18-Vollmacht') .'|'. t('Hiermit bestätige ich, die %1 der Veranstaltung <b>"%2"</b> gelesen zu haben und ggf. ausgefüllt zur Veranstaltung mitzubringen.', "<a href=\"". $cfg["signon_volllink"] ."\" target=\"new\">". t('U18 Vollmacht') .'</a>', $_SESSION['party_info']['name']), 'vollmacht', 'tinyint(1)');
                }
                if (ShowField('agb')) {
                    ($cfg['signon_agb_targetblank']) ? $target = ' target="_blank"' : $target = '';
                    $mf->AddField(t('AGB bestätigen') .'|'. t('Hiermit bestätige ich die %1 der Veranstaltung <b>"%2"</b> gelesen zu haben und stimme ihnen zu.', '<a href="'. urldecode($cfg["signon_agblink"]) .'"'. $target .'>'. t('AGB') .'</a>', $_SESSION['party_info']['name']), 'agb', 'tinyint(1)');
                }
            }
            $mf->AddGroup(t('Verschiedenes'));

        // Add extra admin-defined fields
            $user_fields = $db->qry("SELECT name, caption, optional FROM %prefix%user_fields");
            $extra_found = 0;
            while ($user_field = $db->fetch_array($user_fields)) {
                $mf->AddField($user_field['caption'], $user_field['name'], '', '', $user_field['optional']);
                $extra_found = 1;
            }
            $db->free_result($user_fields);
            if ($extra_found) {
                $mf->AddGroup(t('Sonstiges'));
            }
        }
    }

    if (!$DoSignon and !$quick_signon) {
        // Settings
        if ($auth['type'] >= 2 or !$_GET['userid'] or ($auth['userid'] == $_GET['userid'] and ($cfg['user_self_details_change'] or $missing_fields))) {
            if ($cfg['user_design_change']) {
                $selections = array();
                $selections[''] = t('System-Vorgabe');

                $xml = new xml();

                $ResDesign = opendir('design/');
                while ($dir = readdir($ResDesign)) {
                    if (is_dir("design/$dir") and file_exists("design/$dir/design.xml") and ($dir != 'beamer')) {
                        $file = "design/$dir/design.xml";
                        $ResFile = fopen($file, "rb");
                        $XMLFile = fread($ResFile, filesize($file));
                        fclose($ResFile);
                        $DesignName = $xml->get_tag_content('name', $XMLFile);
                        $selections[$dir] = $DesignName;
                    }
                }
                closedir($ResDesign);
      
                $mf->AddField(t('Design'), 'design', IS_SELECTION, $selections, FIELD_OPTIONAL);
            }
      
            $mf->AddField(t('Mich auf der Karte zeigen') .'|'. t('Meine Adresse in der Besucherkarte anzeigen?'), 'show_me_in_map', '', '', FIELD_OPTIONAL);
            $mf->AddField(t('LS-Mail Alert') .'|'. t('Mir eine E-Mail senden, wenn eine neue LS-Mail eingegangen ist'), 'lsmail_alert', '', '', FIELD_OPTIONAL);
      
            if ($cfg['user_avatarupload']) {
                $mf->AddField(t('Avatar'), 'avatar_path', IS_FILE_UPLOAD, 'ext_inc/avatare/'. $_GET['userid'] .'_', FIELD_OPTIONAL, 'CheckAndResizeUploadPic');
            }
            $mf->AddField(t('Signatur'), 'signature', '', LSCODE_ALLOWED, FIELD_OPTIONAL);
            $mf->AddGroup(t('Einstellungen'));
        }
    }

    if ($_GET['mod'] == 'signon') {
        $mf->SendButtonText = 'Benutzer anlegen';
    } elseif ($_GET['mod'] == 'usrmgr' and $_GET['action'] == 'entrance' and $_GET['step'] == 3) {
        $mf->SendButtonText = 'Edit. + Einchecken';
    }

    $AddUserSuccess = 0;
    $mf->AdditionalDBUpdateFunction = 'Update';
    if ($mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&step='. $_GET['step'] .'&signon='. $_GET['signon'], 'user', 'userid', $_GET['userid'])) {
        // Log in new user
        if (!$auth['login']) {
            $_POST['login'] = 1;
            $_POST['email'] = $_POST['email'];
            $_POST['password'] = $_POST['password_original'];
            $auth = $authentication->login($_POST['email'], $_POST['password_original'], 0);
            initializeDesign();
        }
    
        $AddUserSuccess = 1;
    }
}

if ($_GET['mod'] == 'signon' and $auth['login']) {
    $_GET['mf_step'] = 1;
    $_GET['user_id'] = $auth['userid'];

    $mf_number--;
    include_once("modules/usrmgr/party.php");

  #$func->question(t("Wollen sie auch gleich zur Lan-Anmeldung weitergeleitet werden?"), "index.php?mod=signon", "index.php?mod=home");
}
