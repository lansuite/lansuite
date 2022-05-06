<?php

$mail = new \LanSuite\Module\Mail\Mail();
$usrmgr = new \LanSuite\Module\UsrMgr\UserManager($mail);

$gd = new \LanSuite\GD();

if (!($_GET['mod'] == 'signon' && $auth['login'] && $_GET['party_id'])) {
    $party_user = $db->qry_first("SELECT * FROM %prefix%party_user WHERE user_id = %int% AND party_id= %int%", $_GET["userid"], $party->party_id);
    $mf = new \LanSuite\MasterForm();
  
    if ($cfg['signon_def_locked'] && !$_GET['userid']) {
        $mf->AddFix('locked', 1);
    }

    if ($auth['type'] >= 2 || !$_GET['userid'] || ($auth['userid'] == $_GET['userid'] && ($cfg['user_self_details_change'] || $missing_fields))) {
        if (!$DoSignon) {
            // If Admin, Creating a new user, or Missing fields:
            // Show Username Field
            ($quick_signon)? $optional = 1 : $optional = 0;
            if (($auth['type'] >= 2 || !$_GET['userid'] or $missing_fields)) {
                $mf->AddField(t('Benutzername'), 'username', '', '', $optional, 'CheckValidUsername');
            } else {
                $mf->AddField(t('Benutzername'), '', \LanSuite\MasterForm::IS_TEXT_MESSAGE, t('Als Benutzer kannst du deinen Benutzernamen, Bezahlt & Platz-Status, Ausweis / Sonstiges und Kommentar NICHT ändern. Wenden dich dazu bitte an einen Administrator.'));
            }
  
            if (!$quick_signon) {
                if (ShowFieldUsrMgr('firstname')) {
                    $mf->AddField(t('Vorname'), 'firstname', '', '', Optional('firstname'));
                }
                if (ShowFieldUsrMgr('lastname')) {
                    $mf->AddField(t('Nachname'), 'name', '', '', Optional('lastname'));
                }
                $mf->AddGroup(t('Namen'));
  
                // If Admin: Usertype, Group and Module-Permissions
                if ($auth['type'] >= 2) {
                    // Usertype
                    $selections = [];
                    $selections['1'] = t('Benutzer');
                    $selections['2'] = t('Administrator');
                    if ($auth['type'] >= 3) {
                        $selections['3'] = t('Superadmin');
                    }
                    $mf->AddField(t('Benutzertyp'), 'type', \LanSuite\MasterForm::IS_SELECTION, $selections, '', '', 1, array('2', '3'));
  
                    // Module-Permissions
                    $selections = [];
                    $res = $db->qry("
                      SELECT
                        module.name,
                        module.caption
                      FROM %prefix%modules AS module
                      LEFT JOIN %prefix%menu AS menu ON menu.module = module.name
                      WHERE
                        menu.file != ''
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
                        '('.t('Solange kein Admin einem Modul zugeordnet ist, hat dort jeder Admin Berechtigungen.') .')',
                        'permissions',
                        \LanSuite\MasterForm::IS_MULTI_SELECTION,
                        $selections,
                        \LanSuite\MasterForm::FIELD_OPTIONAL
                    );

                    $mf->AddDropDownFromTable(t('Gruppe'), 'group_id', 'group_id', 'group_name', 'party_usergroups', t('Keine'));
                    $mf->AddGroup('Rechte');
                }
            }

            // If not admin and user is created (not changed)
            // or if quick sign on is enabled
            if ($quick_signon or ($auth['type'] < 2 && !$_GET['userid'])) {
                $mf->AddFix('type', 1);
            }
  
            $mf->AddField(t('E-Mail'), 'email', '', '', '', 'CheckDuplicateAndValidEmail');
            $mf->AddField(t('E-Mail wiederholen'), 'email2', '', '', true);
            if (($_GET['action'] != 'change' && $_GET['action'] != 'entrance') || ($_GET['action'] == 'entrance' && !$_GET['userid'])) {
                if ($cfg['signon_autopw']) {
                    $_SESSION['tmp_pass'] = $usrmgr->GeneratePassword();
                    $mf->AddFix('password', md5($_SESSION['tmp_pass']));
                } else {
                    $mf->AddField(t('Passwort'), 'password', \LanSuite\MasterForm::IS_NEW_PASSWORD);
                }
  
                if ($cfg['signon_captcha'] && !$_GET['userid']) {
                    $mf->AddField('', 'captcha', \LanSuite\MasterForm::IS_CAPTCHA);
                }
            }
            $mf->AddGroup(t('Zugangsdaten'));
        }
  
        if (!$DoSignon && !$quick_signon) {
            // Clan Options
            if (ShowFieldUsrMgr('clan')) {
                if (!isset($_POST['clan'])) {
                    $users_clan = $db->qry_first("SELECT clanid FROM %prefix%user WHERE userid = %int%", $_GET['userid']);
                    $_POST['clan'] = $users_clan['clanid'];
                }

                $selections = [];
                $selections[''] = '---';
                $PWClans = [];
                $clans_query = $db->qry("
                  SELECT
                    c.clanid,
                    c.name,
                    c.url,
                    c.password,
                    COUNT(u.clanid) AS members
                  FROM %prefix%clan AS c
                  LEFT JOIN %prefix%user AS u ON c.clanid = u.clanid
                  WHERE
                    u.clanid IS NULL
                    OR u.type >= 1
                  GROUP BY c.clanid
                  ORDER BY c.name");
                while ($row = $db->fetch_array($clans_query)) {
                    $selections[$row['clanid']] = $row['name'] .' ('. $row['members'] .')';
                    if ($row['password']) {
                        $PWClans[] = $row['clanid'];
                    }
                }
                $db->free_result($clans_query);

                $mf->AddField(t('Vorhandener Clan'), 'clan', \LanSuite\MasterForm::IS_SELECTION, $selections, Optional('clan'), '', 1, $PWClans);
                $mf->AddField(t('Passwort'), 'clanpw', \LanSuite\MasterForm::IS_PASSWORD, '', \LanSuite\MasterForm::FIELD_OPTIONAL, 'CheckClanPWUsrMgr');
                $mf->AddField(t('Neuer Clan'), 'new_clan_select', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL, '', 3);
                $mf->AddField(t('Name'), 'clan_new', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL, 'CheckClanNotExists');
                if (ShowFieldUsrMgr('clanurl')) {
                    $mf->AddField(t('Webseite'), 'clanurl', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
                }
                $mf->AddField(t('Passwort'), 'newclanpw', \LanSuite\MasterForm::IS_NEW_PASSWORD, '', \LanSuite\MasterForm::FIELD_OPTIONAL);
                $mf->AddGroup(t('Clan'));
            }

            // Leagues
            if ($func->isModActive('tournament2')) {
                if (ShowFieldUsrMgr('wwcl_id')) {
                    $mf->AddField(t('WWCL ID'), 'wwclid', '', '', Optional('wwclid'));
                }
                if (ShowFieldUsrMgr('ngl_id')) {
                    $mf->AddField(t('NGL ID'), 'nglid', '', '', Optional('nglid'));
                }
                if (ShowFieldUsrMgr('lgz_id')) {
                    $mf->AddField(t('LGZ ID'), 'lgzid', '', '', Optional('lgzid'));
                }
                $mf->AddGroup(t('Ligen'));
            }

            // Address
            if (ShowFieldUsrMgr('street')) {
                $mf->AddField('', 'street|hnr', \LanSuite\MasterForm::IS_CALLBACK, 'Addr1Input', Optional('street'));
            }
            if (ShowFieldUsrMgr('city')) {
                $mf->AddField('', 'plz|city', \LanSuite\MasterForm::IS_CALLBACK, 'Addr2Input', Optional('city'));
            }

            $list = [];
            if (!isset($_POST['country'])) {
                $_POST['country'] = $cfg['sys_country'];
            }
            $res = $db->qry("SELECT cfg_display, cfg_value FROM %prefix%config_selections WHERE cfg_key = 'country' ORDER BY cfg_display");
            while ($row = $db->fetch_array($res)) {
                $list[$row['cfg_value']] = $row['cfg_display'];
            }
            $db->free_result($res);
            $mf->AddField(t('Land'), 'country', \LanSuite\MasterForm::IS_SELECTION, $list, \LanSuite\MasterForm::FIELD_OPTIONAL);
            $mf->AddGroup(t('Adresse'));

            // Contact
            if (ShowFieldUsrMgr('telefon')) {
                $mf->AddField(t('Telefon'), 'telefon', '', '', Optional('telefon'));
            }
            if (ShowFieldUsrMgr('handy')) {
                $mf->AddField(t('Handy'), 'handy', '', '', Optional('telefon'));
            }
            if (ShowFieldUsrMgr('icq')) {
                $mf->AddField('ICQ', 'icq', '', '', Optional('icq'));
            }
            if (ShowFieldUsrMgr('msn')) {
                $mf->AddField('MSN', 'msn', '', '', Optional('msn'));
            }
            if (ShowFieldUsrMgr('xmpp')) {
                $mf->AddField('XMPP', 'xmpp', '', '', Optional('xmpp'));
            }
            if (ShowFieldUsrMgr('skype')) {
                $mf->AddField('Skype', 'skype', '', '', Optional('skype'));
            }
            $mf->AddGroup(t('Kontakt'));

            // Misc (Perso + Birthday + Gender + Newsletter)
            if (($auth['type'] >= 2 or !$_GET['userid'] or $missing_fields)) {
                if (ShowFieldUsrMgr('perso')) {
                    $mf->AddField(t('Personalausweis'), 'perso', \LanSuite\MasterForm::IS_CALLBACK, 'PersoInput', Optional('perso'));
                }
                if (ShowFieldUsrMgr('birthday')) {
                    $mf->AddField(t('Geburtstag'), 'birthday', '', '-80/-8', Optional('birthday'), 'check_birthday');
                }
            }
            if (ShowFieldUsrMgr('gender')) {
                $selections = [];
                $selections['0'] = t('Keine Angabe');
                $selections['1'] = t('Männlich');
                $selections['2'] = t('Weiblich');
                $mf->AddField(t('Geschlecht'), 'sex', \LanSuite\MasterForm::IS_SELECTION, $selections, Optional('gender'), 'check_opt_gender');
            }
            if (ShowFieldUsrMgr('newsletter')) {
                $mf->AddField(t('Newsletter abonnieren'), 'newsletter', '', '', Optional('newsletter'));
            }

            // If Admin: Picture and Comment
            if (($auth['type'] >= 2)) {
                $mf->AddField(t('Benutzerbild hochladen'), 'picture', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/user_pics/', Optional('picture'));
                $mf->AddField(t('Kommentar'), 'comment', '', \LanSuite\MasterForm::HTML_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
            }

            // AGB and Vollmacht, if new user
            if ((!$_GET['userid'] or $DoSignon) and $auth['type'] <= 1) {
                if (ShowFieldUsrMgr('voll')) {
                    $mf->AddField(t('U18-Vollmacht') .'|'. t('Hiermit bestätige ich, die %1 der Veranstaltung <b>"%2"</b> gelesen zu haben und ggf. ausgefüllt zur Veranstaltung mitzubringen.', "<a href=\"". $cfg["signon_volllink"] ."\" target=\"new\">". t('U18 Vollmacht') .'</a>', $_SESSION['party_info']['name']), 'vollmacht', 'tinyint(1)');
                }
                if (ShowFieldUsrMgr('agb')) {
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

    if (!$DoSignon && !$quick_signon) {
        // Settings
        if ($auth['type'] >= 2 or !$_GET['userid'] or $auth['userid'] == $_GET['userid']) {
            if ($cfg['user_design_change']) {
                $selections = [];
                $selections[''] = t('System-Vorgabe');

                $xml = new \LanSuite\XML();

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
      
                $mf->AddField(t('Design'), 'design', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
            }
      
            $mf->AddField(t('Mich auf der Karte zeigen') .'|'. t('Meine Adresse in der Besucherkarte anzeigen?'), 'show_me_in_map', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
            $mf->AddField(t('LS-Mail Alert') .'|'. t('Mir eine E-Mail senden, wenn eine neue LS-Mail eingegangen ist'), 'lsmail_alert', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
      
            if ($cfg['user_avatarupload']) {
                $mf->AddField(t('Avatar'), 'avatar_path', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/avatare/'. $_GET['userid'] .'_', \LanSuite\MasterForm::FIELD_OPTIONAL, 'CheckAndResizeUploadPic');
            }
            $mf->AddField(t('Signatur'), 'signature', '', \LanSuite\MasterForm::LSCODE_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
            $mf->AddGroup(t('Einstellungen'));
        }
    }

    if ($_GET['mod'] == 'signon') {
        $mf->SendButtonText = 'Benutzer anlegen';
    } elseif ($_GET['mod'] == 'usrmgr' and $_GET['action'] == 'entrance' and $_GET['step'] == 3) {
        $mf->SendButtonText = 'Edit. + Einchecken';
    }

    $AddUserSuccess = 0;
    $mf->AdditionalDBUpdateFunction = 'UpdateUsrMgr';
    if ($mf->SendForm('index.php?mod='. $_GET['mod'] .'&action='. $_GET['action'] .'&step='. $_GET['step'] .'&signon='. $_GET['signon'], 'user', 'userid', $_GET['userid'])) {
        // Log in new user
        if (!$auth['login']) {
            $_POST['login'] = 1;
            $_POST['password'] = $_POST['password_original'];
            $auth = $authentication->login($_POST['email'], $_POST['password_original'], 0);
            initializeDesign();
        }
    
        $AddUserSuccess = 1;
    }
}

if ($_GET['mod'] == 'signon' && $auth['login']) {
    $_GET['mf_step'] = 1;
    $_GET['user_id'] = $auth['userid'];

    $mf->DecrementNumber();
    include_once("modules/usrmgr/party.php");
}
