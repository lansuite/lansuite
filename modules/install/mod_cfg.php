<?php
$CurrentMod = $db->qry_first('SELECT caption FROM %prefix%modules WHERE name=%string%', $_GET['module']);

$dsp->NewContent(t('Modul-Konfiguration') .' - '. $CurrentMod['caption'], t('Hier können Sie dieses Modul Ihren Bedürfnissen anpassen'));

$menunames = array();
$res = $db->qry('SELECT name, caption FROM %prefix%modules WHERE active = 1 ORDER BY caption');
while ($row = $db->fetch_array($res)) $menunames[$row['name']] = $row['caption'];
$db->free_result($res);
$dsp->AddHeaderMenu2($menunames, 'index.php?mod=install&action=mod_cfg&module=', $_GET['headermenuitem']);

// First switch
switch ($_GET['step']) {
  case 31:
		$db->query("INSERT INTO {$config["tables"]["menu"]}
			SET caption = 'Neuer Eintrag',
				requirement = '0',
				hint = '',
				link = 'index.php?mod=',
				needed_config = '',
				module='{$_GET["module"]}',
				level = 1");
    $_GET['step'] = 30;
  break;
}

switch ($_GET['step']) {
  default:
    $dsp->AddFieldsetStart(t('Verwaltungs-Werkzeuge'));
    $find_config = $db->qry_first('SELECT cfg_key FROM %prefix%config WHERE cfg_module = %string%', $_GET['module']);
    if ($find_config['cfg_key']) $dsp->AddDoubleRow('<a href="index.php?mod=install&action=mod_cfg&step=10&module='. $_GET['module'] .'"><img src="design/images/icon_config.png" border="0"> '. t('Konfiguration') .'</a>', t('Hier können Sie Konfigurationen zu diesem Modul vornehmen'));
    $dsp->AddDoubleRow('<a href="index.php?mod=install&action=mod_cfg&step=20&module='. $_GET['module'] .'"><img src="design/images/icon_delete_group.png" border="0"> '. t('Berechtigungen') .'</a>', t('Legen Sie fest, welche Benutzer Berechtigungen zu welchem Menüpunkt erhalten'));
    $dsp->AddDoubleRow('<a href="index.php?mod=install&action=mod_cfg&step=30&module='. $_GET['module'] .'"><img src="design/images/icon_tree.png" border="0"> '. t('Menü') .'</a>', t('Definieren Sie eigene Menüpunkte'));
    $dsp->AddDoubleRow('<a href="index.php?mod=misc&action=translation&step=20&file='. $_GET['module'] .'"><img src="design/images/icon_translate.png" border="0"> '. t('Übersetzung') .'</a>', t('Übersetzen Sie Texte dieses Moduls in andere Sprachen, oder definieren Sie einen deutschen Text, um einen Text umzuformulieren'));
    if (file_exists('modules/'. $_GET['module'] .'/mod_settings/db.xml')) $dsp->AddDoubleRow('<a href="index.php?mod=install&action=modules&step=30&module='. $_GET['module'] .'"><img src="design/images/icon_database.png" border="0"> '. t('Datenbank') .'</a>', t('Verwalten Sie die Datenbanktabellen, die diesem Modul zugeordnet sind'));
    if (file_exists('modules/'. $_GET['module'] .'/docu/'. $language .'_help.php')) $dsp->AddDoubleRow('<a href="#" onclick="javascript:var w=window.open(\'index.php?mod=helplet&action=helplet&design=base&module='. $_GET['module'] .'&helpletid=help\',\'_blank\',\'width=700,height=500,resizable=no,scrollbars=yes\');"><img src="design/images/icon_help.png" border="0"> '. t('Modul-Info') .'</a>', t('Hilfe und Informationen zu diesem Modul aufrufen'));
    $dsp->AddFieldsetEnd();
  break;


  // Config
  case 10:
		$resGroup = $db->qry('SELECT cfg_group FROM %prefix%config WHERE cfg_module = %string% GROUP BY cfg_group ORDER BY cfg_group', $_GET['module']);
		if ($db->num_rows($resGroup) == 0) $func->error(t('Keine Einstellungen zu diesem Modul vorhanden'), 'index.php?mod=install&action=mod_cfg');
		else {
			$dsp->SetForm('index.php?mod=install&action=mod_cfg&step=11&module='. $_GET['module']);
      while ($rowGroup = $db->fetch_array($resGroup)) {
    		$dsp->AddFieldsetStart($rowGroup['cfg_group']);

        // Get items in group
    		$res = $db->qry('SELECT cfg_key, cfg_value, cfg_desc, cfg_type, cfg_group FROM %prefix%config
          WHERE cfg_module = %string% and cfg_group = %string%
          ORDER BY cfg_key',
          $_GET["module"], $rowGroup['cfg_group']
          );
  			while ($row = $db->fetch_array($res)){
  				$row['cfg_desc'] = $func->translate($row['cfg_desc']);
  				if ($row['cfg_type'] == 'string') $row['cfg_value'] = $func->translate($row['cfg_value']);

  				// Get Selections
  				$get_cfg_selection = $db->qry('SELECT cfg_display, cfg_value FROM %prefix%config_selections WHERE cfg_key = %string%', $row['cfg_type']);
  				if ($db->num_rows($get_cfg_selection) > 0) {
  					$t_array = array();
  					while ($selection = $db->fetch_array($get_cfg_selection)){
  						($row['cfg_value'] == $selection['cfg_value']) ? $selected = 'selected' : $selected = '';
  						array_push ($t_array, '<option $selected value="{$selection["cfg_value"]}">'. $func->translate($selection['cfg_display']) .'</option>');
  					}
  					$dsp->AddDropDownFieldRow($row['cfg_key'], $row['cfg_desc'], $t_array, '', 1);

  				// Show Edit-Fields for Settings
  				} else switch ($row['cfg_type']){
  					case 'password':
  						$dsp->AddPasswordRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '', 1);
  					break;

  					case 'datetime':
  						$dsp->AddDateTimeRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '');
  					break;

  					case 'date':
  						$dsp->AddDateTimeRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '', '', '', '', 1);
  					break;

  					case 'time':
  						$dsp->AddDateTimeRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '', '', '', '', '', 2);
  					break;

  					case 'text':
  						$dsp->AddTextAreaRow($row['cfg_key'], '<div style=white-space:normal;>'. $row['cfg_desc'] .'</div>', $row['cfg_value'], '');
  					break;

  					default:
  						$row['cfg_value'] = str_replace('<', '&lt;', $row['cfg_value']);
  						$row['cfg_value'] = str_replace('>', '&gt;', $row['cfg_value']);
  						$row['cfg_value'] = str_replace('"', "'", $row['cfg_value']);
  						$dsp->AddTextFieldRow($row['cfg_key'], $row['cfg_desc'], $row['cfg_value'], '');
  					break;
  				}
  			}
  			$db->free_result($res);
        $dsp->AddFieldsetEnd();
  		}
			$db->free_result($resGroup);
			$dsp->AddFormSubmitRow('next');
		}
    $dsp->AddBackButton('index.php?mod=install&action=mod_cfg&module='. $_GET['module']);
  break;

	// Config - Write to DB
	case 11:
		foreach ($_POST as $key => $val) {
			// Date + Time Values
			if (strpos($key, '_value_') > 0) {
				if (strpos($key, '_value_minutes') > 0) {
					$key = substr($key, 0, strpos($key, '_value_minutes'));
					$cfg_value = mktime($_POST[$key.'_value_hours'], $_POST[$key.'_value_minutes'], $_POST[$key.'_value_seconds'], $_POST[$key.'_value_month'], $_POST[$key.'_value_day'], $_POST[$key.'_value_year']);
					$db->qry('UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = %string%', $cfg_value, $key);
				}
			// Other Values
			} else $db->qry('UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = %string%', $val, $key);
		}
		$func->confirmation(t('Erfolgreich geändert'), 'index.php?mod=install&action=mod_cfg&step=10&module='. $_GET["module"]);
	break;


  // Permission
  case 20:
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();

		$res = $db->qry('SELECT * FROM %prefix%menu WHERE module = %string% AND caption != \'\' ORDER BY level, requirement, pos', $_GET['module']);
    while ($row = $db->fetch_array($res)) {
      $mf->AddDBLineID($row['id']);
      
      $selections = array();
      $selections['0'] = t('Jeder');
      $selections['1'] = t('Nur Eingeloggte');
      $selections['2'] = t('Nur Admins');
      $selections['3'] = t('Nur Operatoren');
      $selections['4'] = t('Keine Admins');
      $selections['5'] = t('Nur Ausgeloggte');
      $mf->AddField(t('Zugriff'), 'requirement', IS_SELECTION, $selections, FIELD_OPTIONAL);

      $selections = array();
      $selections[''] = 'Keine';
  		if ($MenuCallbacks) foreach ($MenuCallbacks as $MenuCallback) $selections[$MenuCallback] = $MenuCallback;
      $mf->AddField(t('Vorraussetzung'), 'needed_config', IS_SELECTION, $selections, FIELD_OPTIONAL);

      $mf->AddGroup($row['caption'] .' ('. $row['link'] .')');
    }
    $db->free_result($res);

    $mf->SendForm('', 'menu', 'id', "module = '". $_GET['module'] ."' AND caption != ''");
    $dsp->AddBackButton('index.php?mod=install&action=mod_cfg&module='. $_GET['module']);
  break;


  // Menu
  case 30:
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();

		$res = $db->qry('SELECT * FROM %prefix%menu WHERE module = %string% AND caption != \'\' ORDER BY level, requirement, pos', $_GET['module']);
    while ($row = $db->fetch_array($res)) {
      $mf->AddDBLineID($row['id']);
      $mf->AddField(t('Titel'), 'caption');
      $mf->AddField(t('Link'), 'link');
      $mf->AddField(t('Popup-Hinweis'), 'hint', '', '', FIELD_OPTIONAL);
      $mf->AddGroup($row['caption']);
    }
    $db->free_result($res);

    $mf->SendForm('', 'menu', 'id', "module = '". $_GET['module'] ."' AND caption != ''");
#    $dsp->AddDoubleRow('', $dsp->FetchSpanButton('Link hinzufügen', 'index.php?mod=install&action=mod_cfg&step=31&module='. $_GET['module']));
    $dsp->AddBackButton('index.php?mod=install&action=mod_cfg&module='. $_GET['module']);
  break;


  // Database
  case 40:
  break;
}
?>