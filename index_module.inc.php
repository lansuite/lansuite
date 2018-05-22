<?php

// Info Seite blockiert
if ($cfg['sys_blocksite'] == 1) {
    if ($framework->modus != "ajax") {
        $func->information($cfg['sys_blocksite_text'], "index.php?mod=install");
    }
}

$missing_fields = 0;
if ($_GET["mod"] != 'install' && $func->admin_exists()) {
    // Check, if all required user data fields, are known and force user to add them, if not.
    if ($auth['login'] and $auth['userid']) {
        include_once('modules/usrmgr/missing_fields.php');
    }

    // Reset $auth['type'], if no permission to Mod
    if ($auth['type'] > 1) {
        // Has at least someone (with rights equal or above) access to this mod?
        $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions AS p
        LEFT JOIN %prefix%user AS u on p.userid = u.userid
        WHERE p.module = %string% AND u.type >= %int%", $_GET['mod'], $auth['type']);

          // If so: Has the current user access to this mod?
        if ($permission['found']) {
            $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions WHERE module = %string% AND userid = %int%", $_GET['mod'], $auth['userid']);

            // If not: Set his rights to user-rights
            if (!$permission['found']) {
                $auth['type'] = 1;
                $_SESSION['auth']['type'] = 1;
                $authentication->auth['type'] = 1;
            }
        }
    }
}


if ($cfg['sys_blocksite'] == 1 and $auth['type'] < 2 and $_GET['mod'] != 'info2' and $framework->modus != "ajax") {
    $siteblock = true;
}

if (!$missing_fields and !$siteblock) {
    switch ($_GET['mod']) {
        case 'logout':
            $func->confirmation(t('Du wurdest erfolgreich ausgeloggt.'), '');
            break;

        case 'auth':
            $_GET['mod'] = 'home';
            break;

        case 'install':
            if ($IsAboutToInstall) {
                include_once('modules/install/wizard.php');
                break;
            }

        default:
            // If module is deactivated display information message and redirect to home-mod
            if (!$func->isModActive($_GET['mod'])) {
                $row = $db->qry_first('SELECT caption FROM %prefix%modules WHERE name = %string%', $_GET['mod']);
                if ($row['caption']) {
                    $func->information(t('Das Modul %1 wurde deaktiviert und steht somit nicht zur Verfügung. Du wurdest zur Startseite weitergeleitet', $row['caption']), NO_LINK);
                } else {
                    $func->information(t('Das Modul %1 existiert nicht. Überprüfe, ob du die Adresse korrekt eingegeben hast. Du wurdest zur Startseite weitergeleitet', $_GET['mod']), NO_LINK);
                }
                $_GET['mod'] = 'home';
            }

            //// Load Mod-Config
            // 1) Search $_GET['action'] in DB (field "action")
            $menu = $db->qry_first("SELECT file, requirement FROM %prefix%menu WHERE (module = %string%) and (action = %string%)", $_GET['mod'], $_GET['action']);
            if ($menu['file'] != '') {
                if ($authentication->authorized($menu['requirement'])) {
                    include_once("modules/{$_GET['mod']}/{$menu['file']}.php");
                }

            // 2) Search $_GET['action'] in DB (field "file")
            } else {
                $menu = $db->qry_first("SELECT file, requirement FROM %prefix%menu WHERE (module = %string%) and (file = %string%)", $_GET['mod'], $_GET['action']);
                if ($menu['file'] != '') {
                    if ($authentication->authorized($menu['requirement'])) {
                        include_once("modules/{$_GET['mod']}/{$menu['file']}.php");
                    }

                // 3) Search file named $_GET['action'] in the Mod-Directory
                } elseif (file_exists("modules/{$_GET['mod']}/{$_GET['action']}.php")) {
                    if ($authentication->authorized($menu['requirement'])) {
                        include_once("modules/{$_GET['mod']}/{$_GET['action']}.php");
                    }

                // 4) Search 'default'-Entry in DB
                } else {
                    $menu = $db->qry_first("SELECT file, requirement FROM %prefix%menu WHERE (module = %string%) and (action = 'default')", $_GET['mod']);
                    if ($menu['file'] != '') {
                        if ($authentication->authorized($menu['requirement'])) {
                            include_once("modules/{$_GET['mod']}/{$menu['file']}.php");
                        }

                    // 4) Error: 'Not Found'
                    } else {
                        $func->error(t('Leider ist die von dir aufgerufene Seite auf diesem Server nicht vorhanden.<br/>Um Fehler zu vermeiden, solltest du die URL nicht manuell ändern, sondern die Links benutzen. Wenn du die Adresse manuell eingegeben hast überprüfe bitte die URL.'));
                    }
                }
            }
            break;
    }
}
