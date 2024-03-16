<?php

// Info Seite blockiert
if ($cfg['sys_blocksite'] == 1) {
    if ($framework->getDisplayModus() != \LanSuite\Framework::DISPLAY_MODUS_AJAX) {
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
    if ($auth['type'] > \LS_AUTH_TYPE_USER) {
        // Has at least someone (with rights equal or above) access to this mod?
        $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions AS p
        LEFT JOIN %prefix%user AS u on p.userid = u.userid
        WHERE p.module = %string% AND u.type >= %int%", $_GET['mod'], $auth['type']);

          // If so: Has the current user access to this mod?
        if ($permission && $permission['found']) {
            $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions WHERE module = %string% AND userid = %int%", $_GET['mod'], $auth['userid']);

            // If not: Set his rights to user-rights
            if (!$permission) {
                $auth['type'] = \LS_AUTH_TYPE_USER;
                $_SESSION['auth']['type'] = 1;
                $authentication->auth['type'] = 1;
            }
        }
    }
}

$siteblock = false;
if ($cfg['sys_blocksite'] == 1 && $auth['type'] < \LS_AUTH_TYPE_ADMIN && $_GET['mod'] != 'info2' && $framework->getDisplayModus() != \LanSuite\Framework::DISPLAY_MODUS_AJAX) {
    $siteblock = true;
}

if (!$missing_fields && !$siteblock) {
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
            $modParameter = $request->query->get('mod');

            // If module is deactivated display information message and redirect to home-mod
            if ($modParameter && !$func->isModActive($modParameter)) {
                $row = $db->qry_first('SELECT caption FROM %prefix%modules WHERE name = %string%', $modParameter);
                if ($row['caption']) {
                    $func->information(t('Das Modul %1 wurde deaktiviert und steht somit nicht zur Verfügung. Du wurdest zur Startseite weitergeleitet', $row['caption']), NO_LINK);
                } else {
                    $func->information(t('Das Modul %1 existiert nicht. Überprüfe, ob du die Adresse korrekt eingegeben hast. Du wurdest zur Startseite weitergeleitet', $_GET['mod']), NO_LINK);
                }

                $modParameter = 'home';
            }

            // If we don't have a module, set home as default
            if (!$modParameter) {
                $modParameter = 'home';
            }

            // Load Mod-Config
            $actionParameter = $request->query->get('action') ?? '';
            // 1) Search $_GET['action'] in DB (field "action")
            $sqlQuery = 'SELECT `file`, `requirement` FROM `%prefix%menu` WHERE `module` = ? AND `action` = ?';
            $menu = $database->queryWithOnlyFirstRow($sqlQuery, [$modParameter, $actionParameter]);
            if ($menu && $menu['file'] != '') {
                // Case like
                //  - /?mod=info2&action=change
                if ($authentication->authorized($menu['requirement'])) {
                    $pathToInclude = BuildModuleFilePath($filesystem, ROOT_DIRECTORY, $modParameter, $menu['file']);
                    include_once($pathToInclude);
                }

            // 2) Search $_GET['action'] in DB (field "file")
            } else {
                $sqlQuery = 'SELECT `file`, `requirement` FROM `%prefix%menu` WHERE `module` = ? AND `file` = ?';
                $menu = $database->queryWithOnlyFirstRow($sqlQuery, [$modParameter, $actionParameter]);

                $fileInModDirectoryExists = false;
                if (!$menu || empty($menu['file'])) {
                    try {
                        $pathToInclude = BuildModuleFilePath($filesystem, ROOT_DIRECTORY, $modParameter, $actionParameter);
                        $fileInModDirectoryExists = true;
                    } catch (\Exception $exception) {
                        // We don't need to catch the exception here.
                        // It means the "modules/{$modParameter}/{$actionParameter}.php" file doesn't exist.
                    }
                }

                if ($menu && $menu['file'] != '') {
                    // Case like
                    //  - /?mod=about&action=overview
                    if ($authentication->authorized($menu['requirement'])) {
                        $pathToInclude = BuildModuleFilePath($filesystem, ROOT_DIRECTORY, $modParameter, $menu['file']);
                        include_once($pathToInclude);
                    }

                // 3) Search file named $_GET['action'] in the Mod-Directory
                } elseif ($fileInModDirectoryExists) {
                    // Case like
                    //  - /?mod=downloads&action=stats_grafik
                    $authRequirement = 0;
                    if ($menu && $menu['requirement']) {
                        $authRequirement = $menu['requirement'];
                    }
                    if ($authentication->authorized($authRequirement)) {
                        include_once($pathToInclude);
                    }

                // 4) Search 'default'-Entry in DB
                } else {
                    // Case like
                    //  - Index homepage in a non logged in status
                    //  - /?mod=news
                    $sqlQuery = 'SELECT `file`, `requirement` FROM `%prefix%menu` WHERE `module` = ? AND `action` = "default"';
                    $menu = $database->queryWithOnlyFirstRow($sqlQuery, [$modParameter]);

                    if ($menu && $menu['file'] != '') {
                        if ($authentication->authorized($menu['requirement'])) {
                            $pathToInclude = BuildModuleFilePath($filesystem, ROOT_DIRECTORY, $modParameter, $menu['file']);
                            include_once($pathToInclude);
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
