<?php
$templ['box']['rows'] = "";

function FetchItem ($item) {
	global $auth, $dsp, $templ, $box, $func, $cfg;

	$item["caption"] = $func->translate($item["caption"]);
	$item["hint"] = $func->translate($item["hint"]);

	// Horrizontal Line IF Caption == '--hr--'
	if ($item["caption"] == "--hr--") switch($item["level"]) {
		default: return $box->HRuleRow(); break;
		case 1: return $box->HRuleEngagedRow(); break;

	} else {
		$submod_pos = strpos($item["link"], "submod=") + 7;
		if ($submod_pos > 7) $submod = substr($item["link"], $submod_pos, strlen($item["link"]) - $submod_pos);
		else $submod = "";

		if ($item["module"] == $_GET["mod"] and ($_GET["mod"] != "info2" or $cfg['info2_use_submenus'] or ($_GET["mod"] == "info2" and $submod == $_GET["submod"]))) $highlighted = 1;
		else $highlighted = 0;

		// Set Item-Class
		switch ($item['requirement']){
			default: $class = "menu"; break;
#			case 1: if ($auth['type'] > 1) $class = "login"; break;
			case 2:
			case 3: $class = "admin"; break;
		}

		switch ($item["level"]) {
			case 0: return $box->DotRow($item["caption"], $item["link"], $item["hint"], $class, $highlighted); break;
			case 1: return $box->EngangedRow($item["caption"], $item["link"], $item["hint"], $class); break;
		}
	}
	return "";
}


$menu_out = "";
if (!$_GET["menu_group"]) $_GET["menu_group"] = 0;

// Get Main-Items
$res = $db->query("SELECT menu.*
	FROM {$config["tables"]["menu"]} AS menu
	LEFT JOIN {$config["tables"]["modules"]} AS module ON menu.module = module.name
	WHERE ((module.active) OR (menu.caption = '--hr--'))
	AND (menu.caption != '') AND (menu.level = 0) AND (menu.group_nr = {$_GET["menu_group"]})
	AND ((menu.requirement = '') OR (menu.requirement = 0)
	OR (menu.requirement = 1 AND ". (int)$auth['login'] ." = 1)
	OR (menu.requirement = 2 AND ". (int)$auth['type'] ." > 1)
	OR (menu.requirement = 3 AND ". (int)$auth['type'] ." > 2)
	OR (menu.requirement = 4 AND ". (int)$auth['type'] ." = 1)
	OR (menu.requirement = 5 AND ". (int)$auth['login'] ." = 0))
	ORDER BY menu.pos");
#or $cfg[$main_item["needed_config"]] or $config['environment'][$main_item["needed_config"]]
while ($main_item = $db->fetch_array($res)) if ($main_item['needed_config'] == '' or call_user_func($main_item['needed_config'], '')) {
	$menu_out .= FetchItem($main_item);

	// If selected Module: Get Sub-Items
	if (isset($_GET["module"])) $module = $_GET["module"]; else $module = $_GET["mod"];
	if ($module and $main_item["module"] == $module and $main_item["action"] != "show_info2") {
		$res2 = $db->query("SELECT menu.*
			FROM {$config["tables"]["menu"]} AS menu
			WHERE (menu.caption != '') AND (menu.level = 1) AND (menu.module = '$module')
			AND ((menu.requirement = '') OR (menu.requirement = 0)
			OR (menu.requirement = 1 AND ". (int)$auth['login'] ." = 1)
			OR (menu.requirement = 2 AND ". (int)$auth['type'] ." > 1)
			OR (menu.requirement = 3 AND ". (int)$auth['type'] ." > 2)
			OR (menu.requirement = 4 AND ". (int)$auth['type'] ." = 1)
			OR (menu.requirement = 5 AND ". (int)$auth['login'] ." = 0))
			ORDER BY menu.requirement, menu.pos");

    #or $cfg[$sub_item["needed_config"]] or $config['environment'][$sub_item["needed_config"]]
		while ($sub_item = $db->fetch_array($res2)) if ($sub_item['needed_config'] == '' or call_user_func($sub_item['needed_config'], ''))
			$menu_out .= FetchItem($sub_item);
		$db->free_result($res2);

		// If Admin add general Management-Links
		if ($auth['type'] > 2) {
			$sub_item["level"] = 1;
			$sub_item["link"] = "";
			$sub_item["caption"] = "--hr--";
			$sub_item['requirement'] = 2;
			$menu_out .= FetchItem($sub_item);

			$find_config = $db->query_first("SELECT cfg_key
					FROM {$config["tables"]["config"]}
					WHERE cfg_module = '$module'
					");
			if ($find_config["cfg_key"]) {
				$sub_item["link"] = "index.php?mod=install&action=modules&step=10&module=$module";
				$sub_item["caption"] = t('Modul-Konfig');
				$menu_out .= FetchItem($sub_item);
			}

			if (file_exists("modules/$module/mod_settings/db.xml")) {
				$sub_item["link"] = "index.php?mod=install&action=modules&step=30&module=$module";
				$sub_item["caption"] =t('Modul-DB');
				$menu_out .= FetchItem($sub_item);
			}

			$sub_item["link"] = "index.php?mod=install&action=modules&step=20&module=$module";
			$sub_item["caption"] = t('Menü-Einträge');
			$menu_out .= FetchItem($sub_item);
		}
	}
}
$db->free_result($res);

$templ['box']['rows'] .= $menu_out;

// Add Language select
$cur_url = parse_url($_SERVER['REQUEST_URI']);
$res = $db->query("SELECT cfg_value, cfg_display FROM {$config["tables"]["config_selections"]} WHERE cfg_key = 'language'");
while ($row = $db->fetch_array($res)) {
  if ($cur_url['query'] == '') $templ['box']['rows'] .= $dsp->FetchIcon($_SERVER['REQUEST_URI'] .'?language='. $row['cfg_value'], $row['cfg_value'], $row['cfg_display']).' ';
  else $templ['box']['rows'] .= $dsp->FetchIcon($_SERVER['REQUEST_URI'] .'&language='. $row['cfg_value'], $row['cfg_value'], $row['cfg_display']).' ';
}
$db->free_result($res);


// Callbacks
$MenuCallbacks = Array('ShowSignon', 'ShowGuestMap');

function ShowSignon() {
  global $cfg, $auth;

  if ($cfg['signon_partyid'] or !$auth['login']) return true;
  else return false;
}

function ShowGuestMap() {
  global $cfg;

  if ($cfg['guestlist_guestmap']) return true;
  else return false;
}

function sys_internet() {
  global $cfg;

  if ($cfg['sys_internet']) return true;
  else return false;
}

function snmp() {
  global $config;

  if ($config['snmp']) return true;
  else return false;
}

?>