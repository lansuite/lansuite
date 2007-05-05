<?php

$BoxContent = array();

function FetchItem ($item) {
	global $box, $func, $cfg;

	$item['caption'] = $func->translate($item['caption']);
	$item['hint'] = $func->translate($item['hint']);

	// Horrizontal Line IF Caption == '--hr--'
	if ($item['caption'] == '--hr--') switch($item['level']) {
		default: return $box->HRuleRow(); break;
		case 1: return $box->HRuleEngagedRow(); break;

	} else {
		$submod_pos = strpos($item['link'], 'submod=') + 7;
		if ($submod_pos > 7) $submod = substr($item['link'], $submod_pos, strlen($item['link']) - $submod_pos);
		else $submod = '';

		if ($item['module'] == $_GET['mod'] and ($_GET['mod'] != 'info2' or ($_GET['mod'] == 'info2' and $submod == $_GET['submod']))) $highlighted = 1;
		else $highlighted = 0;

		// Set Item-Class
		switch ($item['requirement']){
			default: $class = 'menu'; break;
			case 2:
			case 3: $class = 'admin'; break;
		}

		switch ($item['level']) {
			case 0: return $box->DotRow($item['caption'], $item['link'], $item['hint'], $class, $highlighted); break;
			case 1: return $box->EngangedRow($item['caption'], $item['link'], $item['hint'], $class); break;
		}
	}
	return '';
}


if (!$_GET['menu_group']) $_GET['menu_group'] = 0;

// Get Main-Items
$res = $db->query("SELECT menu.*
	FROM {$config['tables']['menu']} AS menu
	LEFT JOIN {$config['tables']['modules']} AS module ON menu.module = module.name
	WHERE ((module.active) OR (menu.caption = '--hr--'))
	AND (menu.caption != '') AND (menu.level = 0) AND (menu.group_nr = {$_GET['menu_group']})
	AND ((menu.requirement = '') OR (menu.requirement = 0)
	OR (menu.requirement = 1 AND ". (int)$auth['login'] ." = 1)
	OR (menu.requirement = 2 AND ". (int)$auth['type'] ." > 1)
	OR (menu.requirement = 3 AND ". (int)$auth['type'] ." > 2)
	OR (menu.requirement = 4 AND ". (int)$auth['type'] ." = 1)
	OR (menu.requirement = 5 AND ". (int)$auth['login'] ." = 0))
	ORDER BY menu.pos");

while ($main_item = $db->fetch_array($res)) if ($main_item['needed_config'] == '' or call_user_func($main_item['needed_config'], '')) {
  $templ['box']['rows'] = '';
	FetchItem($main_item);

	// If selected Module: Get Sub-Items
	if (isset($_GET['module'])) $module = $_GET['module']; else $module = $_GET['mod'];
	if ($module and $main_item['module'] == $module and $main_item['action'] != 'show_info2') {
		$res2 = $db->query("SELECT menu.*
			FROM {$config['tables']['menu']} AS menu
			WHERE (menu.caption != '') AND (menu.level = 1) AND (menu.module = '$module')
			AND ((menu.requirement = '') OR (menu.requirement = 0)
			OR (menu.requirement = 1 AND ". (int)$auth['login'] ." = 1)
			OR (menu.requirement = 2 AND ". (int)$auth['type'] ." > 1)
			OR (menu.requirement = 3 AND ". (int)$auth['type'] ." > 2)
			OR (menu.requirement = 4 AND ". (int)$auth['type'] ." = 1)
			OR (menu.requirement = 5 AND ". (int)$auth['login'] ." = 0))
			ORDER BY menu.requirement, menu.pos");
		while ($sub_item = $db->fetch_array($res2)) if ($sub_item['needed_config'] == '' or call_user_func($sub_item['needed_config'], ''))
			FetchItem($sub_item);
		$db->free_result($res2);

		// If Admin add general Management-Links
		if ($auth['type'] > 2) {
/*
      $AdminIcons = '';
			$find_config = $db->query_first("SELECT cfg_key
					FROM {$config['tables']['config']}
					WHERE cfg_module = '$module'
					");
			if ($find_config['cfg_key'])
				$AdminIcons .= $box->LinkItem('index.php?mod=install&action=modules&step=10&module='. $module, t('Konf.'), 'admin', t('Modul-Konfiguration')) .' | ';

			if (file_exists("modules/$module/mod_settings/db.xml"))
				$AdminIcons .= $box->LinkItem('index.php?mod=install&action=modules&step=30&module='. $module, t('DB'), 'admin', t('Datenbank Tabellen dieses Moduls verwalten')) .' | ';

			$AdminIcons .= $box->LinkItem('index.php?mod=install&action=modules&step=20&module='. $module, t('Menü'), 'admin', t('Menüeinträge verwalten')) .' | ';
			$AdminIcons .= $box->LinkItem('index.php?mod=misc&action=translation&step=20&file='. $module, t('Ü'), 'admin', t('Übersetzungen zu diesem Modul'));
*/
			$AdminIcons .= $box->LinkItem('index.php?mod=install&action=mod_cfg&module='. $module, t('Mod-Konfig'), 'admin', t('Dieses Modul verwalten'));
      $box->EngangedRow('<span class="AdminIcons">'. $AdminIcons .'</span>');
		}
	}
	$BoxContent[$main_item['boxid']] .= $templ['box']['rows'];
}
$db->free_result($res);

foreach ($BoxContent as $key => $val) {
  $templ['box']['rows'] = $val;
  if ($BoxRow['place'] == 0) $templ['index']['control']['boxes_letfside'] .= $box->CreateBox($BoxRow['boxid'].'_'.$key, t($BoxRow['name']));
  elseif ($BoxRow['place'] == 1) $templ['index']['control']['boxes_rightside'] .= $box->CreateBox($BoxRow['boxid'].'_'.$key, t($BoxRow['name']));
  $templ['box']['rows'] = '';
}

$MenuCallbacks = array();
$MenuCallbacks[] = 'ShowSignon';
$MenuCallbacks[] = 'ShowGuestMap';
$MenuCallbacks[] = 'sys_internet';
$MenuCallbacks[] = 'snmp';
$MenuCallbacks[] = 'DokuWikiNotInstalled';

// Callbacks
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

function DokuWikiNotInstalled() {
  if (!file_exists('ext_scripts/dokuwiki/conf/local.php')) return true;
  else return false;
}

?>