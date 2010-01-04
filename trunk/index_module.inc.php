<?php

// Info Seite blockiert
if ($cfg['sys_blocksite'] == 1) $func->information($cfg['sys_blocksite_text'], "index.php?mod=install");

// Check, if all required user data fields, are known and force user to add them, if not.
$missing_fields = 0;
if (func::admin_exists() and $auth['login'] and $auth['userid'] and $_GET["mod"] != 'install')
    include_once ('modules/usrmgr/missing_fields.php');

// Set Mod = 'Home', if none selected
if ($_GET['mod'] == '' or !$func->check_var($_GET['mod'], 'string', 0, 50))
    $mod = 'home'; #($_GET['templ'] == 'install')? $mod = 'install' : $mod = 'home';
else
    $mod = $_GET['mod'];

// Reset $auth['type'], if no permission to Mod
if (func::admin_exists() and $auth['type'] > 1 and $_GET["mod"] != 'install') {

    // Has at least someone (with rights equal or above) access to this mod?
    $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions AS p
      LEFT JOIN %prefix%user AS u on p.userid = u.userid
      WHERE p.module = %string% AND u.type >= %int%", $mod, $auth['type']);

    // If so: Has the current user access to this mod?
    if ($permission['found']) {
        $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions WHERE module = %string% AND userid = %int%", $mod, $auth['userid']);

        // If not: Set his rights to user-rights
        if (!$permission['found']) {
            $auth['type'] = 1;
            $_SESSION['auth']['type'] = 1;
            $authentication->auth['type'] = 1;
        }
    }
}

if ($cfg['sys_blocksite'] == 1 and $auth['type'] < 2 and $_GET['mod'] != 'info2') $siteblock = true;

if (!$missing_fields and !$siteblock) {
    switch ($mod) {
        case 'logout':
            $func->confirmation(t('Sie wurden erfolgreich ausgeloggt.'), '');
            break;

        case 'auth':
            $_GET['mod'] = 'home';
            //$func->confirmation(t('auth.'), '');
            break;

        case 'install':
            if ($IsAboutToInstall) {
                include_once ('modules/install/wizard.php');
                break;
            }

        default:
            // If module is deactivated display error message
            if (!$func->isModActive($mod)) $func->error(t('Das Modul %1 wurde deaktiviert und steht somit nicht zur Verfügung.', $mod));

            //// Load Mod-Config
            else {
                // 1) Search $_GET['action'] in DB (field "action")
                $menu = $db->qry_first("SELECT file, requirement FROM %prefix%menu WHERE (module = %string%) and (action = %string%)", $mod, $_GET['action']);
                if ($menu['file'] != '') {
                    if ($authentication->authorized($menu['requirement']))
                        include_once ("modules/{$mod}/{$menu['file']}.php");

                    // 2) Search $_GET['action'] in DB (field "file")
                } else {
                    $menu = $db->qry_first("SELECT file, requirement FROM %prefix%menu WHERE (module = %string%) and (file = %string%)", $mod, $_GET['action']);
                    if ($menu['file'] != '') {
                        if ($authentication->authorized($menu['requirement']))
                            include_once ("modules/{$mod}/{$menu['file']}.php");

                        // 3) Search file named $_GET['action'] in the Mod-Directory
                    } elseif (file_exists("modules/$mod/{$_GET['action']}.php")) {
                        if ($authentication->authorized($menu['requirement']))
                            include_once ("modules/{$mod}/{$_GET['action']}.php");

                        // 4) Search 'default'-Entry in DB
                    } else {
                        $menu = $db->qry_first("SELECT file, requirement FROM %prefix%menu WHERE (module = %string%) and (action = 'default')",$mod);
                        if ($menu['file'] != '') {
                            if ($authentication->authorized($menu['requirement']))
                                include_once ("modules/{$mod}/{$menu['file']}.php");

                            // 4) Error: 'Not Found'
                        } else
                            $func->error(t('Leider ist die von Ihnen aufgerufene Seite auf diesem Server nicht vorhanden.<br/>Um Fehler zu vermeiden, sollten Sie die URL nicht manuell ändern, sondern die Links benutzen. Wenn Sie die Adresse manuell eingegeben haben überprüfen Sie bitte die URL.'));
                    }
                }
            }
            break;
    }
}
?>