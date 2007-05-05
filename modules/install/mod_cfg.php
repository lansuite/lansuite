<?php
$CurrentMod = $db->qry_first('SELECT caption FROM %prefix%modules WHERE name=%string%', $_GET['module']);

$dsp->NewContent(t('Modul-Konfiguration') .' - '. $CurrentMod['caption'], t('Hier können Sie dieses Modul Ihren Bedürfnissen anpassen'));

$menunames = array();
$res = $db->qry('SELECT name, caption FROM %prefix%modules WHERE active = 1 ORDER BY caption');
while ($row = $db->fetch_array($res)) $menunames[$row['name']] = $row['caption'];
$db->free_result($res);
$dsp->AddHeaderMenu2($menunames, 'index.php?mod=install&action=mod_cfg&module=', $_GET['headermenuitem']);

switch ($_GET['step']) {
  default:
    $dsp->AddFieldsetStart(t('Verwaltungs-Werkzeuge'));
    $find_config = $db->qry_first('SELECT cfg_key FROM %prefix%config WHERE cfg_module = %string%', $_GET['module']);
    if ($find_config['cfg_key']) $dsp->AddDoubleRow('<a href="index.php?mod=install&action=modules&step=10&module='. $_GET['module'] .'"><img src="design/images/icon_config.png" border="0"> '. t('Konfiguration') .'</a>', t('Hier können Sie Konfigurationen zu diesem Modul vornehmen'));
    $dsp->AddDoubleRow('<a href="index.php?mod=install&action=mod_cfg&step=20&module='. $_GET['module'] .'"><img src="design/images/icon_delete_group.png" border="0"> '. t('Berechtigungen') .'</a>', t('Legen Sie fest, welche Benutzer Berechtigungen zu welchem Menüpunkt erhalten'));
    $dsp->AddDoubleRow('<a href="index.php?mod=install&action=modules&step=20&module='. $_GET['module'] .'"><img src="design/images/icon_tree.png" border="0"> '. t('Menü') .'</a>', t('Definieren Sie eigene Menüpunkte'));
    $dsp->AddDoubleRow('<a href="index.php?mod=misc&action=translation&step=20&file='. $_GET['module'] .'"><img src="design/images/icon_translate.png" border="0"> '. t('Übersetzung') .'</a>', t('Übersetzen Sie Texte dieses Moduls in andere Sprachen, oder definieren Sie einen deutschen Text, um einen Text umzuformulieren'));
    if (file_exists('modules/'. $_GET['module'] .'/mod_settings/db.xml')) $dsp->AddDoubleRow('<a href="index.php?mod=install&action=modules&step=30&module='. $_GET['module'] .'"><img src="design/images/icon_database.png" border="0"> '. t('Datenbank') .'</a>', t('Verwalten Sie die Datenbanktabellen, die diesem Modul zugeordnet sind'));
    if (file_exists('modules/'. $_GET['module'] .'/docu/'. $language .'_help.php')) $dsp->AddDoubleRow('<a href="#" onclick="javascript:var w=window.open(\'index.php?mod=helplet&action=helplet&design=base&module='. $_GET['module'] .'&helpletid=help\',\'_blank\',\'width=700,height=500,resizable=no,scrollbars=yes\');"><img src="design/images/icon_help.png" border="0"> '. t('Modul-Info') .'</a>', t('Hilfe und Informationen zu diesem Modul aufrufen'));
    $dsp->AddFieldsetEnd();
  break;

  // Config
  case 10:
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
  
  // Permission - Write to DB
  case 21:
  break;

  // Menu
  case 30:
  break;

  // Database
  case 40:
  break;
}
?>