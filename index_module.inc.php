<?php

function authorized($mod, $action, $requirement) {
	global $auth, $func;

	switch ($requirement) {
		case 1: // Logged in
      if ($auth['login']) return 1;
      else $func->error('NO_LOGIN', '');
    break;

		case 2: // Type is Admin, or Operator
			if ($auth['type'] > 1) return 1;
			elseif (!$auth['login']) $func->error('NO_LOGIN', '');
      else $func->error('ACCESS_DENIED', '');
		break;

		case 3: // Type is Operator
			if ($auth['type'] > 2) return 1;
			elseif (!$auth['login']) $func->error('NO_LOGIN', '');
      else $func->error('ACCESS_DENIED', '');
		break;

		case 4: // Type is User, or less
			if ($auth['type'] < 2) return 1;
      else $func->error('ACCESS_DENIED', '');
		break;

		case 5: // Logged out
			if (!$auth['login']) return 1;
			else $func->error('ACCESS_DENIED', '');
		break;

		default:
			return 1;
		break;
	}
}

// Info Seite blockiert
if ($cfg['sys_blocksite'] == 1) $func->information($cfg['sys_blocksite_text'], "index.php?mod=install");

// Check, if all required user data fields, are known and force user to add them, if not.
$missing_fields = 0;
if ($found_adm and $auth['login'] and $auth['userid'] and $_GET["mod"] != 'install') include_once('modules/usrmgr/missing_fields.php');

// Set Mod = 'Home', if none selected
if ($_GET['mod'] == '' or !$func->check_var($_GET['mod'], 'string', 0, 50)) $mod = 'home'; #($_GET['templ'] == 'install')? $mod = 'install' : $mod = 'home';
else $mod = $_GET['mod'];

//// Load Lang-File
// 1) Include 'de'
// 2) Overwrite with $language
if (file_exists("modules/{$mod}/language/{$mod}_lang_de.php")) include_once("modules/{$mod}/language/{$mod}_lang_de.php");
if ($language != 'de' and file_exists("modules/{$mod}/language/{$mod}_lang_{$language}.php")) include_once("modules/{$mod}/language/{$mod}_lang_{$language}.php");

// Reset $auth['type'], if no permission to Mod
if ($found_adm and $auth['type'] > 1) {

  // Has at least someone access to this mod?
	$permission = $db->query_first("SELECT 1 AS found FROM {$config['tables']['user_permissions']} WHERE module = '$mod'");

  // If so: Has the current user access to this mod?
	if ($permission['found']) {
		$permission = $db->query_first("SELECT 1 AS found FROM {$config['tables']['user_permissions']} WHERE module = '$mod' AND userid = '{$auth['userid']}'");

    // If not: Set his rights to user-rights
		if (!$permission['found']) {
			$auth['type'] = 1;
			$_SESSION['auth']['type'] = 1;
		}
	}
}

if (!$missing_fields and !$siteblock) {
  switch ($mod) {
  	case 'logout': $func->confirmation(t('Sie wurden erfolgreich ausgeloggt.'), '');
  	break;

  	case 'install':
  		if ($IsAboutToInstall) {
        include_once('modules/install/wizard.php');
      	break;
      }

  	default:
  		// If module is deactivated display error message
  		if (!in_array($mod, $ActiveModules)) $func->error('DEACTIVATED', '');

  		//// Load Mod-Config
  		else {
  			// 1) Search $_GET['action'] in DB
  			$menu = $db->query_first("SELECT file, requirement FROM {$config['tables']['menu']} WHERE (module = '$mod') and (action = '{$_GET['action']}')");
  			if ($menu['file'] != '') {
  				if (authorized($mod, $menu['file'], $menu['requirement'])) include_once("modules/{$mod}/{$menu['file']}.php");

  			// 2) Search file named $_GET['action'] in the Mod-Directory
  			} elseif (file_exists("modules/$mod/{$_GET['action']}.php")) {
  				if (authorized($mod, $_GET['action'], $menu['requirement'])) include_once("modules/{$mod}/{$_GET['action']}.php");

  			// 3) Search 'default'-Entry in DB
  			} else {
  				$menu = $db->query_first("SELECT file, requirement FROM {$config['tables']['menu']} WHERE (module = '$mod') and (action = 'default')");
  				if ($menu['file'] != '') {
  					if (authorized($mod, $menu['file'], $menu['requirement'])) include_once("modules/{$mod}/{$menu['file']}.php");

    			// 4) Error: 'Not Found'
  				} else $func->error('NOT_FOUND', '');
  			}
  		}
  	break;
  }

  echo $templ['index']['info']['content'];
}
?>
